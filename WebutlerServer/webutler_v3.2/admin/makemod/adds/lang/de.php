<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

define('_MAKEMODLANG_SQLERROR_','Fehlermeldung');
define('_MAKEMODLANG_ERROR_DBTABLES_','Fehler beim erstellen der Datenbanktabellen.');
define('_MAKEMODLANG_ERROR_DATABASE_','Fehler beim erstellen der ModMaker-Datenbank.');
define('_MAKEMODLANG_ERROR_CHMOD_','Das Verzeichnis für die ModMaker-Datenbank <strong>/content/access</strong><br />ist nicht beschreibbar. Bitte CHMOD laut den Angaben des Providers setzen!');
define('_MAKEMODLANG_ERROR_MODNOTEXISTS_','Das gewählte Modul existiert nicht!');

define('_MAKEMODLANG_FIELDTYPE_DATEFIELD_','Datumsfeld');
define('_MAKEMODLANG_FIELDTYPE_USERNAME_','Benutzername');
define('_MAKEMODLANG_FIELDTYPE_TEXTLINE_','Textfeld einzeilig');
define('_MAKEMODLANG_FIELDTYPE_TEXTAREA_','Textfeld mehrzeilig');
define('_MAKEMODLANG_FIELDTYPE_HTMLEDIT_','HTML Editor');
define('_MAKEMODLANG_FIELDTYPE_CODEEDIT_','BBCode Editor');
define('_MAKEMODLANG_FIELDTYPE_NUMBER_','Zahlenfeld');
define('_MAKEMODLANG_FIELDTYPE_FILEUPLOAD_','Dateiupload');
define('_MAKEMODLANG_FIELDTYPE_IMAGEUPLOAD_','Bildupload');
define('_MAKEMODLANG_FIELDTYPE_MULTIIMAGE_','Mehrfach-Bildupload');
define('_MAKEMODLANG_FIELDTYPE_STATEFIELD_','Statusfeld');
define('_MAKEMODLANG_FIELDTYPE_SELECTBOX_','Auswahlfeld');
define('_MAKEMODLANG_FIELDTYPE_CHECKBOX_','Checkboxen');
define('_MAKEMODLANG_FIELDTYPE_HIDDENFIELD_','Verstecktes Feld');

define('_MAKEMODLANG_FRONTPAGE_TITLE_','Eigenes WEBUTLER-Modul erstellen');
define('_MAKEMODLANG_FRONTPAGE_HEADLINE_','Modul erstellen');
define('_MAKEMODLANG_FRONTPAGE_TEXT_','Mit dem <strong>ModMaker</strong> lassen sich einfache Ein-/Ausgabe Module erstellen. Zuerst werden die Eingabefelder festgelegt und anschließend welche Eingaben in welchem Template ausgegeben werden sollen. Die Ausgabe muß in den Templatedateien *.tpl gestaltet werden. Man sollte gut planen, da sich das erzeugte Modul nach dem Download nicht mehr ändern läst. Weitere Informationen sind in der <a href="###HELP_URL###" target="_blank">Hilfe</a> zu finden.');
define('_MAKEMODLANG_FRONTPAGE_PROJECT_','anderes Projekt wählen');
define('_MAKEMODLANG_FRONTPAGE_MODFOLDER_','Modulname / Verzeichnis');
define('_MAKEMODLANG_FRONTPAGE_SAVED_','Einstellungen wurden gespeichert');

define('_MAKEMODLANG_PAGE_SAVE_SETTINGS_','Einstellungen speichern');
define('_MAKEMODLANG_PAGE_IMAGESCAL_','Bildskalierung');
define('_MAKEMODLANG_PAGE_LIGHTBOX_','Lightbox');
define('_MAKEMODLANG_PAGE_LIST_','Liste');
define('_MAKEMODLANG_PAGE_FULL_','Seite');
define('_MAKEMODLANG_PAGE_IMGWIDTH_','breit');
define('_MAKEMODLANG_PAGE_IMGHEIGHT_','hoch');
define('_MAKEMODLANG_PAGE_EMPTY_','leer');
define('_MAKEMODLANG_PAGE_SAVE_','speichern');

define('_MAKEMODLANG_TABS_DEFINEFIELDS_','Felder definieren');
define('_MAKEMODLANG_TABS_ADMINVIEW_','Administration');
define('_MAKEMODLANG_TABS_USERVIEW_','Besucheransicht');
define('_MAKEMODLANG_TABS_TEMPLATES_','Templates');
define('_MAKEMODLANG_TABS_DOWNLOAD_','Download');
define('_MAKEMODLANG_TABS_TPLLIST_','Listenansicht');
define('_MAKEMODLANG_TABS_TPLFULL_','Seitenansicht');
define('_MAKEMODLANG_TABS_TPLINPUT_','Besuchereingabe');
define('_MAKEMODLANG_TABS_TPLNEWEST_','neuste Einträge');

define('_MAKEMODLANG_PAGE_LOAD_LOADPROJECT_','Projekt laden');
define('_MAKEMODLANG_PAGE_LOAD_NEWPROJECT_','Neues Projekt erstellen');
define('_MAKEMODLANG_PAGE_LOAD_MODNAME_','Modulname');
define('_MAKEMODLANG_PAGE_LOAD_OPENPROJECT_','öffnen');
define('_MAKEMODLANG_PAGE_LOAD_CREATEPROJECT_','anlegen');
define('_MAKEMODLANG_PAGE_LOAD_PROJECTEXISTS_','Ein Projekt mit diesem Namen existiert bereits!');
define('_MAKEMODLANG_PAGE_LOAD_MODNAMEWRONG_','kann nicht als Modulname vergeben werden!');
define('_MAKEMODLANG_PAGE_LOAD_ERRORNEWMOD_','Das Modul konnte nicht angelegt werden!');
define('_MAKEMODLANG_PAGE_LOAD_MODNOSELECT_','Es wurde kein Modul ausgewählt!');
define('_MAKEMODLANG_PAGE_LOAD_MODNOTEXISTS_','Das Modul existiert nicht!');

define('_MAKEMODLANG_PAGE_DEFINES_NEWDATAFIELD_','Neues Datenfeld erstellen');
define('_MAKEMODLANG_PAGE_DEFINES_NEWFIELDTXT_','Die Datenfelder werden später in der Reihenfolge angezeigt, wie sie hier angelegt werden.');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDNAME','Bezeichnung');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDINPUT_','DB Feldname');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDTYPE_','Feldtyp');
define('_MAKEMODLANG_PAGE_DEFINES_CREATEFIELD_','Feld erstellen');
define('_MAKEMODLANG_PAGE_DEFINES_EXISTFIELDS_','Vorhandene Datenfelder');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDSEMPTY_','Es wurden nicht alle Felder ausgefüllt!');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDWRONG_','kann nicht als Feldname vergeben werden!');
define('_MAKEMODLANG_PAGE_DEFINES_FIELDEXISTS_','Der Feldname %s existiert bereits!');
define('_MAKEMODLANG_PAGE_DEFINES_ERRORNEWFIELD_','Das Feld konnte nicht angelegt werden!');
define('_MAKEMODLANG_PAGE_DEFINES_ERRORDELFIELD_','Das Feld konnte nicht gelöscht werden!');
define('_MAKEMODLANG_PAGE_DEFINES_SCALNOTSAVED_','Die Skalierungseigenschaften konnten nicht gespeichert werden!');

define('_MAKEMODLANG_PAGE_ADMIN_SETTINGS_','Folgende Einstellmöglichkeiten zur Verfügung stellen');
define('_MAKEMODLANG_PAGE_ADMIN_SHOWCATS_','Kategorien anbieten');
define('_MAKEMODLANG_PAGE_ADMIN_BASECATS_','Kategorie-IDs als Einstiegspunkt für Multipage-Support nutzen');
define('_MAKEMODLANG_PAGE_ADMIN_CATFIELDS_','Kategorie Eingabefelder');
define('_MAKEMODLANG_PAGE_ADMIN_CATNAME_','Nur Name');
define('_MAKEMODLANG_PAGE_ADMIN_CATNAMEIMG_','Name mit Bild');
define('_MAKEMODLANG_PAGE_ADMIN_CATNAMETXT_','Name mit Text');
define('_MAKEMODLANG_PAGE_ADMIN_CATNAMEIMGTXT_','Name mit Bild und Text');
define('_MAKEMODLANG_PAGE_ADMIN_CATSORTHAND_','Sortierung der Kategorien von Hand');
define('_MAKEMODLANG_PAGE_ADMIN_CATSORTSUBS_','Kategorien in Kategoriebaum einsortieren');
define('_MAKEMODLANG_PAGE_ADMIN_SHOWCATSMENU_','Kategoriebaum als Menü anbieten (zusätzliches Feld Linkname)');
define('_MAKEMODLANG_PAGE_ADMIN_CATLISTIMG_','Übersicht');
define('_MAKEMODLANG_PAGE_ADMIN_SHOWTOPICS_','Themen anbieten');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICINPUT_','Eingabefeld');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICHEADLINE_','Überschrift');
define('_MAKEMODLANG_PAGE_ADMIN_SHOWTOPICHEAD_','anzeigen');
define('_MAKEMODLANG_PAGE_ADMIN_DATASTOTOPIC_','Datensätze werden der Themen-Überschrift zugeordnet');
define('_MAKEMODLANG_PAGE_ADMIN_DATACREATE_','Datensätze erstellen');
define('_MAKEMODLANG_PAGE_ADMIN_COPYDATATOCAT_','Kopien von Datensätzen in verschiedenen Kategorien');
define('_MAKEMODLANG_PAGE_ADMIN_COPYDATATOTOPIC_','Kopien von Datensätzen in verschiedenen Themen');
define('_MAKEMODLANG_PAGE_ADMIN_COPYTOPICTOCAT_','Kopien von Themen in verschiedenen Kategorien');
define('_MAKEMODLANG_PAGE_ADMIN_DISTTOPICSTART_','Startbeitrag des Themas individualisieren');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICSORTHAND_','Sortierung der Themen von Hand');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICSORTFIELD_','Sortierung der Themen nach');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICSORTFIELDTITLE_','Titelfeld');
define('_MAKEMODLANG_PAGE_ADMIN_TOPICSORTFIELDOFSTART_','Datenfeld des Startbeitrages');
define('_MAKEMODLANG_PAGE_ADMIN_FROMTOTOPIC_','Anzeigedatum für Thema setzen (von-bis Zeitraum)');
define('_MAKEMODLANG_PAGE_ADMIN_DATABYLANG_','Mehrsprachige Eingabe');
define('_MAKEMODLANG_PAGE_ADMIN_REQUIREMULTILANG_','Sprachen müssen aktiviert sein');
define('_MAKEMODLANG_PAGE_ADMIN_DATAMULTILANG_','Identische Seiten für alle Sprachen (alle Sprachen in einem Datensatz)');
define('_MAKEMODLANG_PAGE_ADMIN_SETSUBEDITORS_','Admin-Login für Redakteure');
define('_MAKEMODLANG_PAGE_ADMIN_SETPERMISSION_','User-Schreibrechte setzen');
define('_MAKEMODLANG_PAGE_ADMIN_REQUIREUSERS_','Benutzerverwaltung muß aktiviert sein');
define('_MAKEMODLANG_PAGE_ADMIN_DATASORTHAND_','Sortierung der Datensätze von Hand');
define('_MAKEMODLANG_PAGE_ADMIN_DATASORTFIELD_','Sortierung der Datensätze nach Datenfeld');
define('_MAKEMODLANG_PAGE_ADMIN_FROMTODATA_','Anzeigedatum für Datensatz setzen (von-bis Zeitraum)');
define('_MAKEMODLANG_PAGE_ADMIN_OPTIONS_','Optionengruppen zur Anzeige im Datensatz erstellen');
define('_MAKEMODLANG_PAGE_ADMIN_AUTOLIGHTBOX_','automatische Lightbox aktivieren');
define('_MAKEMODLANG_PAGE_ADMIN_SEOFIELDS_','Felder zur Suchmaschinenoptimierung anlegen');
define('_MAKEMODLANG_PAGE_ADMIN_SEOINPUTS_','Als SEO Felder werden jeweils 3 Eingabefelder zur Verfügung gestellt:');
define('_MAKEMODLANG_PAGE_ADMIN_SEOTEXT_','Seitentitel, Meta-Description und Meta-Keywords.');
define('_MAKEMODLANG_PAGE_ADMIN_SEOCATS_','Felder in der Kategorie-Bearbeitung anzeigen');
define('_MAKEMODLANG_PAGE_ADMIN_SEOTOPICS_','Felder in der Themen-Bearbeitung anzeigen');
define('_MAKEMODLANG_PAGE_ADMIN_SEODATAS_','Felder in der Beitrags-Bearbeitung anzeigen');
define('_MAKEMODLANG_PAGE_ADMIN_SAVEERROR_','Die Einstellungen für die Administration konnten nicht gespeichert werden!');

define('_MAKEMODLANG_PAGE_VIEW_HEADLINE_','Legen Sie fest was in der Besucheransicht angezeigt werden soll');
define('_MAKEMODLANG_PAGE_VIEW_SHOWCATS_','Kategorien anzeigen');
define('_MAKEMODLANG_PAGE_VIEW_SHOWTOPICS_','Themen anzeigen');
define('_MAKEMODLANG_PAGE_VIEW_SHOWDATALIST_','Datensatzliste anzeigen');
define('_MAKEMODLANG_PAGE_VIEW_SHOWDATA_','Datensatz anzeigen');
define('_MAKEMODLANG_PAGE_VIEW_SHOWNEWEST_','neuste Einträge anzeigen');
define('_MAKEMODLANG_PAGE_VIEW_SETFILTER_','Datensätze filtern');
define('_MAKEMODLANG_PAGE_VIEW_SETOWNSQL_','eigene SQL Statements setzen');
define('_MAKEMODLANG_PAGE_VIEW_USERINPUT_','Besuchereingabe');
define('_MAKEMODLANG_PAGE_VIEW_CREATENEWTOPIC_','Neues Thema anlegen');
define('_MAKEMODLANG_PAGE_VIEW_INPUTUNDERLIST_','Eingabemaske unter Liste');
define('_MAKEMODLANG_PAGE_VIEW_LINKTOINPUT_','Link zur Eingabemaske');
define('_MAKEMODLANG_PAGE_VIEW_SAVEERROR_','Die Einstellungen für die Besucheransicht konnten nicht gespeichert werden!');

define('_MAKEMODLANG_PAGE_TPLLIST_HEADLINE_','Die Listenansicht zeigt eine Vorschauliste von Datensätzen');
define('_MAKEMODLANG_PAGE_TPLLIST_SHOWFIELDS_','Folgende Felder als Listeneintrag anzeigen');

define('_MAKEMODLANG_PAGE_TPLFULL_HEADLINE_','Die Seitenansicht zeigt einen vollen Datensatz');
define('_MAKEMODLANG_PAGE_TPLFULL_SHOWFIELDS_','Folgende Felder als Datensatz anzeigen');

define('_MAKEMODLANG_PAGE_TPLINPUT_HEADLINE_','Die Besuchereingabe erstellt einen neuen Datensatz');
define('_MAKEMODLANG_PAGE_TPLINPUT_SHOWFIELDS_','Folgende Felder als Eingabemaske anzeigen');

define('_MAKEMODLANG_PAGE_TPLNEWEST_HEADLINE_','Die neusten Datensätze können auf einer beliebigen Seite angezeigt werden');
define('_MAKEMODLANG_PAGE_TPLNEWEST_SHOWFIELDS_','Folgende Felder in neuste Einträge anzeigen');

define('_MAKEMODLANG_PAGE_TPLS_NOFIELDS_','Es wurden noch keine Felder angelegt');
define('_MAKEMODLANG_PAGE_TPLSROW_IDENT_','Bezeichnung');
define('_MAKEMODLANG_PAGE_TPLSROW_DBNAME_','Feldname');
define('_MAKEMODLANG_PAGE_TPLSROW_DBTYPE_','Feldtyp');
define('_MAKEMODLANG_PAGE_TPLSROW_OUTPUT_','Ausgabe des Feldes');
define('_MAKEMODLANG_PAGE_TPLSROW_INPUT_','Eingabefeld');
define('_MAKEMODLANG_PAGE_TPLS_CHECKSHOW_','anzeigen');
define('_MAKEMODLANG_PAGE_TPLS_DELETE_','löschen');
define('_MAKEMODLANG_PAGE_TPLS_MOVEUP_','nach oben');
define('_MAKEMODLANG_PAGE_TPLS_MOVEDOWN_','nach unten');
define('_MAKEMODLANG_PAGE_TPLS_NODEL_','Das Feld konnte nicht aus dem Datenbankfeld %s entfernt werden!');
define('_MAKEMODLANG_PAGE_TPLS_ASKDEL_','Soll das Feld %s wirklich gelöscht werden?');
define('_MAKEMODLANG_PAGE_TPLS_OPTIONS_','Optionen');
define('_MAKEMODLANG_PAGE_TPLS_SAVEERROR_','Die Templateeinstellungen konnten nicht gespeichert werden!');

define('_MAKEMODLANG_PAGE_DOWNLOAD_HEADLINE_','Download des Moduls');
define('_MAKEMODLANG_PAGE_DOWNLOAD_TEXT_','Die Einstellungen werden in die Vorlagedateien übertragen, als Ziparchiv gepackt und anschließend zum Download angeboten. Das Modul muß zuerst lokal entpackt werden. Danach müssen die Templates im Verzeichnis /view/tpls bearbeitet werden. Wenn alles fertig ist wird das Modul mit FTP ins Verzeichnis /modules kopiert. Die Verzeichnisse /data und /media müssen für den Server beschreibbar gesetzt werden.');
define('_MAKEMODLANG_PAGE_DOWNLOAD_MAKEMOD_','Modul %s erstellen');




