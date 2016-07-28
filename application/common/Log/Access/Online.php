<?php

/**
 * 共通ロガー：オンライン用アクセスロガークラスファイル
 *
 * 共通ロガー：オンライン用アクセスロガークラスファイル
 *
 * @category   Zend
 * @package    Zend_Magic
 * @subpackage Wand
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc.(http://www.zend.com)
 * @license    http://framework.zend.com/license   BSD License
 * @version    $Id:$
 * @link       http://framework.zend.com/package/PackageName
 * @since      File available since Release 1.5.0
 */

/**
 * Common_Log_Access_Onlineクラス
 *
 * Common_Log_Access_Onlineクラス
 *
 * @category       Zend
 * @package        Zend_Magic
 * @subpackage     Wand
 * @copyright      Copyright (c) 2005-2011 Zend Technologies USA Inc.(http://www.zend.com)
 * @license        http://framework.zend.com/license   BSD License
 * @version        Release:
 * @package_version@
 * @link           http://framework.zend.com/package/PackageName
 * @since          Class available since Release 1.5.0
 */
class Common_Log_Access_Online extends Common_Log_Access_Abstract
{

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $configs      = new Zend_Config(Zend_Registry::get('log_configs'));
        $config       = $configs->toArray();
        $this->_config = $config['access'];
    }

    /**
     * INFOログを出力する
     *
     * @param $message 出力するログメッセージ
     */
    public function info($message = "")
    {
        $date = date('Y-m-d H:i:s');

        $this->_log
                ->setEventItem('creation_date', $date)
                ->setEventItem('deleted_date', NULL)
                ->setEventItem('updated_date', $date)
                ->info($message);
    }

    /**
     * ログインスタンスを返す
     *
     * @return Zend_Log ログインスタンス
     */
    public function getLog()
    {
        return $this->_log;
    }

}
