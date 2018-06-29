<?php

/**
 * Logic_Payment_Verify_Payment_Factoryクラスのファイル
 *
 * Logic_Payment_Verify_Payment_Factoryクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_Verify_Payment_Factory
 *
 * プラットフォーム決済情報検証ファクトリ
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_Verify_Payment_Factory extends Logic_Payment_FactoryAbstract
{

    /**
     * プラットフォーム決済情報検証ファクトリ
     *
     * プラットフォームに応じた決済情報検証インスタンスを返します<br>
     * <br>
     * 決済情報検証インスタンスのプラットフォーム決定は、factory引数のパラメータに依存します。(下記)
     * <pre>
     * array(
     *   'platformId' => 必須：プラットフォームID(payment_platform.payment_platform_id),
     *   'receipt'    => 任意：レシート,
     *   'signedData' => 任意：署名済みデータ,
     *   'signature'  => 任意：署名
     * );
     * </pre>
     * 
     * <h3>exec実行前の前提</h3>
     * 
     * <ol>
     *  <li>モデル：アプリケーションユーザをセットしておくこと
     *  <li>モデル：アプリケーションユーザペイメントをセットしておくこと
     * </ol>
     * 
     * <h3>使用方法</h3>
     * 
     * <pre>
     * // ファクトリクラスをnewします
     * $fc = new Logic_Payment_Verify_Payment_Factory($buildParams);
     * 
     * // ファクトリされるインスタンスで利用するモデルをセットします
     * $fc->setApplicationUser($applicationUserModel);
     * $fc->setApplicationUserPayment($applicationUserPaymentModel);
     * 
     * // プラットフォーム決済情報検証を実行します
     * if (!$fc->verify()) {
     *   // 決済情報が不正だった場合の処理
     *   // :
     *   // :
     * }
     * </pre>
     * 
     * <code>\@return</code> の指定はインターフェース型とするのがより設計的ですが、
     * 基底クラスの利便メソッドのIDE補完を効かせる目的で、あえてそのクラス名を指定しています。
     *
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     * @return Logic_Payment_Verify_Payment_Abstract プラットフォームに応じた決済情報検証インスタンス
     * @throws Common_Exception_IllegalParameter パラメータ不正：未定義のプラットフォームIDの場合にThrowされます
     */
    public function factory($buildParams)
    {
        // 検証クラスに渡すプラットフォームペイメントモデルを生成
        $platformPayment = new Application_Model_PlatformPayment($buildParams);
        $platformPayment->setPaymentPlatformId($buildParams['platformId']);

        // デバイス・レーティング対応
        if (isset($buildParams['deviceId'])) {
            $platformPayment->setPaymentDeviceId($buildParams['deviceId']);
        }
        if (isset($buildParams['ratingId'])) {
            $platformPayment->setPaymentRatingId($buildParams['ratingId']);
        }

        // ペイメントプラットフォームID検証
        if (!$this->_isValidPlatformId($platformPayment->getPaymentPlatformId(), $platformPayment->getPaymentDeviceId(), $platformPayment->getPaymentRatingId())) {
            throw new Common_Exception_IllegalParameter('不正なペイメントプラットフォームID,またはペイメントデバイスID,またはペイメントレーティングIDです');
        }

        // signedDataが存在する場合セット
        if (isset($buildParams['signedData'])) {
            $platformPayment->setReceipt($buildParams['signedData']);
        }

        // accountが存在する場合セット
        if (isset($buildParams['account'])) {
            if (isset($buildParams['account']['userId'])) {
                $platformPayment->setUserId($buildParams['account']['userId']);
            }
            if (isset($buildParams['account']['authorizationCode'])) {
                $platformPayment->setAuthorizationCode($buildParams['account']['authorizationCode']);
            }
        }


        // インスタンス生成
        //   生成対象インスタンスのクラス名を構築
        $className = $this->_buildClassName($platformPayment);
        $clazz     = $this->_factory($className, $platformPayment, $buildParams);
        $clazz->setApplicationUserPayment($this->getApplicationUserPayment());

        return $clazz;
    }

    /**
     * ファクトリ
     * 
     * @param string $className インスタンス生成対象のクラス名
     * @param Application_Model_PlatformPayment $platformPayment インスタンス生成するクラスコンストラクタの引数
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     * @return Logic_Payment_Verify_Payment_Abstract 生成したインスタンス
     */
    private function _factory($className, $platformPayment, $buildParams)
    {
        return new $className($platformPayment, $buildParams);
    }

    /**
     * 生成対象インスタンスのクラス名を構築
     *
     * @param Application_Model_PlatformPayment $platformPayment
     * @return string 生成対象インスタンスのクラス名
     * @throws Common_Exception_IllegalParameter パラメータ不正：プラットフォームID、デバイスID、レーティングの組み合わせが不正にThrowされます
     */
    private function _buildClassName($platformPayment)
    {
        try {
            return self::getExistingClassName(Logic_Payment_Verify_Payment_Interface::CLASS_PREFIX, $platformPayment->getPaymentPlatformId(), $platformPayment->getPaymentDeviceId(), $platformPayment->getPaymentRatingId());
        } catch (Common_Exception_ClassNotFound $ex) {
            throw new Common_Exception_IllegalParameter('プラットフォームID、デバイスID、レーティングの組み合わせが不正です。');
        }
    }

    /**
     * ペイメントプラットフォームID,デバイスID,レーティングID検証
     *
     * 一応。念のため。
     *
     * @param string $platformId ペイメントプラットフォームID
     * @param string $deviceId デバイスID
     * @param string $ratingId レーティングID
     * @return boolean
     */
    private function _isValidPlatformId($platformId, $deviceId = '', $ratingId = '')
    {
        if (strlen(Logic_Payment_FactoryAbstract::getPlatformConfigName($platformId, $deviceId, $ratingId))) {
            return TRUE;
        }
        return FALSE;
    }

}
