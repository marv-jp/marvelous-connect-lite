<?php

/**
 * MISP拡張SessionSaveHandlerクラス
 */
class Misp_Session_SaveHandler_DbTable extends Common_Session_SaveHandler_DbTable
{

    /**
     * Destroy session
     * 
     * MISPはMySQLイベントでセッションクリーニングするので、<br>
     * DBセッションにおいてフルスキャンとなってしまうセッション削除メソッドを潰す
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        return TRUE;
    }

    /**
     * Garbage Collection
     * 
     * MISPはMySQLイベントでセッションクリーニングするので、<br>
     * DBセッションにおいてフルスキャンとなってしまうセッション削除メソッドを潰す
     *
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
    {
        return TRUE;
    }

}
