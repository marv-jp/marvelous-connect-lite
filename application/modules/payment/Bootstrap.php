<?php

Zend_Loader::loadFile('ApplicationUserPaymentItemRepository.php', Misp_Path::getLogicPaymentTraitPath());
Zend_Loader::loadFile('ApplicationUserPaymentRepository.php', Misp_Path::getLogicPaymentTraitPath());
Zend_Loader::loadFile('ApplicationUserPlatformPaymentRelationRepository.php', Misp_Path::getLogicPaymentTraitPath());
Zend_Loader::loadFile('ApplicationUserTargetCurrencyPaymentItemRepository.php', Misp_Path::getLogicPaymentTraitPath());
Zend_Loader::loadFile('PlatformPaymentItemRepository.php', Misp_Path::getLogicPaymentTraitPath());
Zend_Loader::loadFile('PlatformPaymentRepository.php', Misp_Path::getLogicPaymentTraitPath());
Zend_Loader::loadFile('PlatformProductItemRepository.php', Misp_Path::getLogicPaymentTraitPath());
Zend_Loader::loadFile('PlatformProductRepository.php', Misp_Path::getLogicPaymentTraitPath());
Zend_Loader::loadFile('ApplicationUserCurrencyCreditLogRepository.php', Misp_Path::getLogicPaymentTraitPath());
Zend_Loader::loadFile('DeveloperPayload.php', Misp_Path::getLogicPaymentValidateTraitPath());
Zend_Loader::loadFile('MagicMethodAccessorRepository.php', Misp_Path::getMispTraitPath());

class Payment_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initPlatformCallbackModule()
    {
        /*
         * モジュール用の初期化処理を行う
         */

        $adminLoader = new Zend_Application_Module_Autoloader(array(
            'basePath'  => APPLICATION_PATH . '/modules/payment',
            'namespace' => '',
                )
        );

        $adminLoader->addResourceType('controller', 'controllers/', 'Payment_');

        return $adminLoader;
    }

}
