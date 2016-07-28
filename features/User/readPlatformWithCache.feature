# language: ja
フィーチャ: readPlatformWithCacheメソッドのテスト

  シナリオ: 正常に"プラットフォーム情報取得（キャッシュ版）"が呼び出される
  前提 引数には "platformId" 文字列を使用する
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "readPlatform" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getPlatformModel" ）
  もし "プラットフォーム情報取得（キャッシュ版）"が呼び出される
  ならば "Application_Model_Platform" モデルが返されること

  シナリオ: プラットフォームIDが存在しないプラットフォームIDの状態で、"メインプラットフォーム確認"を実行
  前提 引数には "platformId" 文字列を使用する
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "readPlatform" で空の配列が返ってくる処理がセットされる
  もし "プラットフォーム情報取得（キャッシュ版）"が呼び出される
  ならば "取得対象が存在しません" の例外が返ってくること
  """
  Common_Exception_NotFound
  """
