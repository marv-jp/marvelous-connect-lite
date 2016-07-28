<?php

/**
 * Marvelous_Plugin_Base_ErrorHandlerクラスのファイル
 * 
 * Marvelous_Plugin_Base_ErrorHandlerクラスを定義している
 *
 * @category   Zend
 * @package    Marvelous
 * @subpackage Plugin
 * @version    $Id$
 */

/**
 * Marvelous_Plugin_Base_ErrorHandler
 *
 * @category    Zend
 * @package     Marvelous
 * @subpackage  Plugin
 */
abstract class Marvelous_Plugin_Base_ErrorHandler  extends Marvelous_Plugin_Abstract
{ 
    /**
     * エラーハンドリング時にMarvelous_ErrorControllerが呼ばれるように設定します
     * 
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // リクエストモジュールが、処理対象であるべき"marvelous"以外の場合は何もしない
        if ('marvelous' != $request->getModuleName())
        {
            return;
        }
        
        $frontController = Zend_Controller_Front::getInstance();
        if ($frontController->hasPlugin('Zend_Controller_Plugin_ErrorHandler'))
        {
            Zend_Controller_Front::getInstance()->getPlugin('Zend_Controller_Plugin_ErrorHandler')->setErrorHandlerModule('marvelous');
        }
    }
}

