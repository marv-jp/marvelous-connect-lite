<?php

class OpenIDConnect_Server_DataHandler extends Akita_OpenIDConnect_Server_DataHandler
{
    private $_request;
    private $_userId;
    private $_openIdConnectProfile;

    public function __construct($request)
    {
        $this->_request = $request;
    }

    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    /* abstruct functions */

    public function getRequest()
    {
        return $this->_request;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    public function getUserIdByCredentials($username, $password)
    {
        if ($username == 'fakeuser@example.com' && $password == 'fakepassword') {
            return $username;
        } else {
            return null;
        }
    }

    public function createOrUpdateAuthInfo($params)
    {
        throw new Common_Exception_NotSupported();
    }

    public function createOrUpdateAccessToken($params)
    {
        throw new Common_Exception_NotSupported();
    }

    public function getAuthInfoByCode($code)
    {
        throw new Common_Exception_NotSupported();
    }

    public function getAuthInfoByRefreshToken($refreshToken)
    {
        throw new Common_Exception_NotSupported();
    }

    public function getAccessToken($token)
    {
        throw new Common_Exception_NotSupported();
    }

    public function getAuthInfoById($authId)
    {
        throw new Common_Exception_NotSupported();
    }

    public function validateClient($clientId, $clientSecret, $grantType)
    {
        throw new Common_Exception_NotSupported();
    }

    public function validateClientById($clientId)
    {
        throw new Common_Exception_NotSupported();
    }

    public function validateUserById($userId)
    {
        throw new Common_Exception_NotSupported();
    }

    public function validateRedirectUri($clientId, $redirectUri)
    {
        $openIdConnectProfile = $this->getOpenIdConnectProfile();
        switch ($openIdConnectProfile) {
            case 'implicit':
                $validator = new OpenIDConnect_Server_Validate_Implicit_RedirectUri();
                break;
            case 'basic':
                $validator = new OpenIDConnect_Server_Validate_Basic_RedirectUri();
                break;
            default:
                $validator = new OpenIDConnect_Server_Validate_Basic_RedirectUri();
                break;
        }
        $value = array(
            'client_id'    => $clientId,
            'redirect_uri' => $redirectUri,
        );

        return $validator->isValid($value);
    }

    public function validateScope($clientId, $scope)
    {
        $openIdConnectProfile = $this->getOpenIdConnectProfile();
        switch ($openIdConnectProfile) {
            case 'implicit':
                $validator = new OpenIDConnect_Server_Validate_Implicit_Scope();
                break;
            case 'basic':
                $validator = new OpenIDConnect_Server_Validate_Basic_Scope();
                break;
            default:
                $validator = new OpenIDConnect_Server_Validate_Implicit_Scope();
                break;
        }
        return $validator->isValid($scope);
    }

    public function validateScopeForTokenRefresh($scope, $authInfo)
    {
        throw new Common_Exception_NotSupported();
    }

    public function setRefreshToken($authInfo)
    {
        throw new Common_Exception_NotSupported();
    }

    /**
     * check display param
     *
     * @param string $display display parameter
     * @return boolen
     */
    public function validateDisplay($display)
    {
        return true;
    }

    /**
     * check prompt param
     *
     * @param array $prompt prompt parameter
     * @return boolen
     */
    public function validatePrompt($prompt)
    {
        return true;
    }

    /**
     * check OpenID Request Object
     *
     * @param Akita_OpenID_Server_Request $request request object
     * @return boolen
     */
    public function validateRequestObject($request)
    {
        return true;
    }

    /**
     * check ID Token
     *
     * @param array $prompt prompt parameter
     * @param string $id_token ID Token parameter
     * @return boolen
     */
    public function validateIDToken($prompt, $id_token)
    {
        if ($prompt == array('none') && empty($id_token)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * create ID TOken for Authorization Response
     *
     * @return Akita_OpenIDConnect_Model_IDToken
     */
    public function createIdToken()
    {
        throw new Common_Exception_NotSupported();
    }

    public function setOpenIdConnectProfile($responseType)
    {
        switch ($responseType) {
            case 'id_token token':
                $this->_openIdConnectProfile = 'implicit';
                break;
            case 'code':
                $this->_openIdConnectProfile = 'basic';
                break;
            default :
                $this->_openIdConnectProfile = 'none';
                break;
        }
    }

    public function getOpenIdConnectProfile()
    {
        return $this->_openIdConnectProfile;
    }

}
