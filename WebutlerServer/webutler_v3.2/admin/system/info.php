<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(__FILE__))).'/includes/loader.php';

$webutlerphpinfo = new WebutlerAdminClass;
$webutlerphpinfo->config = $webutler_config;

if(!$webutlerphpinfo->checkadmin())
    exit('no access');

phpinfo();

