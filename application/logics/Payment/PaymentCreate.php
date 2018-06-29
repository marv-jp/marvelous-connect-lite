<?php

/**
 * Logic_Payment_PaymentCreateクラスのファイル
 *
 * Logic_Payment_PaymentCreateクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_PaymentCreate
 *
 * 仮想通貨情報登録
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_PaymentCreate extends Logic_Payment_Abstract
{

    /**
     * execでアプリケーションユーザペイメント登録した際のLastInsertId格納用
     * 
     * 各ペイメント種別ごとの個別メソッドで利用するため、プロパティに保持して共通利用する。
     *
     * @var int
     */
    private $_applicationUserPaymentId;

    /**
     * プラットフォーム決済情報検証インスタンス格納用
     * 
     * 有償購入時(credit)、呼び出し側(API)で(アクセサを通して)検証情報を利用できるようにプロパティに保持
     * 
     *
     * @var Logic_Payment_Verify_Payment_Abstract
     */
    private $_verifyPayment = null;

    /**
     * APIリクエストパラメータとDB値のマッピング
     *
     * @var array
     */
    private $_paymentTypeMapping = array(
        'credit'   => Logic_Payment_Const::PAYMENT_TYPE_CREDIT,
        'bonus'    => Logic_Payment_Const::PAYMENT_TYPE_BONUS,
        'exchange' => Logic_Payment_Const::PAYMENT_TYPE_EXCHANGE,
        'payment'  => Logic_Payment_Const::PAYMENT_TYPE_PAYMENT,
    );

    /**
     * 実処理
     * 
     * <h3>exec実行前の前提</h3>
     * 
     * <ol>
     *  <li>モデル：アプリケーションユーザをセットしておくこと
     *  <li>ロジック：通貨購入をセットしておくこと
     *  <li>ファクトリ：プラットフォーム決済情報検証をセットしておくこと
     * </ol>
     * 
     * <h3>使用方法</h3>
     * 
     * <pre>
     * // ロジック引数の準備
     * $request     = $this->getRequest();
     * $bodyParam   = Zend_Json::decode($request->getRawBody());
     * $buildParams = $bodyParam['entry'];

     * // 登録系ロジックの生成
     * $logic = new Logic_Payment_PaymentCreate();
     * 
     * // 処理に必要な情報の準備
     * //   モデル：アプリケーションユーザ
     * $logic->setApplicationUser($applicationUser);
     * //   ロジック：通貨購入
     * $logic->setCurrencyCreditLogic(new Logic_Payment_Trade_CurrencyCredit());
     * //   ファクトリ：プラットフォーム決済情報検証
     * $fc = new Logic_Payment_VerifyPaymentFactory($buildParams);
     * $fc->setApplicationUserPayment($applicationUserPayment);
     * $logic->setVerifyPaymentFactory($fc);
     *  
     * // 登録系ロジックの実行
     * $logic->exec($buildParams);
     * </pre>
     * 
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     * @throws Common_Exception_AlreadyExists これから処理を行うアプリケーションユーザのレコードが、アプリケーションユーザペイメントテーブルに存在した場合にThrowされます
     * @throws Common_Exception_Verify 検証したレシートが不正だった場合にThrowされます
     * @throws Common_Exception_InsufficientFunds 購入の際、残高不足だった場合にThrowされます
     * @throws Common_Exception_Exception レコード登録が失敗した場合にThrowされます
     * @throws Exception 想定外の例外
     */
    public function exec($buildParams)
    {
        try {
            // トランザクション開始
            Common_Db::beginTransaction();

            $this->_buildParams = $buildParams;

            // アプリケーションユーザ情報を取得
            // (ロジック呼び出し元であらかじめセットされている情報)
            $applicationUser    = $this->getApplicationUser();
            $applicationUserId  = $applicationUser->getApplicationUserId();
            $applicationWorldId = $applicationUser->getApplicationWorldId();

            // パラメータチェック
            $this->_isValidateValue($applicationUserId);
            $this->_isValidateLength($applicationWorldId);

            // アプリケーションユーザペイメントのコンフリクトチェック
            //   ペイメント処理はアプリケーションユーザ単位の並列処理を許可しない
            if ($this->_isConflictPaymentFlow()) {
                throw new Common_Exception_AlreadyExists(sprintf('%s[ApplicationId:%s][ApplicationWorldId:%s][ApplicationUserId:%s]', Logic_Payment_Const::LOG_MSG_ALREADY_PAYMENT_PROCESS, $applicationUser->getApplicationId(), $applicationWorldId, $applicationUserId));
            }

            // アプリケーションユーザペイメント登録
            //   この登録はペイメント処理を「開始した」というマーカーのような意味合いなので、
            //   処理するペイメント種別に関係なく登録する必要がある
            $this->_createApplicationUserPayment();

            // 上記アプリケーションユーザペイメント登録処理後、ペイメント種別で各種別処理を呼び分ける
            $this->_executePaymentProc();

            // 確定
            Common_Db::commit();
            // テキストログ出力
            Misp_TextLog::getInstance()->flush();
            //
        } catch (Common_Exception_AlreadyExists $exc) {
            //
            // ペイメント処理が衝突した場合の例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
            //
        } catch (Common_Exception_Verify $exc) {
            //
            // レシート検証が不正だった場合の例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
            //
        } catch (Common_Exception_InsufficientFunds $exc) {
            //
            // 残高不足だった場合の例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
            //
        } catch (Common_Exception_Exception $exc) {
            //
            // 登録が失敗した場合の例外ハンドリング
            //
            $this->_logInfo($exc, __CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

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
     * ペイメント処理メソッド実行
     * 
     * ペイメント種別に応じた処理メソッドを実行します。
     */
    private function _executePaymentProc()
    {
        $method = $this->_generatePaymentProcMethod();
        $this->$method();
    }

    /**
     * ペイメント処理メソッド名生成
     * 
     * ペイメント種別に対応した処理メソッドの名前を生成します。
     * 
     * <table>
     *   <tr><th>ペイメント種別</th><th>生成されるメソッド名</th></tr>
     *   <tr><td>credit</td><td>_execCredit</td></tr>
     *   <tr><td>exchange</td><td>_execExchange</td></tr>
     *   <tr><td>payment</td><td>_execPayment</td></tr>
     *   <tr><td>bonus</td><td>_execBonus</td></tr>
     * </table>
     * 
     * @return string
     */
    private function _generatePaymentProcMethod()
    {
        // typeがbonusの場合のみ以下の処理を行う
        if (strtolower($this->pickUpType()) == Logic_Payment_Const::PAYMENT_TYPE_BONUS_STRING) {

            // 設定に独自の無償通貨単価がある場合は、そちらを使用する
            if ($this->getBonusUnitPrice()) {
                return '_execBonusCredit';
            }
        }

        return '_exec' . ucfirst($this->pickUpType());
    }

    /**
     * ペイメント種別：credit処理
     * 
     * 有償購入、あるいは有償＋無償購入時の処理です。
     * 
     * @return boolean
     * @throws Common_Exception_Verify 検証したレシートが不正の場合にThrowされます
     * @throws Common_Exception_Verify アプリケーションユーザ通貨購入ログに対象のレシート情報が存在する場合は使用済みとしてThrowされます
     */
    private function _execCredit()
    {
        // レシートがついていなければ通常の購入POSTなので、ここで終わり
        if ($this->_isSkipCreditExec()) {
            return TRUE;
        }

        // レシートがついていたのでそれについての処理を続行する
        //   まずレシート検証
        $verifyPaymentFactory = $this->getVerifyPaymentFactory();
        $verifyPaymentFactory->setApplicationUserPayment($this->getApplicationUserPayment());
        $verifyPayment        = $verifyPaymentFactory->factory($this->_buildParams);
        $verifyPayment->setApplicationUser($this->getApplicationUser());
        if (!$verifyPayment->verify()) {
            throw new Common_Exception_Verify(sprintf('%s[Platform:%s][Receipt:%s][Signature:%s]', Logic_Payment_Const::LOG_MSG_ILLEGAL_RECEIPT, $verifyPayment->getPaymentPlatformId(), $verifyPayment->getReceipt(), $verifyPayment->getSignature()));
        }

        // 使用Mapper取得
        $dbSectionName                          = $this->getDbSectionNameSub();
        $applicationUserCurrencyCreditLogMapper = $this->getApplicationUserCurrencyCreditLogMapper($dbSectionName);
        $platformPaymentMapper                  = $this->getPlatformPaymentMapper($dbSectionName);

        // データ確認
        //   MZCLのレシート検証(パース)結果のペイメント情報をもとに、
        //      
        //   1. アプリケーションユーザ通貨購入ログのレコード存在確認(存在しないことを期待)
        //      →ここにレコードがあるということは購入処理が完了していることが示されるので、レシートの再POSTを認めてはいけない
        //      
        //   2. プラットフォームペイメントのレコード存在確認(存在しないことを期待)
        //      →ここにレコードがあるということは購入処理が完了していることが示されるので、レシートの再POSTを認めてはいけない
        //      
        //   を検証する
        foreach ($verifyPayment->getVerifiedReceipt() as $paymentModel) {

            $where2 = array();
            list($paymentIds, $paymentId) = $verifyPayment->getPaymentIds($paymentModel, NULL);
            foreach ($paymentIds as $platformPaymentId => $data) {

                $where2['platformPaymentId'] = array($platformPaymentId);
                if ($applicationUserCurrencyCreditLogMapper->fetchAll($where2)) {
                    throw new Common_Exception_Verify(sprintf('%s[PlatformPaymentId:%s]', Logic_Payment_Const::LOG_MSG_ALREADY_USED_RECEIPT, $platformPaymentId));
                }
                if ($platformPaymentMapper->fetchAll($where2)) {
                    throw new Common_Exception_Verify(sprintf('%s[PlatformPaymentId:%s]', Logic_Payment_Const::LOG_MSG_ALREADY_USED_RECEIPT, $platformPaymentId));
                }
            }
        }

        // レシート処理がOKだったので、呼び出し側(API)で参照できるようにプロパティ保持
        $this->_verifyPayment = $verifyPayment;
    }

    /**
     * ペイメント種別：bonus処理
     * 
     * 無償購入時の処理です。
     * 
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    private function _execBonus()
    {
        $dbSectionName                   = $this->getDbSectionNameMain();
        $paymentItemMapper               = $this->getApplicationUserPaymentItemMapper($dbSectionName);
        $targetCurrencyPaymentItemMapper = $this->getApplicationUserTargetCurrencyPaymentItemMapper($dbSectionName);
        $currencyCollection              = new Misp_Collection_ApplicationUserCurrency();

        $applicationUser    = $this->getApplicationUser();
        $applicationId      = $applicationUser->getApplicationId();
        $applicationUserId  = $applicationUser->getApplicationUserId();
        $applicationWorldId = $applicationUser->getApplicationWorldId();

        $paymentId         = $this->_applicationUserPaymentId;
        $paymentPlatformId = is_null($this->pickUpPlatformId()) ? '' : $this->pickUpPlatformId();
        $paymentDeviceId   = is_null($this->pickUpDeviceId()) ? '' : $this->pickUpDeviceId();
        $paymentRatingId   = is_null($this->pickUpRatingId()) ? '' : $this->pickUpRatingId();

        // payment項目の内容を取得
        $nowDatetime = $this->getNowDatetime();
        foreach ($this->pickUpPayment() as $payment) {

            $currencyId     = $payment['toId'];
            $currencyAmount = $payment['toCurrency'];

            $applicationUserPaymentItem = new Application_Model_ApplicationUserPaymentItem();
            $applicationUserPaymentItem->setApplicationUserPaymentId($paymentId);
            $applicationUserPaymentItem->setCreatedDate($nowDatetime);
            $applicationUserPaymentItem->setUpdatedDate($nowDatetime);
            $lastInsertId               = $paymentItemMapper->insert($applicationUserPaymentItem);
            if (!$lastInsertId) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($applicationUserPaymentItem));
            }

            // アプリケーションユーザターゲット通貨ペイメントアイテム登録
            $targetCurrencyPaymentItem = new Application_Model_ApplicationUserTargetCurrencyPaymentItem();
            $targetCurrencyPaymentItem->setApplicationUserPaymentItemId($lastInsertId);
            $targetCurrencyPaymentItem->setApplicationCurrencyId($currencyId);
            $targetCurrencyPaymentItem->setCurrencyAmount($currencyAmount);
            $targetCurrencyPaymentItem->setPrice(Logic_Payment_Const::PRICE_BONUS);
            $targetCurrencyPaymentItem->setCreatedDate($nowDatetime);
            $targetCurrencyPaymentItem->setUpdatedDate($nowDatetime);
            if (!$targetCurrencyPaymentItemMapper->insert($targetCurrencyPaymentItem)) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($targetCurrencyPaymentItem));
            }

            // アプリケーションユーザ通貨モデル構築
            $m = new Application_Model_ApplicationUserCurrency();
            $m->setApplicationUserId($applicationUserId);
            $m->setApplicationId($applicationId);
            $m->setApplicationWorldId($applicationWorldId);
            $m->setApplicationCurrencyId($currencyId);
            $m->setUnitPrice(Logic_Payment_Const::UNIT_PRICE_BONUS);
            $m->setPaymentPlatformId($paymentPlatformId);
            $m->setPaymentDeviceId($paymentDeviceId);
            $m->setPaymentRatingId($paymentRatingId);
            $m->setCurrencyAmount($currencyAmount);

            $currencyCollection->attach($m);

            // テキストにボーナスログを残す
            // アプリケーションユーザ通貨ボーナスログモデル構築
            $logModel = new Application_Model_ApplicationUserCurrencyBonusLog();
            $logModel->setApplicationUserPaymentItemId($lastInsertId);
            $logModel->setApplicationUserPaymentId($paymentId);
            $logModel->setPaymentPlatformId($paymentPlatformId);
            $logModel->setPaymentDeviceId($paymentDeviceId);
            $logModel->setPaymentRatingId($paymentRatingId);
            $logModel->setApplicationUserId($applicationUserId);
            $logModel->setApplicationId($applicationId);
            $logModel->setApplicationWorldId($applicationWorldId);
            $logModel->setApplicationCurrencyId($currencyId);
            $logModel->setCurrencyAmount($currencyAmount);
            $logModel->setCreatedDate($nowDatetime);
            $logModel->setUpdatedDate($nowDatetime);

            // 呼び出し
            Misp_TextLog::getInstance()->push($logModel);
        }

        // 通貨購入
        $credit = $this->getCurrencyCreditLogic();
        $credit->setApplicationUserCurrencyCollection($currencyCollection);
        $credit->exec();

        // ペイメントステータスを「完了」状態に更新
        $this->paymentComplete();
    }

    use \misp\logics\payment\traits\Logic_Payment_Trait_PlatformPaymentRepository;
    use \misp\logics\payment\traits\Logic_Payment_Trait_PlatformPaymentItemRepository;
    use \misp\logics\payment\traits\Logic_Payment_Trait_ApplicationUserPaymentItemRepository;
    use \misp\logics\payment\traits\Logic_Payment_Trait_ApplicationUserTargetCurrencyPaymentItemRepository;
    use \misp\logics\payment\traits\Logic_Payment_Trait_ApplicationUserPlatformPaymentRelationRepository;

    /**
     * ペイメント種別：bonus処理(有償通貨扱い)
     * 
     * 無償購入時の処理です。
     * 
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    private function _execBonusCredit()
    {

        $applicationUser    = $this->getApplicationUser();
        $applicationId      = $applicationUser->getApplicationId();
        $applicationUserId  = $applicationUser->getApplicationUserId();
        $applicationWorldId = $applicationUser->getApplicationWorldId();

        $applicationUserPaymentId = $this->_applicationUserPaymentId;
        $this->_paymentId         = $applicationUserPaymentId;
        $paymentPlatformId        = is_null($this->pickUpPlatformId()) ? '' : $this->pickUpPlatformId();
        $this->_platform          = $paymentPlatformId;
        $paymentDeviceId          = is_null($this->pickUpDeviceId()) ? '' : $this->pickUpDeviceId();
        $paymentRatingId          = is_null($this->pickUpRatingId()) ? '' : $this->pickUpRatingId();
        $this->_platformProductId = '';

        $bonusUnitPrice = $this->_bonusUnitPrice;

        // 作成日時
        $this->_nowDatetime = $this->getNowDatetime();
        $nowDatetime        = $this->_nowDatetime;

        // 実行日時
        // (無償POSTで、実行日時が指定されている場合はそれを実行日時として使用する)
        $executedDate = $this->pickUpPublished() ?? $nowDatetime;

        // プラットフォームペイメント 登録
        $platformPayment = new Application_Model_PlatformPayment();
        $platformPayment->setPlatformPaymentId($applicationUserPaymentId);
        $platformPayment->setPaymentPlatformId($paymentPlatformId);
        $platformPayment->setPaymentDeviceId($paymentDeviceId);
        $platformPayment->setPaymentRatingId($paymentRatingId);
        $platformPayment->setPlatformPaymentStatus(NULL);
        $platformPayment->setReceipt(NULL);
        $platformPayment->setSignature(NULL);
        $platformPayment->setCreatedDate($nowDatetime);
        $platformPayment->setUpdatedDate($nowDatetime);
        if (!$this->savePlatformPayment($platformPayment)) {
            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($platformPayment));
        }

        // 通貨購入処理用コレクション作成
        $applicationUserCurrencyCollection = new Misp_Collection_ApplicationUserCurrency();

        // payment項目の内容を取得(bonusItems)
        foreach ($this->pickUpPayment() as $payment) {

            $currencyId     = $payment['toId'];
            $currencyAmount = $payment['toCurrency'];
            $price          = Logic_Payment_Const::PRICE_BONUS;

            // プラットフォームペイメントアイテム登録
            $data                  = ['price' => $price, 'quantity' => 1, 'executedDate' => $executedDate];
            $platformPaymentItemId = $this->savePlatformPaymentItem($data);
            if (!$platformPaymentItemId) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . '[platformPaymentItem]' . print_r($data, 1));
            }
            // 使用したモデルを取得しておく
            $platformPaymentItem = $this->getTraitPlatformPaymentItem();
            $platformPaymentItem->setPlatformPaymentItemId($platformPaymentItemId);

            // アプリケーションユーザペイメントアイテム登録
            $applicationUserPaymentItemId = $this->saveApplicationUserPaymentItem($applicationUserPaymentId);
            if (!$applicationUserPaymentItemId) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . sprintf('[applicationUserPaymentItem][applicationUserPaymentId:%s]', $applicationUserPaymentId));
            }

            // アプリケーションユーザターゲット通貨ペイメントアイテム登録
            $targetCurrencyPaymentItem = new Application_Model_ApplicationUserTargetCurrencyPaymentItem();
            $targetCurrencyPaymentItem->setApplicationUserPaymentItemId($applicationUserPaymentItemId);
            $targetCurrencyPaymentItem->setApplicationCurrencyId($currencyId);
            $targetCurrencyPaymentItem->setCurrencyAmount($currencyAmount);
            $targetCurrencyPaymentItem->setPrice($price);
            $targetCurrencyPaymentItem->setCreatedDate($nowDatetime);
            $targetCurrencyPaymentItem->setUpdatedDate($nowDatetime);
            if (!$this->saveApplicationUserTargetCurrencyPaymentItem($targetCurrencyPaymentItem)) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($targetCurrencyPaymentItem));
            }

            // アプリケーションユーザ通貨モデルを作成し、アタッチ
            $applicationUserCurrencyModel = new Application_Model_ApplicationUserCurrency();
            $applicationUserCurrencyModel->setApplicationUserPaymentItemId($applicationUserPaymentItemId);
            $applicationUserCurrencyModel->setApplicationUserPaymentId($applicationUserPaymentId);
            $applicationUserCurrencyModel->setApplicationUserId($applicationUserId);
            $applicationUserCurrencyModel->setApplicationId($applicationId);
            $applicationUserCurrencyModel->setApplicationWorldId($applicationWorldId);
            $applicationUserCurrencyModel->setPaymentPlatformId($paymentPlatformId);
            $applicationUserCurrencyModel->setPaymentDeviceId($paymentDeviceId);
            $applicationUserCurrencyModel->setPaymentRatingId($paymentRatingId);
            $applicationUserCurrencyModel->setApplicationCurrencyId($currencyId);
            $applicationUserCurrencyModel->setUnitPrice($bonusUnitPrice);
            $applicationUserCurrencyModel->setCurrencyAmount($currencyAmount);
            $applicationUserCurrencyModel->setExecutedDate($executedDate);
            $applicationUserCurrencyModel->setCreatedDate($nowDatetime);
            $applicationUserCurrencyModel->setUpdatedDate($nowDatetime);

            $applicationUserCurrencyCollection->attach($applicationUserCurrencyModel);

            // アプリケーションユーザプラットフォームペイメント関連 登録
            if (!$this->saveApplicationUserPlatformPaymentRelation($applicationUserPaymentItemId, $platformPaymentItemId)) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . sprintf('[applicationUserPlatformPaymentRelation][applicationUserPaymentItemId:%s][platformPaymentItemId:%s]', $applicationUserPaymentItemId, $platformPaymentItemId));
            }

            // 有償ログ出力
            $this->_saveVerifiedApplicationUserCurrencyCreditLog($platformPaymentItem, $platformPayment, $targetCurrencyPaymentItem);
        }

        // 通貨購入
        $credit = $this->getCurrencyCreditLogic();
        $credit->loadExpiredDay($applicationId, $paymentPlatformId);
        $credit->setApplicationUserCurrencyCollection($applicationUserCurrencyCollection);
        $credit->exec();

        // ペイメントステータスを「完了」状態に更新
        $this->paymentComplete();
    }

    /**
     * ペイメント種別：exchange処理
     * 
     * 両替時の処理です。<br>
     * 消費時の処理とコードが似ていますが、共通化の難しい微妙な差異があるため敢えて分けています。
     * 
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     * @throws Common_Exception_InsufficientFunds 両替の際、残高不足だった場合にThrowされます
     */
    private function _execExchange()
    {
        // paidWithのチェック
        $paidWith = $this->pickUpPaidWith();
        if ($paidWith) {
            if ($this->isNotAllowedPaidWith($this->pickUpPaidWith())) {
                throw new Common_Exception_IllegalParameter(Logic_Const::LOG_MSG_ILLEGAL_PARAMETER . 'paidWith = ' . $paidWith);
            }
        }

        // Mapper取得
        $dbSectionName                                  = $this->getDbSectionNameMain();
        $paymentItemMapper                              = $this->getApplicationUserPaymentItemMapper($dbSectionName);
        $applicationUserCurrencyPaymentItemMapper       = $this->getApplicationUserCurrencyPaymentItemMapper($dbSectionName);
        $applicationUserTargetCurrencyPaymentItemMapper = $this->getApplicationUserTargetCurrencyPaymentItemMapper($dbSectionName);

        // 残高確認用配列
        $currencyPayment = array();

        // DB登録用日付
        $nowDatetime = $this->getNowDatetime();

        // POST時共通で行うアプリケーションユーザペイメント登録時のLastInsertIdを取得
        $paymentId = $this->_applicationUserPaymentId;

        // entryを取得
        $entry = $this->pickUpPayment();
        if (Common_Util_String::isEmpty($entry)) {
            // entryがない場合、例外を返す
            throw new Common_Exception_IllegalParameter(Logic_Const::LOG_MSG_ILLEGAL_PARAMETER . 'entryが取得できませんでした');
        }

        // POST送信されたpayment項目の内容をもとに
        // ペイメントアイテムと商品ペイメントアイテムを登録する
        // (複数件の可能性があるのでループ処理する)
        foreach ($entry as $payment) {

            $fromCurrencyId     = $payment['fromId'];
            $toCurrencyId       = $payment['toId'];
            $fromCurrencyAmount = $payment['fromCurrency'];
            $toCurrencyAmount   = $payment['toCurrency'];

            // 処理可能両替レートチェック
            if (!$this->canExchangeRate($fromCurrencyAmount, $toCurrencyAmount)) {
                throw new Common_Exception_Verify(Logic_Payment_Const::LOG_MSG_NO_EXCHANGE_RATE . sprintf('[fromCurrency:%s, toCurrency:%s]', $fromCurrencyAmount, $toCurrencyAmount));
            }

            // アプリケーションユーザペイメントアイテム登録
            $applicationUserPaymentItem = new Application_Model_ApplicationUserPaymentItem();
            $applicationUserPaymentItem->setApplicationUserPaymentId($paymentId);
            $applicationUserPaymentItem->setCreatedDate($nowDatetime);
            $applicationUserPaymentItem->setUpdatedDate($nowDatetime);
            $lastInsertId               = $paymentItemMapper->insert($applicationUserPaymentItem);
            if (!$lastInsertId) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($applicationUserPaymentItem));
            }

            // アプリケーションユーザ通貨ペイメントアイテム登録
            $m = new Application_Model_ApplicationUserCurrencyPaymentItem();
            $m->setApplicationCurrencyId($fromCurrencyId);
            $m->setCurrencyAmount($fromCurrencyAmount);
            $m->setApplicationUserPaymentItemId($lastInsertId);
            $m->setCreatedDate($nowDatetime);
            $m->setUpdatedDate($nowDatetime);
            if (!$applicationUserCurrencyPaymentItemMapper->insert($m)) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
            }

            // アプリケーションユーザターゲット通貨ペイメントアイテム登録
            $m = new Application_Model_ApplicationUserTargetCurrencyPaymentItem();
            $m->setApplicationCurrencyId($toCurrencyId);
            $m->setCurrencyAmount($toCurrencyAmount);
            $m->setApplicationUserPaymentItemId($lastInsertId);
            $m->setPrice(Logic_Payment_Const::PRICE_EXCHANGE);
            $m->setCreatedDate($nowDatetime);
            $m->setUpdatedDate($nowDatetime);
            if (!$applicationUserTargetCurrencyPaymentItemMapper->insert($m)) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
            }

            // 消費額取得
            if (array_key_exists($fromCurrencyId, $currencyPayment)) {
                // 通貨IDがある場合
                $currencyPayment[$fromCurrencyId] += $fromCurrencyAmount;
            } else {
                // 通貨IDがない場合
                $currencyPayment[$fromCurrencyId] = $fromCurrencyAmount;
            }
        }

        // 残高不足チェック
        if (!$this->_isCurrencyEnough($currencyPayment)) {
            throw new Common_Exception_InsufficientFunds(Logic_Payment_Const::MSG_INSUFFICIENTFUNDS);
        }
    }

    /**
     * ペイメント種別：payment処理
     * 
     * 消費時の処理です。<br>
     * 両替時の処理とコードが似ていますが、共通化の難しい微妙な差異があるため敢えて分けています。
     * 
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     * @throws Common_Exception_InsufficientFunds 消費の際、残高不足だった場合にThrowされます
     */
    private function _execPayment()
    {
        // paidWithのチェック
        $paidWith = $this->pickUpPaidWith();
        if ($paidWith) {
            if ($this->isNotAllowedPaidWith($this->pickUpPaidWith())) {
                throw new Common_Exception_IllegalParameter(Logic_Const::LOG_MSG_ILLEGAL_PARAMETER . 'paidWith = ' . $paidWith);
            }
        }

        // Mapper取得
        $dbSectionName                                 = $this->getDbSectionNameMain();
        $paymentItemMapper                             = $this->getApplicationUserPaymentItemMapper($dbSectionName);
        $applicationUserCurrencyPaymentItemMapper      = $this->getApplicationUserCurrencyPaymentItemMapper($dbSectionName);
        $applicationUserTargetProductPaymentItemMapper = $this->getApplicationUserTargetProductPaymentItemMapper($dbSectionName);

        // 残高確認用配列
        $currencyPayment = array();

        // DB登録用日付
        $nowDatetime = $this->getNowDatetime();

        // POST時共通で行うアプリケーションユーザペイメント登録時のLastInsertIdを取得
        $paymentId = $this->_applicationUserPaymentId;

        // entryを取得
        $entry = $this->pickUpPayment();
        if (Common_Util_String::isEmpty($entry)) {
            // entryがない場合、例外を返す
            throw new Common_Exception_IllegalParameter(Logic_Const::LOG_MSG_ILLEGAL_PARAMETER . 'entryが取得できませんでした');
        }

        // POST送信されたpayment項目の内容をもとに
        // ペイメントアイテムと商品ペイメントアイテムを登録する
        // (複数件の可能性があるのでループ処理する)
        foreach ($entry as $payment) {

            // なにを買う(消費)するか
            $toProductId = $payment['productId'];
            $toQuantity  = $payment['quantity'];

            // 空文字チェック
            $this->_isValidateValue($toProductId);
            $this->_isValidateValue($toQuantity);

            // アプリケーションユーザペイメントアイテム登録
            $applicationUserPaymentItem = new Application_Model_ApplicationUserPaymentItem();
            $applicationUserPaymentItem->setApplicationUserPaymentId($paymentId);
            $applicationUserPaymentItem->setCreatedDate($nowDatetime);
            $applicationUserPaymentItem->setUpdatedDate($nowDatetime);
            $lastInsertId               = $paymentItemMapper->insert($applicationUserPaymentItem);
            if (!$lastInsertId) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($applicationUserPaymentItem));
            }

            // アプリケーションユーザターゲット商品ペイメントアイテム登録
            $m = new Application_Model_ApplicationUserTargetProductPaymentItem();
            $m->setApplicationUserPaymentItemId($lastInsertId);
            $m->setApplicationProductId($toProductId);
            $m->setProductQuantity($toQuantity);
            $m->setCreatedDate($nowDatetime);
            $m->setUpdatedDate($nowDatetime);
            if (!$applicationUserTargetProductPaymentItemMapper->insert($m)) {
                throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
            }

            // なにで
            foreach ($payment['paymentItems'] as $paymentItem) {

                $fromCurrencyId     = $paymentItem['fromId'];
                $fromCurrencyAmount = $paymentItem['fromCurrency'];

                // アプリケーションユーザ通貨ペイメントアイテム
                $m = new Application_Model_ApplicationUserCurrencyPaymentItem();
                $m->setApplicationUserPaymentItemId($lastInsertId);
                $m->setApplicationCurrencyId($fromCurrencyId);
                $m->setCurrencyAmount($fromCurrencyAmount);
                $m->setCreatedDate($nowDatetime);
                $m->setUpdatedDate($nowDatetime);
                if (!$applicationUserCurrencyPaymentItemMapper->insert($m)) {
                    throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
                }

                // 消費額取得
                if (array_key_exists($fromCurrencyId, $currencyPayment)) {
                    // 通貨IDがある場合
                    $currencyPayment[$fromCurrencyId] += $fromCurrencyAmount;
                } else {
                    // 通貨IDがない場合
                    $currencyPayment[$fromCurrencyId] = $fromCurrencyAmount;
                }
            }
        }

        // 残高不足チェック
        if (!$this->_isCurrencyEnough($currencyPayment)) {
            throw new Common_Exception_InsufficientFunds(Logic_Payment_Const::MSG_INSUFFICIENTFUNDS);
        }
    }

    /**
     * 検証済みプラットフォーム決済情報取得
     * 
     * レシート処理OK時のプラットフォーム決済情報検証オブジェクトを返します。<br>
     * <br>
     * おもに呼び出し側(API)で検証済みレシートデータ(by MZCL)を参照する場合に使用します。
     * 
     * @return Logic_Payment_Verify_Payment_Abstract
     */
    public function getPaymentVerify()
    {
        return $this->_verifyPayment;
    }

    /**
     * ペイメントフロー衝突確認
     * 
     * ペイメントフローが衝突するかを確認します。<br>
     * <br>
     * アプリケーションユーザペイメントテーブルを検索し、<br>
     * その処理ユーザ(アプリケーションユーザ)のレコードが存在していたら衝突とします。<br>
     * レコードが存在することが問題なので、レコードのペイメント種別は問いません。
     * 
     * @return boolean
     */
    private function _isConflictPaymentFlow()
    {
        $applicationUser    = $this->getApplicationUser();
        $applicationId      = $applicationUser->getApplicationId();
        $applicationUserId  = $applicationUser->getApplicationUserId();
        $applicationWorldId = $applicationUser->getApplicationWorldId();

        $where                       = array();
        $where['applicationUserId']  = array($applicationUserId);
        $where['applicationId']      = array($applicationId);
        $where['applicationWorldId'] = array($applicationWorldId);

        // コンフリクトチェックなので念の為にスレーブではなくマスタ検索する
        if ($this->getApplicationUserPaymentMapper($this->getDbSectionNameMain())->fetchAll($where)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * アプリケーションユーザペイメント登録
     * 
     * ペイメント処理を開始するためにアプリケーションユーザペイメントを登録します。<br>
     * <br>
     * 登録で得られるLastInserIdは後続のペイメント種別ごとの個別メソッドで利用するため、
     * 共通利用できるようにプロパティ(_applicationUserPaymentId)に保持します。
     * 
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    private function _createApplicationUserPayment()
    {
        $applicationUser    = $this->getApplicationUser();
        $applicationId      = $applicationUser->getApplicationId();
        $applicationUserId  = $applicationUser->getApplicationUserId();
        $applicationWorldId = $applicationUser->getApplicationWorldId();

        // ペイメントプラットフォームユーザIDがあれば設定
        $platformPaymentUserId = NULL;
        if (!is_null($this->pickUpAccount())) {
            $account = $this->pickUpAccount();
            // userIdのキーがあれば設定
            if (array_key_exists('userId', $account)) {
                $platformPaymentUserId = $account['userId'];
            }
        }

        // データ構築
        $nowDatetime            = $this->getNowDatetime();
        $applicationUserPayment = new Application_Model_ApplicationUserPayment();
        $applicationUserPayment->setApplicationUserId($applicationUserId);
        $applicationUserPayment->setApplicationId($applicationId);
        $applicationUserPayment->setApplicationWorldId($applicationWorldId);
        $applicationUserPayment->setPaymentPlatformUserId($platformPaymentUserId);
        $applicationUserPayment->setPaymentPlatformId(is_null($this->pickUpPlatformId()) ? '' : $this->pickUpPlatformId());
        $applicationUserPayment->setPaymentDeviceId(is_null($this->pickUpDeviceId()) ? '' : $this->pickUpDeviceId());
        $applicationUserPayment->setPaymentRatingId(is_null($this->pickUpRatingId()) ? '' : $this->pickUpRatingId());
        $applicationUserPayment->setPaymentType($this->_convertPaymentTypeStringToDb($this->pickUpType()));
        $applicationUserPayment->setPaymentStatus(Logic_Payment_Const::PAYMENT_STATUS_START);
        $applicationUserPayment->setCreatedDate($nowDatetime);
        $applicationUserPayment->setUpdatedDate($nowDatetime);

        // 登録
        $this->_applicationUserPaymentId = $this->getApplicationUserPaymentMapper($this->getDbSectionNameMain())->insert($applicationUserPayment);
        if (!$this->_applicationUserPaymentId) {
            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($applicationUserPayment));
        }
        $applicationUserPayment->setApplicationUserPaymentId($this->_applicationUserPaymentId);
        $this->setApplicationUserPayment($applicationUserPayment);
    }

    /**
     * レシート付きPOST処理判定
     * 
     * レシート付きPOSTの処理を行うかどうかを判定します。<br>
     * <br>
     * レシートがついていなければ通常の購入POSTなので、アプリケーションユーザペイメント登録だけで終了(FALSE)<br>
     * レシート付きの場合はログテーブルに存在確認処理を行う必要があるのでスキップしない(TRUE)
     * 
     * @return boolean
     */
    private function _isSkipCreditExec()
    {
        $signature  = $this->pickUpSignature();
        $sigendData = NULL;
        $receipt    = NULL;

        if ($signature) {
            $sigendData = $this->pickUpReceipt();
        } else {
            $receipt = $this->pickUpReceipt();
        }

        // どれか一つでも指定されていた場合は、レシート検証を実施する
        if (Misp_Util::isEmpty($receipt) && Misp_Util::isEmpty($signature) && Misp_Util::isEmpty($sigendData)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * APIリクエストパラメータのペイメント種別文字列をDB値に変換します
     * 
     * @param string $paymentTypeString
     * @return int
     * @throws Common_Exception_IllegalParameter APIリクエストパラメータのペイメント種別が定義リストに存在しない場合にThrowされます
     */
    private function _convertPaymentTypeStringToDb($paymentTypeString)
    {
        // APIリクエストパラメータのペイメント種別が定義リストになければ不正とする
        $paymentType = strtolower($paymentTypeString);
        if (!array_key_exists($paymentType, $this->_paymentTypeMapping)) {
            throw new Common_Exception_IllegalParameter(Logic_Payment_Const::LOG_MSG_ILLEGAL_PARAMETER);
        }

        return $this->_paymentTypeMapping[$paymentType];
    }

}
