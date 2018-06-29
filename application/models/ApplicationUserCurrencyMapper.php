<?php

/**
 * 自動生成ファイル
 *
 * CreateMapperSubClassLogicで自動生成されたファイル
 *
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */

/**
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */
class Application_Model_ApplicationUserCurrencyMapper extends Application_Model_Base_ApplicationUserCurrencyMapper
{

    /**
     * アプリケーションユーザ通貨の有効期限なしのレコードを後ろにするための独自メソッド
     * (ZendのクエリビルダではORDER BY句が期待した形式にならないため)
     * 
     * @param array $where WHERE条件の連想配列
     *                           array('カラム名(camel case)' => 値,
     *                                  ...
     *                                 )
     * @param array $platformGroups WHERE句に使用するプラットフォーム情報の配列
     * @return array 取得結果の連想配列の配列
     */
    public function fetchAllOrderNullsLast($where, $platformGroups)
    {
        // 連想配列を取得
        $dbResults = $this->getDbTable()->fetchAllOrderNullsLast($where, $platformGroups);
        $results   = array();

        // モデルに入れ直す
        foreach ($dbResults as $dbResult) {
            $results[] = new Application_Model_ApplicationUserCurrency($dbResult);
        }

        return $results;
    }

    /**
     * 通貨共有グループ設定が同じ通貨情報を取得するための独自メソッド
     * 
     * @param array $where WHERE条件の連想配列
     *                           array('カラム名(camel case)' => 値,
     *                                  ...
     *                                 )
     * @param array $platformGroups WHERE句に使用するプラットフォーム情報の配列
     * @return array 取得結果の連想配列の配列
     */
    public function fetchAllBySharingGroup($where, $platformGroups)
    {
        // 連想配列を取得
        $dbResults = $this->getDbTable()->fetchAllBySharingGroup($where, $platformGroups);
        $results   = array();

        // モデルに入れ直す
        foreach ($dbResults as $dbResult) {
            $results[] = new Application_Model_ApplicationUserCurrency($dbResult);
        }

        return $results;
    }

}
