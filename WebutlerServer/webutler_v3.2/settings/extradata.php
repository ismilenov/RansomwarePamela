<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/settings/extradata.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

$webutler_autoheaderdata[] = '<meta name="generator" content="Webutler" />';
$webutler_autoheaderdata[] = '<meta name="viewport" content="width=device-width" />';

$webutler_autofooterdata[] = '';


