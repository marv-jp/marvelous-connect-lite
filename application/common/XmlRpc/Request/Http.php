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
class Common_XmlRpc_Request_Http extends Zend_XmlRpc_Request_Http
{
    /**
     * Load XML and parse into request components
     *
     * @param string $request
     * @return boolean True on success, false if an error occurred.
     */
    public function loadXml($request)
    {
        if (!is_string($request)) {
            $this->_fault = new Zend_XmlRpc_Fault(635);
            $this->_fault->setEncoding($this->getEncoding());
            return false;
        }

        // @see ZF-12293 - disable external entities for security purposes
        $loadEntities = libxml_disable_entity_loader(true);
        try {
            $dom = new DOMDocument;
            $dom->loadXML($request);
            foreach ($dom->childNodes as $child) {
                if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                    require_once 'Zend/XmlRpc/Exception.php';
                    throw new Zend_XmlRpc_Exception(
                        'Invalid XML: Detected use of illegal DOCTYPE'
                    );
                }
            }
            $xml = simplexml_import_dom($dom);
            libxml_disable_entity_loader($loadEntities);
        } catch (Exception $e) {
            // Not valid XML
            $this->_fault = new Zend_XmlRpc_Fault(631);
            $this->_fault->setEncoding($this->getEncoding());
            libxml_disable_entity_loader($loadEntities);
            return false;
        }

        // Check for method name
        if (empty($xml->methodName)) {
            // Missing method name
            $this->_fault = new Zend_XmlRpc_Fault(632);
            $this->_fault->setEncoding($this->getEncoding());
            return false;
        }

        $this->_method = (string) $xml->methodName;

        // Check for parameters
        if (!empty($xml->params)) {
            $types = array();
            $argv  = array();
            foreach ($xml->params->children() as $param) {
                if (!isset($param->value)) {
                    $this->_fault = new Zend_XmlRpc_Fault(633);
                    $this->_fault->setEncoding($this->getEncoding());
                    return false;
                }

                try {
                    $param   = Zend_XmlRpc_Value::getXmlRpcValue($param->value, Zend_XmlRpc_Value::XML_STRING);
                    $types[] = $param->getType();
                    $argv[]  = $param->getValue();
                } catch (Exception $e) {
                    $this->_fault = new Zend_XmlRpc_Fault(636);
                    $this->_fault->setEncoding($this->getEncoding());
                    return false;
                }
            }

            $this->_types  = $types;
            $this->_params = $argv;
        }

        $this->_xml = $request;

        return true;
    }
}

