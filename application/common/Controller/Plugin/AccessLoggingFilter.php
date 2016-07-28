<?php

/**
 * Plugins_AccessLoggingFilterクラスのファイル
 * 
 * Plugins_AccessLoggingFilterクラスを定義している
 *
 * @category   Zend
 * @package    Controller
 * @subpackage Plugins
 * @version    $Id$
 */

/**
 * Plugins_AccessLoggingFilter
 * 
 * このフィルターを有効化することにより、
 * クライアントからのリクエストおよびレスポンスのログを自動的に記録する。
 * 有効化は {APPLICATION_PATH}/configs/plugins.yml で設定する。
 * デフォルトでは無効状態なので、任意で有効化の設定をすること。
 *
 * @category   Zend
 * @package    Controller
 * @subpackage Plugins
 */
class Common_Controller_Plugin_AccessLoggingFilter extends Common_Controller_Plugin_Abstract
{
    /** @var string プラグイン名(=plugins.ymlのキー項目) */

    const PLUGIN_NAME = 'access_logging_filter';

    /** @var int プラグインの優先順位 */

    const STACK_INDEX = 1;

    /** @var boolean ログ除外かどうか */
    private $_isLoggingTarget = FALSE;

    /**
     * ディスパッチ前に初期化処理
     *
     * @param Zend_Controller_Request_Abstract $request
     * @throws Common_Exception_FileNotFound
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // 親クラスの設定ファイル読み込み処理をコール
        parent::dispatchLoopStartup($request);

        // アクセスロギングの対象か判別
        $this->_isLoggingTarget = $this->_checkLoggingTarget(self::PLUGIN_NAME);
    }

    /**
     * リクエストログを記録する
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // リクエストされたモジュールがプラグイン無効ならば何もせず次の処理へ
        if ($this->_isDisableModule(self::PLUGIN_NAME))
        {
            return;
        }
                
        // エラーハンドラからフォワードされてきたのならば何もせずに次の処理へ
        if ($request->getParam('error_handler', null))
        {
            return;
        }
        
        if ($this->_isLoggingTarget)
        {
            // ログのIdがオートインクリメントかのフラグ
            $useIncrementId = true;

            $log = Common_Log::getAccessOnlineLog();
            $config = $log->getConfig();
            
            // rowsがfalse
            if (isset($config['input']['rows']) && !$config['input']['rows'])
            {
                return;
            }
            
            $hashValue = md5(uniqid(mt_rand(), TRUE));
            
            // ログに出力する値を取得する            
            $localHost = $this->_checkLoggingColumn($config, 'input', 'local_host') ? $log->getLocalHost() : NULL;
            $remoteHost = $this->_checkLoggingColumn($config, 'input', 'remote_host') ? $log->getRemoteHost() : NULL;
            $request_method = $this->_checkLoggingColumn($config, 'input', 'request_method') ? $log->getRequestMethod() : NULL;
            $http_version = $this->_checkLoggingColumn($config, 'input', 'http_version') ? $log->getHttpVersion() : NULL;            
            $user_agent = $this->_checkLoggingColumn($config, 'input', 'user_agent') ? $log->getUserAgent() : NULL;
            $user_agent = $this->_urlencode($user_agent);            
            $request_uri = $this->_checkLoggingColumn($config, 'input', 'request_uri') ? $log->getRequestUri() : NULL;
            $request_uri = $this->_urlencode($request_uri);
            $request_parameter = $this->_checkLoggingColumn($config, 'input', 'request_parameter') ? $log->formattingRequestParameters() : NULL;
            $request_parameter = $this->_urlencode($request_parameter);
            $http_header = $this->_checkLoggingColumn($config, 'input', 'http_header') ? $log->getRequestHeadersString() : NULL;
            $http_header = $this->_urlencode($http_header);
            $http_body = $this->_checkLoggingColumn($config, 'input', 'http_body') ? $log->getHttpBody() : NULL;
            $http_body = $this->_urlencode($http_body);
            $http_referer = $this->_checkLoggingColumn($config, 'input', 'http_referer') ? $log->getReferer() : NULL;
            $http_referer = $this->_urlencode($http_referer);
                        
            
            try
            {   
                // ログのエラーファイルが存在する場合は、例外を投げ緊急処理へと移る
                if ($log->getMainWriter() instanceof Zend_Log_Writer_Db)
                {
                    if (Common_Log::isExistErrorFile() && !Common_Log::compareErrorFilesTimestamp())
                    {
                        throw new Common_Exception_Exception();
                    }
                }
                
                $log->getLog()
                        ->setEventItem('request_access_log_id', NULL)
                        ->setEventItem('io_type', Common_Log_Abstract::IO_TYPE_REQUEST)
                        ->setEventItem('local_host', $localHost)
                        ->setEventItem('remote_host', $remoteHost)
                        ->setEventItem('request_method', $request_method)
                        ->setEventItem('request_uri', $request_uri)
                        ->setEventItem('request_parameter', $request_parameter)
                        ->setEventItem('http_version', $http_version)
                        ->setEventItem('http_status', NULL)
                        ->setEventItem('http_header', $http_header)
                        ->setEventItem('http_body', $http_body)
                        ->setEventItem('http_referer', $http_referer)
                        ->setEventItem('user_agent', $user_agent);

                if (strcmp($log->getIdType(), Common_Log_Abstract::ID_TYPE_HASH) == 0)
                {
                    $useIncrementId = false;
                    $log->getLog()->setEventItem('access_log_id', $hashValue);
                }

                $log->info();
            }
            catch (Exception $exc)
            {
                try 
                {
                    $commonConfig = Common_Log::getConfig();
                    
                    // 例外がZend_Db_Adapter_Exceptionの場合は、設定ファイルのreconnect_timesの数だけ再接続を試みる
                    if($exc instanceof Zend_Db_Adapter_Exception && 
                       isset($commonConfig['common']['error']['reconnect_times']) &&
                       strcmp($commonConfig['common']['error']['reconnect_times'], '0'))
                    {
                        for($reconnect_times = 0; $reconnect_times < $commonConfig['common']['error']['reconnect_times']; $reconnect_times++)
                        {
                            try
                            {
                                $log->info();
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

                        // 緊急処理
                        // DBへの書き込みが失敗した場合はファイルへの書き込みを試みる
                        if ($log->getMainWriter() instanceof Zend_Log_Writer_Db)
                        {                    
                            $emergencyLog = Common_Log::getAccessOnlineLog(true);

                            $emergencyLog->getLog()
                                    ->setEventItem('access_log_id', $hashValue)
                                    ->setEventItem('request_access_log_id', NULL)
                                    ->setEventItem('io_type', Common_Log_Abstract::IO_TYPE_REQUEST)
                                    ->setEventItem('local_host', $localHost)
                                    ->setEventItem('remote_host', $remoteHost)
                                    ->setEventItem('request_method', $request_method)
                                    ->setEventItem('request_uri', $request_uri)
                                    ->setEventItem('request_parameter', $request_parameter)
                                    ->setEventItem('http_version', $http_version)
                                    ->setEventItem('http_status', NULL)
                                    ->setEventItem('http_header', $http_header)
                                    ->setEventItem('http_body', $http_body)
                                    ->setEventItem('http_referer', $http_referer)
                                    ->setEventItem('user_agent', $user_agent);

                            $emergencyLog->info();                        
                            $emergencyLog->setLastInsertId($hashValue);

                            $useIncrementId = false;
                        }                    
                    }
                    catch (Exception $exc)
                    {
                        // ファイルへの出力も失敗した場合は握りつぶす
                    }
                }                
            }
                                    
            if ($useIncrementId === false)
            {
                // ハッシュの値をセットする。
                $log->setLastInsertId($hashValue);
            }
            else if ($config['database'])
            {
                $log->setLastInsertId(Common_Db::factoryByDbName($config['database'])->lastInsertId());
            }
            else
            {
                $log->setLastInsertId(Common_Log::getLogDbAdapter()->lastInsertId());
            }
        }
    }

    /**
     * レスポンスログを記録する
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        // リクエストされたモジュールがプラグイン無効ならば何もせず次の処理へ
        if ($this->_isDisableModule(self::PLUGIN_NAME))
        {
            return;
        }
        
        // エラーハンドラからフォワードされてきたのならば何もせずに次の処理へ
        if ($request->getParam('error_handler', null))
        {
            return;
        }

        if ($this->_isLoggingTarget)
        {
            $log = Common_Log::getAccessOnlineLog();            
            $config = $log->getConfig();
            
            // rowsがfalse
            if (isset($config['output']['rows']) && !$config['output']['rows'])
            {
                return;
            }
            
            $hashValue = md5(uniqid(mt_rand(), TRUE));
            
            // ログに出力する値を取得する
            $requestAccessLogId = $log->getLastInsertId();
            $httpHeader = $this->_checkLoggingColumn($config, 'output', 'http_header') ? $log->getResponseHeaderString() : NULL;
            $httpHeader = $this->_urlencode($httpHeader);
            $httpBody = $this->_checkLoggingColumn($config, 'output', 'http_body') ? $log->getResponseBody() : NULL;
            $httpBody = $this->_urlencode($httpBody);
            $httpStatus = $this->_checkLoggingColumn($config, 'output', 'http_status') ? $log->getHttpStatusCode() : NULL;
                                    
            try
            {
                // ログのエラーファイルが存在する場合は、例外を投げ緊急処理へと移る
                if ($log->getMainWriter() instanceof Zend_Log_Writer_Db)
                {
                    if (Common_Log::isExistErrorFile() && !Common_Log::compareErrorFilesTimestamp())
                    {
                        throw new Common_Exception_Exception();
                    }
                }
                
                if (strcmp($log->getIdType(), Common_Log_Abstract::ID_TYPE_HASH) == 0)
                {                
                    $log->getLog()->setEventItem('access_log_id', $hashValue);
                }

                $log->getLog()
                        ->setEventItem('request_access_log_id', $requestAccessLogId)
                        ->setEventItem('io_type', Common_Log_Abstract::IO_TYPE_RESPONSE)
                        ->setEventItem('local_host', NULL)
                        ->setEventItem('remote_host', NULL)
                        ->setEventItem('request_method', NULL)
                        ->setEventItem('request_uri', NULL)
                        ->setEventItem('request_parameter', NULL)
                        ->setEventItem('http_version', NULL)
                        ->setEventItem('http_status', $httpStatus)
                        ->setEventItem('http_header', $httpHeader)
                        ->setEventItem('http_body', $httpBody)
                        ->setEventItem('http_referer', NULL)
                        ->setEventItem('user_agent', NULL);
                
                $log->info();
            }
            catch (Exception $exc)
            {
                try 
                {
                    $commonConfig = Common_Log::getConfig();
                    
                    // 例外がZend_Db_Adapter_Exceptionの場合は、設定ファイルのreconnect_timesの数だけ再接続を試みる
                    if($exc instanceof Zend_Db_Adapter_Exception && 
                       isset($commonConfig['common']['error']['reconnect_times']) &&
                       strcmp($commonConfig['common']['error']['reconnect_times'], '0'))
                    {
                        for($reconnect_times = 0; $reconnect_times < $commonConfig['common']['error']['reconnect_times']; $reconnect_times++)
                        {
                            try
                            {
                                $log->info();
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

                        // 緊急処理
                        // DBへの書き込みが失敗した場合はファイルへの書き込みを試みる
                        if ($log->getMainWriter() instanceof Zend_Log_Writer_Db)
                        {

                            $emergencyLog = Common_Log::getAccessOnlineLog(true);
                            $emergencyLog->getLog()
                                    ->setEventItem('access_log_id', $hashValue)
                                    ->setEventItem('request_access_log_id', $requestAccessLogId)
                                    ->setEventItem('io_type', Common_Log_Abstract::IO_TYPE_RESPONSE)
                                    ->setEventItem('local_host', NULL)
                                    ->setEventItem('remote_host', NULL)
                                    ->setEventItem('request_method', NULL)
                                    ->setEventItem('request_uri', NULL)
                                    ->setEventItem('request_parameter', NULL)
                                    ->setEventItem('http_version', NULL)
                                    ->setEventItem('http_status', $httpStatus)
                                    ->setEventItem('http_header', $httpHeader)
                                    ->setEventItem('http_body', $httpBody)
                                    ->setEventItem('http_referer', NULL)
                                    ->setEventItem('user_agent', NULL);

                            $emergencyLog->info();
                        }                    
                    }
                    catch (Exception $exc)
                    {
                        // ファイルへの出力も失敗した場合は握りつぶす
                    }
                }
            }            
        }
    }
    
    /**
     * 指定したカラムがログ出力対象か判定する
     * 
     * @param array $config ログの設定を格納した連想配列
     * @param string $ioType input:リクエストログ, output:レスポンスログ
     * @param string $columnName 判定するカラム名
     * @return boolean true: ログ出力対象, false: ログ出力対象外
     */
    private function _checkLoggingColumn($config, $ioType, $columnName)
    {                    
        // 個別(input,output)の設定がある場合をそちらを見る
        if (isset($config[$ioType]['columnMapping'][$columnName]))
        {
            return (bool)$config[$ioType]['columnMapping'][$columnName];
        }

        // 個別(input,output)の設定がない場合は出力する
        return true;        
    }
    
    /**
     * 文字列をurlencodeする
     * 
     * @param string $str 変換する文字列
     * @return string
     */
    private function _urlencode($str)
    {
        if (is_string($str))
        {
            if (!Common_Util_String::containsOnlySingleByteChars($str))
            {
                $str = urlencode($str);
            }
        }
        
        return $str;
    }

}
