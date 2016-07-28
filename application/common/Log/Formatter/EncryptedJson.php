<?php

/**
 * Common_Log_Formatter_EncryptedJson of Json
 */
class Common_Log_Formatter_EncryptedJson extends Common_Log_Formatter_Array
{

    public function __construct($culumnMapping)
    {
        parent::__construct($culumnMapping);
    }

    public function format($event)
    {
        $config = Common_Log::getConfig();
        $encryptionConfig = $config['common']['encryption'];
        
        // アルゴリズム名を取得
        $cipher = $encryptionConfig['cipher'];
        if (strpos($cipher, 'MCRYPT_') === 0 )
        {
            $cipher = constant($encryptionConfig['cipher']);
        }
        
        // キーを取得
        $key = $encryptionConfig['key'];
        
        // 暗号化モード名を取得
        $mode = $encryptionConfig['mode'];
        if (strpos($mode, 'MCRYPT_MODE_') === 0)
        {
            $mode = constant($encryptionConfig['mode']);
        }
        
        // IVを生成
        $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $iv = str_pad($event['creation_date'], $size);
        
        // 暗号化
        // バイナリデータはjsonに変換できないので16進に変換
        $returnEvent['data'] = bin2hex(mcrypt_encrypt($cipher, $key, Zend_Json::encode(parent::format($event)), $mode, $iv));
                
        // Fluentを利用する場合に必要な time カラムを出力
        // creation_date と同じ値を設定する
        $returnEvent['time'] = $event['creation_date'];

        return Zend_Json::encode($returnEvent) . PHP_EOL;
    }

}
