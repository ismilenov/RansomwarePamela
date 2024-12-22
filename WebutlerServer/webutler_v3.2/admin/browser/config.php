<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/admin/browser/config.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

// Erlaubte Dateitypen
$this->AllowedExtensions['file'] = array('*');
$this->AllowedExtensions['image'] = array('gif','jpg','jpeg','png');
$this->AllowedExtensions['flash'] = array('swf');
$this->AllowedExtensions['track'] = array('flv','mp3','mp4','mpg','mpeg','ogg','ogv','wav','webm');

// In /mediabrowser/images/icons vorhandene Icon-Bilder
$this->AvailableIcons = array('avi','doc','flv','gif','jpg','jpeg','mpg','mpeg','mp3','mp4','mov','ogg','ogv','pdf','png','ppt','swf','txt','wav','webm','xls','zip');

$this->AllowedTypes = array('file','image','flash','track');

