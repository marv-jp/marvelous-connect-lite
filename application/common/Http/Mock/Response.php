<?php

class Common_Http_Mock_Response
{
    /** @var string body */
    protected $_body;

    /** @var boolean successful */
    protected $_successful = TRUE;

    /** @var int responseCode */
    protected $_responseCode = 200;
    protected $_headers;

    public function getBody()
    {
        return $this->_body;
    }

    public function setBody($body)
    {
        $this->_body = $body;
    }

    public function isSuccessful()
    {
        return $this->_successful;
    }

    public function setSuccessful($bool)
    {
        $this->_successful = $bool;
    }

    public function getStatus()
    {
        return $this->_responseCode;
    }

    public function setStatus($responseCode)
    {
        $this->_responseCode = $responseCode;
    }

    public function setHeader($header, $value)
    {
        $this->_headers[strtolower($header)] = $value;
    }

    public function getHeader($header)
    {
        $header = strtolower($header);
        if (!is_string($header) || !isset($this->_headers[$header]))
            return null;

        return $this->_headers[$header];
    }

    public static function fromObject($response)
    {
        $object = new self();
        $object->setBody($response->getBody());
        $object->setStatus($response->getStatus());

        return $object;
    }

}
