<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 基盤が提供するロガーを統括するクラス
 * 
 * <h1>このクラスは、下記４つのロガーを返します。</h1>
 * <ul>
 * <li>アクセスロガー (Common_Log#getAccessOnlineLog)</li>
 * <li>例外ロガー (Common_Log#getExceptionLog)</li>
 * <li>内部ロガー (Common_Log#getInternalLog)</li>
 * <li>外部ロガー (Common_Log#getExternalLog)</li>
 * </ul>
 * このうち基盤利用者が明示的、任意に利用することができるのは「内部ロガー」のみです。<br/>
 * 「アクセスロガー」「例外ロガー」「外部ロガー」は、基盤利用者が明示的に使用することはありません。
 * <h1>各ロガーの概要です。</h1>
 * <dl>
 * <dt>アクセスロガー</dt>
 * <dd>リクエストおよびレスポンスの内容をアクセスログに出力します。<br/>
 * プラグイン「アクセスロギングフィルター ( Plugins_AccessLoggingFilter )」が自動的に呼び出します。<br/>
 * 「アクセスロギングフィルター」はデフォルト<span style="color:red;font-weight:bold">OFF</span>のホワイトリスト方式です。<br/>
 * もし「アクセスロギングフィルター」をONにしたい場合は、 APPLICATION_PATH / configs / plugins.yml を参照してください。<br/>
 * 	アクセスログの出力を抑制したい場合はAPPLICATION_PATH / configs / へ以下のように設定を記述して下さい。
 * <pre>
 * ---------------------------------------------------------------
 * APPLICATION_PATH / configs / application.iniの設定例
 *
 * ; io_type=0のログ出力を抑制
 * log.access.input.rows = false
 *
 * ; io_type=1のログの出力を抑制
 * log.access.output.rows = false
 *
 * ; http_bodyを出力する
 * log.access.columnMapping.http_body             = "http_body"
 * ; io_type=0のhttp_bodyは出力する
 * log.access.input.columnMapping.http_body       = true
 * ; io_type=1のhttp_bodyは出力しない
 * log.access.output.columnMapping.http_body      = false
 * 
 * ; http_headerは出力しない
 * log.access.columnMapping.http_header           = ""
 * ---------------------------------------------------------------
 * </pre>
 * <table>
 *     <tr>
 *         <td>評価順</td>
 *         <td>項目名</td>
 *         <td>説明</td>
 *         <td>項目がない場合のデフォルト値</td>				
 *     </tr>
 *     <tr>
 *         <td>1</td>
 *         <td>log.access.input.rows / log.access.output.rows</td>
 *         <td>falseを指定するとログを出力しなくなります</td>
 *         <td>true</td>				
 *     </tr>
 *     <tr>
 *         <td>2</td>
 *         <td>log.access.columnMapping.*</td>
 *         <td>空文字を指定した場合そのカラムは出力されなくなります</td>
 *         <td>-</td>				
 *     </tr>
 *     <tr>
 *         <td>3</td>
 *         <td>log.access.input.columnMapping.* / log.access.output.columnMapping.*</td>
 *         <td>falseを指定したカラムは出力されなくなります</td>
 *         <td>true</td>				
 *     </tr>
 * </table>
 * <b>注意点</b>
 * <ul>
 * <li>io_typeはDB定義上enumとなっているため、省略不可</li>
 * <li>request_access_log_idを出力しない場合、リクエストのログとの関連がなくなるため、事実上省略不可</li>
 * <li>creation_date・updated_date・deleted_dateはlog.access.input.columnMapping.* / log.access.output.columnMapping.*を設定しても省略できません</li>
 * </ul>
 * </dd>
 * <dt>例外ロガー</dt>
 * <dd>アプリケーション内で発生した例外を例外ログに出力します。<br/>
 * プラグイン「例外ハンドラ－ ( Plugins_ExceptionHandler )」が自動的に呼び出します。<br/>
 * 「例外ハンドラ－」はデフォルト<span style="color:red;font-weight:bold">OFF</span>のホワイトリスト方式です。<br/>
 * もし「例外ハンドラ－」をONにしたい場合は、 APPLICATION_PATH / configs / plugins.yml を参照してください。</dd>
 * <dt>内部ロガー</dt>
 * <dd>アプリケーション内で基盤利用者が任意に使用できるロガーです。ソース中でロガーを取得し、 Common_Log_Internal_Internal#info() もしくは Common_Log_Internal_Internal#debug() をコールすると、内部ログに出力します。</dd>
 * <dt>外部ロガー</dt>
 * <dd>アプリケーション内から他サイトのAPIにリクエストを送信する際、そのリクエストおよびレスポンスの内容を外部ログに出力します。<br/>
 * これは Zend_Http_Client の拡張クラス Common_Http_Client を使用した際に機能します。<br/>
 * 外部ロガーの出力を制御したい場合は、 APPLICATION_PATH / configs / plugins.yml を参照してください。</dd>
 * <dt>その他共通設定</dt>
 * <dd>
 * <pre>
 * ---------------------------------------------------------------
 * APPLICATION_PATH / configs / application.iniの設定例
 *
 * ; IDハッシュ化
 * log.common.id.type = "hash"
 *
 * ; ログDB接続のタイムアウトを1秒に指定
 * db.log.driver_options.2 = "1"
 * ; ログDB接続のタイムアウト発生時にログDBに再接続する回数
 * log.common.error.reconnect_times = "1"
 * ; ログDB接続のタイムアウト発生時に出力されるエラーファイルのパス
 * log.common.error.file = "/tmp/common_logger.err"
 * ; ログDB接続のタイムアウト発生時にログDB接続のリトライを行うまでの分数
 * log.common.error.retry_interval = "60"
 *
 * ; INSERT DELAYED による遅延書き込みの有効化
 * db.log.adapterNamespace = "Common_Db_Adapter"
 * log.access.writer = "Common_Log_Writer_Db"
 * log.internal.writer = "Common_Log_Writer_Db"
 * log.exception.writer = "Common_Log_Writer_Db"
 * log.external.writer = "Common_Log_Writer_Db"
 * ---------------------------------------------------------------
 * </pre>
 * </dd>
 * </dl>
 * <h1>ログテーブルのDDL</h1>
 * 基盤のリポジトリにコミットされています。
 * <code>http://project-bts/projects/standardization/repository/entry/RD/PHP/Framework/Zend/trunk/database/CommonLog.sql</code>
 * <h1>ログテーブルのER図</h1>
 * Eclipseプラグインの「ER Master」で作成しています。
 * <code>http://project-bts/projects/standardization/repository/entry/RD/PHP/Framework/Zend/trunk/database/CommonLog.erm</code>
 */
class Common_Log
{
    /** @var array $_instaces ロガーのインスタンスを複数格納する連想配列 */
    private static $_instances = array();

    /** @var array $_emergencyInstances 緊急ファイル出力用ロガーのインスタンスを複数格納する連想配列 */
    private static $_emergencyInstances = array();

    /** @var array $_commonConfig ロガーの設定を格納する連想配列 */
    private static $_commonConfig = null;

    /**
     * 内部ロガーを返す
     * 
     * @static
     * @param bool $isEmergency 緊急用ファイル出力フラグ
     * @return Common_Log_Internal_Interface
     */
    public static function getInternalLog($isEmergency = false)
    {
        return self::singletonFactory('Common_Log_Internal_Internal', self::_getLogTable('internal'), $isEmergency);
    }

    /**
     * 外部ロガーを返す
     * 
     * @static
     * @param bool $isEmergency 緊急用ファイル出力フラグ
     * @return Common_Log_External_Interface
     */
    public static function getExternalLog($isEmergency = false)
    {
        return self::singletonFactory('Common_Log_External_External', self::_getLogTable('external'), $isEmergency);
    }

    /**
     * 例外ロガーを返す
     * 
     * @static
     * @param bool $isEmergency 緊急用ファイル出力フラグ
     * @return Common_Log_Exception_Interface
     */
    public static function getExceptionLog($isEmergency = false)
    {
        return self::singletonFactory('Common_Log_Exception_Exception', self::_getLogTable('exception'), $isEmergency);
    }

    /**
     * アクセスロガーを返す
     *
     * @static
     * @param bool $isEmergency 緊急用ファイル出力フラグ
     * @return Common_Log_Access_Online アクセスロガー
     */
    public static function getAccessOnlineLog($isEmergency = false)
    {
        return self::singletonFactory('Common_Log_Access_Online', self::_getLogTable('access'), $isEmergency);
    }

    /**
     * ログ用DBのアダプタを返す
     *
     * @static
     * @return Zend_Db_Adapter_Abstract
     */
    public static function getLogDbAdapter()
    {
        return Common_Db::factoryByDbName('log');
    }

    /**
     * ロガーを生成してシングルトン管理する
     * 
     * @static
     * @param string $className ロガークラス名
     * @param string $table ログテーブル名
     * @param bool $isEmergency 緊急用ファイル出力フラグ
     * @return Common_Log_Abstract ロガーインスタンス
     */
    private static function singletonFactory($className, $table, $isEmergency = false)
    {
        if ($isEmergency)
        {
            if (isset(self::$_emergencyInstances[$className]) && self::$_emergencyInstances[$className] instanceof $className)
            {
                return self::$_emergencyInstances[$className];
            }
        }
        else
        {
            if (isset(self::$_instances[$className]) && self::$_instances[$className] instanceof $className)
            {
                return self::$_instances[$className];
            }
        }

        $log        = new $className();
        $config     = $log->getConfig();
        $zendLog    = new Zend_Log();
        $logConfigs = self::getConfig();

        if ($isEmergency)
        {
            $writerType = 'Zend_Log_Writer_Stream';
        }
        else
        {
            $writerType = $config['writer'];
        }

        $writer = null;
        switch ($writerType)
        {
            case 'Zend_Log_Writer_Stream':
                $writer = new Zend_Log_Writer_Stream($config['file']);
                $writer->setFormatter(self::_getFormatter($config, $table));
                break;

            case 'Zend_Log_Writer_Firebug':
                $writer = new Zend_Log_Writer_Firebug();
                $writer->setFormatter(new Common_Log_Formatter_Xml($table, self::_getXmlFormatArray($config['columnMapping'])));
                break;

            case 'Common_Log_Writer_Db':
                if ($config['database'])
                {
                    $writer = new Common_Log_Writer_Db(Common_Db::factoryByDbName($config['database']), $table, $config['columnMapping']);
                }
                else
                {
                    $writer = new Common_Log_Writer_Db(self::getLogDbAdapter(), $table, $config['columnMapping']);
                }
                break;

            default:
                if ($config['database'])
                {
                    $writer = new Zend_Log_Writer_Db(Common_Db::factoryByDbName($config['database']), $table, $config['columnMapping']);
                }
                else
                {
                    $writer = new Zend_Log_Writer_Db(self::getLogDbAdapter(), $table, $config['columnMapping']);
                }

                break;
        }

        $zendLog->addWriter($writer);
        $log->setLog($zendLog);
        $log->setMainWriter($writer);

        $idType = null;
        if (isset($logConfigs['common']['id']['type']))
        {
            $idType = $logConfigs['common']['id']['type'];
        }

        if (!($writer instanceof Zend_Log_Writer_Db) ||
                ($idType && strcmp($idType, 'hash') == 0))
        {
            $log->setIdType(Common_Log_Abstract::ID_TYPE_HASH);
        }
        else
        {
            $log->setIdType(Common_Log_Abstract::ID_TYPE_AUTOINCREMENT);
        }

        if ($isEmergency)
        {
            self::$_emergencyInstances[$className] = $log;
        }
        else
        {
            self::$_instances[$className] = $log;
        }

        return $log;
    }

    /**
     * ログの設定を返す
     * 
     * @return array ログの設定を格納した連想配列
     */
    public static function getConfig()
    {
        if (!self::$_commonConfig)
        {
            $config = new Zend_Config(Zend_Registry::get('log_configs'));
            self::$_commonConfig = $config->toArray();
        }

        return self::$_commonConfig;
    }

    /**
     * ロギング中のエラーがファイルに出力されているかを確認する
     * 
     * @return boolean 出力済み: true, 未出力: false
     */
    public static function isExistErrorFile()
    {
        $config = self::getConfig();

        if (isset($config['common']['error']['file']) && file_exists($config['common']['error']['file']))
        {
            return true;
        }

        return false;
    }

    /**
     * エラーファイルの最終更新日からの経過時間と設定値を比較する
     * 
     * @return boolean  経過時間が設定値以上: true, 経過時間が設定値未満,設定値がない: false
     */
    public static function compareErrorFilesTimestamp()
    {
        $config = self::getConfig();

        if (isset($config['common']['error']['retry_interval']))
        {
            $errorfiles_timestamp = filemtime($config['common']['error']['file']);
            $elapsed              = time() - $errorfiles_timestamp;

            if ($elapsed >= $config['common']['error']['retry_interval'] * 60)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * ロギング中のエラーをファイルに出力する
     * 
     * @param Exception $exc
     */
    public static function outputErrorFile($exc)
    {
        $config = self::getConfig();

        if (isset($config['common']['error']['file']))
        {
            $zendLog = new Zend_Log();
            $zendLog->addWriter(new Zend_Log_Writer_Stream($config['common']['error']['file']));

            $message = <<<ERROR

    Message :   {$exc->getMessage()}
    Code    :   {$exc->getCode()}
    File    :   {$exc->getFile()}
    Line    :   {$exc->getLine()}
ERROR;

            $zendLog->log($message, Zend_Log::ERR);
        }
    }

    /**
     * ログテーブル名を返す
     * 
     * @static
     * @param string $logType ログ種別
     * @return string ログテーブル名
     */
    private static function _getLogTable($logType)
    {
        $logConfigs = Zend_Registry::get('log_configs');
        $logConfig  = $logConfigs[$logType];

        return $logConfig['table'];
    }

    /**
     * Zend_Log_Formatter_Xmlに対応したフォーマット情報を返す
     * 
     * @param array $columnMapping カラム名を格納した配列
     * @return array Zend_Log_Formatter_Xmlに対応したフォーマット情報を格納した配列
     */
    private static function _getXmlFormatArray($columnMapping)
    {
        $format = array();
        foreach ($columnMapping as $column)
        {
            if ($column)
            {
                $format[$column] = $column;
            }
        }

        return $format;
    }

    /**
     * コンフィグ設定のformatterに対応したインスタンスを返す
     * 
     * @param array $config コンフィグ設定を格納した配列
     * @return Zend_Log_Formatter_Abstract $formatter コンフィグ設定のformatterに対応したインスタンス
     */
    private static function _getFormatter($config, $table)
    {
        $formatter = NULL;
        $columuns = self::_getXmlFormatArray($config['columnMapping']);

        if (isset($config['formatter']))
        {
            $format = $config['formatter'];
        }
        else
        {
            $format = '';
        }

        switch ($format)
        {
            case 'Common_Log_Formatter_Json':
                $formatter = new Common_Log_Formatter_Json($columuns);
                break;
            
            case 'Common_Log_Formatter_EncryptedJson':
                $formatter = new Common_Log_Formatter_EncryptedJson($columuns);
                break;

            default:
                $formatter = new Common_Log_Formatter_Xml($table, $columuns);
                break;
        }
        
        return $formatter;
    }

}
