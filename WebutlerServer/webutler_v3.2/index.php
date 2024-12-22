<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(__FILE__).'/includes/loader.php';

$webutler = new WebutlerClass;
$webutler->config = $webutler_config;

if($webutler->checkadmin()) {
    require_once $webutler->config['server_path'].'/admin/system/admin.php';
}
else {
    $webutler->htmlsource = $webutler_htmlsource;
    $webutler->offlinepages = $webutler_offlinepages;
    $webutler->mailaddresses = $webutler_mailaddresses;
    $webutler->langconf = $webutler_langconf;
    $webutler->categories = $webutler_categories;
    $webutler->linkhighlite = $webutler_linkhighlite;
    $webutler->moduleslist = $webutler_moduleslist;
    if(count($webutler_autoheaderdata) > 0)
        $webutler->autoheaderdata = $webutler_autoheaderdata;
    if(count($webutler_autofooterdata) > 0)
        $webutler->autofooterdata = $webutler_autofooterdata;

    $webutler->verifygetpage();
    $webutler->checkantixpost();
    require_once $webutler->setlangdefines();
    $webutler->sethistory();
    $webutler->loadpage();
    
    echo $webutler->loadcontentpage;
}
