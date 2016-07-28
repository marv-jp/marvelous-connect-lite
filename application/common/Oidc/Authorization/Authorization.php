<?php

class Common_Oidc_Authorization_Authorization
{
    /** @var string response_type(Basic Profile) */
    const RESPONSE_TYPE_BASIC = 'code';

    /** @var string response_type(Implicit Profile) */
    const RESPONSE_TYPE_IMPLICIT = 'id_token token';

    /** @var string token_type(Bearer) */
    const TOKEN_TYPE_BEARER = 'Bearer';

    /**
     * @var string response_type varchar(255)
     */
    protected $_responseType = null;

    /**
     * @var string client_id varchar(255)
     */
    protected $_clientId = null;

    /**
     * @var string redirect_uri varchar(255)
     */
    protected $_redirectUri = null;

    /**
     * @var string scope varchar(255)
     */
    protected $_scope = null;

    /**
     * @var string state varchar(255)
     */
    protected $_state = null;

    /**
     * @var string nonce varchar(255)
     */
    protected $_nonce = null;

    /**
     * @var string max_age varchar(255)
     */
    protected $_maxAge = null;

    /**
     * @var string code varchar(255)
     */
    protected $_code = null;

    /**
     * @var string access_token varchar(255)
     */
    protected $_accessToken = null;

    /**
     * @var string token_type varchar(255)
     */
    protected $_tokenType = null;

    /**
     * @var string id_token varchar(255)
     */
    protected $_idToken = null;

    /**
     * @var string expires_in varchar(255)
     */
    protected $_expiresIn = null;

    /**
     * @var string platform_id varchar(255)
     */
    protected $_platformId = null;

    /**
     * @var string refresh_token varchar(255)
     */
    protected $_refreshToken = null;

    /**
     * 自動生成コンストラクタ
     *
     * @param mixed モデルデータ
     */
    public function __construct($options = null)
    {
        if (is_array($options) || is_object($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * モデルのプロパティにデータをセットする
     *
     * @param mixed モデルデータ
     * @return Common_Oidc_IdToken_Payload このクラスのオブジェクト
     */
    public function setOptions($options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            // 正規表現でスネークケース方式から、キャメルケース方式に名前を変換            
            $method = 'set' . ucfirst(preg_replace_callback('/_(.)/', function($m) {return strtoupper($m[1]);}, $key));
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getResponseType()
    {
        return $this->_responseType;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setResponseType($responseType)
    {
        $this->_responseType = $responseType;
    }

    /**
     * 
     * @return string
     */
    public function getClientId()
    {
        return $this->_clientId;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setClientId($clientId)
    {
        $this->_clientId = $clientId;
    }

    /**
     * 
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->_redirectUri;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setRedirectUri($redirectUri)
    {
        $this->_redirectUri = $redirectUri;
    }

    /**
     * 
     * @return string
     */
    public function getScope()
    {
        return $this->_scope;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setScope($scope)
    {
        $this->_scope = $scope;
    }

    /**
     * 
     * @return string
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setState($state)
    {
        $this->_state = $state;
    }

    /**
     * 
     * @return string
     */
    public function getNonce()
    {
        return $this->_nonce;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setNonce($nonce)
    {
        $this->_nonce = $nonce;
    }

    /**
     * 
     * @return string
     */
    public function getMaxAge()
    {
        return $this->_maxAge;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setMaxAge($maxAge)
    {
        $this->_maxAge = $maxAge;
    }

    /**
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setCode($code)
    {
        $this->_code = $code;
    }

    /**
     * 
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setAccessToken($accessToken)
    {
        $this->_accessToken = $accessToken;
    }

    /**
     * 
     * @return string
     */
    public function getTokenType()
    {
        return $this->_tokenType;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setTokenType($tokenType)
    {
        $this->_tokenType = $tokenType;
    }

    /**
     * 
     * @return string
     */
    public function getIdToken()
    {
        return $this->_idToken;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setIdToken($idToken)
    {
        $this->_idToken = $idToken;
    }

    /**
     * 
     * @return string
     */
    public function getExpiresIn()
    {
        return $this->_expiresIn;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setExpiresIn($expiresIn)
    {
        $this->_expiresIn = $expiresIn;
    }

    /**
     * 
     * @return string
     */
    public function getPlatformId()
    {
        return $this->_platformId;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setPlatformId($platformId)
    {
        $this->_platformId = $platformId;
    }

    /**
     * 
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->_refreshToken;
    }

    /**
     * 
     * @param string $responseType
     */
    public function setRefreshToken($refreshToken)
    {
        $this->_refreshToken = $refreshToken;
    }

    /**
     * モデルオブジェクトを連想配列にして返す。
     *
     * @return array モデルオブジェクトの連想配列
     */
    public function toArray()
    {
        $memberArray = array();

        // クラスプロパティを取得
        $properties = get_class_vars(__CLASS__);

        foreach ($properties as $property => $default) {

            // getterメソッド名を生成
            //   先頭の_を削除して
            //   大文字にする
            $camelProperty = ucfirst(str_replace('_', '', $property));
            $getter        = 'get' . $camelProperty;

            // 連想配列のキーを生成(キャメル―ケース→スネークケース)
            $snakeName = preg_replace("/([A-Z])/", "_$1", $camelProperty);
            $snakeName = strtolower($snakeName);
            $snakeName = ltrim($snakeName, "_");

            $memberArray[$snakeName] = $this->$getter();
        }

        return $memberArray;
    }

}
