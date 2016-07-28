<?php

/**
 * Yahoo! JAPAN 連携用クラス
 * 
 * メールアドレス取得のために、OAuth 2.0 に準拠および OpenID Connect もサポートしているYConnectの UserInfo API を使用します。
 * 
 * UserInfo API は、Yahoo! JAPAN 提供の「YConnect PHP SDK」を利用して取り扱います。
 *  
 * @link http://developer.yahoo.co.jp/ Yahoo!デベロッパーネットワーク
 * @link http://developer.yahoo.co.jp/yconnect/ YConnect
 * @link https://e.developer.yahoo.co.jp/dashboard/ アプリケーションの管理
 */
class Hybrid_Providers_YahooJapan extends Hybrid_Provider_Model
{
    /** @var string ID Tokenの発行者を表す文字列 */
    const ISS = 'https://auth.login.yahoo.co.jp';

    /** @var string Yahoo!デベロッパーネットワークのアプリケーションのアプリケーションID */
    protected $_clientId;

    /** @var string Yahoo!デベロッパーネットワークのアプリケーションのシークレット */
    protected $_clientSecret;

    /** @var string プロバイダがリダイレクトするURI */
    protected $_redirectUri;

    /**
     * IDp wrappers initializer 
     */
    function initialize()
    {
        if (!$this->config["keys"]["id"] || !$this->config["keys"]["secret"]) {
            throw new Common_Exception_IllegalConfig("{$this->providerId} の consumer key と consumer secret がありません。hybridauth.ymlの設定を確認してください。");
        }

        // Yahooライブラリにパスを通す
        set_include_path(implode(PATH_SEPARATOR, array(
            realpath(Hybrid_Auth::$config["path_libraries"] . "YahooJapan/lib"),
            get_include_path(),
        )));
        // YConnectライブラリ読み込み
        require("YConnect.inc");

        // アプリケーションID, シークレッvト
        $this->_clientId     = $this->config["keys"]["id"];
        $this->_clientSecret = $this->config["keys"]["secret"];

        // リダイレクトURIの指定
        $this->_redirectUri = $this->endpoint;

        // クレデンシャルインスタンス生成
        $cred = new ClientCredential($this->_clientId, $this->_clientSecret);

        // YConnectクライアントインスタンス生成
        $this->api = new YConnectClient($cred);
    }

    /**
     * 認可応答処理
     * 
     * Authorizationエンドポイントにリクエストして同意画面を表示します。
     * 
     * ユーザが同意画面で「同意」すると、Yahoo!デベロッパーネットワーク管理画面で設定したリダイレクトURIにYahoo! JAPANがリダイレクトし、
     * 当クラスの「loginFinish」メソッドに到達します。
     * その際、下記パラメータが付与されます。
     * 
     * ・code：認可コード（8Byteの固定長文字列）
     * ・state：リクエスト時に指定されたstate値
     * 
     * @link http://developer.yahoo.co.jp/yconnect/server_app/explicit/authorization.html Authorizationエンドポイント
     */
    function loginBegin()
    {
        // state 生成、保存
        // stateはAuthorizationエンドポイントからのコールバックURL受け取り時の検証に使用
        $state = $this->establishCSRFTokenState();
        Hybrid_Auth::storage()->set("hauth_session.state", $state);

        // nonce 生成、保存
        // nonceはIDトークン復号化時の検証に使用
        $nonce = $this->generateNonce();
        Hybrid_Auth::storage()->set("hauth_session.nonce", $nonce);

        // 認証エンドポイントから返却される値の種類
        $responseType = OAuth2ResponseType::CODE_IDTOKEN;

        // 取得する属性を指定
        $scope = array(
            OIDConnectScope::OPENID,
            OIDConnectScope::EMAIL,
        );

        // Authorizationエンドポイントにリクエストし、同意画面を表示(未ログインならログイン画面が表示される)
        $this->api->requestAuth(
                $this->_redirectUri, $state, $nonce, $responseType, $scope
        );
    }

    /**
     * コールバックURLを受け取り、認可コードを抽出します。
     * 
     * @link http://developer.yahoo.co.jp/yconnect/server_app/explicit/authorization.html Authorizationエンドポイント
     */
    function loginFinish()
    {
        try {
            // Authorization Codeを取得
            // (引数のstateとコールバック付与されたstateの検証をSDKで行っている。
            //  検証が正しい場合は認可コードが返り、
            //  コールバックがエラーレスポンスだった場合は内部で例外が投げられる。)
            $authorizationCode = $this->api->getAuthorizationCode(Hybrid_Auth::storage()->get("hauth_session.state"));

            if ($authorizationCode) {

                // Tokenエンドポイントにリクエストしてアクセストークンを取得
                $this->api->requestAccessToken(
                        $this->_redirectUri, $authorizationCode
                );

                // アクセストークン取得
                $accessToken = $this->api->getAccessToken();

                // IDトークンの検証
                // (IDトークンの検証はSDKが行っている。不正な場合は例外が投げられる)
                $this->api->verifyIdToken(Hybrid_Auth::storage()->get("hauth_session.nonce"));

                // YConnectはIDトークン(のペイロード)をstdClassで返してくるのでarrayキャストする
                $payload = (array) $this->api->getIdToken();
                // SDKのチェック外の内容を検証する
                if ($this->_isInvalidIdToken($payload)) {
                    throw new Common_Exception_Oidc_InvalidToken('IDトークンが不正です。');
                }

                // セッションにアクセストークンを保存
                $this->token("access_token", $accessToken);

                // UserInfoエンドポイントにリクエスト
                $this->api->requestUserInfo($this->token("access_token"));
                $data = $this->api->getUserInfo();

                // プラットフォームユーザIDが取得できなかった場合は致命的なので例外を返す
                if (!isset($data["user_id"]) || !strlen($data['user_id'])) {
                    throw new Common_Exception_OauthInvalidClient(sprintf('プロバイダ：%s でユーザ属性を取得できませんでした。', $this->providerId));
                }

                // メールアドレスを取得できなかった場合はログ記録して続行
                if (!isset($data["email"])) {
                    Common_Log::getInternalLog()->info(sprintf('プロバイダ：%s でメールアドレスが返却されませんでした。', $this->providerId));
                }

                // ユーザ情報の取得
                // 現在は、必要なパラメータしか取得していません
                // 他に必要な際は、適宜追加
                $this->user->profile->identifier = (array_key_exists('user_id', $data)) ? $data['user_id'] : "";
                $this->user->profile->email      = (array_key_exists('email', $data)) ? $data['email'] : "";

                // ユーザのログイン情報をセッションに追加
                $this->setUserConnected();
            } else {
                throw new Common_Exception_OauthInvalidClient("Authorization Code がありません。");
            }
            // 投げられる例外で処理を分けないので、Exceptionでざっくり受ける
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
        // UserInfoエンドポイントにリクエスト
        $this->api->requestUserInfo($this->token("access_token"));
        $data = $this->api->getUserInfo();

        // プラットフォームユーザIDが取得できなかった場合は致命的なので例外を返す
        if (!isset($data["user_id"]) || !strlen($data['user_id'])) {
            throw new Common_Exception_OauthInvalidClient(sprintf('プロバイダ：%s でユーザ属性を取得できませんでした。', $this->providerId));
        }

        // メールアドレスを取得できなかった場合はログ記録して続行
        if (!isset($data["email"])) {
            Common_Log::getInternalLog()->info(sprintf('プロバイダ：%s でメールアドレスが返却されませんでした。', $this->providerId));
        }

        // ユーザ情報の取得
        // 現在は、必要なパラメータしか取得していません
        // 他に必要な際は、適宜追加
        $this->user->profile->identifier = (array_key_exists('user_id', $data)) ? $data['user_id'] : "";
        $this->user->profile->email      = (array_key_exists('email', $data)) ? $data['email'] : "";

        return $this->user->profile;
    }

    /**
     * Lays down a CSRF state token for this process.
     *
     * @return void
     */
    protected function establishCSRFTokenState()
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * util function: current nonce
     */
    protected function generateNonce()
    {
        $mt   = microtime();
        $rand = mt_rand();

        return md5($mt . $rand); // md5s look nicer than numbers
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
     *     [iss] => https://auth.login.yahoo.co.jp
     *     [user_id] => DDFJIJYTTV6P6O2IMVP65QMXCE
     *     [aud] => dj0zaiZpPTRZMUZSSUJhVDhTdyZzPWNvbnN1bWVyc2VjcmV0Jng9NmY-
     *     [iat] => 1392348241
     *     [exp] => 1394767441
     *     [nonce] => 53504472ee9ecfc764793428cbf7bfab
     * )
     * </pre>
     * 
     * @param string $idToken
     * @return boolean 不正：TRUE
     *                  正当：FALSE
     */
    private function _isInvalidIdToken($idToken)
    {
        // client_id
        if ($this->_clientId !== $idToken['aud']) {
            Common_Log::getInternalLog()->info(sprintf('%s が一致しません。MISP:%s | %s:%s', 'aud', $this->_clientId, $this->providerId, $idToken['aud']));
            return TRUE;
        }
        // iss
        if (Hybrid_Providers_YahooJapan::ISS !== $idToken['iss']) {
            Common_Log::getInternalLog()->info(sprintf('%s が一致しません。MISP:%s | %s:%s', 'iss', $this->_iss, $this->providerId, $idToken['iss']));
            return TRUE;
        }

        return FALSE;
    }

}
