<?php

abstract class Misp_Base_RestController extends Zend_Rest_Controller
{
    /**
     * 自身を指すセレクタ
     * 
     * @var string SELECTOR_SELF
     */
    const SELECTOR_SELF = '@self';

    /**
     * 全体を指すセレクタ
     * 
     * @var string SELECTOR_ALL
     */
    const SELECTOR_ALL = '@all';

    /**
     * 許可するリクエストパラメータ項目
     * 
     * @var array $_allowRequestParameters
     */
    protected $_allowRequestParameters = array();

    /**
     * 許可するリクエストボディ項目(Request-Payload)(APIごとに定義)
     * 
     * @var array $_allowRequestPayload
     */
    protected $_allowRequestPayload = array();

    /**
     * APIのレスポンス項目定義
     * 
     * @var array $_responsePayload
     */
    protected $_responsePayload = array();

    /**
     * Application_Model_ApplicationMapperのオブジェクトを格納した連想配列
     * 
     * @var array $_applicationMapper
     */
    protected $_applicationMapper = array();

    /**
     * status用の許可リスト
     * 
     * @var array $_statusAllowList
     */
    protected $_statusAllowList = array(
        'inactive' => 0,
        'active'   => 1,
        'banned'   => 6,
    );

    /**
     * ※ Zend Framework 1.12
     */
    public function headAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(405);
        $response->setBody(Zend_Http_Response::responseCodeAsText(405));
    }

    /**
     * Application_Model_ApplicationMapperのオブジェクトを返します
     *
     * @param string $dbSectionName 接続するDB名
     * @return Application_Model_ApplicationMapper Application_Model_ApplicationMapperのオブジェクト
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
     * @param array $mapper Application_Model_ApplicationMapperのオブジェクトが入った配列
     * @return array Application_Model_ApplicationMapperのオブジェクトが入った配列
     */
    public function setApplicationMapper($mapper)
    {
        $this->_applicationMapper = $mapper;
        return $this->_applicationMapper;
    }

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);
    }

    public function indexAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(400);
        $response->setBody(Zend_Http_Response::responseCodeAsText(400));
    }

    /**
     * Commonの例外処理
     * 
     * @param Common_Exception_Abstract $exc
     */
    protected function _responseException(Common_Exception_Abstract $exc)
    {
        // デフォルトは Internal Server Error のステータスコード
        $statusCode       = 500;
        $errorCode        = '';
        $errorDescription = '';
        switch (get_class($exc)) {
            case 'Common_Exception_NotModified':
                // Not Modified のステータスコード
                $statusCode       = 304;
                break;
            case 'Common_Exception_IllegalParameter':
                $statusCode       = 400;
                break;
            case 'Common_Exception_OauthInvalidRequest':
                $statusCode       = 400;
                $errorCode        = 'invalid_request';
                $errorDescription = 'invalid_request';
                break;
            case 'Common_Exception_OauthInvalidGrant':
                $statusCode       = 400;
                $errorCode        = 'invalid_grant';
                $errorDescription = 'invalid_grant';
                break;
            case 'Common_Exception_AuthenticationFailed':
                // パスワード不正
                $statusCode       = 401;
                break;
            case 'Common_Exception_OauthInvalidClient':
                $statusCode       = 401;
                $errorCode        = 'invalid_client';
                $errorDescription = 'invalid_client';
                break;
            case 'Common_Exception_Oidc_InvalidToken':
                $statusCode       = 401;
                $errorCode        = 'invalid_token';
                $errorDescription = 'invalid_token';
                break;
            case 'Common_Exception_InsufficientFunds':
                // 残高不足エラー
                $statusCode       = 402;
                break;
            case 'Common_Exception_Forbidden':
                // Forbidden のステータスコード
                $statusCode       = 403;
                break;
            case 'Common_Exception_NotFound':
                // Not Found のステータスコード
                $statusCode       = 404;
                break;
            case 'Common_Exception_MethodNotAllowed':
                $statusCode       = 405;
                break;
            case 'Common_Exception_NotAcceptable':
                // メソッドで許可されていないAPIモードが設定されている場合のステータスコード
                $statusCode       = 406;
                break;
            case 'Common_Exception_AlreadyExists':
                // Conflict のステータスコード
                $statusCode       = 409;
                break;
            case 'Akita_OAuth2_Server_Error':
                // Akitaの例外対応
                // Akitaの方で設定されたレスポンスコード、エラーコード、エラー説明をセットする
                $statusCode       = $exc->getOAuth2Code();
                $errorCode        = $exc->getOAuth2Error();
                $errorDescription = $exc->getOAuth2ErrorDescription();
                break;
            default :
                break;
        }

        $response = $this->getResponse();
        $response->setHttpResponseCode($statusCode);

        // エラーコードがある場合、OAuth系の仕様に基づきエラーコードを返す
        if (strlen($errorCode)) {
            if ($statusCode == 401) {
                $response->setHeader('WWW-Authenticate', sprintf('Bearer error="%s", error_description="%s"', $errorCode, $errorDescription));
            } elseif ($statusCode == 400) {
                $response->setHeader('Content-Type', 'application/json;charset=UTF-8');
                $response->setBody(Zend_Json::encode(array('error' => $errorCode)));
            }
        }

        // 500エラーの場合レスポンスに例外をセットする
        if ($statusCode == 500) {
            $response->setException($exc);
        }

        Common_Log::getInternalLog()->info(sprintf('Warning: Class:%s | Code:%s | File:%s | Line:%s | Message:%s | Trace:%s |', get_class($exc), $exc->getCode(), $exc->getFile(), $exc->getLine(), $exc->getMessage(), $exc->getTraceAsString()));
    }

    /**
     * リクエストからアプリケーションID、アプリケーション秘密鍵を取得し
     * アプリケーションモデルにセットして返す
     *
     * @return Application_Model_Application アプリケーションモデル
     */
    protected function _generateApplicationModel()
    {
        // DBからアプリケーション秘密鍵取得
        // application.iniからデータベース情報を取得する
        $config        = Zend_Registry::get('misp');
        $applicationDb = $config['db']['sub'];
        try {
            // リクエスト情報からOAuthを構築
            $oauthRequest = OAuthRequest::from_request();

            // アプリケーションIDを取得
            $applicationId = $oauthRequest->get_parameter('oauth_consumer_key');

            // Mapper取得
            $mapper           = $this->getApplicationMapper($applicationDb);
            // アプリケーションIDをキーにアプリケーション情報を取得
            $applicationModel = $mapper->find($applicationId);
            if (!$applicationModel) {
                throw new Common_Exception_NotFound('アプリケーション情報の取得に失敗しました');
            }

            return $applicationModel;
        } catch (Exception $exc) {
            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * 変数が数値かを調べ、数値だった場合は数値に変換して返す
     * 文字列だった場合はそのまま帰す
     * 
     * 基本的にはJSONの仕様に踏襲
     * http://www.json.org/
     * 
     * @param mixed キャスト前の値
     * @return mixed キャスト後の値
     * @see <a href="http://www.json.org/">JSON</a>
     */
    protected function conversionType($value)
    {
        if (is_numeric($value)) {
            // 数値判定で小数点が含まれていればfloatとする。
            // (is_numericは「数値または『数値形式の文字列』」を引数に取るので、
            // "1.23"という文字列を渡しても「数値である」という判定になるが、
            // ここで受ける$valueの「型」は常にstringであるため、is_floatやis_doubleはFALSEを返してしまう対策)
            if (FALSE !== strpos($value, '.')) {
                return (float) $value;
            }
            return (int) $value;
        }
        return $value;
    }

    public function postDispatch()
    {
        $response   = $this->getResponse();
        $statusCode = $response->getHttpResponseCode();
        if ($statusCode == 500) {
            $exc = $response->getException();
            if ($exc) {
                // exception_logに出力
                Common_Log::getExceptionLog()->setException($exc[0]);
                Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc[0]->getFile(), $exc[0]->getLine(), $exc[0]->getMessage()));
            }
            $response->setBody(Zend_Http_Response::responseCodeAsText(500));
        }
    }

    /**
     * アプリケーションユーザテーブルの更新データを構築します。
     * 
     * {code}$allowList{code} は次の形式にしてください。
     * {code}array('項目名1' => '項目名1に対応するモデルのsetter1', ['項目名2', '項目名2に対応するモデルのsetter2',...]{code}
     * 
     * @param Application_Model_ApplicationUser $applicationUserModel
     * @param array $bodyData apps項目の連想配列
     * @param array $allowList アプリケーションユーザテーブルの更新可能項目と、setterの連想配列
     * @return Application_Model_ApplicationUser
     * @throws Common_Exception_IllegalParameter
     */
    protected function _generateApplicationUserData($applicationUserModel, $bodyData, $allowList)
    {
        foreach ($bodyData as $key => $val) {
            if (array_key_exists($key, $allowList)) {
                $data = $val;
                if ('status' == $key) {
                    // キーがstatusの場合、許可文字列かどうかをチェックして
                    // 許可されている場合、数字に置き換える
                    if (isset($this->_statusAllowList[$val])) {
                        $data = $this->_statusAllowList[$val];
                    } else {
                        throw new Common_Exception_IllegalParameter($key . 'のパラメータが不正です');
                    }
                }
                $setMethod = $allowList[$key];
                $applicationUserModel->$setMethod($data);
            }
        }

        return $applicationUserModel;
    }

    /**
     * 引数がNULLもしくは0バイトの場合、空文字を返します。
     * 
     * ToDo: いつかCommon_Util_* に移したい
     * 
     * @param string|array $v
     * @return string
     */
    protected function _nullToBlank($v)
    {
        if (is_array($v) && empty($v)) {
            return '';
        }

        return strlen($v) ? $v : '';
    }

    /**
     * 引数をISO 8601規格の日時形式に変換します。
     * 
     * ToDo: いつかCommon_Util_* に移したい
     * 
     * @param string $v
     * @return string 「2011-09-01T14:26:38+09:00」の書式日時
     * @link http://ja.wikipedia.org/wiki/ISO_8601#.E6.97.A5.E4.BB.98.E3.81.A8.E6.99.82.E5.88.BB.E3.81.AE.E7.B5.84.E5.90.88.E3.81.9B
     */
    protected function _toISO8601($v)
    {
        return date('c', strtotime($v));
    }

}
