<?php

/**
 * Logic_Payment_Trade_CurrencyInterfaceクラスのファイル
 *
 * Logic_Payment_Trade_CurrencyInterfaceクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */

/**
 * Logic_Payment_Trade_CurrencyInterface
 *
 * 仮想通貨取引：通貨処理系インターフェース
 *
 * 取引：購入/消費/両替<br>
 * 通貨処理系：通貨購入、通貨消費
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */
interface Logic_Payment_Trade_CurrencyInterface
{

    public function exec();
}
