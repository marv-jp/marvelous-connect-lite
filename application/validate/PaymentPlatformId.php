<?php

/**
 * Validate_PaymentPlatformクラスのファイル
 * 
 * Validate_PaymentPlatformクラスを定義している
 *
 * @category Zend
 * @package  Common_Validate
 * @version  $Id$
 */

/**
 * Validate_PaymentPlatformクラスファイル
 * 
 * <h3>isValidの挙動</h3>
 * 
 * <table>
 *   <tr>
 *     <th>検索対象テーブル</th><th>検索条件</th><th>検索結果に対する挙動</th>
 *   </tr>
 *   <tr>
 *     <td>ペイメントプラットフォームテーブル</td><td>ペイメントプラットフォームID</td>
 *     <td>
 *       <ul>
 *         <li>hit
 *           <ul>
 *             <li>TRUEを返却
 *           </ul>
 *         <li>no hit
 *           <ul>
 *             <li>FALSEを返却
 *           </ul>
 *       </ul>
 *     </td>
 *   </tr>
 * </table>
 *
 * @category  Zend
 * @package   Validate
 */
class Validate_PaymentPlatformId extends Common_Validate_Abstract
{
    /**
     * Mapper格納配列
     *
     * @var Application_Model_PaymentPlatformMapper
     */
    private $_mapper = null;

    /**
     * 検証がValidかどうかを判定します
     * 
     * @param string $value 検証対象のペイメントプラットフォームID
     * @return boolean TRUE:検証がValidである/FALSE:検証がValidではない
     */
    public function isValid($value)
    {
        return ($this->getPaymentPlatformMapper()->find($value)) ? TRUE : FALSE;
    }

    /**
     * 検証がValidではないことを判定します
     * 
     * @param string $value 検証対象のペイメントプラットフォームID
     * @return boolean TRUE:検証がValidでない/FALSE:検証がValidである
     */
    public function isNotValid($value)
    {
        return !$this->isValid($value);
    }

    /**
     * マッパー：ペイメントプラットフォームをセットします
     * 
     * ほぼ単体テスト用
     * 
     * @param Application_Model_PaymentPlatformMapper $mapper
     */
    public function setPaymentPlatformMapper(Application_Model_PaymentPlatformMapper $mapper)
    {
        $this->_mapper = $mapper;
    }

    /**
     * マッパー：ペイメントプラットフォームを返します
     * 
     * @return Application_Model_PaymentPlatformMapper
     */
    public function getPaymentPlatformMapper()
    {
        if (!$this->_mapper) {
            $config        = Zend_Registry::get('misp');
            $databaseName  = $config['db']['sub'];
            $this->_mapper = new Application_Model_PaymentPlatformMapper($databaseName);
        }

        return $this->_mapper;
    }

}
