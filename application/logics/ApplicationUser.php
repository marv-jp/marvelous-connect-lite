<?php

/**
 * Logic_ApplicationUserクラスのファイル
 * 
 * Logic_ApplicationUserクラスを定義している
 *
 * @category   Zend
 * @package    Logic
 * @version    $Id$
 */

/**
 * Logic_ApplicationUser
 * 
 * アプリケーションユーザに対する基盤のクラス
 *
 * @category   Zend
 * @package    Logic
 */
class Logic_ApplicationUser extends Logic_Abstract
{
    /**
     * @var array
     * Logic_ApplicationUserのオブジェクト
     */
    private $_logicApplicationUser;

    /**
     * @var array
     * ApplicationUserテーブルのupdate時に使用するパラメータリスト
     */
    private $_updateList = array('status', 'accessToken', 'idToken', 'applicationUserName', 'password');

    /**
     * Logic_ApplicationUserのオブジェクトを返します
     *
     * @return Logic_ApplicationUser
     * Logic_ApplicationUserのオブジェクト
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
     * アプリケーションユーザ情報を更新する
     * 
     * @param Application_Model_ApplicationUser $applicationUser アプリケーションユーザモデル
     * @return boolean 処理結果
     */
    public function updateApplicationUser(Application_Model_ApplicationUser $applicationUser)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            // アプリケーションユーザ更新項目チェック
            // status
            if (!in_array($applicationUser->getStatus(), $this->_statusAllowList)) {
                throw new Common_Exception_IllegalParameter('statusに不正なパラメータが指定されました');
            }

            // アプリケーションワールドIDが空の場合、空文字をセット
            $applicationWorldId = $applicationUser->getApplicationWorldId();
            if (!strlen($applicationWorldId)) {
                $applicationWorldId = '';
                $applicationUser->setApplicationWorldId($applicationWorldId);
            }

            // アプリケーションユーザIDのチェックし、問題があれば例外を返す
            $this->_isValidateValue($applicationUser->getApplicationUserId());
            $this->_isValidateLength($applicationWorldId);

            // application.iniからデータベース情報を取得する
            $config            = Zend_Registry::get('misp');
            $applicationUserDb = $config['db']['main'];

            // Select Mapper
            $mapper     = $this->getApplicationUserMapper($applicationUserDb);
            $updateUser = $mapper->find($applicationUser->getApplicationUserId(), $applicationUser->getApplicationId(), $applicationWorldId);
            if (!$updateUser) {
                throw new Common_Exception_NotFound('更新対象が存在しません');
            }
            // アプリケーションユーザ情報に更新する項目が存在する場合、更新情報を上書き
            $setParams = $this->_getUpdateParams($this->_updateList, $applicationUser);

            // 更新情報がなければそのまま返却する
            if (empty($setParams)) {
                Common_Db::commit();
                return $updateUser;
            }

            $updateUser->setOptions($setParams);

            // 更新日時をモデルにセット
            if (Misp_Util::isEmpty($updateUser->getDeletedDate())) {
                $updateUser->setUpdatedDate(date("Y-m-d H:i:s"));
            }

            // update Mapper
            if (!$mapper->update($updateUser, $updateUser->getApplicationUserId(), $updateUser->getApplicationId(), $applicationWorldId)) {
                // 変更がなかった場合の例外
                throw new Common_Exception_NotModified('更新が行われませんでした');
            };
            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $updateUser;
        } catch (Exception $exc) {
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();
            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * アプリケーションユーザパスワードを発行し、アプリケーションユーザを登録し、パスワードを返す
     * 
     * @param Application_Model_ApplicationUser $applicationUser アプリケーションユーザモデル
     * @return Application_Model_ApplicationUser 登録されたアプリケーションユーザモデル
     */
    public function createApplicationUser(Application_Model_ApplicationUser $applicationUser)
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            // アプリケーションワールドIDの取得
            $applicationWorldId = $applicationUser->getApplicationWorldId();

            // アプリケーションワールドIDが空の場合、空文字をセット
            if (!strlen($applicationWorldId)) {
                $applicationWorldId = '';
                $applicationUser->setApplicationWorldId($applicationWorldId);
            }

            // アプリケーションユーザIDのチェックし、問題があれば例外を返す
            $this->_isValidateValue($applicationUser->getApplicationUserId());
            $this->_isValidateLength($applicationWorldId);

            // application.iniからデータベース情報を取得する
            $config            = Zend_Registry::get('misp');
            $applicationUserDb = $config['db']['main'];

            // Select Mapper
            $mapper = $this->getApplicationUserMapper($applicationUserDb);
            if ($mapper->find($applicationUser->getApplicationUserId(), $applicationUser->getApplicationId(), $applicationWorldId)) {
                throw new Common_Exception_AlreadyExists('IDは既に登録されています');
            }

            // パスワードが未セットの場合はパスワードを発行する
            //   ユーザ更新APIをコールされた場合は、パスワードがセットされている可能性がある
            if (!strlen($applicationUser->getPassword())) {
                $applicationUser->setPassword(Common_Oidc_Token::generatePassword());
            }

            // statusに1(有効)をセット
            $applicationUser->setStatus(Logic_Abstract::STATUS_ACTIVE);

            // 作成日時、更新日時をモデルにセット
            $setDate = date("Y-m-d H:i:s");
            $applicationUser->setCreatedDate($setDate);
            $applicationUser->setUpdatedDate($setDate);

            // insert Mapper
            if (!$mapper->insert($applicationUser)) {
                throw new Exception('登録に失敗しました');
            };

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $applicationUser;
        } catch (Exception $exc) {
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();
            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * アプリケーションユーザの認証を行い、IDトークン発行のための情報を整理し、
     * アクセストークン,IDトークンを発行する
     * アプリケーションユーザ情報のアクセストークン,IDトークンを更新する
     * 
     * 前提1：setApplicationUserLogicをセットしてから実行すること
     *
     * @param Application_Model_Application $application アプリケーションモデル
     * @param Application_Model_ApplicationUser $applicationUser アプリケーションユーザモデル
     * @param Common_Oidc_IdToken_Payload $oidcIdTokenPayload IDトークンペイロードモデル
     * @param array $options オプション項目(現状はmaxAgeとpaymentPlatformId)
     * @return Application_Model_ApplicationUser 更新後のアプリケーションユーザモデル
     */
    public function authenticateApplicationUser(Application_Model_Application $application, Application_Model_ApplicationUser $applicationUser, Common_Oidc_IdToken_Payload $oidcIdTokenPayload, $options = array())
    {
        try {
            // トランザクション系コードは消さないこと
            // (SELECTのみや、1テーブルのCUD操作の場合も同様)
            // (パフォーマンス面は別のフェーズで検討します)
            // 
            // トランザクション開始
            Common_Db::beginTransaction();

            // アプリケーションID検証
            if ($application->getApplicationId() != $applicationUser->getApplicationId()) {
                throw new Common_Exception_IllegalParameter('パラメータ不正');
            }
            if ($application->getApplicationId() != $oidcIdTokenPayload->getAud()) {
                throw new Common_Exception_IllegalParameter('パラメータ不正');
            }

            // アプリケーションユーザID検証
            if (Misp_Util::normalizeUserId($applicationUser) != $oidcIdTokenPayload->getSub()) {
                throw new Common_Exception_IllegalParameter('パラメータ不正');
            }

            // アプリケーションユーザIDのチェックし、問題があれば例外を返す
            $this->_isValidateValue($applicationUser->getApplicationUserId());
            // パスワードのチェックし、問題があれば例外を返す
            $this->_isValidateValue($applicationUser->getPassword());
            // subのチェックをし、問題があれば例外を返す
            $this->_isValidateValue($oidcIdTokenPayload->getSub());
            // client_idのチェックをし、問題があれば例外を返す
            $this->_isValidateValue($oidcIdTokenPayload->getAud());
            // nonceのチェックをし、問題があれば例外を返す
            $this->_isValidateLength($oidcIdTokenPayload->getNonce());
            // expのチェックをし、問題があれば例外を返す
            $this->_isValidateValue((string) $oidcIdTokenPayload->getExp());
            // iatのチェックをし、問題があれば例外を返す
            $this->_isValidateValue((string) $oidcIdTokenPayload->getIat());

            // application.iniからデータベース情報を取得する
            $config            = Zend_Registry::get('misp');
            $applicationUserDb = $config['db']['main'];
            $iss               = $config['idToken']['iss'];

            // WHERE条件作成
            $where = array(
                'applicationId'       => array($applicationUser->getApplicationId()),
                'applicationUserId'   => array($applicationUser->getApplicationUserId()),
                'applicationWorldId'  => array($applicationUser->getApplicationWorldId()),
                'password'            => array($applicationUser->getPassword()),
                'deletedDate IS NULL' => NULL,
            );

            // Select Mapper
            $mapper      = $this->getApplicationUserMapper($applicationUserDb);
            $updateUsers = $mapper->fetchAll($where);
            $updateUser  = $updateUsers[0];
            if (!$updateUser) {
                throw new Common_Exception_AuthenticationFailed('パスワード認証に失敗しました');
            }

            // アクセストークンの発行、モデルにセット
            Common_Oidc_Token::setRequiredKeys(array('iss', 'sub', 'aud', 'exp', 'iat'));
            $accessToken = Common_Oidc_Token::generateAccessToken();
            $updateUser->setAccessToken($accessToken);

            // at_hashの発行
            $header       = array('alg' => 'HS256');
            $akita        = new Akita_OpenIDConnect_Model_IDToken($header, array(), '');
            // RPのアクセストークンをセットし、at_hash値をAkitaに生成させる
            $akita->setAccessTokenHash($accessToken);
            $akitaPayload = $akita->getPayload();
            $atHash       = $akitaPayload['at_hash'];

            // IDトークンの発行、モデルにセット            
            $payload              = array(
                'iss'     => $iss,
                'sub'     => $oidcIdTokenPayload->getSub(),
                'aud'     => $oidcIdTokenPayload->getAud(),
                'exp'     => $oidcIdTokenPayload->getExp(),
                'iat'     => $oidcIdTokenPayload->getIat(),
                'nonce'   => $oidcIdTokenPayload->getNonce(),
                'at_hash' => $atHash,
            );
            $updateUser->setIdToken(Common_Oidc_Token::generateIdToken($payload, $accessToken, $application->getApplicationSecret()));
            // updateApplicationUser 実行
            $applicationUserLogic = $this->getApplicationUserLogic();
            $applicationUserLogic->updateApplicationUser($updateUser);

            // TODO 処理判定などで問題無ければcommitする
            // TODO 問題がある場合は、独自例外(今後追加予定)をThrowするか、自分でrollbackを実行すること
            Common_Db::commit();

            return $updateUser;
        } catch (Exception $exc) {
            // 例外発生時もrollbackを試みる
            Common_Db::rollBack();
            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * アプリケーションユーザ情報を取得する
     *
     * @param Application_Model_ApplicationUser $applicationUser アプリケーションユーザモデル
     * @return Application_Model_ApplicationUser 取得したアプリケーションユーザモデル
     */
    public function readApplicationUser(Application_Model_ApplicationUser $applicationUser)
    {
        // application.iniからデータベース情報を取得する
        $config            = Zend_Registry::get('misp');
        $applicationUserDb = $config['db']['main'];
        try {
            // パラメータ取得
            $applicationId      = $applicationUser->getApplicationId();
            $applicationUserId  = $applicationUser->getApplicationUserId();
            $applicationWorldId = $applicationUser->getApplicationWorldId();

            // パラメータチェック
            $this->_isValidateValue($applicationId, 11);
            $this->_isValidateValue($applicationUserId, 255);
            $this->_isValidateLength($applicationWorldId, 255);

            // Select Mapper
            $mapper                = $this->getApplicationUserMapper($applicationUserDb);
            $resultApplicationUser = $mapper->find($applicationUserId, $applicationId, $applicationWorldId);

            if (empty($resultApplicationUser)) {
                throw new Common_Exception_NotFound('取得対象が存在しません');
            }

            return $resultApplicationUser;
        } catch (Exception $exc) {
            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * ページング情報セッテイング
     * 
     * @param SplObjectStorage $inCollection
     * @param SplObjectStorage $outCollection
     * @param type $mapper
     * @param type $where
     * @return \SplObjectStorage
     */
    protected function _setUpPagingInfo(SplObjectStorage $inCollection, SplObjectStorage $outCollection, $mapper, $where)
    {
        // ページング情報セッティング
        $offset = $inCollection->getStartIndex();
        $count  = $inCollection->getCount();

        $itemPerPages = $outCollection->count();

        $totalResults = $itemPerPages + ($offset - 1);
        // 全体件数を取得する
        if ($count <= $itemPerPages) {
            $totalResults = $mapper->count($where);
        }

        $outCollection->setStartIndex($offset);
        $outCollection->setItemsPerPage($itemPerPages);
        $outCollection->setTotalResults($totalResults);
        $outCollection->rewind();

        return $outCollection;
    }

}
