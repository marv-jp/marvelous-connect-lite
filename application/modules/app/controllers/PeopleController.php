<?php

class App_PeopleController extends App_BaseController
{
    /**
     * fields用の許可リスト
     * 
     * @var array $_fieldAllowList
     */
    private $_fieldAllowList = array(
        'displayName' => array('validationType' => 'equals'),
        'groups'      => array('validationType' => 'regex', 'regex' => '/^groups:(\d+?)$/'),
    );

    private function _validateFields($fields)
    {
        foreach ($fields as $field) {

            // そもそも許可リストのフィールド名に定義されているフィールド値ならOK(スキップ)
            if (isset($this->_fieldAllowList[$field]) && 'equals' == $this->_fieldAllowList[$field]) {
                // OKなヤツは後続のループ省力のために要素を取り除く
                unset($this->_fieldAllowList[$field]);
                continue;
            }

            // 許可リスト全走査
            foreach ($this->_fieldAllowList as $allowField => $fieldDefine) {

                switch ($fieldDefine['validationType']) {

                    // 正規表現で検査
                    case 'regex':
                        if (!preg_match($fieldDefine['regex'], $field)) {
                            throw new Common_Exception_IllegalParameter('フィールド名の指定が不正です');
                        }
                        break;

                    default:
                        break;
                }
            }
        }
    }

    public function getAction()
    {
        try {
            $request = $this->getRequest();

            // APIモードチェック
            $this->_checkAppApiMode();

            // ユーザ識別子,セレクタ,フィールズをリクエストパラメータから取得
            // フィールズはカンマ区切りでくる
            $id       = $request->getParam('id');
            $selector = $request->getParam('selector');
            $fields   = $request->getParam('fields');

            // idからアプリケーションワールドIDとアプリケーションユーザIDを取得
            list($applicationWorldId, $applicationUserId) = Misp_Util::pickUpApplicationUserIdAndApplicationWorldId($id);

            // 許可するフィールドのチェック
            if (strlen($fields)) {
                // カンマ区切りで分解し、配列に
                $fields = explode(',', $fields);

                // フィールド検証
                $this->_validateFields($fields);
            }

            // 返却用配列
            $apiResponseData = array();

            // ロジック宣言
            $logicApplicationUser = new Logic_ApplicationUser();
            $logicApplicationUser->setApplicationUserLogic($logicApplicationUser);

            // アプリケーションモデルの取得
            $applicationModel = $this->_generateApplicationModel();

            // ロジック呼び出し、selectorでロジック振り分け
            switch ($selector) {

                // @self
                case Misp_Base_RestController::SELECTOR_SELF:
                    // readApplicationUserメソッドを呼ぶ
                    // 引数は アプリケーションモデル、アプリケーションユーザモデル
                    // アプリケーションユーザモデルにセット
                    $applicationUserModel = new Application_Model_ApplicationUser();
                    $applicationUserModel->setApplicationId($applicationModel->getApplicationId());
                    $applicationUserModel->setApplicationUserId($applicationUserId);
                    $applicationUserModel->setApplicationWorldId($applicationWorldId);

                    // メソッド呼び出し
                    $return = $logicApplicationUser->readApplicationUser($applicationUserModel);

                    // メソッドから返ってきた、アプリケーションユーザーモデルから戻り値を取得し
                    // 連想配列の形にする
                    $statusArray = array_flip($this->_statusAllowList);

                    // $returnからAPIの戻り値を作成
                    // 返却必須だが、NULLがあり得る項目は、NULLの際に空文字に変換する
                    $displayName = strlen($return->getApplicationUserName()) ? $return->getApplicationUserName() : '';
                    $entry       = array(
                        'id'          => Misp_Util::normalizeUserId($return),
                        'displayName' => $displayName,
                        'password'    => $return->getPassword(),
                        'status'      => $statusArray[$return->getStatus()],
                        'published'   => date('c', strtotime($return->getCreatedDate())),
                        'updated'     => date('c', strtotime($return->getUpdatedDate())),
                    );

                    $apiResponseData = array(
                        'startIndex'   => 1,
                        'itemsPerPage' => 1,
                        'totalResults' => 1,
                        'entry'        =>
                        array(
                            $entry
                        )
                    );
                    break;

                default:
                    throw new Common_Exception_IllegalParameter('セレクタのパラメータが不正です');
                    break;
            }

            // 正常終了、連想配列はJSON形式に変更して返す
            $response = $this->getResponse();
            $response->setHttpResponseCode(200);
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody(Zend_Json::encode($apiResponseData));
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
        try {
            $request = $this->getRequest();

            // APIモードチェック
            $this->_checkAppApiMode();

            // ユーザ識別子,セレクタをリクエストパラメータから取得
            $id       = $request->getParam('id');
            $selector = $request->getParam('selector');

            // idからアプリケーションワールドIDとアプリケーションユーザIDを取得
            list($applicationWorldId, $applicationUserId) = Misp_Util::pickUpApplicationUserIdAndApplicationWorldId($id);

            // アプリケーションモデルの取得
            $applicationModel = $this->_generateApplicationModel();
            $applicationId    = $applicationModel->getApplicationId();

            // Body部取得
            $bodyParam  = Zend_Json::decode($request->getRawBody());
            // entryを抜き出す
            $entryParam = array();
            if (isset($bodyParam['entry'][0])) {
                $entryParam = $bodyParam['entry'][0];
            } else {
                throw new Common_Exception_IllegalParameter('パラメータが不正です');
            }

            // アプリケーションユーザモデルにセット
            $applicationUserModel = new Application_Model_ApplicationUser();
            $applicationUserModel->setApplicationId($applicationId);
            $applicationUserModel->setApplicationUserId($applicationUserId);
            $applicationUserModel->setApplicationWorldId($applicationWorldId);

            $applicationUserModel = $this->_generateApplicationUserData($applicationUserModel, $entryParam, array('displayName' => 'setApplicationUserName'));

            // 返却用配列
            $apiResponseData = array();

            // ロジック宣言
            $logicApplicationUser = new Logic_ApplicationUser();
            $logicApplicationUser->setApplicationUserLogic($logicApplicationUser);

            // ロジック呼び出し、selectorでロジック振り分け
            switch ($selector) {

                // @self
                case Misp_Base_RestController::SELECTOR_SELF:

                    // パラメータチェック
                    if (!$this->_validateParams($id, $entryParam, 'id')) {
                        throw new Common_Exception_IllegalParameter('パラメータが不正です');
                    }

                    // createApplicationUserメソッドを呼ぶ
                    // 引数は アプリケーションモデル、アプリケーションユーザモデル
                    $return = $logicApplicationUser->createApplicationUser($applicationUserModel);

                    // メソッドから返ってきた、アプリケーションユーザーモデルから戻り値を取得
                    // 連想配列の形にする
                    $statusArray = array_flip($this->_statusAllowList);

                    // 返却必須だが、NULLがあり得る項目は、NULLの際に空文字に変換する
                    $displayName     = strlen($return->getApplicationUserName()) ? $return->getApplicationUserName() : '';
                    $apiResponseData = array(
                        'startIndex'   => 1,
                        'itemsPerPage' => 1,
                        'totalResults' => 1,
                        'entry'        =>
                        array(
                            array(
                                'id'          => Misp_Util::normalizeUserId($return),
                                'displayName' => $displayName,
                                'password'    => $return->getPassword(),
                                'status'      => $statusArray[$return->getStatus()],
                                'published'   => date('c', strtotime($return->getCreatedDate())),
                                'updated'     => date('c', strtotime($return->getUpdatedDate())),
                            )
                        )
                    );
                    break;

                default:
                    throw new Common_Exception_IllegalParameter('セレクタのパラメータが不正です');
            }

            // 正常終了、連想配列はJSON形式に変更して返す
            $response = $this->getResponse();
            $response->setHttpResponseCode(201);
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody(empty($apiResponseData) ? NULL : Zend_Json::encode($apiResponseData));
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

    public function putAction()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(405);
        $response->setBody(Zend_Http_Response::responseCodeAsText(405));
    }

    private function _issetRequireKey($entry, $key)
    {
        if (!isset($entry[$key])) {
            throw new Common_Exception_IllegalParameter('パラメータが不正です');
        }
        return $entry[$key];
    }

}
