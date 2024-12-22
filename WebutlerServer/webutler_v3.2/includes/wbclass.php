<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#includes/wbclass.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

class WebutlerClass
{
    var $getpage;
    
    var $config;
    var $htmlsource;
    
    var $offlinepages;
    var $mailaddresses;
    var $langconf;
    var $categories;
    var $linkhighlite;
    var $moduleslist;
    
    var $formnocookie;
    var $setnewtitlefrommod = '';
    var $errorpagetext = '';
    var $autoheaderdata = array();
    var $autofooterdata = array();
    
    var $loadcontentpage;
    
	function __construct() {}

    function WebutlerClass($name) {
        $this->name = $name;
    }
    
    function checkadmin()
    {
    	if($this->config['admin_name'] == "" && $this->config['admin_pass'] == "")
        {
        	if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']['username'] == md5($this->config['user_name']) 
              && $_SESSION['loggedin']['userpass'] == $this->config['user_pass'])
            {
                return true;
            }
    	}
        else
        {
        	if(isset($_SESSION['loggedin']) && (($_SESSION['loggedin']['username'] == md5($this->config['user_name']) 
                && $_SESSION['loggedin']['userpass'] == $this->config['user_pass']) || 
                ($_SESSION['loggedin']['username'] == md5($this->config['admin_name']) 
                && $_SESSION['loggedin']['userpass'] == $this->config['admin_pass'])))
            {
        		  return true;
        	}
        }
    }
    
    function filenamesigns($name)
    {
    	$name = str_replace(" ", "_", $name);
    	$name = str_replace("-", "_", $name);
    	$name = strtolower($name);
    	$name = preg_replace("/[^a-z0-9_]/", "", $name);
        
        return $name;
    }
    
    function stylefiles()
    {
    	$directory = $this->config['server_path'].'/content/style';					
    	$handle = opendir($directory);
    	$stylefiles = array();
    	while(false !== ($file = readdir($handle))) {
    		if(!is_dir($directory.'/'.$file.'/')) {
        		if($file != '.' && $file != '..' && strtolower(substr($file, strrpos($file, '.'))) == '.css') {
                    $stylefiles[] = array(
						'file' => 'content/style/'.$file,
						'time' => filemtime($directory.'/'.$file)
					);
        		}
            }
    	}
    	closedir($handle);
		
        return $stylefiles;
    }
    
    function loadpage()
    {
        $_getpage = $this->config['server_path'].'/content/pages/'.$this->getpage;
        /*
		// REQUEST_URI check löschen? 
		$_uri = str_replace('http://', '', $this->config['homepage']);
        $_uri = str_replace('https://', '', $_uri);
        $_uri = str_replace($_SERVER['HTTP_HOST'], '', $_uri);
    	$falsche_endung = '0';
        
    	if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != $_uri && $_SERVER['REQUEST_URI'] != $_uri.'/')
        {
            if($this->config['modrewrite'] == '1')
            {
				if(strpos($_SERVER['REQUEST_URI'], '?'))
				{
					$server_request_uri = explode('?', $_SERVER['REQUEST_URI']);
					$_SERVER['REQUEST_URI'] = $server_request_uri[0];
				}
				if($_SERVER['REQUEST_URI'] == 'index.php' || $_SERVER['REQUEST_URI'] == '/index.php') {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: ".$this->config['homepage']."/index".$this->config['urlendung']);
					exit;
				}
				
                if($this->config['urlendung'] != '')
                	$extension = substr($_SERVER['REQUEST_URI'], strlen($this->config['urlendung'])*(-1));
        		elseif(strrpos($_SERVER['REQUEST_URI'], '.'))
                	$extension = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '.'), strlen($_SERVER['REQUEST_URI'])-strrpos($_SERVER['REQUEST_URI'], '.'));
                
           		$extension = strtolower($extension);
           		
                if($extension != $this->config['urlendung'] && substr($_SERVER['REQUEST_URI'], -1, 1) != '/')
        	        $falsche_endung = '1';
    		}
    		else
            {
                if(!strpos($_SERVER['REQUEST_URI'], 'index.php') && substr($_SERVER['REQUEST_URI'], -1, 1) != '/')
        	        $falsche_endung = '1';
    		}
    	}
    	
    	if(!file_exists($_getpage) || $falsche_endung == '1' || $this->getpage == $this->config['ownerrorpage']) 
		*/
    	if(!file_exists($_getpage) || $this->getpage == $this->config['ownerrorpage']) 
    	{
            header('HTTP/1.0 404 Not Found', true, 404);
    		$this->errorpage(_SYSLANG_NOTEXIST_);
    	}
    	elseif($this->formnocookie == true) 
    	{
    		$this->errorpage(_SYSLANG_NOCOOKIES_);
    	}
    	elseif(in_array($this->getpage, $this->offlinepages))
        {
            $this->errorpage(_SYSLANG_NOTPUBLISHED_);
        }
    	elseif(!$this->accesspage($this->getpage))
    	{
    		$this->errorpage(_SYSLANG_NOAUTH_);
    	}
        else
        {
            ob_start();
            $webutlercouple = $this;
            $showpartfor = (isset($_SESSION['userauth']['groupid'])) ? $_SESSION['userauth']['groupid'] : array();
    		include_once $_getpage;
    		$this->loadcontentpage = ob_get_contents();
    		ob_end_clean();
            
			$stylefiles = $this->stylefiles();
			
			if(!preg_match('#content/columns/columns.css#i', $this->loadcontentpage)) {
				$this->autoheaderdata[] = '<link href="content/columns/columns.css?t='.filemtime($this->config['server_path'].'/content/columns/columns.css').'" rel="stylesheet" type="text/css" />';
			}
			else {
				$stylefiles[] = array(
					'file' => 'content/columns/columns.css',
					'time' => filemtime($this->config['server_path'].'/content/columns/columns.css')
				);
			}
			
			foreach($stylefiles as $stylefile) {
				if(preg_match('#'.$stylefile['file'].'#i', $this->loadcontentpage))
					$this->loadcontentpage = preg_replace('#<link([^>]*?)'.$stylefile['file'].'(.*?)>#si', '<link${1}'.$stylefile['file'].'?t='.$stylefile['time'].'${2}>', $this->loadcontentpage);
			}
			
            $this->loadcontainer('block');
    		$this->deleteofflines('page');
            $this->loadcontainer('menu');
            $this->removewbcomments();
            $this->basetohead();
            $this->lookforms();
            $this->autoheaderdata();
            $this->autofooterdata();
			$this->canonicaltag();
    		$this->rewriting();
            if(isset($this->setnewtitlefrommod) && $this->setnewtitlefrommod != '')
            {
                $this->loadcontentpage = preg_replace('#(<title>)(.*?)(</title>)#Usi', '${1}'.$this->setnewtitlefrommod.'${3}', $this->loadcontentpage);
            }
        }
    }
    
    function removewbcomments()
    {
		$this->loadcontentpage = preg_replace('#<!-- begin_([^ ]+) -->#Usi', '', $this->loadcontentpage);
		$this->loadcontentpage = preg_replace('#<!-- end_([^ ]+) -->#Usi', '', $this->loadcontentpage);
    }
    
    function loadcontainer($container, $isadmin = '', $mode = '')
    {
    	$blocks = $this->config['server_path']."/content/".$container."s";
        
    	$handle = opendir($blocks);
    	while(false !== ($blockfile = readdir($handle)))
        {
    		if($blockfile != "." && $blockfile != ".." && $blockfile != ".htaccess" && preg_match("<!-- ".$container."_".$blockfile." -->", $this->loadcontentpage))
            {
    		    if($isadmin == '1')
                {
        			$block = file_get_contents($blocks."/".$blockfile);
                    if($container == 'menu' && $mode == '')
                        $block = $this->setdefaultlink($block, $blockfile);
    		    }
    		    else
                {
        			ob_start();
                    $webutlercouple = $this;
                    $showpartfor = (isset($_SESSION['userauth']['groupid'])) ? $_SESSION['userauth']['groupid'] : array();
        			include $blocks."/".$blockfile;
        			$block = ob_get_contents();
        			ob_end_clean();
        			if($container == 'menu' && !$this->checkadmin())
                        $block = $this->deleteofflines('menu', $block);
                    if($container == 'menu')
                        $block = $this->setdefaultlink($block, $blockfile);
    			}
    			$this->loadcontentpage = str_replace("<!-- ".$container."_".$blockfile." -->", $block, $this->loadcontentpage);
    		}
    	}
    	closedir($handle);
    }
    
    function getlanguagefromuri()
    {
		$lang = '';
		if($this->config['modrewrite'] == '1' && $this->config['languages'] == '1' && $this->config['langfolder'] == '1' && count($this->langconf) > 0) {
			$language = substr($_SERVER['REQUEST_URI'], (substr($_SERVER['REQUEST_URI'], 0, 1) == '/' ? 1 : 0), 3);
			$language = preg_replace("~[^a-z\/]~", "", $language);
			if(strlen($language) == 3 && substr($language, 2, 1) == '/' && in_array(substr($language, 0, 2), $this->langconf['code'])) {
				$lang = substr($language, 0, 2);
			}
		}
		return $lang;
    }
    
    function verifygetpage()
    {
		$lang = $this->getlanguagefromuri();
        
        if(!isset($_GET['page']) || $_GET['page'] == '')
        {
			if($lang != '' || ($this->config['languages'] == '1' && array_key_exists('homes', $this->langconf) && count($this->langconf['homes']) > 0)) {
				if($lang == '') $lang = $this->config['defaultlang'];
				$startfile = $this->langconf['homes'][$lang];
			}
			else {
				$startfile = $this->config['startseite'];
			}
            
            if($startfile == '' || !file_exists($this->config['server_path'].'/content/pages/'.$startfile) || (!$this->checkadmin() && (in_array($startfile, $this->offlinepages) || !$this->accesspage($startfile))))
        	{
        		$directory = $this->config['server_path'].'/content/pages';				
        		$handle = opendir ($directory);
        		$filearray = array();
        		while(false !== ($file = readdir($handle)))
        		{ 
        			$ext = substr($file, -4);
        			if($file != '.' && $file != '..' && $file != '.htaccess' && $ext != '.bak' && $ext != '.tmp' && $file != $this->config['ownerrorpage'] && !in_array($file, $this->offlinepages) && $this->accesspage($file))
        			{
        				$startfile = $file;
        				break;
        			}
        		}
        		closedir($handle);
        	}
            $_getpage = $startfile;
        }
        else
        {
        	$_getpage = $this->filenamesigns($_GET['page']);
			
			if(!file_exists($this->config['server_path'].'/content/pages/'.$_getpage)) {
				if($lang != '') {
					if(in_array($lang.'_'.$_getpage, $this->langconf['pages'][$lang])) {
						$_getpage = $lang.'_'.$_getpage;
					}
					else {
						foreach($this->langconf['pages'][$lang] as $page) {
							foreach($this->langconf['code'] as $langcode) {
								if($page == $langcode.'_'.$_getpage) {
									$_getpage = $langcode.'_'.$_getpage;
									break;
									break;
								}
							}
						}
					}
				}
				else {
					if($this->config['languages'] == '1' && count($this->langconf) > 0 && isset($this->langconf['code'])) {
						foreach($this->langconf['code'] as $langcode) {
							if(file_exists($this->config['server_path'].'/content/pages/'.$langcode.'_'.$_getpage)) {
								$_getpage = $langcode.'_'.$_getpage;
								break;
							}
						}
					}
				}
			}
    	}
        
        $this->getpage = $_getpage;
    }
    
    function errorpage($errorpagetext)
    {
        if(!$this->checkadmin() && $this->config['ownerrorpage'] != '' && file_exists($this->config['server_path'].'/content/pages/'.$this->config['ownerrorpage']))
        {
    		ob_start();
            $webutlercouple = $this;
            $webutlercouple->errorpagetext = $errorpagetext;
    		include $this->config['server_path'].'/content/pages/'.$this->config['ownerrorpage'];
    		$this->loadcontentpage = ob_get_contents();
    		ob_end_clean();
            $this->loadcontainer('block');
    		$this->deleteofflines('page');
            $this->loadcontainer('menu');
            $this->removewbcomments();
            $this->basetohead();
            $this->lookforms();
            $this->autoheaderdata();
            $this->autofooterdata();
    		$this->rewriting();
            $this->loadcontentpage = preg_replace('#(<title>)(.*?)(</title>)#Usi', '${1}'._SYSLANG_ERRORTITLE_.'$3', $this->loadcontentpage);
        }
        else
        {
            if(isset($_SESSION['language']) && array_key_exists('pages', $this->langconf) && !$this->checkadmin())
            {
                $home = $this->langconf['homes'][$_SESSION['language']];
                $link = ($this->config['modrewrite'] == '1') ? $home.$this->config['urlendung'] : 'index.php?page='.$home;
            }
            else
            {
                $ext = ($this->config['modrewrite'] == '1') ? $this->config['urlendung'] : '.php';
                $link = 'index'.$ext;
            }
            
            $lastpage = (isset($_SESSION['history']['lastpage']) && $_SESSION['history']['lastpage'] != '') ? $_SESSION['history']['lastpage'] : $this->config['homepage'].'/';
            
        	$ausgabe = $this->htmlsource['page_header'];
            $ausgabe.= '<title>'._SYSLANG_ERRORTITLE_.'</title>'."\n".
				'<base href="'.$this->config['homepage'].'/" />'."\n".
				'<link href="admin/system/css/admin.css" rel="stylesheet" type="text/css" />'."\n";
            $ausgabe.= $this->htmlsource['close_page_header'];
            $ausgabe.= '<div class="webutler_errortext">'."\n".
				'<strong>'.$errorpagetext.'</strong>'."\n".
				'<br /><br /><br />'."\n".
				'<a href="'.$lastpage.'">'._SYSLANG_ERRORBACK_.'</a> | <a href="'.$link.'">'._SYSLANG_ERRORHOME_.'</a>'."\n".
				'</div>'."\n";
            $ausgabe.= $this->htmlsource['page_footer'];
            
            $this->loadcontentpage = $ausgabe;
        }
    }
    
    function sethistory()
    {
		if($this->config['modrewrite'] == '1' && isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '?')) {
			$server_request_uri = explode('?', $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = $server_request_uri[0];
		}
		
		if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
			if(isset($_SESSION['history']['thispage']) && $_SESSION['history']['thispage'] != 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])
				$_SESSION['history']['lastpage'] = $_SESSION['history']['thispage'];
			
			if((strrpos($_SERVER['REQUEST_URI'], '.') && ($this->config['urlendung'] != '' && substr($_SERVER['REQUEST_URI'], strlen($this->config['urlendung'])*(-1)) == $this->config['urlendung']) || preg_match('#index\.php\?page=#', $_SERVER['REQUEST_URI'])) || 
			  (!strrpos($_SERVER['REQUEST_URI'], '.') && ($this->config['urlendung'] == '' || substr($_SERVER['REQUEST_URI'], -1, 1) == '/')))
				$_SESSION['history']['thispage'] = 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			else
				$_SESSION['history']['thispage'] = $this->config['homepage'].'/';
		}
    }
    
    function basetohead()
    {
    	if(!preg_match('#<base href="(.*?)</head>#Usi', $this->loadcontentpage))
        {
            $basetag = "\n\t".'<base href="'.$this->config['homepage'].'/" />';
            
        	$this->loadcontentpage = str_replace('<head>', '<head>'.$basetag, $this->loadcontentpage);
        }
    }
    
    function autoheaderdata()
    {
        if(count($this->autoheaderdata) > 0)
        {
            $styles = '';
            $scripts = '';
            
            foreach($this->autoheaderdata as $autoheader)
            {
				if(preg_match('#(<link)#i', $autoheader))
					$styles.= $autoheader."\n\t";
				else
					$scripts.= "\t".$autoheader."\n";
            }
			
			$headpos = strpos($this->loadcontentpage, '</head>');
			$firstlink = strpos($this->loadcontentpage, '<link');
			if($firstlink && $firstlink < $headpos) {
				$before = substr($this->loadcontentpage, 0, $firstlink);
				$after = substr($this->loadcontentpage, $firstlink);
				$this->loadcontentpage = $before.$styles.$after;
			}
			else {
				$scripts = $scripts.$styles;
			}
			
        	$this->loadcontentpage = str_replace('</head>', $scripts.'</head>', $this->loadcontentpage);
        }
    }
    
    function autofooterdata()
    {
        if(count($this->autofooterdata) > 0)
        {
            $parts = '';
            
            foreach($this->autofooterdata as $autofooter)
            {
                $parts.= "\t".$autofooter."\n";
            }
        	$this->loadcontentpage = str_replace('</body>', $parts.'</body>', $this->loadcontentpage);
        }
    }
    
    function canonicaltag()
    {
    	if(!preg_match('#<head>(.*?)rel="canonical"(.*?)</head>#Usi', $this->loadcontentpage))
        {
			$canonicalurl = 'index.php?page='.$this->getpage;
			$canonicaltag = '<link href="'.$canonicalurl.'" rel="canonical" />';
            
        	$this->loadcontentpage = str_replace('</head>', "\t".$canonicaltag."\n".'</head>', $this->loadcontentpage);
        }
    }
    
    function lookforms()
    {
    	if(preg_match('#</form>#Usi', $this->loadcontentpage))
    	{
            $antixpostcode = $this->antixpostcode();
            $_SESSION['antixpost'] = $antixpostcode;
            $this->loadcontentpage = str_replace('</form>', '<input type="hidden" name="webutler_autokill" value="'.md5($antixpostcode).'" />'."\n".'</form>', $this->loadcontentpage);
        }
    }
    
    function setdefaultlink($block, $file)
    {
        $defaults = array_key_exists('files', $this->linkhighlite) ? $this->linkhighlite['files'] : '';
        
        if($defaults != '' && count($defaults) > 0)
        {
            foreach($defaults as $default)
            {
                if($file == $default[1])
                {
                    $block = $this->setclasstolink($block, $default[0]);
					
					if(isset($default[2]) && $default[2] == 'yes')
					{
						$block = $this->setclasstoparents($block, $default[0]);
					}
					
					break;
                }
            }
        }
        
        if($this->config['modrewrite'] == '1')
        {
            $folders = array_key_exists('folders', $this->linkhighlite) ? $this->linkhighlite['folders'] : '';
            
            if($folders != '' && count($folders) > 0) 
            {
                $domain = substr($this->config['homepage'], strpos($this->config['homepage'], '//')+2);
                $subfolder = substr($domain, strpos($domain, '/'));
				if($subfolder != $domain && strlen($subfolder) > 0)
					$uri = substr($_SERVER['REQUEST_URI'], strlen($subfolder));
				else
					$uri = $_SERVER['REQUEST_URI'];
				
                $after = '';
                if(preg_match("#\/".($this->getpage.$this->config['urlendung'])."#Usi", $uri))
                    $after = $this->config['urlendung'];
                elseif(preg_match("#\/".($this->getpage)."(?:([\w\d\-]+))?".($this->config['urlendung'])."#Usi", $uri))
                    $after = '-';
                
                $categories = substr($uri, 0, strpos($uri, '/'.$this->getpage.$after));
                if(substr($categories, 0, 1) == '/')
                    $categories = substr($categories, 1, strlen($categories));
                if(substr($categories, -1) == '/')
                    $categories = substr($categories, 0, strlen($categories)-1);
                
                if($after != '' && $categories != '')
                {
                    $cats = explode('/', $categories);
                    $sub = '';
                    for($i = 0; $i < count($cats); $i++)
                    {
						if($i == 0 && $this->config['langfolder'] == '1' && strlen($cats[0]) == 2 && ctype_alpha($cats[0]) && $this->config['languages'] == '1' && count($this->langconf) > 0 && in_array($cats[0], $this->langconf['code']))
							continue;
						
						$sub = $sub != '' ? $sub.'/'.$cats[$i] : $cats[$i];
						foreach($folders as $folder)
						{
							$return = ($i < count($cats)-1 || ($folder[3] == 'yes' && $i == count($cats)-1)) ? 'yes' : 'no';
							if($return == 'yes' && $folder[1] == $sub && $folder[2] == $file)
							{
								$block = $this->setclassoffolder($block, $folder[0], $folder[1]);
							}
						}
                    }
                }
            }
        }
        
        return $block;
    }
    
    function setclasstolink($block, $class)
    {
        $pattern = '~<li(?:([^>]+))?>(?:([^\/]+))?<a([^>]+)(href=\")(index\.php\?page='.$this->getpage.')([\&|\"])(?:([^>]+))?'.'>~Umsi';
        
        $block = preg_replace_callback($pattern, function($match) use ($class)
        {
            if(preg_match('#class=\"#', $match[1]))
            {
                $match[1] = preg_replace('#class=\"([^"]+)\"#Umsi', 'class="$1 '.$class.'"', $match[1]);
            }
            else
            {
                $match[1] = ' class="'.$class.'"'.$match[1];
            }
            
            $result = $match[3].$match[4].$match[5].$match[6];
            if(isset($match[7])) $result.= $match[7];
            
            return '<li'.$match[1].'>'.$match[2].'<a'.$result.'>';
            
        }, $block);
        
        return $block;
    }
	
    function escapescripts($source)
    {
		if(preg_match('#<script#i', $source)) {
			$source = preg_replace_callback(
				'#(<script)(.*?)(</script>)#si',
				function($matches) {
					return rawurlencode($matches[1].$matches[2].$matches[3]);
				},
			$source);
		}
		
		return $source;
    }
	
    function unescapescripts($source)
    {
		if(preg_match('#%3Cscript#i', $source)) {
			$source = preg_replace_callback(
				'#(%3Cscript)(.*?)(%3C%2Fscript%3E)#si',
				function($matches) {
					return rawurldecode($matches[1].$matches[2].$matches[3]);
				},
			$source);
		}
		
		return $source;
    }
	
    function setclasstoparents($block, $class)
    {
		$block = str_replace('&nbsp;', '&amp;nbsp;', $block);
		$block = $this->escapescripts($block);
		
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = true;
		$dom->formatOutput = false;
		libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="UTF-8">'.$block);
		libxml_use_internal_errors(false);
		libxml_clear_errors();
		$dom->encoding = 'UTF-8';
		$xpath = new DOMXPath($dom);
		foreach($xpath->query('//li[@class="'.$class.'"]') as $node)
		{
			$nodepath = $node->getNodePath();
			$segment = $dom->getElementsByTagName('body')->item(0);
			$elements = explode('/', $nodepath);
			array_pop($elements);
			foreach($elements as $element)
			{
				$nodename = preg_match('#\[#', $element) ? substr($element, 0, strpos($element, '[')) : $element;
				$key = preg_match('#\[#', $element) ? preg_replace("/[^0-9]/", "", $element)-1 : 0;
				
				if($nodename == 'li') {
					$k = 0;
					foreach($segment->childNodes as $level) {
						if($level->nodeName == 'li') {
							if($k == $key) {
								if($level->hasAttribute('class')) {
									$oldclass = $level->getAttribute('class');
									$level->setAttribute('class', $oldclass.' '.$class);
								}
								else {
									$level->setAttribute('class', $class);
								}
								$segment = $level;
								break;
							}
							$k++;
						}
					}
				}
				else {
					$k = 0;
					foreach($segment->childNodes as $level) {
						if($level->nodeName == $nodename) {
							if($k == $key) {
								$segment = $level;
								break;
							}
							$k++;
						}
					}
				}
			}
		}
		
		$body = $xpath->query('/html/body');
		$result = preg_replace('#<(?:/?)body[^>]*?'.'>#Usi', '', $dom->saveHTML($body->item(0)));
		$result = $this->unescapescripts($result);
		$result = str_replace('&amp;nbsp;', '&nbsp;', $result);
        
        return $result;
    }
	
    function setclassoffolder($block, $class, $folder)
    {
        if(array_key_exists('pages', $this->categories) && count($this->categories['pages']) > 0)
        {
            $catpages = $this->categories['pages'];
            
            foreach($catpages as $cat => $pages)
            {
				if($folder == $cat)
				{
					foreach($pages as $page)
					{
                        if($page != $this->getpage)
                        {
                            $pattern = '~<li(?:([^>]+))?>(?:([^\/]+))?<a([^>]+)(href=\")(index\.php\?page='.$page.')([\&|\"])(?:([^>]+))?'.'>~Umsi';
                            
                            $block = preg_replace_callback($pattern, function($match) use ($class)
                            {
                                if(preg_match('#class=\"#', $match[1]))
                                {
                                    $match[1] = preg_replace('#class=\"([^"]+)\"#Umsi', 'class="$1 '.$class.'"', $match[1]);
                                }
                                else
                                {
                                    $match[1] = ' class="'.$class.'"'.$match[1];
                                }
                                
                                $result = $match[3].$match[4].$match[5].$match[6];
                                if(isset($match[7])) $result.= $match[7];
                                
                                return '<li'.$match[1].'>'.$match[2].'<a'.$result.'>';
                                
                            }, $block);
                        }
                    }
                }
            }
        }
            
        return $block;
    }
    
    function rewriting()
    {
    	$type = '([href|action]=\")';
    	$link = '(index\.php\?page=)';
    	
    	if($this->config['modrewrite'] == '1')
        {
    		$count = ($this->config['urlgetvars']*2+2);
    		$name = '([\w\d\_]+)';
    		$var = '(?:&(?:amp;)?([\w\d\_]+))?';
    		$param = '(?:=([\w\d\_\^]+))?';
    		
    		$h = 0;
        	while($h < 2)
            {
            	for($i = 0; $i < ($count+$h); $i++)
                {
                    if($h == 0 || $h == 1)
                        $search = $type.$link.$name;
                    
                    //for($k = (3+$h); $k <= $i; $k++)
                    for($k = (4+$h); $k <= $i; $k++)
                    {
                        $search.= $var.$param;
                        $k = $k+1;
                    }
                    $this->loadcontentpage = preg_replace_callback('#'.$search.'\"#Usi', array('WebutlerClass', 'getpagecategory'), $this->loadcontentpage);
                    $this->loadcontentpage = preg_replace_callback('#'.$search.'(\#)([\w\d\_]+)\"#Usi', array('WebutlerClass', 'getpagecategory'), $this->loadcontentpage);
            	}
                $h++;
        	}
    	}
    	else
        {
            $search = $type.$link.'('.$this->config['startseite'].')';
            $this->loadcontentpage = preg_replace('#'.$search.'\"#Usi', '${1}index.php"', $this->loadcontentpage);
    		$this->loadcontentpage = preg_replace('#'.$search.'\#([\w\d\_]*)\"#Usi', '${1}index.php#$4"', $this->loadcontentpage);
    		$this->loadcontentpage = preg_replace('#'.$search.'&(amp;)?((?!amp;)[^\"]*)\"#Usi', '${1}index.php?$5"', $this->loadcontentpage);
    	}
    }
    
    function setlangascategory($page)
    {
        $lang = '';
		if($this->config['modrewrite'] == '1' && $this->config['languages'] == '1' && count($this->langconf) > 0 && $this->config['langfolder'] == '1') {
        	$language = $this->getlangfrompage($page);
			if($language && $language != '') {
				$lang = $language;
				
				if($page == $this->langconf['homes'][$language]) {
					$page = 'index';
				}
				else {
					if(substr($page, 2, 1) == '_') {
						$checklang = substr($page, 0, 2);
						$checklang = preg_replace("~[^a-z]~", "", $checklang);
						
						if(strlen($checklang) == 2 && $checklang == $lang && in_array($page, $this->langconf['pages'][$lang])) {
							$page = substr($page, 3);
						}
					}
				}
			}
		}
		else {
			if($page == $this->config['startseite']) {
				$page = 'index';
			}
		}
		
		return array($lang, $page);
    }
    
    function getcategoryforpage($page)
    {
		$frontpages = array();
		if($this->config['languages'] == '1' && $this->config['langfolder'] == '1' && count($this->langconf) > 0) {
            foreach($this->langconf['homes'] as $lang => $frontpage) {
				$frontpages[] = $frontpage;
			}
		}
		else {
			$frontpages[] = $this->config['startseite'];
		}
		
        $pagecat = '';
        if(!in_array($page, $frontpages) && $this->config['categories'] == 1 && is_array($this->categories) && count($this->categories) > 0)
        {
            foreach($this->categories['pages'] as $cat => $pages)
            {
                if(in_array($page, $pages))
                {
                    $pagecat = $cat.'/';
                    break;
                }
            }
        }
        return $pagecat;
    }
    
    function getpagecategory($match)
    {
		$getcategoryforpage = $this->getcategoryforpage($match[3]);
        $checklang = $this->setlangascategory($match[3]);
		$lang = $checklang[0] != '' ? $checklang[0].'/' : '';
		$page = $checklang[1];
        $result = $match[1].$lang.$getcategoryforpage.$page;
        
        $matches = count($match);
        $hasanchor = '';
        if($matches > 3)
        {
            for($i = 4; $i < $matches; $i++)
            {
                if($match[$i] == '#')
                {
                    $hasanchor = $match[$i+1];
                    break;
                }
                else
                {
                    $result.= '-'.$match[$i].'-'.$match[$i+1];
                    $i = $i+1;
                }
            }
        }
        
        $result.= $this->config['urlendung'];
        
        if($hasanchor != '') $result.= '#'.$hasanchor;
        
        return $result.'"';
    }
    
    function accesspage($page)
    {
        $userdbfile = $this->config['server_path'].'/content/access/users.db';
        
        if(file_exists($userdbfile) && class_exists('SQLite3'))
        {
            $userdb = new SQLite3($userdbfile);
            
            $blocks = $userdb->query("SELECT id, pages FROM blocks WHERE blocks.id = '1' LIMIT 1");
        	if($block = $blocks->fetchArray())
            {
                $blockedfiles = explode(',', $block['pages']);
                if(in_array($page, $blockedfiles))
                {
                    $groups = $userdb->query("SELECT id, pages FROM groups");
                    while($group = $groups->fetchArray())
                    {
                        $id = $group['id'];
                        $pages = explode(',', $group['pages']);
                        if(in_array($page, $pages))
                        {
    	                    if(isset($_SESSION['userauth']['groupid']) && in_array($id, $_SESSION['userauth']['groupid']))
                                return true;
                        }
                    }
                    return false;
                }
            }
            $userdb->close();
        }
        return true;
    }
    
    function deleteofflines($where, $content = '')
    {
        $userdbfile = $this->config['server_path'].'/content/access/users.db';
        
        if(file_exists($userdbfile) && class_exists('SQLite3')) {
            $userdb = new SQLite3($userdbfile);
            
            $blocks = $userdb->query("SELECT id, pages FROM blocks WHERE blocks.id = '1' LIMIT 1");
        	if($block = $blocks->fetchArray())
            {
                $blockedfiles = explode(',', $block['pages']);
            	foreach($blockedfiles as $userpage)
                {
                    if($userpage != '' && !$this->accesspage($userpage))
                        $this->offlinepages[] = $userpage;
                }
            }
            $userdb->close();
        }
    
        if(($this->config['offline_links'] == '1' || $this->config['offline_links'] == '2') && count($this->offlinepages) >= 1)
        {
            foreach($this->offlinepages as $offlinepage)
            {
                if($where == 'page')
                {
                    $this->loadcontentpage = preg_replace('#<a([^>]+)(href=\"index\.php\?page='.$offlinepage.')(?=\"|\&)([^>]+)>([^<]+)</a>#Umsi', '$4', $this->loadcontentpage);
                }
                if($where == 'menu')
                {
                    if($this->config['offline_links'] == '1')
                    {
                        $content = preg_replace('#<a([^>]+)(href=\"index\.php\?page='.$offlinepage.')(?=\"|\&)([^>]+)>([^<]+)</a>#Umsi', '<span>$4</span>', $content);
                    }
                    elseif($this->config['offline_links'] == '2')
                    {
                        $content = $this->deletelistitem($content, $offlinepage);
                    }
                }
            }
        }
        
        if($where == 'menu')
            return $content;
    }
    
    function deletelistitem($content, $item)
    {
		$prepaired = $content;
        $hrefs = preg_match_all('#(href=\"index\.php\?page='.$item.')(?!\d|\w|\_)([^\"]*)(\")#Usi', $content, $hrefstrs);
		
        if($hrefs)
        {
			$content = str_replace('&nbsp;', '&amp;nbsp;', $content);
			$content = $this->escapescripts($content);
			
            $dom = new DOMDocument;
			$dom->preserveWhiteSpace = true;
			$dom->formatOutput = false;
			libxml_use_internal_errors(true);
			$dom->loadHTML('<?xml encoding="UTF-8">'.$content);
			libxml_use_internal_errors(false);
			libxml_clear_errors();
			$dom->encoding = 'UTF-8';
            $xpath = new DOMXPath($dom);
            for($i = 0; $i < $hrefs; $i++)
            {
                $hrefquery = '//a[@'.$hrefstrs[0][$i].']';
                foreach($xpath->query($hrefquery) as $result)
                {
                    while($result)
                    {
                        if($result->nodeName == 'li')
                        {
                            $result->parentNode->removeChild($result);
                            break;
                        }
                        $result = $result->parentNode;
                    }
                }
            }
			$savedbody = $xpath->query('/html/body');
			$prepaired = preg_replace('#<(?:/?)body[^>]*?'.'>#Usi', '', $dom->saveHTML($savedbody->item(0)));
			$prepaired = $this->unescapescripts($prepaired);
			$prepaired = str_replace('&amp;nbsp;', '&nbsp;', $prepaired);
        }
    
        return $prepaired;
    }
    
    function setlangdefines()
    {
        if($this->config['languages'] == '1')
        {
        	$lang = $this->getlangfrompage($this->getpage);
            if($lang && $lang != '')
                $_SESSION['language'] = $lang;
        }
        else {
            if(isset($_SESSION['language']))
                unset($_SESSION['language']);
        }
    	
        $syslangpath = $this->config['server_path'].'/includes/language/lang';
        if(isset($_SESSION['language']) && file_exists($syslangpath.'/'.$_SESSION['language'].'.php'))
        {
            $pagelanguage = $syslangpath.'/'.$_SESSION['language'].'.php';
        }
        else
        {
            $pagelanguage = $syslangpath.'/'.$this->config['defaultlang'].'.php';
        }
        
        return $pagelanguage;
    }
    
    function getlangfrompage($page)
    {
        if($this->config['languages'] == '1' && count($this->langconf) > 0 && array_key_exists('pages', $this->langconf))
        {
            $lang = '';
            foreach($this->langconf['code'] as $code)
            {
                if(in_array($page, $this->langconf['pages'][$code]))
                {
                    $lang = $code;
                    break;
                }
            }
            
            return $lang;
        }
        
        return false;
    }
	
	function getuserip()
	{
		$ipaddress = '';
		
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(!empty($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(!empty($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(!empty($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(!empty($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		
		return $ipaddress;
	}
    
    function loginattempts()
    {
        if($this->config['logattemptmin'] == '')
		{
			return true;
		}
		else
		{
			$userip = $this->getuserip();
			if($userip == 'UNKNOWN')
			{
				return false;
			}
			else
			{
				$loginattemptsfile = $this->config['server_path'].'/content/access/loginblockedips.php';
				if(!file_exists($loginattemptsfile)) {
					$blockedipsarray = "<?PHP\n\n\$webutler_blockedips = array();\n\n";
					file_put_contents($loginattemptsfile, $blockedipsarray);
					$this->setchmodaftersave($loginattemptsfile);
				}
				include $loginattemptsfile;
				
				if(!isset($_SESSION['logattempts']))
					$_SESSION['logattempts'] = 1;
				else
					$_SESSION['logattempts'] = $_SESSION['logattempts']+1;
				
				if(array_key_exists($userip, $webutler_blockedips)) {
					if($webutler_blockedips[$userip] < time()-60*$this->config['logattemptmin']) {
						unset($webutler_blockedips[$userip]);
						unset($_SESSION['logattempts']);
						$this->saveblockedips($loginattemptsfile, $webutler_blockedips);
						return true;
					}
					else {
						return false;
					}
				}
				else {
					if($_SESSION['logattempts'] >= 5) {
						if($_SESSION['logattempts'] == 5) {
							unset($_SESSION['logattempts']);
							$webutler_blockedips[$userip] = time();
							$this->saveblockedips($loginattemptsfile, $webutler_blockedips);
						}
						return false;
					}
					else {
						return true;
					}
				}
			}
		}
    }
    
    function saveblockedips($file, $blockedips)
    {
		$content = "<?PHP\n\n\$webutler_blockedips = array(\n";
		$ips = array();
        foreach($blockedips as $ip => $time) {
			$ips[] = "\t'".$ip."' => '".$time."'";
        }
		$content.= implode(",\n", $ips);
		$content.= "\n);\n\n";
        
        file_put_contents($file, $content);
        $this->setchmodaftersave($file);
    }
    
    function loadsigns($length, $signs)
    {
        $part = '';
        for($i = 0; $i < $length; $i++)
        {
            $part.= $signs[mt_rand(0, strlen($signs)-1)];
        }
        return $part;
    }
    
    function antixpostcode()
    {
        $antixpost = $this->loadsigns(3, 'abcdefghijkmnpqrstuvwxyz');
        $antixpost.= $this->loadsigns(3, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $antixpost.= $this->loadsigns(2, '1234567890');
        $antixpost.= $this->loadsigns(2, '#+*@%&=!?');
        
        return str_shuffle($antixpost);
    }
    
    function checkantixpost()
    {
        if(!$this->checkadmin())
        {
            if(count($_POST) > 0)
            {
                $this->formnocookie = false;
                if(!isset($_SESSION['antixpost']) || (isset($_POST['webutler_autokill']) && $_POST['webutler_autokill'] != md5($_SESSION['antixpost'])))
                {
                    unset($_POST);
                    unset($_SESSION['antixpost']);
                    $this->formnocookie = true;
                }
                else
                {
                    unset($_POST['webutler_autokill']);
                    unset($_SESSION['antixpost']);
                    
                    array_walk($_POST, array('WebutlerClass', 'stripcode'));
                }
            }
            if(count($_GET) > 0)
            {
                array_walk($_GET, array('WebutlerClass', 'stripcode'));
            }
        }
    }
    
    function stripcode(&$value, $key)
    {
        $key = trim(htmlspecialchars($key, ENT_QUOTES));
        
        $value = preg_replace('#(.*)(<\?.*\?'.'>)(.*)#Usi', '${1}$3', $value);
        $value = str_replace('<?', '', $value);
        $value = str_replace('?'.'>', '', $value);
    }
    
    function setchmodaftersave($file)
    {
		$oldumask = umask(0);
		@chmod($file, $this->config['chmod'][1]);
		umask($oldumask);
    }
}


class WebutlerAdminClass extends WebutlerClass
{
    var $adminpage;
    
    var $loading_css;
    var $loading_div;
    
    var $plugins_fulleditor;
    var $plugins_blockeditor;
    var $plugins_menueditor;
    var $plugins_metaseditor;
    var $plugins_contenteditor;
    
    var $cke_cssclasses;
	
    var $basefonts;
    
	function __construct($useformsendto = '', $usecolumns = '', $useshowprotected = '') {
        $this->adminpage = '';
        
        $this->loading_css = '<link href="admin/system/css/loading.css" rel="stylesheet" type="text/css" />';
        $this->loading_div = "\n".'<div id="webutler_loadingscreen">&nbsp;</div>'."\n";
        
        $this->plugins_fulleditor = 'savetemppage,docprops,browser,closeeditor,stylesheetparser,'.$usecolumns.'flashplayer,'.$useshowprotected.'lightbox,video,audio,formchanges,formfields,'.$useformsendto.'pagelinks,imageedit,googlemap,insertscript,disabletoolbar,measuretool,pastedupload,sourcedialog';
		
        $this->plugins_blockeditor = 'browser,closeeditor,stylesheetparser,'.$usecolumns.'flashplayer,'.$useshowprotected.'lightbox,video,audio,formchanges,formfields,'.$useformsendto.'pagelinks,imageedit,googlemap,pastedupload,sourcedialog';
		
        $this->plugins_menueditor = 'browser,closeeditor,stylesheetparser,sortmenu,'.$useshowprotected.'lightbox,pagelinks,imageedit,pastedupload,sourcedialog';
		
        $this->plugins_metaseditor = 'savetemppage,docprops,browser,closeeditor,'.$usecolumns.'video,audio,googlemap,insertscript,disabletoolbar,measuretool,sourcedialog';
		
        $this->plugins_contenteditor = 'saveinline,savetemppage,autogrow,docprops,browser,closeeditor,stylesheetparser,'.$usecolumns.'flashplayer,'.$useshowprotected.'lightbox,video,audio,googlemap,formchanges,formfields,'.$useformsendto.'pagelinks,insertscript,imageedit,disabletoolbar,measuretool,pastedupload,sourcedialog';
		
        $this->basefonts = array('Arial/Arial, Helvetica, sans-serif', 'Comic/Comic Sans MS, cursive', 'Courier/Courier New, Courier, monospace', 'Georgia/Georgia, serif', 'Lucida/Lucida Sans Unicode, Lucida Grande, sans-serif', 'Tahoma/Tahoma, Geneva, sans-serif', 'Times/Times New Roman, Times, serif', 'Trebuchet/Trebuchet MS, Helvetica, sans-serif', 'Verdana/Verdana, Geneva, sans-serif');
    }
    
    function check_temp_fileexists($file)
    {
		return file_exists($file.'.tmp') ? $file.'.tmp' : $file;
    }
    
    
    // Seitenansicht Admin
    function admineditbox()
    {
    	require_once $this->config['server_path'].'/admin/system/editbox.php';
        
        return $boxresult;
    }
    
    function adminpageview()
    {
        $this->sethistory();
        $_getpage = $this->check_temp_fileexists($this->config['server_path'].'/content/pages/'.$this->getpage);
        
    	if(file_exists($_getpage))
    	{
            require_once $this->config['server_path'].'/admin/system/lang/'.$_SESSION['loggedin']['userlang'].'.php';
            
            $logoimg = '<img id="webutler_logo" src="admin/system/images/webutler.png" />'."\n";
    		$this->autoheaderdata[] = $this->loading_css;
    		
            $pageisoff = '';
            if(file_exists($this->config['server_path'].'/content/pages/'.$this->getpage.'.tmp'))
                $pageisoff = '<div id="webutler_pageisoff">'._WBLANGADMIN_OFF_PAGEUNPUBLIC_.'</div>'."\n";
            elseif(in_array($this->getpage, $this->offlinepages))
                $pageisoff = '<div id="webutler_pageisoff">'._WBLANGADMIN_OFF_PAGEISOFFLINE_.'</div>'."\n";
            elseif(!$this->accesspage($this->getpage))
                $pageisoff = '<div id="webutler_pageisoff">'._WBLANGADMIN_OFF_PAGEISUSERS_.'</div>'."\n";
            
            $this->loadcontentpage = file_get_contents($_getpage);
			
			$admineditbox = $this->admineditbox();
			
			$stylefiles = $this->stylefiles();
			
			if(!preg_match('#content/columns/columns.css#i', $this->loadcontentpage)) {
				$this->autoheaderdata[] = '<link href="content/columns/columns.css?t='.filemtime($this->config['server_path'].'/content/columns/columns.css').'" rel="stylesheet" type="text/css" />';
			}
			else {
				$stylefiles[] = array(
					'file' => 'content/columns/columns.css',
					'time' => filemtime($this->config['server_path'].'/content/columns/columns.css')
				);
			}
			
			foreach($stylefiles as $stylefile) {
				if(preg_match('#'.$stylefile['file'].'#i', $this->loadcontentpage))
					$this->loadcontentpage = preg_replace('#<link([^>]*?)'.$stylefile['file'].'(.*?)>#si', '<link${1}'.$stylefile['file'].'?t='.$stylefile['time'].'${2}>', $this->loadcontentpage);
			}
			
            $this->loadcontainer('menu', 1);
            $this->loadcontainer('block', 1);
            $this->showallparts();
            
            ob_start();
            $webutlercouple = $this;
    		$showpartfor = array('-1');
            echo eval("?".">".$this->loadcontentpage);
    		$this->loadcontentpage = ob_get_contents();
    		ob_end_clean();
    		
    		$this->rewriting();
            $this->basetohead();
            $this->autoheaderdata();
            $this->autofooterdata();
			$this->canonicaltag();
            if(isset($this->setnewtitlefrommod) && $this->setnewtitlefrommod != '') {
                $this->loadcontentpage = preg_replace('#(<title>)(.*?)(</title>)#Usi', '${1}'.$this->setnewtitlefrommod.'$3', $this->loadcontentpage);
            }
    		$this->loadcontentpage = preg_replace('#(<body[^>]*?'.'>)#Usi', '$1'.$this->loading_div.$pageisoff.$logoimg, $this->loadcontentpage);
    		$this->loadcontentpage = str_replace('</body>', $admineditbox.'</body>', $this->loadcontentpage);
    	}
    	else
    	{
    		$this->errorpage(_WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_);
    	}
    }
    
    
    // Editoren
    
    // WYSIWYG Editor
    function adminheader()
    {
    	$header = $this->htmlsource['page_header'];
    	$header.= '<title>WEBUTLER - Administration</title>'."\n";
        $header.= '<script src="admin/ckeditor/ckeditor.js"></script>'."\n".
			'<script>'."\n".
        	'/* <![CDATA[ */'."\n".
            '   CKEDITOR.on(\'instanceCreated\', function( evt )'."\n".
            '   {'."\n".
                    // Mit maximiced Editor funktioniert $loading_div nicht
            '       var loadingDiv = document.createElement(\'div\');'."\n".
            '       var addLoadDiv = document.body.appendChild(loadingDiv);'."\n".
            '       addLoadDiv.id = \'webutler_loadingscreen\';'."\n".
            '   });'."\n".
            '   CKEDITOR.on(\'instanceReady\', function( evt )'."\n".
            '   {'."\n".
            '       document.getElementById(\'webutler_loadingscreen\').style.display = \'none\';'."\n".
            '       var editor = evt.editor;'."\n".
            '       editor.execCommand( \'maximize\' );'."\n".
            '       editor.addCommand( \'maximize\','."\n".
            '       {'."\n".
            '           exec : function( editor ) {}'."\n".
            '       });'."\n".
			'		editor.filter.addTransformations([[\'img{width,height}: sizeToStyle\']]);'."\n".
            '   });'."\n".
            '   var imageeditorWindowWidth = \''.$this->config['imageeditor_wh'][0].'\';'."\n".
            '   var imageeditorWindowHeight = \''.$this->config['imageeditor_wh'][1].'\';'."\n".
            '   var codemirror_rootpath = \'admin/\';'."\n".
            '/* ]]> */'."\n".
        	'</script>'."\n";
    	$header.= $this->loading_css."\n";
		$header.= '<link href="admin/system/css/editor.css" rel="stylesheet" type="text/css" />'."\n";
    	$header.= $this->htmlsource['close_page_header'];
        
        return $header;
    }
    
    function adminpageedit()
    {
        $post_edit = $this->filenamesigns($_POST['edit']);
		$contentFile = $this->check_temp_fileexists($this->config['server_path'].'/content/pages/'.$post_edit);
		if(file_exists($contentFile))
		{
			$wzjsgraphics = '<script src="admin/system/javascript/wz_jsgraphics.js"></script>'."\n";
			$linealdiv = '<div id="WBeditor_linealdiv"></div>'."\n";
			$editorcss = '<link href="admin/system/css/editor.css" rel="stylesheet" type="text/css" />'."\n";
			
			$combobgcss = '';
			if(isset($this->config['ckecombobg']) && $this->config['ckecombobg'] != '' && strlen($this->config['ckecombobg']) > 2 && strlen($this->config['ckecombobg']) < 7) {
				$combobgcss.= '<style type="text/css">'."\n".
					'/* <![CDATA[ */'."\n".
					'	.cke_combopanel .cke_panel_frame{background-color:#'.$this->config['ckecombobg'].'}'."\n".
					'/* ]]> */'."\n".
					'</style>'."\n";
			}
			
            $this->adminpage = $this->adminheader();
			$this->adminpage = str_replace('</head>', $wzjsgraphics.$combobgcss.'</head>', $this->adminpage);
    		$this->adminpage.= $linealdiv.'<form id="wb_editorsaveform" action="'.$this->config['homepage'].'/admin/system/save.php?page='.$this->getpage.'" method="post">'."\n";
            
		    $this->loadcontentpage = file_get_contents($contentFile);
            $this->loadcontainer('menu', 1, 'edit');
            $this->loadcontainer('block', 1, 'edit');
			
			$stylefiles = $this->stylefiles();
			
			if(!preg_match('#content/columns/columns.css#i', $this->loadcontentpage)) {
				$columnscss = '<link href="content/columns/columns.css?t='.filemtime($this->config['server_path'].'/content/columns/columns.css').'" rel="stylesheet" type="text/css" />';
			}
			else {
				$columnscss = '';
				$stylefiles[] = array(
					'file' => 'content/columns/columns.css',
					'time' => filemtime($this->config['server_path'].'/content/columns/columns.css')
				);
			}
			
			foreach($stylefiles as $stylefile) {
				if(preg_match('#'.$stylefile['file'].'#i', $this->loadcontentpage))
					$this->loadcontentpage = preg_replace('#<link([^>]*?)'.$stylefile['file'].'(.*?)>#si', '<link${1}'.$stylefile['file'].'?t='.$stylefile['time'].'${2}>', $this->loadcontentpage);
			}
			
			$this->loadcontentpage = str_replace('</head>', $editorcss.$columnscss.'</head>', $this->loadcontentpage);
			
			$this->adminpage.= '<textarea id="fulleditor" name="fulleditor" style="visibility: hidden">'.htmlspecialchars($this->loadcontentpage).'</textarea>'."\n";
			
			$this->adminpage.= '<script>'."\n".
				'/* <![CDATA[ */'."\n".
				'	CKEDITOR.replace( \'fulleditor\', {'."\n".
                '       customConfig : \'wb_ckeconfig.js\','."\n".
				'       language : \''.$_SESSION['loggedin']['userlang'].'\','."\n".
				'       baseHref : \''.$this->config['homepage'].'/\','."\n".
                '       fullPage : true,'."\n";
			if($this->cke_cssclasses == 1) {
				$this->adminpage.= '       justifyClasses : [ \'alignleft\', \'aligncenter\', \'alignright\', \'alignjustify\' ],'."\n".
					'       indentClasses : [ \'indent1\', \'indent2\', \'indent3\', \'indent4\', \'indent5\' ],'."\n";
			}
            $this->adminpage.= '       extraPlugins : \''.$this->plugins_fulleditor.'\','."\n".
                '       removePlugins : \'autogrow\','."\n".
                '       toolbar : \'EditPage\','."\n".
                '       embed_provider : \''.$this->config['ckeembed'].'\','."\n".
                '       font_names : \''.$this->editorfonts().'\','."\n".
				'		colorButton_colors : \''.$this->editorcicolors().'\' + CKEDITOR.config.colorButton_colors,'."\n".
				'       contentsCss : [\''.implode('\', \'', $this->editorstyles()).'\'],'."\n".
                '       filebrowserWindowWidth : \''.$this->config['mediabrowser_wh'][0].'\','."\n".
                '       filebrowserWindowHeight : \''.$this->config['mediabrowser_wh'][1].'\''."\n".
                '   });'."\n";
			if(isset($this->config['playercolor']) && $this->config['playercolor'] != '' && strlen($this->config['playercolor']) > 2 && strlen($this->config['playercolor']) < 7) {
                $this->adminpage.= '   CKEDITOR.on(\'instanceReady\', function( evt )'."\n".
					'   {'."\n".
					'       playerConf[\'color\'] = \''.$this->config['playercolor'].'\';'."\n".
					'   });'."\n";
			}
			$this->adminpage.= '/* ]]> */'."\n".
				'</script>'."\n";
            
    		$this->adminpage.= "</form>";
            $this->adminpage.= $this->htmlsource['page_footer'];
		}
		else
		{
			$this->adminpage.= _WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_;
		}
	}
    
    function adminpageblock()
    {
        $post_block = $this->filenamesigns($_POST['block']);
		$block = $this->config['server_path'].'/content/blocks/'.$post_block;
		if(file_exists($block))
		{
		    $blockfile = file_get_contents($block);
			
            $adminheader = $this->adminheader();

			if(isset($this->config['ckecombobg']) && $this->config['ckecombobg'] != '' && strlen($this->config['ckecombobg']) > 2 && strlen($this->config['ckecombobg']) < 7) {
				$admincss = '<style type="text/css">'."\n".
					'/* <![CDATA[ */'."\n".
					'	.cke_combopanel .cke_panel_frame{background-color:#'.$this->config['ckecombobg'].'}'."\n".
					'/* ]]> */'."\n".
					'</style>'."\n";
				
				$adminheader = str_replace('</head>', $admincss.'</head>', $adminheader);
			}
			
    		$this->adminpage.= $adminheader;
			
    		$this->adminpage.= '<form action="'.$this->config['homepage'].'/admin/system/save.php?page='.$this->getpage.'&block='.$post_block.'" method="post">'."\n";
			$this->adminpage.= '<textarea id="blockeditor" name="blockeditor" style="visibility: hidden">'.htmlspecialchars($blockfile).'</textarea>'."\n";
			
			$this->adminpage.= '<script>'."\n".
				'/* <![CDATA[ */'."\n".
				'	CKEDITOR.replace( \'blockeditor\', {'."\n".
                '       customConfig : \'wb_ckeconfig.js\','."\n".
				'       language : \''.$_SESSION['loggedin']['userlang'].'\','."\n".
				'       baseHref : \''.$this->config['homepage'].'/\','."\n";
			if($this->cke_cssclasses == 1) {
				$this->adminpage.= '       justifyClasses : [ \'alignleft\', \'aligncenter\', \'alignright\', \'alignjustify\' ],'."\n".
					'       indentClasses : [ \'indent1\', \'indent2\', \'indent3\', \'indent4\', \'indent5\' ],'."\n";
			}
            $this->adminpage.= '       extraPlugins : \''.$this->plugins_blockeditor.'\','."\n".
                '       removePlugins : \'autogrow\','."\n".
                '       toolbar : \'EditBlock\','."\n".
                '       embed_provider : \''.$this->config['ckeembed'].'\','."\n".
                '       bodyClass : \'WBeditor_blockframe\','."\n".
                '       font_names : \''.$this->editorfonts().'\','."\n".
				'		colorButton_colors : \''.$this->editorcicolors().'\' + CKEDITOR.config.colorButton_colors,'."\n".
				'       contentsCss : [\''.implode('\', \'', $this->editorstyles()).'\', \''.$this->config['homepage'].'/admin/system/css/editor.css\'],'."\n".
                '       filebrowserWindowWidth : \''.$this->config['mediabrowser_wh'][0].'\','."\n".
                '       filebrowserWindowHeight : \''.$this->config['mediabrowser_wh'][1].'\''."\n".
                '   });'."\n";
			if(isset($this->config['ckeblockbg']) && $this->config['ckeblockbg'] != '' && strlen($this->config['ckeblockbg']) > 2 && strlen($this->config['ckeblockbg']) < 7) {
				$this->adminpage.= '	CKEDITOR.addCss(\'.WBeditor_blockframe{background-color:#'.$this->config['ckeblockbg'].';}\');'."\n";
			}
			if(isset($this->config['playercolor']) && $this->config['playercolor'] != '' && strlen($this->config['playercolor']) > 2 && strlen($this->config['playercolor']) < 7) {
                $this->adminpage.= '   CKEDITOR.on(\'instanceReady\', function( evt )'."\n".
				'   {'."\n".
				'		playerConf[\'color\'] = \''.$this->config['playercolor'].'\';'."\n".
				'   });'."\n";
			}
			$this->adminpage.= '/* ]]> */'."\n".
				'</script>'."\n";
            
    		$this->adminpage.= "</form>";
            $this->adminpage.= $this->htmlsource['page_footer'];
		}
		else
		{
			$this->adminpage.= _WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_;
		}
	}
            	
    function adminpagemenu()
    {
    	$post_menu = $this->filenamesigns($_POST['menu']);
    	$menu = $this->config['server_path'].'/content/menus/'.$post_menu;
    	if(file_exists($menu))
    	{
		    $menufile = file_get_contents($menu);
            $adminheader = $this->adminheader();
			
			if(isset($this->config['ckecombobg']) && $this->config['ckecombobg'] != '' && strlen($this->config['ckecombobg']) > 2 && strlen($this->config['ckecombobg']) < 7) {
				$admincss = '<style type="text/css">'."\n".
					'/* <![CDATA[ */'."\n".
					'	.cke_combopanel .cke_panel_frame{background-color:#'.$this->config['ckecombobg'].'}'."\n".
					'/* ]]> */'."\n".
					'</style>'."\n";
				
				$adminheader = str_replace('</head>', $admincss.'</head>', $adminheader);
			}
			
            $this->adminpage.= $adminheader;
			
        	$this->adminpage.= '<form action="'.$this->config['homepage'].'/admin/system/save.php?page='.$this->getpage.'&menu='.$post_menu.'" method="post">'."\n";
			$this->adminpage.= '<textarea id="menueditor" name="menueditor" style="visibility: hidden">'.htmlspecialchars($menufile).'</textarea>'."\n";

			$this->adminpage.= '<script>'."\n".
				'/* <![CDATA[ */'."\n".
				'	CKEDITOR.replace( \'menueditor\', {'."\n".
                '       customConfig : \'wb_ckeconfig.js\','."\n".
				'       language : \''.$_SESSION['loggedin']['userlang'].'\','."\n".
				'       baseHref : \''.$this->config['homepage'].'/\','."\n";
			if($this->cke_cssclasses == 1) {
				$this->adminpage.= '       justifyClasses : [ \'alignleft\', \'aligncenter\', \'alignright\', \'alignjustify\' ],'."\n".
					'       indentClasses : [ \'indent1\', \'indent2\', \'indent3\', \'indent4\', \'indent5\' ],'."\n";
			}
            $this->adminpage.= '       extraPlugins : \''.$this->plugins_menueditor.'\','."\n".
                '       removePlugins : \'autogrow,magicline\','."\n".
                '       toolbar : \'EditMenu\','."\n".
                '       embed_provider : \''.$this->config['ckeembed'].'\','."\n".
                '       bodyClass : \'WBeditor_menuframe\','."\n".
                '       font_names : \''.$this->editorfonts().'\','."\n".
				'		colorButton_colors : \''.$this->editorcicolors().'\' + CKEDITOR.config.colorButton_colors,'."\n".
				'       contentsCss : [\''.implode('\', \'', $this->editorstyles()).'\', \''.$this->config['homepage'].'/admin/system/css/editor.css\'],'."\n".
                '       filebrowserWindowWidth : \''.$this->config['mediabrowser_wh'][0].'\','."\n".
                '       filebrowserWindowHeight : \''.$this->config['mediabrowser_wh'][1].'\''."\n".
                '   });'."\n";
				if(isset($this->config['ckemenubg']) && $this->config['ckemenubg'] != '' && strlen($this->config['ckemenubg']) > 2 && strlen($this->config['ckemenubg']) < 7) {
					$this->adminpage.= '	CKEDITOR.addCss(\'.WBeditor_menuframe{background-color:#'.$this->config['ckemenubg'].';}\');'."\n";
				}
			$this->adminpage.= '/* ]]> */'."\n".
				'</script>'."\n";
            
        	$this->adminpage.= "</form>";
            $this->adminpage.= $this->htmlsource['page_footer'];
    	}
    	else
    	{
    		$this->adminpage.= _WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_;
    	}
	}
            	
    function adminpagecontent()
    {
		$editcss = '<link href="admin/system/css/editor.css" rel="stylesheet" type="text/css" />'."\n";
        
    	$ckeditor = '<script src="admin/ckeditor/ckeditor.js"></script>'."\n";
    	$wzjsgraphics = '<script src="admin/system/javascript/wz_jsgraphics.js"></script>'."\n";
        
    	$cketoolbar = '<div id="WBeditor_toolbar"></div>'."\n";
    	$linealdiv = '<div id="WBeditor_linealdiv"></div>'."\n";
		
        $openform = '<form id="wb_editorsaveform" action="'.$this->config['homepage'].'/admin/system/save.php?page='.$this->getpage.'" method="post">'."\n";
		$closeform = '</form>'."\n";
		
		$contfuncs = '<script src="admin/system/javascript/wb_jquery.js"></script>'."\n".
			'<script>'."\n".
			'/* <![CDATA[ */'."\n".
			'	var locationPage = \''.$this->getpage.'\';'."\n".
			'/* ]]> */'."\n".
			'</script>'."\n".
			'<script src="admin/system/javascript/contfunc.js"></script>'."\n";
    	
    	$postcontent = $this->filenamesigns($_POST['content']);
    	$contentfile = $this->check_temp_fileexists($this->config['server_path'].'/content/pages/'.$postcontent);
    	
    	if(file_exists($contentfile))
    	{
    		$loadcontent = file_get_contents($contentfile);
			
			$editcss.= '<style type="text/css">'."\n".
				'/* <![CDATA[ */'."\n".
				'	.wb_blockelement, .wb_menuelement { outline: 0px !important; }'."\n";
			if(isset($this->config['ckecombobg']) && $this->config['ckecombobg'] != '' && strlen($this->config['ckecombobg']) > 2 && strlen($this->config['ckecombobg']) < 7) {
				$editcss.= '	.cke_combopanel .cke_panel_frame{ background-color:#'.$this->config['ckecombobg'].' }'."\n";
			}
			$editcss.= '/* ]]> */'."\n".
				'</style>'."\n";
    		
    		$pageheader = preg_split('#<body#', $loadcontent);
    		$editmetas = $pageheader[0]."<body>\n<p>&nbsp;</p>\n</body>\n</html>";
    		$hiddenmetas = '<div style="overflow: hidden; position: fixed; width: 0px; height: 0px; left: -1000px">'."\n".
        		'<textarea name="metas" id="metas" style="visibility: hidden">'.htmlspecialchars($editmetas).'</textarea>'."\n".
        		'<script>'."\n".
        		'/* <![CDATA[ */'."\n".
				'	var WBcontvar_PageInstances = new Array();'."\n".
				'	var WBcontvar_ScriptInstances = new Array();'."\n".
				'	var WBcontvar_InstancesLoaded = false;'."\n".
				'	var WBcontvar_IframeIsLoaded = false;'."\n".
				'	var WBcontvar_IframeIntervalId = false;'."\n".
				'	var WBcontvar_PageCount;'."\n".
    			'	CKEDITOR.replace( \'metas\', {'."\n".
                '       customConfig : \'wb_ckeconfig.js\','."\n".
        		'       language : \''.$_SESSION['loggedin']['userlang'].'\','."\n".
    			'       baseHref : \''.$this->config['homepage'].'/\','."\n".
                '       fullPage : true,'."\n".
                '       startupFocus : true,'."\n".
                '       extraPlugins : \''.$this->plugins_metaseditor.'\','."\n".
                '       removePlugins : \'autogrow,maximize,magicline\','."\n".
                '       toolbar : \'EditContent\','."\n".
                '       font_names : \''.$this->editorfonts().'\','."\n".
                '       sharedSpaces :'."\n".
        		'		{'."\n".
        		'			top : \'WBeditor_toolbar\''."\n".
        		'		},'."\n".
                '   });'."\n".
                '   CKEDITOR.on(\'instanceReady\', function( evt )'."\n".
                '   {'."\n".
                '       var editor = evt.editor;'."\n".
                '       if(editor.name != \'metas\') {'."\n".
                '           if( editor.getCommand( \'insertscript\' ) ) {'."\n".
                '               editor.getCommand( \'insertscript\' ).setState( CKEDITOR.TRISTATE_DISABLED );'."\n".
                '       	}'."\n".
                '       }'."\n";
				if(isset($this->config['playercolor']) && $this->config['playercolor'] != '' && strlen($this->config['playercolor']) > 2 && strlen($this->config['playercolor']) < 7) {
					$hiddenmetas.= '       else {'."\n".
						'       	playerConf[\'color\'] = \''.$this->config['playercolor'].'\';'."\n".
						'       }'."\n";
				}
			$hiddenmetas.= '       WBcontvar_ScriptInstances.push(editor.name);'."\n".
                '   });'."\n".
        		'	WBcontvar_PageInstances.push(\'metas\') ;'."\n".
                '   var imageeditorWindowWidth = \''.$this->config['imageeditor_wh'][0].'\';'."\n".
                '   var imageeditorWindowHeight = \''.$this->config['imageeditor_wh'][1].'\';'."\n".
                '   var codemirror_rootpath = \'admin/\';'."\n".
				'/* ]]> */'."\n".
        		'</script>'."\n".
        		'</div>'."\n";
            
            $this->loadcontentpage = $loadcontent;
            $this->loadcontainer('block', 1);
            $this->loadcontainer('menu', 1);
			
			$stylefiles = $this->stylefiles();
			
			if(!preg_match('#content/columns/columns.css#i', $this->loadcontentpage)) {
				$columnscss = '<link href="content/columns/columns.css?t='.filemtime($this->config['server_path'].'/content/columns/columns.css').'" rel="stylesheet" type="text/css" />';
			}
			else {
				$columnscss = '';
				$stylefiles[] = array(
					'file' => 'content/columns/columns.css',
					'time' => filemtime($this->config['server_path'].'/content/columns/columns.css')
				);
			}
			
			foreach($stylefiles as $stylefile) {
				if(preg_match('#'.$stylefile['file'].'#i', $this->loadcontentpage))
					$this->loadcontentpage = preg_replace('#<link([^>]*?)'.$stylefile['file'].'(.*?)>#si', '<link${1}'.$stylefile['file'].'?t='.$stylefile['time'].'${2}>', $this->loadcontentpage);
			}
			
            $this->showallparts();
            
            ob_start();
            $webutlercouple = $this;
    		$showpartfor = array('-1');
            echo eval("?".">".$this->loadcontentpage);
    		$this->loadcontentpage = ob_get_contents();
    		ob_end_clean();
            
    		$this->rewriting();
            $this->autoheaderdata();
            $this->autofooterdata();
            
            $contentpage = $this->loadcontentpage;
    		$contentpage = str_replace("</head>", $ckeditor.$wzjsgraphics.$editcss.$columnscss.$this->loading_css."\n</head>", $contentpage);
    		$contentpage = preg_replace("#(<body[^>]*?".">)#Usi", "$1".$this->loading_div.$cketoolbar.$linealdiv.$openform.$hiddenmetas, $contentpage);
    		$contentpage = preg_replace('#(<title>)(.*?)(</title>)#Usi', '$1WEBUTLER - Administration$3', $contentpage);
    
    		$editierbar = substr_count($contentpage, '<!-- begin_content -->');
    		if($editierbar > 0)
    		{
				$startpoint = '<!-- begin_content -->';
				$endpoint = '<!-- end_content -->';
				$htmlsources = preg_split('#'.preg_quote($startpoint).'.*?'.preg_quote($endpoint).'#si', $contentpage);
				preg_match_all('#('.preg_quote($startpoint).')(.*?)('.preg_quote($endpoint).')#si', $loadcontent, $editsources);
				
				foreach($htmlsources as $count => $htmlsource)
				{
					if($count == $editierbar) {
						$this->adminpage.= str_replace('</body>', $closeform.$contfuncs.'</body>', $htmlsource);
					}
					else {
						$this->adminpage.= $htmlsource;
					}
					
					if($count < $editierbar) {
						$nextid = $count+1;
						
						$this->adminpage.= '<textarea name="content_'.$nextid.'" id="content_'.$nextid.'" style="visibility: hidden">'.htmlspecialchars($editsources[2][$count]).'</textarea>'."\n";
						
						$this->adminpage.= '<script>'."\n".
							'/* <![CDATA[ */'."\n".
							'	CKEDITOR.inline( \'content_'.$nextid.'\', {'."\n".
							'       customConfig : \'wb_ckeconfig.js\','."\n".
							'       language : \''.$_SESSION['loggedin']['userlang'].'\','."\n".
							'       baseHref : \''.$this->config['homepage'].'/\','."\n".
							'       fullPage : false,'."\n".
							'       startupFocus : true,'."\n";
						if($this->cke_cssclasses == 1) {
							$this->adminpage.= '       justifyClasses : [ \'alignleft\', \'aligncenter\', \'alignright\', \'alignjustify\' ],'."\n".
								'       indentClasses : [ \'indent1\', \'indent2\', \'indent3\', \'indent4\', \'indent5\' ],'."\n";
						}
						$this->adminpage.= '       extraPlugins : \''.$this->plugins_contenteditor.'\','."\n".
							'       removePlugins : \'maximize\','."\n".
							'       toolbar : \'EditContent\','."\n".
							'       embed_provider : \''.$this->config['ckeembed'].'\','."\n".
							'       font_names : \''.$this->editorfonts().'\','."\n".
							'		colorButton_colors : \''.$this->editorcicolors().'\' + CKEDITOR.config.colorButton_colors,'."\n".
							'       autoGrow_minHeight : 30,'."\n".
							'       autoGrow_bottomSpace : 10,'."\n".
							'		autoGrow_onStartup : true,'."\n".
							'       sharedSpaces :'."\n".
							'		{'."\n".
							'			top : \'WBeditor_toolbar\''."\n".
							'		},'."\n".
							'       contentsCss : [\''.implode('\', \'', $this->editorstyles()).'\'],'."\n".
							'       filebrowserWindowWidth : \''.$this->config['mediabrowser_wh'][0].'\','."\n".
							'       filebrowserWindowHeight : \''.$this->config['mediabrowser_wh'][1].'\','."\n".
							'   });'."\n".
							'	WBcontvar_PageInstances.push(\'content_'.$nextid.'\') ;'."\n".
							'/* ]]> */'."\n".
							'</script>'."\n";
					}
				}
			
        	}
    	}
    	else
    	{
    		$this->adminpage.= _WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_;
    	}
    }
    
    
    // Quellcode-Editor
    function adminsourceedit()
    {
        $template = '';
        
    	if(isset($_POST['editnewlayout']))
    	{
            $editlayoutfile = $this->checkfilenamesigns($_POST['editlayout'], 'tpl');
    		$templatefile = $this->config['server_path'].'/content/layouts/'.$editlayoutfile;
            $template = (file_exists($templatefile)) ? $templatefile : '';
    		$file = $editlayoutfile;
    		$mode = 'html';
    		$savename = 'layoutfile';
    	}
    	elseif(isset($_POST['editnewstyles']))
    	{
			if($_POST['editstyles'] == 'columns') {
				$columnscssfile = $this->config['server_path'].'/content/columns/source/csscolumns.php';
				$template = (file_exists($columnscssfile)) ? $columnscssfile : '';
				$file = 'columns.css';
				$mode = 'css';
				$savename = 'columnstyle';
			}
			else {
				$ext = substr($_POST['editstyles'], strrpos($_POST['editstyles'], '.') + 1);
				$editstylefile = $this->checkfilenamesigns($_POST['editstyles'], $ext);
				$sourcefile = $this->config['server_path'].'/content/style/source/'.$editstylefile;
				$templatefile = $this->config['server_path'].'/content/style/'.$editstylefile;
				if(!file_exists($sourcefile) && file_exists($templatefile)) {
					copy($templatefile, $sourcefile);
				}
				$template = (file_exists($sourcefile)) ? $sourcefile : '';
				$file = $editstylefile;
				$mode = 'css';
				$savename = 'stylefile';
			}
    	}
        
        if($template != '')
        {
        	$editfile = file_get_contents($template);
        	$editfile = htmlspecialchars($editfile);
        	
        	$this->adminpage.= $this->htmlsource['page_header']."\n".
				'<title>WEBUTLER - Administration</title>'."\n".
				'<script src="admin/codemirror/editor/js/codemirror.js"></script>'."\n".
				'<script src="admin/codemirror/lang/'.$_SESSION['loggedin']['userlang'].'.js"></script>'."\n".
				'<style>'."\n".
				'/* <![CDATA[ */'."\n".
				'    body {'."\n".
				'        padding: 0px;'."\n".
				'        margin: 0px;'."\n".
				'        background-color: #FFFFFF;'."\n".
				'    }'."\n".
				'/* ]]> */'."\n".
				'</style>'."\n".
				'<link href="admin/codemirror/config/editor.css" rel="stylesheet" type="text/css" />'."\n";
        	$this->adminpage.= $this->htmlsource['close_page_header'].
				'<form action="'.$this->config['homepage'].'/admin/system/save.php?page='.$this->getpage.'&file='.$file.'" method="post" name="codemirror_form" style="margin: 0px; padding: 0px">'."\n".
				'<div id="codemirror_editormenu"><div id="buttons"></div></div>'."\n".
				'<textarea name="codemirror_editorsource" id="codemirror_editorsource" cols="120" rows="30">'."\n";
        	$this->adminpage.= $editfile;
        	$this->adminpage.= '</textarea>'."\n".
				'<input type="hidden" name="'.$savename.'" />'."\n".
				'</form>'."\n".
				'<script>'."\n".
				'/* <![CDATA[ */'."\n".
				'   var codemirror_syntaxmode = \''.$mode.'\';'."\n".
				'   var codemirror_lastpage = \'index.php?page='.$this->getpage.'\';'."\n".
				'/* ]]> */'."\n".
				'</script>'."\n".
				'<script src="admin/codemirror/codemirror_config.js"></script>'."\n";
        	$this->adminpage.= $this->htmlsource['page_footer'];
        }
        else
        {
    		$this->adminpage.= _WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_;
        }
    }
    
    
    // Minify CSS
    function minifycsssource($source)
    {
		$source = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $source);
    	$source = str_replace(array("\t", "\r", "\n"), '', $source);
		$source = preg_replace('/\s+/', ' ', $source);
		$source = str_replace(array(': ', '; ', ', ', '{ ', '} '), array(':', ';', ',', '{', '}'), $source);
		$source = str_replace(array(' :', ' ;', ' ,', ' {', ' }'), array(':', ';', ',', '{', '}'), $source);
    		
        return $source;
    }
    
    
    // Editor Vorlagen
    function admineditpattern()
    {
		$patterneditfile = $this->checkfilenamesigns($_POST['patternedit'], 'tpl');
		$templatefile = $this->config['server_path'].'/content/pattern/files/'.$patterneditfile;
		$template = (file_exists($templatefile)) ? $templatefile : '';
		$patterninfos = $this->config['server_path'].'/content/pattern/patterninfos.php';
        
        if($template != '')
        {
        	$editfile = file_get_contents($template);
        	$editfile = htmlspecialchars($editfile);
			
			$patterntitle = '';
			$patternimage = '';
			$patterndesc = '';
			
			require_once $patterninfos;
			foreach($infos as $info)
			{
				if($info['file'] == $patterneditfile)
				{
					$patterntitle = $info['title'];
					$patternimage = $info['image'];
					$patterndesc = $info['description'];
				}
			}
        	
        	$this->adminpage.= $this->htmlsource['page_header']."\n".
				'<title>WEBUTLER - Administration</title>'."\n".
				'<script src="admin/codemirror/editor/js/codemirror.js"></script>'."\n".
				'<script src="admin/codemirror/lang/'.$_SESSION['loggedin']['userlang'].'.js"></script>'."\n".
				'<style>'."\n".
				'/* <![CDATA[ */'."\n".
				'    body {'."\n".
				'        padding: 0px;'."\n".
				'        margin: 0px;'."\n".
				'        background-color: #FFFFFF;'."\n".
				'    }'."\n".
				'/* ]]> */'."\n".
				'</style>'."\n".
				'<link href="admin/codemirror/config/editor.css" rel="stylesheet" type="text/css" />'."\n".
				'<link href="admin/system/css/editbox.css" rel="stylesheet" type="text/css" />'."\n";
        	$this->adminpage.= $this->htmlsource['close_page_header'].
				'<form action="'.$this->config['homepage'].'/admin/system/save.php?page='.$this->getpage.'" method="post" name="codemirror_form" style="margin: 0px; padding: 0px">'."\n".
				'<div id="webutler_patterninfos">'."\n".
				'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
				'<tr>'."\n".
				'<td colspan="2"><strong class="webutler_headline">'._WBLANGADMIN_WIN_PATTERN_EDITTEMP_.'</strong></td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td style="width: 120px"><strong>'._WBLANGADMIN_WIN_PATTERN_EDITTITLE_.':</strong></td>'."\n".
				'<td><input type="text" name="title" value="'.$patterntitle.'" class="webutler_input" /></td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td><strong>'._WBLANGADMIN_WIN_PATTERN_EDITIMAGE_.':</strong></td>'."\n".
				'<td><input type="text" name="image" value="'.$patternimage.'" id="webutler_tplicon" class="webutler_input" /><input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_MEDIA_.'" onclick="codemirror_searchicon()" class="webutler_button webutler_searchicon" /></td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td><strong>'._WBLANGADMIN_WIN_PATTERN_EDITDESC_.':</strong></td>'."\n".
				'<td><input type="text" name="description" value="'.$patterndesc.'" class="webutler_input webutler_patterndesc" /></td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td colspan="2" style="height: 20px; padding-bottom: 0px"><strong>'._WBLANGADMIN_WIN_PATTERN_EDITSOURCE_.':</strong></td>'."\n".
				'</tr>'."\n".
				'</table>'."\n".
				'</div>'."\n".
				'<div id="codemirror_editormenu"><div id="buttons"></div></div>'."\n".
				'<textarea name="source" id="codemirror_patternsource" cols="120" rows="30">'."\n";
        	$this->adminpage.= $editfile;
        	$this->adminpage.= '</textarea>'."\n".
				'<div id="webutler_patternsave">'."\n".
				'	<input type="submit" name="savepattern" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" class="webutler_button webutler_mainbutton" />'."\n".
				'	<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" onclick="window.location=window.location;" class="webutler_button webutler_mainbutton" />'."\n".
				'	<input type="hidden" name="patternfile" value="'.$patterneditfile.'" />'."\n".
				'</div>'."\n".
				'</form>'."\n".
				'<script>'."\n".
				'/* <![CDATA[ */'."\n".
				'	function codemirror_searchicon() {'."\n".
				'		var iWidth = \''.$this->config['mediabrowser_wh'][0].'\';'."\n".
				'		if ( typeof iWidth == \'string\' && iWidth.length > 1 && iWidth.substr( iWidth.length - 1, 1 ) == \'%\' )'."\n".
				'			iWidth = parseInt( window.screen.width * parseInt( iWidth, 10 ) / 100, 10 );'."\n\n".
				'	    var iHeight = \''.$this->config['mediabrowser_wh'][1].'\';'."\n".
				'		if ( typeof iHeight == \'string\' && iHeight.length > 1 && iHeight.substr( iHeight.length - 1, 1 ) == \'%\' )'."\n".
				'			iHeight = parseInt( window.screen.height * parseInt( iHeight, 10 ) / 100, 10 );'."\n\n".
				'		if(iWidth < 640) iWidth = 640;'."\n".
				'		if(iHeight < 420) iHeight = 420;'."\n\n".
				'		var iTop = parseInt( ( window.screen.height - iHeight ) / 2, 10 );'."\n".
				'		var iLeft = parseInt( ( window.screen.width  - iWidth ) / 2, 10 );'."\n\n".
				'		var WindowFeatures = \'width=\' + iWidth + \',height=\' + iHeight + \',left=\' + iLeft + \',top=\' + iTop + \',directories=no,location=no,menubar=no,status=no,toolbar=no,resizable=yes,scrollbars=yes\';'."\n".
				'		var BrowseUrl = \'admin/browser/index.php?types=image&actualfolder=%2Ftpl_icons%2F\';'."\n\n".
				'		var popupWindow = window.open( \'\', \'CKBrowseTplIcons\', WindowFeatures, true );'."\n\n".
				'		if ( !popupWindow )'."\n".
				'			return false;'."\n\n".
				'		try {'."\n".
				'			popupWindow.moveTo( iLeft, iTop );'."\n".
				'			popupWindow.resizeTo( iWidth, iHeight );'."\n".
				'			popupWindow.focus();'."\n".
				'			popupWindow.location.href = BrowseUrl;'."\n".
				'		}'."\n".
				'		catch ( e ) {'."\n".
				'			popupWindow = window.open( BrowseUrl, \'CKBrowseTplIcons\', WindowFeatures, true );'."\n".
				'		}'."\n".
				'	}'."\n\n".
				'   var codemirror_syntaxmode = \'html\';'."\n".
				'/* ]]> */'."\n".
				'</script>'."\n".
				'<script src="admin/codemirror/codemirror_config.js"></script>'."\n";
        	$this->adminpage.= $this->htmlsource['page_footer'];
        }
        else
        {
    		$this->adminpage.= _WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_;
        }
    }
    
    // Editorfehler bereinigen
    function deletelastpfromsource($content)
    {
        $start = strrpos($content, '<p>');
        $hasbody = strrpos($content, '</body>');
        $length = ($hasbody === false) ? strlen($content)-$start : $hasbody-$start;
        $substring = substr($content, $start, $length);
        if(preg_replace('#(\s)+#', '', $substring) == '<p>&nbsp;</p>')
        {
            $content = substr_replace($content, '', $start, strlen($substring));
        }
        
        return $content;
    }
    
    
    // Editorfunktionen
    function checkeditelements()
    {
		$content = $this->loadcontentpage;
		
		$result = array();
		$result['editors'] = substr_count($content, '<!-- begin_content -->');
		$result['columns'] = substr_count($content, 'wb_columnselement') + substr_count($content, 'wb_contentelement') + substr_count($content, 'wb_menuelement') + substr_count($content, 'wb_blockelement');
		
        return $result;
    }
    
    function testblockfiles($folder)
    {
        $result = 'false';
        
    	$directory = $this->config['server_path'].'/content/'.$folder;
    	$handle = opendir($directory);
    	while(false !== ($file = readdir ($handle))) {
        	if(!is_dir($directory.'/'.$file.'/') && $file != '.' && $file != '..' && $file != '.htaccess') {
                $result = 'true';
                break;
        	}
        }
    	closedir($handle);
    	
        return $result;
    }
    
    function showallparts()
    {
        $this->loadcontentpage = preg_replace('#(in_array\()([\'"])([0-9]+)([\'"])(\,(?:[\s])?\$showpartfor\))#Usi', '${1}${2}-1$4$5', $this->loadcontentpage);
    }
    
    function editorfonts()
    {
        $allfonts = $this->basefonts;
        $webfonts = $this->webfonts();
    	if($webfonts && is_array($webfonts)) {
            foreach($webfonts as $webfont => $names) {
				$fontname = str_replace(array('-', '_', '.'), array(' ', ' ', ' '), $webfont);
				$fontname = ucwords($fontname);
                $allfonts[] = $fontname.'/'.$webfont;
			}
        }
        natcasesort($allfonts);
        
        return implode(';', $allfonts);
    }
    
    function webfonts()
    {
    	$fontnames = array();
    	$directory = $this->config['server_path'].'/includes/webfonts';					
    	$handle = opendir ($directory);
    	$filearray = array();
    	while(false !== ($file = readdir($handle)))
    	{
            $ext = strtolower(substr($file, strrpos($file, '.')));
    		if($file != '.' && $file != '..' && ($ext == '.eot' || $ext == '.ttf'))
    		{
                $name = substr($file, 0, strrpos($file, '.'));
                $fontnames[$name][] = $ext;
    		}
    	}
    	closedir($handle);
    	
    	if(count($fontnames) >= 1) {
            return $fontnames;
        }
        return false;
    }
    
    function editorcicolors()
    {
		$result = '';
		if($this->config['editorcicolors'] != '')
		{
			$editorcicolors = str_replace('#', '', $this->config['editorcicolors']);
			$cicolors = explode(',', $editorcicolors);
			
			$colors = array();
			for($i = 0; $i < 8; $i++) {
				$color = 'fff';
				if(isset($cicolors[$i])) {
					$color = trim($cicolors[$i]);
					if(strlen($color) > 6) $color = substr($color, 0, 6);
				}
				$colors[$i] = $color;
			}
			
			$result = implode(',', $colors).',';
		}
		
		return $result;
    }
    
    function editorstyles()
    {
    	$directory = $this->config['server_path'].'/content/style';					
    	$handle = opendir ($directory);
    	$stylesheets = array();
		$cssnotineditor = explode(',', $this->config['cssnotineditor']);
    	while(false !== ($file = readdir ($handle))) {
    		if(!is_dir($directory.'/'.$file.'/')) {
        		if($file != '.' && $file != '..') {
                    $ext = strtolower(substr($file, strrpos($file, '.')));
                    if($ext == '.css' && !in_array($file, $cssnotineditor))
						$stylesheets[] = $this->config['homepage'].'/content/style/'.$file.'?t='.filemtime($directory.'/'.$file);
        		}
            }
    	}
    	closedir($handle);
		
        return $stylesheets;
    }
    
    
    // Selects
    function buildstyleselect()
    {
    	$directory = $this->config['server_path'].'/content/style';					
    	$handle = opendir ($directory);
    	$pagestyles = array();
    	while(false !== ($file = readdir ($handle))) {
    		if(!is_dir($directory.'/'.$file.'/')) {
        		if($file != '.' && $file != '..') {
                    $ext = strtolower(substr($file, strrpos($file, '.')));
                    if($ext == '.css')
                        $pagestyles[] = '<option value="'.$file.'">'.$file.'</option>';
        		}
            }
    	}
    	closedir($handle);
    	
        $result = implode("\n", $pagestyles)."\n";
        
        return $result;
    }
    
    function catsselect($page = '')
    {
        $options = array();
        if(array_key_exists('cats', $this->categories) && count($this->categories['cats']) > 0) {
            $categories = $this->categories['cats'];
            if($this->config['languages'] == '1' && array_key_exists('lang', $this->langconf) && count($this->langconf['lang']) > 0) {
                foreach($categories as $lang => $cats) {
                    if(array_key_exists($lang, $this->langconf['lang'])) {
                        $optgroup = '<optgroup label="'.$this->langconf['lang'][$lang].'">'."\n";
                        foreach($cats as $cat) {
                            $selected = '';
                            if($page != '' && array_key_exists($cat, $this->categories['pages'])) {
                                if(in_array($page, $this->categories['pages'][$cat]))
                                    $selected = ' selected="selected"';
                            }
                            $optgroup.= '<option value="'.$cat.'"'.$selected.'>'.$cat.'</option>'."\n";
                        }
                        $optgroup.= '</optgroup>';
                        
                        $options[] = $optgroup;
                    }
                }
            }
            else {
                if(isset($categories[$this->config['defaultlang']]) && is_array($categories[$this->config['defaultlang']])) {
					$categories = $categories[$this->config['defaultlang']];
				}
                foreach($categories as $cat) {
                    $selected = '';
                    if($page != '' && array_key_exists($cat, $this->categories['pages'])) {
                        if(in_array($page, $this->categories['pages'][$cat]))
                            $selected = ' selected="selected"';
                    }
                    $options[] = '<option value="'.$cat.'"'.$selected.'>'.$cat.'</option>';
                }
            }
        }
        
        return $options;
    }
    
    function langselect($default = '')
    {
    	$options = array();
        if(array_key_exists('lang', $this->langconf)) {
    		foreach($this->langconf['lang'] as $lang => $value) {
                $selected = ($lang == $default) ? ' selected="selected"' : '';
                $options[] = '<option value="'.$lang.'"'.$selected.'>'.$lang.': '.$value.'</option>';
            }
        }
        if(count($options) == 0) {
            $options[] = '<option value="">'._WBLANGADMIN_WIN_PAGELANG_NOLANGUAGE_.'</option>';
        }
        else {
            array_unshift($options, '<option value=""></option>');
        }
        return $options;
    }
    
    function buildselect($folder, $selopt, $noerror = false)
    {
    	$options = array();
    	$lang_pages = false;
        $allpages = array();
		$optgroup = array();
        $optclose = '';
        if($folder == 'pages' && $this->config['languages'] == '1' && array_key_exists('pages', $this->langconf)) {
    		$lang_pages = true;
    		$codes = $this->langconf['code'];
    		$langs = $this->langconf['lang'];
            
            foreach($codes as $code) {
                $allpages = array_merge($allpages, $this->langconf['pages'][$code]);
            }
            
    		foreach($langs as $lang => $value) {
                $optgroup[$lang] = '<optgroup label="'.$value.'">'."\n";
            }
            $optclose = '</optgroup>'."\n";
        }
        
    	$directory = $this->config['server_path'].'/content/'.$folder;
    	$handle = opendir($directory);
    	while(false !== ($file = readdir ($handle))) {
        	if(!is_dir($directory.'/'.$file.'/')) {
            	$extension = '';
            	$selected = '';
                if($folder == 'pages' || $folder == 'menus' || $folder == 'blocks') {
        			$extension = substr($file, strrpos($file, '.'));
        			$extension = strtolower($extension);
        		}
                $errorpage = $noerror ? $this->config['ownerrorpage'] : '';
                
                if(!array_key_exists('nolang', $options))
                    $options['nolang'] = array();
                
        		if($file != '.' && $file != '..' && $file != '.htaccess' && $file != $errorpage && $extension != '.bak' && $extension != '.tmp') {
        			$selected = ($selopt != '' && $file == $selopt) ? ' selected="selected"' : '';
                    
        			if($lang_pages !== false && in_array($file, $allpages)) {
                        foreach($codes as $code) {
                            if(!array_key_exists($code, $options))
                                $options[$code] = array();
                            
                            if(array_key_exists('pages', $this->langconf) && in_array($file, $this->langconf['pages'][$code])) {
                                $options[$code][] = '<option value="'.$file.'"'.$selected.'>'.$file.'</option>'."\n";
                			}
            			}
        			}
        			else {
                        $options['nolang'][] = '<option value="'.$file.'"'.$selected.'>'.$file.'</option>'."\n";
        			}
        		}
    		}
    	}
    	closedir($handle);
    	
    	$result = '';
        $notfound = '<option value="" disabled="disabled" style="color: #A0A0A0 !important; font-style: italic">'._WBLANGADMIN_WIN_LANGUAGE_EMPTY_.'</option>'."\n";
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
            $result.= '<optgroup label="'._WBLANGADMIN_WIN_LANGUAGE_NOTINLANG_.'">'."\n";
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
    
    function imgdirlisting($select = '', $folder = '', $indent = '')
    {
    	$directory = $this->config['server_path'].'/content/media/image/'.$folder;
    	static $options = array();
    	
    	$handle = opendir($directory);
    	while(false !== ($file = readdir($handle))) {
            $selected = '';
    		if(is_dir($directory.'/'.$file.'/')) {
        		if($file != '.' && $file != '..' && $file != '.box') {
            		if($file != 'watermarks' && $file != 'tpl_icons') {
                        $sub = ($folder != '') ? $folder.'/' : '';
                        if($select != '' &&  $select == $sub.$file) {
                            $selected = ' selected="selected"';
                        }
            			$options[] = '<option value="'.$sub.$file.'"'.$selected.'>'.$indent.$file.'</option>';
            
            			$this->imgdirlisting($select, $sub.$file, $indent.'&nbsp;&nbsp;&nbsp;');
            		}
                }
            }
    	} 
        closedir($handle);
    
    	return $options;
    }
    
    
    // Spalten
    function createcolumnstemplate($colconfig, $rowclass = '', $coleditors = array(), $colclasses = array())
    {
		if(array_key_exists('single', $colconfig) && $colconfig['single'] == 'true') {
			$template = '<div class="wb_contentelement'.($rowclass != '' ? ' '.$rowclass : '').'">'."\n\t".'<!-- begin_content -->'."\n\t\t".'<p>&nbsp;</p>'."\n\t".'<!-- end_content -->'."\n".'</div>'."\n";
		}
		else {
			$template = '<div class="wb_columnselement'.($rowclass != '' ? ' '.$rowclass : '').'">'."\n\t".'<div class="wb_colgroup">'."\n\t\t".'<div class="wb_colrow">'."\n";
			for($i = 1; $i < count($colconfig)+1; $i++) {
				$orders = array();
				$align = '';
				$classes = array();
				foreach($colconfig['col'.$i] as $key => $value) {
					if($key == 'order') {
						foreach($value as $k => $v) {
							if($v != '')
								$orders[] = 'wb_colorder_'.$k.$v;
						}
					}
					else {
						if($key == 'align') {
							$align = 'wb_colalign_'.$value;
						}
						else {
							if($value != '')
								$classes[] = 'wb_col'.$key.'_'.$value;
						}
					}
				}
				
				$colclass = '';
				if(count($colclasses) > 0 && isset($colclasses['col'.$i]) && $colclasses['col'.$i] != '')
					$colclass = ' '.$colclasses['col'.$i];
				
				$coleditor = '';
				if(count($coleditors) > 0 && isset($coleditors['editor'.$i]) && $coleditors['editor'.$i] == 'set')
					$coleditor = "\t\t\t".'<div class="'.$align.' wb_contentelement">'."\n\t\t\t\t\t".'<!-- begin_content -->'."\n\t\t\t\t\t\t".'<p>&nbsp;</p>'."\n\t\t\t\t\t".'<!-- end_content -->'."\n\t\t\t\t".'</div>'."\n\t\t\t";
				
				$bind = count($classes) > 0 && count($orders) > 0 ? ' ' : '';
				$template.= "\t\t\t".'<div class="'.implode(' ', $classes).$bind.implode(' ', $orders).$colclass.'">'."\n\t".$coleditor.'</div>'."\n";
			}
			$template.= "\t\t".'</div>'."\n\t".'</div>'."\n".'</div>'."\n";
		}
		
		return $template;
	}
	
    function setmargintoptocolumns($columns, $margin)
    {
		$pattern = '#(<div)([^>]+)(class=\")(?:([\w\d\_\- ]+))?(wb_columnselement|wb_contentelement)(?:([\w\d\_\- ]+))?(\")(?:([^>]+))?(>)(.*?)#Usi';
		
		$columns = preg_replace_callback($pattern, function($match) use ($margin)
		{
			if(preg_match('#style=\"#', $match[2]))
			{
				$match[2] = preg_replace('#style=\"([^"]+)\"#Umsi', 'style="${1}; margin-top: '.$margin.'px"', $match[2]);
			}
			elseif(isset($match[10]) && preg_match('#style=\"#', $match[8]))
			{
				$match[8] = preg_replace('#style=\"([^"]+)\"#Umsi', 'style="${1}; margin-top: '.$margin.'px"', $match[8]);
			}
			elseif(!isset($match[10]) && isset($match[9]) && preg_match('#style=\"#', $match[7]))
			{
				$match[7] = preg_replace('#style=\"([^"]+)\"#Umsi', 'style="${1}; margin-top: '.$margin.'px"', $match[7]);
			}
			elseif(!isset($match[10]) && !isset($match[9]) && isset($match[8]) && preg_match('#style=\"#', $match[6]))
			{
				$match[6] = preg_replace('#style=\"([^"]+)\"#Umsi', 'style="${1}; margin-top: '.$margin.'px"', $match[6]);
			}
			else
			{
				$match[2] = $match[2].' style="margin-top: '.$margin.'px" ';
			}
			
			$result = $match[1].$match[2].$match[3].$match[4].$match[5].$match[6].$match[7];
			if(isset($match[8])) $result.= $match[8];
			if(isset($match[9])) $result.= $match[9];
			if(isset($match[10])) $result.= $match[10];
			
			return $result;
			
		}, $columns);
		
		return $columns;
    }
    
    function savecolumnstopage($content, $columns, $pos, $index)
    {
		$content = str_replace('&nbsp;', '&amp;nbsp;', $content);
		$content = $this->escapescripts($content);
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = true;
		$dom->formatOutput = false;
		libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="UTF-8">'.$content);
		libxml_use_internal_errors(false);
		libxml_clear_errors();
		$dom->encoding = 'UTF-8';
		$xpath = new DomXPath($dom);
		$query = "contains(@class, 'wb_columnselement') or contains(@class, 'wb_contentelement')";
		if($this->config['insertpoints'] == '1')
			$query.= " or contains(@class, 'wb_menuelement') or contains(@class, 'wb_blockelement')";
		$nodes = $xpath->query("//div[".$query."]");
		
		$columns = str_replace('&nbsp;', '&amp;nbsp;', $columns);
		$columns = $this->escapescripts($columns);
		$temp = new DOMDocument();
		$temp->preserveWhiteSpace = true;
		libxml_use_internal_errors(true);
		$temp->loadHTML('<?xml encoding="UTF-8">'.$columns);
		libxml_use_internal_errors(false);
		libxml_clear_errors();
		$temp->encoding = 'UTF-8';
		$body = $temp->getElementsByTagName('body')->item(0);
		foreach($body->childNodes as $node){
		   $newnode = $dom->importNode($node, true);
		}

		if($pos == 'before')
			$nodes->item($index)->parentNode->insertBefore($newnode, $nodes->item($index));
		
		if($pos == 'after')
			$nodes->item($index)->parentNode->insertBefore($newnode, $nodes->item($index)->nextSibling);
		
		$savedbody = $xpath->query('/html/body');
		$result = $dom->saveHTML($savedbody->item(0));
		$result = $this->unescapescripts($result);
		$result = str_replace('&amp;nbsp;', '&nbsp;', $result);
		
        return $result;
    }
    
    function deletecolumnsfrompage($content, $index)
    {
		$content = str_replace('&nbsp;', '&amp;nbsp;', $content);
		$content = $this->escapescripts($content);
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = true;
		$dom->formatOutput = false;
		libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="UTF-8">'.$content);
		libxml_use_internal_errors(false);
		libxml_clear_errors();
		$dom->encoding = 'UTF-8';
		$xpath = new DomXPath($dom);
		$query = "contains(@class, 'wb_columnselement') or contains(@class, 'wb_contentelement')";
		$nodes = $xpath->query("//div[".$query."]");
		
		$nodes->item($index)->parentNode->removeChild($nodes->item($index));
		
		$savedbody = $xpath->query('/html/body');
		$result = $dom->saveHTML($savedbody->item(0));
		$result = $this->unescapescripts($result);
		$result = str_replace('&amp;nbsp;', '&nbsp;', $result);
		
        return $result;
    }
    
    
    // Vorschauen
    function adminpreviewtpl()
    {
        $tplfile = $this->checkfilenamesigns($_GET['tplfile'], 'tpl');
        $incfile = $this->config['server_path'].'/content/layouts/'.$tplfile;
    	if($tplfile != '' && file_exists($incfile))
    	{
            $this->loadcontentpage = file_get_contents($incfile);
			if(!preg_match('#content/columns/columns.css#i', $this->loadcontentpage))
				$this->autoheaderdata[] = '<link href="content/columns/columns.css?t='.filemtime($this->config['server_path'].'/content/columns/columns.css').'" rel="stylesheet" type="text/css" />';
            $this->loadcontainer('menu');
            $this->loadcontainer('block');
            
            ob_start();
            $webutlercouple = $this;
    		echo eval("?".">".$this->loadcontentpage);
    		$this->loadcontentpage = ob_get_contents();
    		ob_end_clean();
            
            $this->loadcontentpage = preg_replace('#(<title>)(.*?)(</title>)#Usi', '${1}'._WBLANGADMIN_ADMINPAGE_LAYOUTPREVIEW_.'$3', $this->loadcontentpage);
    		$this->rewriting();
            $this->autoheaderdata();
            $this->autofooterdata();
    	}
    	else
    	{
    		$this->errorpage(_WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_);
    	}
    }
    
    function adminpreviewpage()
    {
        $pagefile = $this->filenamesigns($_GET['pagefile']);
		$incfile = $this->check_temp_fileexists($this->config['server_path'].'/content/pages/'.$pagefile);
    	if($pagefile != '' && file_exists($incfile))
    	{
            $this->loadcontentpage = file_get_contents($incfile);
			if(!preg_match('#content/columns/columns.css#i', $this->loadcontentpage))
				$this->autoheaderdata[] = '<link href="content/columns/columns.css?t='.filemtime($this->config['server_path'].'/content/columns/columns.css').'" rel="stylesheet" type="text/css" />';
            $this->loadcontainer('menu');
            $this->loadcontainer('block');
            $this->showallparts();
            
            ob_start();
            $webutlercouple = $this;
            $webutlercouple->errorpagetext = _WBLANGADMIN_ADMINPAGE_PREVIEWERRORTEXT_;
    		$showpartfor = array('-1');
    		echo eval("?".">".$this->loadcontentpage);
    		$this->loadcontentpage = ob_get_contents();
    		ob_end_clean();
    		
            if(isset($this->setnewtitlefrommod) && $this->setnewtitlefrommod != '') {
                $this->loadcontentpage = preg_replace('#(<title>)(.*?)(</title>)#Usi', '${1}'.$this->setnewtitlefrommod.'$3', $this->loadcontentpage);
            }
    		$this->rewriting();
            $this->autoheaderdata();
            $this->autofooterdata();
    	}
    	else
    	{
    		$this->errorpage(_WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_);
    	}
    }
    
    function adminpreviewblock()
    {
        $blockfile = $this->filenamesigns($_GET['blockfile']);
        $incfile = $this->config['server_path'].'/content/blocks/'.$blockfile;
    	if($blockfile != '' && file_exists($incfile))
    	{
    		$site = $this->htmlsource['page_header'].
        		'<title>'._WBLANGADMIN_ADMINPAGE_BLOCKPREVIEW_.'</title>'."\n";
			$stylesheets = $this->editorstyles();
            foreach($stylesheets as $stylesheet)
            {
                $site.= '<link href="'.$stylesheet.'" rel="stylesheet" type="text/css" />'."\n";
            }
        	$site.= $this->htmlsource['close_page_header'];
            $site.= file_get_contents($incfile);
        	$site.= $this->htmlsource['page_footer'];
    		
            $this->loadcontentpage = $site;
            $this->showallparts();
            
            ob_start();
            $webutlercouple = $this;
    		$showpartfor = array('-1');
            echo eval("?".">".$this->loadcontentpage);
    		$this->loadcontentpage = ob_get_contents();
    		ob_end_clean();
            
    		$this->rewriting();
            $this->autoheaderdata();
            $this->autofooterdata();
    	}
    	else
    	{
    		$this->errorpage(_WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_);
    	}
    }
    
    function adminpreviewmenu()
    {
        $menufile = $this->filenamesigns($_GET['menufile']);
        $incfile = $this->config['server_path'].'/content/menus/'.$menufile;
    	if($menufile != '' && file_exists($incfile))
    	{
    		$site = $this->htmlsource['page_header'].
        		'<title>'._WBLANGADMIN_ADMINPAGE_MENUPREVIEW_.'</title>'."\n";
			$stylesheets = $this->editorstyles();
            foreach($stylesheets as $stylesheet)
            {
                $site.= '<link href="'.$stylesheet.'" rel="stylesheet" type="text/css" />'."\n";
            }
        	$site.= $this->htmlsource['close_page_header'];
            $site.= file_get_contents($incfile);
        	$site.= $this->htmlsource['page_footer'];
            
            $this->loadcontentpage = $site;
            $this->showallparts();
            
            ob_start();
            $webutlercouple = $this;
    		$showpartfor = array('-1');
            echo eval("?".">".$this->loadcontentpage);
    		$this->loadcontentpage = ob_get_contents();
    		ob_end_clean();
    		
    		$this->rewriting();
            $this->autoheaderdata();
            $this->autofooterdata();
    		
    	}
    	else
    	{
    		$this->errorpage(_WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_);
    	}
    }
    
    
    // Save
    function iswriteable($path, $ajax = '')
    {
        $abbruch = '';
        $absolutepath = $this->config['server_path'].$path;
        
        if(!is_dir($absolutepath.'/') && !is_writeable($absolutepath))
        {
            $abbruch = ($ajax == '') ? $this->confirmerror(sprintf(_WBLANGADMIN_POPUPWIN_WRITEABLE_FILE_, $path)) : '<span class="red">'.sprintf(_WBLANGADMIN_POPUPWIN_WRITEABLE_FILE_, $path).'</span>';
        }
        elseif(is_dir($absolutepath.'/') && !is_writeable($absolutepath.'/'))
        {
            $abbruch = ($ajax == '') ? $this->confirmerror(sprintf(_WBLANGADMIN_POPUPWIN_WRITEABLE_FOLDER_, $path)) : '<span class="red">'.sprintf(_WBLANGADMIN_POPUPWIN_WRITEABLE_FOLDER_, $path).'</span>';
        }
        
        clearstatcache();
        
        return $abbruch;
    }
    
    function confirmerror($text, $win = '')
    {
        if($win != '')
            $_SESSION['loggedin']['editwinonload'] = $win;
        
    	$error = '<script>'."\n".
            '/* <![CDATA[ */'."\n".
    		'function webutler_error() {'."\n".
    		'	check = confirm(\''.$text.'\');'."\n".
    		'	if(check == false) {'."\n".
    		'		window.location = "'.$this->config['homepage'].'/index.php?page='.$this->getpage.'";'."\n".
    		'	} else {'."\n".
    		'		window.location = "'.$this->config['homepage'].'/index.php?page='.$this->getpage.'";'."\n".
    		'	}'."\n".
    		'}'."\n".
    		'webutler_error();'."\n".
            '/* ]]> */'."\n".
    		'</script>'."\n";
    	
    	return $error;
    }
    
    function makebakfiles($filename)
    {
        $steps = $this->config['schritte_zurueck'];
        if($steps >= 1) {
            $unbak = '';
        	for($i = 0; $i < $steps; $i++) {
            	$unbak.= '.bak';
        	}
        	if(file_exists($filename.$unbak)) {
        		unlink($filename.$unbak);
        	}
            $baks = '';
        	while($steps >= 1) {
                $baks = '';
                for($i = 0; $i < $steps; $i++) {
                    $baks.= '.bak';
                }
            	if(file_exists($filename.$baks)) {
                    copy($filename.$baks, $filename.$baks.".bak");
                    @touch($filename.$baks.".bak", filemtime($filename.$baks));
                    $this->setchmodaftersave($filename.$baks.".bak");
            	}
                $steps--;
        	}
            copy($filename, $filename.".bak");
            @touch($filename.".bak", filemtime($filename));
            $this->setchmodaftersave($filename.".bak");
    	}
    }
    
    function checkbeforebak($str1, $str2)
    {
    	$arr1 = explode("\n", $str1);
    	$arr2 = explode("\n", $str2);
    	
    	$res1 = array();
    	$res2 = array();
    	
    	foreach($arr1 as $arr_1) {
    		$res = trim($arr_1);
    		if(!empty($res))
    			$res1[] = $res;
    	}
    	
    	foreach($arr2 as $arr_2) {
    		$res = trim($arr_2);
    		if(!empty($res))
    			$res2[] = $res;
    	}
    	
    	$imp1 = implode('', $res1);
    	$imp2 = implode('', $res2);
    	
    	return ($imp1 == $imp2) ? '0' : '1';
    }
    
    function savetemp2real($file)
    {
		$this->makebakfiles($file);
        rename($file.'.tmp', $file);
		
		$this->deletetempfiles($file.'.tmp');
    }
    
    function deletetempfiles($filename)
    {
    	$steps = $this->config['schritte_zurueck'];
        $bakext = '';
        $i = 0;
    	while($i <= $steps)
    	{
            $bakext.= '.bak';
        	if(file_exists($filename.$bakext)) 
        	{
                unlink($filename.$bakext);
        	}
        	else
        	{
                break;
        	}
            $i++;
    	}
    }
    
    function checkfilenamesigns($filename, $type)
    {
        $result = '';
        
        $extlen = strlen($type);
		if($type != '' && substr($filename, $extlen*(-1), $extlen) == $type) {
            $ext = '.'.$type;
    		$name = substr($filename, 0, strlen($filename)-($extlen+1));
            $name = $this->filenamesigns($name);
            $result = $name.$ext;
        }
        
        return $result;
    }
    
    function getsavelocationurl($file, $getlang = true)
    {
        $resurl = '';
        if($this->config['modrewrite'] == '1') {
        	$language = $getlang ? $this->getlangfrompage($file) : false;
			$lang = '';

			if($language && $language != '') {
				$startfile = $this->langconf['homes'][$language];
				$lang = $language.'/';
			}
			else {
				$startfile = $this->config['startseite'];
			}
			
            if($file == $startfile) {
                $resurl = $lang.'index'.$this->config['urlendung'];
			}
            else {
				$filelang = '';
				if(substr($file, 2, 1) == '_' && $language != '' && count($this->langconf) > 0) {
					$fileprefix = substr($file, 0, 2);
					$fileprefix = preg_replace("~[^a-z]~", "", $fileprefix);
					if(strlen($fileprefix) == 2 && in_array($fileprefix, $this->langconf['code'])) {
						$filelang = $fileprefix;
					}
				}
				
				$filename = ($filelang != '') ? substr($file, 3) : $file;
                $resurl = $lang.$this->getcategoryforpage($file).$filename.$this->config['urlendung'];
			}
        }
        else {
            if($file == $this->config['startseite'])
                $resurl = 'index.php';
            else
                $resurl = 'index.php?page='.$file;
        }
        
        return $this->config['homepage'].'/'.$resurl;
    }
    
    function validatemail($mail = '')
    {
		if($mail == '') {
			return 'false';
		}
		else {
			$checkedmail = strtolower(trim($mail));
			
			if(!filter_var($checkedmail, FILTER_VALIDATE_EMAIL)) {
				if(function_exists('idn_to_ascii')) {
					$string = explode('@', $checkedmail);
					$checked = $string[0].'@'.idn_to_ascii($string[1]);
					if(!filter_var($checked, FILTER_VALIDATE_EMAIL)) {
						return 'false';
					}
					else {
						return $checked;
					}
				}
				else {
					return 'false';
				}
			}
			else {
				return $checkedmail;
			}
		}
    }
    
    
    // Boxlayer
    
    // Kategorien
    function checknewcatname($name)
    {
    	$name = strtolower($name);
    	$name = preg_replace("~[^a-z0-9/_]~", "", $name);
		if(substr($name, 0, 1) == '/')
    		$name = substr($name, 1, strlen($name));
		if(substr($name, -1, 1) == '/')
    		$name = substr($name, 0, strlen($name)-1);
        
        return $name;
    }
    
    function savepagetocat($page, $newcat = '', $oldcat = '', $change = false)
    {
        if($newcat == $oldcat || ($newcat == '' && $oldcat == ''))
            return false;
        
        $values = array();
        foreach($this->categories['pages'] as $cat => $pages) {
            if($cat == $newcat) {
                $pages[] = $page;
            }
            if($oldcat != '' && $cat == $oldcat) {
                $newpages = array();
                foreach($pages as $p) {
                    if($p != $page)
                        $newpages[] = $p;
                }
                $pages = $newpages;
            }
            $newvals = (count($pages) > 0) ? "'".implode("','", $pages)."'" : '';
        	$values[] = "  '".$cat."' => array(".$newvals.")";
        }
		
		if($oldcat != '') {
			if(($key = array_search($page, $this->categories['pages'][$oldcat])) !== false) {
				unset($this->categories['pages'][$oldcat][$key]);
			}
		}
		
        if($newcat != '') {
			if(!array_key_exists($newcat, $this->categories['pages'])) {
				$values[] = "	'".$newcat."' => array('".$page."')";
				$this->categories['pages'][$newcat] = array($page);
			}
			else {
				$this->categories['pages'][$newcat][] = $page;
			}
		}
		
        $content = "\n".implode(",\n", $values)."\n";
        
        $categoriesfile =  $this->config['server_path'].'/content/access/categories.php';
        
		$file = file_get_contents($categoriesfile);
		$file = preg_replace('#(\$webutler_categories\[\'pages\'\] = array\()([^;]*)(\);)#Usi', '${1}'.$content.'${3}', $file);
        
        file_put_contents($categoriesfile, $file);
		
        $this->setchmodaftersave($categoriesfile);
        
        if($change)
            return '<span class="green">'._WBLANGADMIN_POPUPWIN_CATEGORIES_ISSAVED_.'</span>';
    }
    
    // loeschen und umbenennen
    function renamefileinmenus($oldname, $newname = '')
    {
    	$directory = $this->config['server_path'].'/content/menus';
    	$handle = opendir($directory);
    	while(false !== ($file = readdir ($handle))) {
        	if(!is_dir($directory.'/'.$file.'/')) {
        		if($file != '.' && $file != '..' && $file != '.htaccess') {
        			$extension = substr($file, strrpos($file, '.'));
        			$extension = strtolower($extension);
            		if($extension != '.bak') {
        				$menufile = $directory.'/'.$file;
        				$content = file_get_contents($menufile);
                        
                        if(!preg_match('#index\.php\?page='.$oldname.'[\"|&|\#]#Usi', $content))
                        {
                            unset($content);
                        }
                        else
                        {
                            $this->makebakfiles($menufile);
                            
                            if($newname != '') {
                                $content = preg_replace('#(href=\"index\.php\?page=)('.$oldname.')([\"|&|\#])#Usi', '${1}'.$newname.'${3}', $content);
                            }
                            else {
                                $content = $this->deletelistitem($content, $oldname);
                            }
                            
                            file_put_contents($menufile, $content);
                        	
                            $this->setchmodaftersave($menufile);
                        }
                    }
                }
    		}
    	}
    	closedir($handle);
    }
    
    function changenameinconfigfile($oldname, $newname = '')
    {
        if($oldname == $this->config['startseite'] || $oldname == $this->config['ownerrorpage'])
        {
            $baseconfig = $this->config['server_path'].'/settings/baseconfig.php';
    		$buf = file_get_contents($baseconfig);
            if($newname != '' && $oldname == $this->config['startseite']) {
        		$buf = preg_replace('#(\$webutler_config\[\'startseite\'\] = ")([^"]*)(";)#Usi', '${1}'.$newname.'$3', $buf);
				$this->config['startseite'] = $newname;
			}
            if($oldname == $this->config['ownerrorpage']) {
        		$buf = preg_replace('#(\$webutler_config\[\'ownerrorpage\'\] = ")([^"]*)(";)#Usi', '${1}'.$newname.'$3', $buf);
				$this->config['ownerrorpage'] = $newname;
			}
            
            file_put_contents($baseconfig, $buf);
            $this->setchmodaftersave($baseconfig);
        }
    }
    
    function changenameinofflinefile($oldname, $newname = '')
    {
        $offlinefile = $this->config['server_path'].'/content/access/offline.php';
        if(file_exists($offlinefile) && in_array($oldname, $this->offlinepages))
        {
            $offlines = file_get_contents($offlinefile);
            $newpages = array();
            foreach($this->offlinepages as $page) {
                if($page != $oldname)
                    $newpages[] = $page;
                if($newname != '' && $page == $oldname)
                    $newpages[] = $newname;
            }
            $newvals = (count($newpages) > 0) ? "'".implode("','", $newpages)."'" : '';
            
    		$offlines = preg_replace('#(\$webutler_offlinepages = array\()([^;]*)(\);)#Usi', '${1}'.$newvals.'${3}', $offlines);
			$this->offlinepages = $newpages;
            
            file_put_contents($offlinefile, $offlines);
            $this->setchmodaftersave($offlinefile);
        }
        
        if(file_exists($this->config['server_path'].'/content/access/users.db') && class_exists('SQLite3')) {
            $userdb = new SQLite3($this->config['server_path'].'/content/access/users.db');
            
            $blocks = $userdb->query("SELECT id, pages FROM blocks WHERE blocks.id = '1' LIMIT 1");
        	if($block = $blocks->fetchArray()) {
                $blockedpages = explode(',', $block['pages']);
                if(in_array($oldname, $blockedpages)) {
                    $newpages = array();
                    foreach($blockedpages as $page) {
                        if($page != $oldname)
                            $newpages[] = $page;
                        if($newname != '' && $page == $oldname)
                            $newpages[] = $newname;
                    }
                    $userdb->query("UPDATE blocks SET pages = '".$userdb->escapeString(implode(',', $newpages))."' WHERE blocks.id = '1'");
                    
                    $groups = $userdb->query("SELECT id, pages FROM groups");
                    while($group = $groups->fetchArray()) {
                        $groupid = $group['id'];
                        $grouppages = explode(',', $group['pages']);
                        if(in_array($oldname, $grouppages)) {
                            $newpages = array();
                            foreach($grouppages as $page) {
                                if($page != $oldname)
                                    $newpages[] = $page;
                                if($newname != '' && $page == $oldname)
                                    $newpages[] = $newname;
                            }
                            $userdb->query("UPDATE groups SET pages = '".$userdb->escapeString(implode(',', $newpages))."' WHERE groups.id = '".$userdb->escapeString($groupid)."'");
                        }
                    }
                }
            }
            $userdb->close();
        }
    }
    
    function changenameincategoriesfile($oldname, $newname = '')
    {
        if($this->config['categories'] == '1' && array_key_exists('pages', $this->categories) && count($this->categories['pages']) > 0)
        {
            $content = '';
            $values = array();
            $newcats = array();
            foreach($this->categories['pages'] as $cat => $pages) {
                if(in_array($oldname, $pages)) {
                    $newpages = array();
                    foreach($pages as $page) {
                        if($page != $oldname)
                            $newpages[] = $page;
                        if($newname != '' && $page == $oldname)
                            $newpages[] = $newname;
                    }
                    $pages = $newpages;
                }
            	$newcats[$cat] = $pages;
                $newvals = (count($pages) > 0) ? "'".implode("','", $pages)."'" : '';
            	$values[] = "  '".$cat."' => array(".$newvals.")";
            }
            $content.= "\n".implode(",\n", $values)."\n";
            
            $categoriesfile =  $this->config['server_path'].'/content/access/categories.php';
            
    		$file = file_get_contents($categoriesfile);
    		$file = preg_replace('#(\$webutler_categories\[\'pages\'\] = array\()([^;]*)(\);)#Usi', '${1}'.$content.'${3}', $file);
			$this->categories['pages'] = $newcats;
            
            file_put_contents($categoriesfile, $file);
            $this->setchmodaftersave($categoriesfile);
        }
    }
    
    function changenameinlanguagefile($oldname, $newname = '')
    {
        if($this->config['languages'] == '1' && array_key_exists('pages', $this->langconf) && count($this->langconf['code']) > 0)
        {
            $langfile = $this->config['server_path'].'/content/access/languages.php';
            $langconfig = file_get_contents($langfile);
            
            foreach($this->langconf['pages'] as $lang => $files) {
                if(in_array($oldname, $files)) {
                    $newpages = array();
                    foreach($files as $file) {
                        if($file != $oldname)
                            $newpages[] = $file;
                        if($newname != '' && $file == $oldname)
                            $newpages[] = $newname;
                    }
                    
                    $pages = "'".implode("','", $newpages)."'";
                    $langconfig = preg_replace('#(\$webutler_langconf\[\'pages\'\]\[\''.$lang.'\'\] = array\()([^\)]*)(\);\n)#Usi', '${1}'.$pages.'$3', $langconfig);
					$this->langconf['pages'][$lang] = $newpages;
                }
            }
            
            file_put_contents($langfile, $langconfig);
            $this->setchmodaftersave($langfile);
        }
    }
    
    // Link Highlite
    function savehighlitelinks()
    {
        $i = 0;
        $hlfiles = array();
        foreach($this->linkhighlite['files'] as $key => $hlfile) {
			$hlfile[2] = isset($hlfile[2]) ? $hlfile[2] : 'no';
    		$hlfiles[] = "   ".$i." => array('".$hlfile[0]."','".$hlfile[1]."','".$hlfile[2]."')";
            $i++;
        }
        
        $k = 0;
        $hlfolders = array();
        foreach($this->linkhighlite['folders'] as $key => $hlfolder) {
			$hlfolder[3] = isset($hlfolder[3]) ? $hlfolder[3] : 'no';
    		$hlfolders[] = "   ".$k." => array('".$hlfolder[0]."','".$hlfolder[1]."','".$hlfolder[2]."','".$hlfolder[3]."')";
            $k++;
        }
        
		$config = "<?PHP\n\n";
		$config.= "\$webutler_linkhighlite['files'] = array(\n";
        $config.= implode(",\n", $hlfiles)."\n";
		$config.= ");\n\n";
		$config.= "\$webutler_linkhighlite['folders'] = array(\n";
        $config.= implode(",\n", $hlfolders)."\n";
		$config.= ");\n\n";
        
        $file = $this->config['server_path'].'/content/access/linkhighlite.php';
        
        file_put_contents($file, $config);
		
        $this->setchmodaftersave($file);
    }
    
    // Languages
    function setpagetolang($page, $lang)
    {
        if($this->config['languages'] == '1' && array_key_exists('pages', $this->langconf))
        {
            $this->langconf['pages'][$lang][] = $page;
            $newvals = $this->langconf['pages'][$lang];
            $pages = (count($newvals) > 0) ? "'".implode("','", $newvals)."'" : '';
            $langfile = $this->config['server_path'].'/content/access/languages.php';
            $langconfig = file_get_contents($langfile);
            $langconfig = preg_replace('#(\$webutler_langconf\[\'pages\'\]\[\''.$lang.'\'\] = array\()([^\)]*)(\);\n)#Usi', '${1}'.$pages.'$3', $langconfig);
            
            file_put_contents($langfile, $langconfig);
    		
            $this->setchmodaftersave($langfile);
        }
    }
    
    function delpagefromlang($page)
    {
        if($this->config['languages'] == '1' && array_key_exists('pages', $this->langconf))
        {
            $langfile = $this->config['server_path'].'/content/access/languages.php';
            $langconfig = file_get_contents($langfile);
            $save = false;
            
            foreach($this->langconf['code'] as $code) {
                if(in_array($page, $this->langconf['pages'][$code])) {
                    $num = array_search($page, $this->langconf['pages'][$code]);
                    unset($this->langconf['pages'][$code][$num]);
                    $newvals = $this->langconf['pages'][$code];
                    $pages = (count($newvals) > 0) ? "'".implode("','", $newvals)."'" : '';
                    $langconfig = preg_replace('#(\$webutler_langconf\[\'pages\'\]\[\''.$code.'\'\] = array\()([^\)]*)(\);\n)#Usi', '${1}'.$pages.'$3', $langconfig);
                    
                    $save = true;
                    file_put_contents($langfile, $langconfig);
                    $this->setchmodaftersave($langfile);
                    break;
                }
            }
            
            if(!$save) {
                unset($langconfig);
            }
        }
    }
    
    // Offlinepages
    function setpageto_offlinearray($page)
    {
        if(!in_array($page, $this->offlinepages))
        {
            $this->offlinepages[] = $page;
            $this->savepagesto_offlinearray($this->offlinepages);
        }
    }
    
    function delpagefrom_offlinearray($page)
    {
        if(in_array($page, $this->offlinepages))
        {
        	foreach ($this->offlinepages as $offlinepage)
        	{
                if($offlinepage != $page) {
                    $newofflinepages[] = $offlinepage;
                }
        	}
            $this->savepagesto_offlinearray($newofflinepages);
        }
    }
    
    function savepagesto_offlinearray($array)
    {
        $iswriteable = $this->iswriteable('/content/access/offline.php');
        if($iswriteable != '')
        {
            echo $iswriteable;
            exit;
        }
        
        $offlinefile = $this->config['server_path']."/content/access/offline.php";
        $arraywerte = array();
        
        for($i = 0; $i < count($array); $i++)
        {
            $arraywerte[] = '\''.$array[$i].'\'';
        }
        
    	$config = "<?PHP\n\n";
    	$config.= "\$webutler_offlinepages = array(".(count($arraywerte) > 0 ? implode(',', $arraywerte) : '').");\n\n";
        
        file_put_contents($offlinefile, $config);
    }
    
    // Formulare
    function getformfieldname($input)
    {
        if($input == 'empfaenger')
            $output = _WBLANGADMIN_WIN_FORMS_INPUT_RECEIVER_;
        elseif($input == 'empfaengermail')
            $output = _WBLANGADMIN_WIN_FORMS_INPUT_MAILADDRESS_;
        elseif($input == 'empfaengername')
            $output = _WBLANGADMIN_WIN_FORMS_INPUT_SHIPPER_;
        elseif($input == 'empfaengerbetreff')
            $output = _WBLANGADMIN_WIN_FORMS_INPUT_SUBJECT_;
        elseif($input == 'bestaetigung')
            $output = _WBLANGADMIN_WIN_FORMS_INPUT_CONFIRM_;
        elseif($input == 'bestaetigungsbetreff')
            $output = _WBLANGADMIN_WIN_FORMS_INPUT_CONFIRMSUB_;
        elseif($input == 'sentalert')
            $output = _WBLANGADMIN_WIN_FORMS_INPUT_SENTALERT_;
        
        return $output;
    }
    
    function saveaddresses()
    {
        $langs = ($this->config['languages'] == '1' && array_key_exists('code', $this->langconf) && count($this->langconf['code']) > 0) ? $this->langconf['code'] : '';
        $addressesfile = $this->config['server_path'].'/content/access/mailaddresses.php';
        ksort($this->mailaddresses, SORT_REGULAR);
        
    	$addresses = '';
    	$config = "<?PHP\n\n";
        foreach($this->mailaddresses as $element => $sendto)
        {
            foreach($sendto as $name => $value)
            {
            	if($name == 'bestaetigungsbetreff' && is_array($langs)) {
                    foreach($langs as $lang) {
                        $_value = (!is_array($value)) ? $value : $value[$lang];
                        $addresses.= "\$webutler_mailaddresses['".$element."']['".$name."']['".$lang."'] = \"".$_value."\";\n";
                    }
            	}
            	else {
                    if($name == 'bestaetigungsbetreff' && is_array($value)) $value = $value[$this->config['defaultlang']];
                    $addresses.= "\$webutler_mailaddresses['".$element."']['".$name."'] = \"".$value."\";\n";
                }
        	}
            $addresses.= "\n";
    	}
    	$config.= ($addresses != '') ? $addresses : "\$webutler_mailadresses = array();\n\n";
        
        file_put_contents($addressesfile, $config);
    }
    
    
    // Module
    function getmodulesloginlang($modname, $adminlang)
    {
    	$langvars = array();
        if(count($this->langconf) > 0 && array_key_exists('code', $this->langconf)) {
            foreach($this->langconf['code'] as $lang) {
    	        $langvar = '<div class="editorlangs" title="'.$this->langconf['lang'][$lang].'">'."\n";
    			$langvar.= '<input type="radio" name="'.$modname.'lang" id="'.$lang.'" value="'.$lang.'"';
    			if($lang == $adminlang)
    				$langvar.= ' checked="checked"';
    			$langvar.= ' onclick="submit()" />'."\n";
    	        $langvar.= '<label for="'.$lang.'">';
    	        if(file_exists($this->config['server_path'].'/includes/language/icons/'.$lang.'.png'))
    	            $langvar.= '<img src="'.$this->config['homepage'].'/includes/language/icons/'.$lang.'.png" />';
    	        else
    	            $langvar.= '<div class="editorlangflag">'.$lang.'</div>';
    	        $langvar.= '</label>'."\n";
    	        $langvar.= '</div>'."\n";
    	        $langvars[$lang] = $langvar;
            }
        }
        
        return $langvars;
    }
    
    function getmodulesheadermenu($modname, $adminlang)
    {
        $result = '';
        $modlines = '';
	    if(count($this->moduleslist) >= 2)
		{
		    $menu = array();
			foreach($this->moduleslist as $module) {
				if((isset($module[2]) && $module[2] != '-') || $this->checkadmin()) {
                    if(!preg_match('#\/'.(strtolower($modname)).'\/?#Usi', $module[1])) {
						$modpath = $module[1];
						if(substr($modpath, 0, 1) != '/') $modpath = '/'.$modpath;
    		    		$menu[] = '<li>'."\n".
    		    		'	<a href="'.$this->config['homepage'].'/modules'.$modpath.'">'.$module[0].'</a> &raquo;'."\n".
    		    		'</li>';
                    }
	    		}
			}
			if(count($menu) >= 1) {
				$modlines.= '<ul>'."\n";
				$modlines.= implode("\n", $menu);
				$modlines.= '</ul>'."\n";
			}
		}
    	
    	$langvars = $this->getmodulesloginlang($modname, $adminlang);
    	
    	if(count($langvars) >= 2 || $modlines != '') {
    		$result.= '<table width="100%" border="0" cellspacing="0" cellpadding="0">'."\n";
    		$result.= '<tr>'."\n";
    		if(count($langvars) > 1) {
    			$result.= '<td id="changelang">'."\n";
    			$result.= '<form action="admin.php'.($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '').'" method="post">'."\n";
    			foreach($langvars as $langvar) {
    				$result.= $langvar;
    			}
    			$result.= '</form>'."\n";
    			$result.= '</td>'."\n";
    		}
    		if($modlines != '') {
    			$result.= '<td id="moduleslinks">'."\n";
    			$result.= $modlines;
    			$result.= '</td>'."\n";
    		}
    		$result.= '</tr>'."\n";
    		$result.= '</table>'."\n";
    	}
    	
    	return $result;
    }
}





