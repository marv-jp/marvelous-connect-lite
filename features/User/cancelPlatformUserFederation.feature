# language: ja
フィーチャ: Logic_User#cancelPlatformUserFederationメソッドのテスト

  シナリオ: 正常系：連携解除通知リクエスト時処理を実行する
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: モック1は "Logic_User" 型である
  かつ: モック1は "updateApplicationUserPlatformRelation" メソッドで "integer" 型の "1" を返却する
  かつ: モック1は "updateUserPlatformApplicationRelation" メソッドで "integer" 型の "1" を返却する
  かつ: モック1は "updatePlatformUser" メソッドで "Application_Model_PlatformUser" クラスのオブジェクトを返却する
  かつ: モック1は "updateUser" メソッドで "Application_Model_User" クラスのオブジェクトを返却する
  かつ: モック1は "isValidUser" メソッドで "boolean" 型の "true" を返却する
  かつ: モック2は "Application_Model_UserPlatformApplicationRelationMapper" 型である
  かつ: モック2は "fetchAll" メソッドで "Application_Model_UserPlatformApplicationRelation" クラスのオブジェクトを 1 つ配列で返却する
  もし: 連携解除通知リクエスト時処理を実行する
  ならば: "boolean" 型の "true" が返却されること

  シナリオ: 異常系：パラメータ不正：プラットフォームユーザIDが未セットの状態で連携解除通知リクエスト時処理を実行すると、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
#  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  もし: 連携解除通知リクエスト時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：パラメータ不正：プラットフォームIDが未セットの状態で連携解除通知リクエスト時処理を実行すると、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
#  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  もし: 連携解除通知リクエスト時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：プラットフォームユーザIDが規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_user_id" に 255 文字を超える値がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  もし: 連携解除通知リクエスト時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：length不正：プラットフォームIDが規定lengthを超える場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に 191 文字を超える値がセットされている
  もし: 連携解除通知リクエスト時処理を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """
