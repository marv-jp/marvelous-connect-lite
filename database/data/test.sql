-- アプリケーション
INSERT INTO application (application_id, developer_id, application_name, application_secret, created_date, updated_date, deleted_date) VALUES ('00000', 'marv.jp', 'test', 'JJBH8Y120NYi0iXbDyJSWsnRTbdcjkNtMCsytlpaD0DEosMMcg02YdooLC02qAji', now(), now(), null);


-- アプリケーションリダイレクトURI
INSERT INTO application_redirect_uri (application_id, redirect_uri, created_date, updated_date, deleted_date) VALUES ('00000', 'misp://mispLogin', now(), now(), null);


