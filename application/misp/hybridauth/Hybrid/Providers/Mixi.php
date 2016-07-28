<?php

/**
 * mixi連携用クラス
 * 
 * メールアドレス取得のために、OpenID Connect 準拠の UserInfo API を使用します。
 * 
 * UserInfo API は、下記のいずれかのアクセストークンが必要で、
 * それ以外のスコープで認可されたアクセストークンを使用して、UserInfo APIにアクセスすることはできません。
 * 
 * ・"openid"スコープについて認可されたアクセストークン
 * ・"openid"と"profile"、"email"スコープのいずれかもしくは両方の組み合わせについて認可されたアクセストークン
 * 
 * また、スコープに"openid"を指定するということは、
 * 「認可とAuthorization Codeの入手」の手順でserver_stateの利用が必須になるということです。
 * 
 * @link http://developer.mixi.co.jp/connect/mixi_graph_api/api_auth/
 * @link http://developer.mixi.co.jp/connect/mixi_graph_api/mixi_io_spec_top/userinfo-api/
 */
class Hybrid_Providers_Mixi extends Hybrid_Provider_Model
{
    /** @var string ID Tokenの発行者を表す文字列 */
    const ISS = 'https://api.mixi-platform.com';

    /** @var string Consumer Key(mixi Graph APIサービスのほう) */
    protected $_clientId;

    /** @var string Consumer Secret(mixi Graph APIサービスのほう) */
    protected $_clientSecret;

    /**
     * IDp wrappers initializer 
     */
    function initialize()
    {
        if (!$this->config["keys"]["id"] || !$this->config["keys"]["secret"]) {
            throw new Common_Exception_IllegalConfig("{$this->providerId} の consumer key と consumer secret がありません。hybridauth.ymlの設定を確認してください。");
        }

        // アプリケーションID, シークレット
        $this->_clientId     = $this->config["keys"]["id"];
        $this->_clientSecret = $this->config["keys"]["secret"];
    }

    /**
     * 認可応答処理
     * 
     * 1. server_stateの入手
     * 2. 認可とAuthorization Codeの入手
     * 
     * 上記2点を行います。
     * 
     * 「1. server_stateの入手」は、スコープに"openid"を指定しているので必須のフローとなっています。
     * 
     * 「1. server_stateの入手」で入手したserver_stateは、
     * Authorization Code と引き換えるために必要な情報(かつCSRF対策)なので、
     * セッションに保存し、あとで使います。
     * 
     * 「2. 認可とAuthorization Codeの入手」でmixiに未ログインの場合は、mixiログイン画面が表示されます。
     * 
     * mixiログイン済み、またはmixiログイン後にmixi認可画面が表示されます。
     * 今回の場合は、"ユーザ識別子"と"メールアドレス"について認可確認されます。
     * 
     * ユーザが認可画面で「同意」すると、mixi Graph API 管理画面で設定したリダイレクトURIにmixiがリダイレクトし、
     * 当クラスの「loginFinish」メソッドに到達します。
     * 
     * @throws Exception
     * @link http://developer.mixi.co.jp/connect/mixi_graph_api/api_auth/#toc-server-state server_stateの入手
     */
    function loginBegin()
    {
        try {
            // server_stateの取得、保存
            $serverStateBody = $this->_requestServerState($this->_clientId);
            $serverState     = $serverStateBody['server_state'];

            Hybrid_Auth::storage()->set("hauth_session.server_state", $serverState);

            // スコープの指定
            // (scopeパラメータに"openid"を指定する場合は「認可とAuthorization Codeの入手」の手順でserver_stateの利用が必須となります。)
            $scope = array('openid', 'email');

            // response_type 指定("code"固定)
            $responseType = 'code';

            // Authorizationエンドポイントにリクエスト
            $this->_requestAuth(
                    $this->_clientId, $scope, $serverState, $responseType
            );
        } catch (Exception $exc) {

            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            throw $exc;
        }
    }

    /**
     * ユーザ認可後のユーザ属性情報取得処理
     * 
     * @throws Exception
     */
    function loginFinish()
    {
        try {

            // Authorization Codeを取得
            $authorizationCode = $_GET["code"];
            $state             = $_GET["state"];

            // server_state取得
            $serverState  = Hybrid_Auth::storage()->get("hauth_session.server_state");
            $sessionState = Hybrid_Auth::storage()->get("hauth_session.state");

            // stateパラメータを利用したセッション維持の確認がされない場合、
            // アプリケーションにCSRF脆弱性が存在する事になります。
            // ユーザ認可後のリダイレクト時に、セッションとstateパラメータの組み合わせが正しいことを確認
            // http://developer.mixi.co.jp/connect/mixi_graph_api/api_auth/#toc-authorization-code
            if ($state !== $sessionState) {
                throw new Common_Exception_OauthInvalidClient('stateが一致しませんでした');
            }

            if ($authorizationCode) {

                // リダイレクトURI(のアクション)をmixi専用URIにする
                $mispConfig  = Zend_Registry::get('misp');
                $redirectUri = $mispConfig['hybridauth']['baseUrl'] . '-mixi';

                // Tokenエンドポイントにリクエスト
                $tokenBody = $this->_requestAccessToken($this->_clientId, $this->_clientSecret, $redirectUri, $authorizationCode, $serverState);

                // アクセストークン、IDトークン取得
                $accessToken = $tokenBody['access_token'];
                $idToken     = $tokenBody['id_token'];

                // IDトークン検証
                if ($this->_isInvalidIdToken($idToken)) {
                    throw new Common_Exception_Oidc_InvalidToken('IDトークンが不正です。');
                }

                // セッションにアクセストークンを保存
                $this->token("access_token", $accessToken);

                // UserInfoエンドポイントにリクエストし、ユーザ情報取得                
                $data = $this->_requestUserInfo($this->token("access_token"));

                // プラットフォームユーザIDが取得できなかった場合は致命的なので例外を返す
                if (!isset($data["sub"]) || !strlen($data['sub'])) {
                    throw new Common_Exception_OauthInvalidClient(sprintf('プロバイダ：%s でユーザ属性を取得できませんでした。', $this->providerId));
                }

                // メールアドレスを取得できなかった場合はログ記録して続行
                if (!isset($data["email"])) {
                    Common_Log::getInternalLog()->info(sprintf('プロバイダ：%s でメールアドレスが返却されませんでした。', $this->providerId));
                }

                // ユーザ情報の取得
                // 現在は、必要なパラメータしか取得していません
                // 他に必要な際は、適宜追加
                $this->user->profile->identifier = (array_key_exists('sub', $data)) ? $data['sub'] : "";
                $this->user->profile->email      = (array_key_exists('email', $data)) ? $data['email'] : "";

                // ユーザのログイン情報をセッションに追加
                $this->setUserConnected();
            } else {
                throw new Common_Exception_OauthInvalidClient("Authorization Code がありません。");
            }
        } catch (Exception $exc) {

            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            throw $exc;
        }
    }

    /**
     * 認可処理を行った後、ユーザ情報を取得する
     */
    function getUserProfile()
    {
        // UserInfoエンドポイントにリクエストし、ユーザ情報取得
        $data = $this->_requestUserInfo($this->token("access_token"));

        // プラットフォームユーザIDが取得できなかった場合は致命的なので例外を返す
        if (!isset($data["sub"]) || !strlen($data['sub'])) {
            throw new Common_Exception_OauthInvalidClient(sprintf('プロバイダ：%s でユーザ属性を取得できませんでした。', $this->providerId));
        }

        // メールアドレスを取得できなかった場合はログ記録して続行
        if (!isset($data["email"])) {
            Common_Log::getInternalLog()->info(sprintf('プロバイダ：%s でメールアドレスが返却されませんでした。', $this->providerId));
        }

        // ユーザ情報の取得
        // 現在は、必要なパラメータしか取得していません
        // 他に必要な際は、適宜追加
        $this->user->profile->identifier = (array_key_exists('sub', $data)) ? $data['sub'] : "";
        $this->user->profile->email      = (array_key_exists('email', $data)) ? $data['email'] : "";

        return $this->user->profile;
    }

    /**
     * Lays down a CSRF state token for this process.
     *
     * @return void
     */
    protected function _establishCSRFTokenState()
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * server_stateリクエストメソッド
     * 
     * server_stateのボディデータが返却される
     * 
     * @param string $clientId クライアントID
     * @return array server_stateのボディデータ
     */
    protected function _requestServerState($clientId)
    {
        // リクエストボディを作成
        $body               = array();
        $body['grant_type'] = 'server_state';
        $body['client_id']  = $clientId;

        // エンドポイントURL設定
        $endpointUrl = 'https://secure.mixi-platform.com/2/token';

        // リクエストの送信とレスポンスの取得
        return json_decode($this->_post($endpointUrl, $body), TRUE);
    }

    /**
     * 認可リクエストメソッド
     *
     * Authorizationエンドポイントにリクエストして同意画面を表示する
     *
     * @param	$clientId クライアントID
     * @param	$scope 配列形式でスコープ指定
     * @param	$serverState 取得したserver_state
     * @param  $responseType レスポンスタイプ(code)
     * @link http://developer.mixi.co.jp/connect/mixi_graph_api/api_auth/#toc-authorization-code 認可とAuthorization Codeの入手
     */
    protected function _requestAuth($clientId, $scope, $serverState, $responseType)
    {
        // パラメータの準備
        $params                  = array();
        // クライアントID
        $params['client_id']     = $clientId;
        // response_type
        $params['response_type'] = $responseType;
        // スコープ
        $params['scope']         = implode(' ', $scope);

        // 認証認可画面を表示するデバイス
        //   公式ドキュメント：http://developer.mixi.co.jp/connect/mixi_graph_api/api_auth/#toc-authorization-code
        //   
        // ドキュメントには「指定しなければならないクエリーパラメータ」とあり、
        // "display"の値を"pc"にすると、アクセスデバイスに関係なくPC向けmixi認証画面が表示された。
        // 
        //  また、"display"パラメータを付与せずにアクセス実行したところ、アクセスデバイスごとに適切なmixi認証画面が表示された。
        //  このことから"display"パラメータの判別はmixi側である程度融通を効かせていることが推測されるため、
        //  こちらでは値は指定しないことにした。(mixi側の仕様変更で将来的に必須チェックが入る懸念はある)
        $params['display'] = '';
        // state(ユーザの認可後に行われるリダイレクト時にこの値が含まれます。アプリケーションがセッションを持つ場合、このパラメータを使用してセッションが維持されている事を確認してください。)
        $params['state']   = $this->_establishCSRFTokenState();
        Hybrid_Auth::storage()->set("hauth_session.state", $params['state']);

        // 取得したserver_state
        $params['server_state'] = $serverState;

        // リクエストURLを生成
        $requestUrl = 'https://mixi.jp/connect_authorize.pl?' . http_build_query($params, null, '&');

        header("Location: " . $requestUrl);
        exit();
    }

    /**
     * トークン取得リクエストメソッド
     *
     * Authorizationエンドポイントにリクエストしてアクセストークンを取得する
     *
     * @param $clientId クライアントID
     * @param $clientSecret 秘密鍵
     * @param $redirectUri リダイレクトURI
     * @param $code 認可コード
     * @param $serverState 取得したserver_state
     * @return array トークンのボディデータ
     * @link http://developer.mixi.co.jp/connect/mixi_graph_api/api_auth/#toc-1 リフレッシュトークン、アクセストークンの入手
     */
    protected function _requestAccessToken($clientId, $clientSecret, $redirectUri, $code, $serverState)
    {
        // リクエストボディを作成
        $body                  = array();
        $body['grant_type']    = 'authorization_code';
        $body['client_id']     = $clientId;
        $body['client_secret'] = $clientSecret;
        $body['redirect_uri']  = $redirectUri;
        $body['code']          = $code;
        $body['server_state']  = $serverState;

        // エンドポイントURL設定
        $endpointUrl = 'https://secure.mixi-platform.com/2/token';

        // リクエストの送信とレスポンスの取得
        return json_decode($this->_post($endpointUrl, $body), TRUE);
    }

    /**
     * UserInfo API リクエストメソッド
     *
     * @param $clientId クライアントID
     * @return array ユーザ属性情報の連想配列(指定したスコープで項目が変動します)
     * <pre>
     * {
     *     "sub":"qgjw87yg3djw",
     *     "name":"アヤコ",
     *     "given_name":"彩子",
     *     "family_name":"佐藤",
     *     "nickname":"アヤコ",
     *     "preferred_username" :"アヤコ",
     *     "profile" : "http://mixi.jp/redirect_friend_api.pl?puid=qgjw87yg3djw&client_id=xxxxx",
     *     "picture" : "http://mixi.jp/photo/user/rdqz7s6ew176q_2105834213.jpg",
     *     "gender":"female",
     *     "birthdate":"1980-04-10",
     *     "email":"ayako@example.com",
     *     "email_verified":true
     * }
     * </pre>
     * @link http://developer.mixi.co.jp/connect/mixi_graph_api/mixi_io_spec_top/userinfo-api/ UserInfo API
     */
    protected function _requestUserInfo($accessToken)
    {
        // パラメータの準備
        $body                = array();
        // アクセストークン
        $body['oauth_token'] = $accessToken;

        // エンドポイントURL設定
        $endpointUrl = 'https://api.mixi-platform.com/2/openid/userinfo';

        // リクエストの送信とレスポンスの取得
        return json_decode($this->_post($endpointUrl, $body), TRUE);
    }

    /**
     * 指定URIにPOSTリクエストします。
     * 
     * リクエストはfile_get_contents()関数で行い、
     * レスポンスヘッダ―はfile_get_contents()関数によるHTTP ラッパーサポートによって
     * $http_response_headerに格納されます。
     * $http_response_headerはPHPの定義済の予約変数で、ローカルスコープで生成されます。
     * 
     * file_get_contents()関数の第三引数($context)のignore_errorsコンテキストをtrueにすることで、
     * ステータスコードが4xxや5xxでもWarnningエラーが発生せず、
     * レスポンスの受け取りを可能としています。
     * 
     * @param string $uri リクエストURI
     * @param array $data リクエストパラメータの連想配列
     * @return mixed レスポンス結果
     * @throws Exception レスポンスステータスが異常系の場合にThrowされる
     * @link http://www.php.net/manual/ja/wrappers.http.php HTTPラッパー
     * @link http://www.php.net/manual/ja/reserved.variables.httpresponseheader.php HTTP レスポンスヘッダ
     */
    private function _post($uri, $data)
    {
        // リクエストコンテキストを準備
        $context = array('http' => array(
                'method'        => 'POST',
                'header'        => 'Content-Type: application/x-www-form-urlencoded',
                'content'       => http_build_query($data, null, '&'),
                'ignore_errors' => true,
        ));

        // リクエスト実行
        $body = file_get_contents($uri, false, stream_context_create($context));

        // レスポンスヘッダ解析
        $header = $this->parseHeader($http_response_header);

        // レスポンスステータス成否判別
        //   エラー系レスポンスの場合はログ出力用文字列を生成し、例外Throwで通知する
        if ($this->isHttpFail($header['Status'])) {

            // エラーフォーマットが OpenID Connect の仕様に沿っていればJSON形式なので、
            // デコードしてログ出力用文字列を作る
            try {
                $messages = array($http_response_header[0]); // 最初のオフセットにレスポンスステータスとメッセージが格納されている
                foreach (Zend_Json::decode($body) as $key => $value) {
                    $messages[] = sprintf('%s:%s', $key, $value);
                }
            } catch (Zend_Json_Exception $exc) {
                // JSON以外はそのまま突っ込み、ThrowされたZend_Jsonの例外は無視する
                $messages[] = $body;
            }
            // ログ出力用文字列
            $message = implode(' | ', $messages);

            throw new Common_Exception_OauthInvalidClient($message);
        }

        return $body;
    }

    /**
     * レスポンスヘッダ解析
     * 
     * 数値オフセットのレスポンスヘッダ配列を、
     * Header-Key:Header-Valueの連想配列形式に変換します。
     * 
     * <pre>
     * Array
     * (
     *     [0] => HTTP/1.1 400 Bad Request
     *     [1] => Date: Thu, 13 Feb 2014 07:50:06 GMT
     *     [2] => Server: Apache
     *     [3] => Cache-Control: no-store
     *     [4] => X-MIXI-GRAPH-API-SPEC: 131072
     *     [5] => Vary: Accept-Encoding
     *     [6] => Content-Type: application/json
     *     [7] => X-Content-Type-Options: nosniff
     *     [8] => Connection: close
     * )
     * 
     * ↓
     * 
     * Array
     * (
     *     [Status] => 400
     *     [Message] => HTTP/1.1 400 Bad Request
     *     [Date] => Thu, 13 Feb 2014 07:50:06 GMT
     *     [Server] => Apache
     *     [Cache-Control] => no-store
     *     [X-MIXI-GRAPH-API-SPEC] => 131072
     *     [Vary] => Accept-Encoding
     *     [Content-Type] => application/json
     *     [X-Content-Type-Options] => nosniff
     *     [Connection] => close
     * )
     * </pre>
     * 
     * @param array $headers レスポンスヘッダ配列
     * @return array Header-Key:Header-Valueの連想配列形式
     */
    private function parseHeader($headers)
    {
        $statusLine        = array_shift($headers);
        list(, $result['Status'], ) = explode(' ', $statusLine);
        $result['Message'] = $statusLine;
        foreach ($headers as $header) {
            list($key, $value) = explode(': ', $header);
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * レスポンスステータス成否判別
     * 
     * @param int $status レスポンスステータス
     * @return boolean 成功：TRUE
     *                  失敗：FALSE
     */
    private function isHttpFail($status)
    {
        return (bool) (empty($status) || ($status >= 400));
    }

    /**
     * IDトークンを検証します。
     * 
     * IDトークンが不正だった場合はその項目が内部ログ出力されます。
     * 
     * IDトークンペイロードは以下のような内容です。
     * <pre>
     * Array
     * (
     *     [exp] => 1392289978
     *     [server_state] => EtVlvMqGaF93vfh2Xh5K25STF3lLqzBT0RZMpZ2ie9w
     *     [sub] => 6ruuqn555izpf
     *     [iat] => 1392286378
     *     [aud] => a58c7f5a75ec5d440cf4
     *     [iss] => https://api.mixi-platform.com
     * )
     * </pre>
     * 
     * @param string $idToken
     * @return boolean 不正：TRUE
     *                  正当：FALSE
     */
    private function _isInvalidIdToken($idToken)
    {
        $decodedIdToken = Common_Oidc_Token::decodeIdToken($idToken);
        $serverState    = Hybrid_Auth::storage()->get("hauth_session.server_state");

        // client_id
        if ($this->_clientId !== $decodedIdToken['aud']) {
            Common_Log::getInternalLog()->info(sprintf('%s が一致しません。MISP:%s | %s:%s', 'aud', $this->_clientId, $this->providerId, $decodedIdToken['aud']));
            return TRUE;
        }
        // server_state
        if ($serverState !== $decodedIdToken['server_state']) {
            Common_Log::getInternalLog()->info(sprintf('%s が一致しません。MISP:%s | %s:%s', 'server_state', $serverState, $this->providerId, $decodedIdToken['server_state']));
            return TRUE;
        }
        // iss
        if (Hybrid_Providers_Mixi::ISS !== $decodedIdToken['iss']) {
            Common_Log::getInternalLog()->info(sprintf('%s が一致しません。MISP:%s | %s:%s', 'iss', $this->_iss, $this->providerId, $decodedIdToken['iss']));
            return TRUE;
        }


        return FALSE;
    }

}
