<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


if(preg_match('#/includes/users/sendmail.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

$wbuser_docroot = str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__));
$wbuser_docroot = (substr($wbuser_docroot, -1) == '/') ? substr($wbuser_docroot, 0, strlen($wbuser_docroot)-1) : $wbuser_docroot;

$wbuser_mailtpl = $wbuser_docroot.'/tpls/mail.tpl';
include $wbuser_docroot.'/config.php';

$mailcontent = array();

$filemailtpl = file_get_contents($wbuser_mailtpl);
foreach($users_mailimages as $imgname => $imgvalue) {
	if(substr_count($filemailtpl, '$mailcontent[\''.$imgname.'\']') > 0) {
		$users_file = $wbuser_docroot.'/imgs/'.$imgvalue;
		if($fp = fopen($users_file, 'rb', 0)) {
			$users_img = fread($fp, filesize($users_file));
			fclose($fp);
			$users_size = getimagesize($users_file);
			if($users_size[2] == 1 || $users_size[2] == 2 || $users_size[2] == 3) {
				$users_base64img = chunk_split(base64_encode($users_img));
				$mailcontent[$imgname] = 'data:'.$users_size['mime'].';base64,'.$users_base64img;
			}
		}
	}
}
unset($filemailtpl);

$mailcontent['headline'] = $wbuser_subject;
$mailcontent['text'] = $wbuser_text;

$mailcontent['footer'] = _USERSLANG_MAILFOOTER_ != '' ? htmlentities(_USERSLANG_MAILFOOTER_) : '';

ob_start();
include $wbuser_mailtpl;
$usermail = ob_get_contents();
ob_end_clean();

$wbuser_mail = new PHPMailer();
$wbuser_mail->IsMail();
$wbuser_mail->SetFrom($wbuser_mailfrom);
$wbuser_mail->AddAddress($wbuser_mailto);
$wbuser_mail->Subject = $wbuser_subject;
$wbuser_mail->AltBody = _USERSLANG_HTMLCOMPATIBLE_;
$wbuser_mail->MsgHTML($usermail);
$wbuser_mail->IsHTML(true);

if(!$wbuser_mail->Send()) {
    $webutlercouple->autoheaderdata[] =  '<script>
        alert(\''._USERSLANG_MAILNOTSEND_.'\n'.$wbuser_mail->ErrorInfo.'\');
    </script>';
}


