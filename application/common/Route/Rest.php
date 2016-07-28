<?php

/**
 * 実行するアクションをリクエストメソッドによって振り分ける Route クラスです。
 * 
 * GET    => getAction
 * POST   => postAction
 * PUT    => putAction
 * DELETE => deleteAction
 * 
 * なお、この Route クラスはデフォルトRouteからのChainで使用することが前提となります。
 * application.ini などに次のように記述することで、Chain動作となります。
 * <pre>
 * routes.people.chains.rest.type = "Common_Route_Rest"
 * ; 各セクションの説明
 * ;   routes
 * ;       予約語(固定)
 * ;   people
 * ;       コントローラ名
 * ;   chains
 * ;       予約語(固定:この予約語でchain動作となる)
 * ;   rest
 * ;       任意の識別子(文字列は何でもいい(hogeなど)が、セクションとしては必須
 * ;   type
 * ;       予約語(固定)
 * ; 値の説明
 * ;   "Common_Route_Rest"
 * ;     このクラス。(Route クラス名を指定する。)
 * </pre>
 * 
 */
class Common_Route_Rest extends Zend_Rest_Route
{


    /**
     * Instantiates route based on passed Zend_Config structure
     */
    public static function getInstance(Zend_Config $config)
    {
        $frontController = Zend_Controller_Front::getInstance();
        $defaultsArray = array();
        $restfulConfigArray = array();
        foreach ($config as $key => $values) {
            if ($key == 'type') {
                // do nothing
            } elseif ($key == 'defaults') {
                $defaultsArray = $values->toArray();
            } else {
                $restfulConfigArray[$key] = explode(',', $values);
            }
        }
        $instance = new self($frontController, $defaultsArray, $restfulConfigArray);
        return $instance;
    }

    /**
     * Matches a user submitted request. Assigns and returns an array of variables
     * on a successful match.
     *
     * If a request object is registered, it uses its setModuleName(),
     * setControllerName(), and setActionName() accessors to set those values.
     * Always returns the values as an array.
     *
     * @param Zend_Controller_Request_Http $request Request used to match against this routing ruleset
     * @return array An array of assigned values or a false on a mismatch
     */
    public function match($request, $partial = false)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            $request = $this->_front->getRequest();
        }
        $this->_request = $request;
        $this->_setRequestKeys();

        $path   = $request->getPathInfo();
        $params = $request->getParams();
        $values = array();
        $path   = trim($path, self::URI_DELIMITER);

        if ($path != '') {

            $path = explode(self::URI_DELIMITER, $path);
            // Determine Module
            $moduleName = $this->_defaults[$this->_moduleKey];
            $dispatcher = $this->_front->getDispatcher();
            if ($dispatcher && $dispatcher->isValidModule($path[0])) {
                $moduleName = $path[0];
                if ($this->_checkRestfulModule($moduleName)) {
                    $values[$this->_moduleKey] = array_shift($path);
                    $this->_moduleValid = true;
                }
            }

            // Determine Controller
            $controllerName = $this->_defaults[$this->_controllerKey];
            if (count($path) && !empty($path[0])) {
                if ($this->_checkRestfulController($moduleName, $path[0])) {
                    $controllerName = $path[0];
                    $values[$this->_controllerKey] = array_shift($path);
                    $values[$this->_actionKey] = 'get';
                } else {
                    // If Controller in URI is not found to be a RESTful
                    // Controller, return false to fall back to other routes
                    return false;
                }
            } elseif ($this->_checkRestfulController($moduleName, $controllerName)) {
                $values[$this->_controllerKey] = $controllerName;
                $values[$this->_actionKey] = 'get';
            } else {
                return false;
            }

            //Store path count for method mapping
            $pathElementCount = count($path);

            // Check for "special get" URI's
            $specialGetTarget = false;
            if ($pathElementCount && array_search($path[0], array('index', 'new')) > -1) {
                $specialGetTarget = array_shift($path);
            } elseif ($pathElementCount && $path[$pathElementCount-1] == 'edit') {
                $specialGetTarget = 'edit';
                $params['id'] = urldecode($path[$pathElementCount-2]);
            } elseif ($pathElementCount == 1) {
                $params['id'] = urldecode(array_shift($path));
            } elseif ($pathElementCount == 0 && !isset($params['id'])) {
                $specialGetTarget = 'index';
            }

            // Digest URI params
            if ($numSegs = count($path)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
                    $val = isset($path[$i + 1]) ? $path[$i + 1] : null;
                    // デフォルトRouteをChainする際にゴミが交じるのでコメントアウト
//                    $params[$key] = urldecode($val);
                }
            }

            // Determine Action
            $requestMethod = strtolower($request->getMethod());
            if ($requestMethod != 'get') {
                if ($request->getParam('_method')) {
                    $values[$this->_actionKey] = strtolower($request->getParam('_method'));
                } elseif ( $request->getHeader('X-HTTP-Method-Override') ) {
                    $values[$this->_actionKey] = strtolower($request->getHeader('X-HTTP-Method-Override'));
                } else {
                    $values[$this->_actionKey] = $requestMethod;
                }

                // Map PUT and POST to actual create/update actions
                // based on parameter count (posting to resource or collection)
                switch( $values[$this->_actionKey] ){
                    case 'post':
                        // リクエストURIによってはputアクションにrouteされてしまうので、
                        // 本拡張ではそれよ抑止し、POSTメソッドは必ずpostアクションにrouteするように修正
//                        if ($pathElementCount > 0) {
//                            $values[$this->_actionKey] = 'put';
//                        } else {
                            $values[$this->_actionKey] = 'post';
//                        }
                        break;
                    case 'put':
                        $values[$this->_actionKey] = 'put';
                        break;
                }

            } elseif ($specialGetTarget) {
                $values[$this->_actionKey] = $specialGetTarget;
            }

        }
        $this->_values = $values + $params;

        $result = $this->_values + $this->_defaults;

        if ($partial && $result)
            $this->setMatchedPath($request->getPathInfo());

        return $result;
    }

}
