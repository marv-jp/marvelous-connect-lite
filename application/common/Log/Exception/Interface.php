<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author tanbaa
 */
interface Common_Log_Exception_Interface
{
    /**
     * 例外インスタンスをセットする
     *
     * @abstract
     * @param $exception 例外インスタンス
     */
    public function setException(Exception $exception);

    /**
     * INFOレベルのログを出力する
     *
     * @param string 出力するメッセージ
     */
    public function info($message);

    /**
     * ERRレベルのログを出力する
     *
     * @param string 出力するメッセージ
     */
    public function error($message);
}
