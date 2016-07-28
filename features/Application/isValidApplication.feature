# language: ja
フィーチャ: isValidApplicationメソッドのテスト

  シナリオ: "application_id"と"application_secret"の組み合わせがあっている場合、"アプリケーション検証"を呼び出す
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ モック"Application_Model_ApplicationMapper->fetchAll"で正常値が返ってくる処理がセットされる
  もし "アプリケーション検証"が呼び出される
  ならば "true"の結果が返ってくること
 
  シナリオ: "application_id"と"application_secret"の組み合わせが間違っている場合、"アプリケーション検証"を呼び出す
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "間違ったアプリケーション秘密鍵" がセットされている
  かつ モック"Application_Model_ApplicationMapper->fetchAll"で異常値が返ってくる処理がセットされる
  もし "アプリケーション検証"が呼び出される
  ならば "認証失敗"の例外が返ってくること
  """
  Common_Exception_OauthInvalidClient
  """

  シナリオ: "application_id"が空の場合、"アプリケーション検証"を呼び出す
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  もし "アプリケーション検証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"を11文字を超える文字列長にして、"アプリケーション検証"を呼び出す
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" は 11 文字を超える文字列長の値になる
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  もし "アプリケーション検証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_secret"を255文字を超える文字列長にして、"アプリケーション検証"を呼び出す
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" は 255 文字を超える文字列長の値になる
  もし "アプリケーション検証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
