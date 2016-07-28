<?php
/**
 * 自動生成ファイル
 *
 * CreateMapperLogicで自動生成されたファイル
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */


/**
 * Application_Model_Base_CommonNgWordMapperクラス
 *
 * 自動生成クラス
 *
 * @category Zend
 * @package Application_Model_Base
 * @subpackage Base
 */
abstract class Application_Model_Base_CommonNgWordMapper extends Application_Model_BaseMapper
{

    /**
     * @var Zend_Db_Table Zend_Db_Table
     */
    protected $_modelName = 'Application_Model_CommonNgWord';

    /**
     * TableDataを返す。
     *
     * @return Application_Model_DbTable_CommonNgWord Application_Model_DbTable_CommonNgWordのオブジェクト
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_CommonNgWord');
        }
        return $this->_dbTable;
    }

    /**
     * モデルオブジェクトのデータを返す。
     *
     * @return array Application_Model_CommonNgWordのオブジェクトから取り出したデータ
     */
    protected function _dataSet(Application_Model_CommonNgWord $commonNgWord)
    {
        return array(
        'application_id' => $commonNgWord->getApplicationId(),
        'ng_word' => $commonNgWord->getNgWord(),
        'created_date' => $commonNgWord->getCreatedDate(),
        'updated_date' => $commonNgWord->getUpdatedDate(),
        'deleted_date' => $commonNgWord->getDeletedDate(),
        );
    }

    /**
     * 自動生成insertWithDefaultValuesメソッド
     *
     * デフォルト値をもつカラムをunsetするINSERTメソッドです。デフォルト値をもつカラムが無い場合は、unset動作は行われません。
     *
     * @param Application_Model_CommonNgWord $commonNgWord Application_Model_CommonNgWordのオブジェクト
     * @return mixed The primary key of the row inserted.
     */
    public function insertWithDefaultValues(Application_Model_CommonNgWord $commonNgWord)
    {
        $data = $this->_dataSet($commonNgWord);
        
        unset($data['application_id']);
        
        return $this->getDbTable()->insert($data);
    }

    /**
     * TableDataを設定する。
     *
     * @param mixed $dbTable TableDataクラス名、またはTableDataオブジェクト
     * @return Application_Model_Base_CommonNgWordMapper Application_Model_Base_CommonNgWordMapperのオブジェクト
     */
    public function setDbTable($dbTable)
    {
        return parent::setDbTable($dbTable);
    }

    /**
     * 自動生成fetchAllメソッド
     *
     * <br>
     * <b>使用方法</b>
     * <pre>
     * // 絞り込み条件、複数の条件を指定した場合はAND検索
     * $where = array('deletedDate is not null' => '', 'userId >=' => 5000);
     * // ソート順
     * $order = array('updateDate' => 'ASC', 'userId' => 'DESC');
     * // 取得件数
     * $count = 100;
     * // 取得範囲(以下の場合だと、最初の100件を除外する)
     * $offset = 100
     *
     * // 全引数を指定して検索
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
     * <td>内部的にはIN検索になりますが、等価演算子「=」と同等の性能を確認しています</td>
     *     </tr>
     *     <tr>
     *         <td>!=</td>
     *         <td>$where = array('key <b>not</b>' =&gt; array('value'));</td>
     * <td>内部的にはIN検索になりますが、等価演算子「=」と同等の性能を確認しています</td>
     *     </tr>
     *     <tr>
     *         <td>like</td>
     *         <td>$where = array('key' =&gt; 'value% ');<br>$where = array('key' =&gt; 'value_');</td>
     *         <td>% ： 任意の0文字以上の文字列<br>_：任意の1文字</td>
     *     </tr>
     *     <tr>
     *         <td>not like</td>
     *         <td>$where = array('key <b>not</b>' =&gt; 'value% ');<br>$where = array('key <b>not</b>' =&gt; 'value_');</td>
     *         <td>% ：任意の0文字以上の文字列<br>_：任意の1文字</td>
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
     *         <td>「&lt;」は未サポートです</td>
     *     </tr>
     *     <tr>
     *         <td>&gt;=</td>
     *         <td>$where = array('key <b>&gt;=</b>' =&gt; 3);</td>
     *         <td>「&gt;」は未サポートです</td>
     *     </tr>
     *     <tr>
     *         <td>それ以外</td>
     *         <td>未サポートです<br></td>
     * <td>使用したい場合は、DbTable拡張で独自クエリを書き、対応するMapperメソッドを作成してください<br>より複雑なクエリの場合は、View作成が効率的です<br></td>
     *     </tr>
     * </table> 
     *
     *
     * @param array $where SELECTの条件を指定する連想配列
     * @param array $order SELECTのソート条件を指定する連想配列
     * @param int $count SELECTの取得件数
     * @param int $offset SELECTの取得範囲
     * @return array Application_Model_CommonNgWordのオブジェクト配列
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        return parent::fetchAll($where, $order, $count, $offset);
    }

    /**
     * 自動生成insertメソッド
     *
     * @param Application_Model_CommonNgWord $commonNgWord Application_Model_CommonNgWordのオブジェクト
     * @return mixed The primary key of the row inserted.
     */
    public function insert(Application_Model_CommonNgWord $commonNgWord)
    {
        return parent::insert($commonNgWord);
    }

    /**
     * 自動生成updateメソッド
     *
     * @param Application_Model_CommonNgWord $commonNgWord Application_Model_CommonNgWordのオブジェクト
     * @param mixed $pk UPDATEのPK
     * @return int The number of rows updated.
     */
    public function update()
    {
        return call_user_func_array(array('parent', 'update'), func_get_args());
    }

    /**
     * 自動生成findメソッド
     *
     * Zend_Db_Table#find のプロキシメソッド。<br>
     * 結果が1件の場合は、Application_Model_CommonNgWordのオブジェクトを返す。<br>
     * 結果が複数件の場合は、Application_Model_CommonNgWordの配列を返す。<br>
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
     * @return Application_Model_CommonNgWord|array メソッドの説明を参照
     * @link http://framework.zend.com/manual/ja/zend.db.table.html#zend.db.table.find
     */
    public function find()
    {
        return call_user_func_array(array('parent', 'find'), func_get_args());
    }

    /**
     * 自動生成replaceメソッド
     *
     * @param Application_Model_CommonNgWord $commonNgWord Application_Model_CommonNgWordのオブジェクト
     * @return mixed The primary key of the row inserted.
     */
    public function replace(Application_Model_CommonNgWord $commonNgWord)
    {
        return parent::replace($commonNgWord);
    }


}

