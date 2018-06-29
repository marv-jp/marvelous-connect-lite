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
 * ペイメントプラットフォーム
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_PaymentPlatform
{

    const CLASS_NAME = 'Application_Model_Base_PaymentPlatform';

    /**
     * @var string ペイメントプラットフォームID PK:varchar(191)
     */
    protected $_paymentPlatformId = null;

    /**
     * @var string プラットフォーム名 varchar(255)
     */
    protected $_platformName = null;

    /**
     * @var string プラットフォームドメイン varchar(255)
     */
    protected $_platformDomain = null;

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
     * @return Application_Model_Base_PaymentPlatform このクラスのオブジェクト
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
     * paymentPlatformIdプロパティーを設定する。
     *
     * @param string $paymentPlatformId paymentPlatformIdの値
     * @return Application_Model_Base_PaymentPlatform Application_Model_Base_PaymentPlatformのオブジェクト
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
     * platformNameプロパティーを設定する。
     *
     * @param string $platformName platformNameの値
     * @return Application_Model_Base_PaymentPlatform Application_Model_Base_PaymentPlatformのオブジェクト
     */
    public function setPlatformName($platformName)
    {
        $this->_platformName = $platformName; 
        return $this;
    }

    /**
     * platformNameプロパティーを返す。
     *
     * @return string platformNameの値
     */
    public function getPlatformName()
    {
        return $this->_platformName;
    }

    /**
     * platformDomainプロパティーを設定する。
     *
     * @param string $platformDomain platformDomainの値
     * @return Application_Model_Base_PaymentPlatform Application_Model_Base_PaymentPlatformのオブジェクト
     */
    public function setPlatformDomain($platformDomain)
    {
        $this->_platformDomain = $platformDomain; 
        return $this;
    }

    /**
     * platformDomainプロパティーを返す。
     *
     * @return string platformDomainの値
     */
    public function getPlatformDomain()
    {
        return $this->_platformDomain;
    }

    /**
     * createdDateプロパティーを設定する。
     *
     * @param string $createdDate createdDateの値
     * @return Application_Model_Base_PaymentPlatform Application_Model_Base_PaymentPlatformのオブジェクト
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
     * @return Application_Model_Base_PaymentPlatform Application_Model_Base_PaymentPlatformのオブジェクト
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
     * @return Application_Model_Base_PaymentPlatform Application_Model_Base_PaymentPlatformのオブジェクト
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
        $memberArray['paymentPlatformId'] = $this->getPaymentPlatformId();
        $memberArray['platformName'] = $this->getPlatformName();
        $memberArray['platformDomain'] = $this->getPlatformDomain();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

