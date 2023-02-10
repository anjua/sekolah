<?php

class RAuthorizer extends CApplicationComponent
{
    public $superuserName;
    public $_authManager;

    public function init()
    {
        parent::init();
        $this->_authManager = Yii::app()->getAuthManager();
    }

    public function getRoles($includeSuperuser=true, $sort=true)
    {
        $exclude = $includeSuperuser===false ? array($this->superuserName) : array();
        $roles = $this->getAuthItems(CAuthItem::TYPE_ROLE, null, null, $sort, $exclude);
        $roles = $this->attachAuthItemBehavior($roles);
        return $roles;
    }

    public function createAuthItem($name, $type, $description='', $bizRule=null, $data=null)
	{
		$bizRule = $bizRule!=='' ? $bizRule : null;

		if( $data!==null )
			$data = $data!=='' ? $this->sanitizeExpression($data.';') : null;

		return $this->_authManager->createAuthItem($name, $type, $description, $bizRule, $data);
	}

    public function updateAuthItem($oldName, $name, $description='', $bizRule=null, $data=null)
	{
		$authItem = $this->_authManager->getAuthItem($oldName);
		$authItem->name = $name;
		$authItem->description = $description!=='' ? $description : null;
		$authItem->bizRule = $bizRule!=='' ? $bizRule : null;

		// Make sure that data is not already serialized.
		if( @unserialize($data)===false )
			$authItem->data = $data!=='' ? $this->sanitizeExpression($data.';') : null;

		$this->_authManager->saveAuthItem($authItem, $oldName);
	}

    public function getAuthItems($types=null, $userId=null, CAuthItem $parent=null, $sort=true, $exclude=array())
	{
		// We have none or a single type.
		if( $types!==(array)$types )
		{
			$items = $this->_authManager->getAuthItems($types, $userId, $sort);
		}
		// We have multiple types.
		else
		{
			$typeItemList = array();
			foreach( $types as $type )
				$typeItemList[ $type ] = $this->_authManager->getAuthItems($type, $userId, $sort);

			// Merge the authorization items preserving the keys.
			$items = array();
			foreach( $typeItemList as $typeItems )
				$items = $this->mergeAuthItems($items, $typeItems);
		}

		$items = $this->excludeInvalidAuthItems($items, $parent, $exclude);
		$items = $this->attachAuthItemBehavior($items, $userId, $parent);

		return $items;
	}

    protected function mergeAuthItems($array1, $array2)
	{
		foreach( $array2 as $itemName=>$item )
			if( isset($array1[ $itemName ])===false )
				$array1[ $itemName ] = $item;

		return $array1;
	}

    protected function excludeInvalidAuthItems($items, CAuthItem $parent=null, $exclude=array())
	{
		// We are getting authorization items valid for a certain item
		// exclude its parents and children aswell.
		if( $parent!==null )
		{
		 	$exclude[] = $parent->name;
		 	foreach( $parent->getChildren() as $childName=>$child )
		 		$exclude[] = $childName;

		 	// Exclude the parents recursively to avoid inheritance loops.
		 	$parentNames = array_keys($this->getAuthItemParents($parent->name));
		 	$exclude = array_merge($parentNames, $exclude);
		}

		// Unset the items that are supposed to be excluded.
		foreach( $exclude as $itemName )
			if( isset($items[ $itemName ]) )
				unset($items[ $itemName ]);

		return $items;
	}

    public function getAuthItemParents($item, $type=null, $parentName=null, $direct=false)
	{
		if( ($item instanceof CAuthItem)===false )
			$item = $this->_authManager->getAuthItem($item);

		$permissions = $this->getPermissions($parentName);
		$parentNames = $this->getAuthItemParentsRecursive($item->name, $permissions, $direct);
		$parents = $this->_authManager->getAuthItemsByNames($parentNames);
		$parents = $this->attachAuthItemBehavior($parents, null, $item);

		if( $type!==null )
			foreach( $parents as $parentName=>$parent )
				if( (int)$parent->type!==$type )
					unset($parents[ $parentName ]);

		return $parents;
	}

    private function getAuthItemParentsRecursive($itemName, $items, $direct)
	{
		$parents = array();
		foreach( $items as $childName=>$children )
		{
		 	if( $children!==array() )
		 	{
		 		if( isset($children[ $itemName ]) )
		 		{
		 			if( isset($parents[ $childName ])===false )
		 				$parents[ $childName ] = $childName;
				}
				else
				{
		 			if( ($p = $this->getAuthItemParentsRecursive($itemName, $children, $direct))!==array() )
		 			{
		 				if( $direct===false && isset($parents[ $childName ])===false )
		 					$parents[ $childName ] = $childName;

		 				$parents = array_merge($parents, $p);
					}
				}
			}
		}

		return $parents;
	}

    public function getAuthItemChildren($item, $type=null)
	{
		if( ($item instanceof CAuthItem)===false )
			$item = $this->_authManager->getAuthItem($item);

		$childrenNames = array();
		foreach( $item->getChildren() as $childName=>$child )
			if( $type===null || (int)$child->type===$type )
				$childrenNames[] = $childName;

		$children = $this->_authManager->getAuthItemsByNames($childrenNames);
		$children = $this->attachAuthItemBehavior($children, null, $item);

		return $children;
	}

    public function attachAuthItemBehavior($items, $userId=null, CAuthItem $parent=null)
	{
		// We have a single item.
		if( $items instanceof CAuthItem )
		{
			$items->attachBehavior('rights', new RAuthItemBehavior($userId, $parent));
		}
		// We have multiple items.
		else if( $items===(array)$items )
		{
			foreach( $items as $item )
				$item->attachBehavior('rights', new RAuthItemBehavior($userId, $parent));
		}

		return $items;
	}

    public function getSuperusers()
	{
		$assignments = $this->_authManager->getAssignmentsByItemName( Rights::module()->superuserName );

		$userIdList = array();
		foreach( $assignments as $userId=>$assignment )
			$userIdList[] = $userId;

		$criteria = new CDbCriteria();
		$criteria->addInCondition(Rights::module()->userIdColumn, $userIdList);

		$userClass = Rights::module()->userClass;
		$users = CActiveRecord::model($userClass)->findAll($criteria);
		$users = $this->attachUserBehavior($users);

		$superusers = array();
		foreach( $users as $user )
			$superusers[] = $user->name;

		// Make sure that we have superusers, otherwise we would allow full access to Rights
		// if there for some reason is not any superusers.
		if( $superusers===array() )
			throw new CHttpException(403, Rights::t('core', 'There must be at least one superuser!', null, null, null));

		return $superusers;
	}

    public function attachUserBehavior($users)
	{
		$userClass = Rights::module()->userClass;

		// We have a single user.
		if( $users instanceof $userClass )
		{
			$users->attachBehavior('rights', new RUserBehavior);
		}
		// We have multiple user.
		else if( $users===(array)$users )
		{
			foreach( $users as $user )
				$user->attachBehavior('rights', new RUserBehavior);
		}

		return $users;
	}

    public function isSuperuser($userId)
	{
		$assignments = $this->_authManager->getAuthAssignments($userId);
		return isset($assignments[ $this->superuserName ]);
	}

    public function getPermissions($itemName=null)
	{
		$permissions = array();

		if( $itemName!==null )
		{
			$item = $this->_authManager->getAuthItem($itemName);
			$permissions = $this->getPermissionsRecursive($item);
		}
		else
		{
			foreach( $this->getRoles() as $roleName=>$role )
				$permissions[ $roleName ] = $this->getPermissionsRecursive($role);
		}

		return $permissions;
	}

    private function getPermissionsRecursive(CAuthItem $item)
	{
		$permissions = array();
	 	foreach( $item->getChildren() as $childName=>$child )
	 	{
	 		$permissions[ $childName ] = array();
	 		if( ($grandChildren = $this->getPermissionsRecursive($child))!==array() )
				$permissions[ $childName ] = $grandChildren;
		}

		return $permissions;
	}

    public function hasPermission($itemName, $parentName=null, $permissions=array())
	{
		if( $parentName!==null )
		{
			if( $parentName===$this->superuserName )
				return 1;

			$permissions = $this->getPermissions($parentName);
		}

		if( isset($permissions[ $itemName ]) )
			return 1;

		foreach( $permissions as $children )
			if( $children!==array() )
				if( $this->hasPermission($itemName, null, $children)>0 )
					return 2;

		return 0;
	}

    protected function sanitizeExpression($code)
	{
		// Language consturcts.
		$languageConstructs = array(
			'echo',
			'empty',
			'isset',
			'unset',
			'exit',
			'die',
			'include',
			'include_once',
			'require',
			'require_once',
		);

		// Loop through the language constructs.
		foreach( $languageConstructs as $lc )
			if( preg_match('/'.$lc.'\ *\(?\ *[\"\']+/', $code)>0 )
				return null; // Language construct found, not safe for eval.

		// Get a list of all defined functions
		$definedFunctions = get_defined_functions();
		$functions = array_merge($definedFunctions['internal'], $definedFunctions['user']);

		// Loop through the functions and check the code for function calls.
		// Append a '(' to the functions to avoid confusion between e.g. array() and array_merge().
		foreach( $functions as $f )
			if( preg_match('/'.$f.'\ *\({1}/', $code)>0 )
				return null; // Function call found, not safe for eval.

		// Evaluate the safer code
		$result = @eval($code);

		// Return the evaluated code or null if the result was false.
		return $result!==false ? $result : null;
	}

    public function getAuthManager()
	{
		return $this->_authManager;
	}
}