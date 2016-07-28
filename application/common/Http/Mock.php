<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mock
 *
 * @author ohara
 */
class Common_Http_Mock
{
    /** @var  Common_Http_Mock_Response response */
    protected $_response;
    
    /** @var array $_responses */
    protected $_responses = array();
    
    /** @var int $_responseCount */
    protected $_responseCount = 0;

    /** @var  Common_Http_Mock_Request request */
    protected $_request;

    /** @var string $_uri URI */
    protected $_uri;

    public function __construct()
    {
        $this->init();
    }

    public function setUri($uri, $platformName, $apiName)
    {
        $this->_uri = $uri;
    }

    public function getUri()
    {
        return $this->_uri;
    }

    public function setParameterGet($key, $value)
    {
        
    }

    public function setParameterPost($key, $value)
    {
        
    }

    /**
     *
     * @return Common_Http_Mock_Response 
     */
    public function request()
    {
        if (count($this->_responses)) {
            $reponse = $this->_responses[$this->_responseCount];
            $this->_responseCount++;
            return $reponse;
        } else {
            return $this->_response;
        }
    }

    public function init()
    {
        $this->_request = new Common_Http_Mock_Request();
        $this->_response = new Common_Http_Mock_Response();
//        $this->_response->setBody(Zend_Json::encode(array(
//                    'id'         => '100002725567846',
//                    'name'       => 'Yuuki  Ogura',
//                    'first_name' => 'Yuuki',
//                    'last_name'  => 'Ogura',
//                    'link'       => 'https://www.facebook.com/hiruandon2nd',
//                    'username'   => 'hiruandon2nd',
//                    'birthday'   => '05/16/1980',
//                    'hometown'   => array(
//                        'id'       => '143461875715406',
//                        'name'     => 'Nagoya-shi, Aichi, Japan'),
//                    'location' => array(
//                        'id'   => '185946428110545',
//                        'name' => 'Taito-ku, Tokyo, Japan'),
//                    'work' => array(
//                        'employer' => array(
//                            'id'   => '265849653448294',
//                            'name' => 'MarvelousAQL')))));
    }

    public function setRawData($data, $enctype = NULL)
    {
        
    }

    public function setMethod($method)
    {
        
    }

    public function setHeaders($name, $value = null)
    {
        
    }

    /**
     * モックレスポンスオブジェクトを返します。
     * 
     * @return Common_Http_Mock_Response 
     */
    public function getResponse($id = null)
    {
        if ($id === null) {
            return $this->_response;
        } else {
            $this->_responses[$id]  = new Common_Http_Mock_Response();
            return $this->_responses[$id];
        }
    }

    /**
     * モックリクエストオブジェクトを返します。
     * 
     * @return Common_Http_Mock_Request 
     */
    public function getRequest()
    {
        return $this->_request;
    }

}

