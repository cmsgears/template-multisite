<?php

return [
	'components' => [
		'urlManager' => [
			'class' => 'yii\web\UrlManager',
			'baseUrl' => 'https://demo.cmsgears.com/templates/multisite'
		],
		// CMG Modules - Core
		'migration' => [
			'class' => 'cmsgears\core\common\components\Migration',
			'cmgPrefix' => 'cmg_',
			'appPrefix' => 'cmg_',
			'siteName' => 'CMSGears',
			'siteTitle' => 'CMSGears Demo',
			'siteMaster' => 'demomaster',
			'primaryDomain' => 'cmsgears.com',
			'defaultSite' => 'https://demo.cmsgears.com/templates/multisite',
			'defaultAdmin' => 'https://demo.cmsgears.com/templates/multisite/admin',
			'uploadsUrl' => 'https://demo.cmsgears.com/templates/multisite/uploads'
		]
	]
];
