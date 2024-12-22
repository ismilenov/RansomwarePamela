<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

define('_MAKEMODLANG_SQLERROR_','error message');
define('_MAKEMODLANG_ERROR_DBTABLES_','Failed to create the database tables.');
define('_MAKEMODLANG_ERROR_DATABASE_','Failed to create the database for ModMaker.');
define('_MAKEMODLANG_ERROR_CHMOD_','The directory for the database of ModMaker <strong>/content/access</strong><br />is not writable. Please set CHMOD according to the data of your provider!');
define('_MAKEMODLANG_ERROR_MODNOTEXISTS_','The selected module does not exist!');

define('_MAKEMODLANG_FIELDTYPE_DATEFIELD_','date field');
define('_MAKEMODLANG_FIELDTYPE_USERNAME_','user name');
define('_MAKEMODLANG_FIELDTYPE_TEXTLINE_','single-line text box');
define('_MAKEMODLANG_FIELDTYPE_TEXTAREA_','multiline text box');
define('_MAKEMODLANG_FIELDTYPE_HTMLEDIT_','HTML Editor');
define('_MAKEMODLANG_FIELDTYPE_CODEEDIT_','BBcode Editor');
define('_MAKEMODLANG_FIELDTYPE_NUMBER_','number field');
define('_MAKEMODLANG_FIELDTYPE_FILEUPLOAD_','file upload');
define('_MAKEMODLANG_FIELDTYPE_IMAGEUPLOAD_','image upload');
define('_MAKEMODLANG_FIELDTYPE_MULTIIMAGE_','multiple image upload');
define('_MAKEMODLANG_FIELDTYPE_STATEFIELD_','status field');
define('_MAKEMODLANG_FIELDTYPE_SELECTBOX_','selection field');
define('_MAKEMODLANG_FIELDTYPE_CHECKBOX_','checkboxes');
define('_MAKEMODLANG_FIELDTYPE_HIDDENFIELD_','hidden field');

define('_MAKEMODLANG_FRONTPAGE_TITLE_','Create your own WEBUTLER module');
define('_MAKEMODLANG_FRONTPAGE_HEADLINE_','create module');
define('_MAKEMODLANG_FRONTPAGE_TEXT_','The <strong>ModMaker</strong> allows you to create simple in-/output modules. First, the input fields are defined and then which inputs are to be in which template output. The output must be designed in *.tpl template files. One should plan well, since the generated module after downloading no longer changable. More information can be found in the <a href="###HELP_URL###" target="_blank">help</a>.');
define('_MAKEMODLANG_FRONTPAGE_PROJECT_','Select another project');
define('_MAKEMODLANG_FRONTPAGE_MODFOLDER_','Module name / directory');
define('_MAKEMODLANG_FRONTPAGE_SAVED_','Settings have been saved');

define('_MAKEMODLANG_PAGE_SAVE_SETTINGS_','save settings');
define('_MAKEMODLANG_PAGE_IMAGESCAL_','image scale');
define('_MAKEMODLANG_PAGE_LIGHTBOX_','lightbox');
define('_MAKEMODLANG_PAGE_LIST_','list');
define('_MAKEMODLANG_PAGE_FULL_','page');
define('_MAKEMODLANG_PAGE_IMGWIDTH_','width');
define('_MAKEMODLANG_PAGE_IMGHEIGHT_','height');
define('_MAKEMODLANG_PAGE_EMPTY_','empty');
define('_MAKEMODLANG_PAGE_SAVE_','save');

define('_MAKEMODLANG_TABS_DEFINEFIELDS_','define fields');
define('_MAKEMODLANG_TABS_ADMINVIEW_','Administration');
define('_MAKEMODLANG_TABS_USERVIEW_','Visitors view');
define('_MAKEMODLANG_TABS_TEMPLATES_','Templates');
define('_MAKEMODLANG_TABS_DOWNLOAD_','Download');
define('_MAKEMODLANG_TABS_TPLLIST_','list view');
define('_MAKEMODLANG_TABS_TPLFULL_','page view');
define('_MAKEMODLANG_TABS_TPLINPUT_','visitors input');
define('_MAKEMODLANG_TABS_TPLNEWEST_','latest records');

define('_MAKEMODLANG_PAGE_LOAD_LOADPROJECT_','Load project');
define('_MAKEMODLANG_PAGE_LOAD_NEWPROJECT_','Create new project');
define('_MAKEMODLANG_PAGE_LOAD_MODNAME_','Module name');
define('_MAKEMODLANG_PAGE_LOAD_OPENPROJECT_','open');
define('_MAKEMODLANG_PAGE_LOAD_CREATEPROJECT_','create');
define('_MAKEMODLANG_PAGE_LOAD_PROJECTEXISTS_','A project with that name already exists!');
define('_MAKEMODLANG_PAGE_LOAD_MODNAMEWRONG_','can not be awarded as a module name!');
define('_MAKEMODLANG_PAGE_LOAD_ERRORNEWMOD_','The module could not be created!');
define('_MAKEMODLANG_PAGE_LOAD_MODNOSELECT_','There was no module selected!');
define('_MAKEMODLANG_PAGE_LOAD_MODNOTEXISTS_','The module does not exist!');

define('_MAKEMODLANG_PAGE_DEFINES_NEWDATAFIELD_','Create a new data field');
define('_MAKEMODLANG_PAGE_DEFINES_NEWFIELDTXT_','The data fields are shown later in the order they are created here.');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDNAME','designation');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDINPUT_','database field name');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDTYPE_','field type');
define('_MAKEMODLANG_PAGE_DEFINES_CREATEFIELD_','create field');
define('_MAKEMODLANG_PAGE_DEFINES_EXISTFIELDS_','existing data fields');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDSEMPTY_','You did not fill all the fields!');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDWRONG_','can not be awarded as a field name!');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDEXISTS_','The field name %s already exists!');
define('_MAKEMODLANG_PAGE_DEFINES_ERRORNEWFIELD_','The field could not be created!');
define('_MAKEMODLANG_PAGE_DEFINES_ERRORDELFIELD_','The field could not be deleted!');
define('_MAKEMODLANG_PAGE_DEFINES_SCALNOTSAVED_','The scaling properties could not be saved!');

define('_MAKEMODLANG_PAGE_ADMIN_SETTINGS_','Make the following settings are available');
define('_MAKEMODLANG_PAGE_ADMIN_SHOWCATS_','offer categories');
define('_MAKEMODLANG_PAGE_ADMIN_BASECATS_','use category IDs as entry point for multi-page support');
define('_MAKEMODLANG_PAGE_ADMIN_CATFIELDS_','category fields');
define('_MAKEMODLANG_PAGE_ADMIN_CATNAME_','only name');
define('_MAKEMODLANG_PAGE_ADMIN_CATNAMEIMG_','name with image');
define('_MAKEMODLANG_PAGE_ADMIN_CATNAMETXT_','name with text');
define('_MAKEMODLANG_PAGE_ADMIN_CATNAMEIMGTXT_','name with image and text');
define('_MAKEMODLANG_PAGE_ADMIN_CATSORTHAND_','sort categories by hand');
define('_MAKEMODLANG_PAGE_ADMIN_CATSORTSUBS_','sort categories in category tree');
define('_MAKEMODLANG_PAGE_ADMIN_SHOWCATSMENU_','category tree from a set menu offer (additional field link name)');
define('_MAKEMODLANG_PAGE_ADMIN_CATLISTIMG_','overview');
define('_MAKEMODLANG_PAGE_ADMIN_SHOWTOPICS_','offer topics');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICINPUT_','input field');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICHEADLINE_','headline');
define('_MAKEMODLANG_PAGE_ADMIN_SHOWTOPICHEAD_','show');
define('_MAKEMODLANG_PAGE_ADMIN_DATASTOTOPIC_','records are the topics heading assigned');
define('_MAKEMODLANG_PAGE_ADMIN_DATACREATE_','create records');
define('_MAKEMODLANG_PAGE_ADMIN_COPYDATATOCAT_','copies of records in different categories');
define('_MAKEMODLANG_PAGE_ADMIN_COPYDATATOTOPIC_','copies of records in different topics');
define('_MAKEMODLANG_PAGE_ADMIN_COPYTOPICTOCAT_','copies of topics in different categories');
define('_MAKEMODLANG_PAGE_ADMIN_DISTTOPICSTART_','individualize starting post in the topic');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICSORTHAND_','sorting of topics by hand');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICSORTFIELD_','sorting of topics according to');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICSORTFIELDTITLE_','title field');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICSORTFIELDOFSTART_','a data field of the starting post');
define('_MAKEMODLANG_PAGE_ADMIN_FROMTOTOPIC_','date of issue set (from-to period)');
define('_MAKEMODLANG_PAGE_ADMIN_DATABYLANG_','multilingual input');
define('_MAKEMODLANG_PAGE_ADMIN_REQUIREMULTILANG_','languages ​​must be enabled');
define('_MAKEMODLANG_PAGE_ADMIN_DATAMULTILANG_','identical pages for all languages ​​(all languages ​​in one record)');
define('_MAKEMODLANG_PAGE_ADMIN_SETSUBEDITORS_','admin login for editors');
define('_MAKEMODLANG_PAGE_ADMIN_SETPERMISSION_','set user write permissions');
define('_MAKEMODLANG_PAGE_ADMIN_REQUIREUSERS_','user management must be enabled');
define('_MAKEMODLANG_PAGE_ADMIN_DATASORTHAND_','sorting of the records by hand');
define('_MAKEMODLANG_PAGE_ADMIN_DATASORTFIELD_','sorting of the records by the data field');
define('_MAKEMODLANG_PAGE_ADMIN_FROMTODATA_','date set for record (from-to period)');
define('_MAKEMODLANG_PAGE_ADMIN_OPTIONS_','create option groups to display in the data set');
define('_MAKEMODLANG_PAGE_ADMIN_AUTOLIGHTBOX_','enable automatic Lightbox');
define('_MAKEMODLANG_PAGE_ADMIN_SEOFIELDS_','create fields for search engine optimization');
define('_MAKEMODLANG_PAGE_ADMIN_SEOINPUTS_','As SEO fields each with 3 input fields are provided:');
define('_MAKEMODLANG_PAGE_ADMIN_SEOTEXT_','page title, meta description and meta keywords.');
define('_MAKEMODLANG_PAGE_ADMIN_SEOCATS_','show fields in the category editing view');
define('_MAKEMODLANG_PAGE_ADMIN_SEOTOPICS_','show fields in the topic editing view');
define('_MAKEMODLANG_PAGE_ADMIN_SEODATAS_','show fields in the record editing view');
define('_MAKEMODLANG_PAGE_ADMIN_SAVEERROR_','The settings for the Administration could not be saved!');

define('_MAKEMODLANG_PAGE_VIEW_HEADLINE_','Decide what you want to display in the visitor view');
define('_MAKEMODLANG_PAGE_VIEW_SHOWCATS_','view categories');
define('_MAKEMODLANG_PAGE_VIEW_SHOWTOPICS_','view topics');
define('_MAKEMODLANG_PAGE_VIEW_SHOWDATALIST_','view records list');
define('_MAKEMODLANG_PAGE_VIEW_SHOWDATA_','view records');
define('_MAKEMODLANG_PAGE_VIEW_SHOWNEWEST_','show latest records');
define('_MAKEMODLANG_PAGE_VIEW_SETFILTER_','filter records');
define('_MAKEMODLANG_PAGE_VIEW_SETOWNSQL_','do own SQL statements');
define('_MAKEMODLANG_PAGE_VIEW_USERINPUT_','visitors input mask');
define('_MAKEMODLANG_PAGE_VIEW_CREATENEWTOPIC_','create new topic');
define('_MAKEMODLANG_PAGE_VIEW_INPUTUNDERLIST_','input screen under list');
define('_MAKEMODLANG_PAGE_VIEW_LINKTOINPUT_','link to the input screen');
define('_MAKEMODLANG_PAGE_VIEW_SAVEERROR_','The settings for visitors to view could not be saved!');

define('_MAKEMODLANG_PAGE_TPLLIST_HEADLINE_','The list view displays a preview list of records');
define('_MAKEMODLANG_PAGE_TPLLIST_SHOWFIELDS_','Show the following fields as a list item');

define('_MAKEMODLANG_PAGE_TPLFULL_HEADLINE_','The side view shows a full record');
define('_MAKEMODLANG_PAGE_TPLFULL_SHOWFIELDS_','Show the following fields as a record');

define('_MAKEMODLANG_PAGE_TPLINPUT_HEADLINE_','Visitors enter creates a new record');
define('_MAKEMODLANG_PAGE_TPLINPUT_SHOWFIELDS_','Show the following fields as input mask');

define('_MAKEMODLANG_PAGE_TPLNEWEST_HEADLINE_','The latest records can be displayed on any page');
define('_MAKEMODLANG_PAGE_TPLNEWEST_SHOWFIELDS_','Show the following fields in latest records');

define('_MAKEMODLANG_PAGE_TPLS_NOFIELDS_','No fields have been created yet');
define('_MAKEMODLANG_PAGE_TPLSROW_IDENT_','designation');
define('_MAKEMODLANG_PAGE_TPLSROW_DBNAME_','field name');
define('_MAKEMODLANG_PAGE_TPLSROW_DBTYPE_','field type');
define('_MAKEMODLANG_PAGE_TPLSROW_OUTPUT_','output of the field');
define('_MAKEMODLANG_PAGE_TPLSROW_INPUT_','input field');
define('_MAKEMODLANG_PAGE_TPLS_CHECKSHOW_','show');
define('_MAKEMODLANG_PAGE_TPLS_DELETE_','delete');
define('_MAKEMODLANG_PAGE_TPLS_MOVEUP_','up');
define('_MAKEMODLANG_PAGE_TPLS_MOVEDOWN_','down');
define('_MAKEMODLANG_PAGE_TPLS_NODEL_','The field could not be removed from the database field %s!');
define('_MAKEMODLANG_PAGE_TPLS_ASKDEL_','Should the field %s really deleted?');
define('_MAKEMODLANG_PAGE_TPLS_OPTIONS_','options');
define('_MAKEMODLANG_PAGE_TPLS_SAVEERROR_','The template settings could not be saved!');

define('_MAKEMODLANG_PAGE_DOWNLOAD_HEADLINE_','Download the module');
define('_MAKEMODLANG_PAGE_DOWNLOAD_TEXT_','The settings will be transferred to the template files packaged as a zip file and then offered for download. The module must be extracted locally first. After the templates in the directory /view/tpls must be edited. When everything is ready, the module with FTP is copied to the /modules directory. The directories /data and /media must be set writable by the server.');
define('_MAKEMODLANG_PAGE_DOWNLOAD_MAKEMOD_','create module %s');




