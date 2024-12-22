<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


if(preg_match('#/settings/globalvars.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

/**********************************************************
     Weitere nicht administrierbare Grundeinstellungen
**********************************************************/


// Admin - zusätzlicher Zugang für den Webmaster
$webutler_config['admin_name'] = "";
$webutler_config['admin_pass'] = ""; // Passwort md5(salt_key1.Passwort.salt_key2) verschlüsseln
$webutler_config['admin_lang'] = ""; // Sprache

// A bis Z, a bis z, 0 bis 9 und #+-_*@%&=!?
$webutler_config['salt_key1'] = "rDZ9Wt9a#@&l6V";
$webutler_config['salt_key2'] = "93R@ZeaP+wv!6V";

// Login bei 5 Fehlversuchen für XX Minuten sperren
$webutler_config['logattemptmin'] = "5";

// Suchmaschinenfreundliche URLs
$webutler_config['modrewrite'] = "0";
// 0 = deaktiviert
// 1 = aktiviert

// Dateiendung der SEO URLs
$webutler_config['urlendung'] = ".phtml"; // Punkt mit Zeichenfolge (nur Kleinbuchstaben) oder leer für keine Endung

// Anzahl der URL GET-Variablen - muß in der .htaccess entsprechend angepasst werden
$webutler_config['urlgetvars'] = "7";

// Standardsprache - folgende Sprache laden, wenn keine andere festgestellt wird
$webutler_config['defaultlang'] = "en";

// Verlinkungen zu Benutzer- oder stillgelegten Seiten
$webutler_config['offline_links'] = "2";
// 0 = nichts ändern
// 1 = Verlinkung (href) entfernen aber Linktext anzeigen
// 2 = Link komplett entfernen (nur in Menüs; im Fliesstext bleibt das Wort erhalten = wie bei 1)

// Wieviele 'Seitenänderung rückgängig machen'-Schritte sollen zur Verfügung stehen
$webutler_config['schritte_zurueck'] = "3"; // es sollten nicht zuviele eingestellt werden

// Fenster "Erweiterte Administratorfunktionen" nur für den Webmaster anzeigen
$webutler_config['admin_erweitert'] = "2";
// 1 = nur Webmaster
// 2 = immer anzeigen

// Anzeige "Aktuelle Seite bearbeiten"
$webutler_config['fullpageedit'] = "2";
// 1 = Im Adminmenü
// 2 = Im Admin-Fenster "Erweiterte Einstellungen"

// Spalten einfügen anbieten
$webutler_config['insertcolumns'] = "1";
// 0 = deaktiviert: Spalten einfügen nicht anzeigen
// 1 = aktiviert: Spalten einfügen anzeigen
// 2 = aktiviert: Spalten einfügen nur im Editor anzeigen

// Spalten Einfügepunkte
$webutler_config['insertpoints'] = "2";
// 1 = Spalten auch bei Blöcken oder Menüs einfügen
// 2 = Spalten nicht bei Blöcken oder Menüs einfügen

// Spalten Abstand nach oben
$webutler_config['insertmargin'] = "1";
// 0 = deaktiviert: Margin-Top Eingabe nicht anzeigen
// 1 = aktiviert: Margin-Top Eingabe anzeigen

// Kategorien anbieten
$webutler_config['categories'] = "0";
// 0 = deaktiviert: Kategorien nicht anzeigen
// 1 = aktiviert: Kategorien anzeigen

// Das Formular Modul benutzen
$webutler_config['forms_modul'] = "1";
// 0 = deaktiviert: schaltet die Empfänger-Administration aus
// 1 = aktiviert: Empfänger anlegen und im Editor auswählen

// Mehrsprachigkeit (Seiten verschiedenen Sprachen zuordnen)
$webutler_config['languages'] = "0";
// 0 = nicht aktiviert
// 1 = aktiviert

// Sprache als Verzeichnis in der URL anzeigen
$webutler_config['langfolder'] = "1";
// 0 = nicht aktiviert
// 1 = aktiviert

// "Neue Sprachen hinzufügen" im Adminmenü nur für den Webmaster anzeigen
$webutler_config['setnewlang'] = "2";
// 1 = nur Webmaster
// 2 = immer anzeigen

// Zugriffsrechte-Steuerung im Adminmenü anzeigen (Benutzer und Benutzergruppen anlegen)
$webutler_config['userlogs'] = "0";
// 0 = nicht anzeigen
// 1 = anzeigen 

// Soll über der Suchergebnisliste ein Suchformular angezeigt werden?
$webutler_config['searchshowinput'] = 1;
// 0 = nicht anzeigen
// 1 = anzeigen

// Wieviele Ergebnisse sollen pro Seite angezeigt werden?
$webutler_config['searchlistitems'] = 10;

// Suchergebnis auf wieviele Zeichen kürzen?
$webutler_config['searchresultlen'] = 180;

// Den Modulemaker im Adminmenü anzeigen
$webutler_config['makemod'] = "1";
// 0 = nicht anzeigen
// 1 = anzeigen 

// Modul-Administrationsbereiche anzeigen (Linkpflege in der Datei settings/modulebox.php)
$webutler_config['modsonlog'] = "2";
// 0 = überhaupt nicht anzeigen - z.B. wenn es keine Module gibt
// 1 = nur unter dem Login anzeigen 
// 2 = nur im Administrationsmenü anzeigen
// 3 = unter dem Login UND im Administrationsmenü anzeigen

// Seiten hinzufügen, umbenennen und löschen
$webutler_config['adminnewpage'] = "1"; // hinzufügen
$webutler_config['adminpagename'] = "1"; // umbenennen
$webutler_config['admindelpage'] = "1"; // löschen
// 0 = deaktiviert
// 1 = aktiviert 


// Bearbeitungsfunktion für Vorlagen
$webutler_config['adminpattern'] = "1";


// Bearbeitungsfunktionen für Layouts, Menüs und Blöcke im Fenster "Erweiterte Administratorfunktionen"
$webutler_config['adminlayouts'] = "1"; // Layouts
$webutler_config['adminmenus'] = "1"; // Menüs
$webutler_config['adminblocks'] = "1"; // Blöcke
// 0 = deaktiviert
// 1 = aktiviert 

// Mediabrowser-Popup: Breite,Höhe
$webutler_config['mediabrowser_wh'] = array('60%','70%');

// Bildeditor-Popup: Breite,Höhe
$webutler_config['imageeditor_wh'] = array('70%','80%');

// Bildkomprimierung
$webutler_config['jpg_quality'] = "75";
$webutler_config['png_compress'] = "9";

// CSS Dateien, die nicht im Editor geladen werden sollen - mehrere kommagetrennt
$webutler_config['cssnotineditor'] = "";

// Editor CI Colors: max. 8 Webfarben, kommagetrennt ohne Raute
$webutler_config['editorcicolors'] = "";

// Webfarbe = Farbe der Bedienelente der Flashplayer
$webutler_config['playercolor'] = "ff0000";

// Icon für PHP- und Javascript-Code im Editor zeigen
$webutler_config['codeicon'] = "1";
// 0 = deaktiviert
// 1 = aktiviert 

// Für text-align und indent CSS-Klassen statt style-Attribut nutzen
$webutler_config['ckecssclasses'] = "1";
// 0 = deaktiviert
// 1 = aktiviert 

// Embed Provider zum laden eingebetteter Medien
$webutler_config['ckeembed'] = "//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}";

// Webfarbe = Hintergrundfarbe Menüeditor
$webutler_config['ckemenubg'] = "ffffff";

// Webfarbe = Hintergrundfarbe Blockeditor
$webutler_config['ckeblockbg'] = "ffffff";

// Webfarbe = Hintergrundfarbe Editor Comboboxen
$webutler_config['ckecombobg'] = "ffffff";

/**********************************************************
                 Grundeinstellungen Ende
**********************************************************/


$webutler_htmlsource['page_header'] = '<!DOCTYPE html>'."\n".
	'<html lang="'.(isset($_SESSION['language']) ? $_SESSION['language'] : $webutler_config['defaultlang']).'">'."\n".
	'<head>'."\n".
	'<meta charset="utf-8" />'."\n".
	'<meta http-equiv="imagetoolbar" content="no" />'."\n".
	'<meta name="robots" content="noindex,nofollow" />'."\n";

$webutler_htmlsource['close_page_header'] = "</head>\n<body>\n";

$webutler_htmlsource['page_footer'] = "</body>\n</html>\n";


