SET SESSION FOREIGN_KEY_CHECKS=0;

/* Drop Indexes */

DROP INDEX created_date ON application_user;
DROP INDEX updated_date ON application_user;
DROP INDEX application_user_id ON application_user_currency;
DROP INDEX expired_date ON application_user_currency;
DROP INDEX payment_platform_user_id ON application_user_payment;
DROP INDEX application_user_id ON application_user_payment_cancel_log;
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
DROP TABLE application_user_currency;
DROP TABLE application_user_currency_payment_item;
DROP TABLE application_user_target_currency_payment_item;
DROP TABLE application_user_target_product_payment_item;
DROP TABLE application_user_payment_item;
DROP TABLE application_user_payment;
DROP TABLE application_user_payment_cancel_log;
DROP TABLE application_user_payment_id;
DROP TABLE application_user_payment_item_id;
DROP TABLE application_user_platform_relation;
DROP TABLE developer;
DROP TABLE payment_device;
DROP TABLE payment_platform;
DROP TABLE payment_rating;
DROP TABLE platform_user;
DROP TABLE platform;
DROP TABLE platform_product_item;
DROP TABLE platform_product;
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


CREATE TABLE application_user_currency
(
	application_user_payment_item_id bigint unsigned NOT NULL COMMENT 'アプリケーションユーザペイメントアイテムID',
	application_user_payment_id bigint unsigned COMMENT 'アプリケーションユーザペイメントID',
	application_currency_id varchar(255) NOT NULL COMMENT 'アプリケーション通貨ID',
	payment_platform_id varchar(191) NOT NULL COMMENT 'ペイメントプラットフォームID',
	payment_device_id varchar(11) NOT NULL COMMENT 'ペイメントデバイスID',
	payment_rating_id varchar(11) NOT NULL COMMENT 'ペイメントレーティングID',
	application_user_id varchar(255) NOT NULL COMMENT 'アプリケーションユーザID',
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	application_world_id varchar(255) NOT NULL COMMENT 'アプリケーションワールドID',
	unit_price decimal(13,4) unsigned NOT NULL COMMENT '単価',
	currency_amount int unsigned NOT NULL COMMENT '通貨額',
	-- iOS: purchase_date
	-- Android: purchaseTime
	-- PSN: created_date (platform_payment_item)
	-- DMM PC: ORDERED_TIME
	-- DMM Android: orderedTime
	executed_date datetime COMMENT '実行日時 : iOS: purchase_date
Android: purchaseTime
PSN: created_date (platform_payment_item)
DMM PC: ORDERED_TIME
DMM Android: orderedTime',
	expired_date datetime COMMENT '期限日時',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_payment_item_id, application_currency_id, payment_platform_id, payment_device_id, payment_rating_id, application_user_id, application_id, application_world_id, unit_price)
) COMMENT = 'アプリケーションユーザ通貨';


CREATE TABLE application_user_currency_payment_item
(
	application_user_payment_item_id bigint unsigned NOT NULL COMMENT 'アプリケーションユーザペイメントアイテムID',
	application_currency_id varchar(255) NOT NULL COMMENT 'アプリケーション通貨ID',
	currency_amount int unsigned NOT NULL COMMENT '通貨額',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_payment_item_id, application_currency_id)
) COMMENT = 'アプリケーションユーザ通貨ペイメントアイテム';


CREATE TABLE application_user_payment
(
	application_user_payment_id bigint unsigned NOT NULL COMMENT 'アプリケーションユーザペイメントID',
	application_user_id varchar(255) NOT NULL COMMENT 'アプリケーションユーザID',
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	application_world_id varchar(255) NOT NULL COMMENT 'アプリケーションワールドID',
	payment_platform_user_id varchar(255) COMMENT 'ペイメントプラットフォームユーザID',
	payment_platform_id varchar(191) NOT NULL COMMENT 'ペイメントプラットフォームID',
	payment_device_id varchar(11) NOT NULL COMMENT 'ペイメントデバイスID',
	payment_rating_id varchar(11) NOT NULL COMMENT 'ペイメントレーティングID',
	-- 10: credit
	-- 11: bonus
	-- 20: exchange
	-- 30: payment
	-- 
	-- 
	payment_type tinyint NOT NULL COMMENT 'ペイメント種別 : 10: credit
11: bonus
20: exchange
30: payment

',
	-- 0: start
	-- 1: error
	-- 2: confirm
	-- 3: order
	-- 10: complete
	-- 
	payment_status tinyint NOT NULL COMMENT 'ペイメントステータス : 0: start
1: error
2: confirm
3: order
10: complete
',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_payment_id),
	CONSTRAINT application_user_id UNIQUE (application_user_id, application_id, application_world_id)
) COMMENT = 'アプリケーションユーザペイメント';


CREATE TABLE application_user_payment_cancel_log
(
	application_user_payment_id bigint unsigned NOT NULL COMMENT 'アプリケーションユーザペイメントID',
	application_user_id varchar(255) NOT NULL COMMENT 'アプリケーションユーザID',
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	application_world_id varchar(255) NOT NULL COMMENT 'アプリケーションワールドID',
	payment_platform_id varchar(191) NOT NULL COMMENT 'ペイメントプラットフォームID',
	payment_device_id varchar(11) NOT NULL COMMENT 'ペイメントデバイスID',
	payment_rating_id varchar(11) NOT NULL COMMENT 'ペイメントレーティングID',
	-- 10: credit
	-- 11: bonus
	-- 20: exchange
	-- 30: payment
	-- 
	-- 
	payment_type tinyint NOT NULL COMMENT 'ペイメント種別 : 10: credit
11: bonus
20: exchange
30: payment

',
	-- 0: start
	-- 1: error
	-- 2: confirm
	-- 3: order
	-- 10: complete
	-- 
	payment_status tinyint NOT NULL COMMENT 'ペイメントステータス : 0: start
1: error
2: confirm
3: order
10: complete
',
	-- アプリケーションユーザペイメントの作成日時
	started_date datetime NOT NULL COMMENT '開始日時 : アプリケーションユーザペイメントの作成日時',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_payment_id, application_id)
) COMMENT = 'アプリケーションユーザペイメントキャンセルログ';


CREATE TABLE application_user_payment_id
(
	application_user_payment_id bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'アプリケーションユーザペイメントID',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_payment_id)
) COMMENT = 'アプリケーションユーザペイメントID';


CREATE TABLE application_user_payment_item
(
	application_user_payment_item_id bigint unsigned NOT NULL COMMENT 'アプリケーションユーザペイメントアイテムID',
	application_user_payment_id bigint unsigned NOT NULL COMMENT 'アプリケーションユーザペイメントID',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_payment_item_id)
) COMMENT = 'アプリケーションユーザペイメントアイテム';


CREATE TABLE application_user_payment_item_id
(
	application_user_payment_item_id bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'アプリケーションユーザペイメントアイテムID',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_payment_item_id)
) COMMENT = 'アプリケーションユーザペイメントアイテムID';


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


CREATE TABLE application_user_target_currency_payment_item
(
	application_user_payment_item_id bigint unsigned NOT NULL COMMENT 'アプリケーションユーザペイメントアイテムID',
	application_currency_id varchar(255) NOT NULL COMMENT 'アプリケーション通貨ID',
	currency_amount int unsigned NOT NULL COMMENT '通貨額',
	price int unsigned NOT NULL COMMENT '価格',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_payment_item_id, application_currency_id, price)
) COMMENT = 'アプリケーションユーザターゲット通貨ペイメントアイテム';


CREATE TABLE application_user_target_product_payment_item
(
	application_user_payment_item_id bigint unsigned NOT NULL COMMENT 'アプリケーションユーザペイメントアイテムID',
	application_product_id varchar(255) NOT NULL COMMENT 'アプリケーション商品ID',
	product_quantity int unsigned NOT NULL COMMENT '商品数量',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (application_user_payment_item_id)
) COMMENT = 'アプリケーションユーザターゲット商品ペイメントアイテム';


CREATE TABLE developer
(
	developer_id varchar(255) NOT NULL COMMENT 'デベロッパID',
	developer_name varchar(255) NOT NULL COMMENT 'デベロッパ名',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (developer_id)
) COMMENT = 'デベロッパ';


CREATE TABLE payment_device
(
	payment_device_id varchar(11) NOT NULL COMMENT 'ペイメントデバイスID',
	device_name varchar(255) NOT NULL COMMENT 'デバイス名',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (payment_device_id)
) COMMENT = 'ペイメントデバイス';


CREATE TABLE payment_platform
(
	payment_platform_id varchar(191) NOT NULL COMMENT 'ペイメントプラットフォームID',
	platform_name varchar(255) NOT NULL COMMENT 'プラットフォーム名',
	platform_domain varchar(255) NOT NULL COMMENT 'プラットフォームドメイン',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (payment_platform_id)
) COMMENT = 'ペイメントプラットフォーム';


CREATE TABLE payment_rating
(
	payment_rating_id varchar(11) NOT NULL COMMENT 'ペイメントレーティングID',
	rating_name varchar(255) NOT NULL COMMENT 'レーティング名',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (payment_rating_id)
) COMMENT = 'ペイメントレーティング';


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


CREATE TABLE platform_product
(
	-- iOS: product_id
	-- Android: productId
	-- DMM: SKU_ID
	platform_product_id varchar(255) NOT NULL COMMENT 'プラットフォーム商品ID : iOS: product_id
Android: productId
DMM: SKU_ID',
	payment_platform_id varchar(191) NOT NULL COMMENT 'ペイメントプラットフォームID',
	payment_device_id varchar(11) NOT NULL COMMENT 'ペイメントデバイスID',
	payment_rating_id varchar(11) NOT NULL COMMENT 'ペイメントレーティングID',
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	platform_product_name varchar(255) COMMENT 'プラットフォーム商品名',
	platform_product_image_url varchar(255) COMMENT 'プラットフォーム商品画像URL',
	platform_product_description text COMMENT 'プラットフォーム商品説明',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (platform_product_id, payment_platform_id, payment_device_id, payment_rating_id, application_id)
) COMMENT = 'プラットフォーム商品';


CREATE TABLE platform_product_item
(
	platform_product_item_id bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'プラットフォーム商品アイテムID',
	-- iOS: product_id
	-- Android: productId
	-- DMM: SKU_ID
	platform_product_id varchar(255) NOT NULL COMMENT 'プラットフォーム商品ID : iOS: product_id
Android: productId
DMM: SKU_ID',
	payment_platform_id varchar(191) NOT NULL COMMENT 'ペイメントプラットフォームID',
	payment_device_id varchar(11) NOT NULL COMMENT 'ペイメントデバイスID',
	payment_rating_id varchar(11) NOT NULL COMMENT 'ペイメントレーティングID',
	application_id varchar(11) NOT NULL COMMENT 'アプリケーションID',
	application_currency_id varchar(255) NOT NULL COMMENT 'アプリケーション通貨ID',
	unit_price decimal(13,4) unsigned NOT NULL COMMENT '単価',
	currency_amount int unsigned NOT NULL COMMENT '通貨額',
	created_date datetime NOT NULL COMMENT '作成日時',
	updated_date datetime NOT NULL COMMENT '更新日時',
	deleted_date datetime COMMENT '削除日時',
	PRIMARY KEY (platform_product_item_id)
) COMMENT = 'プラットフォーム商品アイテム';


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


ALTER TABLE application_user_payment_item
	ADD FOREIGN KEY (application_user_payment_id)
	REFERENCES application_user_payment (application_user_payment_id)
	ON UPDATE RESTRICT
	ON DELETE CASCADE
;


ALTER TABLE application_user_payment
	ADD FOREIGN KEY (application_user_payment_id)
	REFERENCES application_user_payment_id (application_user_payment_id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE application_user_currency_payment_item
	ADD FOREIGN KEY (application_user_payment_item_id)
	REFERENCES application_user_payment_item (application_user_payment_item_id)
	ON UPDATE RESTRICT
	ON DELETE CASCADE
;


ALTER TABLE application_user_target_currency_payment_item
	ADD FOREIGN KEY (application_user_payment_item_id)
	REFERENCES application_user_payment_item (application_user_payment_item_id)
	ON UPDATE RESTRICT
	ON DELETE CASCADE
;


ALTER TABLE application_user_target_product_payment_item
	ADD FOREIGN KEY (application_user_payment_item_id)
	REFERENCES application_user_payment_item (application_user_payment_item_id)
	ON UPDATE RESTRICT
	ON DELETE CASCADE
;


ALTER TABLE application_user_payment_item
	ADD FOREIGN KEY (application_user_payment_item_id)
	REFERENCES application_user_payment_item_id (application_user_payment_item_id)
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


ALTER TABLE platform_product_item
	ADD FOREIGN KEY (platform_product_id, payment_platform_id, payment_device_id, payment_rating_id, application_id)
	REFERENCES platform_product (platform_product_id, payment_platform_id, payment_device_id, payment_rating_id, application_id)
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
CREATE INDEX application_user_id USING BTREE ON application_user_currency (application_user_id ASC, application_id ASC, application_world_id ASC, application_currency_id ASC, payment_platform_id ASC, expired_date ASC);
CREATE INDEX expired_date USING BTREE ON application_user_currency (expired_date ASC);
CREATE INDEX payment_platform_user_id USING BTREE ON application_user_payment (payment_platform_user_id ASC, payment_platform_id ASC);
CREATE INDEX application_user_id USING BTREE ON application_user_payment_cancel_log (application_user_id ASC, application_id ASC, application_world_id ASC);
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



