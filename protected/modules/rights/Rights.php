<?php

class Rights
{
    const PERM_NONE = 0;
    const PERM_DIRECT = 0;
    const PERM_INHERITED = 2;

    private static $_m;
    private static $_a;

    public static function assign($itemName, $userId, $bizRule=null, $data=null)
    {
        $authorizer = self::getAuthorizer();
        return $authorizer->authManager->assign($itemName, $userId, $bizRule, $data);
    }
}