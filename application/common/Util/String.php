<?php

/**
 * Common_Util_Stringクラスのファイル
 * 
 * Common_Util_Stringクラスを定義している
 *
 * @package    Common_Util
 */

/**
 * Common_Util_String
 * 
 * 文字列君
 *
 * @package    Common_Util
 */
class Common_Util_String
{
    /**
     * いわゆるテキストファイルに出現するシングルバイト文字を
     * 検出するためのパターン。
     */
    private static $_patterns = array(
        '/[a-zA-Z0-9!"#$%&()=~^|@`:*;+{}]/',
        '/[- ,.<>?_[\]\/\\\\]/',
        "/['\r\n\t\v\f]/",
    );

    /**
     * アンダースコア区切りの文字列を、キャメルケース方式の文字列に置き換える
     * ※アンダースコアが含まれていない文字列は、そのまま引き渡す
     * 
     * @param string $str アンダースコア区切りの文字列
     * @return string $after キャメルケース方式の文字列
     */
    public static function snakeToCamel($str)
    {
        $after     = '';
        $separator = '_';
        $explode   = explode($separator, $str);

        foreach ($explode as $key => $val) {
            if ($key === 0) {
                $after .= $val;
            } else {
                $after .= ucfirst($val);
            }
        }
        return $after;
    }

    /**
     * キャメルケース方式の文字列をアンダースコア区切りの文字列に置き換える
     * 
     * @param string $str キャメルケース方式の文字列
     * @return string $after アンダースコア区切りの文字列
     */
    public static function camelToSnake($str)
    {
        $str = preg_replace("/([A-Z])/", "_$1", $str);
        $str = strtolower($str);
        return ltrim($str, "_");
    }

    /**
     * 配列のキー名をキャメルケースから、スネークケースに変換する
     * 
     * @param mixed キー名がキャメルケースの連想配列またはスカラー値
     * @return array キー名が、スネークケースに変換された連想配列
     */
    public static function keyNameCamelToSnake($data)
    {
        if (!is_array($data)) {
            // スカラー値は変換せずに返す
            return $data;
        }

        $parsedArray = array();
        foreach ($data as $camelName => $dataValue) {
            // キー名をキャメルケースから、スネークケースに変換する
            $parsedArray[self::camelToSnake($camelName)] = $dataValue;
        }
        return $parsedArray;
    }

    /**
     * キャメルケースから、チェーンケース方式に名前を変換する
     * 
     * @param string $camel キャメルケース形式の名前
     * @return string チェーンケース形式の名前
     */
    public static function camelToChain($camel)
    {
        // ディレクトリ名をチェーンケース方式にする（Zendの規約）
        $separator = '-';    //区切り文字
        return strToLower(preg_replace('/([a-z])([A-Z])|([0-9])([A-Z])/', "$1$3$separator$2$4", $camel));
    }

    /**
     * ハイフン区切りの文字列を、キャメルケース方式の文字列に置き換える
     * ※ハイフンが含まれていない文字列は、そのまま引き渡す
     * 
     * @param string $str ハイフン区切りの文字列
     * @return string $after キャメルケース方式の文字列
     */
    public static function chainToCamel($str)
    {
        $after     = '';
        $separator = '-';
        $explode   = explode($separator, $str);

        foreach ($explode as $key => $val) {
            if ($key === 0) {
                $after .= $val;
            } else {
                $after .= ucfirst($val);
            }
        }
        return $after;
    }

    /**
     * シングルバイト文字のみを含む文字列かどうかを判定する。
     *
     * @param string $str 判定する文字列
     * @return bool true:  シングルバイト文字のみを含む
     *              false: シングルバイト文字以外を含む
     */
    public static function containsOnlySingleByteChars($str)
    {
        $str = preg_replace(self::$_patterns, '', $str);
        return (strlen($str) === 0);
    }

    /**
     * その値が「型」的に"空"かどうかチェックします。
     * 
     * @param mixed $v チェック対象の値
     * @return boolean TRUE:空である<br>
     *                  FALSE:空でない
     */
    public static function isEmpty($v)
    {
        $type    = gettype($v);
        $isEmpty = TRUE;
        switch ($type) {
            case 'string':
            case 'integer':
            case 'double':
            case 'float':
                $isEmpty = strlen($v) ? FALSE : TRUE;
                break;
            case 'array':
                $isEmpty = empty($v);
                break;
            case 'boolean':
                $isEmpty = !($v);
                break;
            case 'object':
                // 中身まではみない。NULLじゃなきゃFALSEということで。
                $isEmpty = FALSE;
                break;
            case 'NULL':
            // nop
            default:
                break;
        }

        return $isEmpty;
    }

    /**
     * その値が「型」的に"空でない"かどうかチェックします。
     * 
     * @param mixed $v チェック対象の値
     * @return boolean TRUE:空でない<br>
     *                  FALSE:空である
     */
    public static function isNotEmpty($v)
    {
        return !(self::isEmpty($v));
    }

    /**
     * その値の文字列の長さを返す
     * 配列の場合は、0を返す
     * 
     * @param mixed $v チェック対象の値
     * @return int 文字列の長さを返す
     */
    public static function exStrlen($v)
    {
        if(is_array($v)){
            return 0;
        }
        return strlen($v);
    }

}
