<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/includes/loader.php';

$webutlerckplugin = new WebutlerAdminClass;
$webutlerckplugin->config = $webutler_config;
$webutlerckplugin->mailaddresses = $webutler_mailaddresses;

if(!$webutlerckplugin->checkadmin())
    exit('no access');


header('Content-type: application/javascript');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP 1.1
header('Cache-Control: post-check=0, pre-check=0, false'); // HTTP 1.0
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');


echo "var SendToAddresses = new Array( new Array( '', '' )";
if(count($webutlerckplugin->mailaddresses) > 0)
{
    foreach($webutlerckplugin->mailaddresses as $mailaddress => $element)
    {
        $mailid = str_replace('sendto', '', $mailaddress);
    	echo ", new Array( 'ID: ".$mailid." - ".$element['empfaenger']."', '".$mailid."' )";
    }
}
echo " );\n";

?>
