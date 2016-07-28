SET SESSION FOREIGN_KEY_CHECKS=0;

/* Drop Indexes */

DROP INDEX created_date ON application_user;
DROP INDEX updated_date ON application_user;
DROP INDEX created_date ON application_user_platform_relation;
DROP INDEX updated_date ON application_user_platform_relation;
DROP INDEX created_date ON platform_user;
DROP INDEX updated_date ON platform_user;
DROP INDEX email_address ON platform_user;
DROP INDEX created_date ON user;
DROP INDEX updated_date ON user;
DROP INDEX access_token ON user_platform_application_relation;
DROP INDEX created_date ON user_platform_application_relation;
DROP INDEX updated_date ON user_platform_application_relation;
DROP INDEX refresh_token ON user_platform_application_relation;



/* Drop Tables */

DROP TABLE application_redirect_uri;
DROP TABLE user_platform_application_relation;
DROP TABLE application;
DROP TABLE application_user;
DROP TABLE application_user_platform_relation;
DROP TABLE developer;
DROP TABLE platform_user;
DROP TABLE platform;
DROP TABLE user;




/* Create Tables */

CREATE TABLE application
(
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	developer_id varchar(255) NOT NULL COMMENT 'デベロッパID',
	application_name varchar(255) NOT NULL COMMENT 'アプリケーション名',
	application_secret varchar(255) COMMENT 'アプリケーション秘密鍵',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_id)
) COMMENT = 'アプリケーション';


CREATE TABLE application_redirect_uri
(
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	redirect_uri varchar(255) NOT NULL COMMENT 'リダイレクトURI',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_id, redirect_uri)
) COMMENT = 'アプリケーションリダイレクトURI';


CREATE TABLE application_user
(
	application_user_id varchar(255) NOT NULL COMMENT 'アプリケーションユーザID',
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	application_world_id varchar(255) NOT NULL COMMENT 'アプリケーションワールドID',
	application_user_name varchar(255) COMMENT 'アプリケーションユーザ名',
	password varchar(255) COMMENT 'パスワード',
	access_token varchar(255) COMMENT 'アクセストークン',
	id_token text COMMENT 'IDトークン',
	-- 0: inactive
	-- 1: active
	-- 6: banned
	-- 
	status tinyint NOT NULL COMMENT 'ステータス : 0: inactive
1: active
6: banned
',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_id, application_id, application_world_id)
) COMMENT = 'アプリケーションユーザ';


CREATE TABLE application_user_platform_relation
(
	application_user_id varchar(255) NOT NULL COMMENT 'アプリケーションユーザID',
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	application_world_id varchar(255) NOT NULL COMMENT 'アプリケーションワールドID',
	platform_user_id varchar(255) NOT NULL COMMENT 'プラットフォームユーザID',
	platform_id varchar(191) NOT NULL COMMENT 'プラットフォームID',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_id, application_id, application_world_id, platform_user_id, platform_id)
) COMMENT = 'アプリケーションユーザプラットフォーム関連';


CREATE TABLE developer
(
	developer_id varchar(255) NOT NULL COMMENT 'デベロッパID',
	developer_name varchar(255) NOT NULL COMMENT 'デベロッパ名',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (developer_id)
) COMMENT = 'デベロッパ';


CREATE TABLE platform
(
	platform_id varchar(191) NOT NULL COMMENT 'プラットフォームID',
	platform_name varchar(255) NOT NULL COMMENT 'プラットフォーム名',
	platform_domain varchar(255) NOT NULL COMMENT 'プラットフォームドメイン',
	-- 0: メインプラットフォーム（MISPログイン可能）
	-- 1: サブプラットフォーム（MISPログイン不可）
	-- 2: プライムプラットフォーム（MISPログイン可能／二重登録不可）
	-- 
	platform_type tinyint NOT NULL COMMENT 'プラットフォーム種別 : 0: メインプラットフォーム（MISPログイン可能）
1: サブプラットフォーム（MISPログイン不可）
2: プライムプラットフォーム（MISPログイン可能／二重登録不可）
',
	sort_order tinyint NOT NULL COMMENT 'ソート順',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (platform_id)
) COMMENT = 'プラットフォーム';


CREATE TABLE platform_user
(
	platform_user_id varchar(255) NOT NULL COMMENT 'プラットフォームユーザID',
	platform_id varchar(191) NOT NULL COMMENT 'プラットフォームID',
	platform_user_name varchar(255) COMMENT 'プラットフォームユーザ名',
	platform_user_display_name varchar(255) COMMENT 'プラットフォームユーザ表示名',
	email_address varchar(255) COMMENT 'メールアドレス',
	access_token text COMMENT 'アクセストークン',
	id_token text COMMENT 'IDトークン',
	-- 0: inactive
	-- 1: active
	-- 6: banned
	-- 
	status tinyint NOT NULL COMMENT 'ステータス : 0: inactive
1: active
6: banned
',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (platform_user_id, platform_id)
) COMMENT = 'プラットフォームユーザ';


CREATE TABLE user
(
	user_id bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ユーザID',
	-- 0: inactive
	-- 1: active
	-- 6: banned
	-- 
	status tinyint NOT NULL COMMENT 'ステータス : 0: inactive
1: active
6: banned
',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (user_id)
) COMMENT = 'ユーザ';


CREATE TABLE user_platform_application_relation
(
	user_id bigint unsigned NOT NULL COMMENT 'ユーザID',
	platform_user_id varchar(255) NOT NULL COMMENT 'プラットフォームユーザID',
	platform_id varchar(191) NOT NULL COMMENT 'プラットフォームID',
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	authorization_code varchar(64) UNIQUE COMMENT '認可コード',
	access_token varchar(255) COMMENT 'アクセストークン',
	id_token text COMMENT 'IDトークン',
	refresh_token varchar(255) COMMENT 'リフレッシュトークン',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (user_id, platform_user_id, platform_id, application_id)
) COMMENT = 'ユーザプラットフォームアプリケーション関連';



/* Create Foreign Keys */

ALTER TABLE application_redirect_uri
	ADD FOREIGN KEY (application_id)
	REFERENCES application (application_id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE user_platform_application_relation
	ADD FOREIGN KEY (application_id)
	REFERENCES application (application_id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE application
	ADD FOREIGN KEY (developer_id)
	REFERENCES developer (developer_id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE platform_user
	ADD FOREIGN KEY (platform_id)
	REFERENCES platform (platform_id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE application_user_platform_relation
	ADD FOREIGN KEY (platform_user_id, platform_id)
	REFERENCES platform_user (platform_user_id, platform_id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE user_platform_application_relation
	ADD FOREIGN KEY (platform_user_id, platform_id)
	REFERENCES platform_user (platform_user_id, platform_id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE user_platform_application_relation
	ADD FOREIGN KEY (user_id)
	REFERENCES user (user_id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;



/* Create Indexes */

CREATE INDEX created_date USING BTREE ON application_user (created_date ASC);
CREATE INDEX updated_date USING BTREE ON application_user (updated_date ASC);
CREATE INDEX created_date USING BTREE ON application_user_platform_relation (created_date ASC);
CREATE INDEX updated_date USING BTREE ON application_user_platform_relation (updated_date ASC);
CREATE INDEX created_date USING BTREE ON platform_user (created_date ASC);
CREATE INDEX updated_date USING BTREE ON platform_user (updated_date ASC);
CREATE INDEX email_address USING BTREE ON platform_user (email_address ASC);
CREATE INDEX created_date USING BTREE ON user (created_date ASC);
CREATE INDEX updated_date USING BTREE ON user (updated_date ASC);
CREATE INDEX access_token USING BTREE ON user_platform_application_relation (access_token ASC);
CREATE INDEX created_date USING BTREE ON user_platform_application_relation (created_date ASC);
CREATE INDEX updated_date USING BTREE ON user_platform_application_relation (updated_date ASC);
CREATE INDEX refresh_token USING BTREE ON user_platform_application_relation (refresh_token ASC);



