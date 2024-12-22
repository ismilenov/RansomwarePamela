<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/includes/search/search_class.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

class SearchEngine {
    var $usergroupid;
    var $serverpath;
	var $resultlen;
	var $searchcontent;
	var $modulesearches = array();
	var $pagesearches = 0;
    var $rootdir = '';
    var $phrases = '';
    var $dirfiles;
    var $founds = array();
    var $offlines = '';
    var $pagesbylang = '';
    var $errorpage;
    var $userpages = '';
    var $sortresult;

    function useraccess()
    {
        $pages = array();
        
        if(file_exists($this->serverpath.'/content/access/users.db') && class_exists('SQLite3'))
        {
            $db = new SQLite3($this->serverpath.'/content/access/users.db');
            
            $files = array();
	        foreach($this->usergroupid as $usergroupid) {
	            $groups = $db->query("SELECT id, pages FROM groups WHERE groups.id = '".$db->escapeString($usergroupid)."' LIMIT 1");
	            if($group = $groups->fetchArray()) {
	                $ons = explode(',', $group['pages']);
	            	foreach($ons as $file) {
	                    if($file != '') $files[] = $file;
	                }
	            }
            }
            
            $blocks = $db->query("SELECT id, pages FROM blocks WHERE blocks.id = '1' LIMIT 1");
        	if($block = $blocks->fetchArray()) {
                $offs = explode(',', $block['pages']);
            	foreach($offs as $file) {
                    if($file != '' && !in_array($file, $files)) $pages[] = $file;
                }
            }
        }
        return $pages;
    }
    
    function getpagesbylang($lang) {
        $langfile = $this->serverpath.'/content/access/languages.php';
        if($lang != '' && file_exists($langfile)) {
            include $langfile;
            $this->pagesbylang = $webutler_langconf['pages'][$lang];
        }
    }
    
    function cleanupsource($content)
    {
    	$content = strip_tags($content);
        $content = str_replace("\n", ' ', $content);
    	$content = str_replace("\r", ' ', $content);
    	$content = str_replace("\t", ' ', $content);
    	$content = str_replace("&nbsp;", ' ', $content);
		$content = preg_replace('#( +)#', ' ', $content);
    	$content = trim($content);
		
    	return $content;
    }
    
    function markwords($text, $words)
    {
    	foreach($words as $word) {
    		$text = preg_replace("#(".$word.")#usi", "<strong>$1</strong>", $text);
    	}
    	return $text;
    }
    
    function sortresults($result)
    {
        $sort = array();
        
        foreach($result as $key => $row) {
            $sort[$key] = $row['sort'];
        }
        
        if(is_array($sort) && count($sort) > 0) {
            array_multisort($sort, SORT_DESC, $result);
        }
        
        $return = array_slice($result, 0, 50);
        
        return $return;
    }
    
    function getpagefiles($dir)
    {
        $files = array();
        $root = opendir($dir);
        while(false !== ($file = readdir($root))) {
            $extension = substr($file, strrpos($file, '.'));
            $extension = strtolower($extension);
            if($file != "." && $file != ".." && $file != ".htaccess" && $file != $this->errorpage && $extension != ".tmp" && $extension != ".bak" && !in_array($file, $this->offlines) && !in_array($file, $this->useraccess()) && ($this->pagesbylang == '' || in_array($file, $this->pagesbylang))) {
                $files[] = $dir."/".$file;
            }
        }
		closedir($root);
        return $files;
    }
    
    function startsearch($query, $sesslang = '')
    {
		if($this->pagesearches == 0 && count($this->modulesearches) == 0) {
			$this->sortresult = '__NOTHING_TO_SEARCH__';
		}
		else {
			if(count($this->modulesearches) > 0)
			{
				foreach($this->modulesearches as $modsearch)
				{
					if(isset($modsearch[1])) {
						if($sesslang != '' && preg_match('#,#', $modsearch[1])) {
							$lang_pages = explode(',', $modsearch[1]);
							foreach($lang_pages as $lang_page) {
								if(in_array($lang_page, $this->pagesbylang)) {
									$page = $lang_page;
									break;
								}
							}
						}
						else {
							$page = $modsearch[1];
						}
						
						if($page != $this->errorpage && !in_array($page, $this->offlines) && !in_array($page, $this->useraccess()))
						{
							$moddocroot = $modsearch[0];
							if(substr($moddocroot, 0, 1) != '/') $moddocroot = '/'.$moddocroot;
							if(substr($moddocroot, -1) != '/') $moddocroot = $moddocroot.'/';
							$searchmodfile = $this->serverpath.'/modules'.$moddocroot.'search.php';
							if(file_exists($searchmodfile)) {
								require_once $searchmodfile;
								$modulename = basename($moddocroot);
								$funcname = 'searchinmodcontent_'.$modulename;
								if(function_exists($funcname)) {
									$result = $funcname($query, $page);
									$this->searchinmodules($result, $query);
								}
								$classname = 'MMSearchMod'.$modulename;
								if(class_exists($classname)) {
									require_once $this->serverpath.'/modules/'.$modulename.'/data/config.php';
									
									$modsearchclass = new $classname;
									$modsearchclass->searchquery = $query;
									$modsearchclass->modulepage = $page;
									$modsearchclass->modname = $modulename;
									$modsearchclass->serverpath = $this->serverpath;
									$configname = $modulename.'_conf';
									$modsearchclass->fileconfig = $$configname;
									if(isset($_SESSION['language']))
										$modsearchclass->pagelang = $_SESSION['language'];
									if(isset($modsearch[2]))
										$modsearchclass->basecat = $modsearch[2];
									$modsearchclass->connectdb();
									$modsearchclass->searchforquery();
									
									$this->searchinmodules($modsearchclass->searchresult, $query);
								}
							}
						}
					}
				}
			}
			
			if($this->pagesearches == 1) {
				$this->searchinpages($this->serverpath.'/content/pages', $query);
			}
			
			$this->sortresult = $this->sortresults($this->founds);
		}
    }
    
    function searchinmodules($moduleresultes, $searches)
    {
        if(!empty($moduleresultes)) {
            $this->phrases = explode(' ', $searches);
            foreach($moduleresultes as $moduleresult) {
                $this->founds[] = $this->getmodulestext($moduleresult, $this->phrases);
            }
        }
    }
    
    function getmodulestext($moduleresult, $search_query)
    {
        $titlefound = $this->prepairtitle($moduleresult['title'], $search_query);
        $contentfound = $this->prepairtext($moduleresult['contents'], $search_query);
    	
		if($titlefound != '' || $contentfound != '') {
			$hits = 0;
			if($titlefound != '')
				$hits = $hits+$titlefound['hit']*2;
			if($contentfound != '')
				$hits = $hits+$contentfound['hit'];
			$result['sort'] = $hits;
			$result['title'] = $titlefound['content'];
			$result['contents'] = str_replace('&amp;', '&', $contentfound['content']);
			$result['url'] = $moduleresult['url'];
			
			return $result;
        }
        return false;
    }
    
    function searchinpages($directory, $searches) {
        $this->rootdir = $directory;
        $this->phrases = explode(' ', $searches);
        $this->dirfiles = $this->getpagefiles($this->rootdir);
        
        foreach($this->dirfiles as $dirfile)
        {
			if(!is_dir($dirfile.'/')) {
				$content = file_get_contents($dirfile);
				$title = preg_replace('#(.*)<title>(.*)</title>(.*)#si', '$2', $content);
				$title = html_entity_decode($title);
				$body = preg_replace('#(.*)<body([^>]*?)>(.*)</body>(.*)#si', '$3', $content);
				$body = preg_replace('#(<\?)(.*)(include|include_once|require|require_once)(.*)(\?>)#Usi', '', $body);
				$body = preg_replace('#(<script)(.*)(<\/script>)#Usi', '', $body);
				unset($content);
				
				ob_start();
				$showpartfor = $this->usergroupid;
				echo eval('?>'.$body);
				$contents = ob_get_contents();
				ob_end_clean();
				
				$contents = html_entity_decode($contents);
				$contents = $this->cleanupsource($contents);
				
				foreach($this->phrases as $phrase) {
					if(mb_stripos($contents, $phrase, 0, 'UTF-8') !== false) {
						$found = $this->getpagestext($title, $contents, basename($dirfile), $this->phrases);
						if($found != '') {
							$this->founds[] = $found;
							break;
						}
					}
				}
			}
        }
    }
    
    function getpagestext($title, $content, $url, $search)
    {
        $titlefound = $this->prepairtitle($title, $search);
        $contentfound = $this->prepairtext($content, $search);
        
		if($titlefound != '' || $contentfound != '') {
			$hits = 0;
			if($titlefound != '')
				$hits = $hits+$titlefound['hit']*2;
			if($contentfound != '')
				$hits = $hits+$contentfound['hit'];
			$result['sort'] = $hits;
			$result['title'] = $titlefound['content'];
			$result['contents'] = str_replace('&amp;', '&', $contentfound['content']);
			$result['url'] = $url;
			
			return $result;
        }
        return false;
    }
    
    function prepairtitle($content, $words)
    {
    	$hit = 0;
		
        foreach($words as $word) {
			if(mb_stristr($content, $word, 0, 'UTF-8') !== false) {
				$hit = ($hit + mb_substr_count($content, $word));
			}
		}
		
		$titleout['content'] = $this->markwords($content, $words);
		$titleout['hit'] = $hit;
		
		return $titleout;
    }
    
    function prepairtext($content, $words)
    {
    	$hit = 0;
		$textparts = array();
		$beforpart = '';
		$afterpart = '';
        $textresult = '';
		$howmuch = 0;
		$howlong = 0;
		$size = 30;
		
        foreach($words as $word) {
			if(mb_stristr($content, $word, 0, 'UTF-8') !== false) {
				$howmuch++;
				$howlong = ($howlong + mb_strlen($word));
				$hit = ($hit + mb_substr_count($content, $word));
				$content = preg_replace('#('.$word.')#usi', '<$1>', $content);
			}
		}
		
		$founds = ($howmuch > 0) ? preg_match_all('#([^>]{0,30})(<[^>]*>)([^<]{0,30})#usi', $content, $textresults) : 0;
		
		$textresult = ($founds > 0) ? implode('...', $textresults[0]) : $content;
		
		if(mb_strlen($textresult) > $this->resultlen)
			$textresult = mb_substr($textresult, 0, $this->resultlen).'...';
		
		$textresult = str_replace(array('<', '>'), array('', ''), $textresult);
		
		$textout['content'] = ($founds > 0) ? $this->markwords($textresult, $words) : $textresult;
		$textout['hit'] = $hit;
		
		return $textout;
    }
    
    function _prepairtext($content, $words)
    {
    	$count = 0;
    	$hit = 0;
		$textparts = array();
		$beforpart = '';
		$afterpart = '';
        $textresult = '';
		$howmuch = 0;
		$howlong = 0;
		
        foreach($words as $word) {
			if(mb_stristr($content, $word, 0, 'UTF-8')) {
				$howmuch++;
				$howlong = ($howlong + mb_strlen($word));
				$hit = ($hit + mb_substr_count($content, $word));
			}
		}
		
		if($howmuch > 0)
		{
			$resultlen = ($this->resultlen - $howlong);
			$contentlength = mb_strlen($content);
			$befor_after = round(($resultlen / $howmuch) / 2);
			
			for($i = 0; $i < count($words); $i++)
			{
				if(mb_stristr($content, $words[$i], 0, 'UTF-8')) {
					$textlength = mb_strlen($content);
					if($textlength > $resultlen) {
						$tstart = mb_stripos($content, $words[$i], 0, 'UTF-8') - $befor_after;
						$rest = 0;
						if($tstart < 0) {
							$rest = ($tstart*(-1) - 2);
							$tstart = 0;
						}
						
						$before_found = ($tstart > 0) ? mb_substr($content, 0, $tstart).'...' : '';
						$tlength = (mb_strlen($words[$i]) + $befor_after + $rest);
						$after_found = mb_substr($content, ($tstart + $tlength));
						$textparts[] = mb_substr($content, $tstart, $tlength);
						$content = $before_found.$after_found;
						
						if($i == 0 && $tstart > 0)
							$beforpart = '...';
						/*
						if($i == count($words))
							$afterpart = '...';
						*/
					}
					else {
						$textparts[] = $content;
					}
				}
			}
			
			if($contentlength > $resultlen)
				$afterpart = '...';
			
			$textresult = $beforpart.implode('...', $textparts).$afterpart;
			if(mb_strlen($textresult) > $this->resultlen) {
				$textresult = mb_substr($textresult, 0, $this->resultlen).'...';
			}
			
			$textout['content'] = $this->markwords($textresult, $words);
			$textout['hit'] = $hit;
			
			return $textout;
		}
		return '';
    }
}




