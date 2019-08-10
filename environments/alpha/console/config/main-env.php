<?php

return [
	'bootstrap' => [ 'gii' ],
	'modules' => [
		'gii' => 'yii\gii\Module'
	],
	'components' => [
		'urlManager' => [
			'class' => 'yii\web\UrlManager',
			'baseUrl' => 'https://dev.vcdevhub.com/multisitedemo/frontend/web'
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
			'defaultSite' => 'https://dev.vcdevhub.com/multisitedemo/frontend/web',
			'defaultAdmin' => 'https://dev.vcdevhub.com/multisitedemo/backend/web',
			'uploadsUrl' => 'https://dev.vcdevhub.com/multisitedemo/uploads'
		]
	]
];
