<?php

abstract class Marvelous_Base_RestController extends Misp_Base_RestController
{

    /**
     * REST-URI-Fragment のパラメータと、 REST-Request-Payload の内容が一致しているか検証します。
     * 
     * @param string $parameterData REST-URI-Fragment のパラメータの値
     * @param array $bodyData REST-Request-Payload の連想配列
     * @param string $keyName REST-Request-Payload の検証対象の連想配列キー名
     * @return boolean TRUE: 一致
     *                  FALSE: 不一致
     */
    protected function _validateParams($parameterData, $bodyData, $keyName)
    {
        // Body部に指定したキーが存在するか確認
        //   キーが存在する場合のみ後続の検証処理をしたいので、
        //   キーが存在しなければ TRUE を返却し終了
        if (!array_key_exists($keyName, $bodyData)) {
            return TRUE;
        }

        // REST-URI-Fragment のパラメータと、 REST-Request-Payload の内容が一致しているか検証
        if ($parameterData == $bodyData[$keyName]) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * APIモードエラー共通処理
     * 
     * @throws Common_Exception_NotAcceptable
     */
    protected function _apiModeErrorProc(Misp_ApiMode $apiMode)
    {
        // APIモード判定でエラーは無かったが、許可されていないAPIモードの場合は406
        if (!$apiMode->isError()) {
            throw new Common_Exception_NotAcceptable('メソッドで許可されていないAPIモードが設定されています');
        }
    }

}