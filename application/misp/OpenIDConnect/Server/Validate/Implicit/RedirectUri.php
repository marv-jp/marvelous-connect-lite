<?php

class OpenIDConnect_Server_Validate_Implicit_RedirectUri extends Common_Validate_Abstract
{

    public function isValid($value)
    {

        // パラメータチェック
        if (!array_key_exists('client_id', $value) || !array_key_exists('redirect_uri', $value)) {
            return false;
        }

        // パラメータ取得
        $clientId    = $value['client_id'];
        $redirectUri = $value['redirect_uri'];

        // リダイレクトURIの検証
        // 検証用のパラメータ準備
        $authorization = new Common_Oidc_Authorization_Authorization();
        $authorization->setClientId($clientId);
        $authorization->setRedirectUri($redirectUri);

        $logicUser = new Logic_User();

        if (!$logicUser->isValidRedirectUri($authorization)) {
            return false;
        }

        return true;
    }

}
