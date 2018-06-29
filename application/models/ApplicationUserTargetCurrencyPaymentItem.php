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
 * アプリケーションユーザターゲット通貨ペイメントアイテム
 *
 *
 *
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */
class Application_Model_ApplicationUserTargetCurrencyPaymentItem extends Application_Model_Base_ApplicationUserTargetCurrencyPaymentItem
{
    const CLASS_NAME = 'Application_Model_ApplicationUserTargetCurrencyPaymentItem';

    private $_platformProductId = null;
    private $_executedDate      = null;

    /**
     * 無償通貨用アプリケーションユーザターゲット通貨ペイメントアイテムコレクション格納用
     *
     * @var Misp_Collection_ApplicationUserTargetCurrencyPaymentItem
     */
    private $_targetCurrencyPaymentItemCollectionForBonus = null;

    /**
     * プラットフォーム商品IDをセットします
     *
     * カラム追加ではなくモデル拡張とした理由：当プロパティはAPIリクエストパラメータで(DBに)永続化する性質ではないため、DBレイヤーではなくモデル拡張で対応する
     *
     * @param string $platformProductId
     * @return \Application_Model_ApplicationUserPaymentItem
     */
    public function setPlatformProductId($platformProductId)
    {
        $this->_platformProductId = $platformProductId;
        return $this;
    }

    /**
     * プラットフォーム商品IDを返します
     *
     * @return string
     */
    public function getPlatformProductId()
    {
        return $this->_platformProductId;
    }

    /**
     * 実行日時をセットします
     *
     * カラム追加ではなくモデル拡張とした理由：当プロパティはAPIリクエストパラメータで(DBに)永続化する性質ではないため、DBレイヤーではなくモデル拡張で対応する
     *
     * @param string $executedDate
     * @return \Application_Model_ApplicationUserPaymentItem
     */
    public function setExecutedDate($executedDate)
    {
        $this->_executedDate = $executedDate;
        return $this;
    }

    /**
     * 実行日時を返します
     *
     * @return string
     */
    public function getExecutedDate()
    {
        return $this->_executedDate;
    }

    /**
     * 無償通貨用アプリケーションユーザターゲット通貨ペイメントアイテムコレクションをセットします
     * 
     * @param Misp_Collection_ApplicationUserTargetCurrencyPaymentItem $collection
     */
    public function setTargetCurrencyPaymentItemCollectionForBonus(Misp_Collection_ApplicationUserTargetCurrencyPaymentItem $collection)
    {
        $this->_targetCurrencyPaymentItemCollectionForBonus = $collection;
    }

    /**
     * 無償通貨用アプリケーションユーザターゲット通貨ペイメントアイテムコレクションを返します
     * 
     * @return Misp_Collection_ApplicationUserTargetCurrencyPaymentItem
     */
    public function getTargetCurrencyPaymentItemCollectionForBonus()
    {
        return $this->_targetCurrencyPaymentItemCollectionForBonus;
    }

}
