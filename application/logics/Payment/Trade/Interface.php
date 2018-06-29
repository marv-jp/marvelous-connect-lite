<?php

/**
 * Logic_Payment_Trade_Interfaceクラスのファイル
 *
 * Logic_Payment_Trade_Interfaceクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */

/**
 * Logic_Payment_Trade_Interface
 *
 * 仮想通貨取引インターフェース
 *
 * @category Zend
 * @package  Logic_Payment_Trade
 */
interface Logic_Payment_Trade_Interface
{
    /**
     * 仮想通貨取引クラスのプレフィックス
     * 
     * @var string
     */
    const CLASS_PREFIX = 'Logic_Payment_Trade_';

    /**
     * 実処理
     * 
     * インスタンス構築引数によって購入/両替/消費の処理を実行する
     */
    public function exec();
}
