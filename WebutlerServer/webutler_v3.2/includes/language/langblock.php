<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


if(preg_match('#/includes/language/langblock.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');


if($webutlercouple->config['languages'] == '1' && array_key_exists('code', $webutlercouple->langconf) && count($webutlercouple->langconf['code']) > 0)
{
    $languages_docroot = $webutlercouple->config['server_path'].'/includes/language';
	if(file_exists($webutlercouple->config['server_path'].'/includes/language/css/langblock.css'))
		$webutlercouple->autoheaderdata[] = '<link href="includes/language/css/langblock.css?t='.filemtime($webutlercouple->config['server_path'].'/includes/language/css/langblock.css').'" rel="stylesheet" type="text/css" />';
	$wblangtplvars = array();
	
	$count = 0;
	foreach($webutlercouple->langconf['code'] as $code)
	{
		$wblang_language = isset($webutlercouple->langconf['lang'][$code]) ? $webutlercouple->langconf['lang'][$code] : '';
		$wblang_homepage = isset($webutlercouple->langconf['homes'][$code]) ? 'index.php?page='.$webutlercouple->langconf['homes'][$code] : '';
		
		$wblangtplvars[$count]['src'] = 'includes/language/icons/'.$code.'.png';
		$wblangtplvars[$count]['lang'] = $wblang_language;
		$wblangtplvars[$count]['href'] = $wblang_homepage;
		
		$count++;
	}
	
	ob_start();
	include $languages_docroot.'/tpls/langblock.tpl';
	$langblock = ob_get_contents();
	ob_end_clean();
	
	echo $langblock;
}


