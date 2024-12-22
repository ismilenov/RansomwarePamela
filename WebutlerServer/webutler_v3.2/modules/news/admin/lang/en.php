<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

define('_NEWSLANGADMIN_TITLE_','Administration');
define('_NEWSLANGADMIN_MODNAME_','NEWS');

define('_NEWSLANGADMIN_LOGIN_HEADER_','Login for editors');
define('_NEWSLANGADMIN_LOGIN_USERNAME_','user name');
define('_NEWSLANGADMIN_LOGIN_PASSWORD_','password');
define('_NEWSLANGADMIN_LOGIN_SUBMIT_','login');
define('_NEWSLANGADMIN_LOGIN_ATTEMPTS_','Login is blocked for __TIME__ minutes!');
define('_NEWSLANGADMIN_LOGIN_NOPERMISSION_','You have no access authorization');
define('_NEWSLANGADMIN_NOTAVAILFORSUBS_','The settings can not be edited by editors!');
define('_NEWSLANGADMIN_LOGOUT_','logout');

define('_NEWSLANGADMIN_SETCHMODFOR_','The file /data/###CHMODMODNAME###.db and the /media directory must be writable!');
define('_NEWSLANGADMIN_WRONGCHMOD_','The following files / directories are not writable:');
define('_NEWSLANGADMIN_SETRIGHTCHMODS_','Please set chmod ###CHMODFOLDER### for files and chmod ###CHMODFILES### for directories with an FTP program.');
define('_NEWSLANGADMIN_INSTALL_','installation');
define('_NEWSLANGADMIN_SETTINGS_','settings');

define('_NEWSLANGADMIN_OPTION_','option');
define('_NEWSLANGADMIN_OPTIONS_','options');
define('_NEWSLANGADMIN_NOOPTIONGROUPS_','There is no options group have been created yet');
define('_NEWSLANGADMIN_NEWOPTION_','new option group');
define('_NEWSLANGADMIN_EDITOPTION_','edit option group');
define('_NEWSLANGADMIN_OPTIONIMGSUPLOAD_','upload option images');
define('_NEWSLANGADMIN_OPTIMGSUBFOLDER_','create a new subdirectory');
define('_NEWSLANGADMIN_CHOOSE_UPLOAD_','choose');
define('_NEWSLANGADMIN_NOAVAILOPTION_','No options available!');
define('_NEWSLANGADMIN_NOEXISTOPTION_','This option group does not exist!');
define('_NEWSLANGADMIN_OPTIONGROUP_','group');
define('_NEWSLANGADMIN_OPTIONVALUES_','options');
define('_NEWSLANGADMIN_OPTIONIMGS_','images');
define('_NEWSLANGADMIN_OPTIONIMGSOPEN_','list');
define('_NEWSLANGADMIN_OPTIONIMGSCLOSE_','close');
define('_NEWSLANGADMIN_OPTIONHEADLINE_','one option per line, disconnect values with |');
define('_NEWSLANGADMIN_OPTIONWITHDEFINES_','it can also be input language defines');
define('_NEWSLANGADMIN_OPTIONEXPLAIN_','Options are issued in the options.tpl template<br />individual templates can be created with options_(ID).tpl<br />output of the values ​​as an array $db_data[\'option\'][0], $db_data[\'option\'][1]...');

define('_NEWSLANGADMIN_CATEGORY_','category');
define('_NEWSLANGADMIN_CATEGORIES_','categories');
define('_NEWSLANGADMIN_NEWCATEGORY_','new category');
define('_NEWSLANGADMIN_SUBCATEGORIES_','category tree');
define('_NEWSLANGADMIN_EDITCATEGORY_','edit category');
define('_NEWSLANGADMIN_NOSELECTCATEGORY_','There is no category chosen!');
define('_NEWSLANGADMIN_NOAVAILCATEGORY_','No category to display!');
define('_NEWSLANGADMIN_NOEXISTCATEGORY_','This category does not exist!');
define('_NEWSLANGADMIN_THEREISNOCAT_','The category tree has not been created');
define('_NEWSLANGADMIN_CATDESCRIPT_','description');
define('_NEWSLANGADMIN_CATIMAGE_','image');
define('_NEWSLANGADMIN_CATLINK_','menu link');
define('_NEWSLANGADMIN_BASECATENTRY_','Entry point');

define('_NEWSLANGADMIN_TOPIC_','topic');
define('_NEWSLANGADMIN_TOPICS_','topics');
define('_NEWSLANGADMIN_NEWTOPIC_','new topic');
define('_NEWSLANGADMIN_COPYTOPIC_','copy topic');
define('_NEWSLANGADMIN_EDITTOPIC_','edit topic');
define('_NEWSLANGADMIN_NOSELECTTOPIC_','No selected topic!');
define('_NEWSLANGADMIN_NOAVAILTOPIC_','No topics to display!');
define('_NEWSLANGADMIN_NOEXISTTOPIC_','This topic does not exist!');
define('_NEWSLANGADMIN_TOPICCOPYID_','enter the ID of to copied topic');
define('_NEWSLANGADMIN_TOPICCOPYOF_','copy of topic wih ID');

define('_NEWSLANGADMIN_DATA_','record');
define('_NEWSLANGADMIN_DATAS_','records');
define('_NEWSLANGADMIN_NEWDATA_','new record');
define('_NEWSLANGADMIN_COPYDATA_','copy record');
define('_NEWSLANGADMIN_EDITDATA_','edit record');
define('_NEWSLANGADMIN_NOSELECTDATA_','No selected record!');
define('_NEWSLANGADMIN_NOAVAILDATA_','No records to display!');
define('_NEWSLANGADMIN_NOEXISTDATA_','This record does not exist!');
define('_NEWSLANGADMIN_DATADELLAST_','The start record can only be moved as a last resort.');
define('_NEWSLANGADMIN_DATACOPYID_','enter the ID of to copied record');
define('_NEWSLANGADMIN_DATACOPYOF_','copy of record with ID');

define('_NEWSLANGADMIN_WRONGLANG_','The target data must be in the same language');
define('_NEWSLANGADMIN_COPYDIST_','The start record can not be copied');
define('_NEWSLANGADMIN_EDITCOPY_','Copies are not editable');
define('_NEWSLANGADMIN_CHANGETOCOPY_','Move to copy is not possible');

define('_NEWSLANGADMIN_BUTTON_EDIT_','edit');
define('_NEWSLANGADMIN_BUTTON_GETORG_','call original');
define('_NEWSLANGADMIN_BUTTON_DELETE_','delete');
define('_NEWSLANGADMIN_PROMPT_SHOULD_','Should');
define('_NEWSLANGADMIN_PROMPT_COPY_','copy of');
define('_NEWSLANGADMIN_PROMPT_REALDEL_','really be deleted?');
define('_NEWSLANGADMIN_BUTTON_CHANGETO_','move');
define('_NEWSLANGADMIN_PROMPT_NEWID_','Please enter the ID');
define('_NEWSLANGADMIN_PROMPT_OFCAT_','of the new category');
define('_NEWSLANGADMIN_PROMPT_OFTOPIC_','of the new topic');
define('_NEWSLANGADMIN_PROMPT_INSERT_','!');
define('_NEWSLANGADMIN_PROMPT_ERROR_','Incorrect input!');
define('_NEWSLANGADMIN_BUTTON_ONLINE_','online');
define('_NEWSLANGADMIN_BUTTON_OFFLINE_','offline');
define('_NEWSLANGADMIN_BUTTON_SETON_','unlock');
define('_NEWSLANGADMIN_BUTTON_SETOFF_','block');
define('_NEWSLANGADMIN_BUTTON_INSERT_','insert');
define('_NEWSLANGADMIN_BUTTON_SHOW_','show');
define('_NEWSLANGADMIN_BUTTON_HOCH_','up');
define('_NEWSLANGADMIN_BUTTON_RUNTER_','down');
define('_NEWSLANGADMIN_BUTTON_LINKS_','out');
define('_NEWSLANGADMIN_BUTTON_RECHTS_','in');
define('_NEWSLANGADMIN_BUTTON_MAKEFOLDER_','create folder');
define('_NEWSLANGADMIN_BUTTON_UPLOAD_','upload');
define('_NEWSLANGADMIN_BUTTON_SAVE_','save');
define('_NEWSLANGADMIN_BUTTON_CANCEL_','cancel');
define('_NEWSLANGADMIN_BUTTON_CHOOSE_','choose');
define('_NEWSLANGADMIN_NAVI_PAGE_','page');
define('_NEWSLANGADMIN_ALERT_UPLOADWRONGMIME_','The selected file type is not allowed!');
define('_NEWSLANGADMIN_ALERT_UPLOADCOMPLETE_','Transfer is complete! Please save the record, so that the file is available.');
define('_NEWSLANGADMIN_ALERT_UPLOADLARGE_1_','The selected file must be uploaded separately due to their size!');
define('_NEWSLANGADMIN_ALERT_UPLOADLARGE_2_','The upload large files usually takes a longer period of time.');
define('_NEWSLANGADMIN_ALERT_UPLOADLARGE_3_','The new record must be after the upload also still be saved, the uploaded file is not otherwise available.');
define('_NEWSLANGADMIN_ALERT_UPLOADSTATE_','upload status');

define('_NEWSLANGADMIN_FIELD_LOGINFORSUBS_','login for editors');
define('_NEWSLANGADMIN_FIELD_NOSUBEDITOR_','disable editor login');
define('_NEWSLANGADMIN_FIELD_HIDEFORSUBS_','hide settings for editors');
define('_NEWSLANGADMIN_FIELD_USERSASSUBS_','set user group as editors');
define('_NEWSLANGADMIN_FIELD_LANGUAGE_','language');
define('_NEWSLANGADMIN_FIELD_NOLANGUAGES_','no languages ​​available');
define('_NEWSLANGADMIN_FIELD_NOUSERS_','no groups available');
define('_NEWSLANGADMIN_FIELD_ISSTARTDATA_','start record is ​​available every time');
define('_NEWSLANGADMIN_FIELD_DISPLAY_','display');
define('_NEWSLANGADMIN_FIELD_DISPLAYFROM_','from');
define('_NEWSLANGADMIN_FIELD_DISPLAYTO_','to');
define('_NEWSLANGADMIN_FIELD_CLEARDATE_','empty date');
define('_NEWSLANGADMIN_FIELD_SETPERPAGE_','display per page');
define('_NEWSLANGADMIN_FIELD_DATAPERPAGE_','(blank for unlimited)');
define('_NEWSLANGADMIN_FIELD_SORTING_','sorting');
define('_NEWSLANGADMIN_FIELD_SORTNEWFIRST_','backward');
define('_NEWSLANGADMIN_FIELD_SORTOLDFIRST_','forward');
define('_NEWSLANGADMIN_FIELD_SORTBYFIELD_','by field');
define('_NEWSLANGADMIN_FIELD_NEWCREATED_','New created');
define('_NEWSLANGADMIN_FIELD_CATMENU_','category tree');
define('_NEWSLANGADMIN_FIELD_ONLYMENU_','only as a menu (no main categories page)');
define('_NEWSLANGADMIN_FIELD_PERMISSION_','write permissions');
define('_NEWSLANGADMIN_FIELD_SELUSERGROUP_','user groups');
define('_NEWSLANGADMIN_FIELD_NOUSERGROUP_','none (all visitors have write permission)');
define('_NEWSLANGADMIN_FIELD_MULTISELECT_','hold down CTRL for multiple selection');
define('_NEWSLANGADMIN_FIELD_RELEASING_','releasing');
define('_NEWSLANGADMIN_FIELD_USERINPUTS_','visitors input');
define('_NEWSLANGADMIN_FIELD_RELEASEDIRECT_','immediately turn online');
define('_NEWSLANGADMIN_FIELD_RELEASEBYHAND_','unlock by hand');
define('_NEWSLANGADMIN_FIELD_BOXCOMMON_','lightbox generally');
define('_NEWSLANGADMIN_FIELD_BOXSINGLE_','open images individually');
define('_NEWSLANGADMIN_FIELD_BOXDATASTEP_','continue and back within a dataset');
define('_NEWSLANGADMIN_FIELD_BOXFULLSTEP_','continue and back on all records (list view)');
define('_NEWSLANGADMIN_FIELD_BOXONLYONFULL_','no lightbox on list view');
define('_NEWSLANGADMIN_FIELD_DBWITHOUTFILE_','database entry exists without a file');
define('_NEWSLANGADMIN_FIELD_STARTONTOPIC_','reply start record');
define('_NEWSLANGADMIN_FIELD_NUMBNEWEST_','number of latest records');
define('_NEWSLANGADMIN_FIELD_NEWESTINBLOCK_','display');
define('_NEWSLANGADMIN_FIELD_COPYHANDLING_','copies');
define('_NEWSLANGADMIN_FIELD_TOPICCOPIES_','make topic copy online/offline switchable');
define('_NEWSLANGADMIN_FIELD_DATACOPIES_','make record copy online/offline switchable');
define('_NEWSLANGADMIN_FIELD_SHOWINLIST_','show in topics list');
define('_NEWSLANGADMIN_FIELD_SHOWINTOPIC_','show on top of every pager page');
define('_NEWSLANGADMIN_FIELD_SHOWINTOPICFILTER_','do not filter');
define('_NEWSLANGADMIN_FIELD_DATAPREVNEXTNAVI_','exclude from records navigation');
define('_NEWSLANGADMIN_FIELD_FILTER_','filter');
define('_NEWSLANGADMIN_FIELD_FILTERMAINTAIN_','receive on whole page');
define('_NEWSLANGADMIN_FIELD_USEASTITLE_','use this field as the title');
define('_NEWSLANGADMIN_FIELD_SELECTOPTIONS_','select options');
define('_NEWSLANGADMIN_FIELD_SELECTPROTOTYPE_','one select option per line - pattern: value|text|__select__');
define('_NEWSLANGADMIN_FIELD_SELECTLEEROPT_','If text is empty, value is taken. \'---\' for empty option');
define('_NEWSLANGADMIN_FIELD_OPTWITHDEFINES_','for value and text are language defines possible');
define('_NEWSLANGADMIN_FIELD_OPTASSELECTED_','\'__select__\' is optional for selected onLoad (only once)');
define('_NEWSLANGADMIN_FIELD_SHOWSELECTASRADIO_','show as radio buttons (\'---\' will be ignored)');
define('_NEWSLANGADMIN_FIELD_NOSELECTS_','no fields have been defined');
define('_NEWSLANGADMIN_FIELD_CHECKBOX_','checkboxes');
define('_NEWSLANGADMIN_FIELD_CHECKPROTOTYPE_','one checkbox per line - pattern: name|value|text|__check__');
define('_NEWSLANGADMIN_FIELD_CHECKLEEROPT_','If text is empty, value is taken.');
define('_NEWSLANGADMIN_FIELD_BOXWITHDEFINES_','for value and text are language defines possible');
define('_NEWSLANGADMIN_FIELD_BOXASCHECKED_','\'__check__\' is optional for activ onLoad (only once)');
define('_NEWSLANGADMIN_FIELD_NOCHECKS_','There are no checkboxes were still defined');
define('_NEWSLANGADMIN_FIELD_BOXOPEN_','open image in the lightbox');
define('_NEWSLANGADMIN_FIELD_BOXOPENS_','open images in the lightbox');
define('_NEWSLANGADMIN_FIELD_PLUSIMGUPLOAD_','add image upload field');
define('_NEWSLANGADMIN_FIELD_ONLYMIMEFILE_','upload only for following file types');
define('_NEWSLANGADMIN_FIELD_MIMEEXPLAIN_1_','for multiple file types separate with | sign - empty for all');
define('_NEWSLANGADMIN_FIELD_MIMEEXPLAIN_2_','file header (MIME type) is queried, not the extension');
define('_NEWSLANGADMIN_FIELD_FILETYPE_','allowed file types');
define('_NEWSLANGADMIN_FIELD_PROTECTFILE_','protect the file access');
define('_NEWSLANGADMIN_FIELD_DOWNLOADFORGROUP_','download only for users group');
define('_NEWSLANGADMIN_FIELD_DOWNLOADFORALL_','all logged-in users');

define('_NEWSLANGADMIN_INPUT_TOPIC_','title');
define('_NEWSLANGADMIN_INPUT_FROMTIME_','date');
define('_NEWSLANGADMIN_OUTPUT_DOWNLOADCOUNTER_','times downloaded');
define('_NEWSLANGADMIN_HEADLINETITLE_SEO_','SEO browser title');
define('_NEWSLANGADMIN_HEADLINETITLE_CAT_','category');
define('_NEWSLANGADMIN_HEADLINETITLE_TOPIC_','topic');
define('_NEWSLANGADMIN_HEADLINETITLE_DATA_','record');
define('_NEWSLANGADMIN_HEADLINETITLE_SHOW_','get from data');
define('_NEWSLANGADMIN_SEOINPUT_METAS_','SEO');
define('_NEWSLANGADMIN_SEOINPUT_METATITLE_','browser title');
define('_NEWSLANGADMIN_SEOINPUT_METADESC_','meta description');
define('_NEWSLANGADMIN_SEOINPUT_METAKEYS_','meta keywords');

define('_NEWSLANGADMIN_INPUT_FIELD_HEADLINE_','Headline');
define('_NEWSLANGADMIN_INPUT_FIELD_DATE_','Datum');
define('_NEWSLANGADMIN_INPUT_FIELD_IMAGE_','Bild');
define('_NEWSLANGADMIN_INPUT_FIELD_IMGALT_','Bild Alt-Text');
define('_NEWSLANGADMIN_INPUT_FIELD_TEASER_','Teaser');
define('_NEWSLANGADMIN_INPUT_FIELD_TEXT_','Text');











