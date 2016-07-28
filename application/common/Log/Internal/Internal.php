<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Internal
 *
 * @author tanbaa
 */
class Common_Log_Internal_Internal extends Common_Log_Internal_Abstract
{

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $configs      = new Zend_Config(Zend_Registry::get('log_configs'));
        $config       = $configs->toArray();
        $this->_config = $config['internal'];
    }

    /**
     * DEBUGログを出力する
     * 
     * @param string $message 出力するメッセージ
     */
    public function debug($message = "")
    {        
        try
        {
            // ログのエラーファイルが存在する場合は、例外を投げ緊急処理へと移る
            if ($this->getMainWriter() instanceof Zend_Log_Writer_Db)
            {
                if (Common_Log::isExistErrorFile() && !Common_Log::compareErrorFilesTimestamp())
                {
                    throw new Common_Exception_Exception();
                }
            }
            
            $this->_setEventItem($message, Zend_Log::DEBUG);
            $this->_log->debug($message);
        }
        catch (Exception $exc)
        {
            try
            {
                $commonConfig = Common_Log::getConfig();
                
                // 例外がZend_Db_Adapter_Exceptionの場合は、設定ファイルのreconnect_timesの数だけ再接続を試みる
                if ($exc instanceof Zend_Db_Adapter_Exception &&
                    isset($commonConfig['common']['error']['reconnect_times']) &&
                    strcmp($commonConfig['common']['error']['reconnect_times'], '0'))
                {
                    for($reconnect_times = 0; $reconnect_times < $commonConfig['common']['error']['reconnect_times']; $reconnect_times++)
                    {
                        try
                        {
                            $this->_log->debug($message);
                            break;
                        }
                        catch (Exception $exc)
                        {
                            if ($reconnect_times >= $commonConfig['common']['error']['reconnect_times'] - 1)
                            {
                                // 再接続も失敗した場合はファイル出力を試みる
                                throw $exc;
                            }
                        }                            
                    }
                }
                // それ以外の場合はファイル出力を試みる
                else
                {
                    throw $exc;
                }
            }
            catch (Exception $exc)
            {
                try
                {
                    // 例外がZend_Db_Adapter_Exceptionの場合はファイルを出力し、
                    // 以降のそのファイルがある限り、ログはファイルへの出力を試みる
                    if ($exc instanceof Zend_Db_Adapter_Exception)
                    {
                        Common_Log::outputErrorFile($exc);
                    }

                    // DBへの書き込みが失敗した場合はファイルへの書き込みを試みる
                    if ($this->getMainWriter() instanceof Zend_Log_Writer_Db)
                    {                
                        $emergencyLog = Common_Log::getInternalLog(true);
                        $emergencyLog->debug($message);
                    }                
                }
                catch (Exception $exc)
                {
                    // ファイルへの書き込みも失敗するようならば握りつぶす
                }
            }
        }
    }

    /**
     * INFOログを出力する
     * 
     * @param string $message 出力するメッセージ
     */
    public function info($message = "")
    {
        try
        {
            // ログのエラーファイルが存在する場合は、例外を投げ緊急処理へと移る
            if ($this->getMainWriter() instanceof Zend_Log_Writer_Db)
            {
                if (Common_Log::isExistErrorFile() && !Common_Log::compareErrorFilesTimestamp())
                {
                    throw new Common_Exception_Exception();
                }
            }
            
            $this->_setEventItem($message, Zend_Log::INFO);
            $this->_log->info($message);
        }
        catch (Exception $exc)
        {
            try
            {
                $commonConfig = Common_Log::getConfig();
                
                // 例外がZend_Db_Adapter_Exceptionの場合は、設定ファイルのreconnect_timesの数だけ再接続を試みる
                if ($exc instanceof Zend_Db_Adapter_Exception &&
                    isset($commonConfig['common']['error']['reconnect_times']) &&
                    strcmp($commonConfig['common']['error']['reconnect_times'], '0'))
                {
                    for($reconnect_times = 0; $reconnect_times < $commonConfig['common']['error']['reconnect_times']; $reconnect_times++)
                    {
                        try
                        {
                            $this->_log->info($message);
                            break;
                        }
                        catch (Exception $exc)
                        {
                            if ($reconnect_times >= $commonConfig['common']['error']['reconnect_times'] - 1)
                            {
                                // 再接続も失敗した場合はファイル出力を試みる
                                throw $exc;
                            }
                        }                            
                    }
                }
                // それ以外の場合はファイル出力を試みる
                else
                {
                    throw $exc;
                }
            }
            catch (Exception $exc)
            {
                try
                {
                    // 例外がZend_Db_Adapter_Exceptionの場合はファイルを出力し、
                    // 以降のそのファイルがある限り、ログはファイルへの出力を試みる
                    if ($exc instanceof Zend_Db_Adapter_Exception)
                    {
                        Common_Log::outputErrorFile($exc);
                    }

                    // DBへの書き込みが失敗した場合はファイルへの書き込みを試みる
                    if ($this->getMainWriter() instanceof Zend_Log_Writer_Db)
                    {                
                        $emergencyLog = Common_Log::getInternalLog(true);
                        $emergencyLog->info($message);
                    }                
                }
                catch (Exception $exc)
                {
                    // ファイルへの書き込みも失敗するようならば握りつぶす
                }
            }
        }        
    }

    /**
     * ログ出力する項目と内容をセットする
     * 
     * @param string $message 出力するメッセージ
     * @param int $logLevel ログレベル
     */
    private function _setEventItem($message, $logLevel = Zend_Log::INFO)
    {
        $date = date('Y-m-d H:i:s');
        
        if (strcmp($this->getIdType(), Common_Log_Abstract::ID_TYPE_HASH) == 0)
        {
            $this->_log->setEventItem('internal_log_id', md5(uniqid(mt_rand(), TRUE)));
        }

        $this->_log
                ->setEventItem('access_log_id', Common_Log::getAccessOnlineLog()->getLastInsertId())
                ->setEventItem('log_level', $logLevel)
                ->setEventItem('log_message', $message)
                ->setEventItem('creation_date', $date)
                ->setEventItem('updated_date', $date)
                ->setEventItem('deleted_date', NULL);
    }

}

