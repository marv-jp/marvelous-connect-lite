<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author tanbaa
 */
interface Common_Log_Internal_Interface
{

    /**
     * INFOレベルのログを出力する
     * 
     * @param string 出力するメッセージ
     */
    public function info($message = "");

    /**
     * DEBUGレベルのログを出力する
     * 
     * @param string 出力するメッセージ
     */
    public function debug($message = "");
}
