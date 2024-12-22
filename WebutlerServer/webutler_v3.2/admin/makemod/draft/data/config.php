<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/modules/###MODULENAME###/data/config.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

###MODCONFIG###


