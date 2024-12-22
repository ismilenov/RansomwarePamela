<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


// installation

define('_WBLANGADMIN_INSTALL_TITLE_','Installation');
define('_WBLANGADMIN_INSTALL_ERROR_','Error!');
define('_WBLANGADMIN_INSTALL_REWRITE_','The Apache module mod_rewrite is not active. Please enable it or change the setting in the file /settings/globalvars.php');
define('_WBLANGADMIN_INSTALL_REWRITEAGAIN_','next');
define('_WBLANGADMIN_INSTALL_SALTKEYS_','Before installation, please add Salt Keys in the file /settings/globalvars.php! The Salt Keys may not be changed after installation.');
define('_WBLANGADMIN_INSTALL_KEYSAGAIN_','next');
define('_WBLANGADMIN_INSTALL_PHPVERSION_','The Webutler requires PHP version 5.4 or higher!');
define('_WBLANGADMIN_INSTALL_CHMODERROR_1_','There are not writable files or directories on the web space!');
define('_WBLANGADMIN_INSTALL_CHMODERROR_2_','Please contact your FTP program to CHMOD according to the specifications of the provider for the following files and/or directories, and then click');
define('_WBLANGADMIN_INSTALL_CHMODAGAIN_','recheck');
define('_WBLANGADMIN_INSTALL_CHMODOK_','All write permissions are set correctly!');
define('_WBLANGADMIN_INSTALL_CONFIGNOWRITE_','The configuration file could not be opened.');
define('_WBLANGADMIN_INSTALL_FIELDEMPTY_','You have not filled in all required fields.');
define('_WBLANGADMIN_INSTALL_FIELDUSERNAME_','user name');
define('_WBLANGADMIN_INSTALL_FIELDUSERPASS_','password');
define('_WBLANGADMIN_INSTALL_FIELDSERVERPATH_','server path');
define('_WBLANGADMIN_INSTALL_FIELDHOMEPAGEURL_','homepage URL');
define('_WBLANGADMIN_INSTALL_FIELDLANGUAGE_','language administrator');
define('_WBLANGADMIN_INSTALL_FIELDHOMEPAGE_','front page');
define('_WBLANGADMIN_INSTALL_SAVESETTINGS_','save settings');
define('_WBLANGADMIN_INSTALL_WRONGSIGN_','Illegal characters in the user name.');
define('_WBLANGADMIN_INSTALL_WRONGPASS_1_','Illegal characters in the password.');
define('_WBLANGADMIN_INSTALL_WRONGPASS_2_','A to Z, a to z, 0 to 9 and #+-_*@%&=!?');
define('_WBLANGADMIN_INSTALL_NOTSAVED_','Your details could not be taken.');
define('_WBLANGADMIN_INSTALL_INSTALLOK_','The installation was successful.');
define('_WBLANGADMIN_INSTALL_INSTOKTXT_1_','advanced preferences');
define('_WBLANGADMIN_INSTALL_INSTOKTXT_2_','can be made in the file');
define('_WBLANGADMIN_INSTALL_INSTOKTXT_3_','when required.');
define('_WBLANGADMIN_INSTALL_LINKSTART_','go to front page');
define('_WBLANGADMIN_INSTALL_LINKLOGIN_','go to login');


// login

define('_WBLANGADMIN_LOGIN_TITLE_','Login');
define('_WBLANGADMIN_LOGIN_ERRORNOCOOKIE_','Cookies must be enabled!');
define('_WBLANGADMIN_LOGIN_ERRORWRONGDATA_','Incorrect login!');
define('_WBLANGADMIN_LOGIN_USER_','user name');
define('_WBLANGADMIN_LOGIN_PASS_','password');
define('_WBLANGADMIN_LOGIN_LOGIN_','login');
define('_WBLANGADMIN_LOGIN_ATTEMPTS_','login blocked for __TIME__ minutes');


// admin pages

define('_WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_','The file does not exist.');
define('_WBLANGADMIN_ADMINPAGE_BLOCKPREVIEW_','block preview');
define('_WBLANGADMIN_ADMINPAGE_MENUPREVIEW_','menu preview');
define('_WBLANGADMIN_ADMINPAGE_LAYOUTPREVIEW_','layout preview');
define('_WBLANGADMIN_ADMINPAGE_PREVIEWERRORTEXT_','output the error message');


// admin menu

define('_WBLANGADMIN_EDITBOX_ADMINISTRATION_','Administration');

define('_WBLANGADMIN_EDITBOX_PAGE_','page');
define('_WBLANGADMIN_EDITBOX_PAGEEDIT_','edit');
define('_WBLANGADMIN_EDITBOX_PAGEADD_','add');
define('_WBLANGADMIN_EDITBOX_PAGEUPPER_','enable');
define('_WBLANGADMIN_EDITBOX_PAGEDOWNER_','disable');
define('_WBLANGADMIN_EDITBOX_PAGEUNDO_','undo');
define('_WBLANGADMIN_EDITBOX_PAGECOLUMNS_','columns');
define('_WBLANGADMIN_EDITBOX_PAGECAT_','category');
define('_WBLANGADMIN_EDITBOX_PAGELANG_','language');
define('_WBLANGADMIN_EDITBOX_PAGERENAME_','rename');
define('_WBLANGADMIN_EDITBOX_PAGEPUBLIC_','publish');
define('_WBLANGADMIN_EDITBOX_PAGEDISCARD_','discard');
define('_WBLANGADMIN_EDITBOX_PAGEDELETE_','delete');
define('_WBLANGADMIN_EDITBOX_PAGECHANGE_','change');

define('_WBLANGADMIN_EDITBOX_EDIT_','edit');
define('_WBLANGADMIN_EDITBOX_EDITPAGE_','page');
define('_WBLANGADMIN_EDITBOX_EDITCONTENT_','content');
define('_WBLANGADMIN_EDITBOX_EDITMENU_','menu');
define('_WBLANGADMIN_EDITBOX_EDITBLOCK_','block');

define('_WBLANGADMIN_EDITBOX_MEDIA_','media');

define('_WBLANGADMIN_EDITBOX_SYSTEM_','system');
define('_WBLANGADMIN_EDITBOX_SYSTEMSETTINGS_','settings');
define('_WBLANGADMIN_EDITBOX_SYSTEMEXTENDED_','extended');
define('_WBLANGADMIN_EDITBOX_SYSTEMSTYLES_','stylesheets');
define('_WBLANGADMIN_EDITBOX_SYSTEMCATS_','categories');
define('_WBLANGADMIN_EDITBOX_SYSTEMLINKS_','link colors');
define('_WBLANGADMIN_EDITBOX_SYSTEMPATTERN_','pattern');
define('_WBLANGADMIN_EDITBOX_SYSTEMFORMS_','forms');
define('_WBLANGADMIN_EDITBOX_SYSTEMLANGS_','languages');
define('_WBLANGADMIN_EDITBOX_SYSTEMUSERS_','users');
define('_WBLANGADMIN_EDITBOX_SYSTEMMODMAKER_','modmaker');

define('_WBLANGADMIN_EDITBOX_MODULES_','modules');

define('_WBLANGADMIN_EDITBOX_LOGOUT_','logout');


// popup windows

define('_WBLANGADMIN_POPUPWIN_PAGETOOFF_','The current page is taken offline.');
define('_WBLANGADMIN_POPUPWIN_PAGETOON_','The current page is brought online.');
define('_WBLANGADMIN_POPUPWIN_PAGEVERSION_','The last version of the current page, saved on %s, is restored.');
define('_WBLANGADMIN_POPUPWIN_VERSION_DATEFORMAT_','y-n-j, H:i');
define('_WBLANGADMIN_POPUPWIN_PAGEDELETE_','Page _STRING_ will be deleted!');
define('_WBLANGADMIN_POPUPWIN_PAGERENAME_','Page _STRING_OLD_ will be renamed to _STRING_NEW_');
define('_WBLANGADMIN_POPUPWIN_LAYOUTDELETE_','Layout _STRING_ will be deleted!');
define('_WBLANGADMIN_POPUPWIN_PATTERNDELETE_','The pattern _STRING_ will be deleted!');
define('_WBLANGADMIN_POPUPWIN_MENUDELETE_','Menu _STRING_ will be deleted!');
define('_WBLANGADMIN_POPUPWIN_BLOCKDELETE_','Block _STRING_ will be deleted!');
define('_WBLANGADMIN_POPUPWIN_LANGUAGE_','The language of the current page will be replaced by _STRING_.');
define('_WBLANGADMIN_POPUPWIN_WRITEABLE_FILE_','There are no access rights to file %s. Please CHMOD set according to the information of the provider.');
define('_WBLANGADMIN_POPUPWIN_WRITEABLE_FOLDER_','There are no access rights to directory %s. Please CHMOD set according to the information of the provider.');
define('_WBLANGADMIN_POPUPWIN_RECEIVERDELETE_','After the delete forms that are no longer using will be shipped this receiver!');
define('_WBLANGADMIN_POPUPWIN_NOPREVIEW_','Please first select a file!');
define('_WBLANGADMIN_POPUPWIN_SAVE_NAMEEXISTS_','The selected file name already exists.');
define('_WBLANGADMIN_POPUPWIN_SAVE_NONAME_','Please enter a file name and select a layout.');
define('_WBLANGADMIN_POPUPWIN_SAVE_NOLOGIN_','Please enter a layout name.');
define('_WBLANGADMIN_POPUPWIN_SAVE_NOUSER_','You have assigned a user name.');
define('_WBLANGADMIN_POPUPWIN_SAVE_WRONGSIGNS_','Illegal characters in the user name.');
define('_WBLANGADMIN_POPUPWIN_SAVE_WRONGPASS_','The password entered was incorrect.');
define('_WBLANGADMIN_POPUPWIN_SAVE_WRONGPASSSIGNS_','Signs: A to Z, a to z, 0 to 9 and #+-_*@%&=!?');
define('_WBLANGADMIN_POPUPWIN_SAVE_OPENCONF_','The configuration file could not be opened.');
define('_WBLANGADMIN_POPUPWIN_SAVE_CONFSAVE_','Your changes could not be saved.');
define('_WBLANGADMIN_POPUPWIN_SAVE_CONFSAVEOK_','Your changes have been saved successfully.');
define('_WBLANGADMIN_POPUPWIN_SAVE_MENUEXISTS_','The selected menu name already exists.');
define('_WBLANGADMIN_POPUPWIN_SAVE_DEFAULTLINK_','front page');
define('_WBLANGADMIN_POPUPWIN_SAVE_MENUNAME_','Please enter a menu name.');
define('_WBLANGADMIN_POPUPWIN_SAVE_BLOCKEXISTS_','The selected block name already exists.');
define('_WBLANGADMIN_POPUPWIN_SAVE_DEFAULTBLOCK_','new block');
define('_WBLANGADMIN_POPUPWIN_SAVE_BLOCKNAME_','Please enter a block name.');

define('_WBLANGADMIN_POPUPWIN_PATTERN_ISSAVED_','The pattern has been created.');
define('_WBLANGADMIN_POPUPWIN_LAYOUT_ISSAVED_','The layout file has been created.');
define('_WBLANGADMIN_POPUPWIN_MENU_ISSAVED_','The menu file has been created.');
define('_WBLANGADMIN_POPUPWIN_BLOCK_ISSAVED_','The block file has been created.');
define('_WBLANGADMIN_POPUPWIN_PAGE_ISSAVED_','The new page has been created.');

define('_WBLANGADMIN_POPUPWIN_PATTERN_ISDELETED_','The pattern has been deleted.');
define('_WBLANGADMIN_POPUPWIN_LAYOUT_ISDELETED_','The layout file has been deleted.');
define('_WBLANGADMIN_POPUPWIN_MENU_ISDELETED_','The menu file has been deleted.');
define('_WBLANGADMIN_POPUPWIN_BLOCK_ISDELETED_','The block file has been deleted.');
define('_WBLANGADMIN_POPUPWIN_PAGE_ISDELETED_','The page has been deleted.');
define('_WBLANGADMIN_POPUPWIN_DEFPAGE_ISDELETED_','The current page has been deleted.');

define('_WBLANGADMIN_POPUPWIN_COLUMNS_INSERTED_','The columns has been inserted.');
define('_WBLANGADMIN_POPUPWIN_COLUMNS_NOTINSERTED_','The columns could not be inserted.');
define('_WBLANGADMIN_POPUPWIN_COLUMNS_DELETED_','The columns was deleted.');
define('_WBLANGADMIN_POPUPWIN_COLUMNS_NOTDELETED_','The columns could not be deleted.');
define('_WBLANGADMIN_POPUPWIN_COLUMNS_NOTWRITEABLE_','is not writable!');

define('_WBLANGADMIN_POPUPWIN_ROUTE_RELOAD_','The page needs to be reloaded!');
define('_WBLANGADMIN_POPUPWIN_ROUTE_NEWPAGE_','Forwarding to the new page:');
define('_WBLANGADMIN_POPUPWIN_ROUTE_FRONTPAGE_','The current page no longer exists. You will be redirected to home page!');

define('_WBLANGADMIN_POPUPWIN_CATREQUEST_','Move page from _STRING_OLD_ to _STRING_NEW_');
define('_WBLANGADMIN_POPUPWIN_CATEGORIES_ISSAVED_','The setting has been changed.');
define('_WBLANGADMIN_POPUPWIN_CATEGORIES_NOTSAVED_','The setting could not be changed.');

define('_WBLANGADMIN_POPUPWIN_LANGUAGE_ISSAVED_','The language setting was changed.');

define('_WBLANGADMIN_POPUPWIN_TEMPFILE_NOTEXISTS_','The page does not exist!');
define('_WBLANGADMIN_POPUPWIN_TEMPFILE_NOTWRITEABLE_','is not writable!');
define('_WBLANGADMIN_POPUPWIN_TEMPFILE_PUBLIC_','The page was published.');
define('_WBLANGADMIN_POPUPWIN_TEMPFILE_DELETE_','If the temporarily saved version of\nthe page really want to delete?');



// offline pages

define('_WBLANGADMIN_OFF_PAGEUNPUBLIC_','This page version is temporarily saved and not publicly visible!');
define('_WBLANGADMIN_OFF_PAGEISOFFLINE_','This page is shut down and not be called!');
define('_WBLANGADMIN_OFF_PAGEISUSERS_','This page is only available to registered users!');


// buttons

define('_WBLANGADMIN_WIN_BUTTONS_PREVIEW_','preview');
define('_WBLANGADMIN_WIN_BUTTONS_SAVE_','save');
define('_WBLANGADMIN_WIN_BUTTONS_CANCEL_','cancel');
define('_WBLANGADMIN_WIN_BUTTONS_INSERT_','insert');
define('_WBLANGADMIN_WIN_BUTTONS_ADD_','add');
define('_WBLANGADMIN_WIN_BUTTONS_DELETE_','delete');
define('_WBLANGADMIN_WIN_BUTTONS_EDIT_','edit');
define('_WBLANGADMIN_WIN_BUTTONS_RENEW_','restore');
define('_WBLANGADMIN_WIN_BUTTONS_CALL_','call');
define('_WBLANGADMIN_WIN_BUTTONS_MODIFY_','modify');
define('_WBLANGADMIN_WIN_BUTTONS_CREATE_','create');
define('_WBLANGADMIN_WIN_BUTTONS_NEXT_','next');
define('_WBLANGADMIN_WIN_BUTTONS_BACK_','back');
define('_WBLANGADMIN_WIN_BUTTONS_FREE_','unlock');
define('_WBLANGADMIN_WIN_BUTTONS_ADDLANG_','add language');
define('_WBLANGADMIN_WIN_BUTTONS_OPENNEWWIN_','open new window');
define('_WBLANGADMIN_WIN_BUTTONS_MEDIA_','search icon');


// add page

define('_WBLANGADMIN_WIN_ADDPAGE_HEADLINE_','add a new page');
define('_WBLANGADMIN_WIN_ADDPAGE_TXT_1_','Enter a page name and select');
define('_WBLANGADMIN_WIN_ADDPAGE_TXT_2_','a layout or');
define('_WBLANGADMIN_WIN_ADDPAGE_TXT_3_','a page, that is to be copied.');
define('_WBLANGADMIN_WIN_ADDPAGE_TXT_4_','The new page can be automatically linked in a menu.');
define('_WBLANGADMIN_WIN_ADDPAGE_PAGENAME_','page name');
define('_WBLANGADMIN_WIN_ADDPAGE_PAGEVALUE_','new page');
define('_WBLANGADMIN_WIN_ADDPAGE_COPYOF_','as copy');
define('_WBLANGADMIN_WIN_ADDPAGE_LAYOUT_','layout');
define('_WBLANGADMIN_WIN_ADDPAGE_DUPLICAT_','duplicate of');
define('_WBLANGADMIN_WIN_ADDPAGE_LANGUAGE_','language');
define('_WBLANGADMIN_WIN_ADDPAGE_AUTOLINK_','page automatically link');
define('_WBLANGADMIN_WIN_ADDPAGE_MENUNAME_','menu');
define('_WBLANGADMIN_WIN_ADDPAGE_CATEGORIE_','category');
define('_WBLANGADMIN_WIN_ADDPAGE_LINKNAME_','link name');
define('_WBLANGADMIN_WIN_ADDPAGE_LINKVALUE_','new link');
define('_WBLANGADMIN_WIN_ADDPAGE_LINKPOS_','position');


// delete page

define('_WBLANGADMIN_WIN_DELPAGE_HEADLINE_','delete page');
define('_WBLANGADMIN_WIN_DELPAGE_ATTENTION_','ATTENTION!');
define('_WBLANGADMIN_WIN_DELPAGE_TXT_','All links are provided as static text in the files. Would search for all files, the system too burdensome. Therefore, only links are automatically deleted in menus. Links in blocks and pages must be removed manually.');
define('_WBLANGADMIN_WIN_DELPAGE_PAGENAME_','page name');
define('_WBLANGADMIN_WIN_DELPAGE_NOUNDO_','The deletion can not be undone!');


// rename page

define('_WBLANGADMIN_WIN_RENAME_HEADLINE_','rename page');
define('_WBLANGADMIN_WIN_RENAME_ATTENTION_','ATTENTION!');
define('_WBLANGADMIN_WIN_RENAME_TXT_','All links are provided as static text in the files. Would search for all files, the system too burdensome. Therefore, only links are automatically adapted menus in the rename. Links in blocks and pages need to be changed manually.');
define('_WBLANGADMIN_WIN_RENAME_OLDNAME_','current name');
define('_WBLANGADMIN_WIN_RENAME_NEWNAME_','new page name');
define('_WBLANGADMIN_WIN_RENAME_SAVE_NEWEXISTS_','A page with the new name already exists');
define('_WBLANGADMIN_WIN_RENAME_SAVE_NONEW_','You did not specify a new name');
define('_WBLANGADMIN_WIN_RENAME_SAVE_ISSAVEED_','The site has been renamed');
define('_WBLANGADMIN_WIN_RENAME_SAVE_RELOAD_','It is headed to the renamed page');


// backup (edit menu/block)

define('_WBLANGADMIN_WIN_EDIT_BACKUP_','backup');
define('_WBLANGADMIN_WIN_EDIT_BAKOF_','from');
define('_WBLANGADMIN_WIN_EDIT_NOBACKUP_','non-existent');

// edit menu

define('_WBLANGADMIN_WIN_EDITMENU_HEADLINE_','edit menu');
define('_WBLANGADMIN_WIN_EDITMENU_TXT_','Select a menu for editing.');
define('_WBLANGADMIN_WIN_EDITMENU_MENU_','menu');
define('_WBLANGADMIN_WIN_EDITMENU_REMENU_','The menu backup has been restored');
define('_WBLANGADMIN_WIN_EDITMENU_NOMENU_','The selected menu does not exist');

// edit block

define('_WBLANGADMIN_WIN_EDITBLOCK_HEADLINE_','edit block');
define('_WBLANGADMIN_WIN_EDITBLOCK_TXT_','Select a block for editing.');
define('_WBLANGADMIN_WIN_EDITBLOCK_BLOCK_','block');
define('_WBLANGADMIN_WIN_EDITBLOCK_REBLOCK_','The block backup has been restored');
define('_WBLANGADMIN_WIN_EDITBLOCK_NOBLOCK_','The selected block does not exist');


// change page

define('_WBLANGADMIN_WIN_OTHERPAGE_HEADLINE_','go to page');
define('_WBLANGADMIN_WIN_OTHERPAGE_TXT_','Go to any page of your choice.');
define('_WBLANGADMIN_WIN_OTHERPAGE_PAGENAME_','page name');


// page language

define('_WBLANGADMIN_WIN_PAGELANG_HEADLINE_','Change language of the page');
define('_WBLANGADMIN_WIN_PAGELANG_TXT_','Select the language for the current page.');
define('_WBLANGADMIN_WIN_PAGELANG_LANGUAGE_','language');
define('_WBLANGADMIN_WIN_PAGELANG_NOLANGUAGE_','No language exists');


// page category

define('_WBLANGADMIN_WIN_PAGECATS_HEADLINE_','Change category of the page');
define('_WBLANGADMIN_WIN_PAGECATS_TXT_','Select the category for the current page.');
define('_WBLANGADMIN_WIN_PAGECATS_CATEGORIE_','category');
define('_WBLANGADMIN_WIN_PAGECATS_NOCATEGORIES_','No category exists');


// highlight current link

define('_WBLANGADMIN_WIN_HIGHLITES_HEADLINE_','Highlight color of the current link');
define('_WBLANGADMIN_WIN_HIGHLITES_TXT_FILES_','Enter the name of the CSS class and select the menu in which the current link will be highlighted.');
define('_WBLANGADMIN_WIN_HIGHLITES_TXT_FOLDERS_','Enter the name of the CSS class, select the parent category to be highlighted and the menu in which the emphasis should be applied.');
define('_WBLANGADMIN_WIN_HIGHLITES_FILESTR_','Highlight links');
define('_WBLANGADMIN_WIN_HIGHLITES_FOLDERSTR_','Highlight category');
define('_WBLANGADMIN_WIN_HIGHLITES_CLASS_','class');
define('_WBLANGADMIN_WIN_HIGHLITES_PARENTSTR_','also highlight parent elements');
define('_WBLANGADMIN_WIN_HIGHLITES_PARENTSYES_','parent elements will be highlighted');
define('_WBLANGADMIN_WIN_HIGHLITES_PARENTSNO_','parent elements will not be highlighted');
define('_WBLANGADMIN_WIN_HIGHLITES_CATEGORIE_','category');
define('_WBLANGADMIN_WIN_HIGHLITES_MENU_','menu');
define('_WBLANGADMIN_WIN_HIGHLITES_CURRENTSTR_','also highlight the current category');
define('_WBLANGADMIN_WIN_HIGHLITES_AVAILABLE_','existing classes');
define('_WBLANGADMIN_WIN_HIGHLITES_CURRENTYES_','the current category will be highlighted');
define('_WBLANGADMIN_WIN_HIGHLITES_CURRENTNO_','the current category will not be highlighted');
define('_WBLANGADMIN_WIN_HIGHLITES_DELETE_','delete');
define('_WBLANGADMIN_WIN_HIGHLITES_FILE_ISSAVEED_','The class has been saved');
define('_WBLANGADMIN_WIN_HIGHLITES_FILE_NOTSAVEED_','For this menu already exists a class');
define('_WBLANGADMIN_WIN_HIGHLITES_FILE_ISDELETED_','The class has been deleted');
define('_WBLANGADMIN_WIN_HIGHLITES_FOLDER_ISSAVEED_','The category class has been saved');
define('_WBLANGADMIN_WIN_HIGHLITES_FOLDER_NOTSAVEED_','A class already exists in this category / menu combination');
define('_WBLANGADMIN_WIN_HIGHLITES_FOLDER_ISDELETED_','The category class has been deleted');


// column elements

define('_WBLANGADMIN_WIN_COLUMNS_HEADLINE_','column elements');
define('_WBLANGADMIN_WIN_COLUMNS_TEXT_','Select number of columns, adjust each column individually and then paste per Click in the page. Possible insertion points are highlighted on mouseover.');
define('_WBLANGADMIN_WIN_COLUMNS_LENGTH_','number of columns');
define('_WBLANGADMIN_WIN_COLUMNS_ROWCSS_','row CSS class');
define('_WBLANGADMIN_WIN_COLUMNS_NUM_','column');
define('_WBLANGADMIN_WIN_COLUMNS_SINGLE_','For a single column, no settings are needed.');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_','alignment');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_TOP_','top');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_MIDDLE_','center');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_BOTTOM_','bottom');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_FULL_','100% height');
define('_WBLANGADMIN_WIN_COLUMNS_WIDTH_','width');
define('_WBLANGADMIN_WIN_COLUMNS_COLSMALL_','small');
define('_WBLANGADMIN_WIN_COLUMNS_COLMEDIUM_','medium');
define('_WBLANGADMIN_WIN_COLUMNS_COLLARGE_','large');
define('_WBLANGADMIN_WIN_COLUMNS_HIDE_','hidden');
define('_WBLANGADMIN_WIN_COLUMNS_ORDER_','order');
define('_WBLANGADMIN_WIN_COLUMNS_EDITOR_','insert editor');
define('_WBLANGADMIN_WIN_COLUMNS_COLCSS_','column CSS class');
define('_WBLANGADMIN_WIN_COLUMNS_BUTTON_','pass column');

define('_WBLANGADMIN_COLUMNS_INSERT_PROMT_','distance upward in pixels');
define('_WBLANGADMIN_COLUMNS_INSERT_BEFORE_','insert column before');
define('_WBLANGADMIN_COLUMNS_INSERT_AFTER_','insert column after');

define('_WBLANGADMIN_WIN_DELCOLUMNS_HEADLINE_','delete columns');
define('_WBLANGADMIN_WIN_DELCOLUMNS_TEXT_','Deletable elements are selectable on mouse over to the page.');
define('_WBLANGADMIN_WIN_DELCOLUMNS_BUTTON_','choose columns');

define('_WBLANGADMIN_COLUMNS_DELETE_TEXT_','delete this column-element');


// access

define('_WBLANGADMIN_WIN_ACCESS_HEADLINE_','lock pages, user groups and user accounts');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_HEADLINE_','Setting the registration fields');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_TXT_','Decide what data you need from your users for identification. All fields are required for registration. The visitor login needed only the name and the user\'s password. The settings can not be subsequently changed.');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_REGFIELDS_','Registration fields have been defined and stored in the config.');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_DEFAULTGROUP_','standard');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_ERROR_','error message');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_DBOK_','The user database is rebuilt.');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_DBERROR_','Failed to create the database tables.');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_COMPLETE_','complete');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_','group ID');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_GROUPNAME_','group name');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USER_','user');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERGROUP_','user group');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERID_','user ID');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_NAME_','name');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_FIRSTNAME_','first name');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_COMPANY_','business');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_STREET_','house no.');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_TOWN_','postal code city');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_PHONE_','phone');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_','email address');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERNAME_','user name');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERPASS_','password');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERMAIL_','email');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERNAMEMAIL_','user or email');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_STATUS_','status');
define('_WBLANGADMIN_WIN_ACCESS_SAVECONF_','set');
define('_WBLANGADMIN_WIN_ACCESS_STAT_ENABLED_','activated');
define('_WBLANGADMIN_WIN_ACCESS_STAT_DISABLED_','closed');
define('_WBLANGADMIN_WIN_ACCESS_STAT_DELETED_','deleted');
define('_WBLANGADMIN_WIN_ACCESS_CONFOPENFILE_','The configuration file could not be opened.');
define('_WBLANGADMIN_WIN_ACCESS_CONFNOTSAVED_','Your changes could not be saved.');
define('_WBLANGADMIN_WIN_ACCESS_CONFSAVED_','Your changes have been saved.');
define('_WBLANGADMIN_WIN_ACCESS_BLOCKEDNOTSAVED_','The blocked pages could not be updated.');
define('_WBLANGADMIN_WIN_ACCESS_BLOCKEDSAVED_','The blocked pages have been updated.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPEXISTS_','A group with this name already exists.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPNOTSAVED_','The new group could not be entered.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPSAVED_','The new group was added.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPSETSNOTSAVED_','The group properties could not be changed.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPSETSSAVED_','The group properties has been changed.');
define('_WBLANGADMIN_WIN_ACCESS_USERNOPASS_','You have no password!');
define('_WBLANGADMIN_WIN_ACCESS_USERNOTSAVED_','The user could not be entered.');
define('_WBLANGADMIN_WIN_ACCESS_USERSAVED_','The user has been registered.');
define('_WBLANGADMIN_WIN_ACCESS_USERNOTDELETED_','The user could not be deleted.');
define('_WBLANGADMIN_WIN_ACCESS_USERDELETED_','The user has been deleted.');
define('_WBLANGADMIN_WIN_ACCESS_USERNOTFREE_','The user could not be unlocked.');
define('_WBLANGADMIN_WIN_ACCESS_USERFREE_','The user has been unlocked.');
define('_WBLANGADMIN_WIN_ACCESS_USERNOTMODIFIED_','The user data could not be changed.');
define('_WBLANGADMIN_WIN_ACCESS_USERMODIFIED_','The user data is changed.');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_LINK_','lock');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_LINK_','groups');
define('_WBLANGADMIN_WIN_ACCESS_USERS_LINK_','users');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_LINK_','settings');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_PAGES_','lock pages');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_TXT_','Decide which of your pages for normal visitors are supposed to be locked. Locked pages can be made ​​visible at the point groups for a selected group of users.');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_BLOCKED_','For visitors locked pages');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_FREE_','For visitors visible pages');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_USERGROUPS_','user groups');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_TXT_','To create a user group and specify which locked pages for this group to be visible (button \'edit\'). Also, single page parts are made ​​visible in the source code using the group ID for this group:');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_VISIBLEPART_','_VISIBLE_PART_');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_GROUPNAME_','group name');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_GROUPID_','group ID');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_FOR_','For');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_VISIBLEPAGES_','visible pages');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_BLOCKEDPAGES_','locked pages');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_VISITORS_','visitors');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_NEWGROUP_','new group');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_GROUPS_','groups');
define('_WBLANGADMIN_WIN_ACCESS_USERS_ACCOUNT_','user accounts');
define('_WBLANGADMIN_WIN_ACCESS_USERS_TXT_','Create access for users or if registration is enabled, turn on newly registered users and/or modify existing user data.');
define('_WBLANGADMIN_WIN_ACCESS_USERS_ADDUSER_','creating a user');
define('_WBLANGADMIN_WIN_ACCESS_USERS_FREENEWREGS_','new users unlock');
define('_WBLANGADMIN_WIN_ACCESS_USERS_EDITUSER_','edit user');
define('_WBLANGADMIN_WIN_ACCESS_USERS_SENDMAIL_','send mail');
define('_WBLANGADMIN_WIN_ACCESS_USERS_SENDDATA_','login data to user');
define('_WBLANGADMIN_WIN_ACCESS_USERS_SENDFREE_','unlocked profile');
define('_WBLANGADMIN_WIN_ACCESS_USERS_LANGUAGE_','language');
define('_WBLANGADMIN_WIN_ACCESS_USERS_NOGROUP_','There is no group for new users.');
define('_WBLANGADMIN_WIN_ACCESS_USERS_NEWREGS_','New registration');
define('_WBLANGADMIN_WIN_ACCESS_USERS_FREEISAUTO_','The release is set to automatic.');
define('_WBLANGADMIN_WIN_ACCESS_USERS_NONEWREGS_','There are no new registrations.');
define('_WBLANGADMIN_WIN_ACCESS_USERS_REACCOUNT_','This account can be reactivated!');
define('_WBLANGADMIN_WIN_ACCESS_USERS_DELETE_','delete');
define('_WBLANGADMIN_WIN_ACCESS_USERS_USERNOTFOUND_','User not found!');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_','basic settings');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_TXT_','Set the mode of operation of user registration. The admin mail address is required as the sender for registration confirmations.');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGISTRATION_','registration');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGON_','activ');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_LOGINLINK_','menu link login');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_LOGINSHOW_','show');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGBY_','activation by');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGADMIN_','Admin');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGAUTO_','automatically');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_ADMINMAIL_','mail to Admin');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_SENDADMINMAIL_','send');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_ADMINMAILADDRESS_','Admin mail address');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_USERMAIL_','mail to user');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGTOUSER_','send activation');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_DELUSER_','delete user');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_DELFROMDB_','delete from database');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGISTRATION_','If visitors can register on the website?');
define('_WBLANGADMIN_WIN_ACCESS_HELP_LOGINLINK_','If the login block is used, the login link in the users page menu can be disabled here.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGBY_','If you want the administrator to unlock new users or to the activation automatically be?');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGBYAUTO_','Auto can be selected only if at least one user group exists.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGGROUP_','Select the user group to which you belong to a newly registered user automatically.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_MAILTOADMIN_','If the administrator will receive an email with a new registration? Should be enabled if the activation is set to Admin.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_ADMINMAIL_','Enter the sender email address of the administrator for the registration email.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGTOUSER_','If a new user must activate his profile through a confirmation email? This measure can help to reduce spam submissions.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_DELFROMDB_','If a user should be removed when delete from the database? If the user account is maintained, the status is set to be deleted and the account can be reactivated.');


// styles

define('_WBLANGADMIN_WIN_EDITSTYLES_HEADLINE_','Edit styles');
define('_WBLANGADMIN_WIN_EDITSTYLES_FILENAME_','file name');


// categories

define('_WBLANGADMIN_WIN_CATEGORIES_HEADLINE_','Categories');
define('_WBLANGADMIN_WIN_CATEGORIES_TXT_','Categories are virtual and only displayed in the URL. Allowed are the signs a - z, 0 - 9, the underscore (_) and the slash (/) for subcategories.');
define('_WBLANGADMIN_WIN_CATEGORIES_NEWCAT_','create new category');
define('_WBLANGADMIN_WIN_CATEGORIES_DELCAT_','delete category');
define('_WBLANGADMIN_WIN_CATEGORIES_DELETE_','selection is cleared');
define('_WBLANGADMIN_WIN_CATEGORIES_BUTTONDEL_','delete');
define('_WBLANGADMIN_WIN_CATEGORIES_BUTTONNEW_','create');
define('_WBLANGADMIN_WIN_CATEGORIES_LANG_','language');
define('_WBLANGADMIN_WIN_CATEGORIES_NAME_','category / directory path');
define('_WBLANGADMIN_WIN_CATEGORIES_SAVED_','The categories have been updated.');


// forms

define('_WBLANGADMIN_WIN_FORMS_HEADLINE_','Recipient data for email forms');
define('_WBLANGADMIN_WIN_FORMS_TXT_','For sending email forms new recipients can be created and existing receivers can be edited or deleted here.');
define('_WBLANGADMIN_WIN_FORMS_INPUT_RECEIVER_','Name of the data set');
define('_WBLANGADMIN_WIN_FORMS_INPUT_MAILADDRESS_','email address');
define('_WBLANGADMIN_WIN_FORMS_INPUT_SHIPPER_','sender name');
define('_WBLANGADMIN_WIN_FORMS_INPUT_SUBJECT_','recipient subject');
define('_WBLANGADMIN_WIN_FORMS_INPUT_CONFIRM_','confirmation');
define('_WBLANGADMIN_WIN_FORMS_INPUT_CONFIRMSUB_','confirmation subject');
define('_WBLANGADMIN_WIN_FORMS_INPUT_SENTALERT_','Sent alert');
define('_WBLANGADMIN_WIN_FORMS_FIELDEMPTY_','You did not fill all the fields.');
define('_WBLANGADMIN_WIN_FORMS_WRONGMAILADDRESS_','This email address can not be used!');
define('_WBLANGADMIN_WIN_FORMS_ISSAVED_','The data of the receiver have been saved.');
define('_WBLANGADMIN_WIN_FORMS_ISDELETED_','The receiver has been deleted.');
define('_WBLANGADMIN_WIN_FORMS_ADDRECEIVER_','create new recipient');
define('_WBLANGADMIN_WIN_FORMS_EDITRECEIVER_','edit recipient');
define('_WBLANGADMIN_WIN_FORMS_NEWRECEIVER_','new recipient');


// pattern

define('_WBLANGADMIN_WIN_PATTERN_FUNCS_','Editor pattern');
define('_WBLANGADMIN_WIN_PATTERN_NEW_','create new pattern');
define('_WBLANGADMIN_WIN_PATTERN_EDIT_','edit pattern');
define('_WBLANGADMIN_WIN_PATTERN_DELETE_','delete pattern');
define('_WBLANGADMIN_WIN_PATTERN_FILENAME_','file name');
define('_WBLANGADMIN_WIN_PATTERN_DUPLICAT_','duplicate of');
define('_WBLANGADMIN_WIN_PATTERN_VALUE_','pattern');
define('_WBLANGADMIN_WIN_PATTERN_FILE_','file');


// edit pattern

define('_WBLANGADMIN_WIN_PATTERN_EDITTEMP_','edit editor pattern');
define('_WBLANGADMIN_WIN_PATTERN_EDITTITLE_','title');
define('_WBLANGADMIN_WIN_PATTERN_EDITIMAGE_','icon');
define('_WBLANGADMIN_WIN_PATTERN_EDITDESC_','description');
define('_WBLANGADMIN_WIN_PATTERN_EDITSOURCE_','pattern source');


// language settings

define('_WBLANGADMIN_WIN_LANGUAGE_HEADLINE_','language settings');
define('_WBLANGADMIN_WIN_LANGUAGE_FIELDEMPTY_','You did not fill all the fields.');
define('_WBLANGADMIN_WIN_LANGUAGE_ISSAVED_','The language settings have been updated.');
define('_WBLANGADMIN_WIN_LANGUAGE_STARTPAGES_','The home pages have been updated.');
define('_WBLANGADMIN_WIN_LANGUAGE_URLFOLDER_','The setting has been updated.');
define('_WBLANGADMIN_WIN_LANGUAGE_DELETETXT_SINGULAR_','When you delete this language all page mappings of this language are also deleted.');
define('_WBLANGADMIN_WIN_LANGUAGE_DELETETXT_PLURAL_','When you delete languages all page mappings of this languages are also deleted.');
define('_WBLANGADMIN_WIN_LANGUAGE_INSTALL_','install language');
define('_WBLANGADMIN_WIN_LANGUAGE_SETSTARTS_','set start pages');
define('_WBLANGADMIN_WIN_LANGUAGE_ORDER_','Languages ​​are displayed in the order they are created here.');
define('_WBLANGADMIN_WIN_LANGUAGE_LANG_','language');
define('_WBLANGADMIN_WIN_LANGUAGE_NOLANG_','no language available');
define('_WBLANGADMIN_WIN_LANGUAGE_EMPTY_','empty');
define('_WBLANGADMIN_WIN_LANGUAGE_NOTINLANG_','not assigned');
define('_WBLANGADMIN_WIN_LANGUAGE_SHORT_','shorthand');
define('_WBLANGADMIN_WIN_LANGUAGE_DESCRIPT_','designation');
define('_WBLANGADMIN_WIN_LANGUAGE_DELETE_','delete');
define('_WBLANGADMIN_WIN_LANGUAGE_POSITION_','position');
define('_WBLANGADMIN_WIN_LANGUAGE_STARTPAGE_','home page');
define('_WBLANGADMIN_WIN_LANGUAGE_POSUP_','up');
define('_WBLANGADMIN_WIN_LANGUAGE_POSDOWN_','down');
define('_WBLANGADMIN_WIN_LANGUAGE_STARTBYLANG_','To specify which page the home page of a language to be.');


// basic settings

define('_WBLANGADMIN_WIN_SETTINGS_HEADLINE_','basic settings');
define('_WBLANGADMIN_WIN_SETTINGS_TEXT_','Change the credentials of the administrator and other settings');
define('_WBLANGADMIN_WIN_SETTINGS_USERNAME_','user name');
define('_WBLANGADMIN_WIN_SETTINGS_NEWPASS_','new password');
define('_WBLANGADMIN_WIN_SETTINGS_REPEATPASS_','repeat password');
define('_WBLANGADMIN_WIN_SETTINGS_ADMINLANG_','language administrator');
define('_WBLANGADMIN_WIN_SETTINGS_HOMEPAGE_','home page');
define('_WBLANGADMIN_WIN_SETTINGS_IMGSCAL_','default image scaling for upload');
define('_WBLANGADMIN_WIN_SETTINGS_SMALLIMG_','image page');
define('_WBLANGADMIN_WIN_SETTINGS_BIGIMG_','image lightbox');
define('_WBLANGADMIN_WIN_SETTINGS_IMGWIDTH_','width');
define('_WBLANGADMIN_WIN_SETTINGS_IMGHEIGHT_','height');


// advanced settings

define('_WBLANGADMIN_WIN_ADVANCED_HEADLINE_','Advanced administrator features');
define('_WBLANGADMIN_WIN_ADVANCED_EDITPAGE_','Edit the current page');
define('_WBLANGADMIN_WIN_ADVANCED_EDITPAGE_TXT_','Click Edit to make changes to the layout of the current page.');
define('_WBLANGADMIN_WIN_ADVANCED_ERRORPAGE_','Define custom error page');
define('_WBLANGADMIN_WIN_ADVANCED_ERRORPAGE_TXT_','For the error message %s must be in the error page.');
define('_WBLANGADMIN_WIN_ADVANCED_ERRORPAGE_NAME_','error page');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTFUNCS_','layout functions');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTNEW_','create new layout');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTEDIT_','edit layout');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTDELETE_','delete layout');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTNAME_','layout name');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTVALUE_','new_layout');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTDUPLICAT_','duplicate of');


define('_WBLANGADMIN_WIN_ADVANCED_MENUFUNCS_','menu functions');
define('_WBLANGADMIN_WIN_ADVANCED_MENUNEW_','create new menu');
define('_WBLANGADMIN_WIN_ADVANCED_MENUDELETE_','delete menu');
define('_WBLANGADMIN_WIN_ADVANCED_MENUNAME_','menu name');
define('_WBLANGADMIN_WIN_ADVANCED_MENUVALUE_','new_menu');
define('_WBLANGADMIN_WIN_ADVANCED_MENUDUPLICAT_','duplicate of');

define('_WBLANGADMIN_WIN_ADVANCED_BLOCKFUNCS_','block functions');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKNEW_','create new block');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKDELETE_','delete block');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKNAME_','block name');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKVALUE_','new_block');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKDUPLICAT_','duplicate of');
define('_WBLANGADMIN_WIN_ADVANCED_PHPINFO_','View information about the server (PHP info)');
define('_WBLANGADMIN_WIN_ADMIN_MODULES_','Administration modules');









