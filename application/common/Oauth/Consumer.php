<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Consumer
 *
 * @author ohara
 */
class Common_Oauth_Consumer extends Zend_Oauth_Consumer
{
    /**
     * Attempts to retrieve a Request Token from an OAuth Provider which is
     * later exchanged for an authorized Access Token used to access the
     * protected resources exposed by a web service API.
     *
     * @param  null|array $customServiceParameters Non-OAuth Provider-specified parameters
     * @param  null|string $httpMethod
     * @param  null|Common_Oauth_Http_RequestToken $request
     * @return Common_Oauth_Token_Request
     */
    public function getRequestToken(
        array $customServiceParameters = null,
        $httpMethod = null,
        Common_Oauth_Http_RequestToken $request = null
    ) {
        if ($request === null) {
            $request = new Common_Oauth_Http_RequestToken($this, $customServiceParameters);
        } elseif($customServiceParameters !== null) {
            $request->setParameters($customServiceParameters);
        }
        if ($httpMethod !== null) {
            $request->setMethod($httpMethod);
        } else {
            $request->setMethod($this->getRequestMethod());
        }
        $this->_requestToken = $request->execute();
        return $this->_requestToken;
    }
    
    /**
     * Retrieve an Access Token in exchange for a previously received/authorized
     * Request Token.
     *
     * @param  array $queryData GET data returned in user's redirect from Provider
     * @param  Common_Oauth_Token_Request Request Token information
     * @param  string $httpMethod
     * @param  Zend_Oauth_Http_AccessToken $request
     * @return Zend_Oauth_Token_Access
     * @throws Zend_Oauth_Exception on invalid authorization token, non-matching response authorization token, or unprovided authorization token
     */
    public function getAccessToken(
        $queryData,
        Common_Oauth_Token_Request $token,
        $httpMethod = null,
        Zend_Oauth_Http_AccessToken $request = null
    ) {
        $authorizedToken = new Zend_Oauth_Token_AuthorizedRequest($queryData);
        if (!$authorizedToken->isValid()) {
            require_once 'Zend/Oauth/Exception.php';
            throw new Zend_Oauth_Exception(
                'Response from Service Provider is not a valid authorized request token');
        }
        if ($request === null) {
            $request = new Zend_Oauth_Http_AccessToken($this);
        }

        // OAuth 1.0a Verifier
        if ($authorizedToken->getParam('oauth_verifier') !== null) {
            $params = array_merge($request->getParameters(), array(
                'oauth_verifier' => $authorizedToken->getParam('oauth_verifier')
            ));
            $request->setParameters($params);
        }
        if ($httpMethod !== null) {
            $request->setMethod($httpMethod);
        } else {
            $request->setMethod($this->getRequestMethod());
        }
        if (isset($token)) {
            if ($authorizedToken->getToken() !== $token->getToken()) {
                require_once 'Zend/Oauth/Exception.php';
                throw new Zend_Oauth_Exception(
                    'Authorized token from Service Provider does not match'
                    . ' supplied Request Token details'
                );
            }
        } else {
            require_once 'Zend/Oauth/Exception.php';
            throw new Zend_Oauth_Exception('Request token must be passed to method');
        }
        $this->_requestToken = $token;
        $this->_accessToken = $request->execute();
        return $this->_accessToken;
    }
}

