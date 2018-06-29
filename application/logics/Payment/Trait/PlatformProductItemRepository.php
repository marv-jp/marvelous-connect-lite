<?php

namespace misp\logics\payment\traits;

/**
 * プラットフォーム商品アイテムのトレイトです。
 * 
 * ※Logic_Payment_Abstract を継承しているサブクラスでの use を前提としています。
 */
trait Logic_Payment_Trait_PlatformProductItemRepository
{

    /**
     * プラットフォーム商品アイテムを検索します。
     * 
     * @param string $platformProductId
     * @return \Application_Model_PlatformProduct
     */
    public function fetchAllPlatformProductItem($platformProductId)
    {
        // プラットフォーム商品アイテム取得(ボーナス含む)
        $where                        = [];
        $where['platformProductId']   = [$platformProductId];
        $where['paymentPlatformId']   = [$this->_platform];
        $where['applicationId']       = [$this->_applicationId];
        $where['deletedDate IS NULL'] = NULL;
        return $this->getPlatformProductItemMapper($this->getDbSectionNameSub())->fetchAll($where);
    }

}
