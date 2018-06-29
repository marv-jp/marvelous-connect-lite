<?php

/**
 * Common_Json クラスのファイル
 * 
 * Common_Json クラスを定義している
 *
 * @package Common
 */

/**
 * Common_Json
 * 
 * Zend_Json クラスのラッパークラス
 *
 * @package Common
 */
class Common_Json
{

    /**
     * Zend_Json#decode のラッパークラス
     * 
     * PHP7 での Zend_Json::decode は空文字(or NULL)を渡すと例外を Throw します。<br>
     * この動作は後方互換性を損なうため、下記のようにオリジナルをラップします。<br>
     * <br>
     * ■ 空文字が渡された場合
     * 
     *    null を返却します。
     *    →PHP7 では 空文字列やNULLを渡すと json_last_error() 関数がエラー値(4)を返し、
     *      その結果 Zend_Json::decode() が例外を Throw してしまうため、PHP5 での互換挙動のために null を返却します。
     *      null であれば、連想配列参照コードであっても PHP Warning が発生しないからです。(空文字では Warning が発生してしまう)
     *
     * @param string $json デコード対象のJSON文字列
     * @param int $objectDecodeType Optional; flag indicating how to decode objects. See {@link Zend_Json_Decoder::decode()} for details.
     * @return mixed デコード後の連想配列(orオブジェクト) または null
     */
    public static function decode($json, $objectDecodeType = Zend_Json::TYPE_ARRAY)
    {
        // 引数はいじらないので別変数にコピー
        $target = $json;
        // PHP7 では 空文字列やNULLを渡すと json_last_error() 関数がエラー値(4)を返し、
        // その結果 Zend_Json::decode() が例外を Throw してしまうため、PHP5 での互換挙動のために null を返却する
        if (Common_Util_String::isEmpty($json)) {
            return null;
        }

        // 普通にZendライブラリをコール
        return Zend_Json::decode($target, $objectDecodeType);
    }

}
