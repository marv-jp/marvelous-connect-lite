<?php

class Common_Http_UserAgent extends Zend_Http_UserAgent
{
    const IPHONE         = 'iPhone';
    const IPAD           = 'iPad';
    const IPOD           = 'iPod';
    const ANDROID_MOBILE = 'Android_Mobile';
    const ANDROID_TABLET = 'Android_Tablet';
    const PC             = 'pc';
    const IE_TRIDENT     = 'Trident';
    const IE_MSIE        = 'MSIE';

    private static $_mobileGroup  = array(
        self::IPHONE,
        self::IPAD,
        self::IPOD,
        self::ANDROID_MOBILE,
        self::ANDROID_TABLET
    );
    private static $_pcGroup      = array(
        self::PC
    );
    private static $_androidGroup = array(
        self::ANDROID_MOBILE,
        self::ANDROID_TABLET
    );
    private static $_iosGroup     = array(
        self::IPHONE,
        self::IPAD,
        self::IPOD
    );

    /**
     * デバイス名を取得する
     * 
     * @return string デバイス名
     */
    public function getDevice()
    {
        $ua = $this->getUserAgent();

        if (strpos($ua, 'iPhone') !== false) {
            return self::IPHONE;
        } else if (strpos($ua, 'iPad') !== false) {
            return self::IPAD;
        } else if (strpos($ua, 'iPod') !== false) {
            return self::IPHONE;
        } else if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false)) {
            return self::ANDROID_MOBILE;
        } else if (strpos($ua, 'Android') !== false) {
            return self::ANDROID_TABLET;
        } else {
            return self::PC;
        }
    }

    /**
     * デバイス判定(PC)
     * 
     * @return boolean PC:ture, PC以外:false
     */
    public function isPc()
    {
        return in_array($this->getDevice(), self::$_pcGroup);
    }

    /**
     * デバイス判定(Mobile)
     * 
     * @return boolean SP:true, SP以外:false
     */
    public function isMobile()
    {
        return in_array($this->getDevice(), self::$_mobileGroup);
    }

    /**
     * デバイス判定(Android)
     * 
     * @return boolean Android:true, Android以外:false
     */
    public function isAndroid()
    {
        return in_array($this->getDevice(), self::$_androidGroup);
    }

    /**
     * デバイス判定(iOS)
     * 
     * @return boolean iOS:true, iOS以外:false
     */
    public function isIos()
    {
        return in_array($this->getDevice(), self::$_iosGroup);
    }

    /**
     * ブラウザ判定(IE)
     * 
     * @return boolean IE:true, IE以外:false
     */
    public function isIe()
    {
        $ua = $this->getUserAgent();
        // IEの場合に含まれる文字列が存在するか確認し、存在する場合はTRUEを返す
        if (strstr($ua, self::IE_TRIDENT) || strstr($ua, self::IE_MSIE)) {
            return TRUE;
        }
        return FALSE;
    }

}
