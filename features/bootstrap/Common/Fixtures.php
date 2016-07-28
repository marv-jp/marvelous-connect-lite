<?php

class Common_Fixtures
{
    public $getApplicationUserModelSet;
    public $getApplicationUserModel00000Set;
    public $getPlatformPaymentModelSet;
    public $getApplicationUserPaymentModelSet;

    public function __construct()
    {
        $this->getApplicationUserModelSet = new Application_Model_ApplicationUser(
                array(
            'applicationId'       => 'applicationId',
            'applicationUserId'   => 'applicationUserId',
            'applicationWorldId'  => 'applicationWorldId',
            'applicationUserName' => 'applicationUserName',
            'password'            => 'password',
            'accessToken'         => 'accessToken',
            'idToken'             => 'idToken',
            'status'              => 'status',
            'createdDate'         => '2013-11-11 11:11:11',
            'updatedDate'         => '2013-11-11 11:11:11'
                )
        );

        $this->getApplicationUserModel00000Set = new Application_Model_ApplicationUser(
                array(
            'applicationId'       => '00000',
            'applicationUserId'   => 'applicationUserId',
            'applicationWorldId'  => 'applicationWorldId',
            'applicationUserName' => 'applicationUserName',
            'password'            => 'password',
            'accessToken'         => 'accessToken',
            'idToken'             => 'idToken',
            'status'              => 'status',
            'createdDate'         => '2013-11-11 11:11:11',
            'updatedDate'         => '2013-11-11 11:11:11'
                )
        );

        $this->getApplicationUserPaymentModelSet = new Application_Model_ApplicationUserPayment(
                array(
            'applicationUserPaymentId'                => 10001,
            'applicationUserId'     => 'user01',
            'applicationId'     => '00000',
            'applicationWorldId'     => '',
            'paymentPlatformId'    => 'paymentPlatformId',
                )
        );
        
        $this->getPlatformPaymentModelSet = new Application_Model_PlatformPayment(
                array(
            'userId'                => 'user01',
            'authorizationCode'     => '認可コード',
            'platformPaymentId'     => 'platformPaymentId',
            'paymentPlatformId'     => 'paymentPlatformId',
            'receipt'               => 'receipt',
            'platformPaymentStatus' => 0,
                )
        );
    }

}
