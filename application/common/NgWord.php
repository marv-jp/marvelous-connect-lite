<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NgWord
 *
 * @author tanbaa
 */
class Common_NgWord
{
    /** @var array NGワード設定 */
    private static $_configs;

    /**
     * NGワードのリストを返す
     * 
     * @param mixed $target チェックする文字列 | Application_Model_CommonNgWordのオブジェクト
     * @return array NGワードのリスト
     */
    public static function getNgWord($target)
    {
        $config   = self::getConfigs();
        $database = null;
        if (isset($config['database'])) {
            $database = $config['database'];
            Common_Db::factoryByDbName($database);
        }

        if ($target instanceof Application_Model_CommonNgWord) {
            $target->setNgWord(self::convertEscape($target->getNgWord()));
        } elseif (is_string($target)) {
            $target = self::convertEscape($target);
        } else {
            // 許可されていない型の場合、例外を返す
            throw new Common_Exception_IllegalParameter('引数の型が不正です');
        }

        // NGワード検索
        // 文字列を片寄せしているため、記号が全角に変換され、シングルクオート・ダブルクオートはエスケープされる為SQLインジェクションは起こらない
        $mapper = new Application_Model_CommonNgWordMapper($database);
        $models = $mapper->getNgWordList($target);

        //結果を返す
        $ngWordList = array();
        foreach ($models as $model) {
            $ngWordList[] = $model->getNgWord();
        }

        return $ngWordList;
    }

    /**
     * NGワードの設定を返す。
     *
     * @return array NGワードの設定
     */
    public static function getConfigs()
    {
        if (is_null(self::$_configs)) {
            self::$_configs = Zend_Registry::get('ngWord_configs');
        }

        return self::$_configs;
    }

    /**
     * 比較対象の文字列を片寄せする
     * 最終的に全角ひらがなでマッチングさせるために
     * 1.半角英数字を全角に変換、半角カタカナを全角カタカナに変換
     * 2.全角カタカナから全角ひらがなに変換
     * 
     * @param string $value 比較対象の文字列
     * @return string 片寄せ後の文字列
     */
    public static function convert($value)
    {
        return mb_convert_kana(mb_convert_kana(strtoupper($value), 'KVA', 'UTF-8'), 'c', 'UTF-8');
    }

    /**
     * 比較対象の文字列をエスケープする
     * 
     * @param string $value 比較対象の文字列
     * @return string エスケープ後の文字列
     */
    public static function escape($value)
    {
        return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
    }

    /**
     * 比較対象の文字列を片寄せし、エスケープして返す
     * 
     * @param string $value 比較対象の文字列
     * @return string 片寄せ・エスケープ後の文字列
     */
    public static function convertEscape($value)
    {
        // 改行やクオートを片寄せ後エスケープ
        return self::escape(self::convert($value));
    }

}
