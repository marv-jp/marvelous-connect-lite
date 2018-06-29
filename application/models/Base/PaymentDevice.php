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
 * ペイメントデバイス
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_PaymentDevice
{

    const CLASS_NAME = 'Application_Model_Base_PaymentDevice';

    /**
     * @var string ペイメントデバイスID PK:varchar(11)
     */
    protected $_paymentDeviceId = null;

    /**
     * @var string デバイス名 varchar(255)
     */
    protected $_deviceName = null;

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
     * @return Application_Model_Base_PaymentDevice このクラスのオブジェクト
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
     * paymentDeviceIdプロパティーを設定する。
     *
     * @param string $paymentDeviceId paymentDeviceIdの値
     * @return Application_Model_Base_PaymentDevice Application_Model_Base_PaymentDeviceのオブジェクト
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
     * deviceNameプロパティーを設定する。
     *
     * @param string $deviceName deviceNameの値
     * @return Application_Model_Base_PaymentDevice Application_Model_Base_PaymentDeviceのオブジェクト
     */
    public function setDeviceName($deviceName)
    {
        $this->_deviceName = $deviceName; 
        return $this;
    }

    /**
     * deviceNameプロパティーを返す。
     *
     * @return string deviceNameの値
     */
    public function getDeviceName()
    {
        return $this->_deviceName;
    }

    /**
     * createdDateプロパティーを設定する。
     *
     * @param string $createdDate createdDateの値
     * @return Application_Model_Base_PaymentDevice Application_Model_Base_PaymentDeviceのオブジェクト
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
     * @return Application_Model_Base_PaymentDevice Application_Model_Base_PaymentDeviceのオブジェクト
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
     * @return Application_Model_Base_PaymentDevice Application_Model_Base_PaymentDeviceのオブジェクト
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
        $memberArray['paymentDeviceId'] = $this->getPaymentDeviceId();
        $memberArray['deviceName'] = $this->getDeviceName();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

