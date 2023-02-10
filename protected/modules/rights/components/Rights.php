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

    public static function revoke($itemName, $userId)
    {
        $authorizer = self::getAuthorizer();
        return $authorizer->authManager->revoke($itemName, $userId);
    }

	public static function getAssignedRoles($userId=null, $sort=true)
	{
		$user = Yii::app()->getUser();
		if($userId===null && $user->isGuest===false)
			$userId = $user->id;
		
		$authorizer = self::getAuthorizer();
		return $authorizer->getAuthItems(CAuthItem::TYPE_ROLE, $userId, null, $sort);
	}

    public static function getAuthorizer()
	{
		if( isset(self::$_a)===false )
			self::$_a = self::module()->getAuthorizer();

		return self::$_a;
	}

    public static function module()
	{
		if( isset(self::$_m)===false )
			self::$_m = self::findModule();

		return self::$_m;
	}

    private static function findModule(CModule $module=null)
	{
		if( $module===null )
			$module = Yii::app();

		if( ($m = $module->getModule('rights'))!==null )
			return $m;

		foreach( $module->getModules() as $id=>$c )
			if( ($m = self::findModule( $module->getModule($id) ))!==null )
				return $m;

		return null;
	}
}