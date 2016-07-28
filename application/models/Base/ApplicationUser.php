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
 * アプリケーションユーザ
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_ApplicationUser
{

    const CLASS_NAME = 'Application_Model_Base_ApplicationUser';

    /**
     * @var string アプリケーションユーザID PK:varchar(255)
     */
    protected $_applicationUserId = null;

    /**
     * @var string アプリケーションID PK:varchar(11)
     */
    protected $_applicationId = null;

    /**
     * @var string アプリケーションワールドID PK:varchar(255)
     */
    protected $_applicationWorldId = null;

    /**
     * @var string アプリケーションユーザ名 varchar(255)
     */
    protected $_applicationUserName = null;

    /**
     * @var string パスワード varchar(255)
     */
    protected $_password = null;

    /**
     * @var string アクセストークン varchar(255)
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
     * @return Application_Model_Base_ApplicationUser このクラスのオブジェクト
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
     * applicationUserIdプロパティーを設定する。
     *
     * @param string $applicationUserId applicationUserIdの値
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
     */
    public function setApplicationUserId($applicationUserId)
    {
        $this->_applicationUserId = $applicationUserId; 
        return $this;
    }

    /**
     * applicationUserIdプロパティーを返す。
     *
     * @return string applicationUserIdの値
     */
    public function getApplicationUserId()
    {
        return $this->_applicationUserId;
    }

    /**
     * applicationIdプロパティーを設定する。
     *
     * @param string $applicationId applicationIdの値
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
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
     * applicationWorldIdプロパティーを設定する。
     *
     * @param string $applicationWorldId applicationWorldIdの値
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
     */
    public function setApplicationWorldId($applicationWorldId)
    {
        $this->_applicationWorldId = $applicationWorldId; 
        return $this;
    }

    /**
     * applicationWorldIdプロパティーを返す。
     *
     * @return string applicationWorldIdの値
     */
    public function getApplicationWorldId()
    {
        return $this->_applicationWorldId;
    }

    /**
     * applicationUserNameプロパティーを設定する。
     *
     * @param string $applicationUserName applicationUserNameの値
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
     */
    public function setApplicationUserName($applicationUserName)
    {
        $this->_applicationUserName = $applicationUserName; 
        return $this;
    }

    /**
     * applicationUserNameプロパティーを返す。
     *
     * @return string applicationUserNameの値
     */
    public function getApplicationUserName()
    {
        return $this->_applicationUserName;
    }

    /**
     * passwordプロパティーを設定する。
     *
     * @param string $password passwordの値
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
     */
    public function setPassword($password)
    {
        $this->_password = $password; 
        return $this;
    }

    /**
     * passwordプロパティーを返す。
     *
     * @return string passwordの値
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * accessTokenプロパティーを設定する。
     *
     * @param string $accessToken accessTokenの値
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
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
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
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
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
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
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
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
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
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
     * @return Application_Model_Base_ApplicationUser Application_Model_Base_ApplicationUserのオブジェクト
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
        $memberArray['applicationUserId'] = $this->getApplicationUserId();
        $memberArray['applicationId'] = $this->getApplicationId();
        $memberArray['applicationWorldId'] = $this->getApplicationWorldId();
        $memberArray['applicationUserName'] = $this->getApplicationUserName();
        $memberArray['password'] = $this->getPassword();
        $memberArray['accessToken'] = $this->getAccessToken();
        $memberArray['idToken'] = $this->getIdToken();
        $memberArray['status'] = $this->getStatus();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

