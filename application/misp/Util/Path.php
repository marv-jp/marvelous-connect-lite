<?php

/**
 * MISPで扱うパス系のあれこれ
 */
class Misp_Path
{

    /**
     * 仮想通貨ロジックのトレイトパスを返します。
     * 
     * ※Zendのオートローダはトレイトに対応していないため、Zend_Loader::loadFile で require させるパスを構築します。
     * 
     * @return string
     */
    public static function getLogicPaymentTraitPath()
    {
        return implode(DIRECTORY_SEPARATOR, [APPLICATION_PATH, 'logics', 'Payment', 'Trait']);
    }

    /**
     * 仮想通貨ロジックのバリデートトレイトパスを返します。
     * 
     * ※Zendのオートローダはトレイトに対応していないため、Zend_Loader::loadFile で require させるパスを構築します。
     * 
     * @return string
     */
    public static function getLogicPaymentValidateTraitPath()
    {
        return implode(DIRECTORY_SEPARATOR, [APPLICATION_PATH, 'logics', 'Payment', 'Trait', 'Validate']);
    }

    /**
     * MISPのトレイトパスを返します。
     * 
     * ※Zendのオートローダはトレイトに対応していないため、Zend_Loader::loadFile で require させるパスを構築します。
     * 
     * @return string
     */
    public static function getMispTraitPath()
    {
        return implode(DIRECTORY_SEPARATOR, [APPLICATION_PATH, 'misp', 'Trait']);
    }

}
