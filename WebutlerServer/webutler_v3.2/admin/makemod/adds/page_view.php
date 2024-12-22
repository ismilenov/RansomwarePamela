<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/adds/page_view.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

?>
<form method="post" action="index.php?<?PHP echo $querystr; ?>">
  <table border="0" cellspacing="10" cellpadding="0">
	<tr>
	  <td colspan="2"><strong><?PHP echo _MAKEMODLANG_PAGE_VIEW_HEADLINE_; ?>:</strong></td>
	</tr>
    <tr>
      <td style="width: 15px; padding-left: 5px; line-height: 16px"><img src="adds/icons/<?PHP echo isset($viewdata['cats']) ? $viewdata['cats'] : ''; ?>.png" /></td>
      <td><?PHP echo _MAKEMODLANG_PAGE_VIEW_SHOWCATS_; ?></td>
	</tr>
    <tr>
      <td style="padding-left: 5px; line-height: 16px"><img src="adds/icons/<?PHP echo isset($viewdata['topics']) ? $viewdata['topics'] : ''; ?>.png" /></td>
      <td><?PHP echo _MAKEMODLANG_PAGE_VIEW_SHOWTOPICS_; ?></td>
	</tr>
    <tr>
      <td style="padding-left: 5px; line-height: 16px"><img src="adds/icons/ok.png" /></td>
      <td><?PHP echo _MAKEMODLANG_PAGE_VIEW_SHOWDATALIST_; ?></td>
	</tr>
    <tr>
      <td style="line-height: 16px"><input type="checkbox" name="full"<?PHP echo isset($viewdata['full']) ? $viewdata['full'] : ''; ?> id="full" /></td>
      <td><label for="full"><?PHP echo _MAKEMODLANG_PAGE_VIEW_SHOWDATA_; ?></label></td>
	</tr>
    <tr>
      <td style="line-height: 16px"><input type="checkbox" name="newest"<?PHP echo isset($viewdata['newest']) ? $viewdata['newest'] : ''; ?> id="newest" /></td>
      <td><label for="newest"><?PHP echo _MAKEMODLANG_PAGE_VIEW_SHOWNEWEST_; ?></label></td>
	</tr>
    <tr>
      <td colspan="2" style="padding-top: 7px"><strong><?PHP echo _MAKEMODLANG_PAGE_VIEW_USERINPUT_; ?></strong></td>
	</tr>
    <tr>
      <td style="line-height: 16px<?PHP if(isset($viewdata['newtopics']) && $viewdata['newtopics'] == 'disabled') { ?>; padding-left: 5px"><img src="adds/icons/no.png" /><?PHP } else { ?>"><input type="checkbox" name="newtopics"<?PHP echo isset($viewdata['newtopics']) ? $viewdata['newtopics'] : ''; ?> id="newtopics" onclick="viewchange('newtopics')" /><?PHP } ?></td>
      <td><label for="newtopics"><?PHP echo _MAKEMODLANG_PAGE_VIEW_CREATENEWTOPIC_; ?></label></td>
	</tr>
    <tr>
      <td style="line-height: 16px"><input type="checkbox" name="newdata"<?PHP echo isset($viewdata['newdata']) ? $viewdata['newdata'] : ''; ?> id="newdata" onclick="viewchange('newdata')" /></td>
      <td><label for="newdata"><?PHP echo _MAKEMODLANG_PAGE_VIEW_INPUTUNDERLIST_; ?></label></td>
	</tr>
    <tr>
      <td style="line-height: 16px"><input type="checkbox" name="newlink"<?PHP echo isset($viewdata['newlink']) ? $viewdata['newlink'] : ''; ?> id="newlink" onclick="viewchange('newlink')" /></td>
      <td><label for="newlink"><?PHP echo _MAKEMODLANG_PAGE_VIEW_LINKTOINPUT_; ?></label></td>
	</tr>
    <tr>
      <td colspan="2" style="padding-top: 7px"><strong><?PHP echo _MAKEMODLANG_PAGE_VIEW_SETFILTER_; ?></strong></td>
	</tr>
    <tr>
      <td style="line-height: 16px"><input type="checkbox" name="filter"<?PHP echo isset($viewdata['filter']) ? $viewdata['filter'] : ''; ?> id="filter" /></td>
      <td><label for="filter"><?PHP echo _MAKEMODLANG_PAGE_VIEW_SETOWNSQL_; ?></label></td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
	  <td colspan="4" style="padding-top: 7px"><input type="submit" name="layoutsets" class="mmbutton" value="<?PHP echo _MAKEMODLANG_PAGE_SAVE_SETTINGS_; ?>"></td>
	</tr>
  </table>
</form>
