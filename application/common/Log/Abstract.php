<?php

/**
 * Common_Log_Abstractクラスのファイル
 * 
 * Common_Log_Abstractクラスを定義している
 *
 * @category Zend
 * @package  Common_Log
 * @version  $Id$
 */

/**
 * 共通ロガー基底クラスファイル
 *
 * @category  Zend
 * @package   Common_Log
 * @version   $Id$
 */
abstract class Common_Log_Abstract
{
    /** @var int IO種別(リクエスト) */
    const IO_TYPE_REQUEST = 0;

    /** @var int IO種別(レスポンス) */
    const IO_TYPE_RESPONSE = 1;
    
    /** @var int IDの種類(autoincrement)*/
    const ID_TYPE_AUTOINCREMENT = 'autoincrement';

    /** @var int IDの種類(hash)*/
    const ID_TYPE_HASH = 'hash';

    /** @var array ロガー設定 */
    protected $_config;

    /** @var Zend_Log */
    protected $_log;
    
    /** @var Zend_Log_Writer_Abstract 使用しているZend_Log_Writer*/
    protected $_writer;
    
    /** @var string ログIdの種類*/
    protected $_idType;

    /**
     * HTTPリクエストのbody部分を返す
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string HTTPリクエストのbody部分
     */
    public function getHttpBody($request = NULL)
    {
        return $this->_getRequest($request)->getRawBody();
    }

    /**
     * HTTPリクエストのheader部分を返す
     *
     * 以下の形式で返します
     * <pre>
     * header名△:△header値\n
     *
     * △ = 半角スペース
     * </pre>
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string HTTPリクエストのheader部分
     */
    public function getRequestHeadersString($request = NULL)
    {
        $request = $this->_getRequest($request);

        if (function_exists('apache_request_headers'))
        {
            $headers = apache_request_headers();
        }
        else
        {

            // http://php.net/manual/ja/function.apache-request-headers.phpより
            // apache_request_headersの代替処理
            $server  = $request->getServer();
            $headers = array();
            foreach ($server as $key => $value)
            {
                if (substr($key, 0, 5) == "HTTP_")
                {
                    $key           = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
                    $headers[$key] = $value;
                }
            }
        }

        return $this->_formattingHeaderParameters($headers);
    }

    /**
     * HTTPリクエストのリファラーを返します
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string HTTPリクエストのリファラー
     */
    public function getReferer($request = NULL)
    {
        return $this->_getRequest($request)->getHeader('referer');
    }

    /**
     * HTTPレスポンスのステータスコードを返します
     *
     * @param Zend_Controller_Response_Abstract $response
     * @return string HTTPレスポンスのステータスコード
     */
    public function getHttpStatusCode($response = NULL)
    {
        return $this->_getResponse($response)->getHttpResponseCode();
    }

    /**
     * サーバのホスト名を返します
     *
     * 接続元（クライアント）のホスト名を返すメソッドは以下になります
     *
     * <code>
     * Common_Log_Abstract#getRemoteHost();
     * </code>
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string サーバのホスト名
     */
    public function getLocalHost($request = NULL)
    {
        return $this->_getRequest($request)->getHttpHost();
    }

    /**
     * 接続元（クライアント）のホスト名を返します
     *
     * サーバのホスト名を返すメソッドは以下になります
     *
     * <code>
     * Common_Log_Abstract#getLocalHost();
     * </code>
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string 接続元（クライアント）のホスト名
     */
    public function getRemoteHost($request = NULL)
    {
        return $this->_getRequest($request)->getClientIp();
    }

    /**
     * HTTPリクエストメソッドを返します
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string HTTPリクエストメソッド
     */
    public function getRequestMethod($request = NULL)
    {
        return $this->_getRequest($request)->getMethod();
    }

    /**
     * HTTPリクエストのパラメータを返します
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return array HTTPリクエストのパラメータ
     */
    public function getRequestParameters($request = NULL)
    {
        return $this->_getRequest($request)->getParams();
    }

    /**
     * HTTPリクエストパラメータを以下の形式にフォーマットします
     *
     * <pre>
     * name△=△value\n
     *
     * △ = 半角スペース
     * </pre>
     *
     * @return string フォーマット済みのHTTPリクエストパラメータ
     */
    public function formattingRequestParameters()
    {
        $parameters       = $this->getRequestParameters();
        $parameterStrings = array();
        foreach ($parameters as $name => $value)
        {
            $parameterStrings[] = $name . '=' . $value;
        }

        return implode("\n", $parameterStrings)
            ;
    }

    /**
     * HTTPリクエストのURIを返します
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string HTTPリクエストのURI
     */
    public function getRequestUri($request = NULL)
    {
        return $this->_getRequest($request)->getRequestUri();
    }

    /**
     * HTTPリクエストのUserAgentを返します
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string HTTPリクエストのUserAgent
     */
    public function getUserAgent($request = NULL)
    {
        return $this->_getRequest($request)->getHeader('user-agent');
    }

    /**
     * PHPのBackTraceを返します
     *
     * 引数 <code>$keys</code> にBackTraceのキー項目を受け取るとそのキー項目に対応したBackTraceを返します
     * <code>$keys</code> が渡されていない、もしくは、BackTraceで無効なキーの場合、<br>
     * debug_backtrace関数の内容をそのまま返します。
     * 以下の例は、呼び出しもとのクラス名とメソッド名を取得します。
     * <pre>
     * $debugTrace = $this->getBacktrace(array('class', 'function'));
     * </pre>
     *
     * @param array $keys BackTraceで有効なキー項目の配列
     * @return array BackTrace
     */
    public function getBacktrace($keys = array())
    {
        $debugTrace = debug_backtrace();

        if (!$keys || !is_array($keys))
        {
            return $debugTrace;
        }
        $results = array();
        foreach ($keys as $key)
        {
            if (isset($debugTrace[2][$key]))
            {
                $results[$key] = $debugTrace[2][$key];
            }
        }

        return $results;
    }

    /**
     * Zend_Logをセットします
     *
     * @param Zend_Log $log
     */
    public function setLog(Zend_Log $log)
    {
        $this->_log = $log;
    }

    /**
     * ロガー設定を返します
     *
     * @return array ロガー設定
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * FrontControllerからリクエストインスタンスを返します
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_Controller_Request_Http
     */
    private function _getRequest($request = NULL)
    {
        if (is_null($request) || !($request instanceof Zend_Controller_Request_Abstract))
        {
            $request = Zend_Controller_Front::getInstance()
                    ->getRequest();
        }

        return $request;
    }

    /**
     * FrontControllerからレスポンスインスタンスを返します
     *
     * @param Zend_Controller_Response_Abstract $response
     * @return Zend_Controller_Response_Abstract
     */
    private function _getResponse($response = NULL)
    {
        if (is_null($response) || !($response instanceof Zend_Controller_Response_Abstract))
        {
            $response = Zend_Controller_Front::getInstance()
                    ->getResponse();
        }

        return $response;
    }

    /**
     * Httpバージョンを返します
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string Httpバージョン
     */
    public function getHttpVersion($request = NULL)
    {
        $version = '';

        $rawVersion = $this->_getRequest($request)->getServer("SERVER_PROTOCOL");
        if ($rawVersion)
        {
            $splitedVersion = explode("/", $rawVersion);
            $version        = $splitedVersion[1];
        }

        return $version;
    }

    /**
     * レスポンスボディを返す
     *
     * @return string レスポンスボディ
     */
    public function getResponseBody()
    {
        return $this->_getResponse()->getBody();
    }

    /**
     * レスポンスヘッダを返す
     *
     * @return string レスポンスヘッダ
     */
    public function getResponseHeaderString()
    {
        $headers = array();

        foreach ($this->_getResponse()->getHeaders() as $header)
        {
            $headers[] = $header["name"] . ": " . $header["value"];
        }

        if (function_exists("apache_response_headers"))
        {
            $funcname = "apache_response_headers";
        }
        else
        {
            $funcname = "headers_list";
        }

        $headers = array_merge($funcname(), $headers);

        return implode("\n", $headers);
    }

    /**
     * ヘッダの配列を文字列に整形する
     *
     * @param array $headers request/responseヘッダの配列
     * @return string 整形されたヘッダの文字列
     */
    private function _formattingHeaderParameters(array $headers)
    {
        $headerStrings = array();

        foreach ($headers as $headerName => $headerValue)
        {
            $headerStrings[] = $headerName . ': ' . $headerValue;
        }

        return implode("\n", $headerStrings);
    }
        
    /**
     * メインに使用するZend_Log_Writerの種類をセットする
     * 
     * @param Zend_Log_Writer_Abstract $writer 使用しているZend_Log_Writer
     */
    public function setMainWriter($writer)
    {
        $this->_writer = $writer;
    }
    
    /**
     * メインで使用しているZend_Log_Writeの種類を返す
     * 
     * @return Zend_Log_Writer_Abstract 使用しているZend_Log_Writer
     */
    public function getMainWriter()
    {
        return $this->_writer;
    }

    /**
     * ログのIdの種類をセットする
     * 
     * @param string $type ログIdの種類 autoIncrement or hash
     */
    public function setIdType($type)
    {
        $this->_idType = $type;
    }
    
    /**
     * ログのIdの種類を返す
     * 
     * @return string ログのIdの種類
     */
    public function getIdType()
    {
        return $this->_idType;
    }
    
    /**
     * ライターを追加する
     * 
     * @param Zend_Log_Writer_Abstract $writer 追加するZend_Log_Writer
     */
    public function addWriter($writer) 
    {
        $this->_log->addWriter($writer);
    }
}
