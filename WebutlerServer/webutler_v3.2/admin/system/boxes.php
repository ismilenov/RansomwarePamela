<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(__FILE__))).'/includes/loader.php';

$webutlerboxlayer = new WebutlerAdminClass;
$webutlerboxlayer->config = $webutler_config;
$webutlerboxlayer->htmlsource = $webutler_htmlsource;
$webutlerboxlayer->offlinepages = $webutler_offlinepages;
$webutlerboxlayer->mailaddresses = $webutler_mailaddresses;
$webutlerboxlayer->langconf = $webutler_langconf;
$webutlerboxlayer->categories = $webutler_categories;
$webutlerboxlayer->linkhighlite = $webutler_linkhighlite;
$webutlerboxlayer->moduleslist = $webutler_moduleslist;

$webutlerboxlayer->verifygetpage();

if(!$webutlerboxlayer->checkadmin())
    exit('no access');

require_once $webutlerboxlayer->config['server_path'].'/admin/system/lang/'.$_SESSION['loggedin']['userlang'].'.php';


if(isset($_POST['webutler_newpage']))
{
	$newpage = '';
	if($webutlerboxlayer->config['adminnewpage'] == '1' || ($webutlerboxlayer->config['adminnewpage'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
    {
        $buildedselect_menus = $webutlerboxlayer->buildselect('menus', '');
        $buildedselect_layouts = $webutlerboxlayer->buildselect('layouts', '');
        
        $newpage.= '<div id="webutler_newpage">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_ADDPAGE_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="3">'._WBLANGADMIN_WIN_ADDPAGE_TXT_1_.' ';
		if($buildedselect_layouts != '')
        {
            $newpage.= _WBLANGADMIN_WIN_ADDPAGE_TXT_2_.' ';
        }
        $newpage.= '<br />'._WBLANGADMIN_WIN_ADDPAGE_TXT_3_;
		if($buildedselect_menus != '')
        {
            $newpage.= '<br />'._WBLANGADMIN_WIN_ADDPAGE_TXT_4_;
        }
        $newpage.= '</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_ADDPAGE_PAGENAME_.':</strong></td>'."\n".
		'<td style="width: 180px"><input type="text" name="filename" value="'._WBLANGADMIN_WIN_ADDPAGE_PAGEVALUE_.'" class="webutler_input" />'."\n".
		'</td>'."\n".
		'<td>'."\n";
		if($buildedselect_layouts == '')
        {
            $newpage.= ' <input type="hidden" name="newfrom" />';
        }
        else
        {
            $newpage.= '<table border="0" cellpadding="0" cellspacing="0">'."\n".
            '<tr>'."\n".
            '<td><input type="checkbox" name="newfrom" id="webutler_newfrom" onclick="WBeditbox_copyfrom()" /></td>'."\n".
            '<td> <label for="webutler_newfrom">'._WBLANGADMIN_WIN_ADDPAGE_COPYOF_.'</label></td>'."\n".
            '</tr>'."\n".
            '</table>'."\n";
        }
        $newpage.= '</td>'."\n".
		'</tr>'."\n";
		if($buildedselect_layouts != '')
        {
    		$newpage.= '<tr id="webutler_layoutrow">'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADDPAGE_LAYOUT_.':</strong></td>'."\n".
    		'<td>'."\n".
    		'<select name="layout" id="webutler_layout" size="1" class="webutler_select">'."\n".
        	'<option value="" selected="selected">&nbsp;</option>'."\n";
            $newpage.= $buildedselect_layouts;
    		$newpage.= '</select>'."\n".
    		'</td>'."\n".
    		'<td>'."\n".
    		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'tplfile=\' + document.getElementById(\'webutler_layout\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" />'."\n".
    		'</td>'."\n".
    		'</tr>'."\n";
		}
		$newpage.= '<tr';
		if($buildedselect_layouts != '')
        {
            $newpage.= ' id="webutler_copyrow" style="display: none"';
        }
		$newpage.= '>'."\n".'<td><strong>'._WBLANGADMIN_WIN_ADDPAGE_DUPLICAT_.':</strong></td>'."\n".
		'<td>'."\n".
		'<select name="copy" id="webutler_copy" size="1" class="webutler_select">'."\n";
        $newpage.= $webutlerboxlayer->buildselect('pages', '');
		$newpage.= '</select>'."\n".
		'</td>'."\n".
		'<td>'."\n".
		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'pagefile=\' + document.getElementById(\'webutler_copy\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" />'."\n".
		'</td>'."\n".
		'</tr>'."\n";
        if($webutlerboxlayer->config['languages'] == '1' && array_key_exists('code', $webutlerboxlayer->langconf))
        {
    		$newpage.= '<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADDPAGE_LANGUAGE_.':</strong></td>'."\n".
    		'<td>'."\n".
    		'<select name="pagelang" id="webutler_pagelang" size="1" class="webutler_select">'."\n";	
            $langoptions = $webutlerboxlayer->langselect();	
            foreach($langoptions as $langoption) {
        		$newpage.= $langoption."\n";
            }
    		$newpage.= '</select>'."\n".
    		'</td>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'</tr>'."\n";
        }
        if($webutlerboxlayer->config['modrewrite'] == '1' && $webutlerboxlayer->config['categories'] == '1' && array_key_exists('cats', $webutlerboxlayer->categories))
        {
    		$newpage.= '<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADDPAGE_CATEGORIE_.':</strong></td>'."\n".
            '<td><select name="pagecategory" size="1" class="webutler_select">'."\n";
            $catsselectoptions = $webutlerboxlayer->catsselect();
            if(count($catsselectoptions) == 0) {
                $newpage.= '<option value="" selected="selected" disabled="disabled">'._WBLANGADMIN_WIN_PAGECATS_NOCATEGORIES_.'</option>'."\n";
            }
            else {
                $newpage.= '<option value=""></option>'."\n";
                foreach($catsselectoptions as $catsselectoption)
                {
                    $newpage.= $catsselectoption."\n";
                }
            }
            $newpage.= '</select></td>'."\n".
    		''."\n".
    		'<td>&nbsp;</td>'."\n".
    		'</tr>'."\n";
        }
        if($buildedselect_menus != '')
        {
    		$newpage.= '<tr>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td colspan="2">'."\n".
            '<table border="0" cellpadding="0" cellspacing="0">'."\n".
            '<tr>'."\n".
            '<td><input type="checkbox" name="check_auto" id="webutler_check_auto" value="on" onclick="WBeditbox_auto_check()" /></td>'."\n".
            '<td> <label for="webutler_check_auto">'._WBLANGADMIN_WIN_ADDPAGE_AUTOLINK_.'</label></td>'."\n".
            '</tr>'."\n".
            '</table>'."\n".
            '</td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'<table id="webutler_sub_auto" width="100%" border="0" cellspacing="0" cellpadding="5" style="display: none">'."\n".
    		'<tr>'."\n".
    		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_ADDPAGE_MENUNAME_.':</strong></td>'."\n".
    		'<td style="width: 180px">'."\n".
    		'<select name="menu_auto" id="webutler_menu_auto" size="1" class="webutler_select">'."\n";
            $newpage.= $buildedselect_menus;
    		$newpage.= '</select>'."\n".
    		'</td>'."\n".
    		'<td>'."\n".
    		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'menufile=\' + document.getElementById(\'webutler_menu_auto\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" />'."\n".
    		'</td>'."\n".
    		'</tr>'."\n";
    		$newpage.= '<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADDPAGE_LINKNAME_.':</strong></td>'."\n".
    		'<td><input type="text" name="name_auto" value="'._WBLANGADMIN_WIN_ADDPAGE_LINKVALUE_.'" class="webutler_input" /></td>'."\n".
    		'<td>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADDPAGE_LINKPOS_.':</strong></td>'."\n".
    		'<td><input type="text" name="pos_auto" value="1" class="webutler_input" style="width: 30px" /></td>'."\n".
    		'</tr>'."\n";
    	}
		$newpage.= '</table>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td style="width: 135px">&nbsp;</td>'."\n".
		'<td style="padding-bottom: 10px"><input type="submit" name="savenewfile" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	}
	echo $newpage;
}

if(isset($_POST['webutler_delpage']))
{
    $delpage = '';
	if($webutlerboxlayer->config['admindelpage'] == '1' || ($webutlerboxlayer->config['admindelpage'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
    {
        $delpage.= '<div id="webutler_delpage">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_DELPAGE_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post" name="delform">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="2" style="padding-right: 15px"><strong style="letter-spacing: 2px !important">'._WBLANGADMIN_WIN_DELPAGE_ATTENTION_.'</strong> '._WBLANGADMIN_WIN_DELPAGE_TXT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_DELPAGE_PAGENAME_.':</strong></td>'."\n".
		'<td>'."\n";
        /*
		'<select name="delfile" id="webutler_delfile" size="1" class="webutler_select">'."\n";	
        $delpage.= $webutlerboxlayer->buildselect('pages', $webutlerboxlayer->filenamesigns($_POST['locationpage']));
		$delpage.= '</select>'."\n".
		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'pagefile=\' + document.getElementById(\'webutler_delfile\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
        */
        $delpagename = $webutlerboxlayer->filenamesigns($_POST['locationpage']);
		$delpage.= $delpagename;
        $delpage.= '<input type="hidden" value="'.$delpagename.'" name="delfile" id="webutler_delfile" />'.
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td colspan="2" class="webutler_delnoundo"><strong>'._WBLANGADMIN_WIN_DELPAGE_NOUNDO_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td style="padding-bottom: 10px"><input type="submit" name="deletefile" value="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" onclick="return WBeditbox_requestdelete(\''._WBLANGADMIN_POPUPWIN_PAGEDELETE_.'\', document.getElementById(\'webutler_delfile\').value);" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	}
	echo $delpage;
}

if(isset($_POST['webutler_rename']))
{
    $rename = '';
	if($webutlerboxlayer->config['adminpagename'] == '1' || ($webutlerboxlayer->config['adminpagename'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1')) {
    $rename.= '<div id="webutler_rename">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_RENAME_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post" name="renameform">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="2" style="padding-right: 15px"><strong style="letter-spacing: 2px !important">'._WBLANGADMIN_WIN_RENAME_ATTENTION_.'</strong> '._WBLANGADMIN_WIN_RENAME_TXT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_RENAME_OLDNAME_.':</strong></td>'."\n".
		'<td>';
        /*
		'<select name="oldpagename" id="webutler_oldpagename" size="1" class="webutler_select">'."\n";	
        $rename.= $webutlerboxlayer->buildselect('pages', $webutlerboxlayer->filenamesigns($_POST['locationpage']));
		$rename.= '</select>'."\n".
		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'pagefile=\' + document.getElementById(\'webutler_pagename\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
        */
        $oldpagename = $webutlerboxlayer->filenamesigns($_POST['locationpage']);
		$rename.= $oldpagename;
        $rename.= '<input type="hidden" value="'.$oldpagename.'" name="oldpagename" id="webutler_oldpagename" />'.
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_RENAME_NEWNAME_.':</strong></td>'."\n".
		'<td>'."\n".
		'<input type="text" value="" id="webutler_newpagename" name="newpagename" class="webutler_input" />'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td style="padding-bottom: 10px"><input type="submit" name="saverename" value="'._WBLANGADMIN_WIN_BUTTONS_MODIFY_.'" onclick="return WBeditbox_requestrename(\''._WBLANGADMIN_POPUPWIN_PAGERENAME_.'\', document.getElementById(\'webutler_oldpagename\').value, document.getElementById(\'webutler_newpagename\').value);" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
    }
	echo $rename;
}

if(isset($_POST['webutler_checkbak']))
{
    $post_file = $webutlerboxlayer->filenamesigns($_POST['file']);
    if($_POST['folder'] == 'menu' || $_POST['folder'] == 'block' || $_POST['folder'] == 'page') {
        $post_folder = $_POST['folder'];
        $backfile = $webutlerboxlayer->config['server_path'].'/content/'.$post_folder.'s/'.$post_file.'.bak';
    	if(file_exists($backfile))
    	{
            echo 'exists###'._WBLANGADMIN_WIN_EDIT_BAKOF_.': '.date(_WBLANGADMIN_POPUPWIN_VERSION_DATEFORMAT_, filemtime($backfile)).'###'._WBLANGADMIN_WIN_BUTTONS_EDIT_.'###'._WBLANGADMIN_WIN_BUTTONS_RENEW_;
    	}
    	else
    	{
            echo 'isnot###'._WBLANGADMIN_WIN_EDIT_NOBACKUP_.'###'._WBLANGADMIN_WIN_BUTTONS_EDIT_;
    	}
    }
}

if(isset($_POST['webutler_newmenu']))
{
    $buildedselect_menus = $webutlerboxlayer->buildselect('menus', '');
    $newmenu = '';
    if($buildedselect_menus != '')
    {
	$newmenu.= '<div id="webutler_newmenu">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_EDITMENU_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="2">'._WBLANGADMIN_WIN_EDITMENU_TXT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_EDITMENU_MENU_.':</strong></td>'."\n".
		'<td>'."\n".
		'<select name="menu" id="webutler_menubox" size="1" onchange="WBeditbox_checkbak(\'menu\')" class="webutler_select">'."\n";		
        $newmenu.= $buildedselect_menus;
		$newmenu.= '</select>'."\n".
		'<input type="button" id="webutler_menubakpreview" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" class="webutler_button" style="margin-left: 20px" />'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_EDIT_BACKUP_.':</strong></td>'."\n".
		'<td>'."\n".
        '<table border="0" cellpadding="0" cellspacing="0">'."\n".
        '<tr>'."\n".
        '<td><input type="checkbox" id="webutler_menubakcheck" name="lastmenu" /></td>'."\n".
        '<td> <label id="webutler_menubaklabel" for="webutler_menubakcheck"></label></td>'."\n".
        '</tr>'."\n".
        '</table>'."\n".
        '</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td style="padding-bottom: 10px"><input type="submit" id="webutler_menubakbutton" value="'._WBLANGADMIN_WIN_BUTTONS_EDIT_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	}
	echo $newmenu;
}

if(isset($_POST['webutler_newblock']))
{
    $buildedselect_blocks = $webutlerboxlayer->buildselect('blocks', '');
	$newblock = '';
    if($buildedselect_blocks != '')
    {
	$newblock.= '<div id="webutler_newblock">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_EDITBLOCK_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="2">'._WBLANGADMIN_WIN_EDITBLOCK_TXT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_EDITBLOCK_BLOCK_.':</strong></td>'."\n".
		'<td>'."\n".
		'<select name="block" id="webutler_blockbox" size="1" onchange="WBeditbox_checkbak(\'block\')" class="webutler_select">'."\n";
        $newblock.= $buildedselect_blocks;
		$newblock.= '</select>'."\n".
		'<input type="button" id="webutler_blockbakpreview" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" class="webutler_button" style="margin-left: 20px" />'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_EDIT_BACKUP_.':</strong></td>'."\n".
		'<td>'."\n".
        '<table border="0" cellpadding="0" cellspacing="0">'."\n".
        '<tr>'."\n".
        '<td><input type="checkbox" id="webutler_blockbakcheck" name="lastblock" /></td>'."\n".
        '<td> <label id="webutler_blockbaklabel" for="webutler_blockbakcheck"></label></td>'."\n".
        '</tr>'."\n".
        '</table>'."\n".
        '</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td style="padding-bottom: 10px"><input type="submit" id="webutler_blockbakbutton" value="'._WBLANGADMIN_WIN_BUTTONS_EDIT_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	}
	echo $newblock;
}

if(isset($_POST['webutler_otherpage']))
{
	$otherpage = '<div id="webutler_otherpage">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_OTHERPAGE_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="2">'._WBLANGADMIN_WIN_OTHERPAGE_TXT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_OTHERPAGE_PAGENAME_.':</strong></td>'."\n".
		'<td>'."\n".
		'<select id="webutler_editselected" size="1" class="webutler_select">'."\n";
        $otherpage.= $webutlerboxlayer->buildselect('pages', $webutlerboxlayer->filenamesigns($_POST['locationpage']));
		$otherpage.= '</select>'."\n".
		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'pagefile=\' + document.getElementById(\'webutler_editselected\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td style="padding-bottom: 10px"><input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_CALL_.'" class="webutler_button webutler_mainbutton" onclick="window.location.href=\'index.php?page=\' + document.getElementById(\'webutler_editselected\').value" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	echo $otherpage;
}

if(isset($_POST['webutler_pagelang']))
{
    $pagelang = '';
    if($webutlerboxlayer->config['languages'] == '1')
    {
	$pagelang.= '<div id="webutler_pagelang">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_PAGELANG_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="2">'._WBLANGADMIN_WIN_PAGELANG_TXT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_PAGELANG_LANGUAGE_.':</strong></td>'."\n".
		'<td>'."\n".		
		'<select name="changelang" id="webutler_changelang" size="1" class="webutler_select">'."\n";	
        $langoptions = $webutlerboxlayer->langselect($webutlerboxlayer->getlangfrompage($webutlerboxlayer->filenamesigns($_POST['locationpage'])));	
        foreach($langoptions as $langoption) {
    		$pagelang.= $langoption."\n";
        }
		$pagelang.= '</select>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td style="padding-bottom: 10px"><input type="submit" name="savepagelang" value="'._WBLANGADMIN_WIN_BUTTONS_MODIFY_.'" onclick="return WBeditbox_requestchange(\''._WBLANGADMIN_POPUPWIN_LANGUAGE_.'\', document.getElementById(\'webutler_changelang\').value);" class="webutler_button webutler_mainbutton"';
        $pagelang.= (count($langoptions) == 1) ? ' disabled="disabled"' : '';
        $pagelang.= ' /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	}
	echo $pagelang;
}

if(isset($_POST['webutler_pagecats']))
{
    $pagecat = '';
    if($webutlerboxlayer->config['categories'] == '1')
    {
	$pagecat.= '<div id="webutler_pagecats">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_PAGECATS_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="2">'._WBLANGADMIN_WIN_PAGECATS_TXT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_PAGECATS_CATEGORIE_.':</strong></td>'."\n".
		'<td>'."\n".		
		'<select name="newcategory" id="webutler_newcategory" size="1" class="webutler_select">'."\n";
        $catsselectoptions = $webutlerboxlayer->catsselect($webutlerboxlayer->filenamesigns($_POST['locationpage']));
        if(count($catsselectoptions) == 0) {
            $pagecat.= '<option value="" selected="selected" disabled="disabled">'._WBLANGADMIN_WIN_PAGECATS_NOCATEGORIES_.'</option>'."\n";
        }
        else {
            $pagecat.= '<option value=""></option>'."\n";
            foreach($catsselectoptions as $catsselectoption)
            {
                $pagecat.= $catsselectoption."\n";
            }
        }
		$pagecat.= '</select>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td><input type="hidden" name="oldcategory" id="webutler_oldcategory" value="" /></td>'."\n".
		'<td style="padding-bottom: 10px"><input type="submit" name="savepagecategory" value="'._WBLANGADMIN_WIN_BUTTONS_MODIFY_.'" onclick="return WBeditbox_requestrename(\''._WBLANGADMIN_POPUPWIN_CATREQUEST_.'\', document.getElementById(\'webutler_oldcategory\').value, document.getElementById(\'webutler_newcategory\').value);" class="webutler_button webutler_mainbutton"';
        $pagecat.= (count($catsselectoptions) == 0) ? ' disabled="disabled"' : '';
        $pagecat.= ' /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	}
	echo $pagecat;
}

if(isset($_POST['webutler_linkhighlite']))
{
    $highlite = '';
    
    $buildedselect_menus = $webutlerboxlayer->buildselect('menus', '');
	$highlite.= '<div id="webutler_linkhighlite">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_HIGHLITES_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
    	'<td style="padding-left: 15px" class="webutler_hidesubtrslinks"><span onclick="WBeditbox_hidehighlitetrs(\'files\',\'folder\')">'._WBLANGADMIN_WIN_HIGHLITES_FILESTR_.'</span>';
        if($webutlerboxlayer->config['modrewrite'] == '1' && $webutlerboxlayer->config['categories'] == '1' && array_key_exists('cats', $webutlerboxlayer->categories)) {
            $highlite.= ' | <span onclick="WBeditbox_hidehighlitetrs(\'folder\',\'files\')">'._WBLANGADMIN_WIN_HIGHLITES_FOLDERSTR_.'</span>';
        }
        $highlite.= '</td>'."\n".
        '</tr>'."\n".
		'<tr class="webutler_trhighfiles"';
        $highlite.= (isset($_POST['highfoldertr'])) ? ' style="display: none"' : '';
        $highlite.= '>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table style="width: 100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="2">'._WBLANGADMIN_WIN_HIGHLITES_TXT_FILES_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_HIGHLITES_CLASS_.'</strong></td>'."\n".
		'<td><input type="text" name="highlitefileclass" class="webutler_input" value="" /></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td><strong>'._WBLANGADMIN_WIN_HIGHLITES_MENU_.'</strong></td>'."\n".
		'<td>'."\n".
		'<select name="highlitefilemenu" size="1" class="webutler_select">'."\n";
        //'<option value=""></option>'."\n";
        $highlite.= $buildedselect_menus;
		$highlite.= '</select>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td>'.
		'<table border="0" cellspacing="0" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td><input type="checkbox" name="highlitefileparents" id="webutler_highparents" value="on" /></td>'."\n".
		'<td><label for="webutler_highparents">'._WBLANGADMIN_WIN_HIGHLITES_PARENTSTR_.'</label></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
        '<td style="padding-bottom: 10px"><input type="submit" name="savehighlitefile" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n";
        if(count($webutlerboxlayer->linkhighlite['files']) > 0) {
    		$highlite.= '<tr class="webutler_trhighfiles"';
            $highlite.= (isset($_POST['highfoldertr'])) ? ' style="display: none"' : '';
    		$highlite.= '>'."\n".
            '<td colspan="2" class="webutler_advancedhr"><hr /></td>'."\n".
    		'</tr>'."\n".
    		'<tr class="webutler_trhighfiles"';
            $highlite.= (isset($_POST['highfoldertr'])) ? ' style="display: none"' : '';
            $highlite.= '>'."\n".
    		'<td colspan="2" style="padding-bottom: 0px">'._WBLANGADMIN_WIN_HIGHLITES_AVAILABLE_.'</td>'."\n".
    		'</tr>'."\n".
    		'<tr class="webutler_trhighfiles"';
            $highlite.= (isset($_POST['highfoldertr'])) ? ' style="display: none"' : '';
            $highlite.= '>'."\n".
    		'<td colspan="2">'."\n".
    		'<table class="webutler_availclasses" border="0" cellspacing="3" cellpadding="0" align="center">'."\n".
    		'<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_HIGHLITES_CLASS_.'</strong></td>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_HIGHLITES_MENU_.'</strong></td>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'</tr>'."\n";
            foreach($webutlerboxlayer->linkhighlite['files'] as $filekey => $linkhighlitefile) {
        		$highlite.= '<tr>'."\n".
        		'<td>'.$linkhighlitefile[0].'</td>'."\n".
        		'<td>'.$linkhighlitefile[1].'</td>'."\n";
				if(isset($linkhighlitefile[2]) && $linkhighlitefile[2] == 'yes') {
					$titlehighliteparent = _WBLANGADMIN_WIN_HIGHLITES_PARENTSYES_;
					$highliteicon = 'yes.gif';
				}
				else {
					$titlehighliteparent = _WBLANGADMIN_WIN_HIGHLITES_PARENTSNO_;
					$highliteicon = 'no.gif';
				}
                $highlite.= '<td><img src="admin/system/images/'.$highliteicon.'" title="'.$titlehighliteparent.'" /></td>'."\n".
				'<td><input type="button" name="'.$filekey.'" value="'._WBLANGADMIN_WIN_HIGHLITES_DELETE_.'" class="webutler_button webutler_delhighlitefile" /></td>'."\n".
        		'</tr>'."\n";
            }
    		$highlite.= '</table>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n";
        }
		$highlite.= '</table>'."\n".
        '</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n";
        if($webutlerboxlayer->config['modrewrite'] == '1' && $webutlerboxlayer->config['categories'] == '1' && array_key_exists('cats', $webutlerboxlayer->categories)) {
    		$highlite.= '<tr class="webutler_trhighfolder"';
            $highlite.= (!isset($_POST['highfoldertr'])) ? ' style="display: none"' : '';
            $highlite.= '>'."\n".
    		'<td style="padding-left: 10px">'."\n".
    		'<form class="webutler_boxesform" method="post">'."\n".
    		'<div class="webutler_winmeldung"></div>'."\n".
    		'<table style="width: 100%" border="0" cellspacing="0" cellpadding="5">'."\n".
    		'<tr>'."\n".
    		'<td colspan="2">'._WBLANGADMIN_WIN_HIGHLITES_TXT_FOLDERS_.'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_HIGHLITES_CLASS_.'</strong></td>'."\n".
    		'<td><input type="text" name="highlitefolderclass" class="webutler_input" value="" /></td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_HIGHLITES_CATEGORIE_.'</strong></td>'."\n".
    		'<td>'."\n".		
    		'<select name="highlitefoldercat" size="1" class="webutler_select">'."\n";
            foreach($webutlerboxlayer->catsselect() as $catsselectoption)
            {
                $highlite.= $catsselectoption."\n";
            }
    		$highlite.= '</select>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_HIGHLITES_MENU_.'</strong></td>'."\n".
    		'<td>'."\n".		
    		'<select name="highlitefoldermenu" size="1" class="webutler_select">'."\n";
            $highlite.= $buildedselect_menus."\n";
    		$highlite.= '</select>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
			'<tr>'."\n".
			'<td>&nbsp;</td>'."\n".
			'<td>'."\n".
			'<table border="0" cellspacing="0" cellpadding="0">'."\n".
			'<tr>'."\n".
			'<td><input type="checkbox" name="highlitefoldercurrent" id="webutler_highcurrent" value="on" /></td>'."\n".
			'<td><label for="webutler_highcurrent">'._WBLANGADMIN_WIN_HIGHLITES_CURRENTSTR_.'</label></td>'."\n".
			'<tr>'."\n".
			'</table>'."\n".
			'</td>'."\n".
			'</tr>'."\n".
			'<tr>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td style="padding-bottom: 10px"><input type="submit" name="savehighlitefolder" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
    		'</tr>'."\n";
            if(count($webutlerboxlayer->linkhighlite['folders']) > 0) {
        		$highlite.= '<tr class="webutler_trhighfolder"';
                $highlite.= (!isset($_POST['highfoldertr'])) ? ' style="display: none"' : '';
                $highlite.= '>'."\n".
        		'<td colspan="2" class="webutler_advancedhr"><hr /></td>'."\n".
        		'</tr>'."\n".
        		'<tr class="webutler_trhighfolder"';
                if(!isset($_POST['highfoldertr'])) {
                    $highlite.= ' style="display: none"';
                }
                $highlite.= '>'."\n".
        		'<td colspan="2" style="padding-bottom: 0px">'._WBLANGADMIN_WIN_HIGHLITES_AVAILABLE_.'</td>'."\n".
        		'</tr>'."\n".
        		'<tr class="webutler_trhighfolder"';
                $highlite.= (!isset($_POST['highfoldertr'])) ? ' style="display: none"' : '';
                $highlite.= '>'."\n".
        		'<td colspan="2">'."\n".
        		'<table class="webutler_availclasses" border="0" cellspacing="3" cellpadding="0" align="center">'."\n".
        		'<tr>'."\n".
        		'<td><strong>'._WBLANGADMIN_WIN_HIGHLITES_CLASS_.'</strong></td>'."\n".
        		'<td><strong>'._WBLANGADMIN_WIN_HIGHLITES_CATEGORIE_.'</strong></td>'."\n".
        		'<td><strong>'._WBLANGADMIN_WIN_HIGHLITES_MENU_.'</strong></td>'."\n".
				'<td>&nbsp;</td>'."\n".
        		'<td>&nbsp;</td>'."\n".
        		'</tr>'."\n";
                foreach($webutlerboxlayer->linkhighlite['folders'] as $folderkey => $linkhighlitefolder) {
            		$highlite.= '<tr>'."\n".
            		'<td>'.$linkhighlitefolder[0].'</td>'."\n".
            		'<td>'.$linkhighlitefolder[1].'</td>'."\n".
            		'<td>'.$linkhighlitefolder[2].'</td>'."\n";
					if(isset($linkhighlitefolder[3]) && $linkhighlitefolder[3] == 'yes') {
						$titlehighlitecurrent = _WBLANGADMIN_WIN_HIGHLITES_CURRENTYES_;
						$highlitecurrenticon = 'yes';
					}
					else {
						$titlehighlitecurrent = _WBLANGADMIN_WIN_HIGHLITES_CURRENTNO_;
						$highlitecurrenticon = 'no';
					}
                    $highlite.= '<td><img src="admin/system/images/'.$highlitecurrenticon.'.gif" title="'.$titlehighlitecurrent.'" /></td>'."\n".
            		'<td><input type="button" name="'.$folderkey.'" value="'._WBLANGADMIN_WIN_HIGHLITES_DELETE_.'" class="webutler_button webutler_delhighlitefolder" /></td>'."\n".
            		'</tr>'."\n";
                }
        		$highlite.= '</table>'."\n".
        		'</td>'."\n".
        		'</tr>'."\n";
            }
    		$highlite.= '</table>'."\n".
            '</form>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n";
        }
		$highlite.= '</table>'."\n".
		'</div>'."\n";
	
	echo $highlite;
}

if(isset($_POST['webutler_newconf'])) //webutler_newlog
{
	$newlog = '<div id="webutler_newconf">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_SETTINGS_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="3">'._WBLANGADMIN_WIN_SETTINGS_TEXT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_SETTINGS_USERNAME_.':</strong></td>'."\n".
		'<td style="width: 150px"><input type="text" name="username" value="'.$webutlerboxlayer->config['user_name'].'" class="webutler_input" /></td>'."\n".
		'<td style="width: 135px">&nbsp;</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td><strong>'._WBLANGADMIN_WIN_SETTINGS_NEWPASS_.':</strong></td>'."\n".
		'<td><input type="password" name="userpass1" value="" class="webutler_input" /></td>'."\n".
		'<td>&nbsp;</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td><strong>'._WBLANGADMIN_WIN_SETTINGS_REPEATPASS_.':</strong></td>'."\n".
		'<td><input type="password" name="userpass2" value="" class="webutler_input" /></td>'."\n".
		'<td>&nbsp;</td>'."\n".
		'</tr>'."\n".
        '<tr>'."\n".
        '<td><strong>'._WBLANGADMIN_WIN_SETTINGS_ADMINLANG_.':</strong></td>'."\n".
        '<td><select name="userlang" size="1" class="webutler_select">'."\n";
        $langdir = $webutlerboxlayer->config['server_path'].'/admin/system/lang';					
        $langhandle = opendir($langdir);
        while(false !== ($langfile = readdir($langhandle)))
        { 
            if(!is_dir($langdir.'/'.$langfile.'/') && $langfile != '.' && $langfile != '..')
            {
                $langext = substr($langfile, strrpos($langfile, '.'));
                $userlang = strtolower(substr($langfile, 0, strlen($langfile)-strlen($langext)));
                $newlog.= '<option value="'.$userlang.'"';
                if($webutlerboxlayer->config['user_lang'] != '' && $webutlerboxlayer->config['user_lang'] == $userlang)
                {
                    $newlog.= ' selected="selected"';
                }
                $newlog.= '>'.$userlang.'</option>'."\n";
            }
        }
        closedir($langhandle);
        $newlog.= '</select></td>'."\n".
        '</tr>'."\n";
		$startseite_saved = '';
        if(!($webutlerboxlayer->config['languages'] == '1' && array_key_exists('homes', $webutlerboxlayer->langconf) && count($webutlerboxlayer->langconf['homes']) > 0)) {
			$newlog.= '<tr>'."\n".
			'<td><strong>'._WBLANGADMIN_WIN_SETTINGS_HOMEPAGE_.':</strong></td>'."\n".
			'<td>'."\n".
			'<select name="startseite" size="1" class="webutler_select">'."\n";
			$newlog.= $webutlerboxlayer->buildselect('pages', $webutlerboxlayer->config['startseite'], true);
			$newlog.= '</select>'."\n".
			'</td>'."\n".
			'<td>&nbsp;</td>'."\n".
			'</tr>'."\n";
		}
		else {
			$startseite_saved = '<input type="hidden" name="startseite" value="'.$webutlerboxlayer->config['startseite'].'" />';
		}
		$newlog.= '<tr>'."\n".
		'<td colspan="3" style="padding-top: 20px"><strong>'._WBLANGADMIN_WIN_SETTINGS_IMGSCAL_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td class="webutler_sizetable"><ul><li>'._WBLANGADMIN_WIN_SETTINGS_SMALLIMG_.'</li></ul></td>'."\n".
		'<td colspan="2"><div class="webutler_sizepixel"><input type="text" name="imgsmallsizewidth" size="4" maxlength="4" class="webutler_sizeinput" value="'.$webutlerboxlayer->config['imgsmallsize'][0].'" />px</div> '._WBLANGADMIN_WIN_SETTINGS_IMGWIDTH_.' &nbsp;x&nbsp; <div class="webutler_sizepixel"><input type="text" name="imgsmallsizeheight" size="4" maxlength="4" class="webutler_sizeinput" value="'.$webutlerboxlayer->config['imgsmallsize'][1].'" />px</div> '._WBLANGADMIN_WIN_SETTINGS_IMGHEIGHT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td class="webutler_sizetable"><ul><li>'._WBLANGADMIN_WIN_SETTINGS_BIGIMG_.'</li></ul></td>'."\n".
		'<td colspan="2"><div class="webutler_sizepixel"><input type="text" name="imgboxsizewidth" size="4" maxlength="4" class="webutler_sizeinput" value="'.$webutlerboxlayer->config['imgboxsize'][0].'" />px</div> '._WBLANGADMIN_WIN_SETTINGS_IMGWIDTH_.' &nbsp;x&nbsp; <div class="webutler_sizepixel"><input type="text" name="imgboxsizeheight" size="4" maxlength="4" class="webutler_sizeinput" value="'.$webutlerboxlayer->config['imgboxsize'][1].'" />px</div> '._WBLANGADMIN_WIN_SETTINGS_IMGHEIGHT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td style="padding: 20px 5px 10px 5px"><input type="submit" name="saveuser" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'<td>&nbsp;</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	echo $newlog;
}

if(isset($_POST['webutler_advanced']))
{
	$advanced = '';
	if($webutlerboxlayer->config['admin_erweitert'] != '1' || ($webutlerboxlayer->config['admin_erweitert'] == '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
    {
	  $advanced.= '<div id="webutler_advanced">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_ADVANCED_HEADLINE_.'<strong></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
        '<div id="webutler_advancedscroller">'."\n".
        '<div id="webutler_advancedscrollarea">'."\n".
        '<div id="webutler_advancedscrollbar"><div id="webutler_advancedtrack"></div></div>'."\n".
        '<div id="webutler_advancedviewport">'."\n".
        '<div id="webutler_advancedviewcontent">'."\n".
		'<table width="435" border="0" cellspacing="10" cellpadding="0">'."\n";
        if($webutlerboxlayer->config['fullpageedit'] != '1')
        {
    		$advanced.= '<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADVANCED_EDITPAGE_.'</strong></td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td style="padding-left: 10px">'."\n".
    		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
    		'<tr>'."\n".
    		'<td colspan="2">'._WBLANGADMIN_WIN_ADVANCED_EDITPAGE_TXT_.'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td style="width: 107px">&nbsp;</td>'."\n".
    		'<td>'."\n".
    		'<form method="post" action="index.php?page='.$webutlerboxlayer->filenamesigns($_POST['locationpage']).'" class="webutler_boxesform">'."\n".
            '<input type="hidden" name="edit" value="'.$webutlerboxlayer->filenamesigns($_POST['locationpage']).'" />'."\n".
            '<input type="submit" value="'._WBLANGADMIN_WIN_BUTTONS_EDIT_.'" class="webutler_button webutler_mainbutton" />'."\n".
    		'</form>'."\n".
            '</td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td class="webutler_advancedhr"><hr /></td>'."\n".
    		'</tr>'."\n";
    	}
		$advanced.= '<tr>'."\n".
		'<td><strong>'._WBLANGADMIN_WIN_ADVANCED_ERRORPAGE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td colspan="2" style="padding-right: 10px">'.sprintf(_WBLANGADMIN_WIN_ADVANCED_ERRORPAGE_TXT_, 'echo $webutlercouple->errorpagetext;').'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="width: 107px"><strong>'._WBLANGADMIN_WIN_ADVANCED_ERRORPAGE_NAME_.':</strong></td>'."\n".
		'<td>'."\n".
		'<select name="errorfile" id="webutler_errorfile" size="1" class="webutler_select">'."\n".
        '<option';
        if($webutlerboxlayer->config['ownerrorpage'] == '' || !file_exists($webutlerboxlayer->config['server_path'].'/content/pages/'.$webutlerboxlayer->config['ownerrorpage']))
        {
            $advanced.= ' selected="selected"';
        }
        $advanced.= '></option>'."\n";
        $advanced.= $webutlerboxlayer->buildselect('pages', $webutlerboxlayer->config['ownerrorpage']);
		$advanced.= '</select>'."\n".
		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'pagefile=\' + document.getElementById(\'webutler_errorfile\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td><input type="submit" name="savenewerror" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td class="webutler_advancedhr"><hr /></td>'."\n".
		'</tr>'."\n";
    	if($webutlerboxlayer->config['adminlayouts'] != '0' || ($webutlerboxlayer->config['adminlayouts'] == '0' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
        {
            $buildedselect_layouts = $webutlerboxlayer->buildselect('layouts', '');
    		$advanced.= '<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADVANCED_LAYOUTFUNCS_.'</strong></td>'."\n".
    		'</tr>'."\n".
            '<tr>'."\n".
    		'<td style="padding-left: 15px" class="webutler_hidesubtrsmenu"><span onclick="WBeditbox_hidesubtrs(\'webutler_trnewtpl\',\'webutler_tredittpl|webutler_trdeltpl\')">'._WBLANGADMIN_WIN_ADVANCED_LAYOUTNEW_.'</span> | <span onclick="WBeditbox_hidesubtrs(\'webutler_tredittpl\',\'webutler_trnewtpl|webutler_trdeltpl\')">'._WBLANGADMIN_WIN_ADVANCED_LAYOUTEDIT_.'</span> | <span onclick="WBeditbox_hidesubtrs(\'webutler_trdeltpl\',\'webutler_trnewtpl|webutler_tredittpl\')">'._WBLANGADMIN_WIN_ADVANCED_LAYOUTDELETE_.'</span></td>'."\n".
    		'</tr>'."\n".
    		'<tr id="webutler_trnewtpl">'."\n".
    		'<td style="padding-left: 10px">'."\n".
    		'<form class="webutler_boxesform" method="post">'."\n".
    		'<div class="webutler_winmeldung"></div>'."\n".
    		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
    		'<tr>'."\n".
    		'<td style="width: 107px"><strong>'._WBLANGADMIN_WIN_ADVANCED_LAYOUTNAME_.':</strong></td>'."\n".
    		'<td><input type="text" name="tplname" value="'._WBLANGADMIN_WIN_ADVANCED_LAYOUTVALUE_.'" class="webutler_input" /></td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADVANCED_LAYOUTDUPLICAT_.':</strong></td>'."\n".
    		'<td>'."\n".
    		'<select name="duplicatetpl" id="webutler_duplicatetpl" size="1" class="webutler_select">'."\n".
    		'<option value="" selected="selected">&nbsp;</option>'."\n";		
            $advanced.= $buildedselect_layouts;
    		$advanced.= '</select>'."\n".
    		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'tplfile=\' + document.getElementById(\'webutler_duplicatetpl\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td><input type="submit" name="savenewtpl" value="'._WBLANGADMIN_WIN_BUTTONS_CREATE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'</form>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr id="webutler_tredittpl" style="display: none">'."\n".
    		'<td style="padding-left: 10px">'."\n".
    		'<form method="post" action="index.php?page='.$webutlerboxlayer->filenamesigns($_POST['locationpage']).'"';
    		if($buildedselect_layouts == '')
            {
                $advanced.= ' onsubmit="return false"';
    		}
            $advanced.= ' class="webutler_boxesform">'."\n".
    		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
    		'<tr>'."\n".
    		'<td style="width: 107px"><strong>'._WBLANGADMIN_WIN_ADVANCED_LAYOUTNAME_.':</strong></td>'."\n".
    		'<td>'."\n".
    		'<select name="editlayout" id="webutler_editlayout" size="1" class="webutler_select">'."\n";
    		if($buildedselect_layouts == '')
            {
        		$advanced.= '<option value="">&nbsp;</option>'."\n";
    		}
            $advanced.= $buildedselect_layouts;
    		$advanced.= '</select>'."\n".
    		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'tplfile=\' + document.getElementById(\'webutler_editlayout\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td><input type="submit" name="editnewlayout" value="'._WBLANGADMIN_WIN_BUTTONS_EDIT_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'</form>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr id="webutler_trdeltpl" style="display: none">'."\n".
    		'<td style="padding-left: 10px">'."\n".
    		'<form class="webutler_boxesform" method="post"';
    		if($buildedselect_layouts != '')
            {
                $advanced.= ' onsubmit="return false"';
    		}
            $advanced.= '>'."\n".
    		'<div class="webutler_winmeldung"></div>'."\n".
    		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
    		'<tr>'."\n".
    		'<td style="width: 107px"><strong>'._WBLANGADMIN_WIN_ADVANCED_LAYOUTNAME_.':</strong></td>'."\n".
    		'<td>'."\n".
    		'<select name="deltpl" id="webutler_deltpl" size="1" class="webutler_select">'."\n";
    		if($buildedselect_layouts == '')
            {
                $advanced.= '<option value="">&nbsp;</option>'."\n";
            }		
            $advanced.= $buildedselect_layouts;
    		$advanced.= '</select>'."\n".
    		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'tplfile=\' + document.getElementById(\'webutler_deltpl\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td><input type="submit" name="delnewtpl" value="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" onclick="return WBeditbox_requestdelete(\''._WBLANGADMIN_POPUPWIN_LAYOUTDELETE_.'\', document.getElementById(\'webutler_deltpl\').value);" class="webutler_button webutler_mainbutton" /></td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'</form>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td class="webutler_advancedhr"><hr /></td>'."\n".
    		'</tr>'."\n";
    	}
    	if($webutlerboxlayer->config['adminmenus'] != '0' || ($webutlerboxlayer->config['adminmenus'] == '0' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
        {
            $buildedselect_menus = $webutlerboxlayer->buildselect('menus', '');
    		$advanced.= '<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADVANCED_MENUFUNCS_.'</strong></td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td style="padding-left: 15px" class="webutler_hidesubtrsmenu"><span onclick="WBeditbox_hidesubtrs(\'webutler_trnewmenu\',\'webutler_trdelmenu\')">'._WBLANGADMIN_WIN_ADVANCED_MENUNEW_.'</span> | <span onclick="WBeditbox_hidesubtrs(\'webutler_trdelmenu\',\'webutler_trnewmenu\')">'._WBLANGADMIN_WIN_ADVANCED_MENUDELETE_.'</span></td>'."\n".
    		'<tr id="webutler_trnewmenu">'."\n".
    		'<td style="padding-left: 10px">'."\n".
    		'<form class="webutler_boxesform" method="post">'."\n".
    		'<div class="webutler_winmeldung"></div>'."\n".
    		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
    		'<tr>'."\n".
    		'<td style="width: 107px"><strong>'._WBLANGADMIN_WIN_ADVANCED_MENUNAME_.':</strong></td>'.
    		'<td><input type="text" name="menuname" value="'._WBLANGADMIN_WIN_ADVANCED_MENUVALUE_.'" class="webutler_input" /></td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADVANCED_MENUDUPLICAT_.':</strong></td>'."\n".
    		'<td>'."\n".
    		'<select name="duplicatemenu" id="webutler_duplicatemenu" size="1" class="webutler_select">'."\n".
            '<option value="">&nbsp;</option>'."\n";
            $advanced.= $buildedselect_menus;
    		$advanced.= '</select>'."\n".
    		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'menufile=\' + document.getElementById(\'webutler_duplicatemenu\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td><input type="submit" name="savenewmenu" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'</form>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr id="webutler_trdelmenu" style="display: none">'."\n".
    		'<td style="padding-left: 10px">'."\n".
    		'<form class="webutler_boxesform" method="post"';
    		if($buildedselect_menus != '')
            {
                $advanced.= ' onsubmit="return false"';
    		}
            $advanced.= '>'."\n".
    		'<div class="webutler_winmeldung"></div>'."\n".
    		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
    		'<tr>'."\n".
    		'<td style="width: 107px"><strong>'._WBLANGADMIN_WIN_ADVANCED_MENUNAME_.':</strong></td>'."\n".
    		'<td>'."\n".
    		'<select name="delmenu" id="webutler_delmenu" size="1" class="webutler_select">'."\n";
    		if($buildedselect_menus == '')
            {
                $advanced.= '<option value="">&nbsp;</option>'."\n";
    		}
            $advanced.= $buildedselect_menus;
    		$advanced.= '</select>'."\n".
    		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'menufile=\' + document.getElementById(\'webutler_delmenu\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td><input type="submit" name="delnewmenu" value="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" onclick="return WBeditbox_requestdelete(\''._WBLANGADMIN_POPUPWIN_MENUDELETE_.'\', document.getElementById(\'webutler_delmenu\').value);" class="webutler_button webutler_mainbutton" /></td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'</form>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td class="webutler_advancedhr"><hr /></td>'."\n".
    		'</tr>'."\n";
    	}
    	if($webutlerboxlayer->config['adminblocks'] != '0' || ($webutlerboxlayer->config['adminblocks'] == '0' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
        {
            $buildedselect_blocks = $webutlerboxlayer->buildselect('blocks', '');
    		$advanced.= '<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADVANCED_BLOCKFUNCS_.'</strong></td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td style="padding-left: 15px" class="webutler_hidesubtrsmenu"><span onclick="WBeditbox_hidesubtrs(\'webutler_trnewblock\',\'webutler_trdelblock\')">'._WBLANGADMIN_WIN_ADVANCED_BLOCKNEW_.'</span> | <span onclick="WBeditbox_hidesubtrs(\'webutler_trdelblock\',\'webutler_trnewblock\')">'._WBLANGADMIN_WIN_ADVANCED_BLOCKDELETE_.'</span></td>'."\n".
    		'<tr id="webutler_trnewblock">'."\n".
    		'<td style="padding-left: 10px">'."\n".
    		'<form class="webutler_boxesform" method="post">'."\n".
    		'<div class="webutler_winmeldung"></div>'."\n".
    		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
    		'<tr>'."\n".
    		'<td style="width: 107px"><strong>'._WBLANGADMIN_WIN_ADVANCED_BLOCKNAME_.':</strong></td>'.
    		'<td><input type="text" name="blockname" value="'._WBLANGADMIN_WIN_ADVANCED_BLOCKVALUE_.'" class="webutler_input" /></td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td><strong>'._WBLANGADMIN_WIN_ADVANCED_BLOCKDUPLICAT_.':</strong></td>'."\n".
    		'<td>'."\n".
    		'<select name="duplicateblock" id="webutler_duplicateblock" size="1" class="webutler_select">'."\n".
            '<option value="">&nbsp;</option>'."\n";
            $advanced.= $buildedselect_blocks;
    		$advanced.= '</select>'."\n".
    		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'blockfile=\' + document.getElementById(\'webutler_duplicateblock\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td><input type="submit" name="savenewblock" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'</form>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr id="webutler_trdelblock" style="display: none">'."\n".
    		'<td style="padding-left: 10px">'."\n".
    		'<form class="webutler_boxesform" method="post"';
    		if($buildedselect_blocks != '')
            {
                $advanced.= ' onsubmit="return false"';
    		}
            $advanced.= '>'."\n".
    		'<div class="webutler_winmeldung"></div>'."\n".
    		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
    		'<tr>'."\n".
    		'<td style="width: 107px"><strong>'._WBLANGADMIN_WIN_ADVANCED_BLOCKNAME_.':</strong></td>'."\n".
    		'<td>'."\n".
    		'<select name="delblock" id="webutler_delblock" size="1" class="webutler_select">'."\n";
    		if($buildedselect_blocks == '')
            {
                $advanced.= '<option value="">&nbsp;</option>'."\n";
    		}
            $advanced.= $buildedselect_blocks;
    		$advanced.= '</select>'."\n".
    		'<input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_PREVIEW_.'" onclick="WBeditbox_vorschau(\'blockfile=\' + document.getElementById(\'webutler_delblock\').value, \''._WBLANGADMIN_POPUPWIN_NOPREVIEW_.'\')" class="webutler_button" style="margin-left: 20px" />'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td>&nbsp;</td>'."\n".
    		'<td><input type="submit" name="delnewblock" value="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" onclick="return WBeditbox_requestdelete(\''._WBLANGADMIN_POPUPWIN_BLOCKDELETE_.'\', document.getElementById(\'webutler_delblock\').value);" class="webutler_button webutler_mainbutton" /></td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'</form>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
    		'<td class="webutler_advancedhr"><hr /></td>'."\n".
    		'</tr>'."\n";
		}
        $advanced.= '<tr>'."\n".
		'<td><strong>'._WBLANGADMIN_WIN_ADVANCED_PHPINFO_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5" style="margin-bottom: -8px">'."\n".
		'<tr>'."\n".
		'<td style="width: 107px; padding-bottom: 0px">&nbsp;</td>'."\n".
		'<td style="padding-bottom: 0px"><input type="button" value="'._WBLANGADMIN_WIN_BUTTONS_OPENNEWWIN_.'" class="webutler_button webutler_mainbutton" onclick="WBeditbox_opennewwin(\'admin/system/info.php\')" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n".
		'</div>'."\n".
		'</div>'."\n".
		'</div>'."\n".
		'</div>'."\n";
	}
	echo $advanced;
}

if(isset($_POST['webutler_categories']))
{
    $langcodes = '';
    if($webutlerboxlayer->config['languages'] == '1' && array_key_exists('code', $webutlerboxlayer->langconf) && count($webutlerboxlayer->langconf['code']) > 0)
    {
        $langcodes = $webutlerboxlayer->langconf['code'];
    }
    
	$categories = '';
	if($webutlerboxlayer->config['categories'] == '1')
    {
        $categories.= '<div id="webutler_categories">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_CATEGORIES_HEADLINE_.'</strong></td>'."\n".
        '</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding: 0px 10px 0px 10px">'._WBLANGADMIN_WIN_CATEGORIES_TXT_.'</td>'."\n".
        '</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding: 0px 10px 0px 10px" class="webutler_hidesubtrsmenu">'.
        '<span onclick="WBeditbox_hidecattrs(\'new\',\'del\');">'._WBLANGADMIN_WIN_CATEGORIES_NEWCAT_.'</span>';
        $catsselectoptions = $webutlerboxlayer->catsselect();
        if(count($catsselectoptions) > 0) {
            $categories.= ' | <span onclick="WBeditbox_hidecattrs(\'del\',\'new\');">'._WBLANGADMIN_WIN_CATEGORIES_DELCAT_.'</span>';
        }
        $categories.= '</td>'."\n".
        '</tr>'."\n".
        '<tr>'."\n".
        '<td>'."\n".
        '<form class="webutler_boxesform" method="post">'."\n".
        '<div class="webutler_winmeldung"></div>'."\n".
        '<table border="0" cellspacing="0" cellpadding="3" align="center">'."\n".
        '<tr>'."\n";
        $categories.= ($langcodes != '') ? '<td><strong>'._WBLANGADMIN_WIN_CATEGORIES_LANG_.'</strong></td>'."\n" : '';
        $categories.= '<td colspan="2"><strong>'._WBLANGADMIN_WIN_CATEGORIES_NAME_.'</strong></td>'."\n".
        '</tr>'."\n";
        if($langcodes != '')
        {
            foreach($langcodes as $langcode)
            {
                $categories.= '<tr>'."\n".
                '<td style="text-align: center"><img src="includes/language/icons/'.$langcode.'.png" /></td>'."\n".
                '<td class="webutler_trnewcat"><input type="text" name="newcat['.$langcode.']" class="webutler_input"></td>'."\n".
                '<td class="webutler_trdelcat" style="display: none">'."\n".
                '<select name="delcat['.$langcode.']" size="1" class="webutler_select">'."\n".
                '<option value="">&nbsp;</option>'."\n";
                if(isset($webutlerboxlayer->categories['cats'][$langcode]) && count($webutlerboxlayer->categories['cats'][$langcode]) > 0) {
                    foreach($webutlerboxlayer->categories['cats'][$langcode] as $category)
                    {
                        $categories.= '<option value="'.$category.'">'.$category.'</option>'."\n";
                    }
                }
                $categories.= '</select>'."\n".
                '</td>'."\n".
                '</tr>'."\n";
            }
        }
        else
        {
            $categories.= '<tr>'."\n".
            '<td class="webutler_trnewcat"><input type="text" name="newcat" class="webutler_input"></td>'."\n".
            '<td class="webutler_trdelcat" style="display: none">'."\n".
            '<select name="delcat" size="1" class="webutler_select">'."\n".
            '<option value="">&nbsp;</option>'."\n";
            foreach($webutlerboxlayer->categories['cats'] as $category)
            {
                if(!is_array($category))
                    $categories.= '<option value="'.$category.'">'.$category.'</option>'."\n";
            }
            $categories.= '</select>'."\n".
            '</td>'."\n".
            '</tr>'."\n";
        }
        
        $categories.= '<tr class="webutler_trdelcat" style="display: none">'."\n";
        $categories.= ($langcodes != '') ? '<td>&nbsp;</td>'."\n" : '';
        $categories.= '<td colspan="2">'._WBLANGADMIN_WIN_CATEGORIES_DELETE_.'</td>'."\n".
        '</tr>'."\n";
        
        $categories.= '<tr>'."\n";
        $categories.= ($langcodes != '') ? '<td>&nbsp;</td>'."\n" : '';
        //$categories.= '<td colspan="2" style="padding-top: 5px"><input type="submit" class="webutler_button webutler_mainbutton" name="savecategories" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" /></td>'."\n".
        $categories.= '<td colspan="2" style="padding-top: 5px">'.
        '<span id="webutler_txtnewbutton">'._WBLANGADMIN_WIN_CATEGORIES_BUTTONNEW_.'</span>'.
        '<span id="webutler_txtdelbutton">'._WBLANGADMIN_WIN_CATEGORIES_BUTTONDEL_.'</span>'.
        '<input type="submit" id="savecategoriesbutton" style="width: 200px" class="webutler_button webutler_mainbutton" name="savenewcategories" value="'._WBLANGADMIN_WIN_CATEGORIES_BUTTONNEW_.'" /></td>'."\n".
        '</tr>'."\n".
        '</table>'."\n".
        '</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
        '</table>'."\n".
		'</div>'."\n";
	}
    
	echo $categories;
}

if(isset($_POST['webutler_editstyles']))
{
	$editstyles = '<div id="webutler_editstyles">'."\n".
	'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
	'<tr>'."\n".
	//'<td><strong>'._WBLANGADMIN_WIN_EDITSTYLES_HEADLINE_.'</strong></td>'."\n".
	'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_EDITSTYLES_HEADLINE_.'</strong></td>'."\n".
	'</tr>'."\n".
	'<tr>'."\n".
	'<td style="padding-left: 10px">'."\n".
	'<form method="post" action="index.php?page='.$webutlerboxlayer->filenamesigns($_POST['locationpage']).'" class="webutler_boxesform">'."\n".
	'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
	'<tr>'."\n".
	'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_EDITSTYLES_FILENAME_.':</strong></td>'."\n".
	'<td><select name="editstyles" id="webutler_editstyles" size="1" class="webutler_select">'."\n";
	$editstyles.= $webutlerboxlayer->buildstyleselect();
	$editstyles.= '<option value="columns">columns/columns.css</option>'."\n".
	'</select></td>'."\n".
	'</tr>'."\n".
	'<tr>'."\n".
	'<td>&nbsp;</td>'."\n".
	'<td><input type="submit" name="editnewstyles" value="'._WBLANGADMIN_WIN_BUTTONS_EDIT_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
	'</tr>'."\n".
	'</table>'."\n".
	'</form>'."\n".
	'</td>'."\n".
	'</tr>'."\n".
	'</table>'."\n".
	'</div>'."\n";
    
	echo $editstyles;
}

/* Spalten einfügen */
if(isset($_POST['webutler_columns']))
{
	$columns = '';
	if($webutlerboxlayer->config['insertcolumns'] == '1' || ($webutlerboxlayer->config['insertcolumns'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1')) {
		$columns.= '<div id="webutler_columns">'."\n".
		'<table style="width: 100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_COLUMNS_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding: 10px 0px 10px 10px"><strong>'._WBLANGADMIN_WIN_COLUMNS_LENGTH_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td id="webutler_colnumb"><ul>'."\n".
		'<li>1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li><li>7</li><li>8</li><li>9</li><li>10</li><li>11</li><li>12</li>'."\n".
		'</ul>'."\n".
		'<div id="webutler_coltemp">'."\n".
		'<div class="webutler_colconf">'."\n".
		'<table border="0" cellspacing="0" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td colspan="3" style="padding-bottom: 5px"><strong>'._WBLANGADMIN_WIN_COLUMNS_NUM_.' ###NUM###</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td colspan="3" style="padding-bottom: 5px">'._WBLANGADMIN_WIN_COLUMNS_ALIGN_.': <select id="webutler_col_align_###NUM###" size="1" class="webutler_select" onchange="WBeditbox_coloverflow(###NUM###)">'."\n".
		'<option value="top">'._WBLANGADMIN_WIN_COLUMNS_ALIGN_TOP_.'</option>'."\n".
		'<option value="middle">'._WBLANGADMIN_WIN_COLUMNS_ALIGN_MIDDLE_.'</option>'."\n".
		'<option value="bottom">'._WBLANGADMIN_WIN_COLUMNS_ALIGN_BOTTOM_.'</option>'."\n".
		'<option value="full">'._WBLANGADMIN_WIN_COLUMNS_ALIGN_FULL_.'</option>'."\n".
		'</select>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td>'._WBLANGADMIN_WIN_COLUMNS_WIDTH_.'</td>'."\n".
		'<td>'._WBLANGADMIN_WIN_COLUMNS_ORDER_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>'._WBLANGADMIN_WIN_COLUMNS_COLSMALL_.'</td>'."\n".
		'<td>'."\n".
		'<select id="webutler_col_small_###NUM###" size="1" class="webutler_select">'."\n".
		'<option value="hide">'._WBLANGADMIN_WIN_COLUMNS_HIDE_.'</option>'."\n".
		'<option value="1">1 / 12</option>'."\n".
		'<option value="2">2 / 12</option>'."\n".
		'<option value="3">3 / 12</option>'."\n".
		'<option value="4">4 / 12</option>'."\n".
		'<option value="5">5 / 12</option>'."\n".
		'<option value="6">6 / 12</option>'."\n".
		'<option value="7">7 / 12</option>'."\n".
		'<option value="8">8 / 12</option>'."\n".
		'<option value="9">9 / 12</option>'."\n".
		'<option value="10">10 / 12</option>'."\n".
		'<option value="11">11 / 12</option>'."\n".
		'<option value="12">12 / 12</option>'."\n".
		'</select>'."\n".
		'</td>'."\n".
		'<td><input type="text" id="webutler_order_small_###NUM###" value="###NUM###" class="webutler_input webutler_order" /></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>'._WBLANGADMIN_WIN_COLUMNS_COLMEDIUM_.'</td>'."\n".
		'<td>'."\n".
		'<select id="webutler_col_medium_###NUM###" size="1" class="webutler_select">'."\n".
		'<option value="hide">'._WBLANGADMIN_WIN_COLUMNS_HIDE_.'</option>'."\n".
		'<option value="1">1 / 12</option>'."\n".
		'<option value="2">2 / 12</option>'."\n".
		'<option value="3">3 / 12</option>'."\n".
		'<option value="4">4 / 12</option>'."\n".
		'<option value="5">5 / 12</option>'."\n".
		'<option value="6">6 / 12</option>'."\n".
		'<option value="7">7 / 12</option>'."\n".
		'<option value="8">8 / 12</option>'."\n".
		'<option value="9">9 / 12</option>'."\n".
		'<option value="10">10 / 12</option>'."\n".
		'<option value="11">11 / 12</option>'."\n".
		'<option value="12">12 / 12</option>'."\n".
		'</select>'."\n".
		'</td>'."\n".
		'<td><input type="text" id="webutler_order_medium_###NUM###" value="###NUM###" class="webutler_input webutler_order" /></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>'._WBLANGADMIN_WIN_COLUMNS_COLLARGE_.'</td>'."\n".
		'<td>'."\n".
		'<select id="webutler_col_large_###NUM###" size="1" class="webutler_select">'."\n".
		'<option value="hide">'._WBLANGADMIN_WIN_COLUMNS_HIDE_.'</option>'."\n".
		'<option value="1">1 / 12</option>'."\n".
		'<option value="2">2 / 12</option>'."\n".
		'<option value="3">3 / 12</option>'."\n".
		'<option value="4">4 / 12</option>'."\n".
		'<option value="5">5 / 12</option>'."\n".
		'<option value="6">6 / 12</option>'."\n".
		'<option value="7">7 / 12</option>'."\n".
		'<option value="8">8 / 12</option>'."\n".
		'<option value="9">9 / 12</option>'."\n".
		'<option value="10">10 / 12</option>'."\n".
		'<option value="11">11 / 12</option>'."\n".
		'<option value="12">12 / 12</option>'."\n".
		'</select>'."\n".
		'</td>'."\n".
		'<td><input type="text" id="webutler_order_large_###NUM###" value="###NUM###" class="webutler_input webutler_order" /></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td colspan="3" style="padding-top: 10px">'."\n".
		'<table border="0" cellspacing="0" cellpadding="0" class="webutler_coleditor"><tr>'."\n".
		'<td><input type="checkbox" id="webutler_coleditor_###NUM###" checked="checked" /></td>'."\n".
		'<td><label for="webutler_coleditor_###NUM###">'._WBLANGADMIN_WIN_COLUMNS_EDITOR_.'</label></td>'."\n".
		'</tr></table>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td colspan="3" style="padding-top: 10px">'._WBLANGADMIN_WIN_COLUMNS_COLCSS_.':</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td colspan="3"><input type="text" id="webutler_colcss_###NUM###" value="" class="webutler_input webutler_colcss" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n".
		'</div>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<div id="webutler_columnsinfotext">'._WBLANGADMIN_WIN_COLUMNS_TEXT_.'</div>'."\n".
		'<div id="webutler_columnsrowcss">'."\n".
		_WBLANGADMIN_WIN_COLUMNS_ROWCSS_.':<br />'."\n".
		'<input type="text" id="webutler_rowcss" value="" class="webutler_input webutler_rowcss" />'."\n".
		'</div>'."\n".
		'<div id="webutler_singlecolumn">'._WBLANGADMIN_WIN_COLUMNS_SINGLE_.'</div>'."\n".
		'<div id="webutler_columnsscroller">'."\n".
		'<div id="webutler_columnsscrollarea">'."\n".
		'<div id="webutler_columnsviewport">'."\n".
		'<div id="webutler_columnsviewcontent">'."\n".
		'</div>'."\n".
		'</div>'."\n".
		'<div id="webutler_columnsscrollbar"><div id="webutler_columnstrack"></div></div>'."\n".
		'</div>'."\n".
		'</div>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding: 5px 0px; text-align: center"><input id="webutler_insertcolumnsbutton" value="'._WBLANGADMIN_WIN_COLUMNS_BUTTON_.'" class="webutler_button webutler_mainbutton" type="button"></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td class="webutler_advancedhr"><hr /></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td><strong class="webutler_headline">'._WBLANGADMIN_WIN_DELCOLUMNS_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding: 0px 10px">'._WBLANGADMIN_WIN_DELCOLUMNS_TEXT_.'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-top: 5px; text-align: center"><input id="webutler_deletecolumnsbutton" value="'._WBLANGADMIN_WIN_DELCOLUMNS_BUTTON_.'" class="webutler_button webutler_mainbutton" type="button"></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	}
    
	echo $columns;
}

if(isset($_POST['webutler_forms']) || isset($_POST['openformnew']) || isset($_POST['openformedit']))
{
    $langs = '';
    if($webutlerboxlayer->config['languages'] == '1' && array_key_exists('code', $webutlerboxlayer->langconf) && count($webutlerboxlayer->langconf['code']) > 0)
    {
        $langs = $webutlerboxlayer->langconf['code'];
    }
    
    $forms = '';
   	if($webutlerboxlayer->config['forms_modul'] == '1')
    {
        $forms.= '<div id="webutler_forms">'."\n".
    		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
    		'<tr>'."\n".
    		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_FORMS_HEADLINE_.'</strong></td>'."\n".
    		'</tr>'."\n".
    		'<tr>'."\n".
            '<td style="padding-left: 10px">'."\n";
		
		if(isset($_POST['openformnew']))
		{
			$post_empfaenger = (isset($_POST['empfaenger'])) ? htmlspecialchars($_POST['empfaenger'], ENT_QUOTES) : '';
			//$post_empfaengermail = (isset($_POST['empfaengermail'])) ? preg_replace('#[^a-z0-9-_.@]#', '', $_POST['empfaengermail']) : '';
			$empfaengermail = isset($_POST['empfaengermail']) ? $webutlerboxlayer->validatemail($_POST['empfaengermail']) : 'false';
			$post_empfaengermail = (isset($_POST['empfaengermail']) && $empfaengermail != 'false') ? $empfaengermail : '';
			$post_empfaengername = (isset($_POST['empfaengername'])) ? htmlspecialchars($_POST['empfaengername'], ENT_QUOTES) : '';
			$post_empfaengerbetreff = (isset($_POST['empfaengerbetreff'])) ? htmlspecialchars($_POST['empfaengerbetreff'], ENT_QUOTES) : '';
			$forms.= '<form class="webutler_boxesform" method="post">'."\n".
				'<div class="webutler_winmeldung"></div>'."\n".
				'<table style="width: 100%" border="0" cellspacing="3" cellpadding="0">'."\n".
				'<tr>'."\n".
				'<td style="padding-bottom: 5px">'._WBLANGADMIN_WIN_FORMS_ADDRECEIVER_.'</td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td>'."\n".
				'<table border="0" cellspacing="3" cellpadding="0" align="center">'."\n".
				'<tr>'."\n".
				'<td style="width: 150px">'._WBLANGADMIN_WIN_FORMS_INPUT_RECEIVER_.':</td>'."\n".
				'<td><input type="text" class="webutler_input webutler_inputform" name="empfaenger" value="'.$post_empfaenger.'" /></td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td>'._WBLANGADMIN_WIN_FORMS_INPUT_MAILADDRESS_.':</td>'."\n".
				'<td><input type="text" class="webutler_input webutler_inputform" name="empfaengermail" value="'.$post_empfaengermail.'" /></td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td>'._WBLANGADMIN_WIN_FORMS_INPUT_SHIPPER_.':</td>'."\n".
				'<td><input type="text" class="webutler_input webutler_inputform" name="empfaengername" value="'.$post_empfaengername.'" /></td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td>'._WBLANGADMIN_WIN_FORMS_INPUT_SUBJECT_.':</td>'."\n".
				'<td><input type="text" class="webutler_input webutler_inputform" name="empfaengerbetreff" value="'.$post_empfaengerbetreff.'" /></td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td>'._WBLANGADMIN_WIN_FORMS_INPUT_CONFIRM_.':</td>'."\n".
				'<td><input type="checkbox" name="bestaetigung"';
			if(isset($_POST['bestaetigung']))
			{
				$forms.= ' checked="checked"';
			}
			$forms.= ' /></td>'."\n".
				'</tr>'."\n";
			
			if($langs != '') {
				foreach($langs as $lang) {
					$post_bestaetigungsbetreff_lang = (isset($_POST['bestaetigungsbetreff'][$lang])) ? htmlspecialchars($_POST['bestaetigungsbetreff'][$lang], ENT_QUOTES) : '';
					$forms.= '<tr>'."\n".
						'<td>'._WBLANGADMIN_WIN_FORMS_INPUT_CONFIRMSUB_.' ('.$lang.'):</td>'."\n".
						'<td><input type="text" class="webutler_input webutler_inputform" name="bestaetigungsbetreff['.$lang.']" value="'.$post_bestaetigungsbetreff_lang.'" /></td>'."\n".
						'</tr>'."\n";
				}
			}
			else 
			{
				$post_bestaetigungsbetreff = (isset($_POST['bestaetigungsbetreff'])) ? htmlspecialchars($_POST['bestaetigungsbetreff'], ENT_QUOTES) : '';
				$forms.= '<tr>'."\n".
					'<td>'._WBLANGADMIN_WIN_FORMS_INPUT_CONFIRMSUB_.':</td>'."\n".
					'<td><input type="text" class="webutler_input webutler_inputform" name="bestaetigungsbetreff" value="'.$post_bestaetigungsbetreff.'" /></td>'."\n".
					'</tr>'."\n";
			}
			
			if($langs != '') {
				foreach($langs as $lang) {
					$post_sentalert_lang = (isset($_POST['sentalert'][$lang])) ? htmlspecialchars($_POST['sentalert'][$lang], ENT_QUOTES) : '';
					$forms.= '<tr>'."\n".
						'<td>'._WBLANGADMIN_WIN_FORMS_INPUT_SENTALERT_.' ('.$lang.'):</td>'."\n".
						'<td><input type="text" class="webutler_input webutler_inputform" name="sentalert['.$lang.']" value="'.$post_sentalert_lang.'" /></td>'."\n".
						'</tr>'."\n";
				}
			}
			else 
			{
				$post_sentalert = (isset($_POST['sentalert'])) ? htmlspecialchars($_POST['sentalert'], ENT_QUOTES) : '';
				$forms.= '<tr>'."\n".
					'<td>'._WBLANGADMIN_WIN_FORMS_INPUT_SENTALERT_.':</td>'."\n".
					'<td><input type="text" class="webutler_input webutler_inputform" name="sentalert" value="'.$post_sentalert.'" /></td>'."\n".
					'</tr>'."\n";
			}
			
			$forms.= '<tr>'."\n".
				'<td>&nbsp;</td>'."\n".
				'<td style="padding-top: 5px"><input type="submit" class="webutler_button" style="margin-right: 5px; width: 99px" name="savenewform" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" /><input type="button" onclick="WBeditbox_open(\'webutler_forms\')" style="margin-left: 5px; width: 99px" class="webutler_button" value="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" /></td>'."\n".
				'</tr>'."\n".
				'</table>'."\n".
				'</td>'."\n".
				'</tr>'."\n".
				'</table>'."\n".
				'</form>'."\n";
		}
		elseif(isset($_POST['openformedit']))
		{
			$sendnum = substr($_POST['sendto'], 6);
			$sendnum = preg_replace('/[^0-9]/', '', $sendnum);
			$sendto = $sendnum != '' ? 'sendto'.$sendnum : '';
			
			$forms.= '<form class="webutler_boxesform" method="post">'."\n".
				'<div class="webutler_winmeldung"></div>'."\n".
				'<table style="width: 100%" border="0" cellspacing="3" cellpadding="0">'."\n".
				'<tr>'."\n".
				'<td style="padding-bottom: 5px">'._WBLANGADMIN_WIN_FORMS_EDITRECEIVER_.'</td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td>'."\n".
				'<table border="0" cellspacing="3" cellpadding="0" align="center">'."\n";
			
			$mailaddresses = $webutlerboxlayer->mailaddresses[$sendto];
			
			foreach($mailaddresses as $mailname => $mailvalue)
			{
				if($mailname != 'sendto' && $mailname != 'saveedit')
				{
					if(($mailname == 'bestaetigungsbetreff' || $mailname == 'sentalert') && $langs != '')
					{
						foreach($langs as $lang)
						{
							//$mail_value = (!is_array($mailvalue)) ? $mailvalue : isset($mailvalue[$lang]) ? $mailvalue[$lang] : '';
							$mail_value = is_array($mailvalue) ? (isset($mailvalue[$lang]) ? $mailvalue[$lang] : '') : (isset($mailvalue) ? $mailvalue : '');
							$forms.= '<tr>'."\n".
								'<td style="width: 150px">'.$webutlerboxlayer->getformfieldname($mailname).' ('.$lang.'):</td>'."\n".
								'<td><input type="text" class="webutler_input webutler_inputform" name="'.$mailname.'['.$lang.']" value="'.$mail_value.'" /></td>'."\n".
								'</tr>'."\n";
						}
					}
					else
					{
						$forms.= '<tr>'."\n".
							'<td style="width: 150px">'.$webutlerboxlayer->getformfieldname($mailname).':</td>'."\n";
						if($mailname == 'bestaetigung')
						{
							$forms.= '<td><input type="checkbox" name="'.$mailname.'"';
							if($mailvalue == '1') $forms.= ' checked="checked"';
							$forms.= ' /></td>'."\n";
						}
						else
						{
							if(($mailname == 'bestaetigungsbetreff' || $mailname == 'sentalert') && is_array($mailvalue) && isset($mailvalue[$webutlerboxlayer->config['defaultlang']]))
							{
								$mailvalue = $mailvalue[$webutlerboxlayer->config['defaultlang']];
							}
							$forms.= '<td><input type="text" class="webutler_input webutler_inputform" name="'.$mailname.'" value="'.$mailvalue.'" /></td>'."\n";
						}
						$forms.= '</tr>'."\n";
					}
				}
			}
			
			$forms.= '<tr>'."\n".
				'<td>&nbsp;<input type="hidden" name="sendto" value="'.$sendto.'" /></td>'."\n".
				'<td style="padding-top: 5px"><input type="submit" class="webutler_button" style="margin-right: 5px; width: 99px" name="saveeditform" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" /><input type="button" onclick="WBeditbox_open(\'webutler_forms\')" style="margin-left: 5px; width: 99px" class="webutler_button" value="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" /></td>'."\n".
				'</tr>'."\n".
				'</table>'."\n".
				'</td>'."\n".
				'</tr>'."\n".
				'</table>'."\n".
				'</form>'."\n";
		}
		else
		{
			$forms.= '<table style="width: 100%" border="0" cellspacing="3" cellpadding="0">'."\n".
				'<tr>'."\n".
				'<td style="padding-bottom: 5px">'._WBLANGADMIN_WIN_FORMS_TXT_.'</td>'."\n".
				'</tr>'."\n".
				'<tr>'."\n".
				'<td>'."\n".
				'<div class="webutler_winmeldung"></div>'."\n".
				'<form class="webutler_boxesform" method="post">'."\n".
				'<table border="0" cellspacing="3" cellpadding="0" align="center">'."\n".
				'<tr>'."\n".
				'<td style="width: 200px">'._WBLANGADMIN_WIN_FORMS_NEWRECEIVER_.':</td>'."\n".
				'<td><input type="submit" class="webutler_button webutler_buttonform" name="openformnew" value="'._WBLANGADMIN_WIN_BUTTONS_ADD_.'" /></td>'."\n".
				'</tr>'."\n".
				'</table>'."\n".
				'</form>'."\n";
			
			if(is_array($webutlerboxlayer->mailaddresses) && count($webutlerboxlayer->mailaddresses) > 0)
			{
				ksort($webutlerboxlayer->mailaddresses, SORT_REGULAR);
				$forms.= '<form class="webutler_boxesform" method="post">'."\n".
					'<table border="0" cellspacing="3" cellpadding="0" align="center">'."\n".
					'<tr>'."\n".
					'<td style="width: 200px">'."\n".
					'<select name="sendto" size="1" class="webutler_select" style="width: 200px">'."\n";
					foreach($webutlerboxlayer->mailaddresses as $mailaddress => $element)
					{
						$forms.= '<option value="'.$mailaddress.'">ID: '.str_replace('sendto', '', $mailaddress).' - '.$element['empfaenger'].'</option>'."\n";
					}
				$forms.= '</select>'."\n".
					'</td>'."\n".
					'<td><input type="submit" class="webutler_button webutler_buttonform" name="openformedit" value="'._WBLANGADMIN_WIN_BUTTONS_EDIT_.'" /></td>'."\n".
					'</tr>'."\n".
					'</table>'."\n".
					'</form>'."\n".
					'<form class="webutler_boxesform" method="post">'."\n".
					'<table border="0" cellspacing="3" cellpadding="0" align="center">'."\n".
					'<tr>'."\n".
					'<td style="width: 200px">'."\n".
					'<select name="delete" size="1" class="webutler_select" style="width: 200px">'."\n";
					foreach($webutlerboxlayer->mailaddresses as $mailaddress => $element)
					{
						$forms.= '<option value="'.$mailaddress.'">ID: '.str_replace('sendto', '', $mailaddress).' - '.$element['empfaenger'].'</option>'."\n";
					}
				$forms.= '</select>'."\n".
					'</td>'."\n".
					'<td><input type="submit" class="webutler_button webutler_buttonform" name="formdelsubmit" value="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" onclick="return WBeditbox_requestdelete(\''._WBLANGADMIN_POPUPWIN_RECEIVERDELETE_.'\')" /></td>'."\n".
					'</tr>'."\n".
					'</table>'."\n".
					'</form>'."\n";
			}
        }
		
        $forms.= '</td>'."\n".
            '</tr>'."\n".
            '</table>'."\n".
            '</td>'."\n".
    		'</tr>'."\n".
    		'</table>'."\n".
    		'</div>'."\n";
	}
	echo $forms;
}

if(isset($_POST['webutler_langs']))
{
    $langcodes = '';
    if($webutlerboxlayer->config['languages'] == '1' && array_key_exists('code', $webutlerboxlayer->langconf) && count($webutlerboxlayer->langconf['code']) > 0)
    {
        $langcodes = $webutlerboxlayer->langconf['code'];
    }
    
	$langs = '';
	if($webutlerboxlayer->config['languages'] == '1' && ($webutlerboxlayer->config['setnewlang'] != '1' || ($webutlerboxlayer->config['setnewlang'] == '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1')))
    {
        $langs.= '<div id="webutler_langs">'."\n";
        if(isset($_POST['langtrcodes']))
        {
            $langs.= '<script>'."\n".
            '/* <![CDATA[ */'."\n".
            '    function WBeditbox_requestlangdelete() {'."\n".
            '        var langs = new Array();'."\n";
            if($langcodes != '')
            {
                foreach($langcodes as $code)
                {
                    $langs.= '        if(document.getElementById(\'delete_'.$code.'\').checked == true) {'."\n".
                    '            langs.push(\''.$code.'\');'."\n".
                    '        }'."\n";
                }
            }
            $langs.= '    	if(langs.length == 0) {'."\n".
            '        	return true;'."\n".
            '    	}'."\n".
            '    	else {'."\n".
            '           var singplur = (langs.length >= 2) ? \''._WBLANGADMIN_WIN_LANGUAGE_DELETETXT_PLURAL_.'\' : \''._WBLANGADMIN_WIN_LANGUAGE_DELETETXT_SINGULAR_.'\';'."\n".
            '        	if(confirm(singplur)) {'."\n".
            '        		return true;'."\n".
            '        	}'."\n".
            '        	return false;'."\n".
            '    	}'."\n".
            '    }'."\n".
            '/* ]]> */'."\n".
            '</script>'."\n";
        }
		$langs.= '<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_LANGUAGE_HEADLINE_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 10px" class="webutler_hidesubtrsmenu">'."\n".
		'<span onclick="WBeditbox_open(\'webutler_langs\', \'langtrcodes=1\')">'._WBLANGADMIN_WIN_LANGUAGE_INSTALL_.'</span>'."\n";
        if($langcodes != '' && count($langcodes) >= 2)
        {
            $langs.= ' | <span onclick="WBeditbox_open(\'webutler_langs\', \'langtrfronts=1\')">'._WBLANGADMIN_WIN_LANGUAGE_SETSTARTS_.'</span>'."\n";
        }
        $langs.= '</td>'."\n".
        '</tr>'."\n";
        if(isset($_POST['langtrcodes']))
        {
            $langs.= '<tr>'."\n".
            '<td>'."\n".
            '<form class="webutler_boxesform" method="post">'."\n".
            '<div class="webutler_winmeldung"></div>'."\n".
            '<div style="padding: 0px 10px 7px 10px">'._WBLANGADMIN_WIN_LANGUAGE_ORDER_.'</div>'."\n".
            '<table border="0" cellspacing="0" cellpadding="3" align="center">'."\n".
            '<tr>'."\n".
            '<td><strong>'._WBLANGADMIN_WIN_LANGUAGE_LANG_.'</strong></td>'."\n".
            '<td><strong>'._WBLANGADMIN_WIN_LANGUAGE_SHORT_.'</strong></td>'."\n".
            '<td style="width: 120px"><strong>'._WBLANGADMIN_WIN_LANGUAGE_DESCRIPT_.'</strong></td>'."\n".
            '<td><strong>'._WBLANGADMIN_WIN_LANGUAGE_DELETE_.'</strong></td>'."\n".
            '<td><strong>'._WBLANGADMIN_WIN_LANGUAGE_POSITION_.'</strong></td>'."\n".
            '</tr>'."\n";
            if($langcodes != '')
            {
                $countpos = 0;
                $lastpos = count($langcodes);
                foreach($langcodes as $langcode)
                {
                    $countpos = $countpos+1;
                    $langs.= '<tr>'."\n".
                    '<td style="text-align: center"><img src="includes/language/icons/'.$langcode.'.png" /></td>'."\n".
                    '<td>'.$langcode.'</td>'."\n".
                    '<td>'.$webutlerboxlayer->langconf['lang'][$langcode].'</td>'."\n".
                    '<td style="text-align: center"><input type="checkbox" name="delete_'.$langcode.'" id="delete_'.$langcode.'" value="'.$langcode.'" /></td>'."\n".
                    '<td style="text-align: center">'."\n";
                    if($countpos == 1)
                    {
                        $langs.= '<img style="width: 15px; height: 15px" src="admin/system/images/blank.gif" />'."\n";
                    }
                    else
                    {
                        $langs.= '<img style="width: 15px; height: 15px" class="langposup" name="'.$langcode.'" src="admin/system/images/upper.gif" title="'._WBLANGADMIN_WIN_LANGUAGE_POSUP_.'" />'."\n";
                    }
                    if($countpos == $lastpos)
                    {
                        $langs.= '<img style="width: 15px; height: 15px" src="admin/system/images/blank.gif" />'."\n";
                    }
                    else
                    {
                        $langs.= '<img style="width: 15px; height: 15px" class="langposdown" name="'.$langcode.'" src="admin/system/images/downer.gif" title="'._WBLANGADMIN_WIN_LANGUAGE_POSDOWN_.'" />'."\n";
                    }
                    $langs.= '</td>'."\n".
                    '</tr>'."\n";
                }
            }
            $langs.= '<tr id="newlang_line" style="display: none">'."\n".
            '<td style="text-align: center"><img id="imgfromcode" style="width: 16px; height: 11px" src="admin/system/images/blank.png" /></td>'."\n".
            '<td><input type="text" name="newcode" class="webutler_input" style="width: 22px" maxlength="2" onkeyup="setimgfromcode(this.value)" /></td>'."\n".
            '<td><input type="text" name="newlang" class="webutler_input" style="width: 112px" /></td>'."\n".
            '<td colspan="2">&nbsp;</td>'."\n".
            '</tr>'."\n".
            '<tr id="newlang_button">'."\n".
            '<td colspan="5" style="text-align: right"><input type="button" class="webutler_button webutler_mainbutton" value="'._WBLANGADMIN_WIN_BUTTONS_ADDLANG_.'" onclick="document.getElementById(\'newlang_line\').style.display=\'\'; document.getElementById(\'newlang_button\').style.display=\'none\';" /></td>'."\n".
            '</tr>'."\n".
            '<tr>'."\n".
            '<td colspan="5" style="text-align: right"><input type="submit" name="savelangcode" class="webutler_button webutler_mainbutton" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" /></td>'."\n".
            '</tr>'."\n".
            '</table>'."\n".
            '</form>'."\n".
            '</td>'."\n".
            '</tr>'."\n";
        }
        if(isset($_POST['langtrfronts']))
        {
            $langs.= '<tr>'."\n".
            '<td>'."\n".
            '<form class="webutler_boxesform" method="post">'."\n".
            '<div class="webutler_winmeldung"></div>'."\n".
            '<div style="padding: 0px 10px 7px 10px">'._WBLANGADMIN_WIN_LANGUAGE_STARTBYLANG_.'</div>'."\n".
            '<table border="0" cellspacing="0" cellpadding="3" align="center">'."\n".
            '<tr>'."\n".
            '<td><strong>'._WBLANGADMIN_WIN_LANGUAGE_LANG_.'</strong></td>'."\n".
            '<td><strong>'._WBLANGADMIN_WIN_LANGUAGE_STARTPAGE_.'</strong></td>'."\n".
            '</tr>'."\n";
            if($langcodes != '')
            {
                foreach($langcodes as $langcode)
                {
                    $langs.= '<tr>'."\n".
                    '<td style="text-align: center"><img src="includes/language/icons/'.$langcode.'.png" /></td>'."\n".
                    '<td>'."\n".
                    '<select name="homes_'.$langcode.'" size="1" class="webutler_select">'."\n".
                    '<option value="">&nbsp;</option>'."\n";
                    $homes_langcode = (isset($webutlerboxlayer->langconf['homes'][$langcode])) ? $webutlerboxlayer->langconf['homes'][$langcode] : '';
                    $langs.= $webutlerboxlayer->buildselect('pages', $homes_langcode);
                    $langs.= '</select>'."\n".
                    '</td>'."\n".
                    '</tr>'."\n";
                }
            }
            $langs.= '<tr>'."\n".
            '<td colspan="2" style="text-align: right; padding-top: 2px"><input type="submit" class="webutler_button webutler_mainbutton" name="savelanghomes" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" /></td>'."\n".
            '</tr>'."\n".
            '</table>'."\n".
            '</form>'."\n".
    		'</td>'."\n".
    		'</tr>'."\n";
        }
		$langs.= '</table>'."\n".
		'</div>'."\n";
	}
	echo $langs;
}

if(isset($_POST['webutler_pattern']))
{
	$pattern = '';
	if($webutlerboxlayer->config['adminpattern'] == '1' || ($webutlerboxlayer->config['adminpattern'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
	{
		$buildedselect_pattern = $webutlerboxlayer->buildselect('pattern/files', '');
		$pattern.= '<div id="webutler_pattern">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_PATTERN_FUNCS_.'</strong></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td style="padding-left: 15px" class="webutler_hidesubtrsmenu"><span onclick="WBeditbox_hidesubtrs(\'webutler_trnewpattern\',\'webutler_treditpattern|webutler_trdelpattern\')">'._WBLANGADMIN_WIN_PATTERN_NEW_.'</span> | <span onclick="WBeditbox_hidesubtrs(\'webutler_treditpattern\',\'webutler_trnewpattern|webutler_trdelpattern\')">'._WBLANGADMIN_WIN_PATTERN_EDIT_.'</span> | <span onclick="WBeditbox_hidesubtrs(\'webutler_trdelpattern\',\'webutler_trnewpattern|webutler_treditpattern\')">'._WBLANGADMIN_WIN_PATTERN_DELETE_.'</span></td>'."\n".
		'</tr>'."\n".
		'<tr id="webutler_trnewpattern">'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post" onsubmit="return false">'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_PATTERN_FILENAME_.':</strong></td>'."\n".
		'<td><input type="text" name="patternfile" value="'._WBLANGADMIN_WIN_PATTERN_VALUE_.'" class="webutler_input" /></td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td><strong>'._WBLANGADMIN_WIN_PATTERN_DUPLICAT_.':</strong></td>'."\n".
		'<td>'."\n".
		'<select name="duplicatepattern" id="webutler_duplicatepattern" size="1" class="webutler_select">'."\n".
		'<option value="" selected="selected">&nbsp;</option>'."\n";		
		$pattern.= $buildedselect_pattern;
		$pattern.= '</select>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td><input type="submit" name="savenewpattern" value="'._WBLANGADMIN_WIN_BUTTONS_CREATE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr id="webutler_treditpattern" style="display: none">'."\n".
		'<td style="padding-left: 10px">'."\n".
		//'<form class="webutler_boxesform" method="post"';
		'<form method="post" action="index.php?page='.$webutlerboxlayer->filenamesigns($_POST['locationpage']).'"';
		if($buildedselect_pattern == '')
		{
			$pattern.= ' onsubmit="return false"';
		}
		$pattern.= '>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_PATTERN_FILE_.':</strong></td>'."\n".
		'<td>'."\n".
		'<select name="patternedit" id="webutler_editpattern" size="1" class="webutler_select">'."\n";
		if($buildedselect_pattern == '')
		{
			$pattern.= '<option value="" selected="selected">&nbsp;</option>'."\n";
		}
		$pattern.= $buildedselect_pattern;
		$pattern.= '</select>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td><input type="submit" name="editpattern" value="'._WBLANGADMIN_WIN_BUTTONS_EDIT_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr id="webutler_trdelpattern" style="display: none">'."\n".
		'<td style="padding-left: 10px">'."\n".
		'<form class="webutler_boxesform" method="post"';
		if($buildedselect_pattern != '')
		{
			$pattern.= ' onsubmit="return false"';
		}
		$pattern.= '>'."\n".
		'<div class="webutler_winmeldung"></div>'."\n".
		'<table width="100%" border="0" cellspacing="0" cellpadding="5">'."\n".
		'<tr>'."\n".
		'<td style="width: 135px"><strong>'._WBLANGADMIN_WIN_PATTERN_FILE_.':</strong></td>'."\n".
		'<td>'."\n".
		'<select name="delpattern" id="webutler_delpattern" size="1" class="webutler_select">'."\n";
		if($buildedselect_pattern == '')
		{
			$pattern.= '<option value="">&nbsp;</option>'."\n";
		}
		$pattern.= $buildedselect_pattern;
		$pattern.= '</select>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'<tr>'."\n".
		'<td>&nbsp;</td>'."\n".
		'<td><input type="submit" name="deletepattern" value="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" onclick="return WBeditbox_requestdelete(\''._WBLANGADMIN_POPUPWIN_PATTERNDELETE_.'\', document.getElementById(\'webutler_delpattern\').value);" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</div>'."\n";
	}
	echo $pattern;
}

if(isset($_POST['webutler_editpattern']))
{
	$pattern = '';
	if($webutlerboxlayer->config['adminpattern'] == '1' || ($webutlerboxlayer->config['adminpattern'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
	{
		$filename = '';
		if(isset($_POST['patternedit']))
		{
			$filename = $webutlerboxlayer->checkfilenamesigns($_POST['patternedit'], 'tpl');
		}
		if(isset($_POST['patternfile']))
		{
			$filename = $webutlerboxlayer->filenamesigns($_POST['patternfile']).'.tpl';
		}
		$pattern.= '<div id="webutler_pattern">'."\n".
		'<form class="webutler_boxesform" method="post" onsubmit="return false">'."\n".
		'<table width="100%" border="0" cellspacing="10" cellpadding="0">'."\n".
		'<tr>'."\n".
		'<td style="padding-right: 10px"><span onclick="WBeditbox_close()"><img class="webutler_closer" src="admin/system/images/closer.gif" /></span><strong class="webutler_headline">'._WBLANGADMIN_WIN_PATTERN_FUNCS_.'</strong></td>'."\n".
		'</tr>'."\n";
		foreach($_POST as $key => $val) {
			$pattern.= '<tr>'."\n".
			'<td>'.$key.': '.$val.'</td>'."\n".
			'</tr>'."\n";
		}
		$pattern.= '<tr>'."\n".
		'<td><input type="submit" name="savepattern" value="'._WBLANGADMIN_WIN_BUTTONS_SAVE_.'" class="webutler_button webutler_mainbutton" /></td>'."\n".
		'</tr>'."\n".
		'</table>'."\n".
		'</form>'."\n".
		'</div>'."\n";
	}
	echo $pattern;
}



