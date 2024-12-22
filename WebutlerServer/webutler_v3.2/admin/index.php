<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(__FILE__)).'/includes/loader.php';

$webutler = new WebutlerClass;
$webutler->config = $webutler_config;
$webutler->htmlsource = $webutler_htmlsource;
$webutler->moduleslist = $webutler_moduleslist;

$loginlang = ($webutler->config['user_lang'] != '') ? $webutler->config['user_lang'] : $webutler->config['defaultlang'];
require_once $webutler->config['server_path'].'/admin/system/lang/'.$loginlang.'.php';

header("Pragma:no-cache");
header("Cache-Control:private,no-store,no-cache,must-revalidate");

if(!isset($_GET['logout'])) {
    if(!isset($_COOKIE['WEBUTLER'])) {
        if(isset($_GET['checkcookie']) && $_GET['checkcookie'] == 1) {
            $meldung = _WBLANGADMIN_LOGIN_ERRORNOCOOKIE_;
            $path = $webutler->config['homepage'].'/admin/';
            $url = $path.'index.php';
        }
        else {
            header('Refresh: 1; url='.$webutler->config['homepage'].'/admin/index.php?checkcookie=1');
            exit;
        }
    }
    else {
        if($webutler->config['modrewrite'] == '1') {
            if('http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] != $webutler->config['homepage'].'/login')
                header('Location: '.$webutler->config['homepage'].'/login');
            
            $url = $webutler->config['homepage'].'/login';
        }
        else {
            if('http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] != $webutler->config['homepage'].'/admin/')
                header('Location: '.$webutler->config['homepage'].'/admin/');
            
            $url = $webutler->config['homepage'].'/admin/index.php';
        }
        $path = $webutler->config['homepage'].'/admin/';
    }
}

if(isset($_SESSION['history']['logpage']) && isset($_SESSION['history']['thispage']) && $_SESSION['history']['thispage'] != $webutler->config['homepage'].'/') {
    unset($_SESSION['history']['logpage']);
}
if(!isset($_SESSION['history']['logpage'])) {
    if(isset($_GET['logout'])) {
        if($webutler->config['modrewrite'] == '1' && preg_match('#index\.php\?page=#', $_SESSION['history']['thispage']))
            $_SESSION['history']['logpage'] = preg_replace('#(.*)(index\.php\?page=)([\w\d\_]+)(.*)#', '${1}${3}', $_SESSION['history']['thispage']).$webutler->config['urlendung'];
        else
            $_SESSION['history']['logpage'] = $_SESSION['history']['thispage'];
    }
    else {
        $_SESSION['history']['logpage'] = (isset($_SESSION['history']['thispage'])) ? $_SESSION['history']['thispage'] : $webutler->config['homepage'].'/';
    }
}

if(isset($_POST['webutler_login'])) 
{
	$webutler->checkantixpost();
	
	if(!$webutler->loginattempts())
	{
		$meldung = str_replace('__TIME__', $webutler->config['logattemptmin'], _WBLANGADMIN_LOGIN_ATTEMPTS_);
	}
	else
	{
		$pass = $webutler->config['salt_key1'].trim($_POST['pass']).$webutler->config['salt_key2'];
		
		if(($_POST['name'] == $webutler->config['user_name'] && md5($pass) == $webutler->config['user_pass']) || 
		  ($_POST['name'] == $webutler->config['admin_name'] && md5($pass) == $webutler->config['admin_pass']))
		{
			$lastpage = $_SESSION['history']['logpage'];
			
			session_regenerate_id();
			
			$_SESSION['loggedin']['username'] = md5($_POST['name']);
			$_SESSION['loggedin']['userpass'] = md5($pass);
			$_SESSION['loggedin']['editboxzustand'] = 'show';
			if($_SESSION['loggedin']['username'] == md5($webutler->config['admin_name']) && 
			  $_SESSION['loggedin']['userpass'] == $webutler->config['admin_pass']) {
				$_SESSION['loggedin']['userlang'] = $webutler->config['admin_lang'];
				$_SESSION['loggedin']['userisadmin'] = '1';
			}
			else {
				$_SESSION['loggedin']['userlang'] = $webutler->config['user_lang'];
				$_SESSION['loggedin']['userisowner'] = '1';
			}
			
			header('Location: '.$lastpage);
			exit;
		}
		else
		{
			$meldung = _WBLANGADMIN_LOGIN_ERRORWRONGDATA_;
		}
	}
}
    
if(isset($_GET['logout']) && $_GET['logout'] == 'yes') {
    if($webutler->checkadmin()) {
        $lastpage = $_SESSION['history']['logpage'];
        
    	$auth = session_name('WEBUTLER');
        setcookie($auth, '', time()-100000, '/');
    	session_destroy();
        
        header('Location: '.$lastpage);
    }
    else {
    	header('Location: '.$webutler->config['homepage'].'/');
    }
	exit;
}

$login_antixpostcode = $webutler->antixpostcode();
$_SESSION['antixpost'] = $login_antixpostcode;

?>
<!DOCTYPE html>
<html lang="<?PHP echo $loginlang; ?>">
<head>
	<title>WEBUTLER - <?PHP echo _WBLANGADMIN_LOGIN_TITLE_; ?></title>
	<meta charset="utf-8" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="robots" content="noindex,nofollow" />
	<link href="<?PHP echo $path; ?>system/css/admin.css" rel="stylesheet" type="text/css" />
    <script>
    /* <![CDATA[ */
        window.onload = function() {
            document.login.name.focus();
        }
    /* ]]> */
    </script>
</head>
<body>
  <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	  <td align="center" style="padding-bottom: 80px">
		  <table border="0" cellspacing="20" cellpadding="0">
			<tr> 
			  <td align="center">
        	    <img src="<?PHP echo $path; ?>system/images/webutler.gif" />
              </td>
			</tr>
			<tr> 
			  <td class="webutler_loginborder">
        		<form name="login" method="post" action="<?PHP echo $url; ?>" style="margin: 0; padding: 0">
        		  <table border="0" cellspacing="5" cellpadding="0">
        			<tr> 
        			  <td colspan="2">
        				<strong><?PHP echo _WBLANGADMIN_LOGIN_TITLE_; ?></strong><?PHP if(isset($meldung)) echo ' - <span style="color: #9E2627">'.$meldung.'</span>'; ?>
        			  </td>
        			</tr>
        			<tr> 
        			  <td><?PHP echo _WBLANGADMIN_LOGIN_USER_; ?>:</td>
        			  <td><input type="text" name="name" class="webutler_input webutler_loginput" /></td>
        			</tr>
        			<tr> 
        			  <td><?PHP echo _WBLANGADMIN_LOGIN_PASS_; ?>:</td>
        			  <td><input type="password" name="pass" class="webutler_input webutler_loginput" /></td>
        			</tr>
        			<tr> 
        			  <td>&nbsp;</td>
        			  <td><input type="submit" name="webutler_login" value="<?PHP echo _WBLANGADMIN_LOGIN_LOGIN_; ?>" class="webutler_button webutler_logbutton" /></td>
        			</tr>
        		  </table>
        		  <input type="hidden" name="webutler_autokill" value="<?PHP echo md5($login_antixpostcode); ?>" />
        		</form>
              </td>
			</tr>
            <?PHP
                if(($webutler->config['modsonlog'] == 1 || $webutler->config['modsonlog'] == 3) && is_array($webutler->moduleslist) && count($webutler->moduleslist) >= 1) {
                    $loadmodules = 'false';
                	foreach($webutler->moduleslist as $module) {
                        if(!isset($module[2]) || (isset($module[2]) && $module[2] != '-')) {
                    		$loadmodules = 'true';
							break;
						}
            		}
                    if($loadmodules == 'true') {
            ?>
			<tr> 
			  <td class="webutler_loginborder">
                <table border="0" cellspacing="10" cellpadding="0">
                  <tr>
                    <td><strong><?PHP echo _WBLANGADMIN_WIN_ADMIN_MODULES_; ?></strong></td>
                  </tr>
                  <?PHP
                	foreach($webutler->moduleslist as $module) {
                        if(!isset($module[2]) || (isset($module[2]) && $module[2] != '-')) {
							$modpath = $module[1];
							if(substr($modpath, 0, 1) != '/') $modpath = '/'.$modpath;
                    		echo '<tr>'."\n".
                    		'<td style="padding-left: 20px">&raquo; <a href="'.$webutler->config['homepage'].'/modules'.$modpath.'">'.$module[0].'</a></td>'."\n".
                    		'</tr>'."\n";
                		}
            		}
                  ?>
                </table>
              </td>
            </tr>
            <?PHP
                    }
                }
            ?>
        </table>
	  </td>
	</tr>
  </table>
</body>
</html>
