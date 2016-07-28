<?php

/**
 * Created by JetBrains PhpStorm.
 * User: oguray
 * Date: 12/07/03
 * Time: 13:29
 * To change this template use File | Settings | File Templates.
 */
abstract class Common_Controller_Plugin_Abstract extends Zend_Controller_Plugin_Abstract
{
    /** @var array プラグイン設定 */
    protected $_configs = NULL;

    /**
     * ディスパッチ前に初期化処理
     *
     * @param Zend_Controller_Request_Abstract $request
     * @throws Common_Exception_FileNotFound
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_configs = Zend_Registry::get('plugins_configs');
    }

    /**
     * リクエストされたモジュールがプラグイン無効かどうか判別する
     *
     * @param $configuredPluginName plugins.ymlに定義済みのプラグイン名
     * @return bool TRUE:プラグイン無効 FALSE:プラグイン有効
     * @throws Common_Exception_FileNotFound plugins.yml が存在しない場合に Throw される
     */
    protected function _isDisableModule($configuredPluginName)
    {
        if (isset($this->_configs[$configuredPluginName]['enable_modules']['all']) &&
            $this->_configs[$configuredPluginName]['enable_modules']['all'])
        {
            return FALSE;
        }
        
        return !isset($this->_configs[$configuredPluginName]['enable_modules'][$this->getRequest()->getModuleName()]);
    }
    
    /**
     * ログ出力対象か判別する
     * 
     * @param $configuredPluginName plugins.ymlに定義済みのプラグイン名
     * @return bool true: ログ出力対象 false: ログ出力除外
     */
    protected function _checkLoggingTarget($configuredPluginName)
    {
        $request = $this->getRequest();
        $reqModule = $request->getModuleName();
        $reqController = $request->getControllerName();
        $reqAction = $request->getActionName();
        $logFlag = TRUE;

        if (is_array($this->_configs[$configuredPluginName]['excludes']))
        {
            foreach ($this->_configs[$configuredPluginName]['excludes'] as $excludes)
            {                
                $isSetModules = isset($excludes['module']);
                $isSetController = isset($excludes['controller']);
                $isSetAction = isset($excludes['action']);
                    
                // モジュール、コントローラ、アクションのすべてを除外対象に指定
                if ($isSetAction && $isSetController && $isSetModules)
                {
                    if ($reqModule === $excludes['module'] && $reqController === $excludes['controller'] && $reqAction === $excludes['action'])
                    {
                        $logFlag = FALSE;
                        break;
                    }
                }
                // モジュール、コントローラを除外対象に指定
                else if ($isSetController && $isSetModules)
                {
                    if ($reqModule === $excludes['module'] && $reqController === $excludes['controller'])
                    {
                        $logFlag = FALSE;
                        break;
                    }
                }
                // モジュールを除外対象に指定
                else if ($isSetModules)
                {
                    if ($reqModule === $excludes['module'])
                    {
                        $logFlag = FALSE;
                        break;
                    }
                }
            }
        }

        return $logFlag;
    }

}
