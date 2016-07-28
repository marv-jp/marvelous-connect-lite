# language: ja
フィーチャ: readApplicationUserのテスト

  シナリオ: 正常に"アプリケーションユーザ取得"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ モック"Application_Model_ApplicationUserMapper->find"で正常値が返ってくる処理がセットされる
  もし "アプリケーションユーザ取得"が呼び出される
  ならば "Application_Model_ApplicationUser" モデルが返されること
  かつ "application_id" プロパティに "mainAppID" が入っていること
  かつ "application_user_id" プロパティに "一意のアプリケーションユーザID" が入っていること
  かつ "password" プロパティに値が入っていること
  かつ "access_token" プロパティがNULLであること
  かつ "id_token" プロパティがNULLであること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: "application_user_id"が空で、"アプリケーションユーザ取得"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  もし "アプリケーションユーザ取得"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_user_id"を255文字を超える文字列長にして、"アプリケーションユーザ取得"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" は 255 文字を超える文字列長の値になる
  もし "アプリケーションユーザ取得"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 存在しないアプリケーションユーザに対して、"アプリケーションユーザ取得"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" には "存在しないアプリケーションID" がセットされている
  かつ モック"Application_Model_ApplicationUserMapper->find"で異常値が返ってくる処理がセットされる
  もし "アプリケーションユーザ取得"が呼び出される
  ならば "対象が存在しません"の例外が返ってくること
  """
  Common_Exception_NotFound
  """
