<?php

/**
 * Logic_Payment_Verify_Payment_Interfaceクラスのファイル
 *
 * Logic_Payment_Verify_Payment_Interfaceクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment_Verify_Payment
 */

/**
 * Logic_Payment_Verify_Payment_Interface
 *
 * プラットフォーム決済情報検証のインターフェース
 *
 * @category Zend
 * @package  Logic_Payment_Verify_Payment
 */
interface Logic_Payment_Verify_Payment_Interface
{
    /**
     * プラットフォーム決済情報検証系クラス名のプレフィックス
     *
     * @var string
     */
    const CLASS_PREFIX = 'Logic_Payment_Verify_Payment_';

    /**
     * プラットフォーム決済情報検証
     * 
     * プラットフォームに応じた決済情報の検証を行います。
     *
     * @return boolean
     */
    public function verify();

    /**
     * プラットフォーム決済情報の検証＋α
     *
     * @param Misp_Collection_ApplicationUserTargetCurrencyPaymentItem $applicationUserTargetCurrencyPaymentItem
     * @return boolean
     */
    public function verifyAnd(Misp_Collection_ApplicationUserTargetCurrencyPaymentItem $applicationUserTargetCurrencyPaymentItem);

    /**
     * MZCL検証がOKだった戻り値(ペイメントコレクション)を返します
     * 
     * @return Common_External_Platform_Model_Collection_Payments
     */
    public function getVerifiedReceipt();
}
