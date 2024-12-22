<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


if(preg_match('#/settings/modulebox.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

/*
$webutler_moduleslist = array(
    array('Name of the link', 'Path to administration', 'Don't show on login page [optional]')
);
*/
$webutler_moduleslist = array(
    array('News', 'news/admin.php', '-')
);

