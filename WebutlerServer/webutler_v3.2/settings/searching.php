<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/settings/searching.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');


// Search in pages of /content/pages directory
$webutler_pagesearches = 1;
// 1 = yes, 0 = no

/*
$webutler_modulesearches = array(
	array('Modulename', 'include pages per languages, comma separated', 'optional: base category ID')
);
*/

$webutler_modulesearches[] = array('news', 'news');


