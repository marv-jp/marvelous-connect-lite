<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author kohnot
 */
interface Common_ScriptEngine_Interface
{
    /**
     * プロパティにスクリプトエンジンオブジェクトをセットする
     */
    public function setUpEngine(); 

    /**
     * PHP拡張が有効かどうか
     * 
     * @return boolean true:拡張が有効 false:拡張が無効
     */
    public function isExtensionLoaded();

    /**
     * スクリプトエンジン側にアサインする変数を設定する
     * 
     * @param array キー名をセットする際の変数名、参照される値をその変数の値と扱う連想配列
     */
    public function setVars(array $vars);
    
    /** 
     * スクリプトエンジン側にアサインする変数を取得する
     * 
     * @return array キー名をセットする際の変数名、参照される値をその変数の値と扱う連想配列
     */
    public function getVars();

    /**
     * スクリプト側に使いたい関数を設定する
     * 
     * @param array キー名をセットする際の関数名、参照される値をその変数の値と扱う連想配列
     */
    public function setFunctions(array $functionNames);

    /**
     * スクリプト側に使いたい関数を取得する
     * 
     * @return array キー名をセットする際の関数名、参照される値をその変数の値と扱う連想配列
     */
    public function getFunctions();
    
    /**
     * スクリプトエンジンでコードを実行する
     * 
     * @param string 実行コード
     * @return mixed 実行結果 実行結果を返さないコードはnullを返す
     */
    public function runScript($code);
}
