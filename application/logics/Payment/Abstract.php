<?php

/**
 * Logic_Payment_Abstractクラスのファイル
 *
 * Logic_Payment_Abstractクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_Abstract
 *
 * 仮想通貨基底クラス
 *
 * @category Zend
 * @package  Logic_Payment
 * @method void setTradeFactory(Logic_Payment_Trade_Factory $fc) 仮想通貨情報取引ファクトリをセットします
 * @method Logic_Payment_Trade_Factory getTradeFactory() 仮想通貨情報取引ファクトリを返します
 * @method void setVerifyPaymentFactory(Logic_Payment_Verify_Payment_Factory $fc) プラットフォーム決済情報検証ファクトリをセットします
 * @method Logic_Payment_Verify_Payment_Factory getVerifyPaymentFactory() プラットフォーム決済情報検証ファクトリを返します
 * @method string pickUpPlatformId() platformId項目の内容をピックアップします
 * @method string pickUpId() id項目の内容をピックアップします
 * @method string pickUpDeviceId() デバイスID項目の内容をピックアップします
 * @method string pickUpRatingId() レーティングID項目の内容をピックアップします
 * @method string pickUpReceipt() receipt項目の内容をピックアップします/sigendData項目の内容をピックアップします
 * @method string pickUpSignature() signature項目の内容をピックアップします
 * @method string pickUpType() type項目の内容をピックアップします
 * @method array pickUpPayment() payment項目(元entry)の内容をピックアップします
 * @method array pickUpAccount() Account項目をピックアップします
 * @method array pickUpPlatformPaymentId() platformPaymentId項目をピックアップします
 * @method array pickUpOrderedTime() orderedTime項目をピックアップします
 * @method void setAccessToken($accessToken) アクセストークンをセットします
 * @method string getAccessToken() アクセストークンを返します
 * @method void setPaidWith($paidWith) paidWithをセットします
 * @method string getPaidWith() paidWithを返します
 */
abstract class Logic_Payment_Abstract extends Logic_Abstract
{

    use \misp\logics\payment\traits\Logic_Payment_Trait_ApplicationUserPaymentItemRepository;
    use \misp\logics\payment\traits\Logic_Payment_Trait_ApplicationUserTargetCurrencyPaymentItemRepository;
    use \misp\logics\payment\traits\Logic_Payment_Trait_ApplicationUserPlatformPaymentRelationRepository;
    /**
     * モデルクラスのプレフィックス
     * 
     * @var string
     */
    const MODEL_CLASS_PREFIX = 'Application_Model_';

    /**
     * MISPコレクションクラスのプレフィックス
     * 
     * @var string
     */
    const MISP_COLLECTION_CLASS_PREFIX = 'Misp_Collection_';

    /**
     * APIリクエストパラメータの"entry"項目の中身
     * 
     * 各処理で参照するので、プロパティに保持する
     *
     * @var array
     */
    protected $_buildParams = array();

    /**
     * 購入処理用コレクション
     *
     * @var Misp_Collection_ApplicationUserTargetCurrencyPaymentItem
     */
    protected $_targetCurrencyPaymentItemCollectionForTradeProc = NULL;

    /**
     * 有償ログ用PlatformPayment配列
     *
     * @var array
     */
    protected $_platformPaymentForCreditLog = array();

    /**
     * 有償ログ用PlatformPaymentItem配列
     *
     * @var array
     */
    protected $_platformPaymentItemForCreditLog = array();

    /**
     * アプリケーションID
     * 
     * インスタンス生成時に保持されます。(アプリケーションユーザペイメントが先に検索されている前提)
     *
     * @var string 
     */
    protected $_applicationId = '';

    /**
     * プラットフォーム
     * 
     * インスタンス生成時にペイメントプラットフォームIDが保持されます。
     *
     * @var string
     */
    protected $_platform = '';

    /**
     * アクセストークン
     *
     * @var string
     */
    protected $_accessToken = '';

    /**
     * 有効期限設定(日)
     *
     * @var int
     */
    private $_expiredDay = array();

    /**
     * 有効期限設定(月)
     *
     * @var int
     */
    private $_expiredMonth = [];

    /**
     * paidWith
     * 
     * 有償通貨のみの両替・消費
     * または、無償通貨のみの両替・消費の設定を保持
     * NULLの場合はどちらの通貨も使用可能
     *
     * @var string
     */
    protected $_paidWith = '';

    /**
     * ペイメントステータス更新
     * 
     * ペイメントステータスを「開始」状態に更新します。
     */
    public function paymentStart()
    {
        $this->_updatePaymentStatus(Logic_Payment_Const::PAYMENT_STATUS_START);
    }

    /**
     * ペイメントステータス更新
     * 
     * ペイメントステータスを「確認」状態に更新します。
     */
    public function paymentConfirm()
    {
        $this->_updatePaymentStatus(Logic_Payment_Const::PAYMENT_STATUS_CONFIRM);
    }

    /**
     * ペイメントステータス更新
     * 
     * ペイメントステータスを「注文」状態に更新します。
     */
    public function paymentOrder()
    {
        $this->_updatePaymentStatus(Logic_Payment_Const::PAYMENT_STATUS_ORDER);
    }

    /**
     * ペイメントステータス更新
     * 
     * ペイメントステータスを「完了」状態に更新します。
     */
    public function paymentComplete()
    {
        $this->_updatePaymentStatus(Logic_Payment_Const::PAYMENT_STATUS_COMPLETE);
    }

    /**
     * ペイメントステータス更新
     * 
     * ペイメントステータスを「エラー」状態に更新します。
     */
    public function paymentError()
    {
        $this->_updatePaymentStatus(Logic_Payment_Const::PAYMENT_STATUS_ERROR);
    }

    /**
     * ペイメントステータス更新
     * 
     * ペイメントステータスを「キャンセル」状態に更新します。
     */
    public function paymentCancel()
    {
        $this->_updatePaymentStatus(Logic_Payment_Const::PAYMENT_STATUS_CANCEL);
    }

    /**
     * ペイメントステータス更新
     * 
     * ペイメントステータスを「決済取消」状態に更新します。
     */
    public function paymentVoid()
    {
        $this->_updatePaymentStatus(Logic_Payment_Const::PAYMENT_STATUS_VOID);
    }

    /**
     * ペイメントステータス判定
     * 
     * ペイメントステータスが完了かを判定します。
     * 
     * @param Application_Model_ApplicationUserPayment $m
     * @return boolean
     */
    public function isPaymentStatusComplete(Application_Model_ApplicationUserPayment $m)
    {
        return $m->getPaymentStatus() == Logic_Payment_Const::PAYMENT_STATUS_COMPLETE;
    }

    /**
     * ペイメントステータス判定
     * 
     * ペイメントステータスが完了ではないことを判定します。
     * 
     * @param Application_Model_ApplicationUserPayment $m
     * @return boolean
     */
    public function isNotPaymentStatusComplete(Application_Model_ApplicationUserPayment $m)
    {
        return !$this->isPaymentStatusComplete($m);
    }

    /**
     * ペイメント種別判定
     * 
     * ペイメント種別がcreditかを判定します。
     * 
     * @param Application_Model_ApplicationUserPayment $m
     * @return boolean
     */
    public function isPaymentTypeCredit(Application_Model_ApplicationUserPayment $m)
    {
        return $m->getPaymentType() == Logic_Payment_Const::PAYMENT_TYPE_CREDIT;
    }

    /**
     * DBセクション名返却
     * 
     * DBのセクション名を返します(main)。
     * 
     * @return string
     */
    public function getDbSectionNameMain()
    {
        // application.iniからデータベース情報を取得する
        $config = Zend_Registry::get('misp');
        return $config['db']['main'];
    }

    /**
     * DBセクション名返却
     * 
     * DBのセクション名を返します(sub)。
     * 
     * @return string
     */
    public function getDbSectionNameSub()
    {
        // application.iniからデータベース情報を取得する
        $config = Zend_Registry::get('misp');
        return $config['db']['sub'];
    }

    /**
     * 未定義メソッドコールのハンドリングマジックメソッド
     * 
     * API層から渡されたパラメータに対して、キーを指定して値を取り出すための<br>
     * <code>pickUp*<code>メソッドのコールに応じる目的で実装されています。
     * 
     * @param string $name コールしようとしたメソッドの名前
     * @param array $arguments メソッドに渡そうとしたパラメータ
     * @return mixed コールしようとしたメソッドの仕様に準じる
     */
    public function __call($name, $arguments)
    {
        try {
            return parent::__call($name, $arguments);
            //
        } catch (Exception $exc) {

            $matches = array();

            // _pickUpはじまり
            if (preg_match('/^pickUp(.+)/', $name, $matches)) {

                // buildParamsにあれば返す
                return $this->_pickUpByBuildParams($matches);
            }
        }
    }

    /**
     * モデルの情報をログ出力用に整形し、フォーマット済みの文字列として返します
     * 
     * [TABLE:テーブル名(モデルクラス名の末尾部分)][プロパティ名1:値1][プロパティ名2:値2]...<br>
     * といったフォーマット済みの文字列を生成します。
     * 
     * <pre>
     * 例)
     * [TABLE:ApplicationUserPayment][ApplicationUserPaymentId:1][ApplicationUserId:user01](以下略)
     * <pre>
     * 
     * @param object $m モデルオブジェクト
     * @param boolean $outputTableName テーブル名のフォーマットを出力するかどうか(Default:TRUE)
     * @return string ログフォーマット
     */
    protected function _generateModelLogFormat($m, $outputTableName = TRUE)
    {
        $arr   = $m->toArray();
        $wkArr = array();
        if ($outputTableName) {
            $wkArr[] = sprintf('[TABLE:%s]', $this->_pickUpTableNameByModel($m));
        }

        // [プロパティ名1:値1]... を構築
        foreach ($arr as $propertyName => $value) {
            $wkArr[] = sprintf('[%s:%s]', $propertyName, $value);
        }
        return implode('', $wkArr);
    }

    /**
     * テーブル名抽出
     * 
     * モデルオブジェクトからテーブル名をピックアップします。<br>
     * モデルオブジェクトのクラス名を取得し、そのクラス名の末尾部分(=テーブル名)をピックアップします。<br>
     * 末尾部分を決定する材料として、モデルクラスのプレフィックス文字列を利用しています。
     * 
     * @param object $m モデルオブジェクト
     * @return string テーブル名
     */
    protected function _pickUpTableNameByModel($m)
    {
        return substr(get_class($m), strlen(Logic_Payment_Abstract::MODEL_CLASS_PREFIX));
    }

    /**
     * 構築パラメータ抽出
     * 
     * キーを指定してbuildParamsから値を取り出します
     * 
     * @param array $matches preg_matchの結果
     * @return string
     */
    private function _pickUpByBuildParams($matches)
    {
        $key = lcfirst($matches[1]);
        $ret = null;
        if (isset($this->_buildParams[$key])) {
            $ret = $this->_buildParams[$key];
        }
        return $ret;
    }

    /**
     * ペイメントステータス更新
     * 
     * 引数のステータスで更新します。<br>
     * 更新が行われなかった場合は内部ログに情報を出力し、処理を終了します。<br>
     * 内部メソッドなので更新ステータス値の妥当性はチェックしません。
     * 
     * @param int $status
     * @throws Common_Exception_NotModified 更新対象が存在しなかった場合にThrowされます
     * @throws Exception 想定外の例外
     */
    private function _updatePaymentStatus($status)
    {
        try {
            // Mapper取得
            $mapper                 = $this->getApplicationUserPaymentMapper($this->getDbSectionNameMain());
            // アプリケーションユーザペイメントモデル
            $applicationUserPayment = $this->getApplicationUserPayment();
            $applicationUserPayment->setUpdatedDate($this->getNowDatetime());
            $applicationUserPayment->setPaymentStatus($status);

            if (!$mapper->update($applicationUserPayment, $applicationUserPayment->getApplicationUserPaymentId())) {
                // 変更がなかった場合の例外
                throw new Common_Exception_NotModified(Logic_Payment_Const::LOG_MSG_UPDATE_FAIL . $this->_generateModelLogFormat($applicationUserPayment));
            }
            //
        } catch (Common_Exception_NotModified $exc) {
            //
            // 変更がなかった場合の例外ハンドリング
            // 
            throw $exc;
            //
        } catch (Exception $exc) {
            //
            // 想定外の例外
            //
            throw $exc;
        }
    }

    /**
     * 残高不足確認
     * 
     * アプリケーションユーザ通貨テーブルを検索し、
     * その処理ユーザ(アプリケーションユーザ)の残高が引数から取得した消費額を満たしているか確認します。
     * 満たしている場合TRUEを返します。
     * 
     * @param array $currencyPayment 通貨IDと通貨額の連想配列
     * @return boolean
     */
    protected function _isCurrencyEnough($currencyPayment)
    {
        $dbSectionName        = $this->getDbSectionNameSub();
        $paidWith             = $this->pickUpPaidWith();
        $currencySharingGroup = new Logic_Payment_CurrencySharingGroup();

        $applicationUser = $this->getApplicationUser();
        if (Common_Util_String::isEmpty($applicationUser)) {
            $applicationUser = $this->getApplicationUserPayment();
        }

        $applicationId      = $applicationUser->getApplicationId();
        $applicationUserId  = $applicationUser->getApplicationUserId();
        $applicationWorldId = $applicationUser->getApplicationWorldId();
        $platformId         = $this->_buildParams['platformId'];
        $deviceId           = $this->_buildParams['deviceId'] ?? '';
        $ratingId           = $this->_buildParams['ratingId'] ?? '';

        $applicationUserCurrencyMapper = $this->getApplicationUserCurrencyMapper($dbSectionName);

        $where                       = array();
        $where['applicationUserId']  = array($applicationUserId);
        $where['applicationId']      = array($applicationId);
        $where['applicationWorldId'] = array($applicationWorldId);

        $config = Zend_Registry::get('apiPayment_config');

        foreach ($currencyPayment as $key => $value) {

            // 検索条件
            $where['applicationCurrencyId'] = array($key);
            // paidWithが null もしくは credit の場合、有償通貨の残高不足確認を行う
            if (is_null($paidWith) || $paidWith == Logic_Payment_Const::PAID_WITH_CREDIT) {
                // 有償通貨で足りるか確認
                $where['unitPrice not'] = array(Logic_Payment_Const::PRICE_BONUS);

                // 有償の通貨共有グループ取得準備
                $currencySharingGroup->init($applicationId, Logic_Payment_CurrencySharingGroup::MODE_CREDIT);

                $results = $applicationUserCurrencyMapper->fetchAllBySharingGroup($where, $currencySharingGroup->get($platformId, $deviceId, $ratingId));
                if ($results) {
                    // 消費額と残高額を比較
                    if ($value <= $this->_totalAmount($results)) {
                        // 足りたので次のアプリケーション通貨IDの残高チェックを行う
                        continue;
                    }
                    $value -= $this->_totalAmount($results);
                }
            }
            // paidWithが null もしくは bonus の場合、無償通貨の残高不足確認を行う
            if (is_null($paidWith) || $paidWith == Logic_Payment_Const::PAID_WITH_BONUS) {

                // 無償通貨で足りるか確認
                unset($where['unitPrice not']);
                $where['unitPrice'] = array(Logic_Payment_Const::PRICE_BONUS);

                // 通貨の消費順を取得
                $currencyPaymentSequence = $this->getConfigCurrencyPaymentSequence($applicationId, $platformId, $deviceId, $ratingId);
                $resultCommonBonus       = [];
                $resultPfBonus           = [];

                foreach ($currencyPaymentSequence as $sequence) {

                    if ($sequence == Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_BONUS) {
                        // プラットフォーム共有無償通貨
                        $where['paymentPlatformId'] = array('');
                        $resultCommonBonus          = $applicationUserCurrencyMapper->fetchAll($where);
                    }
                    if ($sequence == Logic_Payment_Const::CURRENCY_PAYMENT_SEQUENCE_PF_BONUS) {
                        unset($where['paymentPlatformId']);
                        // プラットフォーム固有無償通貨                
                        // 無償の通貨共有グループ取得準備
                        $currencySharingGroup->init($applicationId, Logic_Payment_CurrencySharingGroup::MODE_BONUS);
                        $resultPfBonus = $applicationUserCurrencyMapper->fetchAllBySharingGroup($where, $currencySharingGroup->get($platformId, $deviceId, $ratingId));
                    }
                }

                // ボーナスの配列をマージ
                $results = array_merge($resultPfBonus, $resultCommonBonus);
                if ($results) {
                    // 消費額と残高額を比較
                    if ($value <= $this->_totalAmount($results)) {
                        // 複数の通貨種類を使用する場合のために、unitPrice=0の条件を削除しておく
                        unset($where['unitPrice']);
                        unset($where['paymentPlatformId']);
                        // 足りたので次のアプリケーション通貨IDの残高チェックを行う
                        continue;
                    }
                }
            }
            return FALSE;
        }

        return TRUE;
    }

    /**
     * 通貨額合算
     * 
     * ApplicationUserCurrencyテーブルから取得できたレコードの通貨額の合計を返します。
     * 
     * @param Application_Model_ApplicationUserCurrency[] $results
     * @return int
     */
    private function _totalAmount($results)
    {
        $returnAmount = 0;
        foreach ($results as $m) {
            $returnAmount += $m->getCurrencyAmount();
        }
        return $returnAmount;
    }

    /**
     * アプリケーションユーザペイメントアイテム/アプリケーションユーザターゲット通貨ペイメントアイテム登録
     * 
     * @throws Common_Exception_Exception
     */
    protected function _saveTargetCurrency()
    {
        // 現在時刻
        $nowDatetime = $this->getNowDatetime();

        foreach ($this->_targetCurrencyPaymentItemCollectionForTradeProc as $targetCurrencyPaymentItem) {
            $this->_saveTargetCurrencyPaymentItem($targetCurrencyPaymentItem, $nowDatetime);
        }
    }

    /**
     * アプリケーションユーザターゲット通貨ペイメントアイテム登録
     * 
     * @param Application_Model_ApplicationUserTargetCurrencyPaymentItem $targetCurrencyPaymentItem
     * @param string $nowDatetime
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    protected function _saveTargetCurrencyPaymentItem(Application_Model_ApplicationUserTargetCurrencyPaymentItem $targetCurrencyPaymentItem, $nowDatetime)
    {
        // Mapper取得
        $targetCurrencyPaymentItemMapper = $this->getApplicationUserTargetCurrencyPaymentItemMapper($this->getDbSectionNameMain());

        // 1.アプリケーションユーザペイメントアイテム登録
        $paymentItem   = $this->_createApplicationUserPaymentItem();
        $paymentItemId = $paymentItem->getApplicationUserPaymentItemId();

        // アプリケーションユーザターゲット通貨ペイメントアイテム登録
        $targetCurrencyPaymentItem->setApplicationUserPaymentItemId($paymentItemId);
        $targetCurrencyPaymentItem->setCreatedDate($nowDatetime);
        $targetCurrencyPaymentItem->setUpdatedDate($nowDatetime);
        $this->_createApplicationUserTargetCurrencyPaymentItem($targetCurrencyPaymentItem);

        // 無償通貨用コレクションがセットされていたら無償用処理を行う
        $targetCurrencyPaymentItemForBonusCollection = $targetCurrencyPaymentItem->getTargetCurrencyPaymentItemCollectionForBonus();
        if ($targetCurrencyPaymentItemForBonusCollection) {

            foreach ($targetCurrencyPaymentItemForBonusCollection as $targetCurrencyPaymentItemForBonus) {

                $targetCurrencyPaymentItemForBonus->setApplicationUserPaymentItemId($paymentItemId);
                $targetCurrencyPaymentItemForBonus->setPrice(0);
                $targetCurrencyPaymentItemForBonus->setCreatedDate($nowDatetime);
                $targetCurrencyPaymentItemForBonus->setUpdatedDate($nowDatetime);

                if (!$targetCurrencyPaymentItemMapper->insert($targetCurrencyPaymentItemForBonus)) {
                    throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($targetCurrencyPaymentItemForBonus));
                }
            }
        }
    }

    /**
     * アプリケーションユーザペイメントアイテム登録
     * 
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     * @return Application_Model_ApplicationUserTargetCurrencyPaymentItem
     */
    protected function _createApplicationUserPaymentItem()
    {
        // Mapper取得
        $mapper = $this->getApplicationUserPaymentItemMapper($this->getDbSectionNameMain());

        // 現在時刻
        $nowDatetime = $this->getNowDatetime();

        // 新規(Insert)
        $pi            = new Application_Model_ApplicationUserPaymentItem();
        $pi->setApplicationUserPaymentId($this->pickUpId());
        $pi->setCreatedDate($nowDatetime);
        $pi->setUpdatedDate($nowDatetime);
        $paymentItemId = $mapper->insert($pi);
        if (!$paymentItemId) {
            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($pi));
        }

        $pi->setApplicationUserPaymentItemId($paymentItemId);

        return $pi;
    }

    /**
     * アプリケーションユーザターゲット通貨ペイメントアイテム登録
     * 
     * @param Application_Model_ApplicationUserTargetCurrencyPaymentItem $m
     * @throws Common_Exception_Exception
     */
    protected function _createApplicationUserTargetCurrencyPaymentItem(Application_Model_ApplicationUserTargetCurrencyPaymentItem $m)
    {
        // Mapper取得F
        $mapper = $this->getApplicationUserTargetCurrencyPaymentItemMapper($this->getDbSectionNameMain());

        if (!$mapper->insert($m)) {
            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
        }
    }

    /**
     * アプリケーションユーザ通貨購入ログ登録
     * 
     * @param Application_Model_PlatformPaymentItem $platformPaymentItem
     * @param Application_Model_PlatformPayment $platformPayment
     * @param Application_Model_ApplicationUserTargetCurrencyPaymentItem $targetCurrencyPaymentItem
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    protected function _saveVerifiedApplicationUserCurrencyCreditLog(Application_Model_PlatformPaymentItem $platformPaymentItem, Application_Model_PlatformPayment $platformPayment, Application_Model_ApplicationUserTargetCurrencyPaymentItem $targetCurrencyPaymentItem)
    {
        $applicationUserPayment           = $this->getApplicationUserPayment();
        $lastInserIdPlatformPaymentItemId = $platformPaymentItem->getPlatformPaymentItemId();

        // アプリケーションユーザ通貨購入ログモデル構築
        $logModel = new Application_Model_ApplicationUserCurrencyCreditLog();

        // ペイメントプラットフォームユーザID対応
        $account = $this->pickUpAccount();
        if (isset($account['userId'])) {
            $logModel->setPaymentPlatformUserId($account['userId']);
        } else if ($applicationUserPayment->getPaymentPlatformUserId()) {
            $logModel->setPaymentPlatformUserId($applicationUserPayment->getPaymentPlatformUserId());
        }

        $logModel->setPlatformPaymentItemId($lastInserIdPlatformPaymentItemId);
        $logModel->setPlatformPaymentId($platformPayment->getPlatformPaymentId());
        $logModel->setPaymentPlatformId($platformPayment->getPaymentPlatformId());
        $logModel->setPaymentDeviceId($platformPayment->getPaymentDeviceId() ?? '');
        $logModel->setPaymentRatingId($platformPayment->getPaymentRatingId() ?? '');
        $logModel->setPlatformProductId($platformPaymentItem->getPlatformProductId());
        $logModel->setApplicationUserId($applicationUserPayment->getApplicationUserId());
        $logModel->setApplicationId($applicationUserPayment->getApplicationId());
        $logModel->setApplicationWorldId($applicationUserPayment->getApplicationWorldId());
        $logModel->setApplicationUserPaymentId($applicationUserPayment->getApplicationUserPaymentId());
        $logModel->setApplicationUserPaymentItemId($targetCurrencyPaymentItem->getApplicationUserPaymentItemId());
        $logModel->setApplicationCurrencyId($targetCurrencyPaymentItem->getApplicationCurrencyId());
        $logModel->setCurrencyAmount($targetCurrencyPaymentItem->getCurrencyAmount());
        $logModel->setPrice($platformPaymentItem->getPrice());
        $logModel->setProductQuantity($platformPaymentItem->getProductQuantity());
        $logModel->setPlatformPaymentStatus($platformPayment->getPlatformPaymentStatus());
        $logModel->setReceipt($platformPayment->getReceipt());
        $logModel->setSignature($platformPayment->getSignature());
        $logModel->setExecutedDate($platformPaymentItem->getExecutedDate());
        $logModel->setCreatedDate($this->_nowDatetime);
        $logModel->setUpdatedDate($this->_nowDatetime);

        // Mapper
        $mapper = $this->getApplicationUserCurrencyCreditLogMapper($this->getDbSectionNameMain());

        // Insert
        if (!$mapper->insert($logModel)) {
            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($logModel));
        }

        // 購入ログをテキストログ配列にプッシュする
        Misp_TextLog::getInstance()->push($logModel);
    }

    /**
     * 外部プラットフォーム連携インスタンス取得
     * 
     * 外部プラットフォーム連携インスタンスを返します。
     *
     * @return Common_External_Platform_Abstract
     */
    protected function _getApi()
    {
        $applicationUserPayment = $this->getApplicationUserPayment();

        $externalConfigKey = Logic_Abstract::EXTERNAL_CONFIG_KEY_PREFIX . $applicationUserPayment->getApplicationId();
        $config            = Zend_Registry::get('external_config');
        $externalConfig    = $config['platform'][$externalConfigKey];

        // APIインスタンスを取得
        return Common_External_Platform::getInstance($externalConfig[$this->_buildExternalConfigKey()]);
    }

    /**
     * 外部プラットフォーム設定抽出キー構築
     * 
     * 外部プラットフォーム連携設定群から、特定のプラットフォーム用の設定群を抜き出すための設定キーを構築します。<br>
     * <br>
     * 「特定の」とは、ファクトリ経由で生成されたレシート/署名検証インスタンスを指します。
     *
     * @return string 特定のプラットフォーム用の設定群を抜き出すための設定キー
     */
    private function _buildExternalConfigKey()
    {
        return Logic_Payment_Verify_Payment_Factory::getPlatformConfigName($this->_platform, $this->pickUpDeviceId(), $this->pickUpRatingId());
    }

    /**
     * MZCLの生レスポンスを取得する
     * 
     * @param Common_External_Platform_Abstract
     * @return string 生レスポンス
     */
    protected function _getMzclResponseRawBody(Common_External_Platform_Abstract $api)
    {
        return $api->getLastHttpClient()->getLastResponse()->getRawBody();
    }

    /**
     * application.ini から有効期限設定を読み取り、privateに保持します。
     * 
     * 設定値を参照する場合は、 getExpiredDay() をコールしてください。
     * 
     * @param $applicationId アプリケーションID
     * @param $platformId プラットフォームID
     */
    public function loadExpiredDay($applicationId, $platformId)
    {
        $config   = Zend_Registry::get('apiPayment_config');
        $firstKey = Logic_Abstract::EXTERNAL_CONFIG_KEY_PREFIX . $applicationId;

        if (isset($config[$firstKey][$platformId]['creditExpiredDay'])) {
            foreach ($config[$firstKey][$platformId]['creditExpiredDay'] as $applicationCurrencyId => $value) {
                $this->_expiredDay[$applicationCurrencyId] = $value;
            }
        }

        if (isset($config[$firstKey][$platformId]['creditExpiredMonth'])) {
            foreach ($config[$firstKey][$platformId]['creditExpiredMonth'] as $applicationCurrencyId => $value) {
                $this->_expiredMonth[$applicationCurrencyId] = $value;
            }
        }
    }

    /**
     * 有効期限日を返します。
     * 
     * loadExpiredDay() がコールされていることが前提です。<br>
     * loadExpiredDay() が未コールの場合は初期値 0 が返ります。<br>
     * hasCreditExpiredDay() でTRUEが返ってきていることが前提です。
     * 
     * @param string $applicationCurrencyId アプリケーション通貨ID
     * @return int 有効期限日
     */
    public function getExpiredDay($applicationCurrencyId)
    {
        return $this->_expiredDay[$applicationCurrencyId];
    }

    /**
     * 引数の日時に有効期限日を加算した結果を返します。
     * 
     * loadExpiredDay() がコールされていることが前提です。<br>
     * loadExpiredDay() が未コールの場合は初期値である 0 が加算された結果が返ります。<br>
     * hasCreditExpiredDay() でTRUEが返ってきていることが前提です。
     * 
     * @param string $datetime Y-m-d H:i:s 形式
     * @param string $applicationCurrencyId アプリケーション通貨ID
     * @return string 引数の日時に有効期限日を加算した結果
     */
    public function calcExpiredDate($datetime, $applicationCurrencyId)
    {
        $d = new Zend_Date($datetime);
        $d->addDay($this->_expiredDay[$applicationCurrencyId]);

        return $d->toString('y-MM-dd HH:mm:ss');
    }

    /**
     * 引数の日時に有効期限月を加算した結果を返します。
     * 
     * 第三引数は追加で特殊加工する場合のマーカーです。<br>
     * デフォルトは設定値の「月」を加算したうえで更に「翌月1日の0時」にフォーマットします。
     * 例：設定値が 2 (有効期限を2ヶ月とする)で、第一引数に「2018-03-06 12:34:56」を渡した場合
     * 　　→2018-6-01 00:00:00 が返却される
     * 
     * loadExpiredDay() がコールされていることが前提です。<br>
     * loadExpiredDay() が未コールの場合は初期値である 0 が加算された結果が返ります。<br>
     * hasCreditExpiredDay() でTRUEが返ってきていることが前提です。
     * 
     * @param string $datetime Y-m-d H:i:s 形式
     * @param string $applicationCurrencyId アプリケーション通貨ID
     * @param string $additionalFormat アプリケーション通貨ID
     * @return string 引数の日時に有効期限月および追加フォーマットで加算・フォーマットした結果
     */
    public function calcExpiredMonth($datetime, $applicationCurrencyId, $additionalFormat = Logic_Payment_Const::EXPIRED_MONTH_ADDITIONAL_FORMAT_NEXTMONTH)
    {
        $d               = new Zend_Date($datetime);
        $expiredDatetime = '';

        switch ($additionalFormat) {

            // 翌月1日0時 フォーマット
            case Logic_Payment_Const::EXPIRED_MONTH_ADDITIONAL_FORMAT_NEXTMONTH:

                $d->addMonth($this->_expiredMonth[$applicationCurrencyId]);
                // 翌月にするため、1プラス
                $d->addMonth(1);

                $expiredDatetime = $d->toString('y-MM-01 00:00:00');
                break;

            default:
                break;
        }

        return $expiredDatetime;
    }

    /**
     * 有効期限の優先順位にもとづいて設定値を使用し、ベース日時にソレを加算して Datetime フォーマットで返します。
     * 
     * @param string $baseDatetime Y-m-d H:i:s 形式
     * @param string $applicationCurrencyId アプリケーション通貨ID
     * @return string 有効期限の優先順位にもとづいて計算・フォーマットした期限日時
     */
    public function calcExpiredDatetime($baseDatetime, $applicationCurrencyId)
    {
        // 1. 有効期限「日」が設定されている
        if ($this->hasCreditExpiredDay($applicationCurrencyId)) {
            return $this->calcExpiredDate($baseDatetime, $applicationCurrencyId);
        }

        // 2. 有効期限「月」が設定されている
        if ($this->hasCreditExpiredMonth($applicationCurrencyId)) {
            return $this->calcExpiredMonth($baseDatetime, $applicationCurrencyId);
        }

        // 3. とくにない(未設定時のデフォルト挙動)
        return NULL;
    }

    /**
     * 有償仮想通貨の有効期限が設定されているかどうかを判定します。
     * 
     * @param string $applicationCurrencyId アプリケーション通貨ID
     * @return boolean
     */
    public function hasCreditExpiredDay($applicationCurrencyId)
    {
        $returnValue = FALSE;

        if (array_key_exists($applicationCurrencyId, $this->_expiredDay) && $this->_expiredDay[$applicationCurrencyId] > 0) {
            $returnValue = TRUE;
        }

        return $returnValue;
    }

    /**
     * 有償仮想通貨の有効期限月が設定されているかどうかを判定します。
     * (設定が存在しなかったり、設定値が「0」だったらFALSEを返すイメージ。TRUEは有効値が設定されている場合に返す)
     * 
     * @param string $applicationCurrencyId アプリケーション通貨ID
     * @return boolean
     */
    public function hasCreditExpiredMonth($applicationCurrencyId)
    {
        $returnValue = FALSE;

        if (array_key_exists($applicationCurrencyId, $this->_expiredMonth) && $this->_expiredMonth[$applicationCurrencyId] > 0) {
            $returnValue = TRUE;
        }

        return $returnValue;
    }

    /**
     * 高額から小額の両替かどうかを判定します。
     * 
     * @param int $from from通貨額
     * @param int $to to通貨額
     * @return boolean TRUE:高額から小額両替
     *                  FALSE:高額から小額両替ではない
     */
    public function isHighToLow($from, $to)
    {
        return ($from > $to) ? TRUE : FALSE;
    }

    /**
     * 処理可能な両替レートかどうかを判定します。
     * 
     * @param int $from from通貨額
     * @param int $to to通貨額
     * @return boolean TRUE:処理可能
     *                  FALSE:処理不可能
     */
    public function canExchangeRate($from, $to)
    {
        if ($this->isHighToLow($from, $to)) {
            return ($from % $to) == 0 ? TRUE : FALSE;
        }

        return ($to % $from) == 0 ? TRUE : FALSE;
    }

    /**
     * 通貨使用順序の設定取得。
     * 
     * @return array 設定の配列
     */
    public function getConfigCurrencyPaymentSequence($applicationId, $platformId, $deviceId = '', $ratingId = '')
    {
        // 通貨使用順序のデフォルトを設定
        $currencyPaymentSequence = Logic_Payment_Const::DEFAULT_CURRENCY_PAYMENT_SEQUENCE;

        $platformConfigName = Logic_Payment_FactoryAbstract::getPlatformConfigName($platformId, $deviceId, $ratingId);
        $config             = Zend_Registry::get('apiPayment_config');

        // 設定に独自の通貨使用順序がある場合は、そちらを使用する
        if (isset($config['app' . $applicationId][$platformConfigName]['currencyPaymentSequence'])) {
            $currencyPaymentSequence = $config['app' . $applicationId][$platformConfigName]['currencyPaymentSequence'];
        }

        // カンマ区切りで分割した配列使用順の配列
        $currencyPaymentSequence = explode(',', str_replace(' ', '', $currencyPaymentSequence));

        return $currencyPaymentSequence;
    }

    /**
     * プラットフォーム側の有償通貨IDの設定取得。
     * 
     * @return string 有償通貨IDの設定
     */
    public function getConfigPlatformCurrencyId($applicationId, $platformId, $deviceId = '', $ratingId = '')
    {
        $platformCurrencyId = NULL;

        $platformConfigName = Logic_Payment_FactoryAbstract::getPlatformConfigName($platformId, $deviceId, $ratingId);
        $config             = Zend_Registry::get('apiPayment_config');

        // 設定に独自の通貨使用順序がある場合は、そちらを使用する
        if (isset($config['app' . $applicationId][$platformConfigName]['platformCurrencyId'])) {
            $platformCurrencyId = $config['app' . $applicationId][$platformConfigName]['platformCurrencyId'];
        } else {
            throw new Common_Exception_IllegalConfig('platformCurrencyIdの設定が存在しません。');
        }

        return $platformCurrencyId;
    }

    /**
     * 通貨使用順序の設定取得。
     * 
     * @return array 設定の配列
     */
    public function getConfigCurrencyPaymentSequenceWithPaidWith($applicationId, $platformId, $deviceId = '', $ratingId = '')
    {
        $currencyPaymentSequence = $this->getConfigCurrencyPaymentSequence($applicationId, $platformId, $deviceId, $ratingId);

        // paidWithがある場合、使用順から不要なキーを除外
        $paidWith = $this->getPaidWith();
        if (Common_Util_String::isNotEmpty($paidWith)) {
            $currencyPaymentSequence = array_diff($currencyPaymentSequence, Logic_Payment_Const::PAID_WITH_FOR_OMMIT_PAYMENT_SEQUENCE[$paidWith]);
        }

        return $currencyPaymentSequence;
    }

    /**
     * 商品マスタ(都合2テーブル)を検索し、その情報が合っているかを検証する汎用メソッドです。
     * 
     * (価格も検証します)
     * 不一致の場合はその旨 Internal.log に出力し、FALSE を返却します。
     * 
     * @param Application_Model_PlatformProductItem $platformProductItem
     * @param int $price
     * @return boolean TRUE 正常
     *                  FALSE 異常
     */
    public function verifyProduct(Application_Model_PlatformProductItem $platformProductItem, $price)
    {
        // DB識別子を取得
        $dbSub = $this->getDbSectionNameSub();

        // プラットフォーム商品テーブルの存在チェック
        $where                        = array();
        $where['platformProductId']   = array($platformProductItem->getPlatformProductId());
        $where['paymentPlatformId']   = array($platformProductItem->getPaymentPlatformId());
        $where['paymentDeviceId']     = array($platformProductItem->getPaymentDeviceId());
        $where['paymentRatingId']     = array($platformProductItem->getPaymentRatingId());
        $where['applicationId']       = array($platformProductItem->getApplicationId());
        $where['deletedDate IS NULL'] = NULL;
        $platformProductMapper        = $this->getPlatformProductMapper($dbSub);
        $result                       = $platformProductMapper->fetchAll($where);
        if (!$result) {
            $this->_logInfo(__CLASS__, __METHOD__, __LINE__, sprintf('%s[platformProductItemId:%s|paymentPlatformId:%s|paymentDeviceId:%s|paymentRatingId:%s|applicationId:%s]', Logic_Payment_Const::LOG_MSG_ILLEGAL_PRODUCT_ITEM, $where['platformProductItemId'], $where['paymentPlatformId'], $where['paymentDeviceId'], $where['paymentRatingId'], $where['applicationId']));
            return FALSE;
        }

        // 決済アイテムの検証
        $where                        = array();
        $where['platformProductId']   = array($platformProductItem->getPlatformProductId());
        $where['paymentPlatformId']   = array($platformProductItem->getPaymentPlatformId());
        $where['paymentDeviceId']     = array($platformProductItem->getPaymentDeviceId());
        $where['paymentRatingId']     = array($platformProductItem->getPaymentRatingId());
        $where['applicationId']       = array($platformProductItem->getApplicationId());
        $where['unitPrice not']       = array(Logic_Payment_Const::PRICE_BONUS);
        $where['deletedDate IS NULL'] = NULL;
        $platformProductItemMapper    = $this->getPlatformProductItemMapper($dbSub);
        $result                       = $platformProductItemMapper->fetchAll($where);
        if (!$result) {
            $this->_logInfo(__CLASS__, __METHOD__, __LINE__, sprintf('%s[platformProductItemId:%s|paymentPlatformId:%s|paymentDeviceId:%s|paymentRatingId:%s|applicationId:%s]', Logic_Payment_Const::LOG_MSG_ILLEGAL_PRODUCT_ITEM, $where['platformProductItemId'], $where['paymentPlatformId'], $where['paymentDeviceId'], $where['paymentRatingId'], $where['applicationId']));
            return FALSE;
        }

        // 購入価格を初期化
        $creditPrice = 0;
        foreach ($result as $resultPlatformProductItem) {
            // 購入価格の計算
            $creditPrice += $resultPlatformProductItem->getUnitPrice() * $resultPlatformProductItem->getCurrencyAmount();
        }

        // 価格の検証
        if ($creditPrice != $price) {

            // 単一アイテムの場合、単価で比較する
            // ボリュームディスカウントなどの対応
            if (count($result) != 1) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, sprintf('%s[creditPrice:%s|price:%s]', Logic_Payment_Const::LOG_MSG_ILLEGAL_PRODUCT_ITEM, $creditPrice, $price));
                return FALSE;
            }

            $productUnitPrice = Logic_Payment_Calculator::calcRoundUnitPrice($price, $result[0]->getCurrencyAmount());
            if ($result[0]->getUnitPrice() != $productUnitPrice) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, sprintf('%s[price:%s|amount:%s|unitPrice:%s|productUnitPrice:%s]', Logic_Payment_Const::LOG_MSG_ILLEGAL_PRODUCT_ITEM, $price, $result[0]->getCurrencyAmount(), $result[0]->getUnitPrice(), $productUnitPrice));
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * 引数(paidWith)が許可されていない値かどうかをチェックします。
     * 
     * 「許可されていなければ例外 Throw」という異常系を先にチェックするコーディングスタイルであることから、
     * if文の可読性のために否定形のメソッド設計となっています。<br>
     * (if文条件に否定(!)演算子を記述すると、直感的でなくなるため。perl の unless 文などと同様の理由です。)<br>
     * なお、引数の paidWith は大文字小文字を区別されます。<br>
     * 
     * @param  string  $paidWith 許可されていない値かどうかをチェックしたい paidWith 値
     * @return boolean TRUE:許可されていない値
     *                  FALSE:許可されている値
     */
    public static function isNotAllowedPaidWith($paidWith)
    {
        // in_array は「探したい値が見つかれば TRUE。それ以外では FALSE を返す」ので、
        // ここでは否定して返却する
        return !in_array($paidWith, Logic_Payment_Const::PAID_WITH_ALLOWS);
    }

    /**
     * ロジックにセットされているApplicationUserPaymentのモデルの情報から
     * ApplicationUserPaymentテーブルを検索し、レコードが存在する場合モデルを返し
     * それ以外はNULLを返す
     * 
     * 検索時に必要なパラメータがApplicationUserPaymentのモデルにセットされていない場合は例外を返す
     * 
     * @param  string $dbSectionName  使用したいDBセクション名、NULLの場合サブを使用
     * @return Application_Model_ApplicationUserPayment
     */
    public function fetchApplicationUserPayment($dbSectionName = NULL)
    {
        // $dbSectionNameが指定されていない場合は、サブを使用
        if (!$dbSectionName) {
            $dbSectionName = $this->getDbSectionNameSub();
        }

        $applicationUserPaymentModel = $this->getApplicationUserPayment();
        $applicationId               = $applicationUserPaymentModel->getApplicationId();
        $applicationWorldId          = $applicationUserPaymentModel->getApplicationWorldId();
        $applicationUserId           = $applicationUserPaymentModel->getApplicationUserId();
        $paymentPlatformId           = $applicationUserPaymentModel->getPaymentPlatformId();

        $this->_isValidateValue($applicationId);
        $this->_isValidateValue($applicationUserId);
        $this->_isValidateValue($paymentPlatformId);

        // MISP の決済処理が開始されているか確認する
        $applicationUserPaymentMapper = $this->getApplicationUserPaymentMapper($dbSectionName);
        $where                        = [];
        $where['applicationId']       = [$applicationId];
        $where['applicationWorldId']  = [$applicationWorldId];
        $where['applicationUserId']   = [$applicationUserId];
        $where['paymentPlatformId']   = [$paymentPlatformId];

        $result = $applicationUserPaymentMapper->fetchAll($where);
        if ($result) {
            return $result[0];
        }
        return NULL;
    }

    /**
     * プラットフォームペイメントIDをアプリケーションユーザペイメントモデルの情報をもとに検索します。
     * 
     * @param Application_Model_ApplicationUserPayment $applicationUserPayment
     * @return string プラットフォームペイメントID
     */
    public function getPlatformPaymentIdBy($applicationUserPayment)
    {
        $paymentPlatformId = $applicationUserPayment->getPaymentPlatformId();
        $paymentType       = $applicationUserPayment->getPaymentType();
        $paymentStatus     = $applicationUserPayment->getPaymentStatus();

        return NULL;
    }

    /**
     * コントローラレスポンス追加項目の個別処理
     * 
     * @return array
     */
    public function additionalParams()
    {
        $additionalParams = [];

        return $additionalParams;
    }

    /**
     * アプリケーションユーザターゲット通貨ペイメントアイテムの価格合計を計算します。
     * 
     * @param array $applicationUserTargetCurrencyPaymentItem Application_Model_ApplicationUserTargetCurrencyPaymentItem の配列
     * @return int 価格合計
     */
    public function calcAmountBy($applicationUserTargetCurrencyPaymentItems)
    {
        $totalAmount = 0;

        foreach ($applicationUserTargetCurrencyPaymentItems as $applicationUserTargetCurrencyPaymentItem) {
            $totalAmount += $applicationUserTargetCurrencyPaymentItem->getPrice();
        }

        return $totalAmount;
    }

    /**
     * 残高更新コレクション構築処理
     * 
     * @param type $applicationUserPaymentItemId
     * @return Misp_Collection_ApplicationUserTargetCurrencyPaymentItem 残高更新用コレクション
     * @throws Common_Exception_NotFound ターゲット通貨ペイメントアイテム(有償)が条件でヒットしなかった場合にThrowされます
     */
    public function buildTargetCurrencyItemCollectionForBalanceUpdate($applicationUserPaymentItemId)
    {
        // 返却用コレクション
        $resultCollection = new Misp_Collection_ApplicationUserTargetCurrencyPaymentItem();

        // ターゲット通貨ペイメントアイテム検索(有償)
        $applicationUserTargetCurrencyPaymentItemMapper  = $this->getApplicationUserTargetCurrencyPaymentItemMapper($this->getDbSectionNameSub());
        $fetchedApplicationUserTargetCurrencyPaymentItem = $applicationUserTargetCurrencyPaymentItemMapper->fetchAll([
            'applicationUserPaymentItemId' => [$applicationUserPaymentItemId],
            'price not '                   => [0]
        ]);
        if (!$fetchedApplicationUserTargetCurrencyPaymentItem) {
            throw new Common_Exception_NotFound(sprintf('残高更新に使用する情報が取得できませんでした。%s:applicationUserPaymentItemId=%s', get_class($applicationUserTargetCurrencyPaymentItemMapper, $applicationUserPaymentItemId)));
        }

        // ターゲット通貨ペイメントアイテム検索(ボーナス)
        $fetchedBonusApplicationUserTargetCurrencyPaymentItem = $applicationUserTargetCurrencyPaymentItemMapper->fetchAll([
            'applicationUserPaymentItemId' => [$applicationUserPaymentItemId],
            'price'                        => [0]
        ]);
        if ($fetchedBonusApplicationUserTargetCurrencyPaymentItem) {

            // ボーナス用コレクション
            $resultBonusCollection = new Misp_Collection_ApplicationUserTargetCurrencyPaymentItem();
            foreach ($fetchedBonusApplicationUserTargetCurrencyPaymentItem as $bonusApplicationUserTargetCurrencyPaymentItem) {
                $resultBonusCollection->attach($bonusApplicationUserTargetCurrencyPaymentItem);
            }
            $resultBonusCollection->rewind();
            $fetchedApplicationUserTargetCurrencyPaymentItem[0]->setTargetCurrencyPaymentItemCollectionForBonus($resultBonusCollection);
        }

        foreach ($fetchedApplicationUserTargetCurrencyPaymentItem as $applicationUserTargetCurrencyPaymentItem) {
            $resultCollection->attach($applicationUserTargetCurrencyPaymentItem);
        }
        $resultCollection->rewind();

        return $resultCollection;
    }

    /**
     * 購入通貨情報登録(マスタ版)
     * 
     * @param Application_Model_PlatformPaymentItem $platformPaymentItem 
     * @param Application_Model_ApplicationUserPayment $applicationUserPayment
     * @return boolean
     */
    public function savePurchaseCurrencyInfo($platformPaymentItem, $applicationUserPayment)
    {
        $this->getNowDatetime();

        // プラットフォーム商品アイテムの検索(有償)
        $platformProductItemMapper   = $this->getPlatformProductItemMapper($this->getDbSectionNameSub());
        $fetchedPlatformProductItems = $platformProductItemMapper->fetchAll([
            'platformProductId' => [$platformPaymentItem->getPlatformProductId()],
            'paymentPlatformId' => [$platformPaymentItem->getPaymentPlatformId()],
            'paymentDeviceId'   => [$applicationUserPayment->getPaymentDeviceId()],
            'paymentRatingId'   => [$applicationUserPayment->getPaymentRatingId()],
            'applicationId'     => [$applicationUserPayment->getApplicationId()],
            'unitPrice not '    => [Logic_Payment_Const::UNIT_PRICE_BONUS],
        ]);
        if (!$fetchedPlatformProductItems) {
            return FALSE;
        }

        // アプリケーションユーザペイメントアイテムの登録
        $applicationUserPaymentItemId = $this->saveApplicationUserPaymentItem($applicationUserPayment->getApplicationUserPaymentId());
        if (!$applicationUserPaymentItemId) {
            $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($applicationUserPayment));
            return FALSE;
        }

        // アプリケーションユーザターゲット通貨アイテムの登録(有償)
        foreach ($fetchedPlatformProductItems as $fetchedPlatformProductItem) {

            $toSaveApplicationUserTargetCurrencyPaymentItem = new Application_Model_ApplicationUserTargetCurrencyPaymentItem([
                'applicationUserPaymentItemId' => $applicationUserPaymentItemId,
                'applicationCurrencyId'        => $fetchedPlatformProductItem->getApplicationCurrencyId(),
                'currencyAmount'               => $fetchedPlatformProductItem->getCurrencyAmount() * $platformPaymentItem->getProductQuantity(),
                'price'                        => $fetchedPlatformProductItem->getUnitPrice() * $fetchedPlatformProductItem->getCurrencyAmount() * $platformPaymentItem->getProductQuantity(),
                'createdDate'                  => $this->_nowDatetime,
                'updatedDate'                  => $this->_nowDatetime,
            ]);
            if (!$this->saveApplicationUserTargetCurrencyPaymentItem($toSaveApplicationUserTargetCurrencyPaymentItem)) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($toSaveApplicationUserTargetCurrencyPaymentItem));
                return FALSE;
            }
        }

        // プラットフォーム商品アイテム(ボーナス)の検索
        $fetchedBonusPlatformProductItems = $platformProductItemMapper->fetchAll([
            'platformProductId' => [$platformPaymentItem->getPlatformProductId()],
            'paymentPlatformId' => [$platformPaymentItem->getPaymentPlatformId()],
            'paymentDeviceId'   => [$applicationUserPayment->getPaymentDeviceId()],
            'paymentRatingId'   => [$applicationUserPayment->getPaymentRatingId()],
            'applicationId'     => [$applicationUserPayment->getApplicationId()],
            'unitPrice'         => [Logic_Payment_Const::UNIT_PRICE_BONUS],
        ]);
        if ($fetchedBonusPlatformProductItems) {

            // アプリケーションユーザターゲット通貨アイテムの登録(ボーナス)
            foreach ($fetchedBonusPlatformProductItems as $fetchedBonusPlatformProductItem) {

                $toSaveBonusApplicationUserTargetCurrencyPaymentItem = new Application_Model_ApplicationUserTargetCurrencyPaymentItem([
                    'applicationUserPaymentItemId' => $applicationUserPaymentItemId,
                    'applicationCurrencyId'        => $fetchedBonusPlatformProductItem->getApplicationCurrencyId(),
                    'currencyAmount'               => $fetchedBonusPlatformProductItem->getCurrencyAmount() * $platformPaymentItem->getProductQuantity(),
                    'price'                        => Logic_Payment_Const::UNIT_PRICE_BONUS,
                    'createdDate'                  => $this->_nowDatetime,
                    'updatedDate'                  => $this->_nowDatetime,
                ]);
                if (!$this->saveApplicationUserTargetCurrencyPaymentItem($toSaveBonusApplicationUserTargetCurrencyPaymentItem)) {
                    $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($toSaveBonusApplicationUserTargetCurrencyPaymentItem));
                    return FALSE;
                }
            }
        }

        // アプリケーションユーザプラットフォームペイメント関連の登録
        if (!$this->saveApplicationUserPlatformPaymentRelation($applicationUserPaymentItemId, $platformPaymentItem->getPlatformPaymentItemId())) {
            $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_INSERT_FAIL . ' applicationUserPaymentItemId:' . $applicationUserPaymentItemId . ' platformPaymentItemId:' . $platformPaymentItem->getPlatformPaymentItemId());
            return FALSE;
        }

        return TRUE;
    }

    /**
     * 実行日時をy-MM-dd HH:mm:ssのフォーマットに変換して返す
     * 
     * @param string $executedDate 実行日時
     * @return string
     */
    public function formatExecutedDate($executedDate)
    {
        $d = new Zend_Date($executedDate);
        return $d->toString('y-MM-dd HH:mm:ss');
    }

}
