<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/includes/loader.php';

$webutler = new WebutlerClass;
$webutler->config = $webutler_config;

require_once $webutler->config['server_path'].'/includes/mmclass.php';
require_once $webutler->config['server_path'].'/modules/###MODULENAME###/data/config.php';

$###MODULENAME###_load = new MMViewClass;
$###MODULENAME###_load->modname = '###MODULENAME###';
$###MODULENAME###_load->serverpath = $webutler->config['server_path'];
$###MODULENAME###_load->fileconfig = $###MODULENAME###_conf;
$###MODULENAME###_load->connectdb();

$id = $###MODULENAME###_load->validnum($_GET['id']);
$file = $###MODULENAME###_load->validfield($_GET['file']);

if($id == '' || $file == '')
    die("Wrong Parameter");

if(isset($###MODULENAME###_load->dbconfig['saveload'][$file][0]) && $###MODULENAME###_load->dbconfig['saveload'][$file][0] == 'on') {
    if(!$webutler->checkadmin() && !$###MODULENAME###_load->getdownloadgroup($file, $_SESSION['userauth']['groupid']))
        die("No Permission");
}

$filearray = $###MODULENAME###_load->downloadfile($id, $file);

if($filearray == '')
    die("File don't exists");

$filename = $###MODULENAME###_load->serverpath.'/modules/###MODULENAME###/media/files/'.$filearray[0].'/'.$filearray[2].'.'.$filearray[3];

if(!file_exists($filename))
    die("File not found");

switch($filearray[3])
{
    case "pdf": $ctype="application/pdf"; break;
    case "exe": $ctype="application/octet-stream"; break;
    case "zip": $ctype="application/zip"; break;
    case "doc": $ctype="application/msword"; break;
    case "xls": $ctype="application/vnd.ms-excel"; break;
    case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
    case "flv": $ctype="video/x-flv"; break;
    case "gif": $ctype="image/gif"; break;
    case "png": $ctype="image/png"; break;
    case "jpe":
    case "jpeg":
    case "jpg": $ctype="image/jpg"; break;
    default: $ctype="application/force-download";
}

header("Pragma: public");
header("Cache-Control: private", false);
header("Content-Type: ".$ctype);
header("content-disposition: attachment; filename=".$filearray[1].";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".@filesize($filename));

@$###MODULENAME###_load->readfilechunked($filename) or die("File not found.");



