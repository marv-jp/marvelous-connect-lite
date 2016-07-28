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
 * NGワード
 *
 *
 *
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */
class Application_Model_CommonNgWord extends Application_Model_Base_CommonNgWord
{
    const CLASS_NAME = 'Application_Model_CommonNgWord';

    /**
     * @var string アプリケーションID PK:varchar(11)
     */
    protected $_applicationId = null;

    /**
     * applicationIdプロパティーを設定する。
     *
     * @param string $applicationId アプリケーションIDの値
     * @return Application_Model_Base_CommonNgWord Application_Model_Base_CommonNgWordのオブジェクト
     */
    public function setApplicationId($applicationId)
    {
        $this->_applicationId = $applicationId;
        return $this;
    }

    /**
     * applicationIdプロパティーを返す。
     *
     * @return string applicationIdの値
     */
    public function getApplicationId()
    {
        return $this->_applicationId;
    }

    /**
     * モデルオブジェクトを連想配列にして返す。
     *
     * @return array モデルオブジェクトの連想配列
     */
    public function toArray()
    {
        $memberArray                = parent::toArray();
        $memberArray['applicationId'] = $this->getApplicationId();
        return $memberArray;
    }

}
