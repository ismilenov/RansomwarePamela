<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/admin/system/admin.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

$useformsendto = ($webutler_config['forms_modul'] == '1') ? 'formsendto,' : '';
$usecolumns = ($webutler_config['insertcolumns'] > '0') ? 'columns,' : '';
$useshowprotected = ($webutler_config['codeicon'] == '1') ? 'showprotected,' : '';
$webutleradmin = new WebutlerAdminClass($useformsendto, $usecolumns, $useshowprotected);
$webutleradmin->config = $webutler_config;
$webutleradmin->cke_cssclasses = ($webutler_config['ckecssclasses'] == '1') ? 1 : 0;

if(!$webutleradmin->checkadmin())
    exit('no access');

$webutleradmin->htmlsource = $webutler_htmlsource;
$webutleradmin->offlinepages = $webutler_offlinepages;
$webutleradmin->mailaddresses = $webutler_mailaddresses;
$webutleradmin->langconf = $webutler_langconf;
$webutleradmin->categories = $webutler_categories;
$webutleradmin->linkhighlite = $webutler_linkhighlite;
$webutleradmin->moduleslist = $webutler_moduleslist;
if(count($webutler_autoheaderdata) > 0)
    $webutleradmin->autoheaderdata = $webutler_autoheaderdata;
if(count($webutler_autofooterdata) > 0)
    $webutleradmin->autofooterdata = $webutler_autofooterdata;

$webutleradmin->verifygetpage();


require_once $webutleradmin->config['server_path'].'/admin/system/lang/'.$_SESSION['loggedin']['userlang'].'.php';
require_once $webutleradmin->setlangdefines();

if(isset($_POST['edit']))
{
    $webutleradmin->adminpageedit();
    echo $webutleradmin->adminpage;
}
elseif(isset($_POST['block']))
{
    $webutleradmin->adminpageblock();
    echo $webutleradmin->adminpage;
}
elseif(isset($_POST['menu']))
{
    $webutleradmin->adminpagemenu();
    echo $webutleradmin->adminpage;
}
elseif(isset($_POST['content']))
{
    $webutleradmin->adminpagecontent();
    echo $webutleradmin->adminpage;
}
elseif(isset($_POST['editnewlayout']) || isset($_POST['editnewstyles']))
{
    $webutleradmin->adminsourceedit();
    echo $webutleradmin->adminpage;
}
elseif(isset($_POST['editpattern']))
{
    $webutleradmin->admineditpattern();
    echo $webutleradmin->adminpage;
}
elseif(isset($_GET['tplfile']))
{
    $webutleradmin->adminpreviewtpl();
    echo $webutleradmin->loadcontentpage;
}
elseif(isset($_GET['pagefile']))
{
    $webutleradmin->adminpreviewpage();
    echo $webutleradmin->loadcontentpage;
}
elseif(isset($_GET['blockfile']))
{
    $webutleradmin->adminpreviewblock();
    echo $webutleradmin->loadcontentpage;
}
elseif(isset($_GET['menufile']))
{
    $webutleradmin->adminpreviewmenu();
    echo $webutleradmin->loadcontentpage;
}
else
{
    $webutleradmin->adminpageview();
    echo $webutleradmin->loadcontentpage;
}


