# language: ja
フィーチャ: Logic_User#updateUserメソッドのテスト

  シナリオ: 正常系：ユーザを未登録にする
  前提: 引数1は "Application_Model_User" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "ユーザID" がセットされている
  かつ: 引数1のプロパティ "status" に "integer" 型の "0" がセットされている
  かつ: モック1は "Application_Model_UserMapper" 型である
  かつ: モック1は "find" メソッドで "Application_Model_User" クラスのオブジェクトを返却する
  かつ: モック1は "update" メソッドで "integer" 型の "1" を返却する
  もし: ユーザ更新を実行する
  ならば: "Application_Model_User" 型のオブジェクトが返却されること

  シナリオ: 異常系：パラメータ不正：ユーザIDが未セットの場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_User" 型の "オブジェクト" である
#  かつ: 引数1のプロパティ "user_id" に "integer" 型の "ユーザID" がセットされている
  かつ: 引数1のプロパティ "status" に "integer" 型の "0" がセットされている
  もし: ユーザ更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：パラメータ不正：ステータスが許可されていない値の場合、IllegalParameterの例外が発生する
  前提: 引数1は "Application_Model_User" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "ユーザID" がセットされている
  かつ: 引数1のプロパティ "status" に "integer" 型の "1254" がセットされている
  もし: ユーザ更新を実行する
  ならば "パラメータ不正"の例外が発生すること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 異常系：処理対象ナシ：処理対象が存在しない場合、NotFoundの例外が発生する
  前提: 引数1は "Application_Model_User" 型の "オブジェクト" である
  かつ: 引数1のプロパティ "user_id" に "integer" 型の "ユーザID" がセットされている
  かつ: 引数1のプロパティ "status" に "integer" 型の "ステータス" がセットされている
  かつ: モック1は "Application_Model_UserMapper" 型である
  かつ: モック1は "find" メソッドで空の配列を返却する
  もし: ユーザ更新を実行する
  ならば "NotFound"の例外が発生すること
  """
  Common_Exception_NotFound
  """
