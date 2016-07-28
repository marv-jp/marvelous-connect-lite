<?php

class Marvelous_AuthorizationController extends Zend_Controller_Action
{
    private $_config                           = null;
    private $_applicationId                    = null;
    private $_accessToken                      = null;
    private $_idToken                          = null;
    private $_scope                            = null;
    private $_nonce                            = null;
    private $_redirectUri                      = null;
    private $_state                            = null;
    private $_responseType                     = null;
    private $_maxAge                           = null;
    private $_userPlatformApplicationRelations = array();
    private $_error                            = null;
    private $_viewablePlatformType             = array(Application_Model_Platform::PLATFORM_TYPE_MAIN);

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_config = Zend_Registry::get('misp');


        $request = $this->getRequest();

        $this->_applicationId                    = $request->getParam('client_id');
        $this->_accessToken                      = $request->getParam('access_token');
        $this->_idToken                          = $request->getParam('id_token');
        $this->_nonce                            = $request->getParam('nonce');
        $this->_redirectUri                      = $request->getParam('redirect_uri');
        $this->_scope                            = $request->getParam('scope');
        $this->_state                            = $request->getParam('state');
        $this->_responseType                     = $request->getParam('response_type');
        $this->_maxAge                           = $request->getParam('max_age');
        $this->_userPlatformApplicationRelations = $request->getParam('user_platform_application_relations');

        $this->_error = $request->getParam('error');

        // 多言語設定
        $translate = new Zend_Translate(
                array(
            'adapter' => 'gettext',
            'content' => Zend_Registry::get('language_path'),
            'scan'    => Zend_Translate::LOCALE_FILENAME,
        ));

        $locale = new Zend_Locale();
        if (in_array($locale->getLanguage(), $this->_config['supported']['languages'])) {
            // 対応しているならその言語をセット
            $translate->setLocale($locale->getLanguage());
        } else {
            // サポート外の言語はenに倒す
            $translate->setLocale('en');
        }

        // 多言語設定をビューにアサイン
        $this->view->translate = $translate;
    }

    public function loginAction()
    {
        // ロジック
        $logicApplication = new Logic_Application();
        $logicUser        = new Logic_User();
        $logicUser->setUserLogic($logicUser);

        // process request
        $headers              = apache_request_headers();
        $authorizationRequest = new Akita_OpenIDConnect_Server_Request('authorization', $_SERVER, $_GET, $headers);
        $dataHandler          = new OpenIDConnect_Server_DataHandler($authorizationRequest);
        $authHandler          = new OpenIDConnect_Server_AuthorizationHandler();

        try {
            // Authorization Request チェック
            $authHandler->processAuthorizationRequest($dataHandler, array('id_token token', 'code',));


            // アプリケーション取得
            $application       = new Application_Model_Application();
            $application->setApplicationId($this->_applicationId);
            $resultApplication = $logicApplication->readApplication($application);


            // ログイン画面に表示する有効なプラットフォームを取得
            $resultPlatforms = array();
            foreach ($logicUser->readPlatform(new Application_Model_Platform()) as $p) {
                // メインプラットフォームを表示する
                if ($this->_isViewablePlatformType($p->getPlatformType())) {
                    $resultPlatforms[] = $p;
                }
            }

            if ($this->_idToken) {
                // トークン検証＆有効プラットフォーム一覧取得
                $userPlatformApplicationRelation = new Application_Model_UserPlatformApplicationRelation();
                $userPlatformApplicationRelation->setAccessToken($this->_accessToken);
                $userPlatformApplicationRelation->setIdToken($this->_idToken);
                $userPlatformApplicationRelation->setApplicationId($this->_applicationId);

                $options                                        = array();
                $options['user_platform_application_relations'] = $logicUser->readIdFederationStatus($userPlatformApplicationRelation);

                if (!empty($options['user_platform_application_relations'])) {
                    // MISP連携一覧画面を表示する
                    $this->_forward('index', 'Authorization', 'marvelous', $options);
                    return;
                }
            }
        } catch (Common_Exception_IllegalParameter $exc) {
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外が発生した場合は共通エラー画面を表示
            throw $exc;
        } catch (Common_Exception_OauthInvalidRequest $exc) {
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外が発生した場合は共通エラー画面を表示
            throw $exc;
        } catch (Common_Exception_Oidc_InvalidToken $exc) {
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // トークン認証に失敗した場合は、ログイン画面を表示する
        }

        // ログイン画面に表示する有効なプラットフォームをセット
        $this->view->platforms = $resultPlatforms;

        // ログイン画面に表示するアプリケーション名をセット
        $this->view->applicationName = $resultApplication->getApplicationName();

        // ID連携アクションへのパラメータをセット
        if ($this->getRequest()->has('serial_code') && ("1" === $this->getRequest()->getParam('serial_code'))) {
            $this->_redirectUri .= sprintf('/serial_code/%s', $this->getRequest()->getParam('serial_code'));
        }
        $this->view->queryParams = array(
            'response_type' => $this->_responseType,
            'client_id'     => $this->_applicationId,
            'redirect_uri'  => $this->_redirectUri,
            'scope'         => $this->_scope,
            'state'         => $this->_state,
            'nonce'         => $this->_nonce,
            'max_age'       => $this->_maxAge
        );
    }

    public function indexAction()
    {
        // ロジック
        $logicApplication = new Logic_Application();
        $logicUser        = new Logic_User();

        // 不正なパラメータがないかチェック するための準備
        // process request
        $headers              = apache_request_headers();
        $authorizationRequest = new Akita_OpenIDConnect_Server_Request('authorization', $_SERVER, $_GET, $headers);
        $dataHandler          = new OpenIDConnect_Server_DataHandler($authorizationRequest);
        $authHandler          = new OpenIDConnect_Server_AuthorizationHandler();

        try {
            // Authorization Request チェック
            $authHandler->processAuthorizationRequest($dataHandler, array('id_token token', 'code',));


            $federatedPlatforms = $this->_userPlatformApplicationRelations;

            if (empty($federatedPlatforms)) {
                // プラットフォーム情報が空だった場合ログイン画面を表示させる
                $this->_idToken = '';
                $this->_forward('login');
                return;
            }

            $federatedPlatformIds = array();
            foreach ($federatedPlatforms as $platform) {
                $federatedPlatformIds[] = $platform->getPlatformId();
            }

            // ログイン画面のボタンON/OFF用
            $this->view->platformIds = $federatedPlatformIds;

            // ログイン画面に表示する有効なプラットフォームをセット
            $resultPlatforms = array();
            foreach ($logicUser->readPlatform(new Application_Model_Platform()) as $p) {
                // メインプラットフォームを表示する
                if ($this->_isViewablePlatformType($p->getPlatformType())) {
                    $resultPlatforms[] = $p;
                }
            }
            $this->view->platforms = $resultPlatforms;

            // アプリケーション取得
            $application       = new Application_Model_Application();
            $application->setApplicationId($this->_applicationId);
            $resultApplication = $logicApplication->readApplication($application);

            // ログイン画面に表示するアプリケーション名をセット
            $this->view->applicationName = $resultApplication->getApplicationName();

            // ID連携アクション OR ID連携解除アクション へのパラメータをセット
            $this->view->queryParams = array(
                'response_type' => $this->_responseType,
                'client_id'     => $this->_applicationId,
                'redirect_uri'  => $this->_redirectUri,
                'scope'         => $this->_scope,
                'state'         => $this->_state,
                'nonce'         => $this->_nonce,
                'max_age'       => $this->_maxAge,
                'access_token'  => $this->_accessToken,
                'id_token'      => $this->_idToken,
            );

            // ログインプラットフォームを取得
            $userPlatformApplicationRelation = new Application_Model_UserPlatformApplicationRelation();
            $userPlatformApplicationRelation->setAccessToken($this->_accessToken);
            $userPlatformApplicationRelation->setIdToken($this->_idToken);

            $this->view->loginPlatformId = $logicUser->readUserPlatformApplicationRelationWithValidate($userPlatformApplicationRelation)->getPlatformId();
            $this->view->redirectUri     = $this->_redirectUri;
        } catch (Exception $exc) {
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // 例外が発生した場合はそのまま、ログイン画面を表示
            $this->_idToken = '';
            $this->_forward('login');
            return;
        }
    }

    /**
     * 表示可能なプラットフォーム種別かどうか
     * 
     * @param int $platformType
     * @return boolean
     */
    private function _isViewablePlatformType($platformType)
    {
        return in_array($platformType, $this->_viewablePlatformType);
    }

}
