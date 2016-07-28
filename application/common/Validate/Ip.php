<?php

/**
 * Common_Validate_Ipクラスのファイル
 * 
 * Common_Validate_Ipクラスを定義している
 *
 * @package  Common_Validate
 */

/**
 * Common_Validate_Ipクラスファイル
 *
 * @package   Common_Validate
 */
class Common_Validate_Ip extends Common_Validate_Abstract
{
    /**
     * PHPのFILTER_VALIDATE_IPフィルタ型が許容するフラグ
     * 
     * 複数のフラグを使用する場合は配列でセットしてください
     *
     * @var array $_options
     */
    protected $_options = array();

    /**
     * IPとして妥当なフォーマットかどうかをチェックします。
     * 
     * チェック実装はPHPのfilter_var関数でFILTER_VALIDATE_IPを使用しています。
     * 
     * @param string $ip チェックするIP
     * @return boolean TRUE:IPとして妥当<br>
     *                  FALSE:IPとして妥当ではない
     * @link <a href="http://www.php.net/manual/ja/filter.filters.validate.php">検証フィルタ</a>
     */
    public function isValid($ip)
    {
        $options = 0;
        foreach ($this->_options as $flag) {
            $options |= $flag;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, $options);
    }

    /**
     * バリデータの設定を返します。
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * バリデータの設定をセットします。
     * 
     * PHPのFILTER_VALIDATE_IPフィルタ型が許容するフラグを(複数使用する場合は)配列でセットしてください。
     *
     * @param array $options
     * @return Zend_Validate_Ip
     * @link <a href="http://www.php.net/manual/ja/filter.filters.validate.php">検証フィルタ</a>
     */
    public function setOptions($options)
    {
        $this->_options = $options;

        return $this;
    }

}
