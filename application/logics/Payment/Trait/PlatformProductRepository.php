<?php

namespace misp\logics\payment\traits;

/**
 * プラットフォーム商品のトレイトです。
 * 
 * ※Logic_Payment_Abstract を継承しているサブクラスでの use を前提としています。
 */
trait Logic_Payment_Trait_PlatformProductRepository
{

    /**
     * プラットフォーム商品を検索します。
     * 
     * @param string $platformProductId
     * @return \Application_Model_PlatformProduct
     */
    public function fetchAllPlatformProduct($platformProductId)
    {
        // プラットフォーム商品検証
        $where                        = [];
        $where['platformProductId']   = [$platformProductId];
        $where['paymentPlatformId']   = [$this->_platform];
        $where['applicationId']       = [$this->_applicationId];
        $where['deletedDate IS NULL'] = NULL;
        return $this->getPlatformProductMapper($this->getDbSectionNameSub())->fetchAll($where);
    }

}
