<?php
// CMG Imports
use cmsgears\cms\common\config\CmsGlobal;

use cmsgears\core\common\models\entities\Locale;
use cmsgears\core\common\models\entities\ObjectData;
use cmsgears\core\common\models\entities\Role;
use cmsgears\core\common\models\entities\Site;
use cmsgears\core\common\models\entities\Template;
use cmsgears\core\common\models\entities\User;

use cmsgears\cms\common\models\entities\Menu;
use cmsgears\cms\common\models\entities\Page;
use cmsgears\cms\common\models\entities\Sidebar;
use cmsgears\cms\common\models\entities\Widget;

use cmsgears\core\common\utilities\DateUtil;

// MLS Imports
use modules\core\common\config\CoreGlobal;

class m181221_022751_multi extends \cmsgears\core\common\base\Migration {

	// Public Variables

	// Private Variables

	private $cmgPrefix;
	private $sitePrefix;

	private $site;
	private $locale;

	private $sites;

	private $master;

	public function init() {

		// Table prefix
		$this->cmgPrefix	= Yii::$app->migration->cmgPrefix;
		$this->sitePrefix	= Yii::$app->migration->sitePrefix;

		$this->site		= Site::findBySlug( CoreGlobal::SITE_MAIN );
		$this->master	= User::findByUsername( Yii::$app->migration->getSiteMaster() );

		Yii::$app->core->setSite( $this->site );

		$this->sites = [
			'programming', 'database', 'algorithms', 'networking'
		];

		$this->locale = Locale::findByCode( 'en_US' );
	}

	public function up() {

		$this->insertUsers();

		foreach( $this->sites as $site ) {

			$this->site = Site::findBySlug( $site );

			$this->insertSiteMembers();

			$this->insertPages();

			$this->updatePages();

			$this->updatePagesContent();

			$this->insertWidgets();

			$this->insertPageWidgetMappings();

			$this->insertPageBlockMappings();

			$this->insertSidebars();

			$this->insertMenus();

			$this->insertBlockWidgetMappings();

			$this->updateWidgets();

			$this->insertLinks();

			$this->insertLinkMappings();
		}

		$this->updateAutoIncs();
	}

	private function insertUsers() {

		$columns = [ 'localeId', 'status', 'email', 'username', 'type', 'passwordHash', 'firstName', 'lastName', 'name', 'registeredAt', 'lastLoginAt', 'authKey' ];

		$users = [
			[ $this->locale->id, User::STATUS_ACTIVE, "test1@cmsgears.com", 'test1', CoreGlobal::TYPE_DEFAULT, '$2y$13$Ut5b2RskRpGA9Q0nKSO6Xe65eaBHdx/q8InO8Ln6Lt3HzOK4ECz8W', 'Test1' , NULL, 'Test1', DateUtil::getDateTime(), DateUtil::getDateTime(), 'SQ1LLCWEPva4IKuQklILLGDpmUTGzq8E' ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_user', $columns, $users );
	}

	private function insertSiteMembers() {

		$siteId = $this->site->id;

		$adminRole = Role::findBySlugType( 'admin', CoreGlobal::TYPE_SYSTEM );

		$test1 = User::findByUsername( 'test1' );

		$columns = [ 'id', 'siteId', 'userId', 'roleId', 'createdAt', 'modifiedAt' ];

		$members = [
			[ intval( $siteId . '01' ), $this->site->id, $test1->id, $adminRole->id, DateUtil::getDateTime(), DateUtil::getDateTime() ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_site_member', $columns, $members );
	}

	private function insertPages() {

		$master	= $this->master;

		$siteId = $this->site->id;

		$columns = [ 'id', 'siteId', 'createdBy', 'modifiedBy', 'name', 'slug', 'type', 'icon', 'title', 'status', 'visibility', 'order', 'featured', 'comments', 'createdAt', 'modifiedAt' ];

		$pages	= [
			[ intval( $siteId . '001' ), $this->site->id, $master->id, $master->id, 'Home', 'home', CmsGlobal::TYPE_PAGE, null, null, Page::STATUS_ACTIVE, Page::VISIBILITY_PUBLIC, 0, false, false, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			// Hidden Search Pages
			[ intval( $siteId . '018' ), $this->site->id, $master->id, $master->id, 'Blog', 'blog', CmsGlobal::TYPE_PAGE, null, null, Page::STATUS_ACTIVE, Page::VISIBILITY_PUBLIC, 0, false, false, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			[ intval( $siteId . '019' ), $this->site->id, $master->id, $master->id, 'Search Pages', 'search-pages', CmsGlobal::TYPE_PAGE, null, null, Page::STATUS_ACTIVE, Page::VISIBILITY_PUBLIC, 0, false, false, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			[ intval( $siteId . '020' ), $this->site->id, $master->id, $master->id, 'Search Articles', 'search-articles', CmsGlobal::TYPE_PAGE, null, null, Page::STATUS_ACTIVE, Page::VISIBILITY_PUBLIC, 0, false, false, DateUtil::getDateTime(), DateUtil::getDateTime() ],
			[ intval( $siteId . '021' ), $this->site->id, $master->id, $master->id, 'Search Posts', 'search-posts', CmsGlobal::TYPE_PAGE, null, null, Page::STATUS_ACTIVE, Page::VISIBILITY_PUBLIC, 0, false, false, DateUtil::getDateTime(), DateUtil::getDateTime() ]
		];

		$this->batchInsert( $this->cmgPrefix . 'cms_page', $columns, $pages );

		$summary = '';
		$content = '';

		$columns = [ 'id', 'parentId', 'parentType', 'seoName', 'seoDescription', 'seoKeywords', 'seoRobot', 'summary', 'content', 'publishedAt' ];

		$pagesContent = [
			[ intval( $siteId . '001' ), Page::findBySlugType( 'home', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id, CmsGlobal::TYPE_PAGE, null, null, null, null, $summary, $content, DateUtil::getDateTime() ],
			// Hidden Search Pages
			[ intval( $siteId . '018' ), Page::findBySlugType( 'blog', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id, CmsGlobal::TYPE_PAGE, null, null, null, null, $summary, $content, DateUtil::getDateTime() ],
			[ intval( $siteId . '019' ), Page::findBySlugType( 'search-pages', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id, CmsGlobal::TYPE_PAGE, null, null, null, null, $summary, $content, DateUtil::getDateTime() ],
			[ intval( $siteId . '020' ), Page::findBySlugType( 'search-articles', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id, CmsGlobal::TYPE_PAGE, null, null, null, null, $summary, $content, DateUtil::getDateTime() ],
			[ intval( $siteId . '021' ), Page::findBySlugType( 'search-posts', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id, CmsGlobal::TYPE_PAGE, null, null, null, null, $summary, $content, DateUtil::getDateTime() ]
		];

		$this->batchInsert( $this->cmgPrefix . 'cms_model_content', $columns, $pagesContent );
	}

	private function updatePages() {

		$siteId = $this->site->id;

		$siteName = $this->site->name;

		$desc = [
			"Find tutorials specific to $siteName at Multisite. On this site, you can find latest and easy to understand tutorials written by us for $siteName.",
			'Blog',
			"Browse $siteName Pages", "Browse $siteName Articles", "Browse $siteName Posts"
		];

		$setting = [
			'{"settings":{"defaultAvatar":"0","defaultBanner":"0","fixedBanner":"0","scrollBanner":"0","parallaxBanner":"0","background":"0","backgroundClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerInfo":"0","headerContent":"0","headerIconUrl":"","headerBanner":"0","headerGallery":"0","headerElements":"0","headerElementType":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","maxCover":"0","contentSocial":"0","contentLabels":null,"contentAvatar":"0","contentBanner":"0","contentGallery":"0","contentClass":"","contentDataClass":"","styles":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","footerElements":"0","footerElementType":"","attributes":"0","attributesWithContent":"0","attributesOrder":"","attributeType":"","metaWrapClass":"","elements":"0","elementsBeforeContent":"0","elementsWithContent":"0","elementsOrder":"","elementType":"","boxWrapClass":"","boxWrapper":"","boxClass":"","widgets":"1","widgetsBeforeContent":"0","widgetsWithContent":"1","widgetsOrder":"","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":"","blocks":"1","blocksBeforeContent":"0","blocksWithContent":"0","blocksOrder":"","blockType":"","sidebars":"0","sidebarsBeforeContent":"0","sidebarsWithContent":"0","sidebarsOrder":"","sidebarType":"","topSidebar":"0","topSidebarSlugs":"","bottomSidebar":"0","bottomSidebarSlugs":"","leftSidebar":"0","leftSidebarSlug":"","rightSidebar":"1","rightSidebarSlug":"main-right","footerSidebar":"0","footerSidebarSlug":null,"comments":"0","disqus":"0","pageStyles":""}}',
			'{"settings":{"defaultAvatar":"0","defaultBanner":"0","fixedBanner":"0","scrollBanner":"0","parallaxBanner":"0","background":"0","backgroundClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerInfo":"0","headerContent":"0","headerIconUrl":"","headerBanner":"0","headerGallery":"0","headerElements":"0","headerElementType":"","content":"1","contentTitle":"1","contentInfo":"0","contentSummary":"0","contentData":"1","maxCover":"0","contentSocial":"0","contentLabels":null,"contentAvatar":"0","contentBanner":"0","contentGallery":"0","contentClass":"","contentDataClass":"","styles":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","footerElements":"0","footerElementType":"","attributes":"0","attributesWithContent":"0","attributesOrder":"","attributeType":"","metaWrapClass":"","elements":"0","elementsBeforeContent":"0","elementsWithContent":"0","elementsOrder":"","elementType":"","boxWrapClass":"","boxWrapper":"","boxClass":"","widgets":"1","widgetsBeforeContent":"0","widgetsWithContent":"1","widgetsOrder":"","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":"","blocks":"0","blocksBeforeContent":"0","blocksWithContent":"0","blocksOrder":"","blockType":"","sidebars":"0","sidebarsBeforeContent":"0","sidebarsWithContent":"0","sidebarsOrder":"","sidebarType":"","topSidebar":"0","topSidebarSlugs":"","bottomSidebar":"0","bottomSidebarSlugs":"","leftSidebar":"0","leftSidebarSlug":"","rightSidebar":"0","rightSidebarSlug":"","footerSidebar":"0","footerSidebarSlug":null,"comments":"0","disqus":"0","pageStyles":""}}',
			'{"settings":{"defaultAvatar":"0","defaultBanner":"0","fixedBanner":"0","scrollBanner":"0","parallaxBanner":"0","background":"0","backgroundClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerInfo":"0","headerContent":"0","headerIconUrl":"","headerBanner":"0","headerGallery":"0","headerElements":"0","headerElementType":"","content":"1","contentTitle":"1","contentInfo":"0","contentSummary":"0","contentData":"1","maxCover":"0","contentSocial":"0","contentLabels":null,"contentAvatar":"0","contentBanner":"0","contentGallery":"0","contentClass":"","contentDataClass":"","styles":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","footerElements":"0","footerElementType":"","attributes":"0","attributesWithContent":"0","attributesOrder":"","attributeType":"","metaWrapClass":"","elements":"0","elementsBeforeContent":"0","elementsWithContent":"0","elementsOrder":"","elementType":"","boxWrapClass":"","boxWrapper":"","boxClass":"","widgets":"1","widgetsBeforeContent":"0","widgetsWithContent":"1","widgetsOrder":"","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":"","blocks":"0","blocksBeforeContent":"0","blocksWithContent":"0","blocksOrder":"","blockType":"","sidebars":"0","sidebarsBeforeContent":"0","sidebarsWithContent":"0","sidebarsOrder":"","sidebarType":"","topSidebar":"0","topSidebarSlugs":"","bottomSidebar":"0","bottomSidebarSlugs":"","leftSidebar":"0","leftSidebarSlug":"","rightSidebar":"0","rightSidebarSlug":"","footerSidebar":"0","footerSidebarSlug":null,"comments":"0","disqus":"0","pageStyles":""}}',
			'{"settings":{"defaultAvatar":"0","defaultBanner":"0","fixedBanner":"0","scrollBanner":"0","parallaxBanner":"0","background":"0","backgroundClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerInfo":"0","headerContent":"0","headerIconUrl":"","headerBanner":"0","headerGallery":"0","headerElements":"0","headerElementType":"","content":"1","contentTitle":"1","contentInfo":"0","contentSummary":"0","contentData":"1","maxCover":"0","contentSocial":"0","contentLabels":null,"contentAvatar":"0","contentBanner":"0","contentGallery":"0","contentClass":"","contentDataClass":"","styles":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","footerElements":"0","footerElementType":"","attributes":"0","attributesWithContent":"0","attributesOrder":"","attributeType":"","metaWrapClass":"","elements":"0","elementsBeforeContent":"0","elementsWithContent":"0","elementsOrder":"","elementType":"","boxWrapClass":"","boxWrapper":"","boxClass":"","widgets":"1","widgetsBeforeContent":"0","widgetsWithContent":"1","widgetsOrder":"","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":"","blocks":"0","blocksBeforeContent":"0","blocksWithContent":"0","blocksOrder":"","blockType":"","sidebars":"0","sidebarsBeforeContent":"0","sidebarsWithContent":"0","sidebarsOrder":"","sidebarType":"","topSidebar":"0","topSidebarSlugs":"","bottomSidebar":"0","bottomSidebarSlugs":"","leftSidebar":"0","leftSidebarSlug":"","rightSidebar":"0","rightSidebarSlug":"","footerSidebar":"0","footerSidebarSlug":null,"comments":"0","disqus":"0","pageStyles":""}}',
			'{"settings":{"defaultAvatar":"0","defaultBanner":"0","fixedBanner":"0","scrollBanner":"0","parallaxBanner":"0","background":"0","backgroundClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerInfo":"0","headerContent":"0","headerIconUrl":"","headerBanner":"0","headerGallery":"0","headerElements":"0","headerElementType":"","content":"1","contentTitle":"1","contentInfo":"0","contentSummary":"0","contentData":"1","maxCover":"0","contentSocial":"0","contentLabels":null,"contentAvatar":"0","contentBanner":"0","contentGallery":"0","contentClass":"","contentDataClass":"","styles":"","footer":"0","footerIcon":"0","footerIconClass":null,"footerIconUrl":"","footerTitle":"0","footerTitleData":"","footerInfo":"0","footerInfoData":"","footerContent":"0","footerContentData":"","footerElements":"0","footerElementType":"","attributes":"0","attributesWithContent":"0","attributesOrder":"","attributeType":"","metaWrapClass":"","elements":"0","elementsBeforeContent":"0","elementsWithContent":"0","elementsOrder":"","elementType":"","boxWrapClass":"","boxWrapper":"","boxClass":"","widgets":"1","widgetsBeforeContent":"0","widgetsWithContent":"1","widgetsOrder":"","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":"","blocks":"0","blocksBeforeContent":"0","blocksWithContent":"0","blocksOrder":"","blockType":"","sidebars":"0","sidebarsBeforeContent":"0","sidebarsWithContent":"0","sidebarsOrder":"","sidebarType":"","topSidebar":"0","topSidebarSlugs":"","bottomSidebar":"0","bottomSidebarSlugs":"","leftSidebar":"0","leftSidebarSlug":"","rightSidebar":"0","rightSidebarSlug":"","footerSidebar":"0","footerSidebarSlug":null,"comments":"0","disqus":"0","pageStyles":""}}'
		];

		$this->update( $this->cmgPrefix . 'cms_page', [ 'texture' => 'texture', 'title' => NULL, 'description' => $desc[ 0 ], 'data' => $setting[ 0 ] ], [ 'siteId' => $siteId, 'slug' => 'home' ] );
		$this->update( $this->cmgPrefix . 'cms_page', [ 'texture' => 'texture', 'title' => NULL, 'description' => $desc[ 1 ], 'data' => $setting[ 1 ] ], [ 'siteId' => $siteId, 'slug' => 'blog' ] );
		$this->update( $this->cmgPrefix . 'cms_page', [ 'texture' => 'texture', 'title' => NULL, 'description' => $desc[ 2 ], 'data' => $setting[ 2 ] ], [ 'siteId' => $siteId, 'slug' => 'search-pages' ] );
		$this->update( $this->cmgPrefix . 'cms_page', [ 'texture' => 'texture', 'title' => NULL, 'description' => $desc[ 3 ], 'data' => $setting[ 3 ] ], [ 'siteId' => $siteId, 'slug' => 'search-articles' ] );
		$this->update( $this->cmgPrefix . 'cms_page', [ 'texture' => 'texture', 'title' => NULL, 'description' => $desc[ 4 ], 'data' => $setting[ 4 ] ], [ 'siteId' => $siteId, 'slug' => 'search-posts' ] );
	}

	private function updatePagesContent() {

		$siteName = $this->site->name;

		$summary = [
			"Find tutorials specific to $siteName at Multisite. Our team and site members try to write the content as much descriptive as possible with relevant snapshots and pictures to explain the subject with better clarity. We focus on writing content in details explaining each and every point to share the depth of the subject so that anyone can easily follow and able to do the same as shared by us.",
			'Blog',
			"Browse $siteName Pages", "Browse $siteName Articles", "Browse $siteName Posts"
		];

		$seo = [
			[ null, "Find tutorials specific to $siteName at Multisite.", "Tutorials, Tutorial, Multisite, $siteName", 'index,follow' ],
			[ null, null, 'Multisite', 'index,follow' ],
			[ null, null, 'Multisite', 'index,follow' ],
			[ null, null, 'Multisite', 'index,follow' ],
			[ null, null, 'Multisite', 'index,follow' ]
		];

		$content = [
			"Find tutorials specific to $siteName at Multisite. Our team and site members try to write the content as much descriptive as possible with relevant snapshots and pictures to explain the subject with better clarity. We focus on writing content in details explaining each and every point to share the depth of the subject so that anyone can easily follow and able to do the same as shared by us.",
			null, null, null, null
		];

		// Templates
		$landingTemplate	= Template::findGlobalBySlugType( 'landing', CmsGlobal::TYPE_PAGE );
		$pageTemplate		= Template::findGlobalBySlugType( CoreGlobal::TEMPLATE_DEFAULT, CmsGlobal::TYPE_PAGE );
		$searchTemplate		= Template::findGlobalBySlugType( 'search', CmsGlobal::TYPE_PAGE );

		$homePageId		= Page::findBySlugType( 'home', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id;
		$blogPageId		= Page::findBySlugType( 'blog', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id;
		$searchPageId	= Page::findBySlugType( 'search-pages', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id;
		$searchArtiId	= Page::findBySlugType( 'search-articles', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id;
		$searchPostId	= Page::findBySlugType( 'search-posts', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] )->id;

		$this->update( $this->cmgPrefix . 'cms_model_content', [ 'templateId' => $landingTemplate->id, 'bannerId' => null, 'summary' => $summary[ 0 ], 'seoName' => $seo[ 0 ][ 0 ], 'seoDescription' => $seo[ 0 ][ 1 ], 'seoKeywords' => $seo[ 0 ][ 2 ], 'seoRobot' => $seo[ 0 ][ 3 ], 'content' => $content[ 0 ] ], [ 'parentId' => $homePageId, 'parentType' => 'page' ] );
		$this->update( $this->cmgPrefix . 'cms_model_content', [ 'templateId' => $pageTemplate->id, 'bannerId' => null, 'summary' => $summary[ 1 ], 'seoName' => $seo[ 1 ][ 0 ], 'seoDescription' => $seo[ 1 ][ 1 ], 'seoKeywords' => $seo[ 1 ][ 2 ], 'seoRobot' => $seo[ 1 ][ 3 ], 'content' => $content[ 1 ] ], [ 'parentId' => $blogPageId, 'parentType' => 'page' ] );
		$this->update( $this->cmgPrefix . 'cms_model_content', [ 'templateId' => $searchTemplate->id, 'bannerId' => null, 'summary' => $summary[ 2 ], 'seoName' => $seo[ 2 ][ 0 ], 'seoDescription' => $seo[ 2 ][ 1 ], 'seoKeywords' => $seo[ 2 ][ 2 ], 'seoRobot' => $seo[ 2 ][ 3 ], 'content' => $content[ 2 ] ], [ 'parentId' => $searchArtiId, 'parentType' => 'page' ] );
		$this->update( $this->cmgPrefix . 'cms_model_content', [ 'templateId' => $searchTemplate->id, 'bannerId' => null, 'summary' => $summary[ 3 ], 'seoName' => $seo[ 3 ][ 0 ], 'seoDescription' => $seo[ 3 ][ 1 ], 'seoKeywords' => $seo[ 3 ][ 2 ], 'seoRobot' => $seo[ 3 ][ 3 ], 'content' => $content[ 3 ] ], [ 'parentId' => $searchPostId, 'parentType' => 'page' ] );
		$this->update( $this->cmgPrefix . 'cms_model_content', [ 'templateId' => $searchTemplate->id, 'bannerId' => null, 'summary' => $summary[ 4 ], 'seoName' => $seo[ 4 ][ 0 ], 'seoDescription' => $seo[ 4 ][ 1 ], 'seoKeywords' => $seo[ 4 ][ 2 ], 'seoRobot' => $seo[ 4 ][ 3 ], 'content' => $content[ 4 ] ], [ 'parentId' => $searchPostId, 'parentType' => 'page' ] );
	}

	private function insertWidgets() {

		$site	= $this->site;
		$master	= $this->master;

		$status	= ObjectData::STATUS_ACTIVE;
		$vis	= ObjectData::VISIBILITY_PUBLIC;

		$siteId = $this->site->id;

		$pagecTemplate = Template::findGlobalBySlugType( 'page-card', CmsGlobal::TYPE_WIDGET );
		$pagesTemplate = Template::findGlobalBySlugType( 'page-search', CmsGlobal::TYPE_WIDGET );

		$articlecTemplate	= Template::findGlobalBySlugType( 'article-card', CmsGlobal::TYPE_WIDGET );
		$articlesTemplate	= Template::findGlobalBySlugType( 'article-search', CmsGlobal::TYPE_WIDGET );

		$postcTemplate	= Template::findGlobalBySlugType( 'post-card', CmsGlobal::TYPE_WIDGET );
		$postbTemplate	= Template::findGlobalBySlugType( 'post-box', CmsGlobal::TYPE_WIDGET );
		$postsTemplate	= Template::findGlobalBySlugType( 'post-search', CmsGlobal::TYPE_WIDGET );
		$posthTemplate	= Template::findGlobalBySlugType( 'post-home', CmsGlobal::TYPE_WIDGET );

		$columns = [ 'id', 'siteId', 'templateId', 'avatarId', 'bannerId', 'videoId', 'galleryId', 'createdBy', 'modifiedBy', 'name', 'slug', 'type', 'icon', 'texture', 'title', 'description', 'classPath', 'link', 'status', 'visibility', 'order', 'pinned', 'featured', 'createdAt', 'modifiedAt', 'htmlOptions', 'summary', 'content', 'data' ];

		$models = [
			// Page Widgets
			[ intval( $siteId . '101' ), $site->id, $pagesTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Search Site Pages', 'search-site-pages', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Search Pages', 'It search pages published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-page\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '102' ), $site->id, $pagecTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Recent Site Pages', 'recent-site-pages', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Recent Pages', 'It shows the recent pages published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"0","headerTitle":"1","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"card-page-wrap\" }","singleOptions":"{ \"class\": \"card card-default card-page\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-card widget-card-recent widget-card-page\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"0","paging":"0","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"5","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"120","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '103' ), $site->id, $pagecTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Popular Site Pages', 'popular-site-pages', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Popular Pages', 'It shows the popular pages published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"0","headerTitle":"1","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"card-page-wrap\" }","singleOptions":"{ \"class\": \"card card-default card-page\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"popular","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-card widget-card-popular widget-card-page\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"0","paging":"0","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"5","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"120","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			// Article Widgets
			[ intval( $siteId . '201' ), $site->id, $articlesTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Search Site Articles', 'search-site-articles', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Search Articles', 'It search articles published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-article\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '202' ), $site->id, $articlesTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Author Site Articles', 'author-site-articles', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Author Articles', 'It shows the author articles published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-author widget-box-search-article\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '203' ), $site->id, $articlesTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Archive Site Articles', 'archive-site-articles', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Archive Articles', 'It shows the archive articles according to selected month and published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-archive widget-box-search-article\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '204' ), $site->id, $articlecTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Recent Site Articles', 'recent-site-articles', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Recent Articles', 'It shows the recent articles published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"0","headerTitle":"1","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"card-page-wrap\" }","singleOptions":"{ \"class\": \"card card-default card-page\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-card widget-card-recent widget-card-article\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"0","paging":"0","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"5","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"120","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '205' ), $site->id, $articlecTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Popular Site Articles', 'popular-site-articles', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Popular Articles', 'It shows the popular articles published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"0","headerTitle":"1","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"card-page-wrap\" }","singleOptions":"{ \"class\": \"card card-default card-page\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"popular","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-card widget-card-popular widget-card-article\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"0","paging":"0","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"5","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"120","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			// Post Widgets
			[ intval( $siteId . '301' ), $site->id, $posthTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Home Posts', 'home-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Home Posts', 'It shows posts published on all sites on landing page.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"route":"","allPath":"all","singlePath":"single","wrapperOptions":"{ \"class\": \"box-home-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-home col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":"0","tagParam":"0","wrap":"1","options":"{ \"class\": \"widget-basic widget-box-home widget-box-home-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","showAllPath":"0","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"0","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '302' ), $site->id, $postsTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Search Site Posts', 'search-site-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Search Posts', 'It search posts published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '303' ), $site->id, $postsTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Author Site Posts', 'author-site-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Author Posts', 'It shows the author posts published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-author widget-box-search-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '304' ), $site->id, $postsTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Archive Site Posts', 'archive-site-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Archive Posts', 'It shows the archive posts according to selected month and published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-archive widget-box-search-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '305' ), $site->id, $postcTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Recent Site Posts', 'recent-site-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Recent Posts', 'It shows the recent posts published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"0","headerTitle":"1","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"card-page-wrap\" }","singleOptions":"{ \"class\": \"card card-default card-page\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-card widget-card-recent widget-card-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"0","paging":"0","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"5","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"120","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '306' ), $site->id, $postcTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Popular Site Posts', 'popular-site-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Popular Posts', 'It shows the popular posts published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"0","headerTitle":"1","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"card-page-wrap\" }","singleOptions":"{ \"class\": \"card card-default card-page\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"popular","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-card widget-card-popular widget-card-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"0","paging":"0","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"5","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"120","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '307' ), $site->id, $postbTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Related Site Posts', 'related-site-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Related Posts', 'It shows the related posts published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"1","headerIcon":"0","headerTitle":"1","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"card-page-wrap\" }","singleOptions":"{ \"class\": \"card card-default card-page\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"related","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-card widget-card-related widget-card-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"0","paging":"0","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"5","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"120","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '308' ), $site->id, $postsTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Category Posts', 'category-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Category Posts', 'It search the category posts published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"category","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '309' ), $site->id, $postsTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Tag Posts', 'tag-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Tag Posts', 'It search the tag posts published on active site.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x4 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"tag","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":false,"tagParam":false,"wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","allPath":"all","showAllPath":"0","singlePath":"single","route":"","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"12","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ],
			[ intval( $siteId . '310' ), $site->id, $postbTemplate->id, NULL, NULL, NULL, NULL, $master->id, $master->id, 'Similar Posts', 'similar-posts', CmsGlobal::TYPE_WIDGET, 'icon', 'texture', 'Similar Posts', 'It shows the similar posts having same category or tag.', NULL, NULL, 16000, 1500,0,0,0,DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"route":"","allPath":"all","singlePath":"single","wrapperOptions":"{ \"class\": \"box-page-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page col col12x3\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"similar","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":"0","tagParam":"0","wrap":"1","options":"{ \"class\": \"widget-basic widget-box widget-box-similar widget-box-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","showAllPath":"0","pagination":"0","paging":"0","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"4","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"180","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}' ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_object', $columns, $models );
	}

	private function insertPageWidgetMappings() {

		$siteId = $this->site->id;

		$homePage		= Page::findBySlugType( 'home', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] );
		$blogPage		= Page::findBySlugType( 'blog', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] );
		$searchPage		= Page::findBySlugType( 'search-pages', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] );
		$searchPost		= Page::findBySlugType( 'search-posts', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] );
		$searchArticle	= Page::findBySlugType( 'search-articles', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] );

		$homePostsWidget	= Widget::findBySlugType( 'home-posts', CmsGlobal::TYPE_WIDGET, [ 'siteId' => $this->site->id ] );
		$searchPageWidget	= Widget::findBySlugType( 'search-site-pages', CmsGlobal::TYPE_WIDGET, [ 'siteId' => $this->site->id ] );
		$searchPostWidget	= Widget::findBySlugType( 'search-site-posts', CmsGlobal::TYPE_WIDGET, [ 'siteId' => $this->site->id ] );
		$searchArtiWidget	= Widget::findBySlugType( 'search-site-articles', CmsGlobal::TYPE_WIDGET, [ 'siteId' => $this->site->id ] );

		$columns = [ 'id', 'modelId', 'parentId', 'parentType', 'type', 'order', 'active', 'pinned', 'featured', 'nodes' ];

		$mappings = [
			[ intval( $siteId . '0001' ), $homePostsWidget->id, $homePage->id, 'page', CmsGlobal::TYPE_WIDGET, 0, 1, 0, 0, NULL ],
			[ intval( $siteId . '0002' ), $searchPostWidget->id, $blogPage->id, 'page', CmsGlobal::TYPE_WIDGET, 0, 1, 0, 0, NULL ],
			[ intval( $siteId . '0003' ), $searchPageWidget->id, $searchPage->id, 'page', CmsGlobal::TYPE_WIDGET, 0, 1, 0, 0, NULL ],
			[ intval( $siteId . '0004' ), $searchPostWidget->id, $searchPost->id, 'page', CmsGlobal::TYPE_WIDGET, 0, 1, 0, 0, NULL ],
			[ intval( $siteId . '0005' ), $searchArtiWidget->id, $searchArticle->id, 'page', CmsGlobal::TYPE_WIDGET, 0, 1, 0, 0, NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_model_object', $columns, $mappings );
	}

	private function insertPageBlockMappings() {

		$siteId = $this->site->id;

		$homePage = Page::findBySlugType( 'home', CmsGlobal::TYPE_PAGE, [ 'siteId' => $this->site->id ] );

		$columns = [ 'id', 'modelId', 'parentId', 'parentType', 'type', 'order', 'active', 'pinned', 'featured', 'nodes' ];

		$mappings = [
			//[ intval( $siteId . '1001' ), 10202, $homePage->id, 'page', 'block', 0, 1, 0, 0, NULL ],
		];

		$this->batchInsert( $this->cmgPrefix . 'core_model_object', $columns, $mappings );
	}

	private function insertSidebars() {

		$site	= $this->site;
		$master	= $this->master;

		$status	= ObjectData::STATUS_ACTIVE;
		$vis	= ObjectData::VISIBILITY_PUBLIC;

		$siteId = $this->site->id;

		$hTemplate	= Template::findGlobalBySlugType( CmsGlobal::TEMPLATE_SIDEBAR_VERTICAL, CmsGlobal::TYPE_SIDEBAR );
		$vTemplate	= Template::findGlobalBySlugType( CmsGlobal::TEMPLATE_SIDEBAR_HORIZONTAL, CmsGlobal::TYPE_SIDEBAR );

		$columns = [ 'id', 'siteId', 'templateId', 'avatarId', 'bannerId', 'createdBy', 'modifiedBy', 'name', 'slug', 'type', 'icon', 'description', 'status', 'visibility', 'classPath', 'createdAt', 'modifiedAt', 'htmlOptions', 'content', 'data' ];

		$models = [
			[ intval( $siteId . '501' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Main Top', 'main-top', CmsGlobal::TYPE_SIDEBAR, NULL, 'Main sidebar used at top of landing page.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '502' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Main Right', 'main-right', CmsGlobal::TYPE_SIDEBAR, NULL, 'Main sidebar used at right of landing page.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '503' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Main Bottom', 'main-bottom', CmsGlobal::TYPE_SIDEBAR, NULL, 'Main sidebar used at bottom of landing page.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '504' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Main Left', 'main-left', CmsGlobal::TYPE_SIDEBAR, NULL, 'Main sidebar used at left of landing page.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '505' ), $site->id, $hTemplate->id, NULL, NULL, $master->id, $master->id, 'Main Footer', 'main-footer', CmsGlobal::TYPE_SIDEBAR, NULL, 'Main sidebar used on public footer.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '506' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Page Top', 'page-top', CmsGlobal::TYPE_SIDEBAR, NULL, 'Page sidebar used at top of page.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '507' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Page Right', 'page-right', CmsGlobal::TYPE_SIDEBAR, NULL, 'Page sidebar used at right of page.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '508' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Page Bottom', 'page-bottom', CmsGlobal::TYPE_SIDEBAR, NULL, 'Page sidebar used at bottom of page.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '509' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Page Left', 'page-left', CmsGlobal::TYPE_SIDEBAR, NULL, 'Page sidebar used at left of page.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '510' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Post Top', 'post-top', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at top of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '511' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Post Right', 'post-right', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at right of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '512' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Post Bottom', 'post-bottom', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at bottom of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '513' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Post Left', 'post-left', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at left of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '514' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Category Top', 'category-top', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at top of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '515' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Category Right', 'category-right', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at right of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '516' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Category Bottom', 'category-bottom', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at bottom of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '517' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Category Left', 'category-left', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at left of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '518' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Tag Top', 'tag-top', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at top of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '519' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Tag Right', 'tag-right', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at right of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '520' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Tag Bottom', 'tag-bottom', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at bottom of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '521' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Tag Left', 'tag-left', CmsGlobal::TYPE_SIDEBAR, NULL, 'Post sidebar used at left of post.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '522' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Article Top', 'article-top', CmsGlobal::TYPE_SIDEBAR, NULL, 'Article sidebar used at top of article.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '523' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Article Right', 'article-right', CmsGlobal::TYPE_SIDEBAR, NULL, 'Article sidebar used at right of article.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '524' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Article Bottom', 'article-bottom', CmsGlobal::TYPE_SIDEBAR, NULL, 'Article sidebar used at bottom of article.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '525' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Article Left', 'article-left', CmsGlobal::TYPE_SIDEBAR, NULL, 'Article sidebar used at left of article.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '526' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Form Top', 'form-top', CmsGlobal::TYPE_SIDEBAR, NULL, 'Form sidebar used at top of form.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '527' ), $site->id, $hTemplate->id, NULL ,NULL, $master->id, $master->id, 'Form Right', 'form-right', CmsGlobal::TYPE_SIDEBAR, NULL, 'Form sidebar used at right of form.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '528' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Form Bottom', 'form-bottom', CmsGlobal::TYPE_SIDEBAR, NULL, 'Form sidebar used at bottom of form.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ],
			[ intval( $siteId . '529' ), $site->id, $vTemplate->id, NULL, NULL, $master->id, $master->id, 'Form Left', 'form-left', CmsGlobal::TYPE_SIDEBAR, NULL, 'Form sidebar used at left of form.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, '{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":"","widgets":"1","widgetType":"","widgetWrapClass":"","widgetWrapper":"","widgetClass":""}}' ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_object', $columns, $models );
	}

	private function insertMenus() {

		$master	= $this->master;
		$status	= ObjectData::STATUS_ACTIVE;
		$vis	= ObjectData::VISIBILITY_PUBLIC;

		$siteId = $this->site->id;

		$columns = [ 'id', 'siteId', 'templateId', 'avatarId', 'bannerId', 'createdBy', 'modifiedBy', 'name', 'slug', 'type', 'icon', 'description', 'status', 'visibility', 'classPath', 'createdAt', 'modifiedAt', 'htmlOptions', 'content', 'data' ];

		$models = [
			[ intval( $siteId . '701' ), $siteId, NULL, NULL ,NULL, $master->id, $master->id, 'Main', 'main', CmsGlobal::TYPE_MENU, NULL, 'Main Menu used on landing header.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ intval( $siteId . '702' ), $siteId, NULL, NULL ,NULL, $master->id, $master->id, 'Secondary', 'secondary', CmsGlobal::TYPE_MENU, NULL, 'Secondary Menu used on public header.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ intval( $siteId . '703' ), $siteId, NULL, NULL, NULL, $master->id, $master->id, 'Links', 'links', CmsGlobal::TYPE_MENU, NULL, 'Links menu used on footer.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ intval( $siteId . '704' ), $siteId, NULL, NULL, NULL, $master->id, $master->id, 'Page', 'page', CmsGlobal::TYPE_MENU, NULL, 'Page menu used on footer, system pages.', $status, $vis, NULL, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'core_object', $columns, $models );
	}

	private function insertBlockWidgetMappings() {

		$siteId = $this->site->id;

		$mainSidebar	= Sidebar::findBySlugType( 'main-right', CmsGlobal::TYPE_SIDEBAR, [ 'siteId' => $this->site->id ] );
		$pgrSidebar		= Sidebar::findBySlugType( 'page-right', CmsGlobal::TYPE_SIDEBAR, [ 'siteId' => $this->site->id ] );
		$psrSidebar		= Sidebar::findBySlugType( 'post-right', CmsGlobal::TYPE_SIDEBAR, [ 'siteId' => $this->site->id ] );
		$frmrSidebar	= Sidebar::findBySlugType( 'form-right', CmsGlobal::TYPE_SIDEBAR, [ 'siteId' => $this->site->id ] );

		$popsPosts	= Widget::findBySlugType( 'popular-site-posts', CmsGlobal::TYPE_WIDGET );
		$recsPosts	= Widget::findBySlugType( 'recent-site-posts', CmsGlobal::TYPE_WIDGET );

		$columns = [ 'id', 'modelId', 'parentId', 'parentType', 'type', 'order', 'active', 'pinned', 'featured', 'nodes' ];

		$mappings = [
			//[ intval( $siteId . '5001' ), 10101, $mainSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			[ intval( $siteId . '5002' ), $popsPosts->id, $mainSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ intval( $siteId . '5003' ), 10102, $mainSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ intval( $siteId . '5011' ), 10101, $pgrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			[ intval( $siteId . '5012' ), $recsPosts->id, $pgrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ intval( $siteId . '5013' ), 10102, $pgrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ intval( $siteId . '5021' ), 10101, $psrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			[ intval( $siteId . '5022' ), $recsPosts->id, $psrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ intval( $siteId . '5023' ), 10102, $psrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ intval( $siteId . '5031' ), 10101, $frmrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			[ intval( $siteId . '5032' ), $recsPosts->id, $frmrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
			//[ intval( $siteId . '5033' ), 10102, $frmrSidebar->id, 'sidebar', 'widget', 0, 1, 0, 0, NULL ],
		];

		$this->batchInsert( $this->cmgPrefix . 'core_model_object', $columns, $mappings );
	}

	private function updateWidgets() {

		$site = $this->site;

		$settings = [
			'{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"route":"","allPath":"all","singlePath":"single","wrapperOptions":"{ \"class\": \"box-home-wrap\" }","singleOptions":"{ \"class\": \"box box-default box-home\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":"0","tagParam":"0","wrap":"1","options":"{ \"class\": \"widget-basic widget-box-home widget-box-home-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","showAllPath":"0","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"10","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"250","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}',
			'{"settings":{"defaultAvatar":"0","defaultBanner":"0","bkg":"0","bkgClass":"","texture":"0","header":"0","headerIcon":"0","headerTitle":"0","headerIconUrl":"","content":"1","contentTitle":"0","contentInfo":"0","contentSummary":"0","contentData":"0","contentClass":"","contentDataClass":"","styles":"","attributes":"0","attributeType":"","metaWrapClass":""},"config":{"route":"","allPath":"all","singlePath":"single","wrapperOptions":"{ \"class\": \"box-page-search-wrap row max-cols-50\" }","singleOptions":"{ \"class\": \"box box-default box-page-search col col12x3 row\" }","excludeParams":"{\"params\": [ \"slug\" ] }","widget":"recent","texture":"","defaultBanner":"0","authorParam":"0","categoryParam":"0","tagParam":"0","wrap":"1","options":"{ \"class\": \"widget-basic widget-box-search widget-box-search-post\" }","wrapSingle":"1","singleWrapper":"div","basePath":"","showAllPath":"0","pagination":"1","paging":"1","nextLabel":"&raquo;","prevLabel":"&laquo;","limit":"16","ajaxPagination":"0","ajaxPageApp":"pagination","ajaxPageController":"page","ajaxPageAction":"getPage","ajaxUrl":"","textLimit":"120","excludeMain":"0","siteModels":"1","wrapper":"div","loadAssets":"0","templateDir":null,"template":"default","factory":true,"cache":false,"cacheDb":false,"cacheFile":false,"autoload":"0","autoloadTemplate":"autoload","autoloadApp":"autoload","autoloadController":"autoload","autoloadAction":"autoload","autoloadUrl":""}}'
		];

		$this->update( $this->cmgPrefix . 'core_object', [ 'data' => $settings[ 0 ] ], [ 'siteId' => $site->id, 'slug' => 'home-posts', 'type' => 'widget' ] );
		$this->update( $this->cmgPrefix . 'core_object', [ 'data' => $settings[ 1 ] ], [ 'siteId' => $site->id, 'slug' => 'search-site-posts', 'type' => 'widget' ] );
	}

	private function insertLinks() {

		$site	= $this->site;
		$master	= $this->master;

		$siteId = $this->site->id;

		$columns = [ 'id', 'siteId', 'pageId', 'createdBy', 'modifiedBy', 'name', 'title', 'url', 'type', 'icon', 'order', 'absolute', 'user', 'createdAt', 'modifiedAt', 'htmlOptions', 'urlOptions', 'data' ];

		$links = [
			[ '1' . intval( $siteId . '01' ), $site->id, NULL, $master->id, $master->id, 'Home', NULL, '/', 'site', NULL, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ '1' . intval( $siteId . '02' ), $site->id, NULL, $master->id, $master->id, 'Login', NULL, 'https://www.tutorials24x7.com/login', 'site', NULL, 0, 1, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ '1' . intval( $siteId . '03' ), $site->id, NULL, $master->id, $master->id, 'Register', NULL, 'https://www.tutorials24x7.com/register', 'site', NULL, 0, 1, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ '1' . intval( $siteId . '04' ), $site->id, NULL, $master->id, $master->id, 'About', 'About Us', 'https://www.tutorials24x7.com/about-us', 'site', NULL, 0, 1, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ '1' . intval( $siteId . '05' ), $site->id, NULL, $master->id, $master->id, 'Terms', 'Terms & Conditions', 'https://www.tutorials24x7.com/terms', 'site', NULL, 0, 1, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ '1' . intval( $siteId . '06' ), $site->id, NULL, $master->id, $master->id, 'Privacy', 'Privacy Policy', 'https://www.tutorials24x7.com/privacy', 'site', NULL, 0, 1, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ '1' . intval( $siteId . '07' ), $site->id, NULL, $master->id, $master->id, 'Blog', 'Blog', '/blog/search', 'site', NULL, 0, 0, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ '1' . intval( $siteId . '08' ), $site->id, NULL, $master->id, $master->id, 'Contact Us', NULL, 'https://www.tutorials24x7.com/contact-us', 'site', NULL, 0, 1, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ '1' . intval( $siteId . '09' ), $site->id, NULL, $master->id, $master->id, 'Feedback', NULL, 'https://www.tutorials24x7.com/feedback', 'site', NULL, 0, 1, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ],
			[ '1' . intval( $siteId . '10' ), $site->id, NULL, $master->id, $master->id, 'Testimonial', NULL, 'https://www.tutorials24x7.com/testimonial', 'site', NULL, 0, 1, 0, DateUtil::getDateTime(), DateUtil::getDateTime(), NULL, NULL, NULL ]
		];

		$this->batchInsert( $this->cmgPrefix . 'cms_link', $columns, $links );
	}

	private function insertLinkMappings() {

		$siteId = $this->site->id;

		$mainMenu	= Menu::findBySlugType( 'main', CmsGlobal::TYPE_MENU, [ 'siteId' => $this->site->id ] );
		$secMenu	= Menu::findBySlugType( 'secondary', CmsGlobal::TYPE_MENU, [ 'siteId' => $this->site->id ] );
		$lnkMenu	= Menu::findBySlugType( 'links', CmsGlobal::TYPE_MENU, [ 'siteId' => $this->site->id ] );

		$columns = [ 'id', 'modelId', 'parentId', 'parentType', 'type', 'order', 'active' ];

		$mappings = [
			[ intval( $siteId . '001' ), '1' . intval( $siteId . '01' ), $mainMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '002' ), '1' . intval( $siteId . '04' ), $mainMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '003' ), '1' . intval( $siteId . '05' ), $mainMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '004' ), '1' . intval( $siteId . '06' ), $mainMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '005' ), '1' . intval( $siteId . '01' ), $secMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '006' ), '1' . intval( $siteId . '04' ), $secMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '007' ), '1' . intval( $siteId . '05' ), $secMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '008' ), '1' . intval( $siteId . '06' ), $secMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '009' ), '1' . intval( $siteId . '07' ), $lnkMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '010' ), '1' . intval( $siteId . '08' ), $lnkMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '011' ), '1' . intval( $siteId . '09' ), $lnkMenu->id, 'menu', NULL, 0, 1 ],
			[ intval( $siteId . '012' ), '1' . intval( $siteId . '10' ), $lnkMenu->id, 'menu', NULL, 0, 1 ]
		];

		$this->batchInsert( $this->cmgPrefix . 'cms_model_link', $columns, $mappings );
	}

	private function updateAutoIncs() {

		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_site AUTO_INCREMENT = 10001" );
		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_site_member AUTO_INCREMENT = 1000001" );

		$this->execute( "ALTER TABLE fxs_slider AUTO_INCREMENT = 100001" );
		$this->execute( "ALTER TABLE fxs_slide AUTO_INCREMENT = 1000001" );

		$this->execute( "ALTER TABLE {$this->cmgPrefix}cms_page AUTO_INCREMENT = 1000001" );
		$this->execute( "ALTER TABLE {$this->cmgPrefix}cms_model_content AUTO_INCREMENT = 1000001" );

		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_object AUTO_INCREMENT = 1000001" );
		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_model_object AUTO_INCREMENT = 2000001" );
		$this->execute( "ALTER TABLE {$this->cmgPrefix}cms_link AUTO_INCREMENT = 100001" );
		$this->execute( "ALTER TABLE {$this->cmgPrefix}cms_model_link AUTO_INCREMENT = 1000001" );

		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_form AUTO_INCREMENT = 1000001" );
		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_form_field AUTO_INCREMENT = 1000001" );

		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_template AUTO_INCREMENT = 1000001" );

		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_category AUTO_INCREMENT = 1000001" );
		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_model_category AUTO_INCREMENT = 1000001" );
		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_option AUTO_INCREMENT = 1000001" );
		$this->execute( "ALTER TABLE {$this->cmgPrefix}core_model_option AUTO_INCREMENT = 1000001" );
	}

	public function down() {

		echo "m181221_022751_multi will be deleted with m160621_014408_core.\n";
	}

}
