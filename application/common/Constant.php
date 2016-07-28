<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cache
 *
 * @author oguray
 */
class Common_Constant
{

    /** @var Common_Constant */ 
    private static $_instance;

    private function __construct()
    {
        
    }

    /**
     * 定数名に一致する値を返す。
     * 
     * @param string $name 定数名
     * @return mixed 定数値 
     */
    public function get($name)
    {
        $cache = unserialize(Common_Cache::getInstance()->getApc()->load('constant'));
        return $cache[$name];
    }

    /**
     * 定数操作インスタンスを返す。
     * 
     * @return Common_Constant 定数操作インスタンス 
     */
    public static function getInstance()
    {
        if (!is_null(self::$_instance))
        {
            return self::$_instance;
        }

        if (Common_Cache::getInstance()->getApc()->load('constant'))
        {
            self::$_instance = new Common_Constant();
            return self::$_instance;
        }

        $mapper = new Application_Model_CommonConstantMapper();
        $constants = array();
        foreach ($mapper->fetchAll() as $model)
        {
            $constants[$model->getName()] = $model->getValue();
        }

        Common_Cache::getInstance()->getApc()->save(serialize($constants), 'constant');
        self::$_instance = new Common_Constant();

        return self::$_instance;
    }

}
