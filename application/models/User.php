<?php

/**
 * 自動生成ファイル
 *
 * CreateModelSubClassLogicで自動生成されたファイル
 *
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */

/**
 * ユーザ
 *
 *
 *
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */
class Application_Model_User extends Application_Model_Base_User
{
    const CLASS_NAME = 'Application_Model_User';

    /**
     * @var array アプリケーションユーザ情報
     */
    protected $_apps = null;

    /**
     * @var array<Application_Model_PlatformUser> プラットフォームアカウント情報
     */
    protected $_accounts = null;

    /**
     * appsプロパティーを設定する。
     *
     * @param array $apps アプリケーションユーザの情報の配列
     * @return Application_Model_User
     * Application_Model_Userのオブジェクト
     */
    public function setApps($apps)
    {
        $this->_apps = $apps;
        return $this;
    }

    /**
     * appsプロパティーを返す。
     *
     * @return array アプリケーションユーザの情報の配列
     */
    public function getApps()
    {
        return $this->_apps;
    }

    /**
     * accountsプロパティーを設定する。
     *
     * @param array<Application_Model_PlatformUser> $accounts プラットフォームアカウント情報の配列
     * @return Application_Model_User
     * Application_Model_Userのオブジェクト
     */
    public function setAccounts($accounts)
    {
        $this->_accounts = $accounts;
        return $this;
    }

    /**
     * accountsプロパティーを返す。
     *
     * @return array<Application_Model_PlatformUser> プラットフォームアカウント情報の配列
     */
    public function getAccounts()
    {
        return $this->_accounts;
    }

}

