<?php
// CMG Imports
use cmsgears\cms\common\config\CmsGlobal;

use cmsgears\core\common\models\entities\Site;
use cmsgears\core\common\models\entities\Template;
use cmsgears\core\common\models\entities\User;
use cmsgears\cms\common\models\entities\Block;
use cmsgears\cms\common\models\entities\Sidebar;
use cmsgears\cms\common\models\entities\Widget;

use cmsgears\core\common\utilities\DateUtil;

// MLS Imports
use modules\core\common\config\CoreGlobal;

class m181211_046641_objects extends \cmsgears\core\common\base\Migration {

	// Public Variables

	// Private Variables

	private $cmgPrefix;
	private $sitePrefix;

	private $site;

	private $master;

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

		$this->insertMenus();

		$this->insertElements();
		$this->insertWidgets();
		$this->insertBlocks();
		$this->insertSidebars();

		$this->insertMappings();

		$this->updateWidgetTemplates();
		$this->updateWidgets();
		$this->updateBlocks();
	}

	private function insertFiles() {

		$site	= $this->site;
		$master	= $this->master;

		$columns = [ 'id', 'siteId', 'createdBy', 'modifiedBy', 'name', 'tag', 'title', 'description', 'extension', 'directory', 'size', 'visibility', 'type', 'storage', 'url', 'medium', 'small', 'thumb', 'placeholder', 'smallPlaceholder', 'ogg', 'webm', 'caption', 'altText', 'link', 'shared', 'srcset', 'sizes', 'createdAt', 'modifiedAt', 'content', 'data', 'gridCache', 'gridCacheValid', 'gridCachedAt' ];

		$files = [
			[ 104001, $site->id, $master->id, $master->id, 'logo', null, 'logo', null, 'png', 'avatar', 0.0029, 1500, 'image', NULL, '2018-12-29/avatar/logo.png', '2018-12-29/avatar/logo-medium.png', '2019-08-10/avatar/logo-small.png', '2018-12-29/avatar/logo-thumb.png', '2019-08-10/avatar/logo-pl.png', '2019-08-10/avatar/logo-small-pl.png', NULL, NULL, NULL, NULL, NULL, 0, '160,96,48', '(max-width: 160px) 100vw, 160px', DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, 0, NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_file', $columns, $files );
	}

	private function insertMenus() {

		$site	= $this->site;
		$master	= $this->master;

		$columns = [ 'id', 'siteId', 'themeId', 'templateId', 'createdBy', 'modifiedBy', 'name', 'slug', 'type', 'icon', 'texture', 'title', 'description', 'status', 'visibility', 'order', 'pinned', 'featured', 'createdAt', 'modifiedAt', 'htmlOptions', 'summary', 'content', 'data', 'gridCache', 'gridCacheValid', 'gridCachedAt' ];

		$menus = [
			//[ 8001, $site->id, NULL, NULL, $master->id, $master->id, 'About', 'about', 'menu', 'icon', 'texture', NULL, 'The About Us menu displayed on main header.', 16000, 1500, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, NULL, NULL, 0, NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_object', $columns, $menus );
	}

	private function insertElements() {

		$site	= $this->site;
		$master	= $this->master;

		// Templates
		$cardElement	= Template::findGlobalBySlugType( 'card', CmsGlobal::TYPE_ELEMENT );
		$boxElement		= Template::findGlobalBySlugType( 'box', CmsGlobal::TYPE_ELEMENT );
		$iconElement	= Template::findByThemeSlugType( 'icon', CmsGlobal::TYPE_ELEMENT );
		$socialElement	= Template::findByThemeSlugType( 'social', CmsGlobal::TYPE_ELEMENT );

		$columns = [ 'id', 'siteId', 'themeId', 'templateId', 'avatarId', 'bannerId', 'videoId', 'galleryId', 'createdBy', 'modifiedBy', 'name', 'slug', 'type', 'icon', 'texture', 'title', 'description', 'classPath', 'link', 'status', 'visibility', 'order', 'pinned', 'featured', 'createdAt', 'modifiedAt', 'htmlOptions', 'summary', 'content', 'data', 'gridCache', 'gridCacheValid', 'gridCachedAt' ];

		$elements = [
			[ 10001, $site->id, NULL, $cardElement->id, 104001, NULL, NULL, NULL, $master->id, $master->id, 'Intro', 'intro', 'element', 'icon', 'texture', 'Intro', 'Tutorials for everyone', NULL, NULL, 16000, 1500, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(),NULL,'We at Multisite are happy to help.',null,'{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerInfo":"0","headerContent":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"1","contentData":"0","contentRaw":"","maxCover":"0","contentClass":"","contentDataClass":"","styles":"","scripts":"","footer":"1","footerIcon":"1","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","metas":"0","metaType":"","metaWrapClass":"","attributeType":"","elementStyles":"","attributeTypes":""}}', NULL, 0, NULL ],
			[ 10002, $site->id, NULL, $socialElement->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Social', 'social', 'element', 'icon', 'texture', NULL, NULL, NULL, NULL, 16000, 1500, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"0","headerTitle":"1","headerInfo":"0","headerContent":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"1","contentRaw":"","maxCover":"0","contentClass":"","contentDataClass":"","styles":"","scripts":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","metas":"0","metaType":"","metaWrapClass":"","attributeType":"","elementStyles":"","attributeTypes":""}}', NULL, 0, NULL ],
			[ 10003, $site->id, NULL, $iconElement->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Text 1', 'text1', 'element', 'icon fas fa-burn', 'texture' ,NULL, 'Learn daily to keep updated with the latest trends', NULL, NULL, 16000, 1500, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"1","headerTitle":"1","headerInfo":"0","headerContent":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"1","contentSummary":"0","contentData":"0","contentRaw":"","maxCover":"0","contentClass":"","contentDataClass":"","styles":"","scripts":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","metas":"0","metaType":"","metaWrapClass":"","attributeType":""}}', NULL, 0, NULL ],
			[ 10004, $site->id, NULL, $iconElement->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Text 2', 'text2', 'element','icon fas fa-certificate', 'texture', NULL, 'Become the king by gaining experience from learning and experimenting.', NULL, NULL, 16000, 1500, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"1","headerTitle":"1","headerInfo":"0","headerContent":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"1","contentSummary":"0","contentData":"0","contentRaw":"","maxCover":"0","contentClass":"","contentDataClass":"","styles":"","scripts":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","metas":"0","metaType":"","metaWrapClass":"","attributeType":""}}', NULL, 0, NULL ],
			[ 10005, $site->id, NULL, $iconElement->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Text 3', 'text3', 'element', 'icon fas fa-globe','texture', NULL, 'Share the knowledge gained while learning and experimenting and let others know to create a better community.', NULL, NULL, 16000, 1500, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"1","headerTitle":"1","headerInfo":"0","headerContent":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"1","contentSummary":"0","contentData":"0","contentRaw":"","maxCover":"0","contentClass":"","contentDataClass":"","styles":"","scripts":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","metas":"0","metaType":"","metaWrapClass":"","attributeType":""}}', NULL, 0, NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_object', $columns, $elements );

		$columns = [ 'id', 'modelId', 'name', 'label', 'type', 'active', 'order', 'valueType', 'value', 'data' ];

		$metas = [
			[ 100001, 10002, 'facebook', 'Facebook', '', 1, 0, 'text', 'https://www.facebook.com', NULL ],
			[ 100002, 10002, 'twitter', 'Twitter', '', 1, 0, 'text', 'https://twitter.com', NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_object_meta', $columns, $metas );
	}

	private function insertWidgets() {

		$site	= $this->site;
		$master	= $this->master;

		// Templates
		$defaultWidget = Template::findGlobalBySlugType( 'default', CmsGlobal::TYPE_WIDGET );

		$columns = [ 'id', 'siteId', 'themeId', 'templateId', 'avatarId', 'bannerId', 'videoId', 'galleryId', 'createdBy', 'modifiedBy', 'name', 'slug', 'type', 'icon', 'texture', 'title', 'description', 'classPath', 'link', 'status', 'visibility', 'order', 'pinned', 'featured', 'createdAt', 'modifiedAt', 'htmlOptions', 'summary', 'content', 'data', 'gridCache', 'gridCacheValid', 'gridCachedAt' ];

		$widgets = [
			//[ 10101, NULL, NULL, $defaultWidget->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Google Small 1', 'google-small-1', 'widget', 'icon', 'texture', 'Small Google Ad', NULL, NULL, NULL, 19000, 1500, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"1","contentRaw":"<ins class=\"adsbygoogle\"\r\n      style=\"display:block\"\r\n      data-ad-client=\"ca-pub-9075166357650375\"\r\n      data-ad-slot=\"6184770847\"\r\n      data-ad-format=\"auto\"\r\n      data-full-width-responsive=\"true\"><\/ins>\r\n <script>\r\n (adsbygoogle = window.adsbygoogle || []).push({});\r\n <\/script>","contentClass":"","contentDataClass":"","styles":"","scripts":"","metas":"0","metaType":"","metaWrapClass":"","attributeType":""}}', NULL, 0, NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_object', $columns, $widgets );
	}

	private function insertBlocks() {

		$site	= $this->site;
		$master	= $this->master;

		// Templates
		$defaultBlock = Template::findGlobalBySlugType( 'default', CmsGlobal::TYPE_BLOCK );

		$columns = [ 'id', 'siteId', 'themeId', 'templateId', 'avatarId', 'bannerId', 'videoId', 'galleryId', 'createdBy', 'modifiedBy', 'name', 'slug', 'type', 'icon', 'texture', 'title', 'description', 'classPath', 'link', 'status', 'visibility', 'order', 'pinned', 'featured', 'createdAt', 'modifiedAt', 'htmlOptions', 'summary', 'content', 'data', 'gridCache', 'gridCacheValid', 'gridCachedAt' ];

		$blocks = [
			[ 10201, $site->id, NULL, $defaultBlock->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Multisite Posts', 'multisite-posts', 'block', 'icon', 'texture', 'Popular Posts from All Sites','Shows posts from multiple sites.', NULL, NULL, 16000, 1500, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), '{ "cmt-block": "block-auto" }', NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","fixedBkg":"0","scrollBkg":"0","parallaxBkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerInfo":"0","headerContent":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"1","contentRaw":"","maxCover":"0","contentClass":"content-90","contentDataClass":"","styles":"","scripts":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","metas":"0","metaType":"","metaWrapClass":"","elements":"0","elementsBeforeContent":"0","elementType":"","boxWrapClass":"","boxWrapper":"","boxClass":"","widgets":"1","widgetsBeforeContent":"0","widgetType":"","widgetWrapClass":"widget-box-post-club row max-cols-100","widgetWrapper":"div","widgetClass":"widget-wrap colf colf2","attributeType":""}}', NULL, 0, NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_object', $columns, $blocks );
	}

	private function insertSidebars() {

		$site	= $this->site;
		$master	= $this->master;

		// Templates
		$defaultSidebar	= Template::findGlobalBySlugType( 'default', CmsGlobal::TYPE_SIDEBAR );
		$vertSidebar	= Template::findGlobalBySlugType( 'vsidebar', CmsGlobal::TYPE_SIDEBAR );
		$horizSidebar	= Template::findGlobalBySlugType( 'hsidebar', CmsGlobal::TYPE_SIDEBAR );

		$columns = [ 'id', 'siteId', 'themeId', 'templateId', 'avatarId', 'bannerId', 'videoId', 'galleryId', 'createdBy', 'modifiedBy', 'name', 'slug', 'type', 'icon', 'texture', 'title', 'description', 'classPath', 'link', 'status', 'visibility', 'order', 'pinned', 'featured', 'createdAt', 'modifiedAt', 'htmlOptions', 'summary', 'content', 'data', 'gridCache', 'gridCacheValid', 'gridCachedAt' ];

		$sidebars = [
			//[10301,1,NULL,$vertSidebar->id,NULL,NULL,NULL,NULL,1,1,'Landing Right','landing-right','sidebar','icon','texture','Landing Right Sidebar','Right sidebar displayed on landing page.',NULL,NULL,16000,1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(),NULL,NULL,NULL,NULL,NULL,0,NULL]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_object', $columns, $sidebars );
	}

	private function insertMappings() {

		$mainSidebar	= Sidebar::findBySlugType( 'main-right', CmsGlobal::TYPE_SIDEBAR );
		$pgrSidebar		= Sidebar::findBySlugType( 'page-right', CmsGlobal::TYPE_SIDEBAR );
		$psrSidebar		= Sidebar::findBySlugType( 'post-right', CmsGlobal::TYPE_SIDEBAR );
		$frmrSidebar	= Sidebar::findBySlugType( 'form-right', CmsGlobal::TYPE_SIDEBAR );

		$popPosts	= Widget::findBySlugType( 'popular-posts', CmsGlobal::TYPE_WIDGET );
		$popsPosts	= Widget::findBySlugType( 'popular-site-posts', CmsGlobal::TYPE_WIDGET );
		$recsPosts	= Widget::findBySlugType( 'recent-site-posts', CmsGlobal::TYPE_WIDGET );

		$columns = [ 'id', 'modelId', 'parentId', 'parentType', 'type', 'order', 'active', 'pinned', 'featured', 'nodes' ];

		$mappings = [
			//[ 100001, 10101, $mainSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			[ 100002, $popPosts->id, $mainSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ 100003, 10102, $mainSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ 100004, $msPosts->id, $msBlock->id, 'block', 'widget', 0, 1, 0, 0, NULL ],
			//[ 100011, 10101, $pgrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			[ 100012, $recsPosts->id, $pgrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ 100013, 10102, $pgrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ 100021, 10101, $psrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			[ 100022, $popsPosts->id, $psrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ 100023, 10102, $psrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			[ 100024, $recsPosts->id, $psrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ 100031, 10101, $frmrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			[ 100032, $recsPosts->id, $frmrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ 100033, 10102, $frmrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
		];

		$this->batchInsert( $this->cmgPrefix . 'core_model_object', $columns, $mappings );
	}

	private function updateWidgetTemplates() {

		$this->update( $this->cmgPrefix . 'core_template', [ 'viewPath' => '@themeTemplates/widget/model', 'view' => 'card' ], "slug IN ('page-card', 'post-card', 'article-card')" );
		$this->update( $this->cmgPrefix . 'core_template', [ 'viewPath' => '@themeTemplates/widget/model', 'view' => 'box' ], "slug IN ('page-box', 'post-box', 'article-box')" );
		$this->update( $this->cmgPrefix . 'core_template', [ 'viewPath' => '@themeTemplates/widget/model', 'view' => 'sbox' ], "slug IN ('page-search', 'post-search', 'article-search')" );
		$this->update( $this->cmgPrefix . 'core_template', [ 'viewPath' => '@themeTemplates/widget/model', 'view' => 'hbox' ], "slug IN ('post-home')" );
	}

	private function updateWidgets() {

		$pcMulti = Template::findByThemeSlugType( 'post-card-multi', CmsGlobal::TYPE_WIDGET );

		$settings = [
			'{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"route":"","allPath":"all","singlePath":"single","wrapperOptions":"{ \"class\": \"box-home-wrap\" }","singleOptions":"{ \"class\": \"box box-default box-home\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":"0","tagParam":"0","wrap":"1","options":"{ \"class\": \"widget-basic widget-box-home widget-box-home-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","showAllPath":"0","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"10","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"0","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}',
			'{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"route":"","allPath":"all","singlePath":"single","wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x3 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":"0","tagParam":"0","wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","showAllPath":"0","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"16","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"120","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}'
		];

		$this->update( $this->cmgPrefix . 'core_object', [ 'templateId' => $pcMulti->id ], [ 'slug' => 'recent-posts', 'type' => 'widget' ] );
		$this->update( $this->cmgPrefix . 'core_object', [ 'templateId' => $pcMulti->id ], [ 'slug' => 'popular-posts', 'type' => 'widget' ] );
		$this->update( $this->cmgPrefix . 'core_object', [ 'data' => $settings[ 0 ] ], [ 'slug' => 'home-posts', 'type' => 'widget' ] );
		$this->update( $this->cmgPrefix . 'core_object', [ 'data' => $settings[ 1 ] ], [ 'slug' => 'search-site-posts', 'type' => 'widget' ] );
	}

	private function updateBlocks() {

		$multisite = Template::findByThemeSlugType( 'multisite', CmsGlobal::TYPE_BLOCK );

		$this->update( $this->cmgPrefix . 'core_object', [ 'templateId' => $multisite->id ], [ 'slug' => 'multisite-posts', 'type' => 'block' ] );
	}

	public function down() {

		echo "m181211_046641_objects will be deleted with m160621_014408_core.\n";
	}

}
