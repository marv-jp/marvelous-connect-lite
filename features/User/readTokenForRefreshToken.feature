# language: ja
フィーチャ: readTokenForRefreshTokenメソッドのテスト


  シナリオ: アプリケーションIDとリフレッシュトークンが正しい状態で、"リフレッシュトークンによるトークン再取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "refresh_token" には "リフレッシュトークン" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModelBasicFeature" ）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" で1件ヒットが返ってくる処理がセットされる
  かつ モックとして "Logic_Application" クラスを "logicApplication" として使用する
  かつ モック "logicApplication" の "readApplication" でモデル "Application_Model_User" が返ってくる処理がセットされる（モデルの中身は "getApplicationModel" ）
  もし "リフレッシュトークンによるトークン再取得"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "111" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに値が入っていること
  かつ "id_token" プロパティに値が入っていること
  かつ "refresh_token" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: アプリケーションIDが正しく、リフレッシュトークンが不正な状態で、"リフレッシュトークンによるトークン再取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "refresh_token" には "存在しないリフレッシュトークン" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  もし "リフレッシュトークンによるトークン再取得"が呼び出される
  ならば "OAuthリクエスト不正" の例外が返ってくること
  """
  Common_Exception_OauthInvalidGrant
  """

  シナリオ: アプリケーションIDが不正で、リフレッシュトークンが正しい状態で、"リフレッシュトークンによるトークン再取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "refresh_token" には "不正なリフレッシュトークン" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModelBasicFeature" ）
  かつ モックとして "Logic_Application" クラスを "logicApplication" として使用する
  かつ モック "logicApplication" の "readApplication" で例外 "Common_Exception_NotFound" が投げられる処理がセットされる
  もし "リフレッシュトークンによるトークン再取得"が呼び出される
  ならば "取得対象が存在しません" の例外が返ってくること
  """
  Common_Exception_NotFound
  """

  シナリオ: client_idを空文字にして、"リフレッシュトークンによるトークン再取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
#  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "refresh_token" には "リフレッシュトークン" がセットされている
  もし "リフレッシュトークンによるトークン再取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: refresh_tokenを空文字にして、"リフレッシュトークンによるトークン再取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
#  かつ プロパティ "refresh_token" には "リフレッシュトークン" がセットされている
  もし "リフレッシュトークンによるトークン再取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: client_idを11文字を超える文字列長にして、"リフレッシュトークンによるトークン再取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" は 11 文字を超える文字列長の値になる
  かつ プロパティ "refresh_token" には "リフレッシュトークン" がセットされている
  もし "リフレッシュトークンによるトークン再取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: refresh_tokenを255文字を超える文字列長にして、"リフレッシュトークンによるトークン再取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "refresh_token" は 255 文字を超える文字列長の値になる
  もし "リフレッシュトークンによるトークン再取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
