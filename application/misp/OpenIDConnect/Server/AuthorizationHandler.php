<?php

class OpenIDConnect_Server_AuthorizationHandler extends Akita_OpenIDConnect_Server_AuthorizationHandler
{

    /**
     * process Authorization Request
     *
     * @param Akita_OpenIDConnect_Server_DataHandler $dataHandler
     */
    public function processAuthorizationRequest($dataHandler, $allowed_response_type = array('code', 'id_token', 'token',
        'code id_token', 'code token', 'id_token token',
        'code id_token token'))
    {
        $request = $dataHandler->getRequest();

        $responseType = $request->openidConnectResponseType;

        if (empty($responseType)) {
            throw new Common_Exception_IllegalParameter(sprintf('Code : %s; Error : %s; Error Descripion : %s', '400', 'invalid_request', "'response_type' is required"), 400);
        }
        if (!in_array($responseType, $allowed_response_type)) {
            throw new Akita_OAuth2_Server_Error(
            '400', 'unsupported_response_type'
            );
        }
        // レスポンスタイプを渡しOpenIDConnectのプロファイルモードを決定する
        $dataHandler->setOpenIdConnectProfile($responseType);

        // validate client_id
        // クライアントIDはロジックでチェックするので、ここでは何もしないが他の処理するので定義はする
        $client_id = (isset($request->param['client_id'])) ? $request->param['client_id'] : "";

        // validate redirect_uri
        // リダイレクトURIの存在をチェック
        $redirect_uri = (isset($request->param['redirect_uri'])) ? $request->param['redirect_uri'] : "";

        if (empty($redirect_uri)) {
            throw new Common_Exception_IllegalParameter(sprintf('Code : %s; Error : %s; Error Descripion : %s', '400', 'invalid_request', "'redirect_uri' is required"), 400);
        }
        // リダイレクトURIの妥当性をチェック
        if (!$dataHandler->validateRedirectUri($client_id, $redirect_uri)) {
            throw new Common_Exception_OauthInvalidRequest(sprintf('Code : %s; Error : %s; Error Descripion : %s', '400', 'invalid_request', "'redirect_uri' is invalid"), 400);
        }

        // validate scope
        // 使用可能なスコープがセットされていることのチェック
        $scope = $request->openidConnectScope;
        if (!$dataHandler->validateScope($client_id, $scope)) {
            throw new Akita_OAuth2_Server_Error(
            '400', 'invalid_scope'
            );
        }

        // validate nonce
        $nonce = (isset($request->param['nonce'])) ? $request->param['nonce'] : "";
        //if(!$dataHandler->validateNonce($response_type, $nonce)){
        if (( $responseType != 'code' &&
                $responseType != 'token') && empty($nonce)) {
            throw new Common_Exception_IllegalParameter(sprintf('Code : %s; Error : %s; Error Descripion : %s', '400', 'nonce_required', ""), 400);
        }
    }

    /**
     * create AuthInfo and AccessToken and build response
     *
     * @param Akita_OpenIDConnect_Server_DataHandler $dataHandler
     */
    public function allowAuthorizationRequest($dataHandler)
    {
        throw new Common_Exception_NotSupported();
    }

}
