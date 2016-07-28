<?php

/**
 * Common_Log_Formatter_Xml of Xml
 */
class Common_Log_Formatter_Xml extends Zend_Log_Formatter_Xml
{

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param  array    $event    event data
     * @return string             formatted line to write to the log
     */
    public function format($event)
    {
        if ($this->_elementMap === null)
        {
            $dataToInsert = $event;
        }
        else
        {
            $dataToInsert = array();
            foreach ($this->_elementMap as $elementName => $fieldKey)
            {
                $dataToInsert[$elementName] = $event[$fieldKey];
            }
        }

        $enc = $this->getEncoding();
        $dom = new DOMDocument('1.0', $enc);
        $elt = $dom->appendChild(new DOMElement($this->_rootElement));

        foreach ($dataToInsert as $key => $value)
        {
            if (empty($value)
                    || is_scalar($value)
                    || (is_object($value) && method_exists($value, '__toString'))
            )
            {
                if (is_string($value))
                {
                    if (!Common_Util_String::containsOnlySingleByteChars($value))
                    {
                        $value = urlencode($value);
                    }
                    $value = htmlspecialchars($value, ENT_COMPAT, $enc);
                }
                $elt->setAttribute($key, (string) $value);
            }
        }

        $xml = $dom->saveXML();
        $xml = preg_replace('/<\?xml version="1.0"( encoding="[^\"]*")?\?>\n/u', '', $xml);

        return $xml . PHP_EOL;
    }

}
