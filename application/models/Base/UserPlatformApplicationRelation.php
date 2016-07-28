<?php
/**
 * 自動生成ファイル
 *
 * CreateDataModelLogicで自動生成されたファイル
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */


/**
 * ユーザプラットフォームアプリケーション関連
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_UserPlatformApplicationRelation
{

    const CLASS_NAME = 'Application_Model_Base_UserPlatformApplicationRelation';

    /**
     * @var float ユーザID PK:bigint_unsigned
     */
    protected $_userId = null;

    /**
     * @var string プラットフォームユーザID PK:varchar(255)
     */
    protected $_platformUserId = null;

    /**
     * @var string プラットフォームID PK:varchar(191)
     */
    protected $_platformId = null;

    /**
     * @var string アプリケーションID PK:varchar(11)
     */
    protected $_applicationId = null;

    /**
     * @var string 認可コード varchar(64)
     */
    protected $_authorizationCode = null;

    /**
     * @var string アクセストークン varchar(255)
     */
    protected $_accessToken = null;

    /**
     * @var string IDトークン text
     */
    protected $_idToken = null;

    /**
     * @var string リフレッシュトークン varchar(255)
     */
    protected $_refreshToken = null;

    /**
     * @var string 作成日時 datetime
     */
    protected $_createdDate = null;

    /**
     * @var string 更新日時 datetime
     */
    protected $_updatedDate = null;

    /**
     * @var string 削除日時 datetime
     */
    protected $_deletedDate = null;

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
     * @return Application_Model_Base_UserPlatformApplicationRelation このクラスのオブジェクト
     */
    public function setOptions($options)
    {
        $methods = get_class_methods($this);
        
        // 連想配列か、通常の配列化かを判定
        if (is_array($options) && array_values($options) === $options && !empty($options))
        {
            // 通常の配列の場合、連想配列に組み替える
            $tmpArray = array();
            $indexNumber = 0;
        
            foreach ($methods as $methodName)
            {
                // setOptions以外の頭にsetのつくメソッド
                if(preg_match("/^set/", $methodName) && strcmp($methodName, 'setOptions') !== 0)
                {
                    $tmpArray[lcfirst(preg_replace("/^set/", '', $methodName))] = $options[$indexNumber];
                    $indexNumber++;
                }
            }
            $options = $tmpArray;
        }
        
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
     * userIdプロパティーを設定する。
     *
     * @param float $userId userIdの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setUserId($userId)
    {
        $this->_userId = $userId; 
        return $this;
    }

    /**
     * userIdプロパティーを返す。
     *
     * @return float userIdの値
     */
    public function getUserId()
    {
        return $this->_userId;
    }

    /**
     * platformUserIdプロパティーを設定する。
     *
     * @param string $platformUserId platformUserIdの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setPlatformUserId($platformUserId)
    {
        $this->_platformUserId = $platformUserId; 
        return $this;
    }

    /**
     * platformUserIdプロパティーを返す。
     *
     * @return string platformUserIdの値
     */
    public function getPlatformUserId()
    {
        return $this->_platformUserId;
    }

    /**
     * platformIdプロパティーを設定する。
     *
     * @param string $platformId platformIdの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setPlatformId($platformId)
    {
        $this->_platformId = $platformId; 
        return $this;
    }

    /**
     * platformIdプロパティーを返す。
     *
     * @return string platformIdの値
     */
    public function getPlatformId()
    {
        return $this->_platformId;
    }

    /**
     * applicationIdプロパティーを設定する。
     *
     * @param string $applicationId applicationIdの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setApplicationId($applicationId)
    {
        $this->_applicationId = $applicationId; 
        return $this;
    }

    /**
     * applicationIdプロパティーを返す。
     *
     * @return string applicationIdの値
     */
    public function getApplicationId()
    {
        return $this->_applicationId;
    }

    /**
     * authorizationCodeプロパティーを設定する。
     *
     * @param string $authorizationCode authorizationCodeの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->_authorizationCode = $authorizationCode; 
        return $this;
    }

    /**
     * authorizationCodeプロパティーを返す。
     *
     * @return string authorizationCodeの値
     */
    public function getAuthorizationCode()
    {
        return $this->_authorizationCode;
    }

    /**
     * accessTokenプロパティーを設定する。
     *
     * @param string $accessToken accessTokenの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setAccessToken($accessToken)
    {
        $this->_accessToken = $accessToken; 
        return $this;
    }

    /**
     * accessTokenプロパティーを返す。
     *
     * @return string accessTokenの値
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * idTokenプロパティーを設定する。
     *
     * @param string $idToken idTokenの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setIdToken($idToken)
    {
        $this->_idToken = $idToken; 
        return $this;
    }

    /**
     * idTokenプロパティーを返す。
     *
     * @return string idTokenの値
     */
    public function getIdToken()
    {
        return $this->_idToken;
    }

    /**
     * refreshTokenプロパティーを設定する。
     *
     * @param string $refreshToken refreshTokenの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setRefreshToken($refreshToken)
    {
        $this->_refreshToken = $refreshToken; 
        return $this;
    }

    /**
     * refreshTokenプロパティーを返す。
     *
     * @return string refreshTokenの値
     */
    public function getRefreshToken()
    {
        return $this->_refreshToken;
    }

    /**
     * createdDateプロパティーを設定する。
     *
     * @param string $createdDate createdDateの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setCreatedDate($createdDate)
    {
        $this->_createdDate = $createdDate; 
        return $this;
    }

    /**
     * createdDateプロパティーを返す。
     *
     * @return string createdDateの値
     */
    public function getCreatedDate()
    {
        return $this->_createdDate;
    }

    /**
     * updatedDateプロパティーを設定する。
     *
     * @param string $updatedDate updatedDateの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->_updatedDate = $updatedDate; 
        return $this;
    }

    /**
     * updatedDateプロパティーを返す。
     *
     * @return string updatedDateの値
     */
    public function getUpdatedDate()
    {
        return $this->_updatedDate;
    }

    /**
     * deletedDateプロパティーを設定する。
     *
     * @param string $deletedDate deletedDateの値
     * @return Application_Model_Base_UserPlatformApplicationRelation Application_Model_Base_UserPlatformApplicationRelationのオブジェクト
     */
    public function setDeletedDate($deletedDate)
    {
        $this->_deletedDate = $deletedDate; 
        return $this;
    }

    /**
     * deletedDateプロパティーを返す。
     *
     * @return string deletedDateの値
     */
    public function getDeletedDate()
    {
        return $this->_deletedDate;
    }

    /**
     * モデルオブジェクトを連想配列にして返す。
     *
     * @return array モデルオブジェクトの連想配列
     */
    public function toArray()
    {
        $memberArray = array();
        $memberArray['userId'] = $this->getUserId();
        $memberArray['platformUserId'] = $this->getPlatformUserId();
        $memberArray['platformId'] = $this->getPlatformId();
        $memberArray['applicationId'] = $this->getApplicationId();
        $memberArray['authorizationCode'] = $this->getAuthorizationCode();
        $memberArray['accessToken'] = $this->getAccessToken();
        $memberArray['idToken'] = $this->getIdToken();
        $memberArray['refreshToken'] = $this->getRefreshToken();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

