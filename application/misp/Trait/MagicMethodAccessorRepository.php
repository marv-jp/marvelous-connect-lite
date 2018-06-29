<?php

namespace misp\traits;

/**
 * @var string アクセサタイプ(set)
 */
const TRAIT_ACCESSOR_TYPE_SET = 'set';

/**
 * @var string アクセサタイプ(get)
 */
const TRAIT_ACCESSOR_TYPE_GET = 'get';

/**
 * アクセサマジックメソッドのトレイトです。
 */
trait Misp_Trait_MagicMethodAccessorRepository
{
    /**
     * @var array 通常アクセサ
     */
    private $_normalAccessors = [];

    /**
     * @var array Mapperアクセサ
     */
    private $_mapperAccessors = [];

    /**
     * @var array Logicアクセサ
     */
    private $_logicAccessors = [];

    /**
     * アクセサマジックメソッド
     *
     * 下記用途のアクセサに対応しています。
     *
     * <ul>
     *   <li>ロジックアクセサ
     *     <ul>
     *       <li>ルール
     *         <ul>
     *           <li>"set"か"get"で始まり、"Logic"で終わるメソッド呼び出し<br>例：setHogeLogic()
     *         </ul>
     *     </ul>
     *   <li>マッパーアクセサ
     *     <ul>
     *       <li>ルール
     *         <ul>
     *           <li>"set"か"get"で始まり、"Mapper"で終わるメソッド呼び出し<br>例：setApplicationMapper()
     *         </ul>
     *     </ul>
     * </ul>
     *
     * @param string $name 呼び出されたメソッド名
     * @param array $arguments 呼び出されたメソッドに渡された引数
     * @return mixed getter呼び出しの場合は対応するオブジェクト
     * @throws \BadMethodCallException ルールに合致しなかった場合にThrowされます
     */
    public function __call($name, $arguments)
    {
        // アクセサ確認
        $matches = [];
        preg_match('/(set|get)(.+)/', $name, $matches);
        if (!$matches) {
            // そもそも set / get はじまりでなければ例外とする
            throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $name));
        }

        $accessor = $matches[1]; // "set" or "get"
        $mainName = $matches[2]; // "set" or "get" から後ろの部分(本体)
        // 本体確認
        preg_match('/(set|get)(.+)(Mapper|Logic)/', $name, $matches);

        // 本体部分の後ろが "Mapper" or "Logic" で終わっていない場合は通常のアクセサ(モデルとかコレクション)コールとする
        if (!$matches) {
            return $this->_normalAccessorCall($accessor, $mainName, $arguments);
        }

        $type     = $matches[3]; // "Mapper" or "Logic"
        $callName = $matches[2]; // マッパー/ロジック名

        switch ($type) {

            // Mapper
            case 'Mapper':

                switch ($accessor) {

                    // setter
                    case TRAIT_ACCESSOR_TYPE_SET:

                        $this->_mapperAccessors[$callName] = $arguments[0];
                        break;

                    // getter
                    case TRAIT_ACCESSOR_TYPE_GET:

                        if (!isset($this->_mapperAccessors[$callName][$arguments[0]])) {
                            $clazz                                            = 'Application_Model_' . $callName . $type;
                            $this->_mapperAccessors[$callName][$arguments[0]] = new $clazz($arguments[0]);
                        }
                        return $this->_mapperAccessors[$callName][$arguments[0]];

                    default:

                        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $name));
                }
                break;

            // Logic
            case 'Logic':

                switch ($accessor) {

                    // setter
                    case TRAIT_ACCESSOR_TYPE_SET:

                        $this->_logicAccessors[$callName] = $arguments[0];
                        break;

                    // getter
                    case TRAIT_ACCESSOR_TYPE_GET:

                        return $this->_logicAccessors[$callName];

                    default:

                        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $name));
                }
                break;

            default:

                throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $name));
        }
    }

    /**
     * "Mapper" or "Logic" で終わっていない場合は通常のアクセサ(モデルとかコレクション)コールとして処理する
     *
     * ※本来はスコープもチェックすべきですが、外部公開せずMISP内で完結する機能なのでオミットしています
     *
     * @param string $accessor "set" または "get"
     * @param string $mainName "set" または "get" から後ろの部分(本体)
     * @param array $arguments 本来コールしていたメソッドへの引数
     * @return mixed getter呼び出しの場合は対応するオブジェクト
     * @throws \BadMethodCallException 存在しなかった場合にThrowされます
     */
    protected function _normalAccessorCall($accessor, $mainName, $arguments)
    {
        switch ($accessor) {

            // setter
            case TRAIT_ACCESSOR_TYPE_SET:

                $this->_normalAccessors[$mainName] = $arguments[0];

            // getter
            case TRAIT_ACCESSOR_TYPE_GET:

                // プロパティ未登録で例外を返されるのは使い勝手が悪いので、nullを返す
                if (!isset($this->_normalAccessors[$mainName])) {
                    return null;
                }

                return $this->_normalAccessors[$mainName];

            default:
                break;
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s', get_class($this), $accessor . $mainName));
    }

    public function getDbSectionNameSub()
    {
        // application.iniからデータベース情報を取得する
        $config = \Zend_Registry::get('misp');
        return $config['db']['sub'];
    }

}
