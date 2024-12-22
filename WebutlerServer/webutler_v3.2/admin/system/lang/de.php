<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


// Installation

define('_WBLANGADMIN_INSTALL_TITLE_','Installation');
define('_WBLANGADMIN_INSTALL_ERROR_','Fehler!');
define('_WBLANGADMIN_INSTALL_REWRITE_','Das Apache Modul mod_rewrite ist nicht aktiv. Bitte aktivieren Sie es oder ändern Sie die Einstellung in der Datei /settings/globalvars.php');
define('_WBLANGADMIN_INSTALL_REWRITEAGAIN_','weiter');
define('_WBLANGADMIN_INSTALL_SALTKEYS_','Vor der Installation bitte die Salt Keys in der Datei /settings/globalvars.php eintragen! Die Salt Keys dürfen nach der Installation nicht mehr geändert werden.');
define('_WBLANGADMIN_INSTALL_KEYSAGAIN_','weiter');
define('_WBLANGADMIN_INSTALL_PHPVERSION_','Der Webutler benötigt PHP Version 5.4 oder höher!');
define('_WBLANGADMIN_INSTALL_CHMODERROR_1_','Es befinden sich nicht beschreibbare Dateien oder Verzeichnisse auf dem Webspace!');
define('_WBLANGADMIN_INSTALL_CHMODERROR_2_','Bitte setzen Sie mit Ihrem FTP-Programm CHMOD laut den Angaben des Providers für folgende Dateien und/oder Verzeichnisse und klicken Sie anschließend auf');
define('_WBLANGADMIN_INSTALL_CHMODAGAIN_','Erneut prüfen');
define('_WBLANGADMIN_INSTALL_CHMODOK_','Alle Schreibrechte sind richtig gesetzt!');
define('_WBLANGADMIN_INSTALL_CONFIGNOWRITE_','Die Konfigurationsdatei konnte nicht geöffnet werden.');
define('_WBLANGADMIN_INSTALL_FIELDEMPTY_','Sie haben nicht alle notwendigen Felder ausgefüllt.');
define('_WBLANGADMIN_INSTALL_FIELDUSERNAME_','Benutzername');
define('_WBLANGADMIN_INSTALL_FIELDUSERPASS_','Passwort');
define('_WBLANGADMIN_INSTALL_FIELDSERVERPATH_','Serverpfad');
define('_WBLANGADMIN_INSTALL_FIELDHOMEPAGEURL_','Homepage URL');
define('_WBLANGADMIN_INSTALL_FIELDLANGUAGE_','Sprache Administrator');
define('_WBLANGADMIN_INSTALL_FIELDHOMEPAGE_','Startseite');
define('_WBLANGADMIN_INSTALL_SAVESETTINGS_','Einstellungen speichern');
define('_WBLANGADMIN_INSTALL_WRONGSIGN_','Unerlaubte Zeichen im Benutzernamen.');
define('_WBLANGADMIN_INSTALL_WRONGPASS_1_','Unerlaubte Zeichen im Passwort.');
define('_WBLANGADMIN_INSTALL_WRONGPASS_2_','A bis Z, a bis z, 0 bis 9 und #+-_*@%&=!?');
define('_WBLANGADMIN_INSTALL_NOTSAVED_','Ihre Angaben konnten nicht übernommen werden.');
define('_WBLANGADMIN_INSTALL_INSTALLOK_','Die Installation wurde erfolgreich durchgeführt.');
define('_WBLANGADMIN_INSTALL_INSTOKTXT_1_','Erweiterte Grundeinstellungen');
define('_WBLANGADMIN_INSTALL_INSTOKTXT_2_','können bei Bedarf in der Datei');
define('_WBLANGADMIN_INSTALL_INSTOKTXT_3_','vorgenommen werden.');
define('_WBLANGADMIN_INSTALL_LINKSTART_','zur Startseite');
define('_WBLANGADMIN_INSTALL_LINKLOGIN_','zum Login');


// Login

define('_WBLANGADMIN_LOGIN_TITLE_','Login');
define('_WBLANGADMIN_LOGIN_ERRORNOCOOKIE_','Cookies müssen aktiviert sein!');
define('_WBLANGADMIN_LOGIN_ERRORWRONGDATA_','Falsche Zugangsdaten!');
define('_WBLANGADMIN_LOGIN_USER_','Benutzername');
define('_WBLANGADMIN_LOGIN_PASS_','Passwort');
define('_WBLANGADMIN_LOGIN_LOGIN_','einloggen');
define('_WBLANGADMIN_LOGIN_ATTEMPTS_','Login für __TIME__ Minuten gesperrt');


// Adminpages

define('_WBLANGADMIN_ADMINPAGE_FILENOTEXISTS_','Die Datei existiert nicht.');
define('_WBLANGADMIN_ADMINPAGE_BLOCKPREVIEW_','Blockvorschau');
define('_WBLANGADMIN_ADMINPAGE_MENUPREVIEW_','Menüvorschau');
define('_WBLANGADMIN_ADMINPAGE_LAYOUTPREVIEW_','Layoutvorschau');
define('_WBLANGADMIN_ADMINPAGE_PREVIEWERRORTEXT_','Ausgabe der Fehlermeldung');


// Adminmenü

define('_WBLANGADMIN_EDITBOX_ADMINISTRATION_','Administration');

define('_WBLANGADMIN_EDITBOX_PAGE_','Seite');
define('_WBLANGADMIN_EDITBOX_PAGEEDIT_','bearbeiten');
define('_WBLANGADMIN_EDITBOX_PAGEADD_','hinzufügen');
define('_WBLANGADMIN_EDITBOX_PAGEUPPER_','freischalten');
define('_WBLANGADMIN_EDITBOX_PAGEDOWNER_','stilllegen');
define('_WBLANGADMIN_EDITBOX_PAGEUNDO_','rückgängig');
define('_WBLANGADMIN_EDITBOX_PAGECOLUMNS_','Spalten');
define('_WBLANGADMIN_EDITBOX_PAGECAT_','Kategorie');
define('_WBLANGADMIN_EDITBOX_PAGELANG_','Sprache');
define('_WBLANGADMIN_EDITBOX_PAGERENAME_','umbenennen');
define('_WBLANGADMIN_EDITBOX_PAGEPUBLIC_','publizieren');
define('_WBLANGADMIN_EDITBOX_PAGEDISCARD_','verwerfen');
define('_WBLANGADMIN_EDITBOX_PAGEDELETE_','löschen');
define('_WBLANGADMIN_EDITBOX_PAGECHANGE_','wechseln');

define('_WBLANGADMIN_EDITBOX_EDIT_','Bearbeiten');
define('_WBLANGADMIN_EDITBOX_EDITPAGE_','Seite');
define('_WBLANGADMIN_EDITBOX_EDITCONTENT_','Inhalt');
define('_WBLANGADMIN_EDITBOX_EDITMENU_','Menü');
define('_WBLANGADMIN_EDITBOX_EDITBLOCK_','Block');

define('_WBLANGADMIN_EDITBOX_MEDIA_','Medien');

define('_WBLANGADMIN_EDITBOX_SYSTEM_','System');
define('_WBLANGADMIN_EDITBOX_SYSTEMSETTINGS_','einstellen');
define('_WBLANGADMIN_EDITBOX_SYSTEMEXTENDED_','erweitert');
define('_WBLANGADMIN_EDITBOX_SYSTEMSTYLES_','Stylesheets');
define('_WBLANGADMIN_EDITBOX_SYSTEMCATS_','Kategorien');
define('_WBLANGADMIN_EDITBOX_SYSTEMLINKS_','Linkfarben');
define('_WBLANGADMIN_EDITBOX_SYSTEMPATTERN_','Vorlagen');
define('_WBLANGADMIN_EDITBOX_SYSTEMFORMS_','Formulare');
define('_WBLANGADMIN_EDITBOX_SYSTEMLANGS_','Sprachen');
define('_WBLANGADMIN_EDITBOX_SYSTEMUSERS_','Benutzer');
define('_WBLANGADMIN_EDITBOX_SYSTEMMODMAKER_','ModMaker');

define('_WBLANGADMIN_EDITBOX_MODULES_','Module');

define('_WBLANGADMIN_EDITBOX_LOGOUT_','Logout');


// Popup Windows

define('_WBLANGADMIN_POPUPWIN_PAGETOOFF_','Die aktuelle Seite wird offline geschaltet.');
define('_WBLANGADMIN_POPUPWIN_PAGETOON_','Die aktuelle Seite wird online geschaltet.');
define('_WBLANGADMIN_POPUPWIN_PAGEVERSION_','Die letzte Version der aktuellen Seite, gespeichert am %s, wird wieder hergestellt.');
define('_WBLANGADMIN_POPUPWIN_VERSION_DATEFORMAT_','j.n.y, H:i');
define('_WBLANGADMIN_POPUPWIN_PAGEDELETE_','Die Seite _STRING_ wird gelöscht!');
define('_WBLANGADMIN_POPUPWIN_PAGERENAME_','Die Seite _STRING_OLD_ wird umbenannt in _STRING_NEW_');
define('_WBLANGADMIN_POPUPWIN_LAYOUTDELETE_','Das Layout _STRING_ wird gelöscht!');
define('_WBLANGADMIN_POPUPWIN_PATTERNDELETE_','Die Vorlage _STRING_ wird gelöscht!');
define('_WBLANGADMIN_POPUPWIN_MENUDELETE_','Das Menü _STRING_ wird gelöscht!');
define('_WBLANGADMIN_POPUPWIN_BLOCKDELETE_','Der Block _STRING_ wird gelöscht!');
define('_WBLANGADMIN_POPUPWIN_LANGUAGE_','Die Sprache der aktuellen Seite wird auf _STRING_ geändert.');
define('_WBLANGADMIN_POPUPWIN_WRITEABLE_FILE_','Es bestehen keine Zugriffsrechte auf Datei %s. Bitte CHMOD laut den Angaben des Providers setzen.');
define('_WBLANGADMIN_POPUPWIN_WRITEABLE_FOLDER_','Es bestehen keine Zugriffsrechte auf Verzeichnis %s. Bitte CHMOD laut den Angaben des Providers setzen.');
define('_WBLANGADMIN_POPUPWIN_RECEIVERDELETE_','Nach dem löschen können Formulare, die diesen Empfänger benutzen nicht mehr versendet werden!');
define('_WBLANGADMIN_POPUPWIN_NOPREVIEW_','Bitte wählen Sie zuerst eine Datei!');
define('_WBLANGADMIN_POPUPWIN_SAVE_NAMEEXISTS_','Der gewählte Dateiname existiert bereits.');
define('_WBLANGADMIN_POPUPWIN_SAVE_NONAME_','Bitte geben Sie einen Dateinamen ein und wählen Sie ein Layout.');
define('_WBLANGADMIN_POPUPWIN_SAVE_NOLOGIN_','Bitte geben Sie einen Layoutnamen ein.');
define('_WBLANGADMIN_POPUPWIN_SAVE_NOUSER_','Sie haben keinen Benutzernamen vergeben.');
define('_WBLANGADMIN_POPUPWIN_SAVE_WRONGSIGNS_','Unerlaubte Zeichen im Benutzernamen.');
define('_WBLANGADMIN_POPUPWIN_SAVE_WRONGPASS_','Die Passworteingabe war fehlerhaft.');
define('_WBLANGADMIN_POPUPWIN_SAVE_WRONGPASSSIGNS_','Zeichen: A bis Z, a bis z, 0 bis 9 und #+-_*@%&=!?');
define('_WBLANGADMIN_POPUPWIN_SAVE_OPENCONF_','Die Konfigurationsdatei konnte nicht geöffnet werden.');
define('_WBLANGADMIN_POPUPWIN_SAVE_CONFSAVE_','Ihre Änderungen konnten nicht gespeichert werden.');
define('_WBLANGADMIN_POPUPWIN_SAVE_CONFSAVEOK_','Ihre Änderungen wurden erfolgreich gespeichert.');
define('_WBLANGADMIN_POPUPWIN_SAVE_MENUEXISTS_','Der gewählte Menüname existiert bereits.');
define('_WBLANGADMIN_POPUPWIN_SAVE_DEFAULTLINK_','Startseite');
define('_WBLANGADMIN_POPUPWIN_SAVE_MENUNAME_','Bitte geben Sie einen Menünamen ein.');
define('_WBLANGADMIN_POPUPWIN_SAVE_BLOCKEXISTS_','Der gewählte Blockname existiert bereits.');
define('_WBLANGADMIN_POPUPWIN_SAVE_DEFAULTBLOCK_','Neuer Block');
define('_WBLANGADMIN_POPUPWIN_SAVE_BLOCKNAME_','Bitte geben Sie einen Blocknamen ein.');

define('_WBLANGADMIN_POPUPWIN_PATTERN_ISSAVED_','Die Vorlage wurde angelegt.');
define('_WBLANGADMIN_POPUPWIN_LAYOUT_ISSAVED_','Die Layoutdatei wurde angelegt.');
define('_WBLANGADMIN_POPUPWIN_MENU_ISSAVED_','Die Menüdatei wurde angelegt.');
define('_WBLANGADMIN_POPUPWIN_BLOCK_ISSAVED_','Die Blockdatei wurde angelegt.');
define('_WBLANGADMIN_POPUPWIN_PAGE_ISSAVED_','Die neue Seite wurde angelegt.');

define('_WBLANGADMIN_POPUPWIN_PATTERN_ISDELETED_','Die Vorlage wurde gelöscht.');
define('_WBLANGADMIN_POPUPWIN_LAYOUT_ISDELETED_','Die Layoutdatei wurde gelöscht.');
define('_WBLANGADMIN_POPUPWIN_MENU_ISDELETED_','Die Menüdatei wurde gelöscht.');
define('_WBLANGADMIN_POPUPWIN_BLOCK_ISDELETED_','Die Blockdatei wurde gelöscht.');
define('_WBLANGADMIN_POPUPWIN_PAGE_ISDELETED_','Die Seite wurde gelöscht.');
define('_WBLANGADMIN_POPUPWIN_DEFPAGE_ISDELETED_','Die aktuelle Seite wurde gelöscht.');

define('_WBLANGADMIN_POPUPWIN_COLUMNS_INSERTED_','Die Spalten wurden eingefügt.');
define('_WBLANGADMIN_POPUPWIN_COLUMNS_NOTINSERTED_','Die Spalten konnten nicht eingefügt werden.');
define('_WBLANGADMIN_POPUPWIN_COLUMNS_DELETED_','Die Spalten wurden entfernt.');
define('_WBLANGADMIN_POPUPWIN_COLUMNS_NOTDELETED_','Die Spalten konnten nicht entfernt werden.');
define('_WBLANGADMIN_POPUPWIN_COLUMNS_NOTWRITEABLE_','ist nicht beschreibbar!');

define('_WBLANGADMIN_POPUPWIN_ROUTE_RELOAD_','Die Seite muß neu geladen werden!');
define('_WBLANGADMIN_POPUPWIN_ROUTE_NEWPAGE_','Weiterleitung zur neuen Seite:');
define('_WBLANGADMIN_POPUPWIN_ROUTE_FRONTPAGE_','Die aktuelle Seite existiert nicht mehr. Sie werden zur Startseite weitergeleitet!');

define('_WBLANGADMIN_POPUPWIN_CATREQUEST_','Verschiebe Seite von _STRING_OLD_ nach _STRING_NEW_');
define('_WBLANGADMIN_POPUPWIN_CATEGORIES_ISSAVED_','Die Einstellung wurde geändert.');
define('_WBLANGADMIN_POPUPWIN_CATEGORIES_NOTSAVED_','Die Einstellung konnte nicht geändert werden.');

define('_WBLANGADMIN_POPUPWIN_LANGUAGE_ISSAVED_','Die Spracheinstellung wurde geändert.');

define('_WBLANGADMIN_POPUPWIN_TEMPFILE_NOTEXISTS_','Die Seite existiert nicht!');
define('_WBLANGADMIN_POPUPWIN_TEMPFILE_NOTWRITEABLE_','ist nicht beschreibbar!');
define('_WBLANGADMIN_POPUPWIN_TEMPFILE_PUBLIC_','Die Seite wurde veröffentlicht.');
define('_WBLANGADMIN_POPUPWIN_TEMPFILE_DELETE_','Soll die temporär gespeicherte Version\nder Seite wirklich gelöscht werden?');



// Offlinepages

define('_WBLANGADMIN_OFF_PAGEUNPUBLIC_','Diese Seitenversion ist temporär gespeichert und nicht öffentlich sichtbar!');
define('_WBLANGADMIN_OFF_PAGEISOFFLINE_','Diese Seite ist stillgelegt und nicht aufrufbar!');
define('_WBLANGADMIN_OFF_PAGEISUSERS_','Diese Seite ist nur angemeldeten Benutzern zugänglich!');


// Buttons

define('_WBLANGADMIN_WIN_BUTTONS_PREVIEW_','Vorschau');
define('_WBLANGADMIN_WIN_BUTTONS_SAVE_','speichern');
define('_WBLANGADMIN_WIN_BUTTONS_CANCEL_','abbrechen');
define('_WBLANGADMIN_WIN_BUTTONS_INSERT_','einfügen');
define('_WBLANGADMIN_WIN_BUTTONS_ADD_','anlegen');
define('_WBLANGADMIN_WIN_BUTTONS_DELETE_','löschen');
define('_WBLANGADMIN_WIN_BUTTONS_EDIT_','bearbeiten');
define('_WBLANGADMIN_WIN_BUTTONS_RENEW_','wiederherstellen');
define('_WBLANGADMIN_WIN_BUTTONS_CALL_','aufrufen');
define('_WBLANGADMIN_WIN_BUTTONS_MODIFY_','ändern');
define('_WBLANGADMIN_WIN_BUTTONS_CREATE_','erzeugen');
define('_WBLANGADMIN_WIN_BUTTONS_NEXT_','weiter');
define('_WBLANGADMIN_WIN_BUTTONS_BACK_','zurück');
define('_WBLANGADMIN_WIN_BUTTONS_FREE_','freischalten');
define('_WBLANGADMIN_WIN_BUTTONS_ADDLANG_','Sprache hinzufügen');
define('_WBLANGADMIN_WIN_BUTTONS_OPENNEWWIN_','neues Fenster öffnen');
define('_WBLANGADMIN_WIN_BUTTONS_MEDIA_','Icon suchen');


// Seite hinzufügen

define('_WBLANGADMIN_WIN_ADDPAGE_HEADLINE_','Neue Seite hinzufügen');
define('_WBLANGADMIN_WIN_ADDPAGE_TXT_1_','Geben Sie einen Seitennamen ein und wählen Sie');
define('_WBLANGADMIN_WIN_ADDPAGE_TXT_2_','ein Layout oder');
define('_WBLANGADMIN_WIN_ADDPAGE_TXT_3_','eine Seite, die kopiert werden soll.');
define('_WBLANGADMIN_WIN_ADDPAGE_TXT_4_','Die neue Seite kann automatisch in einem Menü verlinkt werden.');
define('_WBLANGADMIN_WIN_ADDPAGE_PAGENAME_','Seitenname');
define('_WBLANGADMIN_WIN_ADDPAGE_PAGEVALUE_','neue_seite');
define('_WBLANGADMIN_WIN_ADDPAGE_COPYOF_','als Kopie');
define('_WBLANGADMIN_WIN_ADDPAGE_LAYOUT_','Layout');
define('_WBLANGADMIN_WIN_ADDPAGE_DUPLICAT_','Duplikat von');
define('_WBLANGADMIN_WIN_ADDPAGE_LANGUAGE_','Sprache');
define('_WBLANGADMIN_WIN_ADDPAGE_AUTOLINK_','Seite automatisch verlinken');
define('_WBLANGADMIN_WIN_ADDPAGE_MENUNAME_','Menü');
define('_WBLANGADMIN_WIN_ADDPAGE_CATEGORIE_','Kategorie');
define('_WBLANGADMIN_WIN_ADDPAGE_LINKNAME_','Linkname');
define('_WBLANGADMIN_WIN_ADDPAGE_LINKVALUE_','Neuer Link');
define('_WBLANGADMIN_WIN_ADDPAGE_LINKPOS_','Position');


// Seite löschen

define('_WBLANGADMIN_WIN_DELPAGE_HEADLINE_','Seite löschen');
define('_WBLANGADMIN_WIN_DELPAGE_ATTENTION_','ACHTUNG!');
define('_WBLANGADMIN_WIN_DELPAGE_TXT_','Alle Links stehen als statischer Text in den Dateien. Alle Dateien zu durchsuchen würde das System zu sehr belasten. Deshalb werden nur Links in Menüs automatisch gelöscht. Verlinkungen in Blöcken und Seiten müssen manuell entfernt werden.');
define('_WBLANGADMIN_WIN_DELPAGE_PAGENAME_','Seitenname');
define('_WBLANGADMIN_WIN_DELPAGE_NOUNDO_','Die Löschung kann nicht rückgängig gemacht werden!');


// Seite umbenennen

define('_WBLANGADMIN_WIN_RENAME_HEADLINE_','Seite umbenennen');
define('_WBLANGADMIN_WIN_RENAME_ATTENTION_','ACHTUNG!');
define('_WBLANGADMIN_WIN_RENAME_TXT_','Alle Links stehen als statischer Text in den Dateien. Alle Dateien zu durchsuchen würde das System zu sehr belasten. Deshalb werden beim umbenennen nur Links in Menüs automatisch angepasst. Verlinkungen in Blöcken und Seiten müssen manuell geändert werden.');
define('_WBLANGADMIN_WIN_RENAME_OLDNAME_','aktueller Name');
define('_WBLANGADMIN_WIN_RENAME_NEWNAME_','neuer Seitenname');
define('_WBLANGADMIN_WIN_RENAME_SAVE_NEWEXISTS_','Eine Seite mit dem neuen Namen ist bereits vorhanden');
define('_WBLANGADMIN_WIN_RENAME_SAVE_NONEW_','Sie haben keinen neuen Namen vergeben');
define('_WBLANGADMIN_WIN_RENAME_SAVE_ISSAVEED_','Die Seite wurde umbenannt');
define('_WBLANGADMIN_WIN_RENAME_SAVE_RELOAD_','Es wird zur umbenannten Seite geleitet');


// Backup (Menü/Block bearbeiten)

define('_WBLANGADMIN_WIN_EDIT_BACKUP_','Backup');
define('_WBLANGADMIN_WIN_EDIT_BAKOF_','vom');
define('_WBLANGADMIN_WIN_EDIT_NOBACKUP_','nicht vorhanden');

// Menü bearbeiten

define('_WBLANGADMIN_WIN_EDITMENU_HEADLINE_','Menü bearbeiten');
define('_WBLANGADMIN_WIN_EDITMENU_TXT_','Wählen Sie ein Menü zur Bearbeitung.');
define('_WBLANGADMIN_WIN_EDITMENU_MENU_','Menü');
define('_WBLANGADMIN_WIN_EDITMENU_REMENU_','Das Menü-Backup wurde wiederhergestellt');
define('_WBLANGADMIN_WIN_EDITMENU_NOMENU_','Das gewählte Menü existiert nicht');

// Block bearbeiten

define('_WBLANGADMIN_WIN_EDITBLOCK_HEADLINE_','Block bearbeiten');
define('_WBLANGADMIN_WIN_EDITBLOCK_TXT_','Wählen Sie einen Block zur Bearbeitung.');
define('_WBLANGADMIN_WIN_EDITBLOCK_BLOCK_','Block');
define('_WBLANGADMIN_WIN_EDITBLOCK_REBLOCK_','Das Block-Backup wurde wiederhergestellt');
define('_WBLANGADMIN_WIN_EDITBLOCK_NOBLOCK_','Der gewählte Block existiert nicht');


// Seite wechseln

define('_WBLANGADMIN_WIN_OTHERPAGE_HEADLINE_','Seite aufrufen');
define('_WBLANGADMIN_WIN_OTHERPAGE_TXT_','Wechseln Sie zu einer beliebigen Seite Ihrer Wahl.');
define('_WBLANGADMIN_WIN_OTHERPAGE_PAGENAME_','Seitenname');


// Seitensprache

define('_WBLANGADMIN_WIN_PAGELANG_HEADLINE_','Sprache der Seite ändern');
define('_WBLANGADMIN_WIN_PAGELANG_TXT_','Wählen Sie die Sprache für die aktuelle Seite.');
define('_WBLANGADMIN_WIN_PAGELANG_LANGUAGE_','Sprache');
define('_WBLANGADMIN_WIN_PAGELANG_NOLANGUAGE_','Keine Sprache vorhanden');


// Seitenkategorie

define('_WBLANGADMIN_WIN_PAGECATS_HEADLINE_','Kategorie der Seite ändern');
define('_WBLANGADMIN_WIN_PAGECATS_TXT_','Wählen Sie die Kategorie für die aktuelle Seite.');
define('_WBLANGADMIN_WIN_PAGECATS_CATEGORIE_','Kategorie');
define('_WBLANGADMIN_WIN_PAGECATS_NOCATEGORIES_','Keine Kategorien vorhanden');


// Aktuelle Links highlighten

define('_WBLANGADMIN_WIN_HIGHLITES_HEADLINE_','Farbe des aktuellen Links hervorheben');
define('_WBLANGADMIN_WIN_HIGHLITES_TXT_FILES_','Geben Sie den Namen der CSS-Klasse an und wählen Sie das Menü in welchem der aktuelle Link hervorgehoben werden soll.');
define('_WBLANGADMIN_WIN_HIGHLITES_TXT_FOLDERS_','Geben Sie den Namen der CSS-Klasse an, wählen Sie die Elternkategorie, die hervorgehoben werden soll und in welchem Menü die Hervorhebung angewendet werden soll.');
define('_WBLANGADMIN_WIN_HIGHLITES_FILESTR_','Links hervorheben');
define('_WBLANGADMIN_WIN_HIGHLITES_FOLDERSTR_','Kategorien hervorheben');
define('_WBLANGADMIN_WIN_HIGHLITES_CLASS_','Klasse');
define('_WBLANGADMIN_WIN_HIGHLITES_PARENTSTR_','auch Eltern-Elemente hervorheben');
define('_WBLANGADMIN_WIN_HIGHLITES_PARENTSYES_','Eltern-Elemente werden hervorgehoben');
define('_WBLANGADMIN_WIN_HIGHLITES_PARENTSNO_','Eltern-Elemente werden nicht hervorgehoben');
define('_WBLANGADMIN_WIN_HIGHLITES_CATEGORIE_','Kategorie');
define('_WBLANGADMIN_WIN_HIGHLITES_MENU_','Menü');
define('_WBLANGADMIN_WIN_HIGHLITES_CURRENTSTR_','auch aktuelle Kategorie hervorheben');
define('_WBLANGADMIN_WIN_HIGHLITES_AVAILABLE_','Vorhandene Klassen');
define('_WBLANGADMIN_WIN_HIGHLITES_CURRENTYES_','aktuelle Kategorie wird hervorgehoben');
define('_WBLANGADMIN_WIN_HIGHLITES_CURRENTNO_','aktuelle Kategorie wird nicht hervorgehoben');
define('_WBLANGADMIN_WIN_HIGHLITES_DELETE_','löschen');
define('_WBLANGADMIN_WIN_HIGHLITES_FILE_ISSAVEED_','Die Klasse wurde gespeichert');
define('_WBLANGADMIN_WIN_HIGHLITES_FILE_NOTSAVEED_','Für dieses Menü existiert bereits eine Klasse');
define('_WBLANGADMIN_WIN_HIGHLITES_FILE_ISDELETED_','Die Klasse wurde gelöscht');
define('_WBLANGADMIN_WIN_HIGHLITES_FOLDER_ISSAVEED_','Die Kategorie-Klasse wurde gespeichert');
define('_WBLANGADMIN_WIN_HIGHLITES_FOLDER_NOTSAVEED_','Für diese Kategorie- / Menü-Kombination existiert bereits eine Klasse');
define('_WBLANGADMIN_WIN_HIGHLITES_FOLDER_ISDELETED_','Die Kategorie-Klasse wurde gelöscht');


// Spalten-Elemente

define('_WBLANGADMIN_WIN_COLUMNS_HEADLINE_','Spalten Elemente');
define('_WBLANGADMIN_WIN_COLUMNS_TEXT_','Anzahl der Spalten auswählen, jede Spalte individuell einstellen und anschließend per Click in die Seite einfügen. Mögliche Einfügepunkte werden bei Mouseover markiert.');
define('_WBLANGADMIN_WIN_COLUMNS_LENGTH_','Anzahl der Spalten');
define('_WBLANGADMIN_WIN_COLUMNS_ROWCSS_','Block CSS-Klasse');
define('_WBLANGADMIN_WIN_COLUMNS_NUM_','Spalte');
define('_WBLANGADMIN_WIN_COLUMNS_SINGLE_','Für eine einzelne Spalte sind keine Einstellungen nötig');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_','Ausrichtung');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_TOP_','oben');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_MIDDLE_','mittig');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_BOTTOM_','unten');
define('_WBLANGADMIN_WIN_COLUMNS_ALIGN_FULL_','100% hoch');
define('_WBLANGADMIN_WIN_COLUMNS_WIDTH_','Breite');
define('_WBLANGADMIN_WIN_COLUMNS_COLSMALL_','klein');
define('_WBLANGADMIN_WIN_COLUMNS_COLMEDIUM_','normal');
define('_WBLANGADMIN_WIN_COLUMNS_COLLARGE_','groß');
define('_WBLANGADMIN_WIN_COLUMNS_HIDE_','verstecken');
define('_WBLANGADMIN_WIN_COLUMNS_ORDER_','Order');
define('_WBLANGADMIN_WIN_COLUMNS_EDITOR_','Editor einfügen');
define('_WBLANGADMIN_WIN_COLUMNS_COLCSS_','Spalten CSS-Klasse');
define('_WBLANGADMIN_WIN_COLUMNS_BUTTON_','Spalten übergeben');

define('_WBLANGADMIN_COLUMNS_INSERT_PROMT_','Abstand nach oben in Pixel');
define('_WBLANGADMIN_COLUMNS_INSERT_BEFORE_','Spalten davor einfügen');
define('_WBLANGADMIN_COLUMNS_INSERT_AFTER_','Spalten danach einfügen');

define('_WBLANGADMIN_WIN_DELCOLUMNS_HEADLINE_','Spalten löschen');
define('_WBLANGADMIN_WIN_DELCOLUMNS_TEXT_','Löschbare Elemente sind per Mouseover in der Seite auswählbar.');
define('_WBLANGADMIN_WIN_DELCOLUMNS_BUTTON_','Spalten auswählen');

define('_WBLANGADMIN_COLUMNS_DELETE_TEXT_','Dieses Spalten-Element löschen');


// Zugriffssteuerung

define('_WBLANGADMIN_WIN_ACCESS_HEADLINE_','Seiten sperren, Benutzergruppen und Benutzerkonten');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_HEADLINE_','Festlegen der Registrierungsfelder');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_TXT_','Legen Sie fest welche Daten Sie von Ihren Benutzern zur Identifizierung benötigen. Alle Felder sind Pflichtfelder bei der Registrierung. Der Besucher-Login benötigt nur den Namen und das Passwort des Benutzers. Die Einstellungen können nachträglich nicht mehr verändert werden.');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_REGFIELDS_','Die Registrierungsfelder wurden festgelegt und in der Config gespeichert.');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_DEFAULTGROUP_','Standard');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_ERROR_','Fehlermeldung');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_DBOK_','Die User-Datenbank wurde neu erstellt.');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_DBERROR_','Fehler beim erstellen der Datenbanktabellen.');
define('_WBLANGADMIN_WIN_ACCESS_INSTALL_COMPLETE_','fertigstellen');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_GROUPID_','Group ID');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_GROUPNAME_','Gruppenname');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USER_','Benutzer');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERGROUP_','Benutzergruppe');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERID_','User ID');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_NAME_','Name');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_FIRSTNAME_','Vorname');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_COMPANY_','Unternehmen');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_STREET_','Straße Nr.');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_TOWN_','PLZ Ort');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_PHONE_','Telefon');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_MAILADDRESS_','Mailadresse');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERNAME_','Benutzername');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERPASS_','Passwort');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERMAIL_','eMail');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_USERNAMEMAIL_','Benutzer oder Mail');
define('_WBLANGADMIN_WIN_ACCESS_FIELD_STATUS_','Status');
define('_WBLANGADMIN_WIN_ACCESS_SAVECONF_','festlegen');
define('_WBLANGADMIN_WIN_ACCESS_STAT_ENABLED_','aktiviert');
define('_WBLANGADMIN_WIN_ACCESS_STAT_DISABLED_','gesperrt');
define('_WBLANGADMIN_WIN_ACCESS_STAT_DELETED_','gelöscht');
define('_WBLANGADMIN_WIN_ACCESS_CONFOPENFILE_','Die Konfigurationsdatei konnte nicht geöffnet werden.');
define('_WBLANGADMIN_WIN_ACCESS_CONFNOTSAVED_','Ihre Änderungen konnten nicht gespeichert werden.');
define('_WBLANGADMIN_WIN_ACCESS_CONFSAVED_','Ihre Änderungen wurden gespeichert.');
define('_WBLANGADMIN_WIN_ACCESS_BLOCKEDNOTSAVED_','Die geblockten Seiten konnten nicht aktualisiert werden.');
define('_WBLANGADMIN_WIN_ACCESS_BLOCKEDSAVED_','Die geblockten Seiten wurden aktualisiert.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPEXISTS_','Eine Gruppe mit diesem Namen existiert bereits.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPNOTSAVED_','Die neue Gruppe konnte nicht eingetragen werden.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPSAVED_','Die neue Gruppe wurde eingetragen.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPSETSNOTSAVED_','Die Gruppeneigenschaften konnten nicht geändert werden.');
define('_WBLANGADMIN_WIN_ACCESS_GROUPSETSSAVED_','Die Gruppeneigenschaften wurde geändert.');
define('_WBLANGADMIN_WIN_ACCESS_USERNOPASS_','Sie haben kein Passwort vergeben!');
define('_WBLANGADMIN_WIN_ACCESS_USERNOTSAVED_','Der Benutzer konnte nicht eingetragen werden.');
define('_WBLANGADMIN_WIN_ACCESS_USERSAVED_','Der Benutzer wurde eingetragen.');
define('_WBLANGADMIN_WIN_ACCESS_USERNOTDELETED_','Der Benutzer konnte nicht gelöscht werden.');
define('_WBLANGADMIN_WIN_ACCESS_USERDELETED_','Der Benutzer wurde gelöscht.');
define('_WBLANGADMIN_WIN_ACCESS_USERNOTFREE_','Der Benutzer konnte nicht freigeschaltet werden.');
define('_WBLANGADMIN_WIN_ACCESS_USERFREE_','Der Benutzer wurde freigeschaltet.');
define('_WBLANGADMIN_WIN_ACCESS_USERNOTMODIFIED_','Die Benutzerdaten konnten nicht geändert werden.');
define('_WBLANGADMIN_WIN_ACCESS_USERMODIFIED_','Die Benutzerdaten wurden geändert.');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_LINK_','Sperren');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_LINK_','Gruppen');
define('_WBLANGADMIN_WIN_ACCESS_USERS_LINK_','Benutzer');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_LINK_','Einstellungen');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_PAGES_','Seiten sperren');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_TXT_','Entscheiden Sie welche Ihrer Seiten für normale Besucher gesperrt sein sollen. Gesperrte Seiten können unter dem Punkt Gruppen für eine gewählte Benutzergruppe sichtbar geschaltet werden.');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_BLOCKED_','Für Besucher gesperrte Seiten');
define('_WBLANGADMIN_WIN_ACCESS_BLOCK_FREE_','Für Besucher freie Seiten');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_USERGROUPS_','Benutzergruppen');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_TXT_','Erstellen Sie eine Benutzergruppe und legen Sie fest welche gesperrten Seiten für diese Gruppe sichtbar sein sollen (Button \'bearbeiten\'). Es können auch einzelne Seitenteile im Quelltext mittels der Gruppen-ID nur für diese Gruppe sichtbar geschaltet werden:');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_VISIBLEPART_','_SICHTBARER_TEIL_');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_GROUPNAME_','Gruppen-Name');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_GROUPID_','Gruppen-ID');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_FOR_','Für');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_VISIBLEPAGES_','sichtbare Seiten');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_BLOCKEDPAGES_','gesperrte Seiten');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_VISITORS_','Besucher');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_NEWGROUP_','Neue Gruppe');
define('_WBLANGADMIN_WIN_ACCESS_GROUP_GROUPS_','Gruppen');
define('_WBLANGADMIN_WIN_ACCESS_USERS_ACCOUNT_','Benutzerkonten');
define('_WBLANGADMIN_WIN_ACCESS_USERS_TXT_','Legen Sie Zugangsdaten für Benutzer an oder, wenn die Registrierung aktiviert ist, schalten Sie neu registrierte Benutzer frei und / oder ändern vorhandene Benutzerdaten.');
define('_WBLANGADMIN_WIN_ACCESS_USERS_ADDUSER_','Benutzer anlegen');
define('_WBLANGADMIN_WIN_ACCESS_USERS_FREENEWREGS_','Neue Benutzer freischalten');
define('_WBLANGADMIN_WIN_ACCESS_USERS_EDITUSER_','Benutzer bearbeiten');
define('_WBLANGADMIN_WIN_ACCESS_USERS_SENDMAIL_','Mail schicken');
define('_WBLANGADMIN_WIN_ACCESS_USERS_SENDDATA_','Logindaten an Benutzer');
define('_WBLANGADMIN_WIN_ACCESS_USERS_SENDFREE_','Profil freigeschaltet');
define('_WBLANGADMIN_WIN_ACCESS_USERS_LANGUAGE_','Sprache');
define('_WBLANGADMIN_WIN_ACCESS_USERS_NOGROUP_','Es gibt noch keine Gruppe für neue Benutzer.');
define('_WBLANGADMIN_WIN_ACCESS_USERS_NEWREGS_','Neuregistrierung');
define('_WBLANGADMIN_WIN_ACCESS_USERS_FREEISAUTO_','Die Freischaltung ist auf automatisch eingestellt.');
define('_WBLANGADMIN_WIN_ACCESS_USERS_NONEWREGS_','Es gibt keine Neuregistrierungen.');
define('_WBLANGADMIN_WIN_ACCESS_USERS_REACCOUNT_','Dieses Benutzerkonto kann reaktiviert werden!');
define('_WBLANGADMIN_WIN_ACCESS_USERS_DELETE_','Löschen');
define('_WBLANGADMIN_WIN_ACCESS_USERS_USERNOTFOUND_','Benutzer wurde nicht gefunden!');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_','Grundeinstellungen');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_TXT_','Legen Sie die Funktionsweise der Benutzerregistrierung fest. Die Admin-Mailadresse wird als Absender für Registrierungsbestätigungen benötigt.');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGISTRATION_','Registrierung');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGON_','aktiv');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_LOGINLINK_','Menülink Login');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_LOGINSHOW_','anzeigen');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGBY_','Freischaltung durch');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGADMIN_','Admin');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGAUTO_','automatisch');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_ADMINMAIL_','Mail an Admin');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_SENDADMINMAIL_','verschicken');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_ADMINMAILADDRESS_','Admin-Mailadresse');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_USERMAIL_','Mail an Benutzer');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_REGTOUSER_','Aktivierung verschicken');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_DELUSER_','Benutzer löschen');
define('_WBLANGADMIN_WIN_ACCESS_SETTINGS_DELFROMDB_','aus DB entfernen');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGISTRATION_','Sollen sich Besucher auf der Homepage registrieren können?');
define('_WBLANGADMIN_WIN_ACCESS_HELP_LOGINLINK_','Wenn der Loginblock benutzt wird, kann hier der Login-Link im Menü der Userspage deaktiviert werden.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGBY_','Soll der Administrator neue Benutzer freischalten oder soll die Freischaltung automatisch sein?');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGBYAUTO_','Automatisch kann nur gewählt werden, wenn mindestens eine Benutzergruppe existiert.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGGROUP_','Wählen Sie die Benutzergruppe, der ein neu registrierter Benutzer automatisch angehören soll.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_MAILTOADMIN_','Soll der Administrator eine Mail bei einer neuen Registrierung erhalten? Sollte aktiviert sein, wenn die Freischaltung auf Admin eingestellt ist.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_ADMINMAIL_','Tragen Sie die Absender-Mailadresse des Administrators für die Registrierungsmails ein.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_REGTOUSER_','Soll ein neuer Benutzer sein Profil durch eine Bestätigungsmail aktivieren müssen? Diese Maßnahme kann helfen Spam-Anmeldungen zu verringern.');
define('_WBLANGADMIN_WIN_ACCESS_HELP_DELFROMDB_','Soll ein Benutzer beim löschen aus der Datenbank entfernt werden? Wenn das Benutzerkonto erhalten bleibt, wird der Status auf gelöscht gesetzt und das Konto kann reaktiviert werden.');


// Styles

define('_WBLANGADMIN_WIN_EDITSTYLES_HEADLINE_','Styles bearbeiten');
define('_WBLANGADMIN_WIN_EDITSTYLES_FILENAME_','Dateiname');


// Kategorien

define('_WBLANGADMIN_WIN_CATEGORIES_HEADLINE_','Kategorien');
define('_WBLANGADMIN_WIN_CATEGORIES_TXT_','Kategorien sind virtuell und werden nur in der URL angezeigt. Erlaubt sind die Zeichen a bis z, 0 bis 9, der Unterstrich (_) und der Slash (/) für Unterkategorien.');
define('_WBLANGADMIN_WIN_CATEGORIES_NEWCAT_','Neue Kategorie anlegen');
define('_WBLANGADMIN_WIN_CATEGORIES_DELCAT_','Kategorie löschen');
define('_WBLANGADMIN_WIN_CATEGORIES_DELETE_','Auswahl wird gelöscht');
define('_WBLANGADMIN_WIN_CATEGORIES_BUTTONDEL_','löschen');
define('_WBLANGADMIN_WIN_CATEGORIES_BUTTONNEW_','anlegen');
define('_WBLANGADMIN_WIN_CATEGORIES_LANG_','Sprache');
define('_WBLANGADMIN_WIN_CATEGORIES_NAME_','Kategorie / Verzeichnispfad');
define('_WBLANGADMIN_WIN_CATEGORIES_SAVED_','Die Kategorien wurden aktualisiert.');


// Formulare

define('_WBLANGADMIN_WIN_FORMS_HEADLINE_','Empfängerdaten für eMail-Formulare');
define('_WBLANGADMIN_WIN_FORMS_TXT_','Zum Versand von eMail-Formularen können hier neue Empfänger angelegt und vorhandene Empfänger bearbeitet oder gelöscht werden.');
define('_WBLANGADMIN_WIN_FORMS_INPUT_RECEIVER_','Name des Datensatzes');
define('_WBLANGADMIN_WIN_FORMS_INPUT_MAILADDRESS_','eMail-Adresse');
define('_WBLANGADMIN_WIN_FORMS_INPUT_SHIPPER_','Absendername');
define('_WBLANGADMIN_WIN_FORMS_INPUT_SUBJECT_','Empfängerbetreff');
define('_WBLANGADMIN_WIN_FORMS_INPUT_CONFIRM_','Bestätigung');
define('_WBLANGADMIN_WIN_FORMS_INPUT_CONFIRMSUB_','Bestätigungsbetreff');
define('_WBLANGADMIN_WIN_FORMS_INPUT_SENTALERT_','Gesendet Meldung');
define('_WBLANGADMIN_WIN_FORMS_FIELDEMPTY_','Es wurden nicht alle Felder ausgefüllt.');
define('_WBLANGADMIN_WIN_FORMS_WRONGMAILADDRESS_','Diese eMail-Adresse kann nicht genutzt werden!');
define('_WBLANGADMIN_WIN_FORMS_ISSAVED_','Die Daten des Empfängers wurden gespeichert.');
define('_WBLANGADMIN_WIN_FORMS_ISDELETED_','Der Empfänger wurde gelöscht.');
define('_WBLANGADMIN_WIN_FORMS_ADDRECEIVER_','Neuen Empfänger anlegen');
define('_WBLANGADMIN_WIN_FORMS_EDITRECEIVER_','Empfänger bearbeiten');
define('_WBLANGADMIN_WIN_FORMS_NEWRECEIVER_','Neuen Empfänger');


// Vorlagen

define('_WBLANGADMIN_WIN_PATTERN_FUNCS_','Editor Vorlagen');
define('_WBLANGADMIN_WIN_PATTERN_NEW_','Neue Vorlage erstellen');
define('_WBLANGADMIN_WIN_PATTERN_EDIT_','Vorlage bearbeiten');
define('_WBLANGADMIN_WIN_PATTERN_DELETE_','Vorlage löschen');
define('_WBLANGADMIN_WIN_PATTERN_FILENAME_','Dateiname');
define('_WBLANGADMIN_WIN_PATTERN_DUPLICAT_','Duplikat von');
define('_WBLANGADMIN_WIN_PATTERN_VALUE_','pattern');
define('_WBLANGADMIN_WIN_PATTERN_FILE_','Datei');


// Vorlage bearbeiten

define('_WBLANGADMIN_WIN_PATTERN_EDITTEMP_','Editor Vorlage bearbeiten');
define('_WBLANGADMIN_WIN_PATTERN_EDITTITLE_','Titel');
define('_WBLANGADMIN_WIN_PATTERN_EDITIMAGE_','Icon');
define('_WBLANGADMIN_WIN_PATTERN_EDITDESC_','Beschreibung');
define('_WBLANGADMIN_WIN_PATTERN_EDITSOURCE_','Vorlage Quellcode');


// Spracheinstellungen

define('_WBLANGADMIN_WIN_LANGUAGE_HEADLINE_','Spracheinstellungen');
define('_WBLANGADMIN_WIN_LANGUAGE_FIELDEMPTY_','Es wurden nicht alle Felder ausgefüllt.');
define('_WBLANGADMIN_WIN_LANGUAGE_ISSAVED_','Die Spracheinstellungen wurden aktualisiert.');
define('_WBLANGADMIN_WIN_LANGUAGE_STARTPAGES_','Die Startseiten wurden aktualisiert.');
define('_WBLANGADMIN_WIN_LANGUAGE_URLFOLDER_','Die Einstellung wurde aktualisiert.');
define('_WBLANGADMIN_WIN_LANGUAGE_DELETETXT_SINGULAR_','Beim löschen der Sprache werden alle Seitenzuordnungen dieser Sprache ebenfalls gelöscht.');
define('_WBLANGADMIN_WIN_LANGUAGE_DELETETXT_PLURAL_','Beim löschen von Sprachen werden alle Seitenzuordnungen dieser Sprachen ebenfalls gelöscht.');
define('_WBLANGADMIN_WIN_LANGUAGE_INSTALL_','Sprache installieren');
define('_WBLANGADMIN_WIN_LANGUAGE_SETSTARTS_','Startseiten festlegen');
define('_WBLANGADMIN_WIN_LANGUAGE_ORDER_','Sprachen werden in der Reihenfolge angezeigt wie sie hier angelegt sind.');
define('_WBLANGADMIN_WIN_LANGUAGE_LANG_','Sprache');
define('_WBLANGADMIN_WIN_LANGUAGE_NOLANG_','keine Sprache verfügbar');
define('_WBLANGADMIN_WIN_LANGUAGE_EMPTY_','leer');
define('_WBLANGADMIN_WIN_LANGUAGE_NOTINLANG_','nicht zugeordnet');
define('_WBLANGADMIN_WIN_LANGUAGE_SHORT_','Kürzel');
define('_WBLANGADMIN_WIN_LANGUAGE_DESCRIPT_','Bezeichnung');
define('_WBLANGADMIN_WIN_LANGUAGE_DELETE_','Löschen');
define('_WBLANGADMIN_WIN_LANGUAGE_POSITION_','Position');
define('_WBLANGADMIN_WIN_LANGUAGE_STARTPAGE_','Startseite');
define('_WBLANGADMIN_WIN_LANGUAGE_POSUP_','hoch');
define('_WBLANGADMIN_WIN_LANGUAGE_POSDOWN_','runter');
define('_WBLANGADMIN_WIN_LANGUAGE_STARTBYLANG_','Legen Sie fest welche Seite die Startseite einer Sprache sein soll.');


// Grundeinstellungen

define('_WBLANGADMIN_WIN_SETTINGS_HEADLINE_','Grundeinstellungen');
define('_WBLANGADMIN_WIN_SETTINGS_TEXT_','Ändern der Zugangsdaten des Administrators sowie weiterer Einstellungen');
define('_WBLANGADMIN_WIN_SETTINGS_USERNAME_','Benutzername');
define('_WBLANGADMIN_WIN_SETTINGS_NEWPASS_','Neues Passwort');
define('_WBLANGADMIN_WIN_SETTINGS_REPEATPASS_','Passwort wiederholen');
define('_WBLANGADMIN_WIN_SETTINGS_ADMINLANG_','Sprache Administrator');
define('_WBLANGADMIN_WIN_SETTINGS_HOMEPAGE_','Startseite');
define('_WBLANGADMIN_WIN_SETTINGS_IMGSCAL_','Voreinstellung Bildskalierung nach Upload');
define('_WBLANGADMIN_WIN_SETTINGS_SMALLIMG_','Bild Seite');
define('_WBLANGADMIN_WIN_SETTINGS_BIGIMG_','Bild Lightbox');
define('_WBLANGADMIN_WIN_SETTINGS_IMGWIDTH_','breit');
define('_WBLANGADMIN_WIN_SETTINGS_IMGHEIGHT_','hoch');


// erweiterte Einstellungen

define('_WBLANGADMIN_WIN_ADVANCED_HEADLINE_','Erweiterte Administratorfunktionen');
define('_WBLANGADMIN_WIN_ADVANCED_EDITPAGE_','Aktuelle Seite bearbeiten');
define('_WBLANGADMIN_WIN_ADVANCED_EDITPAGE_TXT_','Klicken Sie auf bearbeiten, um Änderungen am Layout der aktuellen Seite vorzunehmen.');
define('_WBLANGADMIN_WIN_ADVANCED_ERRORPAGE_','Eigene Fehlerseite festlegen');
define('_WBLANGADMIN_WIN_ADVANCED_ERRORPAGE_TXT_','Für die Fehlermeldung muß %s in der Fehlerseite stehen.');
define('_WBLANGADMIN_WIN_ADVANCED_ERRORPAGE_NAME_','Fehlerseite');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTFUNCS_','Layoutfunktionen');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTNEW_','Neues Layout erstellen');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTEDIT_','Layout bearbeiten');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTDELETE_','Layout löschen');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTNAME_','Layoutname');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTVALUE_','neues_layout');
define('_WBLANGADMIN_WIN_ADVANCED_LAYOUTDUPLICAT_','Duplikat von');


define('_WBLANGADMIN_WIN_ADVANCED_MENUFUNCS_','Menüfunktionen');
define('_WBLANGADMIN_WIN_ADVANCED_MENUNEW_','Neues Menü erzeugen');
define('_WBLANGADMIN_WIN_ADVANCED_MENUDELETE_','Menü löschen');
define('_WBLANGADMIN_WIN_ADVANCED_MENUNAME_','Menüname');
define('_WBLANGADMIN_WIN_ADVANCED_MENUVALUE_','neues_menu');
define('_WBLANGADMIN_WIN_ADVANCED_MENUDUPLICAT_','Duplikat von');

define('_WBLANGADMIN_WIN_ADVANCED_BLOCKFUNCS_','Blockfunktionen');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKNEW_','Neuen Block erzeugen');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKDELETE_','Block löschen');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKNAME_','Blockname');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKVALUE_','neuer_block');
define('_WBLANGADMIN_WIN_ADVANCED_BLOCKDUPLICAT_','Duplikat von');
define('_WBLANGADMIN_WIN_ADVANCED_PHPINFO_','Informationen über den Server anzeigen (PHP Info)');
define('_WBLANGADMIN_WIN_ADMIN_MODULES_','Administration Module');










