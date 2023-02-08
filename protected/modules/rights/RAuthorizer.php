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
}