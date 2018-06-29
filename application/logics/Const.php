<?php

/**
 * Logic_Constクラスのファイル
 *
 * Logic_Constクラスを定義している
 *
 * @category Zend
 * @package  Logic_Payment
 */

/**
 * Logic_Const
 *
 * 定数クラス
 * 
 * 定義の名称について
 * メッセージ系
 * 　通常の場合：MSG_CONSTNAME = '定義メッセージ'
 * 　全角コロンを付ける場合(ログメッセージ用)：LOG_MSG_CONSTNAME = '定義メッセージ：'
 *
 * @category Zend
 * @package  Logic_Payment
 */
class Logic_Const
{
    /**
     * INSERT失敗時のログメッセージ定義
     * 
     * @string
     */
    const LOG_MSG_INSERT_FAIL = '登録に失敗しました：';

    /**
     * UPDATE対象が存在しなかった場合のログメッセージ定義
     * 
     * @string
     */
    const LOG_MSG_UPDATE_FAIL = '更新が行われませんでした：';

    /**
     * DELETE対象が存在しなかった場合のログメッセージ定義
     * 
     * @string
     */
    const LOG_MSG_DELETE_FAIL = '削除が行われませんでした：';

    /**
     * 不正なパラメータの場合のログメッセージ定義
     * 
     * @string
     */
    const LOG_MSG_ILLEGAL_PARAMETER = '不正なパラメータです：';

    /**
     * レコードが存在しなかった場合のログメッセージ定義
     * 
     * @string
     */
    const LOG_MSG_RECORD_NOT_FOUND = 'レコードが存在しません：';

    /**
     * 処理不能な両替レートの場合のログメッセージ定義
     * 
     * @string
     */
    const LOG_MSG_NO_EXCHANGE_RATE = '処理不能な両替レートでした：';

}
