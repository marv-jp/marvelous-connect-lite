<?php

class Common_Controller_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract
{
    /** @var string ヘルパーのフラグ */
    private $_flag = FALSE;
    
    const MD5_HASH_LENGTH = 32;

    /**
     * 認証されている場合：正常終了
     * 認証されていない場合：LoginActionにフォワード
     * 
     * @return users.txtに定義されたモジュール以外からのアクセスの場合、処理の終了 
     */
    public function preDispatch()
    {
        $request = $this->getRequest();
        $config = Zend_Registry::get('auth_configs');
        
        if ($this->_isSkipAuth($request, $config))
        {
            return;
        }

        // 認証対象外にしたい画面
        $exclude = '/' . $request->getModuleName() . '/auth/login';
        $actual  = sprintf("/%s/%s/%s", $request->getModuleName(), $request->getControllerName(), $request->getActionName());

        // Zend_Auth のシングルトンインスタンスへの参照を取得します
        $auth = Zend_Auth::getInstance();

        // 認証済みかどうか判定
        if ($auth->hasIdentity())
        {
            // 認証済みの場合の処理
            if (isset($config['adapter']) && $config['adapter'] == 'Zend_Auth_Adapter_DbTable')
            {
                $identity = $auth->getStorage()->read();
            }
            else
            {
                $identity = $auth->getIdentity();
            }
            $realm    = $identity['realm'];
            
            // 設定がある場合は、セッションのタイムアウトまでの時間を延長する       
            if (isset($config['session']['timeout']) && $config['session']['timeout'])
            {
                $authSession = new Zend_Session_Namespace('Zend_Auth');
                $authSession->setExpirationSeconds($config['session']['timeout']);
            }

            // realmがリクエストされたモジュールと同一の場合、正常終了（リクエストされたコントローラ・アクションが実行される）
            if ($this->getRequest()->getModuleName() === $realm)
            {
                // 認証済みでログイン画面に直接アクセスされた場合TOP画面にフォワードする
                if ($exclude === $actual)
                {
                    $this->forward($request->getModuleName(), 'index', 'index');
                    return;
                }
                // リクエストされたコントローラ・アクションを実行する
                return;
            }
        }

        // 認証未済でログイン画面にフォワードする際の無限ループを回避
        if ($exclude === $actual)
        {
            return;
        }

        // 認証されていなかった場合は、LoginActionにフォワードする
        // 認証後に本来の画面に遷移できるようにセッションにリクエストされたモジュール名等を格納する
        $session = new Zend_Session_Namespace('Common_Controller_Action_Helper_Auth');
        $session->currentModule = $request->getModuleName();
        $session->currentController = $request->getControllerName();
        $session->currentAction = $request->getActionName();
        $params = $request->getParams();
        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);
        $session->currentParams = $request->getParams();
        $session->currentIsPost = $request->isPost();
        $this->forward($request->getModuleName(), 'auth', 'login');
    }

    /**
     * 認証
     * 
     * @param Zend_Auth_Adapter_Interface $adapter
     * @return Zend_Auth_Result 認証結果オブジェクト 
     */
    public function authenticate(Zend_Auth_Adapter_Interface $adapter)
    {
        $auth = Zend_Auth::getInstance();

        return $auth->authenticate($adapter);
    }

    /**
     * ログアウト
     */
    public function clearIdentity()
    {
        $auth = Zend_Auth::getInstance();

        $auth->clearIdentity();
    }

    /**
     * 指定したパラメータでフォワードする
     * 
     * @param string $module モジュール名
     * @param string $controller コントロール名
     * @param string $action アクション名
     * @param boolean $dispatched ディスパッチフラグ(デフォルト:FALSE)
     */
    public function forward($module, $controller, $action, $dispatched = FALSE)
    {
        $request = $this->getRequest();

        // forward
        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action)->setDispatched($dispatched);
    }

    /**
     * パスワードファイルのフルパスを返す
     * 
     * @return string パスワードファイルのフルパス 
     */
    public static function getUsersTxtPath()
    {
        $config = Zend_Registry::get('auth_configs');
        if (isset($config['adapter']) && $config['adapter'] == 'Zend_Auth_Adapter_DbTable')
        {
            return '/dev/null';
        }
        
        return implode(DIRECTORY_SEPARATOR, array(APPLICATION_PATH, 'configs', 'users.txt'));
    }
    
    /**
     * パスワード用フィルタ
     * 
     * @param string $password パスワード
     * @return string フィルタリングされたパスワード
     */
    public static function digest($password)
    {        
        if (empty($password))
        {
            return '';
        }
        
        if (strlen($password) !== self::MD5_HASH_LENGTH)
        {
            return md5($password);
        }
        
        return $password;
    }
    
    /**
     * 認証処理をスキップするか判定する
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @param array $config authの設定を格納した配列
     * @return boolean true: スキップする, false: スキップしない
     */
    protected function _isSkipAuth($request, $config)
    {
        if (isset($config['adapter']) && $config['adapter'] == 'Zend_Auth_Adapter_DbTable')
        {
            if (!isset($config['database']))
            {
                return true;
            }
        }
        else
        {
            // users.txtに定義されたモジュール以外からのアクセスは認証対象外とする
            $filename = $this->getUsersTxtPath();
            if (!is_readable($filename))
            {
                return true;
            }

            // users.txtを解析
            $usersTxt = file_get_contents($filename);
            $userTxts = preg_split('/\r\n|\n/', trim($usersTxt));
            foreach ($userTxts as $user)
            {
                $configs = explode(':', $user);
                // レルムとリクエストモジュール名を比較
                if ($configs[1] === $request->getModuleName())
                {
                    return false;
                }
            }

            return true;
        }
    }
}
