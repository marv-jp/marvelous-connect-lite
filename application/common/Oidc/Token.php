<?php

/**
 * Common_Oidc_Tokenクラスのファイル
 * 
 * Common_Oidc_Tokenクラスを定義している
 *
 * @category   Zend
 * @package    Common_Oidc
 * @version    $Id$
 */

/**
 * Common_Oidc_Token
 * 
 * OpenID Connect 関連のユーティリティです。
 *
 * @category   Zend
 * @package    Common_Oidc
 */
class Common_Oidc_Token
{
    /** @var string デフォルトハッシュアルゴリズム */
    const DEFAULT_HASH_ALGORISM = 'sha256';

    /** @var int IDトークンのClaim:subの最大長 */
    const SUB_MAX_LENGTH = 255;

    /** @var array IDトークンのペイロードで必須のキー */
    private static $_requiredIdTokenPayload = array('iss', 'sub', 'aud', 'exp', 'iat', 'nonce');

    /**
     * IDトークンのペイロード必須キーを設定します。
     * 
     * @param array IDトークンのペイロード必須キーの配列
     */
    public static function setRequiredKeys(array $keys)
    {
        self::$_requiredIdTokenPayload = $keys;
    }

    /**
     * IDトークンのペイロード必須キーの配列を返します。
     * 
     * @return array IDトークンのペイロード必須キーの配列
     */
    public static function getRequiredKeys()
    {
        return self::$_requiredIdTokenPayload;
    }

    /**
     * アクセストークン用の文字列を生成します。
     * 
     * @param string $hashAlgorism hash_hmac が許容するハッシュアルゴリズム。(default:'sha256' = 64byte)
     * @return string アクセストークン用の文字列
     */
    public static function generateAccessToken($hashAlgorism = self::DEFAULT_HASH_ALGORISM)
    {
        return self::_generateToken(microtime(true) . mt_rand(), $hashAlgorism);
    }

    /**
     * IDトークンを生成します。
     * 
     * 署名済みJWTフォーマットの文字列を返します。
     * 
     * 第一引数(IDトークンのペイロード連想配列)は下記情報が必須となります。必ずセットしてください。
     * 
     * <table border="1">
     *   <tr>
     *     <th>キー</th><th>セットする情報</th><th>実際の値のサンプル</th><th>備考</th>
     *   </tr>
     *   <tr>
     *     <td>iss</td><td>IDトークンの発行元(=MISPから通知された文字列です。通常はURLになります。(エンドポイントURLではありません))</td><td>http://dev-misp.maql-games.jp</td><td></td>
     *     <td>aud</td><td>アプリケーションID</td><td>misp0001(MISPが発行したアプリケーションIDです。)</td><td></td>
     *     <td>sub</td><td>ユーザー識別子</td><td>12200001(アプリケーション側でユニークなユーザIDです。)</td><td></td>
     *     <td>exp</td><td>IDトークンの有効期限(UNIXタイムスタンプ)</td><td>1389824338(有効期限なので未来日付のタイムスタンプを指定してください)</td><td></td>
     *     <td>iat</td><td>現在日時(UNIXタイムスタンプ)</td><td>1386224338</td><td></td>
     *     <td>nonce</td><td>nonce</td><td>(リプレイ攻撃防止用のランダムな文字列)</td><td></td>
     *   </tr>
     * </table>
     * 
     * @param array $payload IDトークンのペイロード(連想配列)
     * @param string $applicationSecret アプリケーション秘密鍵
     * @return string 署名済みJWTフォーマットのIDトークン
     */
    public static function generateIdToken(array $payload, $accessToken, $applicationSecret)
    {
        $header = array("alg" => "HS256");

        // 必須項目チェック
        self::_validateIdTokenPayload($payload);

        $key        = $applicationSecret;
        $idToken    = new Akita_OpenIDConnect_Model_IDToken($header, $payload, $key);
        $idToken->setAccessTokenHash($accessToken);
        $idTokenJwt = $idToken->getTokenString();

        return $idTokenJwt;
    }

    /**
     * リフレッシュトークン用の文字列を生成します。
     * 
     * @param string $hashAlgorism hash_hmac が許容するハッシュアルゴリズム。(default:'sha256' = 64byte)
     * @return string リフレッシュトークン用の文字列
     */
    public static function generateRefreshToken($hashAlgorism = self::DEFAULT_HASH_ALGORISM)
    {
        return self::_generateToken(microtime(true) . mt_rand(), $hashAlgorism);
    }

    /**
     * アプリケーション秘密鍵用の文字列を生成します。
     * 
     * @param string $hashAlgorism hash_hmac が許容するハッシュアルゴリズム。(default:'sha256' = 64byte)
     * @return string アプリケーション秘密鍵用の文字列
     */
    public static function generateApplicationSecret($hashAlgorism = self::DEFAULT_HASH_ALGORISM)
    {
        return self::_generateToken(microtime(true) . mt_rand(), $hashAlgorism);
    }

    /**
     * パスワード用の文字列を生成します。
     * 
     * @param string $hashAlgorism hash_hmac が許容するハッシュアルゴリズム。(default:'sha256' = 64byte)
     * @return string パスワード用の文字列
     */
    public static function generatePassword($hashAlgorism = self::DEFAULT_HASH_ALGORISM)
    {
        return self::_generateToken(microtime(true) . mt_rand(), $hashAlgorism);
    }

    /**
     * Basic Profile で使用する認可コード文字列を生成します。
     * 
     * @param string $hashAlgorism hash_hmac が許容するハッシュアルゴリズム。(default:'sha256' = 64byte)
     * @return string 認可コード文字列
     */
    public static function generateAuthorizationCode($hashAlgorism = self::DEFAULT_HASH_ALGORISM)
    {
        return hash_hmac($hashAlgorism, microtime(true) . mt_rand(), microtime(true) . mt_rand());
    }

    /**
     * IDトークンを復号します。
     * 
     * 署名されたJWTフォーマットのIDトークンを復号し、ペイロードを取り出し、それを返します。
     * 
     * @param string $idToken
     * @return array IDトークン(のペイロード部分)(連想配列)
     */
    public static function decodeIdToken($idToken)
    {
        if (!strlen($idToken)) {
            throw new Common_Exception_IllegalParameter(sprintf('"%s" is empty.', 'ID Token'));
        }

        $akita = Akita_OpenIDConnect_Model_IDToken::loadTokenString($idToken);

        return $akita->getPayload();
    }

    /**
     * IDトークンが正しいかどうか検証します。
     * 
     * 第二引数(IDトークンのペイロードモデル)は下記情報が必須となります。必ずセットしてください。
     * 
     * <table border="1">
     *   <tr>
     *     <th>プロパティ</th><th>セットする情報</th><th>実際の値のサンプル</th><th>備考</th>
     *   </tr>
     *   <tr>
     *     <td>iss</td><td>IDトークンの発行元(=MISPから通知された文字列です。通常はURLになります。(エンドポイントURLではありません))</td><td>http://dev-misp.maql-games.jp</td><td></td>
     *   </tr>
     *   <tr>
     *     <td>aud</td><td>アプリケーションID</td><td>(MISPが発行したアプリケーションIDです。)</td><td></td>
     *   </tr>
     *   <tr>
     *     <td>sub</td><td>ユーザー識別子</td><td>(アプリケーション側でユニークなユーザIDです。)</td><td></td>
     *   </tr>
     *   <tr>
     *     <td>nonce</td><td>nonce</td><td>(アプリケーション側がAPIリクエストする際に発行・保存していたユニークな値です。)</td><td></td>
     *   </tr>
     * </table>
     * 
     * <pre>
     * $accessToken       = 'アプリケーションユーザ認証APIによって返却されたアクセストークン';
     * $applicationSecret = 'アプリケーション秘密鍵';
     * 
     * $idToken               = 'このメソッドで検証したいIDトークン';
     * 
     * // ペイロードモデルにセットする必須情報を準備
     * $iss   = 'IDトークンの発行元(=MISPから通知された文字列です。通常はURLになります。)';
     * $aud   = 'MISPが発行したアプリケーションID';
     * $nonce = 'アプリケーション側がAPIリクエストする際に発行・保存していたユニークな値です。';
     * // ユーザ識別子
     * //   misp app APIの場合はアプリケーションユーザIDをセットしてください。
     * //   misp marvelous APIの場合はユーザIDをセットしてください。
     * // (いずれもgetSelf/putSelf のAPIで取得できる値です。)
     * $sub   = 'ユーザー識別子'; 
     * 
     * $payload = new Common_Oidc_IdToken_Payload();
     * $payload->setIss($iss);
     * $payload->setAud($aud);
     * $payload->setSub($sub);
     * $payload->setNonce($nonce);
     * 
     * // IDトークンの検証
     * if (!Common_Oidc_Token::isValidIdToken($idToken, $payload, $accessToken, $applicationSecret))
     * {
     *   // 不正IDトークンの場合の処理
     * }
     * else
     * {
     *   // 正常系の処理
     * }
     * </pre>
     * 
     * @param string $idToken IDトークン(JWT形式)
     * @param Common_Oidc_IdToken_Payload IDトークンのペイロードモデル
     * @param string $accessToken アクセストークン
     * @param string $applicationSecret アプリケーション秘密鍵
     * @return boolean TRUE:IDトークンが正しい
     *                  FALSE:IDトークンが正しくない
     */
    public static function isValidIdToken($idToken, Common_Oidc_IdToken_Payload $payload, $accessToken, $applicationSecret)
    {
        try {
            // 検証対象のIDトークン文字列からAkitaオブジェクトを生成し、
            // ペイロード部分を取り出す
            $targetIdToken = Akita_OpenIDConnect_Model_IDToken::loadTokenString($idToken);
            $targetPayload = $targetIdToken->getPayload();

            // RPが持っているペイロード情報からAkitaオブジェクトを生成
            $header = array('alg' => 'HS256');
            $akita  = new Akita_OpenIDConnect_Model_IDToken($header, $payload->toArray(), $applicationSecret);

            // RPのアクセストークンをセットし、at_hash値をAkitaに生成させる
            $akita->setAccessTokenHash($accessToken);
            $akitaPayload = $akita->getPayload();
            $atHash       = $akitaPayload['at_hash'];

            // シグネチャ検証
            $targetIdToken->setKey($applicationSecret);
            if (!$targetIdToken->validate()) {
                return FALSE;
            }

            // ペイロードの中身の検証
            // IDトークンのクレーム "iss" が RPが持っている情報と不一致ならNG
            if ($targetPayload['iss'] != $payload->getIss()) {
                return FALSE;
            }

            // IDトークンのクレーム "aud" が RPが持っている情報と不一致ならNG
            // aud = アプリケーションID
            if ($targetPayload['aud'] != $payload->getAud()) {
                return FALSE;
            }

            // IDトークンのクレーム "sub" が RPが持っている情報と不一致ならNG
            // sub = MISPユーザID
            if (Common_Util_String::isNotEmpty($payload->getSub()) && $targetPayload['sub'] != $payload->getSub()) {
                return FALSE;
            }

            // IDトークンのクレーム "nonce" が RPが持っている情報と不一致ならNG
            if ($targetPayload['nonce'] != $payload->getNonce()) {
                return FALSE;
            }

            // IDトークンのクレーム "at_hash" が RPが持っている情報と不一致ならNG
            // アクセストークンから生成した at_hash と IDトークンをデコードしたペイロードの中身の at_hash を比較
            if ($atHash != $payload->getAtHash()) {
                return FALSE;
            }

            // IDトークンのクレーム "exp" が現在時刻のタイムスタンプより過去であればNG
            if ($targetPayload['exp'] < time()) {
                return FALSE;
            }
        } catch (Exception $exc) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * ランダムなトークンを hash_hmac で生成します。
     * 
     * 用途は限定しません。
     * 
     * @param string $data トークンの元ネタ
     * @param string $hashAlgorism hash_hmac が許容するハッシュアルゴリズム。(default:'sha256' = 64byte)
     * @return string ランダムなトークン
     */
    private static function _generateToken($data, $hashAlgorism = self::DEFAULT_HASH_ALGORISM)
    {
        // PHPがサポートしているハッシュアルゴリズム以外は、デフォルトのハッシュアルゴリズムを指定する
        // (in_array は空文字、NULL-Safe)
        if (!in_array($hashAlgorism, hash_algos())) {
            $hashAlgorism = self::DEFAULT_HASH_ALGORISM;
        }

        return hash_hmac($hashAlgorism, $data, microtime(true) . mt_rand());
    }

    /**
     * IDトークンのペイロードの必須チェックをします。
     * 
     * 下記のいずれかのケースで例外を発生させます。
     * 
     * <ul>
     *   <li>ペイロードの必須キーが無い
     *   <li>ペイロードの必須キーの値が空
     *   <li>Claim:sub の文字長が255を超えている
     * </ul>
     * 
     * @param array $payload IDトークンのペイロード(連想配列)
     * @throws Common_Exception_IllegalParameter
     */
    private static function _validateIdTokenPayload(array $payload)
    {

        foreach (self::$_requiredIdTokenPayload as $requiredKey) {
            // 渡されたペイロードに必須キーが無ければパラメータ例外を発生させる
            if (!array_key_exists($requiredKey, $payload)) {
                throw new Common_Exception_IllegalParameter(sprintf('"%s" key payload is required.', $requiredKey));
            }

            // 必須キーの値が空(NULL or 0 byte)の場合はパラメータ例外を発生させる
            if (!strlen($payload[$requiredKey])) {
                throw new Common_Exception_IllegalParameter(sprintf('value of "%s" is empty.', $requiredKey));
            }

            // sub が OIDC仕様以上の文字長の場合はパラメータ例外を発生させる
            if ("sub" == $requiredKey && strlen($payload[$requiredKey]) > self::SUB_MAX_LENGTH) {
                throw new Common_Exception_IllegalParameter(sprintf('sub claims must not exceed %d characters.', self::SUB_MAX_LENGTH));
            }
        }
    }

    /**
     * IDトークンからexpires_inを計算し返却するメソッド
     * 
     * @param string $idToken IDトークン
     * @return expires_in
     * @throws Common_Exception_IllegalParameter
     */
    public static function calcExpiresIn($idToken)
    {
        // IDトークンをデコードして、exp、iatを取得し、
        // expires_inを計算して返す
        $payload = Common_Oidc_Token::decodeIdToken($idToken);

        // exp - iat (UNIXTIME)
        $expiresIn = $payload['exp'] - $payload['iat'];

        return $expiresIn;
    }

}
