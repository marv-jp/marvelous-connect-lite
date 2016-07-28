<?php


/**
 * Application_Model_Base_BaseMapperクラス
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
abstract class Application_Model_Base_BaseMapper
{
    /**
     * @var Zend_Db_Table Zend_Db_Table
     */
    protected $_dbTable = null;

    /**
     * @var string DB名
     */
    protected $_dbName = null;
    
    /**
     * @var string モデル名
     */
    protected $_modelName = '';
    
    /**
     * @var string|Zend_Db_Adapter_Abstract マスタDB
     */
    protected $_masterDb = null;

    /**
     * @var boolean マスタDBへの再取得処理実行フラグ
     */
    protected $_masterRetry = null;
    
    /**
     * コンストラクタ
     *
     * @param string $dbName DB名
     */
    public function __construct($dbName = null)
    {
        $this->_dbName = $dbName;
    }
    
    /**
     * TableDataを設定する。
     *
     * @param mixed $dbTable
     * TableDataクラス名、またはTableDataオブジェクト
     * @return Application_Model_Base_BaseMapper
     * Application_Model_Base_BaseMapperのオブジェクト
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable(array('db' => $this->_dbName));
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * getDbNameメソッド
     *
     * @return string DB名
     */
    public function getDbName()
    {
        return $this->_dbName;
    }

    /**
     * getMasterDbNameメソッド
     *
     * @return string|Zend_Db_Adapter_Abstract
     * マスタDB名|デフォルトDBアダプタ
     */
    public function getMasterDb()
    {
        return $this->_masterDb ? $this->_masterDb : $this->getDbTable()->getDefaultAdapter();
    }

    /**
     * setMasterDbメソッド
     *
     * @param string|Zend_Db_Adapter_Abstract $masterDb マスタDB名|DBアダプタ
     */
    public function setMasterDb($masterDb)
    {
        $this->_masterDb = $masterDb;
    }

    /**
     * masterRetryOnメソッド
     * find,fetchAllで要素を取得できなかった場合、マスタDBへ再取得を行うようにする(デフォルトOff)
     */
    public function masterRetryOn()
    {
        $this->_masterRetry = true;
    }

    /**
     * masterRetryOffメソッド
     * find,fetchAllで要素を取得できなかった場合、マスタDBへ再取得を行わないようにする(デフォルトOff)
     */
    public function masterRetryOff()
    {
        $this->_masterRetry = false;
    }

    /**
     * checksumメソッド
     *
     * @return int チェックサム
     */
    public function checksum()
    {
        return $this->getDbTable()->checksum();
    }

    /**
     * fetchAllメソッド
     *
     * <br>
     * <b>使用方法</b>
     * <pre>
     * // 絞り込み条件、複数の条件を指定した場合はAND検索になります
     * $where = array('deletedDate is not null' => '', 'userId >=' => 5000);
     * // ソート順
     * $order = array('updateDate' => 'ASC', 'userId' => 'DESC');
     * // 取得件数
     * $count = 100;
     * // 取得範囲(以下の場合だと、最初の100件を除外する)
     * $offset = 100
     * 
     * // 絞り込み条件、ソート条件、取得件数、取得範囲を指定して検索
     * $models = $mapper->fetchAll($where, $order, $count, $offset);
     * // 絞り込み条件のみを指定する
     * $models = $mapper->fetchAll($where);
     * // 取得件数のみを指定する
     * $models = $mapper->fetchAll(null, null, $count);
     * </pre>
     * <br>
     * <b>$whereで使用できる演算子</b>
     * <table width="100%" border="1">
     *     <tr>
     *         <td>演算子</td>
     *         <td>$whereで指定する内容</td>
     *         <td>補足</td>
     *     </tr>
     *     <tr>
     *         <td>=</td>
     *         <td>$where = array('key' =&gt; array('value'));</td>
     *         <td>内部的にはIN検索になりますが、等価演算子「=」と同等の性能を確認しています</td>
     *     </tr>
     *     <tr>
     *         <td>!=</td>
     *         <td>$where = array('key <b>not</b>' =&gt; array('value'));</td>
     *         <td>内部的にはIN検索になりますが、等価演算子「=」と同等の性能を確認しています</td>
     *     </tr>
     *     <tr>
     *         <td>like</td>
     *         <td>$where = array('key' =&gt; 'value% ');<br>$where = array('key' =&gt; 'value_');</td>
     *         <td>% ：任意の0文字以上の文字列<br>_ ：任意の1文字</td>
     *     </tr>
     *     <tr>
     *         <td>not like</td>
     *         <td>$where = array('key <b>not</b>' =&gt; 'value% ');<br>$where = array('key <b>not</b>' =&gt; 'value_');</td>
     *         <td>% ：任意の0文字以上の文字列<br>_ ：任意の1文字</td>
     *     </tr>
     *     <tr>
     *         <td>in</td>
     *         <td>$where = array('key' =&gt; array('value1', 'value2', 'value3'));</td>
     *         <td></td>
     *     </tr>
     *     <tr>
     *         <td>not in</td>
     *         <td>$where = array('key <b>not</b>' =&gt; array('value1', 'value2', 'value3'));</td>
     *         <td></td>
     *     </tr>
     *     <tr>
     *         <td>is null</td>
     *         <td>$where = array('key <b>is null</b>' =&gt; '');</td>
     *         <td></td>
     *     </tr>
     *     <tr>
     *         <td>is not null</td>
     *         <td>$where = array('key <b>is not null</b>' =&gt; '');</td>
     *         <td></td>
     *     </tr>
     *     <tr>
     *         <td>&lt;=</td>
     *         <td>$where = array('key <b>&lt;=</b>' =&gt; 3);</td>
     *         <td></td>
     *     </tr>
     *     <tr>
     *         <td>&gt;=</td>
     *         <td>$where = array('key <b>&gt;=</b>' =&gt; 3);</td>
     *         <td></td>
     *     </tr>
     *     <tr>
     *         <td>&lt;</td>
     *         <td>$where = array('key <b>&lt;</b>' =&gt; 3);</td>
     *         <td></td>
     *     </tr>
     *     <tr>
     *         <td>&gt;</td>
     *         <td>$where = array('key <b>&gt;</b>' =&gt; 3);</td>
     *         <td></td>
     *     </tr>
     *     <tr>
     *         <td>それ以外</td>
     *         <td>未サポートです<br></td>
     *         <td>使用したい場合は、DbTable拡張で独自クエリを書き、対応するMapperメソッドを作成してください<br>より複雑なクエリの場合は、View作成が効率的です<br></td>
     *     </tr>
     * </table> 
     * 
     * @param array $where SELECTの条件を指定する連想配列
     * @param array $order SELECTのソート条件を指定する連想配列
     * @param int $count SELECTの取得件数
     * @param int $offset SELECTの取得範囲
     * @return array モデルオブジェクト配列
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $whereSnake = Common_Util_Db::keyNameCamelToSnakeWithPlaceholder($where);
        $orderSnake = Common_Util_Db::keyNameCamelToSnakeWithValue($order);
        $dataList =  $this->getDbTable()->fetchAll($whereSnake, $orderSnake, $count, $offset);
        if ($this->_masterRetry && $dataList->count() == 0) {
            $adapter = $this->getDbTable()->getAdapter();
            $this->getDbTable()->setOptions(array('db' => $this->getMasterDb()));
            $dataList =  $this->getDbTable()->fetchAll($whereSnake, $orderSnake, $count, $offset);
            $this->getDbTable()->setOptions(array('db' => $adapter));
        }
        
        $models = array();
        foreach ($dataList as $data) {
                $models[] = new $this->_modelName($data);
        }
        return $models;
    }

    /**
     * countメソッド
     *
     * @param array $where SELECTの条件を指定する連想配列
     * @return int The number of rows.
     */
    public function count($where)
    {
        $whereSnake = Common_Util_Db::keyNameCamelToSnakeWithPlaceholder($where);
        return $this->getDbTable()->count($whereSnake);
    }

    /**
     * insertメソッド
     *
     * @param $model モデルオブジェクト
     * @return mixed The primary key of the row inserted.
     */
    public function insert($model)
    {
        return $this->getDbTable()->insert($this->_dataSet($model));
    }

    /**
     * updateメソッド
     *
     * @param $model モデルオブジェクト
     * @param mixed $pk UPDATEのPK
     * @return int The number of rows updated.
     */
    public function update()
    {
        $args = func_get_args();
        $data = array_shift($args);
        array_unshift($args, $this->_dataSet($data));
        $result = call_user_func_array(array($this->getDbTable(), 'updateByPk'), $args);
        return $result;
    }

    /**
     * deleteメソッド
     *
     * @param mixed $pk DELETEのPK
     * @return int The number of rows deleted.
     */
    public function delete()
    {
        $result = call_user_func_array(array($this->getDbTable(), 'deleteByPk'), func_get_args());
        return $result;
    }

    /**
     * findメソッド
     *
     * Zend_Db_Table#find のプロキシメソッド。<br>
     * 結果が1件の場合は、モデルオブジェクトを返す。<br>
     * 結果が複数件の場合は、モデルオブジェクトの配列を返す。<br>
     * <br>
     * 使用方法<br>
     * <pre>
     * // 単一の行を探す。
     * $row  = $mapper->find(1234);
     * // 複数の行を探す。(配列で複数のPK値を指定する)
     * $rows = $mapper->find(array(1, 2));
     * // 単一の行を複合主キーで探す。
     * $row  = $mapper->find(1234, 'ABC');
     * // 複数の行を複合主キーで探す。
     * $rows = $mapper->find(array(1234, 5678), array('ABC', 'DEF'))
     * </pre>
     *
     * @param mixed $key SELECTする際のPK。
     * @return Application_Model_*|array メソッドの説明を参照
     * @link http://framework.zend.com/manual/ja/zend.db.table.html#zend.db.table.find
     */
    public function find()
    {
        // Zend_Db_Table#find に委譲
        $result = call_user_func_array(array($this->getDbTable(), 'find'), func_get_args());
        
        if ($this->_masterRetry && 0 === $result->count()) {
            $adapter = $this->getDbTable()->getAdapter();
            $this->getDbTable()->setOptions(array('db' => $this->getMasterDb()));
            $result = call_user_func_array(array($this->getDbTable(), 'find'), func_get_args());
            $this->getDbTable()->setOptions(array('db' => $adapter));
        }
                        
        // 結果が1件の場合、単体のモデルオブジェクトを返す
        if (1 === $result->count())
        {
            return new $this->_modelName($result->current());
        }
        
        // 結果が2件以上の場合は、モデルオブジェクトの配列を返す
        $models = array();
        foreach ($result as $row)
        {
            $models[] = new $this->_modelName($row);
        }
        
        return $models;
    }

    /**
     * replaceメソッド
     *
     * @param $model モデルオブジェクト
     * @return mixed The primary key of the row inserted.
     */
    public function replace($model)
    {
        $data = $this->_dataSet($model);
        return $this->getDbTable()->replace($data);
    }
}

