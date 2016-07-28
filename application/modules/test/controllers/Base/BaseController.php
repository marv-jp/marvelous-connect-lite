<?php

abstract class Test_Base_BaseController extends Zend_Controller_Action
{
    const HEADER_IF_MODIFIED_SINCE = 'If-Modified-Since';

    protected $_api;
    protected $_applicationId;
    protected $_applicationSecret;
    protected $_applicationUserId;
    protected $_authHeader;
    protected $_requestBody;
    protected $_lastRequest;
    protected $_responseBodyAsJson;
    protected $_apiMode;
    protected $_accessToken;
    protected $_idToken;
    protected $_headers = array();

    /**
     * 任意ヘッダをセット
     * 
     * @param string $k ヘッダ名
     * @param string $v ヘッダ値
     */
    protected function _addHeader($k, $v)
    {
        $this->_headers[$k] = $v;
    }

    protected function _request($httpMethod, $endPointUrl, $queryData = NULL, $apiMode = Misp_ApiMode::API_MODE_TRUSTED)
    {
        $this->_authHeader = $this->_getOAuthHeaders($endPointUrl, $httpMethod, $queryData);

        // HTTP通信クライアントの取得とパラメータのセット
        $client = new Zend_Http_Client($endPointUrl);
        $client->setMethod($httpMethod);

        // content-typeの設定
        //   ToDo:ダサい
        if (in_array($this->_api, array('createToken', 'appCreateToken')) && Zend_Http_Client::POST == $httpMethod) {
            $client->setHeaders('content-type', 'application/x-www-form-urlencoded');
            $client->setHeaders('Authorization', $this->_authHeader);
        } else if (Misp_ApiMode::API_MODE_TRUSTED == $apiMode || Misp_ApiMode::API_MODE_TRUSTED_PROXY == $apiMode) {
            $client->setHeaders('content-type', 'application/json; charset=utf8');
            $client->setHeaders('Authorization', $this->_authHeader);
        }
        // 任意ヘッダ追加処理
        foreach ($this->_headers as $k => $v) {
            $client->setHeaders($k, $v);
        }
        
        $client->setRawData($this->_requestBody);

        // リクエストの送信とレスポンスの取得
        $zendResponse = $client->request();

        // 生リクエストを取得(ヘッダ、ボディ込み)
        $this->_lastRequest = $client->getLastRequest();

        // Zendのレスポンスオブジェクト種別を判定し、標準化のレスポンスオブジェクトに変換する
        $response = NULL;
        if ($zendResponse instanceof Zend_Http_Response) {
            $response = Common_Http_Response::fromObject($zendResponse);
        } elseif ($zendResponse instanceof Zend_Http_Response_Stream) {
            $response = Common_Http_Response_Stream::fromObject($zendResponse);
        } else {
            throw new Common_Exception_IllegalConfig();
        }

        if (FALSE !== strpos('application/json', $zendResponse->getHeader('content-type'))) {
            try {
                $this->_responseBodyAsJson = print_r(Zend_Json::decode($response->getBody()), 1);
            } catch (Exception $exc) {
                $this->_responseBodyAsJson = "Not Decoded";
            }
        }

        return $response;
    }

    /**
     * リクエストヘッダからOAuth部分を抜き出し、OAuthヘッダを生成＆返却します。
     * 
     * @param string $endPointUrl エンドポイントURL
     * @param string $httpMethod プラットフォームAPIに対するリクエストメソッド
     * @param array $queryData リクエストパラメータの連想配列(key => value)
     * @return string OAuthヘッダ
     */
    protected function _getOAuthHeaders($endPointUrl, $httpMethod, $queryData)
    {
        $accessTokenWk       = $this->_accessToken;
        $accessTokenSecretWk = $this->_idToken;

        // accessToken, accessTokenSecret が存在しない場合は、
        // ConsumerRequestモードとして OAuthTokenインスタンスを生成しない
        $token = '';
        if ($accessTokenWk && $accessTokenSecretWk) {
            $token = new OAuthToken($accessTokenWk, $accessTokenSecretWk);
        }

        $oauthConsumer   = new OAuthConsumer($this->_applicationId, $this->_applicationSecret);
        $signatureMethod = new OAuthSignatureMethod_HMAC_SHA1();

        $oauthRequest = OAuthRequest::from_consumer_and_token(
                        $oauthConsumer, $token, $httpMethod, $endPointUrl, $queryData
        );

        $oauthRequest->sign_request($signatureMethod, $oauthConsumer, $token);

        // get header
        $authorizationHeaderString = $this->_createAuthHeaderString($oauthRequest);



        $authorizationHeader = substr($authorizationHeaderString, strlen('Authorization:'));

        return $authorizationHeader;
    }

    /**
     * カンマとイコールの前後に空白スペースがあると、OAuthの受け側でうまくヘッダパースができなかった
     */
    protected function _createAuthHeaderString($oauthRequest, $realm = null)
    {
        $first = true;
        if ($realm) {
            $out   = 'Authorization: OAuth realm = "' . OAuthUtil::urlencode_rfc3986($realm) . '"';
            $first = false;
        } else {
            $out = 'Authorization: OAuth';
        }

        foreach ($oauthRequest->get_parameters() as $k => $v) {
            if ((substr($k, 0, 5) != "oauth") && (substr($k, 0, 6) != "xoauth"))
                continue;
            if (is_array($v)) {
                throw new OAuthException('Arrays not supported in headers');
            }
            $out .= ($first) ? ' ' : ',';
            $out .= OAuthUtil::urlencode_rfc3986($k) .
                    '="' .
                    OAuthUtil::urlencode_rfc3986($v) .
                    '"';
            $first = false;
        }
        return $out;
    }

}
