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
 * プラットフォーム商品アイテム
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_PlatformProductItem
{

    const CLASS_NAME = 'Application_Model_Base_PlatformProductItem';

    /**
     * @var float プラットフォーム商品アイテムID PK:bigint_unsigned
     */
    protected $_platformProductItemId = null;

    /**
     * @var string プラットフォーム商品ID varchar(255) iOS: product_id\nAndroid: productId\nDMM: SKU_ID
     */
    protected $_platformProductId = null;

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
     * @var string アプリケーションID varchar(11)
     */
    protected $_applicationId = null;

    /**
     * @var string アプリケーション通貨ID varchar(255)
     */
    protected $_applicationCurrencyId = null;

    /**
     * @var float 単価 decimal_unsigned
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
     * @return Application_Model_Base_PlatformProductItem このクラスのオブジェクト
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
     * platformProductItemIdプロパティーを設定する。
     *
     * @param float $platformProductItemId platformProductItemIdの値
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
     */
    public function setPlatformProductItemId($platformProductItemId)
    {
        $this->_platformProductItemId = $platformProductItemId; 
        return $this;
    }

    /**
     * platformProductItemIdプロパティーを返す。
     *
     * @return float platformProductItemIdの値
     */
    public function getPlatformProductItemId()
    {
        return $this->_platformProductItemId;
    }

    /**
     * platformProductIdプロパティーを設定する。
     *
     * @param string $platformProductId platformProductIdの値
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
     */
    public function setPlatformProductId($platformProductId)
    {
        $this->_platformProductId = $platformProductId; 
        return $this;
    }

    /**
     * platformProductIdプロパティーを返す。
     *
     * @return string platformProductIdの値
     */
    public function getPlatformProductId()
    {
        return $this->_platformProductId;
    }

    /**
     * paymentPlatformIdプロパティーを設定する。
     *
     * @param string $paymentPlatformId paymentPlatformIdの値
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
     * applicationIdプロパティーを設定する。
     *
     * @param string $applicationId applicationIdの値
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
     * applicationCurrencyIdプロパティーを設定する。
     *
     * @param string $applicationCurrencyId applicationCurrencyIdの値
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
     * unitPriceプロパティーを設定する。
     *
     * @param float $unitPrice unitPriceの値
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
     * @return Application_Model_Base_PlatformProductItem Application_Model_Base_PlatformProductItemのオブジェクト
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
        $memberArray['platformProductItemId'] = $this->getPlatformProductItemId();
        $memberArray['platformProductId'] = $this->getPlatformProductId();
        $memberArray['paymentPlatformId'] = $this->getPaymentPlatformId();
        $memberArray['paymentDeviceId'] = $this->getPaymentDeviceId();
        $memberArray['paymentRatingId'] = $this->getPaymentRatingId();
        $memberArray['applicationId'] = $this->getApplicationId();
        $memberArray['applicationCurrencyId'] = $this->getApplicationCurrencyId();
        $memberArray['unitPrice'] = $this->getUnitPrice();
        $memberArray['currencyAmount'] = $this->getCurrencyAmount();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

