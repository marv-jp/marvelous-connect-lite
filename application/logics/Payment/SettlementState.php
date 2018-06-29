<?php

/**
 * Logic_Payment_SettlementStateクラスのファイル
 *
 * Logic_Payment_SettlementStateクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_SettlementState
 *
 * 決済状態あれこれクラス
 *
 * @category Zend
 * @package  Logic_Payment
 * @method Application_Model_ApplicationUserPayment getApplicationUserPayment() 内部検索結果のアプリケーションユーザペイメントを返します。
 * @method Application_Model_PlatformPaymentItem getPlatformPaymentItem() 内部検索結果のプラットフォームペイメントアイテムを返します。
 * @method Application_Model_ApplicationUserPlatformPaymentRelation getApplicationUserPlatformPaymentRelation() 内部検索結果のアプリケーションユーザプラットフォームペイメント関連を返します。
 * @method Application_Model_ApplicationUserPaymentItem getApplicationUserPaymentItem() 内部検索結果のアプリケーションユーザペイメントアイテムを返します。
 */
class Logic_Payment_SettlementState
{

    use misp\traits\Misp_Trait_MagicMethodAccessorRepository;
    use misp\logics\payment\traits\Logic_Payment_Trait_ApplicationUserPaymentItemRepository;
    use misp\logics\payment\traits\Logic_Payment_Trait_ApplicationUserPaymentRepository;
    use misp\logics\payment\traits\Logic_Payment_Trait_ApplicationUserPlatformPaymentRelationRepository;
    use misp\logics\payment\traits\Logic_Payment_Trait_PlatformPaymentItemRepository;
    /**
     * @var Application_Model_ApplicationUserPayment アプリケーションユーザペイメント
     */
    private $_applicationUserPayment = null;

    /**
     * @var Application_Model_PlatformPaymentItem プラットフォームペイメントアイテム
     */
    private $_platformPaymentItem = null;

    /**
     * @var Application_Model_ApplicationUserPlatformPaymentRelation アプリケーションユーザプラットフォームペイメント関連
     */
    private $_applicationUserPlatformPaymentRelation = null;

    /**
     * @var Application_Model_ApplicationUserPaymentItem アプリケーションユーザペイメントアイテム
     */
    private $_applicationUserPaymentItem = null;

    /**
     * 内部検索用にあらかじめアプリケーションユーザペイメントをセットしてください。
     * 
     * @param Application_Model_ApplicationUserPayment $applicationUserPayment
     */
    public function setApplicationUserPayment($applicationUserPayment)
    {
        $this->_applicationUserPayment = $applicationUserPayment;
    }

    /**
     * 決済が開始されていることの確認
     * 
     * @param int $platformPaymentId
     * @return boolean
     */
    public function isStartedByPlatformPaymentId($platformPaymentId)
    {
        $applicationUserPayment = $this->_applicationUserPayment;
        $paymentPlatformId      = $applicationUserPayment->getPaymentPlatformId();

        // 1. プラットフォームペイメントアイテム検索
        $this->_platformPaymentItem = $this->fetchAllPlatformPaymentItem(new Application_Model_PlatformPaymentItem(
                [
            'platformPaymentId' => $platformPaymentId,
            'paymentPlatformId' => $paymentPlatformId
                ]
        ));
        if (!$this->_platformPaymentItem) {
            return FALSE;
        }
        $this->_platformPaymentItem = $this->_platformPaymentItem[0];

        // 2. アプリケーションユーザプラットフォームペイメント関連検索
        $this->_applicationUserPlatformPaymentRelation = $this->fetchAllApplicationUserPlatformPaymentRelation(new Application_Model_ApplicationUserPlatformPaymentRelation(
                [
            'platformPaymentItemId' => $this->_platformPaymentItem->getPlatformPaymentItemId()
                ]
        ));
        if (!$this->_applicationUserPlatformPaymentRelation) {
            return FALSE;
        }
        $this->_applicationUserPlatformPaymentRelation = $this->_applicationUserPlatformPaymentRelation[0];

        // 3. アプリケーションユーザペイメントアイテム検索
        $this->_applicationUserPaymentItem = $this->findApplicationUserPaymentItem($this->_applicationUserPlatformPaymentRelation->getApplicationUserPaymentItemId());
        if (!$this->_applicationUserPaymentItem) {
            return FALSE;
        }

        // 4. アプリケーションユーザペイメント検索
        $applicationUserPayment->setApplicationUserPaymentId($this->_applicationUserPaymentItem->getApplicationUserPaymentId());
        $this->_applicationUserPayment = $this->fetchAllApplicationUserPayment($applicationUserPayment);
        if (!$this->_applicationUserPayment) {
            return FALSE;
        }

        // 5.検索結果をアクセス可能な状態にする
        $this->setFetchedPlatformPaymentItem($this->_platformPaymentItem);
        $this->setFetchedApplicationUserPlatformPaymentRelation($this->_applicationUserPlatformPaymentRelation);
        $this->setFetchedApplicationUserPaymentItem($this->_applicationUserPaymentItem);
        $this->setFetchedApplicationUserPayment($this->_applicationUserPayment[0]);

        return TRUE;
    }

}
