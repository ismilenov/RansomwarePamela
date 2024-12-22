<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/settings/baseconfig.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');


// User - Zugang für den Kunden
$webutler_config['user_name'] = "admin";
$webutler_config['user_pass'] = "8cafa4e8f8b123044158a1ac9d9e4724";

// Sprache der Administration
$webutler_config['user_lang'] = "en";

// Serverpfad "/home/yoursite/htdocs"
$webutler_config['server_path'] = "/var/www/webutler_v3.2";

// Homepageadresse z.B. "http://www.yoursite.de"
$webutler_config['homepage'] = "http://192.168.1.11/webutler_v3.2";


// Startseite - Anzeige bei Aufruf der Index-Seite
$webutler_config['startseite'] = "start";

// Eigene Fehlerseite
$webutler_config['ownerrorpage'] = "error";

// Voreinstellung Skalierung Seitenbild (in Pixel)
$webutler_config['imgsmallsize'] = array('600', '450'); // (width, height)

// Voreinstellung Skalierung Lightboxbild (in Pixel)
$webutler_config['imgboxsize'] = array('1200', '900'); // (width, height)

// Schreibrechte für Verzeichnisse und Dateien
$webutler_config['chmod'] = array(0777, 0777); // (folders, files)


/***********************************************************

    Weitere nicht administrierbare Einstellungen sind
    in der Datei /settings/globalvars.php möglich.

***********************************************************/


