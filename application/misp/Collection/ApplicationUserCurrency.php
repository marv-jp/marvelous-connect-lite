<?php

class Misp_Collection_ApplicationUserCurrency extends Misp_Collection_OpenSocial_Collection
{

    /**
     * @return Application_Model_ApplicationUserCurrency アプリケーションユーザ通貨
     */
    public function current()
    {
        return parent::current();
    }

    /** @var boolean ログをDBに出力するかのフラグ */
    private $_shouldDbLogging = FALSE;

    /**
     * ログをDBに出力するフラグを立てる
     */
    public function shouldDbLoggingOn()
    {
        $this->_shouldDbLogging = TRUE;
    }

    /**
     * ログをDBに出力するフラグを折る
     */
    public function shouldDbLoggingOff()
    {
        $this->_shouldDbLogging = FALSE;
    }

    /**
     * 
     * @return boolean
     */
    public function shouldDbLogging()
    {
        return $this->_shouldDbLogging;
    }

}
