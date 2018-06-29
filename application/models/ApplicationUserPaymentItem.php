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
 * アプリケーションユーザペイメントアイテム
 *
 *
 *
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */
class Application_Model_ApplicationUserPaymentItem extends Application_Model_Base_ApplicationUserPaymentItem
{
    private $_platformProductId = null;
    private $_price             = null;

    const CLASS_NAME = 'Application_Model_ApplicationUserPaymentItem';

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
     * 価格をセットします
     *
     * カラム追加ではなくモデル拡張とした理由：当プロパティはAPIリクエストパラメータで(DBに)永続化する性質ではないため、DBレイヤーではなくモデル拡張で対応する
     *
     * @param string $price
     * @return \Application_Model_ApplicationUserPaymentItem
     */
    public function setPrice($price)
    {
        $this->_price = $price;
        return $this;
    }

    /**
     * 価格を返します
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * モデルオブジェクトを連想配列にして返す
     *
     * @return array モデルオブジェクトの連想配列
     */
    public function toArray()
    {
        $memberArray                      = parent::toArray();
        $memberArray['platformProductId'] = $this->getPlatformProductId();
        $memberArray['price']             = $this->getPrice();
        return $memberArray;
    }

}
