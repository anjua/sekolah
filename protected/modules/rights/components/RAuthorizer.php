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
}