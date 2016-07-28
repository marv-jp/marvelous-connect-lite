<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author tanbaa
 */
abstract class Common_Log_Access_Abstract extends Common_Log_Abstract implements Common_Log_Access_Interface
{
    /** @var int $_lastInsertId lastInsertIdを保持する変数 */
    protected $_lastInsertId;

    /**
     * lastInsertIdを取得する
     * 
     * @return int $lastInsertId
     */
    public function getLastInsertId()
    {
        return $this->_lastInsertId;
    }

    /**
     * lastInsertIdをセットする
     * 
     * @param int $id lastInsertId
     */
    public function setLastInsertId($id)
    {
        $this->_lastInsertId = $id;
    }

}