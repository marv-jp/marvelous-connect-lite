# language: ja
フィーチャ: readUserPlatformApplicationRelationメソッドのテスト

  シナリオ: 正常に"ユーザプラットフォームアプリケーション関連取得"が呼び出される(Implicit)
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "84c126b47e893de6284f273f0ce1a9e386e4014b2643d1e460ea858d124e36ab" がセットされている
  かつ プロパティ "id_token" には "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vbWlzcC5zdGFuZGFyZGl6YXRpb24ubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDgzMTEwMDAwIiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.R2VLAl4hsUKEwXxoDmXxmLaTct5vEbXyU15MNkSPAS8" がセットされている
  かつ プロパティ "user_id" には "111" がセットされている
  かつ プロパティ "platform_user_id" には "11111111" がセットされている
  かつ プロパティ "platform_id" には "Facebook" がセットされている
  かつ プロパティ "application_id" には "00001" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  もし "ユーザプラットフォームアプリケーション関連取得"が呼び出される
  ならば 配列が返されること
  かつ 配列[0]の中身に "Application_Model_UserPlatformApplicationRelation" モデルが入っていること
  かつ 配列[0]内モデルの "user_id" プロパティに "111" が入っていること
  かつ 配列[0]内モデルの "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ 配列[0]内モデルの "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ 配列[0]内モデルの "application_id" プロパティに "appID" が入っていること
  かつ 配列[0]内モデルの "access_token" プロパティに "1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20" が入っていること
  かつ 配列[0]内モデルの "id_token" プロパティに "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vbWlzcC5zdGFuZGFyZGl6YXRpb24ubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDgzMTEwMDAwIiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.R2VLAl4hsUKEwXxoDmXxmLaTct5vEbXyU15MNkSPAS8" が入っていること
  かつ 配列[0]内モデルの "authorization_code" プロパティがNULLであること
  かつ 配列[0]内モデルの "created_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ 配列[0]内モデルの "updated_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ 配列[0]内モデルの "deleted_date" プロパティがNULLであること


  シナリオ: "access_token"を255文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" は 255 文字を超える文字列長の値になる
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "user_id" には "111" がセットされている
  かつ プロパティ "platform_user_id" には "11111111" がセットされている
  かつ プロパティ "platform_id" には "Facebook" がセットされている
  かつ プロパティ "application_id" には "00001" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"を65535文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は 65535 文字を超える文字列長の値になる
  かつ プロパティ "user_id" には "111" がセットされている
  かつ プロパティ "platform_user_id" には "11111111" がセットされている
  かつ プロパティ "platform_id" には "Facebook" がセットされている
  かつ プロパティ "application_id" には "00001" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"を11文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "user_id" には "111" がセットされている
  かつ プロパティ "platform_user_id" には "11111111" がセットされている
  かつ プロパティ "platform_id" には "Facebook" がセットされている
  かつ プロパティ "application_id" は 11 文字を超える文字列長の値になる
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_id"を191文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "user_id" には "111" がセットされている
  かつ プロパティ "platform_user_id" には "11111111" がセットされている
  かつ プロパティ "platform_id" は 191 文字を超える文字列長の値になる
  かつ プロパティ "application_id" には "00001" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"を255文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
   かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "user_id" には "111" がセットされている
  かつ プロパティ "platform_user_id" は 255 文字を超える文字列長の値になる
  かつ プロパティ "platform_id" には "Facebook" がセットされている
  かつ プロパティ "application_id" には "00001" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "authorization_code"を64文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
   かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "user_id" には "111" がセットされている
  かつ プロパティ "platform_user_id" には "11111111" がセットされている
  かつ プロパティ "platform_id" には "Facebook" がセットされている
  かつ プロパティ "application_id" には "00001" がセットされている
  かつ プロパティ "authorization_code" は 64 文字を超える文字列長の値になる
  もし "ユーザプラットフォームアプリケーション関連取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: DBからレコードが取得できない状態で、"ユーザプラットフォームアプリケーション関連取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "存在しないアクセストークン" がセットされている
  かつ プロパティ "id_token" には "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vZGV2LW1pc3AubWFxbC1nYW1lcy5qcCIsInN1YiI6IjExMSIsImF1ZCI6IjAwMDAwIiwiZXhwIjoxMzg1MTAyMDA1LCJpYXQiOjEzODUwOTQ4MDUsIm5vbmNlIjoibm9uY2VuIiwiYXRfaGFzaCI6IkZTd3lDSHFiMldGcnRmZEttaG1wU0EifQ.0RlZwCERS5zPvMl8htgMc0oezyqHEJt1HRAmLgQ7WtE" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  もし "ユーザプラットフォームアプリケーション関連取得"が呼び出される
  ならば "トークンが存在しません" の例外が返ってくること
  """
  Common_Exception_Oidc_InvalidToken
  """
