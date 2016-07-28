<?php

class Common_Oidc_IdToken_Payload
{
    /**
     * @var string iss varchar(255)
     */
    protected $_iss = null;

    /**
     * @var string sub varchar(255)
     */
    protected $_sub = null;

    /**
     * @var string aud varchar(255)
     */
    protected $_aud = null;

    /**
     * @var string exp varchar(255)
     */
    protected $_exp = null;

    /**
     * @var string iat varchar(255)
     */
    protected $_iat = null;

    /**
     * @var string nonce varchar(255)
     */
    protected $_nonce = null;

    /**
     * @var string at_hash varchar(255)
     */
    protected $_atHash = null;

    /**
     * @var string client_id varchar(255)
     */
    protected $_clientId = null;

    /**
     * 自動生成コンストラクタ
     *
     * @param mixed モデルデータ
     */
    public function __construct($options = null)
    {
        if (is_array($options) || is_object($options))
        {
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
        foreach ($options as $key => $value)
        {
            // 正規表現でスネークケース方式から、キャメルケース方式に名前を変換            
            $method = 'set' . ucfirst(preg_replace_callback('/_(.)/', function($m) {return strtoupper($m[1]);}, $key));
            if (in_array($method, $methods))
            {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * issプロパティーを設定する。
     *
     * @param string $iss itemIdの値
     * @return Common_Oidc_IdToken_Payload このオブジェクト
     */
    public function setIss($iss)
    {
        $this->_iss = $iss;
        return $this;
    }

    /**
     * issプロパティーを返す。
     *
     * @return string issの値
     */
    public function getIss()
    {
        return $this->_iss;
    }

    /**
     * subプロパティーを設定する。
     *
     * @param string $sub subの値
     * @return Common_Oidc_IdToken_Payload このオブジェクト
     */
    public function setSub($sub)
    {
        $this->_sub = $sub;
        return $this;
    }

    /**
     * subプロパティーを返す。
     *
     * @return string subの値
     */
    public function getSub()
    {
        return $this->_sub;
    }

    /**
     * audプロパティーを設定する。
     *
     * @param string $aud audの値
     * @return Common_Oidc_IdToken_Payload このオブジェクト
     */
    public function setAud($aud)
    {
        $this->_aud = $aud;
        return $this;
    }

    /**
     * audプロパティーを返す。
     *
     * @return string audの値
     */
    public function getAud()
    {
        return $this->_aud;
    }

    /**
     * expプロパティーを設定する。
     *
     * @param string $exp expの値
     * @return Common_Oidc_IdToken_Payload このオブジェクト
     */
    public function setExp($exp)
    {
        $this->_exp = $exp;
        return $this;
    }

    /**
     * expプロパティーを返す。
     *
     * @return string expの値
     */
    public function getExp()
    {
        return $this->_exp;
    }

    /**
     * iatプロパティーを設定する。
     *
     * @param string $iat iatの値
     * @return Common_Oidc_IdToken_Payload このオブジェクト
     */
    public function setIat($iat)
    {
        $this->_iat = $iat;
        return $this;
    }

    /**
     * iatプロパティーを返す。
     *
     * @return string iatの値
     */
    public function getIat()
    {
        return $this->_iat;
    }

    /**
     * nonceプロパティーを設定する。
     *
     * @param string $nonce nonceの値
     * @return Common_Oidc_IdToken_Payload このオブジェクト
     */
    public function setNonce($nonce)
    {
        $this->_nonce = $nonce;
        return $this;
    }

    /**
     * nonceプロパティーを返す。
     *
     * @return string nonceの値
     */
    public function getNonce()
    {
        return $this->_nonce;
    }

    /**
     * at_hashプロパティーを設定する。
     *
     * @param string $atHash at_hashの値
     * @return Common_Oidc_IdToken_Payload このオブジェクト
     */
    public function setAtHash($atHash)
    {
        $this->_atHash = $atHash;
        return $this;
    }

    /**
     * at_hashプロパティーを返す。
     *
     * @return string at_hashの値
     */
    public function getAtHash()
    {
        return $this->_atHash;
    }

    /**
     * client_idプロパティーを設定する。
     *
     * @param string $clientId client_idの値
     * @return Common_Oidc_IdToken_Payload このオブジェクト
     */
    public function setClientId($clientId)
    {
        $this->_clientId = $clientId;
        return $this;
    }

    /**
     * client_idプロパティーを返す。
     *
     * @return string client_idの値
     */
    public function getClientId()
    {
        return $this->_clientId;
    }

    /**
     * モデルオブジェクトを連想配列にして返す。
     *
     * @return array モデルオブジェクトの連想配列
     */
    public function toArray()
    {
        $memberArray              = array();
        $memberArray['iss']       = $this->getIss();
        $memberArray['sub']       = $this->getSub();
        $memberArray['aud']       = $this->getAud();
        $memberArray['exp']       = $this->getExp();
        $memberArray['iat']       = $this->getIat();
        $memberArray['nonce']     = $this->getNonce();
        $memberArray['at_hash']   = $this->getAtHash();
        $memberArray['client_id'] = $this->getClientId();
        return $memberArray;
    }

}
