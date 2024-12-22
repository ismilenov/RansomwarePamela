<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

define('_MODMAKERLANGADMIN_TITLE_','Administration');
define('_MODMAKERLANGADMIN_MODNAME_','###MODULENAME###');

define('_MODMAKERLANGADMIN_LOGIN_HEADER_','Login for editors');
define('_MODMAKERLANGADMIN_LOGIN_USERNAME_','user name');
define('_MODMAKERLANGADMIN_LOGIN_PASSWORD_','password');
define('_MODMAKERLANGADMIN_LOGIN_SUBMIT_','login');
define('_MODMAKERLANGADMIN_LOGIN_ATTEMPTS_','Login is blocked for __TIME__ minutes!');
define('_MODMAKERLANGADMIN_LOGIN_NOPERMISSION_','You have no access authorization');
define('_MODMAKERLANGADMIN_NOTAVAILFORSUBS_','The settings can not be edited by editors!');
define('_MODMAKERLANGADMIN_LOGOUT_','logout');

define('_MODMAKERLANGADMIN_SETCHMODFOR_','The file /data/###CHMODMODNAME###.db and the /media directory must be writable!');
define('_MODMAKERLANGADMIN_WRONGCHMOD_','The following files / directories are not writable:');
define('_MODMAKERLANGADMIN_SETRIGHTCHMODS_','Please set chmod ###CHMODFOLDER### for files and chmod ###CHMODFILES### for directories with an FTP program.');
define('_MODMAKERLANGADMIN_INSTALL_','installation');
define('_MODMAKERLANGADMIN_SETTINGS_','settings');

define('_MODMAKERLANGADMIN_OPTION_','option');
define('_MODMAKERLANGADMIN_OPTIONS_','options');
define('_MODMAKERLANGADMIN_NOOPTIONGROUPS_','There is no options group have been created yet');
define('_MODMAKERLANGADMIN_NEWOPTION_','new option group');
define('_MODMAKERLANGADMIN_EDITOPTION_','edit option group');
define('_MODMAKERLANGADMIN_OPTIONIMGSUPLOAD_','upload option images');
define('_MODMAKERLANGADMIN_OPTIMGSUBFOLDER_','create a new subdirectory');
define('_MODMAKERLANGADMIN_CHOOSE_UPLOAD_','choose');
define('_MODMAKERLANGADMIN_NOAVAILOPTION_','No options available!');
define('_MODMAKERLANGADMIN_NOEXISTOPTION_','This option group does not exist!');
define('_MODMAKERLANGADMIN_OPTIONGROUP_','group');
define('_MODMAKERLANGADMIN_OPTIONVALUES_','options');
define('_MODMAKERLANGADMIN_OPTIONIMGS_','images');
define('_MODMAKERLANGADMIN_OPTIONIMGSOPEN_','list');
define('_MODMAKERLANGADMIN_OPTIONIMGSCLOSE_','close');
define('_MODMAKERLANGADMIN_OPTIONHEADLINE_','one option per line, disconnect values with |');
define('_MODMAKERLANGADMIN_OPTIONWITHDEFINES_','it can also be input language defines');
define('_MODMAKERLANGADMIN_OPTIONEXPLAIN_','Options are issued in the options.tpl template<br />individual templates can be created with options_(ID).tpl<br />output of the values ​​as an array $db_data[\'option\'][0], $db_data[\'option\'][1]...');

define('_MODMAKERLANGADMIN_CATEGORY_','category');
define('_MODMAKERLANGADMIN_CATEGORIES_','categories');
define('_MODMAKERLANGADMIN_NEWCATEGORY_','new category');
define('_MODMAKERLANGADMIN_SUBCATEGORIES_','category tree');
define('_MODMAKERLANGADMIN_EDITCATEGORY_','edit category');
define('_MODMAKERLANGADMIN_NOSELECTCATEGORY_','There is no category chosen!');
define('_MODMAKERLANGADMIN_NOAVAILCATEGORY_','No category to display!');
define('_MODMAKERLANGADMIN_NOEXISTCATEGORY_','This category does not exist!');
define('_MODMAKERLANGADMIN_THEREISNOCAT_','The category tree has not been created');
define('_MODMAKERLANGADMIN_CATDESCRIPT_','description');
define('_MODMAKERLANGADMIN_CATIMAGE_','image');
define('_MODMAKERLANGADMIN_CATLINK_','menu link');
define('_MODMAKERLANGADMIN_BASECATENTRY_','Entry point');

define('_MODMAKERLANGADMIN_TOPIC_','topic');
define('_MODMAKERLANGADMIN_TOPICS_','topics');
define('_MODMAKERLANGADMIN_NEWTOPIC_','new topic');
define('_MODMAKERLANGADMIN_COPYTOPIC_','copy topic');
define('_MODMAKERLANGADMIN_EDITTOPIC_','edit topic');
define('_MODMAKERLANGADMIN_NOSELECTTOPIC_','No selected topic!');
define('_MODMAKERLANGADMIN_NOAVAILTOPIC_','No topics to display!');
define('_MODMAKERLANGADMIN_NOEXISTTOPIC_','This topic does not exist!');
define('_MODMAKERLANGADMIN_TOPICCOPYID_','enter the ID of to copied topic');
define('_MODMAKERLANGADMIN_TOPICCOPYOF_','copy of topic wih ID');

define('_MODMAKERLANGADMIN_DATA_','record');
define('_MODMAKERLANGADMIN_DATAS_','records');
define('_MODMAKERLANGADMIN_NEWDATA_','new record');
define('_MODMAKERLANGADMIN_COPYDATA_','copy record');
define('_MODMAKERLANGADMIN_EDITDATA_','edit record');
define('_MODMAKERLANGADMIN_NOSELECTDATA_','No selected record!');
define('_MODMAKERLANGADMIN_NOAVAILDATA_','No records to display!');
define('_MODMAKERLANGADMIN_NOEXISTDATA_','This record does not exist!');
define('_MODMAKERLANGADMIN_DATADELLAST_','The start record can only be moved as a last resort.');
define('_MODMAKERLANGADMIN_DATACOPYID_','enter the ID of to copied record');
define('_MODMAKERLANGADMIN_DATACOPYOF_','copy of record with ID');

define('_MODMAKERLANGADMIN_WRONGLANG_','The target data must be in the same language');
define('_MODMAKERLANGADMIN_COPYDIST_','The start record can not be copied');
define('_MODMAKERLANGADMIN_EDITCOPY_','Copies are not editable');
define('_MODMAKERLANGADMIN_CHANGETOCOPY_','Move to copy is not possible');

define('_MODMAKERLANGADMIN_BUTTON_EDIT_','edit');
define('_MODMAKERLANGADMIN_BUTTON_GETORG_','call original');
define('_MODMAKERLANGADMIN_BUTTON_DELETE_','delete');
define('_MODMAKERLANGADMIN_PROMPT_SHOULD_','Should');
define('_MODMAKERLANGADMIN_PROMPT_COPY_','copy of');
define('_MODMAKERLANGADMIN_PROMPT_REALDEL_','really be deleted?');
define('_MODMAKERLANGADMIN_BUTTON_CHANGETO_','move');
define('_MODMAKERLANGADMIN_PROMPT_NEWID_','Please enter the ID');
define('_MODMAKERLANGADMIN_PROMPT_OFCAT_','of the new category');
define('_MODMAKERLANGADMIN_PROMPT_OFTOPIC_','of the new topic');
define('_MODMAKERLANGADMIN_PROMPT_INSERT_','!');
define('_MODMAKERLANGADMIN_PROMPT_ERROR_','Incorrect input!');
define('_MODMAKERLANGADMIN_BUTTON_ONLINE_','online');
define('_MODMAKERLANGADMIN_BUTTON_OFFLINE_','offline');
define('_MODMAKERLANGADMIN_BUTTON_SETON_','unlock');
define('_MODMAKERLANGADMIN_BUTTON_SETOFF_','block');
define('_MODMAKERLANGADMIN_BUTTON_INSERT_','insert');
define('_MODMAKERLANGADMIN_BUTTON_SHOW_','show');
define('_MODMAKERLANGADMIN_BUTTON_HOCH_','up');
define('_MODMAKERLANGADMIN_BUTTON_RUNTER_','down');
define('_MODMAKERLANGADMIN_BUTTON_LINKS_','out');
define('_MODMAKERLANGADMIN_BUTTON_RECHTS_','in');
define('_MODMAKERLANGADMIN_BUTTON_MAKEFOLDER_','create folder');
define('_MODMAKERLANGADMIN_BUTTON_UPLOAD_','upload');
define('_MODMAKERLANGADMIN_BUTTON_SAVE_','save');
define('_MODMAKERLANGADMIN_BUTTON_CANCEL_','cancel');
define('_MODMAKERLANGADMIN_BUTTON_CHOOSE_','choose');
define('_MODMAKERLANGADMIN_NAVI_PAGE_','page');
define('_MODMAKERLANGADMIN_ALERT_UPLOADWRONGMIME_','The selected file type is not allowed!');
define('_MODMAKERLANGADMIN_ALERT_UPLOADCOMPLETE_','Transfer is complete! Please save the record, so that the file is available.');
define('_MODMAKERLANGADMIN_ALERT_UPLOADLARGE_1_','The selected file must be uploaded separately due to their size!');
define('_MODMAKERLANGADMIN_ALERT_UPLOADLARGE_2_','The upload large files usually takes a longer period of time.');
define('_MODMAKERLANGADMIN_ALERT_UPLOADLARGE_3_','The new record must be after the upload also still be saved, the uploaded file is not otherwise available.');
define('_MODMAKERLANGADMIN_ALERT_UPLOADSTATE_','upload status');

define('_MODMAKERLANGADMIN_FIELD_LOGINFORSUBS_','login for editors');
define('_MODMAKERLANGADMIN_FIELD_NOSUBEDITOR_','disable editor login');
define('_MODMAKERLANGADMIN_FIELD_HIDEFORSUBS_','hide settings for editors');
define('_MODMAKERLANGADMIN_FIELD_USERSASSUBS_','set user group as editors');
define('_MODMAKERLANGADMIN_FIELD_LANGUAGE_','language');
define('_MODMAKERLANGADMIN_FIELD_NOLANGUAGES_','no languages ​​available');
define('_MODMAKERLANGADMIN_FIELD_NOUSERS_','no groups available');
define('_MODMAKERLANGADMIN_FIELD_ISSTARTDATA_','start record is ​​available every time');
define('_MODMAKERLANGADMIN_FIELD_DISPLAY_','display');
define('_MODMAKERLANGADMIN_FIELD_DISPLAYFROM_','from');
define('_MODMAKERLANGADMIN_FIELD_DISPLAYTO_','to');
define('_MODMAKERLANGADMIN_FIELD_CLEARDATE_','empty date');
define('_MODMAKERLANGADMIN_FIELD_SETPERPAGE_','display per page');
define('_MODMAKERLANGADMIN_FIELD_DATAPERPAGE_','(blank for unlimited)');
define('_MODMAKERLANGADMIN_FIELD_SORTING_','sorting');
define('_MODMAKERLANGADMIN_FIELD_SORTNEWFIRST_','backward');
define('_MODMAKERLANGADMIN_FIELD_SORTOLDFIRST_','forward');
define('_MODMAKERLANGADMIN_FIELD_SORTBYFIELD_','by field');
define('_MODMAKERLANGADMIN_FIELD_NEWCREATED_','New created');
define('_MODMAKERLANGADMIN_FIELD_CATMENU_','category tree');
define('_MODMAKERLANGADMIN_FIELD_ONLYMENU_','only as a menu (no main categories page)');
define('_MODMAKERLANGADMIN_FIELD_PERMISSION_','write permissions');
define('_MODMAKERLANGADMIN_FIELD_SELUSERGROUP_','user groups');
define('_MODMAKERLANGADMIN_FIELD_NOUSERGROUP_','none (all visitors have write permission)');
define('_MODMAKERLANGADMIN_FIELD_MULTISELECT_','hold down CTRL for multiple selection');
define('_MODMAKERLANGADMIN_FIELD_RELEASING_','releasing');
define('_MODMAKERLANGADMIN_FIELD_USERINPUTS_','visitors input');
define('_MODMAKERLANGADMIN_FIELD_RELEASEDIRECT_','immediately turn online');
define('_MODMAKERLANGADMIN_FIELD_RELEASEBYHAND_','unlock by hand');
define('_MODMAKERLANGADMIN_FIELD_BOXCOMMON_','lightbox generally');
define('_MODMAKERLANGADMIN_FIELD_BOXSINGLE_','open images individually');
define('_MODMAKERLANGADMIN_FIELD_BOXDATASTEP_','continue and back within a dataset');
define('_MODMAKERLANGADMIN_FIELD_BOXFULLSTEP_','continue and back on all records (list view)');
define('_MODMAKERLANGADMIN_FIELD_BOXONLYONFULL_','no lightbox on list view');
define('_MODMAKERLANGADMIN_FIELD_DBWITHOUTFILE_','database entry exists without a file');
define('_MODMAKERLANGADMIN_FIELD_STARTONTOPIC_','reply start record');
define('_MODMAKERLANGADMIN_FIELD_NUMBNEWEST_','number of latest records');
define('_MODMAKERLANGADMIN_FIELD_NEWESTINBLOCK_','display');
define('_MODMAKERLANGADMIN_FIELD_COPYHANDLING_','copies');
define('_MODMAKERLANGADMIN_FIELD_TOPICCOPIES_','make topic copy online/offline switchable');
define('_MODMAKERLANGADMIN_FIELD_DATACOPIES_','make record copy online/offline switchable');
define('_MODMAKERLANGADMIN_FIELD_SHOWINLIST_','show in topics list');
define('_MODMAKERLANGADMIN_FIELD_SHOWINTOPIC_','show on top of every pager page');
define('_MODMAKERLANGADMIN_FIELD_SHOWINTOPICFILTER_','do not filter');
define('_MODMAKERLANGADMIN_FIELD_DATAPREVNEXTNAVI_','exclude from records navigation');
define('_MODMAKERLANGADMIN_FIELD_FILTER_','filter');
define('_MODMAKERLANGADMIN_FIELD_FILTERMAINTAIN_','receive on whole page');
define('_MODMAKERLANGADMIN_FIELD_USEASTITLE_','use this field as the title');
define('_MODMAKERLANGADMIN_FIELD_SELECTOPTIONS_','select options');
define('_MODMAKERLANGADMIN_FIELD_SELECTPROTOTYPE_','one select option per line - pattern: value|text|__select__');
define('_MODMAKERLANGADMIN_FIELD_SELECTLEEROPT_','If text is empty, value is taken. \'---\' for empty option');
define('_MODMAKERLANGADMIN_FIELD_OPTWITHDEFINES_','for value and text are language defines possible');
define('_MODMAKERLANGADMIN_FIELD_OPTASSELECTED_','\'__select__\' is optional for selected onLoad (only once)');
define('_MODMAKERLANGADMIN_FIELD_SHOWSELECTASRADIO_','show as radio buttons (\'---\' will be ignored)');
define('_MODMAKERLANGADMIN_FIELD_NOSELECTS_','no fields have been defined');
define('_MODMAKERLANGADMIN_FIELD_CHECKBOX_','checkboxes');
define('_MODMAKERLANGADMIN_FIELD_CHECKPROTOTYPE_','one checkbox per line - pattern: name|value|text|__check__');
define('_MODMAKERLANGADMIN_FIELD_CHECKLEEROPT_','If text is empty, value is taken.');
define('_MODMAKERLANGADMIN_FIELD_BOXWITHDEFINES_','for value and text are language defines possible');
define('_MODMAKERLANGADMIN_FIELD_BOXASCHECKED_','\'__check__\' is optional for activ onLoad (only once)');
define('_MODMAKERLANGADMIN_FIELD_NOCHECKS_','There are no checkboxes were still defined');
define('_MODMAKERLANGADMIN_FIELD_BOXOPEN_','open image in the lightbox');
define('_MODMAKERLANGADMIN_FIELD_BOXOPENS_','open images in the lightbox');
define('_MODMAKERLANGADMIN_FIELD_PLUSIMGUPLOAD_','add image upload field');
define('_MODMAKERLANGADMIN_FIELD_ONLYMIMEFILE_','upload only for following file types');
define('_MODMAKERLANGADMIN_FIELD_MIMEEXPLAIN_1_','for multiple file types separate with | sign - empty for all');
define('_MODMAKERLANGADMIN_FIELD_MIMEEXPLAIN_2_','file header (MIME type) is queried, not the extension');
define('_MODMAKERLANGADMIN_FIELD_FILETYPE_','allowed file types');
define('_MODMAKERLANGADMIN_FIELD_PROTECTFILE_','protect the file access');
define('_MODMAKERLANGADMIN_FIELD_DOWNLOADFORGROUP_','download only for users group');
define('_MODMAKERLANGADMIN_FIELD_DOWNLOADFORALL_','all logged-in users');

define('_MODMAKERLANGADMIN_INPUT_TOPIC_','title');
define('_MODMAKERLANGADMIN_INPUT_FROMTIME_','date');
define('_MODMAKERLANGADMIN_OUTPUT_DOWNLOADCOUNTER_','times downloaded');
define('_MODMAKERLANGADMIN_HEADLINETITLE_SEO_','SEO browser title');
define('_MODMAKERLANGADMIN_HEADLINETITLE_CAT_','category');
define('_MODMAKERLANGADMIN_HEADLINETITLE_TOPIC_','topic');
define('_MODMAKERLANGADMIN_HEADLINETITLE_DATA_','record');
define('_MODMAKERLANGADMIN_HEADLINETITLE_SHOW_','get from data');
define('_MODMAKERLANGADMIN_SEOINPUT_METAS_','SEO');
define('_MODMAKERLANGADMIN_SEOINPUT_METATITLE_','browser title');
define('_MODMAKERLANGADMIN_SEOINPUT_METADESC_','meta description');
define('_MODMAKERLANGADMIN_SEOINPUT_METAKEYS_','meta keywords');

###ADMIN_LANGS###











