<?php

class Test_MispController extends Test_Base_BaseController
{
    private $_config;

    public function init()
    {
        $this->_config      = Zend_Registry::get('misp');
        $this->view->assign('path', $this->_config['jmeter']['path']);
        $this->_accessToken = '';
        $this->_idToken     = '';
    }

    /**
     * シナリオテスト用のアクション
     * JMeterのテストケース /marvelous/federation の redirect_uri で指定されるアクション
     */
    public function callbackAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);

        $this->_forward('client', 'Misp', 'test', $_REQUEST);
    }

    /**
     * シナリオテスト用のアクション
     * クライアントダミー
     */
    public function clientAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);

        $accessToken = $_REQUEST['access_token'];
        $idToken     = $_REQUEST['id_token'];

        $params                 = array();
        $params['access_token'] = $accessToken;
        $params['id_token']     = $idToken;

        // ステージングに対してのテストを行う
        $endPointUrl                                         = $this->_config['jmeter']['path'] . '/marvelous/people/@me/@self?' . http_build_query($params);
        $queryData                                           = array();
        $queryData['entry'][0]['apps'][0]['value']['userId'] = '111:' . md5(microtime());
        $queryData['entry'][0]['apps'][0]['value']['appId']  = '00000';
        $this->_requestBody                                  = Zend_Json::encode($queryData);

        $response = $this->_request(Common_Http_Client::PUT, $endPointUrl, '');

        $params['response_type'] = 'id_token token';
        $params['client_id']     = '00000';
        $params['redirect_uri']  = $this->_config['jmeter']['path'] . '/test/misp/callback';
        $params['scope']         = 'openid';
        $params['state']         = 'ddd';
        $params['nonce']         = 'nnnnonce';
        $this->_redirect($this->_config['idToken']['iss'] . '/marvelous/authorization/login?' . http_build_query($params));
    }

    /**
     * シナリオテスト用のアクション
     * JMeterのテストケース /marvelous/federation の redirect_uri で指定されるアクション
     */
    public function mispCallbackAction()
    {
        $this->_helper->layout->disableLayout();

        $request     = $this->getRequest();
        $accessToken = $request->getParam('access_token');
        $idToken     = $request->getParam('id_token');
        $code        = $request->getParam('code');
        $this->view->assign('accessToken', $accessToken);
        $this->view->assign('idToken', $idToken);
        $this->view->assign('code', $code);
    }

    /**
     * MISP連携一覧画面にリダイレクトするテスト画面便宜用のアクション
     */
    public function mispCallbackFederationListAction()
    {
        $this->_helper->layout->disableLayout();

        $request     = $this->getRequest();
        $accessToken = $request->getParam('access_token');
        $idToken     = $request->getParam('id_token');
//        $code        = $request->getParam('code');

        $params['response_type'] = 'id_token token';
        $params['client_id']     = '00000';
        $params['redirect_uri']  = $this->_config['jmeter']['path'] . '/test/misp/misp-callback-federation-list';
        $params['scope']         = 'openid';
        $params['state']         = 'ddd';
        $params['nonce']         = 'nnnnonce';
        $params['access_token']  = $accessToken;
        $params['id_token']      = $idToken;
        $this->_redirect($this->_config['idToken']['iss'] . '/marvelous/authorization/login?' . http_build_query($params));
    }

    /**
     * シナリオテスト用のアクション
     * RP
     */
    public function mispRpAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);

        $request   = $this->getRequest();
        $getParams = $request->getParams();


        // 必須パラメータ取得
        $accessToken       = $request->getParam('access_token');
        $idToken           = $request->getParam('id_token');
        $applicationUserId = $_REQUEST['application_user_id'];
        $clientId          = $_REQUEST['client_id'];
        $requestApiMode    = Misp_ApiMode::API_MODE_TRUSTED;

        // 任意パラメータ
        $apiMode           = 'trustedproxy';   // 'proxy' or 'trustedproxy'
        $processFlg        = 'create';  // 'create' or 'update'
        $runCount          = 1;
        $resultFlg         = 'redirect';  // 'redirect' or 'print'
        $username          = NULL;
        $password          = NULL;
        $applicationSecret = '';
        $isPreTest         = FALSE;
        $appdata           = array();

        // 任意パラメータが存在する場合、その値を使用
        if (isset($getParams['api_mode'])) {
            $apiMode = $request->getParam('api_mode');
        }
        if (isset($getParams['process_flg'])) {
            $processFlg = $request->getParam('process_flg');
        }
        if (isset($getParams['run_count'])) {
            $runCount = (int) $request->getParam('run_count');
        }
        if (isset($getParams['result_flg'])) {
            $resultFlg = $request->getParam('result_flg');
        }
        if (isset($getParams['username'])) {
            $username = $request->getParam('username');
        }
        if (isset($getParams['password'])) {
            $password = $request->getParam('password');
        }
        if (isset($getParams['application_secret'])) {
            $applicationSecret = $request->getParam('application_secret');
        }
        // 事前登録テストモードか(ワールドIDをpreにする)
        if (isset($getParams['is_pre_test'])) {
            $isPreTest = ($request->getParam('is_pre_test')) ? TRUE : FALSE;
        }
        if (isset($getParams['appdata'])) {
            parse_str($getParams['appdata'], $appdata);
        }

        Common_Log::getInternalLog()->info(sprintf('<TEST> mispRp | apiMode=%s | processFlg=%s | runCount=%s | resultFlg=%s | username=%s | password=%s | applicationSecret=%s | appdata=%s', $apiMode, $processFlg, $runCount, $resultFlg, $username, $password, $applicationSecret, print_r($appdata, 1)));

        // ヘッダを設定
        $this->_applicationId     = $clientId;
        $this->_applicationSecret = $applicationSecret;
        $this->_accessToken       = $accessToken;
        $this->_idToken           = $idToken;

        $params                 = array();
        $params['access_token'] = $accessToken;
        $params['id_token']     = $idToken;

        // ステージングに対してのテストを行う
        $endPointUrl         = $this->_config['idToken']['iss'] . '/marvelous/people/@me/@self?' . http_build_query($params);
        $queryData           = array();
        $entryData           = array();
        $appsData            = array();
        $appsData[]['value'] = array(
            'userId' => $applicationUserId,
            'appId'  => $clientId,
        );
        // 任意パラメータがある場合設定する
        if (isset($getParams['username'])) {
            $appsData[0]['value']['username'] = $username;
        }
        if (isset($getParams['password'])) {
            $appsData[0]['value']['password'] = $password;
        }

        // 事前登録テストモードのときはワールドIDを'pre'にする
        $worldId = '';
        if ($isPreTest) {
            $worldId = 'pre:';
        } else {
            $worldId = 'world1:';
        }

        $appsData[]['value'] = array(
            'userId' => $worldId . $applicationUserId . '_second',
            'appId'  => $clientId,
        );

        if ($appdata) {
            foreach ($appdata as $key => $value) {
                $appsData[1]['value']['appData'][$key] = $value;
            }
        }

        $entryData['apps']    = $appsData;
        $queryData['entry'][] = $entryData;

        $this->_requestBody = Zend_Json::encode($queryData);

        // putSelf実行
        $response = $this->_request(Common_Http_Client::PUT, $endPointUrl, $trustedData, $requestApiMode);

        // runCount分追加処理
        for ($i = 1; $i < $runCount; $i++) {
            // データ変更して再実行
            $body                                                = Zend_Json::decode($response->getBody());
            $params['access_token']                              = $body['entry'][0]['accessToken'];
            $params['id_token']                                  = $body['entry'][0]['idToken'];
            $endPointUrl                                         = $this->_config['idToken']['iss'] . '/marvelous/people/@me/@self?' . http_build_query($params);
            $queryData['entry'][0]['apps'][0]['value']['userId'] = $applicationUserId . '_third_' . runCount . '_' . md5(microtime());
            // 任意パラメータがある場合設定する
            if (strlen($username)) {
                $queryData['entry'][0]['apps'][0]['value']['displayName'] = $username . '_third' . runCount;
            }
            if (strlen($password)) {
                $queryData['entry'][0]['apps'][0]['value']['password'] = $password . '_third' . runCount;
            }
            $queryData['entry'][0]['apps'][1]['value']['userId'] = $applicationUserId . '_fourth' . runCount;

            $this->_requestBody = Zend_Json::encode($queryData);

            // putSelf再実行
            $response = $this->_request(Common_Http_Client::PUT, $endPointUrl, $queryData);
        }

        if ('redirect' == $resultFlg) {
            $body        = Zend_Json::decode($response->getBody());
            $accessToken = $body['entry'][0]['accessToken'];
            $idToken     = $body['entry'][0]['idToken'];

            $params['response_type'] = 'id_token token';
            $params['client_id']     = $clientId;
            $params['redirect_uri']  = $this->_config['jmeter']['path'] . '/test/misp/misp-callback';
            $params['scope']         = 'openid';
            $params['state']         = 'ddd';
            $params['nonce']         = 'nnnnonce';
            $params['access_token']  = $accessToken;
            $params['id_token']      = $idToken;

            $this->_redirect($this->_config['idToken']['iss'] . '/marvelous/authorization/login?' . http_build_query($params));
        }
        if ('redirect_basic' == $resultFlg) {
            $body        = Zend_Json::decode($response->getBody());
            $accessToken = $body['entry'][0]['accessToken'];
            $idToken     = $body['entry'][0]['idToken'];

            $params['response_type'] = 'code';
            $params['client_id']     = $clientId;
            $params['redirect_uri']  = $this->_config['jmeter']['path'] . '/test/misp/misp-callback';
            $params['scope']         = 'openid';
            $params['state']         = 'ddd';
            $params['nonce']         = 'nnnnonce';
            $params['access_token']  = $accessToken;
            $params['id_token']      = $idToken;

            $this->_redirect($this->_config['idToken']['iss'] . '/marvelous/authorization/login?' . http_build_query($params));
        }
        if ('print' == $resultFlg) {
            print_r('response_status:' . $response->getStatus() . '  ');
            try {
                print_r($response->getBody());
                // 正常時は整形データ表示
                print_r(Zend_Json::decode($response->getBody()));
            } catch (Exception $exc) {
                // エラー時は、エラーメッセージ表示
                print_r(sprintf('error_message:%s', $exc->getMessage()));
            }
        }
    }

    /**
     * シナリオテスト用のアクション
     * RP
     */
    public function mispRpErrorAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);

        $request = $this->getRequest();

        $accessToken       = $request->getParam('access_token');
        $idToken           = $request->getParam('id_token');
        $applicationUserId = $_REQUEST['application_user_id'];
        $clientId          = $_REQUEST['client_id'];

        $params                 = array();
        $params['access_token'] = $accessToken;
        $params['id_token']     = $idToken;

        // ステージングに対してのテストを行う
        $endPointUrl          = $this->_config['jmeter']['path'] . '/marvelous/people/@me/@self?' . http_build_query($params);
        $queryData            = array();
        $entryData            = array();
        $appsData             = array();
        $appsData[]['value']  = array(
            'userId'   => $applicationUserId . md5(microtime()),
            'appId'    => $clientId,
            'password' => 'error',
        );
        $entryData['apps']    = $appsData;
        $queryData['entry'][] = $entryData;

        $this->_requestBody = Zend_Json::encode($queryData);

        $response = $this->_request(Common_Http_Client::PUT, $endPointUrl, '');

        $body = Zend_Json::decode($response->getBody());

        print_r($body, TRUE);
    }

    /**
     * シナリオテスト用のアクション
     * ユーザ取得RP
     */
    public function mispGetAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);

        $request = $this->getRequest();

        $accessToken = $request->getParam('access_token');
        $idToken     = $request->getParam('id_token');

        $params                 = array();
        $params['access_token'] = $accessToken;
        $params['id_token']     = $idToken;

        // ステージングに対してのテストを行う
        $endPointUrl = $this->_config['jmeter']['path'] . '/marvelous/people/@me/@self?' . http_build_query($params);

        $response     = $this->_request(Common_Http_Client::GET, $endPointUrl, '');
        $responseData = Zend_Json::decode($response->getBody());

        print_r($responseData);
    }

    /**
     * シナリオテスト用のダミーOPアクション
     */
    public function discoverAction()
    {
        $request    = $this->getRequest();
        $platformId = $request->getParam('platform_id');

        $this->_helper->layout->disableLayout();
        $this->view->assign('platformId', $platformId);
    }

    public function serverAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);

        $request        = $this->getRequest();
        $platformId     = $request->getParam('platform_id');
        $platformUserId = $this->_config['jmeter']['path'] . '/test/misp/valid/' . mt_rand() . microtime(TRUE);
        $returnToAction = 'auth';

        // プラットフォームユーザを固定したいテスト用(同じプラットフォームユーザに対して複数のアプリケーションをつけるテストなど)
        if ('Dummy3' == $platformId) {
            $platformUserId = $this->_config['jmeter']['path'] . '/test/misp/valid/';
        }

        if ('Mixi' == $platformId) {
            $returnToAction = 'authMixi';
        }

        $params                                        = array();
        $params['hauth.done']                          = $platformId;
        $params['openid.mode']                         = 'id_res';
        $params['openid.claimed_id']                   = $platformUserId;
        $params['openid.identity']                     = 'https://id.mixi.jp/19742911111';
        $params['openid.op_endpoint']                  = 'https://mixi.jp/openid_server.pl';
        $params['openid.return_to']                    = sprintf('%s/marvelous/federation/%s?hauth.done=%s', $this->_config['idToken']['iss'], $returnToAction, $platformId);
        $params['openid.response_nonce']               = 'id_res';
        $params['openid.assoc_handle']                 = 'id_res';
        $params['openid.ax.type.namePerson_friendly']  = 'id_res';
        $params['openid.ns.sreg']                      = 'id_res';
        $params['openid.ax.value.namePerson_friendly'] = 'id_res';
        $params['openid.ns.ax']                        = 'id_res';
        $params['openid.sreg.nickname']                = 'id_res';
        $params['openid.ax.mode']                      = 'id_res';
        $params['openid.ns']                           = 'id_res';
        $params['openid.signed']                       = 'mode,claimed_id,identity,op_endpoint,return_to,response_nonce,assoc_handle,ax.type.namePerson_friendly,ns.sreg,ax.value.namePerson_friendly,ns.ax,sreg.nickname,ax.mode';
        $params['openid.sig']                          = 'cRIG6BfgF3iA8ehwW4dd/kpe0lM=';

        $this->_redirect(sprintf('%s/marvelous/federation/%s?%s', $this->_config['idToken']['iss'], $returnToAction, http_build_query($params)));
    }

    public function validAction()
    {
        $this->_helper->layout->disableLayout();
    }

    public function checkAuthenticationAction()
    {
        $this->_helper->layout->disableLayout();
    }

}
