<?php

return [
	'bootstrap' => [ 'gii' ],
	'modules' => [
		'gii' => 'yii\gii\Module'
	],
	'components' => [
		'urlManager' => [
			'class' => 'yii\web\UrlManager',
			'baseUrl' => 'http://localhost/multisitedemo/frontend/web'
		],
		// CMG Modules - Core
		'migration' => [
			'class' => 'cmsgears\core\common\components\Migration',
			'cmgPrefix' => 'cmg_',
			'sitePrefix' => 'mls_',
			'siteName' => 'Multisite',
			'siteTitle' => 'Multisite Demo',
			'siteMaster' => 'demomaster',
			'primaryDomain' => 'dev.vcdevhub.com',
			'defaultSite' => 'http://localhost/multisitedemo/frontend/web',
			'defaultAdmin' => 'http://localhost/multisitedemo/backend/web',
			'uploadsUrl' => 'http://localhost/multisitedemo/uploads'
		]
	]
];
