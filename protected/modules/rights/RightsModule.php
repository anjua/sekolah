<?php

class RightsModule extends CWebModule
{
    public $superuserName = 'Admin';
    public $authenticatedName = 'Authenticated';
    public $userClass = 'User';
    public $userIdColumn = 'id';
    public $userNameColumn = 'username';
    public $enableBizRule = true;
    public $enableBizRuleData = false;
    public $displayDescription = true;
    public $flashSuccessKey = 'RightSuccess';
    public $flashErrorKey = 'RightsError';
    public $install = false;
    public $baseUrl = '/rights';
    public $layout = 'rights.views.layouts.main';
    public $appLayout = 'application.views.layouts.main';
    public $cssFile;
    public $debug = false;
    // private $_assetsUrl;

    public function init()
    {

        $this->setImport(array(
			'rights.components.*',
			'rights.components.behaviors.*',
			'rights.components.dataproviders.*',
			'rights.controllers.*',
			'rights.models.*',
		));
        $this->setComponents(
            array(
                'authorizer' => array(
                    'class' => 'RAuthorizer',
                    'superuserName' => $this->superuserName,
                ),
                'generator' => array(
                    'class' => 'RGenerator',
                ),
            )
        );

        $this->defaultController = 'assignment';
    }

    public function getAuthorizer()
    {
        return $this->getComponent('authorizer');
    }

    public function getGenerator()
    {
        return $this->getComponent('generator');
    }
}