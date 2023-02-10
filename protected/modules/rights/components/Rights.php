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

	public static function getBaseUrl()
	{
		$module = self::module();
		return Yii::app()->createUrl($module->baseUrl);
	}

	public static function getAuthItemOptions()
	{
		return array(
			CAuthItem::TYPE_OPERATION => Rights::t('core', 'Operation', null, null, null),
			CAuthItem::TYPE_TASK => Rights::t('core', 'Task', null, null, null),
			CAuthItem::TYPE_ROLE => Rights::t('core', 'Role', null, null, null),
		);
	}

	public static function getAuthItemTypeName($type)
	{
		$options = self::getAuthItemOptions();
		if(isset($options[$type])===true)
			return $options[$type];
		else
			throw new CException(Rights::t('core', 'Invalid Authorization item type.', null, null, null));
	}

	public static function getAuthItemTypeNamePlural($type)
	{
		switch( (int)$type )
		{
			case CAuthItem::TYPE_OPERATION: return Rights::t('core', 'Operations', null, null, null);
			case CAuthItem::TYPE_TASK: return Rights::t('core', 'Tasks', null, null, null);
			case CAuthItem::TYPE_ROLE: return Rights::t('core', 'Roles', null, null, null);
			default: throw new CException(Rights::t('core', 'Invalid authorization item type.', null, null, null));
		}
	}

	public static function getAuthItemRoute($type)
	{
		switch( (int)$type )
		{
			case CAuthItem::TYPE_OPERATION: return array('authItem/operations');
			case CAuthItem::TYPE_TASK: return array('authItem/tasks');
			case CAuthItem::TYPE_ROLE: return array('authItem/roles');
			default: throw new CException(Rights::t('core', 'Invalid authorization item type.', null, null, null));
		}
	}

	public static function getValidChildTypes($type)
	{
	 	switch( (int)$type )
		{
			// Roles can consist of any type of authorization items
			case CAuthItem::TYPE_ROLE: return null;
			// Tasks can consist of other tasks and operations
			case CAuthItem::TYPE_TASK: return array(CAuthItem::TYPE_TASK, CAuthItem::TYPE_OPERATION);
			// Operations can consist of other operations
			case CAuthItem::TYPE_OPERATION: return array(CAuthItem::TYPE_OPERATION);
			// Invalid type
			default: throw new CException(Rights::t('core', 'Invalid authorization item type.', null, null, null));
		}
	}

	public static function getAuthItemSelectOptions($type=null, $exclude=array())
	{
		$authorizer = self::getAuthorizer();
		$items = $authorizer->getAuthItems($type, null, null, true, $exclude);
		return self::generateAuthItemSelectOptions($items, $type);
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

	public static function getAuthorizer()
	{
		if( isset(self::$_a)===false )
			self::$_a = self::module()->getAuthorizer();

		return self::$_a;
	}

	public static function t($category, $message, $params=array(), $source=null, $language)
	{
		return Yii::t('RightsModule'.$category, $message, $params, $source, $language);
	}
}