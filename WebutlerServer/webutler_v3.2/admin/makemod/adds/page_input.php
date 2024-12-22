<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/adds/page_input.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

?>
<form method="post" action="index.php?<?PHP echo $querystr; ?>">
  <table border="0" cellspacing="10" cellpadding="0">
	<tr>
	  <td style="padding-bottom: 10px"><?PHP echo _MAKEMODLANG_PAGE_TPLINPUT_HEADLINE_; ?></td>
	</tr>
	<tr>
	  <td><strong><?PHP echo _MAKEMODLANG_PAGE_TPLINPUT_SHOWFIELDS_; ?>:</strong></td>
	</tr>
    <tr>
	  <td>
        <table id="datafields" border="0" cellspacing="0" cellpadding="5">
        <?PHP
            if(!isset($makemodclass->modid) || $makemodclass->modid == '') $makemodclass->modid = $_GET['mod'];
            echo $makemodclass->loadfields('inputtpl');
        ?>
        </table>
      </td>
    </tr>
	<tr>
	  <td><input type="submit" name="inputsets" class="mmbutton" value="<?PHP echo _MAKEMODLANG_PAGE_SAVE_SETTINGS_; ?>"></td>
	</tr>
  </table>
</form>
