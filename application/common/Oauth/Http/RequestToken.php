<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestToken
 *
 * @author ohara
 */
class Common_Oauth_Http_RequestToken extends Zend_Oauth_Http_RequestToken
{

    /**
     * Initiate a HTTP request to retrieve a Request Token.
     *
     * @return Common_Oauth_Token_Request
     */
    public function execute()
    {
        $params   = $this->assembleParams();
        $response = $this->startRequestCycle($params);
        $return   = new Common_Oauth_Token_Request($response);
        return $return;
    }

}
