<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(!class_exists('SQLite3')) {
    echo 'no SQLite3';
	return false;
}

require_once dirname(dirname(dirname(__FILE__))).'/includes/loader.php';
require_once $webutler_config['server_path'].'/includes/mmclass.php';

$webutlermodadmin = new WebutlerAdminClass;
$webutlermodadmin->config = $webutler_config;
$webutlermodadmin->langconf = $webutler_langconf;
$webutlermodadmin->moduleslist = $webutler_moduleslist;

if(isset($_SESSION['###MODULENAME###lang']))
	$###MODULENAME###_adminlang = $_SESSION['###MODULENAME###lang'];
elseif(isset($_SESSION['loggedin']['userlang']))
	$###MODULENAME###_adminlang = $_SESSION['loggedin']['userlang'];
else
	$###MODULENAME###_adminlang = $webutlermodadmin->config['defaultlang'];

$###MODULENAME###_adminlangfile = $webutlermodadmin->config['server_path'].'/modules/###MODULENAME###/admin/lang/'.$###MODULENAME###_adminlang.'.php';
if(file_exists($###MODULENAME###_adminlangfile))
	require_once $###MODULENAME###_adminlangfile;

$###MODULENAME###_viewlangfile = $webutlermodadmin->config['server_path'].'/modules/###MODULENAME###/view/lang/'.$###MODULENAME###_adminlang.'.php';
if(file_exists($###MODULENAME###_viewlangfile))
	require_once $###MODULENAME###_viewlangfile;

if(!isset($###MODULENAME###_conf))
	require_once $webutlermodadmin->config['server_path'].'/modules/###MODULENAME###/data/config.php';

$###MODULENAME###_class = new MMAdminClass;
$###MODULENAME###_class->modname = '###MODULENAME###';
$###MODULENAME###_class->serverpath = $webutlermodadmin->config['server_path'];
$###MODULENAME###_class->homepage = $webutlermodadmin->config['homepage'];
$###MODULENAME###_class->chmod = $webutlermodadmin->config['chmod'];
$###MODULENAME###_class->pngcomp = $webutlermodadmin->config['png_compress'];
$###MODULENAME###_class->jpgqual = $webutlermodadmin->config['jpg_quality'];
$###MODULENAME###_class->langconf = $webutlermodadmin->langconf;
$###MODULENAME###_class->fileconfig = $###MODULENAME###_conf;

if(###SUBEDITORS_LOGCHECK###) {
	###SUBEDITORS_LOGFILE###
	exit;
}

if(!isset($_GET['page'])) $_GET['page'] = '';

$###MODULENAME###_viewlangfile = $webutlermodadmin->config['server_path'].'/modules/###MODULENAME###/view/lang/'.$###MODULENAME###_adminlang.'.php';
if(file_exists($###MODULENAME###_viewlangfile))
	require_once $###MODULENAME###_viewlangfile;

$###MODULENAME###_class->pagelang = $###MODULENAME###_adminlang;
$###MODULENAME###_class->post = $_POST;
$###MODULENAME###_class->files = $_FILES;
$###MODULENAME###_class->get = $_GET;
$###MODULENAME###_class->connectdb();

###CHECK_HIDESUBS_OPEN###
    if(isset($_POST['saveconf'])) {
        $###MODULENAME###_class->saveconfig();
        header("Location: admin.php?page=conf");
    }
    else {
        if($###MODULENAME###_class->dbconfig == '' && $_GET['page'] != 'install')
    		header("Location: admin.php?page=install");
        elseif($###MODULENAME###_class->dbconfig != '' && $_GET['page'] == 'install')
    		header("Location: admin.php");
    }
###CHECK_HIDESUBS_CLOSE###

if(isset($_POST['###MODULENAME###lang'])) {
	$_SESSION['###MODULENAME###lang'] = preg_replace("/[^a-z]/", "", $_POST['###MODULENAME###lang']);
    header("Location: admin.php".(($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : ''));
	exit;
}

###PHPBRICKS_ADMIN###

###STARTPAGE###

?>
<!DOCTYPE html>
<html lang="<?PHP echo $###MODULENAME###_adminlang; ?>">
<head>
<title><?PHP echo _MODMAKERLANGADMIN_TITLE_.' '.strtoupper(_MODMAKERLANGADMIN_MODNAME_); ?></title>
	<meta charset="UTF-8" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="robots" content="noindex,nofollow" />
	<link href="<?PHP echo $webutlermodadmin->config['homepage']; ?>/modules/###MODULENAME###/admin/admin.css" rel="stylesheet" type="text/css" />
	###HEADERJAVASCRIPT###
	<script src="<?PHP echo $webutlermodadmin->config['homepage']; ?>/modules/###MODULENAME###/admin/admin.js"></script>
</head>
<body>
<div id="adminpage">
  <?PHP echo $webutlermodadmin->getmodulesheadermenu('###MODULENAME###', $###MODULENAME###_adminlang); ?>
  <h1 id="adminheadline"><img src="<?PHP echo $webutlermodadmin->config['homepage']; ?>/admin/system/images/webutler_s.gif" align="right" /><?PHP echo _MODMAKERLANGADMIN_TITLE_.' '.strtoupper(_MODMAKERLANGADMIN_MODNAME_); ?></h1>
  <?PHP if($_GET['page'] != 'install') { ?>
  <table id="adminmenu" border="0" cellspacing="0" cellpadding="5">
	<tr>
      ###MENU_ADMIN###
	</tr>
  </table>
  <?PHP } ?>
  <div id="admincontent">
      ###CATPOPUP_ADMIN###
      ###OPTPOPUP_ADMIN###
      ###UPLOADPOPUP_ADMIN###
    <form method="post" name="baseform" action="admin.php<?PHP echo ($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : ''; ?>" enctype="multipart/form-data">
      ###CONFVARS_ADMIN###
      ###OPTIONS_ADMIN###
      ###CATLIST_ADMIN###
      ###CATDATA_ADMIN###
      ###CATSUBS_ADMIN###
      ###TOPICLIST_ADMIN###
      ###TOPICDATA_ADMIN###
      ###LIST_ADMIN###
      ###DATA_ADMIN###
    </form>
  </div>
</div>
</body>
</html>
