<?PHP
/**
* The frame that contains the image to be edited.
* @author Wei Zhuo
* @author Paul Moers <mail@saulmade.nl> - watermarking and replace code + 
* several small enhancements <http://fckplugins.saulmade.nl>
* @version $Id: editframe.php,v 1.7 2006/12/20 18:19:28 thierrybo Exp $
* @package ImageManager
*/

/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(__FILE__))).'/includes/loader.php';

$webutlerimgedit = new WebutlerAdminClass;
$webutlerimgedit->config = $webutler_config;

if(!$webutlerimgedit->checkadmin())
    exit('no access');

require_once $webutlerimgedit->config['server_path'].'/admin/imageedit/lang/'.$_SESSION['loggedin']['userlang'].'.php';

require_once($webutlerimgedit->config['server_path'].'/admin/imageedit/config.php');
require_once($webutlerimgedit->config['server_path'].'/admin/imageedit/classes/files.php');
require_once($webutlerimgedit->config['server_path'].'/admin/imageedit/classes/transform.php');
require_once($webutlerimgedit->config['server_path'].'/admin/imageedit/classes/managerclass.php');
require_once($webutlerimgedit->config['server_path'].'/admin/imageedit/classes/editorclass.php');

$manager = new ImageManager;
$manager->config = $IMConfig;

$editor = new ImageEditor;
$editor->manager = $manager;
$editor->IMConfig = $IMConfig;
$editor->wbconfig = $webutler_config;
$webfonts = $webutlerimgedit->webfonts();

$ckeparams = '';
if(isset($_GET['CKEditor']))
    $ckeparams.= '&CKEditor='.$_GET['CKEditor'];
if(isset($_GET['CKEditorFuncNum']))
    $ckeparams.= '&CKEditorFuncNum='.$_GET['CKEditorFuncNum'];

// get image info and process any action
$iseditfile = $webutlerimgedit->config['server_path'].'/content/media/temp/'.basename($_GET['img']);
if(!file_exists($iseditfile))
{
    $imgfilename = $editor->createUnique(basename($_GET['img']));
    $oldfullpath = $webutlerimgedit->config['server_path'].'/content/media/image'.$_GET['img'];
    if(file_exists($oldfullpath))
    {
        $newfullpath = $webutlerimgedit->config['server_path'].'/content/media/temp/'.$imgfilename;
        $editor->CopyStartFile($oldfullpath, $newfullpath);
        header('Location: editframe.php?img='.$imgfilename.$ckeparams);
    }
    else
    {
        header('Location: editframe.php?img=');
    }
}
else
{
    $imageInfo = $editor->processImage();
}

$watermarkarrays = $editor->watermarkimgs($imageInfo);

?>
<!DOCTYPE html>
<html lang="<?PHP echo isset($_SESSION['loggedin']['userlang']) ? $_SESSION['loggedin']['userlang'] : $webutlerimgedit->config['defaultlang']; ?>">
<head>
<meta charset="UTF-8" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="robots" content="noindex, nofollow" />
<link href="assets/editframe.css" rel="stylesheet" type="text/css" />
<script>
/* <![CDATA[ */
	var messages = new Array(<?PHP echo "'"._WBLANGADMIN_IMGEDIT_MESSAGE_CROP_."','"._WBLANGADMIN_IMGEDIT_MESSAGE_TEXT_."','"._WBLANGADMIN_IMGEDIT_MESSAGE_SCAL_."','"._WBLANGADMIN_IMGEDIT_MESSAGE_FLIP_."','"._WBLANGADMIN_IMGEDIT_MESSAGE_ROTATE_."','"._WBLANGADMIN_IMGEDIT_MESSAGE_SAVE_."','"._WBLANGADMIN_IMGEDIT_MESSAGE_WATERMARK_."','"._WBLANGADMIN_IMGEDIT_MESSAGE_NOFILENAME_."'"; ?>);
/* ]]> */
</script>
<script src="assets/common.js"></script>
<script src="assets/wz_jsgraphics.js"></script>
<script src="assets/wz_dragdrop.js"></script>
<script src="assets/editframe.js"></script>
<script src="assets/content.js"></script>
<script>
/* <![CDATA[ */
	var processedAction = "<?PHP echo (isset($_GET['action']) ? $_GET['action'] : ''); ?>";
	var currentImageFile = "<?PHP if(count($imageInfo)>0) echo rawurlencode($imageInfo['file']); ?>";
	var CKEparams = "<?PHP echo $ckeparams; ?>";

	//show action buttons and current action's controls - were hidden during processing
	if(processedAction != '')
	{
		var buttons = parent.document.getElementById('buttons');
		buttons.style.display = 'block';
	}

<?PHP if($editor->isFileSaved() == 1) { ?>

    /*
	alert(window.top.opener.name);
	var win = parent;
	if(document.all)
		win = parent;
	else
		win = window.top;
    */

    try {
        var CKEopener = window.top.opener.CKEDITOR;
    
    	//Reload editors image
    	if(window.top.opener && (window.top.opener.name == 'CKBrowseNoneDialog' || window.top.opener.name == 'CKBrowseDialog')) //base system is Mediabrowser
        {
    		window.top.opener.location.reload();
    	}
        <?PHP if(isset($_GET['CKEditorFuncNum'])) { ?>
    	else if(CKEopener) //base system is CKEditor
        {
            CKEopener.tools.callFunction( <?PHP echo $_GET['CKEditorFuncNum']; ?>,
            {
                newName : '<?PHP echo $editor->FileNewName(); ?>',
                newWidth : <?PHP echo $imageInfo['width']; ?>,
                newHeight : <?PHP echo $imageInfo['height']; ?>
            });
    	}
        <?PHP } ?>
    } catch(e) {}
	
	<?PHP
		$renamemessage = '';
		if(array_key_exists('rename', $imageInfo) && $imageInfo['rename'] != '') {
			$renamemessage = ' '._WBLANGADMIN_IMGEDIT_SAVED_.': '.$imageInfo['rename'];
		}
	?>
	alert('<?PHP echo _WBLANGADMIN_IMGEDIT_SAVED_.$renamemessage; ?>');
	
	var org_file = parent.document.getElementById('org_file');
	var save_filepath = parent.document.getElementById('save_filepath');
	var save_filename = parent.document.getElementById('save_filename');
	var save_format = parent.document.getElementById('save_format');
	var format;
	if(save_format.options) {
		var formatvalues = save_format.options[save_format.selectedIndex].value;
		var formatvalue = formatvalues.split(',');
		format = formatvalue[0];
	}
	else {
		format = save_format.value;
	}
	var filepath = save_filepath.options ? save_filepath.options[save_filepath.selectedIndex].value : save_filepath.value;
	
	var new_file = '/' + filepath + '/' + <?PHP echo $renamemessage != '' ? "'".$imageInfo['rename']."'" : "save_filename.value + '.' + format"; ?>;
	if(org_file.value != new_file) {
		var redirect = 'tools.php?imgfile=' + encodeURIComponent(new_file) + '<?PHP echo $ckeparams; ?>';
		window.top.location.href = redirect;
	}
	
<?PHP } elseif($editor->isFileSaved() == -1) { ?>
	
    alert('<?PHP echo _WBLANGADMIN_IMGEDIT_NOTSAVED_; ?>');
    
<?PHP } ?>
	
    var watermarkBox = parent.document.getElementById("watermark_file");
    <?PHP
		foreach($watermarkarrays as $watermarkarray) {
			echo $watermarkarray."\n";
		}
    ?>
	
	var watermarkingEnabled;
	if(watermarkBox.options[1]) //show watermark controls
	{
		parent.document.getElementById("watermarkControls").style.display = "block";
		parent.document.getElementById("watermarkMessage").style.display = "none";
		watermarkingEnabled = true;
	}
	
	else //no watermarks found
	{
		parent.document.getElementById("watermarkControls").style.display = "none";
		parent.document.getElementById("watermarkMessage").style.display = "block";
		watermarkingEnabled = false;
	}
/* ]]> */
</script>

<style type="text/css">
/* <![CDATA[ */
<?PHP
    foreach($webfonts as $webfont => $names)
    {
        echo "@font-face {\n";
        echo "  font-family: '".$webfont."';\n";
        foreach($names as $ext => $name) {
            if($ext == 'eot') {
                echo "  src: url('../../../includes/webfonts/".$webfont.".eot');\n";
                echo "  src: url('../../../includes/webfonts/".$webfont.".eot?iefix') format('eot'),\n";
            }
            if($ext == 'ttf') {
                echo "       url('../../../includes/webfonts/".$webfont.".ttf') format('truetype'),\n";
            }
            if($ext == 'woff') {
                echo "       url('../../../includes/webfonts/".$webfont.".woff') format('woff');\n";
            }
        }
        echo "}\n";
    }
?>
/* ]]> */
</style>
</head>

<body>
<table class="editframe">
	<tr>
		<td class="editframe innerframe">
<?PHP if($editor->isGDEditable() == -1) { ?>
			<div class="error"><?PHP echo _WBLANGADMIN_IMGEDIT_NOGIF_; ?><br /><?PHP echo _WBLANGADMIN_IMGEDIT_NOEDIT_; ?></div>
<?PHP } elseif(count($imageInfo) > 0 && is_file($imageInfo['fullpath'])) { ?>
			<div id="ant" class="noselection"><img src="img/blank.gif" id="cropContent" alt="" /></div>
			
			<div id="background" name="background" style="width: <?PHP echo $imageInfo['width']; ?>px; height: <?PHP echo $imageInfo['height']; ?>px; background-image: url('<?PHP echo $imageInfo['src']; ?>');"><img src="img/blank.gif" name="floater" id="floater" /></div>
			
			<div id="TextBG" name="TextBG" style="width: <?PHP echo $imageInfo['width']; ?>px; height: <?PHP echo $imageInfo['height']; ?>px; background-image: url('<?PHP echo $imageInfo['src']; ?>')"><div id="insertText" name="insertText" onmouseup="parent.updatePosition()"></div></div>
			
			<div id="imgCanvas" name="imgCanvas" class="crop"><img src="<?PHP echo $imageInfo['src']; ?>" orgwidth="<?PHP echo $imageInfo['width']; ?>" orgheight="<?PHP echo $imageInfo['height']; ?>" style="width: <?PHP echo $imageInfo['width']; ?>px; height: <?PHP echo $imageInfo['height']; ?>px" name="theImage" id="theImage" /></div>
<?PHP } else { ?>
			<div class="error"><?PHP echo _WBLANGADMIN_IMGEDIT_NOIMAGE_; ?></div>
<?PHP } ?>
		</td>
	</tr>
</table>

<script>
/* <![CDATA[ */
	SET_DHTML("TextBG"+NO_DRAG, "insertText"+CURSOR_MOVE, "background"+NO_DRAG, "floater"+CURSOR_MOVE);

	dd.elements.TextBG.addChild("insertText");
	dd.elements.TextBG.hide(true);
	dd.elements.insertText.hide(true);
	dd.elements.insertText.moveTo(0, 0);
	verify_insertText();

	if (watermarkingEnabled == true) {
		dd.elements.background.addChild("floater");
		dd.elements.floater.swapImage(eval("window.blankPreload.src"));
		dd.elements.floater.resizeTo(1, 1);
        dd.elements.floater.<?PHP echo (isset($_GET['action']) && $_GET['action'] == 'watermark') ? 'show' : 'hide'; ?>(true);
		verifyBounds();
		
		// make sure the slider of the watermark's opacity if at max
		parent.window.updateSlider(100, 'watermark');
	}
	else {
		dd.elements.background.show(true);
		dd.elements.floater.hide(true);
	}

	// hiding parent processing message
	parent.window.hideMessage();
/* ]]> */
</script>
</body>
</html>
