<?PHP

require_once dirname(dirname(__FILE__)).'/loader.php';
require_once $webutler_config['server_path'].'/settings/sitemaps.php';
if(count($webutler_modulesitemaps) > 0)
	require_once $webutler_config['server_path'].'/includes/mmclass.php';

header("Content-type: text/xml");

class SitemapXML extends WebutlerClass {
    
    var $config;
    var $offlinepages;
    var $langconf;
    var $categories;
    var $blockedpages;
	var $modulesitemaps;
	var $modparenturls = array();
    
	function __construct()
	{
		$this->blockedpages = $this->blockedpages();
	}

    function blockedpages()
    {
        $pages = array();
        
        if(file_exists($this->config['server_path'].'/content/access/users.db') && class_exists('SQLite3'))
        {
            $db = new SQLite3($this->config['server_path'].'/content/access/users.db');
            
            $blocks = $db->query("SELECT id, pages FROM blocks WHERE blocks.id = '1' LIMIT 1");
        	if($block = $blocks->fetchArray()) {
                $pages = explode(',', $block['pages']);
            }
        }
        return $pages;
	}
    
	function getpages()
	{
		$result = array();
		
		if(array_key_exists('code', $this->langconf)) {
			foreach($this->langconf['code'] as $code) {
				$contentpages = $this->contentpages($code);
			}
		}
		else {
			$contentpages = $this->contentpages();
		}
		
		$modulepages = $this->modulepages();
		
		$result = array_merge_recursive($contentpages, $modulepages);
		
		return $result;
	}
    
    function contentpages($lang = '')
	{
		$contentpages = array();
		
		$categories = '';
		if($this->config['categories'] == '1' && array_key_exists('pages', $this->categories) && count($this->categories['pages']) > 0) {
			$categories = $this->categories['pages'];
		}
		
		if($lang == '') {
			$pages = $this->config['server_path'].'/content/pages';
			
			$handle = opendir($pages);
			while(false !== ($page = readdir($handle)))
			{
        		$ext = substr($page, -4);
				if($page != "." && $page != ".." && $page != ".htaccess" && $ext != '.bak' && $ext != '.tmp' && $page != $this->config['ownerrorpage'] && !in_array($page, $this->offlinepages) && !in_array($page, $this->blockedpages))
				{
					if($page == $this->config['startseite']) {
						$uri = $this->config['modrewrite'] == '1' ? '/index'.$this->config['urlendung'] : '/index.php';
						$priority = '1.0';
					}
					else {
						$uri = '/index.php?page='.$page;
						$priority = '0.9';
						if($this->config['modrewrite'] == '1') {
							$uri = '/'.$page.$this->config['urlendung'];
							if($categories != '') {
								foreach($categories as $cat => $catpages) {
									if(in_array($page, $catpages)) {
										$uri = '/'.$cat.$uri;
										break;
									}
								}
							}
						}
					}
					
					$contentpages[] = array(
						'location' => $this->config['homepage'].$uri,
						'priority' => $priority
					);
				}
			}
			
			$tmp = array();
			foreach ($contentpages as &$contentpage) {
				$tmp[] = &$contentpage['priority'];
			}
			
			array_multisort($tmp, SORT_DESC, $contentpages);
		}
		else {
			foreach($this->langconf['code'] as $code) {
				foreach($this->langconf['pages'][$code] as $page) {
					if(!in_array($page, $this->offlinepages) && !in_array($page, $this->blockedpages))
					{
						$checklang = $this->setlangascategory($page);
						$language = $checklang[0];
						$page = $checklang[1];
						
						if($language == '' && $page == $this->config['startseite']) {
							$uri = $this->config['modrewrite'] == '1' ? '/index'.$this->config['urlendung'] : '/index.php';
							$priority = '1.0';
						}
						else {
							$uri = $this->config['modrewrite'] == '1' ? '/'.$page.$this->config['urlendung'] : '/index.php?page='.$page;
							$priority = ($this->config['modrewrite'] == '1' && $page == 'index') ? '1.0' : '0.9';
							if($this->config['modrewrite'] == '1') {
								$langfolder = $language != '' ? '/'.$language : '';
								if($categories != '') {
									foreach($categories as $cat => $catpages) {
										if(in_array($page, $catpages)) {
											$uri = '/'.$cat.$uri;
											break;
										}
									}
								}
								$uri = $langfolder.$uri;
							}
						}
						
						$contentpages[$code][] = array(
							'location' => $this->config['homepage'].$uri,
							'priority' => $priority
						);
					}
				}
				
				$tmp = array();
				foreach ($contentpages[$code] as &$contentpage) {
					$tmp[] = &$contentpage['priority'];
				}
				
				array_multisort($tmp, SORT_DESC, $contentpages[$code]);
			}
		}
        
        return $contentpages;
    }
	
    function modulepages()
    {
		$modulepages = array();
		
		if(count($this->modulesitemaps) > 0) {
			$categories = '';
			if($this->config['categories'] == '1' && array_key_exists('pages', $this->categories) && count($this->categories['pages']) > 0) {
				$categories = $this->categories['pages'];
			}
			
			foreach($this->modulesitemaps as $modules) {
				$geturls = $this->getmodulessitemap($modules);
				
				$countparenturls = count($this->modparenturls);
				$priority_data = '0.8';
				if($countparenturls > 0) {
					$priority_parents = '0.7';
				}
				
				if(array_key_exists('code', $this->langconf)) {
					if($countparenturls > 0) {
						foreach($this->modparenturls as $lang => $urls) {
							foreach($urls as $url) {
								$pages = explode('&', $url);
								$page = $pages[0];
								unset($pages);
								
								$checklang = $this->setlangascategory($page);
								$language = $checklang[0];
								$page = $checklang[1];
								
								if($this->config['modrewrite'] == '1') {
									$url = '/'.str_replace(array('&amp;', '='), array('-', '-'), $url).$this->config['urlendung'];
								}
								else {
									$url = '/index.php?page='.$url;
								}
								
								if($this->config['modrewrite'] == '1' && $categories != '') {
									foreach($categories as $cat => $catpages) {
										if(in_array($page, $catpages)) {
											$url = '/'.$cat.$url;
											break;
										}
									}
								}
								
								if($language != '') {
									$url = '/'.$language.$url;
								}
								
								$modulepages[$lang][] = array(
									'location' => $this->config['homepage'].$url,
									'priority' => $priority_parents
								);
							}
						}
					}
					
					foreach($geturls as $lang => $urls) {
						foreach($urls as $geturl) {
							$pages = explode('&', $geturl);
							$page = $pages[0];
							unset($pages);
								
							$checklang = $this->setlangascategory($page);
							$language = $checklang[0];
							$page = $checklang[1];
							
							if($this->config['modrewrite'] == '1') {
								$geturl = '/'.str_replace(array('&amp;', '='), array('-', '-'), $geturl).$this->config['urlendung'];
							}
							else {
								$geturl = '/index.php?page='.$geturl;
							}
							
							if($this->config['modrewrite'] == '1' && $categories != '') {
								foreach($categories as $cat => $catpages) {
									if(in_array($page, $catpages)) {
										$geturl = '/'.$cat.$geturl;
										break;
									}
								}
							}
							
							if($language != '') {
								$geturl = '/'.$language.$geturl;
							}
							
							$modulepages[$lang][] = array(
								'location' => $this->config['homepage'].$geturl,
								'priority' => $priority_data
							);
						}
					}
				}
				else {
					if($countparenturls > 0) {
						foreach($this->modparenturls as $parent => $url) {
							$pages = explode('&', $url);
							$page = $pages[0];
							unset($pages);
								
							$checklang = $this->setlangascategory($page);
							$language = $checklang[0];
							$page = $checklang[1];
							
							if($this->config['modrewrite'] == '1') {
								$url = '/'.str_replace(array('&amp;', '='), array('-', '-'), $url).$this->config['urlendung'];
							}
							else {
								$url = '/index.php?page='.$url;
							}
							
							if($this->config['modrewrite'] == '1' && $categories != '') {
								foreach($categories as $cat => $catpages) {
									if(in_array($page, $catpages)) {
										$url = '/'.$cat.$url;
										break;
									}
								}
							}
							
							if($language != '') {
								$url = '/'.$language.$url;
							}
							
							$modulepages[] = array(
								'location' => $this->config['homepage'].$url,
								'priority' => $priority_parents
							);
						}
					}
					
					foreach($geturls as $geturl) {
						$pages = explode('&', $geturl);
						$page = $pages[0];
						unset($pages);
						
						$checklang = $this->setlangascategory($page);
						$language = $checklang[0];
						$page = $checklang[1];
						
						if($this->config['modrewrite'] == '1') {
							$geturl = '/'.str_replace(array('&amp;', '='), array('-', '-'), $geturl).$this->config['urlendung'];
							if($categories != '') {
								foreach($categories as $cat => $catpages) {
									if(in_array($page, $catpages)) {
										$geturl = '/'.$cat.$geturl;
										break;
									}
								}
							}
						}
						else {
							$geturl = '/index.php?page='.$geturl;
						}
						
						if($language != '') {
							$geturl = '/'.$language.$geturl;
						}
						
						
						$modulepages[] = array(
							'location' => $this->config['homepage'].$geturl,
							'priority' => $priority_data
						);
					}
				}
			}
		}
        
        return $modulepages;
    }
    
    function getmodulessitemap($module) {
		$pages = explode(',', $module[1]);
		$modpages = array();
		if(array_key_exists('code', $this->langconf)) {
			foreach($pages as $page) {
				if(!in_array($page, $this->offlinepages) && !in_array($page, $this->blockedpages)) {
					foreach($this->langconf['code'] as $code) {
						if(in_array($page, $this->langconf['pages'][$code])) {
							if($this->config['modrewrite'] == '1' && (
							  ($this->config['langfolder'] == '1' && $page == $this->langconf['homes'][$code])
								|| 
							  ($this->config['langfolder'] != '1' && $page == $this->config['startseite'])
							)) {
								$modpages[$code] = 'index';
							}
							else {
								$modpages[$code] = $page;
							}
						}
					}
				}
			}
		}
		else {
			if(!in_array($pages[0], $this->offlinepages) && !in_array($pages[0], $this->blockedpages)) {
				$modpages[] = $pages[0];
			}
		}
		
		$modulename = $module[0];
		$sitemapmodfile = $this->config['server_path'].'/modules/'.$modulename.'/sitemap.php';
		if(file_exists($sitemapmodfile)) {
			$classname = 'MMSitemapMod'.$modulename;
			$funcname = 'modulessitemap_'.$modulename;
			if(!class_exists($classname) && !function_exists($funcname))
				require $sitemapmodfile;
			
			if(function_exists($funcname)) {
				$result = $funcname($modpages);
			}
			if(class_exists('SQLite3') && class_exists($classname)) {
				$configname = $modulename.'_conf';
				if(!isset($$configname))
					require $this->config['server_path'].'/modules/'.$modulename.'/data/config.php';
				
				$modsitemapclass = new $classname;
				$modsitemapclass->modulepages = $modpages;
				$modsitemapclass->modname = $modulename;
				if(isset($module[2])) $modsitemapclass->basecat = $module[2];
				$modsitemapclass->langconf = $this->langconf;
				$modsitemapclass->serverpath = $this->config['server_path'];
				$modsitemapclass->fileconfig = $$configname;
				$modsitemapclass->connectdb();
				
				if(array_key_exists('code', $this->langconf)) {
					$langresult = array();
					$langparents = array();
					foreach($this->langconf['code'] as $code) {
						$modsitemapclass->pagelang = $code;
						$langresult = array_merge_recursive($langresult, $modsitemapclass->modsitemap());
						$langparents = array_merge_recursive($langparents, $modsitemapclass->parenturls);
					}
					$result = $langresult;
					$this->modparenturls = $langparents;
				}
				else {
					$result = $modsitemapclass->modsitemap();
					$this->modparenturls = $modsitemapclass->parenturls;
				}
			}
		}
		
		return $result;
    }
}


$mapfile = $webutler_config['server_path'].'/content/access/sitemapxml.txt';

if(!file_exists($mapfile) || filemtime($mapfile) < time()-$revisit_after) {
	$sitemap = new SitemapXML;
	$sitemap->config = $webutler_config;
	$sitemap->offlinepages = $webutler_offlinepages;
	$sitemap->langconf = $webutler_langconf;
	$sitemap->categories = $webutler_categories;
	$sitemap->modulesitemaps = $webutler_modulesitemaps;
	
	$map = '<?xml version="1.0" encoding="UTF-8"?'.'>'."\n";
	$map.= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

	if(array_key_exists('code', $sitemap->langconf)) {
		foreach($sitemap->getpages() as $langs) {
			foreach($langs as $lang => $url) {
				$map.= '  <url>'."\n".
					'      <loc>'.$url['location'].'</loc>'."\n".
					'      <changefreq>always</changefreq>'."\n".
					'      <priority>'.$url['priority'].'</priority>'."\n".
					'  </url>'."\n";
			}
		}
	}
	else {
		foreach($sitemap->getpages() as $url) {
			$map.= '  <url>'."\n".
				'      <loc>'.$url['location'].'</loc>'."\n".
				'      <changefreq>always</changefreq>'."\n".
				'      <priority>'.$url['priority'].'</priority>'."\n".
				'  </url>'."\n";
		}
	}

	$map.= '</urlset>'."\n";
	
	file_put_contents($mapfile, $map);
	
	$oldumask = umask(0);
	@chmod($mapfile, $webutler_config['chmod'][1]);
	umask($oldumask);
}

echo file_get_contents($mapfile);


