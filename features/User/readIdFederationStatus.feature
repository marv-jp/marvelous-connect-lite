# language: ja
フィーチャ: readIdFederationStatusのテスト

  シナリオ: 正常に"ID連携状態確認処理"が呼び出され、結果が一つ返ってくる
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAllReadIdFederationStatus" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "readUserPlatformApplicationRelationWithValidate" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  もし "ID連携状態確認処理"が呼び出される
  ならば 配列が返されること
  かつ 配列[0]の中身に "Application_Model_UserPlatformApplicationRelation" モデルが入っていること
  かつ 配列[0]内モデルの "user_id" プロパティに "111" が入っていること
  かつ 配列[0]内モデルの "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ 配列[0]内モデルの "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ 配列[0]内モデルの "application_id" プロパティに "appID" が入っていること
  かつ 配列[0]内モデルの "access_token" プロパティに値が入っていること
  かつ 配列[0]内モデルの "id_token" プロパティに値が入っていること
  かつ 配列[0]内モデルの "created_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ 配列[0]内モデルの "updated_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ 配列[0]内モデルの "deleted_date" プロパティがNULLであること

  シナリオ: 正常に"ID連携状態確認処理"が呼び出され、結果が2つつ返ってくる
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAllReadIdFederationStatus" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getArrayMultiUserPlatformApplicationRelationModel" 配列の長さは2）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "readUserPlatformApplicationRelationWithValidate" でモデル "Application_Model_UserPlatformApplicationRelation" が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModel" ）
  もし "ID連携状態確認処理"が呼び出される
  ならば 配列が返されること
  かつ 配列[0]の中身に "Application_Model_UserPlatformApplicationRelation" モデルが入っていること
  かつ 配列[0]内モデルの "user_id" プロパティに "ユーザID_0" が入っていること
  かつ 配列[0]内モデルの "platform_user_id" プロパティに "プラットフォームユーザID_0" が入っていること
  かつ 配列[0]内モデルの "platform_id" プロパティに "プラットフォームID_0" が入っていること
  かつ 配列[0]内モデルの "application_id" プロパティに "appID_0" が入っていること
  かつ 配列[0]内モデルの "access_token" プロパティに値が入っていること
  かつ 配列[0]内モデルの "id_token" プロパティに値が入っていること
  かつ 配列[0]内モデルの "created_date" プロパティに "2013-11-11 11:11:00" が入っていること
  かつ 配列[0]内モデルの "updated_date" プロパティに "2013-11-11 11:11:00" が入っていること
  かつ 配列[0]内モデルの "deleted_date" プロパティがNULLであること
  かつ 配列[1]の中身に "Application_Model_UserPlatformApplicationRelation" モデルが入っていること
  かつ 配列[1]内モデルの "user_id" プロパティに "ユーザID_1" が入っていること
  かつ 配列[1]内モデルの "platform_user_id" プロパティに "プラットフォームユーザID_1" が入っていること
  かつ 配列[1]内モデルの "platform_id" プロパティに "プラットフォームID_1" が入っていること
  かつ 配列[1]内モデルの "application_id" プロパティに "appID_1" が入っていること
  かつ 配列[1]内モデルの "access_token" プロパティに値が入っていること
  かつ 配列[1]内モデルの "id_token" プロパティに値が入っていること
  かつ 配列[1]内モデルの "created_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ 配列[1]内モデルの "updated_date" プロパティに "2013-11-11 11:11:11" が入っていること
  かつ 配列[1]内モデルの "deleted_date" プロパティがNULLであること

  シナリオ: "application_id"が空で、"ID連携状態確認処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "application_id" は空である
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "ID連携状態確認処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "access_token"が空で、"ID連携状態確認処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" は空である
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "ID連携状態確認処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"が空で、"ID連携状態確認処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は空である
  もし "ID連携状態確認処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_id"を11文字を超える文字列長にして、"ID連携状態確認処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "application_id" は 11 文字を超える文字列長の値になる
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "ID連携状態確認処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "access_token"を255文字を超える文字列長にして、"ID連携状態確認処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" は 255 文字を超える文字列長の値になる
  かつ プロパティ "id_token" には "IDトークン" がセットされている
  もし "ID連携状態確認処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "id_token"を65535文字を超える文字列長にして、"ID連携状態確認処理"が呼び出される
  前提 引数には "Application_Model_UserPlatformApplicationRelation" モデルを使用する
  かつ プロパティ "application_id" には "appID" がセットされている
  かつ プロパティ "access_token" には "アクセストークン" がセットされている
  かつ プロパティ "id_token" は 65535 文字を超える文字列長の値になる
  もし "ID連携状態確認処理"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
