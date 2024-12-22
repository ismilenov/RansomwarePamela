<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(__FILE__))).'/includes/loader.php';

$webutlermakemod = new WebutlerAdminClass;
$webutlermakemod->config = $webutler_config;


if(!$webutlermakemod->checkadmin())
    exit('no access');

if(!class_exists('SQLite3'))
    exit('no SQLite3');

if(!isset($_SESSION['loggedin']['userlang']) || $_SESSION['loggedin']['userlang'] == '') $_SESSION['loggedin']['userlang'] = $webutlermakemod->config['defaultlang'];
include $webutlermakemod->config['server_path'].'/admin/system/lang/'.$_SESSION['loggedin']['userlang'].'.php';


$dbpath = $webutlermakemod->config['server_path'].'/content/access';
$chmod = $webutlermakemod->config['chmod'];
$makemodpath = $webutlermakemod->config['server_path'].'/admin/makemod';

include $makemodpath.'/adds/lang/'.$_SESSION['loggedin']['userlang'].'.php';

if(!file_exists($dbpath.'/makemod.db')) {
    if(is_writeable($dbpath)) {
        if($makemoddb = new SQLite3($dbpath.'/makemod.db')) {
            $makemoddb->query("BEGIN");
            $makemoddb->query("CREATE TABLE projects (id INTEGER PRIMARY KEY, name TEXT)");
            $makemoddb->query("CREATE TABLE fields (id INTEGER PRIMARY KEY, projectid INTEGER, typeid INTEGER, name TEXT, field TEXT, options TEXT, sort INTEGER)");
            $makemoddb->query("CREATE TABLE types (id INTEGER PRIMARY KEY, name TEXT, type TEXT)");
            
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_DATEFIELD_','date')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_USERNAME_','user')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_TEXTLINE_','text')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_TEXTAREA_','area')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_HTMLEDIT_','html')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_CODEEDIT_','bbcode')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_NUMBER_','number')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_FILEUPLOAD_','file')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_IMAGEUPLOAD_','image')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_MULTIIMAGE_','multi')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_SELECTBOX_','select')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_CHECKBOX_','checkbox')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_STATEFIELD_','state')");
            $makemoddb->query("INSERT INTO types (name,type) VALUES ('_MAKEMODLANG_FIELDTYPE_HIDDENFIELD_','hidden')");
            
            $makemoddb->query("CREATE TABLE admin (id INTEGER PRIMARY KEY, projectid INTEGER, modcats INTEGER, basecatids INTEGER, cats TEXT, catopts TEXT, topics INTEGER, subedit INTEGER, bylang INTEGER, multilang INTEGER, byuser INTEGER, catsort INTEGER, catmenu INTEGER, topicsort INTEGER, breaktopic INTEGER, copytopictocat INTEGER, disttopicstart INTEGER, datasort INTEGER, copydatatocat INTEGER, copydatatotopic INTEGER, breakdata INTEGER, options INTEGER, autolightbox INTEGER, seo INTEGER, seocats INTEGER, seotopics INTEGER, seodatas INTEGER)");
            $makemoddb->query("CREATE TABLE view (id INTEGER PRIMARY KEY, projectid INTEGER, newtopics INTEGER, newdata INTEGER, full INTEGER, newlink INTEGER, newest INTEGER, filter INTEGER)");
            $makemoddb->query("CREATE TABLE listtpl (id INTEGER PRIMARY KEY, projectid INTEGER, tpldata TEXT)");
            $makemoddb->query("CREATE TABLE fulltpl (id INTEGER PRIMARY KEY, projectid INTEGER, tpldata TEXT)");
            $makemoddb->query("CREATE TABLE inputtpl (id INTEGER PRIMARY KEY, projectid INTEGER, tpldata TEXT)");
            $makemoddb->query("CREATE TABLE newesttpl (id INTEGER PRIMARY KEY, projectid INTEGER, tpldata TEXT)");
            if($makemoddb->query("COMMIT")) {
                header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php");
            }
            else {
                $alert = _MAKEMODLANG_ERROR_DBTABLES_;
            }
        }
        else {
            $alert = _MAKEMODLANG_ERROR_DATABASE_;
        }
    }
    else {
        $alert = _MAKEMODLANG_ERROR_CHMOD_;
    }
}
else {
    require_once $makemodpath.'/adds/makemodclass.php';
    
    $makemodclass = new MakeModClass;
    $makemodclass->dbpath = $dbpath;
    $makemodclass->chmod = $chmod;
    $makemodclass->connectdb();
    if(key($_POST) != '') $makemodclass->post = $_POST;
    if(isset($_GET['mod']) && $_GET['mod'] != '') {
        $_GET['mod'] = $makemodclass->validnum($_GET['mod']);
        $makemodclass->modid = $_GET['mod'];
        $makemodclass->getmodname();
    }

    if(isset($_POST['loadmod'])) {
        $makemodclass->loadmod();
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid);
    }

    if(isset($_POST['createmod'])) {
        $makemodclass->createmod();
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid);
    }

    if(isset($_POST['newfield'])) {
        $makemodclass->newfield();
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid."&saved");
    }

    if(isset($_POST['delete'])) {
        $makemodclass->deletefield(key($_POST['delete']));
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid."&saved");
    }

    if(isset($_POST['fieldup'])) {
        $makemodclass->movefieldup(key($_POST['fieldup']));
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid);
    }

    if(isset($_POST['fielddown'])) {
        $makemodclass->movefielddown(key($_POST['fielddown']));
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid);
    }

    if(isset($_POST['scal'])) {
        $makemodclass->imageoptions(key($_POST['scal']), $_POST['scal']);
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid."&saved");
    }

    if(isset($_POST['adminsets'])) {
        $makemodclass->saveadminsets();
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid."&tab=admin&saved");
    }

    if(isset($_POST['layoutsets'])) {
        $makemodclass->savelayoutsets();
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid."&tab=view&saved");
    }

    if(isset($_POST['listsets'])) {
        $tpldata = '';
        if(isset($_POST['tpldata'])) {
            foreach($_POST['tpldata'] as $key => $val) {
                $tpldata[] = $key;
            }
        }
        $makemodclass->savetplsets('listtpl', $tpldata);
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid."&tab=list&saved");
    }

    if(isset($_POST['fullsets'])) {
        $tpldata = '';
        if(isset($_POST['tpldata'])) {
            foreach($_POST['tpldata'] as $key => $val) {
                $tpldata[] = $key;
            }
        }
        $makemodclass->savetplsets('fulltpl', $tpldata);
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid."&tab=full&saved");
    }

    if(isset($_POST['inputsets'])) {
        $tpldata = '';
        if(isset($_POST['tpldata'])) {
            foreach($_POST['tpldata'] as $key => $val) {
                $tpldata[] = $key;
            }
        }
        $makemodclass->savetplsets('inputtpl', $tpldata);
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid."&tab=input&saved");
    }

    if(isset($_POST['newestsets'])) {
        $tpldata = '';
        if(isset($_POST['tpldata'])) {
            foreach($_POST['tpldata'] as $key => $val) {
                $tpldata[] = $key;
            }
        }
        $makemodclass->savetplsets('newesttpl', $tpldata);
        if(count($makemodclass->alert) == 0)
            header("Location: ".$webutlermakemod->config['homepage']."/admin/makemod/index.php?mod=".$makemodclass->modid."&tab=newest&saved");
    }
    
    if(isset($_POST['makezipfile'])) {
        $makemodclass->makezipfile();
        
        require_once $makemodpath.'/adds/pclzip.lib.php';
        $zipfilename = $makemodclass->modname.'.zip';
        
        $ziparchive = new PclZip($makemodclass->dbpath.'/'.$zipfilename);
        $ziparchive->create($makemodclass->dbpath.'/'.$makemodclass->modname, PCLZIP_OPT_REMOVE_PATH, $makemodclass->dbpath.'/');
        
		header("Refresh: 1; url=".$webutlermakemod->config['homepage']."/admin/makemod/index.php");
        header("Content-type: application/zip");
        header("Content-disposition: attachment; filename=".$zipfilename.";");
        header("Content-Length: ".@filesize($makemodclass->dbpath.'/'.$zipfilename));
        
        ob_clean();
        flush();
        readfile($makemodclass->dbpath.'/'.$zipfilename);
        $makemodclass->deletebasefiles($makemodclass->modname);
        unlink($makemodclass->dbpath.'/'.$zipfilename);
    }
	
    if(isset($_GET['mod']) && $_GET['mod'] != '') {
        if($makemodclass->modname == '') {
            $alert = _MAKEMODLANG_ERROR_MODNOTEXISTS_;
        }
    }
    
    if(isset($_GET['tab']) && $_GET['tab'] == 'admin') {
        $admindata = $makemodclass->loadadminsets();
    }
    
    if(isset($_GET['tab']) && $_GET['tab'] == 'view') {
        $viewdata = $makemodclass->loadlayoutsets();
    }
    
    if(isset($_GET['tab']) && ($_GET['tab'] == 'list' || $_GET['tab'] == 'full' || $_GET['tab'] == 'input' || $_GET['tab'] == 'newest')) {
        $fulldata = $makemodclass->checkfulldata();
    }
    
    $querystr = str_replace('&saved', '', $_SERVER['QUERY_STRING']);
}

?>
<!DOCTYPE html>
<html lang="<?PHP echo isset($_SESSION['loggedin']['userlang']) ? $_SESSION['loggedin']['userlang'] : $webutlermakemod->config['defaultlang']; ?>">
<head>
<title><?PHP echo _MAKEMODLANG_FRONTPAGE_TITLE_; ?></title>
	<meta charset="UTF-8" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="robots" content="noindex,nofollow" />
	<link href="<?PHP echo $webutlermakemod->config['homepage']; ?>/admin/makemod/adds/makemod.css" rel="stylesheet" type="text/css" />
    <script>
    /* <![CDATA[ */
        function confirmdelete(text) {
        	check = confirm(text);
        	if(check != false) {
        		return true;
        	}
        }
        function showcatsets() {
            if(document.getElementById('modcats').checked == true)
                document.getElementById('catsettings').style.display = '';
            else
                document.getElementById('catsettings').style.display = 'none';
			
			checkcopying();
			checkseofields();
        }
        function showcatscal() {
            if(document.getElementById('modcats').checked == true) {
                if(document.getElementById('modcatset2').checked == true || document.getElementById('modcatset4').checked == true)
                    document.getElementById('catscal').style.display = '';
                else
                    document.getElementById('catscal').style.display = 'none';
            }
        }
        function showtopicsets() {
            if(document.getElementById('topics').checked == true)
                document.getElementById('topicsettings').style.display = '';
            else
                document.getElementById('topicsettings').style.display = 'none';
			
			checkcopying();
			checkseofields();
        }
        function showmultilang() {
            if(document.getElementById('bylang').checked == true)
                document.getElementById('datamultilang').style.display = '';
            else
                document.getElementById('datamultilang').style.display = 'none';
        }
        function checkcopying() {
			if(document.getElementById('modcats').checked == true) {
				document.getElementById('copytopictocat').disabled = false;
				if(document.getElementById('topics').checked == true) {
					document.getElementById('copydatatocat').checked = false;
					document.getElementById('copydatatocat').disabled = true;
				}
				else {
					document.getElementById('copytopictocat').checked = false;
					document.getElementById('copytopictocat').disabled = true;
					document.getElementById('copydatatocat').disabled = false;
				}
			}
			else {
				document.getElementById('copytopictocat').checked = false;
				document.getElementById('copytopictocat').disabled = true;
				document.getElementById('copydatatocat').checked = false;
				document.getElementById('copydatatocat').disabled = true;
			}
			if(document.getElementById('topics').checked == true) {
				document.getElementById('copydatatotopic').disabled = false;
			}
			else {
				document.getElementById('copydatatotopic').checked = false;
				document.getElementById('copydatatotopic').disabled = true;
			}
        }
        function showseofields() {
            if(document.getElementById('seo').checked == true)
                document.getElementById('seofieldsfor').style.display = '';
            else
                document.getElementById('seofieldsfor').style.display = 'none';
        }
        function checkseofields() {
            if(document.getElementById('modcats').checked == false) {
                document.getElementById('seocats').checked = false;
                document.getElementById('seocats').disabled = true;
			}
            else {
                document.getElementById('seocats').disabled = false;
			}
            if(document.getElementById('topics').checked == false) {
                document.getElementById('seotopics').checked = false;
                document.getElementById('seotopics').disabled = true;
			}
            else {
                document.getElementById('seotopics').disabled = false;
			}
        }
        function viewchange(id) {
            if(id == 'subcats') {
				if(document.getElementById('subcats').checked == true) {
	                document.getElementById('catsort').checked = false;
	                document.getElementById('showcatsasmenu').style.display = '';
	            }
	            else {
	                document.getElementById('showcatsasmenu').style.display = 'none';
	            }
            }
            if(id == 'catsort' && document.getElementById('catsort').checked == true) {
                document.getElementById('subcats').checked = false;
                document.getElementById('showcatsasmenu').style.display = 'none';
            }
            
            if(id == 'newtopics' && document.getElementById('newtopics').checked == true) {
                if(document.getElementById('newlink').checked == false && document.getElementById('newdata').checked == false)
                    document.getElementById('newdata').checked = true;
            }
            
            if(id == 'newlink') {
                if(document.getElementById('newtopics') && document.getElementById('newtopics').checked == true && document.getElementById('newlink').checked == false && document.getElementById('newdata').checked == false)
                    document.getElementById('newdata').checked = true;
                else if(document.getElementById('newlink').checked == true)
                    document.getElementById('newdata').checked = false;
            }
            if(id == 'newdata') {
                if(document.getElementById('newtopics') && document.getElementById('newtopics').checked == true && document.getElementById('newlink').checked === false && document.getElementById('newdata').checked === false) {
                    document.getElementById('newdata').checked = true;
                    document.getElementById('newlink').checked = false;
                }
                else if(document.getElementById('newdata').checked == true)
                    document.getElementById('newlink').checked = false;
            }
            
            if(id == 'datasort' && document.getElementById('datasort').checked == true)
                document.getElementById('sortdatafield').checked = false;
            if(id == 'sortdatafield' && document.getElementById('sortdatafield').checked == true)
                document.getElementById('datasort').checked = false;
            
            if(id == 'topicsort' && document.getElementById('topicsort').checked == true)
                document.getElementById('sorttopicfield').checked = false;
            if(id == 'sorttopicfield' && document.getElementById('sorttopicfield').checked == true)
                document.getElementById('topicsort').checked = false;
            
            if(id == 'breaktopic' && document.getElementById('breaktopic').checked == true)
                document.getElementById('breakdata').checked = false;
            if(id == 'breakdata' && document.getElementById('breakdata').checked == true)
                document.getElementById('breaktopic').checked = false;
        }
        function setdisttopicstart() {
            if(document.getElementById('disttopicstart').checked == true) {
				document.getElementById('sorttopicfieldtitle').style.display = 'none';
				document.getElementById('sorttopicfieldofstart').style.display = 'inline';
			}
			else {
				document.getElementById('sorttopicfieldofstart').style.display = 'none';
				document.getElementById('sorttopicfieldtitle').style.display = 'inline';
			}
        }
        function showloader() {
            document.getElementById('modloader').style.display = 'block';
        }
        function hideloader() {
            document.getElementById('modloader').style.display = 'none';
        }
        window.onload = function() {
            <?PHP
                if(isset($admindata)) {
                    if(isset($admindata['modcats']) && $admindata['modcats'] != '') echo 'showcatsets();'."\n";
                    if(isset($admindata['topics']) && $admindata['topics'] != '') echo 'showtopicsets();'."\n";
                    if(isset($admindata['bylang']) && $admindata['bylang'] != '') echo 'showmultilang();'."\n";
                    if(isset($admindata['seo']) && $admindata['seo'] != '') echo 'showseofields();'."\n";
                    echo 'checkseofields();'."\n";
                }
                if(isset($_GET['tab']) && $_GET['tab'] == 'admin') echo 'checkcopying();'."\n".'showcatscal();'."\n".'setdisttopicstart();'."\n";
            ?>
        }
    /* ]]> */
    </script>
</head>
<body>
<?PHP
    if(isset($_GET['tab']) && $_GET['tab'] == 'download') {
        echo '<div id="modloader"></div>'."\n";
    }
?>
<div id="administration">
  <h1><img src="<?PHP echo $webutlermakemod->config['homepage']; ?>/admin/system/images/webutler.gif" alt="Webutler" /> <?PHP echo _MAKEMODLANG_FRONTPAGE_HEADLINE_; ?></h1>
<?PHP
	if(!isset($_GET['mod']) || $_GET['mod'] == '') {
		echo '<p id="fronttext">';
		echo str_replace('###HELP_URL###', $webutlermakemod->config['homepage'].'/_docs_/'.$_SESSION['loggedin']['userlang'].'/makemod/_readme.htm', _MAKEMODLANG_FRONTPAGE_TEXT_);
		echo '</p>';
    }
    if((isset($alert) && $alert != '') || count($makemodclass->alert) >= '1') {
        if(count($makemodclass->alert) >= '1') {
            $alert = '';
            foreach($makemodclass->alert as $alerts) {
                $alert.= $alerts."<br />\n";
            }
        }
        echo '<div id="error">'.$alert.'</div>'."\n";
    }
    if(!isset($_GET['mod']) || $_GET['mod'] == '') {
        include $makemodpath.'/adds/page_load.php';
    }
    else {
        echo '<div id="changeproject"><a href="index.php">&laquo; '._MAKEMODLANG_FRONTPAGE_PROJECT_.'</a></div>'."\n";
        if($makemodclass->modname != '') {
            if(!isset($_GET['tab']) || $_GET['tab'] == '') $_GET['tab'] = 'defines';
            echo '<div id="opened">'._MAKEMODLANG_FRONTPAGE_MODFOLDER_.': <strong>'.strtoupper($makemodclass->modname).'</strong>';
            if(isset($_GET['saved'])) echo ' <span id="saved">'._MAKEMODLANG_FRONTPAGE_SAVED_.'</span>';
            echo '</div>'."\n".
                '<table width="100%" border="0" cellspacing="0" cellpadding="0">'."\n".
                '<tr>'."\n".
                '<td>'."\n".
                '<div id="reiter">'."\n".
                '<div class="';
            echo ($_GET['tab'] == 'defines') ? 'activ' : 'inactiv';
            echo '"><a href="index.php?mod='.$_GET['mod'].'&tab=defines">'._MAKEMODLANG_TABS_DEFINEFIELDS_.' &raquo;</a></div>'."\n".
                '<div class="';
            echo ($_GET['tab'] == 'admin') ? 'activ' : 'inactiv';
            echo '"><a href="index.php?mod='.$_GET['mod'].'&tab=admin">'._MAKEMODLANG_TABS_ADMINVIEW_.' &raquo;</a></div>'."\n".
                '<div class="';
            echo ($_GET['tab'] == 'view') ? 'activ' : 'inactiv';
            echo '"><a href="index.php?mod='.$_GET['mod'].'&tab=view">'._MAKEMODLANG_TABS_USERVIEW_.' &raquo;</a></div>'."\n".
                '<div class="';
            echo ($_GET['tab'] == 'list' || $_GET['tab'] == 'full' || $_GET['tab'] == 'input' || $_GET['tab'] == 'newest') ? 'activ' : 'inactiv';
            echo '"><a href="index.php?mod='.$_GET['mod'].'&tab=list">'._MAKEMODLANG_TABS_TEMPLATES_.' &raquo;</a></div>'."\n".
                '<div class="';
            echo ($_GET['tab'] == 'download') ? 'activ' : 'inactiv';
            echo '"><a href="index.php?mod='.$_GET['mod'].'&tab=download">'._MAKEMODLANG_TABS_DOWNLOAD_.'</a></div>'."\n".
                '<div id="last">&nbsp;</div>'."\n".
                '</div>'."\n".
                '</td>'."\n".
                '</tr>'."\n".
                '<tr>'."\n".
                '<td>'."\n".
                '<div id="pages">'."\n";
            if($_GET['tab'] == 'list' || $_GET['tab'] == 'full' || $_GET['tab'] == 'input' || $_GET['tab'] == 'newest') {
                echo '<table border="0" cellspacing="10" cellpadding="0">'."\n".
                    '<tr>'."\n".
                    '<td><a href="index.php?mod='.$_GET['mod'].'&tab=list">'._MAKEMODLANG_TABS_TPLLIST_.'</a></td>'."\n";
                if(isset($fulldata['full'])) {
                    echo '<td><a href="index.php?mod='.$_GET['mod'].'&tab=full">'._MAKEMODLANG_TABS_TPLFULL_.'</a></td>'."\n";
                }
                if(isset($fulldata['newdata']) || isset($fulldata['newlink'])) {
                    echo '<td><a href="index.php?mod='.$_GET['mod'].'&tab=input">'._MAKEMODLANG_TABS_TPLINPUT_.'</a></td>'."\n";
                }
                if(isset($fulldata['newest'])) {
                    echo '<td><a href="index.php?mod='.$_GET['mod'].'&tab=newest">'._MAKEMODLANG_TABS_TPLNEWEST_.'</a></td>'."\n";
                }
                echo '</tr>'."\n".
                    '</table>'."\n";
            }
            if(($_GET['tab'] != 'full' || ($_GET['tab'] == 'full' && isset($fulldata['full']))) || ($_GET['tab'] != 'input' || ($_GET['tab'] == 'input' && (isset($fulldata['newdata']) || isset($fulldata['newlink'])))) || ($_GET['tab'] == 'newest' && isset($fulldata['newest']))) {
                include $makemodpath.'/adds/page_'.$_GET['tab'].'.php';
            }
            echo '</div>'."\n".
            '</td>'."\n".
            '</tr>'."\n".
            '</table>'."\n";
        }
    }
?>
</div>
</body>
</html>

