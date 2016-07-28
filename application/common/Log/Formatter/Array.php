<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Array
 *
 * @author ohara
 */
class Common_Log_Formatter_Array extends Zend_Log_Formatter_Abstract
{
    private $_culumnMapping = array();

    public function __construct($culumnMapping)
    {
        $this->_culumnMapping = $culumnMapping;
    }

    public function format($event)
    {
        $returnEvent = array();
        foreach ($this->_culumnMapping as $value)
        {
            // HDFS用にnull値があると数値項目のカラム取り込み時に不都合があるため
            // null値は空文字に変換する
            if (strlen($event[$value]) > 0)
            {
                $returnEvent[$value] = $event[$value];
            }
            else
            {
                $returnEvent[$value] = "";
            }
        }

        return $returnEvent;
    }

    public static function factory($culumnMapping)
    {
        return new self($culumnMapping);
    }

}

