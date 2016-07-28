<?php

/**
 * Application_Model_DbTable_Pk_TriggeredIncrement
 * 
 * @category Zend
 * @package Application_Model_DbTable
 * @subpackage Pk
 */

/**
 * @category Zend
 * @package Application_Model_DbTable
 * @subpackage Pk
 */
abstract class Application_Model_DbTable_Pk_TriggeredIncrement extends Application_Model_DbTable_Pk_Abstract
{

    /**
     * Inserts a new row.
     *
     * @param  array  $data  Column-value pairs.
     * @return mixed         The primary key of the row inserted.
     */
    public function insert(array $data)
    {
        $this->_setupPrimaryKey();

        /**
         * Zend_Db_Table assumes that if you have a compound primary key
         * and one of the columns in the key uses a sequence,
         * it's the _first_ column in the compound key.
         */
        $primary    = (array) $this->_primary;
        $pkIdentity = $primary[(int) $this->_identity];

        /**
         * If this table uses a database sequence object and the data does not
         * specify a value, then get the next ID from the sequence and add it
         * to the row.  We assume that only the first column in a compound
         * primary key takes a value from a sequence.
         */
        if (is_string($this->_sequence) && !isset($data[$pkIdentity])) {
            $data[$pkIdentity]    = $this->_db->nextSequenceId($this->_sequence);
            $pkSuppliedBySequence = true;
        }

        /**
         * If the primary key can be generated automatically, and no value was
         * specified in the user-supplied data, then omit it from the tuple.
         *
         * Note: this checks for sensible values in the supplied primary key
         * position of the data.  The following values are considered empty:
         *   null, false, true, '', array()
         */
        if (!isset($pkSuppliedBySequence) && array_key_exists($pkIdentity, $data)) {
            if ($data[$pkIdentity] === null                                        // null
                    || $data[$pkIdentity] === ''                                       // empty string
                    || is_bool($data[$pkIdentity])                                     // boolean
                    || (is_array($data[$pkIdentity]) && empty($data[$pkIdentity]))) {  // empty array
                unset($data[$pkIdentity]);
            }
        }

        /**
         * INSERT the new row.
         */
        $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
        $this->_db->insert($tableSpec, $data);

        // ID振り出しテーブル(対象テーブル名_id)の最大値を取得
        $selectSql   = 'select max(' . $pkIdentity . ') as maxId from ' . $this->_name;
        $result      = $this->_db->query($selectSql);
        $resultArray = $result->fetchAll(Zend_Db::FETCH_ASSOC);

        // 取得したID振り出しテーブルの最大値をPK値としてセット
        $data[$pkIdentity] = $resultArray[0]['maxId'];

        /**
         * Return the primary key value if the PK is a single column,
         * else return an associative array of the PK column/value pairs.
         */
        $pkData = array_intersect_key($data, array_flip($primary));
        if (count($primary) == 1) {
            reset($pkData);
            return current($pkData);
        }

        return $pkData;
    }

}
