/* Drop Triggers */

DROP TRIGGER application_user_payment_id_delete;
DROP TRIGGER application_user_payment_id_insert;
DROP TRIGGER application_user_payment_item_id_delete;
DROP TRIGGER application_user_payment_item_id_insert;



/* Create Triggers */
delimiter //
CREATE TRIGGER application_user_payment_id_delete after delete
  on application_user_payment for each row
  begin
    update application_user_payment_id
      set deleted_date = now()
        where application_user_payment_id = old.application_user_payment_id;
  end;
CREATE TRIGGER application_user_payment_id_insert before insert
  on application_user_payment for each row
  begin
    insert into application_user_payment_id
      values (null, now(), now(), null);
    set new.application_user_payment_id = last_insert_id();
    set @last_insert_id = last_insert_id();
  end;
CREATE TRIGGER application_user_payment_item_id_delete after delete
  on application_user_payment_item for each row
  begin
    update application_user_payment_item_id
      set deleted_date = now()
        where application_user_payment_item_id = old.application_user_payment_item_id;
  end;
CREATE TRIGGER application_user_payment_item_id_insert before insert
  on application_user_payment_item for each row
  begin
    insert into application_user_payment_item_id
      values (null, now(), now(), null);
    set new.application_user_payment_item_id = last_insert_id();
    set @last_insert_id = last_insert_id();
  end;
//
delimiter ;
