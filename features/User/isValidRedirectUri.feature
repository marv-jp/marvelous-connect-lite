# language: ja
フィーチャ: isValidRedirectUriメソッドのテスト


  シナリオ: アプリケーションIDと認可コードとリダイレクトURIが正しい状態で、"リダイレクトURI検証"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "00000" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  かつ モックとして "Application_Model_ApplicationRedirectUriMapper" クラスを "applicationRedirectUriMapper" として使用する
  かつ モック "applicationRedirectUriMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationRedirectUriModel" ）
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModelBasicFeature" ）
  もし "リダイレクトURI検証"が呼び出される
  ならば: "boolean" 型の "true" が返却されること

  シナリオ: アプリケーションIDと認可コードとリダイレクトURI(パラメータ付き)が正しい状態で、"リダイレクトURI検証"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "00000" がセットされている
  かつ プロパティ "code" には "認可コード" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com?aoba=kyoshukudesu" がセットされている
  かつ モックとして "Application_Model_ApplicationRedirectUriMapper" クラスを "applicationRedirectUriMapper" として使用する
  かつ モック "applicationRedirectUriMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getApplicationRedirectUriModel" ）
  かつ モックとして "Application_Model_UserPlatformApplicationRelationMapper" クラスを "userPlatformApplicationRelationMapper" として使用する
  かつ モック "userPlatformApplicationRelationMapper" の "fetchAll" でモデルの配列が返ってくる処理がセットされる（モデルの中身は "getUserPlatformApplicationRelationModelBasicFeature" ）
  かつ モックとして "Logic_User" クラスを "logicUser" として使用する
  かつ モック "logicUser" の "updateUserPlatformApplicationRelation" で1件ヒットが返ってくる処理がセットされる
  もし "リダイレクトURI検証"が呼び出される
  ならば: "boolean" 型の "true" が返却されること

  シナリオ: リダイレクトURLが不正な状態で、"リダイレクトURI検証"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "00000" がセットされている
  かつ プロパティ "redirect_uri" には "不正なURI" がセットされている
  かつ モックとして "Application_Model_ApplicationRedirectUriMapper" クラスを "applicationRedirectUriMapper" として使用する
  かつ モック "applicationRedirectUriMapper" の "fetchAll" で空の配列が返ってくる処理がセットされる
  もし "リダイレクトURI検証"が呼び出される
  ならば: "boolean" 型の "false" が返却されること

  シナリオ: client_idを空文字にして、"リダイレクトURI検証"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
#  かつ プロパティ "client_id" には "00000" がセットされている
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  もし "リダイレクトURI検証"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: redirect_uriを空文字にして、"リダイレクトURI検証"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "00000" がセットされている
#  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  もし "リダイレクトURI検証"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: client_idを11文字を超える文字列長にして、"リダイレクトURI検証"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" は 11 文字を超える文字列長の値になる
  かつ プロパティ "redirect_uri" には "https://redirect.com" がセットされている
  もし "リダイレクトURI検証"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: redirect_uriを255文字を超える文字列長にして、"リダイレクトURI検証"を実行
  前提 引数には "Common_Oidc_Authorization_Authorization" モデルを使用する
  かつ プロパティ "client_id" には "00000" がセットされている
  かつ プロパティ "redirect_uri" は 255 文字を超える文字列長の値になる
  もし "リダイレクトURI検証"が呼び出される
  ならば "パラメータ不正" の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
