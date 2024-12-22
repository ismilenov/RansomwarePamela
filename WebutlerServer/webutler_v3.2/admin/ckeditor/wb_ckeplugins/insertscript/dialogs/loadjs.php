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


header('Content-type: application/javascript');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP 1.1
header('Cache-Control: post-check=0, pre-check=0, false'); // HTTP 1.0
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');

function load_folders($serverpath, $scriptsdir) {
    $folders = '';
    $handle = opendir($serverpath.'/'.$scriptsdir);
	while(false !== ($scriptfile = readdir($handle))) {
		if($scriptfile != '.' && $scriptfile != '..' && $scriptfile != 'source') {
		    if(is_dir($serverpath.'/'.$scriptsdir.'/'.$scriptfile.'/')) {
                $folders.= '<ul><li><img src="admin/ckeditor/wb_ckeplugins/insertscript/images/in.gif" /><strong>'.$scriptfile.'</strong>'.load_folders($serverpath, $scriptsdir.'/'.$scriptfile).load_scripts($serverpath, $scriptsdir.'/'.$scriptfile).'</li></ul>';
      	    }
		}
	}
    closedir($handle);
    return $folders;
}

function load_scripts($serverpath, $scriptsdir) {
    $scripts = '';
	$handle = opendir($serverpath.'/'.$scriptsdir);
    $scripts.= '<ul>';
	while(false !== ($scriptfile = readdir($handle))) {
		if($scriptfile != '.' && $scriptfile != '..') {
		    if(!is_dir($serverpath.'/'.$scriptsdir.'/'.$scriptfile.'/')) {
                if(substr($scriptfile, -3) == '.js') {
                    $scripts.= '<li><img src="admin/ckeditor/wb_ckeplugins/insertscript/images/js.gif" /><span onclick="setactive(this);" path="'.$scriptsdir.'/'.$scriptfile.'">'.$scriptfile.'<img src="admin/ckeditor/wb_ckeplugins/insertscript/images/add.gif" style="display: none" /></span></li>';
      	        }
      	    }
		}
	}
    $scripts.= '</ul>';
	closedir($handle);
	return $scripts;
}

?>

function loadjs_folders()
{
    return '<?PHP echo load_folders($webutlerckplugin->config['server_path'], 'includes/javascript'); ?>';
}

function loadjs_scripts()
{
    return '<?PHP echo load_scripts($webutlerckplugin->config['server_path'], 'includes/javascript'); ?>';
}

function setactive(elem)
{
    var isActive = elem.getAttribute('active');
    if(isActive == 'yes') {
        elem.removeAttribute('active');
        elem.style.backgroundColor = '#ffffff';
        elem.style.borderColor = '#ffffff';
        elem.getElementsByTagName('img')[0].style.display = 'none';
    }
    else {
        elem.setAttribute('active', 'yes', 0);
        elem.style.backgroundColor = '#DFF1FF';
        elem.style.borderColor = '#316AC5';
        elem.getElementsByTagName('img')[0].style.display = 'inline';
    }
}
