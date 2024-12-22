<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/modules/news/view.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

if(!class_exists('SQLite3')) {
    echo 'no SQLite3';
	return false;
}

if(!class_exists('MMConnectClass'))
	require_once $webutlercouple->config['server_path'].'/includes/mmclass.php';

if(file_exists($webutlercouple->config['server_path'].'/modules/news/view/news.css') && !in_array($news_stylesheet, $webutlercouple->autoheaderdata)) {
	$news_stylesheet = '<link href="modules/news/view/news.css?t='.filemtime($webutlercouple->config['server_path'].'/modules/news/view/news.css').'" rel="stylesheet" type="text/css" />';
    $webutlercouple->autoheaderdata[] = $news_stylesheet;
}

$news_langpath = $webutlercouple->config['server_path'].'/modules/news/view/lang';
$news_language = (isset($_SESSION['language']) && file_exists($news_langpath.'/'.$_SESSION['language'].'.php')) ? $_SESSION['language'] : $webutlercouple->config['defaultlang'];
require_once $news_langpath.'/'.$news_language.'.php';

require $webutlercouple->config['server_path'].'/modules/news/data/config.php';


if(isset($_MMVAR['newsnewest']) && $_MMVAR['newsnewest'] == '1') {
	unset($_MMVAR['newsnewest']);
	$newsnewest_class = new MMViewClass;
	$newsnewest_class->getpage = $webutlercouple->getpage;
	$newsnewest_class->modname = 'news';
	$newsnewest_class->serverpath = $webutlercouple->config['server_path'];
	$newsnewest_class->fileconfig = $news_conf;
	$newsnewest_class->pagelang = $news_language;
	$newsnewest_class->get = $_GET;
	if(isset($_MMVAR['newsmodpage']) && $_MMVAR['newsmodpage'] != '') {
		$newsnewest_class->modpage = $_MMVAR['newsmodpage'];
		unset($_MMVAR['newsmodpage']);
	}
	$newsnewest_class->connectdb();
	echo $newsnewest_class->loadnewestblock();

}
else {


	$news_class = new MMViewClass;
	$news_class->modname = 'news';
	
	$news_class->fileconfig = $news_conf;
    $news_class->userisadmin = $webutlercouple->checkadmin();
	$news_class->sessgrpids = isset($_SESSION['userauth']['groupid']) ? $_SESSION['userauth']['groupid'] : array();
	$news_class->getpage = $webutlercouple->getpage;
	$news_class->serverpath = $webutlercouple->config['server_path'];
	$news_class->pagelang = $news_language;
	$news_class->get = $_GET;
	$news_class->connectdb();




    if($news_class->dbconfig == '') {
        echo _NEWSLANG_NOTINSTALLED_;
        return false;
    }
	
    
	
	if($webutlercouple->checkadmin() || $news_class->getwritepermission()) {
		$webutlercouple->autoheaderdata[] = '';
	}
	
    $news_result = $news_class->loadmodule();
	
	if(count($news_class->metadata) > 0) {
		if(isset($news_class->metadata['title']))
			$webutlercouple->setnewtitlefrommod = $news_class->metadata['title'];
		if(isset($news_class->metadata['description']))
			$webutlercouple->autoheaderdata[] = '<meta name="description" content="'.$news_class->metadata['description'].'" />';
		if(isset($news_class->metadata['keywords']))
			$webutlercouple->autoheaderdata[] = '<meta name="keywords" content="'.$news_class->metadata['keywords'].'" />';
		if(isset($news_class->metadata['robots']))
			$webutlercouple->autoheaderdata[] = '<meta name="robots" content="'.$news_class->metadata['robots'].'" />';
		if(isset($news_class->metadata['canonical']))
			$webutlercouple->autoheaderdata[] = '<link href="'.$news_class->metadata['canonical'].'" rel="canonical" />';
	}
    
    echo $news_result;

}


