<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/modules/###MODULENAME###/search.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

class MMSearchMod###MODULENAME### extends MMViewClass
{
	var $searchlimit = 30;
	var $searchresult;
	var $searchquery;
	var $modulepage;
}


