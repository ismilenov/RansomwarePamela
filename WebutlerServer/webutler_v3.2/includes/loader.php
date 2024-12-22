<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/includes/loader.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

header('content-type: text/html; charset=utf-8');

require_once dirname(dirname(__FILE__)).'/settings/baseconfig.php';

if($webutler_config['server_path'] == '' && $webutler_config['homepage'] == '' && 
  $webutler_config['user_name'] == '' && $webutler_config['user_pass'] == '') {
    header('Location: admin/install.php');
    exit;
}

$webutler_autoheaderdata = array();
$webutler_autofooterdata = array();

require_once $webutler_config['server_path'].'/settings/globalvars.php';
require_once $webutler_config['server_path'].'/settings/extradata.php';
require_once $webutler_config['server_path'].'/includes/wbclass.php';


$webutler_offlinepages = array();
$webutler_mailaddresses = array();
$webutler_langconf = array();
$webutler_categories = array();
$webutler_linkhighlite = array();
$webutler_moduleslist = array();
if(file_exists($webutler_config['server_path'].'/content/access/offline.php'))
    require_once $webutler_config['server_path'].'/content/access/offline.php';

if(file_exists($webutler_config['server_path'].'/content/access/mailaddresses.php'))
    require_once $webutler_config['server_path'].'/content/access/mailaddresses.php';

if(file_exists($webutler_config['server_path'].'/content/access/languages.php'))
    require_once $webutler_config['server_path'].'/content/access/languages.php';

if(file_exists($webutler_config['server_path'].'/content/access/categories.php'))
    require_once $webutler_config['server_path'].'/content/access/categories.php';

if(file_exists($webutler_config['server_path'].'/content/access/linkhighlite.php'))
    require_once $webutler_config['server_path'].'/content/access/linkhighlite.php';

if(file_exists($webutler_config['server_path'].'/settings/modulebox.php'))
    require_once $webutler_config['server_path'].'/settings/modulebox.php';

$_MMVAR = array();

function webutler_setinivalues($var, $val) {
    if(ini_get($var) != $val && ini_set($var, $val) !== false) {
        ini_set($var, $val);
    }
}

webutler_setinivalues('session.auto_start', 0);
webutler_setinivalues('session.cookie_httponly', 1);
webutler_setinivalues('session.use_cookies', 1);
webutler_setinivalues('session.use_only_cookies', 1);
webutler_setinivalues('session.use_trans_sid', 0);

session_name('WEBUTLER');
session_start();


