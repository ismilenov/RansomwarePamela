<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/includes/loader.php';

$webutlerupload = new WebutlerAdminClass;
$webutlerupload->config = $webutler_config;

if(!$webutlerupload->checkadmin())
    exit('no access');

require_once $webutlerupload->config['server_path'].'/admin/browser/lang/'.$_SESSION['loggedin']['userlang'].'.php';
$diroptions = $webutlerupload->imgdirlisting();

$popup = '<div class="cke_upload_background_cover"></div>
<div class="cke_pastedupload_options">
	<table border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<td colspan="5"><strong>'._WBLANGADMIN_BROWSER_FILE_.'</strong></td>
			</tr>
			<tr>
				<td colspan="5"><input type="checkbox" id="cke_pastedupload_overwrite" checked="checked" /> <label for="cke_pastedupload_overwrite">'._WBLANGADMIN_BROWSER_OVERWRITEEXIST_.'</label></td>
			</tr>
			<tr>
				<td colspan="5" class="cke_pastedupload_topdist"><strong>'._WBLANGADMIN_BROWSER_TARGETFOLDER_.'</strong></td>
			</tr>
			<tr>
				<td colspan="5">
					<select id="cke_pastedupload_filepath" name="cke_pastedupload_filepath" size="1">
						<option value=""></option>';
						foreach($diroptions as $diroption) {
							$popup.= $diroption;
						}
$popup.= '			</select>
				</td>
			</tr>
			<tr>
				<td colspan="5" class="cke_pastedupload_topdist"><strong>'._WBLANGADMIN_BROWSER_SCAL_.'</strong></td>
			</tr>
			<tr>
				<td colspan="5" class="cke_pastedupload_topdist2">'._WBLANGADMIN_BROWSER_SIZEPAGEIMG_.'</td>
			</tr>
			<tr>
				<td>'._WBLANGADMIN_BROWSER_SIZEIMGWIDTH_.':</td>
				<td><input type="text" id="cke_pastedupload_imgsmallwidth" size="4" maxlength="4" value="'.$webutlerupload->config['imgsmallsize'][0].'" class="cke_upload_input" /> px</td>
				<td> x </td>
				<td>'._WBLANGADMIN_BROWSER_SIZEIMGHEIGHT_.':</td>
				<td><input type="text" id="cke_pastedupload_imgsmallheight" size="4" maxlength="4" value="'.$webutlerupload->config['imgsmallsize'][1].'" class="cke_upload_input" /> px</td>
			</tr>
			<tr>
				<td colspan="5" class="cke_pastedupload_topdist2">'._WBLANGADMIN_BROWSER_SIZEBOXIMG_.'</td>
			</tr>
			<tr>
				<td>'._WBLANGADMIN_BROWSER_SIZEIMGWIDTH_.':</td>
				<td><input type="text" id="cke_pastedupload_imgboxwidth" size="4" maxlength="4" value="'.$webutlerupload->config['imgboxsize'][0].'" class="cke_upload_input" /> px</td>
				<td> x </td>
				<td>'._WBLANGADMIN_BROWSER_SIZEIMGHEIGHT_.':</td>
				<td><input type="text" id="cke_pastedupload_imgboxheight" size="4" maxlength="4" value="'.$webutlerupload->config['imgboxsize'][1].'" class="cke_upload_input" /> px</td>
			</tr>
			<tr>
				<td colspan="5" class="cke_pastedupload_topdist2"><input type="checkbox" id="cke_pastedupload_lightbox" checked="checked" /> <label for="cke_pastedupload_lightbox">'._WBLANGADMIN_BROWSER_LIGHTBOX_.'</label></td>
			</tr>
			<tr>
				<td colspan="5" class="cke_pastedupload_topdist"><input type="button" id="cke_pastedupload_button" value="'._WBLANGADMIN_BROWSER_IMAGESIZESOK_.'" /></td>
			</tr>
		</tbody>
	</table>
</div>';

header('Content-type: application/javascript');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP 1.1
header('Cache-Control: post-check=0, pre-check=0, false'); // HTTP 1.0
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');


echo 'var popupSource = \''.preg_replace('#>(\s)+<#', '><', $popup).'\';'."\n";



