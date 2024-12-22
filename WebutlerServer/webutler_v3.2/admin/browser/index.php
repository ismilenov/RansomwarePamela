<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(__FILE__))).'/includes/loader.php';

require_once $webutler_config['server_path'].'/admin/browser/assets/functions.php';

$webutlermedia = new WebutlerMBrowserClass;
$webutlermedia->config = $webutler_config;
$webutlermedia->MediaDirectory = $webutlermedia->config['server_path'].'/content/media/';

if(!$webutlermedia->checkadmin())
    exit('no access');

require_once $webutlermedia->config['server_path'].'/admin/browser/lang/'.$_SESSION['loggedin']['userlang'].'.php';

if(isset($_SESSION['browseruploadfile'])) {
    unset($_SESSION['browseruploadfile']);
}

$webutlermedia->type = '';
$webutlermedia->urltype = '';

if(!isset($_GET['type']) || $_GET['type'] == '') {
	if(isset($_GET['types']) && in_array($_GET['types'], $webutlermedia->AllowedTypes))
		$webutlermedia->type = $_GET['types'];
	else
		$webutlermedia->type = 'file';
	
	$webutlermedia->urltype = 'types='.$webutlermedia->type;
}
else {
	if(in_array($_GET['type'], $webutlermedia->AllowedTypes) && $_GET['type'] != 'file') {
		$webutlermedia->type = $_GET['type'];
		$avonly = $webutlermedia->type == 'track' && isset($_GET['av']) && $_GET['av'] == 'only' ? '&av=only' : '';
		$webutlermedia->urltype = 'type='.$webutlermedia->type.$avonly;
	}
}

if($webutlermedia->type == '') {
	echo _WBLANGADMIN_BROWSER_WRONGTYPE_;
	exit;
}

if(!file_exists($webutlermedia->MediaDirectory.$webutlermedia->type.$webutlermedia->actualfolder())) {
	echo _WBLANGADMIN_BROWSER_WRONGPATH_;
	exit;
}

$webutlermedia->getthumb = (isset($_GET['vorschau']) && $webutlermedia->type == 'image') ? '&vorschau=1' : '';

if(isset($_POST['renamepopup_submit']) && $_POST['renamepopup_oldname'] != '' && $_POST['renamepopup_newname'] != '') {
    $renameoldname = preg_replace('/[^a-z0-9_\.]/', '', strtolower($_POST['renamepopup_oldname']));
    $renamenewname = preg_replace('/[^a-z0-9_]/', '', strtolower($_POST['renamepopup_newname']));
    $webutlermedia->renamefileto($renameoldname, $renamenewname);
    unset($_POST);
}

if(isset($_POST['cutoutfile']) && $_POST['cutoutfile'] != '') {
    $filetype = $webutlermedia->type;
    $filevalue = preg_replace('/[^a-z0-9_\|\/\.]/', '', strtolower($_POST['cutoutfile']));
    $_SESSION['cut'.$filetype] = $filevalue;
    unset($_SESSION['copy'.$filetype]);
    unset($_POST['cutoutfile']);
}

if(isset($_POST['copyfile']) && $_POST['copyfile'] != '') {
    $filetype = $webutlermedia->type;
    $filevalue = preg_replace('/[^a-z0-9_\|\/\.]/', '', strtolower($_POST['copyfile']));
    $_SESSION['copy'.$filetype] = $filevalue;
    unset($_SESSION['cut'.$filetype]);
    unset($_POST['copyfile']);
}

if(isset($_POST['insert']) && $_POST['insert'] == $webutlermedia->type) {
    if(isset($_SESSION['cut'.$webutlermedia->type]) && $_SESSION['cut'.$webutlermedia->type] != '') {
    	$webutlermedia->copyfilefromto($_SESSION['cut'.$webutlermedia->type], true);
        unset($_SESSION['cut'.$webutlermedia->type]);
    }
    if(isset($_SESSION['copy'.$webutlermedia->type]) && $_SESSION['copy'.$webutlermedia->type] != '') {
    	$webutlermedia->copyfilefromto($_SESSION['copy'.$webutlermedia->type]);
        unset($_SESSION['copy'.$webutlermedia->type]);
    }
    unset($_POST['insert']);
}

if(isset($_POST['cancel']) && $_POST['cancel'] == $webutlermedia->type) {
    if(isset($_SESSION['cut'.$webutlermedia->type]) && $_SESSION['cut'.$webutlermedia->type] != '') {
        unset($_SESSION['cut'.$webutlermedia->type]);
    }
    if(isset($_SESSION['copy'.$webutlermedia->type]) && $_SESSION['copy'.$webutlermedia->type] != '') {
        unset($_SESSION['copy'.$webutlermedia->type]);
    }
    unset($_POST['cancel']);
}

if(isset($_GET['delete']) && file_exists($webutlermedia->fileactionpath().$_GET['delete'])) {
	$webutlermedia->delete($_GET['delete']);
}

if(isset($_POST['foldername'])) {
	$webutlermedia->makefolder($webutlermedia->cleanpathfromget($_POST['foldername']));
}

if(isset($_GET['upload']) && $_GET['upload'] == 'newfile') {
	$overwrite = isset($_GET['overwrite']) && $_GET['overwrite'] == 'true' ? true : false;
	$imgsmallwidth = isset($_GET['imgsmallwidth']) && $_GET['imgsmallwidth'] > 0 ? $_GET['imgsmallwidth'] : 0;
	$imgsmallheight = isset($_GET['imgsmallheight']) && $_GET['imgsmallheight'] > 0 ? $_GET['imgsmallheight'] : 0;
	$imgboxwidth = isset($_GET['imgboxwidth']) && $_GET['imgboxwidth'] > 0 ? $_GET['imgboxwidth'] : 0;
	$imgboxheight = isset($_GET['imgboxheight']) && $_GET['imgboxheight'] > 0 ? $_GET['imgboxheight'] : 0;
	$lightbox = isset($_GET['lightbox']) && $_GET['lightbox'] == 'true' ? true : false;
	$webutlermedia->uploadfile($_GET['filename'], $overwrite, $imgsmallwidth, $imgsmallheight, $imgboxwidth, $imgboxheight, $lightbox);
	exit;
}

if(isset($_GET['pasted']) && $_GET['pasted'] == 'newfile') {
	$overwrite = isset($_GET['overwrite']) && $_GET['overwrite'] == 'true' ? true : false;
	$imgsmallwidth = isset($_GET['imgsmallwidth']) && $_GET['imgsmallwidth'] > 0 ? $_GET['imgsmallwidth'] : 0;
	$imgsmallheight = isset($_GET['imgsmallheight']) && $_GET['imgsmallheight'] > 0 ? $_GET['imgsmallheight'] : 0;
	$imgboxwidth = isset($_GET['imgboxwidth']) && $_GET['imgboxwidth'] > 0 ? $_GET['imgboxwidth'] : 0;
	$imgboxheight = isset($_GET['imgboxheight']) && $_GET['imgboxheight'] > 0 ? $_GET['imgboxheight'] : 0;
	$lightbox = isset($_GET['lightbox']) && $_GET['lightbox'] == 'true' ? true : false;
	$webutlermedia->uploadfile($_GET['filename'], $overwrite, $imgsmallwidth, $imgsmallheight, $imgboxwidth, $imgboxheight, $lightbox, true);
	exit;
}

$_SESSION['browseruploadfile']['tmpdir'] = $_SERVER['TMP'];

?>
<!DOCTYPE html>
<html lang="<?PHP echo isset($_SESSION['loggedin']['userlang']) ? $_SESSION['loggedin']['userlang'] : $webutlermedia->config['defaultlang']; ?>">
<head>
<title><?PHP echo _WBLANGADMIN_BROWSER_WINTITLE_; ?></title>
<meta charset="UTF-8" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="robots" content="noindex,nofollow" />
<link href="assets/styles.css" rel="stylesheet" type="text/css" />
<link href="assets/progress.css" rel="stylesheet" type="text/css" />
<link href="<?PHP echo $webutlermedia->config['homepage']; ?>/admin/system/css/loading.css" rel="stylesheet" type="text/css" />
<script src="assets/jscript.js"></script>
<script>
/* <![CDATA[ */
    function DeleteFile( FileName )
    {
		var confirmtxt = '<?PHP echo _WBLANGADMIN_BROWSER_DELALERT_; ?>';
    	if (confirm(confirmtxt.replace(/_STRING_/g, FileName)))
    	{
    		window.top.location.href = '<?PHP echo $webutlermedia->config['homepage']; ?>/admin/browser/index.php?<?PHP echo $webutlermedia->urltype.'&actualfolder='.$webutlermedia->makeurlfolder($webutlermedia->actualfolder()).$webutlermedia->getthumb.$webutlermedia->cke_getvars(); ?>&delete=' + FileName;
    	}
    }
    
    function uploadComplete()
    {
		window.top.location.href = '<?PHP echo $webutlermedia->config['homepage']; ?>/admin/browser/index.php?<?PHP echo $webutlermedia->urltype.'&actualfolder='.$webutlermedia->makeurlfolder($webutlermedia->actualfolder()).$webutlermedia->getthumb.$webutlermedia->cke_getvars(); ?>';
    }
	
    var imageeditorWindowWidth = '<?PHP echo $webutlermedia->config['imageeditor_wh'][0]; ?>';
    var imageeditorWindowHeight = '<?PHP echo $webutlermedia->config['imageeditor_wh'][1]; ?>';
    
    window.onresize = function() {
        setheights();
    }
    
    window.onload = function() {
        if(window.name == '')
            window.name = 'CKBrowseDialog';
        setheights();
        <?PHP echo $webutlermedia->jsalert; ?>
    }
/* ]]> */
</script>
</head>
<body scroll="no">
<div id="webutler_loadingscreen"></div>
<div id="uploadscreen">
    <div id="actiontext"><?PHP echo _WBLANGADMIN_BROWSER_UPLOADPROGRESS_; ?></div>
    <div id="actionprogress">
		<div id="prozent">
			<?PHP echo _WBLANGADMIN_BROWSER_UPLOADSTATUS_; ?>: <span id="prozentbar"></span>
		</div>
		<div id="progress">
			<div id="progressbar" style="display: none;"></div>
		</div>
		<div id="uploadcancel"><?PHP echo _WBLANGADMIN_BROWSER_UPLOADCANCEL_; ?></div>
	</div>
</div>
<div id="renamebg"></div>
<div id="renamepopup">
    <div id="renamepopup_close"><img alt="" src="images/close.gif" onclick="renamepopupclose()" /></div>
    <div id="renamepopup_text"><?PHP echo _WBLANGADMIN_BROWSER_RENAMEFILE_; ?></div>
    <div id="renamepopup_frame">
        <form method="post">
            <input type="hidden" name="renamepopup_oldname" id="renamepopup_oldname" value="" />
            <div id="renamepopup_newvalue">
            	<table border="0" cellspacing="0" cellpadding="0">
            	  <tr>
                	<td><input type="text" name="renamepopup_newname" id="renamepopup_newname" value="" /></td>
                	<td id="renamepopup_exttd"><span id="renamepopup_oldext"></span></td>
            	  </tr>
            	</table>
                
            </div>
            <input type="submit" class="inbutton" name="renamepopup_submit" id="renamepopup_submit" value="<?PHP echo _WBLANGADMIN_BROWSER_BUTTON_SAVE_; ?>" />
        </form>
    </div>
</div>
<table style="width: 100%; height: 100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="height: 40px; background-color: #DEDEDE; border-bottom: 1px solid #a6a6a6">
	<table style="width: 100%; height: 20px" border="0" cellspacing="10" cellpadding="0">
	  <tr>
		<?PHP
		if(!isset($_GET['type']) || $_GET['type'] == '') {
			echo '<td style="width: 178px">';
			echo '<select class="SelectResource" id="ResourceSelector" onchange="location.href = this.options[this.selectedIndex].value;" onmouseover="title = this.options[this.selectedIndex].title;">';
			echo '<option value="index.php?types=file&actualfolder='.$webutlermedia->makeurlfolder("/").$webutlermedia->cke_getvars().'" title="'.$webutlermedia->resourcetitle('file').'"';
				if($webutlermedia->type == 'file') {
					echo ' selected="selected"';
				}
				echo '>'.$webutlermedia->resourcetype('file').'</option>';
			echo '<option value="index.php?types=image&actualfolder='.$webutlermedia->makeurlfolder("/").$webutlermedia->cke_getvars().'" title="'.$webutlermedia->resourcetitle('image').'"';
				if($webutlermedia->type == 'image') {
					echo ' selected="selected"';
				}
				echo '>'.$webutlermedia->resourcetype('image').'</option>';
			echo '<option value="index.php?types=flash&actualfolder='.$webutlermedia->makeurlfolder("/").$webutlermedia->cke_getvars().'" title="'.$webutlermedia->resourcetitle('flash').'"';
				if($webutlermedia->type == 'flash') {
					echo ' selected="selected"';
				}
				echo '>'.$webutlermedia->resourcetype('flash').'</option>';
			echo '<option value="index.php?types=track&actualfolder='.$webutlermedia->makeurlfolder("/").$webutlermedia->cke_getvars().'" title="'.$webutlermedia->resourcetitle('track').'"';
				if($webutlermedia->type == 'track') {
					echo ' selected="selected"';
				}
				echo '>'.$webutlermedia->resourcetype('track').'</option>';
			echo '</select>';
			echo '</td>';
		}
		else
		{
    		if($webutlermedia->type == 'flash' || $webutlermedia->type == 'track') {
				if(isset($_GET['av']) && $_GET['av'] == 'only') {
					echo '<td style="width: 178px" class="ResourceType" title="'.$webutlermedia->resourcetitle('track').'">'.$webutlermedia->resourcetype('track').'</td>';
				}
				else {
					echo '<td style="width: 178px">';
					echo '<select class="SelectResource" id="ResourceSelector" onchange="location.href = this.options[this.selectedIndex].value;" onmouseover="title = this.options[this.selectedIndex].title;">';
					echo '<option value="index.php?type=flash&actualfolder='.$webutlermedia->makeurlfolder("/").$webutlermedia->cke_getvars().'" title="'.$webutlermedia->resourcetitle('flash').'"';
						if($webutlermedia->type == 'flash') {
							echo ' selected="selected"';
						}
						echo '>'.$webutlermedia->resourcetype('flash').'</option>';
					echo '<option value="index.php?type=track&actualfolder='.$webutlermedia->makeurlfolder("/").$webutlermedia->cke_getvars().'" title="'.$webutlermedia->resourcetitle('track').'"';
						if($webutlermedia->type == 'track') {
							echo ' selected="selected"';
						}
						echo '>'.$webutlermedia->resourcetype('track').'</option>';
					echo '</select>';
					echo '</td>';
				}
    		}
            else {
			    echo '<td style="width: 178px" class="ResourceType" title="'.$webutlermedia->resourcetitle('image').'">'.$webutlermedia->resourcetype('image').'</td>';
			}
		}
		?>
		<td style="vertical-align: bottom; padding-left: 2px">
			<?PHP echo _WBLANGADMIN_BROWSER_CURRENTPATH_; ?>: <strong><?PHP echo $webutlermedia->actualfolder(); ?></strong>
		</td>
	  </tr>
	</table>
	</td>
  </tr>
  <tr>
    <td style="background-color: #F7F7F7; vertical-align: top">
	<table style="width: 100%; height: 100%" border="0" cellspacing="10" cellpadding="0">
	  <tr>
		<td style="width: 178px; vertical-align: top; border: 1px solid #373737; background-color: #ffffff">
		  <div style="width: 178px; height: 100%; overflow: auto" id="folderswin">
            <table style="margin: 5px" border="0" cellspacing="0" cellpadding="0">
            <?PHP
                if($webutlermedia->actualfolder() != "/")
                {
                	echo '<tr>
                    	<td style="width: 21px"><img alt="" src="images/folderup.gif" style="margin-right: 5px" /></td>
                    	<td style="word-space: nowrap"><a href="index.php?'.$webutlermedia->urltype.'&actualfolder='.$webutlermedia->makeurlfolder($webutlermedia->lastfolder()).$webutlermedia->getthumb.$webutlermedia->cke_getvars().'" style="font-weight: bold; color: #b51719">&laquo; '._WBLANGADMIN_BROWSER_BUTTON_BACK_.'</a></td>
                    	</tr>';
                	
                	$datei = $webutlermedia->MediaDirectory.$webutlermedia->type.$webutlermedia->lastfolder();
                	
                	if(is_dir($datei))
                	{
                		$verzeichnis = opendir($datei);
                		while(false !== ($inhalt = readdir($verzeichnis)))
                		{
                			if($inhalt != "." && $inhalt != ".." && $inhalt != ".box")
                			{
                				if(is_dir($datei."/".$inhalt."/"))
                				{
                        			if(($inhalt == 'tpl_icons' && ($webutlermedia->config['admin_erweitert'] != '1' || (isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))) || $inhalt != 'tpl_icons')
                        			{
                    					echo '<tr>';
                    					if(str_replace('/', '', str_replace($webutlermedia->lastfolder(), '', $webutlermedia->actualfolder())) == $inhalt)
                    					{
                    						echo '<td style="width: 21px"><img alt="" src="images/folderopened.gif" style="margin-right: 5px" /></td>
                                              <td style="word-space: nowrap">'.$inhalt.'</td>';
                    					}
                    					else
                    					{
                    						echo '<td style="width: 16px"><img alt="" src="images/folder.gif" style="margin-right: 5px" /></td>
                                              <td style="word-space: nowrap"><a href="index.php?'.$webutlermedia->urltype.'&actualfolder='.$webutlermedia->makeurlfolder($webutlermedia->lastfolder().$inhalt.'/').$webutlermedia->getthumb.$webutlermedia->cke_getvars().'">'.$inhalt.'</a></td>';
                    					}
                    					echo '</tr>';
                        			}
                				}
                			}
                		}
                		closedir($verzeichnis);
                	}
                }
                clearstatcache();
            ?>
            </table>
          </div>
		</td>
		<td style="vertical-align: top; border: 1px solid #373737; background-color: #ffffff">
		  <div id="fileswin">
            <table style="width: 100%; margin: 3px 0px" border="0" cellspacing="0" cellpadding="0">
                <?PHP
                if($webutlermedia->type == 'image') {
                    echo '<tr>';
                    if(isset($_GET['vorschau'])) {
                        echo '<td colspan="8" class="togglepreview"><a href="index.php?'.$webutlermedia->urltype.'&actualfolder='.$webutlermedia->makeurlfolder($webutlermedia->actualfolder()).$webutlermedia->cke_getvars().'">'._WBLANGADMIN_BROWSER_LISTVIEW_.'</a></td>';
                    }
                    else {
                        echo '<td colspan="8" class="togglepreview"><a href="index.php?'.$webutlermedia->urltype.'&vorschau=1&actualfolder='.$webutlermedia->makeurlfolder($webutlermedia->actualfolder()).$webutlermedia->cke_getvars().'">'._WBLANGADMIN_BROWSER_THUMBVIEW_.'</a></td>';
                    }
                    echo '</tr>';
                }
                
                $directory = $webutlermedia->MediaDirectory.$webutlermedia->type.$webutlermedia->actualfolder();
                $folderslist = $webutlermedia->get_folders($directory);
				$first = $webutlermedia->type != 'image' && $folderslist == '' ? true : false;
                $fileslist = $webutlermedia->get_files($directory, $first);
				echo $folderslist;
				echo $fileslist;
                if((isset($_SESSION['cut'.$webutlermedia->type]) && $_SESSION['cut'.$webutlermedia->type] != '') || (isset($_SESSION['copy'.$webutlermedia->type]) && $_SESSION['copy'.$webutlermedia->type] != ''))
                {
                    echo '<tr';
					if($folderslist == '' && $fileslist == '' && $webutlermedia->type != 'image') {
						echo ' class="firstline"';
					}
                    echo '>';
        			echo '<td colspan="'.($webutlermedia->type == 'image' ? '6' : '5').'"';
					if(($folderslist == '' && $fileslist == '') || isset($_GET['vorschau']))
						echo ' width="100%"';
					echo '>&nbsp;</td>';
					echo '<td style="text-align:center">';
					echo '<form method="post"><input type="hidden" name="insert" value="'.$webutlermedia->type.'" /><div class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_INSERT_.'"><input type="image" src="images/insert.png" /></div></form>';
					echo '</td><td style="text-align:center">';
					echo '<form method="post" style="display:inline"><input type="hidden" name="cancel" value="'.$webutlermedia->type.'" /><div class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_CANCEL_.'"><input type="image" name="" src="images/cancel.png" /></div></form>';
					echo '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
          </div>
		</td>
	  </tr>
	</table>
	</td>
  </tr>
  <tr>
    <td style="height: 70px; background-color: #DEDEDE; border-top: 1px solid #a6a6a6">
	<table style="width: 100%; height: 50px" border="0" cellspacing="10" cellpadding="0">
	  <tr>
		<td style="text-align: right">
          <?PHP
			$disableforsys = ($webutlermedia->actualfolder() == '/watermarks/' || $webutlermedia->actualfolder() == '/tpl_icons/') ? ' disabled="disabled"' : '';
		  ?>
		  <form action="index.php?<?PHP echo $webutlermedia->urltype.'&actualfolder='.$webutlermedia->makeurlfolder($webutlermedia->actualfolder()).$webutlermedia->getthumb.$webutlermedia->cke_getvars(); ?>" method="post">
        	<table border="0" cellspacing="0" cellpadding="0" align="right">
        	  <tr>
        		<td><?PHP echo _WBLANGADMIN_BROWSER_NEWFOLDER_; ?></td>
                <td><input type="text" name="foldername" class="intext" style="margin-left: 5px; width: 220px" value=""<?PHP echo $disableforsys; ?> /></td>
                <td><input type="submit" class="inbutton" value="<?PHP echo _WBLANGADMIN_BROWSER_BUTTON_ADD_; ?>"<?PHP echo $disableforsys; ?> /></td>
        	  </tr>
        	</table>
		  </form>
		</td>
	  </tr>
	  <tr>
		<td style="text-align: right">
        	<table id="linefileupload" border="0" cellspacing="0" cellpadding="0" align="right">
        	  <tr>
        		<td><?PHP
    				if($webutlermedia->type == 'image' && $webutlermedia->actualfolder() != '/watermarks/' && $webutlermedia->actualfolder() != '/tpl_icons/') {
                        echo '<div class="fakeupload"><input type="file" name="uploadfile" id="uploadfile" size="41" style="margin-right: 5px" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="'._WBLANGADMIN_BROWSER_BUTTON_CHOOSE_.'" /></div>';
						echo '</td><td id="overwritetd">';
						echo '<div class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_OVERWRITE_.'"><input type="checkbox" name="overwrite" id="overwrite" checked="checked" /></div>';
						echo '</td><td>';
						echo '<div id="scalbox"><table id="scalimgsizes" border="0" cellspacing="0" cellpadding="0"><tr>';
						echo '<td colspan="5"><strong>'._WBLANGADMIN_BROWSER_SCAL_.'</strong></td>';
						echo '</tr><tr>';
						echo '<td colspan="5" class="scalimgsizetop">'._WBLANGADMIN_BROWSER_SIZEPAGEIMG_.'</td>';
						echo '</tr><tr>';
						echo '<td>'._WBLANGADMIN_BROWSER_SIZEIMGWIDTH_.':</td>';
						echo '<td><input type="text" id="imgsmallwidth" name="imgsmallwidth" size="4" maxlength="4" value="'.$webutlermedia->config['imgsmallsize'][0].'" class="intext" /> px</td>';
						echo '<td> x </td>';
						echo '<td>'._WBLANGADMIN_BROWSER_SIZEIMGHEIGHT_.':</td>';
						echo '<td><input type="text" id="imgsmallheight" name="imgsmallheight" size="4" maxlength="4" value="'.$webutlermedia->config['imgsmallsize'][1].'" class="intext" /> px</td>';
						echo '</tr><tr>';
						echo '<td colspan="5" class="scalimgsizetop">'._WBLANGADMIN_BROWSER_SIZEBOXIMG_.'</td>';
						echo '</tr><tr>';
						echo '<td>'._WBLANGADMIN_BROWSER_SIZEIMGWIDTH_.':</td>';
						echo '<td><input type="text" id="imgboxwidth" name="imgboxwidth" size="4" maxlength="4" value="'.$webutlermedia->config['imgboxsize'][0].'" class="intext" /> px</td>';
						echo '<td> x </td>';
						echo '<td>'._WBLANGADMIN_BROWSER_SIZEIMGHEIGHT_.':</td>';
						echo '<td><input type="text" id="imgboxheight" name="imgboxheight" size="4" maxlength="4" value="'.$webutlermedia->config['imgboxsize'][1].'" class="intext" /> px</td>';
						echo '</tr><tr>';
						echo '<td class="scalimgsizetop" style="text-align: right"><input type="checkbox" name="lightbox" id="lightbox" checked="checked" /></td><td colspan="4" class="scalimgsizetop"><label for="lightbox">'._WBLANGADMIN_BROWSER_LIGHTBOX_.'</label></td>';
						echo '</tr></table><img src="images/pointer.png" id="scalboxpointer" /></div></td>';
						echo '<td id="scalboxviewtd"><div id="scalboxview" class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_SCAL_.'"><img src="images/scaling.png" onclick="ScalboxView()" alt="'._WBLANGADMIN_BROWSER_SCAL_.'" /></div>';
    				}
    				else {
                        echo '<div class="fakeupload"><input type="file" id="uploadfile" name="uploadfile" size="41" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="'._WBLANGADMIN_BROWSER_BUTTON_CHOOSE_.'" /></div>';
						echo '</td><td id="overwritetd">';
						echo '<div class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_OVERWRITE_.'"><input type="checkbox" name="overwrite" id="overwrite" checked="checked" /></div>';
    				}
    			?></td>
                <td>
					<input type="hidden" id="actionpath" value="index.php?upload=newfile&<?PHP echo $webutlermedia->urltype.'&actualfolder='.$webutlermedia->makeurlfolder($webutlermedia->actualfolder()); ?>" />
					<input type="button" class="inbutton" value="<?PHP echo _WBLANGADMIN_BROWSER_MEDIAUPLOAD_; ?>" onclick="streamfileupload()" />
				</td>
        	  </tr>
        	</table>
		</td>
	  </tr>
	</table>
	</td>
  </tr>
</table>
</body>
</html>
