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
 * MISP⇔OP間セッション管理テーブル
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_Session
{

    const CLASS_NAME = 'Application_Model_Base_Session';

    /**
     * @var string セッションID PK:char(32)
     */
    protected $_id = null;

    /**
     * @var integer 最終更新日時 int
     */
    protected $_modified = null;

    /**
     * @var integer セッションの有効期間(秒) int
     */
    protected $_lifetime = null;

    /**
     * @var string セッションに保存するシリアライズデータ blob
     */
    protected $_data = null;

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
     * @return Application_Model_Base_Session このクラスのオブジェクト
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
     * idプロパティーを設定する。
     *
     * @param string $id idの値
     * @return Application_Model_Base_Session Application_Model_Base_Sessionのオブジェクト
     */
    public function setId($id)
    {
        $this->_id = $id; 
        return $this;
    }

    /**
     * idプロパティーを返す。
     *
     * @return string idの値
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * modifiedプロパティーを設定する。
     *
     * @param integer $modified modifiedの値
     * @return Application_Model_Base_Session Application_Model_Base_Sessionのオブジェクト
     */
    public function setModified($modified)
    {
        $this->_modified = $modified; 
        return $this;
    }

    /**
     * modifiedプロパティーを返す。
     *
     * @return integer modifiedの値
     */
    public function getModified()
    {
        return $this->_modified;
    }

    /**
     * lifetimeプロパティーを設定する。
     *
     * @param integer $lifetime lifetimeの値
     * @return Application_Model_Base_Session Application_Model_Base_Sessionのオブジェクト
     */
    public function setLifetime($lifetime)
    {
        $this->_lifetime = $lifetime; 
        return $this;
    }

    /**
     * lifetimeプロパティーを返す。
     *
     * @return integer lifetimeの値
     */
    public function getLifetime()
    {
        return $this->_lifetime;
    }

    /**
     * dataプロパティーを設定する。
     *
     * @param string $data dataの値
     * @return Application_Model_Base_Session Application_Model_Base_Sessionのオブジェクト
     */
    public function setData($data)
    {
        $this->_data = $data; 
        return $this;
    }

    /**
     * dataプロパティーを返す。
     *
     * @return string dataの値
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * モデルオブジェクトを連想配列にして返す。
     *
     * @return array モデルオブジェクトの連想配列
     */
    public function toArray()
    {
        $memberArray = array();
        $memberArray['id'] = $this->getId();
        $memberArray['modified'] = $this->getModified();
        $memberArray['lifetime'] = $this->getLifetime();
        $memberArray['data'] = $this->getData();
        return $memberArray;
    }


}

