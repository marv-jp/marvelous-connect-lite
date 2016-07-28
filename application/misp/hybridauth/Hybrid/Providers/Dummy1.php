<?php

class Hybrid_Providers_Dummy1 extends Hybrid_Provider_Model_OpenID
{

    /**
     * adapter initializer 
     */
    function initialize()
    {
        parent::initialize();
        $config                            = Zend_Registry::get('misp');
        $this->params['openid_identifier'] = $config['jmeter']['path'] . '/test/misp/discover?platform_id=Dummy1';
        $this->openidIdentifier            = $config['jmeter']['path'] . '/test/misp/discover?platform_id=Dummy1';
    }

}