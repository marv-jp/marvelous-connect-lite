SET SESSION FOREIGN_KEY_CHECKS=0;

/* Drop Tables */

DROP TABLE common_ng_word;




/* Create Tables */

CREATE TABLE common_ng_word
(
	application_id varchar(11) NOT NULL DEFAULT '' COMMENT 'アプリケーションID',
	ng_word varchar(255) NOT NULL COMMENT 'NGワード',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_id,ng_word)
) COMMENT = 'NGワード';