<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author tanbaa
 */
interface Common_Log_Access_Interface
{

    /**
     * INFOレベルのログを出力する
     * 
     * @param string 出力するメッセージ
     */
    public function info($message = "");
}
