# language: ja
フィーチャ: createUserメソッドのテスト

  シナリオ: 正常に"ユーザ登録"が呼び出される
  前提 モックとして "Application_Model_UserMapper" クラスを "userMapper" として使用する
  かつ モック "userMapper" の "find" で空の配列が返ってくる処理がセットされる
  かつ モック "userMapper" の "insert" で1件ヒットが返ってくる処理がセットされる
  もし "ユーザ登録"が呼び出される
  ならば "Application_Model_User" モデルが返されること
  かつ "user_id" プロパティに "1" が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: 何らかの原因でUserへのinsertが失敗する状態で、"ユーザ登録"が呼び出される
  前提 モックとして "Application_Model_UserMapper" クラスを "userMapper" として使用する
  かつ モック "userMapper" の "find" で空の配列が返ってくる処理がセットされる
  かつ モック "userMapper" の "insert" で0件ヒットが返ってくる処理がセットされる
  もし "ユーザ登録"が呼び出される
  ならば "登録に失敗しました" の例外が返ってくること
  """
  Exception
  """
