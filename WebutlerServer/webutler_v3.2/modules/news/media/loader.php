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
require_once $webutler->config['server_path'].'/modules/news/data/config.php';

$news_load = new MMViewClass;
$news_load->modname = 'news';
$news_load->serverpath = $webutler->config['server_path'];
$news_load->fileconfig = $news_conf;
$news_load->connectdb();

$id = $news_load->validnum($_GET['id']);
$file = $news_load->validfield($_GET['file']);

if($id == '' || $file == '')
    die("Wrong Parameter");

if(isset($news_load->dbconfig['saveload'][$file][0]) && $news_load->dbconfig['saveload'][$file][0] == 'on') {
    if(!$webutler->checkadmin() && !$news_load->getdownloadgroup($file, $_SESSION['userauth']['groupid']))
        die("No Permission");
}

$filearray = $news_load->downloadfile($id, $file);

if($filearray == '')
    die("File don't exists");

$filename = $news_load->serverpath.'/modules/news/media/files/'.$filearray[0].'/'.$filearray[2].'.'.$filearray[3];

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

@$news_load->readfilechunked($filename) or die("File not found.");



