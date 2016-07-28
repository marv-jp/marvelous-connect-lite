# language: ja
フィーチャ: readUserPlatformApplicationRelationWithValidateメソッドのテスト

  シナリオ: 正常に"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20" がセットされている
  かつ プロパティ "id_token" には "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vbWlzcC5zdGFuZGFyZGl6YXRpb24ubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDgzMTEwMDAwIiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.R2VLAl4hsUKEwXxoDmXxmLaTct5vEbXyU15MNkSPAS8" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  もし "ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "111" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに "1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20" が入っていること
  かつ "id_token" プロパティに "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vbWlzcC5zdGFuZGFyZGl6YXRpb24ubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDgzMTEwMDAwIiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.R2VLAl4hsUKEwXxoDmXxmLaTct5vEbXyU15MNkSPAS8" が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: "access_token"が空で、"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" は空である
  かつ プロパティ "id_token" には "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vcHJkLW1pc3AubWFxbC1nYW1lcy5qcCIsInN1YiI6IjExMSIsImF1ZCI6IjAwMDAxIiwiZXhwIjoxMzg1NTYzNTgzLCJpYXQiOjEzODU1NTYzODMsIm5vbmNlIjoiMzEyMjVzYWRzYXNhYXNhIiwiYXRfaGFzaCI6IlhZc2JjcGJJb0pBb1h3R2RmbENFM2cifQ.uK__w2kmQjSTScRPRBzOkE0icU1-yXT2I_H-CvhPjAA" がセットされている
  もし "ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"が空で、"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "8a2c488ee45488d90b62fecb5a99ea474b7f27ad737486094734502d05fa1d91" がセットされている
  かつ プロパティ "id_token" は空である
  もし "ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "access_token"を255文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" は 255 文字を超える文字列長の値になる
  かつ プロパティ "id_token" には "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vcHJkLW1pc3AubWFxbC1nYW1lcy5qcCIsInN1YiI6IjExMSIsImF1ZCI6IjAwMDAxIiwiZXhwIjoxMzg1NTYzNTgzLCJpYXQiOjEzODU1NTYzODMsIm5vbmNlIjoiMzEyMjVzYWRzYXNhYXNhIiwiYXRfaGFzaCI6IlhZc2JjcGJJb0pBb1h3R2RmbENFM2cifQ.uK__w2kmQjSTScRPRBzOkE0icU1-yXT2I_H-CvhPjAA" がセットされている
  もし "ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"を65535文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は 65535 文字を超える文字列長の値になる
  もし "ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: DBに存在しないアクセストークンを使用して、"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "存在しないアクセストークン" がセットされている
  かつ プロパティ "id_token" には "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vZGV2LW1pc3AubWFxbC1nYW1lcy5qcCIsInN1YiI6IjExMSIsImF1ZCI6IjAwMDAwIiwiZXhwIjoxMzg1MTAyMDA1LCJpYXQiOjEzODUwOTQ4MDUsIm5vbmNlIjoibm9uY2VuIiwiYXRfaGFzaCI6IkZTd3lDSHFiMldGcnRmZEttaG1wU0EifQ.0RlZwCERS5zPvMl8htgMc0oezyqHEJt1HRAmLgQ7WtE" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  もし "ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  ならば "トークンが存在しません" の例外が返ってくること
  """
  Common_Exception_Oidc_InvalidToken
  """

  シナリオ: DBに存在しないIDトークンを使用して、"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "8a2c488ee45488d90b62fecb5a99ea474b7f27ad737486094734502d05fa1d91" がセットされている
  かつ プロパティ "id_token" には "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vZGV2LW1pc3AubWFxbC1nYW1lcy5qcCIsInN1YiI6IjExMSIsImF1ZCI6IjAwMDAwIiwiZXhwIjoxMzg1MTAyMDA1LCJpYXQiOjEzODUwOTQ4MDUsIm5vbmNlIjoibm9uY2VuIiwiYXRfaGFzaCI6IkZTd3lDSHFiMldGcnRmZEttaG1wU0EifQ.0RlZwCERS5zPvMl8htgMc0oezyqHEJt1HRAmLgQ7WtE" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  もし "ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  ならば "トークンが存在しません" の例外が返ってくること
  """
  Common_Exception_Oidc_InvalidToken
  """

  シナリオ: 検証に失敗するトークンを使用して、"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "8a2c488ee45488d90b62fecb5a99ea474b7f27ad737486094734502d05fa1d91" がセットされている
  かつ プロパティ "id_token" には "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vcHJkLW1pc3AubWFxbC1nYW1lcy5qcCIsInN1YiI6IjExMSIsImF1ZCI6IjAwMDAxIiwiZXhwIjoxMzg1NTYzNTgzLCJpYXQiOjEzODU1NTYzODMsIm5vbmNlIjoiMzEyMjVzYWRzYXNhYXNhIiwiYXRfaGFzaCI6IlhZc2JjcGJJb0pBb1h3R2RmbENFM2cifQ.uK__w2kmQjSTScRPRBzOkE0icU1-yXT2I_H-CvhPjAA" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getErrorUserPlatformApplicationRelationModel" ）
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  もし "ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  ならば "トークンが存在しません" の例外が返ってくること
  """
  Common_Exception_Oidc_InvalidToken
  """

  シナリオ: 処理中にアプリケーションが終了する場合、"ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "8a2c488ee45488d90b62fecb5a99ea474b7f27ad737486094734502d05fa1d91" がセットされている
  かつ プロパティ "id_token" には "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vcHJkLW1pc3AubWFxbC1nYW1lcy5qcCIsInN1YiI6IjExMSIsImF1ZCI6IjAwMDAxIiwiZXhwIjoxMzg1NTYzNTgzLCJpYXQiOjEzODU1NTYzODMsIm5vbmNlIjoiMzEyMjVzYWRzYXNhYXNhIiwiYXRfaGFzaCI6IlhZc2JjcGJJb0pBb1h3R2RmbENFM2cifQ.uK__w2kmQjSTScRPRBzOkE0icU1-yXT2I_H-CvhPjAA" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  もし "ユーザプラットフォームアプリケーション関連取得＆トークン検証"が呼び出される
  ならば "アプリケーションが存在しません" の例外が返ってくること
  """
  Common_Exception_Oidc_InvalidToken
  """