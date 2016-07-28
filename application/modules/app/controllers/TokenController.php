<?php

class App_TokenController extends App_BaseController
{

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

    public function postAction()
    {
        // リクエスト取得
        $request = $this->getRequest();
        try {
            // APIモードチェック
            $this->_checkAppApiMode();

            // grant_typeが正しいことを確認
            $grantType = $request->getParam('grant_type');
            if ('password' != $grantType) {
                throw new Common_Exception_IllegalParameter('パラメータが不正です');
            }

            // ユーザ識別子をリクエストパラメータから取得
            $id = $request->getParam('username');

            // idからアプリケーションIDとワールドIDを取得
            list($applicationWorldId, $applicationUserId) = Misp_Util::pickUpApplicationUserIdAndApplicationWorldId($id);

            // アプリケーションモデルの取得
            $applicationModel = $this->_generateApplicationModel();

            // パラメータ取得
            $password = $request->getParam('password');
            $nonce    = $request->getParam('nonce', '');

            // ロジック(authenticateApplicationUser)へのオプション項目
            $options = array();

            // オプションパラメータがある場合、取得
            if (strlen($request->getParam('max_age'))) {
                $maxAge            = (int) $request->getParam('max_age');
                $options['maxAge'] = $maxAge;
            } else {
                //ない場合、application.iniから取得
                $config = Zend_Registry::get('misp');
                $maxAge = (int) $config['idToken']['expTime'];
            }
            if (strlen($request->getParam('platform_id'))) {
                $options['platformId'] = $request->getParam('platform_id');
            }

            // アプリケーションユーザモデルにセット
            $applicationUserModel = new Application_Model_ApplicationUser();
            $applicationUserModel->setApplicationId($applicationModel->getApplicationId());
            $applicationUserModel->setApplicationUserId($applicationUserId);
            $applicationUserModel->setApplicationWorldId($applicationWorldId);
            $applicationUserModel->setPassword($password);

            // IDトークンモデルにセット
            $oidcIdTokenPayload = new Common_Oidc_IdToken_Payload();
            $oidcIdTokenPayload->setAud($applicationModel->getApplicationId());
            $oidcIdTokenPayload->setSub(Misp_Util::normalizeUserId($applicationUserModel));
            $oidcIdTokenPayload->setNonce($nonce);
            // 時間のセット
            $time               = time();
            $oidcIdTokenPayload->setExp($time + $maxAge);
            $oidcIdTokenPayload->setIat($time);

            // authenticateApplicationUserメソッドを呼ぶ
            // 引数は アプリケーションモデル、アプリケーションユーザモデル
            $logicApplicationUser = new Logic_ApplicationUser();
            $logicApplicationUser->setApplicationUserLogic($logicApplicationUser);

            $return = $logicApplicationUser->authenticateApplicationUser($applicationModel, $applicationUserModel, $oidcIdTokenPayload, $options);

            // メソッドから返ってきた、アプリケーションユーザーモデルから戻り値を取得し
            // 連想配列の形にする
            $returnData                 = array();
            $returnData['access_token'] = $return->getAccessToken();
            $returnData['token_type']   = Common_Oidc_Authorization_Authorization::TOKEN_TYPE_BEARER;
            // expires_inをIDトークンから取得する(exp - iat) 
            $idToken                    = $return->getIdToken();
            $returnData['expires_in']   = Common_Oidc_Token::calcExpiresIn($idToken);
            $returnData['id_token']     = $idToken;

            // 正常終了、連想配列はJSON形式に変更して返す
            $response = $this->getResponse();
            $response->setHttpResponseCode(200);
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody(Zend_Json::encode($returnData));
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
