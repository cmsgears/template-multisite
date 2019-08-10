<?php
// CMS Imports
use cmsgears\core\common\config\CoreGlobal;

return [
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm'   => '@vendor/npm-asset'
	],
    'vendorPath' => dirname( dirname( __DIR__ ) ) . '/vendor',
    'components' => [
    	// Yii Components
        'user' => [
        	'class' => 'yii\web\User',
            'identityClass' => 'cmsgears\core\common\models\entities\User',
			'enableAutoLogin' => true,
			'loginUrl' => '@web/login'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => [ 'error', 'warning' ]
				]
			]
		],
        'view' => [
            'renderers' => [
                'twig' => [
                    'class' => 'yii\twig\ViewRenderer',
                    'cachePath' => '@runtime/Twig/cache',
                    // Array of twig options:
                    'options' => [
                        'auto_reload' => true
                    ],
                    'globals' => [
						'html' => '\yii\helpers\Html'
					],
                    //'uses' => [ 'yii\bootstrap' ]
                ]
            ]
        ],
        // CMG Modules - Core
        'factory' => [
        	'class' => 'cmsgears\core\common\components\ObjectFactory'
        ],
		'core' => [
			'class' => 'modules\core\common\components\Core',
			//'editor' => 'cmsgears\widgets\cleditor\ClEditor',
			'editor' => [
				'class' => 'cmsgears\widgets\tinymce\TinyMce',
				'options' => [
					'unsafeTargetLink' => true
				]
			],
			'rbacFilters' => [
				'discover' => 'cmsgears\core\common\filters\DiscoverFilter',
				'owner' => 'cmsgears\core\common\filters\OwnerFilter'
			],
			'notifications' => true,
			'siteConfigAll' => true,
			'multiSite' => true,
			'autoSiteMember' => true,
			'subDirectory' => false,
			'testHosts' => [ 'localhost', 'dev.vcdevhub.com' ],
			'defaultSiteSlug' => 'main',
			'provinceLabel' => 'State',
			'regionLabel' => 'Region',
			'zipLabel' => 'Postal Code'
		],
		'coreFactory' => [
			'class' => 'cmsgears\core\common\components\Factory'
		],
		'coreMessage' => [
			'class' => 'modules\core\common\components\MessageSource'
		],
		'coreMailer' => [
			'class' => 'modules\core\common\components\Mailer'
		],
		'formDesigner' => [
			'class' => 'modules\core\common\components\FormDesigner'
		],
		'templateManager' => [
			'class' => 'modules\core\common\components\TemplateManager',
			'templatesPath' => null,
			'renderers' => [
				'default' => 'Default',
				'twig' => 'Twig',
				'smarty' => 'Smarty'
			]
		],
		'pluginManager' => [
			'class' => 'cmsgears\core\common\components\PluginManager',
			'plugins' => [
				'basicSeo' => [ 'class' => 'cmsgears\seo\plugins\BasicSeo', 'modelTypes' => [] ]
			]
		],
		'eventManager' => [
			'class' => 'cmsgears\notify\common\components\EventManager'
		],
		'smsManager' => [
			'class' => 'modules\core\common\components\SmsManager'
		],
		// CMG Modules - Forms
		'forms' => [
			'class' => 'cmsgears\forms\common\components\Form'
		],
		'formsFactory' => [
			'class' => 'cmsgears\forms\common\components\Factory'
		],
		'formsMessage' => [
			'class' => 'cmsgears\forms\common\components\MessageSource'
		],
		'formsMailer' => [
			'class' => 'cmsgears\forms\common\components\Mailer'
		],
		// CMG Modules - CMS
		'cms' => [
			'class' => 'cmsgears\cms\common\components\Cms'
		],
		'cmsFactory' => [
			'class' => 'cmsgears\cms\common\components\Factory'
		],
		'cmsMessage' => [
			'class' => 'cmsgears\cms\common\components\MessageSource'
		],
		'cmsMailer' => [
			'class' => 'cmsgears\cms\common\components\Mailer'
		],
		// CMG Modules - Newsletter
		'newsletter' => [
			'class' => 'cmsgears\newsletter\common\components\Newsletter'
		],
		'newsletterFactory' => [
			'class' => 'cmsgears\newsletter\common\components\Factory'
		],
		'newsletterMessage' => [
			'class' => 'cmsgears\newsletter\common\components\MessageSource'
		],
		'newsletterMailer' => [
			'class' => 'cmsgears\newsletter\common\components\Mailer'
		],
		// CMG Modules - Notify
		'notify' => [
			'class' => 'cmsgears\notify\common\components\Notify',
			//'defaultLayout' => '//max'
		],
		'notifyFactory' => [
			'class' => 'cmsgears\notify\common\components\Factory'
		],
		'notifyMessage' => [
			'class' => 'cmsgears\notify\common\components\MessageSource'
		],
		'notifyMailer' => [
			'class' => 'cmsgears\notify\common\components\Mailer'
		],
		// CMG Modules - SNS Connect
		'snsConnect' => [
			'class' => 'cmsgears\social\connect\common\components\SnsConnect'
		],
		'snsConnectFactory' => [
			'class' => 'cmsgears\social\connect\common\components\Factory'
		],
		'snsConnectMessage' => [
			'class' => 'cmsgears\social\connect\common\components\MessageSource'
		],
		'snsConnectMailer' => [
			'class' => 'cmsgears\social\connect\common\components\Mailer'
		],
		// CMG Plugins
		'fileManager' => [
			'class' => 'cmsgears\files\components\FileManager'
		],
		'iconManager' => [
			'class' => 'cmsgears\icons\components\IconManager'
		],
		'breeze' => [
			'class' => 'cmsgears\templates\breeze\components\Breeze'
		],
		// FoxSlider
		'foxSlider' => [
			'class' => 'foxslider\common\components\Core'
		],
		// Multisite
		'mlsCoreFactory' => [
			'class' => 'modules\core\common\components\Factory'
		]
	]
];
