<?php

/**
 * Logic_Payment_Calculatorクラスのファイル
 *
 * Logic_Payment_Calculatorクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_Calculator
 *
 * 仮想通貨計算クラス
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_Calculator
{

    /**
     * 単価計算
     * 
     * 価格を通貨額で除算し、単価を計算します
     * 
     * @param int $currency 通貨額
     * @param int $price 価格
     * @return float 単価
     */
    static public function calcUnitPrice($currency, $price)
    {
        return $price / $currency;
    }

    /**
     * 単価計算(小数点丸め用)
     * 
     * 価格を通貨額で除算し、単価を計算します
     * 
     * @param int $price 価格
     * @param int $currency 通貨額
     * @param int $roundNum PHPのround関数に渡す、丸める指示数(デフォルト4)
     * @return float 単価
     */
    static public function calcRoundUnitPrice($price, $currency, $roundNum = 4)
    {
        return round($price / $currency, $roundNum);
    }

    /**
     * to通貨額の計算
     * 
     * 通貨額(from)と通貨価値を乗算し、to通貨額を計算します
     * 
     * @param int $fromCurrency 通貨額(from)
     * @param int $currencyValue 通貨価値
     * @return int to通貨額
     */
    static public function calcExchangeTo($fromCurrency, $currencyValue)
    {
        return $fromCurrency * $currencyValue;
    }

    /**
     * to単価計算
     * 
     * @param float $unitPrice 単価
     * @param int $currencyValue 通貨価値
     * @return float to単価
     */
    static public function calcUnitPriceTo($unitPrice, $currencyValue)
    {
        return $unitPrice / $currencyValue;
    }

    /**
     * 両替通貨の通貨価値計算
     * 
     * 通貨額(to)を通貨額(from)で除算し、両替通貨の通貨価値を計算します
     * 
     * @param int $toCurrency 通貨額(to)
     * @param int $fromCurrency 通貨額(from)
     * @return float 通貨価値
     */
    static public function calcExchangeValue($toCurrency, $fromCurrency)
    {
        return $toCurrency / $fromCurrency;
    }

    /**
     * 残額計算
     * 
     * 引数Aから引数Bを減算した結果を返します
     * 
     * @param int $currencyA 引く通貨額
     * @param int $currencyB 引かれる通貨額
     * @return int 残額
     */
    static public function calcBalance($currencyA, $currencyB)
    {
        return $currencyA - $currencyB;
    }

}
