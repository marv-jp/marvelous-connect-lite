<?php

/**
 * Logic_Payment_Trade_Factoryクラスのファイル
 *
 * Logic_Payment_Trade_Factoryクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_Trade_Factory
 *
 * 仮想通貨情報取引ファクトリ
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_Trade_Factory extends Logic_Payment_FactoryAbstract
{

    /**
     * 仮想通貨取引ファクトリ
     * 
     * ペイメント種別に応じた取引インスタンスを返します
     * 
     * <pre>
     * array(
     *   'type' => 必須：ペイメント種別(application_user_payment.payment_type)
     * );
     * </pre>
     * 
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     * @return Logic_Payment_Trade_Abstract
     * @throws Common_Exception_IllegalParameter パラメータ不正：未定義のペイメント種別の場合にThrowされます
     */
    public function factory($buildParams)
    {
        if ($this->_isNotAllowPaymentType($buildParams)) {
            throw new Common_Exception_IllegalParameter('不正なtypeです');
        }
        
        // インスタンス生成
        //   生成対象インスタンスのクラス名を構築
        $className = $this->_buildClassName($buildParams);
        $clazz     = $this->_factory($className, $buildParams);
        $clazz->setApplicationUserPayment($this->getApplicationUserPayment());
        $clazz->setCurrencyPaymentLogic(new Logic_Payment_Trade_CurrencyPayment());
        $clazz->setCurrencyCreditLogic(new Logic_Payment_Trade_CurrencyCredit());
        $clazz->setVerifyPaymentFactory(new Logic_Payment_Verify_Payment_Factory());

        return $clazz;
    }

    /**
     * ファクトリ
     * 
     * 仮想通貨取引ファクトリの実メソッド
     * 
     * @param string $className インスタンス生成対象のクラス名
     * @param array $buildParams インスタンス生成するクラスコンストラクタの引数(APIリクエストパラメータの"entry"項目の中身)
     * @return Logic_Payment_Trade_Abstract 生成したインスタンス
     */
    private function _factory($className, $buildParams)
    {
        return new $className($buildParams);
    }

    /**
     * 生成対象インスタンスクラス名構築
     * 
     * 生成対象インスタンスのクラス名を構築します
     *
     * @param array $buildParams
     * @return string 生成対象インスタンスのクラス名
     */
    private function _buildClassName($buildParams)
    {
        return Logic_Payment_Trade_Interface::CLASS_PREFIX . self::$allowPaymentTypes[$buildParams['type']];
    }

    /**
     * ペイメントタイプ検証
     *
     * 一応。念のため。
     *
     * @param array $buildParams
     * @return boolean
     */
    private function _isAllowPaymentType($buildParams)
    {
        return isset($buildParams['type']) && isset(self::$allowPaymentTypes[$buildParams['type']]);
    }

    /**
     * ペイメントタイプ不正検証
     * 
     * ペイメントタイプ検証の反証用
     * 
     * @param array $buildParams
     * @return boolean
     */
    private function _isNotAllowPaymentType($buildParams)
    {
        return !$this->_isAllowPaymentType($buildParams);
    }

}
