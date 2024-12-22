<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


if(preg_match('#/includes/users/loginblock.tpl#i', $_SERVER['REQUEST_URI']))
    exit('no access');

if(!isset($userspages_docroot)) {
    $userspages_docroot = $webutlercouple->config['server_path'].'/includes/users';
    if(isset($_SESSION['language']) && file_exists($userspages_docroot.'/'.$_SESSION['language'].'.php'))
        include_once $userspages_docroot.'/lang/'.$_SESSION['language'].'.php';
    else
        include_once $userspages_docroot.'/lang/'.$webutlercouple->config['defaultlang'].'.php';
}

$userscontent = array();

if(!file_exists($webutlercouple->config['server_path'].'/content/access/regconf.php')) {
    $userscontent['error'] = _USERSLANG_ERROR_;
    $userscontent['text'] = _USERSLANG_MODNOTINST_;
	
	ob_start();
	include $userspages_docroot.'/tpls/noregconf.tpl';
	$noregconf = ob_get_contents();
	ob_end_clean();
	
	echo $noregconf;
}
else {
    if(!class_exists('UsersClass'))
        include_once $webutlercouple->config['server_path'].'/includes/users/user_class.php';
    if(!isset($webutler_userreg))
        include_once $webutlercouple->config['server_path'].'/content/access/regconf.php';

	if(file_exists($webutlercouple->config['server_path'].'/includes/users/css/loginblock.css'))
		$webutlercouple->autoheaderdata[] = '<link href="includes/users/css/loginblock.css?t='.filemtime($webutlercouple->config['server_path'].'/includes/users/css/loginblock.css').'" rel="stylesheet" type="text/css" />';

    if(isset($_POST['userlogin']) || isset($_POST['userlogout']))
    {
        $webutler_userslogin = new UsersClass;
        $webutler_userslogin->saltkeys[0] = $webutlercouple->config['salt_key1'];
        $webutler_userslogin->saltkeys[1] = $webutlercouple->config['salt_key2'];
        
        if(isset($_POST['userlogin'])) {
            $webutler_userslogin->dbpath = $webutlercouple->config['server_path'].'/content/access';
            $webutler_userslogin->postdata = $_POST;
			if(!$webutlercouple->loginattempts()) {
				$webutler_userslogin->meldung = str_replace('__TIME__', $webutlercouple->config['logattemptmin'], _USERSLANG_LOGATTEMPTS_);
			}
			else {
				$webutler_userslogin->login();
			}
            if($webutler_userslogin->meldung == '') {
                $_SESSION['userauth'] = $webutler_userslogin->userauth;
                $webutler_userslogin->meldung = _USERSLANG_WELCOME_.' '.$_SESSION['userauth']['username'].', '._USERSLANG_YOULOGIN_;
            }
            unset($_POST);
            unset($webutler_userslogin->postdata);
        }
        if(isset($_POST['userlogout'])) {
            $webutler_userslogin->meldung = _USERSLANG_GOODBY_.' '.$_SESSION['userauth']['username'].', '._USERSLANG_YOULOGOUT_;
            unset($_SESSION['userauth']);
            if(!$webutlercouple->checkadmin()) session_destroy();
        }
        
        $loginblock_autoheader = '<script>
            alert(\''.$webutler_userslogin->meldung.'\');';
            if(isset($_POST['userlogout']))
                $loginblock_autoheader.= 'window.location = \''.$_SERVER['REQUEST_URI'].'\';';
        $loginblock_autoheader.= '</script>';
        
        $webutlercouple->autoheaderdata[] = $loginblock_autoheader;
    }
    
    if(!isset($_SESSION['userauth'])) {
		$userscontent['requesturi'] = $_SERVER['REQUEST_URI'];
		$userscontent['headline'] = _USERSLANG_LOGIN_;
		$userscontent['username'] = _USERSLANG_FIELDUSERNAME_;
		$userscontent['userpass'] = _USERSLANG_FIELDPASSWORD_;
		$userscontent['userlogin'] = _USERSLANG_LOGGING_;
		
		ob_start();
		include $userspages_docroot.'/tpls/block_dologin.tpl';
		$loginblock = ob_get_contents();
		ob_end_clean();
		
		echo $loginblock;
	}
	else {
		$userscontent['requesturi'] = $_SERVER['REQUEST_URI'];
		$userscontent['headline'] = _USERSLANG_LOGGEDAS_;
		$userscontent['username'] = $_SESSION['userauth']['username'];
		$userscontent['userlogout'] = _USERSLANG_LOGGING_;
		
		ob_start();
		include $userspages_docroot.'/tpls/block_islogged.tpl';
		$userislogged = ob_get_contents();
		ob_end_clean();
		
		echo $userislogged;
	}
}


