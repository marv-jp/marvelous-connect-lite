# language: ja
フィーチャ: createPlatformUserメソッドのテスト

  シナリオ: 正常に"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ モックとして "Application_Model_PlatformMapper" クラスを "platformMapper" として使用する
  かつ モック "platformMapper" の "find" でモデル "Application_Model_Platform" が返ってくる処理がセットされる（モデルの中身は "getPlatformModel" ）
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" で空の配列が返ってくる処理がセットされる
  かつ モック "platformUserMapper" の "insert" で1件ヒットが返ってくる処理がセットされる
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "Application_Model_PlatformUser" モデルが返されること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "access_token" プロパティに "アクセストークン" が入っていること
  かつ "id_token" プロパティに "IDトークン" が入っていること
  かつ "status" プロパティに 1 が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: 存在しないプラットフォームIDで、"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "存在しないプラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ モックとして "Application_Model_PlatformMapper" クラスを "platformMapper" として使用する
  かつ モック "platformMapper" の "find" で空の配列が返ってくる処理がセットされる
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "プラットフォームが存在しません" の例外が返ってくること
  """
  Common_Exception_NotFound
  """

  シナリオ: 何らかの原因でPlatformUserへのinsertが失敗する状態で、"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ モックとして "Application_Model_PlatformMapper" クラスを "platformMapper" として使用する
  かつ モック "platformMapper" の "find" でモデル "Application_Model_Platform" が返ってくる処理がセットされる（モデルの中身は "getPlatformModel" ）
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "insert" で0件ヒットが返ってくる処理がセットされる
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "登録に失敗しました" の例外が返ってくること
  """
  Exception
  """

  シナリオ: "platform_id"が空で、"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" は空である
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"が空で、"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" は空である
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_id"を191文字を超える文字列長にして、"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" は 191 文字を超える文字列長の値になる
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_user_id"を255文字を超える文字列長にして、"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" は 255 文字を超える文字列長の値になる
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "access_token"を65535文字を超える文字列長にして、"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" は 65535 文字を超える文字列長の値になる
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"を65535文字を超える文字列長にして、"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は 65535 文字を超える文字列長の値になる
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "platform_id"と"platform_user_id"が既存のもので、"プラットフォームユーザ登録"が呼び出される
  前提 引数には "Application_Model_PlatformUser" モデルを使用する
  かつ プロパティ "platform_id" には "既存プラットフォームID" がセットされている
  かつ プロパティ "platform_user_id" には "既存プラットフォームユーザID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ モックとして "Application_Model_PlatformMapper" クラスを "platformMapper" として使用する
  かつ モック "platformMapper" の "find" でモデル "Application_Model_Platform" が返ってくる処理がセットされる（モデルの中身は "getPlatformModel" ）
  かつ モックとして "Application_Model_PlatformUserMapper" クラスを "platformUserMapper" として使用する
  かつ モック "platformUserMapper" の "find" でモデル "Application_Model_PlatformUser" が返ってくる処理がセットされる（モデルの中身は "getPlatformUserModel" ）
  もし "プラットフォームユーザ登録"が呼び出される
  ならば "既に登録対象が存在しています" の例外が返ってくること
  """
  Common_Exception_AlreadyExists
  """
