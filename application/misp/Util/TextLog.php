<?php

/**
 * Misp_TextLogクラスのファイル
 *
 * Misp_TextLogtクラスを定義している
 *
 * @category Zend
 * @package  Misp
 */

/**
 * Misp_TextLog
 *
 * モデルをテキストログに出力する目的のクラスです。<br>
 * pushメソッドにモデルを貯めていき、最後にflushでまとめてテキストログ(infoログ)に出力する使い方を想定しています。<br>
 * <br>
 * 単発出力する場合はwriteメソッドを利用します。(この場合はpushで詰んだものはクリアされません)
 *
 * @category Zend
 * @package  Misp
 */
class Misp_TextLog
{
    /**
     * テキストログ出力対象モデル格納用
     * 
     * @var object[]
     */
    static private $_models = array();

    /**
     * シングルトンにするためのコンストラクタ潰し
     */
    private function __construct()
    {
        
    }

    /**
     * インスタンス取得
     * 
     * @staticvar Misp_TextLog $instance
     * @return \self
     */
    static public function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * テキストログを出力したいモデルをpushします。
     * 
     * モデルは自身のプロパティと値をkey-value形式の
     * 連想配列で返すtoArrayメソッドを実装している必要があります。
     * 
     * @param object $logModel
     */
    static public function push($logModel)
    {
        self::$_models[] = $logModel;
    }

    /**
     * pushしたログモデルをテキスト(infoログ)に出力します。
     * 
     * 出力後、push情報をクリアします。
     */
    static public function flush()
    {
        foreach (self::$_models as $logModel) {
            self::write($logModel);
        }
        self::clear();
    }

    /**
     * pushされているログモデルを初期化します。
     */
    static public function clear()
    {
        self::$_models = array();
    }

    /**
     * テキストログ出力
     * 
     * 下記フォーマットでモデルの内容をJSON化し、内部ログに出力します。<br>
     * <br>
     * <table>
     *   <tr>
     *     <th>キー</th>
     *     <th>値</th>
     *   </tr>
     *   <tr>
     *     <td>引数のオブジェクトのクラス名</td>
     *     <td>引数のオブジェクトをtoArray()で連想配列化したもの</td>
     *   </tr>
     * </table>
     * <pre>
     * // 例
     * array(
     *      '引数のオブジェクトのクラス名' => 引数のオブジェクトをtoArray()で連想配列化したもの
     * );
     * </pre>
     * 
     * @param object $logModel 内部ログ出力したいモデル
     */
    static public function write($logModel)
    {
        // JSONの第一キーとするログモデルのクラス名を取得
        $logModelName           = get_class($logModel);
        // ログモデル名をキーにして、値にログモデルを連想配列化したものをセットする
        $logData                = array();
        $logData[$logModelName] = $logModel->toArray();
        // JSON化
        $logJson                = Zend_Json::encode($logData);
        // 内部ログ出力
        Common_Log::getInternalLog()->info($logJson);
    }

}
