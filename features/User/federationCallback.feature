# language: ja
フィーチャ: federationCallbackのテスト

  シナリオ: 正常に"プラットフォーム認証後処理"が呼び出される(Implicit)
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "PF_アクセストークン" がセットされている
  かつ プロパティ "id_token" には "PF_IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updatePlatformUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModels" ）
  かつ モック "logicUser" の "updateUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "createPlatformUser" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "createUser" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplication" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" で空の配列が返ってくる処理がセットされる
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "111" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに値が入っていること
  かつ "id_token" プロパティに値が入っていること
  かつ "authorization_code" プロパティがNULLであること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: 正常に"プラットフォーム認証後処理"が呼び出される(Basic)
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "PF_アクセストークン" がセットされている
  かつ プロパティ "id_token" には "PF_IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "code" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updatePlatformUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "updateUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModels" ）
  かつ モック "logicUser" の "createPlatformUser" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "createUser" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplication" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModelBasic" ）
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" で空の配列が返ってくる処理がセットされる
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "111" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに値が入っていること
  かつ "id_token" プロパティに値が入っていること
  かつ "authorization_code" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: nonceを省略した際にも、正常に"プラットフォーム認証後処理"が呼び出される(Basic)
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "PF_アクセストークン" がセットされている
  かつ プロパティ "id_token" には "PF_IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "code" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updatePlatformUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "updateUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModels" ）
  かつ モック "logicUser" の "createPlatformUser" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "createUser" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplication" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModelBasic" ）
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" で空の配列が返ってくる処理がセットされる
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "111" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに値が入っていること
  かつ "id_token" プロパティに値が入っていること
  かつ "authorization_code" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: MISPとの再連携時、"プラットフォーム認証後処理"が呼び出される(Implicit)
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "PF_アクセストークン" がセットされている
  かつ プロパティ "id_token" には "PF_IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updatePlatformUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "updateUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModels" ）
  かつ モック "logicUser" の "createPlatformUser" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "createUser" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplication" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getInactivePlatformUserModel" ）
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Application_Model_UserMapper" クラスを "userMapper" として使用する
  かつ モック "userMapper" の "find" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getInactiveUserModel" ）
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "111" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに値が入っていること
  かつ "id_token" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: プラットフォームユーザが既に登録されている状態で、"プラットフォーム認証後処理"が呼び出される(Implicit)
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "PF_アクセストークン" がセットされている
  かつ プロパティ "id_token" には "PF_IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updatePlatformUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "updateUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModels" ）
  かつ モック "logicUser" の "createPlatformUser" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "createUser" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplication" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "111" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに値が入っていること
  かつ "id_token" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: プラットフォームユーザがバンの状態で、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "PF_アクセストークン" がセットされている
  かつ プロパティ "id_token" には "PF_IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updatePlatformUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "updateUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModels" ）
  かつ モック "logicUser" の "createPlatformUser" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "createUser" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplication" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getBannedPlatformUserModel" ）
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "このユーザはバンされています" の例外が返ってくること
  """
  Logic_Exception_Banned
  """

  シナリオ: リダイレクトURIが不正な状態で、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "PF_アクセストークン" がセットされている
  かつ プロパティ "id_token" には "PF_IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updatePlatformUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "updateUser" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "createPlatformUser" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  かつ モック "logicUser" の "createUser" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplication" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "false" が返ってくる処理がセットされる
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" で空の配列が返ってくる処理がセットされる
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "このユーザはバンされています" の例外が返ってくること
  """
  Common_Exception_OauthInvalidRequest
  """

  シナリオ: "platform_id"が空で、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" は空である
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"が空で、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" は空である
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"が空で、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" は空である
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "authorization_code"が空で、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" は空である
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "authorization_code"に指定外の文字列をセットした状態で、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "FacebookのID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" は空である
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "error_string" がセットされている
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_id"を191文字を超える文字列長にして、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" は 191 文字を超える文字列長の値になる
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"を255文字を超える文字列長にして、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" は 255 文字を超える文字列長の値になる
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "access_token"を65535文字を超える文字列長にして、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" は 65535 文字を超える文字列長の値になる
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"を65535文字を超える文字列長にして、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は 65535 文字を超える文字列長の値になる
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"を11文字を超える文字列長にして、"プラットフォーム認証後処理"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数２つ目のプロパティ "iss" には "http://dev-misp.maql-games.jp" がセットされている
  かつ 引数２つ目のプロパティ "aud" は 11 文字を超える文字列長の値になる
  かつ 引数２つ目のプロパティ "exp" には "1350366631" がセットされている
  かつ 引数２つ目のプロパティ "iat" には "1347947431" がセットされている
  かつ 引数２つ目のプロパティ "nonce" には "のんす" がセットされている
  かつ 引数３つ目には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ 引数３つ目のプロパティ "response_type" には "id_token token" がセットされている
  もし "プラットフォーム認証後処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
