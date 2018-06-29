<?php

/**
 * Logic_Payment_PaymentUpdateクラスのファイル
 *
 * Logic_Payment_PaymentUpdateクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_PaymentUpdate
 *
 * 仮想通貨情報更新
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_PaymentUpdate extends Logic_Payment_Abstract
{

    /**
     * 実処理
     * 
     * <h3>exec実行前の前提</h3>
     * 
     * <ol>
     *  <li>モデル：アプリケーションユーザをセットしておくこと
     *  <li>ファクトリ：仮想通貨情報取引ファクトリ(Logic_Payment_TradeFactory)をセットしておくこと
     * </ol>
     * 
     * <h3>使用方法</h3>
     * 
     * <pre>
     * // 更新系ロジックの生成
     * $logic = new Logic_Payment_PaymentUpdate();
     * 
     * // 取引ファクトリの準備
     * $logic->setTradeFactory(new Logic_Payment_Trade_Factory());
     * 
     * // ロジック引数の準備
     * $request     = $this->getRequest();
     * $bodyParam   = Zend_Json::decode($request->getRawBody());
     * $buildParams = $bodyParam['entry'];
     * 
     * // 更新系ロジックの実行
     * $logic->exec($buildParams);
     * </pre>
     * 
     * <h3>補足</h3>
     * 
     * 入力チェックはここでまとめて行わず、
     * 自分の役割を知っている各取引系のクラスで個別にチェックを行う実装です。
     * 
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     * @throws Common_Exception_NotFound オーダーIDがアプリケーションユーザペイメントテーブルに存在しなかった場合にThrowされます
     * @throws Common_Exception_IllegalParameter ペイメントステータスが期待外だった場合にThrowされます
     * @throws BadMethodCallException 取引ファクトリが未セットでexecされた場合にThrowされます
     * @throws Exception 想定外の例外
     */
    public function exec($buildParams)
    {
        try {
            $this->_buildParams = $buildParams;

            // パラメータチェック
            //   必須
            $platformId = $this->pickUpPlatformId();
            $this->_isValidateValue($platformId);
            $this->_isValidateValue($this->pickUpId());

            // 1. ペイメント情報の取得
            //   アプリケーションユーザペイメントIDで
            //   アプリケーションユーザペイメントのレコードを取得
            $applicationUserPayment = $this->_readApplicationUserPayment();

            if (!$applicationUserPayment) {
                throw new Common_Exception_NotFound(sprintf(Logic_Payment_Const::LOG_MSG_RECORD_NOT_FOUND . '[ApplicationUserPaymentId:%s]', $this->pickUpId()));
            }

            // この時点のアプリケーションユーザペイメントを保持しておく
            $procBeforeApplicationUserPayment = clone $applicationUserPayment;

            // 2. プラットフォームチェック
            if ($applicationUserPayment->getPaymentPlatformId() != $platformId) {
                throw new Common_Exception_PreconditionFailed(Logic_Payment_Const::LOG_MSG_ILLEGAL_PARAMETER . sprintf('[PaymentPlatformId:%s] [platformId:%s]', $applicationUserPayment->getPaymentPlatformId(), $platformId));
            }

            // アプリケーションユーザペイメントを保持し、各取引クラスで扱えるようにする
            $this->setApplicationUserPayment($applicationUserPayment);

            // 3. ステータスチェック
            //   1. で取得したペイメントステータスの確認
            //   招かれざるステータスの入力は不正パラメータ例外とする
            $checkClass = Logic_Payment_FactoryAbstract::getExistingClassName(Logic_Payment_Verify_Payment_Interface::CLASS_PREFIX, $applicationUserPayment->getPaymentPlatformId(), $applicationUserPayment->getPaymentDeviceId(), $applicationUserPayment->getPaymentRatingId());
            if (!$checkClass::isValidPaymentTypePaymentStatusPair($applicationUserPayment->getPaymentType(), $applicationUserPayment->getPaymentStatus())) {
                throw new Common_Exception_IllegalParameter(Logic_Payment_Const::LOG_MSG_ILLEGAL_PARAMETER . sprintf('[PaymentType:%s][PaymentStatus:%s]', $applicationUserPayment->getPaymentType(), $applicationUserPayment->getPaymentStatus()));
            }

            // トランザクション開始
            if ($this->_isDoTransaction($applicationUserPayment)) {
                Common_Db::beginTransaction();
            }

            // 4.呼び分け
            //   1. で取得したペイメント種別で更新ロジックを呼び分ける           
            $buildParams['type'] = Logic_Payment_Const::convertPaymentTypeToType($applicationUserPayment->getPaymentType());
            $fc                  = $this->getTradeFactory();
            $fc->setApplicationUserPayment($applicationUserPayment);
            $logic               = $fc->factory($buildParams);
            $logic->setApplicationUser($this->getApplicationUser());
            $logic->setCurrencyPaymentLogic(new Logic_Payment_Trade_CurrencyPayment());
            $logic->loadExpiredDay($this->getApplicationUser()->getApplicationId(), $platformId);
            $logic->exec();

            // 6. アプリケーションユーザIDのアプリケーションユーザペイメントを完了状態に更新
            // 7. コミットとテキストログ出力
            if ($this->_isDoTransaction($applicationUserPayment)) {

                // ペイメントステータスを complete にするかどうか
                if ($this->_isDoCompletePaymentStatus($procBeforeApplicationUserPayment)) {

                    // アプリケーションユーザIDのアプリケーションユーザペイメントを完了状態に更新
                    $this->paymentComplete();
                }

                // 確定
                Common_Db::commit();

                // テキストログ出力
                Misp_TextLog::getInstance()->flush();
            }
        } catch (Common_Exception_Verify $exc) {
            //
            // 検証エラーの例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            if ($this->_isDoErrorPaymentStatus($applicationUserPayment)) {

                // エラーステータス更新処理
                Common_Db::beginTransaction();

                // エラーステータス更新
                $this->paymentError();

                // 確定
                Common_Db::commit();
            }

            throw $exc;
            //
        } catch (Common_Exception_Forbidden $exc) {
            //
            // 検証エラーの例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            // エラーステータス更新処理
            Common_Db::beginTransaction();

            // エラーステータス更新
            $this->paymentError();

            // 確定
            Common_Db::commit();

            throw $exc;
            //
        } catch (Common_Exception_InsufficientFunds $exc) {
            //
            // 残高不足の場合の例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            // エラーステータス更新処理
            Common_Db::beginTransaction();

            // エラーステータス更新
            $this->paymentError();

            // 確定
            Common_Db::commit();

            throw $exc;
            //
        } catch (Common_Exception_NotFound $exc) {
            //
            // レコードが見つからなかった場合の例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
            //
        } catch (Common_Exception_IllegalParameter $exc) {
            //
            // 不正パラメータの例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
            //
        } catch (BadMethodCallException $exc) {
            //
            // 取引ファクトリが未セットでexecされた場合の例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
            //
        } catch (Common_Exception_NotModified $exc) {
            //
            // 更新が行われなかった場合の例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
        } catch (Common_Exception_AlreadyExists $exc) {
            //
            // 決済プラットフォーム側で別の決済が残っていた場合の例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            // エラーステータス更新処理
            Common_Db::beginTransaction();

            // エラーステータス更新
            $this->paymentError();

            // 確定
            Common_Db::commit();

            throw $exc;
            //
        } catch (Exception $exc) {
            //
            // その他
            //
            $this->_logError($exc, __CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
        }
    }

    /**
     * アプリケーションユーザペイメント取得
     * 
     * アプリケーションユーザペイメントを取得します。
     * 
     * @return Application_Model_ApplicationUserPayment
     */
    private function _readApplicationUserPayment()
    {
        return $this->getApplicationUserPaymentMapper($this->getDbSectionNameMain())
                        ->find($this->pickUpId());
    }

    /**
     * ステータス判定
     * 
     * 仮想通貨情報更新の処理を行っていいステータスかどうかを返します。
     * 
     * @param Application_Model_ApplicationUserPayment $applicationUserPayment
     * @return boolean
     */
    private function _isAllowPaymentStatus(Application_Model_ApplicationUserPayment $applicationUserPayment)
    {
// tinyintなのに何故か文字列で返ることがあるので曖昧に比較する
        return $applicationUserPayment->getPaymentStatus() == Logic_Payment_Const::PAYMENT_STATUS_START ? TRUE : FALSE;
    }

    /**
     * ステータス判定
     * 
     * 仮想通貨情報更新の処理を行ってはダメなステータスかどうかを返します。
     * 
     * @param Application_Model_ApplicationUserPayment $applicationUserPayment
     * @return boolean
     */
    private function _isNotAllowPaymentStatus(Application_Model_ApplicationUserPayment $applicationUserPayment)
    {
        return !$this->_isAllowPaymentStatus($applicationUserPayment);
    }

    /**
     * この処理内でトランザクションを利用するかどうかを判定します。
     * 
     * @param Application_Model_ApplicationUserPayment $applicationUserPayment
     * @return boolean TRUE:利用する
     *                  FALSE:利用しない
     */
    private function _isDoTransaction(Application_Model_ApplicationUserPayment $applicationUserPayment)
    {
        $platformId  = $applicationUserPayment->getPaymentPlatformId();
        $deviceId    = $applicationUserPayment->getPaymentDeviceId();
        $paymentType = $applicationUserPayment->getPaymentType();
        $status      = $applicationUserPayment->getPaymentStatus();

        return TRUE;
    }

    /**
     * この処理内でペイメントステータスを complete にするかどうかを判定します。
     * 
     * @param Application_Model_ApplicationUserPayment $applicationUserPayment
     * @return boolean TRUE:complete にする
     *                  FALSE:complete にしない
     */
    private function _isDoCompletePaymentStatus(Application_Model_ApplicationUserPayment $applicationUserPayment)
    {
        $platformId    = $applicationUserPayment->getPaymentPlatformId();
        $deviceId      = $applicationUserPayment->getPaymentDeviceId();
        $paymentType   = $applicationUserPayment->getPaymentType();
        $paymentStatus = $applicationUserPayment->getPaymentStatus();

        return TRUE;
    }

    /**
     * この処理内でペイメントステータスを error にするかどうかを判定します。
     * 
     * @param Application_Model_ApplicationUserPayment $applicationUserPayment
     * @return boolean TRUE:error にする
     *                  FALSE:error にしない
     */
    protected function _isDoErrorPaymentStatus(Application_Model_ApplicationUserPayment $applicationUserPayment)
    {
        $platformId    = $applicationUserPayment->getPaymentPlatformId();
        $paymentType   = $applicationUserPayment->getPaymentType();
        $paymentStatus = $applicationUserPayment->getPaymentStatus();

        return TRUE;
    }

}
