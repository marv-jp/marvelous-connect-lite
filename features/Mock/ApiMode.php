<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiMode
 *
 * @author oguray
 */
class Mock_ApiMode
{
    public $apiMode;
    public $applicationId;

    public function isTrustedProxy()
    {
        return $this->apiMode;
    }

    public function getApplicationId()
    {
        return $this->applicationId;
    }

}
