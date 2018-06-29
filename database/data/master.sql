-- デベロッパ
INSERT INTO developer (developer_id, developer_name, created_date, updated_date, deleted_date) VALUES ('marv.jp', 'Marvelous', now(), now(), null);


-- プラットフォーム
INSERT INTO platform (platform_id, platform_name, platform_domain, platform_type, sort_order, created_date, updated_date, deleted_date) VALUES ('Facebook', 'Facebook', 'facebook.com', '0', '10', now(), now(), null);
INSERT INTO platform (platform_id, platform_name, platform_domain, platform_type, sort_order, created_date, updated_date, deleted_date) VALUES ('Google', 'Google', 'google.com', '0', '20', now(), now(), null);
INSERT INTO platform (platform_id, platform_name, platform_domain, platform_type, sort_order, created_date, updated_date, deleted_date) VALUES ('MarvelousMembers', 'MarvelousID', 'marv-m.jp', '0', '50', now(), now(), null);


-- ペイメントプラットフォーム
INSERT INTO payment_platform (payment_platform_id, platform_name, platform_domain, created_date, updated_date, deleted_date) VALUES ('moog', 'MooG Games', 'moog-games.jp', now(), now(), null);


-- ペイメントデバイス
INSERT INTO payment_device (payment_device_id, device_name, created_date, updated_date, deleted_date) VALUES ('', '*default*', now(), now(), null);


-- ペイメントレーティング
INSERT INTO payment_rating (payment_rating_id, rating_name, created_date, updated_date, deleted_date) VALUES ('', '*default*', now(), now(), null);


