<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Factory
 *
 * @author kohnot
 */
class Common_ScriptEngine
{
    const LUA = 'Common_ScriptEngine_Lua';

    /**
     * スクリプトエンジンオブジェクトを生成します。
     * 
     * 返すオブジェクトはこのメソッドの引数(クラス名)によって変わります。
     * 指定できるクラス名は、このクラスの定数をご参照ください。
     * 不正なクラス名を渡された場合、このメソッドはnullを返します。
     *
     * @param string $className スクリプトエンジンクラス名
     * @return Common_ScriptEngine_Interface スクリプトエンジンインターフェース
     */
    public static function factory($className)
    {
        $clazz = null;

        if (class_exists($className))
        {
            $clazz = new $className();
        }

        return $clazz;
    }

}
