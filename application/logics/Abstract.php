<?php

/**
 * Logic_Abstractクラスのファイル
 *
 * Logic_Abstractクラスを定義している
 *
 * @category   Zend
 * @package    Logic
 * @version    $Id$
 */

/**
 * Logic_Abstract
 *
 * MISP基盤の基底クラス
 *
 * @category   Zend
 * @package    Logic
 * @method void setApplicationUserPaymentMapper(Application_Model_ApplicationUserPaymentMapper $mapper) Application_Model_ApplicationUserPaymentMapperのオブジェクトをセットします
 * @method Application_Model_ApplicationUserPaymentMapper getApplicationUserPaymentMapper(string $dbSectionName) Application_Model_ApplicationUserPaymentMapperのオブジェクトを返します
 * @method void setApplicationUserPaymentItemMapper(Application_Model_ApplicationUserPaymentItemMapper $mapper) アプリケーションユーザペイメントアイテムMapperのオブジェクトをセットします
 * @method Application_Model_ApplicationUserPaymentItemMapper getApplicationUserPaymentItemMapper(string $dbSectionName) アプリケーションユーザペイメントアイテムMapperのオブジェクトを返します
 * @method void setApplicationUserCurrencyMapper(Application_Model_ApplicationUserCurrencyMapper $mapper) アプリケーションユーザ通貨Mapperをセットします
 * @method Application_Model_ApplicationUserCurrencyMapper getApplicationUserCurrencyMapper(string $dbSectionName) アプリケーションユーザ通貨Mapperを返します
 * @method void setApplicationUserPaymentCancelLogMapper(Application_Model_ApplicationUserPaymentCancelLogMapper $mapper) アプリケーションユーザペイメントキャンセルログMapperをセットします
 * @method Application_Model_ApplicationUserPaymentCancelLogMapper getApplicationUserPaymentCancelLogMapper(string $dbSectionName) アプリケーションユーザペイメントキャンセルログMapperを返します
 * @method void setApplicationUser(Application_Model_ApplicationUser $model) アプリケーションユーザのオブジェクトをセットします
 * @method Application_Model_ApplicationUser getApplicationUser() アプリケーションユーザのオブジェクトを返します
 * @method void setApplicationUserPayment(Application_Model_ApplicationUserPayment $model) アプリケーションユーザペイメントのオブジェクトをセットします
 * @method Application_Model_ApplicationUserPayment getApplicationUserPayment() アプリケーションユーザペイメントのオブジェクトを返します
 * @method void setApplicationUserPaymentItem(Application_Model_ApplicationUserPaymentItem $model) アプリケーションユーザペイメントアイテムのオブジェクトをセットします
 * @method Application_Model_ApplicationUserPaymentItem getApplicationUserPaymentItem() アプリケーションユーザペイメントアイテムのオブジェクトを返します
 * @method void setApplicationUserTargetCurrencyPaymentItem(Application_Model_ApplicationUserTargetCurrencyPaymentItem $model) アプリケーションユーザターゲット通貨ペイメントアイテムのオブジェクトをセットします
 * @method Application_Model_ApplicationUserTargetCurrencyPaymentItem getApplicationUserTargetCurrencyPaymentItem() アプリケーションユーザターゲット通貨ペイメントアイテムのオブジェクトを返します
 * @method void setCurrencyPaymentLogic(Logic_Payment_Trade_CurrencyPayment $logic) 通貨消費ロジックをセットします
 * @method Logic_Payment_Trade_CurrencyPayment getCurrencyPaymentLogic() 通貨消費ロジックのオブジェクトを返します
 * @method void setApplicationLogic(Logic_Application $logic) アプリケーションロジックをセットします
 * @method Logic_Application getApplicationLogic() アプリケーションロジックのオブジェクトを返します
 * @method void setApplicationUserLogic(Logic_ApplicationUser $logic) アプリケーションユーザロジックをセットします
 * @method Logic_ApplicationUser getApplicationUserLogic() アプリケーションユーザロジックのオブジェクトを返します
 * @method void setApplicationUserTargetProductPaymentItemMapper(Application_Model_ApplicationUserTargetProductPaymentItemMapper $mapper) アプリケーションユーザターゲット商品ペイメントアイテムMapperをセットします
 * @method Application_Model_ApplicationUserTargetProductPaymentItemMapper getApplicationUserTargetProductPaymentItemMapper(string $dbSectionName) アプリケーションユーザターゲット商品ペイメントアイテムMapperを返します
 * @method void setApplicationUserCurrencyPaymentItemMapper(Application_Model_ApplicationUserCurrencyPaymentItemMapper $mapper) アプリケーションユーザ通貨ペイメントアイテムMapperをセットします
 * @method Application_Model_ApplicationUserCurrencyPaymentItemMapper getApplicationUserCurrencyPaymentItemMapper(string $dbSectionName) アプリケーションユーザ通貨ペイメントアイテムMapperを返します
 * @method void setApplicationUserTargetCurrencyPaymentItemMapper(Application_Model_ApplicationUserTargetCurrencyPaymentItemMapper $mapper) アプリケーションユーザターゲット通貨ペイメントアイテムMapperをセットします
 * @method Application_Model_ApplicationUserTargetCurrencyPaymentItemMapper getApplicationUserTargetCurrencyPaymentItemMapper(string $dbSectionName) アプリケーションユーザターゲット通貨ペイメントアイテムMapperを返します
 */
abstract class Logic_Abstract
{
    const ACCESSOR_GET = 'get';
    const ACCESSOR_SET = 'set';

    /**
     * ステータス：無効
     *
     * @var int STATUS_INACTIVE
     */
    const STATUS_INACTIVE = 0;

    /**
     * ステータス：有効
     *
     * @var int STATUS_ACTIVE
     */
    const STATUS_ACTIVE = 1;

    /**
     * ステータス：禁止
     *
     * @var int STATUS_BANNED
     */
    const STATUS_BANNED = 6;

    /**
     * contains置換子
     *
     * @var string FILTER_OP_CONTAINS_FORMAT
     */
    const FILTER_OP_CONTAINS_FORMAT = "%%%s%%";

    /**
     * コレクション定数名：アプリケーションユーザ通貨
     *
     * @var string COLLECTION_APPLICATION_USER_CURRENCY
     */
    const COLLECTION_APPLICATION_USER_CURRENCY = 'applicationUserCurrency';

    /**
     * ペイメント種別：購入
     *
     * @var int PAYMENT_TYPE_CREDIT
     */
    const PAYMENT_TYPE_CREDIT = 10;

    /**
     * ペイメント種別：ボーナス
     *
     * @var int PAYMENT_TYPE_BONUS
     */
    const PAYMENT_TYPE_BONUS = 11;

    /**
     * ペイメント種別：両替
     *
     * @var int PAYMENT_TYPE_EXCHANGE
     */
    const PAYMENT_TYPE_EXCHANGE = 20;

    /**
     * ペイメント種別：消費
     *
     * @var int PAYMENT_TYPE_PAYMENT
     */
    const PAYMENT_TYPE_PAYMENT = 30;

    /**
     * ペイメントステータス：開始
     *
     * @var int PAYMENT_STATUS_START
     */
    const PAYMENT_STATUS_START = 0;

    /**
     * ペイメントステータス：エラー
     *
     * @var int PAYMENT_STATUS_ERROR
     */
    const PAYMENT_STATUS_ERROR = 1;

    /**
     * ペイメントステータス：確認
     *
     * @var int PAYMENT_STATUS_CONFIRM
     */
    const PAYMENT_STATUS_CONFIRM = 2;

    /**
     * ペイメントステータス：注文
     *
     * @var int PAYMENT_STATUS_ORDER
     */
    const PAYMENT_STATUS_ORDER = 3;

    /**
     * ペイメントステータス：キャンセル
     *
     * @var int PAYMENT_STATUS_CANCEL
     */
    const PAYMENT_STATUS_CANCEL = 9;

    /**
     * ペイメントステータス：取消
     *
     * @var int PAYMENT_STATUS_VOID
     */
    const PAYMENT_STATUS_VOID = 11;

    /**
     * ペイメントステータス：完了
     *
     * @var int PAYMENT_STATUS_COMPLETE
     */
    const PAYMENT_STATUS_COMPLETE = 10;

    /**
     * 外部プラットフォーム連携インスタンスを取得する際のキーのプレフィックス(application.ini)
     *
     * @var string
     */
    const EXTERNAL_CONFIG_KEY_PREFIX = 'app';

    /**
     * @var array
     * マッパーインスタンス管理用
     */
    private $_mappers = array();

    /**
     * @var array
     * ロジックインスタンス管理用
     */
    private $_logics = array();

    /**
     * @var array
     * 汎用
     */
    private $_others = array();

    /**
     * DB登録/更新日時
     *
     * @var string 
     */
    protected $_nowDatetime = '';

    /**
     * @var array
     * Application_Model_ApplicationMapperのオブジェクトを格納した連想配列
     */
    protected $_applicationMapper = array();

    /**
     * @var array
     * Application_Model_ApplicationUserMapperのオブジェクトを格納した連想配列
     */
    protected $_applicationUserMapper = array();

    /**
     * @var array
     * Application_Model_PlatformMapperのオブジェクトを格納した連想配列
     */
    protected $_platformMapper = array();

    /**
     * @var array
     * Application_Model_PlatformUserMapperのオブジェクトを格納した連想配列
     */
    protected $_platformUserMapper = array();

    /**
     * @var array
     * Application_Model_UserPlatformApplicationRelationのオブジェクトを格納した連想配列
     */
    protected $_userPlatformApplicationRelationMapper = array();

    /**
     * @var array
     * Application_Model_UserMapperのオブジェクトを格納した連想配列
     */
    protected $_userMapper = array();

    /**
     * @var array
     * Application_Model_ApplicationRedirectUriのオブジェクトを格納した連想配列
     */
    protected $_applicationRedirectUriMapper = array();

    /**
     * @var array
     * コレクション格納配列
     */
    protected $_collections = array();

    /**
     * @var array
     * statusの許可パラメータリスト
     */
    protected $_statusAllowList = array(0, 1, 6);

    /**
     * 無償通貨扱いの有償通貨単価
     * 
     * @var string
     */
    protected $_bonusUnitPrice = null;

    /**
     * Application_Model_ApplicationMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_ApplicationMapper
     * Application_Model_ApplicationMapperのオブジェクト
     */
    public function getApplicationMapper($dbSectionName)
    {
        if (@!$this->{_applicationMapper}[$dbSectionName]) {
            @$this->{_applicationMapper}[$dbSectionName] = new Application_Model_ApplicationMapper($dbSectionName);
        }
        return @$this->{_applicationMapper}[$dbSectionName];
    }

    /**
     * Application_Model_ApplicationMapperのオブジェクトをセットします
     *
     * @param array $mapper
     * Application_Model_ApplicationMapperのオブジェクトが入った配列
     * @return array
     * Application_Model_ApplicationMapperのオブジェクトが入った配列
     */
    public function setApplicationMapper($mapper)
    {
        $this->_applicationMapper = $mapper;
        return $this->_applicationMapper;
    }

    /**
     * Application_Model_ApplicationUserMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_ApplicationUserMapper
     * Application_Model_ApplicationUserMapperのオブジェクト
     */
    public function getApplicationUserMapper($dbSectionName)
    {
        if (@!$this->{_applicationUserMapper}[$dbSectionName]) {
            @$this->{_applicationUserMapper}[$dbSectionName] = new Application_Model_ApplicationUserMapper($dbSectionName);
        }
        return @$this->{_applicationUserMapper}[$dbSectionName];
    }

    /**
     * Application_Model_ApplicationUserMapperのオブジェクトをセットします
     *
     * @param array $mapper
     * Application_Model_ApplicationUserMapperのオブジェクトが入った配列
     * @return array
     * Application_Model_ApplicationUserMapperのオブジェクトが入った配列
     */
    public function setApplicationUserMapper($mapper)
    {
        $this->_applicationUserMapper = $mapper;
        return $this->_applicationUserMapper;
    }

    /**
     * Application_Model_PlatformMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_PlatformMapper
     * Application_Model_PlatformMapperのオブジェクト
     */
    public function getPlatformMapper($dbSectionName)
    {
        if (@!$this->{_platformMapper}[$dbSectionName]) {
            @$this->{_platformMapper}[$dbSectionName] = new Application_Model_PlatformMapper($dbSectionName);
        }
        return @$this->{_platformMapper}[$dbSectionName];
    }

    /**
     * Application_Model_PlatformMapperのオブジェクトをセットします
     *
     * @param array $mapper
     * Application_Model_PlatformMapperのオブジェクトが入った配列
     * @return array
     * Application_Model_PlatformMapperのオブジェクトが入った配列
     */
    public function setPlatformMapper($mapper)
    {
        $this->_platformMapper = $mapper;
        return $this->_platformMapper;
    }

    /**
     * Application_Model_PlatformUserMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_PlatformUserMapper
     * Application_Model_PlatformUserMapperのオブジェクト
     */
    public function getPlatformUserMapper($dbSectionName)
    {
        if (@!$this->{_platformUserMapper}[$dbSectionName]) {
            @$this->{_platformUserMapper}[$dbSectionName] = new Application_Model_PlatformUserMapper($dbSectionName);
        }
        return @$this->{_platformUserMapper}[$dbSectionName];
    }

    /**
     * Application_Model_PlatformUserMapperのオブジェクトをセットします
     *
     * @param array $mapper
     * Application_Model_PlatformUserMapperのオブジェクトが入った配列
     * @return array
     * Application_Model_PlatformUserMapperのオブジェクトが入った配列
     */
    public function setPlatformUserMapper($mapper)
    {
        $this->_platformUserMapper = $mapper;
        return $this->_platformUserMapper;
    }

    /**
     * Application_Model_UserPlatformApplicationRelationMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_UserPlatformApplicationRelationMapper
     * Application_Model_UserPlatformApplicationRelationMapperのオブジェクト
     */
    public function getUserPlatformApplicationRelationMapper($dbSectionName)
    {
        if (@!$this->{_userPlatformApplicationRelationMapper}[$dbSectionName]) {
            @$this->{_userPlatformApplicationRelationMapper}[$dbSectionName] = new Application_Model_UserPlatformApplicationRelationMapper($dbSectionName);
        }
        return @$this->{_userPlatformApplicationRelationMapper}[$dbSectionName];
    }

    /**
     * Application_Model_UserPlatformApplicationRelationMapperのオブジェクトをセットします
     *
     * @param array $mapper
     * Application_Model_UserPlatformApplicationRelationMapperのオブジェクトが入った配列
     * @return array
     * Application_Model_UserPlatformApplicationRelationMapperのオブジェクトが入った配列
     */
    public function setUserPlatformApplicationRelationMapper($mapper)
    {
        $this->_userPlatformApplicationRelationMapper = $mapper;
        return $this->_userPlatformApplicationRelationMapper;
    }

    /**
     * Application_Model_ApplicationUserPlatformRelationMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_ApplicationUserPlatformRelationMapper
     * Application_Model_ApplicationUserPlatformRelationMapperのオブジェクト
     */
    public function getApplicationUserPlatformRelationMapper($dbSectionName)
    {
        if (@!$this->{_applicationUserPlatformRelationMapper}[$dbSectionName]) {
            @$this->{_applicationUserPlatformRelationMapper}[$dbSectionName] = new Application_Model_ApplicationUserPlatformRelationMapper($dbSectionName);
        }
        return @$this->{_applicationUserPlatformRelationMapper}[$dbSectionName];
    }

    /**
     * Application_Model_ApplicationUserPlatformRelationMapperのオブジェクトをセットします
     *
     * @param array $mapper
     * Application_Model_ApplicationUserPlatformRelationMapperのオブジェクトが入った配列
     * @return array
     * Application_Model_ApplicationUserPlatformRelationMapperのオブジェクトが入った配列
     */
    public function setApplicationUserPlatformRelationMapper($mapper)
    {
        $this->_applicationUserPlatformRelationMapper = $mapper;
        return $this->_applicationUserPlatformRelationMapper;
    }

    /**
     * Application_Model_ApplicationRedirectUriMapperのオブジェクトをセットします
     *
     * @param Application_Model_ApplicationRedirectUriMapper $mapper Application_Model_ApplicationRedirectUriMapperのオブジェクト
     * @return Application_Model_ApplicationUserPlatformRelationMapperオブジェクト
     */
    public function setApplicationRedirectUriMapper($mapper)
    {
        $this->_applicationRedirectUriMapper = $mapper;
        return $this->_applicationRedirectUriMapper;
    }

    /**
     * Application_Model_ApplicationRedirectUriMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_ApplicationRedirectUriMapper
     * Application_Model_ApplicationRedirectUriMapperのオブジェクト
     */
    public function getApplicationRedirectUriMapper($dbSectionName)
    {
        if (@!$this->{_applicationRedirectUriMapper}[$dbSectionName]) {
            @$this->{_applicationRedirectUriMapper}[$dbSectionName] = new Application_Model_ApplicationRedirectUriMapper($dbSectionName);
        }
        return @$this->{_applicationRedirectUriMapper}[$dbSectionName];
    }

    /**
     * Application_Model_UserMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_UserMapper
     * Application_Model_UserMapperのオブジェクト
     */
    public function getUserMapper($dbSectionName)
    {
        if (@!$this->{_userMapper}[$dbSectionName]) {
            @$this->{_userMapper}[$dbSectionName] = new Application_Model_UserMapper($dbSectionName);
        }
        return @$this->{_userMapper}[$dbSectionName];
    }

    /**
     * Application_Model_PlatformUserMapperのオブジェクトをセットします
     *
     * @param array $mapper
     * Application_Model_PlatformUserMapperのオブジェクトが入った配列
     * @return array
     * Application_Model_PlatformUserMapperのオブジェクトが入った配列
     */
    public function setUserMapper($mapper)
    {
        $this->_userMapper = $mapper;
        return $this->_userMapper;
    }

    /**
     * パラメータのチェックを行う
     *
     * @param string $value チェック対象のパラメータ値
     * @param int $checkCount チェックする文字列の長さ、デフォルトは255($checkCount+1)
     */
    protected function _isValidateValue($value, $checkCount = 255)
    {
        // チェック対象の文字数を取得
        $mbStringLength = mb_strlen($value);

        // 空かどうかのチェック
        if (!$mbStringLength) {
            throw new Common_Exception_IllegalParameter('空のパラメータがあります');
        }

        // 指定文字数を超えるかチェック
        if ($mbStringLength > $checkCount) {
            throw new Common_Exception_IllegalParameter($checkCount . '文字を超えるパラメータがあります');
        }

        // 問題ない場合、TRUEを返す
        return TRUE;
    }

    /**
     * パラメータのチェックを行う
     *
     * 文字数チェック
     *
     * @param string $value チェック対象のパラメータ値
     * @param int $checkCount チェックする文字列の長さ、デフォルトは255($checkCount+1)
     */
    protected function _isValidateLength($value, $checkCount = 255)
    {
        // 指定文字数を超えるかチェック
        if (strlen($value) > $checkCount) {
            throw new Common_Exception_IllegalParameter($checkCount . '文字を超えるパラメータがあります');
        }

        // 問題ない場合、TRUEを返す
        return TRUE;
    }

    /**
     * Updateに必要なパラメータを連想配列の形で取得する
     *
     * @param array $updateList 更新するパラメータ名のリスト
     * @param mixed $model 更新情報の入ったモデル
     * @return array Updateパラメータの連想配列
     */
    protected function _getUpdateParams($updateList, $model)
    {
        // 返却用の配列
        $response = array();

        foreach ($updateList as $key) {
            $getMethod = 'get' . Common_Util_String::snakeToCamel($key);
            $val       = $model->$getMethod();
            if (strlen($val)) {
                $response[$key] = $val;
            }
        }

        return $response;
    }

    /**
     * 現在時刻の日付文字列を返します
     * かつ、$_nowDatetimeに取得した日付をセットします
     * 
     * @return string
     */
    public function getNowDatetime()
    {
        $this->_nowDatetime = date("Y-m-d H:i:s");
        return $this->_nowDatetime;
    }

    /**
     * プラットフォームIDとデバイスIDの組み合わせが正しいかを確認する
     *
     * @param array $params platformIdとdeviceIdの連想配列
     * @param string $expectPlatformId 期待するプラットフォームID
     * @param string $expectDeviceId 期待するデバイスID
     * @return boolean
     */
    public static function isPlatformDevicePair(array $params, $expectPlatformId, $expectDeviceId = '')
    {
        $platformId = $params['platformId'];
        $deviceId   = isset($params['deviceId']) ? $params['deviceId'] : '';

        switch ($platformId) {

            case $expectPlatformId:

                switch ($deviceId) {
                    case $expectDeviceId:
                        return TRUE;
                }
        }

        return FALSE;
    }

    /**
     * アクセサマジックメソッド
     *
     * 下記用途のアクセサに対応しています。
     *
     * <ul>
     *   <li>ロジックアクセサ
     *     <ul>
     *       <li>ルール
     *         <ul>
     *           <li>"set"か"get"で始まり、"Logic"で終わるメソッド呼び出し<br>例：setHogeLogic()
     *         </ul>
     *     </ul>
     *   <li>マッパーアクセサ
     *     <ul>
     *       <li>ルール
     *         <ul>
     *           <li>"set"か"get"で始まり、"Mapper"で終わるメソッド呼び出し<br>例：setApplicationMapper()
     *         </ul>
     *     </ul>
     * </ul>
     *
     * @param string $name 呼び出されたメソッド名
     * @param array $arguments 呼び出されたメソッドに渡された引数
     * @return object getter呼び出しの場合は対応するオブジェクト
     * @throws Common_Exception_NotFound ルールに合致しなかった場合にThrowされます
     */
    public function __call($name, $arguments)
    {
        // アクセサ確認
        $matches = array();
        preg_match('/(set|get)(.+)/', $name, $matches);
        if (!$matches) {
            // そもそも set / get はじまりでなければ例外とする
            throw new BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $name));
        }

        $accessor = $matches[1]; // "set" or "get"
        $mainName = $matches[2]; // "set" or "get" から後ろの部分(本体)
        // 本体確認
        preg_match('/(set|get)(.+)(Mapper|Logic)/', $name, $matches);

        // 本体部分の後ろが "Mapper" or "Logic" で終わっていない場合は通常のアクセサ(モデルとかコレクション)コールとする
        if (!$matches) {
            return $this->_normalAccessorCall($accessor, $mainName, $arguments);
        }

        $type     = $matches[3]; // "Mapper" or "Logic"
        $callName = $matches[2]; // マッパー/ロジック名


        switch ($type) {

            // Mapper
            case 'Mapper':
                switch ($accessor) {

                    // setter
                    case self::ACCESSOR_SET:

                        $this->_mappers[$callName] = $arguments[0];
                        break;

                    // getter
                    case self::ACCESSOR_GET:

                        if (!isset($this->_mappers[$callName][$arguments[0]])) {
                            $clazz                                    = 'Application_Model_' . $callName . $type;
                            $this->_mappers[$callName][$arguments[0]] = new $clazz($arguments[0]);
                        }
                        return $this->_mappers[$callName][$arguments[0]];

                    default:

                        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $name));
                }
                break;

            // Logic
            case 'Logic':
                switch ($accessor) {

                    // setter
                    case self::ACCESSOR_SET:

                        $this->_logics[$callName] = $arguments[0];
                        break;

                    // getter
                    case self::ACCESSOR_GET:

                        return $this->_logics[$callName];

                    default:

                        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $name));
                }
                break;

            default:

                throw new BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $name));
        }
    }

    /**
     * "Mapper" or "Logic" で終わっていない場合は通常のアクセサ(モデルとかコレクション)コールとして処理する
     *
     * ※本来はスコープもチェックすべきですが、外部公開せずMISP内で完結する機能なのでオミットしています
     *
     * @param string $accessor "set" または "get"
     * @param string $mainName "set" または "get" から後ろの部分(本体)
     * @param array $arguments 本来コールしていたメソッドへの引数
     * @return \Logic_Abstract
     * @throws BadMethodCallException 存在しなかった場合にThrowされます
     */
    protected function _normalAccessorCall($accessor, $mainName, $arguments)
    {
        switch ($accessor) {

            // setter
            case self::ACCESSOR_SET:

                $this->_others[$mainName] = $arguments[0];
                return $this;

            // getter
            case self::ACCESSOR_GET:

                // プロパティ未登録ならNG
                if (!isset($this->_others[$mainName])) {
                    break;
                }

                return $this->_others[$mainName];

            default:
                break;
        }

        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $accessor . $mainName));
    }

    protected function _getStdLogFormat($exc = NULL)
    {
        $format = '[CLASS:%s][METHOD:%s][LINE:%s][MESSAGE:%s]';
        if ($exc instanceof Exception) {
            $f = '[Exception:%s]';
            $format .= sprintf($f, get_class($exc));
        }

        return $format;
    }

    protected function _logInfo($class, $method, $line, $message)
    {
        Common_Log::getInternalLog()->info(sprintf($this->_getStdLogFormat(), $class, $method, $line, $message));
    }

    protected function _logError(Exception $exc, $class, $method, $line, $message)
    {
        Common_Log::getExceptionLog()->setException($exc);
        Common_Log::getExceptionLog()->error(sprintf($this->_getStdLogFormat($exc), $class, $method, $line, $message));
    }

    /**
     * MZCLのレスポンスコレクション(エラー)の内容から、適切な例外をthrowする
     *
     * @param Common_External_Platform_Model_Collection_Abstract $responseCollection MZCLのレスポンスコレクション(エラー)
     * @param string $errorMessage エラーメッセージ
     * @throws Exception 各種例外をthrowする
     */
    protected function _throwExceptionByMzclResponseCollection(Common_External_Platform_Model_Collection_Abstract $responseCollection, $errorMessage = NULL)
    {
        // $responseCollectionからステータスコードをセット
        $statusCode = $responseCollection->getResponseCode();
        // 引数の$errorMessageが空の場合、$responseCollectionのエラーメッセージを使用する
        if (!$errorMessage) {
            $errorMessage = $responseCollection->getErrorMessage();
        }
        $exception = 'Exception';

        switch ($statusCode) {
            case 304;
                $exception = 'Common_Exception_NotModified';
                break;
            case 400:
                $exception = 'Common_Exception_IllegalParameter';
                break;
            case 401:
                // 認証エラー
                $exception = 'Common_Exception_AuthenticationFailed';
                break;
            case 402:
                // 残高不足エラー
                $exception = 'Common_Exception_InsufficientFunds';
                break;
            case 403:
                $exception = 'Common_Exception_Forbidden';
                break;
            case 404:
                $exception = 'Common_Exception_NotFound';
                break;
            case 405:
                $exception = 'Common_Exception_MethodNotAllowed';
                break;
            case 406:
                // メソッドで許可されていないAPIモードが設定されている場合のステータスコード
                $exception = 'Common_Exception_NotAcceptable';
                break;
            case 409:
                $exception = 'Common_Exception_AlreadyExists';
                break;
            case 412:
                // 前提条件に一致しない場合のステータスコード
                //   例) 異なるプラットフォームで処理していた決済情報が存在する場合のエラー
                $exception = 'Common_Exception_PreconditionFailed';
                break;
            case 422:
                // レシート検証エラー
                $exception = 'Common_Exception_Verify';
                break;
            default :
                break;
        }

        // 例外とエラーメッセージをthrowする
        throw new $exception($errorMessage, $statusCode);
    }

    /**
     * 無償通貨扱いの有償通貨単価を取得します。
     * 
     * application.ini に該当の設定がなかったり、設定値が無効な場合は int 0 を返却します。
     * これは「無償通貨は単価ゼロ」という互換性便宜のためです。
     * 
     * @return mixed 無償通貨扱いの有償通貨単価
     */
    public function getBonusUnitPrice($applicationId = NULL, $platformId = NULL, $deviceId = NULL, $ratingId = NULL)
    {
        $applicationId = $applicationId ?? $this->getApplicationUser()->getApplicationId();
        $platformId    = $platformId ?? $this->pickUpPlatformId();
        $deviceId      = $deviceId ?? $this->pickUpDeviceId();
        $ratingId      = $ratingId ?? $this->pickUpRatingId();

        $platformConfigName = Logic_Payment_FactoryAbstract::getPlatformConfigName($platformId, $deviceId, $ratingId);
        $config             = Zend_Registry::get('apiPayment_config');
        $appConfigKey       = 'app' . $applicationId;
        // 特に重い処理でもないのでシングルトン的にはしない
        if (!isset($config[$appConfigKey][$platformConfigName]['bonusUnitPrice'])) {
            return 0;
        }
        $this->_bonusUnitPrice = $config[$appConfigKey][$platformConfigName]['bonusUnitPrice'];

        return $this->_bonusUnitPrice;
    }

}
