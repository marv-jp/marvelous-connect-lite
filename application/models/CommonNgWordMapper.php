<?php

/**
 * 自動生成ファイル
 *
 * CreateMapperSubClassLogicで自動生成されたファイル
 *
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */

/**
 * @category Zend
 * @package Application_Model
 * @subpackage Model
 */
class Application_Model_CommonNgWordMapper extends Application_Model_Base_CommonNgWordMapper
{

    /**
     * NGワードのリストを取得する
     * 
     * @param Application_Model_CommonNgWord $ngWordModel NGワードモデル
     * @return array Application_Model_CommonNgWordのオブジェクト配列
     */
    public function getNgWordList($ngWordModel)
    {
        $applicationId = $ngWordModel->getApplicationId();
        $word          = $ngWordModel->getNgWord();

        $where    = array($word . " LIKE concat('%',common_ng_word.ng_word,'%') AND (application_id = '' OR application_id = ?) " => $applicationId);
        $dataList = $this->getDbTable()->fetchAll($where);

        $models = array();
        foreach ($dataList as $data) {
            $models[] = new Application_Model_CommonNgWord($data);
        }
        return $models;
    }

}
