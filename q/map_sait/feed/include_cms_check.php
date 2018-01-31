<?
//CMS
function curl_redir_exec($ch) {
		static $curl_loops = 0;
		static $curl_max_loops = 3; # Максимальное количество перебросов.
		if ($curl_loops >= $curl_max_loops) {
				$curl_loops = 0;
				return FALSE;
			}
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		list($header, $data) = explode("\n\n", $data, 2);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code == 301 || $http_code == 302) {
				$matches = array();
				preg_match('/Location:(.*?)\n/', $header, $matches);
				$url = @parse_url(trim(array_pop($matches)));
				if (!$url) {
					$curl_loops = 0;
					return $data;
					}
				$last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
				if (!$url['scheme'])
						$url['scheme'] = $last_url['scheme'];
				if (!$url['host'])
						$url['host'] = $last_url['host'];
				if (!$url['path'])
						$url['path'] = $last_url['path'];
				$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');
				curl_setopt($ch, CURLOPT_URL, $new_url);
				return curl_redir_exec($ch);
		} else {
				$curl_loops=0;
				return $data;
		}
}

function grab($site) {
		if (function_exists('mb_detect_encoding')){
			if(mb_detect_encoding($site) != "ASCII"){ //если сайт в кириллице переводим в punycode
				include("http://xtoolza.info/q/cms/idna_convert.class.php");
				$IDN = new idna_convert(array('idn_version' => '2008'));
				$site=$IDN->encode($site);
			}
		}
		$ch = curl_init();
		$user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2062.124 Safari/537.36";
		curl_setopt($ch, CURLOPT_URL, $site);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		$data = curl_exec($ch); /* curl_exec($ch); */
		curl_close($ch);
		if ($data)
			return $data;
		else
			return FALSE;
}

function check($html) {
		$cms = array(
				"1minute.website SaaS" => array('1minute.website","type'),
				"GoPhotoWeb SaaS" => array('type="text/css" href="http://cdn.gophotoweb.com/saas','text/javascript" src="http://cdn.gophotoweb.com/saas','target="_blank">сайт от gophotoweb',"cdn_paths.modules = 'http://cdn.gophotoweb.com"),
				"2z Project" => array('name="Generator" content="2z project','/2z/includes/js/functions.js','/2z/includes/js/ajax.js','Powered by CMS 2z project'),
				"3dcart" => array('Software by <a href="http://www.3dcart.com">3dcart</a>','!--START: 3dcart stats--','!--END: 3dcart stats--'),
				"a5 SaaS" => array('!-- siteByName_','img src="/img/zones/a5.ru/site_copyright.png','Создано на конструкторе сайтов - A5.ru'),
				"ABCP SaaS" => array('stylesheet" href="//astatic.abcp.ru','Этот портал работает на платформе ABCP'),
				"ABO CMS" => array("Design and programming (for ABO.CMS)","ABO.CMS"),
				"Absolute CMS" => array("/engine_lib/flash/swfobject.js"),
				"Adobe CQ5" => array('$CQ.getScript(','$CQ(function()','"jquery": "../CQ/main"','/js/CQ/main.js"','/cq5/css/main.css','/cq5/css/print.css'),
				"AdVantShop.NET" => array('Работает на <a data-cke-saved-href="http://www.advantshop.net" href="http://www.advantshop.net"','generator" content="AdVantShop'),
				"Admin CMS" => array('http://www.admin-cms.com" target="_blank" class="v">"Admin CMS'),
				"Adventor CMS" => array('name="engine-copyright" content="Adventor CMS','if (player == "adventor"','Разработка сайта - Adventor Lab'),
				"Ai-CMS" => array('rel="stylesheet" href="/ai_css/','img src="/ai_img/',"var Images=['/ai_fill",'img src=/ai_fill/','img src= "ai_img/','img src="ai_fill','background:url(/ai_img/','alt="" src="/ai_fill/','alt="" src="ai_fill/','ackground-image: url(/ai_img','" src="/ai_fill/Image'),
				"AllaraCMS" => array('author" content="Allara-Studio','http://www.allara-studio.ru" target="_blank">Сделано в'),
				"Alltrades SaaS" => array('type="text/css" href="https://www.alltrades.ru/css1/preloaders/preloader1.css', 'http://www.alltrades.ru/js/jquery.js"></script>','onclick="alltrades_shop.add_to_basket','onclick="alltrades_shop.show_item_modal','script src="//alltrades.ru/js/jquery.slicknav.min.js'),
				"almaCMS" => array("LINK href='well/templates/"),
				"AltaDoc CMS" => array('* AltaDoc-CMS',"$.cookie('altadoc'",'Copyright (c) AltaDoc Group',"'basket_cookie']='altadoc_basket",'"_blank" href="http://altadoc.ru/">&amp; AltaDoc Group','//ALTADOC_COPYRIGHT_TEMPLATE','div id="altadoc-loading"'),
				"AltConstructor" => array('href="http://altsolution.ua">Дизайн сайта','Перейти на сайт студии AltSolution','name="generator" content="AltConstructor','href="http://altsolution.ua" target="_blank"','name="author" content="AltSolution'),
				"Anchor CMS" => array('name="generator" content="Anchor CMS','script src="/anchor/views/assets'),
				"AsciiDoc" => array('meta name="generator" content="AsciiDoc'),
				"Altedit SaaS" => array('name="altedit_widget_header_menu','class="altedit_widget"','name="altedit_region','class="altedit-row"','Разработано на платформе &quot;<a href="http://altedit.ru"','.alteditBlockWrapper'),
				"Ametys" => array('content="Ametys','content="Anyware Technologies','div id="ametys-cms-zone'),
				"AmieCMS" => array('Сайт работает на <a href="http://amiecms.ru','target="_blank">AmieCMS'),
				"Amiro CMS" => array("/amiro_sys_css.php?","/amiro_sys_js.php?","-= Amiro.CMS (c) =-","Работает на Amiro.CMS",'Сайт работает на Amiro.CMS','content="Amiro"'),
				"AMPcms" => array('amp_js_init'),
				"Amparo" => array('content="Made by Amparo'),
				"Apache Lenya" => array('<alt="Built with Apache Lenya"','content="Apache Lenya'),
				"ARTpublicator 3" => array('content="К.А. АРТполитика','Создано в <strong>АРТ</strong><span>политике'),
				"Atlassian Confluence" => array('owered by <a href="atlassian.com/software/confluence'),
				"Atlassian Jira" => array('Powered by <a href=atlassian.com/software/jira'),
				"Atilekt.CMS" => array('script src="/atilektcms/','src="/files/img/atilekt.png','Content management system</a> &mdash; ATILEKT.CMS','script type="text/javascript" src="/atilektcms','href="http://www.atilekt.ru/">Разработка сайта</a>','href="http://www.atilekt.ru" target="_blank">Разработка сайта','a href="http://cms.atilekt.com">Система управления сайтом','href="http://www.atilekt.ru/">Создание сайта','Система управления сайтом</a> — Atilekt.CMS'),
				"Ave CMS" => array('var aveabspath ='),
				"Avenue Shop" => array('работает на системе &laquo;Avenue Shop','Система Avenuesoft.ru &mdash;','Developed by IT Avenue','href="http://www.it-avenue.ru">Создание сайта','It-avenue.ru - создание и продвижение сайтов'),
				"AxCMS" => array('Build and published by AxCMS.net','content="Axinom'),
				"Backdrop" => array('<script>BackDrop.show','BackDrop.js"></script>'),
				"Bagira.CMS" => array('Сайт разработали Махогани','Разработано в «Махогани групп','title="Махогани-групп">разработка сайтов','Группа разработчиков Махогани','Махогани групп»"/></a><br/>Создание сайта'),
				"Banshee" => array('Built upon the <a href="http://banshee-php.org'),
				"Bazium SaaS" => array("class='no-js' lang='ru' ng-app='bazium'","<!-- %title Bazium -->","angular.module('bazium')"),
				"Bigcommerce" => array('shortcut icon" href="http://cdn2.bigcommerce.com/',"'platform':'bigcommerce'",'link rel="shortcut icon" href="http://cdn3.bigcommerce.com','link rel="shortcut icon" href="http://cdn4.bigcommerce.com','link rel="shortcut icon" href="http://cdn1.bigcommerce.com/','link rel="shortcut icon" href="http://cdn2.bigcommerce.com/','link href="http://cdn1.bigcommerce.com/','link href="http://cdn3.bigcommerce.com/'),
				"Bigace" => array('Работает на BIGACE','Site is running BIGACE','meta name="generator" content="BIGACE'),
				"Bigace" => array('Работает на BIGACE','Site is running BIGACE','meta name="generator" content="BIGACE'),
				"Biggo SaaS" => array('href="http://biggo.pro">Создание сайта — сервис «Бигго.Про»','.biggoCarousel'),
				"Bigware" => array('Diese <a href="http://bigware.de','<a href=/main_bigware_'),
				"Bitrix" => array("/bitrix/templates/", "/bitrix/js/", "/bitrix/admin/",'href="/bitrix/cache/',"'COOKIE_PREFIX':'BITRIX_SM'",'/bitrix/cache/css/','/bitrix/cache/js/'),
				"Blogger" => array("content='blogger","rel='stylesheet' href='https://www.blogger.com/static/","window['blogger_blog_id'] = ","<meta content='http://www.blogger.com/profile/"),
				"BLOX CMS" => array('div id="blox-html-container"','Powered by <a href="http://bloxcms.com" title="BLOX Content Management System"'),
				"BM Shop 5 SaaS" => array('Разработка сайта — <a href="http://renua.ru" target="_blank">Renua</a><br/> проект <a href="http://bmshop5.ru" target="_blank">bmshop5','Создание интернет-магазинов <a href="http://bmshop.ru" target="_blank">BmShop'),
				"Bolt CMS" => array('name="generator" content="Bolt','ul class="bolt-menu"'),
				"BORNET CMS" => array('Создание интернет магазина: <b><u>BORNET.ru','href="http://www.bornet.ru" class=menu'),
				"Boxcode CMS" => array('www.boxcode.ru','box_mini_panel_manager','box_panel_light_auth_user','box_user_geo','box_panel_user','box_authoriz_site','contr_box_authoriz_sit','box_manager_authoriz_user','box_panel_light_auth_user'),
				"Browser CMS" => array('generator" content="BrowserCMS'),
				"Cargo" => array('type="text/javascript" src="a/js/cargo.js','meta name="cargo_title" content=','Cargo.IncludeSocialMedia({','type="application/rss+xml" title="Cargo feed"','Running on <a href="http://cargocollective.com">Cargo</a>','cargo_title:',"text/javascript' src='/_js/cargo.jquery.package","text/javascript' src='/_js/cargo.site.package.js"),
				"Ckan" => array('name="generator" content="ckan','<script>var ckan_data'),
				"CMS 5+" => array('href="/?CMS5','href=/_cms5/admin'),
				"CMS BS" => array('meta name="GENERATOR" content="CMS-BS"'),
				"Brane CMS" => array('r52.ru/cms.3.8/data/cms.js','href="http://design.r52.ru">создание сайта и хостинг','/cms/data/styles/cms.main.css','/cms/data/cms.js','http://design.r52.ru" id="create">создание сайта и хостинг','http://www.design.r52.ru" title="создание сайта Нижний Новгород','alt="Дизайн и хостинг Р52.РУ"','ALT="Создание сайта и хостинг R52.RU','title="Создание сайта R52.ru">Создание сайта'),
				"Bricolage" => array('meta name="generator" content="Bricolage','is powered by Bricolage'),
				"BrowserCMS" => array('content="BrowserCMS'),
				"Business Catalyst" => array('rel="stylesheet" href="/CatalystStyles/','src="/CatalystScripts','businesscatalyst.com/favicon.ico"','<!-- BC_OBNW -->'),
				"CartEnergy SaaS" => array('link rel="shortcut icon" href="http://cdn1.cartenergy.ru','link href="http://cdn1.cartenergy.ru','script src="http://cdn1.cartenergy.ru','img src="http://cdn1.cartenergy.ru','работает на платформе CartEnerg'),
				"C-Gator" => array('Powered by C-Gator','Powered by <a href="http://www.c-gator.ru','target=_blank><b>C-gator','anguage="JavaScript" src="/_c-gator','href="http://www.c-gator.ru">C-Gator'),
				"Chameleon" => array('meta name="generator" content="Chameleon Content Management System - chameleon-cms.com'),
				"Chamilo" => array('meta name="generator" content="Chamilo'),
				"CMSimple" => array('meta name="generator" content="CMSimple','Сайт работает на CMSimple','Powered by CMSimple','www.cmsimplewebsites.com">Designed By CmSimpleWebsites.com'),
				"CMS Made Simple" => array("Released under the GPL - http://cmsmadesimple.org",'name="generator" content="CMS Made Simple'),
				"CMS.Pro-is" => array('Copyright (c) Pro-is – I n f o @ p r o – i s  . r u - http://www.pro-is.ru/','href="http://pro-is.ru" target="_blank"><img src="/images/pro.png','href="http://pro-is.ru" target="_blank">создание сайта','href="http://pro-is.ru" style="text-decoration:none;','http://pro-is.ru" target="_blank"><img src="/img/pro.png'),
				"CommonSpot" => array('var emptyimg = "/commonspot/','Powered by CommonSpot','commonspot.csPage'),
				"Cotonti" => array('meta name="generator" content="Cotonti'),
				"Concrete5" => array("/concrete/js/", "concrete5 - 5.","/concrete/css/",'IMAGE_PATH="/concrete/','meta name="generator" content="concrete5'),
				"Contao" => array("This website is powered by Contao Open Source CMS", 'link rel="stylesheet" href="system/contao.css','src="tl_files/','a href="tl_files/'),
				"Contenido CMS" => array('meta name="generator" content="CMS CONTENIDO','meta name="generator" content="CMS Contenido'),
				"Contensis CMS" => array('meta name="GENERATOR" content="Contensis CMS'),
				"Convio" => array('CONVIO.pageUserName','CONVIO.pageSessionID'),
				"CoreMedia" => array('content="CoreMedi'),
				"CPG Dragonfly" => array('meta name="generator" content="CPG Dragonfly CMS'),
				"Craft CMS" => array('CraftSessionId'),
				"CS Cart" => array("/skins/basic/customer/addons/","/skins/basic/customer/images/icons/favicon.ico","/auth-loginform?return_url=index.php","/index.php?dispatch=auth.recover_password","cm-popup-box hidden","cm-popup-switch hand cart-list-icon","cart-list hidden cm-popup-box cm-smart-position","index.php?dispatch=checkout.cart","cm-notification-container","/index.php?dispatch=pages.view&page_id="),
				"Danneo CMS" => array("Danneo Русская CMS", 'content="CMS Danneo','META NAME="GENERATOR" CONTENT="Danneo CMS','meta name="generator" content="CMS Danneo'),
				"D2S/CMS" => array('Разработано на <a href="http://www.d2s-systems.com/" target="_blank">D2S/CMS','Разработано на <a href="http://www.d2s-systems.com/">D2S/CMS','/Project/Frontend/Components/d2s_tools/','div onclick="d2sLightGalleryVideo','Developed by <a href="http://www.d2s-systems.com/'),
				"DedeCMS" => array("dedeajax"),
				"Digistr SaaS" => array('script type="text/javascript" src="/static/digistr.js','Платформа интернет-магазина &mdash; DigiStr'),
				"Demandware" => array("Demandware Analytics code", 'shortcut icon" type="image/png" href="http://demandware.edgesuite.net/','link rel="stylesheet" href="http://demandware.edgesuite.net/','img src="http://demandware.edgesuite.net/'),
				"DevExpress ASPX" => array('script id="dxis','type="text/css" href="/DXR.axd?','script id="dxss_'),
				"DataLife Engine" => array("DataLife Engine Copyright", "index.php?do=lostpassword", "/engine/ajax/dle_ajax.js","engine/opensearch.php","/index.php?do=feedback","/index.php?do=rules","/?do=lastcomments",'meta name="generator" content="DataLife Engine','/engine/editor/css/default.css','/engine/editor/scripts/webfont.js','dle_root','var dle_del_agree'),
				"diafan.CMS" => array('http://www.diafan.ru/'),
				"DirektWEB" => array('tnsCounterDirectline_ru','typeof tnsCounterDirectline_ru'),
				"Discuz!" => array('- Powered by Discuz!</title>','meta name="generator" content="Discuz!','meta name="author" content="Discuz! Team and Comsenz UI Team"','<p>Powered by <b>Discuz!</b>','div id="discuz_bad_','Powered by <strong><a href="http://www.discuz.net"',"discuz_uid = '0'"),
				"Divolta CMS" => array('Разработка сайта <a href="http://divolta.com.ua'),
				"Django CMS" => array('meta name="generator" content="Django-CMS'),
				"Drupal" => array("Drupal.settings","Drupal 7 (http://drupal.org)","misc\/drupal.js","drupal_alter_by_ref","/sites/default/files/css/css_","/sites/all/files/css/css_",'text/javascript" src="/misc/drupal.js'),
				"DokuWiki" => array("DokuWiki Release"),
				"Donbo" => array('href="http://donbo.by">Создание сайтов'),
				"DotNetNuke" => array('meta id="MetaGenerator" name="GENERATOR" content="DotNetNuke','by DotNetNuke Corporation','meta id="MetaDescription" name="DESCRIPTION" content=','name="COPYRIGHT" content="Copyright 2015 by DotNetNuke Corporation'),
				"dotSoft CMS" => array('type="text/javascript" src="/scripts/dotSoft.js','type="text/javascript" src="/scripts/dotsoft_pager.js','Разработка сайта: <b>dotSoft','title="dotSoft - разработка сайта и дизайн сайта'),
				"DNN" => array('script src="/DesktopModules/DNNGo_','src="/js/dnncore.js','hidden" name="dnn$mobile_search','id="dnn_mobLogi','!-- DNN Platform -'),
				"DTG" => array('Site Powered by DTG',"d.location.protocol) ? 'https://resellerstat.mono.net/dtg/"),
				"Dynamicweb" => array('meta name="Generator" content="Dynamicweb','meta name="generator" content="Dynamicweb'),
				"dxCMS" => array('Создание сайта &mdash; <a href="http://www.dvx-dev.ru">Студия dvx','http://www.dvx-dev.ru/">Студия dvx','Создание сайта — <a href="http://www.dvx-dev.ru',''),
				"e107" => array("This site is powered by e107","text/javascript' src='/content_files/e107.js","stylesheet' href='/content_files/e107.css",'Powered by e107 website system','/e107_files/e107.css','/e107_files/e107.js','img src="/e107_themes'),
				"Ecosite CMS" => array('name="generator" content="Ecosite Cms'),
				"Edicy SaaS" => array("http://stats.edicy.com:8000/tracker.js","http://static.edicy.com/assets/site_search/"),
				"eKontora CMS" => array('generator" content="eKontora','Сайт работает на платформе <a href="http://ekontora.ru','Конструктор сайтов" target="_blank">еКонтора'),
				"Ektron" => array("EktronClientManager","Ektron.PBSettings","ektron.modal.css","Ektron/Ektron.WebForms.js","EktronSiteDataJS","/Workarea/java/ektron.js","Amend the paths to reflect the ektron system"),
				"Ekwid SaaS" => array("ecwid_product_browser_scroller","push-menu-trigger ecwid-icons-menu","ecwid-starter-site-links","ecwid-loading loading-start",'var ecwid_ProductBrowserURL','script type="text/javascript" src="http://app.ecwid.com/script.js'),
				"Eleanor CMS" => array('generator" content="Eleanor CMS'),
				"ELDORADO.CMS" => array('href="http://www.eldorado-cms.ru" id="eldorado-link','href="http://www.eldorado-cms.ru">ELDORADO.CMS','target="_blank">Система управления сайтом: ELDORADO.CMS','href="http://www.eldorado-cms.ru" target="_blank">Система управления сайтом'),
				"Emonster CMS" => array('Создание сайтов, продвижение сайтов" target="_blank">KubanTrend.ru','name="web_studio" content="WWW.KUBANTREND.RU','Разработано <a title="создание сайтов KubanTrend','href="http://www.kubantrend.ru/">KT</a>','http://www.kubantrend.ru" title="Создание сайтов, продвижение сайтов" target="_blank">KubanTrend.ru','title="Создание и продвижение сайтов" target="_blank">KT','href="http://www.kubantrend.ru"><img src="/images/kubantrend.gif"','title="Создание сайтов, продвижение сайтов" target="_blank" href="http://www.kubantrend.ru'),
				"EPiServer CMS" => array('meta name="generator" content="EPiServer','meta name="EPi.ID','!-- EPi metatags --','meta name="generator" content="http://www.episerver.com','meta name="Author-Template" content="EPiServer CSS design','meta name="EPi.Description','meta name="EPi.Keywords'),
				"EShoper SaaS" => array('Сделано на платформе <a class="eshoper__link"','"name": "Eshoper.ru"
','src="//s1.eshoper.ru','src="http://s4.eshoper.ru','var remoteUrl = "http://order.eshoper.ru"'),
				"eSyndiCat" => array('meta name="generator" content="eSyndiCat','Powered by <a href="http://www.esyndicat.com/">eSyndiCat Directory Software'),
				"Excite CMS" => array('Дизайн сайта разработан в студии<a href="http://b-alt.ru','name="Author" content="&copy; Business Alliance','Разработка сайта: <a href="http://b-alt.ru','href="http://www.b-alt.ru/" alt="Разработка корпоративных сайтов','Разработка сайта:&nbsp;<a href="http://b-alt.ru','name="author" content="«Бизнес-Альянс','name="author" content="Business Alliance','name="generator" content="Excite'),
				"Explay CMS" => array('meta name="generator" content="Explay CMS','Engine &copy; <a href="http://www.explay.su/">Explay CMS','meta name="generator" content="EXPLAY Engine CMS"','alt="Explay Engine CMS"'),
				"ExpertPlus CMS" => array('Веб-студия ExpertPlus.ru','alt="Web студия ExpertPlus: создание интернет магазина - создание и раскрутка сайтов','Веб-студия ExpertPlus.ru: <a href="http://www.expertplus.ru','class="weblink">создание интернет-магазина</a>, разработка и продвижение сайтов'),
				"Express Site" => array('"http://www.expresssite.ru">изготовление сайтов - www.expresssite.ru</a>'),
				"ExpressionEngine" => array('"http://www.expresssite.ru">изготовление сайтов - www.expresssite.ru</a>','alt="Expression Engine"border="0"/></a>'),
				"eZ Publish" => array('img src="/var/ezflow_site','img src="/design/ezflow','meta name="generator" content="eZ Publish','import url(/extension/ezwebin/design/','link rel="stylesheet" type="text/css" href="/var/ezflow_site'),
				"F-CMS" => array('a href="http://www.f-cms.ru/"'),
				"Fast-Sales Pro" => array('<script type="text/javascript">var BASE_URL = "http://','function DoFastSearch() {'),
				"Fenrir.CMS" => array('Работает на Fenrir.CMS'),
				"FERT" => array('Создание сайта веб-студия ФЕРТ','Разработка веб-сайта: <a href="http://fertdesign.ru'),
				"FlexBe SaaS" => array('<a href="http://flexbe.com/land/?utm_source=clients"','Создано на платформе «Флексби»','href="/_s/css/land/_core.css?v'),
				"FlexCMP" => array("meta name='generator' content='FlexCMP",'FlexCMP - CMS per Siti Accessibili'),
				"Flexcore CMS" => array("<!-- Oliwa-pro service -->"),
				"Flexites CMS" => array('href="http://flexites.org/"><span>Разработка сайта','href="http://flexites.org/"><span>Разработка сайтов и продвижение сайтов','href="http://flexites.org/"><span>Создание и продвижение сайтов','swfobject.embedSWF("/i/flexites.swf','class="vcard" href="http://flexites.org','class="developer" href="http://flexites.org'),
				"Fo.ru SaaS" => array("MLP_NAVIGATION_MENU_ITEM_START","MLP_WINDOW_HEAD","/MLP_WINDOW_END","MLP_NAVIGATION_MENU_ITEM_END","window.location.replace('http://fo.ru/signup"),
				"FokCMS" => array('Разработка сайта — <strong>FOKGroup','http://fokgroup.com">Разработка сайта - FokGroup','http://fokgroup.com/">Создание сайта</a> - FokGroup'),
				"For.ru SaaS" => array('for.ru/favicon.ico" type="image/x-icon','href="http://for.ru/go.php?mode=','value="http://for.ru/xy/bn.swf','href="http://for.ru/complain.php'),
				"Fusion CMS" => array('id="logo_footer" href="http://fusionweb.ru'),
				"Gamburger CMS" => array('<span class="web"><a href="http://gamburger.ru/" target="_blank">','/templates/default/images/gamburger.png'),
				"GD SiteManager" => array("name='generator' content='GD SiteManager'"),
				"Geeklog" => array('var geeklog = {'),
				"General-CMS" => array('Generator" content="Система управления сайтом General-CMS'),
				"GetSimple" => array('meta name="generator" content="GetSimple','Powered by  GetSimple'),
				"GitHub Pages" => array('Powered by <a href="http://pages.github.com">GitHub Pages</a>','a href="https://github.com/bip32/bip32.github.io">GitHub Repository</a>'),
				"Google Sites" => array('class="powered-by"><a href="http://sites.google.com','\u003dhttps://sites.google.com/','meta itemprop="image" content="https://sites.google.com/','meta property="og:image" content="https://sites.google.com/'),
				"Gollos SaaS" => array('<meta name="generator" content="Gollos.com, <script src="http://s4.golloscdn.com/'),
				"Government Site Builder" => array('content="Government Site Builder'),
				"Graffiti CMS" => array('meta name="generator" content="Graffiti','a title="Powered by Graffiti CMS" href="http://graffiticms.com'),
				"Grav" => array('generator" content="Grav'),
				"GX WebManager" => array('meta name="generator" content="GX WebManager','meta name="Generator" content="GX WebManager'),
				"Gugx SaaS" => array('href="http://static.gugx.net','href="http://images.gugx.net','src="//images.gugx.net'),
				"Homestead" => array('meta name="generator" content="Homestead SiteBuilder','link rel="stylesheet" href="http://www.homestead.com'),
				"HostCMS" => array("/hostcmsfiles/",'<!-- HostCMS Counter -->','type="application/rss+xml" title="HostCMS RSS Feed"'),
				"Hotaru CMS" => array('meta name="generator" content="Hotaru'),
				"Hotlist.biz SaaS" => array("hotengine-hotlist_logo","Аренда и Создание интернет магазина Hotlist.biz","hotengine-hotcopyright","hotlist.biz/ru/?action=logout","hotengine-dialog-email","hotengine-shop-cart-message-empty-cart","hotengine-footer-copyright","hotengine-counters",'class="hotengine-seo-likeit"','class="hotengine-footer-copyright"','Powered by <img class="hotengine-hotcopyright'),
				"Howbay SaaS" => array("http://rtty.howbay.ru/","howbay-snapprodnamehldr","Аренда онлайн магазина howbay.ru"),
				"i-CMS" => array('/lib/i-cms.css','/lib/i-cms.js','/fckeditor/i-cms.js'),
				"iQuadCMS" => array('Дизайн сайта</span> <p><img src="./i/iquad-link.gif','iquadart.by" target="_blank" id="iquad-link','author" content="Дизайн студия iquadart','Создание сайта - <a href="http://iquadart.by/','a href="http://iquadart.by/">Студия «Иквадарт»','href="http://iquadart.by" target="_blank" title="Создание сайта — Иквадарт'),
				"IBM WebSphere Portal" => array('section class="ibmPortalControl',':ibmCfg.portalConfig.',"var pageMenuURL = '/wps/portal/",'href="/wps/portal/'),
				"IdeaCMS" => array('href="http://ideaweblab.com" target="_blank">Разработка',"href='http://ideaweblab.com'>Создание сайта",'href="http://ideaweblab.com">Разработка','Сайт разработан - <a href="http://www.ideaweblab.com','href="http://www.IdeaWebLab.com">Разработано'),
				"Indexhibit" => array("Built with <a href='http://www.indexhibit.org/'>Indexhibit",'you must provide a link to Indexhibit on your site someplace','Visit Indexhibit.org for more information!'),
				"inDynamic" => array("Система управления сайтом и контекстом (cms) - inDynamic",'Управление сайтом — <a href="http://www.indynamic.ru/">inDynamic'),
				"Infopark" => array("meta content='https://www.infopark.com/"),
				"InSales SaaS" => array("InSales.formatMoney", ".html(InSales.formatMoney","http://assets3.insales.ru/assets/","http://assets2.insales.ru/assets/","http://static12.insales.ru","Insales.money_format",'--InsalesCounter --'),
				"Interra CMS" => array("/templates/js/jquery-interra-slider"),
				"InstantCMS" => array("InstantCMS - www.instantcms.ru","/templates/instant/css/popalas.css","/templates/instant/css/siplim.css",'link href="/templates/_default_/css/styles.css"','href="http://www.instantcms.ru/" title="Работает на InstantCMS"','meta name="generator" content="InstantCMS'),
				"Introweb" => array('href="http://introweb.ru/">Создание сайтов</a>','<a href="http://www.introweb.ru">Создание сайта - introweb.ru</a>'),
				"im.Engine" => array('ref="http://itlooks.ru">Разработка интернет-магазина','href="http://itlooks.ru" target="_blank" title="Итлукс">Создание сайта'),
				"Imperia CMS" => array('meta name="generator" content="IMPERIA'),
				"ImpressCMS" => array('meta name="generator" content="ImpressCMS"'),
				"Image CMS" => array('meta name="generator" content="ImageCMS"','name="cms_token" />'),
				"ImpressPages" => array('/ip_cms/','ip_themes','ip_libs','ip_plugins','class="ipWidget ipWidget-Html','class="ipWidget ipWidget-Image','content="ImpressPages'),
				"IP.Board" => array("id='ipboard_body'",'ipbfs_login_col','new ipb.Menu','ipb.templates','ipb.vars[',"!--ipb.javascript.start--","IBResouce\invisionboard.com",'/forum/index.php?act=boardrules','Powered By IP.Board'),
				"Itex CMS" => array('name="Developer" content="http://www.itex.ru','href="http://www.itex.ru">Создание <br />сайта','Создание <br />сайта: Айтекс.ру'),
				"Jadu" => array('powered by Jadu CMS','content="http://www.jadu.net','content="Jadu.net'),
				"Jalios CMS" => array('meta name="Generator" content="Jalios JCMS'),
				"JCMS" => array('name="COPYRIGHT" content="Powered by JCMS','name="GENERATOR" content="JCMS','!-- generated by jcms.ru --','Сайт под управлением <a href="http://jcms.ru','& All rights reserved Powered by JCMS"'),
				"Jimdo SaaS" => array('var jimdoData = ','link href="http://u.jimdo.com','link rel="shortcut icon" href="http://u.jimdo.com','twitter:app:id:googleplay" content="com.jimdo','href="http://e.jimdo.com/app/cms'),
				"Joomla!" => array("/css/template_css.css", "Joomla! 1.5 - Open Source Content Management",'src="/templates/marshgreen/js/', "/templates/system/css/system.css", "Joomla! - the dynamic portal engine and content management system","/templates/system/css/system.css","/media/system/js/caption.js","/templates/system/css/general.css","/index.php?option=com_content&task=view",'name="generator" content="Joomla! - Open Source Content Management"','href="/components/com_rsform/assets/css/front.css','"stylesheet" href="/media/jui/css/bootstrap.min.css','script src="/modules/mod_slideshowck/assets/camera.min.js','src="/modules/mod_slideshowck/assets/jquery.mobile.customized.min.js','/templates/yoo_digit/css/bootstrap','link rel="stylesheet" href="/templates/yoo_glass/css/','link rel="stylesheet" href="/media/zoo/elements/','script src="/media/system/js/modal.js"','script src="/templates/yoo_nano3/warp/','meta name="generator" content="Joomla!','/css/joomla.css'),
				"Joostina" => array('Joostina CMS','Работает на Joostina'),
				"Josephine" => array('type="text/javascript" src="/portal-core/josephine','Наш сайт работает <br /> на CMS Josephine','href="/solutions/josephine/','Сайт работает на CMS Josephine'),
				"JSmart CMS" => array('generator" content="JSmart CMS','jsmart.css" type="text/css" media="screen','jscripts/jsmart.js"></script>','Powered by <a href="http://jsmart.ru','/js/jsmart.js"></script>','Работает на <a href="http://jsmart.ru','Система управления контентом">JSmart CMS'),
				"KasperCMS" => array('http://www.kasper.by/">Разработка сайта</a>','title="Студия Каспер: создание и разработка сайтов','Самый главный объект-родитель для KasperCMS','title="Редизайн и разработка сайтов от компании КасперСистемс','<small>Редизайн сайта - Kasper.by'),
				"Kentico" => array("CMSListMenuLI","CMSListMenuUL","Lvl2CMSListMenuLI","/CMSPages/GetResource.ashx"),
				"Kernel Video Sharing: KVS" => array("/js/KernelTeamVideoSharingSystem"),
				"Koala Web Framework CMS" => array('This website is powered by Koala Web Framework CMS','name="generator" content="Koala','class="koalaSign'),
				"Komodo" => array('Developed by: Komodo CMS','content="Komodo CMS','a href="/komodo-cms'),
				"Kooboo CMS" => array('Stylesheet" href="/Kooboo-WebResource','href="/bitportal/Cms_Data/Sites/','type="text/css" href="/Cms_Data/Sites/'),
				"Koobi CMS" => array('meta name="generator" content="(c) Koobi',"expires: 30,path: '/koobi7",'meta name="generator" content="KOOBI','koobi_dream4_showresults'),
				"Kotisivukone" => array('type="text/css" href="https://cdn.kotisivukone','src="https://files.kotisivukone.com/files','text/javascript" src="https://cdn.kotisivukone'),
				"Kwimba SaaS" => array("Kwimba.ru - он-лайн сервис для создания Интернет-магазина",'a title="Kwimba.ru - он-лайн сервис для создания Интернет-магазина" href="http://kwimba.ru'),
				"Lasto" => array('Programming <a href="http://lasto.com"'),
				"Leap.CMS" => array('Компания Leap. Разработка и развитие проекта','href="http://www.leapwork.ru/"><img src="templates/default/images/leap.png','Copyright (c) LEAP Company','name="tagline" content="http://www.leapwork.ru','href="http://www.leapwork.ru/">Разработка и поддержка проекта'),
				"LEPTON CMS" => array('/templates/lepton/css/template.css" media="screen,projection"','/templates/lepton/css/print.css" media="print"'),
				"Lark.ru SaaS" => array("/user_login.lm?back=%2F","http://lark.ru/gb.lm?u=", "http://lark.ru/news.lm?u="),
				"LightWeb" => array('Центр Веб-решений<br>создание и поддержка сайта','href="http://www.cwr.ru/">Центр Веб-решений','class="copyright" href="http://cwr.ru','Разработано в<br/><a href="http://www.cwr.ru'),
				"Limb CMS" => array("!-- POWERED BY limb",'!-- POWERED BY limb | HTTP://WWW.LIMB-PROJECT.COM/ --'),
				"LightMon Engine" => array('meta name="copyright" content="Powered by LightMon','!-- Lightmon Engine Copyright'),
				"Liferay CMS" => array("var Liferay={Browser:",'Liferay.currentURL="','var themeDisplay=Liferay.','Liferay.Portlet.onLoad','comboBase:Liferay','Liferay.AUI.getFilter','Liferay.Portlet.runtimePortletIds','Liferay.Util.evalScripts','Liferay.Publisher.register','Liferay.Publisher.deliver','Liferay.Popup.center'),
				"LiveStreet" => array("LIVESTREET_SECURITY_KEY","Free social engine"),
				"LinkorCMS" => array('Сайт работает на <a href="http://linkorcms.ru','a href="http://linkorcms.ru" target="_blank" title="Бесплатная система управления контентом','src="http://linkorcms.ru/images/linkorcms_free.gif','name="generator" content="LinkorCMS','LinkorCMS Development Group','Powered by <a href="http://linkorcms.ru','Сайт работает на LinkorCMS'),
				"Limbo (Lite mambo)" => array('meta name="GENERATOR" content="Limbo - Lite Mambo'),
				"LSHOPcms" => array('script type="text/javascript" src="/js/lshopcms','powered" href="http://www.lshopcms.com','Сайт разработан на LSHOPCMS','href="http://www.lshopcms.com" target="_blank">LSHOPcms'),
				"Magento" => array("___store=eng&___from_store=rus"),
				"Magnolia" => array('http://www.magnolia-cms.com/'),
				"Mambo" => array('meta name="Generator" content="Mambo'),
				"Mart CMS" => array('name="author" content="Mart Studio','href="http://www.mart.com.ua/" title="Создание сайта','title="Создание сайта — студия «Март»">Создание сайта','name="author" content="Azazello, Mart','Сайт создан при участии студии «<a href="http://www.martsite.ru'),
				"MaxSite CMS" => array("/application/maxsite/shared/","/application/maxsite/templates/","/application/maxsite/common/","/application/maxsite/plugins/",'meta name="generator" content="MaxSite CMS'),
				"MediaWiki" => array("/common/wikibits.js","/common/images/poweredby_mediawiki_",'Powered by MediaWiki','mediawiki.page.startup'),
				"Megagroup SaaS" => array("https://cabinet.megagroup.ru/client.", "https://counter.megagroup.ru/loader.js","создание сайтов в студии Мегагруп","создание сайтов</a> в студии Мегагруп.",">Мегагрупп.ру</a>","изготовление интернет магазина</a> - сделано в megagroup.ru","сайт визитка</a> от компании Мегагруп","Разработка сайтов</a>: megagroup.ru","веб студия exclusive.megagroup.ru"),
				"Melbis Shop" => array('meta name="generator" content="Melbis Shop'),
				"Merchium SaaS" => array('a class="bottom-copyright" href="http://www.merchium.ru'),
				"Methode CMS" => array('<!-- Methode uuid:'),
				"Microsoft SharePoint" => array('meta name="GENERATOR" content="Microsoft SharePoint"','meta name="progid" content="SharePoint.','id="MSOWebPartPage_Shared"','helps SharePoint put the web part','interacts with script and the sharepoint','load SharePoint javascript','if the IM pressence icons are needed in SharePoint','hide body scrolling (SharePoint will handle','content="SharePoint.WebPartPage.Document"','CollaborationServer" content="SharePoint Team Web Site','Microsoft.SharePoint.Taxonomy.ScriptForWebTaggingUI'),
				"Mindy CMS" => array("jquery.mindy.notify.css",'jquery.mindy.modal.css'),
				"Minicart CMS" => array('Работает на <a href="http://minicart.ru" target="_blank">Minicart CMS'),
				"Miva Merchant" => array("merchant.mvc", "admin.mvc"),
				"MODx" => array('var MODX_MEDIA_PATH = "media";', 'modxmenu.css', 'modx.css','assets/templates/modxhost/','/assets/js/jquery.colorbox-min.js','/assets/js/jquery-1.3.2.min.js','/assets/components/ajaxform/css/default.css','/assets/components/ajaxform/js/config.js','/assets/components/ajaxform/js/default.js','/assets/components/ajaxform/js/lib/jquery.min.js','/assets/components/minifyx/cache/','img src="assets/images/catalog/','src="/manager/includes/','/manager/includes/veriword.php','link href="/assets/templates/css/style.css','My MODx Site" />','img src="/image.php?src=/assets/images/catalog/','javascript" src="/assets/components/minishop/js/web/minishop.js"','src="/manager/templates/','- My MODx Site" />','link href="/assets/templates/css/style.css"','img src="assets/images/','text/javascript" src="assets/js/jquery-1.4.1.min.js','rel="stylesheet" href="assets/templates/','/image.php?src=assets/images/','meta name="modxru" content=','src="/assets/components/','type="text/css" rel="stylesheet" href="assets/templates/','"shortcut icon" href="/template/images/favicon.ico','link href="assets/templates/site/menu.css','link href="assets/templates/site/style.css','/assets/templates/mosint/js/jquery.tinycarousel.min.js','script type="text/javascript" src="assets/fancybox/jquery.mousewheel-3.0.4.pack.js','javascript" src="assets/fancybox/jquery.fancybox-1.3.4.pack.js','/assets/plugins/qm/js/jquery.colorbox-min.js"></script>','link href="assets/template/js/fancy_box/source/jquery.fancybox.css','script type="text/javascript" src="assets/templates/'),
				"Moguta CMS" => array('/mg-templates/"','/mg-core/','/mg-plugins/'),
				"Moogo" => array('kotisivukone.js'),
				"MoinMoin" => array('link rel="stylesheet" type="text/css" href="/moin_static','This website is based on <a href="/wiki/MoinMoin">MoinMoin','This site uses the MoinMoin Wiki software.">MoinMoin Powered','rel="Start" href="/cgi-bin/moin.cgi/MainPage">','a href="/cgi-bin/moin.cgi/MainPage"','a href="http://moinmo.in/">MoinMoin Powered</a>'),
				"mojoPortal" => array('content="http://www.mojoportal.com','var mojoPageTracker'),
				"Mono.net" => array('src="/skinCss/website/js/monotracker','_monoTracker.addTracker'),
				"Monolit.CMS" => array('Создание сайта – IT Группа "<a target="_blank" href="http://peredovik.ru/">Передовик точка ру','templates/_shablon/CFW/CFW_styles.css'),
				"Movable Type" => array('meta name="generator" content="Movable Type','Powered by<br /><a href="http://www.sixapart.jp/movabletype/">Movable Type'),
				"Mozello SaaS" => array("//cache.mozello.com/designs/","//cache.mozello.com/libs/js/jquery/jquery.js","Mozello</a> - самым удобным онлайн конструктором сайтов","mz_component mz_wysiwyg mz_editable","moze-wysiwyg-editor","//cache.mozello.com/mozello.ico"),
				"Mura CMS" => array('meta name="generator" content="Mura'),
				"myBB SaaS" => array("http://bs.mybb.ru/adverification?","Mybb_Brown_Assembly","mybb-counter","mybb.ru/userlist.php","mybb.ru/search.php?action=show_recent","unescape(mybb_ad4)"),
				"Mynetcap" => array('meta name="generator" content="Mynetcap'),
				"NetDo SaaS" => array("Мой сайт на конструкторе сайтов netdo.ru","http://netdo.ru/min/g/web.js", "http://netdo.ru/engine/css/layout/", "http://netdo.ru/engine/template/style/"),
				"NetCat" => array("/netcat_template/","/netcat_files/"),
				"Nethouse" => array('data-ng-app="Nethouse"','data-host="nethouse.ru"','Конструктор сайтов<br/><a href="http://www.nethouse.ru/?footer"'),
				"Next Generation" => array('generator" content="NGCMS','Powered by <a title="Next Generation CMS"','href="http://ngcms.ru/">NG CMS'),
				"Ning" => array('import url(http://api.ning.com:80','src="http://api.ning.com:80/files/','href="http://static.ning.com/socialnetworkmain/'),
				"nnovo.ru SaaS" => array('src="/temp/default/images/logonnovo.png'),
				"NQcontent" => array('content="nqcontent'),
				"Nubex CMS" => array('name="copyright" content="Powered by Nubex"','Конструктор&nbsp;сайтов&nbsp;<a href="http://nubex.ru"','href="/_nx/plain/css/'),
				"Nucleus CMS" => array('content="Nucleus CMS v3.24"'),
				"ocPortal" => array('Powered by ocPortal'),
				"Odoo" => array('name="generator" content="Odoo'),
				"Office42 CMS" => array('создание сайтов в Тольятти">Создание сайта - Office42','создание сайтов в Тольятти" href="http://www.office42.ru/">Office42','href="http://www.office42.ru/" target="_blank">Офис42','title="создание сайтов в Тольятти" target="_blank">Office42','alt="создание сайтов в Тольятти - Офис42"'),
				"OlmiCMS" => array('http://www.olmisoft.ru" style="color:#fff;">Pазработка сайта','href="http://www.olmisoft.ru" traget="_blank">Разработка сайта'),
				"Open CMS" => array('/system/modules/com.gridnine.opencms.modules'),
				"openEngine" => array('openEngine'),
				"OpenMall SaaS" => array('copyright">© 2016 <a href="http://blog.openmall.info" target="_blank">OpenMall, Inc'),
				"OpenNemas" => array('openEngine','name="generator" content="OpenNemas','opennemas-white.png" alt="OpenNeMaS CMS'),
				"OpenText Web Solutions" => array('published by Open Text Web Solutions'),
				"OpenCart (ocStore)" => array('<div class="cart-add-wrap"><input type="button" class="cart-add"','type="button" class="cart-add" value="Купить" onclick="addToCart',"catalog/view/theme/default/stylesheet/","catalog/view/javascript/jquery/colorbox/jquery","catalog/view/theme/default/stylesheet/stylesheet.css", "index.php?route=account/account", "index.php?route=account/login","index.php?route=account/simpleregister",'class="jcarousel-skin-opencart"','index.php?route=checkout/simplecheckout'),
				"osCommerce" => array('osCommerce Template &copy;','Powered by <a href="http://www.oscommerce.ru" target="_blank">osCommerce','/index.php?osCsid=','shopping_cart.php?osCsid=','/shipping.php?osCsid=','/account.php?osCsid=','/products_new.php?osCsid=','&amp;osCsid='),
				"Orchard" => array('content="Orchard" name="generator','script src="/Modules/Orchard.jQuery'),
				"Oridis CMS" => array('href="http://www.oridis.ru">ORIDIS Software','title="Разработка и продвижение сайтов - ORIDIS','alt="Разработка и продвижение сайтов - ORIDIS">ORIDIS','img src="/img/oridis.gif" alt="Разработка и продвижение сайтов'),
				"OSG" => array('Online System Group - создание интернет магазина'),
				"OXID eShop" => array('OXID eShop'),
				"Pagekit" => array('generator" content="Pagekit'),
				"Pagelife SaaS" => array('href="http://pagelife.ru"><img src="/img/pllogo.png'),
				"Parrot CMS" => array('/theme/001/adaptor/reset.css" rel="stylesheet'),
				"PANSITE" => array('generator" content="PANSITE'),
				"papaya CMS" => array('/papaya-themes/'),
				"PencilBlue CMS" => array('lang="en" ng-app="pencilblueApp','ng-controller="PencilBlueController','var pencilblueApp','pencilblueApp.config'),
				"PMD.CMS" => array('Создание и Продвижение сайта по России</a> «Pi-Media','name="generator" content="Pi-CMS','href="http://pi-media.ru/">Создание и<br />продвижение сайтов'),
				"ReadyScript CMS" => array('Работает на<br><span>ReadyScript','Работает на ReadyScript','var global = {"baseLang":"ru","lang":"ru","folder":""};'),
				"Sitebill CMS" => array("jQuery.getJSON('http://sitebill.ru/js/","$.getJSON('http://sitebill.ru/js/","var SitebillCore={",'script type="text/javascript" src="http://www.sitebill.ru/js/nanoapi','script type="text/javascript" src="/apps/system/js/sitebillcore'),
				"Shopify SaaS" => array('var Shopify = Shopify','Shopify.theme = {"name":"','//cdn.shopify.com/s/files/'),
				"Shop-Script Lego SP" => array('Powered By <a href="http://legosp.net">Shop-Script Lego SP','Разработано на <a rel="nofollow" href="http://legosp.net">LegoSP','Powered By Shop-Script<br /> <a href="http://lego.shop-script.org','Powered By Shop-Script Lego SP','<a href="http://legosp.blogspot.com/">Lego Edition SP'),
				"Shopium SaaS" => array('link rel="stylesheet" href="//cdn2.shopium.ua','meta property= content="http://cdn2.shopium.ua/','img src="//cdn2.shopium.ua','script type="text/javascript" src="//cdn1.shopium.ua'),
				"Pantera CMS" => array('3webcats_logo.png','href="http://www.3webcats.ru" target="_blank" title="Разработка, создание и продвижение сайтов Мытищи','Разработка, создание, техническая поддержка 3Webcats'),
				"Parallels Presence Builder" => array('meta name="generator" content="Parallels Presence Builder'),
				"Percussion CMS" => array('meta content="Percussion CM System" name="generator','meta name="generator" content="Percussion',"var evergageAccount = 'percussion"),
				"Perfecto CMS" => array('generator" content="Perfecto CMS','target="_blank">Создание сайта студия Perfecto Web'),
				"Perspektiva.CMS" => array("name='generator' content='Perspektiva.CMS'",'class="footer-perspektiva"'),
				"phpBB" => array("phpBB style name: prosilver", "The phpBB Group : 2006", "linked to www.phpbb.com. If you refuse","_phpbbprivmsg","Русская поддержка phpBB","below including the link to www.phpbb.com",'Движется на пхпББ'),
				"phpCMS" => array("selectmenu('phpcms'",'/phpcms.css" rel="stylesheet"',"getid=='phpcms",'type="text/css" href="/phpcms'),
				"PHP-Fusion" => array("Powered by <noindex><a href='http://www.php-fusion.co.uk'>PHP-Fusion</a>","Powered by <a href='http://www.php-fusion.co.uk'>PHP-Fusion</a>","script src='infusions/","language='javascript' src='infusions/","background-image: url('infusions/","alt='PHP-Fusion' title='PHP-Fusion'","Powered by <a href='http://www.php-fusion.co.uk'"),
				"PHP Link Directory" => array('<a href="http://www.phplinkdirectory.com" title="PHP Link Directory">PHP Link Directory</a>','Powered By <a href="http://www.phplinkdirectory.com/">PHPLD</a>','meta name="generator" content="Internet Directory One Running on PHP Link Directory','href="/profile.php?mode=register" title="Register">Register to PHPLD</a>','<a href="http://www.phplinkdirectory.com" title="PHP Link Directory">PHP LD</a>'),
				"PHP-Nuke" => array('META NAME="GENERATOR" CONTENT="PHP-Nuke - Copyright by http://phpnuke.org"','META NAME="GENERATOR" CONTENT="PHP-Nuke Copyright','Powered by PHP-Nuke Platinum','META NAME="GENERATOR" CONTENT="PHP-Nuke'),
				"PhpShop" => array("/phpshop/templates/",'Скрипт интернет-магазина PHPShop','PHPShop Software 2005-','META name="engine-copyright" content="PHPSHOP.RU','href="http://phpshopcms.ru/">PHPShopCMS</a>','content="PHPSHOP.RU','content="PHPShop Enterprise"','type="text/javascript" src="/java/phpshop.js','script src="/phpshop/templates'),
				"phpSQLiteCMS" => array('meta name="generator" content="phpSQLiteCMS'),
				"phpwind" => array('meta name="generator" content="phpwind'),
				"Pligg" => array('Pligg is an open source content management system that lets you easily','Pligg <a href="http://www.pligg.com/" target="_blank">Content Management System','name="description" content="Pligg is an open source content management system that lets you easily','var my_pligg_base=','meta name="generator" content="Pligg'),
				"Plone" => array('generator" content="Plone','template-homepage_f8_view portaltype-homepagef8 site-en'),
				"Posterous" => array('class="posterous_autopost','class="posterous_bookmarklet_entry','class="posterous_short_quote'),
				"Promodo CMS" => array('author" content="Promodo CMS','Дизайн - PROMODO" src="/images/promodo.png'),
				"PrestaShop" => array("/themes/prestashop/cache/","/themes/prestashop/","Prestashop 1.5"." || Presta-Module.com",'meta name="generator" content="PrestaShop"','meta name="keywords" content="магазин, prestashop"','Работает на <a href="http://www.prestashop'),
				"cubiQue" => array("http://www.laconix.net/cubiQue"),
				"prCMS" => array('alt="Создание и продвижение сайтов - Приоритет','a target="_blank" href="http://vprioritete.ru">ИК Приоритет','Разработано <a href="http://vprioritete.ru/">ИК «Приоритет','Создание сайта - <a href="http://vprioritete.ru'),
				"PrimalSite" => array('javascript" src="/primal/pub/','href="http://www.primalsite.ru">PrimalSite CMS'),
				"Quick.CMS" => array('name="Generator" content="Quick.Cms','DONT DELETE/HIDE LINK "CMS by Quick.Cms','CMS by Quick.Cms'),
				"Rada CMS" => array('meta name="author" content="Сайтопром — Министерство сайтостроения Украины: http://site-prom.com','title="Создание сайтов">Сайтопром','href="http://site-prom.com" title="Создание сайтов"','content="Дизайн: http://crea-pro.com.ua. Вёрстка и программирование: http://site-prom.com'),
				"Rainbow" => array('meta name="generator" content="rainbow-cms'),
				"R70.CMS" => array('NAME="Author" CONTENT="R70','Разработка, продвижение и поддержка веб-сайтов. Веб-студия R70','href="http://r70.ru" title="Веб студия Р70','Дизайн сайта, разработка сайта: Веб-студия R70','Создание сайта</a>: Веб-студия R70','Создание сайта</a>: <span>Веб-студия R70'),
				"Rapido.CMS" => array('http://webportnoy.ru" target="_blank">Webportnoy','Разработка сайтов" href="http://webportnoy.ru','http://webportnoy.ru" target="_blank">Создание<br /> сайта','title="Сайт создан студией Вебпортной">Разработка<br />сайта','Создание сайта -</span><br />Webportnoy.ru</a>','http://webportnoy.ru" target="_blank" title="Разработка сайтов">Создание сайта','Создание сайта ВебПортной" href="http://webportnoy.ru','http://webportnoy.ru" target="_blank" title="ВебПортной - Разработка сайтов, интернет-магазинов'),
				"RiteCMS" => array('meta name="generator" content="RiteCMS'),
				"RCMS" => array('meta name="generator" content="RCMS','link href="//rcms-r-production'),
				"RBC Contents" => array('rbccontents_ee_block','<!--rbccontents_ee','content="RBC Contents, http://www.rbccontents.ru/'),
				"RBS Change" => array('<body xmlns:change="http://www.rbs.fr','meta name="generator" content="RBS Change'),
				"ReadyScript SaaS" => array('ype="text/css" href="/resource/css/admin/readyscript.ui','Работает на <span>ReadyScript'),
				"re-commerce SaaS" => array('<div class="recommerce-logo">','Магазин создан в <br> Re-commerce.ru'),
				"Redham CMS" => array('Разработано <a rel="nofollow" href="http://www.redhamsites.ru','Создание сайта:&nbsp;<a href="http://www.redhamsites.ru','eveloped-by"> Веб студия Redham <br/><a href="http://www.redhamsites.ru','Создание сайта:<img src="/Images/redham-logo.jpg','window.Redham == undefined','Разработано <a href="http://www.redhamsites.ru">веб-студией Redham','href="http://www.redhamsites.ru">Создание сайта'),
				"ReCMS" => array('Разработка сайта &laquo;<a href="http://www.renua.ru/" target="_blank">Renua','Разработка сайта &mdash; <a href="http://www.renua.ru">Renua','Powered by <a href="http://www.renua.ru"','Сделано в <a href="http://www.renua.ru','/template/renua/','Сделано в <a href="#" target="_blank">Renua'),
				"Roadiz" => array('name="generator" content="Roadiz','twitter:site" content="@roadiz_cms','twitter:creator" content="@roadiz_cms'),
				"s9y" => array('name="generator" content="Serendipity','Powered-By:Serendipity'),
				"Sarka-SPIP" => array('media="all" href="plugins/sarkaspip','rel="stylesheet" href="plugins/sarkaspip','shortcut icon" href="plugins/auto/sarkaspip'),
				"SDL Tridion" => array("application='Tridion"),
				"Seditio" => array('name="generator" content="Seditio','Powered by Seditio'),
				"Sellbe SaaS" => array('link href="http://cdn6.sellbe.com','src="//sellbe.com/js/'),
				"Sequnda" => array('alt="Работает на CMS Sequnda"','img src="/i/2sun.gif','2Sun. Web-дизайн и реклама в интернете','href="/images/2sun/'),
				"Sense/Net" => array('content="Sense/Net','Powered by Sense/Net'),
				"SEOCMS" => array('Создание сайта веб студия  <a href="http://www.seotm.com','"http://www.seotm.com" target="_blank"> SEOTM','Створення інформаційного порталу">SEOTM','target="_blank" class="footer">SEOTM.COM','Разработка сайта веб студия <a href="http://www.seotm.com','Создание сайта веб студия SEOTM'),
				"Serendipity" => array('meta name="Powered-By" content="Serendipity','div id="serendipity_banner"','meta name="generator" content="Serendipity'),
				"SETUP.ru SaaS" => array("Сделано на SETUP.ru"),
				"S.Builder" => array('<a href="/techine/Sbuilder_sites.php">'),
				"SharePoint" => array('meta name="GENERATOR" content="Microsoft SharePoint','"ProgId" content="SharePoint.WebPartPage.Document','=== STARTER: Core SharePoint CSS ==','STARTER: SharePoint Reqs this for adding colu','xmlns:SharePoint="Microsoft.SharePoint.WebControls'),
				"Shopdepot" => array('Shopdepot.ru</title>'),
				"Shopium" => array('href="//cdn2.shopium.ua','src="//cdn1.shopium.ua','Shopium.GA.trackPageview'),
				"Shopware" => array('stylesheet" href="/engine/Shopware/Plugins','div class="shopware_footer"'),
				"Squiz Matrix" => array('Running Squiz Matrix','Developed by Squiz'),
				"Squarespace" => array('itemscope itemtype="http://schema.org/Thing" class="squarespace-cameron"','http://static.squarespace.com/static/','Squarespace.afterBodyLoad(Y);','Squarespace.Constants.CORE_APPLICATION_DOMAIN = "squarespace.com"','div id="squarespace-powered"','alt="Powered by Squarespace"'),
				"Sibloma" => array('<!-- Js плагинов -->'),
				"SilverStripe" => array('meta name="generator" content="SilverStripe'),
				"Sing CMS" => array('Сайт управляется <b><a href="http://sing-cms.ru"','Сайт управляется <a href="http://sing-cms.ru"'),
				"SIMsite" => array('meta name="SIM.medium','href="/simsite/css/','javascript" src="/simsite/js/','simSiteDomainRoot','SIManalytics'),
				"Simpla CMS" => array("design/default/css/main.css","design/default/images/favicon.ico","tooltip='section' section_id=",'Slider_Simpla_Module'),
				"Simpio SaaS" => array('Магазин работает на Simpio.RU'),
				"SimpleSite SaaS" => array('content="SimpleSite.com"','"text/css" href="http://css.simplesite.com/','"text/javascript" src="http://css.simplesite.com/','Сайт создан с помощью SimpleSite'),
				"Simple Machines Forum" => array('<a href="http://www.simplemachines.org/" title="Simple Machines Forum" target="_blank" class="new_win">Powered by SMF</a>','alt="Simple Machines Forum" title="Simple Machines Forum"','a href="http://www.simplemachines.org" title="Simple Machines"','title="Simple Machines" target="_blank" class="new_win">Simple Machines</a>','gaq.push(["_setDomainName", "simplemachines.org"'),
				"SiteDNK" => array('http://company.nn.ru/sitednk/" target="_blank"><img src="/img/sdnk.gif"'),
				"SiteEdit" => array('meta name="generator" content="CMS EDGESTILE SiteEdit"','Сайт разработан и работает на CMS SiteEdit'),
				"SiteForever CMS" => array('name="generator" content="SiteForever CMS','module\/siteforever','rel="stylesheet" href="/misc/siteforever.css','text/javascript" src="/misc/siteforever.js'),
				"site2start" => array('title="site2start" href="http://www.site2start.ru','http://www.site2start.ru">Разработка и продвижение','/images/sts_logo.png" alt="site2start'),
				"SiTex" => array('Портал реализован на платформе &quot;<a href="http://www.mysitex.ru','target="blank">SiTex','href="http://www.mysitex.com">SiTex'),
				"Shop2You" => array('href="http://www.shop2you.ru/" target=_blank>Создание интернет-магазина</A>','href="http://www.shop2you.ru/" target=_blank>Создание интернет-магазина</a>','Создание сайта: Александр Фролов, <a href="http://www.shop2you.ru/"','A href="http://www.shop2you.ru/" target=_blank>Услуги по созданию интернет-магазинов</A>: Александр Фролов','href="http://www.shop2you.ru/" target=_blank>Создание интернет-магазина</A>: Александр Фролов'),
				"ShopOS" => array('meta name="generator" content="(c) by ShopOS , http://www.shopos.ru"','Telerik.Sitefinity.Services.Search.Web.UI.Public.SearchBox'),
				"SKY CMS" => array('enerator" content="SKY CMS','/img/skycms.gif" alt="SKY CMS'),
				"Skynell SaaS" => array('<meta property="og:image" content="http://skynell.com"/>','href="http://skynell.com/promo/shop.php" class','href="http://skynell.com/promo/crm.php','href="http://skynell.com/company/','skynell.biz" class="theme_show_logo'),
				"Slaed" => array('meta name="generator" content="SLAED','SLAED CMS'),
				"SMF" => array('action="/smartsite.dws','class="smartlet'),
				"SMF" => array("var smf_images_url","PHP, MySQL, bulletin, board, free, open, source, smf, simple, machines, forum","Simple Machines Forum","Powered by SMF"),
				"sNews" => array('meta name="Generator" content="sNews','meta name="generator" content="sNews'),
				"Squarespace SaaS" => array('Squarespace.Constants','CORE_APPLICATION_DOMAIN = "squarespace.com"','onclick="Squarespace.Interaction.shareLink','Squarespace.Constants.WEBSITE_TITLE','Squarespace.Constants.SS_AUTHKEY','Squarespace.Constants.ADMINISTRATION_UI','Squarespace.Constants.WEBSITE_ID'),
				"SmileCMS" => array('BulgarPromo - <a href="http://bulgar-promo.ru"  title="Продвижение сайтов"','div class="copybulgar"','Bulgar Promo - <a href="http://bulgar-promo.ru">продвижение сайтов','BulgarPromo - <a href="http://bulgar-promo.ru"'),
				"Solodev" => array('/core/image.php?path=/Solodev/','Web Design</a>by Solodev','http://sp.solodev.net/core/'),
				"SPIP CMS" => array('meta name="generator" content="SPIP','href="prive/spip_style.css"','id="searchform" name="search" action="spip.php"','<!-- SPIP-CRON -->',"img class='spip_logos'"),
				"SSPRO CMS" => array('name="CMS" content="SSPRO','Copyright" content="2004 (c) SWE-ART','Создание сайта</font></a> Cве Арт','(c) SWE-ART Web-design&programming(www.swe.ru)">','http://www.sspro.ru/" target="_blank">Управление сайтом','http://www.sspro.ru/">Управление сайтом'),
				"Status-X CMS" => array('CMS Status-X-->','/status_x_cssstylesheet','generator" content="CMS Status-X',"href='http://www.status-x.ru'>Скриптинг",'/status_x_javascript'),
				"Stearling CMS " => array('name="copyright" content="Stearling Studio','name="reply-to" content="info@stearling.net','href="http://www.stearling.net" class="BotMail','Разработка и поддержка — Stearling.net','href="http://stearling.net" class="developed_by"','Создание сайтов </a>- Stearling Studio','Технічна підтримка Stearling Studio','class="stearling">Розробка сайту -  Stearling Studio','alt="Stealing Studio" title="Студия Стерлинг" class="StearlingLogo"'),
				"Strawberry " => array('Powered by Strawberry','Powered by Strawberry | http://Strawberry.GoodGirl.ru'),
				"Strikingly SaaS" => array('"host_suffix": "strikingly.com"','"pages_show_static_path": "//assets.strikingly.com/assets/','"show_strikingly_logo"','<meta content="//assets.strikingly.com"','<div id="strikingly-navigation-menu">','<div class="strikingly-footer-spacer"','Rendered by Strikingly','Powered by Strikingly'),
				"Storeland SaaS" => array("storeland.net/favicon.ico","http://storeland.ru/?utm_source=powered_by_link&amp;utm_medium=","StoreLand.Ru: Сервис создания интернет-магазинов",'src="http://statistics3.storeland.ru/stat.js?site_id=','src="http://statistics2.storeland.ru/stat.js?site_id=','src="http://statistics1.storeland.ru/stat.js?site_id='),
				"Studio2d" => array('Разработка сайта Studio2D.ru'),
				"Submarine CMS" => array('href="http://itmedia.by/" target="_blank">itmedia.by','name="creator" content="ОДО &laquo;АйТи Медиа&raquo;, www.itmedia.by','href="http://itmedia.by" title="Профессиональная разработка сайтов в Беларуси','name="creator" content="ITMEDIA, www.itmedia.by','Дизайн и разработка &laquo;<a href="http://itmedia.by">ITMEDIA','href="http://itmedia.by/" title="Профессиональная разработка сайтов в Беларуси – АйТи Медиа'),
				"Subrion CMS" => array('meta name="generator" content="Subrion'),
				"Subrion CMS" => array('meta name="generator" content="Subrion'),
				"SunSite CMS" => array('content="SunSite','var sunsite = new sunsite'),
				"swift.engine" => array('uralweb_d=document','uralweb_s.colorDepth:uralweb_s'),
				"TAO.CMS/CMF" => array('http://www.web-techart.ru/">разработка сайта</a>  - Текарт','src="/files/_assets/scripts/tao.js','Query.extend(true, TAO.settings','TAO.fields.autocomplete','rel="nofollow" href="http://www.web-techart.ru">Создание и поддержка сайта','Дизайн и разработка сайта – <a href="http://www.techart.ru/"','"http://www.promo-techart.ru/" target="_blank">продвижение сайта</a> — Текарт','href="http://www.web-techart.ru/" target="_blank">разработка сайта'),
				"TiddlyWiki" => array('var version = {title: "TiddlyWiki','name="copyright" content="TiddlyWiki','popupTiddler {background'),
				"Telerik Sitefinity" => array('<meta name="Generator" content="Sitefinity','class="RadMenu RadMenu_Sitefinity"','src="/Sitefinity/WebsiteTemplates/','Telerik.Sitefinity.Resources'),
				"TextPattern" => array('meta name="generator" content="Textpattern','CMS Textpattern'),
				"Thelia" => array('name="generator" content="Thelia','name="thelia_newsletter[email]'),
				"Tiki Wiki CMS Groupware" => array('meta name="generator" content="Tiki Wiki CMS Groupware','#tiki-center','body class="tiki tiki_wiki_page','action="tiki-login.php"','a href="tiki-remind_password.php"'),
				"Timelabs CMS" => array("X-Powered-By: TimeLabs CMS"),
				"Tiu.ru SaaS" => array('href="http://tiu.ru/" class="b-head-control-panel__logo','data-propopup-url="http://tiu.ru/util/ajax_get_pro_popup_new','Сайт создан на платформе Tiu.ru</a>','href="http://tiu.ru/how_to_order?source_id='),
				"Trac" => array('rel="help" href="/wiki/TracGuide"','/wiki/WikiStart?format=txt" type="text/x-trac-wiki','аботает на <a href="/about"><strong>Trac','owered by <a href="/about"><strong>Trac'),
				"Treegraph" => array('/comm/treegraph.css','/comm/treegraph.js','Powered by TreeGraph'),
				"Tumblr" => array('arning: Never enter your Tumblr password unless \u201chttps://www.tumblr.com/login','background-image: url(http://static.tumblr.com','href="android-app://com.tumblr/tumblr/','BEGIN TUMBLR FACEBOOK OPENGRAPH TAGS'),
				"TypePad" => array('meta name="generator" content="http://www.typepad.com/"','application/rsd+xml" title="RSD" href="http://www.typepad.com'),
				"TYPO 3" => array("This website is powered by TYPO3","typo3temp/",'meta name="generator" content="TYPO3','src="/typo3conf/','--TYPO3SEARCH_end'),
				"Twilight CMS" => array('<A HREF="http://www.twl.ru" target="_blank" >Система управления сайтом TWL CMS</A>','<link rel="stylesheet" href="Sites/','<link rel="stylesheet" href="/Sites/','<link rel="stylesheet" href="/Sites/','<img src="/Sites/'),
				"uCore" => array('name="generator" content="uCore','script src="/uTemplates/main/js/main.js'),
				"uCoz SaaS" => array("cms-index-index","U1BFOOTER1Z","U1DRIGHTER1Z","U1CLEFTER1","U1AHEADER1Z","U1TRAKA1Z","U1YANDEX1Z"),
				"UkroCMS" => array('target="_blank" href="http://ukro.in.ua">UkroCMS</a>'),
				"Umbraco" => array('xmlns:umbraco.library="urn:umbraco.library','/umbraco/imageGen.ashx','uComponents: Multipicker','umbraco:Item field=','umbraco:macro alias=','html xmlns:umbraco="http://umbraco.org'),
				"UMI CMS" => array('xmlns:umi="http://www.umi-cms.ru/',"umi:element-id=", "umi:field-name=","umi:method=", "umi:module=",'<!-- Подключаем title, description и keywords -->'),
				"UniversalCMS" => array('generator" content="UniversalCMS','Создано на базе universalcms'),
				"Ural CMS" => array('<meta name="author" content="Ural-Soft"','uss-copyright_logo" href="http://www.ural-soft.ru/','http://www.ural-soft.ru/" target="_blank" title="создание сайтов Екатеринбург'),
				"VamShop" => array("templates/vamshop/css/","templates/vamshop/img","templates/vamshop/buttons"),
				"Unbounce" => array('/unbounce.js"></script>'),
				"Ushahidi" => array('Initialize the Ushahidi namespace','they are accessible by Ushahidi.js'),
				"USWeb CMS" => array('name="author" content="USWeb'),
				"uWeb SaaS" => array('Хостинг от <a href="http://www.uweb.ru/" title="Создать сайт">uWeb'),
				"vBulletin" => array("vbulletin_css", "vbulletin_important.css","clientscript/vbulletin_read_marker.js", "Powered by vBulletin", "Main vBulletin Javascript Initialization","var vb_disable_ajax = parseInt","vbmenu_control"),
				"viennaCMS" => array('viennacms'),
				"Vignette" => array('begCacheTok=com.vignette','link href="/vgn-ext-templating'),
				"VIVVO CMS" => array('meta name="generator" content="Vivvo','new vivvoTicker','VIVVO CMS'),
				"Vixi CMS" => array('Developed by — <a href="http://zeema.com/','Создание сайта: </span><img id="zeemaLogo','href="http://zeema.com" target="_blank"><img id="zeemaLogo"','Разработано -</span><img id="zeemaLogo','$("#zeemaLogo")','id="zeemaCont"'),
				"Volga CMS" => array('Сайт создан в студии <a href="http://volga-w.ru/','Волга-Веб" target="_blank">Волга-Веб</a>','Сайт создан в студии <a id="made_link" href="http://volga-w.ru','Создание сайта</a> — студия <a rel="nofollow" href="http://volga-w.ru/','Сайт создан в студии  <a href="http://volga-w.ru','Сайт создан в студии <a href="http://volga-w.ru'),
				"W3 Total Cache" => array('name="generator" content="WebCodePortalSystem'),
				"Webadminka CMS" => array('создание сайтов webadminka.ru</a>','сайт работает на CMS-Webadminka','id="webadminka','http://webadminka.ru/" title="Cоздание сайтов Москва','Добро пожаловать в CMS-WEBADMINKA!'),
				"Webarty CMS" => array('href="http://www.webarty.ru/" target="_blank">Студия Вебарти','Сайт визитка</a> на базе CMS WebArty','target="_blank" href="http://www.webarty.ru">студия Вебарти','сайт разработан</span><a target="_blank" href="http://www.webarty.ru">студией WebArty','_blank" href="http://www.cms-webarty.com">CMS WebArty','Corporate website</a> is based on CMS Webarty','Сайт визитка</a> на базе CMS WebArty',"http://www.cms-webarty.com'>CMS Webarty</a>,<br> разработка сайта",'Сайт визитка</a> на базе CMS WebArty','href="http://www.cms-webarty.ru">CMS WebArty'),
				"WebAsyst" => array("/published/SC/","/published/publicdata/","aux_page=","auxpages_navigation","auxpage_","?show_aux_page=",'/wa-data/public/shop/themes/'),
				"Web Driver CMS" => array('Создание сайта — «Актив Дизайн»','Разработка сайта</a><br />«Актив дизайн»','Модернизация дизайна, система управления WebDriver','одернизация дизайна и программирование "Актив Дизайн"</a>','Сайт разработан<br />в студии «<a href="http://www.a-dn.ru','Разработка сайта</a> - «Актив Дизайн»'),
				"webEdition" => array('meta name="generator" content="webEdition"'),
				"WebGUI" => array('meta name="generator" content="WebGUI','function getWebguiProperty','content="WebGUI'),
				"WebPublisher" => array('meta name="generator" content="WEB|Publisher'),
				"WEB-ROBOTS.CMS" => array('web-robots.gif" alt="Дизайн и создание web-robots.ru','Создание сайта</a> <a href="http://www.web-robots.ru','/_pic/web-robots.gif" alt="Создание сайта"','title="Разработка сайта web-robots.ru','alt="Дизайн и разработка сайта web-robots.ru"'),
				"Website Baker" => array('meta name="generator" content="CMS: Website Baker','meta name="generator" content="WebsiteBaker'),
				"Webs" => array('thumbServer: "http://thumbs.webs.com',"if(typeof(webs)==='undefined')",'<link rel="stylesheet" type="text/css" href="http://static.websimages.com/','text/javascript" src="http://static.websimages.com/JS/','webs.theme.style = {','webs-allow-nav-wrap'),
				"WebSitePro" => array('Разработка сайта - <a href="http://www.websait.spb.ru','href="http://websait.spb.ru" title="Создание сайта','Создание сайта - Первая Веб Дизайн Студия','href="http://websait.spb.ru">Разработка - Первая Веб Дизайн Студия','href="http://websait.spb.ru" class="creater">Создание сайта','href="http://websait.spb.ru" class="creater">Первая Веб Дизайн Студия'),
				"WebSite X5" => array('generator" content="Incomedia WebSite X5'),
				"WebsPlanet" => array('meta name="generator" content="WebsPlanet Core'),
				"Web Canape CMS" => array('Web-canape - <a href="http://www.web-canape.','a href="http://www.web-canape.ru/seo/?utm_source=copyright">продвижение</a>','/themes/canape1/css/ie/main.ie.css'),
				"Weebly SaaS" => array("Weebly.Commerce = Weebly.Commerce","Weebly.setup_rpc","editmysite.com/js/site/main.js","editmysite.com/css/sites.css","editmysite.com/editor/libraries","weebly-footer-signup-container","link weebly-icon"),
				"Wilmark CMS" => array('Изготовление cайта - Wilmark Design','Разработка сайта Wilmark Design','http://www.wilmark.ru/" target="_blank" title="Создание сайта','Создание сайта</a> &mdash;  Wilmark Design','target="_blank" class="date">Wilmark Design','http://www.wilmark.ru" class="common_link">Wilmark Design'),
				"Wix SaaS" => array("static.wix.com/client/","X-Wix-Published-Version", "X-Wix-Renderer-Server","X-Wix-Meta-Site-Id",'http-equiv="X-Wix-Application-Instance-Id"'),
				"Wolf CMS" => array('href="http://www.wolfcms.org/">Wolf CMS</a> Inside','title="Wolf CMS" target=_blank>Wolf CMS</a> Inside','href="http://www.wolfcms.org">Wolf CMS Inside</a>'),
				"WordPress" => array("/wp-includes/", "wp-content/", "/wp-admin/", "/wp-login/",'meta name="generator" content="WordPress'),
				"WYSIWYG Web Builder" => array('name="generator" content="WYSIWYG Web Builder'),
				"X3M.CMS" => array('/templates/_ares/css/style.css','/templates/_ares/css/global.css','/templates/_ares/css/skin.css','/templates/_ares/css/settings.css','/templates/_ares/js/main.js'),
				"XenForo" => array('html id="XenForo" lang="','link rel="stylesheet" href="css.php?css=xenforo','script src="js/xenforo/xenforo.js','src="styles/default/xenforo/','Forum software by XenForo&trade; <span>','action="login/login" method="post" class="xenForm"'),
				"XOOPS" => array('meta name="generator" content="XOOPS','meta name="author" content="XOOPS"','/include/xoops.js'),
				"XpressEngine" => array('meta name="Generator" content="XpressEngine"'),
				"xt:Commerce" => array('meta name="generator" content="xt:Commerce','alt="xt:Commerce Payments','div class="copyright">xt:Commerce','This OnlineStore is brought to you by XT-Commerce'),
				"xToolza" => array('content="xToolza: инструмент проверки'),
				"Yahoo! Small Business" => array('(new Image).src="http://store.yahoo.net/cgi-bin/refsd?e='),
				"Yu CMS" => array('(new Image).src="http://store.yahoo.net/cgi-bin/refsd?e='),
				"Zen Cart" => array('meta name="generator" content="shopping cart program by Zen Cart','meta name="author" content="The Zen Cart&trade; Team and others"','greybox 1: greybox for zencart',"n&amp;zenid="),
				"ZetaWeb" => array('name="generator" content="ZetaWeb','name="author" content="http://www.zetasoft.ru','Разработано: </font><a title="www.zetasoft.ru','Разработано ZetaSoft','работает на: <a href="http://www.zetasoft.ru','Разработано <a href="http://www.zetasoft.ru'),
				"ZMS" => array('generator" content="ZMS http://www.zms-publishing.com"'),
				"ZoKeR CMS" => array('Проект студии WebTaller.RU" title="Проект студии WebTaller',"title='Web-студия WebTaller - разработка сайтов' '>WebTaller.ru"),
				"Веб-АвтоРесурс" => array('text/javascript" src="/_syslib/mootools.js','javascript" src="/_syslib/mootools-more.js','javascript" src="/_syslib/menu/codethatmenupro.js'),
				"ЕвроCMS" => array('Продвижение и поддержка сайта <a href="http://eurosites.ru','<a href="http://eurosites.ru/?page=prodvizhenie_saytov" title="Продвижение сайта"','Создание и поддержка сайта - Евросайты','Создание, продвижение сайта - веб-студия ЕвроСайты','a href="http://eurosites.ru" title="Создание сайта"','author" content="www.eurosites.ru','copyright" content="CMS EuroSites','name="copyright" content="ЕвроCMS"','Создание сайта</a> веб-студия ЕвроСайты'),
				"ЗВЕЗДА" => array('создание сайта: Web-студия &laquo;ЗВЕЗДА&raquo','ref="http://www.studiostar.ru/" TITLE="создание сайтa','NAME="CopyRight" CONTENT="Web-студия «ЗВЕЗДА','создание сайтов - на CMS Звезда','web-дизайн - Web-студия &laquo;ЗВЕЗДА&raquo;</a>'),
				"Итари" => array('Система управления сайтом &laquo;<a href="http://www.itari.ru','оздание сайтов в Нижнем Новгороде">Итари'),
				"Корнет-CMS" => array('Разработка и <a href="http://www.htkornet.ru"','Разработка и продвижение <br>НПП "Корнет"','Разработка сайта <A href="http://www.htkornet.ru/">НПП "Корнет"','Разработка сайта: ООО НПП “Корнет”','Создание сайта <a href="http://htkornet.ru/">ООО НПП Корнет','Разработка и сопровождение сайта: <a href="http://www.htkornet.ru','href="http://www.htkornet.ru/">ООО НПП Корнет','target="_blank">Создание и продвижение сайта НПП &quot;Корнет&quot;'),
				"Мультимедиа компания «КСК» CMS" => array('Сделано в КСК','rev="made" href="http://www.cural.ru/"','link rev="cural" href=','name="author" content="http://www.cural.ru/'),
				"Неосфера CMS" => array('Разработка сайта - <a href="http://neonstudio.ru','Разработка сайта</a> &ndash;<br />&laquo;Неон-студия'),
				"Платформа" => array('href="http://itisinfo.ru/">ООО «ИТИС»','Сайт поддерживается <a terget="_blank" href="http://itisinfo.ru/','Разработка<br />сайта: <a href="http://itisinfo.ru/','ООО «Инновационные Технологии Информационных Систем»" target="_blank">ООО «ИТИС»','target="_blank">ООО «ИТИС»','Разработка, поддержка сайта: <a href="http://itisinfo.ru/','title="Инновационные технологии информационных систем">ИТИС'),
				"Просто Сайт CMS" => array('<a title="создание сайтов" href="http://www.yalstudio.ru/services/corporativ/">создание сайтов</a> — Студия ЯЛ','http://www.yalstudio.ru/services/complex/" title="продвижение сайтов','title="продвижение сайтов" href="http://www.yalstudio.ru/services/complex/">Продвижение сайтов','<a href="http://www.yalstudio.ru/services/complex/">продвижение сайтов</a>'),
				"Строитель CMS" => array('title="Создание сайтов" href="http://www.metadesign.ru/"'.'alt="Создание сайтов" src="/media/i/1x1.gif">создание сайтов<br><span>Метадизайн','href="http://www.metadesign.ru/" class="link link-meta">студии Метадизайн','создание сайтов в Красноярске<br><span>Метадизайн','Разработка сайтов Метадизайн" class="meta-logo">разработка сайтов'),
				"Спутник CMS" => array('CMS Sputnik Team','CMS Sputnik Team','Создание сайта <a href="#">Web-sputnik'),
				"Управлятор" => array('Создание и продвижение сайта</a> — Студия Арт-Дизайн','http://www.dizain.org" target="_blank">Создание и продвижение сайта','href="http://www.dizain.org/" class="down">Дизайн и разработка сайта','Разработка сайта, продвижение</a> — Студия Арт-Дизайн','http://www.dizain.org" target="_blank" rel="nofollow">Студия Арт-Дизайн','http://www.dizain.org" target="_blank" rel="nofollow">Студия Арт-Дизайн','Продвижение сайта</a> - Студия Арт-Дизайн')
		);
		foreach ($cms as $name => $rules) {
			$c = count($rules);
			for ($i = 0; $i < $c; $i++) {
					if (stripos($html, $rules[$i]) !== FALSE) {
						return '<b><abbr data-title="Система управления сайтом (админка)">CMS</abbr></b>: '.$name . '<br>';
						}
				}
		}
		return "<b><span><abbr data-title='Система управления сайтом (админка)'>CMS</abbr>: <abbr data-title='Среди всех известных нам CMS не нашлось ни одного совпадения' style='color:red'>НЕ ОПРЕДЕЛЕНА</abbr></span><br></b>";
}
function cmscheck() {
	$contents = trim($GLOBALS['siteurl']);
	$parseurl = parse_url($contents);
	$scheme = $parseurl['scheme'];
	if (!$scheme) {
		$scheme = 'http://';
	} else $scheme = $scheme.'://';
	if (!$parseurl['host']) {
		$host = $parseurl['path'];
	} else $host = $parseurl['host'];
	echo '<p style="margin-top:20px;">Для сайта <b><a href="http://'. $host . '" target="_blank">'. $host.'</a></b> &nbsp;<span><a class="btn-warning btn" href="/algorythms/salestart.html" target="_blank" style="padding-top:0px;padding-bottom:0px;">Требования к сайтам</a></span></p>'.checktraffic(). checkyaca($host);
	echo check(grab($scheme.$host));
}

function checktraffic(){
	$contents = trim($GLOBALS['siteurl']);
	$parseurl = parse_url($contents);
	if (!$parseurl['host']) {
	    $host = $parseurl['path'];
	  } else $host = $parseurl['host'];
	  if (!empty(gettraffic($host))){
	  	echo '<span><abbr data-title="По данным LiveInternet">Трафик: '.gettraffic($host).' уникальных посетителей в месяц</abbr></span>';
	  }
}

function checkyaca($host){
	$contents = trim($GLOBALS['siteurl']);
	$parseurl = parse_url($contents);
	if (!$parseurl['host']) {
	    $host = $parseurl['path'];
	  } else $host = $parseurl['host'];
	  if (!empty(trim(strip_tags(getyaca($host))))){
	  	echo '<span style="margin-left:60px;">Яндекс.Каталог: '.getyaca($host).'</span>';
	  }
}

function merchcheck() {
	$contents = trim($GLOBALS['siteurl']);
  $parseurl = parse_url($contents);
	$scheme = $parseurl['scheme'];
	if (!$scheme) {
		$scheme = 'http://';
	} else $scheme = $scheme.'://';
	if (!$parseurl['host']) {
		$host = $parseurl['path'];
	} else $host = $parseurl['host'];
  $googleurl = $scheme.$host.'/google729d33f2559c664d.html';
  $content = getpagecontent($googleurl);
  $status = http_status_code($googleurl);
  if ($status == '200') { //проверим статус код
    $needle = 'google-site-verification: google729d33f2559c664d.html';
    $content = getpagecontent($googleurl);
    // var_dump($content);
    $lookfor = strpos($content, $needle);
    // var_dump($lookfor);
    if ($lookfor === false) { //проверим тот ли текст в файле
      echo 'Текст google-site-verification: <a href="http://reentermanual.local/campaign/google/google729d33f2559c664d.html" download>google729d33f2559c664d.html</a> не найден в <a href="'.$googleurl.'" target="_blank">файле</a>.<br>';
    } else echo '<span style="color:#005F29;font-weight:bold">Файл подтверждения <a href="http://reentermanual.local/campaign/google/google729d33f2559c664d.html" download>google729d33f2559c664d.html</a> <a href="'.$googleurl.'" target="_blank">найден!</a></span><br>';
  } else echo '<span style="color:red;font-weight:bold">Ошибка: файл подтверждения отвечает статус-кодом: <a href="'.$googleurl.'" target="_blank">' . $status . '</a>. Возможно в корне сайта не размещен файл <a href="http://reentermanual.local/campaign/google/google729d33f2559c664d.html" download><abbr data-title="Файл для подтверждения прав в Google">google729d33f2559c664d.html</abbr></a>.</span><br>';
}

function robotscheck(){
  $contents = trim($GLOBALS['siteurl']);
  $parseurl = parse_url($contents);
	$scheme = $parseurl['scheme'];
	if (!$scheme) {
		$scheme = 'http://';
	} else $scheme = $scheme.'://';
	if (!$parseurl['host']) {
		$host = $parseurl['path'];
	} else $host = $parseurl['host'];
  $robotstxturi =$scheme.$host.'/robots.txt';
  echo '<a href="#open" onclick="show(\'hidden_3\',200,30)" class="btn-default btn" >'.$robotstxturi.'</a> ';
  echo '<div id="hidden_3" style="display:none;height:200px;width:350px;background-color:#f0f0f0;position:fixed;top:455px;right:10px;"><pre style="background-color:#a8b98c">'.file_get_contents($robotstxturi).'</pre></div>';
  if (http_status_code($robotstxturi) >= '500') {
    echo '<a class="btn-danger btn" href="'.$robotstxturi.'" target="_blank">'. http_status_code($robotstxturi) . '<br />Данные статус-код блокирует индексацию роботами!</a>';
  } else echo '<a class="btn-success btn" href="'.$robotstxturi.'" target="_blank">' . http_status_code($robotstxturi) . '</a>';
}

function gettraffic($site) {
	$xml = getpage('http://counter.yadro.ru/values?site='.$site);
	preg_match('|LI_month_vis\s=\s(.*);|isU', $xml, $links2);
	$LI_month_vis = "LI_month_vis = ";
				foreach ($links2 as $link2){
			$res2 = iconv("Windows-1251", "UTF-8", $link2);
			return rtrim(ltrim($res2, $LI_month_vis),";");
		}
}

function getyaca($site) {
	$xml = getpage('http://bar-navig.yandex.ru/u?ver=2&show=32&url='.'http://'.$site);
	preg_match('|<textinfo>(.*)</textinfo>|ism', $xml, $links);
		foreach ($links as $link){
			$res = iconv("Windows-1251", "UTF-8", $link);
			return trim($res);
		}
}

function shortener($in){
  if (strlen($in)>85) {
    return substr($in,0,85)."...";
  } else return $in;
}

?>
