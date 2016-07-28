# language: ja
フィーチャ: Logic_User#updatePlatformUserメソッドのテスト

  シナリオ: 正常系：プラットフォームユーザを未登録にする
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "status" に "integer" 型の "0" がセットされている
  かつ: モック1は "Application_Model_PlatformUserMapper" 型である
  かつ: モック1は "find" メソッドで "Application_Model_PlatformUser" クラスのオブジェクトを返却する
  かつ: モック1は "update" メソッドで "integer" 型の "1" を返却する
  もし: プラットフォームユーザ更新を実行する
  ならば: "Application_Model_PlatformUser" 型のオブジェクトが返却されること

  シナリオ: 異常系：パラメータ不正：プラットフォームユーザIDが未セットの場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
#  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "status" に "integer" 型の "0" がセットされている
  もし: プラットフォームユーザ更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：パラメータ不正：プラットフォームIDが未セットの場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
#  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "status" に "integer" 型の "0" がセットされている
  もし: プラットフォームユーザ更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：パラメータ不正：ステータスが許可されていない値の場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "プラットフォームユーザID" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "status" に "integer" 型の "100" がセットされている
  もし: プラットフォームユーザ更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：処理対象ナシ：処理対象が存在しない場合、NotFoundの例外が発生する
  前提: 引数1は "Application_Model_PlatformUser" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "platform_user_id" に "string" 型の "9999999999" がセットされている
  かつ: 引数1のプロパティ "platform_id" に "string" 型の "プラットフォームID" がセットされている
  かつ: 引数1のプロパティ "status" に "integer" 型の "0" がセットされている
  かつ: モック1は "Application_Model_PlatformUserMapper" 型である
  かつ: モック1は "find" メソッドで空の配列を返却する
  もし: プラットフォームユーザ更新を実行する
  ならば "NotFound"の例外が発生すること
  """
  Common_Exception_NotFound
  """
