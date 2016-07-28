<?php

/**
 * Common_Log_External_Externalクラスのファイル
 * 
 * Common_Log_External_Externalクラスを定義している
 *
 * @category   Zend
 * @package    Common_Log
 * @subpackage External
 * @version    $Id$
 */

/**
 * 外部ロガー
 * 
 * @category   Zend
 * @package    Common_Log
 * @subpackage External
 * @see Zend_Http_Client
 */
class Common_Log_External_External extends Common_Log_External_Abstract
{
    /** @var array ログ対象の設定を格納する配列*/
    private $_includes = null;
    
    /** @var array ログ除外対象の設定を格納する配列*/
    private $_excludes = null;
    

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $configs = new Zend_Config(Zend_Registry::get('log_configs'));
        $config = $configs->toArray();
        $this->_config = $config['external'];
         
        $pluginsConfig = Zend_Registry::get('plugins_configs');
        $externalConfig = $pluginsConfig['external_logger'];
        if (isset($externalConfig))
        {
            $this->_includes = $externalConfig['includes'];
            $this->_excludes = $externalConfig['excludes'];
        }
    }

    /**
     * INFOログを出力する
     * 
     * @param string $message 出力するログメッセージ
     */
    public function info($message = "")
    {
        $date = date('Y-m-d H:i:s');
        $hashValue = NULL;
        
        if (strcmp($this->getIdType(), Common_Log_Abstract::ID_TYPE_HASH) == 0)
        {
            $hashValue = md5(uniqid(mt_rand(), TRUE));
            $this->_log->setEventItem('external_log_id', $hashValue);
        }

        $this->_log
                ->setEventItem('creation_date', $date)
                ->setEventItem('deleted_date', NULL)
                ->setEventItem('updated_date', $date)
                ->info($message);
        
        if ($hashValue)
        {
            $this->setLastInsertId($hashValue);
        }
        else
        {
            $config = $this->getConfig();
            if ($config['database'])
            {
                $this->setLastInsertId(Common_Db::factoryByDbName($config['database'])->lastInsertId());
            }
            else
            {
                $this->setLastInsertId(Common_Log::getLogDbAdapter()->lastInsertId());
            }
        }
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
    
    /**
     * ログ出力対象の設定を返す
     * 
     * @return array ログ出力対象の設定
     */
    public function getIncludes()
    {
        return $this->_includes;
    }
    
    /**
     * ログ出力除外対象の設定を返す
     * 
     * @return array ログ出力除外対象の設定
     */
    public function getExcludes()
    {
        return $this->_excludes;
    }

}

