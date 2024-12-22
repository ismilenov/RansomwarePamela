<?PHP
/**
 * Image Editor. Editing tools, crop, rotate, scale and save.
 * @author Wei Zhuo
 * @author Paul Moers <mail@saulmade.nl> - watermarking and replace code + several small enhancements <http://fckplugins.saulmade.nl>
 * @version $Id: editorclass.php,v 1.3 2006/12/20 18:34:11 thierrybo Exp $
 * @package ImageManager
 *
 * Handles the basic image editing capbabilities.
 * @author Wei Zhuo
 * @version $Id: editorclass.php,v 1.3 2006/12/20 18:34:11 thierrybo Exp $
 * @package ImageManager
 * @subpackage Editor
 */

/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

class ImageEditor extends Image_Transform
{
	// ImageManager instance.
	var $manager;

	// user based on IP address
	var $_uid;

	// tmp file storage time.
	var $lapse_time = 900; //15 mins

	var $filesaved = 0;
	
	var $imgarraycount = 0;
	
	var $watermarkimgarray = array();
    
    var $IMConfig;
    
    var $wbconfig;
    
    
	function filterdateiname($variableupload) {
    	$extension = strtolower(substr($variableupload, strrpos($variableupload, '.') + 1));
    	$filename = substr($variableupload, 0, strrpos($variableupload, '.'));
        
    	$filename = str_replace(' ', '_', $filename);
    	$filename = strtolower($filename);
    	$filename = preg_replace('/[^a-z0-9_]/', '', $filename);
        
    	return $filename.'.'.$extension;
    }
    
	function watermarks($imageInfo)
	{
		$verzeichnis = $this->wbconfig['server_path'].'/content/media/image/watermarks/';
		if(is_dir($verzeichnis."/")) {
    		$handle = opendir($verzeichnis);
    		while(false !== ($datei = readdir($handle)))
    		{ 
    			if($datei != "." && $datei != "..")
    			{
    				if(!is_dir($verzeichnis.$datei."/"))
    				{
    					$dateityp = @GetImageSize($verzeichnis.$datei);
    					if(($dateityp[2] == 1 || $dateityp[2] == 2 || $dateityp[2] == 3) && $dateityp[0] < $imageInfo['width'] && $dateityp[1] < $imageInfo['height'])
    					{
    						$this->watermarkimgarray[$this->imgarraycount][0] = '/content/media/image/watermarks/'.$datei;
    						$this->watermarkimgarray[$this->imgarraycount][1] = $dateityp[0];
    						$this->watermarkimgarray[$this->imgarraycount][2] = $dateityp[1];
    						
                            $this->imgarraycount = $this->imgarraycount + 1;
    					}
    				}
    			}
    		}
    		closedir($handle);
		}
    }
    
	function watermarkimgs($imageInfo)
	{
        $this->watermarks($imageInfo);
        $watermarkimgs = $this->watermarkimgarray;

		$watermarkarrays[] = "watermarkBox.options[0] = new Option('   ', 'blank');\n".
		     "watermarkBox.options[0].setAttribute('fullPath', '/admin/system/images/blank.gif');\n".
		     "watermarkBox.options[0].setAttribute('x', 1);\n".
		     "watermarkBox.options[0].setAttribute('y', 1);\n".
		     "blankPreload = new Image(10, 10);\n".
             "blankPreload.src = '".$this->wbconfig['homepage']."/admin/system/images/blank.gif';\n";
             
        $count = 1;
        foreach($watermarkimgs as $watermarkimg)
        {
    		$fileid = preg_replace("/[^a-z0-9]/", "", strtolower($watermarkimg[0]));
    		$fileid = strtolower($fileid);
    		$fileid = str_replace("contentmediaimage", "", $fileid);
            
            $watermarkarrays[] = "watermarkBox.options[".$count."] = new Option('".basename($watermarkimg[0])."', '".$fileid."');\n".
    		     "watermarkBox.options[".$count."].setAttribute('fullPath', '".$watermarkimg[0]."');\n".
    		     "watermarkBox.options[".$count."].setAttribute('x', ".$watermarkimg[1].");\n".
    		     "watermarkBox.options[".$count."].setAttribute('y', ".$watermarkimg[2].");\n".
    		     $fileid."Preload = new Image(10, 10);\n".
                 $fileid."Preload.src = '".$this->wbconfig['homepage'].$watermarkimg[0]."';\n";
        
            $count = $count + 1;
        }
		return $watermarkarrays;
	}

	/**
	 * Create a new ImageEditor instance. Editing requires a 
	 * tmp file, which is saved in the current directory where the
	 * image is edited. The tmp file is assigned by md5 hash of the
	 * user IP address. This hashed is used as an ID for cleaning up
	 * the tmp files. In addition, any tmp files older than the
	 * the specified period will be deleted.
	 * @param ImageManager $manager the image manager, we need this
	 * for some file and path handling functions.
	function ImageEditor($manager) 
	{
		$this->manager = $manager;
		$this->_uid = md5($_SERVER['REMOTE_ADDR']);
	}
	 */
	function ImageEditor() 
	{
		$this->_uid = md5($_SERVER['REMOTE_ADDR']);
	}
	
	/**
	 * Did we save a file?
	 * @return int 1 if the file was saved sucessfully, 
	 * 0 no save operation, -1 file save error.
	 */
	function isFileSaved() 
	{
		Return $this->filesaved;
	}
	
	function FileNewName() 
	{
		Return $this->echofilename;
	}
    
    function generateautofilename($path, $file)
    {
		$name = substr($file, 0, strrpos($file, '.'));
		$ext = substr($file, strrpos($file, '.'));
		
		$count = 0;
        $stop = false;
        while(!$stop) {
			$count++;
			$nameneu = $name.'_'.$count.$ext;
			if(!file_exists($path.'/'.$nameneu))
				$stop = true;
		}
		
		return $nameneu;
    }


	// Resize the Startfile
	
	function CopyStartFile($oldImagefile, $newImagefile)
	{
		copy($oldImagefile, $newImagefile);
		$imagefile = $newImagefile;
		$format = GetImageSize($imagefile);
		$bildtyp = $format[2];
		
		list($width_orig, $height_orig) = getimagesize($imagefile);
		
		if ($bildtyp == 1) // gif
		{
			$image = imagecreatefromgif($imagefile);
			$image_p = imagecreate($width_orig, $height_orig);
			$black = imagecolorallocate($image_p, 0x00, 0x00, 0x00);
			$trans = imagecolortransparent($image_p, $black);
			imagefill($image_p, 0, 0, $trans);
			imagecopyresized($image_p, $image, 0, 0, 0, 0, $width_orig, $height_orig, $width_orig, $height_orig);
		}
		elseif ($bildtyp == 2) // jpg
		{
			$image = imagecreatefromjpeg($imagefile);
			$image_p = imagecreatetruecolor($width_orig, $height_orig);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width_orig, $height_orig, $width_orig, $height_orig);
		}
		elseif ($bildtyp == 3) // png
		{
			$image = imagecreatefrompng($imagefile);
			$image_p = imagecreatetruecolor($width_orig, $height_orig);
			if(ord(file_get_contents($imagefile, NULL, NULL, 25, 1)) == 6) {
				imagecolortransparent($image_p, imagecolorallocatealpha($image_p, 0, 0, 0, 127));
				imagealphablending($image_p, false);
				imagesavealpha($image_p, true);
			}
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width_orig, $height_orig, $width_orig, $height_orig);
		}
		imagepng($image_p, $imagefile);
	}
	

	/**
	 * Process the image, if not action, just display the image.
	 * @return array with image information, empty array if not an image.
	 * <code>array('src'=>'url of the image', 'dimensions'=>'width="xx" height="yy"',
	 * 'file'=>'image file, relative', 'fullpath'=>'full path to the image');</code>
	 */
	function processImage()
	{
		if(isset($_GET['img']))
		{
			$relative = basename($_GET['img']);
		}
		else
		{
			Return array();
		}
		
		$imgURL = $this->wbconfig['homepage']."/content/media/temp/".$relative;
		$fullpath = $this->wbconfig['server_path']."/content/media/temp/".$relative;
		
		$imgInfo = @getImageSize($fullpath);
		if(!is_array($imgInfo))
			Return array();

		$action = $this->getAction();

		if(!is_null($action))
		{
			$image = $this->processAction($action, $relative, $fullpath);
		}
		else
		{
			$image['src'] = $imgURL;
			$image['dimensions'] = $imgInfo[3];
			$image['width'] = $imgInfo[0];
			$image['height'] = $imgInfo[1];
			$image['file'] = $relative;
			$image['fullpath'] = $fullpath;
		}

		Return $image;
	}


	/**
	 * Process the actions, crop, scale(resize), rotate, flip, and save.
	 * When ever an action is performed, the result is save into a
	 * temporary image file, see createUnique on the filename specs.
	 * It does not return the saved file, alway returning the tmp file.
	 * @param string $action, should be 'crop', 'scale', 'rotate','flip', or 'save'
	 * @param string $relative the relative image filename
	 * @param string $fullpath the fullpath to the image file
	 * @return array with image information
	 * <code>array('src'=>'url of the image', 'dimensions'=>'width="xx" height="yy"',
	 * 'file'=>'image file, relative', 'fullpath'=>'full path to the image');</code>
	 */
	function processAction($action, $relative, $fullpath) 
	{
		$params = '';

		if(isset($_GET['params']))
			$params = $_GET['params'];

		$values =  explode(',',$params,7);
//print_r($values);
		$theFullPath = $this->getSaveFileName($values[0]);
		$theSavePath = substr($theFullPath, 0, strrpos($theFullPath, '/'));
		$theSaveFile = substr($theFullPath, strlen($theSavePath));
		$theSaveFile = preg_replace('/\\.(?![^.]*$)/', '_', $theSaveFile);
		$theSaveFile = $this->filterdateiname($theSaveFile);
		$saveFile = $theSavePath."/".$theSaveFile;
		$saveBoxFile = $theSavePath."/.box/".$theSaveFile;
		
		$img = $this->factory(IMAGE_CLASS);
		$img->load($fullpath);

		switch ($action) 
		{
			case 'watermark':
				$urlstr = str_replace("../", "", $values[3]);
				$img->watermark($this->wbconfig['server_path'].rawurldecode($urlstr),intval($values[2]),intval($values[0]),intval($values[1]));
				break;
			case 'crop':
				$img->crop(intval($values[0]),intval($values[1]),intval($values[2]),intval($values[3]));
				break;
			case 'text':
				$img->addText(rawurldecode($values[0]),floatval($values[1]),intval($values[2]),intval($values[3]),floatval($values[4]),rawurldecode($values[5]),rawurldecode($values[6]));
				break;
			case 'scale':
				$img->resize(intval($values[0]),intval($values[1]));
				break;
			case 'rotate':
				$img->rotate(floatval($values[0]));
				break;
			case 'flip':
				if ($values[0] == 'hor')
					$img->flip('hor');
				elseif($values[0] == 'ver')
					$img->flip('ver');
				break;
			case 'save':
				if(!is_null($saveFile))
				{
					$orgBoxFile = false;
					if(isset($values[2]) && $values[2] != '') {
						$orgBoxFile = urldecode($values[2]);
						$newNameSavePath = $this->wbconfig['server_path']."/content/media/image".$theSavePath;
						$newNameSaveFile = $this->generateautofilename($newNameSavePath, $theSaveFile);
						$saveFile = $theSavePath."/".$newNameSaveFile;
						$saveBoxFile = $theSavePath."/.box/".$newNameSaveFile;
						$message = 'rename';
					}
					
					if($values[0] == 'jpg' || $values[0] == 'jpeg' || $values[0] == '') {
						$quality = intval($values[1]);
						if($quality <= 0) {
							$quality = $this->wbconfig['jpg_quality'] == '' ? 99 : $this->wbconfig['jpg_quality'];
						}
					}
					else {
						$quality = $this->wbconfig['png_compress'] == '' ? 0 : $this->wbconfig['png_compress'];
					}
					
					$newSaveFullpath = $this->wbconfig['server_path']."/content/media/image".$saveFile;
					$newSaveBoxFullpath = $this->wbconfig['server_path']."/content/media/image".$saveBoxFile;
					$orgBoxFilepath = $this->wbconfig['server_path']."/content/media/image".$orgBoxFile;
					$img->save($newSaveFullpath, $values[0], $quality);
					if(is_file($newSaveFullpath)) {
						$this->filesaved = 1;
						$this->echofilename = "content/media/image".$saveFile;
						
                		$oldumask = umask(0);
                		@chmod($newSaveFullpath, $this->wbconfig['chmod'][1]);
                		umask($oldumask);
						
						if($orgBoxFile && file_exists($orgBoxFilepath)) { // && !file_exists($newSaveBoxFullpath)
							$temp_img = $this->createUnique($orgBoxFile);
							$temp_new = $this->wbconfig['server_path'].'/content/media/temp/'.$temp_img;
							$this->CopyStartFile($orgBoxFilepath, $temp_new);
							$img->load($temp_new);
							$img->save($newSaveBoxFullpath, $values[0], $quality);
							
							if(is_file($newSaveBoxFullpath)) {
								$oldumask = umask(0);
								@chmod($newSaveBoxFullpath, $this->wbconfig['chmod'][1]);
								umask($oldumask);
							}
						}
					}
					else {
						$this->filesaved = -1;
					}
					
				}
				break;
		}
		
		//create the tmp image file
		
		$filename = $this->createUnique($fullpath);
		$newRelative = $this->makeRelative($relative, $filename);
		$newFullpath = $this->wbconfig['server_path']."/content/media/temp/".$filename;
		$newURL = $this->wbconfig['homepage']."/content/media/temp/".$filename;

		//save the file.
		$img->savetmp($newFullpath);
		$img->free();

		//get the image information
		$imgInfo = @getimagesize($newFullpath);

		$image['src'] = $newURL;
		$image['dimensions'] = $imgInfo[3];
		$image['width'] = $imgInfo[0];
		$image['height'] = $imgInfo[1];
		$image['file'] = $newRelative;
		$image['fullpath'] = $newFullpath;
		if(isset($message))
			$image[$message] = $newNameSaveFile;

		Return $image;
	}



	/**
	 * Get the file name base on the save name
	 * and the save type.
	 * @param string $type image type, 'jpeg', 'png', or 'gif'
	 * @return string the filename according to save type
	 */
	function getSaveFileName($type) 
	{
		if(!isset($_GET['file']))
		{
			Return null;
		}

		//$base = Files::escape(rawurldecode($_GET['file']));
		$base = rawurldecode($_GET['file']);
		$base = str_replace("../", "", $base);

		if($type=='png')
		{
			Return $base.'.png';
		}
		if($type=='gif')
		{
			Return $base.'.gif';
		}
		else
		{
			Return $base.'.jpg';
		}
	}

	/**
	 * Get the default save file name, used by editframe.php.
	 * @return string a suggestive filename, this should be unique
	 */
	function getDefaultSaveFile() 
	{
		if(isset($_GET['img']))
			$relative = rawurldecode($_GET['img']);
		else
			Return null;

		Return $this->getUniqueFilename($relative);
	}

	/**
	 * Get a unique filename. If the file exists, the filename
	 * base is appended with an increasing integer.
	 * @param string $relative the relative filename to the base_dir
	 * @return string a unique filename in the current path
	 */
	function getUniqueFilename($relative) 
	{
		$fullpath = $this->manager->getFullPath($relative);
		
		$pathinfo = pathinfo($fullpath);

		$path = $this->fixPath($pathinfo['dirname']);
		$file = $this->escape($pathinfo['basename']);
		
		$filename = $file;

		$dotIndex = strrpos($file, '.');
		$ext = '';

		if(is_int($dotIndex)) 
		{
			$ext = substr($file, $dotIndex);
			$base = substr($file, 0, $dotIndex);
		}

		$counter = 0;
		while(is_file($path.$filename)) 
		{
			$counter++;
			$filename = $base.'_'.$counter.$ext;
		}
		
		Return $filename;
		
	}

	/**
	 * Specifiy the original relative path, a new filename
	 * and return the new filename with relative path.
	 * i.e. $pathA (-filename) + $file
	 * @param string $pathA the relative file
	 * @param string $file the new filename
	 * @return string relative path with the new filename
	 */
	function makeRelative($pathA, $file) 
	{
		$index = strrpos($pathA,'/');
		if(!is_int($index))
			Return $file;
		
		$path = substr($pathA, 0, $index);
		
		Return $this->fixPath($path).$file;
	}

	/**
	 * Get the action GET parameter
	 * @return string action parameter
	 */
	function getAction() 
	{
		$action = null;
		if(isset($_GET['action']))
			$action = $_GET['action'];
		Return $action;
	}

	/**
	 * Generate a unique string based on md5(microtime()).
	 * Well not so uniqe, as it is limited to 6 characters
	 * @return string unique string.
	 */
    function uniqueStr()
    {
      return substr(md5(microtime()),0,6);
    }

	/**
	 * Create unique tmp image file name.
	 * The filename is based on the tmp file prefix
	 * specified in config.inc.php plus 
	 * the UID (basically a md5 of the remote IP)
	 * and some random 6 character string.
	 * This function also calls to clean up the tmp files.
	 * @param string $file the fullpath to a file
	 * @return string a unique filename for that path
	 * NOTE: it only returns the filename, path no included.	
	 */
	function createUnique($file) 
	{
		$pathinfo = pathinfo($file);
		$path = $this->fixPath($pathinfo['dirname']);
		$imgType = $this->getImageType($file);

		$unique_str = $this->manager->getTmpPrefix().$this->_uid.'_'.$this->uniqueStr().".png";

	   //make sure the the unique temp file does not exists
        while (file_exists($path.$unique_str))
        {
            $unique_str = $this->manager->getTmpPrefix().$this->_uid.'_'.$this->uniqueStr().".png";
        }

		$this->cleanUp($path,$pathinfo['basename']);

		Return $unique_str;
	}

	/**
	 * Delete any tmp image files.
	 * @param string $path the full path 
	 * where the clean should take place.
	 */
	function cleanUp($path,$file) 
	{
		$path = $this->fixPath($path);

		if(!is_dir($path))
			Return false;

		$d = @dir($path);
		
		$tmp = $this->manager->getTmpPrefix();
		$tmpLen = strlen($tmp);

		$prefix = $tmp.$this->_uid;
		$len = strlen($prefix);

		while (false !== ($entry = $d->read())) 
		{
			//echo $entry."<br>";
			if(is_file($path.$entry) && $this->manager->isTmpFile($entry))
			{
				if(substr($entry,0,$len)==$prefix && $entry != $file)
					$this->delFile($path.$entry);
				elseif(substr($entry,0,$tmpLen)==$tmp && $entry != $file)
				{
					if(filemtime($path.$entry)+$this->lapse_time < time())
						$this->delFile($path.$entry);
				}
			}
		}
		$d->close();
	}

	/**
	 * Get the image type base on an image file.
	 * @param string $file the full path to the image file.
	 * @return string of either 'gif', 'jpeg', 'png' or 'bmp'
	 * otherwise it will return null.
	 */
	function getImageType($file) 
	{
		$imageInfo = @getImageSize($file);

		if(!is_array($imageInfo))
			Return null;

		switch($imageInfo[2]) 
		{
			case 1:
				Return 'gif';
			case 2:
				Return 'jpeg';
			case 3:
				Return 'png';
		}

		Return null;
	}

	/**
	 * Check if the specified image can be edit by GD
	 * mainly to check that GD can read and save GIFs
	 * @return int 0 if it is not a GIF file, 1 is GIF is editable, -1 if not editable.
	 */
	function isGDEditable() 
	{
		if(isset($_GET['img']))
			$relative = rawurldecode($_GET['img']);
		else
			Return 0;
		if(IMAGE_CLASS != 'GD')
			Return 0;

		$fullpath = $this->manager->getFullPath($relative);

		$type = $this->getImageType($fullpath);
		if($type != 'gif')
			Return 0;
			
		if(function_exists('ImageCreateFrom' . $type) && function_exists('image' .$type))
			Return 1;
		else
			Return -1;

		/*
		// Anyway, the variable $type always contains a string 'gif' at this point so the code snippet above could look like this:

		if(function_exists('imagecreatefromgif') && function_exists('imagegif'))
			return 1;
		else
			return -1;
		*/
		
	}

	/**
	 * Check if GIF can be edit by GD.
	 * @return int 0 if it is not using the GD library, 1 is GIF is editable, -1 if not editable.
	 */
	function isGDGIFAble() 
	{
		if(IMAGE_CLASS != 'GD')
			Return 0;

		if(function_exists('ImageCreateFromGif') && function_exists('imagegif'))
			Return 1;
		else
			Return -1;
	}
}


