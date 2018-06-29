<?php

namespace misp\logics\payment\traits;

/**
 * アプリケーションユーザペイメントのトレイトです。
 * 
 * ※Logic_Payment_Abstract を継承しているサブクラスでの use を前提としています。
 */
trait Logic_Payment_Trait_ApplicationUserPaymentRepository
{

    /**
     * アプリケーションユーザペイメントを取得します。
     * 
     * @param Application_Model_ApplicationUserPayment $conditionModel
     * @param string $dbSectionName
     * @return array
     */
    public function fetchAllApplicationUserPayment(\Application_Model_ApplicationUserPayment $conditionModel, $dbSectionName = NULL)
    {
        if (is_null($dbSectionName)) {
            $dbSectionName = $this->getDbSectionNameSub();
        }

        // アプリケーションユーザペイメント取得
        $where   = [];
        $wkWhere = $conditionModel->toArray();
        foreach ($wkWhere as $key => $value) {
            if (\Common_Util_String::isNotEmpty($value)) {
                $where[$key] = [$value];
            }
        }

        return $this->getApplicationUserPaymentMapper($dbSectionName)->fetchAll($where);
    }

}
