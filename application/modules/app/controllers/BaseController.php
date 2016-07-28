<?php

abstract class App_BaseController extends Misp_Base_RestController
{

    public function init()
    {
        // 親クラスの初期化処理を先に実行
        parent::init();
    }

    /**
     * 各App系APIの先頭で行うAPIモードチェックを共通化(各APIの先頭でコールする)
     * 
     * ・例外を投げる必要があるため、 init() メソッドの処理に格上げは不可
     * 
     * @throws Common_Exception_NotAcceptable
     */
    protected function _checkAppApiMode()
    {
        // APIモード処理クラス取得
        $apiMode = Misp_ApiMode::getInstance();

        // app は Trusted オンリーなのでここでチェック
        if (!$apiMode->isTrusted()) {
            throw new Common_Exception_NotAcceptable('メソッドで許可されていないAPIモードが設定されています');
        }
    }

    /**
     * REST-URI-Fragment のパラメータと、 REST-Request-Payload の内容が一致しているか検証します。
     * 
     * @param string $parameterData REST-URI-Fragment のパラメータの値
     * @param array $bodyData REST-Request-Payload の連想配列
     * @param string $keyName REST-Request-Payload の検証対象の連想配列キー名
     * @return boolean TRUE: 一致
     *                  FALSE: 不一致
     */
    protected function _validateParams($parameterData, $bodyData, $keyName)
    {
        // Body部に指定したキーが存在するか確認
        //   キーが存在する場合のみ後続の検証処理をしたいので、
        //   キーが存在しなければ TRUE を返却し終了
        if (!array_key_exists($keyName, $bodyData)) {
            return TRUE;
        }

        // REST-URI-Fragment のパラメータと、 REST-Request-Payload の内容が一致しているか検証
        if ($parameterData == $bodyData[$keyName]) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 引数がグループIDの様式かどうかを検証します。
     * 
     * @param mixed $i 検証対象の値
     * @return boolean TRUE:グループIDの様式である<br>
     *                  FALSE:グループIDの様式でない
     */
    protected function _isGroupId($i)
    {
        return FALSE !== filter_var($i, FILTER_VALIDATE_INT);
    }

    /**
     * 引数がグループIDの様式「でない」ことを検証します。
     * 
     * @param mixed $i 検証対象の値
     * @return boolean TRUE:グループIDの様式でない<br>
     *                  FALSE:グループIDの様式である
     */
    protected function _isNotGroupId($i)
    {
        return !($this->_isGroupId($i));
    }

    /**
     * 引数のモデルがPlural-Field項目の親モデルかどうかを判定します。
     * 
     * @param type $model
     * @param array $nestedGroupBy
     * @return boolean TRUE:Plural-Field項目の親モデルである。<br>
     *                  FALSE:Plural-Field項目の親モデルででない。
     */
    protected function _isParentModelOfPluralField($model, $nestedGroupBy)
    {
        // groupBy状態管理変数
        // (このメソッド内でgroupBy状態を管理できればいいので、静的変数として宣言する)
        static $state = '';

        // Plural-Field項目をまとめる要素(=モデル内のデータ)を抽出し、
        // JSONに変換したのちに状態変数に格納する
        // この値が同値か相違かで「Plural-Field項目を作成中」なのか、「親モデルポインタが進んだ」かを判別する
        $v = array();
        foreach ($nestedGroupBy as $columnName) {
            $groupByMethodName = sprintf('get%s', ucfirst($columnName));
            $v[]               = $model->$groupByMethodName();
        }
        // serializeよりJSON化のほうがパフォーマンスが良い
        $tmp = Zend_Json::encode($v);

        // 前回シリアライズ時と相違した場合はPlural-Field項目の親モデルポインタが進んだということなので、
        // 状態管理変数を更新し、「Plural-Field項目の親モデル」であることを示すTRUE値を返す
        if ($state != $tmp) {
            $state = $tmp;
            return TRUE;
        }

        // 前回シリアライズ値と同値の場合はまだPlural-Field項目を構築中ということなので、
        // 「Plural-Field項目の親モデル」ではないことを示すFALSE値を返す
        return FALSE;
    }

    /**
     * APIレスポンス用の連想配列を構築します。
     * 
     * @param mixed $mixed モデルまたはコレクション
     * @param string $apiName
     * @param string $httpMethod
     * @return array
     */
    protected function _convertAssociativeArray($mixed, $apiName, $httpMethod)
    {
        $collection = $mixed;
        if (!($collection instanceof Misp_Collection_OpenSocial_Collection)) {
            // モデルとみなす
            $collection = new Misp_Collection_OpenSocial_Collection();
            $collection->attach($mixed);
        }

        return $this->_collectionReturnArrayForPluralField($collection, $apiName, $httpMethod);
    }

    /**
     * モデルを連想配列に変換します。(単なるtoArrayではない)
     * 
     * @param type $model
     * @param array $convertDefines
     * @return array
     */
    protected function _convertModelToAssociativeArray($model, $convertDefines)
    {
        $associativeArray = array();

        foreach ($convertDefines as $fieldName => $defines) {

            // モデルのデータとは無関係にとにかく固定値を入れたい場合に指定する項目
            if (isset($defines['value'])) {
                $associativeArray[$fieldName] = $defines['value'];
                // 固定値いれるなら以降の処理は無駄なので次いこう
                continue;
            }

            if (isset($defines['column'])) {

                // 定義リストのフィールド名に対応するアクセサがあれば、値を取得する
                $methodName = sprintf('get%s', ucfirst($defines['column']));

                if (method_exists($model, $methodName)) {

                    // アクセサから値を取得
                    $v = $model->$methodName();

                    // 項目の値が空ならスキップ？
                    if ($this->_isBlankSkip($v, $defines)) {
                        continue;
                    }

                    // OpenSocial#User-Id 定義に準拠したユーザIDを生成
                    if (isset($defines['normalizeUserId'])) {
                        $model->setApplicationUserId($v);
                        $v = Misp_Util::normalizeUserId($model);
                    }

                    // NULL(or 0byte)→空文字
                    if (isset($defines['nullToBlank'])) {
                        $v = $this->_nullToBlank($v);
                    }

                    // ISO8601
                    if (isset($defines['ISO8601'])) {
                        $v = $this->_toISO8601($v);
                    }

                    // 独自処理
                    // (そのデータに対して任意の処理をかけたい場合に指定する項目)
                    if (isset($defines['proc'])) {

                        // 指定された任意処理のメソッド名を取得
                        $procName = sprintf('%s', $defines['proc']['method']);

                        // 一応、メソッドタイプによって呼び分ける
                        // (ToDo:現状は引数１つのみの受け渡しなので、引数受け渡し処理は改善の余地がある)
                        switch ($defines['proc']['type']) {
                            // クラスメソッド
                            case 'static':
                                $v = self::$procName($v);
                                break;
                            // インスタンスメソッド
                            case 'instance':
                                $v = $this->$procName($v);
                                break;
                            default:
                                break;
                        }
                    }

                    $associativeArray[$fieldName] = $v;
                }
            }
        }

        return $associativeArray;
    }

    /**
     * Plural-Field項目があるレスポンス構造を構築します。
     * 
     * @param type $collection
     * @param type $apiName
     * @param type $httpMethod
     * @return type
     */
    protected function _collectionReturnArrayForPluralField($collection, $apiName, $httpMethod)
    {
        $collection->rewind();
        $defines = $this->_responsePayload[$apiName][$httpMethod];

        // まとめるカラム定義
        $nestedGroupBy = isset($defines['nestedGroupBy']['column']) ? $defines['nestedGroupBy']['column'] : '';

        $entry   = array();
        $entries = array();

        // OpenSearchフィールドをレスポンスに追加
        $this->_addOpenSearchStandardFields($entries, $collection->getStartIndex(), $collection->getItemsPerPage(), $collection->getTotalResults());

        // コレクションに格納されている検索結果のモデルをすべて処理する
        foreach ($collection as $model) {

            if (isset($defines['nestedColumns'])) {

                // Plural-Fieldの親モデルであれば、以後のPlural-Field項目の中身を構築するために連想配列化する
                if ($this->_isParentModelOfPluralField($model, $nestedGroupBy)) {

                    if (0 !== $collection->key()) {
                        $entries['entry'][] = $entry;
                    }

                    $entry = $this->_convertModelToAssociativeArray($model, $defines['fields']);
                }

                // Plural-Field項目の中身を構築
                $plurals = array();
                foreach ($defines['nestedColumns'] as $pluralColumn => $pluralField) {

                    // Plural-Field
                    $plural = array();

                    $pluralContents = $this->_convertModelToAssociativeArray($model, $pluralField['fields']);

                    if (Misp_Util::isNotEmpty($pluralContents)) {

                        // Plural-Filedの型をセット
                        $plural['type'] = $pluralField['type'];

                        // Plural-Fieldの'value' 項目の中身
                        $plural['value'] = $pluralContents;

                        $plurals[$pluralColumn][] = $plural;
                    }
                }

                if (Misp_Util::isNotEmpty($plurals)) {
                    $entry += $plurals;
                }
            } else {
                $entries['entry'][] = $this->_convertModelToAssociativeArray($model, $defines['fields']);
            }
        }

        if (isset($defines['nestedColumns'])) {
            $entries['entry'][] = $entry;
        }

        return $entries;
    }

    /**
     * APIレスポンス連想配列にOpenSearch準拠のフィールドを追加します。
     * 
     * @param array &$entries
     * @param string|int $startIndex
     * @param string|int $itemsPerPage
     * @param string|int $totalResults
     */
    protected function _addOpenSearchStandardFields(&$entries, $startIndex, $itemsPerPage, $totalResults)
    {
        $entries['startIndex']   = $this->conversionType($startIndex);
        $entries['itemsPerPage'] = $this->conversionType($itemsPerPage);
        $entries['totalResults'] = $this->conversionType($totalResults);
    }

    /**
     * 世界は同じか
     * 
     * @param type $aWorldId
     * @param type $bWorldId
     * @return boolean
     * @throws Common_Exception_IllegalParameter
     */
    protected function _validateWorldIdPairs($aWorldId, $bWorldId)
    {
        if ($aWorldId == $bWorldId) {
            return true;
        }
        throw new Common_Exception_IllegalParameter('不正なパラメータです');
    }

    /**
     * リクエストパラメータの項目が、許可されているものかどうか検証します。
     * 
     * 許可リストに未定義のリクエストパラメータが存在した場合、このメソッドは不正パラメータの例外をThrowします。
     * 
     * @param string $apiName
     * @param string $httpMethod
     * @throws Common_Exception_IllegalParameter
     */
    protected function _validateAllowedRequestParameters($apiName, $httpMethod)
    {
        // リクエストパラメータを取得する
        // (POSTで送信されたリクエストパラメータも取得可能)
        // (入れ子には対応していない模様)
        $requestParameters = $this->getRequest()->getQuery();

        // countのゼロ以下とか非数値は勘弁してください
        if (array_key_exists(Misp_Collection_OpenSocial_Collection::COUNT, $requestParameters)) {

            $options = array(
                'options' => array(
                    'min_range' => Misp_Collection_OpenSocial_Collection::COUNT_BORDER,
                )
            );

            if (!filter_var($requestParameters[Misp_Collection_OpenSocial_Collection::COUNT], FILTER_VALIDATE_INT, $options)) {
                throw new Common_Exception_IllegalParameter('不正なパラメータです');
            }
        }

        // 許可リスト取得
        $allowRequestParameterNames = isset($this->_allowRequestParameters[$apiName][$httpMethod]) ? $this->_allowRequestParameters[$apiName][$httpMethod] : NULL;

        // (未定義のHTTPメソッドでリクエストパラメータが送信された場合はNG)
        if (!empty($requestParameters) && is_null($allowRequestParameterNames)) {
            throw new Common_Exception_IllegalParameter('不正なパラメータです');
        }

        // リクエストパラメータ項目を全て検証する
        foreach ($requestParameters as $parameterName => $value) {

            // 許可リストに存在しない項目名は即NG
            if (!array_key_exists($parameterName, $allowRequestParameterNames)) {
                throw new Common_Exception_IllegalParameter('不正なパラメータです');
            }
        }

        // filterByとfilterValueは双方片手落ちはNG
        if (array_key_exists(Misp_Collection_OpenSocial_Collection::FILTER_BY, $requestParameters) ^ array_key_exists(Misp_Collection_OpenSocial_Collection::FILTER_VALUE, $requestParameters)) {
            throw new Common_Exception_IllegalParameter('不正なパラメータです');
        }

        // filterByとかきてる？
        if (array_key_exists(Misp_Collection_OpenSocial_Collection::FILTER_BY, $requestParameters)) {

            $parsedFilterBy    = array();
            $parsedFilterOp    = array();
            $parsedFilterValue = array();

            // filterByたちをリクエストパラメータから一式取得する
            $request     = $this->getRequest();
            $filterBy    = $request->getParam(Misp_Collection_OpenSocial_Collection::FILTER_BY);
            $filterOp    = $request->getParam(Misp_Collection_OpenSocial_Collection::FILTER_OP);
            $filterValue = $request->getParam(Misp_Collection_OpenSocial_Collection::FILTER_VALUE);

            // filterBy
            if ($filterBy) {
                $parsedFilterBy = array_map('trim', explode(',', $filterBy));
            }
            // filterOp
            if ($filterOp) {
                $parsedFilterOp = array_map('trim', explode(',', $filterOp));
            }
            // filterValue
            if ($filterValue) {
                $parsedFilterValue = array_map('trim', explode(',', $filterValue));
            }

            // こっちが許可するfieldたち
            $allowFields = array_keys($allowRequestParameterNames[Misp_Collection_OpenSocial_Collection::FILTER_BY]);

            // 定義
            $filterDefines = $allowRequestParameterNames[Misp_Collection_OpenSocial_Collection::FILTER_BY];

            // そのfilterByで指定したフィールド名、こっちが許可している名前ですか？
            foreach ($parsedFilterBy as $offset => $fieldName) {

                // そんなもんねーよ
                if (!in_array($fieldName, $allowFields)) {
                    throw new Common_Exception_IllegalParameter('不正なパラメータです');
                }

                // そのfilterByに対するfilterOpが指定必須なのに指定されてない場合は、400 Bad Requestにする
                if ($filterDefines[$fieldName][Misp_Collection_OpenSocial_Collection::FILTER_OP]['required']) {
                    if (!isset($parsedFilterOp[$offset]) || !strlen($parsedFilterOp[$offset])) {
                        throw new Common_Exception_IllegalParameter('不正なパラメータです');
                    }
                }
                // そのfilterByに対するfilterOpの値がこっちが許可しているやつじゃない場合は、400 Bad Requestにする
                if (isset($parsedFilterOp[$offset]) && strlen($parsedFilterOp[$offset])) {
                    if (!in_array($parsedFilterOp[$offset], $filterDefines[$fieldName][Misp_Collection_OpenSocial_Collection::FILTER_OP]['allow'])) {
                        throw new Common_Exception_IllegalParameter('不正なパラメータです');
                    }
                }
                // そのfilterByに対するfilterValueの値がこっちが許可しているやつじゃない場合は、400 Bad Requestにする
                if (isset($filterDefines[$fieldName][Misp_Collection_OpenSocial_Collection::FILTER_VALUE]['allow'])) {
                    if (!in_array($parsedFilterValue[$offset], $filterDefines[$fieldName][Misp_Collection_OpenSocial_Collection::FILTER_VALUE]['allow'])) {
                        throw new Common_Exception_IllegalParameter('不正なパラメータです');
                    }
                }
                // そのfilterByに対するfilterValueが必須指定なのに指定されてない場合は、400 Bad Requestにする
                if ($filterDefines[$fieldName][Misp_Collection_OpenSocial_Collection::FILTER_VALUE]['required']) {
                    if (!isset($parsedFilterValue[$offset]) || !strlen($parsedFilterValue[$offset])) {
                        throw new Common_Exception_IllegalParameter('不正なパラメータです');
                    }
                }
            }
        }
    }

    /**
     * リクエストボディ(Request-Payload)の項目が、許可されているものかどうか検証します。
     * 
     * リクエストボディ項目の入れ子にも対応しています。<br>
     * 許可リストは下記プロパティで定義します。<br>
     * <code>App_PeopleController#_allowPostPayload<code><br>
     * <br>
     * 
     * 許可されていないリクエストボディ項目が含まれていた場合、このメソッドは不正パラメータの例外をThrowします。
     * 
     * @param string $apiName API名
     * @param array $postedPayload リクエストボディの連想配列
     * @throws Common_Exception_IllegalParameter
     */
    protected function _validateAllowedRequestPayload($apiName, array $postedPayload, $httpMethod, $defines = array())
    {
        // 許可リスト受け
        $allows = $defines;
        if (empty($defines)) {
            // デフォルトでコールされた場合は許可リスト定義から取得する
            $allows = $this->_allowRequestPayload[$apiName][$httpMethod]['names'];
        }

        // リクエストボディ値を全部調べる
        foreach ($postedPayload as $k => $v) {

            // 許可リストに存在しないフィールド名は即NG
            if (!array_key_exists($k, $allows)) {
                throw new Common_Exception_IllegalParameter('不正なパラメータです');
            }

            // 許可リストが入れ子構造になっている場合は更に検証
            if (is_array($allows[$k]) && is_array($v)) {
                // 自分を再帰的にコール
                $this->_validateAllowedRequestPayload($apiName, $v, $httpMethod, $allows[$k]);
            }
        }
    }

    /**
     * JSONスキーマを用いてJSONを検証します
     * 
     * @param string|object $target 検証対象のJSON。JSON文字列、あるいはPHPのオブジェクト
     * @param string|object $jsonSchema 検証根拠となるJSONスキーマ。JSON文字列、あるいはPHPのオブジェクト
     * @throws Common_Exception_IllegalParameter 不正なパラメータが検知された場合にThrowされます
     */
    protected function _validateJsonBySchema($target, $jsonSchema)
    {
        $json   = $target;
        $schema = $jsonSchema;
        // JSONが文字列ならJSONオブジェクトにデコードする
        if (is_string($target)) {
            $json = json_decode($target);
        }
        // JSONスキーマが文字列ならJSONオブジェクトにデコードする
        if (is_string($jsonSchema)) {
            $schema = json_decode($jsonSchema);
        }
        require_once 'autoload.php';
        // 3rdライブラリで検証
        $v = new JsonSchema\Validator;
        $v->check($json, $schema);
        if ($v->getErrors()) {
            throw new Common_Exception_IllegalParameter(sprintf('不正なパラメータです:%s', print_r($v->getErrors(), 1)));
        }
    }

    /**
     * 項目値がブランクの場合にスキップするかどうか
     * 
     * @param string $v
     * @param array $defines
     * @return boolean
     */
    private function _isBlankSkip($v, $defines)
    {
        $isEmpty = Misp_Util::isEmpty($v);
        $isSkip  = TRUE;

        switch (isset($defines['blankSkip'])) {

            case TRUE:

                // 何が設定されているかな
                $define = strtolower($defines['blankSkip']);

                switch ($define) {

                    case 'true':

                        if (!$isEmpty) {
                            $isSkip = FALSE;
                        }
                        break;

                    case 'false':

                        $isSkip = FALSE;
                        break;

                    default:
                        break;
                }
                break;

            case FALSE:

                if (!$isEmpty) {
                    $isSkip = FALSE;
                }
                break;

            default:
                break;
        }

        return $isSkip;
    }

    /**
     * applicaton.iniに記述されたペイメントAPI設定群下にあるアプリケーションに対応した小設定群のキー名を構築します
     * 
     * @return string
     */
    protected function _buildApiConfigKey()
    {
        return 'app' . Misp_ApiMode::getInstance()->getApplicationId();
    }

    /**
     * 対象のアプリケーションユーザの情報を取得する
     * 
     * @param string $id APIをリクエストしたユーザの「ワールドID:アプリケーションユーザID」で構成されるid
     * @return Application_Model_ApplicationUser idから取得したアプリケーションユーザモデル
     */
    protected function _readApplicationUserModel($id)
    {
        // idをワールドID、アプリケーションユーザIDに分解
        list($applicationWorldId, $applicationUserId) = Misp_Util::pickUpApplicationUserIdAndApplicationWorldId($id);
        // アプリケーションID取得
        $applicationId = Misp_ApiMode::getInstance()->getApplicationId();

        // 引数用アプリケーションユーザモデル作成
        $applicationUserModel = new Application_Model_ApplicationUser();
        $applicationUserModel->setApplicationId($applicationId);
        $applicationUserModel->setApplicationUserId($applicationUserId);
        $applicationUserModel->setApplicationWorldId($applicationWorldId);

        // アプリケーションユーザロジック
        $logic = new Logic_ApplicationUser();
        return $logic->readApplicationUser($applicationUserModel);
    }

}
