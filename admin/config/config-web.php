<?php

$params = yii\helpers\ArrayHelper::merge(
    require( dirname( dirname( __DIR__ ) ) . '/common/config/params.php' ),
    require( __DIR__ . '/params.php' )
);

return [
    'id' => 'app-admin',
    'basePath' => dirname( __DIR__ ),
    'controllerNamespace' => 'admin\controllers',
    'defaultRoute' => 'cmgcore/site/index',
    'bootstrap' => [ 'log', 'cmgCore' ],
    'modules' => [
        'cmgcore' => [
            'class' => 'cmsgears\core\admin\Module'
        ],
        'cmgforms' => [
            'class' => 'cmsgears\forms\admin\Module'
        ],
        'cmgcms' => [
            'class' => 'cmsgears\cms\admin\Module'
        ]
    ],
    'components' => [
        'view' => [
            'theme' => 'themes\admin\Theme'
        ],
        'urlManager' => [
	        'rules' => [
	        	// Sub-Directory based multisite rules -------------
	        	// apix request rules - custom + 4th rule
	        	'<site:\w+>/apix/<module:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<site>/<module>/apix/<controller>/<action>',
	        	// regular request rule - custom + 2nd rule
	        	'<site:\w+>/<module:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<site>/<module>/<controller>/<action>',
	        	// direct actions - custom + 2nd rule
	        	'<site:\w+>/<action:(login|logout|dashboard)>' => '<site>/cmgcore/site/<action>',
				// Sub-Domain based multisite rules ----------------
	        	// apix request rules - 4th rule
	        	'apix/<module:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<module>/apix/<controller>/<action>',
				// regular request rule - 2nd rule
	        	'<module:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
	        	// direct actions - 2nd rule
	        	'<action:(login|logout|dashboard)>' => 'cmgcore/site/<action>'
	        ]
		],
        'cmgCore' => [
        	'loginRedirectPage' => '/dashboard',
        	'logoutRedirectPage' => '/login',
        	'editorClass' => 'cmsgears\widgets\cleditor\ClEditor',
        ],
        'sidebar' => [
        	'class' => 'cmsgears\core\admin\components\Sidebar',
        	'modules' => [ 'cmgcms', 'cmgcore' ]
        ]
    ],
    'params' => $params
];

?>