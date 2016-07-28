<?php

/**
 * Common_Util_Dateクラスのファイル
 * 
 * Common_Util_Dateクラスを定義している
 *
 * @package Common_Util
 */

/**
 * Common_Util_Date
 * 
 * 日付君
 *
 * @package Common_Util
 */
class Common_Util_Date
{

    /**
     * RFC1123形式の日付文字列を、任意のフォーマット(デフォルト：Y-m-d H:i:s)に変換します。
     * 
     * RFC1123形式<br>
     * <pre>
     * Mon, 23 Mar 2015 08:24:36 GMT
     * <pre>
     * 
     * @param string $date RFC1123形式の日付文字列
     * @param string $format 変換したい日付フォーマット(PHPのdate関数などに使う書式)
     * @return string 変換後の日付文字列
     * @throws Common_Exception_IllegalParameter $dateがRFC1123形式ではない場合にThrowします。
     * @see <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html">RFC2616 からのrfc1123-date部分</a>
     */
    public static function convertFromRFC1123($date, $format = 'Y-m-d H:i:s')
    {
        // RFC1123はGMTなので、ロケールをUSで処理させる
        $locale = new Zend_Locale('en_US');

        // マニュアルに書かれているとおり、isDateで日付形式のチェックを行う
        //   http://framework.zend.com/manual/1.12/ja/zend.date.additional.html
        // RFC1123の形式でなければ、パラメータ不正例外を投げる
        if (!Zend_Date::isDate($date, Zend_Date::RFC_1123, $locale)) {
            throw new Common_Exception_IllegalParameter(sprint('日付文字列がRFC_1123形式ではありませんでした。(%s)', $date));
        }

        Zend_Date::setOptions(array('format_type' => 'php'));
        $d = new Zend_Date($date, Zend_Date::RFC_1123, $locale);
        // GMTはグリニッジ標準時なので、日本ロケールでの差分秒をdate('Z')で算出し、
        // 秒加算する
        $d->addSecond(date('Z'));

        // 一旦変換
        $convertedDate = $d->toString($format);
        // 変更したフォーマットを元に戻す
        Zend_Date::setOptions(array('format_type' => 'iso'));

        return $convertedDate;
    }

}
