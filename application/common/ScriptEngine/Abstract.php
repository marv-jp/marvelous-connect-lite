<?php

/**
 * ScriptEngine抽象クラス
 */
abstract class Common_ScriptEngine_Abstract implements Common_ScriptEngine_Interface
{

    /** @var mixed スクリプトエンジンオブジェクト */
    protected $_scriptEngine = null;

    /** @var array スクリプトで使いたいPHP関数名を定義する */
    protected $_phpFunctions = array();

    /** @var array スクリプトで使いたいPHP変数名を保持する */
    protected $_phpVars = array();

    public function __construct()
    {
        // スクリプトエンジンブリッジオブジェクトを生成
        $this->setUpEngine();
        
        // 拡張が使えるかチェック
        if (!$this->isExtensionLoaded()) {
            throw new Common_Exception_ExtensionNotLoaded();
        }
    }

}
