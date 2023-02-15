<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Aplikasi Sekolah',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.module.*',
		'ext.EDataTables.*',

		'application.modules.rights.*',
		'application.modules.rights.models.*',
		'application.modules.rights.components.*',
		'application.modules.rights.components.dataproviders.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
		'user'=>array(
			'tableUsers'=>'users',
			'tableProfiles'=>'profiles',
			'tableProfileFields'=>'profile_fields',
			'hash'=>'md5',
			'sendActivationMail'=>true,
			'loginNotActive'=>false,
			'activeAfterRegister'=>false,
			'autoLogin'=>true,
			'registrationUrl'=>array('/user/registration'),
			'recoveryUrl'=>array('/user/recovery'),
			'loginUrl'=>array('/user/login'),
			'returnLogoutUrl'=>array('/user/login'),
		),

		'rights'=>array(
			'superuserName'=>'Admin',
			'authenticatedName' => 'Authenticated',  // Name of the authenticated user role. 
			'userIdColumn' => 'id', // Name of the user id column in the database. 
			'userNameColumn' => 'username',  // Name of the user name column in the database. 
			'enableBizRule' => true,  // Whether to enable authorization item business rules. 
			'enableBizRuleData' => true,   // Whether to enable data for business rules. 
			'displayDescription' => true,  // Whether to use item description instead of name. 
			'flashSuccessKey' => 'RightsSuccess', // Key to use for setting success flash messages. 
			'flashErrorKey' => 'RightsError', // Key to use for setting error flash messages. 
			'baseUrl' => '/rights', // Base URL for Rights. Change if module is nested.  // Style sheet file to use for Rights. 
			'debug' => false,
		),
	),

	// application components
	'components'=>array(

		'user'=>array(
			'class'=>'RWebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'loginUrl'=>array('/user/login'),
		),

		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/

		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/database.php'),

		'authManager' => array(
			'class' => 'RDbAuthManager',
			'connectionID' => 'db',
			'itemTable' => 'authitem',
			'itemChildTable' => 'authitemchild',
			'assignmentTable' => 'authassignment',
			'rightsTable' => 'rights',
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>YII_DEBUG ? null : 'site/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'app_name' => 'Aplikasi Sekolah',
		'version' => '1.0',
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);
