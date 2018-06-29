<?php

/**
 * Logic_Payment_PaymentDeleteクラスのファイル
 *
 * Logic_Payment_PaymentDeleteクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Payment_PaymentDelete
 *
 * 仮想通貨情報削除
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Payment_PaymentDelete extends Logic_Payment_Abstract
{

    use \misp\logics\payment\traits\Logic_Payment_Trait_PlatformPaymentItemRepository;

    /**
     * 実処理
     * 
     * ペイメントステータスが完了以外の場合は、アプリケーションユーザペイメントキャンセルログテーブルに記録します。
     * 
     * <h3>exec実行前の前提</h3>
     * 
     * <ol>
     *  <li>モデル：アプリケーションユーザをセットしておくこと
     * </ol>
     * 
     * <h3>使用方法</h3>
     * 
     * <pre>
     * // 削除系ロジックの生成
     * $logic = new Logic_Payment_PaymentDelete();
     * 
     * // 処理に必要な情報の準備
     * //   モデル：アプリケーションユーザ
     * $logic->setApplicationUser($applicationUser);
     * </pre>
     * 
     * @param array $buildParams
     * @throws Common_Exception_Exception アプリケーションユーザペイメントキャンセルログに登録が失敗した場合にThrowされます
     * @throws Common_Exception_NotFound そもそもレコードが見つからなかった場合
     * @throws Exception 想定外の例外
     * @return boolean 
     */
    public function exec($buildParams = [])
    {
        $this->_buildParams = $buildParams;

        try {
            // トランザクション開始
            Common_Db::beginTransaction();

            // アプリケーションユーザ情報を取得
            // (ロジック呼び出し元であらかじめセットされている情報)
            $applicationUser    = $this->getApplicationUser();
            $applicationId      = $applicationUser->getApplicationId();
            $applicationUserId  = $applicationUser->getApplicationUserId();
            $applicationWorldId = $applicationUser->getApplicationWorldId();

            // パラメータチェック
            $this->_isValidateValue($applicationUserId);
            $this->_isValidateLength($applicationWorldId);

            // アプリケーションユーザペイメント取得
            //   レコードがない場合は例外が投げられる
            $m                      = new Application_Model_ApplicationUserPayment();
            $m->setApplicationUserId($applicationUserId);
            $m->setApplicationId($applicationId);
            $m->setApplicationWorldId($applicationWorldId);
            $applicationUserPayment = $this->_readApplicationUserPayment($m);

            if (!$applicationUserPayment) {
                return FALSE;
            }

            // ペイメントステータスが完了以外の場合は、アプリケーションユーザペイメントキャンセルログテーブルに記録
            if ($this->isNotPaymentStatusComplete($applicationUserPayment)) {

                $applicationUserPaymentCancelLog = new Application_Model_ApplicationUserPaymentCancelLog();
                $applicationUserPaymentCancelLog->setApplicationUserPaymentId($applicationUserPayment->getApplicationUserPaymentId());
                $applicationUserPaymentCancelLog->setApplicationUserId($applicationUserId);
                $applicationUserPaymentCancelLog->setApplicationId($applicationId);
                $applicationUserPaymentCancelLog->setApplicationWorldId($applicationWorldId);
                $applicationUserPaymentCancelLog->setPaymentPlatformId($applicationUserPayment->getPaymentPlatformId());
                $applicationUserPaymentCancelLog->setPaymentDeviceId($applicationUserPayment->getPaymentDeviceId());
                $applicationUserPaymentCancelLog->setPaymentRatingId($applicationUserPayment->getPaymentRatingId());
                $applicationUserPaymentCancelLog->setPaymentType($applicationUserPayment->getPaymentType());
                $applicationUserPaymentCancelLog->setPaymentStatus($applicationUserPayment->getPaymentStatus());
                $applicationUserPaymentCancelLog->setStartedDate($applicationUserPayment->getCreatedDate());
                $nowDatetime                     = $this->getNowDatetime();
                $applicationUserPaymentCancelLog->setCreatedDate($nowDatetime);
                $applicationUserPaymentCancelLog->setUpdatedDate($nowDatetime);
                // 登録
                $this->_createApplicationUserPaymentCancelLog($applicationUserPaymentCancelLog);
            }

            $pks = array();

            // アプリケーションユーザペイメントのペイメント種別をみて、creditの場合はプラットフォームペイメントも削除する
            if ($this->isPaymentTypeCredit($applicationUserPayment) && $this->isPaymentStatusComplete($applicationUserPayment)) {

                // プラットフォームペイメントを削除するためのネタ収集
                // アプリケーションユーザペイメント関連からネタ収集するため、
                // アプリケーションユーザペイメントからのカスケード削除の前に行う必要がある
                $pks = $this->_generatePlatformPaymentPksForDelete($applicationUserPayment);

                // アプリケーションユーザペイメントからのカスケード削除
                $this->_deleteApplicationUserPayment($applicationUserPayment);

                // プラットフォームペイメントからのカスケード削除
                //   1. プラットフォームペイメント
                //   2. プラットフォームペイメントアイテム
                foreach ($pks as $pk) {
                    $condPlatformPayment = new Application_Model_PlatformPayment($pk);
                    $this->_deletePlatformPayment($condPlatformPayment);
                }
            } else {

                // アプリケーションユーザペイメントからのカスケード削除
                $this->_deleteApplicationUserPayment($applicationUserPayment);
            }


            // 確定
            Common_Db::commit();

            // テキストログ出力
            Misp_TextLog::getInstance()->flush();

            return TRUE;
            //
        } catch (Common_Exception_Exception $exc) {
            //
            // アプリケーションユーザペイメントキャンセルログに登録が失敗した場合の例外ハンドリング
            //
            $this->_logError($exc, __CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
            //
        } catch (Common_Exception_NotFound $exc) {
            //
            // レコードが見つからなかった場合の例外ハンドリング
            //
            $this->_logInfo(__CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
            //
        } catch (Exception $exc) {
            //
            // その他
            //
            $this->_logError($exc, __CLASS__, __METHOD__, $exc->getLine(), $exc->getMessage());

            Common_Db::rollBack();

            throw $exc;
        }
    }

    /**
     * アプリケーションユーザペイメントキャンセルログ登録
     * 
     * アプリケーションユーザペイメントキャンセルログを登録します。
     * 
     * @param Application_Model_ApplicationUserPaymentCancelLog $m
     * @throws Common_Exception_Exception 登録に失敗した場合にThrowされます
     */
    private function _createApplicationUserPaymentCancelLog(Application_Model_ApplicationUserPaymentCancelLog $m)
    {
        // Mapper取得
        $mapper = $this->getApplicationUserPaymentCancelLogMapper($this->getDbSectionNameMain());

        // 登録
        if (!$mapper->insert($m)) {
            throw new Common_Exception_Exception(Logic_Payment_Const::LOG_MSG_INSERT_FAIL . $this->_generateModelLogFormat($m));
        }

        // テキストログ配列にプッシュ
        Misp_TextLog::getInstance()->push($m);
    }

    /**
     * アプリケーションユーザペイメント取得
     * 
     * アプリケーションユーザペイメントを取得します。
     * 引数のモデルに、条件となる値をセットしてください。
     * 
     * @param Application_Model_ApplicationUserPayment $m 検索条件をセットされたモデル
     * @return Application_Model_ApplicationUserPayment
     */
    private function _readApplicationUserPayment(Application_Model_ApplicationUserPayment $m)
    {
        // モデルにセットされている項目をWHERE条件に使用する
        $where  = $this->_generateWhere($m);
        // Mapper取得
        $mapper = $this->getApplicationUserPaymentMapper($this->getDbSectionNameMain());
        // 検索
        $r      = $mapper->fetchAll($where);

        if ($r) {
            // データが取得できた場合は確定で1レコードなので、決め打ちで返却
            return $r[0];
        } else {
            return NULL;
        }
    }

    /**
     * アプリケーションユーザペイメント削除
     * 
     * アプリケーションユーザペイメントを削除します。<br>
     * <br>
     * 引数のモデルに、条件となる値をセットしてください。
     * 
     * @param Application_Model_ApplicationUserPayment $m
     * @return void
     */
    private function _deleteApplicationUserPayment(Application_Model_ApplicationUserPayment $m)
    {
        // アプリケーションユーザペイメントからのカスケード削除
        //   1. アプリケーションユーザペイメント
        //   2. アプリケーションユーザペイメントアイテム
        //   3-1. アプリケーションユーザ通貨ペイメントアイテム
        //   3-2. アプリケーションユーザターゲット商品ペイメントアイテム
        //   3-3. アプリケーションユーザターゲット通貨ペイメントアイテム
        //   3-4. アプリケーションユーザプラットフォームペイメント関連
        $delModel = new Application_Model_ApplicationUserPayment();
        $delModel->setApplicationUserPaymentId($m->getApplicationUserPaymentId());

        // モデルにセットされている項目をWHERE条件に使用する
        $where  = $this->_generateWhere($delModel);
        // Mapper取得
        $mapper = $this->getApplicationUserPaymentMapper($this->getDbSectionNameMain());
        // 削除
        $r      = $mapper->delete($where);
        // その結果！
        if (!$r) {
            // 削除レコードがなかった場合は一応内部ログに出しておく
            $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_RECORD_NOT_FOUND . $this->_generateModelLogFormat($m));
        }
    }

    /**
     * プラットフォームペイメント削除
     * 
     * プラットフォームペイメントを削除します。<br>
     * <br>
     * 引数のモデルに、条件となる値をセットしてください。
     * 
     * @param Application_Model_PlatformPayment $m
     */
    private function _deletePlatformPayment(Application_Model_PlatformPayment $m)
    {
        // Mapper取得
        $mapper = $this->getPlatformPaymentMapper($this->getDbSectionNameMain());
        // 削除
        $r      = $mapper->delete($m->getPlatformPaymentId(), $m->getPaymentPlatformId());
        // その結果！
        if (!$r) {
            // 削除レコードがなかった場合は一応内部ログに出しておく
            $this->_logInfo(__CLASS__, __METHOD__, __LINE__, Logic_Payment_Const::LOG_MSG_RECORD_NOT_FOUND . $this->_generateModelLogFormat($m));
        }
    }

    /**
     * アプリケーションユーザペイメントアイテム取得
     * 
     * アプリケーションユーザペイメントアイテムを取得します。
     * 引数のモデルに、条件となる値をセットしてください。
     * 
     * @param Application_Model_ApplicationUserPaymentItem $m
     * @return Application_Model_ApplicationUserPaymentItem[]
     * @throws Common_Exception_NotFound レコードが見つからなかった場合にThrowされます
     */
    private function _readApplicationUserPaymentItem(Application_Model_ApplicationUserPaymentItem $m)
    {
        // モデルにセットされている項目をWHERE条件に使用する
        $where  = $this->_generateWhere($m);
        // Mapper取得
        $mapper = $this->getApplicationUserPaymentItemMapper($this->getDbSectionNameMain());
        // 検索
        $result = $mapper->fetchAll($where);
        // その結果！
        return $result;
    }

    /**
     * アプリケーションユーザプラットフォームペイメント関連取得
     * 
     * アプリケーションユーザプラットフォームペイメント関連を取得します。
     * 引数のモデルに、条件となる値をセットしてください。
     * 
     * @param Application_Model_ApplicationUserPlatformPaymentRelation $m
     * @return Application_Model_ApplicationUserPlatformPaymentRelation[]
     * @throws Common_Exception_NotFound レコードが見つからなかった場合にThrowされます
     */
    private function _readApplicationUserPlatformPaymentRelation(Application_Model_ApplicationUserPlatformPaymentRelation $m)
    {
        // モデルにセットされている項目をWHERE条件に使用する
        $where  = $this->_generateWhere($m);
        // Mapper取得
        $mapper = $this->getApplicationUserPlatformPaymentRelationMapper($this->getDbSectionNameMain());
        // 検索
        $result = $mapper->fetchAll($where);
        // その結果！
        return $result;
    }

    /**
     * プラットフォームペイメントアイテム取得
     * 
     * プラットフォームペイメントアイテムを取得します。
     * 
     * @param Application_Model_PlatformPaymentItem $m
     * @return Application_Model_PlatformPaymentItem[]
     * @throws Common_Exception_NotFound レコードが見つからなかった場合にThrowされます
     */
    private function _readPlatformPaymentItem(Application_Model_PlatformPaymentItem $m)
    {
        // モデルにセットされている項目をWHERE条件に使用する
        $where  = $this->_generateWhere($m);
        // Mapper取得
        $mapper = $this->getPlatformPaymentItemMapper($this->getDbSectionNameMain());
        // 検索
        $result = $mapper->fetchAll($where);
        // その結果！
        return $result;
    }

    /**
     * プラットフォームペイメント削除情報構築
     * 
     * プラットフォームペイメントを削除するためのネタ(PK)を構築します。
     * 
     * @param Application_Model_ApplicationUserPayment $m
     * @return array
     */
    private function _generatePlatformPaymentPksForDelete(Application_Model_ApplicationUserPayment $m)
    {
        // 最終的に返却するやつ
        $keys = array();

        // まずはアプリケーションユーザペイメントアイテムを取得
        $condApplicationUserPaymentItem = new Application_Model_ApplicationUserPaymentItem();
        $condApplicationUserPaymentItem->setApplicationUserPaymentId($m->getApplicationUserPaymentId());
        $applicationUserPaymentItems    = $this->_readApplicationUserPaymentItem($condApplicationUserPaymentItem);

        // そしてアプリケーションユーザプラットフォームペイメント関連を取得
        foreach ($applicationUserPaymentItems as $applicationUserPaymentItem) {

            $condApplicationUserPlatformPaymentRelation = new Application_Model_ApplicationUserPlatformPaymentRelation();
            $condApplicationUserPlatformPaymentRelation->setApplicationUserPaymentItemId($applicationUserPaymentItem->getApplicationUserPaymentItemId());
            $applicationUserPlatformPaymentRelations    = $this->_readApplicationUserPlatformPaymentRelation($condApplicationUserPlatformPaymentRelation);

            // 取得できたらネタに積む
            $keys[] = $this->_pickUpPlatformPaymentItemKeys($applicationUserPlatformPaymentRelations[0]);
        }

        // そしてそのキーでプラットフォームペイメントアイテムテーブルを検索
        // プラットフォームペイメントのPKを取り出して配列に積んでいく
        $returnKeys = array();  // 戻り値用
        foreach ($keys as $where) {

            $platformPaymentItems = $this->_readPlatformPaymentItem(new Application_Model_PlatformPaymentItem($where));

            foreach ($platformPaymentItems as $platformPaymentItem) {
                $returnKeys[] = array(
                    'platformPaymentId' => $platformPaymentItem->getPlatformPaymentId(),
                    'paymentPlatformId' => $platformPaymentItem->getPaymentPlatformId(),
                );
            }
        }

        return $returnKeys;
    }

    /**
     * プラットフォームペイメントアイテムキー抽出
     * 
     * アプリケーションユーザプラットフォームペイメント関連モデルから、プラットフォームペイメントアイテムの削除キーを取り出して連想配列にします。
     * 
     * @param Application_Model_ApplicationUserPlatformPaymentRelation $m
     * @return array
     */
    private function _pickUpPlatformPaymentItemKeys(Application_Model_ApplicationUserPlatformPaymentRelation $m)
    {
        $result                          = array();
        $result['platformPaymentItemId'] = $m->getPlatformPaymentItemId();
        return $result;
    }

    /**
     * WHERE構築
     * 
     * モデルからWHERE条件となる連想配列を構築します。<br>
     * 値が空でないモデル項目で連想配列を構築します。
     * 
     * @param object $m モデル
     * @return array WHERE条件となる連想配列
     */
    private function _generateWhere($m)
    {
        $where   = array();
        $arrData = $m->toArray();
        foreach ($arrData as $column => $value) {
            if (Misp_Util::isNotEmpty($value)) {
                $where[$column] = array($value);
            }
        }
        return $where;
    }

}
