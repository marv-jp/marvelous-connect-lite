<?php

/* !
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html 
 */

/**
 * Hybrid_Providers_Google provider adapter based on OAuth2 protocol
 * 
 * http://hybridauth.sourceforge.net/userguide/IDProvider_info_Google.html
 */
class Hybrid_Providers_Google extends Hybrid_Provider_Model_OpenIDConnect
{
    // > more infos on google APIs: http://developer.google.com (official site)
    // or here: http://discovery-check.appspot.com/ (unofficial but up to date)
    // default permissions 
    public $scope = "openid email";

    /** @var string アプリケーションのアプリケーションID */
    protected $_clientId;

    /** @var string アプリケーションのシークレット */
    protected $_clientSecret;

    /** @var string プロバイダがリダイレクトするURI */
    protected $_redirectUri;

    /**
     * IDp wrappers initializer 
     */
    function initialize()
    {
        parent::initialize();

        // Provider api end-points
        $this->api->authorize_url  = "https://accounts.google.com/o/oauth2/auth";
        $this->api->token_url      = "https://accounts.google.com/o/oauth2/token";
        $this->api->token_info_url = "https://www.googleapis.com/oauth2/v2/tokeninfo";
    }

    /**
     * begin login step 
     */
    function loginBegin()
    {
        try {
            $parameters = array("scope"       => $this->scope, "access_type" => "offline", "state"       => $this->_createState());
            $optionals  = array("scope", "access_type", "redirect_uri", "approval_prompt", "hd");

            foreach ($optionals as $parameter) {
                if (isset($this->config[$parameter]) && !empty($this->config[$parameter])) {
                    $parameters[$parameter] = $this->config[$parameter];
                }
                if (isset($this->config["scope"]) && !empty($this->config["scope"])) {
                    $this->scope = $this->config["scope"];
                }
            }

            $this->api->scope = $this->scope;

            Hybrid_Auth::redirect($this->api->authorizeUrl($parameters));
        } catch (Exception $exc) {

            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            throw $exc;
        }
    }

    /**
     * finish login step 
     */
    function loginFinish()
    {
        try {
            $error = (array_key_exists('error', $_REQUEST)) ? $_REQUEST['error'] : "";

            // check for errors
            if ($error) {
                throw new Exception("Authentication failed! {$this->providerId} returned an error: $error", 5);
            }

            // state がパラメータにある場合チェック
            $state = (array_key_exists('state', $_REQUEST)) ? $_REQUEST['state'] : "";
            if (!empty($state)) {
                $beginState = Hybrid_Auth::storage()->get("hauth_session.state");
                if ($state != $beginState) {
                    throw new Exception("Authentication failed! {$this->providerId} returned an error: invalid_state", 5);
                }
            }

            // try to authenicate user
            $code = (array_key_exists('code', $_REQUEST)) ? $_REQUEST['code'] : "";

            try {
                $this->api->authenticate($code);
            } catch (Exception $e) {
                throw new Exception("User profile request failed! {$this->providerId} returned an error: $e", 6);
            }

            // check if authenticated
            if (!$this->api->access_token) {
                throw new Exception("Authentication failed! {$this->providerId} returned an invalid access token.", 5);
            }

            // Obtain user information from the ID token
            $idToken     = $this->api->id_token;
            $accessToken = $this->api->access_token;

            $payload        = Common_Oidc_Token::decodeIdToken($idToken);
            $payload['iss'] = "accounts.google.com";
            Common_Oidc_Token::isValidIdToken($idToken, new Common_Oidc_IdToken_Payload($payload), $accessToken, $this->api->client_secret);
            Hybrid_Logger::info('Google_IdToken_Payload : ' . print_r($payload, TRUE));

            // store tokens
            $this->token("access_token", $this->api->access_token);
            $this->token("id_token", $this->api->id_token);
            $this->token("refresh_token", $this->api->refresh_token);
            $this->token("token_type", $this->api->token_type);
            $this->token("expires_in", $this->api->access_token_expires_in);
            $this->token("expires_at", $this->api->access_token_expires_at);

            // set user connected locally
            $this->setUserConnected();
        } catch (Exception $exc) {

            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            throw $exc;
        }
    }

    /**
     * load the user profile from the IDp api client
     */
    function getUserProfile()
    {
        // refresh tokens if needed 
        $this->refreshToken();

        // ask google api for user infos
        $this->api->curl_header = array('Authorization: ' . $this->api->token_type . ' ' . $this->api->access_token,);
        $response               = $this->api->api("https://www.googleapis.com/plus/v1/people/me/openIdConnect");

        if (!isset($response->sub) || isset($response->error)) {
            throw new Exception("User profile request failed! {$this->providerId} returned an invalid response.", 6);
        }

        // ユーザ情報セット
        $this->user->profile->identifier    = (property_exists($response, 'sub')) ? $response->sub : "";
        $this->user->profile->firstName     = (property_exists($response, 'given_name')) ? $response->given_name : "";
        $this->user->profile->lastName      = (property_exists($response, 'family_name')) ? $response->family_name : "";
        $this->user->profile->displayName   = (property_exists($response, 'name')) ? $response->name : "";
        $this->user->profile->photoURL      = (property_exists($response, 'picture')) ? $response->picture : "";
        $this->user->profile->profileURL    = (property_exists($response, 'profile')) ? $response->profile : "";
        $this->user->profile->gender        = (property_exists($response, 'gender')) ? $response->gender : "";
        $this->user->profile->language      = (property_exists($response, 'locale')) ? $response->locale : "";
        $this->user->profile->email         = (property_exists($response, 'email')) ? $response->email : "";
        $this->user->profile->emailVerified = (property_exists($response, 'email_verified')) ? $response->email_verified : "";

        return $this->user->profile;
    }

    /**
     * state を作成し、セッションに保存する
     *
     * @return string
     */
    protected function _createState()
    {
        $state = md5(uniqid(mt_rand(), true));
        Hybrid_Auth::storage()->set("hauth_session.state", $state);
        return $state;
    }

}