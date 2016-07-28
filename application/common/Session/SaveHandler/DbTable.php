<?php

/**
 * デフォルトの挙動が遅いのでセッションデータを圧縮/展開するように拡張したクラス
 * 
 * このクラスは、セッションの保存先をDBにする場合に利用するZend_Session_SaveHandler_DbTableを拡張しています。<br>
 * 
 * <h3>このクラスの目的</h3> 
 * 
 * 通常、Zend_Session_SaveHandler_DbTableはセッションデータをそのままテーブルに保存しますが、
 * 保存データが大きくなってくるとDBパフォーマンスの低下を招きます。
 * <br>
 * そのため、セッションデータの書き込み/読み出しの際にデータを圧縮/展開し、
 * パフォーマンス低下を軽減することを目的としたのがこのクラスです。<br>
 * <br>
 * 圧縮/展開にはPHPのgzcompress関数(圧縮)、gzuncompress関数(展開)を使用しています。<br>
 * 同様の機能関数にgzdeflate関数がありますが、MySQLの展開関数と互換性がないため、gzcompress関数を採用しています。<br>
 * <br>
 * 圧縮率は<b>50%</b>ほどです。
 * 
 * <h3>利用方法</h3>
 * 
 * Zend Framework においては基底で利用可能なリソースプラグインですので、
 * セッションデータを保存するテーブルを作成し、application.iniに利用設定を記述するだけで利用できます。<br>
 * コードの実装は不要です。
 * 
 * <h4>テーブル定義(DDL)</h4>
 * 
 * 下記DDLでテーブルを作成してください。<br>
 * PHPのgzcompress関数は結果をバイナリで返すので、dataカラムを<b>blob型</b>にしています。
 * 
 * <pre>
 * CREATE TABLE `session` (
 *   `id` char(32) COMMENT 'セッションID',
 *   `modified` int COMMENT '最終更新日時',
 *   `lifetime` int COMMENT 'セッションの有効期間(秒)',
 *   `data` blob COMMENT 'セッションに保存するシリアライズデータ',
 *   PRIMARY KEY (`id`)
 * ) COMMENT = 'セッション管理テーブル';
 * </pre>
 * 
 * <h4>application.ini設定</h4>
 * 
 * <ol>
 *   <li><code>resources.session.saveHandler.class</code> にこのクラス名を指定してください
 *   <li><code>resources.session.saveHandler.name</code> にセッションデータを保存するテーブル名を指定してください
 *     <ul>
 *       <li>カラム名の調整や複数カラムの主キーにも対応しています。詳細はリンク先をご参照ください
 *     </ul>
 *   </li>
 *   <li><code>resources.session.saveHandler.name</code> にセッションデータの有効期間(秒)を指定してください
 *     <ul>
 *       <li>読み出しの際、そのセッションデータが有効期限切れかどうかチェックし、もし有効期限切れならば該当レコードを削除します(Zend側の機能)
 *     </ul>
 *   </li>
 * </ol>
 * 
 * 設定例
 * 
 * <pre>
 * ; セッション設定
 * resources.session.saveHandler.class = "Common_Session_SaveHandler_DbTable"
 * resources.session.saveHandler.options.name = "session"
 * resources.session.saveHandler.options.primary = "id"
 * resources.session.saveHandler.options.modifiedColumn = "modified"
 * resources.session.saveHandler.options.dataColumn = "data"
 * resources.session.saveHandler.options.lifetimeColumn = "lifetime"
 * resources.session.saveHandler.options.lifetime = 600
 * </pre>
 * 
 * <h4>MySQLでの確認方法</h4>
 * 
 * 下記のDMLで、圧縮されたセッションデータの内容を確認することができます。
 * 
 * <pre>
 * SELECT UNCOMPRESS(data) FROM session;
 * </pre>
 * 
 * 詳細はリンク先をご参照ください。
 * 
 * @see <a href="http://framework.zend.com/manual/1.11/ja/zend.session.savehandler.dbtable.html">Zend_Session_SaveHandler_DbTable(日本語)</a>
 * @see <a href="http://framework.zend.com/manual/1.11/ja/zend.application.available-resources.html#zend.application.available-resources.session">リソースプラグイン(Zend_Application_Resource_Session(日本語))</a>
 * @see <a href="http://jp1.php.net/manual/ja/function.gzcompress.php">PHP - gzcompress</a>
 * @see <a href="http://jp1.php.net/manual/ja/function.gzuncompress.php">PHP - gzuncompress</a>
 * @see <a href="https://dev.mysql.com/doc/refman/5.1/ja/encryption-functions.html">MySQL - UNCOMPRESS</a>
 */
class Common_Session_SaveHandler_DbTable extends Zend_Session_SaveHandler_DbTable
{

    /**
     * セッションデータを圧縮して保存します。
     * 
     * @param type $id
     * @param type $data
     * @return boolean
     */
    public function write($id, $data)
    {
        $return = false;

        $data = array($this->_modifiedColumn => time(),
            $this->_dataColumn     => (string) pack('L', strlen($data)) . gzcompress($data));

        $rows = call_user_func_array(array(&$this, 'find'), $this->_getPrimary($id));

        if (count($rows)) {

            $data[$this->_lifetimeColumn] = $this->_getLifetime($rows->current());

            if ($this->update($data, $this->_getPrimary($id, self::PRIMARY_TYPE_WHERECLAUSE))) {
                $return = true;
            }
        } else {
            $data[$this->_lifetimeColumn] = $this->_lifetime;


            if ($this->insert(array_merge($this->_getPrimary($id, self::PRIMARY_TYPE_ASSOC), $data))) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * 圧縮されたセッションデータを展開して返します。
     * 
     * @param type $id
     * @return type
     */
    public function read($id)
    {
        $return = '';

        $rows = call_user_func_array(array(&$this, 'find'), $this->_getPrimary($id));

        if (count($rows)) {
            if ($this->_getExpirationTime($row = $rows->current()) > time()) {
                $return = $row->{$this->_dataColumn};
            } else {
                $this->destroy($id);
            }
        }

        // セッションデータが0バイトの場合,gzuncompressがfalseを返してしまうので、データが存在する場合のみ展開する
        if (strlen($return)) {
            return gzuncompress(substr($return, 4));
        } else {
            return $return;
        }
    }

}
