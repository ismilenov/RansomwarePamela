<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


if(preg_match('#/includes/forms/sendform.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');


$forms_langpath = $webutlercouple->config['server_path'].'/includes/forms/lang';
if(isset($_SESSION['language']) && file_exists($forms_langpath.'/'.$_SESSION['language'].'.php'))
    include $forms_langpath.'/'.$_SESSION['language'].'.php';
else
    include $forms_langpath.'/'.$webutlercouple->config['defaultlang'].'.php';

if(count($_POST) > 0 && isset($_POST['sendform']))
{
	$forms_infosend = array();
    
	$forms_sendto = preg_replace('#[^0-9]#', '', $_POST['sendto']);
    if(!isset($forms_sendto) || $forms_sendto == "")
        $forms_sendto = "1";
	
	$postmail = strtolower(trim($_POST['eMail']));
	
	if(!filter_var($postmail, FILTER_VALIDATE_EMAIL)) {
		if(function_exists('idn_to_ascii')) {
			$str = explode('@', $postmail);
			$checked = $str[0].'@'.idn_to_ascii($str[1]);
			if(!filter_var($checked, FILTER_VALIDATE_EMAIL)) {
				$forms_mailfalse = true;
				$forms_infosend[] = _FORMSLANG_VALIDMAIL_;
			}
			else {
				$forms_postmail = $checked;
			}
		}
		elseif(!preg_match('#^[a-zA-Z0-9\.\!\#\$\%\&\'\*\+\/\=\?\^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$#', $forms_postmail)) {
			$forms_mailfalse = true;
			$forms_infosend[] = _FORMSLANG_VALIDMAIL_;
		}
		else {
			$forms_mailfalse = true;
			$forms_infosend[] = _FORMSLANG_VALIDMAIL_;
		}
	}
	else {
		$forms_postmail = $postmail;
	}
	
	unset($_POST['sendform']);
	unset($_POST['sendto']);
	$_post_email = htmlspecialchars($_POST['eMail']);
	unset($_POST['eMail']);
    
    if(!is_array($webutlercouple->mailaddresses) || !array_key_exists('sendto'.$forms_sendto, $webutlercouple->mailaddresses))
    {
        $forms_mailfalse = true;
        $forms_infosend[] = _FORMSLANG_NOADDRESSE_;
    }
    
    if(!isset($forms_mailfalse))
    {
        $forms_docroot = $webutlercouple->config['server_path'].'/includes/forms';
		$forms_usertpl = $forms_docroot.'/tpls/usermail'.(file_exists($forms_docroot.'/tpls/usermail_'.$forms_sendto.'.tpl') ? '_'.$forms_sendto : '').'.tpl';
		$forms_admintpl = $forms_docroot.'/tpls/adminmail'.(file_exists($forms_docroot.'/tpls/adminmail_'.$forms_sendto.'.tpl') ? '_'.$forms_sendto : '').'.tpl';
        require_once $forms_docroot.'/config.php';
		
		$mailcontent = array();
        
    	$count = 0;
		$mailcontent['text'] = array();
    	
    	foreach($_POST as $name => $value) {
    		if($value != "") {
                $mailcontent['text'][$count]['name'] = htmlentities($name);
    			if(is_array($value)) {
					$postvalue = array();
					$mailvalue = array();
    				foreach($value as $val) {
                        $postvalue[] = htmlspecialchars($val);
                        $mailvalue[] = htmlentities($val);
    				}
					$_POST[$name] = $postvalue;
					unset($postvalue);
					$mailcontent['text'][$count]['value'] = implode('<br>', $mailvalue);
    			}
    			else {
					$_POST[$name] = htmlspecialchars($value);
                    $mailcontent['text'][$count]['value'] = htmlentities($value);
    			}
				$count++;
    		}
    	}
		$_POST['eMail'] = $_post_email;
		unset($_post_email);
		
		$fileusertpl = file_get_contents($forms_usertpl);
		$fileadmintpl = file_get_contents($forms_admintpl);
		foreach($forms_mailimages as $imgname => $imgvalue) {
			$forms_file = $forms_docroot.'/imgs/'.$imgvalue;
			if(file_exists($forms_file) && (substr_count($fileusertpl, '$mailcontent[\''.$imgname.'\']') > 0 || substr_count($fileadmintpl, '$mailcontent[\''.$imgname.'\']') > 0)) {
				if($fp = fopen($forms_file, 'rb', 0)) {
					$forms_img = fread($fp, filesize($forms_file));
					fclose($fp);
					$forms_size = getimagesize($forms_file);
					if($forms_size[2] == 1 || $forms_size[2] == 2 || $forms_size[2] == 3) {
						$forms_base64img = chunk_split(base64_encode($forms_img));
						$mailcontent[$imgname] = 'data:'.$forms_size['mime'].';base64,'.$forms_base64img;
					}
				}
			}
		}
		unset($fileusertpl);
		unset($fileadmintpl);
		
        $forms_lang = (isset($_SESSION['language'])) ? $_SESSION['language'] : $webutlercouple->config['defaultlang'];
        $forms_bestaetigungsbetreff = $webutlercouple->mailaddresses['sendto'.$forms_sendto]['bestaetigungsbetreff'];
        $formsbestaetigungsbetreff = (!is_array($forms_bestaetigungsbetreff)) ? $forms_bestaetigungsbetreff : $forms_bestaetigungsbetreff[$forms_lang];
		
		$formssentalert = '';
		if(isset($webutlercouple->mailaddresses['sendto'.$forms_sendto]['sentalert'])) {
			$forms_sentalert = $webutlercouple->mailaddresses['sendto'.$forms_sendto]['sentalert'];
			$formssentalert = (!is_array($forms_sentalert)) ? $forms_sentalert : $forms_sentalert[$forms_lang];
		}
        
		$mailcontent['adminsubject'] = htmlentities($webutlercouple->mailaddresses['sendto'.$forms_sendto]['empfaengerbetreff']);
		$mailcontent['usersubject'] = htmlentities($formsbestaetigungsbetreff);
		
		$mailcontent['footer'] = '';
        if(_FORMSLANG_MAILFOOTER_ != '') {
            $forms_conf_fusslines = explode('<br>', _FORMSLANG_MAILFOOTER_);
			$forms_mailfoot = array();
            foreach($forms_conf_fusslines as $forms_conf_fussline) {
                $forms_mailfoot[] = htmlentities($forms_conf_fussline);
            }
			$mailcontent['footer'] = implode('<br>', $forms_mailfoot);
        }
        
        require_once $webutlercouple->config['server_path'].'/includes/modexts/phpmailer/mailer.php';
		
		ob_start();
		include $forms_admintpl;
		$adminmail = ob_get_contents();
		ob_end_clean();
        
        $forms_mailtohome = new PHPMailer();
        $forms_mailtohome->isMail();
        $forms_mailtohome->setFrom($forms_postmail);
        $forms_mailtohome->addAddress($webutlercouple->mailaddresses['sendto'.$forms_sendto]['empfaengermail'], $webutlercouple->mailaddresses['sendto'.$forms_sendto]['empfaengername']);
        $forms_mailtohome->Subject = $webutlercouple->mailaddresses['sendto'.$forms_sendto]['empfaengerbetreff'];
        $forms_mailtohome->AltBody = _FORMSLANG_HTMLCOMPATIBLE_;
        $forms_mailtohome->msgHTML($adminmail);
        $forms_mailtohome->isHTML(true);
    
        if(!$forms_mailtohome->send()) {
            $forms_infosend[] = _FORMSLANG_MAILNOTSEND_;
			$forms_mailfalse = true;
			if($forms_mailtohome->ErrorInfo != '')
				$forms_infosend[] = $forms_mailtohome->ErrorInfo;
    	}
        else {
            $forms_infosend[] = $formssentalert != '' ? $formssentalert : _FORMSLANG_SENDINGOK_;
            if($webutlercouple->mailaddresses['sendto'.$forms_sendto]['bestaetigung'] == "1") {
				ob_start();
				include $forms_usertpl;
				$usermail = ob_get_contents();
				ob_end_clean();
				
                $forms_mailtouser = new PHPMailer();
                $forms_mailtouser->isMail();
                $forms_mailtouser->setFrom($webutlercouple->mailaddresses['sendto'.$forms_sendto]['empfaengermail'], $webutlercouple->mailaddresses['sendto'.$forms_sendto]['empfaengername']);
                $forms_mailtouser->addAddress($forms_postmail, $forms_postmail);
                $forms_mailtouser->Subject = $formsbestaetigungsbetreff;
                $forms_mailtouser->AltBody = _FORMSLANG_HTMLCOMPATIBLE_;
                $forms_mailtouser->msgHTML($usermail);
                $forms_mailtouser->isHTML(true);
				
                if(!$forms_mailtouser->send()) {
					$forms_infosend[] = _FORMSLANG_CONFIRMNOTSEND_;
					if($forms_mailtouser->ErrorInfo != '')
						$forms_infosend[] = $forms_mailtouser->ErrorInfo;
            	}
            }
			unset($_POST);
			usleep(500000);
        }
    }
    
    if(count($forms_infosend) > 0)
    {
        $forms_headerdata = '<script>
			(function() {
				function forms_infosend() {
					alert(\''.implode('\n', $forms_infosend).'\');'."\n";
		if(!isset($forms_mailfalse)) $forms_headerdata.= 'location.replace(window.location);';
        $forms_headerdata.= "\n".'
				}
				if(window.addEventListener) {
					window.addEventListener(\'load\', forms_infosend, false);    
				} else {
					window.attachEvent(\'onload\', forms_infosend);
				}
			})();
		</script>'."\n";
        $webutlercouple->autoheaderdata[] = $forms_headerdata;
    }
}



