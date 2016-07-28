<?php

/**
 * Logic_Applicationクラスのファイル
 * 
 * Logic_Applicationクラスを定義している
 *
 * @category   Zend
 * @package    Logic
 * @version    $Id$
 */

/**
 * Logic_Application
 * 
 * アプリケーションに対する基盤のクラス
 *
 * @category   Zend
 * @package    Logic
 */
class Logic_Application extends Logic_Abstract
{

    /**
     * アプリケーションIDとアプリケーション秘密鍵の検証を行う
     *
     * @param Application_Model_Application $application
     */
    public function isValidApplication(Application_Model_Application $application)
    {
        // application.iniからデータベース情報を取得する
        $config = Zend_Registry::get('misp');
        $db     = $config['db']['sub'];
        try {
            $applicationId     = $application->getApplicationId();
            $applicationSecret = $application->getApplicationSecret();

            Common_Log::getInternalLog()->info(sprintf('アプリケーション鍵検証 > appID:%s | appSecret:%s', $applicationId, $applicationSecret));

            // アプリケーションIDのチェックし、問題があれば例外を返す
            $this->_isValidateValue($applicationId, 11);
            // アプリケーション秘密鍵のチェックし、問題があれば例外を返す
            $this->_isValidateLength($applicationSecret);

            // WHERE条件作成
            $where = array(
                'applicationId' => array($applicationId),
            );
            // applicationSecretのWhere条件は、$applicationSecretが空ではない場合はその値、空の場合はNULL
            if (strlen($applicationSecret)) {
                $where['applicationSecret'] = array($application->getApplicationSecret());
            } else {
                $where['applicationSecret IS NULL'] = NULL;
            }

            // Mapper
            $mapper = $this->getApplicationMapper($db);

            if ($mapper->fetchAll($where)) {
                return TRUE;
            } else {
                throw new Common_Exception_OauthInvalidClient('アプリケーション検証に失敗しました');
            }
        } catch (Exception $exc) {
            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * アプリケーションの情報を取得する
     *
     * @param Application_Model_Application $application
     */
    public function readApplication(Application_Model_Application $application)
    {
        // application.iniからデータベース情報を取得する
        $config = Zend_Registry::get('misp');
        $db     = $config['db']['sub'];
        try {

            // パラメータ取得
            $applicationId = $application->getApplicationId();
            $developerId   = $application->getDeveloperId();

            // パラメータチェック
            $this->_isValidateValue($applicationId, 11);
            $this->_isValidateLength($developerId);

            // Select Mapper
            $applicationMapper = $this->getApplicationMapper($db);


            // WHERE条件作成            
            $where = array(
                'applicationId'       => array($applicationId),
                'deletedDate IS NULL' => NULL,
            );
            // デベロッパーIDがある場合、WHERE条件に追加
            if (strlen($developerId)) {
                $where['developerId'] = array($developerId);
            }

            // アプリケーション取得
            $resultApplications = $applicationMapper->fetchAll($where);
            if (empty($resultApplications)) {
                throw new Common_Exception_NotFound('取得対象が存在しません');
            }
            $resultApplication = $resultApplications[0];    // アプリケーションIDはユニークなため0番目を取る

            return $resultApplication;
        } catch (Exception $exc) {
            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

    /**
     * NGワードを含むか確認する
     * 
     * @param Application_Model_Application $application
     * @param string $textData チェック対象の文字列
     * @return Boolian true：NGワードを含む、 false：NGワードを含まない
     */
    public function hasNgWord($application, $textData)
    {
        try {
            // application.iniからデータベース情報を取得する
            $config = Zend_Registry::get('ngWord_configs');
            $db     = $config['database'];

            $validator = new Common_Validate_NotNgWord();

            $ngWordModel = new Application_Model_CommonNgWord();
            $ngWordModel->setApplicationId($application->getApplicationId());
            $ngWordModel->setNgWord($textData);
            
            $isValid = $validator->isValid($ngWordModel);

            return !$isValid;
        } catch (Exception $exc) {
            // 最後に上位に丸投げ(注意：このthrowは消さないこと)
            throw $exc;
        }
    }

}