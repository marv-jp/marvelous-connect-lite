<?php

abstract class Misp_Base_BaseController extends Zend_Controller_Action
{
    /**
     * @var array
     * Application_Model_ApplicationMapperのオブジェクトを格納した連想配列
     */
    protected $_applicationMapper = array();

    /**
     * Application_Model_ApplicationMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_ApplicationMapper
     * Application_Model_ApplicationMapperのオブジェクト
     */
    public function getApplicationMapper($dbSectionName)
    {
        if (@!$this->{_applicationMapper}[$dbSectionName]) {
            @$this->{_applicationMapper}[$dbSectionName] = new Application_Model_ApplicationMapper($dbSectionName);
        }
        return @$this->{_applicationMapper}[$dbSectionName];
    }

    /**
     * Application_Model_ApplicationMapperのオブジェクトをセットします
     *
     * @param array $mapper
     * Application_Model_ApplicationMapperのオブジェクトが入った配列
     * @return array
     * Application_Model_ApplicationMapperのオブジェクトが入った配列
     */
    public function setApplicationMapper($mapper)
    {
        $this->_applicationMapper = $mapper;
        return $this->_applicationMapper;
    }

    /**
     * 例外処理
     * 
     * @param string $class エラー発生クラス名
     * @param string $method エラー発生メソッド名
     * @param string $line エラー発生行数
     * @param Exception $exc 例外
     */
    public function processException($class, $method, $line, Exception $exc)
    {
        // デフォルトは InternalLog の呼び出し
        $statusCode = 500;
        $logger     = Common_Log::getInternalLog();
        $logMethod  = 'info';
        switch (get_class($exc)) {
            case 'Common_Exception_NotModified':
                // Not Modified のステータスコード
                $statusCode = 304;
                break;
            case 'Common_Exception_IllegalParameter':
                $statusCode = 400;
                break;
            case 'Common_Exception_OauthInvalidRequest':
                $statusCode = 400;
                break;
            case 'Common_Exception_OauthInvalidGrant':
                $statusCode = 400;
                break;
            case 'Common_Exception_AuthenticationFailed':
                // パスワード不正
                $statusCode = 401;
                break;
            case 'Common_Exception_OauthInvalidClient':
                $statusCode = 401;
                break;
            case 'Common_Exception_Oidc_InvalidToken':
                $statusCode = 401;
                break;
            case 'Common_Exception_InsufficientFunds':
                // 残高不足エラー
                $statusCode = 402;
                break;
            case 'Common_Exception_Forbidden':
                // Forbidden のステータスコード
                $statusCode = 403;
                break;
            case 'Common_Exception_NotFound':
                // Not Found のステータスコード
                $statusCode = 404;
                break;
            case 'Common_Exception_MethodNotAllowed':
                $statusCode = 405;
                break;
            case 'Common_Exception_NotAcceptable':
                // メソッドで許可されていないAPIモードが設定されている場合のステータスコード
                $statusCode = 406;
                break;
            case 'Common_Exception_AlreadyExists':
                // Conflict のステータスコード
                $statusCode = 409;
                break;
            case 'Common_Exception_PreconditionFailed':
                // 前提条件に一致しない場合のステータスコード
                //   例) 異なるプラットフォームで処理していた決済情報が存在する場合のエラー
                $statusCode = 412;
                break;
            case 'Common_Exception_Verify':
                // レシート検証エラー
                $statusCode = 422;
                break;
            case 'Akita_OAuth2_Server_Error':
                // Akitaの例外対応
                // Akitaの方で設定されたレスポンスコードをセットする
                $statusCode = $exc->getOAuth2Code();
                break;
            default :
                $logger     = Common_Log::getExceptionLog();
                $logMethod  = 'error';
                break;
        }

        // ログ出力
        $logger->$logMethod(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', $class, $method, $line, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

        // レスポンスにステータスコードセット
        $response = $this->getResponse();
        $response->setHttpResponseCode($statusCode);

        // 500エラーの場合レスポンスに例外をセットする
        if ($statusCode == 500) {
            $response->setException($exc);
        }
    }

}
