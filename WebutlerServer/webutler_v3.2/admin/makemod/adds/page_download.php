<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/adds/page_download.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

?>
<form method="post" action="index.php?<?PHP echo $querystr; ?>">
  <table border="0" cellspacing="10" cellpadding="0">
	<tr>
	  <td><strong><?PHP echo _MAKEMODLANG_PAGE_DOWNLOAD_HEADLINE_; ?></strong></td>
	</tr>
	<tr>
	  <td><?PHP echo _MAKEMODLANG_PAGE_DOWNLOAD_TEXT_; ?></td>
	</tr>
	<tr>
	  <td style="padding-top: 10px"><input type="submit" name="makezipfile" class="mmbutton" value="<?PHP echo sprintf(_MAKEMODLANG_PAGE_DOWNLOAD_MAKEMOD_, $makemodclass->modname); ?>" onclick="showloader();"></td>
	</tr>
  </table>
</form>
