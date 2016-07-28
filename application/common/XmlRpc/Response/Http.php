<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Http
 *
 * @author murakamit
 */
class Common_XmlRpc_Response_Http extends Zend_XmlRpc_Response_Http
{
    /**
     * Load a response from an XML response
     *
     * Attempts to load a response from an XMLRPC response, autodetecting if it
     * is a fault response.
     *
     * @param string $response
     * @return boolean True if a valid XMLRPC response, false if a fault
     * response or invalid input
     */
    public function loadXml($response)
    {
        if (!is_string($response)) {
            $this->_fault = new Zend_XmlRpc_Fault(650);
            $this->_fault->setEncoding($this->getEncoding());
            return false;
        }

        // @see ZF-12293 - disable external entities for security purposes
        $loadEntities         = libxml_disable_entity_loader(true);
        $useInternalXmlErrors = libxml_use_internal_errors(true);
        try {
            $dom = new DOMDocument;
            $dom->loadXML($response);
            foreach ($dom->childNodes as $child) {
                if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                    require_once 'Zend/XmlRpc/Exception.php';
                    throw new Zend_XmlRpc_Exception(
                        'Invalid XML: Detected use of illegal DOCTYPE'
                    );
                }
            }
            // TODO: Locate why this passes tests but a simplexml import doesn't
            // $xml = simplexml_import_dom($dom);
            $xml = new SimpleXMLElement($response);
            libxml_disable_entity_loader($loadEntities);
            libxml_use_internal_errors($useInternalXmlErrors);
        } catch (Exception $e) {
            libxml_disable_entity_loader($loadEntities);
            libxml_use_internal_errors($useInternalXmlErrors);
            // Not valid XML
            $this->_fault = new Zend_XmlRpc_Fault(651);
            $this->_fault->setEncoding($this->getEncoding());
            return false;
        }

        if (!empty($xml->fault)) {
            // fault response
            $this->_fault = new Zend_XmlRpc_Fault();
            $this->_fault->setEncoding($this->getEncoding());
            $this->_fault->loadXml($response);
            return false;
        }

        if (empty($xml->params)) {
            // Invalid response
            $this->_fault = new Zend_XmlRpc_Fault(652);
            $this->_fault->setEncoding($this->getEncoding());
            return false;
        }

        try {
            if (!isset($xml->params) || !isset($xml->params->param) || !isset($xml->params->param->value)) {
                require_once 'Zend/XmlRpc/Value/Exception.php';
                throw new Zend_XmlRpc_Value_Exception('Missing XML-RPC value in XML');
            }
            $valueXml = $xml->params->param->value->asXML();
            $value = Zend_XmlRpc_Value::getXmlRpcValue($valueXml, Zend_XmlRpc_Value::XML_STRING);
        } catch (Zend_XmlRpc_Value_Exception $e) {
            $this->_fault = new Zend_XmlRpc_Fault(653);
            $this->_fault->setEncoding($this->getEncoding());
            return false;
        }

        $this->setReturnValue($value->getValue());
        return true;
    }
}
