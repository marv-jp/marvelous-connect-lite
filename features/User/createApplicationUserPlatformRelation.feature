# language: ja
フィーチャ: createApplicationUserPlatformRelationメソッドのテスト

  シナリオ: 正常に"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ モックとして "Application_Model_ApplicationUserPlatformRelationMapper" クラスを "applicationUserPlatformRelationMapper" として使用する
  かつ モック "applicationUserPlatformRelationMapper" の "replace" で1件ヒットが返ってくる処理がセットされる
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "Application_Model_ApplicationUserPlatformRelation" モデルが返されること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "application_user_id" プロパティに "アプリケーションユーザID" が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: 何らかの原因でApplicationUserPlatformRelationへの登録が失敗する状態で、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ モックとして "Application_Model_ApplicationUserPlatformRelationMapper" クラスを "applicationUserPlatformRelationMapper" として使用する
  かつ モック "applicationUserPlatformRelationMapper" の "insert" で0件ヒットが返ってくる処理がセットされる
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "登録に失敗しました" の例外が返ってくること
  """
  Exception
  """

  シナリオ: "application_user_id"が空で、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "application_user_id" は空である
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"が空で、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" は空である
  かつ プロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"が空で、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" は空である
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_id"が空で、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" は空である
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_id"を191文字を超える文字列長にして、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" は 191 文字を超える文字列長の値になる
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"を255文字を超える文字列長にして、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" は 255 文字を超える文字列長の値になる
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"を11文字を超える文字列長にして、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" は 11 文字を超える文字列長の値になる
  かつ プロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_user_id"を255文字を超える文字列長にして、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "application_user_id" は 255 文字を超える文字列長の値になる
  かつ プロパティ "application_world_id" には " " がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_world_id"を255文字を超える文字列長にして、"アプリケーションユーザプラットフォーム関連登録"が呼び出される
  前提 引数には "Application_Model_ApplicationUserPlatformRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "application_user_id" には "アプリケーションユーザID" がセットされている
  かつ プロパティ "application_world_id" は 255 文字を超える文字列長の値になる
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  もし "アプリケーションユーザプラットフォーム関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """