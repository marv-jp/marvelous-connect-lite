<?php

/**
 * Common_Validate_NotNgWordクラスのファイル
 * 
 * Common_Validate_NotNgWordクラスを定義している
 *
 * @category Zend
 * @package  Common_Validate
 * @version  $Id$
 */

/**
 * Validate_NotNgWordクラスファイル
 *
 * @category  Zend
 * @package   Common_Validate
 * @version   $Id$
 */
class Common_Validate_NotNgWord extends Common_Validate_Abstract
{
    /**
     * NGワード入ってないかチェックする
     * 
     * @param mixed $target チェックする文字列 | Application_Model_CommonNgWordのオブジェクト
     * @return Boolean true：NGワードが含まれていない、false：NGワードが含まれている
     */
    public function isValid($target)
    {
        return !count(Common_NgWord::getNgWord($target));
    }
}
