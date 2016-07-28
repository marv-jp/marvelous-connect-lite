<?php

class Marvelous_TokenController extends Marvelous_Base_RestController
{

    public function indexAction()
    {
        // get に forward し、とりあえずエラーとさせる
        $this->_forward('get');
    }

    public function getAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(405);
        $response->setBody(Zend_Http_Response::responseCodeAsText(405));
    }

    public function deleteAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(405);
        $response->setBody(Zend_Http_Response::responseCodeAsText(405));
    }

    /*
     * トークン取得API
     */

    public function postAction()
    {
        // リクエスト取得
        $request = $this->getRequest();
        try {
            // APIモード判定
            $apiMode = Misp_ApiMode::getInstance();

            // 正常系: Trusted のみ
            if ($apiMode->isTrusted()) {

                // パラメータ取得
                $grantType    = $request->getParam('grant_type');
                $code         = $request->getParam('code');
                $redirectUri  = $request->getParam('redirect_uri');
                $refreshToken = $request->getParam('refresh_token');

                // アプリケーションID取得                
                // リクエスト情報からOAuthを構築
                $oauthRequest = OAuthRequest::from_request();
                // アプリケーションIDを取得
                $clientId     = $oauthRequest->get_parameter('oauth_consumer_key');

                // grant_typeのチェック
                // (リフレッシュトークンに対応)
                if ('authorization_code' == $grantType) {
                    $method = 'readTokenForBasic';
                } elseif ('refresh_token' == $grantType) {
                    $method = 'readTokenForRefreshToken';
                } else {
                    throw new Common_Exception_OauthInvalidGrant('invalid_grant');
                }

                // 引数のモデル
                $authorization = new Common_Oidc_Authorization_Authorization();
                $authorization->setCode($code);
                $authorization->setRedirectUri($redirectUri);
                $authorization->setClientId($clientId);
                $authorization->setRefreshToken($refreshToken);

                // Userロジック
                $logicUser            = new Logic_User();
                // Applicationロジック
                $logicApplication     = new Logic_Application();
                // ApplicationUserロジック
                $logicApplicationUser = new Logic_ApplicationUser();
                // 各種依存ロジックをセット
                $logicApplicationUser->setApplicationUserLogic($logicApplicationUser);
                $logicUser->setUserLogic($logicUser);
                $logicUser->setApplicationLogic($logicApplication);
                $logicUser->setApplicationUserLogic($logicApplicationUser);
                $logicUser->setMispApiMode($apiMode);


                // 返却データ
                $returnData = array();

                // トークン取得処理
                $userPlatformApplicationRelation = $logicUser->$method($authorization);

                // 返却データ構築
                $returnData['access_token']  = $userPlatformApplicationRelation->getAccessToken();
                $returnData['token_type']    = Common_Oidc_Authorization_Authorization::TOKEN_TYPE_BEARER;
                $returnData['refresh_token'] = $userPlatformApplicationRelation->getRefreshToken();

                // expires_inをIDトークンから取得する(exp - iat) 
                $idToken                  = $userPlatformApplicationRelation->getIdToken();
                $returnData['expires_in'] = Common_Oidc_Token::calcExpiresIn($idToken);
                $returnData['id_token']   = $idToken;

                // 正常終了、連想配列はJSON形式に変更して返す
                $response = $this->getResponse();
                $response->setHttpResponseCode(200);
                $response->setHeader('Content-Type', 'application/json');
                $response->setBody(Zend_Json::encode($returnData));
                //
            } else {
                // 共通APIモードエラー処理
                $this->_apiModeErrorProc($apiMode);
            }
        } catch (Common_Exception_Abstract $exc) {
            $response = $this->_responseException($exc);
        } catch (Exception $exc) {
            // 500エラー
            $response = $this->getResponse();
            $response->setHttpResponseCode(500);
            $response->setException($exc);
        }
    }

    public function putAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(405);
        $response->setBody(Zend_Http_Response::responseCodeAsText(405));
    }

}
