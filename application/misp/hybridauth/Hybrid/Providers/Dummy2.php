<?php

class Hybrid_Providers_Dummy2 extends Hybrid_Provider_Model_OpenID
{

    /**
     * adapter initializer 
     */
    function initialize()
    {
        parent::initialize();
        $config                            = Zend_Registry::get('misp');
        $this->params['openid_identifier'] = $config['jmeter']['path'] . '/test/misp/discover?platform_id=Dummy2';
        $this->openidIdentifier            = $config['jmeter']['path'] . '/test/misp/discover?platform_id=Dummy2';
    }

}