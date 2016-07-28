# language: ja
フィーチャ: readPlatformUserのテスト

  シナリオ: 正常に"プラットフォームユーザ取得"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_user_id" には "PlatformUserID" がセットされている
  かつ プロパティ "platform_id" には "PlatformID" がセットされている
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  もし "プラットフォームユーザ取得"が呼び出される
  ならば "Application_Model_PlatformUser" モデルが返されること

  シナリオ: "platform_id"を191文字を超える文字列長にして、"プラットフォームユーザ取得"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_user_id" には "PlatformUserID" がセットされている
  かつ プロパティ "platform_id" は 191 文字を超える文字列長の値になる
  もし "プラットフォームユーザ取得"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"を255文字を超える文字列長にして、"プラットフォームユーザ取得"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_user_id" は 255 文字を超える文字列長の値になる
  かつ プロパティ "platform_id" には "PlatformID" がセットされている
  もし "プラットフォームユーザ取得"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 存在しないプラットフォームユーザに対して、"プラットフォームユーザ取得"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_user_id" には "存在しないプラットフォームユーザID" がセットされている
  かつ プロパティ "platform_id" には "PlatformID" がセットされている
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  もし "プラットフォームユーザ取得"が呼び出される
  ならば "対象が存在しません"の例外が返ってくること
  """
  Common_Exception_NotFound
  """

