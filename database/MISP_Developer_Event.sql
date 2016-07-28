DROP EVENT IF EXISTS session_gc_event;

-- 有効期限切れのセッションを削除する
CREATE EVENT session_gc_event
    ON SCHEDULE EVERY '1' DAY
    STARTS '2015-02-24 04:00:00'
DO
    DELETE FROM session WHERE (modified + lifetime) < UNIX_TIMESTAMP();
