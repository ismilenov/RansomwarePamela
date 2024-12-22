<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/modules/news/data/config.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

$news_conf['base'] = array('newest');

$news_conf['data'] = array('onoff', 'field_headline', 'field_date', 'field_image', 'field_imgalt', 'field_teaser', 'field_text');

$news_conf['urlparams'] = array('data' => 'newsdata');

$news_conf['types'] = array('text' => 'field_headline,field_imgalt', 'date' => 'field_date', 'image' => 'field_image', 'area' => 'field_teaser', 'html' => 'field_text');




$news_conf['imgsize']['field_image']['box']['width'] = "900";
$news_conf['imgsize']['field_image']['box']['height'] = "600";
$news_conf['imgsize']['field_image']['view']['width'] = "300";
$news_conf['imgsize']['field_image']['view']['height'] = "200";
$news_conf['imgsize']['field_image']['full']['width'] = "600";
$news_conf['imgsize']['field_image']['full']['height'] = "400";



