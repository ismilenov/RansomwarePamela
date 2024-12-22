<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/includes/users/user_class.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');


/********************************************************

Einzelne Seitenteile können mit: 

< ?PHP if(in_array('__GROUP_ID__', $showpartfor)) { ? >
    sichtbarer Teil der Seite
< ?PHP } ? >

gesperrt werden. Sie sind dann nur für angemeldete Besucher sichtbar.

********************************************************/


class UsersClass
{
    var $dbpath;
    var $meldung = '';
    var $userdb;
    var $config;
	var $saltkeys = array();
    var $getpage;
    var $userregvars;
    var $postdata;
    var $userdata = array();
    var $userauth = array();
    var $mailtouser = 0;
    var $mailtoadmin = 0;
    var $mailtext;
    
    function connectdb()
    {
        $this->userdb = new SQLite3($this->dbpath.'/users.db');
    }

    function usernumrows($check)
    {
        $rows = 0;
        while($row = $check->fetchArray()) {
            $rows++;
        }
        return $rows;
    }

    function login()
    {
        $this->connectdb();
        $post_uname = $this->userregvalidate($this->postdata['username'], '', 2);
        $users = $this->userdb->query("SELECT id, uname, umail, upass, status, groupid FROM users WHERE users.uname = '".$this->userdb->escapeString($post_uname)."' AND users.upass = '".$this->userdb->escapeString(md5($this->saltkeys[0].trim($this->postdata['userpass']).$this->saltkeys[1]))."' LIMIT 1");
        
        /*
        if($this->usernumrows($users) !== 0) {
        	$user = $users->fetchArray();
        */
        if($user = $users->fetchArray()) {
            if($user['status'] == 'enabled') {
                $this->userauth['userid'] = $user['id'];
                $this->userauth['username'] = $user['uname'];
                $this->userauth['usermail'] = $user['umail'];
                $this->userauth['groupid'] = explode('|', $user['groupid']);
            }
            elseif($user['status'] == 'deleted') {
                $this->meldung = _USERSLANG_USERDELETED_;
            }
            else {
                $this->meldung = _USERSLANG_USERNOTACTIV_;
            }
        }
        else {
            $this->meldung = _USERSLANG_WRONGLOGIN_;
        }
    }

    function registrierung()
    {
        $this->connectdb();
        $post_uname = $this->userregvalidate($this->postdata['uname'], _USERSLANG_FIELDUSERNAME_, 2);
        $post_umail = $this->userregvalidate($this->postdata['umail'], _USERSLANG_FIELDMAILADDRESS_, 3);
        $this->check_userexists($post_uname);
        $this->check_mailexists($post_umail);
        if($this->config['reg_active'] == 0 || $this->config['newreg_groupid'] == '')
            $this->meldung = _USERSLANG_REGNOTACTIV_;
        if($this->meldung == '') {
            $dbvalues = "";
            if($this->userregvars != '') {
                $fieldnames = $this->userregvars.',';
                $regfields = $this->userregfields();
                if(is_array($regfields)) {
                    foreach($regfields as $name => $value) {
                        $this->postdata[$name] = $this->userregvalidate($this->postdata[$name], $value, 4);
                        $dbvalues.= "'".$this->userdb->escapeString($this->postdata[$name])."',";
                    }
                }
            }
            
            if($this->config['activator_mail'] == 1) $status = 'waitforcode';
            elseif($this->config['newreg_adm_auto'] == 1) $status = 'newreg';
            elseif($this->config['newreg_adm_auto'] == 2) $status = 'enabled';
            
            $ugroup = $this->userregvalidate($this->config['newreg_groupid'], _USERSLANG_FIELDGROUP_, 1);
            $post_upass = $this->userregvalidate($this->postdata['upass'], _USERSLANG_FIELDPASSWORD_, 6);
            
            if($this->meldung == '') {
                $dbvalues.= "'".$this->userdb->escapeString($post_uname)."',";
                $dbvalues.= "'".$this->userdb->escapeString($post_umail)."',";
                $dbvalues.= "'".$this->userdb->escapeString(md5($this->saltkeys[0].$post_upass.$this->saltkeys[1]))."',";
                $dbvalues.= "'".$this->userdb->escapeString($status)."',";
                $dbvalues.= "'".$this->userdb->escapeString($ugroup)."'";
                
                $todb = $this->userdb->query("INSERT INTO users (".$fieldnames."uname,umail,upass,status,groupid) VALUES (".$dbvalues.")");
                if(!$todb) {
                    $this->meldung = _USERSLANG_REGNOTCLOSE_;
                }
                else {
                    $users = $this->userdb->query("SELECT id, uname, umail FROM users WHERE users.uname = '".$this->userdb->escapeString($post_uname)."' AND users.umail = '".$this->userdb->escapeString($post_umail)."' LIMIT 1");
			        /*
                    if($this->usernumrows($users) !== 0) {
                    	$user = $users->fetchArray();
			        */
			        if($user = $users->fetchArray()) {
                        $this->mailtext = array();
                        if($status == 'waitforcode') {
                            $date = time();
                            $code = md5($post_uname.$date);
                            $regvals = "'".$this->userdb->escapeString($user['id'])."', ";
                            $regvals.= "'".$this->userdb->escapeString($code)."', ";
                            $regvals.= "'wait', ";
                            $regvals.= "'".$this->userdb->escapeString($date)."'";
                            $makecode = $this->userdb->query("INSERT INTO reloads (userid, code, what, date) VALUES (".$regvals.")");
                            if(!$makecode) {
                                $this->meldung = _USERSLANG_REGNOTCLOSE_;
                            }
                            else {
                                $this->mailtouser = 1;
                                $this->mailtext['code'] = $code;
                                $this->mailtext['uname'] = $user['uname'];
                                $this->mailtext['umail'] = $user['umail'];
                                $this->meldung = _USERSLANG_OPENMAILACTIV_;
                            }
                        }
                        else {
                            $this->mailtoadmin = 1;
                            $this->mailtext['uname'] = $user['uname'];
                            $this->mailtext['umail'] = $user['umail'];
                            $meldung = _USERSLANG_REGSUCCESS_;
                            if($status == 'newreg')
                                $meldung.= ' '._USERSLANG_REGBYADMIN_;
                            $this->meldung = $meldung;
                        }
                    }
                }
            }
        }
    }
    
    function passsigns($length, $signs)
    {
        $part = '';
        for($i = 0; $i < $length; $i++) {
            $part.= $signs[mt_rand(0, strlen($signs)-1)];
        }
        return $part;
    }
    
    function genpass()
    {
        $pass = $this->passsigns(2, 'abcdefghijkmnpqrstuvwxyz');
        $pass.= $this->passsigns(2, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $pass.= $this->passsigns(1, '1234567890');
        $pass.= $this->passsigns(1, '#+-_*@%&=!?');
        
        return str_shuffle($pass);
    }

    function neuespasswort()
    {
        $post_uname = $this->userregvalidate($this->postdata['username'], _USERSLANG_FIELDUSERNAME_, 2);
        $post_umail = $this->userregvalidate($this->postdata['usermail'], _USERSLANG_FIELDMAILADDRESS_, 3);
        if($this->meldung == '') {
            $this->connectdb();
            $this->delete_unordered();
            $users = $this->userdb->query("SELECT id, uname, umail, status FROM users WHERE users.uname = '".$this->userdb->escapeString($post_uname)."' AND users.umail = '".$this->userdb->escapeString($post_umail)."' LIMIT 1");
	        /*
            if($this->usernumrows($users) !== 0) {
            	$user = $users->fetchArray();
	        */
	        if($user = $users->fetchArray()) {
                if($user['status'] == 'enabled') {
                    $date = time();
                    $code = md5($post_uname.$date);
                    $regvals = "'".$this->userdb->escapeString($user['id'])."', ";
                    $regvals.= "'".$this->userdb->escapeString($code)."', ";
                    $regvals.= "'pass', ";
                    $regvals.= "'".$this->userdb->escapeString($date)."'";
                    $todo = $this->userdb->query("INSERT INTO reloads (userid, code, what, date) VALUES (".$regvals.")");
                    if(!$todo) {
                        $this->meldung = _USERSLANG_NEWPASSFAILED_;
                    }
                    else {
                        $this->mailtouser = 1;
                        $this->mailtext = array();
                        $this->mailtext['code'] = $code;
                        $this->mailtext['uname'] = $user['uname'];
                        $this->mailtext['umail'] = $user['umail'];
                        $this->meldung = _USERSLANG_OPENMAILACCOUNT_;
                    }
                }
                else {
                    $this->meldung = _USERSLANG_NOPASSNOTACTIV_;
                }
            }
            else {
                $this->meldung = _USERSLANG_NOUSERORMAIL_;
            }
        }
    }

    function aktivierung($code)
    {
        $this->connectdb();
        $this->delete_unordered();
        $activ = $this->userdb->query("SELECT id, userid, code, what FROM reloads WHERE reloads.code = '".$this->userdb->escapeString($code)."' LIMIT 1");
        /*
        if($this->usernumrows($activ) !== 0) {
        	$activate = $activ->fetchArray();
        */
        if($activate = $activ->fetchArray()) {
            if($activate['what'] == 'wait') {
                if($this->config['newreg_adm_auto'] == 1) {
                    $status = 'newreg';
                    $meldung = _USERSLANG_REGOKCONFIRMADMIN_;
                }
                elseif($this->config['newreg_adm_auto'] == 2) {
                    $status = 'enabled';
                    $meldung = _USERSLANG_REGOKCANLOGIN_;
                }
                
                $todo = $this->userdb->query("UPDATE users SET status = '".$status."' WHERE users.id = '".$this->userdb->escapeString($activate['userid'])."'");
                if(!$todo) {
                    $this->meldung = _USERSLANG_ACTIVFAILEDTRY_;
                }
                else {
                    $this->userdb->query("DELETE FROM reloads WHERE reloads.id = '".$this->userdb->escapeString($activate['id'])."'");
                    if($this->config['mailtoadmin'] == 1 && $this->config['admin_email'] != '') {
                        $users = $this->userdb->query("SELECT id, uname, umail FROM users WHERE users.id = '".$this->userdb->escapeString($activate['userid'])."' LIMIT 1");
				        /*
                        if($this->usernumrows($users) !== 0) {
                        	$user = $users->fetchArray();
				        */
				        if($user = $users->fetchArray()) {
                            $this->mailtoadmin = 1;
                            $this->mailtext = array();
                            $this->mailtext['uname'] = $user['uname'];
                            $this->mailtext['umail'] = $user['umail'];
                        }
                    }
                    $this->meldung = $meldung;
                }
            }
            if($activate['what'] == 'pass') {
                $newpass = $this->genpass();
                
                $todo = $this->userdb->query("UPDATE users SET upass = '".md5($this->saltkeys[0].$newpass.$this->saltkeys[1])."' WHERE users.id = '".$this->userdb->escapeString($activate['userid'])."'");
                
                if(!$todo) {
                    $this->meldung = _USERSLANG_PASSFAILEDTRY_;
                }
                else {
                    $this->userdb->query("DELETE FROM reloads WHERE reloads.id = '".$this->userdb->escapeString($activate['id'])."'");
                    $users = $this->userdb->query("SELECT id, uname, umail FROM users WHERE users.id = '".$this->userdb->escapeString($activate['userid'])."' LIMIT 1");
			        /*
                    if($this->usernumrows($users) !== 0) {
                    	$user = $users->fetchArray();
			        */
			        if($user = $users->fetchArray()) {
                        $this->meldung = _USERSLANG_NEWPASSTOMAIL_;
                        $this->mailtouser = 1;
                        $this->mailtext['uname'] = $user['uname'];
                        $this->mailtext['umail'] = $user['umail'];
                        $this->mailtext['upass'] = $newpass;
                    }
                    /*
                    else {
                        $this->meldung = 'Sie haben Ihr Passwort erfolgreich ge&auml;ndert.';
                    }
                    */
                }
            }
        }
    }

    function delete_unordered()
    {
        $regdate = time()-60*60*24;
        $delusers = $this->userdb->query("SELECT userid, what, date FROM reloads WHERE reloads.date < '".$this->userdb->escapeString($regdate)."'");
        /*
        if($this->usernumrows($delusers) !== 0) {
        	$deluser = $delusers->fetchArray();
        */
        if($deluser = $delusers->fetchArray()) {
            if($deluser['what'] == 'wait') {
                $this->userdb->query("DELETE FROM users WHERE users.id = '".$this->userdb->escapeString($deluser['userid'])."'");
            }
            $this->userdb->query("DELETE FROM reloads WHERE reloads.userid = '".$this->userdb->escapeString($deluser['userid'])."'");
        }
    }

    function speicherneuedaten()
    {
//print_r($_SESSION['userauth']);
        $uid = $this->userregvalidate($_SESSION['userauth']['userid'], _USERSLANG_FIELDUSERID_, 1);
        $uname = $this->userregvalidate($_SESSION['userauth']['username'], _USERSLANG_FIELDUSERNAME_, 2);
        $umail = $this->userregvalidate($_SESSION['userauth']['usermail'], _USERSLANG_FIELDMAILADDRESS_, 3);
        $newmail = $this->userregvalidate($this->postdata['umail'], _USERSLANG_FIELDMAILADDRESS_, 3);
        $this->connectdb();
        if($umail != $newmail)
            $this->check_mailexists($newmail);
        if($this->meldung == '') {
//print_r(array($this->meldung));
            $newdata = "";
            $regfields = $this->userregfields();
            if(is_array($regfields)) {
                foreach($regfields as $name => $value) {
                    $newdata.= $name." = '".$this->userdb->escapeString($this->userregvalidate($this->postdata[$name], $value, 4))."', ";
                }
            }
            if($this->postdata['upass'] != '') {
				$post_upass = $this->userregvalidate($this->postdata['upass'], _USERSLANG_FIELDPASSWORD_, 6);
                $newdata.= "upass = '".$this->userdb->escapeString(md5($this->saltkeys[0].$post_upass.$this->saltkeys[1]))."', ";
			}
            $newdata.= "umail = '".$this->userdb->escapeString($newmail)."'";
            if($this->meldung == '') {
                $todo = $this->userdb->query("UPDATE users SET ".$newdata." WHERE users.id = '".$this->userdb->escapeString($uid)."'");
                if(!$todo) {
                    $this->meldung = _USERSLANG_EDITNOSAVE_;
                }
                else {
                    $this->userauth['userid'] = $_SESSION['userauth']['userid'];
                    $this->userauth['username'] = $_SESSION['userauth']['username'];
                    $this->userauth['usermail'] = $this->postdata['umail'];
                    $this->userauth['groupid'] = $_SESSION['userauth']['groupid'];
                    
					//$_SESSION['userauth'] = $this->userauth;
                    $this->benutzerdaten();
                    //$this->benutzerdaten($this->userauth);
                }
            }
        }
    }

    function benutzerdaten()
    {
        $uname = $this->userregvalidate($_SESSION['userauth']['username'], _USERSLANG_FIELDUSERNAME_, 2);
        $umail = $this->userregvalidate($_SESSION['userauth']['usermail'], _USERSLANG_FIELDMAILADDRESS_, 3);
		
		if(!isset($this->postdata['neuedaten'])) $this->connectdb();
		$selects = 'id,uname,upass,umail';
		$selects.= ($this->userregvars != '') ? ','.$this->userregvars : '';
		$users = $this->userdb->query("SELECT ".$selects." FROM users WHERE users.uname = '".$this->userdb->escapeString($uname)."' AND users.umail = '".$this->userdb->escapeString($umail)."' LIMIT 1");
		/*
		if($this->usernumrows($users) !== 0) {
			$user = $users->fetchArray();
		*/
		if($user = $users->fetchArray()) {
			foreach($user as $name => $value) {
				if(!is_numeric($name))
					$this->userdata[$name] = $value;
			}
		}
    }

    function check_userexists($postuname)
    {
	    if($postuname != '') {
	        $checks = $this->userdb->query("SELECT uname FROM users WHERE users.uname = '".$this->userdb->escapeString($postuname)."' LIMIT 1");
	        /*
	        if($this->usernumrows($checks) !== 0) {
	        	$check = $checks->fetchArray();
	        */
	        if($check = $checks->fetchArray()) {
	            if($check['uname'] == $postuname) $this->meldung = _USERSLANG_USEREXISTS_;
	        }
        }
    }

    function check_mailexists($postumail, $postid = '')
    {
	    if($postumail != '') {
		    if($postid != '')
		        $checks = $this->userdb->query("SELECT id, umail FROM users WHERE users.id <> '".$this->userdb->escapeString($postid)."' AND users.umail = '".$this->userdb->escapeString($postumail)."' LIMIT 1");
		    else
		        $checks = $this->userdb->query("SELECT umail FROM users WHERE users.umail = '".$this->userdb->escapeString($postumail)."' LIMIT 1");
	        
	        /*
	        if($this->usernumrows($checks) !== 0) {
	        	$check = $checks->fetchArray();
	        */
	        if($check = $checks->fetchArray()) {
	            if($check['umail'] == $postumail) $this->meldung = _USERSLANG_MAILEXISTS_;
	        }
        }
    }

    function userregfields()
    {
    	$table = '';
    	if($this->userregvars != '')
        {
    		$fields = explode(',', $this->userregvars);
    		$table = array();
    		if(in_array('nachname', $fields)) $table['nachname'] = _USERSLANG_FIELDLASTNAME_;
    		if(in_array('vorname', $fields)) $table['vorname'] = _USERSLANG_FIELDFIRSTNAME_;
    		if(in_array('firma', $fields)) $table['firma'] = _USERSLANG_FIELDCOMPANY_;
    		if(in_array('str_nr', $fields)) $table['str_nr'] = _USERSLANG_FIELDSTREET_;
    		if(in_array('plz_ort', $fields)) $table['plz_ort'] = _USERSLANG_FIELDLOCATION_;
    		if(in_array('tel', $fields)) $table['tel'] = _USERSLANG_FIELDFON_;
    	}
    	return $table;
    }

    function userregvalidate($string, $fieldname, $set)
    {
    	$string = preg_replace('#( +)#', ' ', $string);
    	$string = trim($string);
        $meldung = '';
    	
    	if($string == '') {
            $meldung = _USERSLANG_THEFIELD_.' '.$fieldname.' '._USERSLANG_ISNOTSET_;
    	}
    	else {    	
            // Zahl
            if($set == 1) {
                $newstring = preg_replace('#[^0-9]#', '', $string);
                if($newstring != $string)
                    $meldung = _USERSLANG_INFIELD_.' '.$fieldname.' '._USERSLANG_ONLYNUMBER_;
            }
            // Username
            elseif($set == 2) {
                $newstring = preg_replace('#[^a-zA-Z0-9_]#', '', $string); //str_replace(' ', '_', $string)
                if($newstring != $string)
                    $meldung = _USERSLANG_WRONGFIELDUSER_;
            }
            // eMail
            elseif($set == 3) {
                $string = strtolower($string);
				
				if(!filter_var($string, FILTER_VALIDATE_EMAIL)) {
					if(function_exists('idn_to_ascii')) {
						$str = explode('@', $string);
						$checked = $str[0].'@'.idn_to_ascii($str[1]);
						if(!filter_var($checked, FILTER_VALIDATE_EMAIL)) {
							$meldung = _USERSLANG_WRONGFIELDMAIL_;
						}
						else {
							$newstring = $checked;
						}
					}
					else {
						$meldung = _USERSLANG_WRONGFIELDMAIL_;
					}
				}
				else {
					$newstring = $string;
				}
				
                /*
			    if(!preg_match('#^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$#', $string))
                    $meldung = _USERSLANG_WRONGFIELDMAIL_;
                else
                    $newstring = $string;
                */
            }
            // Text
            elseif($set == 4) {
                $newstring = htmlentities(preg_replace('#[^/-_ .\'&[:alnum:]]#', '', $string));
                if($newstring != $string)
                    $meldung = _USERSLANG_INFIELD_.' '.$fieldname.' '._USERSLANG_WRONGSIGNS_;
            }
            // Code
            elseif($set == 5) {
                $newstring = preg_replace('#[^a-zA-Z0-9]#', '', $string);
                if($newstring != $string || strlen($newstring) != 32)
                    $meldung = sprintf(_USERSLANG_WRONGTYPE_, $fieldname);
            }
            // Passwort
            elseif($set == 6) {
                $newstring = $string;
				if(!preg_match('#^[a-zA-Z0-9\#\+\-_\*\@\%\&\=\!\?]+$#', $string))
                    $meldung = _USERSLANG_INFIELD_.' '.$fieldname.' '._USERSLANG_WRONGSIGNS_.'<br />'._USERSLANG_WRONGPASS_;
            }
        }
        
        if($meldung != '') {
            $this->meldung = $meldung;
            //return false;
        }
		else {
			return $newstring;
		}
    }
}



