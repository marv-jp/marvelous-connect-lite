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
 * アプリケーションユーザ通貨消費ログ
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_ApplicationUserCurrencyPaymentLog
{

    const CLASS_NAME = 'Application_Model_Base_ApplicationUserCurrencyPaymentLog';

    /**
     * @var float アプリケーションユーザペイメントアイテムID PK:bigint_unsigned
     */
    protected $_applicationUserPaymentItemId = null;

    /**
     * @var float アプリケーションユーザペイメントID bigint_unsigned
     */
    protected $_applicationUserPaymentId = null;

    /**
     * @var string アプリケーション通貨ID PK:varchar(255)
     */
    protected $_applicationCurrencyId = null;

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
     * @var string アプリケーション商品ID varchar(255)
     */
    protected $_applicationProductId = null;

    /**
     * @var integer 商品数量 int_unsigned
     */
    protected $_productQuantity = null;

    /**
     * @var float 単価 PK:decimal_unsigned
     */
    protected $_unitPrice = null;

    /**
     * @var integer 通貨額 int_unsigned
     */
    protected $_currencyAmount = null;

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
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog このクラスのオブジェクト
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
     * applicationUserPaymentItemIdプロパティーを設定する。
     *
     * @param float $applicationUserPaymentItemId applicationUserPaymentItemIdの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
     */
    public function setApplicationUserPaymentItemId($applicationUserPaymentItemId)
    {
        $this->_applicationUserPaymentItemId = $applicationUserPaymentItemId; 
        return $this;
    }

    /**
     * applicationUserPaymentItemIdプロパティーを返す。
     *
     * @return float applicationUserPaymentItemIdの値
     */
    public function getApplicationUserPaymentItemId()
    {
        return $this->_applicationUserPaymentItemId;
    }

    /**
     * applicationUserPaymentIdプロパティーを設定する。
     *
     * @param float $applicationUserPaymentId applicationUserPaymentIdの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
     * applicationCurrencyIdプロパティーを設定する。
     *
     * @param string $applicationCurrencyId applicationCurrencyIdの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
     */
    public function setApplicationCurrencyId($applicationCurrencyId)
    {
        $this->_applicationCurrencyId = $applicationCurrencyId; 
        return $this;
    }

    /**
     * applicationCurrencyIdプロパティーを返す。
     *
     * @return string applicationCurrencyIdの値
     */
    public function getApplicationCurrencyId()
    {
        return $this->_applicationCurrencyId;
    }

    /**
     * paymentPlatformIdプロパティーを設定する。
     *
     * @param string $paymentPlatformId paymentPlatformIdの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
     * applicationUserIdプロパティーを設定する。
     *
     * @param string $applicationUserId applicationUserIdの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
     * applicationProductIdプロパティーを設定する。
     *
     * @param string $applicationProductId applicationProductIdの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
     */
    public function setApplicationProductId($applicationProductId)
    {
        $this->_applicationProductId = $applicationProductId; 
        return $this;
    }

    /**
     * applicationProductIdプロパティーを返す。
     *
     * @return string applicationProductIdの値
     */
    public function getApplicationProductId()
    {
        return $this->_applicationProductId;
    }

    /**
     * productQuantityプロパティーを設定する。
     *
     * @param float $productQuantity productQuantityの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
     */
    public function setProductQuantity($productQuantity)
    {
        $this->_productQuantity = $productQuantity; 
        return $this;
    }

    /**
     * productQuantityプロパティーを返す。
     *
     * @return float productQuantityの値
     */
    public function getProductQuantity()
    {
        return $this->_productQuantity;
    }

    /**
     * unitPriceプロパティーを設定する。
     *
     * @param float $unitPrice unitPriceの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
     */
    public function setUnitPrice($unitPrice)
    {
        $this->_unitPrice = $unitPrice; 
        return $this;
    }

    /**
     * unitPriceプロパティーを返す。
     *
     * @return float unitPriceの値
     */
    public function getUnitPrice()
    {
        return $this->_unitPrice;
    }

    /**
     * currencyAmountプロパティーを設定する。
     *
     * @param float $currencyAmount currencyAmountの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
     */
    public function setCurrencyAmount($currencyAmount)
    {
        $this->_currencyAmount = $currencyAmount; 
        return $this;
    }

    /**
     * currencyAmountプロパティーを返す。
     *
     * @return float currencyAmountの値
     */
    public function getCurrencyAmount()
    {
        return $this->_currencyAmount;
    }

    /**
     * createdDateプロパティーを設定する。
     *
     * @param string $createdDate createdDateの値
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserCurrencyPaymentLog Application_Model_Base_ApplicationUserCurrencyPaymentLogのオブジェクト
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
        $memberArray['applicationUserPaymentItemId'] = $this->getApplicationUserPaymentItemId();
        $memberArray['applicationUserPaymentId'] = $this->getApplicationUserPaymentId();
        $memberArray['applicationCurrencyId'] = $this->getApplicationCurrencyId();
        $memberArray['paymentPlatformId'] = $this->getPaymentPlatformId();
        $memberArray['paymentDeviceId'] = $this->getPaymentDeviceId();
        $memberArray['paymentRatingId'] = $this->getPaymentRatingId();
        $memberArray['applicationUserId'] = $this->getApplicationUserId();
        $memberArray['applicationId'] = $this->getApplicationId();
        $memberArray['applicationWorldId'] = $this->getApplicationWorldId();
        $memberArray['applicationProductId'] = $this->getApplicationProductId();
        $memberArray['productQuantity'] = $this->getProductQuantity();
        $memberArray['unitPrice'] = $this->getUnitPrice();
        $memberArray['currencyAmount'] = $this->getCurrencyAmount();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

