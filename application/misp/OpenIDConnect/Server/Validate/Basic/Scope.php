<?php

class OpenIDConnect_Server_Validate_Basic_Scope extends Common_Validate_Abstract
{

    public function isValid($value)
    {
        if (empty($value)) {
            return false;
        }
        if (!in_array('openid', $value)) {
            return false;
        }
        foreach ($value as $scope) {
            if (!in_array($scope, array('openid', 'profile'))) {
                return false;
            }
        }
        return true;
    }

}
