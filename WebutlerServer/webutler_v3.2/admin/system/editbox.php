<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/admin/system/editbox.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

$checkeditelements = $this->checkeditelements();
$pagehascontent = $checkeditelements['editors'];
$pagehascolumns = $checkeditelements['columns'];
$pagehasmenufiles = $this->testblockfiles('menus');
$pagehasblockfiles = $this->testblockfiles('blocks');

$this->autoheaderdata[] = '<link href="admin/system/css/editbox.css" rel="stylesheet" type="text/css" />';

$editbox_scripts = '<script src="admin/system/javascript/wb_jquery.js"></script>'."\n".
    '<script>'."\n".
    '/* <![CDATA[ */'."\n".
    '    var mediabrowserWindowWidth = \''.$this->config['mediabrowser_wh'][0].'\';'."\n".
    '    var mediabrowserWindowHeight = \''.$this->config['mediabrowser_wh'][1].'\';'."\n".
    '    var locationPage = \''.$this->getpage.'\';'."\n";
if($pagehascolumns > 0 && ($this->config['insertcolumns'] == '1' || ($this->config['insertcolumns'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))) {
	$editbox_scripts.= '    var insertColumnsOnElements = \''.($this->config['insertpoints'] == '1' ? 'full' : 'small').'\';'."\n".
	'    var insertColumnsPromtText = \''.($this->config['insertmargin'] == '1' ? _WBLANGADMIN_COLUMNS_INSERT_PROMT_ : 'disabled').'\';'."\n";
}
$editbox_scripts.= '/* ]]> */'."\n".
    '</script>'."\n".
    '<script src="admin/system/javascript/editbox.js"></script>'."\n";

$insertcolumnsbefore = '';
$insertcolumnsafter = '';
$deletecolumnsbefore = '';
$deletecolumnsafter = '';
if($pagehascolumns > 0 && ($this->config['insertcolumns'] == '1' || ($this->config['insertcolumns'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))) {
	$insertcolumnsbefore.= '<div id="webutler_insertcolumnsbefore"><div class="icons"><img class="webutler_insertcolumns" src="admin/system/images/insert.png" alt="'._WBLANGADMIN_WIN_BUTTONS_INSERT_.'" title="'._WBLANGADMIN_WIN_BUTTONS_INSERT_.'" /><img class="webutler_cancelcolumns" src="admin/system/images/cancel.png" alt="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" title="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" /></div><div class="textline"><span class="text"><span class="webutler_columnsname"></span> '._WBLANGADMIN_COLUMNS_INSERT_BEFORE_.'</span></div></div>'."\n";
	$insertcolumnsafter.= '<div id="webutler_insertcolumnsafter"><div class="icons"><img class="webutler_insertcolumns" src="admin/system/images/insert.png" alt="'._WBLANGADMIN_WIN_BUTTONS_INSERT_.'" title="'._WBLANGADMIN_WIN_BUTTONS_INSERT_.'" /><img class="webutler_cancelcolumns" src="admin/system/images/cancel.png" alt="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" title="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" /></div><div class="textline"><span class="text"><span class="webutler_columnsname"></span> '._WBLANGADMIN_COLUMNS_INSERT_AFTER_.'</span></div></div>'."\n";

	$deletecolumnsbefore.= '<div id="webutler_deletecolumnsbefore"><div class="icons"><img class="webutler_deletecolumns" src="admin/system/images/remove.png" alt="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" title="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" /><img class="webutler_canceldelcolumns" src="admin/system/images/cancel.png" alt="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" title="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" /></div><div class="textline"><span class="text">'._WBLANGADMIN_COLUMNS_DELETE_TEXT_.'</span></div></div>'."\n";
	$deletecolumnsafter.= '<div id="webutler_deletecolumnsafter"><div class="icons"><img class="webutler_deletecolumns" src="admin/system/images/remove.png" alt="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" title="'._WBLANGADMIN_WIN_BUTTONS_DELETE_.'" /><img class="webutler_canceldelcolumns" src="admin/system/images/cancel.png" alt="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" title="'._WBLANGADMIN_WIN_BUTTONS_CANCEL_.'" /></div><div class="textline"><span class="text">'._WBLANGADMIN_COLUMNS_DELETE_TEXT_.'</span></div></div>'."\n";
}

$blockerbg = '<div id="webutler_blockerbackground"></div>'."\n";
$blockerdiv = '<div id="webutler_blockerdiv"></div>'."\n";

$sliderdiv = '<div id="webutler_sliderdiv">'."\n".
   '<div id="webutler_slider"><div><div>&nbsp;</div></div></div>'."\n".
   '<div id="webutler_boxwindow"></div>'."\n".
   '</div>'."\n";

$editbox = '<img id="webutler_hidebox" src="admin/system/images/hidebox.png"';
    if($_SESSION['loggedin']['editboxzustand'] == 'show') {
        $editbox.= ' style="display: block"';
    }
$editbox.= ' />'."\n".
    '<img id="webutler_showbox" src="admin/system/images/showbox.png"';
    if($_SESSION['loggedin']['editboxzustand'] == 'hide') {
        $editbox.= ' style="display: block"';
    }
$editbox.= ' />'."\n".
    '<div id="webutler_editbox"';
    if($_SESSION['loggedin']['editboxzustand'] == 'hide') {
        $editbox.= ' style="margin-left: -121px"';
    }
$editbox.= '>'."\n".
    '<table width="100%" border="0" cellspacing="0" cellpadding="0">'."\n".
	'<thead class="webutler_boxheadline">'."\n".
	'<tr>'."\n".
	'<td><strong>'._WBLANGADMIN_EDITBOX_ADMINISTRATION_.'</strong></td>'."\n".
	'</tr>'."\n".
	'</thead>'."\n".
	'<tbody class="webutler_editboxmenu">'."\n".
	'<tr>'."\n".
	'<td class="webutler_submenu"><img class="webutler_adminicon" id="webutler_iconseite" src="admin/system/images/blank.png" /><span>'._WBLANGADMIN_EDITBOX_PAGE_.'</span>'."\n".
    '<div class="webutler_submenudiv">'."\n".
    '<img class="webutler_subpointer" src="admin/system/images/pointer.png" />'."\n".
	'<table border="0" cellspacing="0" cellpadding="0">'."\n";
	if($this->config['fullpageedit'] == '1' && $pagehascontent == 0 && $pagehasmenufiles == 'false' && $pagehasblockfiles == 'false') {
		$editbox.= '<tr>'."\n".
        '<td>'."\n".'<form method="post" name="LoadEditFullpage" action="index.php?page='.$this->getpage.'" style="margin: 0; padding: 0">'."\n".
		'&raquo; <span onclick="document.forms.LoadEditFullpage.submit()">'._WBLANGADMIN_EDITBOX_PAGEEDIT_.'</span>'."\n".
		'<input type="hidden" name="edit" value="'.$this->getpage.'" />'."\n".
		'</form>'."\n".'</td>'."\n".
		'</tr>'."\n";
	}
	if($this->config['adminnewpage'] == '1' || ($this->config['adminnewpage'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1')) {
		$editbox.= '<tr>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_newpage\')">'._WBLANGADMIN_EDITBOX_PAGEADD_.'</span></td>'."\n".
		'</tr>'."\n";
	}
	$tempversion = $this->config['server_path']."/content/pages/".$this->getpage.".tmp";
	if($this->getpage != $this->config['ownerrorpage']) {
        $editbox.= '<tr id="webutler_showgetpage"';
        if(!in_array($this->getpage, $this->offlinepages)) {
            $editbox.= ' style="display: none"';
        }
        $editbox.= '>'."\n".
        '<td>&raquo; <span onclick="WBeditbox_setpagemodus(\''._WBLANGADMIN_POPUPWIN_PAGETOON_.'\', \''.$this->getpage.'\', \'on\', \''.(file_exists($tempversion) ? '' : _WBLANGADMIN_OFF_PAGEISUSERS_).'\')">'._WBLANGADMIN_EDITBOX_PAGEUPPER_.'</span></td>'."\n".
        '</tr>'."\n";
        $editbox.= '<tr id="webutler_hidegetpage"';
        if(in_array($this->getpage, $this->offlinepages)) {
            $editbox.= ' style="display: none"';
        }
        $editbox.= '>'."\n".
        '<td>&raquo; <span onclick="WBeditbox_setpagemodus(\''._WBLANGADMIN_POPUPWIN_PAGETOOFF_.'\', \''.$this->getpage.'\', \'off\', \''.(file_exists($tempversion) ? '' : _WBLANGADMIN_OFF_PAGEISOFFLINE_).'\')">'._WBLANGADMIN_EDITBOX_PAGEDOWNER_.'</span></td>'."\n".
        '</tr>'."\n";
	}
	if(file_exists($tempversion)) {
		$lastversion = $tempversion.".bak";
		if(file_exists($lastversion)) {
			$editbox.= '<tr>'."\n".
			'<td>&raquo; <span onclick="WBeditbox_lastversion(\''.sprintf(_WBLANGADMIN_POPUPWIN_PAGEVERSION_, date(_WBLANGADMIN_POPUPWIN_VERSION_DATEFORMAT_, (file_exists($lastversion) ? filemtime($lastversion) : time()))).'\', \''.$this->getpage.'\')">'._WBLANGADMIN_EDITBOX_PAGEUNDO_.'</span></td>'."\n".
			'</tr>'."\n";
		}
		$editbox.= '<tr id="webutler_boxtemppublic">'."\n".
		'<td>&raquo; <span onclick="WBeditbox_dopublic(\''.$this->getpage.'\')">'._WBLANGADMIN_EDITBOX_PAGEPUBLIC_.'</span></td>'."\n".
		'</tr>'."\n".
		'<tr id="webutler_boxtempdelete">'."\n".
		'<td>&raquo; <span onclick="WBeditbox_deltemp(\''.$this->getpage.'\', \''._WBLANGADMIN_POPUPWIN_TEMPFILE_DELETE_.'\')">'._WBLANGADMIN_EDITBOX_PAGEDISCARD_.'</span></td>'."\n".
		'</tr>'."\n";
	}
	else {
		$lastversion = $this->config['server_path']."/content/pages/".$this->getpage.".bak";
		if(file_exists($lastversion)) {
			$editbox.= '<tr>'."\n".
			'<td>&raquo; <span onclick="WBeditbox_lastversion(\''.sprintf(_WBLANGADMIN_POPUPWIN_PAGEVERSION_, date(_WBLANGADMIN_POPUPWIN_VERSION_DATEFORMAT_, (file_exists($lastversion) ? filemtime($lastversion) : time()))).'\', \''.$this->getpage.'\')">'._WBLANGADMIN_EDITBOX_PAGEUNDO_.'</span></td>'."\n".
			'</tr>'."\n";
		}
	}
	if($this->config['adminpagename'] == '1' || ($this->config['adminpagename'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1')) {
    	$editbox.= '<tr>'."\n".
    	'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_rename\')">'._WBLANGADMIN_EDITBOX_PAGERENAME_.'</span></td>'."\n".
    	'</tr>'."\n";
    }
	if($this->config['admindelpage'] == '1' || ($this->config['admindelpage'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1')) {
		$editbox.= '<tr>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_delpage\')">'._WBLANGADMIN_EDITBOX_PAGEDELETE_.'</span></td>'."\n".
		'</tr>'."\n";
	}
	if($pagehascolumns > 0 && ($this->config['insertcolumns'] == '1' || ($this->config['insertcolumns'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))) {
		$editbox.= '<tr>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_columns\')">'._WBLANGADMIN_EDITBOX_PAGECOLUMNS_.'</span></td>'."\n".
		'</tr>'."\n";
	}
	if($this->config['modrewrite'] == '1' && $this->config['categories'] == '1') {
        $editbox.= '<tr id="webutler_showeditcat"';
        if(!array_key_exists('cats', $this->categories)) {
            $editbox.= ' style="display: none"';
        }
        $editbox.= '>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_pagecats\')">'._WBLANGADMIN_EDITBOX_PAGECAT_.'</span></td>'."\n".
		'</tr>'."\n";
	}
	if($this->config['languages'] == '1' && $this->getpage != $this->config['ownerrorpage']) {
        $editbox.= '<tr id="webutler_showeditlang"';
        if(!array_key_exists('code', $this->langconf)) {
            $editbox.= ' style="display: none"';
        }
        $editbox.= '>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_pagelang\')">'._WBLANGADMIN_EDITBOX_PAGELANG_.'</span></td>'."\n".
		'</tr>'."\n";
	}
    $editbox.= '<tr>'."\n".
	'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_otherpage\')">'._WBLANGADMIN_EDITBOX_PAGECHANGE_.'</span></td>'."\n".
	'</tr>'."\n";
	$editbox.= '</table>'."\n".
	'</div>'."\n".
	'</td>'."\n".
	'</tr>'."\n";
	if($pagehascontent > 0 || $pagehasmenufiles == 'true' || $pagehasblockfiles == 'true') {
		$editbox.= '<tr>'."\n".
		'<td class="webutler_submenu"><img class="webutler_adminicon" id="webutler_iconedit" src="admin/system/images/blank.png" /><span>'._WBLANGADMIN_EDITBOX_EDIT_.'</span>'."\n".
        '<div class="webutler_submenudiv">'."\n".
        '<img class="webutler_subpointer" src="admin/system/images/pointer.png" />'."\n".
		'<table border="0" cellspacing="0" cellpadding="0">'."\n";
		if($this->config['fullpageedit'] == '1') {
    		$editbox.= '<tr>'."\n".
            '<td>'."\n".'<form method="post" name="LoadEditFullpage" action="index.php?page='.$this->getpage.'" style="margin: 0; padding: 0">'."\n".
    		'&raquo; <span onclick="document.forms.LoadEditFullpage.submit()">'._WBLANGADMIN_EDITBOX_EDITPAGE_.'</span>'."\n".
    		'<input type="hidden" name="edit" value="'.$this->getpage.'" />'."\n".
    		'</form>'."\n".'</td>'."\n".
    		'</tr>'."\n";
		}
    	if($pagehascontent > 0) {
            $editbox.= '<tr>'."\n".
            '<td>'."\n".'<form method="post" name="LoadEditContent" action="index.php?page='.$this->getpage.'" style="margin: 0; padding: 0">'."\n".
    		'&raquo; <span onclick="document.forms.LoadEditContent.submit()">'._WBLANGADMIN_EDITBOX_EDITCONTENT_.'</span>'."\n".
    		'<input type="hidden" name="content" value="'.$this->getpage.'" />'."\n".
    		'</form>'."\n".'</td>'."\n".
    		'</tr>'."\n";
		}
    	if($pagehasmenufiles == 'true') {
    		$editbox.= '<tr>'."\n".
    		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_newmenu\')">'._WBLANGADMIN_EDITBOX_EDITMENU_.'</span></td>'."\n".
    		'</tr>'."\n";
		}
    	if($pagehasblockfiles == 'true') {
            $editbox.= '<tr>'."\n".
    		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_newblock\')">'._WBLANGADMIN_EDITBOX_EDITBLOCK_.'</span></td>'."\n".
    		'</tr>'."\n";
    	}
		$editbox.= '</table>'."\n".
		'</div>'."\n";
	}
	$editbox.= '</td>'."\n".
	'</tr>'."\n".
	'<tr>'."\n".
	'<td class="webutler_submenu"><img class="webutler_adminicon" id="webutler_iconmedia" src="admin/system/images/blank.png" /><span onclick="WBeditbox_openmedia()">'._WBLANGADMIN_EDITBOX_MEDIA_.'</span></td>'."\n".
	'</tr>'."\n".
	'<tr>'."\n".
	'<td class="webutler_submenu"><img class="webutler_adminicon" id="webutler_iconsystem" src="admin/system/images/blank.png" /><span>'._WBLANGADMIN_EDITBOX_SYSTEM_.'</span>'."\n".
    '<div class="webutler_submenudiv">'."\n".
    '<img class="webutler_subpointer" src="admin/system/images/pointer.png" />'."\n".
	'<table border="0" cellspacing="0" cellpadding="0">'."\n".
	'<tr>'."\n".
	'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_newconf\')">'._WBLANGADMIN_EDITBOX_SYSTEMSETTINGS_.'</span></td>'."\n".
	'</tr>'."\n";
	if($this->config['admin_erweitert'] != '1' || ($this->config['admin_erweitert'] == '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1')) {
        $editbox.= '<tr>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_advanced\')">'._WBLANGADMIN_EDITBOX_SYSTEMEXTENDED_.'</span></td>'."\n".
		'</tr>'."\n";
	}
	if($this->config['modrewrite'] == '1' && $this->config['categories'] == '1') {
    	$editbox.= '<tr>'."\n".
    	'<td>&raquo; <span onclick="WBeditbox_checkfile(\'categories\')">'._WBLANGADMIN_EDITBOX_SYSTEMCATS_.'</span></td>'."\n".
    	'</tr>'."\n";
    }
	$editbox.= '<tr>'."\n".
	'<td>&raquo; <span onclick="WBeditbox_checkfile(\'linkhighlite\')">'._WBLANGADMIN_EDITBOX_SYSTEMLINKS_.'</span></td>'."\n".
	'</tr>'."\n";
	if($this->config['adminpattern'] == '1' || ($this->config['adminpattern'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
	{
		$editbox.= '<tr>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_pattern\')">'._WBLANGADMIN_EDITBOX_SYSTEMPATTERN_.'</span></td>'."\n".
		'</tr>'."\n";
	}
	$editbox.= '<tr>'."\n".
	'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_editstyles\')">'._WBLANGADMIN_EDITBOX_SYSTEMSTYLES_.'</span></td>'."\n".
	'</tr>'."\n";
	if($this->config['forms_modul'] == '1' || ($this->config['forms_modul'] != '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))
	{
		$editbox.= '<tr>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_checkfile(\'forms\')">'._WBLANGADMIN_EDITBOX_SYSTEMFORMS_.'</span></td>'."\n".
		'</tr>'."\n";
	}
    if($this->config['languages'] == '1' && ($this->config['setnewlang'] != '1' || ($this->config['setnewlang'] == '1' && isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))) {
        $editbox.= '<tr>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_checkfile(\'langs\');">'._WBLANGADMIN_EDITBOX_SYSTEMLANGS_.'</span></td>'."\n".
		'</tr>'."\n";
	}
	if($this->config['userlogs'] == '1' && class_exists('SQLite3')) {
        $editbox.= '<tr>'."\n".
		'<td>&raquo; <span onclick="WBeditbox_open(\'webutler_access\');">'._WBLANGADMIN_EDITBOX_SYSTEMUSERS_.'</span></td>'."\n".
		'</tr>'."\n";
	}
	if(((isset($this->config['makemod']) && $this->config['makemod'] == '1') || (isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1')) && file_exists($this->config['server_path'].'/admin/makemod/index.php') && class_exists('SQLite3')) {
        $editbox.= '<tr>'."\n".
		'<td>&raquo; <a href="admin/makemod/index.php" target="_blank">'._WBLANGADMIN_EDITBOX_SYSTEMMODMAKER_.'</a></td>'."\n".
		'</tr>'."\n";
	}
	$editbox.= '</table>'."\n".
	'</div>'."\n".
	'</td>'."\n".
	'</tr>'."\n";
	if(($this->config['modsonlog'] == '2' || $this->config['modsonlog'] == '3') && is_array($this->moduleslist) && count($this->moduleslist) >= 1) {
		$editbox.= '<tr>'."\n".
		'<td class="webutler_submenu"><img class="webutler_adminicon" id="webutler_iconmodule" src="admin/system/images/blank.png" /><span>'._WBLANGADMIN_EDITBOX_MODULES_.'</span>'."\n".
		'<div class="webutler_submenudiv">'."\n".
		'<img class="webutler_subpointer" src="admin/system/images/pointer.png" />'."\n".
		'<table border="0" cellspacing="0" cellpadding="0">'."\n";
		foreach($this->moduleslist as $module) {
			$modpath = $module[1];
			if(substr($modpath, 0, 1) != '/') $modpath = '/'.$modpath;
			$editbox.= '<tr>'."\n".
			'<td>&raquo; <a href="modules'.$modpath.'" target="_blank">'.$module[0].'</a></td>'."\n".
			'</tr>'."\n";
		}
		$editbox.= '</table>'."\n".
		'</div>'."\n".
		'</td>'."\n".
		'</tr>'."\n";
	}
    $logouturl = ($this->config['modrewrite'] == '1') ? '/logout' : '/admin/index.php?logout=yes';
	$editbox.= '<tr>'."\n".
	'<td class="webutler_submenu"><img class="webutler_adminicon" id="webutler_iconlogout" src="admin/system/images/blank.png" /><a href="'.$this->config['homepage'].$logouturl.'">'._WBLANGADMIN_EDITBOX_LOGOUT_.'</a></td>'."\n".
	'</tr>'."\n".
	'</tbody>'."\n".
	'</table>'."\n".
    '</div>'."\n";


$boxresult = $editbox_scripts.$editbox.$insertcolumnsbefore.$insertcolumnsafter.$deletecolumnsbefore.$deletecolumnsafter.$blockerbg.$blockerdiv.$sliderdiv;



