<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/admin/browser/assets/functions.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

class WebutlerMBrowserClass extends WebutlerAdminClass
{
    //var $config;
    var $jsalert = '';
    var $AllowedExtensions;
    var $AvailableIcons;
    var $AllowedTypes;
    var $MediaDirectory;
    var $type;
    var $urltype;
    var $getthumb;
    
	function __construct() {
        require_once dirname(dirname(dirname(dirname(__FILE__)))).'/admin/browser/config.php';
    }
    
    function sendalert($text) {
    	$this->jsalert = "alert('".$text."');\n";
    	$this->jsalert.= "window.top.location.href = '".$this->config['homepage']."/admin/browser/index.php?".$this->urltype."&actualfolder=".$this->makeurlfolder($this->actualfolder()).$this->getthumb.$this->cke_getvars()."';";
    }
    
    function filterzahlen($variablezahlen) {
    	$variablezahlen = preg_replace('/[^0-9]/', '', $variablezahlen);
    	return $variablezahlen;
    }
    
    function filterupload($variableupload) {
    	$extension = strtolower(substr($variableupload, strrpos($variableupload, '.') + 1));
    	$filename = substr($variableupload, 0, strrpos($variableupload, '.'));
        
    	$filename = str_replace(' ', '_', $filename);
    	$filename = strtolower($filename);
    	$filename = preg_replace('/[^a-z0-9_]/', '', $filename);
        
    	return $filename.'.'.$extension;
    }
    
    function cleanpathfromget($path)
    {
    	$path = utf8_decode($path);
    	$path = rawurldecode($path);
    	$path = strip_tags($path);
    	$path = stripslashes($path);
    	$path = str_replace( $this->config['server_path'], '', $path );
    	$path = str_replace( $this->config['homepage'], '', $path );
    	$path = str_replace( '../', '', $path );
    	
    	return $path;
    }
    	
    function cke_getvars()
    {
        $getvars = '';
        $getvars .= isset($_GET['CKEditor']) ? '&CKEditor='.$_GET['CKEditor'] : '';
        $getvars .= isset($_GET['CKEditorFuncNum']) ? '&CKEditorFuncNum='.$_GET['CKEditorFuncNum'] : '';
        $getvars .= isset($_GET['langCode']) ? '&langCode='.$_GET['langCode'] : '';
        
    	return $getvars;
    }
    	
    function makeurlfolder($urlfolder)
    {
    	$urlfolder = rawurlencode(utf8_encode($urlfolder));
    	return $urlfolder;
    }
    
    function actualfolder()
    {
    	if(isset($_GET['actualfolder']))
    	{
    		$actualfolder = $this->cleanpathfromget($_GET['actualfolder']);
    		if(!preg_match('#/$#', $actualfolder))
    		{
    			$actualfolder .= '/';
    		}
    		if(strpos($actualfolder, '/') !== 0)
    		{
    			$actualfolder = '/'.$actualfolder;
    		}
    		return $actualfolder;
    	}
    	else
    	{
    		return '/';
    	}
    }
    
    function lastfolder()
    {
    	$folderpath = $this->actualfolder();
    	$killlastslash = substr($folderpath, 0, -1);
    	$lastfolder = substr($killlastslash, 0, strrpos($killlastslash, '/'));
    	return $lastfolder.'/';
    }
    
    function seticon($datei)
    {
    	$icon = substr($datei, strrpos($datei, '.') + 1);
    	$icon = strtolower($icon);
    	if(in_array($icon, $this->AvailableIcons))
    	{
    		return $icon;
    	}
    	else
    	{
    		return 'default';
    	}
    }
    
    function fileactionpath()
    {
    	$fileactionpath = $this->MediaDirectory.$this->type.$this->actualfolder();
    
    	return $fileactionpath;
    }
    
    function fileexists($file)
    {
    	$file = $this->cleanpathfromget($file);
    	$file = str_replace( '/', '', $file );
    
    	if(file_exists($this->fileactionpath().$file)) 
    	{
    		return $file;
    	}
    	else
    	{
    	    $this->sendalert(_WBLANGADMIN_BROWSER_FILENOTEXISTS_);
    	}
    }
    
    function renamefileto($oldname, $newname)
    {
        $path = $this->MediaDirectory.$this->type.$this->actualfolder();
        if(strrpos($oldname, '.') == '')
        {
			if(file_exists($path.$newname))
				$newfile = $this->generateautofilename($path, $newname);
            else
				$newfile = $newname;
        }
        else
        {
    		$extension = substr($oldname, strrpos($oldname, '.') + 1);
        	$extension = strtolower($extension);
			
			if(file_exists($path.$newname.'.'.$extension))
				$newfile = $this->generateautofilename($path, $newname.'.'.$extension);
            else
				$newfile = $newname.'.'.$extension;
        }
        rename($path.$oldname, $path.$newfile);
		
		if($this->type == 'image' && file_exists($path.'.box/'.$oldname)) {
			rename($path.'.box/'.$oldname, $path.'.box/'.$newfile);
		}
    }
    
    function copyfilefromto($data, $unlink = false)
    {
        $file = explode('|', $data);
        $filepath = $file[0];
        $filename = $file[1];
        $frompath = $this->MediaDirectory.$this->type.$filepath;
        $topath = $this->MediaDirectory.$this->type.$this->actualfolder();
	    $skipsys = $this->actualfolder() == '/watermarks/' || $this->actualfolder() == '/tpl_icons/' ? true : false;
        
        $filefrom = $frompath.$filename;
		
		if(file_exists($topath.$filename))
			$tofilename = $this->generateautofilename($topath, $filename);
		else
			$tofilename = $filename;
		
		$fileto = $topath.$tofilename;
        
        copy($filefrom, $fileto);
		if($unlink)
            unlink($filefrom);
        
	    if($this->type == 'image' && file_exists($frompath.'.box/'.$filename))
	    {
            $boxfilefrom = $frompath.'.box/'.$filename;
            $boxfolder = $topath.'.box/';
            $boxfileto = $boxfolder.$tofilename;
            
			if(!$skipsys && !file_exists($boxfolder)) 
			{
				$oldumask = umask(0);
				mkdir($boxfolder, $this->config['chmod'][0]);
				umask($oldumask);
			}
            
    		if(file_exists($boxfilefrom)) {
				if(!$skipsys)
					copy($boxfilefrom, $boxfileto);
				
        		if($unlink)
                    unlink($boxfilefrom);
            }
	    }
    }
    
    function delete($file)
    {
		if($this->fileexists($file))
		{
			$datei = $this->fileactionpath().$file;
			$candelete = true;
			$error = false;
				
			if(is_dir($datei.'/'))
			{
				$verzeichnis = opendir($datei);
				while(false !== ($inhalt = readdir($verzeichnis)))
				{
					if ($inhalt != '' && $inhalt != '.' && $inhalt != '..')
					{
						$candelete = false;
					}
				}
				closedir($verzeichnis);
				
				if(!$candelete)
				{
					$error = true;
					$this->sendalert(_WBLANGADMIN_BROWSER_DELONLYEMPTY_);
				}
				else
				{
					if(!rmdir($datei.'/'))
					{
						$error = true;
						$this->sendalert(_WBLANGADMIN_BROWSER_THISFOLDER_.' '.$file.' '._WBLANGADMIN_BROWSER_NOTDELETEABLE_);
					}
				}
			}
			else
			{
				$datei = $this->fileactionpath().$file;
				$format = GetImageSize($datei);
				if($format)
				{
					$boxfile = $this->fileactionpath().'.box/'.$file;
					if(file_exists($boxfile))
					{
						unlink($boxfile);
					}
				}
				
				if(!unlink($datei))
				{
					$error = true;
					$this->sendalert(_WBLANGADMIN_BROWSER_THISFILE_.' '.$file.' '._WBLANGADMIN_BROWSER_NOTDELETEABLE_);
				}
			}
			
			if(!$error) {
				$location = $this->getserverlocation();
				//header('Location: '.$location);
				unset($_GET['delete']);
				$getvars = array();
				foreach($_GET as $key => $val)
				{
					$getvars[] = $key.'='.($key == 'actualfolder' ? $this->makeurlfolder($val) : $val);
				}
				
				header('Location: '.$location.'?'.implode('&', $getvars));
			}
		}
    }
    
    function getserverlocation()
    {
		/*
        $location = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://');
        $location.= $_SERVER['HTTP_HOST'];
        $location.= $_SERVER['REDIRECT_URL'];
		*/
		$location = $this->config['homepage'].'/admin/browser/index.php';
        
        return $location;
    }
    
    function makefolder($name)
    {
		/*
		$name = strip_tags($name);
		$name = stripslashes($name);
		$name = trim($name);
		$name = str_replace('.', ' ', $name);
		$name = str_replace('-', ' ', $name);
		$name = preg_replace('/( +)/', '_', $name);
		$name = $this->filterupload($name);
		*/
		$name = preg_replace('/[^a-z0-9_]/', '', strtolower($name));
		
		if($name != '') 
		{
			$newfolder = $this->fileactionpath().$name;
			if(!file_exists($newfolder)) 
			{
				$oldumask = umask(0);
				if(mkdir($newfolder, $this->config['chmod'][0]))
				{
					umask($oldumask);
					$this->sendalert(sprintf(_WBLANGADMIN_BROWSER_FOLDERADDED_, $name));
				}
				else
				{
					$this->sendalert(sprintf(_WBLANGADMIN_BROWSER_FOLDERNOTADDED_, $name));
				}
			}
			else
			{ 
				$this->sendalert(sprintf(_WBLANGADMIN_BROWSER_FOLDEREXISTS_, $name));
			}
		}  
		else
		{
			$this->sendalert(_WBLANGADMIN_BROWSER_NOFOLDERNAME_);
		}
    }
    
    function resizeimage($imagefile, $imgwidth, $imgheight)
    {
	    if($imgwidth > 0 || $imgheight > 0) {
			list($orgwidth, $orgheight, $type) = getimagesize($imagefile);
			
			$cropwidth = $imgwidth > 0 ? $imgwidth : $orgwidth*($imgheight/$orgheight);
			$cropheight = $imgheight > 0 ? $imgheight : $orgheight*($imgwidth/$orgwidth);
			
			if($cropwidth > 0 && $cropheight > 0 && ($type == 1 || $type == 2 || $type == 3)) {
				switch($type) {
					case 1: $image = imagecreatefromgif($imagefile); break;
					case 2: $image = imagecreatefromjpeg($imagefile); break;
					case 3: $image = imagecreatefrompng($imagefile); break;
				}
				
				if($imgwidth > 0 && $imgheight > 0) {
					$ratio = max($cropwidth/$orgwidth, $cropheight/$orgheight);
					$pos_x = intval(($orgwidth - $cropwidth / $ratio) / 2);
					$pos_y = intval(($orgheight - $cropheight / $ratio) / 2);
					$orgwidth = intval($cropwidth / $ratio);
					$orgheight = intval($cropheight / $ratio);
				}
				else {
					$pos_x = 0;
					$pos_y = 0;
				}
				
				if($type == 1) {
					$newimage = imagecreate($cropwidth, $cropheight);
					$black = imagecolorallocate($newimage, 0x00, 0x00, 0x00);
					$trans = imagecolortransparent($newimage, $black);
					imagefill($newimage, 0, 0, $trans);
				}
				else {
					$newimage = imagecreatetruecolor($cropwidth, $cropheight);
				}
				
				if($type == 3 && ord(file_get_contents($imagefile, NULL, NULL, 25, 1)) == 6) {
					imagecolortransparent($newimage, imagecolorallocatealpha($newimage, 0, 0, 0, 127));
					imagealphablending($newimage, false);
					imagesavealpha($newimage, true);
				}
				
				imagecopyresampled($newimage, $image, 0, 0, $pos_x, $pos_y, $cropwidth, $cropheight, $orgwidth, $orgheight);
				
				if($this->config['jpg_quality'] == '') $this->config['jpg_quality'] = 99;
				if($this->config['png_compress'] == '') $this->config['png_compress'] = 0;
				
				switch($type) {
					case 1: imagegif($newimage, $imagefile); break;
					case 2: imagejpeg($newimage, $imagefile, $this->config['jpg_quality']); break;
					case 3: imagepng($newimage, $imagefile, $this->config['png_compress']); break;
					//case 3: imagepng($newimage, $imagefile); break;
				}
			}
		}
    }
    
    function resourcetype($type)
    {
    	if($type == 'file') {
    		$resource = _WBLANGADMIN_BROWSER_RESOURCE_FILE_;
    	}
    	if($type == 'image') {
    		$resource = _WBLANGADMIN_BROWSER_RESOURCE_IMAGE_;
    	}
    	if($type == 'flash') {
    		$resource = _WBLANGADMIN_BROWSER_RESOURCE_FLASH_;
    	}
    	if($type == 'track') {
    		$resource = _WBLANGADMIN_BROWSER_RESOURCE_TRACK_;
    	}
    	return $resource;
    }
    
    function resourcetitle($type)
    {
		if($type == 'file') {
			$title = _WBLANGADMIN_BROWSER_RESOURCE_ALLFORMATS_;
		}
		else {
			$title = _WBLANGADMIN_BROWSER_RESOURCE_FORMATS_.': ';
			$laenge = count($this->AllowedExtensions[$type]);
			
			for($i = 0; $i < $laenge; $i++)
			{
				$title .= $this->AllowedExtensions[$type][$i];
				if($i < ($laenge-1))
				{
					$title .= ', ';
				}
			}
		}
    
    	return $title;
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
			if(!file_exists($path.$nameneu))
				$stop = true;
		}
		
		return $nameneu;
    }
    
	function uploadfile($filename, $overwrite, $imgsmallwidth, $imgsmallheight, $imgboxwidth, $imgboxheight, $lightbox, $live = false)
    {
		if($filename != '')
		{
			$filename = rawurldecode($filename);
			$filename = preg_replace('/\\.(?![^.]*$)/', '_', $filename);
			
			$imgsmallwidth = $this->filterzahlen($imgsmallwidth);
			$imgsmallheight = $this->filterzahlen($imgsmallheight);
			$imgboxwidth = $this->filterzahlen($imgboxwidth);
			$imgboxheight = $this->filterzahlen($imgboxheight);
			
			$extension = substr($filename, strrpos($filename, '.') + 1);
			$extension = strtolower($extension);
			
			$nameneu = $this->filterupload($filename);
			$dateipath = $this->fileactionpath().$nameneu;
			
			if(file_exists($dateipath))
			{
				if($overwrite) {
					unlink($dateipath);
					
					$boxfile = $this->fileactionpath().'.box/'.$nameneu;
					if(file_exists($boxfile))
						unlink($boxfile);
				}
				else {
					$nameneu = $this->generateautofilename($this->fileactionpath(), $nameneu);
					$dateipath = $this->fileactionpath().$nameneu;
				}
			}
			
			if(!file_exists($dateipath))
			{
				if($this->type == 'file' || in_array($extension, $this->AllowedExtensions[$this->type]))
				{
					if($this->type == 'image' && $this->actualfolder() != '/watermarks/' && $this->actualfolder() != '/tpl_icons/')
					{
						$boxfolder = $this->fileactionpath().'.box/';
						if(!file_exists($boxfolder)) 
						{
							$oldumask = umask(0);
							mkdir($boxfolder, $this->config['chmod'][0]);
							umask($oldumask);
						}
						$dateiboxpath = $boxfolder.$nameneu;
						
						$file = fopen($dateiboxpath, 'w');
						fclose($file);
						$this->setchmodaftersave($dateiboxpath);
						
						$upload = fopen('php://input', 'r');
						while(!feof($upload)) {
							file_put_contents($dateiboxpath, fread($upload, 4096), FILE_APPEND);
						}
						fclose($upload);
						
						copy($dateiboxpath, $dateipath);
						
						if($lightbox && ($imgboxwidth > 0 || $imgboxheight > 0))
							 $this->resizeimage($dateiboxpath, $imgboxwidth, $imgboxheight);
						
						if($imgsmallwidth > 0 || $imgsmallheight > 0)
							$this->resizeimage($dateipath, $imgsmallwidth, $imgsmallheight);
						
						$this->setchmodaftersave($dateipath);
						
						if(!$lightbox)
							unlink($dateiboxpath);
						
						if(!$live) {
							echo sprintf(_WBLANGADMIN_BROWSER_FILEUPLOADED_, $nameneu);
						}
						else {
							list($smallwidth, $smallheight) = getimagesize($dateipath);
							$jsonresult = array('uploaded' => 1, 'fileName' => $nameneu, 'url' => 'content/media/image'.$this->actualfolder().$nameneu, 'attributes' => array('width' => $smallwidth, 'height' => $smallheight));
							echo json_encode($jsonresult);
						}
					}
					else
					{
						$file = fopen($dateipath, 'w');
						fclose($file);
						$this->setchmodaftersave($dateipath);
						
						$upload = fopen('php://input', 'r');
						while(!feof($upload)) {
							file_put_contents($dateipath, fread($upload, 4096), FILE_APPEND);
						}
						fclose($upload);
						
						if($this->actualfolder() == '/watermarks/' || $this->actualfolder() == '/tpl_icons/')
						{
							if($imgsmallwidth > 0 || $imgsmallheight > 0)
								$this->resizeimage($dateipath, $imgsmallwidth, $imgsmallheight);
						}
						
						echo sprintf(_WBLANGADMIN_BROWSER_FILEUPLOADED_, $nameneu);
					}
				}
				else
				{
					if(!$live) {
						echo sprintf(_WBLANGADMIN_BROWSER_FILEWRONGFORMAT_, $extension, $this->resourcetype($this->type));
					}
					else {
						echo json_encode(array('uploaded' => 0, 'error' => array('message' => sprintf(_WBLANGADMIN_BROWSER_FILEWRONGFORMAT_, $extension, $this->resourcetype($this->type)))));
					}
				}
			}
		}
		else
		{
			echo _WBLANGADMIN_BROWSER_NOFILE_;
		}
    }
	
	function format_filesize($file)
	{
		$bytes = filesize($file);
		
		if($bytes >= 1073741824)
			$bytes = number_format($bytes/1073741824, 2).' GB';
		elseif($bytes >= 1048576)
			$bytes = number_format($bytes/1048576, 2).' MB';
		elseif($bytes >= 1024)
			$bytes = number_format($bytes/1024).' KB';
		elseif($bytes > 0)
			$bytes = number_format($bytes/1024, 1).' KB';
		else
			$bytes = '0 Bytes';

		return $bytes;
	}
    
    function get_folders($dateien)
    {
        $result = '';
		$firstline = true;
        
        if(is_dir($dateien))
        {
            $verzeichnis = opendir($dateien);
            while(false !== ($inhalt = readdir($verzeichnis)))
            {
            	if($inhalt != '.' && $inhalt != '..' && $inhalt != '.box')
            	{
            		if(is_dir($dateien.'/'.$inhalt.'/'))
            		{
            			if((($inhalt == 'watermarks' || $inhalt == 'tpl_icons') && ($this->config['admin_erweitert'] != '1' || (isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1'))) || ($inhalt != 'watermarks' && $inhalt != 'tpl_icons'))
            			{
                			$result.= '<tr';
							if($this->type != 'image' && $firstline) {
								$result.= ' class="firstline"';
								$firstline = false;
							}
                			$result.= '>';
                			$result.= '<td width="21"><img alt="" src="images/folder.gif" style="margin-right: 5px" /></td>';
                			$result.= '<td style="word-space: nowrap"><a href="index.php?'.$this->urltype.'&actualfolder='.$this->makeurlfolder($this->actualfolder().$inhalt.'/').$this->getthumb.$this->cke_getvars().'">'.$inhalt.'</a>';
                            $result.= ($inhalt != 'watermarks' && $inhalt != 'tpl_icons') ? '<div class="tooltip tipleft" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_RENAME_.'"><img alt="" src="images/edit.png" onclick="renamepopupopen(\''.$inhalt.'\')" class="renameedit" /></div>' : '';
                            $result.= '</td>';
                			$result.= '<td>&nbsp;</td>';
                			$result.= '<td>&nbsp;</td>';
                			if($this->type == 'image')
                			{
                				$result.= '<td>&nbsp;</td>';
                			}
                			if($inhalt == 'watermarks' || $inhalt == 'tpl_icons')
                			{
                				$result.= '<td>&nbsp;</td>';
                				$result.= '<td>&nbsp;</td>';
                				$result.= '<td>&nbsp;</td>';
                			}
                			else
                            {
                                $result.= '<td width="18">&nbsp;</td>
                                    <td width="18">&nbsp;</td>
                                    <td width="18"><a href="#" class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_DELETE_.'" onclick="DeleteFile(\''.$inhalt.'\');return false;"><img alt="" src="images/trash.gif" style="margin: 0px 0px 0px 3px" /></a></td>';
                			}
                			$result.= '</tr>';
            			}
            		}
            	}
            }
            closedir($verzeichnis);
        }
        
        return $result;
    }
    
    function get_files($dateien, $first)
    {
        $result = '';
		$firstline = true;
        
        if(is_dir($dateien))
        {
    		if(isset($_GET['vorschau']) && $this->type == 'image') {
    			$result.= '<tr>
    			  <td class="previewimgs" colspan="8">';
    		}
            $verzeichnis = opendir($dateien);
            while(false !== ($inhalt = readdir($verzeichnis)))
            {
            	if($inhalt != '.' && $inhalt != '..' && $inhalt != '.box')
            	{
            		if(is_file($dateien.'/'.$inhalt))
            		{
            			$icon = $this->seticon($inhalt);
            			$format = GetImageSize($dateien.'/'.$inhalt);
                    	$wh = ($format[0] >= $format[1]) ? 'width' : 'height';
            			$filesize = $this->format_filesize($dateien.'/'.$inhalt);
            			
            			if(isset($_GET['vorschau']) && $this->type == 'image') {
                		  $result.= '<div style="float: left; width: 125px; margin: 5px; border: 1px solid #373737">
                        	<table border="0" cellspacing="5" cellpadding="0">
                        	  <tr>
                        		<td style="width: 115px; height: 115px; background-color: #e9e9e9; text-align: center">';
                                if($this->actualfolder() == '/watermarks/') {
                				    $result.= '<span class="noinsert" style="display:block;line-height:0;"><img title="'.$inhalt.'" alt="" style="'.$wh.': 109px; margin: 3px" src="'.$this->config['homepage'].'/content/media/'.$this->type.$this->actualfolder().$inhalt.'" /></span>';
                                } elseif($this->actualfolder() == '/tpl_icons/') {
                				    $result.= '<a style="display:block;line-height:0;" href="#" onclick="setTplIcon(\''.$inhalt.'\');return false;"><img title="'.$inhalt.'" alt="" style="'.$wh.': 109px; margin: 3px" src="'.$this->config['homepage'].'/content/media/'.$this->type.$this->actualfolder().$inhalt.'" /></a>';
                                } else {
                                    $result.= '<a style="display:block;line-height:0;" title="'.$inhalt.' '._WBLANGADMIN_BROWSER_IMAGE_INSERT_.'" href="#" onclick="OpenFile(\'content/media/'.$this->type.$this->actualfolder().$inhalt.'\');return false;"><img alt="" style="'.$wh.': 109px; margin: 3px" src="'.$this->config['homepage'].'/content/media/'.$this->type.$this->actualfolder().$inhalt.'" /></a>';
                                }
                                $result.= '</td>
                        	  </tr>
                        	  <tr>
                        		<td style="height: 15px; text-align: right; vertical-align: bottom"><img alt="" src="images/icons/'.$icon.'.gif" style="margin: 0px 3px -2px 0px" /> '.$filesize.' <a href="#" class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_IMGEDIT_.'" onclick="OpenEditWin(\''.$this->config['homepage'].'\', \''.$this->actualfolder().$inhalt.'\');return false;"><img alt="" src="images/brush.png" style="margin: 0px 3px -1px 3px" /></a> <a href="#" class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_DELETE_.'" onclick="DeleteFile(\''.$inhalt.'\');return false;"><img alt="" src="images/trash.gif" style="margin-bottom: -2px" /></a></td>
                        	  </tr>
                        	</table>
                		  </div>';
            			}
            			else {
                			$result.= '<tr';
							if($first && $firstline) {
								$result.= ' class="firstline"';
								$firstline = false;
							}
							$result.= '>';
                			if($icon == 'flv' && (!isset($_GET['types']) || $_GET['types'] == ''))
                			{
                				$result.= '<td style="width: 21px"><img alt="" src="images/icons/'.$icon.'.gif" style="margin-right: 5px" /></td>
                				  <td style="word-space: nowrap"><a href="#" onclick="OpenFile(\'';
								//if(!isset($_GET['av']))
								  $result.= 'includes/javascript/player/flvplayer.swf?flv=/';
								$result.= 'content/media/'.$this->type.$this->actualfolder().$inhalt.'\');return false;">'.$inhalt.'</a><div class="tooltip tipleft" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_RENAME_.'"><img alt="" src="images/edit.png" onclick="renamepopupopen(\''.$inhalt.'\')" class="renameedit" /></div></td>';
                			}
                			elseif($icon == 'mp3' && (!isset($_GET['types']) || $_GET['types'] == ''))
                			{
                				$result.= '<td style="width: 21px"><img alt="" src="images/icons/'.$icon.'.gif" style="margin-right: 5px" /></td>
                				  <td style="word-space: nowrap"><a href="#" onclick="OpenFile(\'';
								if(!isset($_GET['av']))
								  $result.= 'includes/javascript/player/mp3player.swf?mp3=/';
								$result.= 'content/media/'.$this->type.$this->actualfolder().$inhalt.'\');return false;">'.$inhalt.'</a><div class="tooltip tipleft" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_RENAME_.'"><img alt="" src="images/edit.png" onclick="renamepopupopen(\''.$inhalt.'\')" class="renameedit" /></div></td>';
                			}
                			else
                			{
                				$result.= '<td style="width: 21px"><img alt="" src="images/icons/'.$icon.'.gif" style="margin-right: 5px" /></td>
                                <td style="word-space: nowrap">';
                                if($this->actualfolder() == '/watermarks/') {
                				    $result.= '<span class="noinsert">'.$inhalt.'</span>';
								}
                                elseif($this->actualfolder() == '/tpl_icons/') {
                				    $result.= '<a href="#" onclick="setTplIcon(\''.$inhalt.'\');return false;">'.$inhalt.'</a>';
								}
                                else {
                				    $result.= '<a href="#" onclick="OpenFile(\'content/media/'.$this->type.$this->actualfolder().$inhalt.'\');return false;">'.$inhalt.'</a>';
								}
                                    
                                $result.= '<div class="tooltip tipleft" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_RENAME_.'"><img alt="" src="images/edit.png" onclick="renamepopupopen(\''.$inhalt.'\')" class="renameedit" /></div>';
								if($this->type == 'image' && $this->actualfolder() != '/watermarks/' && $this->actualfolder() != '/tpl_icons/' && !file_exists($dateien.'/.box/'.$inhalt)) {
									$result.= '<div class="tooltip tipleft" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_HASNOBOXIMG_.'"><img alt="" src="images/box.png" class="hasnoboximg" /></div>';
								}
								$result.= '</td>';
                			}
                			$result.= '<td style="text-align: right; word-space: nowrap">'.$filesize.'</td>';
                			if($icon == 'flv')
                			{
                				$result.= '<td style="width: 21px"><a href="#" class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_PREVIEW_.'" onclick="OpenWindow(\''.$this->config['homepage'].'/includes/javascript/player/flvplayer.swf?flv=/content/media/'.$this->type.$this->actualfolder().$inhalt.'\', \''._WBLANGADMIN_BROWSER_PREVIEW_.'\', \'true\')"><img alt="" src="images/show.png" style="margin: 0px 3px" /></a></td>';
                			}
                			elseif($icon == 'mp3')
                			{
                				$result.= '<td style="width: 21px"><a href="#" class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_PREVIEW_.'" onclick="OpenWindow(\''.$this->config['homepage'].'/includes/javascript/player/mp3player.swf?mp3=/content/media/'.$this->type.$this->actualfolder().$inhalt.'\', \''._WBLANGADMIN_BROWSER_PREVIEW_.'\', \'true\')"><img alt="" src="images/show.png" style="margin: 0px 3px" /></a></td>';
                			}
                			else
                			{
                				$result.= '<td style="width: 21px"><a href="#" class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_PREVIEW_.'" onclick="OpenWindow(\''.$this->config['homepage'].'/content/media/'.$this->type.$this->actualfolder().$inhalt.'\', \''._WBLANGADMIN_BROWSER_PREVIEW_.'\', \'false\')"><img alt="" src="images/show.png" style="margin: 0px 3px" /></a></td>';
                			}
                			if($this->type == 'image')
                			{
                				$result.= '<td style="width: 21px"><a href="#" class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_IMGEDIT_.'" onclick="OpenEditWin(\''.$this->config['homepage'].'\', \''.$this->actualfolder().$inhalt.'\');return false;"><img alt="" src="images/brush.png" style="margin: 0px 3px" /></a></td>';
                			}
                            $result.= '<td style="width: 21px">';
                            if(isset($_SESSION['copy'.$this->type]) && $_SESSION['copy'.$this->type] == $this->actualfolder().'|'.$inhalt)
                            {
                                $result.= '<img alt="" src="images/copied.png" style="margin: 0px 3px" />';
                            }
                            else
                            {
                                $result.= '<form method="post"><input type="hidden" name="copyfile" value="'.$this->actualfolder().'|'.$inhalt.'" /><div class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_COPY_.'"><input type="image" name="copy" src="images/copy.png" style="margin: 0px 3px" /></div></form>';
                            }
                            $result.= '</td>
                                <td style="width: 21px">';
                            if(isset($_SESSION['cut'.$this->type]) && $_SESSION['cut'.$this->type] == $this->actualfolder().'|'.$inhalt)
                            {
                                $result.= '<img alt="" src="images/cutted.png" style="margin: 0px 3px" />';
                            }
                            else
                            {
                                $result.= '<form method="post"><input type="hidden" name="cutoutfile" value="'.$this->actualfolder().'|'.$inhalt.'" /><div class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_CUT_.'"><input type="image" name="cutout" src="images/cutout.png" style="margin: 0px 3px" /></div></form>';
                            }
                            $result.= '</td>
                                <td style="width: 18px"><a href="#" class="tooltip tipright" data-tooltip="'._WBLANGADMIN_BROWSER_ICON_DELETE_.'" onclick="DeleteFile(\''.$inhalt.'\');return false;"><img alt="" src="images/trash.gif" style="margin: 0px 0px 0px 3px" /></a></td>
                			  </tr>';
            			}
            		}
            	}
            }
            closedir($verzeichnis);
    		if(isset($_GET['vorschau']) && $this->type == 'image') {
    			$result.= '</td>
    			  </tr>';
    		}
        }
        
        return $result;
    }
}


