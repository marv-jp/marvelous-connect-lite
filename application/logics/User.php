<?php

/**
 * Logic_Userクラス
 *
 * 自動生成クラス
 *
 * @category Zend
 * @package Zend_Magic
 * @subpackage Wand
 */
class Logic_User extends Logic_Abstract
{
    /**
     * @var array
     * Logic_Userのオブジェクト
     */
    private $_logicUser;

    /**
     * @var array
     * Logic_Applicationのオブジェクト
     */
    private $_logicApplication;

    /**
     * @var array
     * Logic_ApplicationUserのオブジェクト
     */
    private $_logicApplicationUser;

    /**
     * @var Misp_ApiMode Misp_ApiModeのオブジェクト
     */
    private $_mispApiMode;

    /**
     * @var Application_Model_Platform Application_Model_Platformのオブジェクト
     */
    private $_platform;

    /**
     * Logic_Userのオブジェクトを返します
     *
     * @return Logic_User
     * Logic_Userのオブジェクト
     */
    public function getUserLogic()
    {
        return $this->_logicUser;
    }

    public function setUserLogic($logic)
    {
        $this->_logicUser = $logic;
        return $this->_logicUser;
    }

    /**
     * Logic_Applicationのオブジェクトを返します
     *
     * @return Logic_Application
     * Logic_Applicationのオブジェクト
     */
    public function getApplicationLogic()
    {
        return $this->_logicApplication;
    }

    public function setApplicationLogic($logic)
    {
        $this->_logicApplication = $logic;
        return $this->_logicApplication;
    }

    /**
     * Logic_ApplicationUserのオブジェクトを返します
     *
     * @return Logic_ApplicationUser
     * Logic_Applicationのオブジェクト
     */
    public function getApplicationUserLogic()
    {
        return $this->_logicApplicationUser;
    }

    public function setApplicationUserLogic($logic)
    {
        $this->_logicApplicationUser = $logic;
        return $this->_logicApplicationUser;
    }

    /**
     * Misp_ApiModeのオブジェクトを返します
     *
     * @return Misp_ApiMode
     * Misp_ApiModeのオブジェクト
     */
    public function getMispApiMode()
    {
        return $this->_mispApiMode;
    }

    public function setMispApiMode($mispApiMode)
    {
        $this->_mispApiMode = $mispApiMode;
        return $this->_mispApiMode;
    }

    /**
     * アクセストークン・IDトークンを元にユーザの情報を返す。
     *
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     * @return Application_Model_User ユーザモデル
     */
    public function readUser(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        $config        = Zend_Registry::get('misp');
        $dbSectionName = $config['db']['main'];
        try {
            // パラメータ取得
            $idToken     = $userPlatformApplicationRelation->getIdToken();
            $accessToken = $userPlatformApplicationRelation->getAccessToken();

            // パラメータチェック
            $this->_isValidateValue($idToken, 65535);
            $this->_isValidateValue($accessToken);

            // ユーザプラットフォームアプリケーション関連モデル取得
            $userLogic = $this->getUserLogic();

            $userPlatformApplicationRelation = $userLogic->readUserPlatformApplicationRelationWithValidate($userPlatformApplicationRelation);

            // トークンから取得したプラットフォームID
            $platformId = $userPlatformApplicationRelation->getPlatformId();

            // application.iniからデータベース情報を取得する
            // Select Mapper
            $applicationUserPlatformRelationMapper = $this->getApplicationUserPlatformRelationMapper($dbSectionName);
            $applicationUserMapper                 = $this->getApplicationUserMapper($dbSectionName);
            $userMapper                            = $this->getUserMapper($dbSectionName);
            $platformUserMapper                    = $this->getPlatformUserMapper($dbSectionName);

            // アプリケーションユーザプラットフォーム関連を取得するWHERE条件作成
            $applicationUserPlatformRelationWhere = array(
                'applicationId'       => array($userPlatformApplicationRelation->getApplicationId()),
                'platformUserId'      => array($userPlatformApplicationRelation->getPlatformUserId()),
                'platformId'          => array($platformId),
                'deletedDate IS NULL' => NULL,
            );
            $applicationUserPlatformRelationOrder = array(
                'updatedDate' => 'DESC',
            );
            $applicationUserPlatformRelations     = $applicationUserPlatformRelationMapper->fetchAll($applicationUserPlatformRelationWhere, $applicationUserPlatformRelationOrder);


            // ユーザモデルに詰め込むアプリケーションユーザ情報を構築
            $applicationUsers = array();
            foreach ($applicationUserPlatformRelations as $applicationUserPlatformRelation) {

                // findキー
                $applicationUserId  = $applicationUserPlatformRelation->getApplicationUserId();
                $applicationId      = $applicationUserPlatformRelation->getApplicationId();
                $applicationWorldId = $applicationUserPlatformRelation->getApplicationWorldId();

                // アプリケーションユーザ取得
                $applicationUser = $applicationUserMapper->find($applicationUserId, $applicationId, $applicationWorldId);
                if (empty($applicationUser)) {
                    throw new Common_Exception_NotFound('取得対象が存在しません');
                }

                // 取得できたアプリケーションユーザを配列にスタック
                //   中身のステータス(有効/無効など)は判別しない(=呼び出し側での使い勝手を考慮)
                $applicationUsers[] = $applicationUser;
            }

            // プラットフォームユーザ情報を構築の準備
            // TODO : 現状は、readUserPlatformApplicationRelationメソッドのDB接続先に依存しており
            //        masterDBを参照しているので問題ないが
            //        このreadUserメソッドとしてはMapperを使用した方が依存先メソッドのDB接続先に影響されないので安全だった
            $applicationModelUserPlatformRelation = new Application_Model_UserPlatformApplicationRelation();
            $applicationModelUserPlatformRelation->setApplicationId($userPlatformApplicationRelation->getApplicationId());
            $applicationModelUserPlatformRelation->setUserId($userPlatformApplicationRelation->getUserId());
            $userPlatformApplicationRelations     = $userLogic->readUserPlatformApplicationRelation($applicationModelUserPlatformRelation);

            // ユーザモデルに詰め込むプラットフォームユーザの情報を構築
            $platformUsers = array();
            foreach ($userPlatformApplicationRelations as $userPlatformApplicationRelation) {

                $addPlatformId = $userPlatformApplicationRelation->getPlatformId();

                // 処理対象のプラットフォームIDがログイン中のものでなければスキップする
                if ($this->_doNotBuildAccount($addPlatformId, $platformId)) {
                    continue;
                }

                // findキー
                $platformUserId = $userPlatformApplicationRelation->getPlatformUserId();

                // プラットフォームユーザ情報取得
                $platformUser = $platformUserMapper->find($platformUserId, $addPlatformId);
                if (empty($platformUser)) {
                    throw new Common_Exception_NotFound('取得対象が存在しません');
                }

                // プラットフォームユーザの日時ではなく、「そのアプリケーションとプラットフォームユーザが連携した日時」を返すために
                // データを詰め替える
                $platformUser->setCreatedDate($userPlatformApplicationRelation->getCreatedDate());
                $platformUser->setUpdatedDate($userPlatformApplicationRelation->getUpdatedDate());

                // レスポンスに不要な項目があるので空文字で上書きしておく
                $platformUser->setPlatformUserId('');
                $platformUser->setPlatformUserName('');
                $platformUser->setPlatformUserDisplayName('');
                $platformUser->setAccessToken('');
                $platformUser->setIdToken('');

                // 取得できたプラットフォームユーザを配列にスタック
                //   中身のステータス(有効/無効など)は判別しない(=呼び出し側での使い勝手を考慮)
                $platformUsers[] = $platformUser;
            }


            // ユーザ取得
            $user = $userMapper->find($userPlatformApplicationRelation->getUserId());
            //   ユーザモデルにアプリケーションユーザ情報(モデルの配列)をセット
            $user->setApps($applicationUsers);
            //   ユーザモデルにプラットフォームユーザ情報(モデルの配列)をセット
            $user->setAccounts($platformUsers);

            return $user;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * プラットフォームの情報を返す。
     *
     * @param Application_Model_Platform $platform プラットフォームモデル
     * @return Application_Model_Platform プラットフォームモデル
     */
    public function readPlatform(Application_Model_Platform $platform)
    {
        // application.iniからデータベース情報を取得する
        $config        = Zend_Registry::get('misp');
        $dbSectionName = $config['db']['sub'];
        try {
            // パラメータ取得
            $platformId = $platform->getPlatformId();

            // パラメータチェック
            $this->_isValidateLength($platformId, 191);

            // Select Mapper
            $platformMapper = $this->getPlatformMapper($dbSectionName);


            // プラットフォームを取得するWHERE条件作成
            $where = array(
                'deletedDate IS NULL' => NULL,
            );
            // プラットフォームを取得するORDER条件作成
            $order = array(
                'sortOrder' => 'ASC'
            );
            // プラットフォームIDがある場合、WHERE条件に追加
            if (strlen($platformId)) {
                $where['platformId'] = array($platformId);
            }
            // プラットフォーム取得
            $resultPlatforms = $platformMapper->fetchAll($where, $order);

            // プラットフォームが取得できなければ例外を投げる
            if (empty($resultPlatforms)) {
                throw new Common_Exception_NotFound('取得対象が存在しません');
            }

            return $resultPlatforms;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * プラットフォームユーザの情報を返す。
     *
     * @param Application_Model_PlatformUser $platformUser プラットフォームユーザモデル
     * @return Application_Model_PlatformUser プラットフォームユーザモデル
     */
    public function readPlatformUser(Application_Model_PlatformUser $platformUser)
    {
        // application.iniからデータベース情報を取得する
        $config        = Zend_Registry::get('misp');
        $dbSectionName = $config['db']['sub'];
        try {
            // パラメータ取得
            $platformId     = $platformUser->getPlatformId();
            $platformUserId = $platformUser->getPlatformUserId();

            // パラメータチェック
            $this->_isValidateValue($platformId, 191);
            $this->_isValidateValue($platformUserId);

            // Select Mapper
            $platformUserMapper = $this->getPlatformUserMapper($dbSectionName);


            // プラットフォームユーザを取得するWHERE条件作成
            $where               = array(
                'platformId'          => $platformId,
                'platformUserId'      => $platformUserId,
                'deletedDate IS NULL' => NULL,
            );
            // プラットフォームユーザ取得
            $resultPlatformUsers = $platformUserMapper->fetchAll($where);
            // プラットフォームユーザが取得できなければ例外を投げる
            if (Common_Util_String::isEmpty($resultPlatformUsers)) {
                throw new Common_Exception_NotFound('取得対象が存在しません');
            }

            // 取得できるのは1件だけなので
            return $resultPlatformUsers[0];
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ユーザプラットフォームアプリケーション関連の連携状態を確認する。
     *
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplication ユーザプラットフォームアプリケーション関連モデル
     * @return array Application_Model_UserPlatformApplicationRelation の配列
     */
    public function readIdFederationStatus(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        // application.iniからデータベース情報を取得する
        $config        = Zend_Registry::get('misp');
        $dbSectionName = $config['db']['main'];
        try {

            // パラメータ取得
            $applicationId = $userPlatformApplicationRelation->getApplicationId();
            $accessToken   = $userPlatformApplicationRelation->getAccessToken();
            $idToken       = $userPlatformApplicationRelation->getIdToken();

            // パラメータチェック            
            $this->_isValidateValue($applicationId, 11);
            $this->_isValidateValue($accessToken);
            $this->_isValidateValue($idToken, 65535);

            // ロジック
            $userLogic = $this->getUserLogic();

            // Select Mapper
            // ユーザプラットフォームアプリケーション関連
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSectionName);

            // ユーザプラットフォームアプリケーション関連モデル取得
            $resultUserPlatformApplicationRelation = $userLogic->readUserPlatformApplicationRelationWithValidate($userPlatformApplicationRelation);

            // ユーザIDを取り出す
            $userId = $resultUserPlatformApplicationRelation->getUserId();

            // アプリケーションユーザプラットフォーム関連から、プラットフォームID一覧を取得
            $groupBy                          = array('platformId');
            $where                            = array(
                'applicationId' => array($applicationId),
                'userId'        => array($userId),
            );
            $userPlatformApplicationRelations = $userPlatformApplicationRelationMapper->fetchAllReadIdFederationStatus($where, $groupBy);

            Common_Log::getInternalLog()->info('method:' . __METHOD__ . ' | 取得アプリケーションユーザプラットフォーム関連:' . print_r($userPlatformApplicationRelations, 1));

            return $userPlatformApplicationRelations;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ID連携の検証を行い、アクセストークン・IDトークンを返す。
     *
     * @param Application_Model_PlatformUser $platformUser プラットフォームユーザモデル
     * @param Common_Oidc_IdToken_Payload $oidcIdTokenPayload IDトークンペイロードモデル
     * @param Common_Oidc_Authorization_Authorization $authorization Authorizationモデル
     * @return Application_Model_UserPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     */
    public function federationCallback(Application_Model_PlatformUser $platformUser, Common_Oidc_IdToken_Payload $oidcIdTokenPayload, Common_Oidc_Authorization_Authorization $authorization)
    {
        // application.iniからデータベース情報を取得する
        $config        = Zend_Registry::get('misp');
        $dbSectionName = $config['db']['main'];

        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction($dbSectionName);

            // パラメータ取得
            $platformUserId = $platformUser->getPlatformUserId();
            $platformId     = $platformUser->getPlatformId();
            $emailAddress   = $platformUser->getEmailAddress();
            $accessToken    = $platformUser->getAccessToken();
            $idToken        = $platformUser->getIdToken();
            $applicationId  = $oidcIdTokenPayload->getAud();
            $responseType   = $authorization->getResponseType();

            // パラメータチェック
            $this->_isValidateValue($platformUserId);
            $this->_isValidateValue($platformId, 191);
            $this->_isValidateLength($emailAddress, 255);
            $this->_isValidateValue($applicationId, 11);
            $this->_isValidateValue($responseType);
            $this->_isValidateLength($accessToken, 65535);
            $this->_isValidateLength($idToken, 65535);

            // ユーザロジック取得
            $userLogic = $this->getUserLogic();

            // リダイレクトURI検証
            // $applicationIDをclient_idとして$authorizationにセットする
            $authorization->setClientId($applicationId);
            if (!$userLogic->isValidRedirectUri($authorization)) {
                throw new Common_Exception_OauthInvalidRequest(sprintf('OAuthリクエスト不正: application_id: %s | redirect_uri: %s', $applicationId, $authorization->getRedirectUri()));
            }

            // Select Mapper
            $applicationMapper                     = $this->getApplicationMapper($dbSectionName);
            $userMapper                            = $this->getUserMapper($dbSectionName);
            $platformUserMapper                    = $this->getPlatformUserMapper($dbSectionName);
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSectionName);

            // プラットフォームユーザ情報存在確認
            $resultPlatformUser = $platformUserMapper->find($platformUserId, $platformId);

            // 新規登録時のユーザID
            $userId = 0;

            // 存在しない場合は初連携なので、登録処理を行う
            if (empty($resultPlatformUser)) {

                // プラットフォームユーザ情報を登録
                $platformUser->setStatus(Logic_User::STATUS_ACTIVE);
                $userLogic->createPlatformUser($platformUser);

                // ユーザを登録
                $userId = $userLogic->createUser()->getUserId();
            } else {

                $status = $resultPlatformUser->getStatus();

                switch ($status) {
                    // ユーザが無効
                    case Logic_User::STATUS_INACTIVE:

                        // 無効ステータスを有効にする
                        // (＋アクセストークン、IDトークンも新しいものに更新する)
                        $platformUser->setStatus(Logic_User::STATUS_ACTIVE);
                        $userLogic->updatePlatformUser($platformUser);


                        // 処理対象のユーザIDを取得するために、
                        // ユーザプラットフォームアプリケーション関連を検索し、
                        // そこから取得する
                        // ただし、ユーザIDを昇順ソートしたレコードを使用する
                        //   →理由：ID統合は古いIDに統合されていくため
                        $userPlatformApplicationRelationWhere = array(
                            'platformUserId' => array($platformUserId),
                            'platformId'     => array($platformId),
                        );
                        $userPlatformApplicationRelationOrder = array(
                            'userId' => 'ASC',
                        );
                        $userPlatformApplicationRelations     = $userPlatformApplicationRelationMapper->fetchAll($userPlatformApplicationRelationWhere, $userPlatformApplicationRelationOrder);

                        // レコードが取得できない場合はユーザが存在しない状態なので、ここで登録する
                        if (empty($userPlatformApplicationRelations)) {

                            // ユーザを登録
                            $userId = $userLogic->createUser()->getUserId();
                        } else {

                            // 登録されていたユーザIDを取得(0番目確定で問題ない)
                            $userPlatformApplicationRelation = $userPlatformApplicationRelations[0];
                            $userId                          = $userPlatformApplicationRelation->getUserId();

                            // ユーザ情報を取得
                            $user = $userMapper->find($userId);
                            if (empty($user)) {
                                // 超異常系なので共通例外
                                throw new Common_Exception_Exception('データが破損している可能性があります');
                            }

                            // ステータスによって処理分岐
                            switch ($user->getStatus()) {

                                // 無効ユーザを有効化
                                case Logic_User::STATUS_INACTIVE:

                                    $user->setStatus(Logic_User::STATUS_ACTIVE);
                                    $userLogic->updateUser($user);
                                    break;

                                // ユーザが既に有効の場合は処理しない
                                case Logic_User::STATUS_ACTIVE:

                                    // 処理の必要がないので終了
                                    break;

                                // ユーザ情報が取得できたが、バン状態のユーザは連携すべきではないので、専用例外で通知する
                                case Logic_User::STATUS_BANNED:

                                    // バンされていることを専用例外で通知する
                                    throw new Logic_Exception_Banned(sprintf('このユーザはバンされています:user_id:%s', $userId));
                                    break;

                                default:
                                    // 超異常系なので共通例外
                                    throw new Common_Exception_Exception(sprintf('ステータス値が異常です:status:%s', $status));
                                    break;
                            }
                        }

                        break;

                    // ユーザが有効
                    case Logic_User::STATUS_ACTIVE:

                        // プラットフォームユーザ情報更新
                        $platformUser->setStatus(Logic_User::STATUS_ACTIVE);
                        $userLogic->updatePlatformUser($platformUser);

                        // ユーザIDの取得
                        // 処理対象のユーザIDを取得するために、
                        // ユーザプラットフォームアプリケーション関連を検索し、
                        // そこから取得する
                        // ただし、ユーザIDを昇順ソートしたレコードを使用する
                        //   →理由：ID統合は古いIDに統合されていくため
                        $userPlatformApplicationRelationWhere = array(
                            'platformUserId' => array($platformUserId),
                            'platformId'     => array($platformId),
                        );
                        $userPlatformApplicationRelationOrder = array(
                            'userId' => 'ASC',
                        );
                        $userPlatformApplicationRelations     = $userPlatformApplicationRelationMapper->fetchAll($userPlatformApplicationRelationWhere, $userPlatformApplicationRelationOrder);
                        // レコードが取得できない場合はユーザが存在しない状態なので、ここで登録する
                        if (empty($userPlatformApplicationRelations)) {
                            throw new Common_Exception_NotFound('対象が見つかりませんでした');
                        }
                        // 登録されていたユーザIDを取得(0番目確定で問題ない)
                        $userPlatformApplicationRelation = $userPlatformApplicationRelations[0];
                        $userId                          = $userPlatformApplicationRelation->getUserId();

                        break;

                    // ユーザがバンされている
                    case Logic_User::STATUS_BANNED:

                        // バンされていることを専用例外で通知する
                        throw new Logic_Exception_Banned(sprintf('このユーザはバンされています:platform_user_id:%s | platform_id:%s', $platformUserId, $platformId));
                        break;

                    default:

                        // 超異常系なので共通例外
                        throw new Common_Exception_Exception(sprintf('ステータス値が異常です:status:%s', $status));
                        break;
                }
            }

            // OpenID Connect のresponse_typeによってIDトークンペイロードのnonceの必須/省略可が変わるので
            // ここで必須項目を調整する
            // ＋認可コードを生成する
            $authorizationCode = NULL;
            $mispAccessToken   = NULL;
            $mispIdToken       = NULL;
            $mispRefreshToken  = NULL;

            // MISPのアクセストークン、IDトークンを生成
            $payload           = array(
                'iss'   => $oidcIdTokenPayload->getIss(),
                'sub'   => $userId,
                'aud'   => $applicationId,
                'exp'   => $oidcIdTokenPayload->getExp(),
                'iat'   => $oidcIdTokenPayload->getIat(),
                'nonce' => $oidcIdTokenPayload->getNonce(),
            );
            $applicationWhere  = array(
                'applicationId'       => array($applicationId),
                'deletedDate IS NULL' => NULL
            );
            $applications      = $applicationMapper->fetchAll($applicationWhere);
            $application       = $applications[0]; // 0番目確定で問題ない
            $applicationSecret = $application->getApplicationSecret();

            if (Common_Oidc_Authorization_Authorization::RESPONSE_TYPE_BASIC == $responseType) {
                // 必須項目調整
                Common_Oidc_Token::setRequiredKeys(array('iss', 'sub', 'aud', 'exp', 'iat'));
                // 認可コード生成
                $authorizationCode = Common_Oidc_Token::generateAuthorizationCode();
                // リフレッシュトークンはcodeフローのみ対応する(OpenID Connectの仕様)
                $mispRefreshToken  = Common_Oidc_Token::generateRefreshToken();
            }

            $mispAccessToken = Common_Oidc_Token::generateAccessToken();
            $mispIdToken     = Common_Oidc_Token::generateIdToken($payload, $mispAccessToken, $applicationSecret);

            // ユーザプラットフォームアプリケーション関連登録
            $userPlatformApplication = new Application_Model_UserPlatformApplicationRelation();
            $userPlatformApplication->setUserId($userId);
            $userPlatformApplication->setPlatformUserId($platformUserId);
            $userPlatformApplication->setPlatformId($platformId);
            $userPlatformApplication->setApplicationId($applicationId);
            // 既にユーザプラットフォームアプリケーション関連情報が作成されている場合は、その作成日時を使用する
            try {
                $tempUserPlatformApplication = $userLogic->readUserPlatformApplicationRelation($userPlatformApplication);
                $userPlatformApplication->setCreatedDate($tempUserPlatformApplication[0]->getCreatedDate());
            } catch (Common_Exception_Oidc_InvalidToken $exc) {
                // 何もしない
            }
            $userPlatformApplication->setAccessToken($mispAccessToken);
            $userPlatformApplication->setIdToken($mispIdToken);
            $userPlatformApplication->setRefreshToken($mispRefreshToken);
            $userPlatformApplication->setAuthorizationCode($authorizationCode);

            $resultUserPlatformApplication = $userLogic->createUserPlatformApplicationRelation($userPlatformApplication);

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $resultUserPlatformApplication;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * アクセストークン・IDトークンに紐づく、プラットフォームユーザ情報と
     * 送られてきたアプリケーションユーザ情報を連携状態にする。
     *
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     * @param Application_Model_ApplicationUser $applicationUser アプリケーションユーザモデル
     * @return Application_Model_UserPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     */
    public function createIdFederation(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation, Application_Model_ApplicationUser $applicationUser = NULL)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();
            // application.iniからデータベース情報を取得する
            $config        = Zend_Registry::get('misp');
            $dbSectionName = $config['db']['main'];


            // ユーザプラットフォームアプリケーション関連パラメータ取得
            $accessToken = $userPlatformApplicationRelation->getAccessToken();
            $idToken     = $userPlatformApplicationRelation->getIdToken();

            // ユーザプラットフォームアプリケーション関連パラメータチェック            
            $this->_isValidateValue($accessToken);
            $this->_isValidateValue($idToken, 65535);


            // ロジック
            $userLogic = $this->getUserLogic();

            // ユーザプラットフォームアプリケーション関連モデル取得
            $userPlatformApplicationRelationValidated = $userLogic->readUserPlatformApplicationRelationWithValidate($userPlatformApplicationRelation);

            // Select Mapper
            $applicationUserMapper = $this->getApplicationUserMapper($dbSectionName);
            $applicationMapper     = $this->getApplicationMapper($dbSectionName);


            // 取得したユーザプラットフォーム関連モデルから必要な値を修得
            $applicationId  = $userPlatformApplicationRelationValidated->getApplicationId();
            $platformUserId = $userPlatformApplicationRelationValidated->getPlatformUserId();
            $platformId     = $userPlatformApplicationRelationValidated->getPlatformId();

            // ユーザ更新APIでアプリケーションユーザとIDを連携させたい場合の処理
            if (!empty($applicationUser)) {

                // アプリケーションユーザパラメータ取得
                $applicationUserId  = $applicationUser->getApplicationUserId();
                $applicationWorldId = $applicationUser->getApplicationWorldId();

                // アプリケーションユーザパラメータチェック
                $this->_isValidateValue($applicationUserId);
                $this->_isValidateLength($applicationWorldId);

                // アプリケーションワールドIDが空の場合、空文字をセット
                if (!strlen($applicationWorldId)) {
                    $applicationWorldId = '';
                }

                // InputのアプリケーションIDとIDトークンから取得したアプリケーションIDが同じかをチェックする
                if ($applicationId != $applicationUser->getApplicationId()) {
                    throw new Common_Exception_IllegalParameter('パラメータが不正です');
                }

                //送られてきたアプリケーションユーザIDがアプリケーションユーザテーブルに存在しているかチェック
                $applicationUserWhere = array(
                    'applicationUserId'   => array($applicationUserId),
                    'applicationId'       => array($applicationId),
                    'applicationWorldId'  => array($applicationWorldId),
                    'deletedDate IS NULL' => NULL
                );
                $applicationUsers     = $applicationUserMapper->fetchAll($applicationUserWhere);

                // アプリケーションユーザが取得できない場合、新規登録
                $applicationUserLogic = $this->getApplicationUserLogic();
                $applications         = $applicationMapper->fetchAll(array('applicationId' => array($applicationId)));
                $application          = $applications[0];  // 複合キーで検索しているため一意
                $applicationUser->setApplicationId($applicationId);
                if (empty($applicationUsers)) {
                    $resultApplicationUser = $applicationUserLogic->createApplicationUser($applicationUser);
                }
                //アプリケーションユーザが登録されていた場合は、引数のアプリケーションユーザモデルの内容で更新する
                else {

                    try {
                        $resultApplicationUser = $applicationUserLogic->updateApplicationUser($applicationUser);
                    } catch (Common_Exception_NotModified $exc) {
                        // 無変更の場合は異常系ではないので一応ログを出力し、処理続行
                        Common_Log::getInternalLog()->info(sprintf('Warning: %s | %s | %s | %s | 処理は続行します', __CLASS__, __METHOD__, __LINE__, $exc->getMessage()));
                    }
                }

                // アプリケーションユーザ情報とプラットフォームユーザ情報をアプリケーションユーザプラットフォーム関連テーブルに登録
                // 登録用のモデル作成
                $applicationUserPlatform = new Application_Model_ApplicationUserPlatformRelation();
                $applicationUserPlatform->setApplicationUserId($applicationUserId);
                $applicationUserPlatform->setApplicationId($applicationId);
                $applicationUserPlatform->setApplicationWorldId($applicationWorldId);
                $applicationUserPlatform->setPlatformUserId($platformUserId);
                $applicationUserPlatform->setPlatformId($platformId);
                // アプリケーションユーザプラットフォーム関連登録
                $userLogic->createApplicationUserPlatformRelation($applicationUserPlatform);
            }

            $userPlatformApplicationRelations = $userLogic->readUserPlatformApplicationRelation(new Application_Model_UserPlatformApplicationRelation(
                    array(
                'platformId'     => $platformId,
                'platformUserId' => $platformUserId,
                'applicationId'  => $applicationId,
                    )
            ));

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();
            return $userPlatformApplicationRelations[0];
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * アプリケーションユーザプラットフォーム関連を論理削除する。
     * 必要であれば、ユーザアプリケーション関連も論理削除する。
     *
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     * @return boolean 処理結果
     */
    public function cancelIdFederation(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            // パラメータ取得
            $platformId    = $userPlatformApplicationRelation->getPlatformId();
            $applicationId = $userPlatformApplicationRelation->getApplicationId();
            $accessToken   = $userPlatformApplicationRelation->getAccessToken();
            $idToken       = $userPlatformApplicationRelation->getIdToken();

            // パラメータチェック            
            $this->_isValidateValue($platformId, 191);
            $this->_isValidateValue($applicationId, 11);
            $this->_isValidateValue($accessToken);
            $this->_isValidateValue($idToken, 65535);

            // application.iniからデータベース情報を取得する
            $config        = Zend_Registry::get('misp');
            $dbSectionName = $config['db']['main'];

            // ロジック
            $userLogic = $this->getUserLogic();

            // Select Mapper
            //   ユーザプラットフォームアプリケーション関連
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSectionName);

            // ユーザプラットフォームアプリケーション関連モデル取得
            $userPlatformApplicationRelation = $userLogic->readUserPlatformApplicationRelationWithValidate($userPlatformApplicationRelation);


            // 解除対象のプラットフォームユーザ情報を取得
            $userId                                     = $userPlatformApplicationRelation->getUserId();
            $userPlatformApplicationRelationMapperWhere = array(
                'userId'              => array($userId),
                'platformId'          => array($platformId),
                'applicationId'       => array($applicationId),
                'deletedDate IS NULL' => NULL,
            );
            $resultUserPlatformApplicationRelations     = $userPlatformApplicationRelationMapper->fetchAll($userPlatformApplicationRelationMapperWhere);
            if (empty($resultUserPlatformApplicationRelations)) {
                throw new Common_Exception_NotFound('解除対象が取得できませんでした');
            }

            // アプリケーションユーザプラットフォーム関連テーブル更新
            $deletedDate = date('Y-m-d H:i:s');
            foreach ($resultUserPlatformApplicationRelations as $resultUserPlatformApplicationRelation) {
                $model = new Application_Model_ApplicationUserPlatformRelation($resultUserPlatformApplicationRelation->toArray());
                $model->setDeletedDate($deletedDate);
                try {
                    $userLogic->updateApplicationUserPlatformRelation($model);
                } catch (Common_Exception_NotFound $excNotFound) {
                    // データ構造的にこの例外はあり得るが、この処理では異常系ではないので
                    // ログを出力し、処理を続ける
                    // API連携で何らかのエラーが発生した際などにあり得る
                    Common_Log::getInternalLog()->info(sprintf('%s | アプリケーションユーザプラットフォーム関連の更新に失敗. user_id:%s platform_id:%s application_id:%s', $excNotFound->getMessage(), $userId, $platformId, $applicationId));
                }
            }

            // ユーザプラットフォームアプリケーション関連テーブル更新
            // プラットフォームIDはモデルから取得したものを使用
            $userPlatformApplicationRelation->setPlatformId($platformId);
            $userPlatformApplicationRelation->setPlatformUserId(NULL);
            $userPlatformApplicationRelation->setAccessToken(NULL);
            $userPlatformApplicationRelation->setIdToken(NULL);
            $userPlatformApplicationRelation->setDeletedDate($deletedDate);

            $userLogic->updateUserPlatformApplicationRelation($userPlatformApplicationRelation);

            foreach ($resultUserPlatformApplicationRelations as $resultUserPlatformApplicationRelation) {

                $platformUserId = $resultUserPlatformApplicationRelation->getPlatformUserId();
                $platformId     = $resultUserPlatformApplicationRelation->getPlatformId();

                $where  = array(
                    'platformUserId'      => array($platformUserId),
                    'platformId'          => array($platformId),
                    'deletedDate IS NULL' => NULL,
                );
                $result = $userPlatformApplicationRelationMapper->fetchAll($where);

                // 取得できなかった場合はプラットフォームユーザを解除状態にする
                if (empty($result)) {

                    $platformUser = new Application_Model_PlatformUser();
                    $platformUser->setPlatformUserId($platformUserId);
                    $platformUser->setPlatformId($platformId);
                    $platformUser->setStatus(Logic_User::STATUS_INACTIVE);
                    $userLogic->updatePlatformUser($platformUser);
                }
            }

            // ユーザ検証
            $user = new Application_Model_User();
            $user->setUserId($userId);
            $userLogic->disableNonFederationUser($user);


            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return TRUE;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ユーザプラットフォームアプリケーション関連とプラットフォームユーザのstatusを未連携状態にする。
     *
     * プラットフォームユーザの情報を元に、ユーザプラットフォームアプリケーション関連とプラットフォームユーザのstatusを未連携状態にする。
     * 必要であればユーザのstatusも未連携状態にする。
     *
     * @param Application_Model_PlatformUser $platformUser プラットフォームユーザモデル
     * @return boolean 処理結果
     */
    public function cancelPlatformUserFederation(Application_Model_PlatformUser $platformUser)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            // パラメータ取得
            $platformUserId = $platformUser->getPlatformUserId();
            $platformId     = $platformUser->getPlatformId();

            // パラメータチェック            
            $this->_isValidateValue($platformUserId);
            $this->_isValidateValue($platformId, 191);

            // application.iniからデータベース情報を取得する
            $config        = Zend_Registry::get('misp');
            $dbSectionName = $config['db']['main'];

            // ロジック
            $userLogic = $this->getUserLogic();

            // Select Mapper
            //   ユーザプラットフォームアプリケーション関連
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSectionName);

            // ユーザプラットフォームアプリケーション関連取得
            $userPlatformApplicationRelationMapperWhere = array(
                'platformUserId'      => array($platformUserId),
                'platformId'          => array($platformId),
                'deletedDate is null' => NULL,
            );
            $resultUserPlatformApplicationRelations     = $userPlatformApplicationRelationMapper->fetchAll($userPlatformApplicationRelationMapperWhere);

            if (empty($resultUserPlatformApplicationRelations)) {
                throw new Common_Exception_NotFound('処理対象が見つかりませんでした');
            }

            // ユーザIDを取り出す
            $resultUserPlatformApplicationRelation = $resultUserPlatformApplicationRelations[0];
            $userId                                = $resultUserPlatformApplicationRelation->getUserId();

            // 論理削除日時生成
            $deletedDate = date('Y-m-d H:i:s');

            // アプリケーションユーザプラットフォーム関連は更新されない可能性があるため、NotFoundとModifyについてはログを出力し、処理を継続する
            try {
                // アプリケーションユーザプラットフォーム関連の論理削除
                $applicationUserPlatformRelation = new Application_Model_ApplicationUserPlatformRelation($platformUser->toArray());
                $applicationUserPlatformRelation->setDeletedDate($deletedDate);
                $userLogic->updateApplicationUserPlatformRelation($applicationUserPlatformRelation);
            } catch (Common_Exception_NotFound $exc) {
                Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | メッセージ:%s', __CLASS__, __METHOD__, __LINE__, $exc->getMessage()));
            } catch (Common_Exception_NotModified $exc) {
                Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | メッセージ:%s', __CLASS__, __METHOD__, __LINE__, $exc->getMessage()));
            } catch (Exception $exc) {
                throw $exc;
            }

            // ユーザプラットフォームアプリケーション関連は更新されない可能性があるため、NotFoundとModifyについてはログを出力し、処理を継続する
            try {
                // ユーザプラットフォームアプリケーション関連の論理削除
                $userPlatformApplicationRelation = new Application_Model_UserPlatformApplicationRelation();
                $userPlatformApplicationRelation->setPlatformUserId($platformUserId);
                $userPlatformApplicationRelation->setPlatformId($platformId);
                $userPlatformApplicationRelation->setDeletedDate($deletedDate);
                $userLogic->updateUserPlatformApplicationRelation($userPlatformApplicationRelation);
            } catch (Common_Exception_NotFound $exc) {
                Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | メッセージ:%s', __CLASS__, __METHOD__, __LINE__, $exc->getMessage()));
            } catch (Common_Exception_NotModified $exc) {
                Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | メッセージ:%s', __CLASS__, __METHOD__, __LINE__, $exc->getMessage()));
            } catch (Exception $exc) {
                throw $exc;
            }

            // プラットフォームユーザを解除状態に更新
            $platformUser->setStatus(Logic_User::STATUS_INACTIVE);
            $userLogic->updatePlatformUser($platformUser);

            // ユーザ検証
            $user = new Application_Model_User();
            $user->setUserId($userId);
            $userLogic->disableNonFederationUser($user);


            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return TRUE;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * プラットフォームユーザ情報を登録する。
     *
     * @param Application_Model_PlatformUser $platformUser プラットフォームユーザモデル
     * @return Application_Model_PlatformUser プラットフォームユーザモデル
     */
    public function createPlatformUser(Application_Model_PlatformUser $platformUser)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            $platformUserId = $platformUser->getPlatformUserId();
            $platformId     = $platformUser->getPlatformId();
            $emailAddress   = $platformUser->getEmailAddress();
            $accessToken    = $platformUser->getAccessToken();
            $idToken        = $platformUser->getIdToken();

            // パラメータチェック
            $this->_isValidateValue($platformUserId);
            $this->_isValidateValue($platformId, 191);
            $this->_isValidateLength($emailAddress);
            $this->_isValidateLength($accessToken, 65535);
            $this->_isValidateLength($idToken, 65535);

            // application.iniからデータベース情報を取得する
            $config    = Zend_Registry::get('misp');
            $dbSection = $config['db']['main'];

            // Mapper取得
            $platformUserMapper = $this->getPlatformUserMapper($dbSection);
            $platformMapper     = $this->getPlatformMapper($dbSection);

            // プラットフォームの存在確認
            if (!$platformMapper->find($platformId)) {
                // 存在しないので例外を投げる
                throw new Common_Exception_NotFound('プラットフォームが存在しません。');
            }

            // 今から登録しようとしているプラットフォームユーザが、既に登録されているか確認
            if ($platformUserMapper->find($platformUserId, $platformId)) {
                // 既に登録されているので例外を投げる
                throw new Common_Exception_AlreadyExists('プラットフォームユーザが既に登録されています。');
            }

            // statusに1(有効)をセット
            $platformUser->setStatus(Logic_User::STATUS_ACTIVE);

            // 作成日時、更新日時をモデルにセット
            $setDate = date("Y-m-d H:i:s");
            $platformUser->setCreatedDate($setDate);
            $platformUser->setUpdatedDate($setDate);

            // insert Mapper
            if (!$platformUserMapper->insert($platformUser)) {
                throw new Exception('登録に失敗しました');
            }

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $platformUser;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ユーザプラットフォームアプリケーション関連情報を登録する。
     *
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     * @return Application_Model_UserPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     */
    public function createUserPlatformApplicationRelation(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            $userId            = $userPlatformApplicationRelation->getUserId();
            $platformUserId    = $userPlatformApplicationRelation->getPlatformUserId();
            $platformId        = $userPlatformApplicationRelation->getPlatformId();
            $applicationId     = $userPlatformApplicationRelation->getApplicationId();
            $accessToken       = $userPlatformApplicationRelation->getAccessToken();
            $refreshToken      = $userPlatformApplicationRelation->getRefreshToken();
            $idToken           = $userPlatformApplicationRelation->getIdToken();
            $authorizationCode = $userPlatformApplicationRelation->getAuthorizationCode();
            $createdDate       = $userPlatformApplicationRelation->getCreatedDate();

            // パラメータチェック
            $this->_isValidateValue($userId);
            $this->_isValidateValue($platformUserId);
            $this->_isValidateValue($platformId, 191);
            $this->_isValidateValue($applicationId, 11);

            $this->_isValidateLength($accessToken);
            $this->_isValidateLength($idToken, 65535);
            $this->_isValidateLength($refreshToken);
            $this->_isValidateLength($authorizationCode, 64);

            // application.iniからデータベース情報を取得する
            $config    = Zend_Registry::get('misp');
            $dbSection = $config['db']['main'];

            // Mapper取得
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSection);

            // 作成日時、更新日時をモデルにセット
            $setDate = date("Y-m-d H:i:s");
            $userPlatformApplicationRelation->setCreatedDate(strlen($createdDate) ? $createdDate : $setDate);
            $userPlatformApplicationRelation->setUpdatedDate($setDate);

            // 削除日時にNULLをセット
            $userPlatformApplicationRelation->setDeletedDate(NULL);

            // replace Mapper
            if (!$userPlatformApplicationRelationMapper->replace($userPlatformApplicationRelation)) {
                throw new Exception('登録に失敗しました');
            }

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $userPlatformApplicationRelation;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ユーザプラットフォームアプリケーション関連情報を登録する。
     *
     * @param Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation アプリケーションユーザプラットフォーム関連モデル
     * @return Application_Model_ApplicationUserPlatformRelation アプリケーションユーザプラットフォーム関連モデル
     */
    public function createApplicationUserPlatformRelation(Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            $applicationUserId  = $applicationUserPlatformRelation->getApplicationUserId();
            $applicationId      = $applicationUserPlatformRelation->getApplicationId();
            $applicationWorldId = $applicationUserPlatformRelation->getApplicationWorldId();
            $platformUserId     = $applicationUserPlatformRelation->getPlatformUserId();
            $platformId         = $applicationUserPlatformRelation->getPlatformId();

            // アプリケーションワールドIDが空の場合、空文字をセット
            if (!strlen($applicationWorldId)) {
                $applicationWorldId = '';
                $applicationUserPlatformRelation->setApplicationWorldId($applicationWorldId);
            }

            // パラメータチェック
            $this->_isValidateValue($applicationUserId);
            $this->_isValidateValue($applicationId, 11);
            $this->_isValidateLength($applicationWorldId);
            $this->_isValidateValue($platformUserId);
            $this->_isValidateValue($platformId, 191);

            // application.iniからデータベース情報を取得する
            $config    = Zend_Registry::get('misp');
            $dbSection = $config['db']['main'];

            // Mapper取得
            $userApplicationUserPlatformRelationMapper = $this->getApplicationUserPlatformRelationMapper($dbSection);

            // 作成日時、更新日時をモデルにセット
            $setDate = date("Y-m-d H:i:s");
            $applicationUserPlatformRelation->setCreatedDate($setDate);
            $applicationUserPlatformRelation->setUpdatedDate($setDate);

            // 削除日時にNULLをセット
            $applicationUserPlatformRelation->setDeletedDate(NULL);

            // replace Mapper
            if (!$userApplicationUserPlatformRelationMapper->replace($applicationUserPlatformRelation)) {
                throw new Exception('登録に失敗しました');
            }


            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $applicationUserPlatformRelation;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ユーザ情報を登録する。
     *
     * @return Application_Model_User ユーザモデル
     */
    public function createUser()
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            // application.iniからデータベース情報を取得する
            $config    = Zend_Registry::get('misp');
            $dbSection = $config['db']['main'];

            // Mapper取得
            $userMapper = $this->getUserMapper($dbSection);

            // 登録するユーザモデルを生成
            $user = new Application_Model_User();

            // statusに1(有効)をセット
            $user->setStatus(1);

            // 作成日時、更新日時をモデルにセット
            $setDate = date("Y-m-d H:i:s");
            $user->setCreatedDate($setDate);
            $user->setUpdatedDate($setDate);

            // insert Mapper
            $lastInsertId = $userMapper->insert($user);
            if (!$lastInsertId) {
                throw new Exception('登録に失敗しました');
            }

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            $user->setUserId($lastInsertId);

            return $user;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * プラットフォームユーザ情報を更新する。
     *
     * @param Application_Model_PlatformUser $platformUser プラットフォームユーザモデル
     * @return array(Application_Model_PlatformUser) プラットフォームユーザモデルの配列
     */
    public function updatePlatformUser(Application_Model_PlatformUser $platformUser)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            $platformUserId = $platformUser->getPlatformUserId();
            $platformId     = $platformUser->getPlatformId();
            $emailAddress   = $platformUser->getEmailAddress();
            $status         = $platformUser->getStatus();
            $accessToken    = $platformUser->getAccessToken();
            $idToken        = $platformUser->getIdToken();

            // パラメータチェック
            // プラットフォームユーザ更新項目チェック
            // status
            if (!in_array($status, $this->_statusAllowList)) {
                throw new Common_Exception_IllegalParameter('statusに不正なパラメータが指定されました');
            }

            $this->_isValidateValue($platformUserId);
            $this->_isValidateValue($platformId, 191);
            $this->_isValidateLength($emailAddress);
            $this->_isValidateLength($accessToken, 65535);
            $this->_isValidateLength($idToken, 65535);

            // application.iniからデータベース情報を取得する
            $config        = Zend_Registry::get('misp');
            $dbSectionName = $config['db']['main'];

            // Select Mapper
            $platformUserMapper = $this->getPlatformUserMapper($dbSectionName);
            $updatePlatformUser = $platformUserMapper->find($platformUserId, $platformId);

            if (!$updatePlatformUser) {
                throw new Common_Exception_NotFound('更新対象が存在しません');
            }

            $updateList = array('emailAddress', 'status', 'accessToken', 'idToken', 'platformUserName', 'platformUserDisplayName');

            // プラットフォームユーザ情報に更新する項目が存在する場合、更新情報を上書き
            $setParams = $this->_getUpdateParams($updateList, $platformUser);
            $updatePlatformUser->setOptions($setParams);

            // 更新日時をモデルにセット
            if (Misp_Util::isEmpty($updatePlatformUser->getDeletedDate())) {
                $updatePlatformUser->setUpdatedDate(date("Y-m-d H:i:s"));
            }

            // update Mapper
            if (!$platformUserMapper->update($updatePlatformUser, $platformUserId, $platformId)) {
                // 変更がなかった場合の例外
                throw new Common_Exception_NotModified('更新が行われませんでした');
            }

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $updatePlatformUser;
        } catch (Common_Exception_NotModified $exc) {
            // 変更がなかった場合の例外処理は内部ログ出力して処理続行
            // 現状、無変更を検知したとしても特にその後の処理がないのでログ出力に留める
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ユーザプラットフォームアプリケーション関連情報を更新する。
     *
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     * @return int 更新件数
     */
    public function updateUserPlatformApplicationRelation(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            $userId            = $userPlatformApplicationRelation->getUserId();
            $platformUserId    = $userPlatformApplicationRelation->getPlatformUserId();
            $platformId        = $userPlatformApplicationRelation->getPlatformId();
            $applicationId     = $userPlatformApplicationRelation->getApplicationId();
            $accessToken       = $userPlatformApplicationRelation->getAccessToken();
            $idToken           = $userPlatformApplicationRelation->getIdToken();
            $refreshToken      = $userPlatformApplicationRelation->getRefreshToken();
            $authorizationCode = $userPlatformApplicationRelation->getAuthorizationCode();
            $deletedDate       = $userPlatformApplicationRelation->getDeletedDate();

            // パラメータチェック
            $this->_isValidateLength($userId, PHP_INT_MAX);
            $this->_isValidateLength($platformUserId);
            $this->_isValidateLength($platformId, 191);
            $this->_isValidateLength($applicationId, 11);
            $this->_isValidateLength($accessToken);
            $this->_isValidateLength($idToken, 65535);
            $this->_isValidateLength($refreshToken);
            $this->_isValidateLength($authorizationCode, 64);

            // application.iniからデータベース情報を取得する
            $config        = Zend_Registry::get('misp');
            $dbSectionName = $config['db']['main'];

            // Select Mapper
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSectionName);


            // 呼び出すMapperメソッド名と更新対象確認のWHEREキーを生成
            $pk = array();
            if (strlen($platformUserId) && strlen($platformId) && !strlen($userId) && !strlen($applicationId)) {

                $mapperMethod = 'updateByPlatformUserIdAndPlatformId';
                $where        = array(
                    'platformUserId' => array($platformUserId),
                    'platformId'     => array($platformId)
                );
            } elseif (strlen($userId) && strlen($applicationId) && !strlen($platformUserId) && !strlen($platformId)) {

                $mapperMethod = 'updateByUserIdAndApplicationId';
                $where        = array(
                    'userId'        => array($userId),
                    'applicationId' => array($applicationId)
                );
            } elseif (strlen($userId) && strlen($platformId) && strlen($applicationId) && !strlen($platformUserId)) {

                $mapperMethod = 'updateUserIdAndPlatformIdAndApplicationId';
                $where        = array(
                    'userId'        => array($userId),
                    'platformId'    => array($platformId),
                    'applicationId' => array($applicationId)
                );
            } elseif (strlen($userId) && strlen($platformUserId) && strlen($platformId) && strlen($applicationId)) {

                $mapperMethod = 'update';
                $where        = array(
                    'userId'         => array($userId),
                    'platformUserId' => array($platformUserId),
                    'platformId'     => array($platformId),
                    'applicationId'  => array($applicationId)
                );
                $pk           = $where;

                if (strlen($authorizationCode)) {
                    // 認可コードをNULLにする更新なのでこのタイミングでセットしておく
                    $userPlatformApplicationRelation->setAuthorizationCode(NULL);
                }
            } else {
                throw new Common_Exception_IllegalParameter('キーの設定が不正です');
            }

            // 更新対象確認
            $result = $userPlatformApplicationRelationMapper->fetchAll($where);
            if (empty($result)) {
                throw new Common_Exception_NotFound('更新対象が存在しません');
            }

            // 更新日時をモデルにセット
            if (Misp_Util::isEmpty($userPlatformApplicationRelation->getDeletedDate())) {
                $userPlatformApplicationRelation->setUpdatedDate(date("Y-m-d H:i:s"));
            }

            // update Mapper
            if ($pk) {
                $result = $userPlatformApplicationRelationMapper->update($userPlatformApplicationRelation, $pk['userId'], $pk['platformUserId'], $pk['platformId'], $pk['applicationId']);
            } else {
                $result = $userPlatformApplicationRelationMapper->$mapperMethod($userPlatformApplicationRelation);
            }

            if (!$result) {
                // 変更がなかった場合の例外
                throw new Common_Exception_NotModified('更新が行われませんでした');
            }

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $result;
        } catch (Common_Exception_NotModified $exc) {
            // 変更がなかった場合の例外処理は内部ログ出力して処理続行
            // 現状、無変更を検知したとしても特にその後の処理がないのでログ出力に留める
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * アプリケーションユーザプラットフォーム関連情報を更新する。
     *
     * @param Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation アプリケーションユーザプラットフォーム関連モデル
     * @return array(Application_Model_ApplicationUserPlatformRelation) アプリケーションユーザプラットフォーム関連モデルの配列
     */
    public function updateApplicationUserPlatformRelation(Application_Model_ApplicationUserPlatformRelation $applicationUserPlatformRelation)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            $applicationUserId  = $applicationUserPlatformRelation->getApplicationUserId();
            $applicationId      = $applicationUserPlatformRelation->getApplicationId();
            $applicationWorldId = $applicationUserPlatformRelation->getApplicationWorldId();
            $platformUserId     = $applicationUserPlatformRelation->getPlatformUserId();
            $platformId         = $applicationUserPlatformRelation->getPlatformId();

            // パラメータチェック
            $this->_isValidateLength($applicationUserId);
            $this->_isValidateLength($applicationId, 11);
            $this->_isValidateLength($applicationWorldId);
            $this->_isValidateLength($platformUserId);
            $this->_isValidateLength($platformId, 191);

            // application.iniからデータベース情報を取得する
            $config        = Zend_Registry::get('misp');
            $dbSectionName = $config['db']['main'];

            // Select Mapper
            $applicationUserPlatformRelationMapper = $this->getApplicationUserPlatformRelationMapper($dbSectionName);

            // 呼び出すMapperメソッド名と更新対象確認のWHEREキーを生成
            $pk = array();
            if (strlen($platformUserId) && strlen($platformId) && !strlen($applicationUserId) && !strlen($applicationId)) {

                $mapperMethod = 'updateByPlatformUserIdAndPlatformId';
                $where        = array(
                    'platformUserId' => array($platformUserId),
                    'platformId'     => array($platformId)
                );
            } elseif (strlen($applicationUserId) && strlen($applicationId) && !strlen($platformUserId) && !strlen($platformId)) {

                $mapperMethod = 'updateByApplicationUserIdAndApplicationId';
                $where        = array(
                    'applicationUserId'  => array($applicationUserId),
                    'applicationWorldId' => array($applicationWorldId),
                    'applicationId'      => array($applicationId)
                );
            } elseif (strlen($platformUserId) && strlen($platformId) && !strlen($applicationUserId) && strlen($applicationId)) {

                $mapperMethod = 'updateByPlatformUserIdAndPlatformIdAndApplicationId';
                $where        = array(
                    'platformUserId' => array($platformUserId),
                    'platformId'     => array($platformId),
                    'applicationId'  => array($applicationId)
                );
            } elseif (strlen($applicationUserId) && strlen($applicationId) && strlen($platformUserId) && strlen($platformId)) {

                $mapperMethod = 'update';
                $where        = array(
                    'applicationUserId'  => array($applicationUserId),
                    'applicationId'      => array($applicationId),
                    'applicationWorldId' => array($applicationWorldId),
                    'platformUserId'     => array($platformUserId),
                    'platformId'         => array($platformId),
                );
                $pk           = $where;
            } else {
                throw new Common_Exception_IllegalParameter('キーの設定が不正です');
            }

            // 更新対象確認
            $result = $applicationUserPlatformRelationMapper->fetchAll($where);
            if (empty($result)) {
                throw new Common_Exception_NotFound('更新対象が存在しません');
            }

            // 更新日時をモデルにセット
            if (Misp_Util::isEmpty($applicationUserPlatformRelation->getDeletedDate())) {
                $applicationUserPlatformRelation->setUpdatedDate(date("Y-m-d H:i:s"));
            }

            // update Mapper
            if ($pk) {
                $result = $applicationUserPlatformRelationMapper->update($applicationUserPlatformRelation, $pk['applicationUserId'], $pk['applicationId'], $pk['applicationWorldId'], $pk['platformUserId'], $pk['platformId']);
            } else {
                $result = $applicationUserPlatformRelationMapper->$mapperMethod($applicationUserPlatformRelation);
            }

            if (!$result) {
                // 変更がなかった場合の例外
                throw new Common_Exception_NotModified('更新が行われませんでした');
            }

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $result;
        } catch (Common_Exception_NotModified $exc) {
            // 変更がなかった場合の例外処理は内部ログ出力して処理続行
            // 現状、無変更を検知したとしても特にその後の処理がないのでログ出力に留める
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ユーザ情報を更新する。
     *
     * @param Application_Model_User $user ユーザモデル
     * @return array(Application_Model_User) ユーザモデルの配列
     */
    public function updateUser(Application_Model_User $user)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            $userId = $user->getUserId();
            $status = $user->getStatus();

            // パラメータチェック
            // ユーザ更新項目チェック
            // status
            if (!in_array($status, $this->_statusAllowList)) {
                throw new Common_Exception_IllegalParameter('statusに不正なパラメータが指定されました');
            }

            $this->_isValidateValue($userId);

            // application.iniからデータベース情報を取得する
            $config        = Zend_Registry::get('misp');
            $dbSectionName = $config['db']['main'];

            // Select Mapper
            $userMapper = $this->getUserMapper($dbSectionName);
            $updateUser = $userMapper->find($userId);

            if (!$updateUser) {
                throw new Common_Exception_NotFound('更新対象が存在しません');
            }

            $updateList = array('status');

            // ユーザ情報に更新する項目が存在する場合、更新情報を上書き
            $setParams = $this->_getUpdateParams($updateList, $user);
            $updateUser->setOptions($setParams);

            // 更新日時をモデルにセット
            if (Misp_Util::isEmpty($updateUser->getDeletedDate())) {
                $updateUser->setUpdatedDate(date("Y-m-d H:i:s"));
            }

            // update Mapper
            if (!$userMapper->update($updateUser, $userId)) {
                // 変更がなかった場合の例外
                throw new Common_Exception_NotModified('更新が行われませんでした');
            }

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $updateUser;
        } catch (Common_Exception_NotModified $exc) {
            // 変更がなかった場合の例外処理は内部ログ出力して処理続行
            // 現状、無変更を検知したとしても特にその後の処理がないのでログ出力に留める
            Common_Log::getInternalLog()->info(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ユーザプラットフォームアプリケーション関連からプラットフォームユーザの情報を取得し、
     * アクセストークン・IDトークンを検証する
     *
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     * @return Application_Model_UserPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     */
    public function readUserPlatformApplicationRelationWithValidate(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        // application.iniからデータベース情報を取得する
        $config        = Zend_Registry::get('misp');
        $dbSectionName = $config['db']['main'];
        try {
            // パラメータ取得
            $accessToken = $userPlatformApplicationRelation->getAccessToken();
            $idToken     = $userPlatformApplicationRelation->getIdToken();

            // パラメータチェック
            $this->_isValidateValue($accessToken);
            $this->_isValidateValue($idToken, 65535);

            // IDトークンを分解し、
            // ユーザID,アプリケーションIDを取り出す
            $payload       = Common_Oidc_Token::decodeIdToken($idToken);
            $userId        = $payload['sub'];
            $applicationId = $payload['aud'];


            // Select Mapper
            $applicationMapper = $this->getApplicationMapper($dbSectionName);


            // アプリケーションテーブルからデータ取得
            //   (IDトークン検証のために、アプリケーション秘密鍵が必要)
            $applicationWhere = array('applicationId' => array($applicationId));
            $applications     = $applicationMapper->fetchAll($applicationWhere);

            // データ取得できなければ不正として例外を投げる
            if (empty($applications)) {
                throw new Common_Exception_Oidc_InvalidToken('データが存在しません');
            }
            $application       = $applications[0];
            $applicationSecret = $application->getApplicationSecret();


            // ユーザプラットフォームアプリケーション関連からデータ取得
            $userPlatformApplicationRelationCondition = new Application_Model_UserPlatformApplicationRelation();

            $userPlatformApplicationRelationCondition->setUserId($userId);
            $userPlatformApplicationRelationCondition->setAccessToken($accessToken);
            $userPlatformApplicationRelationCondition->setApplicationId($applicationId);
            $resultUserPlatformApplicationRelations = $this->readUserPlatformApplicationRelation($userPlatformApplicationRelationCondition);

            // データ取得できなければ不正として例外を投げる
            if (empty($resultUserPlatformApplicationRelations)) {
                throw new Common_Exception_Oidc_InvalidToken('データが存在しません');
            }

            // アクセストークンの正当性とIDトークンのヘッダ部・ペイロード部・シグネチャ部の正当性を確認
            // アクセストークンが正しいのでデータ取得できるが、
            // IDトークンが一致していない可能性があるので、
            // データ取得したIDトークンと入力のIDトークンを比較する
            $resultUserPlatformApplicationRelation = $resultUserPlatformApplicationRelations[0];
            if ($idToken != $resultUserPlatformApplicationRelation->getIdToken()) {
                throw new Common_Exception_Oidc_InvalidToken('データが存在しません');
            }

            // IDトークンの検証（有効期限チェックとして使用）
            if (!Common_Oidc_Token::isValidIdToken($idToken, new Common_Oidc_IdToken_Payload($payload), $accessToken, $applicationSecret)) {
                throw new Common_Exception_Oidc_InvalidToken('IDトークンの検証に失敗しました');
            }

            return $resultUserPlatformApplicationRelation;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * 認可コードの正当性を検証し、トークンを返す。
     *
     * @param Common_Oidc_Authorization_Authorization $authorization Authorizationモデル
     * @return Application_Model_UserPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     */
    public function readTokenForBasic(Common_Oidc_Authorization_Authorization $authorization)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            $clientId    = $authorization->getClientId();
            $code        = $authorization->getCode();
            $redirectUri = $authorization->getRedirectUri();

            // パラメータチェック
            $this->_isValidateValue($clientId, 11);
            $this->_isValidateValue($code, 64);
            $this->_isValidateValue($redirectUri);

            // application.iniからデータベース情報を取得する
            $config    = Zend_Registry::get('misp');
            $dbSection = $config['db']['main'];

            // Mapper取得
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSection);

            // ユーザロジック取得
            $userLogic = $this->getUserLogic();

            // Marvelous Connect に登録されているアプリケーションとリダイレクトURIの組み合わせが正しいか検証(Basic 2.1.1.1)
            if (!$userLogic->isValidRedirectUri($authorization)) {
                throw new Common_Exception_OauthInvalidRequest(sprintf('OAuthリクエスト不正: application_id: %s | redirect_uri: %s', $clientId, $redirectUri));
            }

            $userPlatformApplicationRelations = $userPlatformApplicationRelationMapper->fetchAll(array('applicationId' => array($clientId), 'authorizationCode' => array($code)));
            // 見つからないのはあり得ない(想定外)ので、例外
            if (empty($userPlatformApplicationRelations)) {
                throw new Common_Exception_OauthInvalidGrant(sprintf('OAuth認可コード不正: application_id: %s | code: %s', $clientId, $code));
            }

            // 有効期限確認(see→application.ini:misp.authorizationCode.expire)
            //   有効期限：ユーザの認可コード日付に規定時間を加算したもの
            $userAuthorizationCodeDate = new Zend_Date($userPlatformApplicationRelations[0]->getUpdatedDate());
            $authorizationCodeExpire   = new Zend_Date($userAuthorizationCodeDate);
            $authorizationCodeExpire->add($config['authorizationCode']['expire'], Zend_Date::SECOND);
            //   現在日時
            $authorizationCodeNow      = new Zend_Date();
            // 有効期限切れかどうか
            if ($authorizationCodeExpire->isEarlier($authorizationCodeNow)) {
                throw new Common_Exception_OauthInvalidGrant(sprintf('OAuth認可コード不正: application_id: %s | code: %s | 認可コード有効期限切れ | 認可コード日付: %s | 認可コード有効期限: %s | 有効期限チェックを行った時刻: %s', $clientId, $code, $userAuthorizationCodeDate, $authorizationCodeExpire, $authorizationCodeNow));
            }

            // ユーザプラットフォームアプリケーション関連更新
            if (!$userLogic->updateUserPlatformApplicationRelation($userPlatformApplicationRelations[0])) {
                throw new Common_Exception_OauthInvalidGrant(sprintf('OAuth認可コード不正: application_id: %s | code: %s | 更新エラー', $clientId, $code));
            }

            // 認可コード使用済みとしてNULLにする
            $userPlatformApplicationRelations[0]->setAuthorizationCode(NULL);

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $userPlatformApplicationRelations[0];
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * リフレッシュトークンによるトークン取得
     *
     * @param Common_Oidc_Authorization_Authorization $authorization Authorizationモデル
     * @return Application_Model_UserPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     */
    public function readTokenForRefreshToken(Common_Oidc_Authorization_Authorization $authorization)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            $clientId     = $authorization->getClientId();
            $refreshToken = $authorization->getRefreshToken();

            // パラメータチェック
            $this->_isValidateValue($clientId, 11);
            $this->_isValidateValue($refreshToken);


            // application.iniからデータベース情報を取得する
            $config    = Zend_Registry::get('misp');
            $dbSection = $config['db']['main'];

            // Mapper取得
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSection);

            // ロジック取得
            $applicationLogic = $this->getApplicationLogic();
            $userLogic        = $this->getUserLogic();

            $userPlatformApplicationRelations = $userPlatformApplicationRelationMapper->fetchAll(array('applicationId' => array($clientId), 'refreshToken' => array($refreshToken)));
            // 見つからないのはあり得ない(想定外)ので、例外
            if (empty($userPlatformApplicationRelations)) {
                throw new Common_Exception_OauthInvalidGrant(sprintf('リフレッシュトークン不正: application_id: %s | refreshToken: %s', $clientId, $refreshToken));
            }

            // アクセストークンを再生成
            $regenerateAccessToken  = Common_Oidc_Token::generateAccessToken();
            // リフレッシュトークンを再生成
            $regenerateRefreshToken = Common_Oidc_Token::generateRefreshToken();

            // IDトークンを再構築
            //   アプリケーション秘密鍵を取得(IDトークン生成に必要)
            $applicationCondition  = new Application_Model_Application();
            $applicationCondition->setApplicationId($clientId);
            $application           = $applicationLogic->readApplication($applicationCondition);
            $applicationSecret     = $application->getApplicationSecret();
            //   既存のIDトークンをデコード
            $decodedPayload        = Common_Oidc_Token::decodeIdToken($userPlatformApplicationRelations[0]->getIdToken());
            //   デコードしたペイロードの有効期限など、時間情報部分だけ更新
            $maxAge                = $decodedPayload['exp'] - $decodedPayload['iat'];  // 差分をとることで延長する時間が得られる
            $iat                   = time();
            $exp                   = $iat + $maxAge;
            $decodedPayload['exp'] = $exp;
            $decodedPayload['iat'] = $iat;
            //   IDトークン再生成
            Common_Oidc_Token::setRequiredKeys(array('iss', 'sub', 'aud', 'exp', 'iat'));
            $regenerateIdToken     = Common_Oidc_Token::generateIdToken($decodedPayload, $regenerateAccessToken, $applicationSecret);

            // 各種情報を更新用モデルにセット
            $userPlatformApplicationRelations[0]->setAccessToken($regenerateAccessToken);
            $userPlatformApplicationRelations[0]->setIdToken($regenerateIdToken);
            $userPlatformApplicationRelations[0]->setRefreshToken($regenerateRefreshToken);

            // ユーザプラットフォームアプリケーション関連更新
            if (!$userLogic->updateUserPlatformApplicationRelation($userPlatformApplicationRelations[0])) {
                throw new Common_Exception_OauthInvalidGrant(sprintf('OAuth認可コード不正: application_id: %s | code: %s | 更新エラー', $clientId, $refreshToken));
            }

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $userPlatformApplicationRelations[0];
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ユーザプラットフォームアプリケーション関連からプラットフォームユーザの情報を取得する
     *
     * @param Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     * @return Application_Model_UserPlatformApplicationRelation ユーザプラットフォームアプリケーション関連モデル
     */
    public function readUserPlatformApplicationRelation(Application_Model_UserPlatformApplicationRelation $userPlatformApplicationRelation)
    {
        // application.iniからデータベース情報を取得する
        $config        = Zend_Registry::get('misp');
        $dbSectionName = $config['db']['main'];
        try {
            // パラメータ取得
            $accessToken       = $userPlatformApplicationRelation->getAccessToken();
            $idToken           = $userPlatformApplicationRelation->getIdToken();
            $refreshToken      = $userPlatformApplicationRelation->getRefreshToken();
            $userId            = $userPlatformApplicationRelation->getUserId();
            $applicationId     = $userPlatformApplicationRelation->getApplicationId();
            $platformId        = $userPlatformApplicationRelation->getPlatformId();
            $platformUserId    = $userPlatformApplicationRelation->getPlatformUserId();
            $authorizationCode = $userPlatformApplicationRelation->getAuthorizationCode();

            // パラメータチェック
            $this->_isValidateLength($accessToken);
            $this->_isValidateLength($idToken, 65535);
            $this->_isValidateLength($refreshToken);
            $this->_isValidateLength($applicationId, 11);
            $this->_isValidateLength($platformId, 191);
            $this->_isValidateLength($platformUserId);
            $this->_isValidateLength($authorizationCode, 64);


            // Select Mapper
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSectionName);


            // ユーザプラットフォームアプリケーション関連からデータ取得するWHERE句
            $userPlatformApplicationRelationWhere = array();
            if (strlen($accessToken)) {
                $userPlatformApplicationRelationWhere['accessToken'] = array($accessToken);
            }
            if (strlen($idToken)) {
                $userPlatformApplicationRelationWhere['idToken'] = array($idToken);
            }
            if (strlen($refreshToken)) {
                $userPlatformApplicationRelationWhere['refreshToken'] = array($refreshToken);
            }
            if (strlen($userId)) {
                $userPlatformApplicationRelationWhere['userId'] = array($userId);
            }
            if (strlen($applicationId)) {
                $userPlatformApplicationRelationWhere['applicationId'] = array($applicationId);
            }
            if (strlen($platformId)) {
                $userPlatformApplicationRelationWhere['platformId'] = array($platformId);
            }
            if (strlen($platformUserId)) {
                $userPlatformApplicationRelationWhere['platformUserId'] = array($platformUserId);
            }
            if (strlen($authorizationCode)) {
                $userPlatformApplicationRelationWhere['authorizationCode'] = array($authorizationCode);
            }
            $userPlatformApplicationRelationWhere['deletedDate IS NULL'] = NULL;

            // 取得
            $resultUserPlatformApplicationRelations = $userPlatformApplicationRelationMapper->fetchAll($userPlatformApplicationRelationWhere);

            Common_Log::getInternalLog()->info('method:' . __METHOD__ . ' | 取得ユーザプラットフォームアプリケーション関連' . print_r($resultUserPlatformApplicationRelations, 1));

            // データ取得できなければ不正として例外を投げる
            if (empty($resultUserPlatformApplicationRelations)) {
                throw new Common_Exception_Oidc_InvalidToken('データが存在しません');
            }

            return $resultUserPlatformApplicationRelations;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * 非連携ユーザを無効化する。
     *
     * ユーザプラットフォームアプリケーション関連にユーザIDがない場合、
     * (↑MISP、プラットフォーム、アプリケーション間の連携が１つもない状態)
     * ユーザを無効にする
     *
     * @param Application_Model_User $user ユーザモデル
     * @return boolean true ユーザを無効化した
     *                  false ユーザを無効化しなかった
     */
    public function disableNonFederationUser(Application_Model_User $user)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            // パラメータ取得
            $userId = $user->getUserId();

            // パラメータチェック
            $this->_isValidateValue($userId, PHP_INT_MAX);

            // application.iniからデータベース情報を取得する
            $config        = Zend_Registry::get('misp');
            $dbSectionName = $config['db']['main'];

            // Select Mapper
            $userPlatformApplicationRelationMapper = $this->getUserPlatformApplicationRelationMapper($dbSectionName);

            // ユーザ情報を無効化するかの判断のために、ユーザプラットフォームアプリケーション関連を検索
            $result = $userPlatformApplicationRelationMapper->fetchAll(array('userId' => array($userId), 'deletedDate IS NULL' => NULL));
            // ユーザプラットフォームアプリケーション関連にユーザが登録されていれば何もせず終了
            if ($result) {
                Common_Db::commit();
                return FALSE;
            }

            // ユーザプラットフォームアプリケーション関連にユーザが登録されていないので、
            // (＝MISPとユーザの連携が完全に切れた状態)
            // ユーザ情報を無効化する
            // statusに0(無効)をセット
            $user->setStatus(0);

            // ユーザ情報を更新
            $this->updateUser($user);

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return TRUE;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * 送られてきたリダイレクトURIが正しいかチェックする。
     *
     * @param Common_Oidc_Authorization_Authorization $authorization Authorizationモデル
     * @return boolean true 正しいリダイレクトURI
     *                  false 不正なリダイレクトURI
     */
    public function isValidRedirectUri(Common_Oidc_Authorization_Authorization $authorization)
    {
        try {
            // パラメータ取得
            $clientId    = $authorization->getClientId();
            $redirectUri = $authorization->getRedirectUri();

            // パラメータチェック
            $this->_isValidateValue($clientId, 11);
            $this->_isValidateValue($redirectUri);

            // application.iniからデータベース情報を取得する
            $config        = Zend_Registry::get('misp');
            $dbSectionName = $config['db']['sub'];

            // Mapper取得
            $applicationRedirectUriMapper = $this->getApplicationRedirectUriMapper($dbSectionName);

            // Marvelous Connect に登録されているアプリケーションとリダイレクトURIの組み合わせが正しいか検証(Basic 2.1.1.1)
            list($uri) = explode('?', $redirectUri);

            $resultApplicationRedirectUri = $applicationRedirectUriMapper->fetchAll(array('applicationId' => array($clientId), 'redirectUri' => array($uri)));

            // データ取得できなければfalseを返す
            if (empty($resultApplicationRedirectUri)) {
                return false;
            }

            return true;
        } catch (Exception $exc) {
            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * プラットフォーム情報を取得します。
     * 
     * プラットフォーム情報をインスタンスにキャッシュしていて、
     * そのプラットフォームIDと引数のプラットフォームIDが同じ場合は
     * DB検索せずそれを即座に返却します。
     * 
     * @param string $platformId 取得したいプラットフォーム情報のID
     * @return Application_Model_Platform
     * @throws Exception
     * @throws Common_Exception_NotFound
     */
    public function readPlatformWithCache($platformId)
    {
        try {

            if ($this->_platform && $this->_platform->getPlatformId() == $platformId) {
                return $this->_platform;
            }

            // ロジック
            $userLogic = $this->getUserLogic();

            // プラットフォーム取得
            $platform = $userLogic->readPlatform(new Application_Model_Platform(array('platformId' => $platformId)));

            if (empty($platform)) {
                throw new Common_Exception_NotFound('取得対象が存在しません');
            }

            $this->_platform = $platform[0];

            return $this->_platform;
        } catch (Exception $exc) {

            Common_Log::getExceptionLog()->setException($exc);
            Common_Log::getExceptionLog()->error(sprintf('発生クラス:%s | 発生メソッド:%s | 発生行数:%s | 例外詳細: FILE->%s LINE->%s MESSAGE->%s', __CLASS__, __METHOD__, __LINE__, $exc->getFile(), $exc->getLine(), $exc->getMessage()));

            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * Accountsで情報を返す必要のないプラットフォームの場合、TRUEを返す
     * 
     * ログイン中のものでなければスキップする。
     * 
     * 詳細はMarvelous_Connect_アクティビティ図 の [ユーザ取得API] 参照
     * 
     * @param string $addPlatformId ユーザIDから取得したプラットフォームID
     * @param string $platformId トークンから取得したプラットフォームID
     * @return array オブジェクトの配列
     */
    private function _doNotBuildAccount($addPlatformId, $platformId)
    {
        if ($addPlatformId != $platformId) {
            return TRUE;
        }
        return FALSE;
    }

}
