# language: ja
フィーチャ: readUserのテスト

  シナリオ: 正常に"ユーザ取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には 255 文字列長のデータがセットされている
  かつ プロパティ "id_token" には 65535 文字列長のデータがセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "readUserPlatformApplicationRelationWithValidate" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Application_Model_ApplicationUserPlatformRelationMapper" クラスを "applicationUserPlatformRelationMapper" として使用する
  かつ モック "applicationUserPlatformRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationUserPlatformRelationModel" ）
  かつ モックとして "Application_Model_ApplicationUserMapper" クラスを "applicationUserMapper" として使用する
  かつ モック "applicationUserMapper" の "find" でモデル "Application_Model_ApplicationUser" が返ってくる処理がセットされる（モデルの中身は "getApplicationUserModel" ）
  かつ モックとして "Application_Model_UserMapper" クラスを "userMapper" として使用する
  かつ モック "userMapper" の "find" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getUserModel" ）
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  もし "ユーザ取得"が呼び出される
  ならば "Application_Model_User" モデルが返されること
  かつ "user_id" プロパティに "ユーザID" が入っていること
  かつ "apps" プロパティに配列が入っていること
  かつ "apps" プロパティの配列[0]は "Application_Model_ApplicationUser" モデルが入っていること
  かつ "apps" プロパティの配列[0]に入っているモデルの "application_user_id" プロパティに "アプリケーションユーザID" が入っていること
  かつ "apps" プロパティの配列[0]に入っているモデルの "application_id" プロパティに "appID" が入っていること
  かつ "apps" プロパティの配列[0]に入っているモデルの "application_world_id" プロパティに " " が入っていること
  かつ "apps" プロパティの配列[0]に入っているモデルの "application_user_name" プロパティに "アプリケーションユーザ名" が入っていること
  かつ "apps" プロパティの配列[0]に入っているモデルの "created_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ "apps" プロパティの配列[0]に入っているモデルの "updated_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ "accounts" プロパティに配列が入っていること
  かつ "accounts" プロパティの配列[0]は "Application_Model_PlatformUser" モデルが入っていること
  かつ "accounts" プロパティの配列[0]に入っているモデルの "platform_user_id" プロパティに "" が入っていること
  かつ "accounts" プロパティの配列[0]に入っているモデルの "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "accounts" プロパティの配列[0]に入っているモデルの "platform_user_name" プロパティに "" が入っていること
  かつ "accounts" プロパティの配列[0]に入っているモデルの "Platform_user_display_name" プロパティに "" が入っていること
  かつ "accounts" プロパティの配列[0]に入っているモデルの "created_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ "accounts" プロパティの配列[0]に入っているモデルの "updated_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ "created_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ "updated_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: "access_token"が空で、"ユーザ取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" は空である
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "ユーザ取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"が空で、"ユーザ取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は空である
  もし "ユーザ取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "access_token"を255文字を超える文字列長にして、"ユーザ取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" は 255 文字を超える文字列長の値になる
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "ユーザ取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"を65535文字を超える文字列長にして、"ユーザ取得"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は 65535 文字を超える文字列長の値になる
  もし "ユーザ取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
