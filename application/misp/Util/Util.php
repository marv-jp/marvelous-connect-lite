<?php

class Misp_Util
{

    /**
     * OpenSocial#User-Id 定義に準拠したユーザIDを生成します。
     * 
     * User-Id = Object-Id とします。(link 参照)
     * 
     * <ul>
     *   <li>ワールドIDがある場合(ワールド制)
     *     <ul>
     *       <li>Global-Id の定義に準拠
     *         <ul>
     *           <li>ワールドID ":" アプリケーションユーザID</li>
     *         </ul>
     *       </li>
     *     </ul>
     *   </li>
     *   <li>ワールドIDがない場合(ワールド制でない)
     *     <ul>
     *       <li>Local-Id の定義に準拠
     *         <ul>
     *           <li>アプリケーションユーザID</li>
     *         </ul>
     *       </li>
     *     </ul>
     *   </li>
     * </ul>
     * 
     * @param Application_Model_ApplicationUser|Application_Model_ApplicationUserPlatformRelation $model アプリケーションワールドIDおよびアプリケーションユーザIDをもっているオブジェクト
     * @return string OpenSocial#User-Id 定義に準拠したユーザID
     * @link http://opensocial.github.io/spec/2.5.1/Core-Data.xml#User-Id User-Id
     * @link http://opensocial.github.io/spec/2.5.1/Core-Data.xml#Object-Id Object-Id
     * @link http://opensocial.github.io/spec/2.5.1/Core-Data.xml#Global-Id Global-Id
     * @link http://opensocial.github.io/spec/2.5.1/Core-Data.xml#Local-Id Local-Id
     */
    public static function normalizeUserId($model)
    {
        // ワールドIDがあるかどうかを確認
        if (strlen($model->getApplicationWorldId())) {
            // Global-Id
            return $model->getApplicationWorldId() . ':' . $model->getApplicationUserId();
        } else {
            // Local-Id
            return $model->getApplicationUserId();
        }
    }

    public static function pickUpApplicationUserIdAndApplicationWorldId($id)
    {
        // idからアプリケーションIDとワールドIDを取得
        $wk                 = explode(':', $id);
        $applicationWorldId = isset($wk[1]) ? $wk[0] : '';
        $applicationUserId  = isset($wk[1]) ? $wk[1] : $wk[0];

        return array($applicationWorldId, $applicationUserId);
    }

    /**
     * 世界は同じか
     * 
     * @param type $aWorldId
     * @param type $bWorldId
     * @return boolean
     * @throws Common_Exception_IllegalParameter
     */
    public static function validateWorldIdPairs($aWorldId, $bWorldId)
    {
        if ($aWorldId == $bWorldId) {
            return true;
        }
        throw new Common_Exception_IllegalParameter('不正なパラメータです');
    }

    /**
     * BASE64 URLをデコードします。
     * 
     * @param string $input デコードしたい文字列
     * @return string デコードした文字列
     */
    public static function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * その値が「型」的に"空"かどうかチェックします。
     * 
     * @param mixed $v チェック対象の値
     * @return boolean TRUE:空である<br>
     *                  FALSE:空でない
     */
    public static function isEmpty($v)
    {
        $type    = gettype($v);
        $isEmpty = TRUE;
        switch ($type) {
            case 'string':
            case 'integer':
            case 'double':
            case 'float':
                $isEmpty = strlen($v) ? FALSE : TRUE;
                break;
            case 'array':
                $isEmpty = empty($v);
                break;
            case 'boolean':
                $isEmpty = !($v);
                break;
            case 'object':
                // 中身まではみない。NULLじゃなきゃFALSEということで。
                $isEmpty = FALSE;
                break;
            case 'NULL':
            // nop
            default:
                break;
        }

        return $isEmpty;
    }

    /**
     * その値が「型」的に"空でない"かどうかチェックします。
     * 
     * @param mixed $v チェック対象の値
     * @return boolean TRUE:空でない<br>
     *                  FALSE:空である
     */
    public static function isNotEmpty($v)
    {
        return !(self::isEmpty($v));
    }

    /**
     * リクエストヘッダを取得します。(Nginx対応)
     * 
     * @return array リクエストヘッダの配列
     */
    public static function getRequestHeaders()
    {
        $headers = array();

        if (!function_exists('apache_request_headers')) {
            // apache_request_headers関数が存在しない場合は、環境変数より取得する
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $name = substr($name, 5);
                    $name = str_replace('_', ' ', $name);
                    $name = strtolower($name);
                    $name = ucwords($name);
                    $name = str_replace(' ', '-', $name);

                    $headers[$name] = $value;
                }
            }
        } else {
            $headers = apache_request_headers();
        }

        return $headers;
    }

    /**
     * モデルの配列を受取り、指定したプロパティ名のメソッドでその値の配列を生成し返します
     * 
     * @param object[] $models
     * @param string $propertyName
     * @param string $method
     * @return array
     */
    public static function generateArrayByModel($models, $propertyName, $method = NULL)
    {
        // メソッド名生成(NULLの場合)
        if (!$method) {
            $method = 'get' + ucfirst($propertyName);
        }

        // 戻り値初期化
        $array = array();

        // モデルの配列から、プロパティ名のメソッドで配列を生成し直す
        foreach ($models as $model) {
            $array[$propertyName][] = $model->$method();
        }

        return $array;
    }

    /**
     * リダイレクトURLに含めたいパラメータをエンコードして返す
     * 
     * @param array $params リダイレクトURLに含めたいパラメータ
     * @return string 
     */
    public static function buildHttpParameter(array $params)
    {
        $parameter = '';
        if ($params) {
            $parameter = '?' . http_build_query($params);
        }
        return $parameter;
    }

}
