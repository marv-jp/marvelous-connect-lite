<?php

/**
 * Logic_Payment_FactoryAbstractクラスのファイル
 *
 * Logic_Payment_FactoryAbstractクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_FactoryAbstract
 *
 * ファクトリ基底クラス
 *
 * @category Zend
 * @package  Logic_Payment
 */
abstract class Logic_Payment_FactoryAbstract
{
    /* ペイメントプラットフォームIDとコンフィグ名のマッピング */

    static private $_platformConfigMapping = array(
        'moog'    => 'moog',
    );

    /* ペイメントデバイスIDとコンフィグ名のマッピング */
    static private $_deviceConfigMapping = array(
    );

    /* ペイメントレーティングIDとコンフィグ名のマッピング */
    static private $_ratingConfigMapping = array(
    );

    /* ペイメントプラットフォームIDとクラス名のマッピング */
    static private $_platformClassMapping = array(
        'moog'    => 'Moog',
    );

    /* ペイメントデバイスIDとクラス名(一部)のマッピング */
    static private $_deviceClassMapping = array(
    );

    /* ペイメントレーティングIDとクラス名(一部)のマッピング */
    static private $_ratingClassMapping = array(
    );

    /**
     * ペイメントプラットフォームIDとコンフィグ名のマッピングをし、コンフィグ名を取得する
     * 
     * @param $platformId プラットフォームID
     * @param $deviceId デバイスID
     * @param $ratingId レーティングID
     * @return string コンフィグ名
     */
    static public function getPlatformConfigName($platformId, $deviceId = '', $ratingId = '')
    {
        $configNames = array();

        if (isset(self::$_platformConfigMapping[$platformId])) {
            $configNames[] = self::$_platformConfigMapping[$platformId];
        }

        if (isset(self::$_deviceConfigMapping[$deviceId])) {
            $configNames[] = self::$_deviceConfigMapping[$deviceId];
        }

        if (isset(self::$_ratingConfigMapping[$ratingId])) {
            $configNames[] = self::$_ratingConfigMapping[$ratingId];
        }

        return implode('_', $configNames);
    }

    /**
     * ペイメントプラットフォームIDとクラス名のマッピングをし、クラス名を取得する
     * 
     * @param $platformId プラットフォームID
     * @param $deviceId デバイスID
     * @param $ratingId レーティングID
     * @return string クラス名
     * @throws Common_Exception_ClassNotFound 生成したクラス(名)が存在しなかった
     */
    static public function getExistingClassName($classPrefix, $platformId, $deviceId = '', $ratingId = '')
    {
        $className     = $classPrefix;
        $classSuffixes = array();

        if (isset(self::$_platformClassMapping[$platformId])) {
            $classSuffixes[] = self::$_platformClassMapping[$platformId];
        }

        if (isset(self::$_deviceClassMapping[$deviceId])) {
            $classSuffixes[] = self::$_deviceClassMapping[$deviceId];
        }

        if (isset(self::$_ratingClassMapping[$ratingId])) {
            $classSuffixes[] = self::$_ratingClassMapping[$ratingId];
        }
        $classSuffix = implode('_', $classSuffixes);
        $className   .= $classSuffix;

        if (class_exists($className)) {
            return $className;
        }

        // マッピングできなかった場合は例外を返す
        throw new Common_Exception_ClassNotFound('Class not found: ' . $className);
    }

    /**
     * ペイメント種別
     * 
     * ※外部からも参照するので意図的にpublicにしています。(PHPは(連想)配列の定数が定義できないため)
     *
     * @var array
     */
    static public $allowPaymentTypes = array(
        'credit'   => 'Credit',
        'exchange' => 'Exchange',
        'payment'  => 'Payment',
    );

    /**
     * モデルオブジェクトの連想配列
     * 
     * @var array 
     */
    private $_model = array();

    /**
     * ファクトリ
     * 
     * 仕様は実装先に依存するのでPHPDocの詳細はここでは記載しません。
     * 
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     * @return object ファクトリされたインスタンス
     */
    abstract public function factory($buildParams);

    /**
     * アプリケーションユーザペイメントモデルをセットします
     * 
     * @param Application_Model_ApplicationUserPayment $applicationUserPayment
     */
    public function setApplicationUserPayment($applicationUserPayment)
    {
        $this->_model['applicationUserPayment'] = $applicationUserPayment;
    }

    /**
     * アプリケーションユーザペイメントモデルを返します
     * 
     * @return Application_Model_ApplicationUserPayment
     */
    public function getApplicationUserPayment()
    {
        return $this->_model['applicationUserPayment'];
    }

}
