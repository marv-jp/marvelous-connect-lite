<?php

/**
 * APIのリクエストモードを扱うクラス
 *
 * このクラスを利用する際は、最初に
 * <code>Misp_ApiMode::getInstance()->initApiMode()</code>
 * を実行してください。
 */
class Misp_ApiMode
{
    /** @var string OAuthヘッダ不正 */
    const ILLEGAL_OAUTH_HEADER = 'ILLEGAL_OAUTH_HEADER';

    /** @var string パラメータ不正(OAuthヘッダおよびトークンが欠けている場合) */
    const ILLEGAL_PARAMETER = 'ILLEGAL_PARAMETER';

    /** @var string トークン不正(IDトークン検証エラー) */
    const ILLEGAL_TOKEN = 'ILLEGAL_TOKEN';

    /** @var メソッドで許可されていないAPIモード不正 */
    const ILLEGAL_NOT_ACCEPTABLE = 'ILLEGAL_NOT_ACCEPTABLE';

    /** @var string APIモードTrusted(token_secretが要らないモード) */
    const API_MODE_TRUSTED = 'trusted';

    /** @var string APIモードTrusted Proxy(アクセストークン、IDトークン、アプリケーション秘密鍵(OAuthヘッダ)が必要なセキュアなモード) */
    const API_MODE_TRUSTED_PROXY = 'trusted proxy';

    /** @var Misp_ApiMode APIモードを扱うクラスのインスタンス */
    private static $_instance;

    /** @var mixed APIモード */
    private $_apiMode = NULL;

    /** @var boolean APIモード判定フラグ */
    private $_isError = TRUE;

    /** @var string エラー */
    private $_error = '';

    /** @var string アクセストークン */
    private $_accessToken = '';

    /** @var string IDトークン */
    private $_idToken = '';

    /** @var string アプリケーションID */
    private $_applicationId = '';

    /** @var string アプリケーション秘密鍵(OAuthシグネチャ検証用) */
    private $_secret = '';

    /** @var boolean OAuthヘッダ付きリクエストか */
    private $_isOAuthRequest = FALSE;

    /** @var array OAuthリクエストヘッダ */
    private $_oauthHeaders = array();

    /**
     * コンストラクタを private にして外部からのインスタンス生成をブロック
     */
    private function __construct()
    {
        
    }

    /**
     * OAuthシグネチャが正しいかどうか検証します。
     *
     * 同時に、アプリケーション秘密鍵をプロパティに保持します。
     * 下記コードで参照できます。
     * <code>Misp_ApiMode::getInstance()->getSecret()</code>
     *
     * @return boolean TRUE: OAuthシグネチャが正しい
     *                  FALSE: OAuthシグネチャが正しくない
     * @throws Exception
     */
    public function isValidSignature()
    {
        // DBからアプリケーション秘密鍵取得
        // application.iniからデータベース情報を取得する
        $config = Zend_Registry::get('misp');
        $subDb  = $config['db']['sub'];
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            //
            // トランザクション開始
            Common_Db::beginTransaction($subDb);

            // リクエスト情報からOAuthを構築
            $oauthRequest     = OAuthRequest::from_request();
            // シグネチャ取得
            $signature        = $oauthRequest->get_parameter('oauth_signature');
            // アプリケーションIDを取得
            $applicationId    = $oauthRequest->get_parameter('oauth_consumer_key');
            // Mapper生成
            $mapper           = new Application_Model_ApplicationMapper($subDb);
            // アプリケーションIDをキーにアプリケーション情報を取得
            $applicationModel = $mapper->find($applicationId);
            if (!$applicationModel) {
                // 検証エラー
                Common_Log::getInternalLog()->info(sprintf('Warning: Class:%s | Code:%s | File:%s | Line:%s | Message:%s | Trace:%s |', get_class($this), 0, __FILE__, __LINE__, 'アプリケーション情報が取得できませんでした', ''));
                return FALSE;
            }

            // アプリケーションIDをセット
            $this->_applicationId = $applicationId;

            // アプリケーション秘密鍵(OAuthシグネチャ検証用) をセット
            $this->_secret = $applicationModel->getApplicationSecret();

            // OAuthシグネチャ作成
            $signatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
            $oauthConsumer   = new OAuthConsumer($applicationId, $applicationModel->getApplicationSecret());
            $token           = new OAuthToken($this->_accessToken, $this->_idToken);

            // 作成したOAuthシグネチャとリクエスト情報から取得したOAuthシグネチャを比較
            // $oauthConsumerの内容が正しいかを確認するためシグネチャを比較
            if (!$signatureMethod->check_signature($oauthRequest, $oauthConsumer, $token, $signature)) {
                Common_Log::getInternalLog()->info(sprintf('Warning: Class:%s | Code:%s | File:%s | Line:%s | Message:%s | Trace:%s |', get_class($this), 0, __FILE__, __LINE__, 'OAuthシグネチャの検証に失敗しました', ''));
                return FALSE;
            }
            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit($subDb);

            // 検証OK
            return TRUE;
        } catch (Exception $exc) {
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack($subDb);
            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * APIモードを決定します。
     *
     * リクエスト内容からAPIモードを判別し、 Misp_ApiMode オブジェクトに状態をセットします。
     *
     * @return Misp_ApiMode
     */
    public function initApiMode()
    {
        // OAuthヘッダ有無
        //   OAuthヘッダ有り
        //   　→仮 Trusted
        //   OAuthヘッダ無し
        //   　→仮 Proxy
        if (isset($this->_oauthHeaders['Authorization']) && substr($this->_oauthHeaders['Authorization'], 0, 6) == 'OAuth ') {
            // 仮Trusted
            // OAuthヘッダ有り
            if (!$this->isValidSignature()) {
                // ヘッダ不正
                $this->_error = self::ILLEGAL_OAUTH_HEADER;
                return $this;
            }

            // 秘密鍵が空文字でないことを確認
            if (!strlen($this->_secret)) {
                // 406
                $this->_error = self::ILLEGAL_NOT_ACCEPTABLE;
                return $this;
            }

            // トークン有無
            //    アクセストークン無し、IDトークン無し
            //    　→Trusted 確定
            //    アクセストークン有り、IDトークン有り
            //    　→仮 Trusted Proxy
            //        →IDトークンが正しい
            //        　→Trusted Proxy 確定
            //        →IDトークンが正しくない
            //        　→トークン不正
            //    アクセストークン有り、IDトークン無し
            //    　→パラメータ不正
            //    アクセストークン無し、IDトークン有り
            //    　→パラメータ不正
            if (!strlen($this->_accessToken) && !strlen($this->_idToken)) {
                // Trusted 確定
                $this->_apiMode = Misp_ApiMode::API_MODE_TRUSTED;
                $this->_isError = FALSE;
                return $this;
            } elseif (strlen($this->_accessToken) && strlen($this->_idToken)) {
                // 仮 Trusted Proxy の仮判定
                // IDトークン検証
                try {
                    $payload = new Common_Oidc_IdToken_Payload(Common_Oidc_Token::decodeIdToken($this->_idToken));
                    if (Common_Oidc_Token::isValidIdToken($this->_idToken, $payload, $this->_accessToken, $this->_secret)) {
                        // Trusted Proxy 確定
                        $this->_apiMode = Misp_ApiMode::API_MODE_TRUSTED_PROXY;
                        $this->_isError = FALSE;
                        return $this;
                    } else {
                        // トークン不正
                        $this->_error = self::ILLEGAL_TOKEN;
                        return $this;
                    }
                } catch (Exception $exc) {
                    // トークン不正
                    $this->_error = self::ILLEGAL_TOKEN;
                    return $this;
                }
            } else {
                // パラメータ不正
                $this->_error = self::ILLEGAL_PARAMETER;
                return $this;
            }
        } else {

            // パラメータ不正
            $this->_error = self::ILLEGAL_PARAMETER;
            return $this;
        }
    }

    /**
     * APIモード判定のエラーを返す
     *
     * @return string
     */
    public function getApiModeError()
    {
        return $this->_error;
    }

    /**
     * アプリケーションIDを返します。
     *
     * @return string アプリケーションID
     */
    public function getApplicationId()
    {
        return $this->_applicationId;
    }

    /**
     * アプリケーション秘密鍵を返します。
     *
     * @return string アプリケーション秘密鍵
     */
    public function getSecret()
    {
        return $this->_secret;
    }

    /**
     * APIモード判定でエラーがあったかどうかを返します。
     *
     * もし、このメソッドが TRUE を返す時、
     * <code>Misp_ApiMode::getInstance()->getApiModeError()</code> でエラー内容を確認してください。
     *
     * @return boolean TRUE:エラーあり
     *                  FALSE:エラーなし
     */
    public function isError()
    {
        return $this->_isError;
    }

    /**
     * オブジェクト生成とプロパティ初期化処理
     *
     * @return Misp_ApiMode
     */
    public static function getInstance()
    {
        if (self::$_instance instanceof Misp_ApiMode) {
            return self::$_instance;
        }

        self::$_instance = new self();

        // リクエストパラメータ取得
        $request = Zend_Controller_Front::getInstance()->getRequest();

        // OAuthヘッダ
        self::$_instance->_oauthHeaders = OAuthUtil::get_headers();
        // OAuthリクエストかどうか
        if (isset(self::$_instance->_oauthHeaders['Authorization']) && substr(self::$_instance->_oauthHeaders['Authorization'], 0, 6) == 'OAuth ') {
            self::$_instance->_isOAuthRequest = TRUE;
        }
        // アクセストークン
        self::$_instance->_accessToken = $request->getParam('access_token', NULL);
        // IDトークン
        self::$_instance->_idToken     = $request->getParam('id_token', NULL);

        Common_Log::getInternalLog()->info(sprintf('API取得トークン > access_token:%s | id_token:%s', self::$_instance->_accessToken, self::$_instance->_idToken));

        // APIモードを初期設定
        self::$_instance->initApiMode();

        // APIモードにエラーが有るか確認
        if (self::$_instance->_isError) {
            switch (self::$_instance->_error) {
                case self::ILLEGAL_OAUTH_HEADER:
                    throw new Common_Exception_OauthInvalidClient('invalid_client');
                    break;
                case self::ILLEGAL_PARAMETER:
                    throw new Common_Exception_IllegalParameter('invalid_request');
                    break;
                case self::ILLEGAL_TOKEN:
                    throw new Common_Exception_Oidc_InvalidToken('invalid_token');
                    break;
                case self::ILLEGAL_NOT_ACCEPTABLE:
                    throw new Common_Exception_NotAcceptable('not_acceptable');
                    break;
            }
        }

        // requestのcontent-typeがapplication/json;でBodyがある場合
        // そのBodyがJSONフォーマットかどうかをチェックする
        if (FALSE !== strpos($request->getHeader('content-type'), 'application/json;') && strlen($request->getRawBody())) {
            try {
                Zend_Json::decode($request->getRawBody());
            } catch (Exception $exc) {
                throw new Common_Exception_IllegalParameter('パラメータが不正です');
            }
        }
        return self::$_instance;
    }

    /**
     * OAuthリクエストかどうかを返します。
     *
     * @return boolean TRUE:OAuthリクエストである
     *                  FALSE:OAuthリクエストでない
     */
    public function isOAuthRequest()
    {
        return $this->_isOAuthRequest;
    }

    /**
     * アクセストークンとIDトークンが REST-Query-Parameters で送信されていないかどうかを返します。
     *
     * 値ではなく、リクエストパラメータそのものの有無をチェックします。
     *
     * @return boolean TRUE: アクセストークンとIDトークンが REST-Query-Parameters として送信されていない
     *                  FALSE: アクセストークンとIDトークンが REST-Query-Parameters として送信されている
     */
    public function isTokenEmpty()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        if (!$request->has('access_token') && !$request->has('id_token')) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * アクセストークン、IDトークンのどちらかが REST-Query-Parameters で送信されていないかどうかを返します。
     *
     * 値ではなく、リクエストパラメータそのものの有無をチェックします。
     *
     * @return boolean TRUE: アクセストークン、IDトークンのどちらかが REST-Query-Parameters で送信されていない
     *                  FALSE: アクセストークン、IDトークンのどちらかが REST-Query-Parameters で送信されている
     */
    public function isTokenIncomplete()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        return $request->has('access_token') ^ $request->has('id_token');
    }

    /**
     * APIモードをセットします。
     *
     * @param string $apiMode APIモードを表す文字列
     */
    public function setApiMode($apiMode)
    {
        $this->_apiMode = $apiMode;
    }

    /**
     * APIモードが Trusted Proxy かどうかを返します。
     *
     * @return boolean TRUE: Trusted Proxy モード
     *                  FALSE Trusted Proxy モードではない
     */
    public function isTrustedProxy()
    {
        return Misp_ApiMode::API_MODE_TRUSTED_PROXY == $this->_apiMode;
    }

    /**
     * * APIモードが Trusted かどうかを返します。
     *
     * @return boolean TRUE: Trusted モード
     *                  FALSE Trusted モードではない
     */
    public function isTrusted()
    {
        return Misp_ApiMode::API_MODE_TRUSTED == $this->_apiMode;
    }

    /**
     * APIモードを返します。
     *
     * @return string APIモード
     */
    public function getApiMode()
    {
        return $this->_apiMode;
    }

    /**
     * 有効なAPIリクエストモードかどうかを返します。
     *
     * @return boolean TRUE:有効なAPIリクエストモード
     *                  FALSE:無効なAPIリクエストモード
     */
    public function isValidApiMode()
    {
        return !self::isInvalidApiMode();
    }

}
