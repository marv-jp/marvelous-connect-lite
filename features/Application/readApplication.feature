# language: ja
フィーチャ: readApplicationのテスト

  シナリオ: 正常に"アプリケーション取得"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  もし "アプリケーション取得"が呼び出される
  ならば "Application_Model_Application" モデルが返されること
  かつ "application_id" プロパティに値が入っていること
  かつ "developer_id" プロパティに値が入っていること
  かつ "application_name" プロパティに値が入っていること
  かつ "application_secret" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: 正常に"アプリケーション取得"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "developer_id" には "devID" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  もし "アプリケーション取得"が呼び出される
  ならば "Application_Model_Application" モデルが返されること
  かつ "application_id" プロパティに値が入っていること
  かつ "developer_id" プロパティに値が入っていること
  かつ "application_name" プロパティに値が入っていること
  かつ "application_secret" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: "application_id"が空で、"アプリケーション取得"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
#  かつ プロパティ "application_id" には "mainAppID" がセットされている
  もし "アプリケーション取得"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"を11文字を超える文字列長にして、"アプリケーション取得"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" は 11 文字を超える文字列長の値になる
  かつ プロパティ "developer_id" には "devID" がセットされている
  もし "アプリケーション取得"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "developer_id"を255文字を超える文字列長にして、"アプリケーション取得"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "developer_id" は 255 文字を超える文字列長の値になる
  もし "アプリケーション取得"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: 存在しないアプリケーションに対して、"アプリケーション取得"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  もし "アプリケーション取得"が呼び出される
  ならば "対象が存在しません"の例外が返ってくること
  """
  Common_Exception_NotFound
  """

  シナリオ: 存在しないアプリケーションに対して、"アプリケーション取得"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "developer_id" には "devID" がセットされている
  かつ モックとして "Application_Model_ApplicationMapper" クラスを "applicationMapper" として使用する
  かつ モック "applicationMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  もし "アプリケーション取得"が呼び出される
  ならば "対象が存在しません"の例外が返ってくること
  """
  Common_Exception_NotFound
  """
