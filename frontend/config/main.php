<?php

$params = yii\helpers\ArrayHelper::merge(
	require( dirname( dirname( __DIR__ ) ) . '/common/config/params.php' ),
	require( __DIR__ . '/params.php' )
);

return [
	'id' => 'app-frontend',
	'name' => 'MultiSite Demo',
	'version' => '1.0.0',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'frontend\controllers',
	'defaultRoute' => 'core/site/index',
	'bootstrap' => [
		'log',
		'core', 'cms', 'forms', 'snsLogin', 'newsletter', 'notify',
		'foxSlider'
	],
	'modules' => [
		'core' => [
			'class' => 'cmsgears\core\frontend\Module'
		],
		'cms' => [
			'class' => 'cmsgears\cms\frontend\Module'
		],
		'forms' => [
			'class' => 'cmsgears\forms\frontend\Module'
		],
		'snslogin' => [
			'class' => 'cmsgears\social\login\frontend\Module'
		],
		'newsletter' => [
			'class' => 'cmsgears\newsletter\frontend\Module'
		],
		'notify' => [
			'class' => 'cmsgears\notify\frontend\Module'
		]
	],
	'components' => [
		'view' => [
			'theme' => [
				'class' => 'themes\blog\Theme',
				'childs' => [
					// Child themes to override theme css and to add additional js
				]
			]
		],
		'request' => [
			'csrfParam' => '_csrf-app',
			'parsers' => [
				'application/json' => 'yii\web\JsonParser'
			]
		],
		'user' => [
			'identityCookie' => [ 'name' => '_identity-app', 'httpOnly' => true ]
		],
		'session' => [
			'name' => 'cmg-blog-app'
		],
		'errorHandler' => [
			'errorAction' => 'core/site/error'
		],
		'assetManager' => [
			'bundles' => require( __DIR__ . '/' . ( YII_ENV_PROD ? 'assets-prod.php' : 'assets-dev.php' ) )
		],
		'urlManager' => [
			'rules' => [
				// TODO: Use Group Rule for api and apix prefix
				// TODO: Add multisite rules
				// api request rules ---------------------------
				'api/<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/api/<controller>/<action>',
				'api/<module:\w+>/<controller:[\w\-]+>/<pcontroller:[\w\-]+>/<action:[\w\-]+>' => '<module>/api/<controller>/<pcontroller>/<action>',
				'api/<module:\w+>/<pcontroller1:\w+>/<pcontroller2:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<module>/api/<pcontroller1>/<pcontroller2>/<controller>/<action>',
				// apix request rules --------------------------
				// Forms - site forms
				'apix/form/<slug:[\w\-]+>' => 'forms/apix/form/submit',
				// Core - 2 levels
				'apix/<controller:[\w\-]+>/<action:[\w\-]+>' => 'core/apix/<controller>/<action>',
				// Generic - 3, 4 and 5 levels - catch all
				'apix/<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/apix/<controller>/<action>',
				'apix/<module:\w+>/<pcontroller:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/apix/<pcontroller>/<controller>/<action>',
				'apix/<module:\w+>/<pcontroller1:[\w\-]+>/<pcontroller2:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/apix/<pcontroller1>/<pcontroller2>/<controller>/<action>',
				// regular request rules -----------------------
				// SNS Login
				'sns/<controller:\w+>/<action:[\w\-]+>' => 'snslogin/<controller>/<action>',
				// TODO: Use Group Rule for blog
				// Blog Posts - Public - search, category, tag and single
				'blog/search' => 'cms/post/search',
				'blog/category/<slug:[\w\-]+>' => 'cms/post/category',
				'blog/tag/<slug:[\w\-]+>' => 'cms/post/tag',
				'blog/<slug:[\w\-]+>' => 'cms/post/single',
				// Blog Posts - Private 2 and 3 levels
				'blog/manage/<action:[\w\-]+>' => 'core/listing/<action>',
				'blog/<controller:\w+>/<action:[\w\-]+>' => 'core/listing/<controller>/<action>',
				'blog/<pcontroller:\w+>/<controller:\w+>/<action:[\w\-]+>' => 'core/listing/<pcontroller>/<controller>/<action>',
				// Forms
				'form/<slug:[\w\-]+>' => 'forms/form/single',
				// Core Module Pages
				'<controller:[\w\-]+>/<action:[\w\-]+>' => 'core/<controller>/<action>',
				// Module Pages - 2 and 3 levels - catch all
				'<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
				'<module:\w+>/<pcontroller:[\w\-]+>/<controller:\w+>/<action:[\w\-]+>' => '<module>/<pcontroller>/<controller>/<action>',
				// Standard Pages
				'<action:(home|profile|account|address|settings)>' => 'core/user/<action>',
				'<action:(login|logout|register|forgot-password|reset-password|activate-account|confirm-account)>' => 'core/site/<action>',
				// CMS Pages
				'<slug:[\w\-]+>' => 'cms/page/single'
			]
		],
		'core' => [
			'loginRedirectPage' => '/home',
			'logoutRedirectPage' => '/'
		]
	],
	'params' => $params
];

/*
// Group rules example

// Post - 2 and 3 levels
[
	'class' => 'yii\web\GroupUrlRule',
	'prefix' => 'apix/post',
	'routePrefix' => 'cms/apix/post',
	'rules' => [
		'<controller:[\w\-]+>/<action:[\w\-]+>' => '<controller>/<action>',
		'<pcontroller:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<pcontroller>/<controller>/<action>'
	]
],
// Core - 2 levels
'apix/<controller:\w+>/<action:[\w\-]+>' => 'core/apix/<controller>/<action>',
// Generic - 3 and 4 levels - must be last, since it got wildcard in prefix
[
	'class' => 'yii\web\GroupUrlRule',
	'prefix' => 'apix/<module:\w+>',
	'routePrefix' => '<module>/apix',
	'rules' => [
		'<controller:[\w\-]+>/<action:[\w\-]+>' => '<controller>/<action>',
		'<pcontroller:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<pcontroller>/<controller>/<action>'
	]
],

// Blog
[
	'class' => 'yii\web\GroupUrlRule',
	'prefix' => 'blog',
	'routePrefix' => 'cms/post',
	'rules' => [
		'search' => 'search',
		'category/<slug:[\w\-]+>' => 'category',
		'tag/<slug:[\w\-]+>' => 'tag',
		'<slug:[\w\-]+>' => 'single'
	]
],

 */