# language: ja
フィーチャ: Logic_User#cancelIdFederationメソッドのテスト

  シナリオ: 正常系：ID連携解除時処理を実行する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "access_token" に "string" 型の "アクセストークン" がセットされている
  かつ: 引数1のプロパティ "id_token" に "string" 型の "IDトークン" がセットされている
  かつ: モック1は "Logic_User" 型である
  かつ: モック1は "updateApplicationUserPlatformRelation" メソッドで "Application_Model_ApplicationUserPlatformRelation" クラスのオブジェクトを 1 つ配列で返却する
  かつ: モック1は "updateUserPlatformApplicationRelation" メソッドで "Application_Model_UserPlatformApplicationRelation" クラスのオブジェクトを 1 つ配列で返却する
  かつ: モック1は "updaterPlatformUser" メソッドで "Application_Model_PlatformUser" クラスのオブジェクトを 1 つ配列で返却する
  かつ: モック1は "readUserPlatformApplicationRelationWithValidate" メソッドで "Application_Model_UserPlatformApplicationRelation" クラスのオブジェクトを返却する
  かつ: モック2は "Application_Model_UserPlatformApplicationRelationMapper" 型である
  かつ: モック2は "fetchAll" メソッドで "Application_Model_UserPlatformApplicationRelation" クラスのオブジェクトを 1 つ配列で返却する
  もし: ID連携解除時処理を実行する
  ならば: "boolean" 型の "true" が返却されること


  シナリオ: 異常系：パラメータ不正：プラットフォームIDが未セットの状態でID連携解除時処理を実行すると、パラメータ不正の例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
#  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "access_token" に "string" 型の "アクセストークン" がセットされている
  かつ: 引数1のプロパティ "id_token" に "string" 型の "IDトークン" がセットされている
  もし: ID連携解除時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：パラメータ不正：アプリケーションIDが未セットの状態でID連携解除時処理を実行すると、パラメータ不正の例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
#  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "access_token" に "string" 型の "アクセストークン" がセットされている
  かつ: 引数1のプロパティ "id_token" に "string" 型の "IDトークン" がセットされている
  もし: ID連携解除時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：パラメータ不正：アクセストークンが未セットの状態でID連携解除時処理を実行すると、パラメータ不正の例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
#  かつ: 引数1のプロパティ "access_token" に "string" 型の "アクセストークン" がセットされている
  かつ: 引数1のプロパティ "id_token" に "string" 型の "IDトークン" がセットされている
  もし: ID連携解除時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：パラメータ不正：IDトークンが未セットの状態でID連携解除時処理を実行すると、パラメータ不正の例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "access_token" に "string" 型の "アクセストークン" がセットされている
#  かつ: 引数1のプロパティ "id_token" に "string" 型の "IDトークン" がセットされている
  もし: ID連携解除時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：プラットフォームIDが規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に 191 文字を超える値がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "access_token" に "string" 型の "アクセストークン" がセットされている
  かつ: 引数1のプロパティ "id_token" に "string" 型の "IDトークン" がセットされている
  もし: ID連携解除時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：アプリケーションIDが規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "application_id" に 11 文字を超える値がセットされている
  かつ: 引数1のプロパティ "access_token" に "string" 型の "アクセストークン" がセットされている
  かつ: 引数1のプロパティ "id_token" に "string" 型の "IDトークン" がセットされている
  もし: ID連携解除時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：アクセストークンが規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "access_token" に "string" 型の "アクセストークン" がセットされている
  かつ: 引数1のプロパティ "access_token" に 255 文字を超える値がセットされている
  かつ: 引数1のプロパティ "id_token" に "string" 型の "IDトークン" がセットされている
  もし: ID連携解除時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：IDトークンが規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_UserPlatformApplicationRelation" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "application_id" に "string" 型の "appID" がセットされている
  かつ: 引数1のプロパティ "access_token" に "string" 型の "アクセストークン" がセットされている
  かつ: 引数1のプロパティ "id_token" に "string" 型の "IDトークン" がセットされている
  かつ: 引数1のプロパティ "id_token" に 65535 文字を超える値がセットされている
  もし: ID連携解除時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """
