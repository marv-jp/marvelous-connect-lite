# language: ja
フィーチャ: createApplicationUserメソッドのテスト

  シナリオ: 正常に"アプリケーションユーザ登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ モック"Application_Model_ApplicationUserMapper->find"で異常値が返ってきて、"Application_Model_ApplicationUserMapper->insert"で正常値が返ってくる処理がセットされる
  もし "アプリケーションユーザ登録"が呼び出される
  ならば "Application_Model_ApplicationUser" モデルが返されること
  かつ "application_id" プロパティに "mainAppID" が入っていること
  かつ "application_user_id" プロパティに "一意のアプリケーションユーザID" が入っていること
  かつ "password" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: 何らかの原因でApplicationUserへのinsertが失敗する状態で、"アプリケーションユーザ登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ モック"Application_Model_ApplicationUserMapper->insert"で0が返ってくる処理がセットされる
  もし "アプリケーションユーザ登録"が呼び出される
  ならば "登録に失敗しました"の例外が返ってくること
  """
  Exception
  """

  シナリオ: "application_user_id"が空で、"アプリケーションユーザ登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  もし "アプリケーションユーザ登録"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_user_id"を255文字を超える文字列長にして、"アプリケーションユーザ登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" は 255 文字を超える文字列長の値になる
  もし "アプリケーションユーザ登録"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_user_id"が既存アプリケーションユーザIDで、"アプリケーションユーザ登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUser" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_user_id" には "既存のアプリケーションユーザID" がセットされている
  かつ モック"Application->isValidApplication"で正常値が返ってくる処理がセットされる
  かつ モックにアプリケーションユーザがヒットする処理をセットする
  もし "アプリケーションユーザ登録"が呼び出される
  ならば "既に登録対象が存在しています"の例外が返ってくること
  """
  Common_Exception_AlreadyExists
  """
