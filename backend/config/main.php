<?php

$params = yii\helpers\ArrayHelper::merge(
    require( __DIR__ . '/../../common/config/params.php' ),
    require( __DIR__ . '/params.php' )
);

return [
    'id' => 'app-admin',
    'basePath' => dirname( __DIR__ ),
    'controllerNamespace' => 'admin\controllers',
    'defaultRoute' => 'core/site/index',
	'bootstrap' => [
		'log',
		'core', 'cms', 'forms', 'snsLogin', 'newsletter', 'notify',
		'foxSlider'
	],
    'modules' => [
        'core' => [
            'class' => 'cmsgears\core\admin\Module'
        ],
        'cms' => [
            'class' => 'cmsgears\cms\admin\Module'
        ],
		'forms' => [
            'class' => 'cmsgears\forms\admin\Module'
        ],
        'snslogin' => [
            'class' => 'cmsgears\social\login\admin\Module'
        ],
        'newsletter' => [
            'class' => 'cmsgears\newsletter\admin\Module'
        ],
        'notify' => [
            'class' => 'cmsgears\notify\admin\Module'
        ],
        'foxslider' => [
            'class' => 'foxslider\admin\Module'
        ]
    ],
    'components' => [
		'view' => [
			'theme' => [
				'class' => 'themes\admin\Theme',
				'childs' => [ 'themes\adminchild\Theme' ]
			]
		],
		'request' => [
			'csrfParam' => '_csrf-admin',
			'parsers' => [
				'application/json' => 'yii\web\JsonParser'
			]
		],
		'user' => [
			'identityCookie' => [ 'name' => '_identity-admin', 'httpOnly' => true ]
		],
		'session' => [
			'name' => 'blog-admin'
		],
		'errorHandler' => [
			'errorAction' => 'core/site/error'
		],
		'assetManager' => [
			'bundles' => require( __DIR__ . '/' . ( YII_ENV_PROD ? 'assets-prod.php' : 'assets-dev.php' ) )
		],
		'urlManager' => [
			'rules' => [
				// TODO: Check configurations to use sub domain rules
				// apix request rules - sub directory ----------
				// Core - 2 levels
				'<site:\w+>/apix/<controller:[\w\-]+>/<action:[\w\-]+>' => '<site>/core/apix/<controller>/<action>',
				// Generic - 3, 4 and 5 levels - catch all
				'<site:\w+>/apix/<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<site>/<module>/apix/<controller>/<action>',
				'<site:\w+>/apix/<module:\w+>/<pcontroller:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<site>/<module>/apix/<pcontroller>/<controller>/<action>',
				'<site:\w+>/apix/<module:\w+>/<pcontroller1:[\w\-]+>/<pcontroller2:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<site>/<module>/apix/<pcontroller1>/<pcontroller2>/<controller>/<action>',
				// apix request rules - sub domain -------------
				// Core - 2 levels
				'apix/<controller:[\w\-]+>/<action:[\w\-]+>' => 'core/apix/<controller>/<action>',
				// Generic - 3, 4 and 5 levels - catch all
				'apix/<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/apix/<controller>/<action>',
				'apix/<module:\w+>/<pcontroller:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/apix/<pcontroller>/<controller>/<action>',
				'apix/<module:\w+>/<pcontroller1:[\w\-]+>/<pcontroller2:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/apix/<pcontroller1>/<pcontroller2>/<controller>/<action>',
				// regular request rules - sub domain ----------
				// Core Module Pages
				'<site:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<site:\w+>/core/<controller>/<action>',
				// Module Pages - 2 and 3 levels - catch all
				'<site:\w+>/<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<site:\w+>/<module>/<controller>/<action>',
				'<site:\w+>/<module:\w+>/<pcontroller:[\w\-]+>/<controller:\w+>/<action:[\w\-]+>' => '<site:\w+>/<module>/<pcontroller>/<controller>/<action>',
				// Standard Pages
				'<site:\w+>/<action:(login|logout|dashboard|forgot-password|reset-password|activate-account)>' => '<site:\w+>/core/site/<action>',
				// regular request rules - sub directory -------
				// Core Module Pages
				'<controller:[\w\-]+>/<action:[\w\-]+>' => 'core/<controller>/<action>',
				// Module Pages - 2 and 3 levels - catch all
				'<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
				'<module:\w+>/<pcontroller:[\w\-]+>/<controller:\w+>/<action:[\w\-]+>' => '<module>/<pcontroller>/<controller>/<action>',
				// Standard Pages
				'<action:(login|logout|dashboard|forgot-password|reset-password|activate-account)>' => 'core/site/<action>',
				// additional rules ----------------------------
				'<site:\w+>' => '<site>/cmgcore/site/index',
	        	'<site:\w+><action:(/)>' => '<site>/cmgcore/site/index'
			]
		],
        'core' => [
        	'loginRedirectPage' => '/dashboard',
        	'logoutRedirectPage' => '/login'
        ],
        'sidebar' => [
        	'class' => 'cmsgears\core\admin\components\Sidebar',
        	'modules' => [ 'cms', 'foxslider', 'forms', 'core', 'notify', 'newsletter' ],
			'plugins' => [
				'socialMeta' => [ 'twitter-meta', 'facebook-meta' ],
				'fileManager' => [ 'file' ]
			]
        ],
        'dashboard' => [
        	'class' => 'cmsgears\core\admin\components\Dashboard',
        	'modules' => [ 'cms', 'core' ]
        ]
    ],
    'params' => $params
];