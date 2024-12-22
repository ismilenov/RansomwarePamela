<?PHP
/**
 * The PHP Image Editor user interface.
 * @author Wei Zhuo
 * @author Paul Moers <mail@saulmade.nl> - watermarking and replace code + 
 *  several small enhancements <http://fckplugins.saulmade.nl>
 * @version $Id: tools.php,v 1.3 2006/12/17 13:53:34 thierrybo Exp $
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

$ckeparams = '';
if(isset($_GET['CKEditor']))
    $ckeparams.= '&CKEditor='.$_GET['CKEditor'];
if(isset($_GET['CKEditorFuncNum']))
    $ckeparams.= '&CKEditorFuncNum='.$_GET['CKEditorFuncNum'];

$_GET['imgfile'] = rawurldecode($_GET['imgfile']);

$ext = substr($_GET['imgfile'], strrpos($_GET['imgfile'], '.') + 1);
$theimgfile = basename($_GET['imgfile'], '.'.$ext);
$theimgpath = dirname($_GET['imgfile']);
$selectpath = $theimgpath;
if(substr($selectpath, 0, 1) == '/')
	$selectpath = substr($selectpath, 1, strlen($selectpath));
if(substr($selectpath, -1, 1) == '/')
	$selectpath = substr($selectpath, 0, strlen($selectpath)-1);
$ext = strtolower($ext);

$pagefile = '/'.$selectpath.'/'.$theimgfile.'.'.$ext;
$boxfile = '/'.$selectpath.'/.box/'.$theimgfile.'.'.$ext;

if(isset($_GET['box']) && $_GET['box'] == 'show') {
	$_GET['imgfile'] = $boxfile;
}

$webfonts = $webutlerimgedit->webfonts();
$diroptions = $webutlerimgedit->imgdirlisting(($selectpath != '\\' && $selectpath != '/' ? $selectpath : ''));

?>
<!DOCTYPE html>
<html lang="<?PHP echo isset($_SESSION['loggedin']['userlang']) ? $_SESSION['loggedin']['userlang'] : $webutlerimgedit->config['defaultlang']; ?>">
<head>
<title><?PHP echo _WBLANGADMIN_IMGEDIT_WINTITLE_; ?></title>
<meta charset="UTF-8" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="robots" content="noindex, nofollow" />
<link href="assets/tools.css" rel="stylesheet" type="text/css" />
<link href="<?PHP echo $webutlerimgedit->config['homepage']; ?>/admin/system/css/loading.css" rel="stylesheet" type="text/css" />
<script src="assets/common.js"></script>
<script src="assets/slider.js"></script>
<script src="assets/tools.js"></script>
<script src="assets/jscolor/jscolor.js"></script>
<script src="assets/jscolor/config.js"></script>
</head>
<body scroll="no">
<div id="webutler_loadingscreen">
    <div id="message"></div>
</div>
<table id="toolstable" border="0" cellspacing="10" cellpadding="0">
  <tr>
    <td id="indicator"><img src="img/blank.gif" id="indicatorimg" alt="" /></td>
    <td style="height: 24px">
	<div id="tools">
	<div id="tools_save" style="display:none;">
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="headlines"><?PHP echo _WBLANGADMIN_IMGEDIT_TOOLS_SAVE_; ?>:</td>
			<?PHP
			  $save_filepath = $selectpath != '\\' && $selectpath != '/' ? $selectpath : '';
			  if(isset($_GET['box']) && $_GET['box'] == 'show') {
				if($ext == "jpeg") $ext = "jpg";
			?>
			<td>
			  <?PHP echo $save_filepath.'/'.basename($theimgfile).'.'.$ext; ?>
			  <input type="hidden" id="save_filepath" value="<?PHP echo $save_filepath.'/.box'; ?>" />
			  <input type="hidden" id="save_filename" value="<?PHP echo basename($theimgfile); ?>" />
			  <input type="hidden" id="save_format" value="<?PHP echo $ext; ?>" />
			</td>
			<?PHP } else { ?>
			<td>
			  <?PHP if($save_filepath == 'watermarks' || $save_filepath == 'tpl_icons') { ?>
				  <?PHP echo $save_filepath; ?>
				  <input type="hidden" id="save_filepath" value="<?PHP echo $save_filepath.'/.box'; ?>" />
			  <?PHP } else { ?>
				  <select id="save_filepath" size="1">
					<option value=""></option>
					<?PHP
						foreach($diroptions as $diroption)
							echo $diroption."\n";
					?>
				  </select>
			  <?PHP } ?>
			</td>
			<td>
			  <input type="text" id="save_filename" class="textInput" style="width: 100px" value="<?PHP echo basename($theimgfile); ?>" />
			</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td>Format:</td>
			<td>
			  <select name="format" id="save_format" style="width: 60px" onchange="updateFormat(this, 'save')">
				<option value="jpg,100"<?php if($ext == "jpg" || $ext == "jpeg") { echo ' selected="selected"'; } ?>>JPG</option>
				<option value="jpg,85"><?PHP echo _WBLANGADMIN_IMGEDIT_FORMAT_JPG85_; ?></option>
				<option value="jpg,60"><?PHP echo _WBLANGADMIN_IMGEDIT_FORMAT_JPG60_; ?></option>
				<option value="jpg,35"><?PHP echo _WBLANGADMIN_IMGEDIT_FORMAT_JPG35_; ?></option>
				<option value="png,0"<?PHP if($ext == "png") { echo ' selected="selected"'; } ?>><?PHP echo _WBLANGADMIN_IMGEDIT_FORMAT_PNG_; ?></option>
				<?PHP if($editor->isGDGIFAble() != -1) { ?>
				<option value="gif,0"<?PHP if($ext == "gif") { echo ' selected="selected"'; } ?>><?PHP echo _WBLANGADMIN_IMGEDIT_FORMAT_GIF_; ?></option>
				<?PHP } ?>
			  </select>
			</td>
			<?PHP } ?>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td id="thesaveslider" style="display:none;">
			<table border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td><?PHP echo _WBLANGADMIN_IMGEDIT_TOOLS_QUALITY_; ?>:</td>
				<td>
				  <div id="slidercasingsave" class="slidercasing"> 
					<div id="slidertracksave" class="slidertrack" style="width: 100px"><img src="img/blank.gif" alt="" /></div>
					<div id="sliderbarsave" class="sliderbar" onmousedown="captureStart('save');" onmouseup="UpdateSliderField('save');" style="left: <?PHP echo $webutlerimgedit->config['jpg_quality']; ?>px"></div>
				  </div>
				</td>
				<td>
				  <input type="text" id="sliderfieldsave" class="textInput" onkeyup="UpdateSliderField('save');updateSlider(this.value, 'save');" style="width: 23px; text-align: center" value="<?PHP echo $webutlerimgedit->config['jpg_quality']; ?>" />
				</td>
				<td><img src="img/sep.gif" class="separator" alt="" /></td>
			  </tr>
			</table>
			</td>
			<td>
			  <input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_SAVE_; ?>" onclick="ckeckFilename( document.getElementById('save_filename'), '<?PHP echo _WBLANGADMIN_IMGEDIT_MESSAGE_FILENAME_; ?>' )" />
			  <input type="hidden" id="org_file" value="<?PHP echo isset($_GET['box']) && $_GET['box'] == 'show' ? $boxfile : $pagefile; ?>" />
			  <input type="hidden" id="org_boxfile" value="<?PHP echo file_exists($webutlerimgedit->config['server_path'].'/content/media/image'.$boxfile) ? $boxfile : ''; ?>" />
			</td>
		  </tr>
		</table>
	</div>
	
	<div id="tools_scale" style="display:none;">
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="headlines"><?PHP echo _WBLANGADMIN_IMGEDIT_TOOLS_SIZE_; ?>:</td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_WIDTH_; ?></td>
			<td><input type="text" id="sw" class="textInput" style="width: 30px; text-align: right" onkeyup="checkConstrains('width')" /> px</td>
			<td><a href="javascript:toggleConstraints();" title="<?PHP echo _WBLANGADMIN_IMGEDIT_LOCK_; ?>"><img src="img/locked.gif" id="scaleConstImg" style="height: 14px; width: 7px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_LOCK_; ?>" class="div" /></a></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_HEIGHT_; ?></td>
			<td><input type="text" id="sh" class="textInput" style="width: 30px; text-align: right" onkeyup="checkConstrains('height')" /> px</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td>
			<table border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td><input type="checkbox" id="constProp" value="1" checked="checked" onclick="toggleConstraints()" /></td>
				<td><label for="constProp"><?PHP echo _WBLANGADMIN_IMGEDIT_PROPORTION_; ?></label></td>
			  </tr>
			</table>
			</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_APPLY_; ?>" onclick="editor.doSubmit('scale')" /></td>
			<td><input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_RESET_; ?>" onclick="editor._reset()" /></td>
		  </tr>
		</table>		
	</div>
	
	<div id="tools_flip" style="display:none;">
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="headlines"><?PHP echo _WBLANGADMIN_IMGEDIT_TOOLS_FLIP_; ?>:</td>
			<td>
			<select id="flip" name="flip">
			  <option value="" selected="selected"></option>
			  <option value="hor"><?PHP echo _WBLANGADMIN_IMGEDIT_FLIPHOR_; ?></option>
			  <option value="ver"><?PHP echo _WBLANGADMIN_IMGEDIT_FLIPVER_; ?></option>
			</select>
			</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_APPLY_; ?>" onclick="if(document.getElementById('flip').value != '') editor.doSubmit('flip')" /></td>
		  </tr>
		</table>
	</div>
	
	<div id="tools_rotate" style="display:none;">
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="headlines"><?PHP echo _WBLANGADMIN_IMGEDIT_TOOLS_ROTATE_; ?>:</td>
			<td>
			<select name="rotate" onchange="rotatePreset(this)">
			  <option value=""></option>
			  <option value="180">180&deg; <?PHP echo _WBLANGADMIN_IMGEDIT_ROT180_; ?></option>
			  <option value="90">90&deg; <?PHP echo _WBLANGADMIN_IMGEDIT_ROT90_; ?></option>
			  <option value="-90">90&deg; <?PHP echo _WBLANGADMIN_IMGEDIT_ROTM90_; ?></option>
			</select>
			</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td>Winkel</td>
			<td><input type="text" id="ra" class="textInput" style="width: 40px; text-align: right" value="0" /> °</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_APPLY_; ?>" onclick="editor.doSubmit('rotate')" /></td>
		  </tr>
		</table>
	</div>	
	
	<div id="tools_crop" style="display:none;">
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="headlines"><?PHP echo _WBLANGADMIN_IMGEDIT_TOOLS_CROP_; ?>:</td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_WIDTH_; ?></td>
			<td><input type="text" id="cw" class="textInput" style="width: 30px; text-align: right" onkeyup="updateMarker('crop')" /> px</td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_HEIGHT_; ?></td>
			<td><input type="text" id="ch" class="textInput" style="width: 30px; text-align: right" onkeyup="updateMarker('crop')" /> px</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td>
			  <input type="hidden" id="cx" value="" />
			  <input type="hidden" id="cy" value="" />
			  <input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_APPLY_; ?>" onclick="editor.doSubmit('crop')" />
			</td>
			<td><input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_RESET_; ?>" onclick="editor._reset()" /></td>
		  </tr>
		</table>		
	</div>
	
	<div id="tools_text" style="display:none;">
		<?PHP if(is_array($webfonts)) { ?>
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="headlines"><?PHP echo _WBLANGADMIN_IMGEDIT_TOOLS_TEXT_; ?>:</td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_INPUT_; ?>:</td>
			<td>
			  <input type="hidden" name="text_top" id="text_top" />
			  <input type="hidden" name="text_left" id="text_left" />
              <input type="text" value="" class="textInput" name="text" id="text" style="width: 100px" onkeyup="updateFont()" />
			</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_FONT_; ?>:</td>
			<td>
			<select name="font_family" id="font_family" onchange="updateFont()">
            <?PHP
                foreach($webfonts as $webfont => $names) {
					$fontname = str_replace(array('-', '_', '.'), array(' ', ' ', ' '), $webfont);
					$fontname = ucwords($fontname);
                    echo "<option value=\"".$webfont."\">".$fontname."</option>\n";
				}
            ?>
			</select>
			</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_ANGLE_; ?>:</td>
			<td><input type="text" id="angle" class="textInput" style="width: 40px; text-align: right" value="0" onkeyup="fontAngle()" /> °</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_FONTSIZE_; ?>:</td>
			<td><input type="text" value="18" class="textInput" name="font_size" id="font_size" style="width: 30px; text-align: right" onkeyup="updateFont()" /> px</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_FONTCOLOR_; ?>:</td>
			<td>
			  <input type="hidden" name="font_color" id="font_color" value="808080" onchange="updateFont()" />
			  <input type="button" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_FONTCOLORS_; ?>" class="color {valueElement:'font_color',styleElement:'font_color'}" autocomplete="off" />
			</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td>
			  <input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_APPLY_; ?>" onclick="editor.doSubmit('text')" />
			</td>
		  </tr>
		</table>
		<?PHP } else { ?>
		<div id="nowebfonts">
            <span style="font-weight: bold; letter-spacing: 2px"><?PHP echo _WBLANGADMIN_IMGEDIT_NOTLABELIZE_; ?></span><span style="margin-left: 20px"><?PHP echo _WBLANGADMIN_IMGEDIT_NOWEBFONTS_; ?> /webfonts</span>
		</div>
		<?PHP } ?>
	</div>
	
	<div id="tools_watermark" style="display: none;">
	  <div id="watermarkControls" style="display: none;">
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="headlines"><?PHP echo _WBLANGADMIN_IMGEDIT_TOOLS_WATERMARK_; ?>:</td>
			<td>
			  <select name="watermark_file" id="watermark_file" onchange="changeWatermark(this)"></select>
			</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_TRANSPARENT_; ?>:</td>
			<td>
			  <div id="slidercasingwatermark" class="slidercasing"> 
				<div id="slidertrackwatermark" class="slidertrack" style="width: 100px"></div>
				<div id="sliderbarwatermark" class="sliderbar" onmousedown="captureStart('watermark');" style="left: 100px"></div>
			  </div>
			</td>
			<td><input type="text" class="textInput" id="sliderfieldwatermark" onkeyup="updateSlider(this.value, 'watermark')" style="width: 23px; text-align: center" value="100" /></td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td>
			  <div id="watermarkalign">
				<div style="left: 1px; top: 1px" onmouseover="this.style.backgroundColor='#BE3545'" onmouseout="this.style.backgroundColor='transparent'" onclick="moveWatermark(0, 0);"></div>
				<div style="left: 10px; top: 1px" onmouseover="this.style.backgroundColor='#BE3545'" onmouseout="this.style.backgroundColor='transparent'" onclick="moveWatermark(0.5, 0);"></div>
				<div style="left: 19px; top: 1px" onmouseover="this.style.backgroundColor='#BE3545'" onmouseout="this.style.backgroundColor='transparent'" onclick="moveWatermark(1, 0);"></div>
				<div style="left: 1px; top: 10px" onmouseover="this.style.backgroundColor='#BE3545'" onmouseout="this.style.backgroundColor='transparent'" onclick="moveWatermark(0, 0.5);"></div>
				<div style="left: 10px; top: 10px" onmouseover="this.style.backgroundColor='#BE3545'" onmouseout="this.style.backgroundColor='transparent'" onclick="moveWatermark(0.5, 0.5);"></div>
				<div style="left: 19px; top: 10px" onmouseover="this.style.backgroundColor='#BE3545'" onmouseout="this.style.backgroundColor='transparent'" onclick="moveWatermark(1, 0.5);"></div>
				<div style="left: 1px; top: 19px" onmouseover="this.style.backgroundColor='#BE3545'" onmouseout="this.style.backgroundColor='transparent'" onclick="moveWatermark(0, 1);"></div>
				<div style="left: 10px; top: 19px" onmouseover="this.style.backgroundColor='#BE3545'" onmouseout="this.style.backgroundColor='transparent'" onclick="moveWatermark(0.5, 1);"></div>
				<div style="left: 19px; top: 19px" onmouseover="this.style.backgroundColor='#BE3545'" onmouseout="this.style.backgroundColor='transparent'" onclick="moveWatermark(1, 1);"></div>
			  </div>
			</td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_APPLY_; ?>" onclick="editor.doSubmit('watermark')" /></td>
		  </tr>
		</table>
		</div>	
		<div id="watermarkMessage" style="display: none;">
		  <div id="nowatermark">
            <span style="font-weight: bold; letter-spacing: 2px"><?PHP echo _WBLANGADMIN_IMGEDIT_NOTMARKABLE_; ?></span><span style="margin-left: 20px"><?PHP echo sprintf(_WBLANGADMIN_IMGEDIT_MARKTXT_, '/watermarks'); ?></span>
          </div>
		</div>	
	</div>
	
	<div id="tools_measure" style="display:none;">
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="headlines"><?PHP echo _WBLANGADMIN_IMGEDIT_TOOLS_MEASURE_; ?>:</td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_STARTX_; ?>:<input type="text" class="measureStats" id="sx" readonly="readonly" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_STARTY_; ?>:<input type="text" class="measureStats" id="sy" readonly="readonly" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_WIDTH_; ?>:<input type="text" class="measureStats" id="mw" readonly="readonly" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_HEIGHT_; ?>:<input type="text" class="measureStats" id="mh" readonly="readonly" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_ANGLE_; ?>:<input type="text" class="measureStats" id="ma" readonly="readonly" /></td>
			<td><?PHP echo _WBLANGADMIN_IMGEDIT_DIAGONAL_; ?>:<input type="text" class="measureStats" id="md" readonly="readonly" /></td>
			<td><img src="img/sep.gif" class="separator" alt="" /></td>
			<td><input type="button" class="inputButton" value="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_REMOVE_; ?>" onclick="editor._reset()" /></td>
		  </tr>
		</table>
	</div>
	</div>
	</td>
  </tr>
  <tr>
    <td style="vertical-align: top; width: 26px">
    <div id="toolbar">
	  <div id="buttons">
		<div onclick="toggle('save')" id="icon_save" title="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_SAVE_; ?>"><img src="img/save.gif" style="height: 20px; width: 20px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_SAVE_; ?>" /></div>
		<div onclick="toggle('scale')" id="icon_scale" title="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_SCAL_; ?>"><img src="img/scale.gif" style="height: 20px; width: 20px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_SCAL_; ?>"></div>
		<div onclick="toggle('flip')" id="icon_flip" title="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_FLIP_; ?>"><img src="img/flip.gif" style="height: 20px; width: 20px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_FLIP_; ?>" /></div>
		<div onclick="toggle('rotate')" id="icon_rotate" title="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_ROTATE_; ?>"><img src="img/rotate.gif" style="height: 20px; width: 20px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_ROTATE_; ?>" /></div>
		<div onclick="toggle('crop')" id="icon_crop" title="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_CROP_; ?>"><img src="img/crop.gif" style="height: 20px; width: 20px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_CROP_; ?>" /></div>
		<div onclick="toggle('text')" id="icon_text" title="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_TEXT_; ?>"><img src="img/text.gif" style="height: 20px; width: 20px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_TEXT_; ?>" /></div>
		<div onclick="toggle('watermark')" id="icon_watermark" title="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_WATERMARK_; ?>"><img src="img/watermark.gif" style="height: 20px; width: 20px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_WATERMARK_; ?>" /></div>
		<div onclick="toggle('measure')" id="icon_measure" title="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_MEASURE_; ?>"><img src="img/measure.gif" style="height: 20px; width: 20px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_MEASURE_; ?>" /></div>
		<div onclick="toggleMarker();" title="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_TOGGLEMARKER_; ?>"><img id="markerImg" src="img/t_black.gif" style="height: 20px; width: 20px; border: 0px" alt="<?PHP echo _WBLANGADMIN_IMGEDIT_BUTTONS_TOGGLEMARKER_; ?>" /></div>
	  </div>
    </div>
	</td>
    <td style="vertical-align: top" id="contents">
      <iframe src="editframe.php?img=<?PHP if(isset($_GET['imgfile'])) echo rawurlencode($_GET['imgfile']).$ckeparams; ?>" name="editor" id="editor" onload="toggle('')" scrolling="auto" frameborder="0"></iframe>
	</td>
  </tr>
<?PHP
	if(file_exists($webutlerimgedit->config['server_path'].'/content/media/image'.$boxfile)) {
		echo '<tr><td>&nbsp;</td>';
  
		echo '<td><div id="showbox">';
		if(isset($_GET['box']) && $_GET['box'] == 'show') {
			echo '<a href="tools.php?imgfile='.rawurlencode($pagefile).$ckeparams.'">'._WBLANGADMIN_IMGEDIT_SWITCHFROMBOX_.'</a>';
		}
		else {
			echo '<a href="tools.php?imgfile='.rawurlencode($pagefile).$ckeparams.'&box=show">'._WBLANGADMIN_IMGEDIT_SWITCHTOBOX_.'</a>';
		}
		echo '</div></td></tr>';
	}
?>
</table>
</body>
</html>
