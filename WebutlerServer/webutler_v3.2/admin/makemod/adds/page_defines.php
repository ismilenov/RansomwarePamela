<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/adds/page_defines.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

?>
<form method="post" action="index.php?<?PHP echo $querystr; ?>">
  <table border="0" cellspacing="10" cellpadding="0">
	<tr>
	  <td><strong><?PHP echo _MAKEMODLANG_PAGE_DEFINES_NEWDATAFIELD_; ?>:</strong></td>
	</tr>
	<tr>
	  <td><?PHP echo _MAKEMODLANG_PAGE_DEFINES_NEWFIELDTXT_; ?></td>
	</tr>
	<tr>
	  <td>
        <table border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td><?PHP echo _MAKEMODLANG_PAGE_DEFINES_FIELDNAME; ?>:</td>
            <td><input type="text" name="fieldname" class="mminput" value="<?PHP echo isset($_POST['fieldname']) ? $_POST['fieldname'] : ''; ?>" /></td>
          </tr>
          <tr>
            <td><?PHP echo _MAKEMODLANG_PAGE_DEFINES_FIELDINPUT_; ?>:</td>
            <td><input type="text" name="fieldinput" class="mminput" value="<?PHP echo isset($_POST['fieldinput']) ? $_POST['fieldinput'] : ''; ?>" /></td>
          </tr>
          <tr>
            <td><?PHP echo _MAKEMODLANG_PAGE_DEFINES_FIELDTYPE_; ?>:</td>
            <td>
              <select name="fieldtype" size="1" class="mmselect">
                <?PHP
                  foreach($makemodclass->listfieldtypes((isset($_POST['fieldtype']) ? $_POST['fieldtype'] : '')) as $listfieldtype) {
                    echo $listfieldtype;
                  }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="newfield" class="mmbutton" value="<?PHP echo _MAKEMODLANG_PAGE_DEFINES_CREATEFIELD_; ?>" /></td>
          </tr>
        </table>
      </td>
	</tr>
  </table>
</form>
  <table border="0" cellspacing="10" cellpadding="0" style="margin-top: 20px">
	<tr>
	  <td><strong><?PHP echo _MAKEMODLANG_PAGE_DEFINES_EXISTFIELDS_; ?>:</strong></td>
	</tr>
    <tr>
	  <td>
        <table id="datafields" border="0" cellspacing="0" cellpadding="5">
        <?PHP
            if(!isset($makemodclass->modid) || $makemodclass->modid == '') $makemodclass->modid = $_GET['mod'];
            echo $makemodclass->loadfields('define');
        ?>
        </table>
      </td>
    </tr>
  </table>
