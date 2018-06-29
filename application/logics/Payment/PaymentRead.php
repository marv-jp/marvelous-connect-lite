<?php

/**
 * Logic_Payment_PaymentReadクラスのファイル
 *
 * Logic_Payment_PaymentReadクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_PaymentRead
 *
 * 仮想通貨情報取得
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_PaymentRead extends Logic_Payment_Abstract
{
    /**
     * 残高の連想配列
     *
     * @var array 
     */
    private $_returnBalance = array();

    /**
     * 商品リストの連想配列
     *
     * @var array 
     */
    private $_returnProductItems = array();

    /**
     * 実処理
     * 
     * <h3>exec実行前の前提</h3>
     * 
     * <ol>
     *  <li>モデル：アプリケーションユーザをセットしておくこと
     * </ol>
     * 
     * <h3>使用方法</h3>
     * 
     * <pre>
     * // 取得系ロジックの生成
     * $logic = new Logic_Payment_PaymentRead();
     * 
     * // 処理に必要な情報の準備
     * //   モデル：アプリケーションユーザ
     * $logic->setApplicationUser($applicationUser);
     * </pre>
     * 
     * @throws Exception 想定外の例外
     */
    public function exec($buildParams)
    {
        try {
            $this->_buildParams = $buildParams;

            // アプリケーションユーザ情報を取得
            // (ロジック呼び出し元であらかじめセットされている情報)
            $applicationUser      = $this->getApplicationUser();
            $applicationUserId    = $applicationUser->getApplicationUserId();
            $applicationId        = $applicationUser->getApplicationId();
            $applicationWorldId   = $applicationUser->getApplicationWorldId();
            $platformId           = $buildParams['platformId'] ?? '';
            $deviceId             = $buildParams['deviceId'] ?? '';
            $ratingId             = $buildParams['ratingId'] ?? '';
            $currencySharingGroup = new Logic_Payment_CurrencySharingGroup();

            // パラメータチェック
            $this->_isValidateValue($applicationUserId);
            $this->_isValidateLength($applicationWorldId);

            // Mapper
            $dbSectionName                 = $this->_getDbSectionName();
            $applicationUserCurrencyMapper = $this->getApplicationUserCurrencyMapper($dbSectionName);
            $applicationUserPaymentMapper  = $this->getApplicationUserPaymentMapper($dbSectionName);

            // 無償アプリケーションユーザ通貨連想配列
            $returnBalance = array();

            // アプリケーションユーザペイメント取得
            // アプリケーションユーザ情報で検索
            $where = array();

            $where['applicationUserId']  = array($applicationUserId);
            $where['applicationId']      = array($applicationId);
            $where['applicationWorldId'] = array($applicationWorldId);

            $resultApplicationUserPayment = $applicationUserPaymentMapper->fetchAll($where);
            if ($resultApplicationUserPayment) {
                $this->setApplicationUserPayment($resultApplicationUserPayment[0]);
            }

            // プラットフォームIDがない場合、有償通貨とプラットフォーム固有の無償通貨が取得できず、
            // 中途半端な情報となるため残高情報は返さない
            if (Common_Util_String::isEmpty($platformId)) {
                return;
            }

            // 通貨の消費順を取得
            $currencyPaymentSequence = $this->getConfigCurrencyPaymentSequence($applicationId, $platformId, $deviceId, $ratingId);

            $resultCommonBonusBalance = [];
            $resultPfBonusBalance     = [];

            // 無償アプリケーションユーザ通貨取得
            foreach ($currencyPaymentSequence as $value) {

                if ($value == Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_BONUS) {
                    // プラットフォーム共通無償通貨取得
                    $where['paymentPlatformId'] = [''];
                    $where['unitPrice']         = [Logic_Payment_Const::UNIT_PRICE_BONUS];
                    $resultCommonBonusBalance   = $applicationUserCurrencyMapper->fetchAll($where);
                }

                if ($value == Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_PF_BONUS) {
                    // プラットフォーム固有無償通貨取得
                    unset($where['paymentPlatformId']);
                    $where['unitPrice']   = [$this->getBonusUnitPrice()];
                    // 通貨共有グループ取得
                    $currencySharingGroup->init($applicationId, Logic_Payment_CurrencySharingGroup::MODE_BONUS);
                    $resultPfBonusBalance = $applicationUserCurrencyMapper->fetchAllBySharingGroup($where, $currencySharingGroup->get($platformId, $deviceId, $ratingId));
                }
            }

            // プラットフォーム共通無償通貨とプラットフォーム固有無償通貨の配列をまとめる
            $resultBonusBalance = array_merge($resultCommonBonusBalance, $resultPfBonusBalance);

            // 無償通貨モデルの中身を配列にセット
            foreach ($resultBonusBalance as $m) {
                if (!isset($returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::BONUS_BALANCE][$m->getApplicationCurrencyId()])) {
                    // 通貨IDのキーとその値が存在しない場合、通貨額をそのままセット
                    $returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::BONUS_BALANCE][$m->getApplicationCurrencyId()] = $m->getCurrencyAmount();
                } else {
                    // キーが存在する場合加算
                    $returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::BONUS_BALANCE][$m->getApplicationCurrencyId()] += $m->getCurrencyAmount();
                    $returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::BONUS_BALANCE][$m->getApplicationCurrencyId()] = (string) $returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::BONUS_BALANCE][$m->getApplicationCurrencyId()];
                }
            }

            // 有償アプリケーションユーザ通貨取得
            // 検索条件
            // アプリケーションユーザ情報と単価≠0 は共通条件
            unset($where['unitPrice']);
            unset($where['paymentPlatformId']);
            $where['unitPrice not'] = [$this->getBonusUnitPrice()];

            // 通貨共有グループ取得
            $currencySharingGroup->init($applicationId, Logic_Payment_CurrencySharingGroup::MODE_CREDIT);

            // 取得処理
            $resultCreditBalance = $applicationUserCurrencyMapper->fetchAllBySharingGroup($where, $currencySharingGroup->get($platformId, $deviceId, $ratingId));

            // 有償通貨モデルの中身を
            // 配列にセット
            // ペイメントプラットフォームID毎/アプリケーション通貨ID毎 に通貨額を合計
            foreach ($resultCreditBalance as $m) {

                if (isset($returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::CREDIT_BALANCE][$m->getApplicationCurrencyId()])) {
                    $returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::CREDIT_BALANCE][$m->getApplicationCurrencyId()] += $m->getCurrencyAmount();
                    $returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::CREDIT_BALANCE][$m->getApplicationCurrencyId()] = (string) $returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::CREDIT_BALANCE][$m->getApplicationCurrencyId()];
                } else {
                    $returnBalance[Logic_Payment_Const::BALANCE][Logic_Payment_Const::CREDIT_BALANCE][$m->getApplicationCurrencyId()] = $m->getCurrencyAmount();
                }
            }

            $this->_loadProductItems();

            $this->_returnBalance = $returnBalance;
            //
        } catch (Exception $exc) {
            //
            // その他
            //
            $this->_logError($exc, __CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            throw $exc;
        }
    }

    /**
     * 残高配列取得
     * 
     * exec後に構築された残高連想配列を返します。<br>
     * 
     * exec実行前にコールした場合は空の配列が返ります
     * 
     * @return array
     */
    public function getBalance()
    {
        return $this->_returnBalance;
    }

    /**
     * リクエストメソッドに応じてDBセクション名を返す
     * 
     * @return string DBセクション名
     */
    private function _getDbSectionName()
    {
        $returntDbSectionName = $this->getDbSectionNameMain();
        // リクエストメソッドがGETの場合、サブDBセクション名を返す
        if (Zend_Controller_Front::getInstance()->getRequest()->isGet()) {
            $returntDbSectionName = $this->getDbSectionNameSub();
        }

        return $returntDbSectionName;
    }

    /**
     * 商品リストを返却します。
     * 
     * @return array 商品リストの連想配列
     */
    public function getProductItems()
    {
        return $this->_returnProductItems;
    }

    /**
     * DBを検索し、商品リストの連想配列を構築します。
     */
    protected function _loadProductItems()
    {
        $dbSectionName = $this->_getDbSectionName();

        // プラットフォーム商品テーブルの存在チェック
        $where                        = array();
        $where['paymentPlatformId']   = array($this->pickUpPlatformId());
        $where['paymentDeviceId']     = array($this->pickUpDeviceId());
        $where['paymentRatingId']     = array($this->pickUpRatingId());
        $where['applicationId']       = array($this->getApplicationUser()->getApplicationId());
        $where['deletedDate IS NULL'] = NULL;
        $platformProductMapper        = $this->getPlatformProductMapper($dbSectionName);
        $platformProducts             = $platformProductMapper->fetchAll($where);
        if (!$platformProducts) {
            return;
        }

        $productList = array();

        foreach ($platformProducts as $platformProduct) {

            $where                        = array();
            $where['platformProductId']   = array($platformProduct->getPlatformProductId());
            $where['paymentPlatformId']   = array($this->pickUpPlatformId());
            $where['paymentDeviceId']     = array($this->pickUpDeviceId());
            $where['paymentRatingId']     = array($this->pickUpRatingId());
            $where['applicationId']       = array($this->getApplicationUser()->getApplicationId());
            $where['deletedDate IS NULL'] = NULL;
            $platformProductItemMapper    = $this->getPlatformProductItemMapper($dbSectionName);
            $result                       = $platformProductItemMapper->fetchAll($where);
            if (!$result) {
                throw new Common_Exception_NotFound(sprintf(Logic_Payment_Const::LOG_MSG_RECORD_NOT_FOUND . $this->_generateModelLogFormat(new Application_Model_PlatformProductItem($where))));
            }

            $amountPrice  = 0;
            $productItems = array();
            foreach ($result as $platformProductItem) {

                // 購入価格の計算
                $price       = $platformProductItem->getUnitPrice() * $platformProductItem->getCurrencyAmount();
                $amountPrice += $price;

                if ($platformProductItem->getUnitPrice() == 0) {
                    $productItems['bonusItems'][$platformProductItem->getApplicationCurrencyId()] = $platformProductItem->getCurrencyAmount();
                } else {
                    $productItems['creditItems'][$platformProductItem->getApplicationCurrencyId()] = $platformProductItem->getCurrencyAmount();
                }
            }

            // 値があるものだけ返却項目に加える
            $productItems['productId'] = $platformProduct->getPlatformProductId();
            $productItems['price']     = $amountPrice;

            if (Common_Util_String::isNotEmpty($platformProduct->getPlatformProductName())) {
                $productItems['name'] = $platformProduct->getPlatformProductName();
            }
            if (Common_Util_String::isNotEmpty($platformProduct->getPlatformProductImageUrl())) {
                $productItems['imageUrl'] = $platformProduct->getPlatformProductImageUrl();
            }
            if (Common_Util_String::isNotEmpty($platformProduct->getPlatformProductDescription())) {
                $productItems['description'] = $platformProduct->getPlatformProductDescription();
            }


            $productList[] = $productItems;
        }

        $this->_returnProductItems = $productList;
    }

}
