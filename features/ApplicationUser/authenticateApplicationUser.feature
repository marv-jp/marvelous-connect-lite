# language: ja
フィーチャ: authenticateApplicationUserのテスト

  シナリオ: 正常に"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  かつ 引数4つ目には連想配列を使用する 
  かつ モック"Application_Model_ApplicationUserMapper->fetchAll"で正常値が返ってくる処理がセットされる
  かつ モック"ApplicationUser->updateApplicationUser"で正常値が返ってくる処理がセットされる
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "Application_Model_ApplicationUser" モデルが返されること
  かつ "application_id" プロパティに "mainAppID" が入っていること
  かつ "application_user_id" プロパティに "一意のアプリケーションユーザID" が入っていること
  かつ "password" プロパティに "正しいパスワード" が入っていること
  かつ "access_token" プロパティに値が入っていること
  かつ "id_token" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "deleted_date" プロパティがNULLであること
  
  シナリオ: 正常に"アプリケーションユーザ認証(アプリケーショニューザアクセスログ出力を伴う)"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  かつ 引数4つ目には連想配列を使用する
  かつ 引数4つ目の連想配列のキー "maxAge" の値は "123456" がセットされている
  かつ 引数4つ目の連想配列のキー "platformId" の値は "ios" がセットされている
  かつ モック"Application_Model_ApplicationUserMapper->fetchAll"で正常値が返ってくる処理がセットされる
  かつ モック"ApplicationUser->updateApplicationUser"で正常値が返ってくる処理がセットされる
  かつ モックとして "Application_Model_ApplicationUserAccessLogMapper" クラスを "applicationUserAccessLogMapper" として使用する
  かつ モック "applicationUserAccessLogMapper" の "insert" でインサートID 1000 が返ってくる処理がセットされる
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "Application_Model_ApplicationUser" モデルが返されること
  かつ "application_id" プロパティに "mainAppID" が入っていること
  かつ "application_user_id" プロパティに "一意のアプリケーションユーザID" が入っていること
  かつ "password" プロパティに "正しいパスワード" が入っていること
  かつ "access_token" プロパティに値が入っていること
  かつ "id_token" プロパティに値が入っていること
  かつ "created_date" プロパティに値が入っていること
  かつ "updated_date" プロパティに値が入っていること
  かつ "deleted_date" プロパティがNULLであること

  シナリオ: "IDトークン発行"でエラーが出る状態で、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_user_id"が空で、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "password"が空で、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "application_user_id"を255文字を超える文字列長にして、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" は 255 文字を超える文字列長の値になる
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "password"を255文字を超える文字列長にして、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" は 255 文字を超える文字列長の値になる
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "password"が間違ったもので、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "間違ったパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  かつ モック"Application_Model_ApplicationUserMapper->fetchAll"で異常値が返ってくる処理がセットされる
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "認証失敗"の例外が返ってくること
  """
  Common_Exception_AuthenticationFailed
  """

  シナリオ: "sub"が空で、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

シナリオ: "exp"が空で、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

シナリオ: "iat"が空で、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "sub"を255文字を超える文字列長にして、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" は 255 文字を超える文字列長の値になる
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "nonce"を255文字を超える文字列長にして、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" は 255 文字を超える文字列長の値になる
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "exp"を255文字を超える文字列長にして、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" は 255 文字を超える文字列長の値になる
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "ita"を255文字を超える文字列長にして、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" は 255 文字を超える文字列長の値になる
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "Application_Model_Application"、"Common_Oidc_IdToken_Payload"の"application_id"、"aud"が異なる状態で、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "サブアプリケーションID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """

  シナリオ: "Application_Model_ApplicationUser"、"Common_Oidc_IdToken_Payload"の"application_user_id"、"sub"が異なる状態で、"アプリケーションユーザ認証"が呼び出される
  前提 引数には "Application_Model_Application" モデルを使用する
  かつ プロパティ "application_id" には "mainAppID" がセットされている
  かつ プロパティ "application_secret" には "アプリケーション秘密鍵" がセットされている
  かつ 引数２つ目には "Application_Model_ApplicationUser" モデルを使用する
  かつ 引数２つ目のプロパティ "application_id" には "mainAppID" がセットされている
  かつ 引数２つ目のプロパティ "application_user_id" には "一意のアプリケーションユーザID" がセットされている
  かつ 引数２つ目のプロパティ "password" には "正しいパスワード" がセットされている
  かつ 引数３つ目には "Common_Oidc_IdToken_Payload" モデルを使用する
  かつ 引数３つ目のプロパティ "aud" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "sub" には "アプリケーションユーザIDとは異なるユーザID" がセットされている
  かつ 引数３つ目のプロパティ "nonce" には "mainAppID" がセットされている
  かつ 引数３つ目のプロパティ "exp" には "123457789" がセットされている
  かつ 引数３つ目のプロパティ "iat" には "123456789" がセットされている
  もし "アプリケーションユーザ認証"が呼び出される
  ならば "パラメータ不正"の例外が返ってくること
  """
  Common_Exception_IllegalParameter
  """
