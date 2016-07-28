<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Random
 *
 * @author oguray
 */
class Common_Random
{
    /** @var string ParkMillerランダムクラス。FLASH連携用 */
    const RAMDOM_NAME_PARK_MILLER = 'Common_Random_ParkMiller';

    /**
     * ランダム値を返すオブジェクトを生成します。
     * 
     * 返すオブジェクトはこのメソッドの引数(クラス名)によって変わります。
     * 指定できるクラス名は、このクラスの定数をご参照ください。
     * 不正なクラス名を渡された場合、このメソッドはnullを返します。
     *
     * @param string $className ランダム値生成クラス名
     * @return Common_Random_Interface_RandomGenerator ランダム値操作インターフェース
     */
    public static function factory($className)
    {
        $clazz = null;

        if (class_exists($className))
        {
            $clazz = new $className();
        }

        return $clazz;
    }

}
