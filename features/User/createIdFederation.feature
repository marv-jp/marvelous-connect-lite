# language: ja
フィーチャ: createIdFederationのテスト

  シナリオ: アプリケーションユーザが登録済みで、正常に"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モックとして "Application_Model_ApplicationUserMapper" クラスを "applicationUserMapper" として使用する
  かつ モックとして "Application_Model_ApplicationUserPlatformRelationMapper" クラスを "applicationUserPlatformRelationMapper" として使用する
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  かつ モック "applicationUserMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationUserModel" ）
  かつ モック "applicationUserPlatformRelationMapper" の "fetchAllCreateIdFederation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationUserPlatformRelationModel" ）
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAllCreateIdFederation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "createApplicationUserPlatformRelation" でモデル "Application_Model_ApplicationUserPlatformRelation" が返ってくる処理がセットされる（モデルの中身は "getApplicationUserPlatformRelationModel" ）
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelationWithValidate" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Logic_ApplicationUser" クラスを "logicApplicationUser" として使用する
  かつ モック "logicApplicationUser" の "updateApplicationUser" でモデル "Application_Model_ApplicationUser " が返ってくる処理がセットされる（モデルの中身は "getApplicationUserModel" ）
  かつ モックとして "Mock_ApiMode" クラスを "apiMode" として使用する
  かつ モック "apiMode" の "isTrustedProxy" でboolean型の "true" が返ってくる処理がセットされる
  もし "ID連携処理"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること

  シナリオ: アプリケーションユーザが未登録で、"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モックとして "Application_Model_ApplicationUserMapper" クラスを "applicationUserMapper" として使用する
  かつ モックとして "Application_Model_ApplicationUserPlatformRelationMapper" クラスを "applicationUserPlatformRelationMapper" として使用する
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  かつ モック "applicationUserMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  かつ モック "applicationUserPlatformRelationMapper" の "fetchAllCreateIdFederation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationUserPlatformRelationModel" ）
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAllCreateIdFederation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Logic_ApplicationUser" クラスを "logicApplicationUser" として使用する
  かつ モック "logicApplicationUser" の "createApplicationUser" でモデル "Application_Model_ApplicationUser" が返ってくる処理がセットされる（モデルの中身は "getApplicationUserModel" ）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "createApplicationUserPlatformRelation" でモデル "Application_Model_ApplicationUserPlatformRelation" が返ってくる処理がセットされる（モデルの中身は "getApplicationUserPlatformRelationModel" ）
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelationWithValidate" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Logic_ApplicationUser" クラスを "logicApplicationUser" として使用する
  かつ モック "logicApplicationUser" の "updateApplicationUser" でモデル "Application_Model_ApplicationUser " が返ってくる処理がセットされる（モデルの中身は "getApplicationUserModel" ）
  かつ モックとして "Mock_ApiMode" クラスを "apiMode" として使用する
  かつ モック "apiMode" の "isTrustedProxy" でboolean型の "true" が返ってくる処理がセットされる
  もし "ID連携処理"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること

  シナリオ: アプリケーションユーザが登録済み・アプリケーションユーザプラットフォーム関連に既登録で、正常に"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モックとして "Application_Model_ApplicationUserMapper" クラスを "applicationUserMapper" として使用する
  かつ モックとして "Application_Model_ApplicationUserPlatformRelationMapper" クラスを "applicationUserPlatformRelationMapper" として使用する
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  かつ モック "applicationUserMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationUserModel" ）
  かつ モック "applicationUserPlatformRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationUserPlatformRelationModel" ）
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAllCreateIdFederation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "createApplicationUserPlatformRelation" でモデル "Application_Model_ApplicationUserPlatformRelation" が返ってくる処理がセットされる（モデルの中身は "getApplicationUserPlatformRelationModel" ）
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "createUserPlatformApplicationRelation" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelationWithValidate" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モック "logicUser" の "readUserPlatformApplicationRelation" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Logic_ApplicationUser" クラスを "logicApplicationUser" として使用する
  かつ モック "logicApplicationUser" の "updateApplicationUser" でモデル "Application_Model_ApplicationUser " が返ってくる処理がセットされる（モデルの中身は "getApplicationUserModel" ）
  かつ モックとして "Mock_ApiMode" クラスを "apiMode" として使用する
  かつ モック "apiMode" の "isTrustedProxy" でboolean型の "true" が返ってくる処理がセットされる
  もし "ID連携処理"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること

  シナリオ: "access_token"が空で、"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
#  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  もし "ID連携処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"が空で、"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
#  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  もし "ID連携処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_user_id"が空で、"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
#  かつ 引数２つ目のプロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "readUserPlatformApplicationRelationWithValidate" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Mock_ApiMode" クラスを "apiMode" として使用する
  かつ モック "apiMode" の "isTrustedProxy" でboolean型の "true" が返ってくる処理がセットされる
  もし "ID連携処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"が空で、"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
#  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "readUserPlatformApplicationRelationWithValidate" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Mock_ApiMode" クラスを "apiMode" として使用する
  かつ モック "apiMode" の "isTrustedProxy" でboolean型の "true" が返ってくる処理がセットされる
  もし "ID連携処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "access_token"を255文字を超える文字列長にして、"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" は 255 文字を超える文字列長の値になる
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  もし "ID連携処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"を65535文字を超える文字列長にして、"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は 65535 文字を超える文字列長の値になる
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  もし "ID連携処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_user_id"を255文字を超える文字列長にして、"ID連携処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "appID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" は 255 文字を超える文字列長の値になる
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "readUserPlatformApplicationRelationWithValidate" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Mock_ApiMode" クラスを "apiMode" として使用する
  かつ モック "apiMode" の "isTrustedProxy" でboolean型の "true" が返ってくる処理がセットされる
  もし "ID連携処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
