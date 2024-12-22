<?PHP 

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


header('content-type: text/html; charset=utf-8');

require_once '../settings/baseconfig.php';
require_once '../settings/globalvars.php';

function teste_chmods($directory)
{
	$handle = opendir($directory);
	$zeilen = '';
	while(false !== ($file = readdir($handle))) {
		if($file != '.' && $file != '..' && $file != '.htaccess' && $directory.'/'.$file != '../modules/news/data/media/loader.php') {
            $directoryfile = str_replace('../', '/', $directory.'/'.$file);
            if(is_dir($directory.'/'.$file)) {
                if(!is_writeable($directory.'/'.$file)) {
                    $zeilen.= '<tr><td style="padding-left: 20px">'.$directoryfile.'</td></tr>'."\n";
                }
                else {
                    $zeilen.= teste_chmods($directory.'/'.$file);
                }
            }
			else {
                if(!is_writeable($directory.'/'.$file)) {
                    $zeilen.= '<tr><td style="padding-left: 20px">'.$directoryfile.'</td></tr>'."\n";
    			}
			}
		}
	}
	closedir($handle);

	return $zeilen;
}

function generate_saltkeys()
{
	$lowersigns = 'abcdefghijklmnopqrstuvwxyz';
	$uppersigns = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$numbers = '0123456789';
	$signs = '#+-_*@%&=!?';
	
	$string = '';
	for($i = 0; $i < 4; $i++) {
		$string.= $lowersigns[mt_rand(0, strlen($lowersigns)-1)];
		$string.= $uppersigns[mt_rand(0, strlen($uppersigns)-1)];
	}
	for($k = 0; $k < 3; $k++) {
		$string.= $numbers[mt_rand(0, strlen($numbers)-1)];
		$string.= $signs[mt_rand(0, strlen($signs)-1)];
	}
	
	return str_shuffle(str_shuffle($string));
}

if($webutler_config['server_path'] != '' && $webutler_config['homepage'] != '' && 
    $webutler_config['user_name'] != '' && $webutler_config['user_pass'] != '') {
    	header('Location: '.$webutler_config['homepage'].'/');
    	exit;
}
else {
	if(isset($_GET['modrewritecheck']) && $_GET['modrewritecheck'] == 1) {
		echo 'enabled';
		exit;
	}
	
    session_start();
    if(isset($_POST['install_lang'])) {
        $_SESSION['install_lang'] = $_POST['install_lang'];
        header('Location: install.php');
    }
    if(isset($_POST['next_step'])) {
        $_SESSION['install_next'] = 1;
        header('Location: install.php');
    }
    
    if(isset($_SESSION['install_lang'])) $installlang = $_SESSION['install_lang'];
    elseif($webutler_config['user_lang'] != '') $installlang = $webutler_config['user_lang'];
    else $installlang = 'de';
    
    require_once 'system/lang/'.$installlang.'.php';

    $teste_chmods = teste_chmods('../content');
    clearstatcache();
	
	if(file_exists('../modules/news/data/news.db')) {
		if(!is_writeable('../modules/news/data/news.db'))
			$teste_chmods.= '<tr><td style="padding-left: 20px">/modules/news/data/news.db</td></tr>'."\n";
		
		if(is_dir('../modules/news/data/media/'))
            $teste_chmods.= teste_chmods('../modules/news/data/media');
		
		clearstatcache();
	}
    
?>
<!DOCTYPE html>
<html lang="<?PHP echo $installlang; ?>">
<head>
	<title>WEBUTLER - <?PHP echo _WBLANGADMIN_INSTALL_TITLE_; ?></title>
	<meta charset="utf-8" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="robots" content="noindex,nofollow" />
	<link href="system/css/admin.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div style="text-align: center; margin-top: 110px">
	<img src="system/images/webutler.gif" />
	<div style="margin: 10px 0px"><strong style="letter-spacing: 8px"><?PHP echo _WBLANGADMIN_INSTALL_TITLE_; ?></strong></div>
<?PHP if(!isset($_SESSION['install_next'])) { ?>
	<div id="webutler_install" style="text-align: center">
		<form action="install.php" style="margin: 0; padding: 0" method="post">
<?PHP
			$directory = 'system/lang';					
			$handle = opendir ($directory);
			while(false !== ($file = readdir($handle))) { 
				if(!is_dir($directory.'/'.$file.'/') && $file != '.' && $file != '..') {
					$ext = substr($file, strrpos($file, '.'));
					$lang = strtolower(substr($file, 0, strlen($file)-strlen($ext)));
					echo '<div class="webutler_installlangs">'."\n".
						'<table border="0" cellspacing="0" cellpadding="0">'."\n".
						'<tr>'."\n".
						'<td><input type="radio" name="install_lang" class="webutler_langradio" id="'.$lang.'_lang" value="'.$lang.'"';
					if($lang == $installlang)
						echo ' checked="checked"';
					echo ' onclick="submit()" /></td>'."\n".
						'<td><label for="'.$lang.'_lang">';
					if(file_exists('../includes/language/icons/'.$lang.'.png'))
						echo '<img src="../includes/language/icons/'.$lang.'.png" />';
					else
						echo '<div class="webutler_installflag">'.$lang.'</div>';
					echo '</label></td>'."\n".
						'</tr>'."\n".
						'</table>'."\n".
						'</div>'."\n";
				}
			}
			closedir($handle);
?>
			<div id="webutler_installnext"><input type="submit" name="next_step" class="webutler_button" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_NEXT_; ?>" /></div>
		</form>
	</div>
<?PHP } elseif(version_compare(PHP_VERSION, '5.4.0', '<')) { ?>
    <div id="webutler_install"><?PHP echo _WBLANGADMIN_INSTALL_PHPVERSION_; ?></div>
<?PHP } elseif(@file_get_contents('http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'], 0, -18).'/checks/server/mod/rewrite') != 'enabled' && $webutler_config['modrewrite'] == '1') { ?>
	<div style="width: 420px; margin: 40px auto">
		<?PHP echo _WBLANGADMIN_INSTALL_REWRITE_; ?>
		<div style="margin: 15px 0px 20px 0px; text-align: center">
			$webutler_config['modrewrite'] = &quot;0&quot;;
		</div>
		<input type="button" value="<?PHP echo _WBLANGADMIN_INSTALL_REWRITEAGAIN_; ?>" class="webutler_button" onclick="location.reload()" />
	</div>
<?PHP } elseif($webutler_config['salt_key1'] == '' || $webutler_config['salt_key2'] == '') { ?>
	<div style="width: 480px; margin: 40px auto">
		<?PHP echo _WBLANGADMIN_INSTALL_SALTKEYS_; ?>
		<div style="margin: 15px 0px 20px 100px; text-align: left">
			$webutler_config['salt_key1'] = &quot;<?PHP echo generate_saltkeys(); ?>&quot;;<br />
			$webutler_config['salt_key2'] = &quot;<?PHP echo generate_saltkeys(); ?>&quot;;
		</div>
		<input type="button" value="<?PHP echo _WBLANGADMIN_INSTALL_KEYSAGAIN_; ?>" class="webutler_button" onclick="location.reload()" />
	</div>
<?PHP } elseif(!is_writeable('../settings/baseconfig.php') || $teste_chmods != '') { ?>
	<div id="webutler_install">
		<div style="overflow: auto">
			<table width="100%" border="0" cellspacing="5" cellpadding="0">
			  <tr> 
				<td><strong><?PHP echo _WBLANGADMIN_INSTALL_ERROR_; ?></strong></td>
			  </tr>
			  <tr> 
				<td style="padding: 5px 0px">
				  <?PHP echo _WBLANGADMIN_INSTALL_CHMODERROR_1_; ?><br />
				  <?PHP echo _WBLANGADMIN_INSTALL_CHMODERROR_2_; ?>
				  &quot;<?PHP echo _WBLANGADMIN_INSTALL_CHMODAGAIN_; ?>&quot;
				</td>
			  </tr>
<?PHP
			if(!is_writeable('../settings/baseconfig.php'))
				echo '<tr>'."\n".'<td style="padding-left: 20px">'."\n".'settings/baseconfig.php'."\n".'</td>'."\n".'</tr>'."\n";
			
			echo $teste_chmods;
?>
			  <tr>
				<td style="text-align: center; padding-top: 5px"><input type="button" value="<?PHP echo _WBLANGADMIN_INSTALL_CHMODAGAIN_; ?>" class="webutler_button" onclick="location.reload()" /></td>
			  </tr>
			</table>
		</div>
	</div>
<?PHP } else {
	if (isset($_POST['save'])) {
		if(!($fp = @fopen('../settings/baseconfig.php', 'r'))) {
			echo '<strong>'._WBLANGADMIN_INSTALL_ERROR_.'</strong><br /><br />'._WBLANGADMIN_INSTALL_CONFIGNOWRITE_.'<br /><br />';
		} 
		else {
			$username = trim(strip_tags($_POST['username']));
			$username = preg_replace('/[^a-zA-Z0-9\.\_\-]/', '', $username);
			
			if($_POST['server'] == '' || $_POST['homepage'] == '' || $_POST['username'] == '' || $_POST['userpass'] == '' || $_POST['userlang'] == '') {
				echo '<strong>'._WBLANGADMIN_INSTALL_ERROR_.'</strong><br /><br />'._WBLANGADMIN_INSTALL_FIELDEMPTY_.'<br /><br />'.
					'<a href="javascript:history.back()">'._WBLANGADMIN_WIN_BUTTONS_BACK_.'</a>';
			}
			elseif($username != trim($_POST['username'])) {
				echo '<strong>'._WBLANGADMIN_INSTALL_ERROR_.'</strong><br /><br />'._WBLANGADMIN_INSTALL_WRONGSIGN_.'<br /><br />'.
					'<a href="javascript:history.back()">'._WBLANGADMIN_WIN_BUTTONS_BACK_.'</a>';
			}
			elseif(!preg_match('#^[a-zA-Z0-9\#\+\-_\*\@\%\&\=\!\?]+$#', $_POST['userpass'])) {
				echo '<strong>'._WBLANGADMIN_INSTALL_ERROR_.'</strong><br /><br />'._WBLANGADMIN_INSTALL_WRONGPASS_1_.'<br />'._WBLANGADMIN_INSTALL_WRONGPASS_2_.'<br /><br />'.
					'<a href="javascript:history.back()">'._WBLANGADMIN_WIN_BUTTONS_BACK_.'</a>';
			}
			else {
				$server = trim(strip_tags($_POST['server']));
				if(substr($server, -1) == '/') $server = substr($server, 0, strlen($server)-1);
				$homepage = trim(strip_tags($_POST['homepage']));
				if(substr($homepage, -1) == '/') $homepage = substr($homepage, 0, strlen($homepage)-1);
				$userpass = md5($webutler_config['salt_key1'].trim($_POST['userpass']).$webutler_config['salt_key2']);
				$userlang = preg_replace('/[^a-z]/', '', strtolower(strip_tags($_POST['userlang'])));
				$startseite = strip_tags($_POST['startseite']);
				$chmodfolders = substr(sprintf('%o', fileperms($server.'/content/access')), -4);
				$chmodfiles = substr(sprintf('%o', fileperms($server.'/settings/baseconfig.php')), -4);
				clearstatcache();
				
				$buf = file_get_contents('../settings/baseconfig.php');
				$buf = preg_replace('#(\$webutler_config\[\'server_path\'\] = ")([^\"]*)(";)#Usi', '${1}'.$server.'$3', $buf);
				$buf = preg_replace('#(\$webutler_config\[\'homepage\'\] = ")([^\"]*)(";)#Usi', '${1}'.$homepage.'$3', $buf);
				$buf = preg_replace('#(\$webutler_config\[\'user_name\'\] = ")([^\"]*)(";)#Usi', '${1}'.$username.'$3', $buf);
				$buf = preg_replace('#(\$webutler_config\[\'user_pass\'\] = ")([^\"]*)(";)#Usi', '${1}'.$userpass.'$3', $buf);
				$buf = preg_replace('#(\$webutler_config\[\'user_lang\'\] = ")([^\"]*)(";)#Usi', '${1}'.$userlang.'$3', $buf);
				$buf = preg_replace('#(\$webutler_config\[\'startseite\'\] = ")([^\"]*)(";)#Usi', '${1}'.$startseite.'$3', $buf);
				$buf = preg_replace('#(\$webutler_config\[\'chmod\'\] = array\()([^\)]*)(\);)#Usi', '${1}'.$chmodfolders.', '.$chmodfiles.'$3', $buf);

				if (!($fp = @fopen('../settings/baseconfig.php', 'w'))) {
					echo '<strong>'._WBLANGADMIN_INSTALL_ERROR_.'</strong><br /><br />'._WBLANGADMIN_INSTALL_NOTSAVED_.'<br /><br />'.
					'<a href="javascript:history.back()">'._WBLANGADMIN_WIN_BUTTONS_BACK_.'</a>';
				} 
				else {
					fwrite($fp, $buf);
					fclose($fp);
					unset($_SESSION);
					echo '<strong>'._WBLANGADMIN_INSTALL_INSTALLOK_.'</strong><br /><br /><br />'.
						'<strong>'._WBLANGADMIN_INSTALL_INSTOKTXT_1_.'</strong> '._WBLANGADMIN_INSTALL_INSTOKTXT_2_.'<br />'.
						'<strong>/settings/globalvars.php</strong> '._WBLANGADMIN_INSTALL_INSTOKTXT_3_.'<br /><br /><br />'.
						'<a href="../">'._WBLANGADMIN_INSTALL_LINKSTART_.'</a> &nbsp; &nbsp; <a href="index.php">'._WBLANGADMIN_INSTALL_LINKLOGIN_.'</a>';
				}
			}
		}
	}
	else {
		$host = 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'];
		$doc_root = substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME'])-18);
		$subfolder = substr($_SERVER['SCRIPT_NAME'], 0, -18);
		if(substr($doc_root, strlen($subfolder)*(-1)) == $subfolder) {
			$docroot = substr($doc_root, 0, strlen($doc_root)-strlen($subfolder));
		}
		else {
			$docroot = $doc_root;
		}
		
		echo _WBLANGADMIN_INSTALL_CHMODOK_.'<br /><br />';
?>
	<form action="install.php" style="margin: 0; padding: 0" method="post">
		<div id="webutler_install">
			<table width="100%" border="0" cellspacing="5" cellpadding="0">
			  <tr> 
				<td><strong><?PHP echo _WBLANGADMIN_INSTALL_FIELDUSERNAME_; ?>:</strong></td>
				<td><input type="text" name="username" class="webutler_input" /></td>
			  </tr>
			  <tr> 
				<td><strong><?PHP echo _WBLANGADMIN_INSTALL_FIELDUSERPASS_; ?>:</strong></td>
				<td><input type="text" name="userpass" class="webutler_input" /></td>
			  </tr>
			  <tr> 
				<td><strong><?PHP echo _WBLANGADMIN_INSTALL_FIELDSERVERPATH_; ?>:</strong></td>
				<td><input type="text" name="server" value="<?PHP echo $docroot.$subfolder; ?>" class="webutler_input" /></td>
			  </tr>
			  <tr> 
				<td><strong><?PHP echo _WBLANGADMIN_INSTALL_FIELDHOMEPAGEURL_; ?>:</strong></td>
				<td><input type="text" name="homepage" value="<?PHP echo $host.$subfolder; ?>" class="webutler_input" /></td>
			  </tr>
			  <tr> 
				<td><strong><?PHP echo _WBLANGADMIN_INSTALL_FIELDLANGUAGE_; ?>:</strong></td>
				<td><select name="userlang" size="1" class="webutler_select">
<?PHP 
					$directory = $docroot.$subfolder.'/admin/system/lang';					
					$handle = opendir ($directory);
					while(false !== ($file = readdir($handle))) { 
						if(!is_dir($directory.'/'.$file.'/') && $file != '.' && $file != '..') {
							$ext = substr($file, strrpos($file, '.'));
							$lang = strtolower(substr($file, 0, strlen($file)-strlen($ext)));
							echo '<option value="'.$lang.'"';
							if($lang == $_SESSION['install_lang']) {
								echo ' selected="selected"';
							}
							echo '>'.$lang.'</option>'."\n";
						}
					}
					closedir($handle);
?>
				</select></td>
			  </tr>
			  <tr> 
				<td><strong><?PHP echo _WBLANGADMIN_INSTALL_FIELDHOMEPAGE_; ?>:</strong></td>
				<td><select name="startseite" size="1" class="webutler_select">
<?PHP 
					$directory = $docroot.$subfolder.'/content/pages';					
					$handle = opendir ($directory);
					while(false !== ($file = readdir($handle))) { 
						$extension = substr($file, strrpos($file, '.'));
						$extension = strtolower($extension);
						if($file != '.' && $file != '..' && $file != '.htaccess' && $extension != '.bak') {
							echo '<option value="'.$file.'"';
							if($webutler_config['startseite'] != '' && $file == $webutler_config['startseite']) {
								echo ' selected="selected"';
							}
							echo '>'.$file.'</option>'."\n";
						}
					}
					closedir($handle);
?>
				</select></td>
			  </tr>
			  <tr> 
				<td>&nbsp;</td>
				<td><input type="submit" name="save" value="<?PHP echo _WBLANGADMIN_INSTALL_SAVESETTINGS_; ?>" class="webutler_button" /></td>
			  </tr>
			</table>
		</div>
	</form> 
<?PHP
        }
    }
?>
</div>
<?PHP } ?>
</body>
</html>
