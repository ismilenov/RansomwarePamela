<?PHP
/**
 * File Utilities.
 * @author Wei Zhuo
 * @version $Id: files.php,v 1.2 2006/12/16 21:38:13 thierrybo Exp $
 * @package ImageManager
 *
 * File Utilities
 * @author Wei Zhuo
 * @version $Id: files.php,v 1.2 2006/12/16 21:38:13 thierrybo Exp $
 * @package ImageManager
 * @subpackage files
 */

/**************************************
    File modified for:
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

define('FILE_ERROR_NO_SOURCE', 100);
define('FILE_ERROR_COPY_FAILED', 101);
define('FILE_ERROR_DST_DIR_FAILED', 102);
define('FILE_COPY_OK', 103);

class Files 
{
	
	/**
	 * Copy a file from source to destination. If unique == true, then if
	 * the destination exists, it will be renamed by appending an increamenting 
	 * counting number.
	 * @param string $source where the file is from, full path to the files required
	 * @param string $destination_file name of the new file, just the filename
	 * @param string $destination_dir where the files, just the destination dir,
	 * e.g., /www/html/gallery/
	 * @param boolean $unique create unique destination file if true.
	 * @return string the new copied filename, else error if anything goes bad.
	 */
	function copyFile($source, $destination_dir, $destination_file, $unique=true) 
	{
		if(!(file_exists($source) && is_file($source))) 
			return FILE_ERROR_NO_SOURCE;

		$destination_dir = Files::fixPath($destination_dir);

		if(!is_dir($destination_dir)) 
			Return FILE_ERROR_DST_DIR_FAILED;

		$filename = $this->escape($destination_file);

		if($unique) 
		{
			$dotIndex = strrpos($destination_file, '.');
			$ext = '';
			if(is_int($dotIndex)) 
			{
				$ext = substr($destination_file, $dotIndex);
				$base = substr($destination_file, 0, $dotIndex);
			}
			$counter = 0;
			while(is_file($destination_dir.$filename)) 
			{
				$counter++;
				$filename = $base.'_'.$counter.$ext;
			}
		}

		if (!copy($source, $destination_dir.$filename))
			return FILE_ERROR_COPY_FAILED;
		
		//verify that it copied, new file must exists
		if (is_file($destination_dir.$filename))
			Return $filename;
		else
			return FILE_ERROR_COPY_FAILED;
	}


	/**
	 * Escape the filenames, any non-word characters will be
	 * replaced by an underscore.
	 * @param string $filename the orginal filename
	 * @return string the escaped safe filename
	 */
	function escape($filename) 
	{
		Return preg_replace('/[^\w\._]/', '_', $filename);
	}

	/**
	 * Delete a file.
	 * @param string $file file to be deleted
	 * @return boolean true if deleted, false otherwise.
	 */
	function delFile($file) 
	{
		if(is_file($file)) 
			Return unlink($file);
		else
			Return false;
	}

	/**
	 * Append a / to the path if required.
	 * @param string $path the path
	 * @return string path with trailing /
	 */
	function fixPath($path) 
	{
		//append a slash to the path if it doesn't exists.
		if(!(substr($path,-1) == '/'))
			$path .= '/';
		Return $path;
	}

	/**
	 * Concat two paths together. Basically $pathA+$pathB
	 * @param string $pathA path one
	 * @param string $pathB path two
	 * @return string a trailing slash combinded path.
	 */
	function makePath($pathA, $pathB) 
	{
		$pathA = $this->fixPath($pathA);
		if(substr($pathB,0,1)=='/')
			$pathB = substr($pathB,1);
		Return $this->fixPath($pathA.$pathB);
	}

	/**
	 * Similar to makePath, but the second parameter
	 * is not only a path, it may contain say a file ending.
	 * @param string $pathA the leading path
	 * @param string $pathB the ending path with file
	 * @return string combined file path.
	 */
	function makeFile($pathA, $pathB) 
	{		
		$pathA = $this->fixPath($pathA);
		if(substr($pathB,0,1)=='/')
			$pathB = substr($pathB,1);
		
		Return $pathA.$pathB;
	}

	
	/**
	 * Format the file size, limits to Mb.
	 * @param int $size the raw filesize
	 * @return string formated file size.
	 */
	function formatSize($size) 
	{
		if($size < 1024) 
			return $size.' bytes';	
		else if($size >= 1024 && $size < 1024*1024) 
			return sprintf('%01.2f',$size/1024.0).' Kb';	
		else
			return sprintf('%01.2f',$size/(1024.0*1024)).' Mb';	
	}
}

?>
