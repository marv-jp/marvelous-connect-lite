SET SESSION FOREIGN_KEY_CHECKS=0;

/* Drop Tables */
DROP TABLE `session`;

/* Create Tables */
CREATE TABLE `session` (
  `id` char(32) NOT NULL DEFAULT '' COMMENT 'セッションID',
  `modified` int COMMENT '最終更新日時',
  `lifetime` int COMMENT 'セッションの有効期間(秒)',
  `data` blob COMMENT 'セッションに保存するシリアライズデータ',
  PRIMARY KEY (`id`)
) COMMENT = 'MISP⇔OP間セッション管理テーブル';
