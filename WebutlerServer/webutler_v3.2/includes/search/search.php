<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


if(preg_match('#/includes/search/search.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');


header("Cache-Control: max-age=600");

require_once $webutlercouple->config['server_path'].'/settings/searching.php';

if(count($webutler_modulesearches) > 0 && !class_exists('MMConnectClass'))
	require_once $webutlercouple->config['server_path'].'/includes/mmclass.php';

$search_docroot = $webutlercouple->config['server_path'].'/includes/search';
require_once $search_docroot.'/search_class.php';

if(isset($_SESSION['language']) && file_exists($search_docroot.'/lang/'.$_SESSION['language'].'.php'))
    require_once $search_docroot.'/lang/'.$_SESSION['language'].'.php';
else
    require_once $search_docroot.'/lang/'.$webutlercouple->config['defaultlang'].'.php';


if(file_exists($webutlercouple->config['server_path'].'/includes/search/css/search.css'))
	$webutlercouple->autoheaderdata[] = '<link href="includes/search/css/search.css?t='.filemtime($webutlercouple->config['server_path'].'/includes/search/css/search.css').'" rel="stylesheet" type="text/css" />';

$search_lang = (isset($_SESSION['language'])) ? $_SESSION['language'] : '';
$search_bylang = ($search_lang != '') ? '_'.$search_lang : '';

if(isset($_SESSION['searchengine']) && !isset($_SESSION['searchengine']['searchwords'.$search_bylang]))
{
    unset($_SESSION['searchengine']);
}

$search_query = false;
if(isset($_POST['search']))
{
    unset($_SESSION['searchengine']);
    
	$search_query = $_POST['query'];
	$search_query = strip_tags(stripslashes($search_query));
	$search_query = trim($search_query);
    $search_query = preg_replace('#[^ [:alnum:]]#usi', '', $search_query);
	
	if($search_query != '') {
		$_SESSION['searchengine']['searchwords'.$search_bylang] = $search_query;
        $search_engine = new SearchEngine;
        $search_engine->offlines = $webutlercouple->offlinepages;
        $search_engine->errorpage = $webutlercouple->config['ownerrorpage'];
        $search_engine->serverpath = $webutlercouple->config['server_path'];
        $search_engine->getpagesbylang($search_lang);
        $search_engine->resultlen = $webutlercouple->config['searchresultlen'];
        $search_engine->usergroupid = isset($_SESSION['userauth']['groupid']) ? $_SESSION['userauth']['groupid'] : array();
		$search_engine->pagesearches = $webutler_pagesearches;
		$search_engine->modulesearches = $webutler_modulesearches;
        $search_engine->startsearch($search_query, $search_lang);
		if(!is_array($search_engine->sortresult) && $search_engine->sortresult == '__NOTHING_TO_SEARCH__') {
			$_SESSION['searchenginefailed'] = '1';
		}
		else {
			$_SESSION['searchengine']['searchresult'.$search_bylang] = $search_engine->sortresult;
		}
    }
}

$wbsearch_resultlist = array();
$wbsearch_resultlist['searchbox'] = '';
$wbsearch_resultlist['pager'] = '';
if($webutlercouple->config['searchshowinput'] == 1) {
	$wbsearch_box = array();
	$wbsearch_box['getpage'] = $webutlercouple->getpage;
	$wbsearch_box['searchname'] = (isset($_SESSION['searchengine']['searchwords'.$search_bylang]) ? _SEARCHLANG_NEW_.' ' : '')._SEARCHLANG_INPUT_;
	$wbsearch_box['searchbutton'] = _SEARCHLANG_BUTTON_;
	
	ob_start();
	include $search_docroot.'/tpls/searchbox.tpl';
	$wbsearchbox = ob_get_contents();
	ob_end_clean();
	
    if(isset($_POST['search']) || isset($_SESSION['searchengine'])) {
		$wbsearch_resultlist['searchbox'] = $wbsearchbox;
	}
	else {
		echo $wbsearchbox;
	}
}

if(isset($_SESSION['searchengine']['searchwords'.$search_bylang]))
{
	$wbsearch_resultlist['headline'] = _SEARCHLANG_RESULT_.': '.$_SESSION['searchengine']['searchwords'.$search_bylang];
	
    $search_searchresult = isset($_SESSION['searchengine']['searchresult'.$search_bylang]) ? count($_SESSION['searchengine']['searchresult'.$search_bylang]) : 0;
    
    if($search_searchresult > $webutlercouple->config['searchlistitems']) {
		$wbsearch_pager = array();
		$search_page = isset($_GET['num']) ? preg_replace("/[^0-9]/", "", $_GET['num']) : '1';
		$wbsearch_pager['searchpage'] = _SEARCHLANG_PAGE_.' '.$search_page;
        //$search_start = ceil(($search_page-1)*$webutlercouple->config['searchlistitems']);
        $search_start = ($search_page-1)*$webutlercouple->config['searchlistitems'];
        $search_fromto = ($search_start+$webutlercouple->config['searchlistitems']);
		$search_prev = '';
		$search_next = '';
        if($search_page >= '2') {
			$wbsearch_pager['prevlink'] = 'index.php?page='.$webutlercouple->getpage.'&num='.($search_page-1);
			$wbsearch_pager['prevtext'] = _SEARCHLANG_PREV_;
        }
        //if($search_page < ceil($search_searchresult/$webutlercouple->config['searchlistitems'])) {
        if($search_page < $search_searchresult/$webutlercouple->config['searchlistitems']) {
			$wbsearch_pager['nextlink'] = 'index.php?page='.$webutlercouple->getpage.'&num='.($search_page+1);
			$wbsearch_pager['nexttext'] = _SEARCHLANG_NEXT_;
        }
		
		ob_start();
		include $search_docroot.'/tpls/pager.tpl';
		$wbsearch_resultlist['pager'] = ob_get_contents();
		ob_end_clean();
    }
    else {
        $search_start = '0';
        $search_fromto = $search_searchresult;
    }

    $search_result = false;
    //$search_return = $_SESSION['searchengine']['searchresult'.$search_bylang];
    $search_return = isset($_SESSION['searchengine']['searchresult'.$search_bylang]) ? $_SESSION['searchengine']['searchresult'.$search_bylang] : array();
    for($i = $search_start; $i < $search_fromto; $i++)
    {
        $search_result = true;
        //if($search_return[$i] != '') {
        if(!isset($search_return[$i])) {
			break;
		}
		else {
            $searchurl = $search_return[$i]['url'];
            if($webutlercouple->config['modrewrite'] == '1') {
                $filename = substr($search_return[$i]['url'], 0, strpos($search_return[$i]['url'], '&'));
                if($filename == '') $filename = $search_return[$i]['url'];
                $categoriepage = $webutlercouple->getcategoryforpage($filename);
				$langascategory = $webutlercouple->setlangascategory($filename);
				
				$search_uri = '';
				if($filename != $search_return[$i]['url']) {
					$search_uri = substr($search_return[$i]['url'], strpos($search_return[$i]['url'], '&'));
					$search_uri = str_replace('&amp;', '-', $search_uri);
					$search_uri = str_replace('&', '-', $search_uri);
					$search_uri = str_replace('=', '-', $search_uri);
					$search_uri = str_replace('/', '^', $search_uri);
				}
				
				$langfolder = $langascategory[0] != '' ? $langascategory[0].'/' : '';
				
                $search_returnpage = $langfolder.$categoriepage.$langascategory[1].$search_uri.$webutlercouple->config['urlendung'];
            }
            else {
                if($search_return[$i]['url'] == $webutlercouple->config['startseite'])
                    $search_returnpage = 'index.php';
                else
                    $search_returnpage = 'index.php?page='.$search_return[$i]['url'];
            }
			
			$wbsearch_resultlist['result'][$i]['link'] = $search_returnpage;
			$wbsearch_resultlist['result'][$i]['title'] = $search_return[$i]['title'];
			$wbsearch_resultlist['result'][$i]['contents'] = $search_return[$i]['contents'];
			$wbsearch_resultlist['result'][$i]['url'] = $webutlercouple->config['homepage'].'/'.$search_returnpage;
        }
    }
	
    if(!$search_result) {
        unset($_SESSION['searchengine']);
		$wbsearch_resultlist['nohits'] = _SEARCHLANG_NOHITS_;
		
		ob_start();
		include $search_docroot.'/tpls/nohits.tpl';
		$nohits = ob_get_contents();
		ob_end_clean();
		
		echo $nohits;
    }
	else {
		ob_start();
		include $search_docroot.'/tpls/resultlist.tpl';
		$resultlist = ob_get_contents();
		ob_end_clean();
		
		echo $resultlist;
	}
}
else
{
//echo '- '.$_SESSION['searchenginefailed'].' -';
	if(isset($_SESSION['searchenginefailed']) && $_SESSION['searchenginefailed'] == 1) {
		unset($_SESSION['searchenginefailed']);
		$wbsearch_nosearch = _SEARCHLANG_NOTHINGTOSEARCH_;
		
		ob_start();
		include $search_docroot.'/tpls/nosearch.tpl';
		$nosearch = ob_get_contents();
		ob_end_clean();
		
		echo $nosearch;
    }
    else {
		if(isset($_POST['search'])) {
			unset($_SESSION['searchengine']);
			$wbsearch_noquery = _SEARCHLANG_NOQUERY_;
			
			ob_start();
			include $search_docroot.'/tpls/noquery.tpl';
			$noquery = ob_get_contents();
			ob_end_clean();
			
			echo $noquery;
		}
	}
}

unset($_POST);
//sleep(1);




