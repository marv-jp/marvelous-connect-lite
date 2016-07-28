<?php

/**
 * LuaスクリプトをPHP上から実行するクラス
 *
 * @author nishiokah
 */
class Common_ScriptEngine_Lua extends Common_ScriptEngine_Abstract
{

    public function setUpEngine()
    {
        $this->_scriptEngine = new Lua();
    }

    public function isExtensionLoaded()
    {
        // PHPの拡張モジュールとして読み込まれてるか
        return extension_loaded('Lua');
    }

    public function setVars(array $vars)
    {
        $this->_phpVars = $vars;

        foreach ($vars as $varName => $varValue) {
            $this->_scriptEngine->assign($varName, $varValue);
        }
    }

    public function getVars()
    {
        foreach ($this->_phpVars as $varName => $varValue) {
           $this->_phpVars[$varName] = $this->_scriptEngine->$varName;
        }

        return $this->_phpVars;
    }

    public function setFunctions(array $functions)
    {
        // 必要性は薄いがプロパティに登録した関数情報をセット
        $this->_phpFunctions = $functions;

        //キー名をLuaでの関数名、参照される値をPHPの対応関数として扱う
        foreach ($functions as $scriptFunctionName => $phpFunctionName) {
            $this->_scriptEngine->registerCallback($scriptFunctionName, $phpFunctionName);
        }
    }

    public function getFunctions()
    {
        return $this->_phpFunctions;
    }

    public function runScript($code)
    {
        try {
            $this->_scriptEngine->eval($code);
        } catch (LuaException $exc) {
            // @todo Luaクラスの例外処理（ログを残す）
            throw $exc;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

}
