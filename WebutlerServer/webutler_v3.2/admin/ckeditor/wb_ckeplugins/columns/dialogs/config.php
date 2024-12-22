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

if(!$webutlerckplugin->checkadmin())
    exit('no access');

require_once $webutlerckplugin->config['server_path'].'/admin/system/lang/'.$_SESSION['loggedin']['userlang'].'.php';

header('Content-type: application/javascript');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP 1.1
header('Cache-Control: post-check=0, pre-check=0, false'); // HTTP 1.0
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');

?>

var wbcke_setmargintop = '<?PHP echo $webutlerckplugin->config['insertmargin']; ?>';
var wbcke_setmargintext = '<?PHP echo _WBLANGADMIN_COLUMNS_INSERT_PROMT_; ?>';
