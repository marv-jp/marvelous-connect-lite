<?php

namespace misp\logics\payment\traits;

/**
 * アプリケーションユーザターゲット通貨ペイメントアイテムのトレイトです。
 * 
 * ※Logic_Payment_Abstract を継承しているサブクラスでの use を前提としています。
 */
trait Logic_Payment_Trait_ApplicationUserTargetCurrencyPaymentItemRepository
{

    /**
     * アプリケーションユーザターゲット通貨ペイメントアイテムを登録します。
     * 
     * @param Application_Model_ApplicationUserTargetCurrencyPaymentItem $m 登録情報をつめたモデル
     * @return int Last Insert Id
     * @throws Common_Exception_Exception
     */
    public function saveApplicationUserTargetCurrencyPaymentItem($m)
    {
        // Insert
        $mapper = $this->getApplicationUserTargetCurrencyPaymentItemMapper($this->getDbSectionNameMain());
        return $mapper->insert($m);
    }

    /**
     * アプリケーションユーザターゲット通貨ペイメントアイテムを取得します。
     * 
     * @param Application_Model_ApplicationUserTargetCurrencyPaymentItem $conditionModel
     * @param string $dbSectionName
     * @return array
     */
    public function fetchAllApplicationUserTargetCurrencyPaymentItem($conditionModel, $dbSectionName = NULL)
    {
        if (is_null($dbSectionName)) {
            $dbSectionName = $this->getDbSectionNameSub();
        }

        $where   = [];
        $wkWhere = $conditionModel->toArray();
        foreach ($wkWhere as $key => $value) {
            if (\Common_Util_String::isNotEmpty($value)) {
                $where[$key] = $value;
            }
        }

        return $this->getApplicationUserTargetCurrencyPaymentItemMapper($dbSectionName)->fetchAll($where);
    }

}
