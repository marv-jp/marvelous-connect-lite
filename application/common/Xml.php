<?php

/**
 * Common_Xmlクラスのファイル
 * 
 * Common_Xmlクラスを定義している
 *
 * @category   Zend
 * @package    Common
 * @version    $Id$
 */

/**
 * Common_Xml
 * 
 * SimpleXMLでパース出来ないXMLを扱うクラス
 * 
 * 内部では xml_parse_into_struct 関数を用いて解析しています。
 *
 * @category   Zend
 * @package    Common
 */
class Common_Xml
{
    /** @var array XML要素の位置を示す配列 */
    private $_xmlIndex = array();

    /** @var array XML要素の位置に基づいたXMLデータを格納する配列 */
    private $_xmlValues = array();

    /**
     * XML文字列を受け取って xml_parse_into_struct 関数でパースします。
     * 
     * 要素の取り出しは Common_Xml#get を用います。
     * 
     * @param string $xmlString XML文字列
     */
    public function __construct($xmlString)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xmlString, $this->_xmlValues, $this->_xmlIndex);
        xml_parser_free($p);
    }

    /**
     * 指定されたXMLの要素名と属性名から値を取得します。
     * 
     * <pre>
     * $xml = new Common_Xml('XML文字列');
     * 
     * // テキストデータ(CDATA)を取得したい場合
     * $work = $xml->get('ID', 'value');
     * $id = $work[0]; // getメソッドは配列で返すため、オフセットを指定して取り出す(通常は一つの値しかないため、0指定で問題ありません)
     * 
     * // 属性の値を取得したい場合
     * $work = $xml->get('POINT:URL', 'CALLBACK_URL'); // 'CALLBACK_URL'は属性名
     * $callbackUrl = $work[0];
     * </pre>
     * 
     * @param string $elementName XMLの要素名
     * @param string $attributeName 取得したい属性名(省略した場合、要素内の配列を取得する)
     * @return array 指定された $elementName がなければ空のarray。あれば、それに対応した値。
     */
    public function get($elementName, $attributeName = NULL)
    {
        return $this->_getElements($elementName, $attributeName);
    }

    private function _getElements($elementName, $attributeName = NULL)
    {
        $elements = array();
        if (!isset($this->_xmlIndex[$elementName]))
        {
            return $elements;
        }
        $offsets = $this->_xmlIndex[$elementName];

        foreach ($offsets as $offset)
        {
            $work = $this->_xmlValues[$offset];
            $val  = '';

            if (strlen($attributeName))
            {
                if (isset($work[$attributeName]))
                {
                    $val = $work[$attributeName];
                }
                elseif (isset($work['attributes']))
                {
                    if (isset($work['attributes'][$attributeName]))
                    {
                        $val = $work['attributes'][$attributeName];
                    }
                }
            }
            else
            {
                $val        = $work;
            }
            $elements[] = $val;
        }
        return $elements;
    }

}
