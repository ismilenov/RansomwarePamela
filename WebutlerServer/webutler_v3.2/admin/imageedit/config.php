<?PHP
/*
 Image Manager configuration file.
 @author $Author: Wei Zhuo $
 @author $Author: Paul Moers <mail@saulmade.nl> $ - watermarking and replace code + several small enhancements <http://fckplugins.saulmade.nl>
 @version $Id: config.inc.php 27 2004-04-01 08:31:57Z Wei Zhuo $
 @package ImageManager

 File system path to the directory you want to manage the images
 for multiple user systems, set it dynamically.

 NOTE: This directory requires write access by PHP. That is, 
       PHP must be able to create files in this directory.
	   Able to create directories is nice, but not necessary.

 The URL to the above path, the web browser needs to be able to see it.
 It can be protected via .htaccess on apache or directory permissions on IIS,
 check you web server documentation for futher information on directory protection
 If this directory needs to be publicly accessiable, remove scripting capabilities
 for this directory (i.e. disable PHP, Perl, CGI). We only want to store assets
 in this directory and its subdirectories.
*/

/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/


$IMConfig['base_media_url'] = $webutlerimgedit->config['homepage'].'/content/media/';
$IMConfig['base_url'] = $webutlerimgedit->config['homepage'].'/content/media/image/';
$IMConfig['base_tmp_url'] = $webutlerimgedit->config['homepage'].'/content/media/temp/';
$IMConfig['base_media_dir'] = $webutlerimgedit->config['server_path'].'/content/media/';
$IMConfig['base_dir'] = $webutlerimgedit->config['server_path'].'/content/media/image/';
$IMConfig['base_tmp_dir'] = $webutlerimgedit->config['server_path'].'/content/media/temp/';
$IMConfig['server_name'] = $_SERVER['SERVER_NAME'];


/*
  Possible values: true, false

  TRUE - If PHP on the web server is in safe mode, set this to true.
         SAFE MODE restrictions: directory creation will not be possible,
		 only the GD library can be used, other libraries require
		 Safe Mode to be off.

  FALSE - Set to false if PHP on the web server is not in safe mode.
*/
$IMConfig['safe_mode'] = false;

/* 
 Possible values: 'GD' or 'IM'

 The image manipulation library to use, either GD or ImageMagick.
 If you have safe mode ON, or don't have the binaries to other packages, 
 your choice is 'GD' only. Other packages require Safe Mode to be off.
*/
// GDLib oder iMagick für die Bildbearbeitung
define('IMAGE_CLASS', 'gdlib');
// gdlib = PHP Grafikbibliothek
// imagick = ImageMagick (wenn installiert) für bessere Bildqualität


/*
 After defining which library to use, if it is NetPBM or IM, you need to
 specify where the binary for the selected library are. And of course
 your server and PHP must be able to execute them (i.e. safe mode is OFF).
 GD does not require the following definition.
*/
//define('IMAGE_TRANSFORM_LIB_PATH', '/user/lib/ImageMagick-6.0.6/');
define('IMAGE_TRANSFORM_LIB_PATH', '');


/*
  Image Editor temporary filename prefix.
*/
$IMConfig['tmp_prefix'] = '.edit_';



