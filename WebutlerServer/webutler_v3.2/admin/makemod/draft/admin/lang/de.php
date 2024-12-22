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

define('_MODMAKERLANGADMIN_LOGIN_HEADER_','Login für Redakteure');
define('_MODMAKERLANGADMIN_LOGIN_USERNAME_','Benutzername');
define('_MODMAKERLANGADMIN_LOGIN_PASSWORD_','Passwort');
define('_MODMAKERLANGADMIN_LOGIN_SUBMIT_','einloggen');
define('_MODMAKERLANGADMIN_LOGIN_ATTEMPTS_','Login ist für __TIME__ Minuten gesperrt!');
define('_MODMAKERLANGADMIN_LOGIN_NOPERMISSION_','Sie haben keine Zugangsberechtigung');
define('_MODMAKERLANGADMIN_NOTAVAILFORSUBS_','Die Einstellungen können von Redakteuren nicht bearbeitet werden!');
define('_MODMAKERLANGADMIN_LOGOUT_','Logout');

define('_MODMAKERLANGADMIN_SETCHMODFOR_','Die Datei /data/###CHMODMODNAME###.db und das Verzeichnis /media müssen beschreibbar sein!');
define('_MODMAKERLANGADMIN_WRONGCHMOD_','Folgende Dateien/Verzeichnisse sind nicht beschreibbar:');
define('_MODMAKERLANGADMIN_SETRIGHTCHMODS_','Bitte Chmod ###CHMODFOLDER### für Dateien und Chmod ###CHMODFILES### für Verzeichnisse mit einem FTP-Programm setzen.');
define('_MODMAKERLANGADMIN_INSTALL_','Installation');
define('_MODMAKERLANGADMIN_SETTINGS_','Einstellungen');

define('_MODMAKERLANGADMIN_OPTION_','Option');
define('_MODMAKERLANGADMIN_OPTIONS_','Optionen');
define('_MODMAKERLANGADMIN_NOOPTIONGROUPS_','Es wurden noch keine Optionengruppen angelegt');
define('_MODMAKERLANGADMIN_NEWOPTION_','Neue Optionengruppe');
define('_MODMAKERLANGADMIN_EDITOPTION_','Optionengruppe bearbeiten');
define('_MODMAKERLANGADMIN_OPTIONIMGSUPLOAD_','Optionenbilder hochladen');
define('_MODMAKERLANGADMIN_OPTIMGSUBFOLDER_','Neues Unterverzeichnis anlegen');
define('_MODMAKERLANGADMIN_CHOOSE_UPLOAD_','wählen');
define('_MODMAKERLANGADMIN_NOAVAILOPTION_','Keine Optionen vorhanden!');
define('_MODMAKERLANGADMIN_NOEXISTOPTION_','Diese Optionengruppe existiert nicht!');
define('_MODMAKERLANGADMIN_OPTIONGROUP_','Gruppe');
define('_MODMAKERLANGADMIN_OPTIONVALUES_','Optionen');
define('_MODMAKERLANGADMIN_OPTIONIMGS_','Bilder');
define('_MODMAKERLANGADMIN_OPTIONIMGSOPEN_','auflisten');
define('_MODMAKERLANGADMIN_OPTIONIMGSCLOSE_','schließen');
define('_MODMAKERLANGADMIN_OPTIONHEADLINE_','Eine Option pro Zeile, Werte mit | trennen');
define('_MODMAKERLANGADMIN_OPTIONWITHDEFINES_','Es können auch Sprachdefines eingegeben werden');
define('_MODMAKERLANGADMIN_OPTIONEXPLAIN_','Optionen werden im Template options.tpl ausgegeben<br />individuelle Templates mit options_(ID).tpl erstellen<br />Ausgabe der Werte als Array $db_data[\'option\'][0], $db_data[\'option\'][1]...');

define('_MODMAKERLANGADMIN_CATEGORY_','Kategorie');
define('_MODMAKERLANGADMIN_CATEGORIES_','Kategorien');
define('_MODMAKERLANGADMIN_NEWCATEGORY_','Neue Kategorie');
define('_MODMAKERLANGADMIN_SUBCATEGORIES_','Kategoriebaum');
define('_MODMAKERLANGADMIN_EDITCATEGORY_','Kategorie bearbeiten');
define('_MODMAKERLANGADMIN_NOSELECTCATEGORY_','Es wurde keine Kategorie ausgewählt!');
define('_MODMAKERLANGADMIN_NOAVAILCATEGORY_','Keine Kategorie vorhanden!');
define('_MODMAKERLANGADMIN_NOEXISTCATEGORY_','Diese Kategorie existiert nicht!');
define('_MODMAKERLANGADMIN_THEREISNOCAT_','Der Kategoriebaum wurde noch nicht angelegt');
define('_MODMAKERLANGADMIN_CATDESCRIPT_','Beschreibung');
define('_MODMAKERLANGADMIN_CATIMAGE_','Bild');
define('_MODMAKERLANGADMIN_CATLINK_','Menülink');
define('_MODMAKERLANGADMIN_BASECATENTRY_','Einstiegspunkt');

define('_MODMAKERLANGADMIN_TOPIC_','Thema');
define('_MODMAKERLANGADMIN_TOPICS_','Themen');
define('_MODMAKERLANGADMIN_NEWTOPIC_','Neues Thema');
define('_MODMAKERLANGADMIN_COPYTOPIC_','Thema kopieren');
define('_MODMAKERLANGADMIN_EDITTOPIC_','Thema bearbeiten');
define('_MODMAKERLANGADMIN_NOSELECTTOPIC_','Es wurde kein Thema ausgewählt!');
define('_MODMAKERLANGADMIN_NOAVAILTOPIC_','Keine Themen vorhanden!');
define('_MODMAKERLANGADMIN_NOEXISTTOPIC_','Dieses Thema existiert nicht!');
define('_MODMAKERLANGADMIN_TOPICCOPYID_','ID des zu kopierenden Themas eingeben');
define('_MODMAKERLANGADMIN_TOPICCOPYOF_','Kopie von Thema mit ID');

define('_MODMAKERLANGADMIN_DATA_','Beitrag');
define('_MODMAKERLANGADMIN_DATAS_','Beiträge');
define('_MODMAKERLANGADMIN_NEWDATA_','Neuer Beitrag');
define('_MODMAKERLANGADMIN_COPYDATA_','Beitrag kopieren');
define('_MODMAKERLANGADMIN_EDITDATA_','Beitrag bearbeiten');
define('_MODMAKERLANGADMIN_NOSELECTDATA_','Es wurde kein Beitrag ausgewählt!');
define('_MODMAKERLANGADMIN_NOAVAILDATA_','Keine Beiträge vorhanden!');
define('_MODMAKERLANGADMIN_NOEXISTDATA_','Dieser Beitrag existiert nicht!');
define('_MODMAKERLANGADMIN_DATADELLAST_','Der Startbeitrag kann erst als letztes verschoben werden.');
define('_MODMAKERLANGADMIN_DATACOPYID_','ID des zu kopierenden Beitrags eingeben');
define('_MODMAKERLANGADMIN_DATACOPYOF_','Kopie von Beitrag mit ID');

define('_MODMAKERLANGADMIN_WRONGLANG_','Die Zieldaten müssen in der selben Sprache sein');
define('_MODMAKERLANGADMIN_COPYDIST_','Der Startbeitrag kann nicht kopiert werden');
define('_MODMAKERLANGADMIN_EDITCOPY_','Kopien können nicht bearbeitet werden');
define('_MODMAKERLANGADMIN_CHANGETOCOPY_','In eine Kopie verschieben ist nicht möglich');

define('_MODMAKERLANGADMIN_BUTTON_EDIT_','bearbeiten');
define('_MODMAKERLANGADMIN_BUTTON_GETORG_','Orginal aufrufen');
define('_MODMAKERLANGADMIN_BUTTON_DELETE_','löschen');
define('_MODMAKERLANGADMIN_PROMPT_SHOULD_','Soll');
define('_MODMAKERLANGADMIN_PROMPT_COPY_','die Kopie von');
define('_MODMAKERLANGADMIN_PROMPT_REALDEL_','wirklich gelöscht werden?');
define('_MODMAKERLANGADMIN_BUTTON_CHANGETO_','verschieben');
define('_MODMAKERLANGADMIN_PROMPT_NEWID_','Bitte die ID');
define('_MODMAKERLANGADMIN_PROMPT_OFCAT_','der neuen Kategorie');
define('_MODMAKERLANGADMIN_PROMPT_OFTOPIC_','des neuen Themas');
define('_MODMAKERLANGADMIN_PROMPT_INSERT_','eingeben!');
define('_MODMAKERLANGADMIN_PROMPT_ERROR_','Fehlerhafte Eingabe!');
define('_MODMAKERLANGADMIN_BUTTON_ONLINE_','online');
define('_MODMAKERLANGADMIN_BUTTON_OFFLINE_','offline');
define('_MODMAKERLANGADMIN_BUTTON_SETON_','freischalten');
define('_MODMAKERLANGADMIN_BUTTON_SETOFF_','sperren');
define('_MODMAKERLANGADMIN_BUTTON_INSERT_','einfügen');
define('_MODMAKERLANGADMIN_BUTTON_SHOW_','anzeigen');
define('_MODMAKERLANGADMIN_BUTTON_HOCH_','nach oben');
define('_MODMAKERLANGADMIN_BUTTON_RUNTER_','nach unten');
define('_MODMAKERLANGADMIN_BUTTON_LINKS_','raus');
define('_MODMAKERLANGADMIN_BUTTON_RECHTS_','rein');
define('_MODMAKERLANGADMIN_BUTTON_MAKEFOLDER_','Ordner erstellen');
define('_MODMAKERLANGADMIN_BUTTON_UPLOAD_','hochladen');
define('_MODMAKERLANGADMIN_BUTTON_SAVE_','speichern');
define('_MODMAKERLANGADMIN_BUTTON_CANCEL_','abbrechen');
define('_MODMAKERLANGADMIN_BUTTON_CHOOSE_','wählen');
define('_MODMAKERLANGADMIN_NAVI_PAGE_','Seite');
define('_MODMAKERLANGADMIN_ALERT_UPLOADWRONGMIME_','Der gewählte Dateityp ist nicht erlaubt!');
define('_MODMAKERLANGADMIN_ALERT_UPLOADCOMPLETE_','Übertragung abgeschlossen! Bitte speichern Sie den Datensatz, damit die Datei verfügbar ist.');
define('_MODMAKERLANGADMIN_ALERT_UPLOADLARGE_1_','Die gewählte Datei muß aufgrund ihrer Größe separat hochgeladen werden!');
define('_MODMAKERLANGADMIN_ALERT_UPLOADLARGE_2_','Der Upload großer Dateien nimmt in der Regel einen längeren Zeitraum in Anspruch.');
define('_MODMAKERLANGADMIN_ALERT_UPLOADLARGE_3_','Der neue Datensatz muß nach dem Upload ebenfalls noch gespeichert werden, die hochgeladene Datei ist sonst nicht verfügbar.');
define('_MODMAKERLANGADMIN_ALERT_UPLOADSTATE_','Uploadstatus');

define('_MODMAKERLANGADMIN_FIELD_LOGINFORSUBS_','Login für Redakteure');
define('_MODMAKERLANGADMIN_FIELD_NOSUBEDITOR_','Redakteurslogin deaktivieren');
define('_MODMAKERLANGADMIN_FIELD_HIDEFORSUBS_','Einstellungen vor Redakteuren verbergen');
define('_MODMAKERLANGADMIN_FIELD_USERSASSUBS_','Benutzergruppe als Redakteure festlegen');
define('_MODMAKERLANGADMIN_FIELD_LANGUAGE_','Sprache');
define('_MODMAKERLANGADMIN_FIELD_NOLANGUAGES_','keine Sprachen vorhanden');
define('_MODMAKERLANGADMIN_FIELD_NOUSERS_','keine Benutzergruppe vorhanden');
define('_MODMAKERLANGADMIN_FIELD_ISSTARTDATA_','Startbeitrag wird immer angezeigt');
define('_MODMAKERLANGADMIN_FIELD_DISPLAY_','Anzeige');
define('_MODMAKERLANGADMIN_FIELD_DISPLAYFROM_','von');
define('_MODMAKERLANGADMIN_FIELD_DISPLAYTO_','bis');
define('_MODMAKERLANGADMIN_FIELD_CLEARDATE_','Datum leeren');
define('_MODMAKERLANGADMIN_FIELD_SETPERPAGE_','Anzeige pro Seite');
define('_MODMAKERLANGADMIN_FIELD_DATAPERPAGE_','(leer für unbegrenzt)');
define('_MODMAKERLANGADMIN_FIELD_SORTING_','Sortierung');
define('_MODMAKERLANGADMIN_FIELD_SORTNEWFIRST_','rückwärts');
define('_MODMAKERLANGADMIN_FIELD_SORTOLDFIRST_','vorwärts');
define('_MODMAKERLANGADMIN_FIELD_SORTBYFIELD_','nach Feld');
define('_MODMAKERLANGADMIN_FIELD_NEWCREATED_','Neu erstellte');
define('_MODMAKERLANGADMIN_FIELD_CATMENU_','Kategoriebaum');
define('_MODMAKERLANGADMIN_FIELD_ONLYMENU_','nur als Menü anzeigen (keine Hauptkategorien-Seite)');
define('_MODMAKERLANGADMIN_FIELD_PERMISSION_','Schreibrechte');
define('_MODMAKERLANGADMIN_FIELD_SELUSERGROUP_','Benutzergruppen');
define('_MODMAKERLANGADMIN_FIELD_NOUSERGROUP_','keine (alle Besucher haben Schreibrechte)');
define('_MODMAKERLANGADMIN_FIELD_MULTISELECT_','zur Mehrfachauswahl STRG gedrückt halten');
define('_MODMAKERLANGADMIN_FIELD_RELEASING_','Freischaltung');
define('_MODMAKERLANGADMIN_FIELD_USERINPUTS_','Besuchereingaben');
define('_MODMAKERLANGADMIN_FIELD_RELEASEDIRECT_','sofort online schalten');
define('_MODMAKERLANGADMIN_FIELD_RELEASEBYHAND_','von Hand freischalten');
define('_MODMAKERLANGADMIN_FIELD_BOXCOMMON_','Lightbox allgemein');
define('_MODMAKERLANGADMIN_FIELD_BOXSINGLE_','Bilder einzeln öffnen');
define('_MODMAKERLANGADMIN_FIELD_BOXDATASTEP_','weiter und zurück innerhalb eines Datensatzes');
define('_MODMAKERLANGADMIN_FIELD_BOXFULLSTEP_','weiter und zurück über alle Datensätze (Listenansicht)');
define('_MODMAKERLANGADMIN_FIELD_BOXONLYONFULL_','Lightbox nicht in der Listenansicht öffnen');
define('_MODMAKERLANGADMIN_FIELD_DBWITHOUTFILE_','Datenbankeintrag ohne Datei vorhanden');
define('_MODMAKERLANGADMIN_FIELD_STARTONTOPIC_','Thema Startbeitrag');
define('_MODMAKERLANGADMIN_FIELD_NUMBNEWEST_','Anzahl neuste Einträge');
define('_MODMAKERLANGADMIN_FIELD_NEWESTINBLOCK_','anzeigen');
define('_MODMAKERLANGADMIN_FIELD_COPYHANDLING_','Kopien');
define('_MODMAKERLANGADMIN_FIELD_TOPICCOPIES_','Themenkopie online/offline schaltbar machen');
define('_MODMAKERLANGADMIN_FIELD_DATACOPIES_','Beitragskopie online/offline schaltbar machen');
define('_MODMAKERLANGADMIN_FIELD_SHOWINLIST_','in der Themenliste anzeigen');
define('_MODMAKERLANGADMIN_FIELD_SHOWINTOPIC_','auf jeder Pagerseite oben anzeigen');
define('_MODMAKERLANGADMIN_FIELD_SHOWINTOPICFILTER_','nicht filtern');
define('_MODMAKERLANGADMIN_FIELD_DATAPREVNEXTNAVI_','aus Beitragsnavigation ausschliessen');
define('_MODMAKERLANGADMIN_FIELD_FILTER_','Filter');
define('_MODMAKERLANGADMIN_FIELD_FILTERMAINTAIN_','auf ganzer Seite erhalten');
define('_MODMAKERLANGADMIN_FIELD_USEASTITLE_','Dieses Feld als Titel benutzen');
define('_MODMAKERLANGADMIN_FIELD_SELECTOPTIONS_','Selectoptionen');
define('_MODMAKERLANGADMIN_FIELD_SELECTPROTOTYPE_','Eine Selectoption pro Zeile - Muster: Wert|Text|__select__');
define('_MODMAKERLANGADMIN_FIELD_SELECTLEEROPT_','Wenn Text leer, wird Wert genommen. \'---\' für leere Option');
define('_MODMAKERLANGADMIN_FIELD_OPTWITHDEFINES_','für Wert und Text sind Sprachdefines möglich');
define('_MODMAKERLANGADMIN_FIELD_OPTASSELECTED_','\'__select__\' ist optional für gewählt onLoad (nur einmalig)');
define('_MODMAKERLANGADMIN_FIELD_SHOWSELECTASRADIO_','als Radiobuttons anzeigen (\'---\' wird ignoriert)');
define('_MODMAKERLANGADMIN_FIELD_NOSELECTS_','Es wurden noch keine Felder definiert');
define('_MODMAKERLANGADMIN_FIELD_CHECKBOX_','Checkboxen');
define('_MODMAKERLANGADMIN_FIELD_CHECKPROTOTYPE_','Eine Checkbox pro Zeile - Muster: Name|Wert|Text|__check__');
define('_MODMAKERLANGADMIN_FIELD_CHECKLEEROPT_','Wenn Text leer, wird Wert genommen.');
define('_MODMAKERLANGADMIN_FIELD_BOXWITHDEFINES_','für Wert und Text sind Sprachdefines möglich');
define('_MODMAKERLANGADMIN_FIELD_BOXASCHECKED_','\'__check__\' ist optional für aktiv onLoad (nur einmalig)');
define('_MODMAKERLANGADMIN_FIELD_NOCHECKS_','Es wurden noch keine Checkboxen definiert');
define('_MODMAKERLANGADMIN_FIELD_BOXOPEN_','Bild in der Lightbox öffnen');
define('_MODMAKERLANGADMIN_FIELD_BOXOPENS_','Bilder in der Lightbox öffnen');
define('_MODMAKERLANGADMIN_FIELD_PLUSIMGUPLOAD_','Bild-Uploadfeld hinzufügen');
define('_MODMAKERLANGADMIN_FIELD_ONLYMIMEFILE_','Upload nur für folgende Dateitypen');
define('_MODMAKERLANGADMIN_FIELD_MIMEEXPLAIN_1_','mehrere Dateitypen mit | trennen - leer für alle');
define('_MODMAKERLANGADMIN_FIELD_MIMEEXPLAIN_2_','Dateiheader (MIME Type) wird abgefragt, nicht die Endung');
define('_MODMAKERLANGADMIN_FIELD_FILETYPE_','Erlaubte Dateitypen');
define('_MODMAKERLANGADMIN_FIELD_PROTECTFILE_','Datei vor Zugriff schützen');
define('_MODMAKERLANGADMIN_FIELD_DOWNLOADFORGROUP_','Download nur für Benutzergruppe');
define('_MODMAKERLANGADMIN_FIELD_DOWNLOADFORALL_','alle eingeloggten Benutzer');

define('_MODMAKERLANGADMIN_INPUT_TOPIC_','Titel');
define('_MODMAKERLANGADMIN_INPUT_FROMTIME_','Anzeigedatum');
define('_MODMAKERLANGADMIN_OUTPUT_DOWNLOADCOUNTER_','mal heruntergeladen');
define('_MODMAKERLANGADMIN_HEADLINETITLE_SEO_','SEO Browsertitel');
define('_MODMAKERLANGADMIN_HEADLINETITLE_CAT_','Kategorie');
define('_MODMAKERLANGADMIN_HEADLINETITLE_TOPIC_','Thema');
define('_MODMAKERLANGADMIN_HEADLINETITLE_DATA_','Beitrag');
define('_MODMAKERLANGADMIN_HEADLINETITLE_SHOW_','aus Daten holen');
define('_MODMAKERLANGADMIN_SEOINPUT_METAS_','SEO');
define('_MODMAKERLANGADMIN_SEOINPUT_METATITLE_','Browser Titel');
define('_MODMAKERLANGADMIN_SEOINPUT_METADESC_','Meta Description');
define('_MODMAKERLANGADMIN_SEOINPUT_METAKEYS_','Meta Keywords');

###ADMIN_LANGS###








