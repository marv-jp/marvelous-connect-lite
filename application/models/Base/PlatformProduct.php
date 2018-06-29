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
 * プラットフォーム商品
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_PlatformProduct
{

    const CLASS_NAME = 'Application_Model_Base_PlatformProduct';

    /**
     * @var string プラットフォーム商品ID PK:varchar(255) iOS: product_id\nAndroid: productId\nDMM: SKU_ID
     */
    protected $_platformProductId = null;

    /**
     * @var string ペイメントプラットフォームID PK:varchar(191)
     */
    protected $_paymentPlatformId = null;

    /**
     * @var string ペイメントデバイスID PK:varchar(11)
     */
    protected $_paymentDeviceId = null;

    /**
     * @var string ペイメントレーティングID PK:varchar(11)
     */
    protected $_paymentRatingId = null;

    /**
     * @var string アプリケーションID PK:varchar(11)
     */
    protected $_applicationId = null;

    /**
     * @var string プラットフォーム商品名 varchar(255)
     */
    protected $_platformProductName = null;

    /**
     * @var string プラットフォーム商品画像URL varchar(255)
     */
    protected $_platformProductImageUrl = null;

    /**
     * @var string プラットフォーム商品説明 text
     */
    protected $_platformProductDescription = null;

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
     * @return Application_Model_Base_PlatformProduct このクラスのオブジェクト
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
     * platformProductIdプロパティーを設定する。
     *
     * @param string $platformProductId platformProductIdの値
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
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
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
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
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
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
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
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
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
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
     * platformProductNameプロパティーを設定する。
     *
     * @param string $platformProductName platformProductNameの値
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
     */
    public function setPlatformProductName($platformProductName)
    {
        $this->_platformProductName = $platformProductName; 
        return $this;
    }

    /**
     * platformProductNameプロパティーを返す。
     *
     * @return string platformProductNameの値
     */
    public function getPlatformProductName()
    {
        return $this->_platformProductName;
    }

    /**
     * platformProductImageUrlプロパティーを設定する。
     *
     * @param string $platformProductImageUrl platformProductImageUrlの値
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
     */
    public function setPlatformProductImageUrl($platformProductImageUrl)
    {
        $this->_platformProductImageUrl = $platformProductImageUrl; 
        return $this;
    }

    /**
     * platformProductImageUrlプロパティーを返す。
     *
     * @return string platformProductImageUrlの値
     */
    public function getPlatformProductImageUrl()
    {
        return $this->_platformProductImageUrl;
    }

    /**
     * platformProductDescriptionプロパティーを設定する。
     *
     * @param string $platformProductDescription platformProductDescriptionの値
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
     */
    public function setPlatformProductDescription($platformProductDescription)
    {
        $this->_platformProductDescription = $platformProductDescription; 
        return $this;
    }

    /**
     * platformProductDescriptionプロパティーを返す。
     *
     * @return string platformProductDescriptionの値
     */
    public function getPlatformProductDescription()
    {
        return $this->_platformProductDescription;
    }

    /**
     * createdDateプロパティーを設定する。
     *
     * @param string $createdDate createdDateの値
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
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
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
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
     * @return Application_Model_Base_PlatformProduct Application_Model_Base_PlatformProductのオブジェクト
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
        $memberArray['platformProductId'] = $this->getPlatformProductId();
        $memberArray['paymentPlatformId'] = $this->getPaymentPlatformId();
        $memberArray['paymentDeviceId'] = $this->getPaymentDeviceId();
        $memberArray['paymentRatingId'] = $this->getPaymentRatingId();
        $memberArray['applicationId'] = $this->getApplicationId();
        $memberArray['platformProductName'] = $this->getPlatformProductName();
        $memberArray['platformProductImageUrl'] = $this->getPlatformProductImageUrl();
        $memberArray['platformProductDescription'] = $this->getPlatformProductDescription();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

