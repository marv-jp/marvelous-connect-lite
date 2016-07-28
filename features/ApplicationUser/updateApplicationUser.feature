# language: ja
フィーチャ: updateApplicationUserのテスト

  シナリオ: 正常に"アプリケーションユーザ更新"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ プロパティ "status" には "1" がセットされている
  かつ モック"Application_Model_ApplicationUserMapper->find"で正常値が返ってきて、"Application_Model_ApplicationUserMapper->update"で正常値が返ってくる処理がセットされる
  もし "アプリケーションユーザ更新"が呼び出される
  ならば "Application_Model_ApplicationUser" モデルが返されること

  シナリオ: "application_user_id"が空で、"アプリケーションユーザ更新"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "status" には "1" がセットされている
  もし "アプリケーションユーザ更新"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_user_id"を255文字を超える文字列長にして、"アプリケーションユーザ更新"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" は 255 文字を超える文字列長の値になる
  かつ プロパティ "status" には "1" がセットされている
  もし "アプリケーションユーザ更新"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 存在しないアプリケーションユーザに対して、"アプリケーションユーザ更新"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" には "存在しないアプリケーションユーザID" がセットされている
  かつ プロパティ "status" には "1" がセットされている
  かつ モック"Application->isValidApplication"で正常値が返ってくる処理がセットされる
  かつ モック"Application_Model_ApplicationUserMapper->find"で異常値が返ってくる処理がセットされる
  もし "アプリケーションユーザ更新"が呼び出される
  ならば "対象が存在しません"の例外が返ってくること
  """
  Common_Exception_NotFound
  """

  シナリオ: "status"を0,1以外にして、"アプリケーションユーザ更新"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ プロパティ "status" には "3" がセットされている
  もし "アプリケーションユーザ更新"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 更新が行われない内容で、"アプリケーションユーザ更新"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ プロパティ "status" には "1" がセットされている
  かつ モック"Application->isValidApplication"で正常値が返ってくる処理がセットされる
  かつ モック"Application_Model_ApplicationUserMapper->find"で正常値が返ってきて、"Application_Model_ApplicationUserMapper->update"で異常値が返ってくる処理がセットされる
  もし "アプリケーションユーザ更新"が呼び出される
  ならば "更新が行われませんでした"の例外が返ってくること
  """
  Common_Exception_NotModified
  """