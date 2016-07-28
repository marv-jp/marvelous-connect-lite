<?php

/*
 * 例外処理の共通基底クラス
 * 
 * LICENSE: ライセンスに関する情報
 *
 * @category   Zend
 * @package    Zend_Magic
 * @subpackage Wand
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license   BSD License
 * @version    $Id:$
 * @link       http://framework.zend.com/package/PackageName
 * @since      File available since Release 1.5.0
 */

/**
 * 例外処理の共通基底クラス
 *
 * @author nishiokah
 */
abstract class Common_Exception_Abstract extends Exception
{
    /** @var bool ログ出力済みフラグ */
    private $_isLogged = FALSE;

    public function __construct($message = '', $code = 0, $previous = null)
    {
        if (empty($message))
        {
            $trace = $this->getTrace();
            $class = isset($trace[0]['class']) ? $trace[0]['class'] : '';
            $method = isset($trace[0]['function']) ? $trace[0]['function'] : '';
            $line = isset($trace[0]['line']) ? $trace[0]['line'] : '';

            $message = $message ? $message : sprintf('例外クラス: %s<br> 発生クラス: %s<br>発生メソッド: %s<br>発生行数: %s<br>例外詳細: FILE->%s LINE->%s', get_class($this), $class, $method, $line, $this->getFile(), $this->getLine());
        }
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * この例外がログ出力済みの場合、TRUEを返します。
     *
     * @return bool TRUE:ログ出力済み | FALSE:ログ未出力
     */
    public function isLogged()
    {
        return $this->_isLogged;
    }

    /**
     * この例外がログをまだ出力していない場合、TRUEを返します。
     *
     * @return bool TRUE:ログ未出力 | FALSE:ログ出力済み
     */
    public function isNotLogged()
    {
        return !$this->_isLogged;
    }

    /**
     * ログ出力済みフラグをOnにする
     */
    public function isLoggedOn()
    {
        $this->_isLogged = TRUE;
    }

    /**
     * ログ出力済みフラグをOffにする
     */
    public function isLoggedOff()
    {
        $this->_isLogged = FALSE;
    }
}
