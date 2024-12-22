<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/adds/page_admin.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

?>
<form method="post" action="index.php?<?PHP echo $querystr; ?>">
  <table border="0" cellspacing="10" cellpadding="0">
	<tr>
	  <td colspan="2"><strong><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SETTINGS_; ?>:</strong></td>
	</tr>
    <tr>
      <td style="width: 15px"><input type="checkbox" name="subedit" id="subedit"<?PHP echo isset($admindata['subedit']) ? $admindata['subedit'] : ''; ?> /></td>
      <td><label for="subedit"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SETSUBEDITORS_; ?></label> (<?PHP echo _MAKEMODLANG_PAGE_ADMIN_REQUIREUSERS_; ?>)</td>
	</tr>
    <tr>
      <td><input type="checkbox" name="byuser" id="byuser"<?PHP echo isset($admindata['byuser']) ? $admindata['byuser'] : ''; ?> /></td>
      <td><label for="byuser"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SETPERMISSION_; ?></label> (<?PHP echo _MAKEMODLANG_PAGE_ADMIN_REQUIREUSERS_; ?>)</td>
	</tr>
    <tr>
      <td><input type="checkbox" name="bylang" id="bylang" onclick="showmultilang()"<?PHP echo isset($admindata['bylang']) ? $admindata['bylang'] : ''; ?> /></td>
      <td><label for="bylang"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_DATABYLANG_; ?></label> (<?PHP echo _MAKEMODLANG_PAGE_ADMIN_REQUIREMULTILANG_; ?>)</td>
	</tr>
	<tr id="datamultilang" style="display: none">
      <td>&nbsp;</td>
	  <td>
		<table style="margin-top: -5px" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td><input type="checkbox" name="multilang" id="multilang"<?PHP echo isset($admindata['multilang']) ? $admindata['multilang'] : ''; ?> /></td>
		    <td style="padding-left: 5px"><label for="multilang"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_DATAMULTILANG_; ?></label></td>
		  </tr>
		</table>
	  </td>
	</tr>
    <tr>
      <td><input type="checkbox" name="modcats" id="modcats"<?PHP echo isset($admindata['modcats']) ? $admindata['modcats'] : ''; ?> onclick="showcatsets()" /></td>
      <td><label for="modcats"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SHOWCATS_; ?></label></td>
	</tr>
    <tr id="catsettings" style="display: none">
      <td>&nbsp;</td>
	  <td>
        <table style="margin-top: -10px" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td colspan="3" style="vertical-align: top">
				<table class="catfields" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td><input type="checkbox" name="basecat" id="setbasecat"<?PHP echo isset($admindata['basecat']) ? $admindata['basecat'] : ''; ?> /></td>
					<td><label for="setbasecat"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_BASECATS_; ?></label></td>
				  </tr>
				</table>
			</td>
		  </tr>
          <tr>
            <td style="vertical-align: top">
				<table class="catfields" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td colspan="2"><strong><?PHP echo _MAKEMODLANG_PAGE_ADMIN_CATFIELDS_; ?>:</strong></td>
				  </tr>
				  <tr>
					<td><input type="radio" name="cats" value="catname"<?PHP if(!isset($admindata['cats']) || (isset($admindata['cats']) && $admindata['cats'] == 'catname')) echo ' checked="checked"'; ?> id="modcatset1" onclick="showcatscal()" /></td>
					<td><label for="modcatset1"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_CATNAME_; ?></label></td>
				  </tr>
				  <tr>
					<td><input type="radio" name="cats" value="catname_catimage"<?PHP if(isset($admindata['cats']) && $admindata['cats'] == 'catname_catimage') echo ' checked="checked"'; ?> id="modcatset2" onclick="showcatscal()" /></td>
					<td><label for="modcatset2"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_CATNAMEIMG_; ?></label></td>
				  </tr>
				  <tr>
					<td><input type="radio" name="cats" value="catname_cattext"<?PHP if(isset($admindata['cats']) && $admindata['cats'] == 'catname_cattext') echo ' checked="checked"'; ?> id="modcatset3" onclick="showcatscal()" /></td>
					<td><label for="modcatset3"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_CATNAMETXT_; ?></label></td>
				  </tr>
				  <tr>
					<td><input type="radio" name="cats" value="catname_catimage_cattext"<?PHP if(isset($admindata['cats']) && $admindata['cats'] == 'catname_catimage_cattext') echo ' checked="checked"'; ?> id="modcatset4" onclick="showcatscal()" /></td>
					<td><label for="modcatset4"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_CATNAMEIMGTXT_; ?></label></td>
				  </tr>
				  <tr>
					<td><input type="checkbox" name="catsort" id="catsort"<?PHP echo isset($admindata['catsort']) ? $admindata['catsort'] : ''; ?> onclick="viewchange('catsort')" /></td>
					<td><label for="catsort"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_CATSORTHAND_; ?></label></td>
				  </tr>
				  <tr>
					<td><input type="checkbox" name="subcats" id="subcats"<?PHP echo isset($admindata['subcats']) ? $admindata['subcats'] : ''; ?> onclick="viewchange('subcats')" /></td>
					<td><label for="subcats"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_CATSORTSUBS_; ?></label></td>
				  </tr>
				</table>
            </td>
            <td style="width: 50px">&nbsp;</td>
            <td id="catscal" style="vertical-align: top; display: none">
				<table class="catfields" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td colspan="3"><strong><?PHP echo _MAKEMODLANG_PAGE_IMAGESCAL_; ?>:</strong></td>
				  </tr>
				  <tr>
					<td><?PHP echo _MAKEMODLANG_PAGE_LIGHTBOX_; ?></td>
					<td><input type="text" class="mminput" style="width: 25px" name="catopts[cat][box][width]" value="<?PHP echo isset($admindata['catopts']['cat']['box']['width']) ? $admindata['catopts']['cat']['box']['width'] : ''; ?>" /> px <?PHP echo _MAKEMODLANG_PAGE_IMGWIDTH_; ?></td>
					<td><input type="text" class="mminput" style="width: 25px" name="catopts[cat][box][height]" value="<?PHP echo isset($admindata['catopts']['cat']['box']['height']) ? $admindata['catopts']['cat']['box']['height'] : ''; ?>" /> px <?PHP echo _MAKEMODLANG_PAGE_IMGHEIGHT_; ?></td>
				  </tr>
				  <tr>
					<td><?PHP echo _MAKEMODLANG_PAGE_ADMIN_CATLISTIMG_; ?></td>
					<td><input type="text" class="mminput" style="width: 25px" name="catopts[cat][view][width]" value="<?PHP echo isset($admindata['catopts']['cat']['view']['width']) ? $admindata['catopts']['cat']['view']['width'] : ''; ?>" /> px <?PHP echo _MAKEMODLANG_PAGE_IMGWIDTH_; ?></td>
					<td><input type="text" class="mminput" style="width: 25px" name="catopts[cat][view][height]" value="<?PHP echo isset($admindata['catopts']['cat']['view']['height']) ? $admindata['catopts']['cat']['view']['height'] : ''; ?>" /> px <?PHP echo _MAKEMODLANG_PAGE_IMGHEIGHT_; ?></td>
				  </tr>
				</table>
            </td>
          </tr>
		  <tr id="showcatsasmenu"<?PHP if(!isset($admindata['subcats']) || $admindata['subcats'] == '') echo ' style="display: none"'; ?>>
			<td colspan="3" style="padding: 0px">
			  <table class="catfields" border="0" cellspacing="0" cellpadding="0">
				<tr>
				  <td style="padding-left: 25px"><input type="checkbox" name="catmenu" id="catmenu"<?PHP echo isset($admindata['catmenu']) ? $admindata['catmenu'] : ''; ?> /></td>
				  <td><label for="catmenu"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SHOWCATSMENU_; ?></label></td>
				</tr>
			  </table>
			</td>
		  </tr>
        </table>
      </td>
	</tr>
    <tr>
      <td style="width: 15px"><input type="checkbox" name="topics"<?PHP echo isset($admindata['topics']) ? $admindata['topics'] : ''; ?> id="topics" onclick="showtopicsets()" /></td>
      <td><label for="topics"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SHOWTOPICS_; ?></label></td>
	</tr>
    <tr id="topicsettings" style="display: none">
      <td>&nbsp;</td>
	  <td>
        <table style="margin-top: -10px" class="topicfields" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" style="padding-bottom: 7px"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_DATASTOTOPIC_; ?></td>
          </tr>
		  <tr>
			<td style="padding: 0px 0px 7px 5px"><img src="adds/icons/ok.png" /></td>
			<td><?PHP echo _MAKEMODLANG_PAGE_ADMIN_TOPICINPUT_; ?> <?PHP echo _MAKEMODLANG_PAGE_ADMIN_TOPICHEADLINE_; ?> (<?PHP echo _MAKEMODLANG_FIELDTYPE_TEXTLINE_; ?>) <?PHP echo _MAKEMODLANG_PAGE_ADMIN_SHOWTOPICHEAD_; ?></td>
		  </tr>
		  <tr>
			<td><input type="checkbox" name="disttopicstart" id="disttopicstart"<?PHP echo isset($admindata['disttopicstart']) ? $admindata['disttopicstart'] : ''; ?> onclick="setdisttopicstart()" /></td>
			<td><label for="disttopicstart"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_DISTTOPICSTART_; ?></label></td>
          </tr>
		  <tr>
			<td><input type="checkbox" name="copytopictocat" id="copytopictocat"<?PHP echo isset($admindata['copytopictocat']) ? $admindata['copytopictocat'] : ''; ?> /></td>
			<td><label for="copytopictocat"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_COPYTOPICTOCAT_; ?></label></td>
		  </tr>
		  <tr>
			<td><input type="checkbox" name="topicsort" id="topicsort"<?PHP echo isset($admindata['topicsort']) ? $admindata['topicsort'] : ''; ?> onclick="viewchange('topicsort')" /></td>
			<td><label for="topicsort"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_TOPICSORTHAND_; ?></label></td>
		  </tr>
		  <tr>
			<td><input type="checkbox" name="sorttopicfield" id="sorttopicfield"<?PHP echo isset($admindata['sorttopicfield']) ? $admindata['sorttopicfield'] : ''; ?> onclick="viewchange('sorttopicfield')" /></td>
			<td><label for="sorttopicfield"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_TOPICSORTFIELD_; ?><span id="sorttopicfieldtitle" style="display: none"> <?PHP echo _MAKEMODLANG_PAGE_ADMIN_TOPICSORTFIELDTITLE_; ?></span><span id="sorttopicfieldofstart" style="display: none"> <?PHP echo _MAKEMODLANG_PAGE_ADMIN_TOPICSORTFIELDOFSTART_; ?></span></label></td>
		  </tr>
		  <tr>
			<td><input type="checkbox" name="breaktopic" id="breaktopic"<?PHP echo isset($admindata['breaktopic']) ? $admindata['breaktopic'] : ''; ?> onclick="viewchange('breaktopic')" /></td>
			<td><label for="breaktopic"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_FROMTOTOPIC_; ?></label></td>
		  </tr>
        </table>
      </td>
	</tr>
	<tr>
	  <td style="padding-left: 3px"><img src="adds/icons/ok.png" /></td>
	  <td><?PHP echo _MAKEMODLANG_PAGE_ADMIN_DATACREATE_; ?></td>
	</tr>
	<tr>
	  <td><input type="checkbox" name="copydatatocat" id="copydatatocat"<?PHP echo isset($admindata['copydatatocat']) ? $admindata['copydatatocat'] : ''; ?> /></td>
	  <td><label for="copydatatocat"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_COPYDATATOCAT_; ?></label></td>
	</tr>
	<tr>
	  <td><input type="checkbox" name="copydatatotopic" id="copydatatotopic"<?PHP echo isset($admindata['copydatatotopic']) ? $admindata['copydatatotopic'] : ''; ?> /></td>
	  <td><label for="copydatatotopic"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_COPYDATATOTOPIC_; ?></label></td>
	</tr>
    <tr>
      <td><input type="checkbox" name="datasort" id="datasort"<?PHP echo isset($admindata['datasort']) ? $admindata['datasort'] : ''; ?> onclick="viewchange('datasort')" /></td>
      <td><label for="datasort"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_DATASORTHAND_; ?></label></td>
	</tr>
    <tr>
      <td><input type="checkbox" name="sortdatafield" id="sortdatafield"<?PHP echo isset($admindata['sortdatafield']) ? $admindata['sortdatafield'] : ''; ?> onclick="viewchange('sortdatafield')" /></td>
      <td><label for="sortdatafield"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_DATASORTFIELD_; ?></label></td>
	</tr>
    <tr>
      <td><input type="checkbox" name="breakdata" id="breakdata"<?PHP echo isset($admindata['breakdata']) ? $admindata['breakdata'] : ''; ?> onclick="viewchange('breakdata')" /></td>
      <td><label for="breakdata"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_FROMTODATA_; ?></label></td>
	</tr>
    <tr>
      <td><input type="checkbox" name="options" id="options"<?PHP echo isset($admindata['options']) ? $admindata['options'] : ''; ?> /></td>
      <td><label for="options"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_OPTIONS_; ?></label></td>
	</tr>
    <tr>
      <td><input type="checkbox" name="autolightbox" id="autolightbox"<?PHP echo isset($admindata['autolightbox']) ? $admindata['autolightbox'] : ''; ?> /></td>
      <td><label for="autolightbox"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_AUTOLIGHTBOX_; ?></label></td>
	</tr>
    <tr>
      <td><input type="checkbox" name="seo" id="seo" onclick="showseofields()"<?PHP echo isset($admindata['seo']) ? $admindata['seo'] : ''; ?> /></td>
      <td><label for="seo"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SEOFIELDS_; ?></label></td>
	</tr>
	<tr id="seofieldsfor" style="display: none">
      <td>&nbsp;</td>
	  <td>
		<table style="margin-top: -10px" class="seofields" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td colspan="2" style="padding-bottom: 7px"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SEOINPUTS_.'<br />'._MAKEMODLANG_PAGE_ADMIN_SEOTEXT_; ?></td>
		  </tr>
		  <tr>
		    <td style="width: 15px"><input type="checkbox" name="seocats" id="seocats"<?PHP echo isset($admindata['seocats']) ? $admindata['seocats'] : ''; ?> /></td>
		    <td><label for="seocats"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SEOCATS_; ?></label></td>
		  </tr>
		  <tr>
		    <td><input type="checkbox" name="seotopics" id="seotopics"<?PHP echo isset($admindata['seotopics']) ? $admindata['seotopics'] : ''; ?> /></td>
		    <td><label for="seotopics"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SEOTOPICS_; ?></label></td>
		  </tr>
		  <tr>
		    <td><input type="checkbox" name="seodatas" id="seodatas"<?PHP echo isset($admindata['seodatas']) ? $admindata['seodatas'] : ''; ?> /></td>
		    <td><label for="seodatas"><?PHP echo _MAKEMODLANG_PAGE_ADMIN_SEODATAS_; ?></label></td>
		  </tr>
		</table>
	  </td>
	</tr>
	<tr>
      <td>&nbsp;</td>
	  <td style="padding-top: 7px"><input type="submit" name="adminsets" class="mmbutton" value="<?PHP echo _MAKEMODLANG_PAGE_SAVE_SETTINGS_; ?>"></td>
	</tr>
  </table>
</form>
