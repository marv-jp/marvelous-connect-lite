# language: ja
フィーチャ: readTokenForBasicメソッドのテスト


  シナリオ: アプリケーションIDと認可コードとリダイレクトURIが正しい状態で、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "code" には "認可コード" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModelBasicFeature" ）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" で1件ヒットが返ってくる処理がセットされる
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  もし "認可コードによるトークン取得"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "111" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに "1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20" が入っていること
  かつ "id_token" プロパティに "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vZGV2LW1pc3AubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDUxNTczOTk5IiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.qZu4EjtTEnUdgLlikkgBcttzkjB_-GJJb5iP8wb9C7k" が入っていること
  かつ "refresh_token" プロパティに "refresh_token" が入っていること
  かつ "authorization_code" プロパティがNULLであること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: アプリケーションIDと認可コードとリダイレクトURI(パラメータ付き)が正しい状態で、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "code" には "認可コード" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com?aoba=kyoshukudesu" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModelBasicFeature" ）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" で1件ヒットが返ってくる処理がセットされる
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  もし "認可コードによるトークン取得"が呼び出される
  ならば "Application_Model_UserPlatformApplicationRelation" モデルが返されること
  かつ "user_id" プロパティに "111" が入っていること
  かつ "platform_id" プロパティに "プラットフォームID" が入っていること
  かつ "platform_user_id" プロパティに "プラットフォームユーザID" が入っていること
  かつ "application_id" プロパティに "appID" が入っていること
  かつ "access_token" プロパティに "1953a7bbe447613bb1f9eca1ec3d8e290640e7ba0d07ac17277e307940993b20" が入っていること
  かつ "id_token" プロパティに "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJodHRwOi8vZGV2LW1pc3AubWFxbC1nYW1lcy5qcCIsImF1ZCI6Im1pc3AwMDAxIiwic3ViIjoiMTIyMDAwMDEiLCJleHAiOiIxNDUxNTczOTk5IiwiaWF0IjoiMTM4OTkzNzAwNyIsIm5vbmNlIjoiYjYxMzY3OWEwODE0ZDllYzc3MmY5NWQ3NzhjMzVmYzVmZjE2OTdjNDkzNzE1NjUzYzZjNzEyMTQ0MjkyYzVhZCIsImF0X2hhc2giOiI4b1hKa0hyNk5QcWpGYWU0by1OUDFnIn0.qZu4EjtTEnUdgLlikkgBcttzkjB_-GJJb5iP8wb9C7k" が入っていること
  かつ "refresh_token" プロパティに "refresh_token" が入っていること
  かつ "authorization_code" プロパティがNULLであること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "created_date" プロパティと "updated_date" プロパティに同じ値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: アプリケーションIDと認可コードが正しい、リダイレクトURLが不正な状態で、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "code" には "認可コード" がセットされている
  かつ プロパティ "redirect_uri" には "不正なURI" がセットされている
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" で1件ヒットが返ってくる処理がセットされる
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "false" が返ってくる処理がセットされる
  もし "認可コードによるトークン取得"が呼び出される
  ならば "OAuthリクエスト不正" の例外が返ってくること
  """
  Common_Exception_OauthInvalidRequest
  """

  シナリオ: アプリケーションIDとリダイレクトURIが正しい、認可コードが不正な状態で、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "code" には "不正な認可コード" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  もし "認可コードによるトークン取得"が呼び出される
  ならば "OAuth認可コード不正" の例外が返ってくること
  """
  Common_Exception_OauthInvalidGrant
  """

  シナリオ: アプリケーションIDとリダイレクトURIが正しい、認可コードが不正(有効期限切れ)な状態で、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "code" には "不正な認可コード" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModelBasic" ）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "isValidRedirectUri" でboolean型の "true" が返ってくる処理がセットされる
  もし "認可コードによるトークン取得"が呼び出される
  ならば "OAuth認可コード不正" の例外が返ってくること
  """
  Common_Exception_OauthInvalidGrant
  """

  シナリオ: client_idを空文字にして、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
#  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "code" には "不正な認可コード" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  もし "認可コードによるトークン取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: codeを空文字にして、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
#  かつ プロパティ "code" には "不正な認可コード" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  もし "認可コードによるトークン取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """


  シナリオ: redirect_uriを空文字にして、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "code" には "不正な認可コード" がセットされている
#  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  もし "認可コードによるトークン取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: client_idを11文字を超える文字列長にして、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" は 11 文字を超える文字列長の値になる
  かつ プロパティ "code" には "認可コード" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  もし "認可コードによるトークン取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: codeを64文字を超える文字列長にして、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "code" は 64 文字を超える文字列長の値になる
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  もし "認可コードによるトークン取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: redirect_uriを255文字を超える文字列長にして、"認可コードによるトークン取得"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "アプリケーションID" がセットされている
  かつ プロパティ "code" には "認可コード" がセットされている
  かつ プロパティ "redirect_uri" は 255 文字を超える文字列長の値になる
  もし "認可コードによるトークン取得"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
