# language: ja
フィーチャ: Logic_User#updateUserPlatformApplicationRelationメソッドのテスト

  シナリオ: 正常系：ユーザプラットフォームアプリケーション関連を論理削除する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  かつ: モック1は "Application_Model_UserPlatformApplicationRelationMapper" 型である
  かつ: モック1は "fetchAll" メソッドで "Application_Model_UserPlatformApplicationRelation" クラスのオブジェクトを 1 つ配列で返却する
  かつ: モック1は "update" メソッドで "integer" 型の "1" を返却する
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば: "integer" 型の "1" が返却されること

  シナリオ: 正常系：ユーザプラットフォームアプリケーション関連情報を複数レコード論理削除する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
#  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
#  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  かつ: モック1は "Application_Model_UserPlatformApplicationRelationMapper" 型である
  かつ: モック1は "fetchAll" メソッドで "Application_Model_UserPlatformApplicationRelation" クラスのオブジェクトを 3 つ配列で返却する
  かつ: モック1は "updateByUserIdAndApplicationId" メソッドで "integer" 型の "3" を返却する
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば: "integer" 型の "3" が返却されること

  シナリオ: 正常系：ユーザプラットフォームアプリケーション関連情報を複数レコード論理削除する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
#  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
#  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  かつ: モック1は "Application_Model_UserPlatformApplicationRelationMapper" 型である
  かつ: モック1は "fetchAll" メソッドで "Application_Model_UserPlatformApplicationRelation" クラスのオブジェクトを 2 つ配列で返却する
  かつ: モック1は "updateByPlatformUserIdAndPlatformId" メソッドで "integer" 型の "2" を返却する
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば: "integer" 型の "2" が返却されること

  シナリオ: 正常系：ユーザプラットフォームアプリケーション関連の認可コードをNULLにする
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "authorization_code" に "string" 型の "認可コード" がセットされている
#  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  かつ: モック1は "Application_Model_UserPlatformApplicationRelationMapper" 型である
  かつ: モック1は "fetchAll" メソッドで "Application_Model_UserPlatformApplicationRelation" クラスのオブジェクトを 1 つ配列で返却する
  かつ: モック1は "update" メソッドで "integer" 型の "1" を返却する
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば: "integer" 型の "1" が返却されること

  シナリオ: 異常系：パラメータ不正：キー項目全てが未セットの場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
#  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
#  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
#  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
#  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：platform_user_id が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に 255 文字を超える値がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：platform_id が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に 191 文字を超える値がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：application_id が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "application_id" に 11 文字を超える値がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """
  
  シナリオ: 異常系：length不正：refresh_token が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "refresh_token" に "string" 型の "refresh_token" がセットされている
  かつ: 引数1のプロパティ "refresh_token" に 255 文字を超える値がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：authorization_code が規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "1" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "authorization_code" に 64 文字を超える値がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：処理対象ナシ：処理対象が存在しない場合、NotFoundの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "999999999999999" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "deleted_date" に "string" 型の "2013-11-11 11:11:11" がセットされている
  かつ: モック1は "Application_Model_UserPlatformApplicationRelationMapper" 型である
  かつ: モック1は "fetchAll" メソッドで空の配列を返却する
  もし: ユーザプラットフォームアプリケーション関連更新を実行する
  ならば "NotFound"の例外が発生すること
  """
  Common_Exception_NotFound
  """
