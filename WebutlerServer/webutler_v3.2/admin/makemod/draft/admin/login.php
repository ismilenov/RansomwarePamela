<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/modules/###MODULENAME###/login.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

if(isset($_GET['logout']) && $_GET['logout'] == 1) {
	$auth = session_name("WEBUTLER");
    setcookie($auth, "", time()-100000, "/");
	session_destroy();
    
    header("Location: admin.php");
	exit;
}
elseif(isset($_POST['###MODULENAME###lang'])) {
	$_SESSION['###MODULENAME###lang'] = preg_replace("/[^a-z]/", "", $_POST['###MODULENAME###lang']);
	
    header("Location: admin.php");
	exit;
}
elseif(isset($_POST['editorlogin'])) {
    require_once $webutlermodadmin->config['server_path'].'/includes/users/lang/'.$###MODULENAME###_adminlang.'.php';
    
    $webutlermodadmin->checkantixpost();
	
	if(!$webutlermodadmin->loginattempts())
	{
		$###MODULENAME###_loginattempts = str_replace('__TIME__', $webutlermodadmin->config['logattemptmin'], _MODMAKERLANGADMIN_LOGIN_ATTEMPTS_);
	}
	else
	{
		$pass = $webutlermodadmin->config['salt_key1'].trim($_POST['userpass']).$webutlermodadmin->config['salt_key2'];
		
		if(($_POST['username'] == $webutlermodadmin->config['user_name'] && md5($pass) == $webutlermodadmin->config['user_pass']) || 
		  ($_POST['username'] == $webutlermodadmin->config['admin_name'] && md5($pass) == $webutlermodadmin->config['admin_pass']))
		{
			session_regenerate_id();
			
			$_SESSION['loggedin']['username'] = md5($_POST['username']);
			$_SESSION['loggedin']['userpass'] = md5($pass);
			$_SESSION['loggedin']['editboxzustand'] = 'show';
			if($_SESSION['loggedin']['username'] == md5($webutlermodadmin->config['admin_name']) && 
			  $_SESSION['loggedin']['userpass'] == $webutlermodadmin->config['admin_pass']) {
				$_SESSION['loggedin']['userlang'] = $webutlermodadmin->config['admin_lang'];
				$_SESSION['loggedin']['userisadmin'] = '1';
			}
			else {
				$_SESSION['loggedin']['userlang'] = $webutlermodadmin->config['user_lang'];
				$_SESSION['loggedin']['userisowner'] = '1';
			}
			if(isset($_POST['editorlang']))
				$_SESSION['###MODULENAME###lang'] = preg_replace("/[^a-z]/", "", $_POST['editorlang']);
			
			header("Location: admin.php");
			exit;
		}
		else {
			require_once $webutlermodadmin->config['server_path'].'/includes/users/user_class.php';
			$###MODULENAME###_editorlogin = new UsersClass;
			$###MODULENAME###_editorlogin->dbpath = $webutlermodadmin->config['server_path'].'/content/access';
			$###MODULENAME###_editorlogin->saltkeys[0] = $webutlermodadmin->config['salt_key1'];
			$###MODULENAME###_editorlogin->saltkeys[1] = $webutlermodadmin->config['salt_key2'];
			$###MODULENAME###_editorlogin->postdata = $_POST;
			$###MODULENAME###_editorlogin->login();
			
			if($###MODULENAME###_editorlogin->meldung == '') {
				$_SESSION['userauth'] = $###MODULENAME###_editorlogin->userauth;
				if($###MODULENAME###_class->getsubeditorsgroup($_SESSION['userauth']['groupid'])) {
					$_SESSION['###MODULENAME###log'] = 'true';
					if(isset($_POST['editorlang']))
						$_SESSION['###MODULENAME###lang'] = preg_replace("/[^a-z]/", "", $_POST['editorlang']);
					
					header("Location: admin.php");
					exit;
				}
				else {
					$###MODULENAME###_editorlogin->meldung = _MODMAKERLANGADMIN_LOGIN_NOPERMISSION_;
				}
			}
		}
		unset($_POST);
		unset($###MODULENAME###_editorlogin->postdata);
	}
}
else {
	if(isset($_SESSION['userauth']) && $_SESSION['userauth']['groupid'] != '' && $###MODULENAME###_class->getsubeditorsgroup($_SESSION['userauth']['groupid']) && (!isset($_SESSION['###MODULENAME###log']) || $_SESSION['###MODULENAME###log'] != 'true')) {
	    $_SESSION['###MODULENAME###log'] = 'true';
	    header("Location: admin.php");
		exit;
	}
}

$###MODULENAME###_langvars = $webutlermodadmin->getmodulesloginlang('###MODULENAME###', $###MODULENAME###_adminlang);

$modlog_antixpostcode = $webutlermodadmin->antixpostcode();
$_SESSION['antixpost'] = $modlog_antixpostcode;

?>
<!DOCTYPE html>
<html lang="<?PHP echo $###MODULENAME###_adminlang; ?>">
<head>
<title><?PHP echo _MODMAKERLANGADMIN_MODNAME_.' - '._MODMAKERLANGADMIN_LOGIN_HEADER_; ?></title>
	<meta charset="UTF-8" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="robots" content="noindex,nofollow" />
	<link href="<?PHP echo $webutlermodadmin->config['homepage']; ?>/modules/###MODULENAME###/admin/admin.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div style="padding: 30px">
	<h1 id="editorsloghead"><?PHP echo _MODMAKERLANGADMIN_MODNAME_.'<br /><br />'._MODMAKERLANGADMIN_LOGIN_HEADER_; ?></h1>
	<div id="editorslogin">
		<?PHP if(isset($###MODULENAME###_loginattempts) || (isset($###MODULENAME###_editorlogin) && $###MODULENAME###_editorlogin->meldung != '') || count($###MODULENAME###_langvars) >= 2) { ?>
		<table border="0" cellspacing="0" cellpadding="5" align="center">
		  <?PHP if(isset($###MODULENAME###_editorlogin) && $###MODULENAME###_editorlogin->meldung != '') { ?>
		  <tr>
			<td colspan="2" class="fehler"><?PHP echo $###MODULENAME###_editorlogin->meldung; ?></td>
		  </tr>
		  <?PHP } ?>
		  <?PHP if(isset($###MODULENAME###_loginattempts)) { ?>
		  <tr>
			<td colspan="2" class="fehler"><?PHP echo $###MODULENAME###_loginattempts; ?></td>
		  </tr>
		  <?PHP } ?>
		  <?PHP
			if(is_array($###MODULENAME###_langvars) && count($###MODULENAME###_langvars) >= 2) {
			  	echo '<tr><td colspan="2" class="setlang">';
				echo '<form action="admin.php" method="post">';
				foreach($###MODULENAME###_langvars as $langvar) {
					echo $langvar;
				}
				echo '</form>';
				echo '</td></tr>';
			}
		  ?>
		</table>
		<?PHP } ?>
		<form action="admin.php" method="post">
			<table border="0" cellspacing="0" cellpadding="5" align="center">
			  <tr>
				<td><?PHP echo _MODMAKERLANGADMIN_LOGIN_USERNAME_; ?></td>
				<td><input type="text" class="userinput" name="username" /></td>
			  </tr>
			  <tr>
				<td><?PHP echo _MODMAKERLANGADMIN_LOGIN_PASSWORD_; ?></td>
				<td><input type="password" class="userinput" name="userpass" /></td>
			  </tr>
			  <tr>
				<td></td>
				<td><input type="submit" class="userbutton" name="editorlogin" value="<?PHP echo _MODMAKERLANGADMIN_LOGIN_SUBMIT_; ?>" /></td>
			  </tr>
			</table>
			<input type="hidden" name="editorlang" value="<?PHP echo $###MODULENAME###_adminlang; ?>" />
	        <input type="hidden" name="webutler_autokill" value="<?PHP echo md5($modlog_antixpostcode); ?>" />
		</form>
    </div>
</div>
</body>
</html>
