<?php

class Hybrid_Providers_MarvelousMembers extends Hybrid_Provider_Model_OpenID
{

    /**
     * adapter initializer 
     */
    function initialize()
    {
        parent::initialize();
        $config                 = Zend_Registry::get('misp');
        $this->openidIdentifier = $config['marvelousmembers']['path'];
    }

}