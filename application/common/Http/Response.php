<?php

/**
 * Common_Http_Responseクラスのファイル
 * 
 * Common_Http_Responseクラスを定義している
 *
 * @category   Zend
 * @package    Common_Http
 * @subpackage Response
 * @version    $Id$
 */

/**
 * Common_Http_Response
 * 
 * レスポンスボディへのアクセスを開放するために拡張したクラス
 *
 * @category   Zend
 * @package    Common_Http
 * @subpackage Response
 * @see Zend_Http_Response
 */
class Common_Http_Response extends Zend_Http_Response
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
     * Common_Http_Responseを返却する
     * 
     * @param Zend_Http_Response $response
     * @return \Common_Http_Response
     */
    public static function fromObject($response)
    {
        return new self($response->getStatus(), $response->getHeaders(), $response->getRawBody(), $response->getVersion(), $response->getMessage());
    }

}