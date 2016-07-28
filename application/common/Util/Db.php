<?php

/**
 * Common_Util_Dbクラスのファイル
 *
 * @category   Zend
 * @package    Common_Util
 * @subpackage Util
 * @version    $Id:$
 */

/**
 * Common_Util_Db
 *
 * DB君
 *
 * @category   Zend
 * @package    Common_Util
 * @subpackage Util
 */
class Common_Util_Db
{

    /**
     * MYSQLのデータ型の名称ををPHPの変数の型の名称に置き換える
     * 
     * @param string $mySqlType MySQLデータベースの型名
     * @return string $phpType PHPの変数の型名
     */
    public static function mySqlTypeToPhp($mySqlType, $unsignedFlag = null)
    {
        $_mySqlType = strtoupper($mySqlType);
        $phpType    = '';

        switch ($_mySqlType) {
            case 'CHAR':
            case 'VARCHAR':
            case 'TEXT':
            case 'DATE':
            case 'DATETIME':
            case 'TIME':
            case 'TIMESTAMP':
                $phpType = 'string';
                break;

            case 'INT':
                if ($unsignedFlag) {
                    $phpType = 'float';
                } else {
                    $phpType = 'integer';
                }
                break;

            case 'TINYINT':
            case 'SMALLINT':
            case 'MEDIUMINT':
                $phpType = 'integer';
                break;

            case 'BIGINT':
            case 'DECIMAL':
            case 'FLOAT':
            case 'DOUBLE':
            case 'BIT':
                $phpType = 'float';
                break;

            case 'BOOLEAN':
                $phpType = 'boolean';
                break;

            default:
                $phpType = 'string';
                break;
        }

        return $phpType;
    }

    /**
     * MySQLのデータ型の名称ををActionScriptの変数の型の名称に置き換える
     * 
     * @param string $mySqlType MySQLデータベースの型名
     * @param string $unsignedFlag 符号なしフラグ
     * @return string $asType ActionScriptの変数の型名
     */
    public static function mySqlTypeToAs($mySqlType, $unsignedFlag)
    {
        $_mySqlType = strtoupper($mySqlType);
        $asType     = '';

        switch ($_mySqlType) {
            case 'CHAR':
            case 'VARCHAR':
            case 'TEXT':
            case 'DATE':
            case 'DATETIME':
            case 'TIME':
            case 'TIMESTAMP':
                $asType = 'String';
                break;

            case 'INT':
            case 'TINYINT':
            case 'SMALLINT':
            case 'MEDIUMINT':
                $asType = 'int';
                break;

            case 'BIGINT':
            case 'DECIMAL':
            case 'FLOAT':
            case 'DOUBLE':
            case 'BIT':
                $asType = 'Number';
                break;

            case 'BOOLEAN':
                $asType = 'Boolean';
                break;

            default:
                $asType = 'String';
                break;
        }

        if ($unsignedFlag) {
            switch ($asType) {
                case 'int':
                    $asType = 'uint';
                    break;
            }
        }

        return $asType;
    }

    /**
     * TSVファイルから読み込んだ情報に、YAMLを元にキー名を割り当てた連想配列を返す
     * 
     * @param array $yamlArray YAMLの配列
     * @param string $className クラス名
     * @param array $tsvArray TSVファイルから読み込んだ情報を格納した配列
     * @return array $tsvArrayの内容にキー名を割り当てた連想配列
     */
    public static function formatTsvArray($yamlArray, $className, $tsvArray)
    {
        $formatTsvArray = array();

        if (is_array($yamlArray['main_menu']['categorized'])) {
            foreach ($yamlArray['main_menu']['categorized'] as $num => $models) {
                if (is_array($models['models'])) {
                    foreach ($models['models'] as $num => $model) {
                        if (strcmp($model['name'], $className) == 0) {
                            foreach ($model['properties'] as $colNum => $property) {
                                $colValue                                                            = trim(isset($tsvArray[$colNum]) ? $tsvArray[$colNum] : '', "\"");
                                $colValue                                                            = mb_convert_encoding($colValue, 'utf8', 'sjis-win');
                                $formatTsvArray[Common_Util_String::camelToSnake($property['name'])] = $colValue;
                            }
                            return $formatTsvArray;
                        }
                    }
                }
            }
        }

        if (is_array($yamlArray['main_menu']['uncategorized'])) {
            foreach ($yamlArray['main_menu']['uncategorized'] as $num => $models) {
                if (is_array($models['models'])) {
                    foreach ($models['models'] as $num => $model) {
                        if (strcmp($model['name'], $className) == 0) {
                            foreach ($model['properties'] as $colNum => $property) {
                                $colValue                                                            = trim(isset($tsvArray[$colNum]) ? $tsvArray[$colNum] : '', "\"");
                                $colValue                                                            = mb_convert_encoding($colValue, 'utf8', 'sjis-win');
                                $formatTsvArray[Common_Util_String::camelToSnake($property['name'])] = $colValue;
                            }
                        }
                    }
                }
            }
        }

        return $formatTsvArray;
    }

    /**
     * 配列のキー名をキャメルケースから、プレースホルダー込みのスネークケースに変換する
     * 
     * @param mixed キー名がキャメルケースの連想配列またはスカラー値
     * @return array キー名が、プレースホルダー込みのスネークケースに変換された連想配列
     */
    public static function keyNameCamelToSnakeWithPlaceholder($data)
    {
        if (!is_array($data)) {
            // スカラー値は変換せずに返す
            return $data;
        }

        $parsedArray = array();
        foreach ($data as $camelName => $dataValue) {
            if (is_array($dataValue)) {
                // 配列だった場合、条件式にINを使用する
                $parsedArray[Common_Util_String::camelToSnake($camelName) . ' IN (?)'] = $dataValue;
            } elseif (preg_match('/^.*\s(<=)|(>=)$/', $camelName)) {
                $parsedArray[Common_Util_String::camelToSnake($camelName) . ' ?'] = $dataValue;
            } elseif (preg_match('/^.*\s<|>$/', $camelName)) {
                $parsedArray[Common_Util_String::camelToSnake($camelName) . ' ?'] = $dataValue;
            } elseif (preg_match('/^.*\s*(is\s+(not\s+)?null)\s*$/i', $camelName, $matches)) {
                // Key側の正規表現で IS NULL / IS NOT NULL を判定する
                // IS NULL / IS NOT NULL にマッチしたらValue部分は強制的にNULLにする
                //   camelToSnakeは大文字を"_"つきの小文字に変換するので、キー名(=カラム名)が崩れてしまう。
                //   このため、マッチした"IS NULL","IS NOT NULL"部分のみを小文字に変換し、それをcamelToSnakeする
                $parsedArray[Common_Util_String::camelToSnake(str_replace($matches[1], strtolower($matches[1]), $camelName))] = NULL;
            } else {
                // キー名をキャメルケースから、プレースホルダー込みのスネークケースに変換する
                $parsedArray[Common_Util_String::camelToSnake($camelName) . ' LIKE ?'] = $dataValue;
            }
        }

        return $parsedArray;
    }

    /**
     * 配列のキー名をキャメルケースからスネークケースに変換し、値とスペース区切りで連結した文字列の一次元配列に変換する
     * 
     * @param mixed キー名がキャメルケースの連想配列またはスカラー値
     * @return array キー名がスネークケースに変換され、値とスペース区切りで連結された文字列の一次元配列
     */
    public static function keyNameCamelToSnakeWithValue($data)
    {
        if (!is_array($data)) {
            // スカラー値は変換せずに返す
            return $data;
        }

        $parsedArray = array();
        foreach ($data as $camelName => $dataValue) {
            // キー名をキャメルケースから、スネークケースに変換する
            $parsedArray[] = Common_Util_String::camelToSnake($camelName) . ' ' . $dataValue;
        }
        return $parsedArray;
    }

    /**
     * 
     * @param string $modelName モデル名
     * @param array $yamlParsedArray YAMLからパースした結果を格納した配列
     * @return array YAMLから抜き出した情報
     */
    public static function getYamlInfo($modelName, $yamlParsedArray)
    {
        foreach ($yamlParsedArray['main_menu']['categorized'] as $category) {
            if (is_array($category['models'])) {
                foreach ($category['models'] as $data) {
                    if (strcmp($data['name'], $modelName) === 0) {
                        return $data;
                    }
                }
            }
        }

        if (is_array($yamlParsedArray['main_menu']['uncategorized'][0]['models'])) {
            foreach ($yamlParsedArray['main_menu']['uncategorized'][0]['models'] as $data) {
                if (strcmp($data['name'], $modelName) === 0) {
                    return $data;
                }
            }
        }

        return array();
    }

    /**
     * YAMLから指定したModelオブジェクトの各プロパティの設定を取り出す
     * 
     * @param array $yamlParsedArray YAMLをパースした結果の配列
     * @param string $modelObjectName Modelオブジェクト
     * @return array 論理名をキーにvisbleフラグとeditableフラグを格納した連想配列
     */
    public static function getPropertiesConfig($yamlParsedArray, $modelObjectName)
    {
        $titleHeaderArray = array();
        foreach ($yamlParsedArray['main_menu']['categorized'] as $category) {
            if (is_array($category['models'])) {
                foreach ($category['models'] as $modelData) {
                    if (strcmp($modelData['name'], $modelObjectName) == 0) {
                        foreach ($modelData['properties'] as $property) {
                            $titleHeaderArray[$property['name']] = array(
                                'visible'  => $property['visible'],
                                'editable' => $property['editable']
                            );
                        }
                        break;
                    }
                }
            }
        }

        if (is_array($yamlParsedArray['main_menu']['uncategorized'][0]['models'])) {
            foreach ($yamlParsedArray['main_menu']['uncategorized'][0]['models'] as $modelData) {
                if (strcmp($modelData['name'], $modelObjectName) == 0) {
                    foreach ($modelData['properties'] as $property) {
                        $titleHeaderArray[$property['name']] = array(
                            'visible'  => $property['visible'],
                            'editable' => $property['editable']
                        );
                    }
                    break;
                }
            }
        }
        return $titleHeaderArray;
    }

    /**
     * 論理名を取得する
     * 
     * @param stirng $class   モデルクラス名
     * @param string $columnName カラム名
     * @return string 論理名
     */
    public static function getLogicName($class, $columnName)
    {
        $refClass = new ReflectionClass($class);
        $props    = null;
        foreach ($refClass->getProperties() as $refProperty) {
            if (strcmp(ltrim($refProperty->getName(), '_'), $columnName) == 0) {
                $prop  = $refProperty->getDocComment();
                $prop  = str_replace('* @var', '', $prop);
                $prop  = str_replace('/**', '', $prop);
                $prop  = str_replace('*/', '', $prop);
                $props = explode(' ', trim($prop));
                return $props[1];
            }
        }

        return $columnName;
    }

    /**
     * 文字列を圧縮します。
     *
     * @param string $raw 圧縮したい文字列
     * @param boolean $toHex TRUE の場合、戻り値が HEX 文字列になります。デフォルトは TRUE です。
     * @param boolean $addHeader TRUE の場合、MySQLのUNCOMPRESS関数互換フォーマットで圧縮します。デフォルトは TRUE です。
     * @param int $level 圧縮レベル。0 で圧縮無し、9 で最大限の圧縮を指定できます。-1 を指定すると、zlib ライブラリのデフォルトを使います。デフォルトは 6 です。
     * @return mixed $toHex が TRUE：圧縮したデータを HEX 化した文字列<br>
     *               $toHex が FALSE: バイナリ文字列(PHPのgzcompress関数の戻り値)
     * @see <a href="http://jp1.php.net/manual/ja/function.gzcompress.php">PHP - gzcompress</a>
     */
    public static function compress($raw, $toHex = TRUE, $addHeader = TRUE, $level = 6)
    {
        // まず圧縮
        $compressed = gzcompress($raw, $level);

        // HEX化もヘッダ処理も不要ならすぐ返却
        if (!$toHex && !$addHeader) {
            return $compressed;
        }

        // ヘッダ処理(MySQLのUNCOMPRESS関数互換)
        $header = '';
        if ($addHeader) {
            $header     = (string) pack('L', strlen($raw));
            $compressed = $header . $compressed;
        }

        // HEX処理
        if ($toHex) {
            $ret = bin2hex($compressed);
        } else {
            $ret = $compressed;
        }

        return $ret;
    }

    /**
     * 圧縮データを展開します。
     *
     * @param string $compressed 展開したいデータ
     * @param boolean $byHex TRUE の場合、$commpressed が HEX であることをこのメソッドに伝えます。デフォルトは TRUE です。
     * @param boolean $removeHeader TRUE の場合、MySQLのUNCOMPRESS関数互換フォーマットで圧縮したデータであることをこのメソッドに伝えます。デフォルトは TRUE です。
     * @return string 展開したデータ
     */
    public static function uncompress($compressed, $byHex = TRUE, $removeHeader = TRUE)
    {
        // HEX化もヘッダ除去も不要なら展開してすぐ返却
        if (!$byHex && !$removeHeader) {
            return gzuncompress($compressed);
        }

        $bin = $compressed;
        if ($byHex) {
            // hex2bin関数はPHP5.4から…
            $bin = pack('H*', $compressed);
        }

        // ヘッダ処理
        // (MySQLのUNCOMPRESS関数互換フォーマットで圧縮したデータは、PHP関数で展開前にヘッダ部分を除去する必要がある)
        if ($removeHeader) {
            $uncompressed = gzuncompress(substr($bin, 4));
        } else {
            $uncompressed = gzuncompress($bin);
        }

        return $uncompressed;
    }

}
