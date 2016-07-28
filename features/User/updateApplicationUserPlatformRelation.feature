# language: ja
フィーチャ: Logic_User#updateApplicationUserPlatformRelationメソッドのテスト

  シナリオ: 正常系：アプリケーションユーザプラットフォーム関連を論理削除する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
  かつ: 引数1のプロパティ "application_world_id" に "string" 型の " " がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  かつ: モック1は "Application_Model_ApplicationUserPlatformRelationMapper" 型である
  かつ: モック1は "fetchAll" メソッドで "Application_Model_ApplicationUserPlatformRelation" クラスのオブジェクトを 1 つ配列で返却する
  かつ: モック1は "update" メソッドで "integer" 型の "1" を返却する
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば: "integer" 型の "1" が返却されること

  シナリオ: 正常系：アプリケーションユーザプラットフォーム関連情報を複数レコード論理削除する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
#  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
#  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
#  かつ: 引数1のプロパティ "application_world_id" に "string" 型の " " がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  かつ: モック1は "Application_Model_ApplicationUserPlatformRelationMapper" 型である
  かつ: モック1は "fetchAll" メソッドで "Application_Model_ApplicationUserPlatformRelation" クラスのオブジェクトを 3 つ配列で返却する
  かつ: モック1は "updateByPlatformUserIdAndPlatformId" メソッドで "integer" 型の "3" を返却する
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば: "integer" 型の "3" が返却されること

  シナリオ: 正常系：アプリケーションユーザプラットフォーム関連情報を複数レコード論理削除する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
#  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
#  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
#  かつ: 引数1のプロパティ "application_world_id" に "string" 型の " " がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  かつ: モック1は "Application_Model_ApplicationUserPlatformRelationMapper" 型である
  かつ: モック1は "fetchAll" メソッドで "Application_Model_ApplicationUserPlatformRelation" クラスのオブジェクトを 2 つ配列で返却する
  かつ: モック1は "updateByApplicationUserIdAndApplicationId" メソッドで "integer" 型の "2" を返却する
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば: "integer" 型の "2" が返却されること

  シナリオ: 異常系：パラメータ不正：キー項目全てが未セットの場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
#  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
#  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
#  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
#  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
#  かつ: 引数1のプロパティ "application_world_id" に "string" 型の " " がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：platform_user_id が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に 255 文字を超える値がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
  かつ: 引数1のプロパティ "application_world_id" に "string" 型の " " がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：platform_id が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に 191 文字を超える値がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
  かつ: 引数1のプロパティ "application_world_id" に "string" 型の " " がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：application_user_id が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
  かつ: 引数1のプロパティ "application_user_id" に 255 文字を超える値がセットされている
  かつ: 引数1のプロパティ "application_world_id" に "string" 型の " " がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：application_id が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "application_id" に 11 文字を超える値がセットされている
  かつ: 引数1のプロパティ "application_world_id" に "string" 型の " " がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：application_world_id が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "application_world_id" に "string" 型の " " がセットされている
  かつ: 引数1のプロパティ "application_world_id" に 255 文字を超える値がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：処理対象ナシ：処理対象が存在しない場合、NotFoundの例外が発生する
  前提: 引数1は "Application_Model_ApplicationUserPlatformRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "存在しないプラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_user_id" に "string" 型の "アプリケーションユーザID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  かつ: モック1は "Application_Model_ApplicationUserPlatformRelationMapper" 型である
  かつ: モック1は "fetchAll" メソッドで空の配列を返却する
  もし: アプリケーションユーザプラットフォーム関連更新を実行する
  ならば "NotFound"の例外が発生すること
  """
  Common_Exception_NotFound
  """
