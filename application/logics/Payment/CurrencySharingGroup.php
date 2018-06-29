<?php
/**
 * Logic_Payment_CurrencySharingGroupクラスのファイル
 *
 * Logic_Payment_CurrencySharingGroupクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_CurrencySharingGroup
 *
 * 通貨共有グループ取り扱いクラス
 * 
 * Bootstarp.php あたりで下記 Zend_Registry されていることが前提
 * <pre>
 * // application.iniの設定を取得
 * $options        = new Zend_Config($this->getOptions());
 * $paymentConfig  = $options->payment->toArray();  // Payment API設定群
 * :
 * :
 * // 登録
 * Zend_Registry::set('apiPayment_config', $paymentConfig);
 * </pre>
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_CurrencySharingGroup
{
    /** @var string application.ini から設定を取得する際の有償/無償キー */
    const MODE_CREDIT = 'credit';

    /** @var string application.ini から設定を取得する際の有償/無償キー */
    const MODE_BONUS = 'bonus';

    /** @var string プラットフォーム情報の区切り文字 */
    const PLATFORM_INFO_DELIMITER = '_';

    /** @var string ペイメントプラットフォームIDの連想配列名 */
    const NAME_PLATFORM_ID = 'paymentPlatformId';

    /** @var string ペイメントデバイスIDの連想配列名 */
    const NAME_DEVICE_ID = 'paymentDeviceId';

    /** @var string ペイメントレーティングIDの連想配列名 */
    const NAME_RATING_ID = 'paymentRatingId';

    /** @var string グループキーが見つからなかった場合の例外メッセージ */
    const MSG_ERROR_SEARCH_GROUP_KEY = 'グループキーがヒットしませんでした。application.ini の設定値かパラメータが誤っています。:applicationId:%s | paymentType:%s | needle:%s';

    /** @var string application.ini 設定値参照用 */
    private $_applicationId;

    /** @var string ペイメント種別値保持用 */
    private $_paymentType;

    /** @var array 通貨共有グループ群保持用 */
    private $_groups = [];

    /**
     * 初期化
     * 
     * 通貨共有グループ設定を読み込み、プロパティに保持します。<br>
     * このメソッドを引数を変えて実行することによって、ループ時使用などを想定しています。
     * 
     * @param string $applicationId アプリケーションID
     * @param string $paymentType ペイメント種別値("credit" or "bonus")
     */
    public function init(string $applicationId, string $paymentType)
    {
        $this->_applicationId = $applicationId;
        $this->_paymentType   = $paymentType;

        $paymentConfig = Zend_Registry::get('apiPayment_config');

        // payment.appXXXXX.currencySharingGroup.credit または
        // payment.appXXXXX.currencySharingGroup.bonus の値をまるごと格納
        $app           = 'app' . $applicationId;
        $this->_groups = $paymentConfig[$app]['currencySharingGroup'][$paymentType];
    }

    /**
     * 通貨を共有するグループの設定値を取得します。(連想配列)
     * 
     * このメソッドは返却値を連想配列とするため、ソースの可読性に寄与します。
     * 
     * @param string $paymentPlatformId ペイメントプラットフォームID
     * @param string $paymentDeviceId ペイメントデバイスID
     * @param string $paymentRatingId ペイメントレーティングID
     * @return array
     */
    public function get(string $paymentPlatformId, string $paymentDeviceId = '', string $paymentRatingId = ''): array
    {
        // プラットフォーム情報を探すための探索針を生成
        $needle = $this->_generateNeedle($paymentPlatformId, $paymentDeviceId, $paymentRatingId);

        // 探索針を刺してプラットフォーム情報に該当する通貨共有グループキーを取得(0とか1とか)
        $groupKey = $this->_searchGroupKey($needle);

        // 通貨共有グループキーでプラットフォーム情報群を application.ini の設定値から取得
        $platformInfos = $this->_groups[$groupKey];

        // プラットフォーム情報の配列を連想配列化して名前付け
        return $this->_naming($platformInfos);
    }

    /**
     * プラットフォーム情報の配列を連想配列化します。
     * 
     * 数字添字ではなく名前がキーになるため、使いやすくなることを目的としています。
     * 
     * @param array $platformInfos
     * @return array
     */
    private function _naming(array $platformInfos): array
    {
        // 返却値
        $ret = [];

        foreach ($platformInfos as $platformInfo) {

            $wk = [];

            // application.ini の設定値は "_" で区切られているので、一旦分割する
            $separatedPlatformInfo = explode(self::PLATFORM_INFO_DELIMITER, $platformInfo);

            // ここは決め打ちせざるを得ない
            $wk[self::NAME_PLATFORM_ID] = $separatedPlatformInfo[0];

            // ペイメントデバイスID
            //   ない場合もあるので isset で確認する
            if (isset($separatedPlatformInfo[1])) {
                $wk[self::NAME_DEVICE_ID] = $separatedPlatformInfo[1];
            }

            // ペイメントレーティングID
            //   ない場合もあるので isset で確認する
            if (isset($separatedPlatformInfo[2])) {
                $wk[self::NAME_RATING_ID] = $separatedPlatformInfo[2];
            }

            $ret[] = $wk;
        }

        return $ret;
    }

    /**
     * グループキーを探します。
     * 
     * @param string $needle application.ini の設定値と一致するはずの探索針
     * @return string application.ini の設定値と $needle が一致した際のグループキー
     * @throws Common_Exception_IllegalParameter application.ini の設定に一致しなかった場合
     */
    private function _searchGroupKey(string $needle): string
    {
        foreach ($this->_groups as $groupKey => $platformInfos) {

            foreach ($platformInfos as $platformInfo) {
                if ($platformInfo === $needle) {
                    return $groupKey;
                }
            }
        }

        throw new Common_Exception_IllegalParameter(sprintf(self::MSG_ERROR_SEARCH_GROUP_KEY, $this->_applicationId, $this->_paymentType, $needle));
    }

    /**
     * application.ini からプラットフォーム情報を探すための探索針を生成します。
     * 
     * @param string $paymentPlatformId ペイメントプラットフォームID
     * @param string $paymentDeviceId   ペイメントデバイスID
     * @param string $paymentRatingId   ペイメントレーティングID
     * @return string 探索針
     */
    private function _generateNeedle(string $paymentPlatformId, string $paymentDeviceId = '', string $paymentRatingId = '')
    {
        $platformInfo   = [];
        $platformInfo[] = $paymentPlatformId;
        if (Common_Util_String::isNotEmpty($paymentDeviceId)) {
            $platformInfo[] = $paymentDeviceId;
        }
        if (Common_Util_String::isNotEmpty($paymentRatingId)) {
            $platformInfo[] = $paymentRatingId;
        }
        return implode(self::PLATFORM_INFO_DELIMITER, $platformInfo);
    }

}
