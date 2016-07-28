<?php

class Common_Http_Mock_Request
{
    private $_body;

    //put your code here
    public function setBody($body)
    {
        $this->_body = $body;
    }

    public function getBody()
    {
        return $this->_body;
    }

}

