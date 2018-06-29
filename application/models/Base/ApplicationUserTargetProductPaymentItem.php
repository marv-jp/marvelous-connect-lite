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
 * アプリケーションユーザターゲット商品ペイメントアイテム
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_ApplicationUserTargetProductPaymentItem
{

    const CLASS_NAME = 'Application_Model_Base_ApplicationUserTargetProductPaymentItem';

    /**
     * @var float アプリケーションユーザペイメントアイテムID PK:bigint_unsigned
     */
    protected $_applicationUserPaymentItemId = null;

    /**
     * @var string アプリケーション商品ID varchar(255)
     */
    protected $_applicationProductId = null;

    /**
     * @var integer 商品数量 int_unsigned
     */
    protected $_productQuantity = null;

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
     * @return Application_Model_Base_ApplicationUserTargetProductPaymentItem このクラスのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserTargetProductPaymentItem Application_Model_Base_ApplicationUserTargetProductPaymentItemのオブジェクト
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
     * applicationProductIdプロパティーを設定する。
     *
     * @param string $applicationProductId applicationProductIdの値
     * @return Application_Model_Base_ApplicationUserTargetProductPaymentItem Application_Model_Base_ApplicationUserTargetProductPaymentItemのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserTargetProductPaymentItem Application_Model_Base_ApplicationUserTargetProductPaymentItemのオブジェクト
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
     * createdDateプロパティーを設定する。
     *
     * @param string $createdDate createdDateの値
     * @return Application_Model_Base_ApplicationUserTargetProductPaymentItem Application_Model_Base_ApplicationUserTargetProductPaymentItemのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserTargetProductPaymentItem Application_Model_Base_ApplicationUserTargetProductPaymentItemのオブジェクト
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
     * @return Application_Model_Base_ApplicationUserTargetProductPaymentItem Application_Model_Base_ApplicationUserTargetProductPaymentItemのオブジェクト
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
        $memberArray['applicationProductId'] = $this->getApplicationProductId();
        $memberArray['productQuantity'] = $this->getProductQuantity();
        $memberArray['createdDate'] = $this->getCreatedDate();
        $memberArray['updatedDate'] = $this->getUpdatedDate();
        $memberArray['deletedDate'] = $this->getDeletedDate();
        return $memberArray;
    }


}

