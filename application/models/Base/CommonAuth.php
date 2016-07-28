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
 * ユーザー認証・認可管理
 *
 *
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
class Application_Model_Base_CommonAuth
{

    const CLASS_NAME = 'Application_Model_Base_CommonAuth';

    /**
     * @var string レルム PK:varchar(255)
     */
    protected $_realm = null;

    /**
     * @var string ユーザー名 PK:varchar(255)
     */
    protected $_username = null;

    /**
     * @var string 表示名 varchar(255)
     */
    protected $_displayName = null;

    /**
     * @var string パスワード varchar(255)
     */
    protected $_password = null;

    /**
     * @var string ロール PK:varchar(255)
     */
    protected $_role = null;

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
     * @return Application_Model_Base_CommonAuth このクラスのオブジェクト
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
     * realmプロパティーを設定する。
     *
     * @param string $realm realmの値
     * @return Application_Model_Base_CommonAuth Application_Model_Base_CommonAuthのオブジェクト
     */
    public function setRealm($realm)
    {
        $this->_realm = $realm; 
        return $this;
    }

    /**
     * realmプロパティーを返す。
     *
     * @return string realmの値
     */
    public function getRealm()
    {
        return $this->_realm;
    }

    /**
     * usernameプロパティーを設定する。
     *
     * @param string $username usernameの値
     * @return Application_Model_Base_CommonAuth Application_Model_Base_CommonAuthのオブジェクト
     */
    public function setUsername($username)
    {
        $this->_username = $username; 
        return $this;
    }

    /**
     * usernameプロパティーを返す。
     *
     * @return string usernameの値
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * displayNameプロパティーを設定する。
     *
     * @param string $displayName displayNameの値
     * @return Application_Model_Base_CommonAuth Application_Model_Base_CommonAuthのオブジェクト
     */
    public function setDisplayName($displayName)
    {
        $this->_displayName = $displayName; 
        return $this;
    }

    /**
     * displayNameプロパティーを返す。
     *
     * @return string displayNameの値
     */
    public function getDisplayName()
    {
        return $this->_displayName;
    }

    /**
     * passwordプロパティーを設定する。
     *
     * @param string $password passwordの値
     * @return Application_Model_Base_CommonAuth Application_Model_Base_CommonAuthのオブジェクト
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
     * roleプロパティーを設定する。
     *
     * @param string $role roleの値
     * @return Application_Model_Base_CommonAuth Application_Model_Base_CommonAuthのオブジェクト
     */
    public function setRole($role)
    {
        $this->_role = $role; 
        return $this;
    }

    /**
     * roleプロパティーを返す。
     *
     * @return string roleの値
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * モデルオブジェクトを連想配列にして返す。
     *
     * @return array モデルオブジェクトの連想配列
     */
    public function toArray()
    {
        $memberArray = array();
        $memberArray['realm'] = $this->getRealm();
        $memberArray['username'] = $this->getUsername();
        $memberArray['displayName'] = $this->getDisplayName();
        $memberArray['password'] = $this->getPassword();
        $memberArray['role'] = $this->getRole();
        return $memberArray;
    }


}

