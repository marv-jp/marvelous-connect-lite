<?php

class App_NgwordController extends App_BaseController
{

    public function checkAction()
    {
        try {
            // APIモードチェック
            $this->_checkAppApiMode();

            $request = $this->getRequest();

            // リクエストデータ取得
            $body = Zend_Json::decode($request->getRawBody());
            if (!isset($body['data'])) {
                throw new Common_Exception_IllegalParameter('dataが存在しません');
            }

            $application = $this->_generateApplicationModel();
            $logic       = new Logic_Application();
            $result      = array('valid' => !$logic->hasNgWord($application, $body['data']));

            $response = $this->getResponse();
            $response->setHttpResponseCode(200);
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody(Zend_Json::encode($result));
        } catch (Common_Exception_Abstract $exc) {
            $response = $this->_responseException($exc);
        } catch (Exception $exc) {
            // 500エラー
            $response = $this->getResponse();
            $response->setHttpResponseCode(500);
            $response->setException($exc);
        }
    }

    public function postAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(405);
        $response->setBody(Zend_Http_Response::responseCodeAsText(405));
    }

    public function getAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(405);
        $response->setBody(Zend_Http_Response::responseCodeAsText(405));
    }

    public function putAction()
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

}
