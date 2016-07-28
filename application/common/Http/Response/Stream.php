<?php

/**
 * Common_Http_Response_Streamクラスのファイル
 * 
 * Common_Http_Response_Streamクラスを定義している
 *
 * @category   Zend
 * @package    Common_Http_Response
 * @subpackage Stream
 * @version    $Id$
 */

/**
 * Common_Http_Response
 * 
 * レスポンスボディへのアクセスを開放するために拡張したクラス
 *
 * @category   Zend
 * @package    Common_Http_Response
 * @subpackage Stream
 * @see Zend_Http_Response_Stream
 */
class Common_Http_Response_Stream extends Zend_Http_Response_Stream
{

    /**
     * レスポンスボディへのアクセスを開放
     * 
     * @param string $body レスポンスボディ
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
    
    /**
     * Common_Http_Response_Streamを返却する
     * 
     * @param Zend_Http_Response_Stream $response
     * @return \Common_Http_Response_Stream
     */
    public static function fromObject($response)
    {
        return new self($response->getStatus(), $response->getHeaders(), $response->getStream(), $response->getVersion, $response->getMessage());
    }

}