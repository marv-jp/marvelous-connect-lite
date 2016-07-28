<?php

class Marvelous_PeopleController extends Marvelous_Base_RestController
{

    public function indexAction()
    {
        // get に forward し、とりあえずエラーとさせる
        $this->_forward('get');
    }

    public function getAction()
    {
        // リクエスト取得
        $request = $this->getRequest();
        try {
            // APIモード判定
            $apiMode = Misp_ApiMode::getInstance();

            // パラメータ取得
            $id          = $request->getParam('id'); // 未使用だがURI形式上、route定義している
            $selector    = $request->getParam('selector');
            $accessToken = $request->getParam('access_token');
            $idToken     = $request->getParam('id_token');

            // モデル
            $userPlatformApplicationRelation = new Application_Model_UserPlatformApplicationRelation();
            $userPlatformApplicationRelation->setAccessToken($accessToken);
            $userPlatformApplicationRelation->setIdToken($idToken);


            // ロジック
            $logicUser            = new Logic_User();
            $logicApplicationUser = new Logic_ApplicationUser();
            // 各種依存ロジックをセット
            $logicApplicationUser->setApplicationUserLogic($logicApplicationUser);
            $logicUser->setUserLogic($logicUser);
            $logicUser->setApplicationUserLogic($logicApplicationUser);
            $logicUser->setMispApiMode($apiMode);


            // 返却データ
            $returnData = array();

            switch ($selector) {

                // @self
                case Misp_Base_RestController::SELECTOR_SELF:

                    // ユーザ取得処理
                    $user = $logicUser->readUser($userPlatformApplicationRelation);

                    // 返却データ構築
                    $returnData['startIndex']   = 1;
                    $returnData['itemsPerPage'] = 1;
                    $returnData['totalResults'] = 1;
                    $entries                    = array();
                    $entry                      = array();
                    $entry['id']                = $user->getUserId();
                    $entry['published']         = date('c', strtotime($user->getCreatedDate()));
                    $entry['updated']           = date('c', strtotime($user->getUpdatedDate()));

                    // セレクターが @self の場合、ゲームを遊んでいること前提なので
                    // hasAppはTRUE固定で問題ない
                    $entry['hasApp'] = TRUE;

                    // Apps
                    $entry['apps'] = $this->_generateApps($user->getApps());

                    // Accounts
                    $entry['accounts'] = $this->_generateAccounts($user->getAccounts());

                    $entries[] = $entry;

                    $returnData['entry'] = $entries;

                    break;

                default:
                    throw new Common_Exception_IllegalParameter('セレクタのパラメータが不正です');
            }

            // 正常終了、連想配列はJSON形式に変更して返す
            $response = $this->getResponse();
            $response->setHttpResponseCode(200);
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody(Zend_Json::encode($returnData));
            //
        } catch (Common_Exception_Abstract $exc) {

            $response = $this->_responseException($exc);
        } catch (Exception $exc) {

            // 500エラー
            $response = $this->getResponse();
            $response->setHttpResponseCode(500);
            $response->setException($exc);
        }
    }

    public function deleteAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(405);
        $response->setBody(Zend_Http_Response::responseCodeAsText(405));
    }

    public function postAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(405);
        $response->setBody(Zend_Http_Response::responseCodeAsText(405));
    }

    /**
     * ユーザ更新API
     */
    public function putAction()
    {
        // リクエスト取得
        $request = $this->getRequest();
        try {
            // APIモード判定
            $apiMode = Misp_ApiMode::getInstance();

            // REST-Query-Parameters
            $id       = $request->getParam('id'); // 未使用だがURI形式上、route定義している
            $selector = $request->getParam('selector');

            // REST-Request-Payload
            $bodyParam = Zend_Json::decode($request->getRawBody());
            //   entry
            if (!isset($bodyParam['entry'][0])) {
                throw new Common_Exception_IllegalParameter('パラメータが不正です');
            }
            $inEntry = $bodyParam['entry'][0];

            // apps が存在しない場合、パラメータエラー
            if (!isset($inEntry['apps'])) {
                throw new Common_Exception_IllegalParameter('パラメータが不正です');
            }
            $inApps     = $inEntry['apps'];

            // REST-URI-Fragment
            //   Trusted Proxy
            //      id:@me
            if ('@me' != $id) {
                throw new Common_Exception_IllegalParameter('パラメータが不正です');
            }

            // ロジック
            $logicUser            = new Logic_User();
            $logicApplicationUser = new Logic_ApplicationUser();
            // 各種依存ロジックをセット
            $logicApplicationUser->setApplicationUserLogic($logicApplicationUser);
            $logicUser->setUserLogic($logicUser);
            $logicUser->setApplicationUserLogic($logicApplicationUser);
            $logicUser->setMispApiMode($apiMode);

            // 返却データ用の配列
            $returnData = array();

            // SELECTOR
            switch ($selector) {

                // @self
                case Misp_Base_RestController::SELECTOR_SELF:

                    // apps のInput分ID連携処理を行う
                    $resultUserPlatformApplicationRelation = $this->_idFederation($logicUser, $inApps);


                    // 更新したユーザの取得
                    // $resultUserPlatformApplicationRelationは最新のアクセストークン、IDトークンのものを使用
                    $user = $logicUser->readUser($resultUserPlatformApplicationRelation);

                    // 返却データ構築
                    $returnData['startIndex']   = 1;
                    $returnData['itemsPerPage'] = 1;
                    $returnData['totalResults'] = 1;
                    $entries                    = array();
                    $entry                      = array();
                    $entry['id']                = $resultUserPlatformApplicationRelation->getUserId();
                    $entry['accessToken']       = $resultUserPlatformApplicationRelation->getAccessToken();
                    $entry['idToken']           = $resultUserPlatformApplicationRelation->getIdToken();
                    $entry['published']         = date('c', strtotime($resultUserPlatformApplicationRelation->getCreatedDate()));
                    $entry['updated']           = date('c', strtotime($resultUserPlatformApplicationRelation->getUpdatedDate()));
                    $entry['hasApp']            = TRUE;
                    $entry['apps']              = $this->_generateApps($user->getApps());
                    $entry['accounts']          = $this->_generateAccounts($user->getAccounts());
                    $entries[]                  = $entry;
                    $returnData['entry']        = $entries;
                    break;

                default:
                    throw new Common_Exception_IllegalParameter('セレクタのパラメータが不正です');
            }

            // 正常終了、連想配列はJSON形式に変更して返す
            $response = $this->getResponse();
            $response->setHttpResponseCode(201);
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody(Zend_Json::encode($returnData));
            //
        } catch (Common_Exception_Abstract $exc) {

            $response = $this->_responseException($exc);
        } catch (Exception $exc) {
            // 500エラー
            // デバッグ用にinfoログは出力する
            $response = $this->getResponse();
            $response->setHttpResponseCode(500);
            $response->setException($exc);
        }
    }

    /**
     * appsのデータを構築
     * 
     * Application_Model_ApplicationUser の配列から app 構造の配列を返却します。
     * 
     * <b>返却する app 配列構造</b><br>
     * <br>
     * <pre>
     * Array
     * (
     *     [0] => Array
     *         (
     *             [value] => Array
     *                 (
     *                     [appId]     => アプリケーションID
     *                     [username]  => アプリケーションユーザ名(OpenSocial#People#displayNameと同義)
     *                     [userId]    => アプリケーションワールドID:アプリケーションID もしくは アプリケーションユーザ名
     *                     [password]  => アプリケーションユーザパスワード
     *                     [published] => アプリケーションユーザ作成日時
     *                     [updated]   => アプリケーションユーザ更新日時
     *                 )
     * 
     *             [type] => app
     *         )
     *     [1] = Array
     *         (
     *          ... 
     *         )
     * 
     * )
     * 
     * <b>app 項目</b><br>
     * <br>
     * <pre>
     * appId    : アプリケーションID
     * username : アプリケーションユーザ名(OpenSocial#Peopel#displayNameと同義)
     * userId   : アプリケーションワールドID:アプリケーションユーザ名 もしくは アプリケーションユーザID
     * password : アプリケーションユーザパスワード
     * published: アプリケーションユーザ作成日時
     * updated  : アプリケーションユーザ更新日時
     * </pre>
     * 
     * @param array $applicationUsers Application_Model_ApplicationUser の配列
     * @return array app 構造の配列
     */
    private function _generateApps(array $applicationUsers)
    {
        $apps = array();

        // メソッドから返ってきた、アプリケーションユーザーモデルから戻り値を取得
        // 連想配列の形にする
        $statusArray = array_flip($this->_statusAllowList);

        foreach ($applicationUsers as $applicationUser) {
            // 返却必須だが、NULLがあり得る項目は、NULLの際に空文字に変換する
            $username = strlen($applicationUser->getApplicationUserName()) ? $applicationUser->getApplicationUserName() : '';
            $password = strlen($applicationUser->getPassword()) ? $applicationUser->getPassword() : '';
            $status   = strlen($applicationUser->getStatus()) ? $statusArray[$applicationUser->getStatus()] : '';

            $applicationId = $applicationUser->getApplicationId();

            // 配列に入れなおす
            $value = array(
                'appId'     => $applicationId,
                'username'  => $username,
                'userId'    => Misp_Util::normalizeUserId($applicationUser),
                'password'  => $password,
                'status'    => $status,
                'published' => date('c', strtotime($applicationUser->getCreatedDate())),
                'updated'   => date('c', strtotime($applicationUser->getUpdatedDate())),
            );

            $apps[] = array(
                'value' => $value,
                'type'  => 'app',
            );
        }

        return $apps;
    }

    /**
     * accountsのデータを構築
     * 
     * Application_Model_PlatformUser の配列から accounts 構造の配列を返却します。
     * 
     * <b>返却する accounts 配列構造</b><br>
     * <br>
     * メソッド一覧参照
     * 
     * @param array $platformUsers Application_Model_ApplicationUser の配列
     * @param int $userId ユーザID
     * @param string $applicationId アプリケーションID
     * @return array accounts 構造の配列
     */
    private function _generateAccounts(array $platformUsers)
    {
        $accounts = array();

        $logicUser = new Logic_User();

        foreach ($platformUsers as $platformUser) {

            // 返却必須だが、NULLがあり得る項目は、NULLの際に空文字に変換する
            $username          = strlen($platformUser->getPlatformUserName()) ? $platformUser->getPlatformUserName() : '';
            $displayName       = strlen($platformUser->getPlatformUserDisplayName()) ? $platformUser->getPlatformUserDisplayName() : '';
            $accessToken       = strlen($platformUser->getAccessToken()) ? $platformUser->getAccessToken() : '';
            $accessTokenSecret = strlen($platformUser->getIdToken()) ? $platformUser->getIdToken() : '';

            $platforms = $logicUser->readPlatform(new Application_Model_Platform(
                    array('platformId' => $platformUser->getPlatformId())
            ));

            $value = array(
                'domain'            => $platforms[0]->getPlatformDomain(),
                'userId'            => $platformUser->getPlatformUserId(),
                'username'          => $username,
                'displayName'       => $displayName,
                'accessToken'       => $accessToken,
                'accessTokenSecret' => $accessTokenSecret,
                'published'         => date('c', strtotime($platformUser->getCreatedDate())),
                'updated'           => date('c', strtotime($platformUser->getUpdatedDate())),
            );

            $accounts[] = array(
                'value' => $value,
                'type'  => 'account',
            );
        }

        return $accounts;
    }

    /**
     * ID連携処理の煩雑な部分を切り出したメソッド
     * 
     * API層の処理から逸脱しない範囲でロジックメソッドに渡すデータの整形を行い、ロジックコールするコンセプト
     * 
     * @param Logic_User $logicUser ユーザロジック
     * @param array $inApps appsの配列
     * @return Application_Model_UserPlatformApplicationRelation
     * @throws Common_Exception_IllegalParameter
     * @throws Common_Exception_NotAcceptable
     */
    private function _idFederation($logicUser, $inApps = array())
    {
        // アクセストークン、IDトークンを取得
        $accessToken = $this->getRequest()->getParam('access_token');
        $idToken     = $this->getRequest()->getParam('id_token');

        $payload       = Common_Oidc_Token::decodeIdToken($idToken);
        $applicationId = $payload['aud'];

        // apps のInput分ID連携処理を行う
        if (!empty($inApps)) {

            foreach ($inApps as $inApp) {

                $applicationUserName = NULL;
                $password            = NULL;

                // アプリケーションユーザIDの必須チェック
                if (!isset($inApp['value']['userId']) && !strlen($inApp['value']['userId'])) {
                    throw new Common_Exception_IllegalParameter('パラメータが不正です');
                }
                // アプリケーションIDの必須チェック
                if (!isset($inApp['value']['appId']) && !strlen($inApp['value']['appId'])) {
                    throw new Common_Exception_IllegalParameter('パラメータが不正です');
                }
                // アプリケーションユーザ名がセットされていれば使用
                if (isset($inApp['value']['username']) && strlen($inApp['value']['username'])) {
                    $applicationUserName = $inApp['value']['username'];
                }

                //   パスワードの登録/更新は可能とするので、 パスワードがセットされていれば使用する。
                //   パスワード項目があるが値が未セットの場合は、パスワードを自動生成し、それを利用する
                if (isset($inApp['value']['password'])) {

                    // password 項目があるが、値が未セットの場合はこちらで生成してあげる
                    $password = $inApp['value']['password'];
                    if (!strlen($inApp['value']['password'])) {
                        $password = Common_Oidc_Token::generatePassword();
                    }
                }

                // idからアプリケーションワールドIDとアプリケーションユーザIDを取得
                list($applicationWorldId, $applicationUserId) = Misp_Util::pickUpApplicationUserIdAndApplicationWorldId($inApp['value']['userId']);
                $applicationId = $inApp['value']['appId'];

                // ユーザプラットフォームアプリケーション関連モデルの準備
                $userPlatformApplicationRelation = new Application_Model_UserPlatformApplicationRelation();

                // アプリケーションユーザモデルの準備
                $appliationUser = new Application_Model_ApplicationUser();
                $appliationUser->setApplicationUserId($applicationUserId);
                $appliationUser->setApplicationId($applicationId);
                $appliationUser->setApplicationWorldId($applicationWorldId);
                $appliationUser->setApplicationUserName($applicationUserName);
                $appliationUser->setPassword($password);
                $appliationUser = $this->_generateApplicationUserData($appliationUser, $inApp['value'], array('status' => 'setStatus',));

                //
                $userPlatformApplicationRelation->setAccessToken($accessToken);
                $userPlatformApplicationRelation->setIdToken($idToken);
                //
                // ID連携処理
                // $resultUserPlatformApplicationRelationは最新のアクセストークン、IDトークンを取得する必要が有るため毎回受ける
                $resultUserPlatformApplicationRelation = $logicUser->createIdFederation($userPlatformApplicationRelation, $appliationUser);
                // 最新のアクセストークン、IDトークンに更新
                $accessToken                           = $resultUserPlatformApplicationRelation->getAccessToken();
                $idToken                               = $resultUserPlatformApplicationRelation->getIdToken();
            }
        }

        return $resultUserPlatformApplicationRelation;
    }

}
