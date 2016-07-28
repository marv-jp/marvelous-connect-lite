<?php

/**
 * Common_Http_Clientクラスのファイル
 * 
 * Common_Http_Clientクラスを定義している
 *
 * @category   Zend
 * @package    Common_Http
 * @subpackage Client
 * @version    $Id$
 */

/**
 * Common_Http_Client
 * 
 * APIリクエストの際に、Zend_Http_Client のリクエスト/レスポンスのログを
 * 記録する機能を追加したクラス
 *
 * @category   Zend
 * @package    Common_Http
 * @subpackage Client
 * @see Zend_Http_Client
 */
class Common_Http_Client extends Zend_Http_Client
{
    /** @var string プラットフォーム名 */
    private $_platformName = null;

    /** @var string api名 */
    private $_apiName = null;

    /**
     * uriをセットする
     * 
     * オーバーロードが出来ない関係上プラットフォーム名とapi名に初期値を与えていますが、動作的には必須パラメータです。
     * 
     * @param Zend_Uri_Http|string $uri uri
     * @param string $platformName プラットフォーム名
     * @param string $apiName api名
     * @return Common_Http_Client Common_Http_Clientインスタンス
     */
    public function setUri($uri, $platformName = "", $apiName = "")
    {
        $this->_platformName = $platformName;
        $this->_apiName      = $apiName;

        return parent::setUri($uri);
    }

    /**
     * Httpリクエスト
     * 
     * @param string $method リクエストメソッド
     * @return Zend_Http_Response Httpレスポンス
     * @throws Common_Http_Client_Exception
     */
    public function request($method = null)
    {
        if (!$this->_platformName) {
            throw new Common_Http_Client_Exception('プラットフォーム名をセットして下さい');
        }

        if (!$this->_apiName) {
            throw new Common_Http_Client_Exception('api名をセットして下さい');
        }

        // orginal code start
        if (!$this->uri instanceof Zend_Uri_Http) {
            /** @see Zend_Http_Client_Exception */
            require_once 'Zend/Http/Client/Exception.php';
            throw new Zend_Http_Client_Exception('No valid URI has been passed to the client');
        }

        if ($method) {
            $this->setMethod($method);
        }
        $this->redirectCounter = 0;
        $response              = null;

        // Make sure the adapter is loaded
        if ($this->adapter == null) {
            $this->setAdapter($this->config['adapter']);
        }

        // Send the first request. If redirected, continue.
        do {
            // Clone the URI and add the additional GET parameters to it
            $uri = clone $this->uri;
            if (!empty($this->paramsGet)) {
                $query = $uri->getQuery();
                if (!empty($query)) {
                    $query .= '&';
                }
                $query .= http_build_query($this->paramsGet, null, '&');
                if ($this->config['rfc3986_strict']) {
                    $query = str_replace('+', '%20', $query);
                }

                // @see ZF-11671 to unmask for some services to foo=val1&foo=val2
                if ($this->getUnmaskStatus()) {
                    if ($this->_queryBracketsEscaped) {
                        $query = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
                    } else {
                        $query = preg_replace('/\\[(?:[0-9]|[1-9][0-9]+)\\]=/', '=', $query);
                    }
                }

                $uri->setQuery($query);
            }

            $body    = $this->_prepareBody();
            $headers = $this->_prepareHeaders();

            // check that adapter supports streaming before using it
            if (is_resource($body) && !($this->adapter instanceof Zend_Http_Client_Adapter_Stream)) {
                /** @see Zend_Http_Client_Exception */
                require_once 'Zend/Http/Client/Exception.php';
                throw new Zend_Http_Client_Exception('Adapter does not support streaming');
            }

            // Open the connection, send the request and read the response
            $this->adapter->connect($uri->getHost(), $uri->getPort(), ($uri->getScheme() == 'https' ? true : false));

            if ($this->config['output_stream']) {
                if ($this->adapter instanceof Zend_Http_Client_Adapter_Stream) {
                    $stream = $this->_openTempStream();
                    $this->adapter->setOutputStream($stream);
                } else {
                    /** @see Zend_Http_Client_Exception */
                    require_once 'Zend/Http/Client/Exception.php';
                    throw new Zend_Http_Client_Exception('Adapter does not support streaming');
                }
            }
            // original code end
            // リクエストログを出力
            $log = Common_Log::getExternalLog();

            // ログ除外対象か確認
            $isLogging = true;
            $excludes  = $log->getExcludes();
            $includes  = $log->getIncludes();

            if ($excludes) {
                foreach ($excludes as $exclude) {
                    if ($exclude['api'] && $exclude['platform']) {
                        if (strcmp($exclude['api'], $this->_apiName) == 0 &&
                                strcmp($exclude['platform'], $this->_platformName) == 0) {
                            $isLogging = false;
                            break;
                        }
                    } else if ($exclude['platform']) {
                        if (strcmp($exclude['platform'], $this->_platformName) == 0) {
                            $isLogging = false;
                            break;
                        }
                    }
                }
            }
            // ログ出力対象か確認
            else if ($includes) {
                $isLogging = false;
                foreach ($includes as $include) {
                    if ($include['api'] && $include['platform']) {
                        if (strcmp($include['api'], $this->_apiName) == 0 &&
                                strcmp($include['platform'], $this->_platformName) == 0) {
                            $isLogging = true;
                            break;
                        }
                    } else if ($include['platform']) {
                        if (strcmp($include['platform'], $this->_platformName) == 0) {
                            $isLogging = true;
                            break;
                        }
                    }
                }
            }


            if ($isLogging) {
                try {
                    // ログのエラーファイルが存在する場合は、例外を投げ緊急処理へと移る
                    if ($log->getMainWriter() instanceof Zend_Log_Writer_Db) {
                        if (Common_Log::isExistErrorFile() && !Common_Log::compareErrorFilesTimestamp()) {
                            throw new Common_Exception_Exception();
                        }
                    }

                    $log->getLog()
                            ->setEventItem('access_log_id', Common_Log::getAccessOnlineLog()->getLastInsertId())
                            ->setEventItem('request_external_log_id', NULL)
                            ->setEventItem('platform', $this->_platformName)
                            ->setEventItem('api_name', $this->_apiName)
                            ->setEventItem('io_type', Common_Log_Abstract::IO_TYPE_REQUEST)
                            ->setEventItem('local_host', $log->getLocalHost())
                            ->setEventItem('remote_host', $uri->getHost())
                            ->setEventItem('request_method', $this->method)
                            ->setEventItem('request_uri', str_replace('?' . $uri->getQuery(), '', $uri->getUri()))
                            ->setEventItem('request_parameter', $uri->getQuery())
                            ->setEventItem('http_version', NULL)
                            ->setEventItem('http_status', NULL)
                            ->setEventItem('http_header', implode("\n", $headers))
                            ->setEventItem('http_body', $body)
                            ->setEventItem('http_referer', NULL)
                            ->setEventItem('user_agent', NULL);
                    $log->info();
                } catch (Exception $exc) {
                    try {
                        $commonConfig = Common_Log::getConfig();

                        // 例外がZend_Db_Adapter_Exceptionの場合は、設定ファイルのreconnect_timesの数だけ再接続を試みる
                        if ($exc instanceof Zend_Db_Adapter_Exception &&
                                isset($commonConfig['common']['error']['reconnect_times']) &&
                                strcmp($commonConfig['common']['error']['reconnect_times'], '0')) {
                            for ($reconnect_times = 0; $reconnect_times < $commonConfig['common']['error']['reconnect_times']; $reconnect_times++) {
                                try {
                                    $log->info();
                                    break;
                                } catch (Exception $exc) {
                                    if ($reconnect_times >= $commonConfig['common']['error']['reconnect_times'] - 1) {
                                        // 再接続も失敗した場合はファイル出力を試みる
                                        throw $exc;
                                    }
                                }
                            }
                        }
                        // それ以外の場合はファイル出力を試みる
                        else {
                            throw $exc;
                        }
                    } catch (Exception $exc) {
                        try {
                            // 例外がZend_Db_Adapter_Exceptionの場合はファイルを出力し、
                            // 以降のそのファイルがある限り、ログはファイルへの出力を試みる
                            if ($exc instanceof Zend_Db_Adapter_Exception) {
                                Common_Log::outputErrorFile($exc);
                            }

                            // DBへの書き出しが失敗した場合はファイルへの書き出しを試みる
                            if ($log->getMainWriter() instanceof Zend_Log_Writer_Db) {
                                $emergencyLog = Common_Log::getExternalLog(true);
                                $emergencyLog->getLog()
                                        ->setEventItem('access_log_id', Common_Log::getAccessOnlineLog()->getLastInsertId())
                                        ->setEventItem('request_external_log_id', NULL)
                                        ->setEventItem('platform', $this->_platformName)
                                        ->setEventItem('api_name', $this->_apiName)
                                        ->setEventItem('io_type', Common_Log_Abstract::IO_TYPE_REQUEST)
                                        ->setEventItem('local_host', $log->getLocalHost())
                                        ->setEventItem('remote_host', $uri->getHost())
                                        ->setEventItem('request_method', $this->method)
                                        ->setEventItem('request_uri', str_replace('?' . $uri->getQuery(), '', $uri->getUri()))
                                        ->setEventItem('request_parameter', $uri->getQuery())
                                        ->setEventItem('http_version', NULL)
                                        ->setEventItem('http_status', NULL)
                                        ->setEventItem('http_header', implode("\n", $headers))
                                        ->setEventItem('http_body', $body)
                                        ->setEventItem('http_referer', NULL)
                                        ->setEventItem('user_agent', NULL);
                                $emergencyLog->info();

                                $log->setLastInsertId($emergencyLog->getLastInsertId());
                            }
                        } catch (Exception $exc) {
                            // ファイルへの書き出しも失敗する場合は握りつぶす
                        }
                    }
                }
            }

            // original code start
            $this->last_request = $this->adapter->write($this->method, $uri, $this->config['httpversion'], $headers, $body);

            $response = $this->adapter->read();
            if (!$response) {
                /** @see Zend_Http_Client_Exception */
                require_once 'Zend/Http/Client/Exception.php';
                throw new Zend_Http_Client_Exception('Unable to read response, or response is empty');
            }

            if ($this->config['output_stream']) {
                $streamMetaData = stream_get_meta_data($stream);
                if ($streamMetaData['seekable']) {
                    rewind($stream);
                }
                // cleanup the adapter
                $this->adapter->setOutputStream(null);
                $response = Zend_Http_Response_Stream::fromStream($response, $stream);
                $response->setStreamName($this->_stream_name);
                if (!is_string($this->config['output_stream'])) {
                    // we used temp name, will need to clean up
                    $response->setCleanup(true);
                }
            } else {
                $response = Zend_Http_Response::fromString($response);
            }

            if ($this->config['storeresponse']) {
                $this->last_response = $response;
            }

            // Load cookies into cookie jar
            if (isset($this->cookiejar)) {
                $this->cookiejar->addCookiesFromResponse($response, $uri, $this->config['encodecookies']);
            }

            // If we got redirected, look for the Location header
            if ($response->isRedirect() && ($location = $response->getHeader('location'))) {

                // Avoid problems with buggy servers that add whitespace at the
                // end of some headers (See ZF-11283)
                $location = trim($location);

                // Check whether we send the exact same request again, or drop the parameters
                // and send a GET request
                if ($response->getStatus() == 303 ||
                        ((!$this->config['strictredirects']) && ($response->getStatus() == 302 ||
                        $response->getStatus() == 301))) {

                    $this->resetParameters();
                    $this->setMethod(self::GET);
                }

                // If we got a well formed absolute URI
                if (($scheme = substr($location, 0, 6)) && ($scheme == 'http:/' || $scheme == 'https:')) {
                    $this->setHeaders('host', null);
                    $this->setUri($location);
                } else {

                    // Split into path and query and set the query
                    if (strpos($location, '?') !== false) {
                        list($location, $query) = explode('?', $location, 2);
                    } else {
                        $query = '';
                    }
                    $this->uri->setQuery($query);

                    // Else, if we got just an absolute path, set it
                    if (strpos($location, '/') === 0) {
                        $this->uri->setPath($location);

                        // Else, assume we have a relative path
                    } else {
                        // Get the current path directory, removing any trailing slashes
                        $path = $this->uri->getPath();
                        $path = rtrim(substr($path, 0, strrpos($path, '/')), "/");
                        $this->uri->setPath($path . '/' . $location);
                    }
                }
                ++$this->redirectCounter;
            } else {
                // If we didn't get any location, stop redirecting
                break;
            }
        }
        while ($this->redirectCounter < $this->config['maxredirects']);
        // original code end
        // レスポンスログを出力
        if ($isLogging) {
            try {
                // ログのエラーファイルが存在する場合は、例外を投げ緊急処理へと移る
                if ($log->getMainWriter() instanceof Zend_Log_Writer_Db) {
                    if (Common_Log::isExistErrorFile() && !Common_Log::compareErrorFilesTimestamp()) {
                        throw new Common_Exception_Exception();
                    }
                }

                $log->getLog()
                        ->setEventItem('access_log_id', Common_Log::getAccessOnlineLog()->getLastInsertId())
                        ->setEventItem('request_external_log_id', $log->getLastInsertId())
                        ->setEventItem('platform', $this->_platformName)
                        ->setEventItem('api_name', $this->_apiName)
                        ->setEventItem('io_type', Common_Log_Abstract::IO_TYPE_RESPONSE)
                        ->setEventItem('local_host', NULL)
                        ->setEventItem('remote_host', NULL)
                        ->setEventItem('request_method', NULL)
                        ->setEventItem('request_uri', NULL)
                        ->setEventItem('request_parameter', NULL)
                        ->setEventItem('http_version', NULL)
                        ->setEventItem('http_status', $response->getStatus())
                        ->setEventItem('http_header', $response->getHeadersAsString())
                        ->setEventItem('http_body', $response->getBody())
                        ->setEventItem('http_referer', NULL)
                        ->setEventItem('user_agent', NULL);
                $log->info();
            } catch (Exception $exc) {
                try {
                    $commonConfig = Common_Log::getConfig();

                    // 例外がZend_Db_Adapter_Exceptionの場合は、設定ファイルのreconnect_timesの数だけ再接続を試みる
                    if ($exc instanceof Zend_Db_Adapter_Exception &&
                            isset($commonConfig['common']['error']['reconnect_times']) &&
                            strcmp($commonConfig['common']['error']['reconnect_times'], '0')) {
                        for ($reconnect_times = 0; $reconnect_times < $commonConfig['common']['error']['reconnect_times']; $reconnect_times++) {
                            try {
                                $log->info();
                                break;
                            } catch (Exception $exc) {
                                if ($reconnect_times >= $commonConfig['common']['error']['reconnect_times'] - 1) {
                                    // 再接続も失敗した場合はファイル出力を試みる
                                    throw $exc;
                                }
                            }
                        }
                    }
                    // それ以外の場合はファイル出力を試みる
                    else {
                        throw $exc;
                    }
                } catch (Exception $exc) {
                    try {
                        // 例外がZend_Db_Adapter_Exceptionの場合はファイルを出力し、
                        // 以降のそのファイルがある限り、ログはファイルへの出力を試みる
                        if ($exc instanceof Zend_Db_Adapter_Exception) {
                            Common_Log::outputErrorFile($exc);
                        }

                        // DBへの書き出しが失敗した場合はファイルへの書き出しを試みる
                        if ($log->getMainWriter() instanceof Zend_Log_Writer_Db) {
                            $emergencyLog = Common_Log::getExternalLog(true);
                            $emergencyLog->getLog()
                                    ->setEventItem('access_log_id', Common_Log::getAccessOnlineLog()->getLastInsertId())
                                    ->setEventItem('request_external_log_id', $log->getLastInsertId())
                                    ->setEventItem('platform', $this->_platformName)
                                    ->setEventItem('api_name', $this->_apiName)
                                    ->setEventItem('io_type', Common_Log_Abstract::IO_TYPE_RESPONSE)
                                    ->setEventItem('local_host', NULL)
                                    ->setEventItem('remote_host', NULL)
                                    ->setEventItem('request_method', NULL)
                                    ->setEventItem('request_uri', NULL)
                                    ->setEventItem('request_parameter', NULL)
                                    ->setEventItem('http_version', NULL)
                                    ->setEventItem('http_status', $response->getStatus())
                                    ->setEventItem('http_header', $response->getHeadersAsString())
                                    ->setEventItem('http_body', $response->getBody())
                                    ->setEventItem('http_referer', NULL)
                                    ->setEventItem('user_agent', NULL);
                            $emergencyLog->info();
                        }
                    } catch (Exception $exc) {
                        // ファイルへの書き出しも失敗する場合は握りつぶす
                    }
                }
            }
        }

        return $response;
    }

    /**
     * api名を返す
     * 
     * @return string api名
     */
    public function getApiName()
    {
        return $this->_apiName;
    }

    /**
     * api名をセットする
     * 
     * @param string $apiName api名
     */
    public function setApiName($apiName)
    {
        $this->_apiName = $apiName;
    }

    /**
     * プラットフォーム名を返す
     * 
     * @return string プラットフォーム名
     */
    public function getPlatformName()
    {
        return $this->_platformName;
    }

    /**
     * プラットフォーム名をセットする
     * 
     * @param string $platformName プラットフォーム名
     */
    public function setPlatformName($platformName)
    {
        $this->_platformName = $platformName;
    }

}