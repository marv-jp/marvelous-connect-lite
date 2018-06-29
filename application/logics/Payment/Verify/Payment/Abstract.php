<?php

/**
 * Logic_Payment_Verify_Payment_Abstractクラスのファイル
 *
 * Logic_Payment_Verify_Payment_Abstractクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment_Verify_Payment
 */

/**
 * Logic_Payment_Verify_Payment_Abstract
 *
 * プラットフォーム決済情報検証基底クラス
 *
 * @category Zend
 * @package  Logic_Payment_Verify_Payment
 * @method void setPaymentItemCollection(Misp_Collection_ApplicationUserTargetCurrencyPaymentItem $paymentItemCollection) アプリケーションユーザペイメントアイテムコレクションをセットします
 * @method Misp_Collection_ApplicationUserTargetCurrencyPaymentItem getPaymentItemCollection() アプリケーションユーザペイメントアイテムコレクションを返します
 * @method void setPlatformPaymentForCreditLog(array $platformPaymentForCreditLog) 有償ログ用プラットフォームペイメント配列をセットします
 * @method array getPlatformPaymentForCreditLog() 有償ログ用プラットフォームペイメント配列を返します
 * @method void setPlatformPaymentItemForCreditLog(array $platformPaymentItemForCreditLog) 有償ログ用プラットフォームペイメントアイテム配列をセットします
 * @method array getPlatformPaymentItemForCreditLog() 有償ログ用プラットフォームペイメントアイテム配列を返します
 */
abstract class Logic_Payment_Verify_Payment_Abstract extends Logic_Payment_Abstract implements Logic_Payment_Verify_Payment_Interface
{
    /**
     * ペイメントプラットフォームID
     *
     * @var string
     */
    protected $_paymentPlatformId = '';

    /**
     * コレクション：ペイメント
     *
     * @var Common_External_Platform_Model_Collection_Payments
     */
    protected $_verifiedReceiptPaymentCollection = null;

    /**
     * コレクション：ペイメントアイテム
     *
     * @var Misp_Collection_ApplicationUserTargetCurrencyPaymentItem
     */
    protected $_targetCurrencyPaymentItemCollection = null;

    /**
     * コレクション：アプリケーションユーザプラットフォームペイメント関連
     *
     * @var Misp_Collection_ApplicationUserPlatformPaymentRelation
     */
    protected $_platformPaymentRelationCollection = null;

    /**
     * モデル：プラットフォームペイメント
     *
     * @var Application_Model_PlatformPayment
     */
    protected $_platformPayment = null;

    /**
     * ペイメントID(トランザクションID/オーダーID)
     *
     * @var string
     */
    protected $_paymentId = '';

    /**
     * コンストラクタ
     *
     * @param Application_Model_PlatformPayment $platformPayment プラットフォームペイメントモデル
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     */
    public function __construct(Application_Model_PlatformPayment $platformPayment, $buildParams)
    {
        $this->init($platformPayment, $buildParams);
    }

    /**
     * レシート検証および情報登録
     * 
     * @param Misp_Collection_ApplicationUserTargetCurrencyPaymentItem $applicationUserTargetCurrencyPaymentItemCollection
     * @return boolean
     */
    public function verifyAnd(Misp_Collection_ApplicationUserTargetCurrencyPaymentItem $applicationUserTargetCurrencyPaymentItemCollection)
    {
        try {
            // 処理をメソッド分けした先で使用するので、プロパティに保持する
            $this->_targetCurrencyPaymentItemCollection = $applicationUserTargetCurrencyPaymentItemCollection;
            $this->_targetCurrencyPaymentItemCollection->rewind();

            // レシート検証
            if (!$this->verify()) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::MSG_ILLEGAL_RECEIPT);
                return FALSE;
            }

            if ($this->_isAlreadyVerify()) {
                return TRUE;
            }

            // レシートが複数の場合
            if ($this->getVerifiedReceipt()->current()->getPaymentItems()->count() > 1) {
                // ログ出力
                Common_Log::getInternalLog()->info('Multiple receipts count : ' . $this->getVerifiedReceipt()->current()->getPaymentItems()->count());

                $receipts     = clone $this->getVerifiedReceipt()->current()->getPaymentItems();
                $paymentItem  = NULL;
                $executedDate = '';

                foreach ($receipts as $receipt) {

                    // MZCL の戻り値(日付文字列='2017-05-13 17:15:40 Etc/GMT')を使用すると、タイムゾーンを無視して「2017-05-13 17:15:40」という実値より 9 時間過去の日付情報で登録されるため、
                    // この日付文字列でもタイムゾーンを解釈できる strtotime 関数で一旦 UNIX タイムスタンプに戻し、それを実行環境のタイムゾーンをもって RFC3339 フォーマットで日付文字列を得る
                    $executedDateTimestamp = strtotime($receipt->getExecutedTime());
                    $wkExecutedDate        = date(DATE_RFC3339, $executedDateTimestamp);
                    // 最新の purchase_date をもつレシートを採択するために、purchase_date(MZCL：executedTime)を比較する
                    // (文字コードで大小比較されるため文字列比較で問題ない)
                    // (2018-01-11現在では先頭方面が新しいトランザクションらしいので、同じ日時のレシートがきた場合は無視するために等号(=)はつけていない)
                    if ($wkExecutedDate > $executedDate) {
                        // 1ループ目は必ず代入が走る
                        $executedDate = $wkExecutedDate;
                        $paymentItem  = $receipt;
                    }
                    // 回った分はいずれにしてもデタッチしてオブジェクトを消しこんでいく
                    $this->getVerifiedReceipt()->current()->getPaymentItems()->detach($receipt);
                }
                // 最終的な最新レシートをアタッチする
                $this->getVerifiedReceipt()->current()->getPaymentItems()->attach($paymentItem);
                $this->getVerifiedReceipt()->current()->getPaymentItems()->rewind();

                // ログ出力
                Common_Log::getInternalLog()->info('Since there were two or more receipts, only the last receipt was valid');
            }

            // 商品ID数チェック
            //   APIリクエストパラメータの商品ID数とレシートの商品ID数が一致しているか
            if (!$this->_isValidPlatformProductIdNum()) {
                return FALSE;
            }

            // 数量チェック
            if (!$this->_isValidProductQuantityNum()) {
                return FALSE;
            }

            // 共通情報
            $verifiedReceipt    = $this->getVerifiedReceipt();
            $verifiedReceipt->rewind();
            $inRecieptPayment   = $verifiedReceipt->current(); // これのpaymentItemsコレクションの要素を消しこんでいく処理をするので、変数に受けておく
            // DB登録/更新日時
            $this->_nowDatetime = $this->getNowDatetime();

            // 怒涛のループ処理開始！
            // アプリケーションユーザターゲット通貨ペイメントアイテム単位で処理を行う
            foreach ($this->_targetCurrencyPaymentItemCollection as $targetCurrencyPaymentItem) {

                // 商品IDチェック
                //   APIリクエストパラメータの商品IDとレシートの商品IDの内容が一致しているか
                //   検証対象のプラットフォーム商品IDを取り出す
                $platformProductId = $targetCurrencyPaymentItem->getPlatformProductId();

                // MZCLのレシート/署名検証結果のコレクション
                // このコレクションは複数In-App(3件購入していたら3モデル保持)ですが、
                // 毎回その数だけループしない制御を後続にいれています。
                // 商品IDを取り出して、検証対象のプラットフォーム商品IDと突合
                // (念のための複数対応ループ。基本的に要素は1つのみ)
                foreach ($inRecieptPayment->getPaymentItems() as $inRecieptPaymentItem) {

                    // 商品IDが合致した場合のみ処理を続行
                    if ($inRecieptPaymentItem->getItemId() == $platformProductId) {

                        list($paymentIds, $paymentId) = $this->getPaymentIds($inRecieptPayment, $inRecieptPaymentItem);
                        $data             = $paymentIds[$paymentId];
                        $this->_paymentId = $paymentId;

                        // 使い回し確認1
                        if ($this->_isAlreadyExistPlatformPaymentItem()) {
                            $this->_logInfo(__CLASS__, __METHOD__, __LINE__, sprintf('プラットフォームペイメントテーブルに登録済みのプラットフォームペイメントIDです：[Platform:%s][PaymentId:%s]', $this->_platform, $paymentId));
                            return FALSE;
                        }

                        // 使い回し確認2
                        if ($this->_isAlreadyExistCurrencyCreditLog()) {
                            $this->_logInfo(__CLASS__, __METHOD__, __LINE__, sprintf('通貨購入ログテーブルに登録済みのプラットフォームペイメントIDです：[Platform:%s][PaymentId:%s]', $this->_platform, $paymentId));
                            return FALSE;
                        }

                        // 検証情報保存
                        //   プラットフォームペイメント登録
                        $platformPayment     = $this->_saveVerifiedPlatformPayment($data);
                        //   プラットフォームペイメントアイテム登録
                        $platformPaymentItem = $this->_saveVerifiedPlatformPaymentItem($data, $targetCurrencyPaymentItem);

                        // アプリケーションユーザプラットフォームペイメント関連登録
                        $this->_saveVerifiedApplicationUserPlatformPaymentRelation($targetCurrencyPaymentItem, $platformPaymentItem);

                        // アプリケーションユーザ通貨購入ログ登録
                        $this->_saveVerifiedApplicationUserCurrencyCreditLog($platformPaymentItem, $platformPayment, $targetCurrencyPaymentItem);

                        // MZCLのレシート検証結果コレクションから処理済みのモデルを消しこむ
                        $inRecieptPayment->getPaymentItems()->detach($inRecieptPaymentItem);

                        // 後続で参照するため実行日時をこのタイミングでセットしておく
                        // (コレクションループのモデルはリファレンスなので、ここでいじればattachし直さなくても更新される)
                        $targetCurrencyPaymentItem->setExecutedDate($data['executedDate']);

                        // レシートループの終端までジャーンプ！
                        continue 2;
                    }
                }
            }

            $this->_targetCurrencyPaymentItemCollection->rewind();
            $this->setPaymentItemCollection($this->_targetCurrencyPaymentItemCollection);

            // MZCLのレシート検証結果コレクションが空であれば商品IDチェックはTRUE
            if (!$inRecieptPayment->getPaymentItems()->count()) {
                // 真偽値返却(9-a)
                return TRUE;
            } else {
                // MZCLのレシート検証結果コレクションが空でない場合は商品IDチェックはFALSE
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, 'レシートの商品IDとリクエストの商品IDが一致しません');
                return FALSE;
            }

            //
        } catch (Common_Exception_Exception $exc) {
            //
            // 登録が失敗した場合の例外ハンドリング
            // (上位のキャッチ文でログ出力される)
            //
            throw $exc;
            //
        } catch (Exception $exc) {
            //
            // その他
            //
            $this->_logError($exc, __CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            throw $exc;
            //
        }
    }

    /**
     * プラットフォームIDを返します
     * 
     * @return string
     */
    public function getPaymentPlatformId()
    {
        return $this->_platform;
    }

    /**
     * デバイスIDを返します
     * 
     * @return string
     */
    public function getPaymentDeviceId()
    {
        return $this->_platformPayment->getPaymentDeviceId();
    }

    /**
     * 検証のためにセットされたレシートを返します
     * 
     * @return string
     */
    public function getReceipt()
    {
        $this->_platformPayment->getReceipt();
    }

    /**
     * 検証のためにセットされた署名を返します
     * 
     * @return string
     */
    public function getSignature()
    {
        $this->_platformPayment->getSignature();
    }

    /**
     * 商品ID数チェック
     *
     * 引数から取得した商品ID数と、レシートの商品ID数が一致しているかをチェックします。<br>
     * FALSE時は内部ログに情報を出力します。
     *
     * @return boolean
     */
    private function _isValidPlatformProductIdNum()
    {
        // 引数から取得した商品ID数
        $inputProductIdNum = $this->_targetCurrencyPaymentItemCollection->count();

        // レシートの商品ID数
        $inReceiptProductIdNum = $this->getVerifiedReceipt()->current()->getPaymentItems()->count();

        // 引数から取得した商品ID数と、レシートの商品ID数が一致していればOK
        if ($inputProductIdNum === $inReceiptProductIdNum) {
            return TRUE;
        }
        // 一致していない場合は内部ログ出力してFALSE
        $this->_logInfo(__CLASS__, __METHOD__, __LINE__, sprintf('APIリクエストパラメータの商品ID数とレシートの商品ID数が一致していません：[InputProductIdNum:%s][ProductIdNumInReceipt:%s]', $inputProductIdNum, $inReceiptProductIdNum));
        return FALSE;
    }

    /**
     * 商品数量チェック
     *
     * 引数から取得した商品数量と、レシートの商品数量が一致しているかをチェックします。<br>
     * FALSE時は内部ログに情報を出力します。
     *
     * @return boolean
     */
    private function _isValidProductQuantityNum()
    {
        // 引数から取得した商品
        $inputProducts     = $this->pickUpPayment();
        // レシートの商品
        $inReceiptProducts = clone $this->getVerifiedReceipt()->current()->getPaymentItems();

        // 商品IDと数量チェック
        foreach ($inputProducts as $inputProduct) {
            if (array_key_exists('productId', $inputProduct)) {
                foreach ($inReceiptProducts as $paymentItem) {
                    $inReceiptProduct = $paymentItem->toArray();
                    if (array_key_exists('itemId', $inReceiptProduct)) {
                        if ($inReceiptProduct['itemId'] == $inputProduct['productId']) {
                            if ($inReceiptProduct['quantity'] == $inputProduct['quantity']) {
                                // 商品IDと商品数量が一致した場合、デタッチ
                                $inReceiptProducts->detach($paymentItem);
                                continue 2;
                            }
                        }
                    }
                }
            }
        }

        // 引数から取得した商品が空になっていればOK
        if (!$inReceiptProducts->count()) {
            // 真偽値返却(9-a)
            return TRUE;
        } else {
            // 商品が一致していない場合は内部ログ出力してFALSE
            // レシートの中身の出力
            foreach ($inReceiptProducts as $inReceiptProduct) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, sprintf('APIリクエストパラメータの商品数量とレシートの商品数量が一致していません：[ReceiptProductId:%s][ReceiptQuantity:%s]', $inReceiptProduct->getItemId(), $inReceiptProduct->getQuantity()));
            }

            // 引数の中身の出力
            foreach ($inputProducts as $inputProduct) {
                $this->_logInfo(__CLASS__, __METHOD__, __LINE__, sprintf('APIリクエストパラメータの商品数量とレシートの商品数量が一致していません：[EntryProductId:%s][EntryQuantity:%s]', $inputProduct['productId'], $inputProduct['quantity']));
            }

            return FALSE;
        }
    }

    /**
     * 使い回し確認1(5-a, 5-b)
     *
     * クライアントから送信されたレシート/署名内のトランザクションID/オーダーIDが、
     * プラットフォームペイメントアイテムテーブルに登録済み(=使い回し、重複)かどうか確認します。
     *
     * @return boolean TRUE:  使い回しのトランザクションID/オーダーIDである
     *                  FALSE: 使い回しのトランザクションID/オーダーIDではない
     */
    private function _isAlreadyExistPlatformPaymentItem()
    {
        // Where
        $where                      = array();
        $where['platformPaymentId'] = array($this->_paymentId);
        $where['paymentPlatformId'] = array($this->_platform);
        // Mapper
        $mapper                     = $this->getPlatformPaymentItemMapper($this->getDbSectionNameMain());
        // Select
        if ($mapper->fetchAll($where)) {
            // 取得できたら使い回しということなので、存在確認のこのメソッド的にはTRUEを返す
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 使い回し確認2(6-a, 6-b)
     *
     * クライアントから送信されたレシート/署名内のトランザクションID/オーダーIDが、
     * アプリケーションユーザ通貨購入ログに登録済み(=使い回し、重複)かどうか確認します。
     *
     * @return boolean TRUE:  使い回しのトランザクションID/オーダーIDである
     *                  FALSE: 使い回しのトランザクションID/オーダーIDではない
     */
    private function _isAlreadyExistCurrencyCreditLog()
    {
        // Where
        $where                      = array();
        $where['platformPaymentId'] = array($this->_paymentId);
        $where['paymentPlatformId'] = array($this->getApplicationUserPayment()->getPaymentPlatformId());
        // Mapper
        $mapper                     = $this->getApplicationUserCurrencyCreditLogMapper($this->getDbSectionNameMain());
        // Select
        if ($mapper->fetchAll($where)) {
            // 取得できたら使い回しということなので、存在確認のこのメソッド的にはTRUEを返す
            return TRUE;
        }

        return FALSE;
    }

    /**
     * プラットフォームペイメントテーブルの存在確認を行います。
     *
     * @return boolean 存在する場合 TRUE / 存在しない場合 FALSE を返します
     */
    protected function _isPlatformPayment()
    {
        // Select
        $mapper = $this->getPlatformPaymentMapper($this->getDbSectionNameMain());
        if ($mapper->find($this->_paymentId, $this->_platform)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 検証情報保存(7,12)
     * 
     * 検証情報保存(7,12)で行う処理の論理メソッドです。
     * プラットフォームペイメントテーブルに登録します。
     *
     * @param array $data ペイメントモデルの中身を連想配列化したもの
     * @return Application_Model_PlatformPayment 登録成功したプラットフォームペイメントのモデル
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    protected function _saveVerifiedPlatformPayment($data)
    {
        // 登録情報
        $m = new Application_Model_PlatformPayment();
        $m->setPlatformPaymentId($this->_paymentId);
        $m->setPaymentPlatformId($this->_platform);
        $m->setPaymentDeviceId($this->_platformPayment->getPaymentDeviceId() ?? '');
        $m->setPaymentRatingId($this->_platformPayment->getPaymentRatingId() ?? '');
        $m->setPlatformPaymentStatus($data['status']);
        $m->setReceipt($this->_platformPayment->getReceipt());
        $m->setSignature($this->_platformPayment->getSignature());
        $m->setCreatedDate($this->_nowDatetime);
        $m->setUpdatedDate($this->_nowDatetime);

        // Insert
        $mapper = $this->getPlatformPaymentMapper($this->getDbSectionNameMain());
        if (!$mapper->insert($m)) {
            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
        }

        return $m;
    }

    /**
     * 検証情報保存(7,12)
     * 
     * 検証情報保存(7,12)で行う処理の論理メソッドです。
     * プラットフォームペイメントアイテムテーブルに登録します。
     *
     * @param array $data ペイメントモデルの中身を連想配列化したもの
     * @param Application_Model_ApplicationUserTargetCurrencyPaymentItem $targetCurrencyPaymentItem
     * @return Application_Model_PlatformPaymentItem 登録成功したプラットフォームペイメントアイテムのモデル(LastInsertId入り)
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    protected function _saveVerifiedPlatformPaymentItem($data, Application_Model_ApplicationUserTargetCurrencyPaymentItem $targetCurrencyPaymentItem)
    {
        // 登録情報
        $m = new Application_Model_PlatformPaymentItem();
        $m->setPlatformPaymentId($this->_paymentId);
        $m->setPaymentPlatformId($this->_platform);
        $m->setPlatformProductId($data['itemId']);
        // アプリケーションユーザターゲット通貨ペイメントアイテムの価格は通貨額の価格
        // プラットフォームペイメントアイテムでは商品ひとつ辺りの価格が入るため、商品数量で割る必要がある
        $m->setPrice($targetCurrencyPaymentItem->getPrice() / $data['quantity']);
        $m->setProductQuantity($data['quantity']);
        $m->setExecutedDate($data['executedDate']);
        $m->setCreatedDate($this->_nowDatetime);
        $m->setUpdatedDate($this->_nowDatetime);

        // Insert
        $mapper = $this->getPlatformPaymentItemMapper($this->getDbSectionNameMain());
        // LastInsetIdをセット
        $m->setPlatformPaymentItemId($mapper->insert($m));
        if (!$m->getPlatformPaymentItemId()) {
            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
        }

        return $m;
    }

    /**
     * アプリケーションユーザプラットフォームペイメント関連登録
     * 
     * @param Application_Model_ApplicationUserTargetCurrencyPaymentItem $targetCurrencyPaymentItem
     * @param Application_Model_PlatformPaymentItem $platformPaymentItem
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    protected function _saveVerifiedApplicationUserPlatformPaymentRelation(Application_Model_ApplicationUserTargetCurrencyPaymentItem $targetCurrencyPaymentItem, Application_Model_PlatformPaymentItem $platformPaymentItem)
    {
        // 登録情報
        $m = new Application_Model_ApplicationUserPlatformPaymentRelation();
        $m->setApplicationUserPaymentItemId($targetCurrencyPaymentItem->getApplicationUserPaymentItemId());
        $m->setPlatformPaymentItemId($platformPaymentItem->getPlatformPaymentItemId());
        $m->setCreatedDate($this->_nowDatetime);
        $m->setUpdatedDate($this->_nowDatetime);

        // Mapper
        $mapper = $this->getApplicationUserPlatformPaymentRelationMapper($this->getDbSectionNameMain());
        // Insert
        if (!$mapper->insert($m)) {
            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
        }
    }

    /**
     * ペイメントID関連データ返却
     * 
     * プラットフォームのモデル構造の差異を吸収し、ペイメントIDをキーとするモデルデータの連想配列を返します。<br>
     * <br>
     * <b>注意：このメソッドはverify()後に使用してください<b>
     *
     * @param Application_Model_CommonExternalPlatformPayment $externalPlatformPayment
     * @param Application_Model_CommonExternalPlatformPaymentItem $inRecieptPaymentItem
     * @return array プラットフォームのペイメントIDと商品IDなどの連想配列
     * <pre>
     * array(
     *    'ペイメントID1' => array('itemId' => 'item1', 'quantity' => 1, 'status' => 'success', 'executedDate' => '2015-07-31T12:34:56'),
     *    'ペイメントID2' => array('itemId' => 'item2', 'quantity' => 1, 'status' => 'cancel',  'executedDate' => '2015-08-01T02:57:11'),
     * );
     * </pre>
     */
    abstract public function getPaymentIds(Application_Model_CommonExternalPlatformPayment $externalPlatformPayment, Application_Model_CommonExternalPlatformPaymentItem $inRecieptPaymentItem = NULL);

    /**
     * 別レシートを検証する際の当インスタンス初期化用メソッドです
     *
     * 一度当インスタンスを生成したあとに、別のレシートを検証する場合に使用します。<br>
     * initの引数には、新たに検証したい別のレシート情報をセットした Application_Model_PlatformPayment を渡してください。
     *
     * @param Application_Model_PlatformPayment $platformPayment
     * @param array $buildParams APIリクエストパラメータの"entry"項目の中身
     */
    public function init(Application_Model_PlatformPayment $platformPayment, $buildParams)
    {
        if ($this->getApplicationUserPayment()) {
            $this->_applicationId = $this->getApplicationUserPayment()->getApplicationId();
        }
        $this->_platformPayment                   = $platformPayment;
        $this->_platform                          = $platformPayment->getPaymentPlatformId();
        $this->_platformPaymentRelationCollection = new Misp_Collection_ApplicationUserPlatformPaymentRelation();
        $this->_buildParams                       = $buildParams;
    }

    /**
     * プラットフォームの検証ステータスを返します
     *
     * <b>注意：このメソッドはverify()後に使用してください<b>
     *
     * @return mixed プラットフォームの検証ステータス
     */
    public function getStatus()
    {
        $this->getVerifiedReceipt()->rewind();
        $model = $this->getVerifiedReceipt()->current();
        return $model->getStatus();
    }

    /**
     * 検証結果返却
     * 
     * 検証結果を判定し、結果を返します。
     *
     * @return boolean
     */
    protected function _returnResult()
    {
        // isSuccessで成否を判定など
        if (!$this->getVerifiedReceipt()->isSuccess()) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * 検証済みレシートを返します
     * 
     * MZCLの戻りのコレクションそのままです。
     * 
     * @return Common_External_Platform_Model_Collection_Payments
     */
    public function getVerifiedReceipt()
    {
        return $this->_verifiedReceiptPaymentCollection;
    }

    /**
     * 検証済みレシート(MZCLコレクション)をセットします
     * 
     * MZCLの戻りのコレクションそのままをそのまま引数に渡してください。
     * 
     * @param Common_External_Platform_Model_Collection_Payments $verifiedReceiptPaymentCollection
     */
    public function setVerifiedReceiptPaymentCollection($verifiedReceiptPaymentCollection)
    {
        $this->_verifiedReceiptPaymentCollection = $verifiedReceiptPaymentCollection;
    }

    /**
     * プラットフォーム固有の処理(vefiry())で検証済みかどうかを返します。
     * 
     * 検証済みかどうかは、プラットフォームIDとデバイスIDの組み合わせで判断されます。
     * 
     * @return boolean TRUE:検証済み
     *                  FALSE:検証済みでない
     */
    private function _isAlreadyVerify()
    {
        $platformId = $this->getPaymentPlatformId();
        $deviceId   = $this->getPaymentDeviceId();
        $return     = FALSE;

        switch ($platformId) {

            case Logic_Payment_Const::PLATFORM_ID_MOOG:

                $return = TRUE;
                break;

            default:
                break;
        }

        return $return;
    }

}
