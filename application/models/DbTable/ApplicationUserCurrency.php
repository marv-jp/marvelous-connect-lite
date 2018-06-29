<?php

/**
 * 自動生成ファイル
 *
 * CreateDataMapperSubClassLogicで自動生成されたファイル
 *
 * @category Zend
 * @package Application_Model
 * @subpackage DbTable
 */

/**
 * @category Zend
 * @package Application_Model
 * @subpackage DbTable
 */
class Application_Model_DbTable_ApplicationUserCurrency extends Application_Model_Base_DbTable_ApplicationUserCurrency
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
    public function fetchAllOrderNullsLast($where, $platformGroups): array
    {
        $unionSqlInfo = $this->_buildUnionSqlInfo($where, $platformGroups);
        $sql          = $unionSqlInfo['sql'];
        $placedValues = $unionSqlInfo['placedValues'];

        // ORDER BY句生成
        // (Zendのクエリビルダではカラム部分に is null が含まれてしまう)
        $sql .= ' ORDER BY `expired_date` is null ASC , `expired_date` ASC , `executed_date` is null ASC , `executed_date` ASC ';

        // クエリの置換部分の引数の値をセットし、クエリ実行
        return $this->getAdapter()->query($sql, $placedValues)->fetchAll();
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
    public function fetchAllBySharingGroup($where, $platformGroups): array
    {
        $unionSqlInfo = $this->_buildUnionSqlInfo($where, $platformGroups);
        $sql          = $unionSqlInfo['sql'];
        $placedValues = $unionSqlInfo['placedValues'];

        // クエリの置換部分の引数の値をセットし、クエリ実行
        return $this->getAdapter()->query($sql, $placedValues)->fetchAll();
    }

    /**
     * アプリケーションユーザ通貨の期限日時アリ/ナシそれぞれUNION取得するSQLと、それに対応するプリペアステートメント値を構築します。
     * 
     * @param array $where WHERE条件
     * @param array $platformGroups 通貨共有グループ設定から算出されたプラットフォーム情報
     * @return array ['sql' => UNION文, 'placedValues' => UNION文に対応するプリペアステートメント値]
     */
    private function _buildUnionSqlInfo($where, $platformGroups): array
    {
        $sqlArray          = [];
        $mergePlacedValues = [];

        foreach ($platformGroups as $platformGroup) {

            // クエリ生成
            $expirationSql = '(SELECT * FROM application_user_currency WHERE ';
            $indefiniteSql = '(SELECT * FROM application_user_currency WHERE ';

            // プラットフォーム情報をwhere条件に追加
            $where['paymentPlatformId'] = [$platformGroup['paymentPlatformId']];
            $where['paymentDeviceId']   = [''];
            if (array_key_exists('paymentDeviceId', $platformGroup)) {
                $where['paymentDeviceId'] = [$platformGroup['paymentDeviceId']];
            }
            $where['paymentRatingId'] = [''];
            if (array_key_exists('paymentRatingId', $platformGroup)) {
                $where['paymentRatingId'] = [$platformGroup['paymentRatingId']];
            }

            // 引数からWHERE句生成
            $placedColumns = [];
            $placedValues  = [];
            foreach (Common_Util_Db::keyNameCamelToSnakeWithPlaceholder($where) as $placedColumn => $value) {
                $placedColumns[] = $placedColumn;
                $placedValues[]  = implode(',', $value);
            }
            $expirationSql .= implode(' AND ', $placedColumns);
            $indefiniteSql .= implode(' AND ', $placedColumns);

            $expirationSql .= ' AND expired_date >= now())';
            $indefiniteSql .= ' AND expired_date is null)';

            // 期限日時あり＋期限日時なしのクエリをUNIONしたものを配列に格納
            $sqlArray[] = $expirationSql . ' UNION ' . $indefiniteSql;

            // クエリの置換部分の引数の値をマージ
            $mergePlacedValues = array_merge($mergePlacedValues, $placedValues, $placedValues);
        }

        // クエリ配列をUNIONする
        $sql = implode(' UNION ', $sqlArray);

        return ['sql' => $sql, 'placedValues' => $mergePlacedValues];
    }

}
