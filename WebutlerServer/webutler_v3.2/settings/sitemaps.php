<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/settings/sitemaps.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

$revisit_after = 60*60*24;

/*
$webutler_modulesitemaps = array(
	array('Modulename', 'include pages per languages, comma separated', 'optional: base category ID')
);
*/
$webutler_modulesitemaps = array(
	array('news', 'news')
);


