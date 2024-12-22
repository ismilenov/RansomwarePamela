<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/adds/page_load.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

?>
  <table id="startwork" border="0" cellspacing="0" cellpadding="20">
	<tr>
	  <td>
        <form method="post" action="index.php">
          <table border="0" cellspacing="10" cellpadding="0">
        	<tr>
        	  <td colspan="2"><strong><?PHP echo _MAKEMODLANG_PAGE_LOAD_LOADPROJECT_; ?></strong></td>
        	</tr>
        	<tr>
        	  <td><?PHP echo _MAKEMODLANG_PAGE_LOAD_MODNAME_; ?>:</td>
        	  <td>
                <select name="loadmod" size="1" class="mmselect">
                  <?PHP
                    foreach($makemodclass->listmods() as $listmod) {
                        echo $listmod."\n";
                    }
                  ?>
                </select>
              </td>
        	</tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td><input type="submit" value="<?PHP echo _MAKEMODLANG_PAGE_LOAD_OPENPROJECT_; ?>" class="mmbutton" /></td>
        	</tr>
          </table>
        </form>
	  </td>
	  <td>
        <form method="post" action="index.php">
          <table border="0" cellspacing="10" cellpadding="0">
        	<tr>
        	  <td colspan="2"><strong><?PHP echo _MAKEMODLANG_PAGE_LOAD_NEWPROJECT_; ?></strong></td>
        	</tr>
        	<tr>
        	  <td><?PHP echo _MAKEMODLANG_PAGE_LOAD_MODNAME_; ?>:</td>
        	  <td><input type="text" name="createmod" class="mminput" /></td>
        	</tr>
        	<tr>
        	  <td>&nbsp;</td>
        	  <td><input type="submit" value="<?PHP echo _MAKEMODLANG_PAGE_LOAD_CREATEPROJECT_; ?>" class="mmbutton" /></td>
        	</tr>
          </table>
        </form>
	  </td>
	</tr>
  </table>

