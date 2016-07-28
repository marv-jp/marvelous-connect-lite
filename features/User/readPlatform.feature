# language: ja
フィーチャ: readPlatformのテスト

  シナリオ: 正常に"プラットフォーム取得"が呼び出される
  前提 引数には "Application_Model_Platform" モデルを使用する
  かつ モックとして "Application_Model_PlatformMapper" クラスを "platformMapper" として使用する
  かつ モック "platformMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformModel" ）
  もし "プラットフォーム取得"が呼び出される
  ならば 配列が返されること
  かつ 配列[0]の中身に "Application_Model_Platform" モデルが入っていること

  シナリオ: 正常に"プラットフォーム取得"が呼び出される
  前提 引数には "Application_Model_Platform" モデルを使用する
  かつ プロパティ "platform_id" には "platformID" がセットされている
  かつ モックとして "Application_Model_PlatformMapper" クラスを "platformMapper" として使用する
  かつ モック "platformMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformModel" ）
  もし "プラットフォーム取得"が呼び出される
  ならば 配列が返されること
  かつ 配列[0]の中身に "Application_Model_Platform" モデルが入っていること

  シナリオ: "platform_id"を191文字を超える文字列長にして、"プラットフォーム取得"が呼び出される
  前提 引数には "Application_Model_Platform" モデルを使用する
  かつ プロパティ "platform_id" は 191 文字を超える文字列長の値になる
  もし "プラットフォーム取得"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 存在しないプラットフォームに対して、"プラットフォーム取得"が呼び出される
  前提 引数には "Application_Model_Platform" モデルを使用する
  かつ プロパティ "platform_id" には "AppID" がセットされている
  かつ モックとして "Application_Model_PlatformMapper" クラスを "platformMapper" として使用する
  かつ モック "platformMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  もし "プラットフォーム取得"が呼び出される
  ならば "対象が存在しません"の例外が返ってくること
  """
  Common_Exception_NotFound
  """

