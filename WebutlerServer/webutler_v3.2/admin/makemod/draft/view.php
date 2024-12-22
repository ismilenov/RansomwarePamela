<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/modules/###MODULENAME###/view.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

if(!class_exists('SQLite3')) {
    echo 'no SQLite3';
	return false;
}

if(!class_exists('MMConnectClass'))
	require_once $webutlercouple->config['server_path'].'/includes/mmclass.php';

if(file_exists($webutlercouple->config['server_path'].'/modules/###MODULENAME###/view/###MODULENAME###.css') && !in_array($###MODULENAME###_stylesheet, $webutlercouple->autoheaderdata)) {
	$###MODULENAME###_stylesheet = '<link href="modules/###MODULENAME###/view/###MODULENAME###.css?t='.filemtime($webutlercouple->config['server_path'].'/modules/###MODULENAME###/view/###MODULENAME###.css').'" rel="stylesheet" type="text/css" />';
    $webutlercouple->autoheaderdata[] = $###MODULENAME###_stylesheet;
}

$###MODULENAME###_langpath = $webutlercouple->config['server_path'].'/modules/###MODULENAME###/view/lang';
$###MODULENAME###_language = (isset($_SESSION['language']) && file_exists($###MODULENAME###_langpath.'/'.$_SESSION['language'].'.php')) ? $_SESSION['language'] : $webutlercouple->config['defaultlang'];
require_once $###MODULENAME###_langpath.'/'.$###MODULENAME###_language.'.php';

require $webutlercouple->config['server_path'].'/modules/###MODULENAME###/data/config.php';

###CATMENUOPEN###
###NEWESTOPEN###
###BLOCKSELSE###

	$###MODULENAME###_class = ###USERINPUTINIT###new MMViewClass;
	$###MODULENAME###_class->modname = '###MODULENAME###';
	###MODBASECAT###
	$###MODULENAME###_class->fileconfig = $###MODULENAME###_conf;
    $###MODULENAME###_class->userisadmin = $webutlercouple->checkadmin();
	$###MODULENAME###_class->sessgrpids = isset($_SESSION['userauth']['groupid']) ? $_SESSION['userauth']['groupid'] : array();
	$###MODULENAME###_class->getpage = $webutlercouple->getpage;
	$###MODULENAME###_class->serverpath = $webutlercouple->config['server_path'];
	$###MODULENAME###_class->pagelang = $###MODULENAME###_language;
	$###MODULENAME###_class->get = $_GET;
	$###MODULENAME###_class->connectdb();

###USERINPUTACTION###
###SETFILTER###

    if($###MODULENAME###_class->dbconfig == '') {
        echo _MODMAKERLANG_NOTINSTALLED_;
        return false;
    }
	
    ###LIGHTBOXJAVASCRIPT###
	
	if($webutlercouple->checkadmin() || $###MODULENAME###_class->getwritepermission()) {
		$webutlercouple->autoheaderdata[] = '###INPUTJAVASCRIPT###';
	}
	
    $###MODULENAME###_result = $###MODULENAME###_class->loadmodule();
	
	if(count($###MODULENAME###_class->metadata) > 0) {
		if(isset($###MODULENAME###_class->metadata['title']))
			$webutlercouple->setnewtitlefrommod = $###MODULENAME###_class->metadata['title'];
		if(isset($###MODULENAME###_class->metadata['description']))
			$webutlercouple->autoheaderdata[] = '<meta name="description" content="'.$###MODULENAME###_class->metadata['description'].'" />';
		if(isset($###MODULENAME###_class->metadata['keywords']))
			$webutlercouple->autoheaderdata[] = '<meta name="keywords" content="'.$###MODULENAME###_class->metadata['keywords'].'" />';
		if(isset($###MODULENAME###_class->metadata['robots']))
			$webutlercouple->autoheaderdata[] = '<meta name="robots" content="'.$###MODULENAME###_class->metadata['robots'].'" />';
		if(isset($###MODULENAME###_class->metadata['canonical']))
			$webutlercouple->autoheaderdata[] = '<link href="'.$###MODULENAME###_class->metadata['canonical'].'" rel="canonical" />';
	}
    
    echo $###MODULENAME###_result;

###BLOCKSCLOSE###


