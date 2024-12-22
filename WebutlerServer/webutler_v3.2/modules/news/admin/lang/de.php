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

define('_NEWSLANGADMIN_LOGIN_HEADER_','Login für Redakteure');
define('_NEWSLANGADMIN_LOGIN_USERNAME_','Benutzername');
define('_NEWSLANGADMIN_LOGIN_PASSWORD_','Passwort');
define('_NEWSLANGADMIN_LOGIN_SUBMIT_','einloggen');
define('_NEWSLANGADMIN_LOGIN_ATTEMPTS_','Login ist für __TIME__ Minuten gesperrt!');
define('_NEWSLANGADMIN_LOGIN_NOPERMISSION_','Sie haben keine Zugangsberechtigung');
define('_NEWSLANGADMIN_NOTAVAILFORSUBS_','Die Einstellungen können von Redakteuren nicht bearbeitet werden!');
define('_NEWSLANGADMIN_LOGOUT_','Logout');

define('_NEWSLANGADMIN_SETCHMODFOR_','Die Datei /data/###CHMODMODNAME###.db und das Verzeichnis /media müssen beschreibbar sein!');
define('_NEWSLANGADMIN_WRONGCHMOD_','Folgende Dateien/Verzeichnisse sind nicht beschreibbar:');
define('_NEWSLANGADMIN_SETRIGHTCHMODS_','Bitte Chmod ###CHMODFOLDER### für Dateien und Chmod ###CHMODFILES### für Verzeichnisse mit einem FTP-Programm setzen.');
define('_NEWSLANGADMIN_INSTALL_','Installation');
define('_NEWSLANGADMIN_SETTINGS_','Einstellungen');

define('_NEWSLANGADMIN_OPTION_','Option');
define('_NEWSLANGADMIN_OPTIONS_','Optionen');
define('_NEWSLANGADMIN_NOOPTIONGROUPS_','Es wurden noch keine Optionengruppen angelegt');
define('_NEWSLANGADMIN_NEWOPTION_','Neue Optionengruppe');
define('_NEWSLANGADMIN_EDITOPTION_','Optionengruppe bearbeiten');
define('_NEWSLANGADMIN_OPTIONIMGSUPLOAD_','Optionenbilder hochladen');
define('_NEWSLANGADMIN_OPTIMGSUBFOLDER_','Neues Unterverzeichnis anlegen');
define('_NEWSLANGADMIN_CHOOSE_UPLOAD_','wählen');
define('_NEWSLANGADMIN_NOAVAILOPTION_','Keine Optionen vorhanden!');
define('_NEWSLANGADMIN_NOEXISTOPTION_','Diese Optionengruppe existiert nicht!');
define('_NEWSLANGADMIN_OPTIONGROUP_','Gruppe');
define('_NEWSLANGADMIN_OPTIONVALUES_','Optionen');
define('_NEWSLANGADMIN_OPTIONIMGS_','Bilder');
define('_NEWSLANGADMIN_OPTIONIMGSOPEN_','auflisten');
define('_NEWSLANGADMIN_OPTIONIMGSCLOSE_','schließen');
define('_NEWSLANGADMIN_OPTIONHEADLINE_','Eine Option pro Zeile, Werte mit | trennen');
define('_NEWSLANGADMIN_OPTIONWITHDEFINES_','Es können auch Sprachdefines eingegeben werden');
define('_NEWSLANGADMIN_OPTIONEXPLAIN_','Optionen werden im Template options.tpl ausgegeben<br />individuelle Templates mit options_(ID).tpl erstellen<br />Ausgabe der Werte als Array $db_data[\'option\'][0], $db_data[\'option\'][1]...');

define('_NEWSLANGADMIN_CATEGORY_','Kategorie');
define('_NEWSLANGADMIN_CATEGORIES_','Kategorien');
define('_NEWSLANGADMIN_NEWCATEGORY_','Neue Kategorie');
define('_NEWSLANGADMIN_SUBCATEGORIES_','Kategoriebaum');
define('_NEWSLANGADMIN_EDITCATEGORY_','Kategorie bearbeiten');
define('_NEWSLANGADMIN_NOSELECTCATEGORY_','Es wurde keine Kategorie ausgewählt!');
define('_NEWSLANGADMIN_NOAVAILCATEGORY_','Keine Kategorie vorhanden!');
define('_NEWSLANGADMIN_NOEXISTCATEGORY_','Diese Kategorie existiert nicht!');
define('_NEWSLANGADMIN_THEREISNOCAT_','Der Kategoriebaum wurde noch nicht angelegt');
define('_NEWSLANGADMIN_CATDESCRIPT_','Beschreibung');
define('_NEWSLANGADMIN_CATIMAGE_','Bild');
define('_NEWSLANGADMIN_CATLINK_','Menülink');
define('_NEWSLANGADMIN_BASECATENTRY_','Einstiegspunkt');

define('_NEWSLANGADMIN_TOPIC_','Thema');
define('_NEWSLANGADMIN_TOPICS_','Themen');
define('_NEWSLANGADMIN_NEWTOPIC_','Neues Thema');
define('_NEWSLANGADMIN_COPYTOPIC_','Thema kopieren');
define('_NEWSLANGADMIN_EDITTOPIC_','Thema bearbeiten');
define('_NEWSLANGADMIN_NOSELECTTOPIC_','Es wurde kein Thema ausgewählt!');
define('_NEWSLANGADMIN_NOAVAILTOPIC_','Keine Themen vorhanden!');
define('_NEWSLANGADMIN_NOEXISTTOPIC_','Dieses Thema existiert nicht!');
define('_NEWSLANGADMIN_TOPICCOPYID_','ID des zu kopierenden Themas eingeben');
define('_NEWSLANGADMIN_TOPICCOPYOF_','Kopie von Thema mit ID');

define('_NEWSLANGADMIN_DATA_','Beitrag');
define('_NEWSLANGADMIN_DATAS_','Beiträge');
define('_NEWSLANGADMIN_NEWDATA_','Neuer Beitrag');
define('_NEWSLANGADMIN_COPYDATA_','Beitrag kopieren');
define('_NEWSLANGADMIN_EDITDATA_','Beitrag bearbeiten');
define('_NEWSLANGADMIN_NOSELECTDATA_','Es wurde kein Beitrag ausgewählt!');
define('_NEWSLANGADMIN_NOAVAILDATA_','Keine Beiträge vorhanden!');
define('_NEWSLANGADMIN_NOEXISTDATA_','Dieser Beitrag existiert nicht!');
define('_NEWSLANGADMIN_DATADELLAST_','Der Startbeitrag kann erst als letztes verschoben werden.');
define('_NEWSLANGADMIN_DATACOPYID_','ID des zu kopierenden Beitrags eingeben');
define('_NEWSLANGADMIN_DATACOPYOF_','Kopie von Beitrag mit ID');

define('_NEWSLANGADMIN_WRONGLANG_','Die Zieldaten müssen in der selben Sprache sein');
define('_NEWSLANGADMIN_COPYDIST_','Der Startbeitrag kann nicht kopiert werden');
define('_NEWSLANGADMIN_EDITCOPY_','Kopien können nicht bearbeitet werden');
define('_NEWSLANGADMIN_CHANGETOCOPY_','In eine Kopie verschieben ist nicht möglich');

define('_NEWSLANGADMIN_BUTTON_EDIT_','bearbeiten');
define('_NEWSLANGADMIN_BUTTON_GETORG_','Orginal aufrufen');
define('_NEWSLANGADMIN_BUTTON_DELETE_','löschen');
define('_NEWSLANGADMIN_PROMPT_SHOULD_','Soll');
define('_NEWSLANGADMIN_PROMPT_COPY_','die Kopie von');
define('_NEWSLANGADMIN_PROMPT_REALDEL_','wirklich gelöscht werden?');
define('_NEWSLANGADMIN_BUTTON_CHANGETO_','verschieben');
define('_NEWSLANGADMIN_PROMPT_NEWID_','Bitte die ID');
define('_NEWSLANGADMIN_PROMPT_OFCAT_','der neuen Kategorie');
define('_NEWSLANGADMIN_PROMPT_OFTOPIC_','des neuen Themas');
define('_NEWSLANGADMIN_PROMPT_INSERT_','eingeben!');
define('_NEWSLANGADMIN_PROMPT_ERROR_','Fehlerhafte Eingabe!');
define('_NEWSLANGADMIN_BUTTON_ONLINE_','online');
define('_NEWSLANGADMIN_BUTTON_OFFLINE_','offline');
define('_NEWSLANGADMIN_BUTTON_SETON_','freischalten');
define('_NEWSLANGADMIN_BUTTON_SETOFF_','sperren');
define('_NEWSLANGADMIN_BUTTON_INSERT_','einfügen');
define('_NEWSLANGADMIN_BUTTON_SHOW_','anzeigen');
define('_NEWSLANGADMIN_BUTTON_HOCH_','nach oben');
define('_NEWSLANGADMIN_BUTTON_RUNTER_','nach unten');
define('_NEWSLANGADMIN_BUTTON_LINKS_','raus');
define('_NEWSLANGADMIN_BUTTON_RECHTS_','rein');
define('_NEWSLANGADMIN_BUTTON_MAKEFOLDER_','Ordner erstellen');
define('_NEWSLANGADMIN_BUTTON_UPLOAD_','hochladen');
define('_NEWSLANGADMIN_BUTTON_SAVE_','speichern');
define('_NEWSLANGADMIN_BUTTON_CANCEL_','abbrechen');
define('_NEWSLANGADMIN_BUTTON_CHOOSE_','wählen');
define('_NEWSLANGADMIN_NAVI_PAGE_','Seite');
define('_NEWSLANGADMIN_ALERT_UPLOADWRONGMIME_','Der gewählte Dateityp ist nicht erlaubt!');
define('_NEWSLANGADMIN_ALERT_UPLOADCOMPLETE_','Übertragung abgeschlossen! Bitte speichern Sie den Datensatz, damit die Datei verfügbar ist.');
define('_NEWSLANGADMIN_ALERT_UPLOADLARGE_1_','Die gewählte Datei muß aufgrund ihrer Größe separat hochgeladen werden!');
define('_NEWSLANGADMIN_ALERT_UPLOADLARGE_2_','Der Upload großer Dateien nimmt in der Regel einen längeren Zeitraum in Anspruch.');
define('_NEWSLANGADMIN_ALERT_UPLOADLARGE_3_','Der neue Datensatz muß nach dem Upload ebenfalls noch gespeichert werden, die hochgeladene Datei ist sonst nicht verfügbar.');
define('_NEWSLANGADMIN_ALERT_UPLOADSTATE_','Uploadstatus');

define('_NEWSLANGADMIN_FIELD_LOGINFORSUBS_','Login für Redakteure');
define('_NEWSLANGADMIN_FIELD_NOSUBEDITOR_','Redakteurslogin deaktivieren');
define('_NEWSLANGADMIN_FIELD_HIDEFORSUBS_','Einstellungen vor Redakteuren verbergen');
define('_NEWSLANGADMIN_FIELD_USERSASSUBS_','Benutzergruppe als Redakteure festlegen');
define('_NEWSLANGADMIN_FIELD_LANGUAGE_','Sprache');
define('_NEWSLANGADMIN_FIELD_NOLANGUAGES_','keine Sprachen vorhanden');
define('_NEWSLANGADMIN_FIELD_NOUSERS_','keine Benutzergruppe vorhanden');
define('_NEWSLANGADMIN_FIELD_ISSTARTDATA_','Startbeitrag wird immer angezeigt');
define('_NEWSLANGADMIN_FIELD_DISPLAY_','Anzeige');
define('_NEWSLANGADMIN_FIELD_DISPLAYFROM_','von');
define('_NEWSLANGADMIN_FIELD_DISPLAYTO_','bis');
define('_NEWSLANGADMIN_FIELD_CLEARDATE_','Datum leeren');
define('_NEWSLANGADMIN_FIELD_SETPERPAGE_','Anzeige pro Seite');
define('_NEWSLANGADMIN_FIELD_DATAPERPAGE_','(leer für unbegrenzt)');
define('_NEWSLANGADMIN_FIELD_SORTING_','Sortierung');
define('_NEWSLANGADMIN_FIELD_SORTNEWFIRST_','rückwärts');
define('_NEWSLANGADMIN_FIELD_SORTOLDFIRST_','vorwärts');
define('_NEWSLANGADMIN_FIELD_SORTBYFIELD_','nach Feld');
define('_NEWSLANGADMIN_FIELD_NEWCREATED_','Neu erstellte');
define('_NEWSLANGADMIN_FIELD_CATMENU_','Kategoriebaum');
define('_NEWSLANGADMIN_FIELD_ONLYMENU_','nur als Menü anzeigen (keine Hauptkategorien-Seite)');
define('_NEWSLANGADMIN_FIELD_PERMISSION_','Schreibrechte');
define('_NEWSLANGADMIN_FIELD_SELUSERGROUP_','Benutzergruppen');
define('_NEWSLANGADMIN_FIELD_NOUSERGROUP_','keine (alle Besucher haben Schreibrechte)');
define('_NEWSLANGADMIN_FIELD_MULTISELECT_','zur Mehrfachauswahl STRG gedrückt halten');
define('_NEWSLANGADMIN_FIELD_RELEASING_','Freischaltung');
define('_NEWSLANGADMIN_FIELD_USERINPUTS_','Besuchereingaben');
define('_NEWSLANGADMIN_FIELD_RELEASEDIRECT_','sofort online schalten');
define('_NEWSLANGADMIN_FIELD_RELEASEBYHAND_','von Hand freischalten');
define('_NEWSLANGADMIN_FIELD_BOXCOMMON_','Lightbox allgemein');
define('_NEWSLANGADMIN_FIELD_BOXSINGLE_','Bilder einzeln öffnen');
define('_NEWSLANGADMIN_FIELD_BOXDATASTEP_','weiter und zurück innerhalb eines Datensatzes');
define('_NEWSLANGADMIN_FIELD_BOXFULLSTEP_','weiter und zurück über alle Datensätze (Listenansicht)');
define('_NEWSLANGADMIN_FIELD_BOXONLYONFULL_','Lightbox nicht in der Listenansicht öffnen');
define('_NEWSLANGADMIN_FIELD_DBWITHOUTFILE_','Datenbankeintrag ohne Datei vorhanden');
define('_NEWSLANGADMIN_FIELD_STARTONTOPIC_','Thema Startbeitrag');
define('_NEWSLANGADMIN_FIELD_NUMBNEWEST_','Anzahl neuste Einträge');
define('_NEWSLANGADMIN_FIELD_NEWESTINBLOCK_','anzeigen');
define('_NEWSLANGADMIN_FIELD_COPYHANDLING_','Kopien');
define('_NEWSLANGADMIN_FIELD_TOPICCOPIES_','Themenkopie online/offline schaltbar machen');
define('_NEWSLANGADMIN_FIELD_DATACOPIES_','Beitragskopie online/offline schaltbar machen');
define('_NEWSLANGADMIN_FIELD_SHOWINLIST_','in der Themenliste anzeigen');
define('_NEWSLANGADMIN_FIELD_SHOWINTOPIC_','auf jeder Pagerseite oben anzeigen');
define('_NEWSLANGADMIN_FIELD_SHOWINTOPICFILTER_','nicht filtern');
define('_NEWSLANGADMIN_FIELD_DATAPREVNEXTNAVI_','aus Beitragsnavigation ausschliessen');
define('_NEWSLANGADMIN_FIELD_FILTER_','Filter');
define('_NEWSLANGADMIN_FIELD_FILTERMAINTAIN_','auf ganzer Seite erhalten');
define('_NEWSLANGADMIN_FIELD_USEASTITLE_','Dieses Feld als Titel benutzen');
define('_NEWSLANGADMIN_FIELD_SELECTOPTIONS_','Selectoptionen');
define('_NEWSLANGADMIN_FIELD_SELECTPROTOTYPE_','Eine Selectoption pro Zeile - Muster: Wert|Text|__select__');
define('_NEWSLANGADMIN_FIELD_SELECTLEEROPT_','Wenn Text leer, wird Wert genommen. \'---\' für leere Option');
define('_NEWSLANGADMIN_FIELD_OPTWITHDEFINES_','für Wert und Text sind Sprachdefines möglich');
define('_NEWSLANGADMIN_FIELD_OPTASSELECTED_','\'__select__\' ist optional für gewählt onLoad (nur einmalig)');
define('_NEWSLANGADMIN_FIELD_SHOWSELECTASRADIO_','als Radiobuttons anzeigen (\'---\' wird ignoriert)');
define('_NEWSLANGADMIN_FIELD_NOSELECTS_','Es wurden noch keine Felder definiert');
define('_NEWSLANGADMIN_FIELD_CHECKBOX_','Checkboxen');
define('_NEWSLANGADMIN_FIELD_CHECKPROTOTYPE_','Eine Checkbox pro Zeile - Muster: Name|Wert|Text|__check__');
define('_NEWSLANGADMIN_FIELD_CHECKLEEROPT_','Wenn Text leer, wird Wert genommen.');
define('_NEWSLANGADMIN_FIELD_BOXWITHDEFINES_','für Wert und Text sind Sprachdefines möglich');
define('_NEWSLANGADMIN_FIELD_BOXASCHECKED_','\'__check__\' ist optional für aktiv onLoad (nur einmalig)');
define('_NEWSLANGADMIN_FIELD_NOCHECKS_','Es wurden noch keine Checkboxen definiert');
define('_NEWSLANGADMIN_FIELD_BOXOPEN_','Bild in der Lightbox öffnen');
define('_NEWSLANGADMIN_FIELD_BOXOPENS_','Bilder in der Lightbox öffnen');
define('_NEWSLANGADMIN_FIELD_PLUSIMGUPLOAD_','Bild-Uploadfeld hinzufügen');
define('_NEWSLANGADMIN_FIELD_ONLYMIMEFILE_','Upload nur für folgende Dateitypen');
define('_NEWSLANGADMIN_FIELD_MIMEEXPLAIN_1_','mehrere Dateitypen mit | trennen - leer für alle');
define('_NEWSLANGADMIN_FIELD_MIMEEXPLAIN_2_','Dateiheader (MIME Type) wird abgefragt, nicht die Endung');
define('_NEWSLANGADMIN_FIELD_FILETYPE_','Erlaubte Dateitypen');
define('_NEWSLANGADMIN_FIELD_PROTECTFILE_','Datei vor Zugriff schützen');
define('_NEWSLANGADMIN_FIELD_DOWNLOADFORGROUP_','Download nur für Benutzergruppe');
define('_NEWSLANGADMIN_FIELD_DOWNLOADFORALL_','alle eingeloggten Benutzer');

define('_NEWSLANGADMIN_INPUT_TOPIC_','Titel');
define('_NEWSLANGADMIN_INPUT_FROMTIME_','Anzeigedatum');
define('_NEWSLANGADMIN_OUTPUT_DOWNLOADCOUNTER_','mal heruntergeladen');
define('_NEWSLANGADMIN_HEADLINETITLE_SEO_','SEO Browsertitel');
define('_NEWSLANGADMIN_HEADLINETITLE_CAT_','Kategorie');
define('_NEWSLANGADMIN_HEADLINETITLE_TOPIC_','Thema');
define('_NEWSLANGADMIN_HEADLINETITLE_DATA_','Beitrag');
define('_NEWSLANGADMIN_HEADLINETITLE_SHOW_','aus Daten holen');
define('_NEWSLANGADMIN_SEOINPUT_METAS_','SEO');
define('_NEWSLANGADMIN_SEOINPUT_METATITLE_','Browser Titel');
define('_NEWSLANGADMIN_SEOINPUT_METADESC_','Meta Description');
define('_NEWSLANGADMIN_SEOINPUT_METAKEYS_','Meta Keywords');

define('_NEWSLANGADMIN_INPUT_FIELD_HEADLINE_','Headline');
define('_NEWSLANGADMIN_INPUT_FIELD_DATE_','Datum');
define('_NEWSLANGADMIN_INPUT_FIELD_IMAGE_','Bild');
define('_NEWSLANGADMIN_INPUT_FIELD_IMGALT_','Bild Alt-Text');
define('_NEWSLANGADMIN_INPUT_FIELD_TEASER_','Teaser');
define('_NEWSLANGADMIN_INPUT_FIELD_TEXT_','Text');








