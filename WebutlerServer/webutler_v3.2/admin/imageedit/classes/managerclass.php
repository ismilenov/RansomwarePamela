<?PHP
/**
 * ImageManager, list images, directories, and thumbnails.
 * @author Wei Zhuo
 * @version $Id: managerclass.php,v 1.4 2006/12/21 21:28:00 thierrybo Exp $
 * @package ImageManager
 *
 * ImageManager Class.
 * @author Wei Zhuo
 * @version $Id: managerclass.php,v 1.4 2006/12/21 21:28:00 thierrybo Exp $
 */

/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

class ImageManager extends Image_Transform
{
	/**
	 * Configuration array.
	 */
	var $config;

	/**
	 * Array of directory information.
	 */
	var $dirs;

	/**
	 * Constructor. Create a new Image Manager instance.
	 * @param array $config configuration array, see config.inc.php
	function ImageManager($config) 
	{
		$this->config = $config;
	}
	 */

	/**
	 * Get the base directory.
	 * @return string base dir, see config.inc.php
	 */
	function getBaseDir() 
	{
		Return $this->config['base_dir'];
	}

	/**
	 * Get the base URL.
	 * @return string base url, see config.inc.php
	 */
	function getBaseURL() 
	{
		Return $this->config['base_url'];
	}

	function isValidBase()
	{
		return is_dir($this->getBaseDir());
	}

	/**
	 * Get the tmp file prefix.
     * @return string tmp file prefix.
	 */
	function getTmpPrefix() 
	{
		Return $this->config['tmp_prefix'];
	}

	/**
	 * Get the sub directories in the base dir.
	 * Each array element contain
	 * the relative path (relative to the base dir) as key and the 
	 * full path as value.
	 * @return array of sub directries
	 * <code>array('path name' => 'full directory path', ...)</code>
	 */
	function getDirs() 
	{
		if(is_null($this->dirs))
		{
			$dirs = $this->_dirs($this->getBaseDir(),'/');
			ksort($dirs);
			$this->dirs = $dirs;
		}
		return $this->dirs;
	}

	/**
	 * Recursively travese the directories to get a list
	 * of accessable directories.
	 * @param string $base the full path to the current directory
	 * @param string $path the relative path name
	 * @return array of accessiable sub-directories
	 * <code>array('path name' => 'full directory path', ...)</code>
	 */
	function _dirs($base, $path) 
	{
		$base = $this->fixPath($base);
		$dirs = array();

		if($this->isValidBase() == false)
			return $dirs;

		$d = @dir($base);
		
		while (false !== ($entry = $d->read())) 
		{
			//If it is a directory, and it doesn't start with
			// a dot, and if is it not the thumbnail directory
			if(is_dir($base.$entry) && substr($entry,0,1) != '.' && $this->isThumbDir($entry) == false) 
			{
				$relative = $this->fixPath($path.$entry);
				$fullpath = $this->fixPath($base.$entry);
				$dirs[$relative] = $fullpath;
				$dirs = array_merge($dirs, $this->_dirs($fullpath, $relative));
			}
		}
		$d->close();

		Return $dirs;
	}

	/**
	 * Get all the files and directories of a relative path.
	 * @param string $path relative path to be base path.
	 * @return array of file and path information.
	 * <code>array(0=>array('relative'=>'fullpath',...), 1=>array('filename'=>fileinfo array(),...)</code>
	 * fileinfo array: <code>array('url'=>'full url', 
	 *                       'relative'=>'relative to base', 
	 *                        'fullpath'=>'full file path', 
	 *                        'image'=>imageInfo array() false if not image,
	 *                        'stat' => filestat)</code>
	 */
	function getFiles($path) 
	{
		$files = array();
		$dirs = array();

		if($this->isValidBase() == false)
			return array($files,$dirs);

		$path = $this->fixPath($path);
		$base = $this->fixPath($this->getBaseDir());
		$fullpath = $this->makePath($base,$path);


		$d = @dir($fullpath);
		
		while (false !== ($entry = $d->read())) 
		{
			//not a dot file or directory
			if(substr($entry,0,1) != '.')
			{
				if(is_dir($fullpath.$entry)
					&& $this->isThumbDir($entry) == false)
				{
					$relative = $this->fixPath($path.$entry);
					$full = $this->fixPath($fullpath.$entry);
					$count = $this->countFiles($full);
					$dirs[$relative] = array('fullpath'=>$full,'entry'=>$entry,'count'=>$count);
				}
				else if(is_file($fullpath.$entry) && $this->isThumb($entry)==false && $this->isTmpFile($entry) == false) 
				{
					$img = $this->getImageInfo($fullpath.$entry);

					if(!(!is_array($img)&&$this->config['validate_images']))
					{
						$file['url'] = $this->makePath($this->config['base_url'],$path).$entry;
						$file['relative'] = $path.$entry;
						$file['fullpath'] = $fullpath.$entry;
						$file['image'] = $img;
						$file['stat'] = stat($fullpath.$entry);
						$files[$entry] = $file;
					}
				}
			}
		}
		$d->close();
		ksort($dirs);
		ksort($files);
		
		Return array($dirs, $files);
	}	

	/**
	 * Count the number of files and directories in a given folder
	 * minus the thumbnail folders and thumbnails.
	 */
	function countFiles($path) 
	{
		$total = 0;

		if(is_dir($path)) 
		{
			$d = @dir($path);

			while (false !== ($entry = $d->read())) 
			{
				//echo $entry."<br>";
				if(substr($entry,0,1) != '.'
					&& $this->isThumbDir($entry) == false
					&& $this->isTmpFile($entry) == false
					&& $this->isThumb($entry) == false) 
				{
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}

	/**
	 * Get image size information.
	 * @param string $file the image file
	 * @return array of getImageSize information, 
	 *  false if the file is not an image.
	 */
	function getImageInfo($file) 
	{
		Return @getImageSize($file);
	}

	/**
	 * Check if the given file is a tmp file.
	 * @param string $file file name
	 * @return boolean true if it is a tmp file, false otherwise
	 */
	function isTmpFile($file) 
	{
		$len = strlen($this->config['tmp_prefix']);
		if(substr($file,0,$len)==$this->config['tmp_prefix'])
			Return true;
		else
			Return false;	 	
	}

	/**
	 * Check if the given path is part of the subdirectories
	 * under the base_dir.
	 * @param string $path the relative path to be checked
	 * @return boolean true if the path exists, false otherwise
	 */
	function validRelativePath($path) 
	{
		$dirs = $this->getDirs();
		if($path == '/' || $path == '')
			Return true;
		//check the path given in the url against the 
		//list of paths in the system.
		for($i = 0; $i < count($dirs); $i++)
		{
			$key = key($dirs);
			//we found the path
			if($key == $path)
				Return true;
		
			next($dirs);
		}		
		Return false;
	}

	/**
	 * Process upload files. The file must be an 
	 * uploaded file. If 'validate_images' is set to
	 * true, only images will be processed. Any duplicate
	 * file will be renamed. See Files::copyFile for details
	 * on renaming.
	 * @param string $relative the relative path where the file
	 * should be copied to.
	 * @param array $file the uploaded file from $_FILES
	 * @return boolean true if the file was processed successfully, 
	 * false otherwise
	 */
	function _processFiles($relative, $file)
	{
		
		if($file['error']!=0)
		{
			Return false;
		}

		if(!is_file($file['tmp_name']))
		{
			Return false;
		}

		if(!is_uploaded_file($file['tmp_name']))
		{
			$this->delFile($file['tmp_name']);
			Return false;
		}
		

		if($this->config['validate_images'] == true)
		{
			$imgInfo = @getImageSize($file['tmp_name']);
			if(!is_array($imgInfo))
			{
				$this->delFile($file['tmp_name']);
				Return false;
			}
		}

		//now copy the file
		$path = $this->makePath($this->getBaseDir(),$relative);
		$result = $this->copyFile($file['tmp_name'], $path, $file['name']);

       // constrain image size
       if(!is_int($result))
       {
           $img = $this->factory(IMAGE_CLASS);
           $img->load($path . $result);

           // If maximum size is specified, constrain image to it.
           if ($this->config['maxWidth'] > 0 && $this->config['maxHeight'] > 0 && ($img->img_x > $this->config['maxWidth'] || $img->img_y > $this->config['maxHeight']))
           {
               $percentage = min($this->config['maxWidth']/$img->img_x, $this->config['maxHeight']/$img->img_y);
               $img->scale($percentage);
           }
           $img->save($path . $result);
           $img->free();
       }
       
		//no copy error
		if(!is_int($result))
		{
			$this->delFile($file['tmp_name']);
			Return $result;
		}

		//delete tmp files.
		$this->delFile($file['tmp_name']);
		Return false;
	}

	/**
	 * Get the URL of the relative file.
	 * basically appends the relative file to the 
	 * base_url given in config.inc.php
	 * @param string $relative a file the relative to the base_dir
	 * @return string the URL of the relative file.
	 */
	function getFileURL($relative) 
	{
		Return $this->makeFile($this->getBaseURL(),$relative);
	}

	/**
	 * Get the fullpath to a relative file.
	 * @param string $relative the relative file.
	 * @return string the full path, .ie. the base_dir + relative.
	 */
	function getFullPath($relative) 
	{
		Return $this->makeFile($this->getBaseDir(),$relative);;
	}

	/**
	 * Do some graphic library method checkings
	 * @param string $library the graphics library, GD, NetPBM, or IM.
	 * @param string $method the method to check.
	 * @return boolean true if able, false otherwise.
	 */
	function validGraphicMethods($library,$method)
	{
		switch ($library)
		{
			case 'GD':
				return $this->_checkGDLibrary($method);
				break;
			case 'NetPBM':
				return $this->_checkNetPBMLibrary($method);
				break;
			case 'IM':
				return $this->_checkIMLibrary($method);
		}
		return false;
	}

	function _checkIMLibrary($method)
	{
		//ImageMagick goes throught 1 single executable
		if(is_file($this->fixPath(IMAGE_TRANSFORM_LIB_PATH).'convert'))
			return true;
		else
			return false;
	}

	/**
	 * Check the GD library functionality.
	 * @param string $library the graphics library, GD, NetPBM, or IM.
	 * @return boolean true if able, false otherwise.
	 */
	function _checkGDLibrary($method)
	{
		$errors = array();
		switch($method)
		{
			case 'create':
				$errors['createjpeg'] = function_exists('imagecreatefromjpeg');
				$errors['creategif'] = function_exists('imagecreatefromgif');
				$errors['createpng'] = function_exists('imagecreatefrompng');
				break;
			case 'modify':
				$errors['create'] = function_exists('ImageCreateTrueColor') || function_exists('ImageCreate');
				$errors['copy'] = function_exists('ImageCopyResampled') || function_exists('ImageCopyResized');
				break;
			case 'save':
				$errors['savejpeg'] = function_exists('imagejpeg');
				$errors['savegif'] = function_exists('imagegif');
				$errors['savepng'] = function_exists('imagepng');
				break;
		}

		return $errors;
	}
}

?>
