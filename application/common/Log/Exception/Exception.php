<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Exception
 *
 * @author tanbaa
 */
class Common_Log_Exception_Exception extends Common_Log_Exception_Abstract
{
    /** @var Exception 例外インスタンス */
    private $_exception;

    /** @var array 例外インスタンスのトレース内容 */
    private $_trace;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $configs      = new Zend_Config(Zend_Registry::get('log_configs'));
        $config       = $configs->toArray();
        $this->_config = $config['exception'];
    }

    /**
     * INFOログを出力する
     * 
     * @param string $message 出力するメッセージ
     */
    public function info($message)
    {
        
    }

    /**
     * ERRORログを出力する
     * 
     * @param string $message 出力するメッセージ
     */
    public function error($message)
    {
        if ($this->_exception)
        {

            if(strcmp($this->getIdType(), Common_Log_Abstract::ID_TYPE_HASH) == 0)
            {
                $this->_log->setEventItem('exception_log_id', md5(uniqid(mt_rand(),TRUE)));
            }
            
            $date = date('Y-m-d H:i:s');

            try
            {
                // ログのエラーファイルが存在する場合は、例外を投げ緊急処理へと移る
                if ($this->getMainWriter() instanceof Zend_Log_Writer_Db)
                {
                    if (Common_Log::isExistErrorFile() && !Common_Log::compareErrorFilesTimestamp())
                    {
                        throw new Common_Exception_Exception;
                    }
                }
                                               
                
                $this->_log
                    ->setEventItem('access_log_id', Common_Log::getAccessOnlineLog()->getLastInsertId())
                    ->setEventItem('log_level', Zend_Log::ERR)
                    ->setEventItem('log_message', $message)
                    ->setEventItem('exception_class', get_class($this->_exception))
                    ->setEventItem('code', $this->_exception->getCode())
                    ->setEventItem('file', $this->_exception->getFile())
                    ->setEventItem('class', $this->_getClass())
                    ->setEventItem('method', $this->_getMethod())
                    ->setEventItem('line', $this->_exception->getLine())
                    ->setEventItem('creation_date', $date)
                    ->setEventItem('updated_date', $date)
                    ->setEventItem('deleted_date', NULL);

                $this->_log->err($message);
            }
            catch (Exception $exc)
            {
                $commonConfig = Common_Log::getConfig();
                
                try
                {
                    // 例外がZend_Db_Adapter_Exceptionの場合は、設定ファイルのreconnect_timesの数だけ再接続を試みる
                    if ($exc instanceof Zend_Db_Adapter_Exception &&
                        isset($commonConfig['common']['error']['reconnect_times']) &&
                        strcmp($commonConfig['common']['error']['reconnect_times'], '0'))
                    {
                        for($reconnect_times = 0; $reconnect_times < $commonConfig['common']['error']['reconnect_times']; $reconnect_times++)
                        {
                            try
                            {
                                $this->_log->err($message);
                                break;
                            }
                            catch (Exception $exc)
                            {
                                if ($reconnect_times >= $commonConfig['common']['error']['reconnect_times']  - 1)
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
                        throw new $exc;
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
                            $emergencyLog = Common_Log::getExceptionLog(true);
                            $emergencyLog->setException($this->_exception);
                            $emergencyLog->error($message);
                        }

                    }
                    catch (Exception $exc)
                    {
                        // ファイルへの書き込みも失敗した場合は握りつぶす
                    }
                }                
            }
                        
            // 基盤系例外の場合は、ログ出力フラグを立てる
            if ($this->_exception instanceof Common_Exception_Abstract)
            {
                $this->_exception->isLoggedOn();
            }
        }
    }

    /**
     * 例外インスタンスをセットする
     *
     * @param Exception $e 例外インスタンス
     */
    public function setException(Exception $e)
    {
        $this->_exception = $e;
        $this->_trace     = $e->getTrace();
    }

    /**
     * 例外インタンスからクラス名を取得する
     *
     * @return string 例外を発生させたクラス名
     */
    private function _getClass()
    {
        return $this->_getTraceValue("class");
    }

    /**
     * 例外インスタンスからメソッド名を取得する
     *
     * @return string 例外を発生させたメソッド名
     */
    private function _getMethod()
    {
        return $this->_getTraceValue("function");
    }

    /**
     * 例外インスタンスから指定したキーに対応した情報を取得する
     *
     * @param $key 例外インスタンスが保持しているキーの名前
     * @return string キーに対応した例外インスタンスの情報
     */
    private function _getTraceValue($key)
    {
        $value = "";
        if ($this->_exception && $this->_trace)
        {
            $trace = $this->_trace[0];
            $value = $trace[$key];
        }

        return $value;
    }

}

