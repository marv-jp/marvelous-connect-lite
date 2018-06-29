<?php

class Marvelous_FederationController extends Marvelous_Base_BaseController
{

    /**
     * 画面のないアクション群なのでビューを無効化する
     */
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);
    }

    /**
     * ID連携認証
     * 
     * どこから呼ばれるアクションなのか
     * 　1. MISPログイン画面
     * 　　　　→プラットフォームアイコンをタップした際に呼ばれる
     * 　2. MISI連携一覧画面
     * 　　　　→OFFのものをONにした際に呼ばれる
     * 
     * 不正なプラットフォームIDを渡された場合
     * 　MISPログイン画面にリダイレクトする
     */
    public function indexAction()
    {
        // HybridAuth設定を取得
        $hybridAuthConfig = Zend_Registry::get('hybridauth_configs');

        // MISP設定を取得
        $mispConfig = Zend_Registry::get('misp');

        // HybridAuth設定にMISP設定のbase_urlを追加
        $hybridAuthConfig['hybridauth']['base_url'] = $mispConfig['hybridauth']['baseUrl'];

        // 不正なパラメータがないかチェック するための準備
        // process request
        $headers              = Misp_Util::getRequestHeaders();
        $authorizationRequest = new Akita_OpenIDConnect_Server_Request('authorization', $_SERVER, $_GET, $headers);
        $dataHandler          = new OpenIDConnect_Server_DataHandler($authorizationRequest);
        $authHandler          = new OpenIDConnect_Server_AuthorizationHandler();

        try {
            // Authorization Request チェック
            $authHandler->processAuthorizationRequest($dataHandler, array('id_token token', 'code',));

            // パラメータに問題がない場合、パラメータ取得
            // パラメータ取得
            $request       = $this->getRequest();
            $platformId    = $request->getParam('platform_id');
            $applicationId = $request->getParam('client_id');
            $redirectUri   = $request->getParam('redirect_uri');
            $scope         = $request->getParam('scope');
            $state         = $request->getParam('state');
            $nonce         = $request->getParam('nonce');
            //   オプション
            $maxAge        = $request->getParam('max_age', $mispConfig['idToken']['expTime']);

            try {
                // HybridAuth
                $hybridAuth = new Hybrid_Auth($hybridAuthConfig['hybridauth']);
            } catch (Exception $exc) {
                Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
                $this->_redirect($redirectUri);
            }

            // プラットフォーム認証画面を呼び出す
            //    未認証の場合はプラットフォーム認証画面に移動する
            //    既に認証済みの場合は接続アダプタが返却される
            $adapter = $hybridAuth->authenticate($platformId);


            // 既に認証済みなので、ID連携コールバック処理を行う
            if ($adapter) {
                $this->_forward('federation', $request->getControllerName(), $request->getModuleName(), array(
                    'client_id'    => $applicationId,
                    'platform_id'  => $platformId,
                    'redirect_uri' => $redirectUri,
                    'max_age'      => $maxAge,
                    'state'        => $state,
                    'nonce'        => $nonce,
                ));
            }
        } catch (Exception $exc) {
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            throw $exc;
        }
    }

    /**
     * ID連携
     * 
     * 例外発生時
     * 　MISPログイン画面にリダイレクトする
     */
    public function federationAction()
    {
        // HybridAuth設定を取得
        $hybridAuthConfig = Zend_Registry::get('hybridauth_configs');

        // MISP設定を取得
        $mispConfig = Zend_Registry::get('misp');

        // HybridAuth設定にMISP設定のbase_urlを追加
        $hybridAuthConfig['hybridauth']['base_url'] = $mispConfig['hybridauth']['baseUrl'];

        // パラメータ取得
        $request       = $this->getRequest();
        $responseType  = $request->getParam('response_type');
        $applicationId = $request->getParam('client_id');
        $platformId    = $request->getParam('platform_id');
        $maxAge        = (int) $request->getParam('max_age');
        $redirectUri   = $request->getParam('redirect_uri');
        $state         = $request->getParam('state');
        $nonce         = $request->getParam('nonce');

        try {
            // HybridAuth
            $hybridAuth = new Hybrid_Auth($hybridAuthConfig['hybridauth']);
            $adapter    = $hybridAuth->getAdapter($platformId);

            try {
                $userProfile = $adapter->getUserProfile();
            } catch (Exception $exc) {
                // ユーザ情報が取得できなかった場合は、
                // ユーザが任意でプラットフォーム側で連携を解除した可能性がある。
                // その場合は、セッションを削除し、プラットフォームの認可画面に遷移させる。
                Zend_Session::destroy();

                // 再度、MISP連携アクションを呼び出す
                $redirectParams                = $this->_generateRedirectParams();
                $redirectParams['platform_id'] = $platformId;
                $this->_redirectFederation($redirectParams);
            }

            $accessTokens = $adapter->getAccessToken();

            $platformUserId          = $userProfile->identifier;
            $emailAddress            = $userProfile->email;
            $platformUserName        = $userProfile->displayName;
            $platformUserDisplayName = strlen($userProfile->lastName) ? $userProfile->firstName . ' ' . $userProfile->lastName : $userProfile->firstName;
            $accessToken             = $accessTokens['access_token'];
            $idToken                 = $accessTokens['access_token_secret'];

            // プラットフォームユーザ情報の準備
            $platformUser = new Application_Model_PlatformUser();
            $platformUser->setPlatformUserId($platformUserId);
            $platformUser->setPlatformId($platformId);
            $platformUser->setEmailAddress($emailAddress);
            $platformUser->setAccessToken($accessToken);
            $platformUser->setIdToken($idToken);
            $platformUser->setPlatformUserName($platformUserName);
            $platformUser->setPlatformUserDisplayName($platformUserDisplayName);

            // MISPのIDトークン生成の準備
            $time    = time();
            $iss     = $mispConfig['idToken']['iss'];
            $exp     = $time + $maxAge;
            $payload = new Common_Oidc_IdToken_Payload();
            $payload->setIss($iss);
            $payload->setAud($applicationId);
            $payload->setExp($exp);
            $payload->setIat($time);
            $payload->setNonce($nonce);

            // response_typeのセット
            $authorization = new Common_Oidc_Authorization_Authorization();
            $authorization->setResponseType($responseType);
            $authorization->setRedirectUri($redirectUri);

            // プラットフォーム認証後処理
            $logicUser                       = new Logic_User();
            $logicUser->setUserLogic($logicUser);
            $userPlatformApplicationRelation = $logicUser->federationCallback($platformUser, $payload, $authorization);

            // Authorizaton Response
            $redirectParams = array();

            switch ($responseType) {
                case 'id_token token':
                    $redirectParams['access_token'] = $userPlatformApplicationRelation->getAccessToken();
                    $redirectParams['token_type']   = 'bearer';
                    $redirectParams['id_token']     = $userPlatformApplicationRelation->getIdToken();
                    $redirectParams['expires_in']   = $maxAge;
                    if (strlen($state)) {
                        $redirectParams['state'] = $state;
                    }
                    $redirectParams['platform_id'] = $userPlatformApplicationRelation->getPlatformId();
                    break;
                case 'code':
                    $redirectParams['code']        = $userPlatformApplicationRelation->getAuthorizationCode();
                    $redirectParams['state']       = $state;
                    break;
                default:
                    break;
            }

            $this->_redirectWithParams($redirectUri, $redirectParams);
        } catch (Exception $exc) {
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // MISP初回ログイン画面にリダイレクト
            $redirectParams          = $this->_generateRedirectParams();
            $redirectParams['error'] = $exc->getMessage();
            $this->_redirectLogin($redirectParams);
        }
    }

    /**
     * プラットフォームユーザID連携解除
     * 
     * プラットフォームユーザの情報を元に
     * ユーザプラットフォームアプリケーション関連とプラットフォームユーザのstatusを未連携状態にする
     * 必要であればユーザのstatusも未連携状態にする
     */
    public function cancelPlatformUserFederationAction()
    {
        $request = $this->getRequest();

        // プラットフォームからのリクエストを取得
        $platformId = $request->getParam('platform_id');

        // プラットフォームによってリクエストパラメータ様式とその処理内容が異なるので、ケース分岐する
        switch ($platformId) {

            // Facebook
            case 'Facebook':

                // Facebook は signed_request パラメータ内にユーザID情報などを埋め込んでいる
                $signature = $request->getParam('signed_request');
                // signed_request をデコードし、ユーザIDなどが格納されているペイロードを取得する
                $payload   = $this->_decodeSignatureFacebook($signature, $platformId);

                // ペイロードから Facebook のユーザIDを収得
                $platformUserId = $payload['user_id'];
                break;

            default:
                Common_Log::getInternalLog()->info('プラットフォームからの解除リクエストで不正なリクエストがありました');
                break;
        }

        // プラットフォームユーザID連携解除のために必要な情報をモデルにセット
        $platformUser = new Application_Model_PlatformUser();
        $platformUser->setPlatformUserId($platformUserId);
        $platformUser->setPlatformId($platformId);

        // ユーザロジック
        $logicUser = new Logic_User();
        $logicUser->setUserLogic($logicUser);

        // プラットフォームユーザID連携解除
        //   最上位の例外ハンドラでログ出力させるので、ここではハンドルしない
        $logicUser->cancelPlatformUserFederation($platformUser);
    }

    /**
     * アプリケーションユーザID連携解除
     * 
     * ユーザがMISP連携一覧画面からプラットフォームの連携解除操作を行った際に呼ばれるアクション
     */
    public function cancelApplicationUserFederationAction()
    {
        // パラメータ取得
        $request       = $this->getRequest();
        $applicationId = $request->getParam('client_id');
        $platformId    = $request->getParam('platform_id');
        $accessToken   = $request->getParam('access_token');
        $idToken       = $request->getParam('id_token');

        // ユーザプラットフォームアプリケーション関連モデル
        $userPlatformApplicationRelation = new Application_Model_UserPlatformApplicationRelation();
        $userPlatformApplicationRelation->setPlatformId($platformId);
        $userPlatformApplicationRelation->setApplicationId($applicationId);
        $userPlatformApplicationRelation->setAccessToken($accessToken);
        $userPlatformApplicationRelation->setIdToken($idToken);

        // ユーザロジック
        $logicUser = new Logic_User();
        $logicUser->setUserLogic($logicUser);

        try {
            // ID連携解除時処理
            $logicUser->cancelIdFederation($userPlatformApplicationRelation);

            // MISP初回ログイン画面にリダイレクト
            $redirectParams                 = $this->_generateRedirectParams();
            $redirectParams['access_token'] = $accessToken;
            $redirectParams['id_token']     = $idToken;
            $this->_redirectLogin($redirectParams);
            //
        } catch (Exception $exc) {
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // MISP初回ログイン画面にリダイレクト
            $redirectParams          = $this->_generateRedirectParams();
            $redirectParams['error'] = $exc->getMessage();
            $this->_redirectLogin($redirectParams);
        }
    }

    /**
     * プラットフォーム認証画面を表示する際に HybridAuth を起動して処理を開始する、 HybridAuth のエンドポイントアクション
     * 
     * いじるな危険(本当に危険)
     * STAN-82参照
     * 
     * @see application/configs/hybridauth.yml
     */
    public function authAction()
    {
        Hybrid_Endpoint::process();
    }

    /**
     * mixiが認可後にリダイレクトするアクション
     * 
     * ----
     * mixiのリダイレクトURI設定方法
     * 
     * mixiデベロッパーサイト→コンテンツ一覧→mixiアプリ→管理アプリ一覧→＜MISPアプリ名＞→アプリ設定変更
     * →新方式(GraphAPI)利用に関する設定(PC、モバイル、スマートフォン)
     * →リダイレクトURI
     * で設定する
     * ----
     * 
     * ＜このアクションを作成する動機となった問題＞
     * 
     * HybridAuthは「プロバイダ認可後のシーケンスを判別するために"hauth_done=<ProviderId>"というリクエストパラメータを使用している」が、
     * mixiはデベロッパーサイトで設定したリダイレクトURIの任意パラメータ("hauth_done=<ProviderId>")を除去してリダイレクトするため、
     * HybridAuth側でプロバイダ認可後のシーケンスが判別できなくなってしまう。
     * 
     * ＜問題の解決策＞
     * 
     * 　・mixiが認可後にリダイレクトするアクションを専用に用意(authMixiAction)
     * 　・authMixiActionから"hauth_done=Mixi"というパラメータをともなってauthActionにリダイレクトする(本来の処理にまわす)
     */
    public function authMixiAction()
    {
        $request         = $this->getRequest();
        // hybridauth.ymlのproviders項目のmixi名と一致させること。
        // (hybridauth/Hybrid/Providers/Mixi.phpのクラス名：Hybrid_Providers_Mixi　の生成ネタに使用される)
        $params          = array('hauth_done' => 'Mixi'); // 'Mixi'がベタ書きなのはどうしようもない。
        $params['code']  = $request->getParam('code');
        $params['state'] = $request->getParam('state');
        // forwardはリクエストパラメータがZF管理となりHybridAuthにパラメータを引き渡せない(HybridAuthは$_REQUESTを直接触る)ので、
        // リダイレクト方式にする
        $this->_redirectAuth($params);
    }

    /**
     * HybridAuth のエンドポイントアクションにリダイレクトする
     * 
     * @param array $params リダイレクトパラメータの連想配列
     */
    private function _redirectAuth($params)
    {
        $this->_redirectWithParams('/marvelous/federation/auth', $params);
    }

    /**
     * MISP初回ログイン画面にリダイレクトする
     * 
     * @param array $params リダイレクトパラメータの連想配列
     */
    private function _redirectLogin($params)
    {
        $this->_redirectWithParams('/marvelous/authorization/login', $params);
    }

    /**
     * MISP連携アクションにリダイレクトする
     * 
     * @param array $params リダイレクトパラメータの連想配列
     */
    private function _redirectFederation($params)
    {
        $this->_redirectWithParams('/marvelous/federation', $params);
    }

    /**
     * パラメータを構築してリダイレクトする
     * 
     * @param string $redirectUri リダイレクトするURI
     * @param array $params リダイレクトパラメータの連想配列
     */
    private function _redirectWithParams($redirectUri, $params)
    {
        // パラメータ付きのリダイレクトURIもある(例：マベメン)ので、
        // 「?」で分割を試み、パラメータの存在を確認できた場合は
        // MISPのパラメータと一緒に「&」で連結する
        // パラメータ付きでない場合は、通常通り「?」で連結する
        $uri       = '';
        $param     = '';
        $delimiter = '?';
        list($uri, $param) = explode('?', $redirectUri);

        // $paramがある場合は任意のリダイレクトURIパラメータが有るということなので、
        // MISPのパラメータに「&」で連結するようにする
        if (strlen($param)) {
            $delimiter = '&';
        }

        $this->_redirect($redirectUri . $delimiter . http_build_query($params));
    }

    /**
     * リダイレクトパラメータ構築
     * 
     * @return array リダイレクトパラメータの連想配列
     */
    private function _generateRedirectParams()
    {
        $request = $this->getRequest();

        $options                  = array();
        $options['response_type'] = $request->getParam('response_type');
        $options['client_id']     = $request->getParam('client_id');
        $options['redirect_uri']  = $request->getParam('redirect_uri');
        $options['state']         = $request->getParam('state');
        $options['nonce']         = $request->getParam('nonce');
        $options['scope']         = $request->getParam('scope');
        if (strlen($request->getParam('state'))) {
            $options['state'] = $request->getParam('state');
        }

        if (strlen($request->getParam('max_age'))) {
            $options['max_age'] = $request->getParam('max_age');
        }

        return $options;
    }

    /**
     * Facebook解除通知で送られてくる signed_request を検証、デコードします。
     * 
     * @param string $signedRequest Facebook解除通知で送られてくる signed_request の値
     * @param string $platformId hybridauth.yml に定義した Facebook 項目名
     * @return array デコードした signed_request のペイロードの中身(連想配列)
     * @throws Common_Exception_OauthInvalidClient
     */
    private function _decodeSignatureFacebook($signedRequest, $platformId)
    {
        // HybridAuth設定を取得
        $hybridAuthConfig = Zend_Registry::get('hybridauth_configs');

        // 設定から秘密鍵を取得
        $secret = $hybridAuthConfig['hybridauth']['providers'][$platformId]['keys']['secret'];

        // シグネチャとペイロードに分割
        list($encodedSignature, $encodedPayload) = explode('.', $signedRequest);

        // シグネチャのデコード開始
        $decodedSignature = Misp_Util::base64UrlDecode($encodedSignature);
        $decodedPayload   = Zend_Json::decode(Misp_Util::base64UrlDecode($encodedPayload));

        // ペイロードを復号
        $expectedSignature = hash_hmac('sha256', $encodedPayload, $secret, TRUE);

        // HMACのアルゴリズムチェック
        if (strtoupper($decodedPayload['algorithm']) !== 'HMAC-SHA256') {
            // 不一致の場合は例外
            throw new Common_Exception_OauthInvalidClient(sprintf('HMACアルゴリズムが一致しません。[リクエストされたアルゴリズム：%s', $decodedPayload['algorithm']));
        }

        // 検証
        if ($decodedSignature !== $expectedSignature) {
            // 不一致の場合は例外
            throw new Common_Exception_OauthInvalidClient('シグネチャが一致しません');
        }

        return $decodedPayload;
    }

}
