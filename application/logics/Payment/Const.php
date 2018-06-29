<?php

/**
 * Logic_Payment_Constクラスのファイル
 *
 * Logic_Payment_Constクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_Const
 *
 * 仮想通貨定数クラス
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_Const extends Logic_Const
{

    /**
     * プラットフォームID MooG Games
     *
     * @var string
     */
    const PLATFORM_ID_MOOG = 'moog';

    /**
     * デバイスID 空文字(プラットフォーム側にデバイス区別がない場合に使用する)
     *
     * @var string
     */
    const DEVICE_ID_BLANK = '';

    /**
     * モード：有償
     *
     * @var int
     */
    const MODE_CREDIT = 0;

    /**
     * モード：無償
     *
     * @var int
     */
    const MODE_BONUS = 1;

    /**
     * モード：全部(有償無償の区別をしない)
     *
     * @var int
     */
    const MODE_ALL = 2;

    /**
     * ペイメントステータス：開始
     * 
     * @var int
     */
    const PAYMENT_STATUS_START = 0;

    /**
     * ペイメントステータス：エラー
     * 
     * @var int
     */
    const PAYMENT_STATUS_ERROR = 1;

    /**
     * ペイメントステータス：確認
     * 
     * @var int
     */
    const PAYMENT_STATUS_CONFIRM = 2;

    /**
     * ペイメントステータス：注文
     * 
     * @var int
     */
    const PAYMENT_STATUS_ORDER = 3;

    /**
     * ペイメントステータス：完了
     * 
     * @var int
     */
    const PAYMENT_STATUS_COMPLETE = 10;

    /**
     * ペイメントステータス：キャンセル
     * 
     * @var int
     */
    const PAYMENT_STATUS_CANCEL = 9;

    /**
     * ペイメントステータス：決済取消
     * 
     * @var int
     */
    const PAYMENT_STATUS_VOID = 11;

    /**
     * ペイメント種別：credit
     * 
     * @var int
     */
    const PAYMENT_TYPE_CREDIT = 10;

    /**
     * ペイメント種別：bonus
     * 
     * @var int
     */
    const PAYMENT_TYPE_BONUS = 11;

    /**
     * ペイメント種別：bonus
     * 
     * @var string
     */
    const PAYMENT_TYPE_BONUS_STRING = 'bonus';

    /**
     * ペイメント種別：exchange
     * 
     * @var int
     */
    const PAYMENT_TYPE_EXCHANGE = 20;

    /**
     * ペイメント種別：payment
     * 
     * @var int
     */
    const PAYMENT_TYPE_PAYMENT                          = 30;

    /**
     * 無償単価
     * 
     * @var int
     */
    const UNIT_PRICE_BONUS = 0;

    /**
     * 無償価格
     * 
     * @var int
     */
    const PRICE_BONUS = 0;

    /**
     * 両替価格
     * (両替では価格は不要なのでデフォルト値を設定)
     * 
     * @var int
     */
    const PRICE_EXCHANGE = 0;

    /**
     * 残高
     * 
     * @var string
     */
    const BALANCE = 'balance';

    /**
     * エントリ
     * 
     * @var string
     */
    const ENTRY = 'entry';

    /**
     * 有償残高
     * 
     * @var string
     */
    const CREDIT_BALANCE = 'creditItems';

    /**
     * 無償残高
     * 
     * @var string
     */
    const BONUS_BALANCE = 'bonusItems';

    /**
     * 購入オブジェクト名
     * 
     * @var string
     */
    const REQUEST_OBJECT_CREDIT = 'creditItem';

    /**
     * 購入オブジェクト名
     * 
     * @var string
     */
    const REQUEST_OBJECT_CREDIT_BONUS = 'bonusItem';

    /**
     * 無償オブジェクト名
     * 
     * @var string
     */
    const REQUEST_OBJECT_BONUS = 'bonusItems';

    /**
     * 両替オブジェクト名
     * 
     * @var string
     */
    const REQUEST_OBJECT_EXCHANGE = 'exchangeItems';

    /**
     * 消費オブジェクト名
     * 
     * @var string
     */
    const REQUEST_OBJECT_PAYMENT = 'paymentItems';

    /**
     * 同じユーザのペイメント処理が検出された場合のログメッセージ定義
     * 
     * @var string
     */
    const LOG_MSG_ALREADY_PAYMENT_PROCESS = '同じユーザのペイメント処理が検出されました：';

    /**
     * 不正なレシートの場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_ILLEGAL_RECEIPT = '不正なレシートです';

    /**
     * 不正なレシートの場合のログメッセージ定義
     * 
     * @var string
     */
    const LOG_MSG_ILLEGAL_RECEIPT = '不正なレシートです：';

    /**
     * 不要なレシートデータがある場合のログメッセージ定義
     * 
     * @var string
     */
    const LOG_MSG_CANCEL_LOG_IS_NOT_FOUND = 'キャンセルログにありません：';

    /**
     * 必須入力パラメータが無い場合のメッセージ定義
     * 
     * @var string
     */
    const LOG_MSG_REQUIRED = '必須入力パラメータがありません：';

    /**
     * すでに使用されているレシートの場合のログメッセージ定義
     * 
     * @var string
     */
    const LOG_MSG_ALREADY_USED_RECEIPT = 'すでに使用されているレシートです：';

    /**
     * 残高不足時のログメッセージ定義
     * 
     * @var string
     */
    const MSG_INSUFFICIENTFUNDS = '残高不足です';

    /**
     * アクセストークン取得失敗の場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_FAILED_GET_ACCESSTOKEN = 'アクセストークン取得に失敗しました';

    /**
     * アクセストークン検証失敗の場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_INVALID_ACCESSTOKEN = 'アクセストークン検証に失敗しました';

    /**
     * カタログが取得できていない場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_FAILED_GET_CATALOG = 'カタログの取得に失敗しました';

    /**
     * カタログが取得できていない場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_FAILED_NOT_PRODUCT = '未購入です';

    /**
     * 決済処理取得失敗の場合のメッセージ定義
     * 
     * @var string
     */
    const LOG_MSG_FAILED_PAYMENT_NOT_FOUND = '決済処理が見つかりませんでした：';

    /**
     * プラットフォームの決済処理が見つからなかった場合のメッセージ定義
     * 
     * @var string
     */
    const LOG_MSG_FAILED_PLATFORM_PAYMENT_NOT_FOUND = 'プラットフォームの決済処理が見つかりませんでした：';

    /**
     * 不正な商品の場合のメッセージ定義
     * 
     * @var string
     */
    const LOG_MSG_ILLEGAL_PRODUCT_ITEM = '不正な商品です：';

    /**
     * すでに使用されている決済情報の場合のログメッセージ定義
     * 
     * @var string
     */
    const LOG_MSG_ALREADY_USED_PAYMENT = 'すでに使用されている決済情報です：';

    /**
     * 決済情報ステータスが完了していない場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_FAILED_PAYMENT_STATUS = '決済情報ステータスが完了していません';

    /**
     * プラットフォームの決済が完了していない場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_FAILED_PLATFORM_PAYMENT_STATUS = 'プラットフォームの決済情報ステータスが完了していません';

    /**
     * 検証系の処理でNGがあった場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_FAILED_VERIFY = '検証に失敗しました';

    /**
     * プラットフォームの検証系の処理でNGがあった場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_FAILED_PLATFORM_VERIFY = 'プラットフォームの検証に失敗しました';

    /**
     * プラットフォームの決済情報登録に失敗した場合のメッセージ定義
     * 
     * @var string
     */
    const MSG_FAILED_PLATFORM_PAYMENT_REGISTER = 'プラットフォームの決済情報登録に失敗しました';

    /**
     * 通貨使用順序のデフォルト設定
     * 
     * @var string
     */
    const DEFAULT_CURRENCY_PAYMENT_SEQUENCE = 'credit,pf_bonus,common_bonus';

    /**
     * 通貨使用順序ラベル：有償通貨
     *
     * @var string
     */
    const CURRENCY_PAYMENT_SEQUENCE_CREDIT = 'credit';

    /**
     * 通貨使用順序ラベル：プラットフォーム固有無償通貨
     *
     * @var string
     */
    const CURRENCY_PAYMENT_SEQUENCE_PF_BONUS = 'pf_bonus';

    /**
     * 通貨使用順序ラベル：プラットフォーム共通無償通貨
     *
     * @var string
     */
    const CURRENCY_PAYMENT_SEQUENCE_BONUS = 'common_bonus';

    /**
     * 有償通貨・無償通貨の限定消費指定値：有償通貨
     * 
     * @var string
     */
    const PAID_WITH_CREDIT = 'credit';

    /**
     * 有償通貨・無償通貨の限定消費指定値：無償通貨
     * 
     * @var string
     */
    const PAID_WITH_BONUS = 'bonus';

    /**
     * 有償通貨・無償通貨の限定消費指定値の許可リスト
     * 
     * @var string[]
     */
    const PAID_WITH_ALLOWS = [self::PAID_WITH_CREDIT, self::PAID_WITH_BONUS];

    /**
     * 有償通貨・無償通貨の限定消費指定値の除外キーリスト
     * 
     * @var array
     */
    const PAID_WITH_FOR_OMMIT_PAYMENT_SEQUENCE = [
        self::PAID_WITH_CREDIT => [self::CURRENCY_PAYMENT_SEQUENCE_BONUS, self::CURRENCY_PAYMENT_SEQUENCE_PF_BONUS],
        self::PAID_WITH_BONUS  => [self::CURRENCY_PAYMENT_SEQUENCE_CREDIT]
    ];

    /**
     * 【月次償却対応】翌月1日0時 フォーマットにするための定数(マーカー)
     * 
     * @var string
     */
    const EXPIRED_MONTH_ADDITIONAL_FORMAT_NEXTMONTH = 'nextmonth';

    /**
     * 履歴種別：購入
     * 
     * @var string
     */
    const HISTORY_TYPE_CREDIT = 'credit';

    /**
     * 履歴種別：キャンセル
     * 
     * @var string
     */
    const HISTORY_TYPE_CANCEL = 'cancel';

    /**
     * 履歴種別リスト
     * 
     * @var array {
     *   @type string 履歴種別
     * }
     */
    const HISTORY_TYPE_LIST = [
        self::HISTORY_TYPE_CANCEL,
        self::HISTORY_TYPE_CREDIT,
    ];

    /**
     * DBのペイメント種別値(int)をAPIレイヤーの値(string)に変換します
     * 
     * @param int $paymentType
     * @return string
     */
    static public function convertPaymentTypeToType($paymentType)
    {
        $mapping = self::_getPaymentTypesMapping();
        return $mapping[$paymentType];
    }

    /**
     * DBのペイメント種別値(int)とAPIレイヤーの値(string)のマッピングを返します
     * 
     * PHPは(連想)配列定数を持てないための苦肉の策
     * 
     * @return array
     */
    static private function _getPaymentTypesMapping()
    {
        $a                              = array();
        $a[self::PAYMENT_TYPE_CREDIT]   = 'credit';
        $a[self::PAYMENT_TYPE_BONUS]    = 'bonus';
        $a[self::PAYMENT_TYPE_EXCHANGE] = 'exchange';
        $a[self::PAYMENT_TYPE_PAYMENT]  = 'payment';
        return $a;
    }

}
