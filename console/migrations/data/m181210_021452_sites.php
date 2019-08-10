<?php
// CMG Imports
use cmsgears\core\common\models\entities\Locale;
use cmsgears\core\common\models\entities\Site;
use cmsgears\core\common\models\entities\Role;
use cmsgears\core\common\models\entities\Theme;
use cmsgears\core\common\models\entities\User;

use cmsgears\core\common\utilities\DateUtil;

// MLS Imports
use modules\core\common\config\CoreGlobal;

class m181210_021452_sites extends \cmsgears\core\common\base\Migration {

	// Public Variables

	// Private Variables

	private $cmgPrefix;
	private $sitePrefix;

	private $site;

	private $master;

	private $locale;

	public function init() {

		// Table prefix
		$this->cmgPrefix	= Yii::$app->migration->cmgPrefix;
		$this->sitePrefix	= Yii::$app->migration->sitePrefix;

		$this->site		= Site::findBySlug( CoreGlobal::SITE_MAIN );
		$this->master	= User::findByUsername( Yii::$app->migration->getSiteMaster() );

		Yii::$app->core->setSite( $this->site );
	}

	public function up() {

		$this->insertFiles();

		$this->updateSite();

		$this->insertSites();

		$this->insertSitesMeta();

		$this->insertSiteUsers();

		$this->insertSiteMembers();
	}

	private function insertFiles() {

		$site	= $this->site;
		$master	= $this->master;

		$columns = [ 'id', 'siteId', 'createdBy', 'modifiedBy', 'name', 'tag', 'title', 'description', 'extension', 'directory', 'size', 'visibility', 'type', 'storage', 'url', 'medium', 'small', 'thumb', 'placeholder', 'smallPlaceholder', 'ogg', 'webm', 'caption', 'altText', 'link', 'shared', 'srcset', 'sizes', 'createdAt', 'modifiedAt', 'content', 'data', 'gridCache', 'gridCacheValid', 'gridCachedAt' ];

		$files = [
			[ 100001, $site->id, $master->id, $master->id, 'banner', null, 'multisite', '', 'jpg', 'banner', 0.1914, 1500, 'image', NULL, '2018-12-29/banner/banner.jpg', '2018-12-29/banner/banner-medium.jpg', NULL, '2018-12-29/banner/banner-thumb.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, 0, NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_file', $columns, $files );
	}

	private function updateSite() {

		$this->update( $this->cmgPrefix . 'core_site', [ 'bannerId' => 100001 ], [ 'slug' => 'main' ] );
	}

	private function insertSites() {

		$master	= $this->master;
		$theme	= Theme::findBySlug( 'blog' );

		$columns = [ 'id', 'themeId', 'createdBy', 'modifiedBy', 'name', 'slug', 'title', 'order', 'active', 'createdAt', 'modifiedAt' ];

		$sites = [
			[ 101, $theme->id, $master->id, $master->id, 'Programming', 'programming', 'Programming', 0, 1, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			[ 102, $theme->id, $master->id, $master->id, 'Database', 'database', 'Database', 0, 1, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			[ 103, $theme->id, $master->id, $master->id, 'Algorithms', 'algorithms', 'Algorithms', 0, 1, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			[ 104, $theme->id, $master->id, $master->id, 'Networking', 'networking', 'Networking', 0, 1, DateUtil::getDateTime(), DateUtil::getDateTime() ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_site', $columns, $sites );
	}

	private function insertSitesMeta() {

	}

	private function insertSiteUsers() {

		// Default password for all test users is test#123
		// Super Admin i.e. demomaster must change username, password and email on first login and remove other users if required.

		$columns = [ 'id', 'localeId', 'genderId', 'avatarId', 'bannerId', 'videoId', 'templateId' , 'status', 'email', 'username', 'slug', 'passwordHash' , 'type' , 'icon' , 'title' , 'firstName' , 'middleName', 'lastName' , 'name', 'message', 'description', 'dob', 'mobile', 'phone', 'emailVerified', 'mobileVerified', 'timeZone', 'avatarUrl', 'websiteUrl', 'verifyToken', 'verifyTokenValidTill', 'resetToken', 'resetTokenValidTill', 'registeredAt', 'lastLoginAt', 'lastActivityAt', 'authKey', 'otp', 'otpValidTill', 'accessToken', 'accessTokenType', 'tokenCreatedAt', 'tokenAccessedAt', 'content', 'data', 'gridCache', 'gridCacheValid', 'gridCachedAt' ];

		$this->locale = Locale::findByCode( 'en_US' );

		$users = [
			//[ 5, $this->locale->id, NULL, NULL, NULL, NULL, NULL, User::STATUS_ACTIVE, 'user@example.com', 'user', 'user', '$2y$13$Ut5b2RskRpGA9Q0nKSO6Xe65eaBHdx/q8InO8Ln6Lt3HzOK4ECz8W',CoreGlobal::TYPE_DEFAULT,'icon','','Archana','','','Archana','','',NULL,'7259224413','',1,0,NULL,'','','xnaNUktj2Lh0F3WtGjvcgm7viJMu0i2N',NULL,NULL,NULL,'2018-12-07 12:32:25',NULL,NULL,'tFhJLcg8qQa6hRm01eW9miO9cfxNcDhm',645105,'2018-12-14 11:12:25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_user', $columns, $users );
	}

	private function insertSiteMembers() {

		$superAdminRole = Role::findBySlugType( 'super-admin', CoreGlobal::TYPE_SYSTEM );

		$columns = [ 'id', 'siteId', 'userId', 'roleId', 'createdAt', 'modifiedAt' ];

		$members = [
			[ 10001, Site::findBySlug( 'programming' )->id, $this->master->id, $superAdminRole->id, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			[ 10002, Site::findBySlug( 'database' )->id, $this->master->id, $superAdminRole->id, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			[ 10003, Site::findBySlug( 'algorithms' )->id, $this->master->id, $superAdminRole->id, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			[ 10004, Site::findBySlug( 'networking' )->id, $this->master->id, $superAdminRole->id, DateUtil::getDateTime(), DateUtil::getDateTime() ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_site_member', $columns, $members );
	}

	public function down() {

		echo "m181210_021452_sites will be deleted with m160621_014408_core.\n";
	}

}
