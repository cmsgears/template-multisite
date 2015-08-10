<?php

$params = yii\helpers\ArrayHelper::merge(
    require( dirname( dirname( __DIR__ ) ) . '/common/config/params.php' ),
    require( __DIR__ . '/params.php' )
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'cmgcore/site/index',
    'bootstrap' => [ 'log', 'cmgCore' ],
    'modules' => [
        'cmgcore' => [
            'class' => 'cmsgears\core\frontend\Module'
        ],
        'cmgforms' => [
            'class' => 'cmsgears\forms\frontend\Module'
        ],
        'cmgcms' => [
            'class' => 'cmsgears\cms\frontend\Module'
        ]
    ],
    'components' => [
        'view' => [
            'theme' => 'themes\multisite\Theme'
        ],
        'urlManager' => [
	        'rules' => [
	        	// APIX - Sub-Directory based multisite rules ************* - custom + 4th rule
	        	'<site:\w+>/apix/<module:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<site>/<module>/apix/<controller>/<action>',
	        	'<site:\w+>/apix/<controller:(user|file)>/<action:[\w\-]+>' => '<site>/cmgcore/apix/<controller>/<action>',
	        	'<site:\w+>/apix/<action:(login|register)>' => '<site>/cmgcore/apix/site/<action>',
	        	'<site:\w+>/apix/<action:(contact)>' => '<site>/cmgforms/apix/site/<action>',
				// APIX - Sub-Domain based multisite rules **************** - 4th rule
	        	'apix/<module:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<module>/apix/<controller>/<action>',
	        	'apix/<controller:(user|file)>/<action:[\w\-]+>' => 'cmgcore/apix/<controller>/<action>',
	        	'apix/<action:(login|register)>' => 'cmgcore/apix/site/<action>',
	        	'apix/<action:(contact)>' => 'cmgforms/apix/site/<action>',
	        	// Regular - Sub-Directory/Domain based multisite rules *** - custom + 2nd rule
	        	// sub-directory - catch all
	        	'<site:\w+>/<module:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<site>/<module>/<controller>/<action>',
	        	// sub-directory - cms posts
	        	'<site:\w+>/post/<slug:[\w\-]+>' => '<site>/cmgcms/site/post',
	        	// sub-domain - direct actions - 2nd rule
	        	'<action:(home)>' => 'cmgcore/user/<action>',
	        	'<action:(contact|feedback)>' => 'cmgforms/site/<action>',
	        	'<action:(login|logout|register|forgot-password|reset-password|activate-account|confirm-account)>' => 'cmgcore/site/<action>',
	        	// sub-directory - site home page
	        	// TODO: Allow URL Manager to show landing page for side. It's clashing with last rule for page slug. Check Creating Rule Classes on http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html
	        	//'<site:\w+>' => '<site>/cmgcore/site/index',
	        	'<site:\w+><action:(/)>' => '<site>/cmgcore/site/index',
				// sub-directory - direct actions - custom + 2nd rule
	        	'<site:\w+>/<action:(home)>' => '<site>/cmgcore/user/<action>',
	        	'<site:\w+>/<action:(contact|feedback)>' => '<site>/cmgforms/site/<action>',
	        	'<site:\w+>/<action:(login|logout|register|forgot-password|reset-password|activate-account|confirm-account)>' => '<site>/cmgcore/site/<action>',
				// sub-domain - cms posts
				'post/<slug:[\w\-]+>' => 'cmgcms/site/post',
				// sub-directory - cms pages
				'<site:\w+>/<slug:[\w\-]+>' => '<site>/cmgcms/site/index',
				// sub-domain - catch all
				'<module:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
	        	// cms pages
	        	'<slug:[\w\-]+>' => 'cmgcms/site/index'
	        ]
		],
        'cmgCore' => [
        	'loginRedirectPage' => '/cmgcore/user/home'
        ]
    ],
    'params' => $params
];

?>