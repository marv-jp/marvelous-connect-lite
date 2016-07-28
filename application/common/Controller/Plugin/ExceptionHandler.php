<?php

/**
 * 例外ハンドラプラグイン
 * Created by JetBrains PhpStorm.
 * User: murakamit
 * Date: 12/06/27
 * Time: 14:31
 * To change this template use File | Settings | File Templates.
 */
class Common_Controller_Plugin_ExceptionHandler extends Common_Controller_Plugin_Abstract
{
    /** @var string プラグイン名(=plugins.ymlのキー項目) */
    const PLUGIN_NAME = 'exception_handler';
    
    /** @var int プラグインの優先順位 */
    const STACK_INDEX = 0;

    /** @var bool 例外発生済みフラグ */
    protected $_isInsideExceptionHandler = FALSE;

    /** @var int 例外の発生件数 */
    protected $_exceptionCountAtFirstEncounter = 0;

    /**
     * このメソッドですべての例外を処理する
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        // リクエストされたモジュールがプラグイン無効、または、ログ出力対象外ならば何もせず次の処理へ
        if ($this->_isDisableModule(self::PLUGIN_NAME) || !$this->_checkLoggingTarget(self::PLUGIN_NAME))
        {
            return;
        }
        
        $this->_handleException($request);
    }

    /**
     * 例外が発生していないか確認し、発生していた場合にはログ出力を行う
     *
     * @param Zend_Controller_Request_Abstract $request
     * @throws mixed
     */
    protected function _handleException(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();

        if ($this->_isInsideExceptionHandler)
        {

            $exceptions = $response->getException();

            // すでに例外が発生しており、例外の発生件数が前回よりも多い場合に処理を行う
            if (count($exceptions) > $this->_exceptionCountAtFirstEncounter)
            {
                $frontController = Zend_Controller_Front::getInstance();
                $frontController->throwExceptions(TRUE);
                throw array_pop($exceptions);
            }
        }

        if ($response->isException() && !$this->_isInsideExceptionHandler)
        {

            $this->_isInsideExceptionHandler = TRUE;

            $error = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            $exceptions = $response->getException();
            $exception  = $exceptions[0];
            $error->exception = $exception;

            $log = Common_Log::getExceptionLog();
            $log->setException($exception);

            // 基盤系例外の場合は、ログ出力フラグを判定し、ログ出力処理する
            // それ以外の例外は一律ログ出力する
            if ($exception instanceof Common_Exception_Abstract)
            {

                if ($exception->isNotLogged())
                {
                    $log->error($exception->getMessage());
                }
                else
                {
//                    throw;
                }
            }
            else if ($exception instanceof Exception)
            {

                $log->error($exception->getMessage());
            }

            // 例外発生件数を更新する
            $this->_exceptionCountAtFirstEncounter = count($exceptions);
        }
    }

}

