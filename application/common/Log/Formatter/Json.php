<?php

/**
 * Common_Log_Formatter_Json of Json
 */
class Common_Log_Formatter_Json extends Common_Log_Formatter_Array
{
    public function __construct($culumnMapping)
    {
        parent::__construct($culumnMapping);
    }

    public function format($event)
    {
        // Fluentを利用する場合に必要な time カラムを出力
        // creation_date と同じ値を設定する
        $returnEvent         = parent::format($event);
        if(isset($event['creation_date'])){
            $returnEvent['time'] = $event['creation_date'];
        }
        
        return Zend_Json::encode($returnEvent) . PHP_EOL;
    }

}