<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/includes/loader.php';

class WebutlerAdminCKEClass extends WebutlerAdminClass
{
	function pagesselect()
	{
		$options = array();
		$lang_pages = false;
		$allpages = array();
		$optgroup = array();
		$optclose = '';
		if($this->config['languages'] == '1' && array_key_exists('pages', $this->langconf)) {
			$lang_pages = true;
			$codes = $this->langconf['code'];
			$langs = $this->langconf['lang'];
			
			foreach($codes as $code) {
				$allpages = array_merge($allpages, $this->langconf['pages'][$code]);
			}
			
			foreach($langs as $lang => $value) {
				$optgroup[$lang] = "[ '".$value."', [ ";
			}
			$optclose = " ] ], ";
		}
		
		$directory = $this->config['server_path'].'/content/pages';
		$handle = opendir($directory);
		while(false !== ($file = readdir ($handle))) {
			if(!is_dir($directory.'/'.$file.'/')) {
				$extension = substr($file, strrpos($file, '.'));
				$extension = strtolower($extension);
				
				if(!array_key_exists('nolang', $options))
					$options['nolang'] = array();
				
				if($file != '.' && $file != '..' && $file != '.htaccess' && $file != $this->config['ownerrorpage'] && $extension != '.bak' && $extension != '.tmp') {
					if($lang_pages !== false && in_array($file, $allpages)) {
						foreach($codes as $code) {
							if(!array_key_exists($code, $options))
								$options[$code] = array();
							
							if(array_key_exists('pages', $this->langconf) && in_array($file, $this->langconf['pages'][$code])) {
								$options[$code][] = "[ '".$file."', 'index.php?page=".$file."' ], ";
							}
						}
					}
					else {
						$options['nolang'][] = "[ '".$file."', 'index.php?page=".$file."' ], ";
					}
				}
			}
		}
		closedir($handle);
		
		$result = "[ '', '' ], ";
		$notfound = "[ '"._WBLANGADMIN_WIN_LANGUAGE_EMPTY_."', '', 'disabled=\"disabled\" style=\"color: #A0A0A0 !important; font-style: italic\"' ]";
		if($lang_pages !== false) {
			foreach($codes as $code) {
				if(array_key_exists($code, $optgroup)) {
					$result.= $optgroup[$code];
					if(count($options[$code]) == 0) {
						$result.= $notfound;
					}
					else {
						sort($options[$code]);
						foreach($options[$code] as $opt) {
							$result.= $opt;
						}
					}
					$result.= $optclose;
				}
			}
			$result.= "[ '"._WBLANGADMIN_WIN_LANGUAGE_NOTINLANG_."', [ ";
		}
		
		if(count($options['nolang']) == 0) {
			$result.= $notfound;
		}
		else {
			sort($options['nolang']);
			foreach($options['nolang'] as $nolang) {
				$result.= $nolang;
			}
		}
		
		$result.= (!$lang_pages) ? '' : $optclose;

		return $result;
	}
}

$webutlerckplugin = new WebutlerAdminCKEClass;
$webutlerckplugin->config = $webutler_config;
$webutlerckplugin->langconf = $webutler_langconf;

if(!$webutlerckplugin->checkadmin())
    exit('no access');

require_once $webutlerckplugin->config['server_path']."/admin/system/lang/".$_SESSION['loggedin']['userlang'].".php";

$pagesselect = $webutlerckplugin->pagesselect();

header('Content-type: application/javascript');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP 1.1
header('Cache-Control: post-check=0, pre-check=0, false'); // HTTP 1.0
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');

echo "var InternPagesSelectBox = [ ".$pagesselect." ];";









