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
 * プラットフォームユーザ
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_PlatformUser
{

    const CLASS_NAME = 'Application_Model_Base_PlatformUser';

    /**
     * @var string プラットフォームユーザID PK:varchar(255)
     */
    protected $_platformUserId = null;

    /**
     * @var string プラットフォームID PK:varchar(191)
     */
    protected $_platformId = null;

    /**
     * @var string プラットフォームユーザ名 varchar(255)
     */
    protected $_platformUserName = null;

    /**
     * @var string プラットフォームユーザ表示名 varchar(255)
     */
    protected $_platformUserDisplayName = null;

    /**
     * @var string メールアドレス varchar(255)
     */
    protected $_emailAddress = null;

    /**
     * @var string アクセストークン text
     */
    protected $_accessToken = null;

    /**
     * @var string IDトークン text
     */
    protected $_idToken = null;

    /**
     * @var integer ステータス tinyint 0: inactive\r\n1: active\r\n6: banned\r\n
     */
    protected $_status = null;

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
     * @return Application_Model_Base_PlatformUser このクラスのオブジェクト
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
     * platformUserIdプロパティーを設定する。
     *
     * @param string $platformUserId platformUserIdの値
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
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
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
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
     * platformUserNameプロパティーを設定する。
     *
     * @param string $platformUserName platformUserNameの値
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
     */
    public function setPlatformUserName($platformUserName)
    {
        $this->_platformUserName = $platformUserName; 
        return $this;
    }

    /**
     * platformUserNameプロパティーを返す。
     *
     * @return string platformUserNameの値
     */
    public function getPlatformUserName()
    {
        return $this->_platformUserName;
    }

    /**
     * platformUserDisplayNameプロパティーを設定する。
     *
     * @param string $platformUserDisplayName platformUserDisplayNameの値
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
     */
    public function setPlatformUserDisplayName($platformUserDisplayName)
    {
        $this->_platformUserDisplayName = $platformUserDisplayName; 
        return $this;
    }

    /**
     * platformUserDisplayNameプロパティーを返す。
     *
     * @return string platformUserDisplayNameの値
     */
    public function getPlatformUserDisplayName()
    {
        return $this->_platformUserDisplayName;
    }

    /**
     * emailAddressプロパティーを設定する。
     *
     * @param string $emailAddress emailAddressの値
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
     */
    public function setEmailAddress($emailAddress)
    {
        $this->_emailAddress = $emailAddress; 
        return $this;
    }

    /**
     * emailAddressプロパティーを返す。
     *
     * @return string emailAddressの値
     */
    public function getEmailAddress()
    {
        return $this->_emailAddress;
    }

    /**
     * accessTokenプロパティーを設定する。
     *
     * @param string $accessToken accessTokenの値
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
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
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
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
     * statusプロパティーを設定する。
     *
     * @param integer $status statusの値
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
     */
    public function setStatus($status)
    {
        $this->_status = $status; 
        return $this;
    }

    /**
     * statusプロパティーを返す。
     *
     * @return integer statusの値
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * createdDateプロパティーを設定する。
     *
     * @param string $createdDate createdDateの値
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
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
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
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
     * @return Application_Model_Base_PlatformUser Application_Model_Base_PlatformUserのオブジェクト
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
        $memberArray['platformUserId'] = $this->getPlatformUserId();
        $memberArray['platformId'] = $this->getPlatformId();
        $memberArray['platformUserName'] = $this->getPlatformUserName();
        $memberArray['platformUserDisplayName'] = $this->getPlatformUserDisplayName();
        $memberArray['emailAddress'] = $this->getEmailAddress();
        $memberArray['accessToken'] = $this->getAccessToken();
        $memberArray['idToken'] = $this->getIdToken();
        $memberArray['status'] = $this->getStatus();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

