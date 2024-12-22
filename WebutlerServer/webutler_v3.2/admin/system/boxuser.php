<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(__FILE__))).'/includes/loader.php';

$webutlerboxlayer = new WebutlerAdminClass;
$webutlerboxlayer->config = $webutler_config;
//$webutlerboxlayer->htmlsource = $webutler_htmlsource;
$webutlerboxlayer->offlinepages = $webutler_offlinepages;
$webutlerboxlayer->langconf = $webutler_langconf;

$webutlerboxlayer->verifygetpage();

if(!$webutlerboxlayer->checkadmin())
    exit('no access');

if(!class_exists('SQLite3')) {
    echo 'no SQLite3';
	return false;
}


require_once $webutlerboxlayer->config['server_path'].'/admin/system/lang/'.$_SESSION['loggedin']['userlang'].'.php';


// Installation

$dbpath = $webutlerboxlayer->config['server_path'].'/content/access';
$userconfig = '';
$color = '';
$startafterinstall = '';
$dberror = '';
$meldung = '';
$curpageisblocked = 0;

if(!file_exists($dbpath.'/regconf.php') || !file_exists($dbpath.'/users.db')) {
    //if(is_writeable($dbpath)) {
	if($webutlerboxlayer->iswriteable('/content/access') == '') {
        if(!file_exists($dbpath.'/regconf.php')) {
            if(isset($_POST['makeuserconfig']) && $_POST['makeuserconfig'] != '') {
            
                $configvars = array();
                if(isset($_POST['nachname'])) $configvars[] = 'nachname';
                if(isset($_POST['vorname'])) $configvars[] = 'vorname';
                if(isset($_POST['firma'])) $configvars[] = 'firma';
                if(isset($_POST['str_nr'])) $configvars[] = 'str_nr';
                if(isset($_POST['plz_ort'])) $configvars[] = 'plz_ort';
                if(isset($_POST['tel'])) $configvars[] = 'tel';
                
        		$configfile = $dbpath.'/regconf.php';
        		$saveconfig = fopen($configfile, "w+");
        		$config = "<?PHP\n\n";
        		$config.= "\$webutler_userreg['regfields'] = \"".implode(',', $configvars)."\";\n";
        		$config.= "\$webutler_userreg['reg_active'] = \"0\";\n";
        		$config.= "\$webutler_userreg['login_link'] = \"0\";\n";
        		$config.= "\$webutler_userreg['newreg_adm_auto'] = \"1\";\n";
        		$config.= "\$webutler_userreg['newreg_groupid'] = \"1\";\n";
        		$config.= "\$webutler_userreg['mailtoadmin'] = \"0\";\n";
        		$config.= "\$webutler_userreg['admin_email'] = \"\";\n";
        		$config.= "\$webutler_userreg['activator_mail'] = \"0\";\n";
        		$config.= "\$webutler_userreg['deluser_db'] = \"0\";\n\n";
        		$config.= "?>\n";
        		fwrite($saveconfig, $config);
        		fclose($saveconfig);
				$oldumask = umask(0);
				chmod($configfile, $webutlerboxlayer->config['chmod'][1]);
				umask($oldumask);
        		
                $userconfig = _WBLANGADMIN_WIN_ACCESS_INSTALL_REGFIELDS_;
                $color = 'green';
                $userconfigtext = '<div style="text-align: center; margin-top: 20px"><input type="button" onclick="WBeditbox_open(\'webutler_access\', \'showtr=blocks\')" class="webutler_button" value="'._WBLANGADMIN_WIN_BUTTONS_NEXT_.'" /></div>';
            }
            else {
                $userconfig = _WBLANGADMIN_WIN_ACCESS_INSTALL_HEADLINE_;
                $color = 'red';
                $userconfigtext = _WBLANGADMIN_WIN_ACCESS_INSTALL_TXT_;
            }
        }
        else {
            if(!file_exists($dbpath.'/users.db')) {
                /*
                if($userdb = new SQLiteDatabase($dbpath.'/users.db', 0777, $sqlerror)
                  or die(_WBLANGADMIN_WIN_ACCESS_INSTALL_ERROR_.": ".$sqlerror)) {
                */
                if($userdb = new SQLite3($dbpath.'/users.db')) {
                    $userdb->query("BEGIN");
                    $userdb->query("CREATE TABLE blocks (id INTEGER PRIMARY KEY, pages TEXT)");
                    $userdb->query("CREATE TABLE groups (id INTEGER PRIMARY KEY, name TEXT, pages TEXT)");
                    $userdb->query("CREATE TABLE reloads (id INTEGER PRIMARY KEY, userid INTEGER, code TEXT, what TEXT, date TEXT)");
                    $userdb->query("CREATE TABLE users (id INTEGER PRIMARY KEY, uname TEXT, umail TEXT, upass TEXT, nachname TEXT, vorname TEXT, firma TEXT, str_nr TEXT, plz_ort TEXT, tel TEXT, status TEXT, groupid TEXT)");
                    $userdb->query("INSERT INTO blocks (pages) VALUES ('')");
                    $userdb->query("INSERT INTO groups (name) VALUES ('".$userdb->escapeString(_WBLANGADMIN_WIN_ACCESS_INSTALL_DEFAULTGROUP_)."')");
                    if($userdb->query("COMMIT")) {
                        $meldung = _WBLANGADMIN_WIN_ACCESS_INSTALL_DBOK_;
                        $color = 'green';
                        $startafterinstall = '<input type="button" onclick="WBeditbox_open(\'webutler_access\', \'showtr=usersets\')" class="webutler_button" value="'._WBLANGADMIN_WIN_ACCESS_INSTALL_COMPLETE_.'" />';
                    }
                    else {
                        $dberror = _WBLANGADMIN_WIN_ACCESS_INSTALL_DBERROR_;
                        $color = 'red';
                    }
                }
                else {
                    $dberror = _WBLANGADMIN_WIN_ACCESS_INSTALL_DBERROR_;
                    $color = 'red';
                }
            }
        }
    }
}
else {

// Administration

    require_once $dbpath.'/regconf.php';
    if(isset($_POST['sendmail'])) {
		$incuserlang = (isset($_POST['userlang']) && $_POST['userlang'] != '') ? $_POST['userlang'] : $webutlerboxlayer->config['defaultlang'];
	}
	else {
		$incuserlang = $_SESSION['loggedin']['userlang'];
	}
	require_once $webutlerboxlayer->config['server_path'].'/includes/users/lang/'.$incuserlang.'.php';
    require_once $webutlerboxlayer->config['server_path'].'/includes/users/user_class.php';
    require_once $webutlerboxlayer->config['server_path'].'/includes/modexts/phpmailer/mailer.php';
    
    $webutleruserclass = new UsersClass();
    $webutleruserclass->dbpath = $dbpath;
    $webutleruserclass->config = $webutler_userreg;
    $webutleruserclass->userregvars = $webutleruserclass->config['regfields'];
    $webutleruserclass->connectdb();
    
    if(count($_POST) > 0)
    {
        if(isset($_POST['update_config_file']))
        {
            if(isset($_POST['config_regon'])) {
                $config_regon = 1;
                $config_loginlink = isset($_POST['config_loginlink']) ? 1 : 0;
                $config_regby = ($_POST['config_regby'] == 1) ? 1 : 2;
                $config_reggroupid = ($_POST['config_reggroupid'] != '') ? $webutleruserclass->userregvalidate($_POST['config_reggroupid'], _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_, 1) : '';
                $config_regtoadmin = (isset($_POST['config_regtoadmin']) && $_POST['config_regadminmail'] != '') ? 1 : 0;
                $config_regadminmail = ($_POST['config_regadminmail'] != '') ? $webutleruserclass->userregvalidate($_POST['config_regadminmail'], _WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_, 3) : '';
                $config_regtouser = isset($_POST['config_regtouser']) ? 1 : 0;
                $config_deluserdb = isset($_POST['config_deluserdb']) ? 1 : 0;
            }
            else {
                $config_regon = 0;
                $config_loginlink = isset($_POST['config_loginlink']) ? 1 : 0;
                $config_regby = $webutleruserclass->config['newreg_adm_auto'];
                $config_reggroupid = $webutleruserclass->config['newreg_groupid'];
                $config_regtoadmin = $webutleruserclass->config['mailtoadmin'];
                $config_regadminmail = ($_POST['config_regadminmail'] != '') ? $webutleruserclass->userregvalidate($_POST['config_regadminmail'], _WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_, 3) : '';
                $config_regtouser = $webutleruserclass->config['activator_mail'];
                $config_deluserdb = isset($_POST['config_deluserdb']) ? 1 : 0;
            }
            
            if(!($fp = @fopen($dbpath.'/regconf.php', 'r'))) {
                $meldung = _WBLANGADMIN_WIN_ACCESS_CONFOPENFILE_;
                $color = 'red';
    		} 
    		else {
                $buf = file_get_contents($dbpath.'/regconf.php');
                $buf = preg_replace('#(\$webutler_userreg\[\'reg_active\'\] = ")([^"]*)(";)#Usi', '${1}'.$config_regon.'$3', $buf);
                $buf = preg_replace('#(\$webutler_userreg\[\'login_link\'\] = ")([^"]*)(";)#Usi', '${1}'.$config_loginlink.'$3', $buf);
                $buf = preg_replace('#(\$webutler_userreg\[\'newreg_adm_auto\'\] = ")([^"]*)(";)#Usi', '${1}'.$config_regby.'$3', $buf);
                $buf = preg_replace('#(\$webutler_userreg\[\'newreg_groupid\'\] = ")([^"]*)(";)#Usi', '${1}'.$config_reggroupid.'$3', $buf);
                $buf = preg_replace('#(\$webutler_userreg\[\'mailtoadmin\'\] = ")([^"]*)(";)#Usi', '${1}'.$config_regtoadmin.'$3', $buf);
                $buf = preg_replace('#(\$webutler_userreg\[\'admin_email\'\] = ")([^"]*)(";)#Usi', '${1}'.$config_regadminmail.'$3', $buf);
                $buf = preg_replace('#(\$webutler_userreg\[\'activator_mail\'\] = ")([^"]*)(";)#Usi', '${1}'.$config_regtouser.'$3', $buf);
                $buf = preg_replace('#(\$webutler_userreg\[\'deluser_db\'\] = ")([^"]*)(";)#Usi', '${1}'.$config_deluserdb.'$3', $buf);

    			if (!($fp = @fopen($dbpath.'/regconf.php', 'w'))) {
                    $meldung = _WBLANGADMIN_WIN_ACCESS_CONFNOTSAVED_;
                    $color = 'red';
    			} 
    			else {
    				fwrite($fp, $buf);
    				fclose($fp);
					
                    $webutleruserclass->config['reg_active'] = $config_regon;
                    $webutleruserclass->config['login_link'] = $config_loginlink;
                    $webutleruserclass->config['newreg_adm_auto'] = $config_regby;
                    $webutleruserclass->config['newreg_groupid'] = $config_reggroupid;
                    $webutleruserclass->config['mailtoadmin'] = $config_regtoadmin;
                    $webutleruserclass->config['admin_email'] = $config_regadminmail;
                    $webutleruserclass->config['activator_mail'] = $config_regtouser;
                    $webutleruserclass->config['deluser_db'] = $config_deluserdb;
                    
                    $meldung = _WBLANGADMIN_WIN_ACCESS_CONFSAVED_;
                    $color = 'green';
    			}
    		}
        }
        
        if(isset($_POST['update_blocked_pages']) && isset($_POST['blocked_pages'])) {
            $todo = $webutleruserclass->userdb->query("UPDATE blocks SET pages = '".$webutleruserclass->userdb->escapeString($_POST['blocked_pages'])."' WHERE blocks.id = '1'");
            if(!$todo) { 
                $meldung = _WBLANGADMIN_WIN_ACCESS_BLOCKEDNOTSAVED_;
                $color = 'red';
            }
            else {
                if(isset($_POST['locationpage']) && in_array($_POST['locationpage'], explode(',', $_POST['blocked_pages'])))
                    $curpageisblocked = 1;
                $meldung = _WBLANGADMIN_WIN_ACCESS_BLOCKEDSAVED_;
                $color = 'green';
            }
        }
        
        if(isset($_POST['make_new_group'])) {
            $post_new_group = isset($_POST['new_group']) ? $webutleruserclass->userregvalidate($_POST['new_group'], _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPNAME_, 4) : '';
            if($webutleruserclass->meldung == '' && $post_new_group != '') {
				$checks = $webutleruserclass->userdb->query("SELECT id, name FROM groups WHERE lower(groups.name) = lower('".$webutleruserclass->userdb->escapeString($post_new_group)."') ORDER BY id");
				
                if($webutleruserclass->usernumrows($checks) > 0) {
                    $meldung = _WBLANGADMIN_WIN_ACCESS_GROUPEXISTS_;
                    $color = 'red';
                }
                else {
                    $todo = $webutleruserclass->userdb->query("INSERT INTO groups (name) VALUES ('".$webutleruserclass->userdb->escapeString($post_new_group)."')");
                    if(!$todo) {
                        $meldung = _WBLANGADMIN_WIN_ACCESS_GROUPNOTSAVED_;
                        $color = 'red';
                    }
                    else {
                        $meldung = _WBLANGADMIN_WIN_ACCESS_GROUPSAVED_;
                        $color = 'green';
                    }
                }
            }
        }
        
        if(isset($_POST['make_update_group'])) {
            $post_update_group = isset($_POST['update_group']) ? $webutleruserclass->userregvalidate($_POST['update_group'], _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPNAME_, 4) : '';
            $post_update_groupid = isset($_POST['update_groupid']) ? $webutleruserclass->userregvalidate($_POST['update_groupid'], _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_, 1) : '';
            if($webutleruserclass->meldung == '' && $post_update_group != '' && $post_update_groupid != '') {
                $checks = $webutleruserclass->userdb->query("SELECT id, name FROM groups WHERE lower(groups.name) = lower('".$webutleruserclass->userdb->escapeString($post_update_group)."') AND groups.id != '".$webutleruserclass->userdb->escapeString($post_update_groupid)."' ORDER BY name");

		        if($webutleruserclass->usernumrows($checks) > 0) {
                    $meldung = _WBLANGADMIN_WIN_ACCESS_GROUPEXISTS_;
                    $color = 'red';
                }
                else {
                    $todo = $webutleruserclass->userdb->query("UPDATE groups SET name = '".$webutleruserclass->userdb->escapeString($post_update_group)."', pages = '".$webutleruserclass->userdb->escapeString($_POST['free_pages'])."' WHERE groups.id = '".$webutleruserclass->userdb->escapeString($post_update_groupid)."'");
                    if(!$todo) { 
                        $meldung = _WBLANGADMIN_WIN_ACCESS_GROUPSETSNOTSAVED_;
                        $color = 'red';
                    }
                    else {
                        $meldung = _WBLANGADMIN_WIN_ACCESS_GROUPSETSSAVED_;
                        $color = 'green';
                    }
                }
            }
        }
        
        if(isset($_POST['make_new_user'])) {
            if(!isset($_POST['upass']) || $_POST['upass'] == '') {
                $meldung = _WBLANGADMIN_WIN_ACCESS_USERNOPASS_;
                $color = 'red';
            }
            else {
                $post_uname = isset($_POST['uname']) ? $webutleruserclass->userregvalidate($_POST['uname'], _WBLANGADMIN_WIN_ACCESS_FIELD_USERNAME_, 2) : '';
                $post_umail = isset($_POST['umail']) ? $webutleruserclass->userregvalidate($_POST['umail'], _WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_, 3) : '';
                if(isset($_POST['ugroup']) && $_POST['ugroup'] != '') {
                    $post_ugroups = array();
    				foreach($_POST['ugroup'] as $ugroup) {
    					$post_ugroups[] = $webutleruserclass->userregvalidate($ugroup, _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_, 1);
    				}
                    $post_ugroup = implode('|', $post_ugroups);
                }
                else {
                    $post_ugroup = $webutleruserclass->config['newreg_groupid'];
                }
                if($_POST['status'] == 'enabled') $post_status = 'enabled';
                elseif($_POST['status'] == 'disabled') $post_status = 'disabled';
                if($webutleruserclass->meldung == '') {
                    $webutleruserclass->check_userexists($post_uname);
                    $webutleruserclass->check_mailexists($post_umail);
                }
                if($webutleruserclass->meldung == '') {
                    $fieldnames = $webutleruserclass->config['regfields'] != '' ? $webutleruserclass->config['regfields'].',' : '';
                    $dbvalues = '';
                    $userregfields = $webutleruserclass->userregfields();
                    if(is_array($userregfields)) {
                        foreach($userregfields as $name => $value) {
                            $_POST[$name] = $webutleruserclass->userregvalidate($_POST[$name], $value, 4);
                            $dbvalues.= "'".$webutleruserclass->userdb->escapeString($_POST[$name])."',";
                        }
                    }
					$post_upass = $webutleruserclass->userregvalidate($_POST['upass'], _USERSLANG_FIELDPASSWORD_, 6);
                    if($webutleruserclass->meldung == '') {
                        $dbvalues.= "'".$webutleruserclass->userdb->escapeString($post_uname)."',";
                        $dbvalues.= "'".$webutleruserclass->userdb->escapeString($post_umail)."',";
                        $dbvalues.= "'".$webutleruserclass->userdb->escapeString(md5($webutlerboxlayer->config['salt_key1'].$post_upass.$webutlerboxlayer->config['salt_key2']))."',";
                        $dbvalues.= "'".$webutleruserclass->userdb->escapeString($post_status)."',";
                        $dbvalues.= "'".$webutleruserclass->userdb->escapeString($post_ugroup)."'";
                        
                        $todo = $webutleruserclass->userdb->query("INSERT INTO users (".$fieldnames."uname,umail,upass,status,groupid) VALUES (".$dbvalues.")");
                        if(!$todo) { 
                            $meldung = _WBLANGADMIN_WIN_ACCESS_USERNOTSAVED_;
                            $color = 'red';
                        }
                        else {
                            if(isset($_POST['sendmail'])) {
                         
                                //Logindaten an Benutzer
                                
								/*
                                $userlang = (isset($_POST['userlang']) && $_POST['userlang'] != '') ? $_POST['userlang'] : $webutlerboxlayer->config['defaultlang'];
                                require_once $webutlerboxlayer->config['server_path'].'/includes/users/lang/'.$userlang.'.php';
								*/
                                
                                $wbuser_text[] = htmlentities(sprintf(_USERSLANG_NEWACCOUNTTXT_, $_SERVER['HTTP_HOST'])).':';
								$wbuser_text[] = '<strong>'.htmlentities(_USERSLANG_FIELDUSERNAME_).':</strong> '.$post_uname;
								$wbuser_text[] = '<strong>'.htmlentities(_USERSLANG_FIELDPASSWORD_).':</strong> '.$_POST['upass'];
								$wbuser_text[] = htmlentities(_USERSLANG_GREET_);
								$wbuser_text[] = htmlentities(_USERSLANG_YOURTEAM_).' '.$_SERVER['HTTP_HOST'];
                                $wbuser_subject = _USERSLANG_YOURACCOUNT_.' '.$_SERVER['HTTP_HOST'];
                                $wbuser_mailfrom = $webutleruserclass->config['admin_email'];
                                $wbuser_mailto = $post_umail;
                                
                                require_once $webutlerboxlayer->config['server_path'].'/includes/users/sendmail.php';
                            }
                            $meldung = _WBLANGADMIN_WIN_ACCESS_USERSAVED_;
                            $color = 'green';
                        }
                        //unset($_GET['subtr']);
                        unset($_POST);
                        $_POST['webutler_access'] = true;
                    }
                }
            }
        }
        
        if(isset($_POST['update_newregs_user'])) {
            $post_userid = $webutleruserclass->userregvalidate($_POST['userid'], _WBLANGADMIN_WIN_ACCESS_FIELD_USERID_, 1);
            if($webutleruserclass->meldung == '') {
                if(isset($_POST['deluser'])) {
                    $todo = $webutleruserclass->userdb->query("DELETE FROM users WHERE users.id = '".$webutleruserclass->userdb->escapeString($post_userid)."'");
                    if(!$todo) { 
                        $meldung = _WBLANGADMIN_WIN_ACCESS_USERNOTDELETED_;
                        $color = 'red';
                    }
                    else {
                        $meldung = _WBLANGADMIN_WIN_ACCESS_USERDELETED_;
                        $color = 'green';
                    }
                }
                else {
                    $dbvalues = '';
                    if(isset($_POST['groupid']) && $_POST['groupid'] != '') {
    	                $post_groupids = array();
    					foreach($_POST['groupid'] as $groupid) {
    						$post_groupids[] = $webutleruserclass->userregvalidate($groupid, _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_, 1);
    					}
    	                $post_groupid = implode('|', $post_groupids);
                    }
                    else {
                        $post_groupid = $webutleruserclass->config['newreg_groupid'];
                    }
                    if($_POST['status'] == 'enabled') $post_status = 'enabled';
                    elseif($_POST['status'] == 'disabled') $post_status = 'disabled';
                    $dbvalues.= "status = '".$webutleruserclass->userdb->escapeString($post_status)."', ";
                    $dbvalues.= "groupid = '".$webutleruserclass->userdb->escapeString($post_groupid)."'";
                
                    $todo = $webutleruserclass->userdb->query("UPDATE users SET ".$dbvalues." WHERE users.id = '".$webutleruserclass->userdb->escapeString($post_userid)."'");
                    if(!$todo) { 
                        $meldung = _WBLANGADMIN_WIN_ACCESS_USERNOTFREE_;
                        $color = 'red';
                    }
                    else {
                        if(isset($_POST['sendmail'])) {
    
                            //Profil freigeschaltet
                            
							/*
                            $userlang = (isset($_POST['userlang']) && $_POST['userlang'] != '') ? $_POST['userlang'] : $webutlerboxlayer->config['defaultlang'];
                            require_once $webutlerboxlayer->config['server_path'].'/includes/users/lang/'.$userlang.'.php';
							*/
                            
                            $wbuser_text[] = htmlentities(sprintf(_USERSLANG_REGCOMPLETE_, $_SERVER['HTTP_HOST']));
							$wbuser_text[] = htmlentities(_USERSLANG_GREET_);
							$wbuser_text[] = htmlentities(_USERSLANG_YOURTEAM_).' '.$_SERVER['HTTP_HOST'];
                            $wbuser_subject = _USERSLANG_YOURREG_.' '.$_SERVER['HTTP_HOST'];
                            $wbuser_mailfrom = $webutleruserclass->config['admin_email'];
                            $wbuser_mailto = $_POST['umail'];
                            
                            require_once $webutlerboxlayer->config['server_path'].'/includes/users/sendmail.php';
                        }
                        $meldung = _WBLANGADMIN_WIN_ACCESS_USERFREE_;
                        $color = 'green';
                    }
                }
            }
            unset($_POST);
            $_POST['webutler_access'] = true;
        }
        
        if(isset($_POST['make_update_user'])) {
            $post_userid = $webutleruserclass->userregvalidate($_POST['userid'], _WBLANGADMIN_WIN_ACCESS_FIELD_USERID_, 1);
            if($webutleruserclass->meldung == '') {
                if(isset($_POST['deluser'])) {
                    if($webutleruserclass->config['deluser_db'] == 1) {
                        $todo = $webutleruserclass->userdb->query("DELETE FROM users WHERE users.id = '".$webutleruserclass->userdb->escapeString($post_userid)."'");
                    }
                    else {
                        $todo = $webutleruserclass->userdb->query("UPDATE users SET status = 'deleted' WHERE users.id = '".$webutleruserclass->userdb->escapeString($post_userid)."'");
                    }
                    if(!$todo) { 
                        $meldung = _WBLANGADMIN_WIN_ACCESS_USERNOTDELETED_;
                        $color = 'red';
                        $noupdate = true;
                    }
                    else {
                        $webutleruserclass->userdb->query("DELETE FROM reloads WHERE reloads.userid = '".$webutleruserclass->userdb->escapeString($post_userid)."'");
                        $meldung = _WBLANGADMIN_WIN_ACCESS_USERDELETED_;
                        $color = 'green';
                    }
                }
                else {
                    $post_umail = $webutleruserclass->userregvalidate($_POST['umail'], _WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_, 3);
                    if(isset($_POST['groupid']) && $_POST['groupid'] != '') {
    	                $post_groupids = array();
    					foreach($_POST['groupid'] as $groupid) {
    						$post_groupids[] = $webutleruserclass->userregvalidate($groupid, _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_, 1);
    					}
    	                $post_groupid = implode('|', $post_groupids);
                    }
                    else {
                        $post_groupid = $webutleruserclass->config['newreg_groupid'];
                    }
                    if($_POST['status'] == 'enabled') $post_status = 'enabled';
                    elseif($_POST['status'] == 'disabled') $post_status = 'disabled';
                    
                    if($webutleruserclass->meldung == '') {
                        $webutleruserclass->check_mailexists($post_umail, $post_userid);
                        if($webutleruserclass->meldung == '') {
                            $dbvalues = '';
                            $userregfields = $webutleruserclass->userregfields();
                            if(is_array($userregfields)) {
                                foreach($userregfields as $name => $value) {
                                    $_POST[$name] = $webutleruserclass->userregvalidate($_POST[$name], $value, 4);
                                    $dbvalues.= $name." = '".$webutleruserclass->userdb->escapeString($_POST[$name])."', ";
                                }
                            }
							$post_upass = '';
							if(isset($_POST['upass']) && $_POST['upass'] != '') {
								$post_upass = $webutleruserclass->userregvalidate($_POST['upass'], _USERSLANG_FIELDPASSWORD_, 6);
							}
                            if($webutleruserclass->meldung == '') {
                                $dbvalues.= "umail = '".$webutleruserclass->userdb->escapeString($post_umail)."', ";
                                if($post_upass != '') {
                                    $dbvalues.= "upass = '".$webutleruserclass->userdb->escapeString(md5($webutlerboxlayer->config['salt_key1'].$post_upass.$webutlerboxlayer->config['salt_key2']))."', ";
                                }
                                $dbvalues.= "status = '".$webutleruserclass->userdb->escapeString($post_status)."', ";
                                $dbvalues.= "groupid = '".$webutleruserclass->userdb->escapeString($post_groupid)."'";
                            
                                $todo = $webutleruserclass->userdb->query("UPDATE users SET ".$dbvalues." WHERE users.id = '".$webutleruserclass->userdb->escapeString($post_userid)."'");
                                if(!$todo) { 
                                    $meldung = _WBLANGADMIN_WIN_ACCESS_USERNOTMODIFIED_;
                                    $color = 'red';
                                    $noupdate = true;
                                }
                                else {
                                    $meldung = _WBLANGADMIN_WIN_ACCESS_USERMODIFIED_;
                                    $color = 'green';
			                        //unset($_GET['subtr']);
                                }
    //echo 'meld: '.$meldung.'<br>';
                            }
                        }
                    }
                }
            }
            if($webutleruserclass->meldung != '') {
                $noupdate = true;
            }
            else {
                unset($_POST);
                $_POST['webutler_access'] = true;
            }
        }
    }

    $reggroups = $webutleruserclass->userdb->query("SELECT id, name FROM groups ORDER BY id");
	if($webutleruserclass->usernumrows($reggroups) > 0) {
        $autoregok = 1;
    }
}
if(class_exists('UsersClass')) {
    if($webutleruserclass->meldung != '') {
        $meldung = $webutleruserclass->meldung;
        $color = 'red';
    }
}
if($color == 'red') { $color = ' style="color: #D02705"'; }
if($color == 'green') { $color = ' style="color: #1A8E1A"'; }

$sendmailbylang = false;
if($webutlerboxlayer->config['languages'] == '1' && file_exists($webutlerboxlayer->config['server_path'].'/content/access/languages.php')) {
    $sendmailbylang = true;
}

if(isset($_POST['webutler_access'])) {
?>
    <script>
    /* <![CDATA[ */
    function WBeditbox_opencloseusermaillang(id) {
        if(document.getElementById(id)) {
            if(document.getElementById(id).style.display == 'none')
                document.getElementById(id).style.display = '';
            else
                document.getElementById(id).style.display = 'none';
        }
    }
    function WBeditbox_regelements() {
        if(document.getElementById('config_regon')) {
            var modus;
            
            //if(document.getElementById('config_regon').checked == false) modus = 'disabled';
            //else modus = '';
            if(document.getElementById('config_regon').checked == false) modus = true;
            else modus = false;
            
            document.getElementById('config_regby1').disabled = modus;
            document.getElementById('config_regby2').disabled = <?PHP echo (!isset($autoregok)) ? "'disabled'" : "modus"; ?>;
            document.getElementById('config_reggroupid').disabled = <?PHP echo (!isset($autoregok)) ? "'disabled'" : "modus"; ?>;
            document.getElementById('config_regtoadmin').disabled = modus;
            WBeditbox_enableforcer();
        }
        <?PHP if(class_exists('UsersClass') && $webutleruserclass->config['admin_email'] == '') { ?>
        if(document.getElementById('senddatenmail'))
            document.getElementById('senddatenmail').disabled = true;
        if(document.getElementById('sendprofilmail'))
            document.getElementById('sendprofilmail').disabled = true;
        <?PHP } ?>
    }
    function WBeditbox_subtrsonload() {
        var show = '<?PHP echo (isset($_GET['showtr']) ? $_GET['showtr'] : 'blocks'); ?>';
        var sub = '<?PHP echo (isset($_GET['subtr']) ? $_GET['subtr'] : 'newuser'); ?>';
		if(document.getElementById('webutler_tr' + show)) {
    		if(show == 'blocks')
                WBeditbox_hidesubtrs('webutler_trblocks','webutler_trgruppen|webutler_trzugang|webutler_trusersets');
            if(show == 'gruppen')
                WBeditbox_hidesubtrs('webutler_trgruppen','webutler_trblocks|webutler_trzugang|webutler_trusersets');
            if(show == 'zugang') {
                WBeditbox_hidesubtrs('webutler_trzugang','webutler_trblocks|webutler_trgruppen|webutler_trusersets');
                if(sub != '') {
                    if(sub == 'newuser')
                        WBeditbox_hidesubtrs('webutler_trnewuser','webutler_trnewregs|webutler_tredituser');
                    if(sub == 'newregs')
                        WBeditbox_hidesubtrs('webutler_trnewregs','webutler_trnewuser|webutler_tredituser');
                    if(sub == 'edituser')
                        WBeditbox_hidesubtrs('webutler_tredituser','webutler_trnewuser|webutler_trnewregs');
                }
            }
            if(show == 'usersets')
                WBeditbox_hidesubtrs('webutler_trusersets','webutler_trblocks|webutler_trgruppen|webutler_trzugang');
        }
        WBeditbox_regelements();
    }
    WBeditbox_subtrsonload();
    
    <?PHP if($curpageisblocked == 0 && isset($_POST['update_blocked_pages']) && !in_array($_POST['locationpage'], $webutlerboxlayer->offlinepages)) { ?>
    if(wbjq('#webutler_pageisoff').length > 0)
        wbjq('#webutler_pageisoff').fadeOut( 500, function() { wbjq(this).remove(); } );
    <?PHP } elseif($curpageisblocked == 1) { ?>
    if(wbjq('#webutler_pageisoff').length == 0)
        wbjq('body').prepend(wbjq('<div id="webutler_pageisoff"><?PHP echo _WBLANGADMIN_OFF_PAGEISUSERS_; ?></div>\n').fadeIn(500));
    <?PHP } ?>
	
    var useralert = '';
	if(wbjq('#webutler_alertcell').length > 0)
		useralert = wbjq('#webutler_alertcell');
	if(wbjq('#webutler_errorcell').length > 0)
		useralert = wbjq('#webutler_errorcell');
	if(wbjq('#webutler_usererror').length > 0)
		useralert = wbjq('#webutler_usererror');
    if(useralert != '')
    	setTimeout(function() {
            if(useralert.length > 0 && useralert.is(':visible'))
                useralert.fadeOut(700);
        }, 3000);
    
    /* ]]> */
    </script>
<?PHP if($webutlerboxlayer->config['userlogs'] == '1') { ?>
	<div id="webutler_access">
	<table width="100%" border="0" cellspacing="10" cellpadding="0">
	<tr>
	<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline"><?PHP echo _WBLANGADMIN_WIN_ACCESS_HEADLINE_; ?></strong></td>
	</tr>
	<tr>
	<td style="padding-left: 5px">
	<table width="100%" border="0" cellspacing="0" cellpadding="5">
<?PHP if($userconfig != '') { ?>
    <tr>
     <td style="padding-left: 10px; text-align: center"><strong<?PHP echo $color; ?>><?PHP echo $userconfig; ?></strong></td>
    </tr>
    <tr>
     <td style="padding-left: 10px"><?PHP echo $userconfigtext; ?></td>
    </tr>
<?PHP if(!isset($_POST['makeuserconfig'])) { ?>
        <tr>
         <td style="padding-left: 10px">
          <form class="webutler_boxesform" method="post">
            <table border="0" cellspacing="3" cellpadding="0" align="center">
             <tr>
               <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_NAME_; ?>:</td>
               <td><input type="checkbox" name="nachname" /></td>
             </tr>
             <tr>
               <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_FIRSTNAME_; ?>:</td>
               <td><input type="checkbox" name="vorname" /></td>
             </tr>
             <tr>
               <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_COMPANY_; ?>:</td>
               <td><input type="checkbox" name="firma" /></td>
             </tr>
             <tr>
               <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_STREET_; ?>:</td>
               <td><input type="checkbox" name="str_nr" /></td>
             </tr>
             <tr>
               <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_TOWN_; ?>:</td>
               <td><input type="checkbox" name="plz_ort" /></td>
             </tr>
             <tr>
               <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_PHONE_; ?>:</td>
               <td><input type="checkbox" name="tel" /></td>
             </tr>
             <tr>
               <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERNAME_; ?>:</td>
               <td><input type="checkbox" checked="checked" disabled="disabled" /></td>
             </tr>
             <tr>
               <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERPASS_; ?>:</td>
               <td><input type="checkbox" checked="checked" disabled="disabled" /></td>
             </tr>
             <tr>
               <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERMAIL_; ?>:</td>
               <td><input type="checkbox" checked="checked" disabled="disabled" /></td>
             </tr>
             <tr>
               <td></td>
               <td><input type="submit" name="makeuserconfig" class="webutler_button" value="<?PHP echo _WBLANGADMIN_WIN_ACCESS_SAVECONF_; ?>" /></td>
             </tr>
            </table>
          </form>
         </td>
        </tr>
<?PHP } } elseif($startafterinstall != '') { ?>
      <tr id="webutler_alertcell">
        <td style="padding-left: 10px; text-align: center"><strong<?PHP echo $color; ?>><?PHP echo $meldung; ?></strong></td>
      </tr>
      <tr>
        <td style="padding: 20px 0px 0px 10px; text-align: center"><?PHP echo $startafterinstall; ?></td>
      </tr>
<?PHP } elseif($dberror != '') { ?>
    <tr id="webutler_errorcell">
        <td style="padding-left: 10px; text-align: center"><strong<?PHP echo $color; ?>><?PHP echo $dberror; ?></strong></td>
    </tr>
<?PHP } else { if($meldung != '') { ?>
      <tr id="webutler_alertcell">
        <td style="padding-left: 10px; text-align: center"><strong<?PHP echo $color; ?>><?PHP echo $meldung; ?></strong></td>
      </tr>
<?PHP } ?>
      <tr>
		<td style="padding-left: 10px" class="webutler_hidesubtrsmenu"><span onclick="WBeditbox_hidesubtrs('webutler_trblocks','webutler_trgruppen|webutler_trzugang|webutler_trusersets');"><?PHP echo _WBLANGADMIN_WIN_ACCESS_BLOCK_LINK_; ?></span> | <span onclick="WBeditbox_hidesubtrs('webutler_trgruppen','webutler_trblocks|webutler_trzugang|webutler_trusersets');"><?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_LINK_; ?></span> | <span onclick="WBeditbox_hidesubtrs('webutler_trzugang','webutler_trblocks|webutler_trgruppen|webutler_trusersets');"><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_LINK_; ?></span> | <span onclick="WBeditbox_hidesubtrs('webutler_trusersets','webutler_trblocks|webutler_trgruppen|webutler_trzugang');"><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_LINK_; ?></span></td>
      </tr>
      <tr id="webutler_trblocks" style="display: none">
        <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
         <tr>
           <td><strong><?PHP echo _WBLANGADMIN_WIN_ACCESS_BLOCK_PAGES_; ?>:</strong></td>
         </tr>
         <tr>
           <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_BLOCK_TXT_; ?></td>
         </tr>
         <tr>
          <td>
    		<form class="webutler_boxesform" method="post" onsubmit="if(!this.ok) return false">
            <table border="0" cellspacing="5" cellpadding="0" align="center">
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_BLOCK_BLOCKED_; ?>:</td>
              <td></td>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_BLOCK_FREE_; ?>:</td>
             </tr>
             <tr>
              <td>
               <select id="block_delpage" size="6" class="webutler_select webutler_selsize6">
               <?PHP
                    $blocks = $webutleruserclass->userdb->query("SELECT id, pages FROM blocks WHERE blocks.id = '1' LIMIT 1");
			        if($block = $blocks->fetchArray()) {
                        $files = explode(',', $block['pages']);
                    	foreach($files as $page) {
                            if($page != '') {
                                echo '<option value="'.$page.'">'.$page.'</option>'."\n";
                            }
                        }
                    }
                ?>
               </select>
              </td>
              <td style="text-align: center; padding: 0px 5px"><input type="button" value="&laquo;" onclick="WBeditbox_changeblockedpage('block_newpage', 'block_delpage')" class="webutler_button_switcher" /><br /><br /><input type="button" value="&raquo;" onclick="WBeditbox_changeblockedpage('block_delpage', 'block_newpage')" class="webutler_button_switcher" /></td>
              <td>
               <select id="block_newpage" size="6" class="webutler_select webutler_selsize6">
                <?PHP
                	$directory = $webutlerboxlayer->config['server_path']."/content/pages";					
                	$handle = opendir($directory);
                	while (false !== ($file = readdir ($handle))) {
                		if(!is_dir($directory.'/'.$file.'/')) {
                            $extension = substr($file, -4);
                        	if($file != '.' && $file != '..' && $file != $webutlerboxlayer->config['ownerrorpage'] && !in_array($file, $files) && $file != '.htaccess' && $extension != '.bak' && $extension != '.tmp') {
                    			echo '<option value="'.$file.'">'.$file.'</option>'."\n";
                    		}
                		}
                	}
                	closedir($handle);
                ?>
               </select>
              </td>
             </tr>
             <tr>
              <td colspan="3" style="text-align: center; padding-top: 10px"><input type="hidden" id="blocked_pages" name="blocked_pages" value="" /><input type="submit" class="webutler_button" style="width: 120px" onclick="this.form.ok = WBeditbox_savelistedpages('block_delpage', 'blocked_pages')" name="update_blocked_pages" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_SAVE_; ?>" /></td>
             </tr>
            </table>
            </form>
          </td>
         </tr>
        </table>
        </td>
      </tr>
      <tr id="webutler_trgruppen" style="display: none">
        <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
         <tr>
           <td><strong><?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_USERGROUPS_; ?>:</strong></td>
         </tr>
         <tr>
           <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_TXT_; ?></td>
         </tr>
         <tr>
           <td style="text-align: center">if(in_array(&quot;GROUPID&quot;, $showpartfor)) { ###<?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_VISIBLEPART_; ?>### }</td>
         </tr>
         <tr>
          <td>
            <?PHP
                if(isset($_POST['update_edit_group']) || isset($_POST['make_update_group'])) {
                    if(isset($_POST['update_edit_group'])) $groupid = $webutleruserclass->userregvalidate($_POST['edit_groupid'], 'Group ID', 1);
                    if(isset($_POST['make_update_group'])) $groupid = $webutleruserclass->userregvalidate($_POST['update_groupid'], 'Group ID', 1);
                    $updategroups = $webutleruserclass->userdb->query("SELECT id, name, pages FROM groups WHERE groups.id = '".$webutleruserclass->userdb->escapeString($groupid)."' LIMIT 1");
                	$updategroup = $updategroups->fetchArray();
            ?>
            <form class="webutler_boxesform" method="post" onsubmit="if(!this.ok) return false">
            <!--  action="access.php?showtr=gruppen" -->
            <table border="0" cellspacing="5" cellpadding="0">
             <tr>
              <td style="padding-right: 10px"><?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_GROUPNAME_; ?>:</td>
              <td><input type="text" name="update_group" value="<?PHP echo stripslashes($updategroup['name']); ?>" class="webutler_input" /></td>
             </tr>
             <tr>
              <td style="padding-right: 10px"><?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_GROUPID_; ?>:</td>
              <td><?PHP echo $updategroup['id']; ?><input type="hidden" name="update_groupid" value="<?PHP echo $updategroup['id']; ?>" /></td>
             </tr>
            </table>
            <table border="0" cellspacing="5" cellpadding="0" align="center">
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_FOR_.' '.stripslashes($updategroup['name']).' '._WBLANGADMIN_WIN_ACCESS_GROUP_VISIBLEPAGES_; ?>:</td>
              <td></td>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_FOR_.' '._WBLANGADMIN_WIN_ACCESS_GROUP_VISITORS_.' '._WBLANGADMIN_WIN_ACCESS_GROUP_BLOCKEDPAGES_; ?>:</td>
             </tr>
             <tr>
              <td>
               <select id="group_newpage" size="6" class="webutler_select webutler_selsize6">
                <?PHP
                    $pages = explode(',', $updategroup['pages']);
                    $files = array();
                	foreach($pages as $page) {
                        if($page != '') {
                            echo '<option value="'.$page.'">'.$page.'</option>'."\n";
                            $files[] = $page;
                        }
                    }
                ?>
               </select>
              </td>
              <td style="text-align: center; padding: 0px 5px"><input type="button" value="&laquo;" onclick="WBeditbox_changeblockedpage('group_delpage', 'group_newpage')" class="webutler_button_switcher" /><br /><br /><input type="button" value="&raquo;" onclick="WBeditbox_changeblockedpage('group_newpage', 'group_delpage')" class="webutler_button_switcher" /></td>
              <td>
               <?PHP $blocks = $webutleruserclass->userdb->query("SELECT id, pages FROM blocks WHERE blocks.id = '1' LIMIT 1"); ?>
               <select id="group_delpage" size="6" class="webutler_select webutler_selsize6">
                <?PHP
                	if($block = $blocks->fetchArray()) {
                        $pages = explode(',', $block['pages']);
                    	foreach($pages as $page) {
                            if($page != '' && !in_array($page, $files)) {
                                echo '<option value="'.$page.'">'.$page.'</option>'."\n";
                            }
                        }
                    }
                ?>
               </select>
              </td>
             </tr>
            </table>
            <table border="0" cellspacing="5" cellpadding="0" align="center">
             <tr>
        	  <td style="text-align: center; padding-top: 5px"><input type="hidden" id="free_pages" name="free_pages" value="" /><input type="submit" class="webutler_button" style="margin-right: 5px; width: 120px" onclick="this.form.ok = WBeditbox_savelistedpages('group_newpage', 'free_pages')" name="make_update_group" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_MODIFY_; ?>" /><input type="button" class="webutler_button" style="margin-left: 5px; width: 120px" onclick="WBeditbox_open('webutler_access', 'showtr=gruppen')" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_CANCEL_; ?>" /></td>
             </tr>
            </table>
            </form>
            <?PHP } else { ?>
            <form class="webutler_boxesform" method="post">
            <!--  action="access.php?showtr=gruppen" -->
            <table width="100%" border="0" cellspacing="5" cellpadding="0">
             <tr>
              <td style="width: 135px"><?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_NEWGROUP_; ?>:</td>
              <td style="width: 160px"><input type="text" name="new_group" class="webutler_input" /></td>
        	  <td><input type="submit" class="webutler_button" style="width: 100px" name="make_new_group" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_ADD_; ?>" />
              </td>
             </tr>
            </table>
            </form>
            <?PHP
                $showgroups = $webutleruserclass->userdb->query("SELECT id, name FROM groups ORDER BY id");
                if($webutleruserclass->usernumrows($showgroups) > 0) {
            ?>
            <form class="webutler_boxesform" method="post">
            <!--  action="access.php?showtr=gruppen" -->
            <table width="100%" border="0" cellspacing="5" cellpadding="0">
             <tr>
              <td style="width: 135px"><?PHP echo _WBLANGADMIN_WIN_ACCESS_GROUP_GROUPS_; ?>:</td>
              <td style="width: 160px">
               <select name="edit_groupid" size="1" class="webutler_select">
                <?PHP
                    while($showgroup = $showgroups->fetchArray()) {
                        echo '<option value="'.$showgroup['id'].'">ID: '.$showgroup['id'].' -&gt; '.stripslashes($showgroup['name']).'</option>'."\n";
                    }
                ?>
               </select>
              </td>
        	  <td><input type="submit" class="webutler_button" style="width: 100px" name="update_edit_group" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_EDIT_; ?>" />
              </td>
             </tr>
            </table>
            </form>
            <?PHP } } ?>
          </td>
         </tr>
        </table>
        </td>
      </tr>
      <tr id="webutler_trzugang" style="display: none">
        <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
         <tr>
           <td><strong><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_ACCOUNT_; ?>:</strong></td>
         </tr>
         <tr>
           <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_TXT_; ?></td>
         </tr>
         <tr>
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
              <td style="padding: 5px 0px 10px 0px" class="webutler_hidesubtrsmenu">
              <?PHP if(!isset($_POST['update_newreg_user']) && !isset($_POST['update_edit_user']) && !isset($noupdate)) { ?><span onclick="WBeditbox_hidesubtrs('webutler_trnewuser','webutler_trnewregs|webutler_tredituser');"><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_ADDUSER_; ?></span><?PHP } else { ?><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_ADDUSER_; ?><?PHP } ?> | <?PHP if(!isset($_POST['make_new_user']) && !isset($_POST['update_edit_user']) && !isset($noupdate)) { ?><span onclick="WBeditbox_hidesubtrs('webutler_trnewregs','webutler_trnewuser|webutler_tredituser');"><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_FREENEWREGS_; ?></span><?PHP } else { ?><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_FREENEWREGS_; ?><?PHP } ?> | <?PHP if(!isset($_POST['make_new_user']) && !isset($_POST['update_newreg_user'])) { ?><span onclick="WBeditbox_hidesubtrs('webutler_tredituser','webutler_trnewuser|webutler_trnewregs');"><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_EDITUSER_; ?></span><?PHP } else { ?><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_EDITUSER_; ?><?PHP } ?>
              </td>
             </tr>
             <tr id="webutler_trnewuser" style="display: none">
              <td>
                <?PHP
                    $usergroups = $webutleruserclass->userdb->query("SELECT id, name FROM groups ORDER BY id");
                    if($webutleruserclass->usernumrows($usergroups) > 0) {
                ?>
                <form class="webutler_boxesform" method="post">
                <!--  action="access.php?showtr=zugang&subtr=newuser" -->
                <table border="0" cellspacing="5" cellpadding="0" align="center">
                    <?PHP
                    $userregfields = $webutleruserclass->userregfields();
                    if(is_array($userregfields)) {
                        foreach($userregfields as $name => $value) {
                            echo '<tr>
                              <td>'.$value.':</td>
                              <td><input type="text" name="'.$name.'" value="';
                            if(isset($_POST[$name]))
                                echo $webutleruserclass->userregvalidate($_POST[$name], $value, 4);
                            echo '" class="webutler_input" /></td>
                             </tr>
                             ';
                        }
                    }
                    ?>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERNAME_; ?>:</td>
                  <td><input type="text" name="uname" value="<?PHP if(isset($_POST['uname'])) echo $webutleruserclass->userregvalidate($_POST['uname'], _WBLANGADMIN_WIN_ACCESS_FIELD_USERNAME_, 2); ?>" class="webutler_input" /></td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERPASS_; ?>:</td>
                  <td><input type="text" name="upass" class="webutler_input" /></td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_; ?>:</td>
                  <td><input type="text" name="umail" value="<?PHP if(isset($_POST['umail'])) echo $webutleruserclass->userregvalidate($_POST['umail'], _WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_, 3); ?>" class="webutler_input" /></td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERGROUP_; ?>:</td>
                  <td>
                    <select name="ugroup[]" size="3" multiple="multiple" class="webutler_select webutler_selsize3">
                    <?PHP
                    $postugroups = array();
                    if(isset($_POST['ugroup'])) {
                        foreach($_POST['ugroup'] as $postugroup) {
                        	$postugroups[] = $webutleruserclass->userregvalidate($postugroup, _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_, 1);
                        }
                    }
                    while($usergroup = $usergroups->fetchArray()) {
                        echo '<option value="'.$usergroup['id'].'"';
                        if((!isset($_POST['ugroup']) && $usergroup['id'] == $webutleruserclass->config['newreg_groupid']) || (isset($_POST['ugroup']) && in_array($usergroup['id'], $postugroups))) echo ' selected="selected"';
                        echo '>ID: '.$usergroup['id'].' -&gt; '.stripslashes($usergroup['name']).'</option>'."\n";
                    }
                    ?>
                    </select>
                  </td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_STATUS_; ?>:</td>
                  <td>
                    <select name="status" size="1" class="webutler_select">
                     <option value="disabled"<?PHP if(isset($_POST['status']) && $_POST['status'] == 'disabled') echo ' selected="selected"'; ?>><?PHP echo _WBLANGADMIN_WIN_ACCESS_STAT_DISABLED_; ?></option>
                     <option value="enabled"<?PHP if(!isset($_POST['status']) || (isset($_POST['status']) && $_POST['status'] == 'enabled')) echo ' selected="selected"'; ?>><?PHP echo _WBLANGADMIN_WIN_ACCESS_STAT_ENABLED_; ?></option>
                    </select>
                  </td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_SENDMAIL_; ?>:</td>
                  <td>
                    <table border="0" cellspacing="0" cellpadding="0">
                     <tr>
                      <td><input type="checkbox" name="sendmail" id="senddatenmail"<?PHP if($sendmailbylang == true) {
                        echo ' onclick="WBeditbox_opencloseusermaillang(\'webutler_userlangdaten\')"';
                        } ?> /></td>
                      <td> <label for="senddatenmail">(<?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_SENDDATA_; ?>)</label></td>
                     </tr>
                    </table>
                  </td>
                 </tr>
                <?PHP if($sendmailbylang == true) { ?>
                 <tr id="webutler_userlangdaten" style="display: none">
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_LANGUAGE_; ?>:</td>
                  <td>
                    <select name="userlang" size="1" class="webutler_select">
                     <?PHP
						$langoptions = $webutlerboxlayer->langselect();	
						foreach($langoptions as $langoption) {
							echo $langoption."\n";
						}
					 ?>
                    </select>
                  </td>
                 </tr>
                <?PHP } ?>
                 <tr>
            	  <td colspan="2" style="text-align: center; padding-top: 10px"><input type="submit" class="webutler_button" style="width: 110px" name="make_new_user" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_SAVE_; ?>" /><?PHP if(isset($_POST['make_new_user'])) { ?><input type="button" class="webutler_button" style="margin-left: 10px; width: 110px" onclick="WBeditbox_open('webutler_access', 'showtr=zugang&subtr=')" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_CANCEL_; ?>" /><?PHP } ?></td>
                 </tr>
                </table>
                </form>
                <?PHP
                    }
                    else {
                ?>
                <table width="100%" border="0" cellspacing="5" cellpadding="0">
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_NOGROUP_; ?></td>
                 </tr>
                </table>
                <?PHP
                    }
                ?>
              </td>
             </tr>
             <tr id="webutler_trnewregs" style="display: none">
              <td>
                <?PHP if(isset($_POST['update_newreg_user'])) {
                    $users = $webutleruserclass->userdb->query("SELECT * FROM users WHERE users.id = '".$webutleruserclass->userdb->escapeString($webutleruserclass->userregvalidate($_POST['newreg_userid'], _WBLANGADMIN_WIN_ACCESS_FIELD_USERID_, 1))."' LIMIT 1");
                	if($user = $users->fetchArray()) {
                ?>
                <form class="webutler_boxesform" method="post">
                <!--  action="access.php?showtr=zugang&subtr=newregs" -->
                <table border="0" cellspacing="5" cellpadding="0" align="center">
                 <tr>
                  <td>L&ouml;schen:</td>
                  <td><input type="checkbox" name="deluser" /><input type="hidden" name="userid" value="<?PHP echo $user['id']; ?>" /></td>
                 </tr>
                    <?PHP
                    $userregfields = $webutleruserclass->userregfields();
                    if(is_array($userregfields)) {
                        foreach($userregfields as $name => $value) {
                             echo '<tr>
                              <td style="padding: 3px 0px">'.$value.':</td>
                              <td>'.stripslashes($user[$name]).'</td>
                             </tr>';
                        }
                    }
                    ?>
                 <tr>
                  <td style="padding: 3px 0px"><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERNAME_; ?>:</td>
                  <td><?PHP echo $user['uname']; ?></td>
                 </tr>
                 <tr>
                  <td style="padding: 3px 0px"><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_; ?>:</td>
                  <td><?PHP echo $user['umail']; ?><input type="hidden" name="umail" value="<?PHP echo $user['umail']; ?>" /></td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERGROUP_; ?>:</td>
                  <td>
                    <select name="groupid[]" size="3" multiple="multiple" class="webutler_select webutler_selsize3">
                    <?PHP
                    if(isset($_POST['groupid'])) {
                        $postgroups = array();
                        foreach($_POST['groupid'] as $postgroup) {
                        	$postgroups[] = $webutleruserclass->userregvalidate($postgroup, _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_, 1);
                        }
                    }
                    $usergroupids = explode('|', $user['groupid']);
                    $usergroups = $webutleruserclass->userdb->query("SELECT id, name FROM groups ORDER BY id");
                    while($usergroup = $usergroups->fetchArray()) {
                        echo '<option value="'.$usergroup['id'].'"';
                    	if((isset($_POST['groupid']) && in_array($usergroup['id'], $postgroups)) || (!isset($_POST['groupid']) && in_array($usergroup['id'], $usergroupids)))
							echo ' selected="selected"';
						echo '>ID: '.$usergroup['id'].' -&gt; '.stripslashes($usergroup['name']).'</option>'."\n";
                    }
                    ?>
                    </select>
                  </td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_STATUS_; ?>:</td>
                  <td>
                    <select name="status" size="1" class="webutler_select">
                     <option value="enabled" selected="selected"><?PHP echo _WBLANGADMIN_WIN_ACCESS_STAT_ENABLED_; ?></option>
                     <option value="disabled"><?PHP echo _WBLANGADMIN_WIN_ACCESS_STAT_DISABLED_; ?></option>
                    </select>
                  </td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_SENDMAIL_; ?>:</td>
                  <td>
                    <table border="0" cellspacing="0" cellpadding="0">
                     <tr>
                      <td><input type="checkbox" name="sendmail" id="sendprofilmail"<?PHP if($sendmailbylang == true) {
                        echo ' onclick="WBeditbox_opencloseusermaillang(\'webutler_userlangprofil\')"';
                        } ?> /></td>
                      <td> <label for="sendprofilmail">(<?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_SENDFREE_; ?>)</label></td>
                     </tr>
                    </table>
                  </td>
                 </tr>
                <?PHP if($sendmailbylang == true) { ?>
                 <tr id="webutler_userlangprofil" style="display: none">
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_LANGUAGE_; ?>:</td>
                  <td>
                    <select name="userlang" size="1" class="webutler_select">
                     <?PHP
						$langoptions = $webutlerboxlayer->langselect();	
						foreach($langoptions as $langoption) {
							echo $langoption."\n";
						}
					 ?>
                    </select>
                  </td>
                 </tr>
                <?PHP } ?>
                 <tr>
            	  <td colspan="2" style="text-align: center; padding-top: 10px"><input type="submit" class="webutler_button" style="margin-right: 5px; width: 110px" name="update_newregs_user" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_SAVE_; ?>" /><input type="button" class="webutler_button" style="margin-left: 5px; width: 110px" onclick="WBeditbox_open('webutler_access', 'showtr=zugang&subtr=newregs')" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_CANCEL_; ?>" /></td>
                 </tr>
                </table>
                </form>
                <?PHP
                    }
                }
                else {
                    $users = $webutleruserclass->userdb->query("SELECT * FROM users WHERE users.status = 'newreg' ORDER BY uname");
                    if($webutleruserclass->usernumrows($users) > 0) {
                ?>
                <form class="webutler_boxesform" method="post">
                <!--  action="access.php?showtr=zugang&subtr=newregs" -->
                <table width="100%" border="0" cellspacing="5" cellpadding="0" align="center">
                 <tr>
                  <td style="width: 135px"><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_NEWREGS_; ?>:</td>
                  <td style="width: 160px">
                    <select name="newreg_userid" size="1" class="webutler_select">
                    <?PHP
                    while($user = $users->fetchArray()) {
                        $username = $user['uname'];
                        if($user['nachname']) {
                            $username.= ' ('.stripslashes($user['nachname']);
                            if($user['firma']) $username.= '/'.stripslashes($user['firma']);
                            $username.= ')';
                        }
                        echo '<option value="'.$user['id'].'">'.$username.'</option>'."\n";
                    }
                    ?>
                    </select>
                  </td>
            	  <td><input type="submit" class="webutler_button" name="update_newreg_user" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_FREE_; ?>" />
                  </td>
                 </tr>
                </table>
                </form>
                <?PHP
                    }
                    else {
                ?>
                <table width="100%" border="0" cellspacing="5" cellpadding="0">
                 <tr>
                  <td><?PHP
                        if($webutleruserclass->config['newreg_adm_auto'] == '2') {
                            echo _WBLANGADMIN_WIN_ACCESS_USERS_FREEISAUTO_;
                        }
                        else {
                            echo _WBLANGADMIN_WIN_ACCESS_USERS_NONEWREGS_;
                        }
                    ?></td>
                 </tr>
                </table>
                <?PHP
                    }
                }
                ?>
              </td>
             </tr>
             <tr id="webutler_tredituser" style="display: none">
              <td>
                <?PHP
				if(isset($_POST['update_edit_user']) && isset($_POST['edit_username'])) {
					if($_POST['edit_username'] != '') {
						if(preg_match('#@#i', $_POST['edit_username'])) {
							$validusermail = $webutleruserclass->userdb->escapeString($webutleruserclass->userregvalidate($_POST['edit_username'], _WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_, 3));
							if($webutleruserclass->meldung == '') {
								$users = $webutleruserclass->userdb->query("SELECT * FROM users WHERE users.umail = '".$validusermail."' LIMIT 1");
							}
						}
						else {
							$validusername = $webutleruserclass->userdb->escapeString($webutleruserclass->userregvalidate($_POST['edit_username'], _WBLANGADMIN_WIN_ACCESS_FIELD_USERNAME_, 2));
							if($webutleruserclass->meldung == '') {
								$users = $webutleruserclass->userdb->query("SELECT * FROM users WHERE users.uname = '".$validusername."' LIMIT 1");
							}
						}
					}
					if(!($user = $users->fetchArray())) {
						$searchuser_notfound = 1;
					}
				}
                if(!isset($searchuser_notfound) && (isset($_POST['update_edit_user']) || isset($noupdate))) {
                    if(isset($noupdate)) {
						$userid = $webutleruserclass->userdb->escapeString($webutleruserclass->userregvalidate($_POST['userid'], _WBLANGADMIN_WIN_ACCESS_FIELD_USERID_, 1));
						if($webutleruserclass->meldung == '') {
							$users = $webutleruserclass->userdb->query("SELECT * FROM users WHERE users.id = '".$userid."' LIMIT 1");
							$user = $users->fetchArray();
						}
                    }
					if(isset($user) && is_array($user)) {
                ?>
                <form class="webutler_boxesform" method="post">
                <!--  action="access.php?showtr=zugang&subtr=edituser" -->
                <table border="0" cellspacing="5" cellpadding="0" align="center">
                 <tr>
                    <?PHP if($user['status'] == 'deleted') { ?>
                  <td colspan="2" style="padding-bottom: 5px">
                    <strong><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_REACCOUNT_; ?></strong>
                  </td>
                 </tr>
                 <tr>
                    <?PHP } ?>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_DELETE_; ?>:</td>
                  <td><input type="checkbox" name="deluser"<?PHP if($user['status'] == 'deleted') { echo ' checked="checked"'; } ?> /></td>
                 </tr>
                    <?PHP
                    $userregfields = $webutleruserclass->userregfields();
                    if(is_array($userregfields)) {
                        foreach($userregfields as $name => $value) {
                             echo '<tr>
                              <td>'.$value.':</td>
                              <td><input type="text" name="'.$name.'" value="'.stripslashes((isset($_POST[$name])) ? $_POST[$name] : isset($user[$name]) ? $user[$name] : '').'" class="webutler_input" /></td>
                             </tr>';
                        }
                    }
                    ?>
                 <tr>
                  <td style="padding: 3px 0px"><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERNAME_; ?>:</td>
                  <td><?PHP echo $user['uname']; ?><input type="hidden" name="userid" value="<?PHP echo $user['id']; ?>" /></td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERPASS_; ?>:</td>
                  <td><input type="text" name="upass" class="webutler_input" /></td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_; ?>:</td>
                  <td><input type="text" name="umail" value="<?PHP echo (isset($_POST['umail'])) ? $_POST['umail'] : $user['umail']; ?>" class="webutler_input" /></td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERGROUP_; ?>:</td>
                  <td>
                    <select name="groupid[]" size="3" multiple="multiple" class="webutler_select webutler_selsize3">
                    <?PHP
                    if(isset($_POST['groupid'])) {
                        $postgroups = array();
                        foreach($_POST['groupid'] as $postgroup) {
                        	$postgroups[] = $webutleruserclass->userregvalidate($postgroup, _WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_, 1);
                        }
                    }
                    $usergroupids = explode('|', $user['groupid']);	
                    $usergroups = $webutleruserclass->userdb->query("SELECT id, name FROM groups ORDER BY id");
                    while($usergroup = $usergroups->fetchArray()) {
                        echo '<option value="'.$usergroup['id'].'"';
                    	if((isset($_POST['groupid']) && in_array($usergroup['id'], $postgroups)) || (!isset($_POST['groupid']) && in_array($usergroup['id'], $usergroupids)))
							echo ' selected="selected"';
                        echo '>ID: '.$usergroup['id'].' -&gt; '.stripslashes($usergroup['name']).'</option>'."\n";
                    }
                    ?>
                    </select>
                  </td>
                 </tr>
                 <tr>
                  <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_STATUS_; ?>:</td>
                  <td>
                    <select name="status" size="1" class="webutler_select">
                    <?PHP
                    $statuses = array('enabled','disabled');
                    foreach($statuses as $status) {
                        echo '<option value="'.$status.'"';
                        if((isset($_POST['status']) && $status == $_POST['status']) || ($status == 'disabled' && $user['status'] == 'deleted') || $status == $user['status'])
                            echo ' selected="selected"';
                        echo '>';
                        if($status == 'enabled')
	                        echo _WBLANGADMIN_WIN_ACCESS_STAT_ENABLED_;
                        elseif($status == 'disabled')
	                        echo _WBLANGADMIN_WIN_ACCESS_STAT_DISABLED_;
                        echo '</option>'."\n";
                    }
                    ?>
                    </select>
                  </td>
                 </tr>
                 <tr>
            	  <td colspan="2" style="text-align: center; padding-top: 10px"><input type="submit" class="webutler_button" style="margin-right: 5px; width: 110px" name="make_update_user" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_SAVE_; ?>" /><input type="button" class="webutler_button" style="margin-left: 5px; width: 110px" onclick="WBeditbox_open('webutler_access', 'showtr=zugang&subtr=edituser')" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_CANCEL_; ?>" /></td>
                 </tr>
                </table>
                </form>
                <?PHP
					}
                }
                else {
                ?>
                <form class="webutler_boxesform" method="post">
                <!-- action="access.php?showtr=zugang&subtr=edituser"  -->
                <table width="100%" border="0" cellspacing="5" cellpadding="0" align="center">
                 <?PHP if(isset($searchuser_notfound)) { ?>
                 <tr id="webutler_usererror">
                  <td colspan="3" style="text-align: center; padding-bottom: 5px"><strong style="color: #D02705"><?PHP echo _WBLANGADMIN_WIN_ACCESS_USERS_USERNOTFOUND_; ?></strong></td>
                 </tr>
                 <?PHP } ?>
                 <tr>
                  <td style="width: 135px"><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERNAMEMAIL_; ?>:</td>
                  <td style="width: 170px"><input type="text" name="edit_username" size="1" class="webutler_input" /></td>
            	  <td><input type="submit" class="webutler_button" name="update_edit_user" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_EDIT_; ?>" /></td>
                 </tr>
                </table>
                </form>
                <?PHP } ?>
              </td>
             </tr>
            </table>
          </td>
         </tr>
        </table>
        </td>
      </tr>
      <tr id="webutler_trusersets" style="display: none">
        <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
         <tr>
           <td><strong><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_; ?>:</strong></td>
         </tr>
         <tr>
           <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_TXT_; ?></td>
         </tr>
         <tr>
          <td>
           <form class="webutler_boxesform" method="post">
           <!--  action="access.php?showtr=usersets" -->
            <table border="0" cellspacing="5" cellpadding="0" align="center">
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_REGISTRATION_; ?>:</td>
              <td>
                <table border="0" cellspacing="0" cellpadding="0">
                 <tr>
                  <td><input type="checkbox" onclick="WBeditbox_regelements()" id="config_regon" name="config_regon"<?PHP if($webutleruserclass->config['reg_active'] == "1") { echo ' checked="checked"'; } ?> /></td>
                  <td><label for="config_regon"> <?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_REGON_; ?></label></td>
                 </tr>
                </table>
              </td>
              <td><img src="admin/system/images/icons/help.png" onmouseover="WBeditbox_showexplaination('<?PHP echo _WBLANGADMIN_WIN_ACCESS_HELP_REGISTRATION_; ?>')" /></td>
             </tr>
			 
			 
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_LOGINLINK_; ?>:</td>
              <td>
                <table border="0" cellspacing="0" cellpadding="0">
                 <tr>
                  <td><input type="checkbox" id="config_loginlink" name="config_loginlink"<?PHP if($webutleruserclass->config['login_link'] == "1") { echo ' checked="checked"'; } ?> /></td>
                  <td><label for="config_loginlink"> <?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_LOGINSHOW_; ?></label></td>
                 </tr>
                </table>
              </td>
              <td><img src="admin/system/images/icons/help.png" onmouseover="WBeditbox_showexplaination('<?PHP echo _WBLANGADMIN_WIN_ACCESS_HELP_LOGINLINK_; ?>')" /></td>
             </tr>
			 
			 
			 
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_REGBY_; ?>:</td>
              <td>
                <table border="0" cellspacing="0" cellpadding="0">
                 <tr>
                  <td><input type="radio" onclick="WBeditbox_enableforcer()" name="config_regby" id="config_regby1" value="1"<?PHP if($webutleruserclass->config['newreg_adm_auto'] == "1" || !isset($autoregok)) { echo ' checked="checked"'; } ?> /></td>
                  <td><label for="config_regby1"> <?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_REGADMIN_; ?> </label></td>
                  <td><input type="radio" onclick="WBeditbox_enableforcer()" name="config_regby" id="config_regby2" value="2"<?PHP if($webutleruserclass->config['newreg_adm_auto'] == "2") { echo ' checked="checked"'; } if(!isset($autoregok)) { echo ' disabled="disabled"'; } ?> /></td>
                  <td><label for="config_regby2"> <?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_REGAUTO_; ?></label></td>
                 </tr>
                </table>
              </td>
              <td><img src="admin/system/images/icons/help.png" onmouseover="WBeditbox_showexplaination('<?PHP echo _WBLANGADMIN_WIN_ACCESS_HELP_REGBY_; if(!isset($autoregok)) echo ' '._WBLANGADMIN_WIN_ACCESS_HELP_REGBYAUTO_; ?>')" /></td>
             </tr>
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_FIELD_USERGROUP_; ?>:</td>
              <td>
                <?PHP
                    echo '<select id="config_reggroupid" name="config_reggroupid" size="1" class="webutler_select"';
                    if(!isset($autoregok)) echo ' disabled="disabled"';
                    echo '>'."\n";
                    while($reggroup = $reggroups->fetchArray()) {
                        echo '<option value="'.$reggroup['id'].'"';
                        if($reggroup['id'] == $webutleruserclass->config['newreg_groupid']) echo ' selected="selected"';
                        echo '>ID: '.$reggroup['id'].' -&gt; '.stripslashes($reggroup['name']).'</option>'."\n";
                    }
                    echo '</select>'."\n";
                ?>
              </td>
              <td><img src="admin/system/images/icons/help.png" onmouseover="WBeditbox_showexplaination('<?PHP echo _WBLANGADMIN_WIN_ACCESS_HELP_REGGROUP_; ?>')" /></td>
             </tr>
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_ADMINMAIL_; ?>:</td>
              <td>
                <table border="0" cellspacing="0" cellpadding="0">
                 <tr>
                  <td><input type="checkbox" name="config_regtoadmin" id="config_regtoadmin"<?PHP if($webutleruserclass->config['mailtoadmin'] == "1") { echo ' checked="checked"'; } ?> /></td>
                  <td><label for="config_regtoadmin"> <?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_SENDADMINMAIL_; ?></label></td>
                 </tr>
                </table>
              </td>
              <td><img src="admin/system/images/icons/help.png" onmouseover="WBeditbox_showexplaination('<?PHP echo _WBLANGADMIN_WIN_ACCESS_HELP_MAILTOADMIN_; ?>')" /></td>
             </tr>
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_ADMINMAILADDRESS_; ?>:</td>
              <td><input type="text" name="config_regadminmail" id="config_regadminmail" class="webutler_input" value="<?PHP echo $webutleruserclass->config['admin_email']; ?>" /></td>
              <td><img src="admin/system/images/icons/help.png" onmouseover="WBeditbox_showexplaination('<?PHP echo _WBLANGADMIN_WIN_ACCESS_HELP_ADMINMAIL_; ?>')" /></td>
             </tr>
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_USERMAIL_; ?>:</td>
              <td>
                <table border="0" cellspacing="0" cellpadding="0">
                 <tr>
                  <td><input type="checkbox" name="config_regtouser" id="config_regtouser"<?PHP if($webutleruserclass->config['activator_mail'] == "1") { echo ' checked="checked"'; } ?> /></td>
                  <td><label for="config_regtouser"> <?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_REGTOUSER_; ?></label></td>
                 </tr>
                </table>
              </td>
              <td><img src="admin/system/images/icons/help.png" onmouseover="WBeditbox_showexplaination('<?PHP echo _WBLANGADMIN_WIN_ACCESS_HELP_REGTOUSER_; ?>')" /></td>
             </tr>
             <tr>
              <td><?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_DELUSER_; ?>:</td>
              <td>
                <table border="0" cellspacing="0" cellpadding="0">
                 <tr>
                  <td><input type="checkbox" name="config_deluserdb" id="config_deluserdb"<?PHP if($webutleruserclass->config['deluser_db'] == "1") { echo ' checked="checked"'; } ?> /></td>
                  <td><label for="config_deluserdb"> <?PHP echo _WBLANGADMIN_WIN_ACCESS_SETTINGS_DELFROMDB_; ?></label></td>
                 </tr>
                </table>
              </td>
              <td><img src="admin/system/images/icons/help.png" onmouseover="WBeditbox_showexplaination('<?PHP echo _WBLANGADMIN_WIN_ACCESS_HELP_DELFROMDB_; ?>')" /></td>
             </tr>
             <tr>
              <td colspan="2" style="text-align: center; padding-top: 10px"><input type="submit" name="update_config_file" class="webutler_button" style="width: 120px" value="<?PHP echo _WBLANGADMIN_WIN_BUTTONS_SAVE_; ?>" /></td>
              <td></td>
             </tr>
            </table>
           </form>
          </td>
         </tr>
        </table>
        </td>
      </tr>
<?PHP } ?>
    </table>
    </td>
    </tr>
    </table>
    </div>
<?PHP
	}
}
?>
