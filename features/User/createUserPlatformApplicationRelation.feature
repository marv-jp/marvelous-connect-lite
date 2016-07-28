# language: ja
フィーチャ: createUserPlatformApplicationRelationメソッドのテスト

  シナリオ: 正常に"ユーザプラットフォームアプリケーション関連登録"が呼び出される(Implicit)
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
#  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "replace" で1件ヒットが返ってくる処理がセットされる
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "ユーザID" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに "アクセストークン" が入っていること
  かつ "id_token" プロパティに "IDトークン" が入っていること
  かつ "authorization_code" プロパティがNULLであること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: 正常に"ユーザプラットフォームアプリケーション関連登録"が呼び出される(Basic)
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "refresh_token" には "リフレッシュトークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "replace" で1件ヒットが返ってくる処理がセットされる
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "ユーザID" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに "アクセストークン" が入っていること
  かつ "id_token" プロパティに "IDトークン" が入っていること
  かつ "refresh_token" プロパティに "リフレッシュトークン" が入っていること
  かつ "authorization_code" プロパティに "認可コード" が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: 何らかの原因でUserPlatformApplicationRelationへの登録が失敗する状態で、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "replace" で0件ヒットが返ってくる処理がセットされる
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "登録に失敗しました" の例外が返ってくること
  """
  Exception
  """

  シナリオ: "user_id"が空で、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" は空である
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_id"が空で、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" は空である
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"が空で、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" は空である
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"が空で、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" は空である
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_id"を191文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" は 191 文字を超える文字列長の値になる
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"を255文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" は 255 文字を超える文字列長の値になる
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"を11文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" は 11 文字を超える文字列長の値になる
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "access_token"を255文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" は 255 文字を超える文字列長の値になる
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"を65535文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は 65535 文字を超える文字列長の値になる
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
  
  シナリオ: "refresh_token"を255文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "refresh_token" は 255 文字を超える文字列長の値になる
  かつ プロパティ "authorization_code" には "認可コード" がセットされている
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "authorization_code"を64文字を超える文字列長にして、"ユーザプラットフォームアプリケーション関連登録"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "user_id" には "ユーザID" がセットされている
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ プロパティ "authorization_code" は 64 文字を超える文字列長の値になる
  もし "ユーザプラットフォームアプリケーション関連登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
