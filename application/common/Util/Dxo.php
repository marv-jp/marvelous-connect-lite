<?php

/**
 * Common_Util_Dxoクラスのファイル
 * 
 * Common_Util_Dxoクラスを定義している
 *
 * @category   Zend
 * @package    Common_Util
 * @subpackage Util
 * @version    $Id:$
 */

/**
 * Common_Util_Dxo
 * 
 * 詰め替え君
 *
 * @category   Zend
 * @package    Common_Util
 * @subpackage Util
 */
class Common_Util_Dxo
{

    /**
     * ERモデルオブジェクトから、運び屋オブジェクトに詰め替えます。
     * 
     * 戻り値は運び屋オブジェクトが新規にnewされたものです。
     *
     * @param object $model ERモデルオブジェクト
     * @param string $toClassName 運び屋クラス名
     * @return object 第二引数で指定された運び屋クラスのオブジェクト
     */
    public static function convertModelToVo($model, $toClassName)
    {
        if (is_string($toClassName))
        {
            // 運び屋クラスをインスタンス化する
            $vo = new $toClassName();
        }
        else
        {
            $vo = $toClassName;
        }

        // 運び屋オブジェクトのプロパティを取得する。
        $properties = get_object_vars($vo);

        // 運び屋のプロパティを探索する
        foreach ($properties as $propertyName => $propertyValue)
        {

            // ERモデルオブジェクトに問い合わせるgetter名を生成
            $method = 'get' . $propertyName;
            if (method_exists($model, $method))
            {
                // ERモデルオブジェクトに、運び屋インスタンスのプロパティ名に一致するgetterがあれば、それ経由で代入する
                $vo->$propertyName = $model->$method();
            }
        }

        return $vo;
    }

    /**
     * 運び屋オブジェクトからERモデルオブジェクトに詰め替えます。
     * 
     * 戻り値はERモデルオブジェクトが新規にnewされたものです。
     *
     * @param object $vo 運び屋オブジェクト
     * @param string $toClassName ERモデルクラス名
     * @return object 第二引数で指定されたERモデルクラスのオブジェクト
     */
    public static function convertVoToModel($vo, $toClassName)
    {
        if (is_string($toClassName))
        {
            // ERモデルクラスをインスタンス化する
            $model = new $toClassName();
        }
        else
        {
            $model = $toClassName;
        }

        // ERモデルオブジェクトのプロパティを取得する。
        $properties = get_object_vars($vo);

        // 運び屋のプロパティを探索する
        foreach ($properties as $propertyName => $propertyValue)
        {

            // ERモデルオブジェクトに問い合わせるsetter名を生成
            $method = 'set' . $propertyName;
            if (method_exists($model, $method))
            {
                // setterがあれば、それ経由でVOのデータをセットする
                $model->$method($vo->$propertyName);
            }
        }

        return $model;
    }

}
