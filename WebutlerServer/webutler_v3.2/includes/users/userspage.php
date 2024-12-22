<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


if(preg_match('#/includes/users/userspage.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

if(!isset($userspages_docroot)) {
    $userspages_docroot = $webutlercouple->config['server_path'].'/includes/users';
    if(isset($_SESSION['language']) && file_exists($userspages_docroot.'/lang/'.$_SESSION['language'].'.php'))
        require_once $userspages_docroot.'/lang/'.$_SESSION['language'].'.php';
    else
        require_once $userspages_docroot.'/lang/'.$webutlercouple->config['defaultlang'].'.php';
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
        require_once $webutlercouple->config['server_path'].'/includes/users/user_class.php';
    if(!isset($webutler_userreg))
        require_once $webutlercouple->config['server_path'].'/content/access/regconf.php';
    if(!class_exists('PHPMailerLite'))
        require_once $webutlercouple->config['server_path'].'/includes/modexts/phpmailer/mailer.php';
    
	if(file_exists($webutlercouple->config['server_path'].'/includes/users/css/userspage.css'))
		$webutlercouple->autoheaderdata[] = '<link href="includes/users/css/userspage.css?t='.filemtime($webutlercouple->config['server_path'].'/includes/users/css/userspage.css').'" rel="stylesheet" type="text/css" />';
    
    if($webutler_userreg['newreg_groupid'] == '') {
		$userscontent['nousergroup'] = _USERSLANG_REGNOTCONF_;
		
		ob_start();
		include $userspages_docroot.'/tpls/nousergroup.tpl';
		$nousergroup = ob_get_contents();
		ob_end_clean();
		
		echo $nousergroup;
    }
    else {
        $webutler_userspage = new UsersClass;
        $webutler_userspage->dbpath = $webutlercouple->config['server_path'].'/content/access';
        $webutler_userspage->userregvars = $webutler_userreg['regfields'];
		unset($webutler_userreg['regfields']);
        $webutler_userspage->config = $webutler_userreg;
        $webutler_userspage->saltkeys[0] = $webutlercouple->config['salt_key1'];
        $webutler_userspage->saltkeys[1] = $webutlercouple->config['salt_key2'];
		unset($webutler_userreg);
        $webutler_userspage->getpage = $webutlercouple->getpage;

        $userspage_mailtemplate = $webutlercouple->config['server_path'].'/includes/users/sendmail.php';

        if(isset($_GET['code']) || isset($_GET['newpass'])) {
            if(isset($_GET['code']))
                $userspage_code = $webutler_userspage->userregvalidate($_GET['code'], _USERSLANG_ACTIVCODE_, 5);
            elseif(isset($_GET['newpass']))
                $userspage_code = $webutler_userspage->userregvalidate($_GET['newpass'], _USERSLANG_ACCOUNTCODE_, 5);
            if($webutler_userspage->meldung == '') {
                $webutler_userspage->aktivierung($userspage_code);
                if($webutler_userspage->mailtoadmin == 1 && $webutler_userspage->config['mailtoadmin'] == 1 && $webutler_userspage->config['admin_email'] != '') {
                    $wbuser_text[] = htmlentities(_USERSLANG_NEWUSER_);
                    $wbuser_text[] = htmlentities(_USERSLANG_FIELDUSERNAME_).': '.$webutler_userspage->mailtext['uname'];
                    $wbuser_subject = _USERSLANG_NEWREG_.' '.$_SERVER['HTTP_HOST'];
                    $wbuser_mailfrom = $webutler_userspage->mailtext['umail'];
                    $wbuser_mailto = $webutler_userspage->config['admin_email'];
                    require_once $userspage_mailtemplate;
                }
                elseif($webutler_userspage->mailtouser == 1) {
                    $wbuser_text[] = htmlentities(_USERSLANG_HELLO_).' '.$webutler_userspage->mailtext['uname'];
					$wbuser_text[] = htmlentities(sprintf(_USERSLANG_PASSCHANGED_, $_SERVER['HTTP_HOST']));
					$wbuser_text[]['highlight'] = htmlentities(_USERSLANG_PASSCHANGEDNEW_).': '.$webutler_userspage->mailtext['upass'];
					$wbuser_text[] = htmlentities(_USERSLANG_GREET_);
					$wbuser_text[] = htmlentities(_USERSLANG_YOURTEAM_).' '.$_SERVER['HTTP_HOST'];
                    $wbuser_subject = _USERSLANG_NEWPASS_.' '.$_SERVER['HTTP_HOST'];
                    $wbuser_mailfrom = $webutler_userspage->config['admin_email'];
                    $wbuser_mailto = $webutler_userspage->mailtext['umail'];
                    require_once $userspage_mailtemplate;
                }
            }
        }
        elseif(isset($_GET['logout']) && $webutler_userspage->config['login_link'] == '1') {
            $userspage_logged = _USERSLANG_GOODBY_.' '.$_SESSION['userauth']['username'].', '._USERSLANG_YOULOGOUT_;
            unset($_SESSION['userauth']);
            if(!$webutlercouple->checkadmin()) session_destroy();
            unset($_GET['logout']);
        }
        elseif(isset($_POST) && count($_POST) > 0 && (isset($_POST['neuedaten']) || isset($_POST['sendnewpass']) || isset($_POST['registrieren']) || isset($_POST['userslogin']))) {
            $webutler_userspage->postdata = $_POST;
            if(isset($_POST['userslogin'])) {
				if(!$webutlercouple->loginattempts()) {
					$webutler_userspage->meldung = str_replace('__TIME__', $webutlercouple->config['logattemptmin'], _USERSLANG_LOGATTEMPTS_);
				}
				else {
					$webutler_userspage->login();
				}
				if($webutler_userspage->meldung == '') {
					$_SESSION['userauth'] = $webutler_userspage->userauth;
					$userspage_logged = _USERSLANG_WELCOME_.' '.$_SESSION['userauth']['username'].', '._USERSLANG_YOULOGIN_;
					unset($_GET['userpage']);
				}
            }
            if(isset($_POST['registrieren'])) {
                $webutler_userspage->registrierung();
                if($webutler_userspage->mailtoadmin == 1 && $webutler_userspage->config['mailtoadmin'] == 1 && $webutler_userspage->config['admin_email'] != '') {
					$wbuser_text[] = htmlentities(_USERSLANG_NEWUSER_);
                    $wbuser_text[] = htmlentities(_USERSLANG_FIELDUSERNAME_).': '.$webutler_userspage->mailtext['uname'];
                    $wbuser_subject = _USERSLANG_NEWREG_.' '.$_SERVER['HTTP_HOST'];
                    $wbuser_mailfrom = $webutler_userspage->mailtext['umail'];
                    $wbuser_mailto = $webutler_userspage->config['admin_email'];
                    require_once $userspage_mailtemplate;
                    unset($_POST);
                }
                elseif($webutler_userspage->mailtouser == 1) {
                    $wbuser_text[] = htmlentities(_USERSLANG_HELLO_).' '.$webutler_userspage->mailtext['uname'];
					$wbuser_text[] = htmlentities(sprintf(_USERSLANG_CONFIRMREG_, $_SERVER['HTTP_HOST']));
					if($webutlercouple->config['modrewrite'] == "1")
						$wbuser_text[]['highlight'] = '<a href="'.$webutlercouple->config['homepage'].'/'.$webutler_userspage->getpage.'-code-'.$webutler_userspage->mailtext['code'].$webutlercouple->config['urlendung'].'">'.htmlentities(_USERSLANG_CONFIRMREGLINK_).'</a>';
					else
						$wbuser_text[]['highlight'] = '<a href="'.$webutlercouple->config['homepage'].'/index.php?page='.$webutler_userspage->getpage.'&code='.$webutler_userspage->mailtext['code'].'">'.htmlentities(_USERSLANG_CONFIRMREGLINK_).'</a>';
					$wbuser_text[] = htmlentities(_USERSLANG_GREET_);
					$wbuser_text[] = htmlentities(_USERSLANG_YOURTEAM_).' '.$_SERVER['HTTP_HOST'];
                    $wbuser_subject = _USERSLANG_YOURREG_.' '.$_SERVER['HTTP_HOST'];
                    $wbuser_mailfrom = $webutler_userspage->config['admin_email'];
                    $wbuser_mailto = $webutler_userspage->mailtext['umail'];
                    require_once $userspage_mailtemplate;
                    unset($_POST);
                }
            }
            if(isset($_POST['sendnewpass'])) {
                $webutler_userspage->neuespasswort();
                if($webutler_userspage->mailtouser == 1) {
                    $wbuser_text[] = htmlentities(_USERSLANG_HELLO_).' '.$webutler_userspage->mailtext['uname'];
					$wbuser_text[] = htmlentities(sprintf(_USERSLANG_PASSORDER_, $_SERVER['HTTP_HOST']));
					if($webutlercouple->config['modrewrite'] == "1")
						$wbuser_text[]['highlight'] = '<a href="'.$webutlercouple->config['homepage'].'/'.$webutler_userspage->getpage.'-newpass-'.$webutler_userspage->mailtext['code'].$webutlercouple->config['urlendung'].'">'.htmlentities(_USERSLANG_PASSORDERLINK_).'</a>';
					else
						$wbuser_text[]['highlight'] = '<a href="'.$webutlercouple->config['homepage'].'/index.php?page='.$webutler_userspage->getpage.'&newpass='.$webutler_userspage->mailtext['code'].'">'.htmlentities(_USERSLANG_PASSORDERLINK_).'</a>';
					$wbuser_text[] = htmlentities(_USERSLANG_GREET_);
					$wbuser_text[] = htmlentities(_USERSLANG_YOURTEAM_).' '.$_SERVER['HTTP_HOST'];
                    $wbuser_subject = _USERSLANG_NEWPASSORDER_.' '.$_SERVER['HTTP_HOST'];
                    $wbuser_mailfrom = $webutler_userspage->config['admin_email'];
                    $wbuser_mailto = $webutler_userspage->mailtext['umail'];
                    require_once $userspage_mailtemplate;
                }
            }
            if(isset($_POST['neuedaten'])) {
                $webutler_userspage->speicherneuedaten();
                if($webutler_userspage->meldung == '') {
                    unset($_SESSION['userauth']);
                    $_SESSION['userauth'] = $webutler_userspage->userauth;
                    $webutler_userspage->meldung = _USERSLANG_MODISAVE_;
					unset($_GET['userpage']);
                }
            }
        }
        
        if(isset($userspage_logged))
			$userscontent['islogged'] = $userspage_logged;
        
		$userspages_alert = '';
        if($webutler_userspage->meldung != '') {
			$userscontent['alert'] = $webutler_userspage->meldung;
			
			ob_start();
			include $userspages_docroot.'/tpls/alert.tpl';
			$userspages_alert = ob_get_contents();
			ob_end_clean();
		}
        
        if(!isset($_SESSION['userauth'])) {
			
			if($webutler_userspage->config['reg_active'] == '1' && $webutler_userspage->config['newreg_groupid'] != '')
				$userscontent['links'][] = '<a href="index.php?page='.$webutler_userspage->getpage.'&userpage=newreg">'._USERSLANG_REGISTRATION_.'</a>';
            
			if($webutler_userspage->config['login_link'] == '1')
				$userscontent['links'][] = '<a href="index.php?page='.$webutler_userspage->getpage.'&userpage=login">'._USERSLANG_LOGIN_.'</a>';
			
			$userscontent['links'][] = '<a href="index.php?page='.$webutler_userspage->getpage.'&userpage=newpass">'._USERSLANG_PASSFORGET_.'</a>';
            
            if(!isset($_GET['userpage']))
            {
				$userscontent['page'] = $userspages_alert;
            }
            elseif(isset($_GET['userpage']) && $_GET['userpage'] == 'newreg' && $webutler_userspage->config['reg_active'] == '1')
            {
                $userpage_newreg = array();
                $userpage_newreg['headline'] = _USERSLANG_REGISTRATION_;
                $userpage_newreg['alert'] = $userspages_alert;
                $userpage_newreg['regtext'] = _USERSLANG_REGIST_;
                if($webutler_userspage->config['activator_mail'] == 1) {
                    $userpage_newreg['regtext'] .= ' '._USERSLANG_REGISTLINK_;
                }
                if($webutler_userspage->config['newreg_adm_auto'] == 1) {
                    $userpage_newreg['regtext'] .= ' '._USERSLANG_REGISTADMIN_;
                }
				
				$userpage_newreg['requesturi'] = $_SERVER['REQUEST_URI'];
				$userpage_newreg['username'] = _USERSLANG_FIELDUSERNAME_;
				$userpage_newreg['password'] = _USERSLANG_FIELDPASSWORD_;
				$userpage_newreg['mailaddress'] = _USERSLANG_FIELDMAILADDRESS_;
				$userpage_newreg['register'] = _USERSLANG_REGISTER_;
				
				$userpage_newreg['post']['uname'] = isset($_POST['uname']) ? htmlentities($_POST['uname']) : '';
				$userpage_newreg['post']['umail'] = isset($_POST['umail']) ? htmlentities($_POST['umail']) : '';
				
				$userpage_regfields = $webutler_userspage->userregfields();
				if(is_array($userpage_regfields)) {
					foreach($userpage_regfields as $name => $value) {
						$userpage_newreg['regfields'][$name] = $value;
						$userpage_newreg['post'][$name] = isset($_POST[$name]) ? htmlentities($_POST[$name]) : '';
					}
				}
				
				ob_start();
				include $userspages_docroot.'/tpls/users_newreg.tpl';
				$userscontent['page'] = ob_get_contents();
				ob_end_clean();
            }
            elseif(isset($_GET['userpage']) && $_GET['userpage'] == 'login' && $webutler_userspage->config['login_link'] == '1')
            {
                $userpage_login = array();
                $userpage_login['headline'] = _USERSLANG_LOGIN_;
                $userpage_login['alert'] = $userspages_alert;
                $userpage_login['logtext'] = _USERSLANG_YOURDATAPLEASE_;
				
				$userpage_login['requesturi'] = $_SERVER['REQUEST_URI'];
				$userpage_login['username'] = _USERSLANG_FIELDUSERNAME_;
				$userpage_login['password'] = _USERSLANG_FIELDPASSWORD_;
				$userpage_login['logging'] = _USERSLANG_LOGGING_;
				
				$userpage_login['post']['username'] = isset($_POST['username']) ? htmlentities($_POST['username']) : '';
				
				ob_start();
				include $userspages_docroot.'/tpls/users_login.tpl';
				$userscontent['page'] = ob_get_contents();
				ob_end_clean();
            }
            elseif(isset($_GET['userpage']) && $_GET['userpage'] == 'newpass')
            {
                $userpage_newpass = array();
                $userpage_newpass['headline'] = _USERSLANG_REQUIRENEWPASS_;
                $userpage_newpass['alert'] = $userspages_alert;
                $userpage_newpass['passtext'] = _USERSLANG_SENDNEWPASS_;
				$userpage_newpass['requesturi'] = $_SERVER['REQUEST_URI'];
				$userpage_newpass['username'] = _USERSLANG_FIELDUSERNAME_;
				$userpage_newpass['mailaddress'] = _USERSLANG_FIELDMAILADDRESS_;
				$userpage_newpass['requestpass'] = _USERSLANG_REQUESTPASS_;
				
				$userpage_newpass['post']['username'] = isset($_POST['username']) ? htmlentities($_POST['username']) : '';
				$userpage_newpass['post']['usermail'] = isset($_POST['usermail']) ? htmlentities($_POST['usermail']) : '';
				
				ob_start();
				include $userspages_docroot.'/tpls/users_newpass.tpl';
				$userscontent['page'] = ob_get_contents();
				ob_end_clean();
            }
        }
        elseif($_SESSION['userauth'])
        {
            $userscontent['links'][] = '<a href="index.php?page='.$webutler_userspage->getpage.'">'._USERSLANG_USERACCOUNT_.'</a>';
			
			$userscontent['links'][] = '<a href="index.php?page='.$webutler_userspage->getpage.'&userpage=edit">'._USERSLANG_EDITDATA_.'</a>';
            
            if($webutler_userspage->config['login_link'] == '1')
                $userscontent['links'][] = '<a href="index.php?page='.$webutler_userspage->getpage.'&logout=1">'._USERSLANG_LOGOUT_.'</a>';
            
            if(!isset($_GET['userpage']))
			{
                $userspage_normfields = array();
                $userspage_normfields['uname'] = _USERSLANG_FIELDUSERNAME_;
                $userspage_normfields['umail'] = _USERSLANG_FIELDMAILADDRESS_;

                $userspage_regfields = $webutler_userspage->userregfields();
                $userpage_fields = is_array($userspage_regfields) ? array_merge($userspage_normfields, $userspage_regfields) : $userspage_normfields;
                
                $userpage_account = array();
                $userpage_account['headline'] = _USERSLANG_USERACCOUNT_;
                $userpage_account['alert'] = $userspages_alert;
                $userpage_accountshow = array();
                $userpage_accountshow['text'] = _USERSLANG_EDITDATAPLEASE_;
				
                $webutler_userspage->benutzerdaten();
                $userpage_userdata = $webutler_userspage->userdata;
				
				$count = 0;
                foreach($userpage_fields as $name => $value) {
					foreach($userpage_userdata as $field => $datas) {
						if($field == $name) {
							$data = $datas;
							continue;
						}
					}
					
					$userpage_accountshow['fields'][$count]['value'] = $value;
					$userpage_accountshow['fields'][$count]['data'] = $data;
					
					$count++;
                }
                
				ob_start();
				include $userspages_docroot.'/tpls/users_accountshow.tpl';
				$userpage_account['account'] = ob_get_contents();
				ob_end_clean();
            }
			elseif(isset($_GET['userpage']) && $_GET['userpage'] == 'edit')
			{
                $userspage_normfields = array();
                $userspage_normfields['uname'] = _USERSLANG_FIELDUSERNAME_;
                $userspage_normfields['upass'] = _USERSLANG_FIELDPASSWORD_;
                $userspage_normfields['umail'] = _USERSLANG_FIELDMAILADDRESS_;

                $userspage_regfields = $webutler_userspage->userregfields();
                $userpage_fields = is_array($userspage_regfields) ? array_merge($userspage_normfields, $userspage_regfields) : $userspage_normfields;
                
                $userpage_account = array();
                $userpage_account['headline'] = _USERSLANG_USERACCOUNT_;
                $userpage_account['alert'] = $userspages_alert;
                $userpage_accountedit = array();
                $userpage_accountedit['text'] = _USERSLANG_INSERTNEWDATA_;
                $userpage_accountedit['send'] = _USERSLANG_SAVEDATA_;
                $userpage_accountedit['formurl'] = 'index.php?page='.$webutler_userspage->getpage.'&userpage=edit';
				
                $webutler_userspage->benutzerdaten();
                $userpage_userdata = $webutler_userspage->userdata;
				
				$count = 0;
                foreach($userpage_fields as $name => $value) {
                    foreach($userpage_userdata as $field => $datas) {
                        if($field == $name) {
                            $data = $datas;
                            continue;
                        }
                    }
					
                    $userpage_accountedit['fields'][$count]['name'] = $name;
                    $userpage_accountedit['fields'][$count]['value'] = $value;
                    $userpage_accountedit['fields'][$count]['data'] = $name == 'upass' ? '' : $data;
					
					$count++;
                }
                
				ob_start();
				include $userspages_docroot.'/tpls/users_accountedit.tpl';
				$userpage_account['account'] = ob_get_contents();
				ob_end_clean();
            }
			
			ob_start();
			include $userspages_docroot.'/tpls/users_account.tpl';
			$userscontent['page'] = ob_get_contents();
			ob_end_clean();
        }
    }
	
	ob_start();
	include $userspages_docroot.'/tpls/users_page.tpl';
	$userspage = ob_get_contents();
	ob_end_clean();
	
	echo $userspage;
}



