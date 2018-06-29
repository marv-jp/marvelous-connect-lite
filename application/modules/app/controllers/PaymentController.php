<?php

class App_PaymentController extends App_BaseController
{

    /**
     * ペイメントAPI設定の先頭のキー名(設定はapplication.iniに記述)
     * 
     * @var string
     */
    const API_CONFIG_NAME = 'apiPayment_config';

    /**
     * ペイメントAPI設定(連想配列)格納用(設定はapplication.iniに記述)
     *
     * @var array 
     */
    protected $_apiConfig = array();

    /**
     * 許可するリクエストパラメータ項目
     * 
     * @var array $_allowRequestParameters
     */
    protected $_allowRequestParameters = array(
        // 仮想通貨系API
        self::API_NAME_PAYMENT => array(
            // GET時
            Common_Http_Client::GET => array(
                Misp_Collection_OpenSocial_Collection::COUNT       => '',
                Misp_Collection_OpenSocial_Collection::START_INDEX => '',
            )
        ),
    );
    protected $_jsonSchema             = array(
        // PUT時
        Common_Http_Client::PUT => '{
    "type":"object",
    "properties":{
        "platformId": {"type": "string"},
        "deviceId": {"type": "string"},
        "ratingId": {"type": "string"},
        "id": {"type": "string"},
        "platformPaymentId": {"type": "string"},
        "receipt": {"type": "string"},
        "signature": {"type": "string"},
        "account": {
            "userId":{"type":"string"},
            "authorizationCode":{"type":"string"},
            "accessToken":{"type":"string"},
            "accessTokenSecret":{"type":"string"}
        },
        "finishUrl": {"type": "string"},
        "paidWith": {"type": "string"},
        "entry": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "creditItem": {"type": "object"},
                    "productId": {"type": "string"},
                    "price": {"type": ["string","null"]},
                    "quantity": {"type": ["string","null"]},
                    "bonusItem": {"type": "object"}
                },
                "dependencies": {
                    "creditItem": ["productId"]
                }
            }
        }
    },
    "required": ["platformId", "id"]
}'
    );

    /**
     * APIのレスポンス項目定義
     *
     * @var array $_responsePayload
     */
    protected $_responsePayload = array(
        // 仮想通貨API 消費系
        self::API_NAME_PAYMENT         => array(
            'platformId' => array('column' => 'paymentPlatformId'),
            'deviceId'   => array('column' => 'paymentDeviceId', 'blankSkip' => 'true'),
            'ratingId'   => array('column' => 'paymentRatingId', 'blankSkip' => 'true'),
            'id'         => array('column' => 'applicationUserPaymentId'),
            'status'     => array('column' => 'paymentStatus', 'proc' => array('type' => 'instance', 'method' => '_convertPaymentStatusToOrderStatus')),
            'type'       => array('column' => 'paymentType', 'proc' => array('type' => 'instance', 'method' => '_convertPaymentTypeToPaymentType')),
            'published'  => array('column' => 'createdDate', 'proc' => array('type' => 'instance', 'method' => '_convertToOpnesocialDateformat')),
            'updated'    => array('column' => 'updatedDate', 'proc' => array('type' => 'instance', 'method' => '_convertToOpnesocialDateformat')),
        ),
        self::API_NAME_PAYMENT_BALANCE => array(
            'paymentId'  => array('column' => 'applicationUserPaymentId', 'blankSkip' => 'true'),
            'currencyId' => array('column' => 'applicationCurrencyId'),
            'unitPrice'  => array('column' => 'unitPrice'),
            'amount'     => array('column' => 'currencyAmount'),
            'published'  => array('column' => 'executedDate', 'proc' => array('type' => 'instance', 'method' => '_convertToOpnesocialDateformat'), 'blankSkip' => 'true'),
            'updated'    => array('column' => 'updatedDate', 'proc' => array('type' => 'instance', 'method' => '_convertToOpnesocialDateformat')),
            'expired'    => array('column' => 'expiredDate', 'proc' => array('type' => 'instance', 'method' => '_convertToOpnesocialDateformat'), 'blankSkip' => 'true'),
        ),
        self::API_NAME_PAYMENT_HISTORY => array(
            'type'       => array('column' => 'type'),
            'platformId' => array('column' => 'paymentPlatformId'),
            'productId'  => array('column' => 'platformProductId', 'blankSkip' => 'true'),
            'paymentId'  => array('column' => 'applicationUserPaymentId'),
            'currencyId' => array('column' => 'applicationCurrencyId'),
            'unitPrice'  => array('column' => 'unitPrice'),
            'amount'     => array('column' => 'currencyAmount'),
            'quantity'   => array('column' => 'productQuantity', 'blankSkip' => 'true'),
            'published'  => array('column' => 'publishedDate', 'proc' => array('type' => 'instance', 'method' => '_convertToOpnesocialDateformat')),
        ),
    );

    public function getAction()
    {
        // リクエスト取得
        $request = $this->getRequest();
        try {
            // APIモードチェック
            $this->_checkAppApiMode();

            // ユーザ識別子,セレクタをリクエストパラメータから取得
            $id               = $request->getParam('id');
            $selector         = $request->getParam('selector');
            $platformId       = $request->getParam('platformId', '');
            $deviceId         = $request->getParam('deviceId', '');
            $ratingId         = $request->getParam('ratingId', '');
            $relatedPaymentId = $request->getParam('relatedPaymentId', '');
            $count            = $request->getParam('count', '10');
            $startIndex       = $request->getParam('startIndex', '1');
            $type             = $request->getParam('type', '');

            // idからアプリケーションユーザモデルを取得
            $applicationUserModel = $this->_readApplicationUserModel($id);

            $httpResponseCode = 200;
            $returnData       = [];

            // ロジック呼び出し、selectorでロジック振り分け(現状振り分ける必要はないが一応)
            switch ($selector) {
                // @self
                case Misp_Base_RestController::SELECTOR_SELF:

                    switch ($relatedPaymentId) {

                        case Misp_Base_RestController::SELECTOR_BALANCE:

                            // パラメータ必須チェック
                            if (Common_Util_String::isEmpty($platformId)) {
                                throw new Common_Exception_IllegalParameter('パラメータが不正です');
                            }

                            $validatorPaymentPlatformId = new Validate_PaymentPlatformId();
                            if ($validatorPaymentPlatformId->isNotValid($platformId)) {
                                throw new Common_Exception_IllegalParameter('パラメータが不正です');
                            }

                            // 所持通貨・履歴取扱ロジック
                            $logicPaymentBalanceHistory = new Logic_Payment_BalanceHistory();

                            // 引数のアプリケーションユーザ通貨モデル作成
                            $conditionalApplicationUserCurrency = new Application_Model_ApplicationUserCurrency($applicationUserModel->toArray());
                            $conditionalApplicationUserCurrency->setPaymentPlatformId($platformId);
                            $conditionalApplicationUserCurrency->setPaymentDeviceId($deviceId);
                            $conditionalApplicationUserCurrency->setPaymentRatingId($ratingId);

                            // 所持通貨情報取得
                            $applicationUserCurrencyModels = $logicPaymentBalanceHistory->getBalance($conditionalApplicationUserCurrency);

                            // レスポンス用の配列作成
                            foreach ($applicationUserCurrencyModels as $applicationUserCurrencyModel) {
                                $returnData['entry'][] = $this->_convertModelToAssociativeArray($applicationUserCurrencyModel, $this->_responsePayload[self::API_NAME_PAYMENT_BALANCE]);
                            }

                            // 所持通貨情報が存在しない場合、レスポンスコードは205
                            if (Misp_Util::isEmpty($returnData)) {
                                $httpResponseCode = 205;
                            }

                            break;
                        case Misp_Base_RestController::SELECTOR_HISTORY:

                            // type検証
                            $validatorHistoryType = new Validate_HistoryType();
                            if (Misp_Util::isNotEmpty($type) && $validatorHistoryType->isNotValid($type)) {
                                throw new Common_Exception_IllegalParameter('パラメータが不正です');
                            }

                            // 所持通貨・履歴取扱ロジック
                            $logicPaymentBalanceHistory = new Logic_Payment_BalanceHistory();

                            // 引数のアプリケーションユーザペイメントモデル作成
                            $conditionalApplicationUserPayment = new Application_Model_ApplicationUserPayment($applicationUserModel->toArray());

                            // 通貨履歴取得
                            // 履歴のモデル取得
                            $histories = $logicPaymentBalanceHistory->getHistory($conditionalApplicationUserPayment, $type, $count, $startIndex);

                            $returnData['startIndex']   = $startIndex;
                            $returnData['itemsPerPage'] = count($histories);
                            $returnData['totalResults'] = $logicPaymentBalanceHistory->getHistoryTotalResults();

                            // レスポンス用の配列作成
                            foreach ($histories as $hitstory) {
                                $returnData['entry'][] = $this->_convertModelToAssociativeArray($hitstory, $this->_responsePayload[self::API_NAME_PAYMENT_HISTORY]);
                            }

                            // 履歴が存在しない場合、レスポンスコードは205
                            if (!array_key_exists('entry', $returnData)) {
                                $httpResponseCode = 205;
                            }

                            break;
                        default:

                            // パラメータ必須チェック
                            if (Common_Util_String::isEmpty($platformId)) {
                                throw new Common_Exception_IllegalParameter('パラメータが不正です');
                            }

                            // レスポンス情報用の連想配列生成
                            // 仮想通貨処理状態、残高取得
                            $returnData = $this->_convertPaymentCurrencyArray($applicationUserModel, array('platformId' => $platformId, 'deviceId' => $deviceId, 'ratingId' => $ratingId));

                            // 決済情報が存在しない場合、レスポンスコードは205
                            if (!isset($returnData['id'])) {
                                $httpResponseCode = 205;
                            }
                    }

                    break;
                default:
                    throw new Common_Exception_IllegalParameter('セレクタのパラメータが不正です');
            }

            // 正常終了、連想配列はJSON形式に変更して返す
            $response = $this->getResponse();
            $response->setHttpResponseCode($httpResponseCode);
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody(Zend_Json::encode($returnData));
            //
        } catch (Common_Exception_Abstract $exc) {
            $response = $this->_responseException($exc);
        } catch (Exception $exc) {
            // 500エラー
            $response = $this->getResponse();
            $response->setHttpResponseCode(500);
            $response->setException($exc);
        }
    }

    public function deleteAction()
    {
        // リクエスト取得
        $request = $this->getRequest();
        try {
            // APIモードチェック
            $this->_checkAppApiMode();

            // ユーザ識別子,セレクタをリクエストパラメータから取得
            $id          = $request->getParam('id');
            $selector    = $request->getParam('selector');
            $accessToken = $request->getParam('platformAccessToken', '');

            // idからアプリケーションユーザモデルを取得
            $applicationUserModel = $this->_readApplicationUserModel($id);

            // ロジック呼び出し、selectorでロジック振り分け(現状振り分ける必要はないが一応)
            switch ($selector) {
                // @self
                case Misp_Base_RestController::SELECTOR_SELF:

                    $buildParams = [
                        'accessToken' => $accessToken,
                    ];
                    $logic       = new Logic_Payment_PaymentDelete();
                    $logic->setApplicationUser($applicationUserModel);
                    // 削除処理実行
                    $result      = $logic->exec($buildParams);

                    break;
                default:
                    throw new Common_Exception_IllegalParameter('セレクタのパラメータが不正です');
            }

            // 正常終了、連想配列はJSON形式に変更して返す
            $response = $this->getResponse();
            $response->setHttpResponseCode(200); // 削除対象があった場合
            if (!$result) {
                $response->setHttpResponseCode(204); // 削除対象がなかった場合
            }

            $response->setHeader('Content-Type', 'application/json');
            //
        } catch (Common_Exception_Abstract $exc) {
            $response = $this->_responseException($exc);
        } catch (Exception $exc) {
            // 500エラー
            $response = $this->getResponse();
            $response->setHttpResponseCode(500);
            $response->setException($exc);
        }
    }

    public function postAction()
    {
        // リクエスト取得
        $request = $this->getRequest();
        try {
            // APIモードチェック
            $this->_checkAppApiMode();

            // ユーザ識別子,セレクタをリクエストパラメータから取得
            $id       = $request->getParam('id');
            $selector = $request->getParam('selector');

            // Body部取得
            $bodyParam = Zend_Json::decode($request->getRawBody());

            // 手動validate
            if (array_key_exists('entry', $bodyParam)) {
                foreach ($bodyParam['entry'] as $entry) {
                    if (array_key_exists('bonusItems', $entry)) {
                        if (!$this->_validateBonusItems($entry['bonusItems'])) {
                            throw new Common_Exception_IllegalParameter('パラメータが不正です');
                        }
                        if (array_key_exists('published', $entry)) {
                            $v = new Validate_Date_Iso8601();
                            if ($v->isNotValid($entry['published'])) {
                                throw new Common_Exception_IllegalParameter('パラメータが不正です');
                            }
                        }
                    }
                }
            }

            // ロジック呼び出し、selectorでロジック振り分け(現状振り分ける必要はないが一応)
            switch ($selector) {
                // @self
                case Misp_Base_RestController::SELECTOR_SELF:

                    // idからアプリケーションワールドIDとアプリケーションユーザIDを取得
                    // idからアプリケーションユーザモデルを取得
                    $applicationUserModel = $this->_readApplicationUserModel($id);

                    $logic = new Logic_Payment_PaymentCreate();
                    $logic->setApplicationUser($applicationUserModel);
                    $logic->setCurrencyCreditLogic(new Logic_Payment_Trade_CurrencyCredit());
                    $logic->setVerifyPaymentFactory(new Logic_Payment_Verify_Payment_Factory());
                    // 登録処理実行
                    $logic->exec($this->_changeEntryFormatForLogic($bodyParam));

                    // レスポンス情報用の連想配列生成
                    // 仮想通貨処理状態、残高取得
                    $returnData = $this->_convertPaymentCurrencyArray($applicationUserModel, $bodyParam);

                    break;
                default:
                    throw new Common_Exception_IllegalParameter('セレクタのパラメータが不正です');
            }

            // 正常終了、連想配列はJSON形式に変更して返す
            $response = $this->getResponse();
            $response->setHttpResponseCode(201);
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody(Zend_Json::encode($returnData));
        } catch (Common_Exception_Abstract $exc) {
            $response = $this->_responseException($exc);
        } catch (Exception $exc) {
            // 500エラー
            $response = $this->getResponse();
            $response->setHttpResponseCode(500);
            $response->setException($exc);
        }
    }

    public function putAction()
    {
        // リクエスト取得
        $request = $this->getRequest();
        try {
            // APIモードチェック
            $this->_checkAppApiMode();

            // ユーザ識別子,セレクタをリクエストパラメータから取得
            $id       = $request->getParam('id');
            $selector = $request->getParam('selector');

            // Body部取得
            $bodyParam = Zend_Json::decode($request->getRawBody());

            // 手動validate
            if (array_key_exists('entry', $bodyParam)) {
                foreach ($bodyParam['entry'] as $entry) {
                    if (array_key_exists('bonusItem', $entry)) {
                        if (!$this->_validateBonusItems($entry['bonusItem'])) {
                            throw new Common_Exception_IllegalParameter('パラメータが不正です');
                        }
                    }
                }
            }

            // 正常終了のレスポンス情報作成
            $response = $this->getResponse();
            $response->setHttpResponseCode(201);

            // ロジック呼び出し、selectorでロジック振り分け(現状振り分ける必要はないが一応)
            switch ($selector) {
                // @self
                case Misp_Base_RestController::SELECTOR_SELF:

                    // リクエストボディ検証
                    $this->_validateJsonBySchema($request->getRawBody(), $this->_jsonSchema[Common_Http_Client::PUT]);

                    // idからアプリケーションユーザモデルを取得
                    $applicationUserModel = $this->_readApplicationUserModel($id);

                    // ロジック生成
                    $logic = new Logic_Payment_PaymentUpdate();
                    $logic->setTradeFactory(new Logic_Payment_Trade_Factory());
                    $logic->setApplicationUser($applicationUserModel);

                    // receiptとentry部が存在する場合、type=creditを$bodyParamにセットする
                    // また、その際にsetVerifyPaymentFactoryも行う
                    // パラメータ変換のための処理、他に良い方法があればそちらで対応
                    if (isset($bodyParam['entry'][0]) && isset($bodyParam['receipt'])) {
                        $bodyParam['type'] = 'credit';
                    }
                    // receiptが存在し、entryが存在しない場合、type=creditを$bodyParamにセットする
                    if (!isset($bodyParam['entry']) && isset($bodyParam['receipt'])) {
                        $bodyParam['type'] = 'credit';
                    }
                    // accountとentry部が存在する場合、type=creditを$bodyParamにセットする
                    if (isset($bodyParam['entry'][0]) && isset($bodyParam['account'])) {
                        $bodyParam['type'] = 'credit';
                    }
                    // entryにbonusItemとcreditItemが存在しない場合、type=creditを$bodyParamにセットする
                    if (isset($bodyParam['entry'][0]) && (!isset($bodyParam['entry'][0]['bonusItem']) && !isset($bodyParam['entry'][0]['creditItem']))) {
                        $bodyParam['type'] = 'credit';
                    }
                    // entryにbonusItemのみ存在する場合、type=bonusを$bodyParamにセットする
                    if (isset($bodyParam['entry'][0]) && isset($bodyParam['entry'][0]['bonusItem']) && !isset($bodyParam['entry'][0]['creditItem'])) {
                        $bodyParam['type'] = 'exchangeBonus';
                    }

                    $buildParams = $this->_changeEntryFormatForLogic($bodyParam);
                    // typeは実処理では必要ないので削除
                    unset($buildParams['type']);

                    try {
                        // 更新処理実行
                        $logic->exec($buildParams);
                    } catch (Common_Exception_NotModified $exc) {
                        $response->setHttpResponseCode(304);
                    }

                    // レスポンス情報用の連想配列生成
                    // 仮想通貨処理状態、残高取得
                    $returnData = $this->_convertPaymentCurrencyArray($applicationUserModel, $bodyParam);

                    break;
                default:
                    throw new Common_Exception_IllegalParameter('セレクタのパラメータが不正です');
            }
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody(Zend_Json::encode($returnData));
        } catch (Common_Exception_Abstract $exc) {
            $response = $this->_responseException($exc);
        } catch (Exception $exc) {
            // 500エラー
            $response = $this->getResponse();
            $response->setHttpResponseCode(500);
            $response->setException($exc);
        }
    }

    /**
     * 対象のユーザの残高情報と仮想通貨処理状態を取得し、
     * レスポンスの連想配列の形になおして返却する
     * 
     * @param Application_Model_ApplicationUser $applicationUserModel
     * @param array $buildParams
     * @return array
     */
    private function _convertPaymentCurrencyArray($applicationUserModel, $buildParams)
    {
        // 残高情報と仮想通貨処理状態を取得
        $logic = new Logic_Payment_PaymentRead();
        $logic->setApplicationUser($applicationUserModel);
        $logic->exec($buildParams);

        // 戻り値用の配列
        $returnArray = array();

        // 処理状態取得
        $applicationUserPayment = $logic->getApplicationUserPayment();
        if ($applicationUserPayment) {
            $returnArray = $this->_convertModelToAssociativeArray($applicationUserPayment, $this->_responsePayload[self::API_NAME_PAYMENT]);

            // プラットフォームの決済IDを返したい場合の処理
            $wkPlatformPaymentId = $logic->getPlatformPaymentIdBy($applicationUserPayment);
            if ($wkPlatformPaymentId) {
                $returnArray['platformPaymentId'] = $wkPlatformPaymentId;
            }

            // プラットフォーム個別に返却したい場合の処理
            $additionalParams = $logic->additionalParams();
            if ($additionalParams) {
                $returnArray = array_merge($returnArray, $additionalParams);
            }
        }

        // 残高情報の連想配列取得
        $balance = $logic->getBalance();

        if (isset($balance[Logic_Payment_Const::BALANCE])) {
            $returnArray[Logic_Payment_Const::BALANCE] = $balance[Logic_Payment_Const::BALANCE];
        }

        // 商品リストの連想配列取得
        $productItems = $logic->getProductItems();

        if ($productItems) {
            $returnArray[Logic_Payment_Const::ENTRY] = $productItems;
        }

        // 個別項目の処理
        //   paidWith
        //     項目があり、nullでなければその値を返却値に含める
        if (isset($buildParams['paidWith'])) {
            $returnArray['paidWith'] = $buildParams['paidWith'];
        }

        // 返却
        return $returnArray;
    }

    /**
     * 有償通貨をすべてのプラットフォーム合算値としないかどうかを判定します
     * 
     * @return boolean TRUE:としない
     *                  FALSE:とする
     */
    protected function _isNotCreditTotalingByAllPlatform()
    {
        return !$this->_isClassificationCreditTotaling();
    }

    private function _changeEntryFormatForLogic($bodyParam)
    {
        $buildParams = $bodyParam;

        // entryを抜き出しlogic用の形式に変更
        $payment = NULL;
        if (isset($bodyParam['entry'][0])) {
            $method                 = '_changeEntryFormatBy' . ucfirst($bodyParam['type']);
            $payment                = $this->$method($bodyParam['entry']);
            $buildParams['payment'] = $payment;
            if (isset($bodyParam['entry'][0]['published'])) {
                $buildParams['published'] = $bodyParam['entry'][0]['published'];
            }
            // 不要な entry キーを削除
            unset($buildParams['entry']);
        }

        if (!isset($buildParams['deviceId'])) {
            $buildParams['deviceId'] = '';
        }

        if (!isset($buildParams['ratingId'])) {
            $buildParams['ratingId'] = '';
        }

        return $buildParams;
    }

    private function _changeEntryFormatByCredit($entry)
    {
        $payment = array();

        foreach ($entry as $paymentObject) {
            $bonusItem = array();
            foreach ((array) $paymentObject[Logic_Payment_Const::REQUEST_OBJECT_CREDIT_BONUS] as $key => $value) {
                $bonusItem[] = array(
                    'toId'       => $key,
                    'toCurrency' => $value,
                );
            }
            $fromKeys = array_keys($paymentObject[Logic_Payment_Const::REQUEST_OBJECT_CREDIT]);
            $wk       = array(
                'productId'  => $paymentObject['productId'],
                'price'      => $paymentObject['price'],
                'name'       => $paymentObject['name'],
                'toId'       => $fromKeys[0],
                'toCurrency' => $paymentObject[Logic_Payment_Const::REQUEST_OBJECT_CREDIT][$fromKeys[0]],
            );
            if ($bonusItem) {
                $wk['bonusItem'] = $bonusItem;
            }
            if (isset($paymentObject['quantity'])) {
                $wk['quantity'] = $paymentObject['quantity'];
            }
            $payment[] = $wk;
        }

        return $payment;
    }

    private function _changeEntryFormatByBonus($entry)
    {
        $payment = array();

        foreach ($entry as $paymentObject) {
            foreach ($paymentObject[Logic_Payment_Const::REQUEST_OBJECT_BONUS] as $key => $value) {
                $payment[] = array(
                    'toId'       => $key,
                    'toCurrency' => $value,
                );
            }
        }

        return $payment;
    }

    private function _changeEntryFormatByExchange($entry)
    {
        $payment = array();

        foreach ($entry as $paymentObject) {

            $fromKeys = array_keys($paymentObject[Logic_Payment_Const::REQUEST_OBJECT_EXCHANGE][0]);
            $toKeys   = array_keys($paymentObject[Logic_Payment_Const::REQUEST_OBJECT_EXCHANGE][1]);

            $payment[] = array(
                'fromId'       => $fromKeys[0],
                'toId'         => $toKeys[0],
                'fromCurrency' => $paymentObject[Logic_Payment_Const::REQUEST_OBJECT_EXCHANGE][0][$fromKeys[0]],
                'toCurrency'   => $paymentObject[Logic_Payment_Const::REQUEST_OBJECT_EXCHANGE][1][$toKeys[0]],
            );
        }

        return $payment;
    }

    private function _changeEntryFormatByExchangeBonus($entry)
    {
        $payment = array();

        foreach ($entry as $paymentObject) {
            foreach ($paymentObject[Logic_Payment_Const::REQUEST_OBJECT_CREDIT_BONUS] as $key => $value) {
                $payment[] = array(
                    'fromId'       => $key,
                    'fromCurrency' => $value,
                );
            }
        }

        return $payment;
    }

    private function _changeEntryFormatByPayment($entry)
    {
        $payment = array();

        foreach ($entry as $paymentObject) {
            $paymentItems = array();
            foreach ($paymentObject[Logic_Payment_Const::REQUEST_OBJECT_PAYMENT] as $key => $value) {
                $paymentItems[] = array(
                    'fromId'       => $key,
                    'fromCurrency' => $value,
                );
            }
            $payment[] = array(
                'productId'    => $paymentObject['productId'],
                'quantity'     => $paymentObject['quantity'],
                'paymentItems' => $paymentItems,
            );
        }

        return $payment;
    }

    /**
     * bonusItemsに不正な値が入っていないか確認を行う
     * 
     * @param array $bonusItems bonusItemsパラメータ
     * @return boolean
     */
    protected function _validateBonusItems($bonusItems)
    {
        // bonusItemsが配列でなかった場合、不正とする
        if (!is_array($bonusItems)) {
            Common_Log::getInternalLog()->info(sprintf('bonusItems が連想配列ではありません：%s', print_r($bonusItems, 1)));
            return FALSE;
        }

        // 通貨額に 0 以下の値が入っていた場合、不正とする
        foreach ($bonusItems as $key => $value) {
            if ($value <= 0) {
                Common_Log::getInternalLog()->info(sprintf('bonusItems に不正なパラメータがあります(通貨額は 1 以上としてください)：%s | Key : %s, Value : %s', print_r($bonusItems, 1), $key, $value));
                return FALSE;
            }
        }

        return TRUE;
    }

}
