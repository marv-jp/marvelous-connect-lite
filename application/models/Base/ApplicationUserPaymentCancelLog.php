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
 * アプリケーションユーザペイメントキャンセルログ
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_ApplicationUserPaymentCancelLog
{

    const CLASS_NAME = 'Application_Model_Base_ApplicationUserPaymentCancelLog';

    /**
     * @var float アプリケーションユーザペイメントID PK:bigint_unsigned
     */
    protected $_applicationUserPaymentId = null;

    /**
     * @var string アプリケーションユーザID varchar(255)
     */
    protected $_applicationUserId = null;

    /**
     * @var string アプリケーションID PK:varchar(11)
     */
    protected $_applicationId = null;

    /**
     * @var string アプリケーションワールドID varchar(255)
     */
    protected $_applicationWorldId = null;

    /**
     * @var string ペイメントプラットフォームID varchar(191)
     */
    protected $_paymentPlatformId = null;

    /**
     * @var string ペイメントデバイスID varchar(11)
     */
    protected $_paymentDeviceId = null;

    /**
     * @var string ペイメントレーティングID varchar(11)
     */
    protected $_paymentRatingId = null;

    /**
     * @var integer ペイメント種別 tinyint 10: credit\r\n11: bonus\r\n20: exchange\r\n30: payment\r\n\r\n
     */
    protected $_paymentType = null;

    /**
     * @var integer ペイメントステータス tinyint 0: start\r\n1: error\r\n2: confirm\r\n3: order\r\n10: complete\r\n
     */
    protected $_paymentStatus = null;

    /**
     * @var string 開始日時 datetime アプリケーションユーザペイメントの作成日時
     */
    protected $_startedDate = null;

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
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog このクラスのオブジェクト
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
     * applicationUserPaymentIdプロパティーを設定する。
     *
     * @param float $applicationUserPaymentId applicationUserPaymentIdの値
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
     */
    public function setApplicationUserPaymentId($applicationUserPaymentId)
    {
        $this->_applicationUserPaymentId = $applicationUserPaymentId; 
        return $this;
    }

    /**
     * applicationUserPaymentIdプロパティーを返す。
     *
     * @return float applicationUserPaymentIdの値
     */
    public function getApplicationUserPaymentId()
    {
        return $this->_applicationUserPaymentId;
    }

    /**
     * applicationUserIdプロパティーを設定する。
     *
     * @param string $applicationUserId applicationUserIdの値
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
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
     * paymentPlatformIdプロパティーを設定する。
     *
     * @param string $paymentPlatformId paymentPlatformIdの値
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
     */
    public function setPaymentPlatformId($paymentPlatformId)
    {
        $this->_paymentPlatformId = $paymentPlatformId; 
        return $this;
    }

    /**
     * paymentPlatformIdプロパティーを返す。
     *
     * @return string paymentPlatformIdの値
     */
    public function getPaymentPlatformId()
    {
        return $this->_paymentPlatformId;
    }

    /**
     * paymentDeviceIdプロパティーを設定する。
     *
     * @param string $paymentDeviceId paymentDeviceIdの値
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
     */
    public function setPaymentDeviceId($paymentDeviceId)
    {
        $this->_paymentDeviceId = $paymentDeviceId; 
        return $this;
    }

    /**
     * paymentDeviceIdプロパティーを返す。
     *
     * @return string paymentDeviceIdの値
     */
    public function getPaymentDeviceId()
    {
        return $this->_paymentDeviceId;
    }

    /**
     * paymentRatingIdプロパティーを設定する。
     *
     * @param string $paymentRatingId paymentRatingIdの値
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
     */
    public function setPaymentRatingId($paymentRatingId)
    {
        $this->_paymentRatingId = $paymentRatingId; 
        return $this;
    }

    /**
     * paymentRatingIdプロパティーを返す。
     *
     * @return string paymentRatingIdの値
     */
    public function getPaymentRatingId()
    {
        return $this->_paymentRatingId;
    }

    /**
     * paymentTypeプロパティーを設定する。
     *
     * @param integer $paymentType paymentTypeの値
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
     */
    public function setPaymentType($paymentType)
    {
        $this->_paymentType = $paymentType; 
        return $this;
    }

    /**
     * paymentTypeプロパティーを返す。
     *
     * @return integer paymentTypeの値
     */
    public function getPaymentType()
    {
        return $this->_paymentType;
    }

    /**
     * paymentStatusプロパティーを設定する。
     *
     * @param integer $paymentStatus paymentStatusの値
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->_paymentStatus = $paymentStatus; 
        return $this;
    }

    /**
     * paymentStatusプロパティーを返す。
     *
     * @return integer paymentStatusの値
     */
    public function getPaymentStatus()
    {
        return $this->_paymentStatus;
    }

    /**
     * startedDateプロパティーを設定する。
     *
     * @param string $startedDate startedDateの値
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
     */
    public function setStartedDate($startedDate)
    {
        $this->_startedDate = $startedDate; 
        return $this;
    }

    /**
     * startedDateプロパティーを返す。
     *
     * @return string startedDateの値
     */
    public function getStartedDate()
    {
        return $this->_startedDate;
    }

    /**
     * createdDateプロパティーを設定する。
     *
     * @param string $createdDate createdDateの値
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserPaymentCancelLog Application_Model_Base_ApplicationUserPaymentCancelLogのオブジェクト
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
        $memberArray['applicationUserPaymentId'] = $this->getApplicationUserPaymentId();
        $memberArray['applicationUserId'] = $this->getApplicationUserId();
        $memberArray['applicationId'] = $this->getApplicationId();
        $memberArray['applicationWorldId'] = $this->getApplicationWorldId();
        $memberArray['paymentPlatformId'] = $this->getPaymentPlatformId();
        $memberArray['paymentDeviceId'] = $this->getPaymentDeviceId();
        $memberArray['paymentRatingId'] = $this->getPaymentRatingId();
        $memberArray['paymentType'] = $this->getPaymentType();
        $memberArray['paymentStatus'] = $this->getPaymentStatus();
        $memberArray['startedDate'] = $this->getStartedDate();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

