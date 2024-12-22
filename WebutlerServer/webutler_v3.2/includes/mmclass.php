<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#includes/mmclass.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

class MMConnectClass
{
    var $serverpath;
    var $getpage;
    var $chmod;
	var $pngcomp = 0;
	var $jpgqual = 99;
    var $langconf;
    var $modname;
    var $moddb;
    var $fileconfig;
    var $dbconfig;
    var $dbselects = array();
    var $dbchecks = array();
	var $params;
    var $alert = array();
    var $sessgrpids;
    var $pagelang;
	var $subcatmenu = array();
    var $get;
    var $post;
    var $files;
	var $modpage = '';
    var $basecat = '';
    var $datastartid;
	var $tplpath;
    
	
    function connectdb()
    {
        $this->moddb = new SQLite3($this->serverpath.'/modules/'.$this->modname.'/data/'.$this->modname.'.db');
		
    	$this->tplpath = $this->serverpath.'/modules/'.$this->modname.'/view/tpls';
        
        $conf = $this->loadconfigfromdb();
        $this->dbconfig = unserialize($conf['config']);
        $this->dbselects = $this->getdbselects($conf);
        $this->dbchecks = $this->getdbchecks($conf);
		
		$this->params['cat'] = (isset($this->fileconfig['urlparams']['cat'])) ? $this->fileconfig['urlparams']['cat'] : false;
		$this->params['order'] = (isset($this->fileconfig['urlparams']['order'])) ? $this->fileconfig['urlparams']['order'] : false;
		$this->params['topic'] = (isset($this->fileconfig['urlparams']['topic'])) ? $this->fileconfig['urlparams']['topic'] : false;
		$this->params['data'] = (isset($this->fileconfig['urlparams']['data'])) ? $this->fileconfig['urlparams']['data'] : false;
		$this->params['load'] = (isset($this->fileconfig['urlparams']['load'])) ? $this->fileconfig['urlparams']['load'] : false;
		unset($this->fileconfig['urlparams']);
		
		if($this->basecat != '')
			$this->basecat = array_key_exists('base', $this->fileconfig) && in_array('basecat', $this->fileconfig['base']) ? $this->validnum($this->basecat) : '';
    }

    function getdbnumrows($check)
    {
        $rows = 0;
        while($row = $check->fetchArray()) {
            $rows++;
        }
        return $rows;
    }
    
    function validnum($num)
    {
    	$num = preg_replace("/[^0-9]/", "", $num);
    	return $num;
    }
    
    function validfield($field)
    {
    	$field = strtolower($field);
    	$field = preg_replace("/[^a-z0-9_]/", "", $field);
    	return $field;
    }
    
    function validfile($filename)
    {
        $newfilename = strtolower($filename);
        $extension = substr(strrchr($newfilename, '.'), 1);
        $newfilename = substr($newfilename, 0, strrpos($newfilename, '.'));
        $newfilename = str_replace(".", "_", $newfilename);
        $newfilename = str_replace("-", "_", $newfilename);
        $newfilename = str_replace(" ", "_", $newfilename);
        $newfilename = preg_replace("/[^a-z0-9_]/", "", $newfilename);
        
        return $newfilename.'.'.$extension;
    }
    
    function validinput($field, $input)
    {
    	$output = '';
    	
    	$skiptypes = array('option', 'catname', 'catlink', 'cattext', 'lang', 'topic', 'fromtime', 'totime', 'username');
    	$type = (in_array($field, $skiptypes)) ? $field : $this->searchfieldintypes($field);
    	
		if($type == '') {
	    	$output = '';
    	}
	    elseif($type == 'lang') {
	    	$output = preg_replace("/[^a-z]/", "", $input);
	    }
	    elseif($type == 'seo') {
	    	$output = strip_tags($input);
	    	$output = str_replace("\n", " ", $output);
	    	$output = htmlspecialchars($output);
	    }
	    elseif($type == 'catname') {
	    	$output = htmlspecialchars($input);
	    }
	    elseif($type == 'catlink') {
	    	$output = htmlspecialchars($input);
	    }
	    elseif($type == 'cattext') {
	    	$output = preg_replace("#<script(.*?)>(.*?)</script>#is", "", $input);
	    	$output = preg_replace("#<\?(.*?)\?>#is", "", $output);
	    }
	    elseif($type == 'topic') {
	    	$output = htmlspecialchars($input);
	    }
	    elseif($type == 'fromtime') {
	    	$output = preg_replace("/[^0-9\-]/", "", $input);
	    }
	    elseif($type == 'totime') {
	    	$output = preg_replace("/[^0-9\-]/", "", $input);
	    }
	    elseif($type == 'date') {
	    	$output = preg_replace("/[^0-9\-]/", "", $input);
	    }
	    elseif($type == 'user') {
	    	$output = preg_replace("/[^0-9]/", "", $input);
	    }
	    elseif($type == 'username') {
			$output = str_replace('###', '', $input);
	    	$output = htmlspecialchars($output);
	    }
	    elseif($type == 'text') {
	    	$output = htmlspecialchars($input);
	    }
	    elseif($type == 'area') {
	    	$output = str_replace("\n", " ", $input);
	    	$output = htmlspecialchars($output);
	    }
	    elseif($type == 'html') {
	    	$output = preg_replace("#<script(.*?)>(.*?)</script>#is", "", $input);
	    	$output = preg_replace("#<\?(.*?)\?>#is", "", $output);
	    }
	    elseif($type == 'bbcode') {
	    	$output = strip_tags($input);
	    	$output = htmlspecialchars($output);
	    }
	    elseif($type == 'number') {
	    	$output = str_replace(',', '.', $input);
	    	$output = preg_replace("/[^0-9\.]/", "", $output);
			if(substr_count($output, '.') > 1) {
				$lastdot = strrpos($output, '.');
				$beforedot = str_replace('.', '', substr($output, 0, $lastdot));
				$afterdot = substr($output, $lastdot+1, strlen($output));
				$output = $beforedot.'.'.$afterdot;
			}
	    }
	    elseif($type == 'state') {
	    	$output = preg_replace("/[^0-9]/", "", $input);
	    }
	    elseif($type == 'select') {
	    	$output = htmlspecialchars($input);
		    $output = $this->getselectcontent($field, $output);
	    }
	    elseif($type == 'checkbox') {
	    	$out = array();
		    foreach($input as $key => $val) {
		    	$val = trim(htmlspecialchars($val));
		    	$val = $this->getcheckboxcontent($field, $val);
			    $out[$key] = str_replace(array("<"."?", "?".">"), array("&lt;?", "?&gt;"), $val);
		    }
	    	$output = serialize($out);
	    }
	    elseif($type == 'hidden') {
	    	$output = htmlspecialchars($input);
	    }
	    elseif($type == 'option') {
	    	$output = htmlspecialchars($input);
	    }
	    
		if($type != 'checkbox') {
		    $output = trim($output);
			$output = str_replace(array("<"."?", "?".">"), array("&lt;?", "?&gt;"), $output);
		}
		
    	return $output;
    }
    
    function checkforlangdefine($phrase)
	{
		return (defined($phrase)) ? constant($phrase) : $phrase;
	}
    
    function searchfieldintypes($field)
    {
		$fieldtypes = $this->fileconfig['types'];
		$result = '';
		foreach($fieldtypes as $key => $value) {
			$komma = strpos($value, ',');
			if($komma === false) {
				if($value == $field) {
					$result = $key;
					break;
				}
			}
			else {
				$vals = explode(',', $value);
				if(in_array($field, $vals)) {
					$result = $key;
					break;
				}
			}
		}
		return $result;
    }
    
    function getdbfieldfromfile($type, $result = '')
    {
        $types = $this->fileconfig['types'];
        
        if(array_key_exists($type, $types)) {
            $fields = explode(',', $types[$type]);
            foreach($fields as $field)
                $result[] = $field;
        }
        
        return $result;
    }
    
    function loadconfigfromdb()
    {
        $selectfields = $this->getdbfieldfromfile('select');
        $checkboxfields = $this->getdbfieldfromfile('checkbox');
        
        $fields = '';
        $configfields = array_merge(is_array($selectfields) ? $selectfields : array(), is_array($checkboxfields) ? $checkboxfields : array());
        if(count($configfields) >= 1)
            $fields = ', '.implode(', ', $configfields);
        
        $confs = $this->moddb->query("SELECT id, config".$fields." FROM confs WHERE id = '1'");
        $conf = $confs->fetchArray();
        
        return $conf;
    }
    
    function getdbselects($conf)
    {
        $selectfields = $this->getdbfieldfromfile('select');
        
        if(is_array($selectfields)) {
	        foreach($selectfields as $selectfield)
	            $dbselects[$selectfield] = $conf[$selectfield];
	        
	        return $dbselects;
        }
    }
    
    function getdbchecks($conf)
    {
        $checkboxfields = $this->getdbfieldfromfile('checkbox');
        
        if(is_array($checkboxfields)) {
	        foreach($checkboxfields as $checkboxfield)
	            $dbchecks[$checkboxfield] = $conf[$checkboxfield];
	        
	        return $dbchecks;
        }
    }
    
    function getselectcontent($name, $value)
    {
        $lines = explode("\n", $this->dbselects[$name]);
        
        foreach($lines as $line) {
            if(trim($line) != '' && trim($line) != '---') {
	            $option = explode("|", trim($line));
	            if(defined($option[0]) && $this->checkforlangdefine($option[0]) == $value) {
				    return $option[0];
	            	break;
	            }
	        }
	    }
	    return $value;
    }
    
    function getcheckboxcontent($name, $value)
    {
        $lines = explode("\n", $this->dbchecks[$name]);
        
        foreach($lines as $line) {
            if(trim($line) != '') {
	            $box = explode("|", trim($line));
	            if(defined($box[1]) && $this->checkforlangdefine($box[1]) == $value) {
				    return $box[1];
	            	break;
	            }
	        }
	    }
	    return $value;
    }
    
    function getselectfields($name, $data = '', $view = false)
    {
        $fields = '';
        $lines = explode("\n", $this->dbselects[$name]);
        $selected = 'false';
		
		if(isset($this->dbconfig[$name.'asradio']))
		{
			$id_count = 1;
			$hasnoselect = !preg_match('#__select__#i', $this->dbselects[$name]) ? 'true' : 'false';
			foreach($lines as $line) {
				if(trim($line) != '' && trim($line) != '---') {
					$option = explode("|", trim($line));
					if($option[0] != '') {
						$value = $this->checkforlangdefine($option[0]);
						$label = (isset($option[1]) && $option[1] != '__select__') ? $this->checkforlangdefine($option[1]) : $value;
						
						$fields.= '<div><input type="radio" name="'.$name.'" value="'.$value.'" id="radio'.$id_count.$name.'"';
						if(!$view)
							$fields.= ' style="width: 12px"';
						if(
						  $selected == 'false' && 
						  ($data != '' && $data == $option[0]) || 
						  ($data == '' && ($hasnoselect == 'true' || (!isset($option[2]) && isset($option[1]) && $option[1] == '__select__') || (isset($option[2]) && $option[2] == '__select__')))
							) {
								$fields.= ' checked="checked"';
								$selected = 'true';
						}
						$fields.= ' /><label for="radio'.$id_count.$name.'">'.$label.'</label></div>'."\n";
						$id_count++;
					}
				}
			}
			
			if($fields == '') {
				$select = $view ? $this->viewdefine('field_noselects') : $this->admindefine('field_noselects');
			}
			else {
				$select = $fields."\n";
			}
		}
		else
        {
			foreach($lines as $line) {
				if(trim($line) != '') {
					if(trim($line) == '---') {
						$fields.= '<option value=""></option>'."\n";
					}
					else {
						$option = explode("|", trim($line));
						if($option[0] != '') {
							$value = $this->checkforlangdefine($option[0]);
							$label = (isset($option[1]) && $option[1] != '__select__') ? $this->checkforlangdefine($option[1]) : $value;
							
							$fields.= '<option value="'.$value.'"';
							if(
							  $selected == 'false' && 
							  ($data != '' && $data == $option[0]) || 
							  ($data == '' && ((!isset($option[2]) && isset($option[1]) && $option[1] == '__select__') || 
							  (isset($option[2]) && $option[2] == '__select__')))
								) {
									$fields.= ' selected="selected"';
									$selected = 'true';
							}
							$fields.= '>'.$label.'</option>'."\n";
						}
					}
				}
			}
			
			if($fields == '') {
				$select = $view ? $this->viewdefine('field_noselects') : $this->admindefine('field_noselects');
			}
			else {
				$select = '<select name="'.$name.'" size="1">'."\n".$fields.'</select>'."\n";
			}
		}
        
        return $select;
    }
    
    function getcheckboxfields($name, $data = '', $view = false)
    {
        $fields = array();
        $lines = explode("\n", $this->dbchecks[$name]);
        if($data != '') $data = unserialize($data);
        
        $i = 0;
        foreach($lines as $line) {
            if(trim($line) != '') {
	            $box = explode("|", trim($line));
	            if(isset($box[0])) {
	                $value = (isset($box[1]) && $box[1] != '__check__') ? $box[1] : $box[0];
		            $label = (isset($box[2]) && $box[2] != '__check__') ? $this->checkforlangdefine($box[2]) : ((isset($box[1]) && $box[1] != '__check__') ? $this->checkforlangdefine($box[1]) : $box[0]);
	                
		            $fields[$i] = '<div><input type="checkbox" name="'.$name.'['.$box[0].']" value="'.$this->checkforlangdefine($value).'" id="check'.$box[0].'"';
					if(!$view)
						$fields[$i].= ' style="width: 12px"';
		            if(
                      (is_array($data) && isset($data[$box[0]]) && $data[$box[0]] == $value) || 
                      ($data == '' && 
                        ((!isset($box[2]) && isset($box[1]) && $box[1] == '__check__') || 
                        (!isset($box[3]) && isset($box[2]) && $box[2] == '__check__') || 
                        (isset($box[3]) && $box[3] == '__check__'))
                      )
                          ) {
    		                $fields[$i].= ' checked="checked"';
		            }
		            $fields[$i].= ' /><label for="check'.$box[0].'">'.$label.'</label></div>';
		            
		            $i++;
	            }
            }
        }
        
        if(count($fields) == 0) {
            $checks = $view ? $this->viewdefine('field_nochecks') : $this->admindefine('field_nochecks');
        }
        else {
	        $checks = implode("\n", $fields);
        }
        
        return $checks;
    }
    
	function is_serialized($data)
	{
	    return (@unserialize($data) !== false);
	}
    
    function makefolder($sub)
    {
    	$path = $this->serverpath.'/modules/'.$this->modname.'/media/'.$sub;
        if(!file_exists($path)) {
    		$umask = umask(0);
    		if(@mkdir($path, $this->chmod[0])) {
        		umask($umask);
            }
        }
    }
    
    function createunique($sub, $extension = '')
    {
    	$path = $this->serverpath.'/modules/'.$this->modname.'/media/'.$sub;
        $stop = false;
        while(!$stop) {
            $uniqid = uniqid();
            
			if(($sub == 'cats' || $sub == 'imgs') && $extension == '')
				$file = $path.'/'.$uniqid;
			elseif(substr($sub, 0, 5) == 'cats/' || substr($sub, 0, 5) == 'imgs/')
				$file = $path.'/'.$uniqid.'_box.'.$extension;
			else
				$file = $path.'/'.$uniqid.'.'.$extension;
			
            if(!file_exists($file)) {
                $stop = true;
            }
        }
        return $uniqid;
    }
    
    function upload($file, $field, $folder, $mediatype)
    {
        if(is_array($field)) {
            $name = $field[0];
            $i = $field[1];
        }
        else {
            $name = $field;
            $i = '';
        }
        $result = '';
        
    	$filename = ($i != '') ? $file[$name]['name'][$i] : $file[$name]['name'];
    	if($filename != '') {
    		
    		if($mediatype == 'opt') $mediafolder = 'opts';
    		if($mediatype == 'cat') $mediafolder = 'cats';
    		if($mediatype == 'image' || $mediatype == 'multi') $mediafolder = 'imgs';
    		if($mediatype == 'file') $mediafolder = 'files';
    		
    		$filepath = $this->serverpath.'/modules/'.$this->modname.'/media/'.$mediafolder.'/'.$folder;
    		
    		if(!file_exists($filepath))
                $this->makefolder($mediafolder.'/'.$folder);
    		
            $type = strtolower(substr(strrchr($filename, '.'), 1));
            
            if($mediatype != 'opt')
	            $uniqid = $this->createunique($mediafolder.'/'.$folder, $type);
            
    		if($mediatype == 'opt' || $mediatype == 'cat' || $mediatype == 'image' || $mediatype == 'multi') {
                if($type == 'jpeg') $type = 'jpg';
            	if($type == 'jpg' || $type == 'gif' || $type == 'png') {
                    $imgsize = $this->fileconfig['imgsize'][$name];
                    $newfilename = ($mediatype == 'opt') ? $filepath.'/'.$this->validfile($filename) : $filepath.'/'.$uniqid.'-org.'.$type;
					
            		$filetmpname = ($i != '') ? $file[$name]['tmp_name'][$i] : $file[$name]['tmp_name'];
            		if(move_uploaded_file($filetmpname, $newfilename)) {
                        $sizes = array_keys($imgsize);
                        
                        if($mediatype == 'opt') {
	            			$this->resizeimg($newfilename, $imgsize['view']);
                            $oldumask = umask(0);
            				chmod($newfilename, $this->chmod[1]);
            				umask($oldumask);
                        }
                        else {
	                		foreach($sizes as $size) {
	                            $resized = $filepath.'/'.$uniqid.'_'.$size.'.'.$type;
	                            copy($newfilename, $resized);
	            				$this->resizeimg($resized, $imgsize[$size]);
	                            $oldumask = umask(0);
	            				chmod($resized, $this->chmod[1]);
	            				umask($oldumask);
	            			}
	            			unlink($newfilename);
	                        $result = array($folder, $uniqid, $type);
                        }
        			}
            	}
    		}
    		if($mediatype == 'file') {
                $newuniqidname = $filepath.'/'.$uniqid.'.'.$type;
                $newfilename = $this->validfile($file[$name]['name']);
				
				if(move_uploaded_file($file[$name]['tmp_name'], $newuniqidname)) {
					$oldumask = umask(0);
					chmod($newuniqidname, $this->chmod[1]);
					umask($oldumask);
					
					$result = array($folder, $newfilename, $uniqid, $type);
				}
    		}
    	}
        return $result;
    }
    
    function resizeimg($imagefile, $size)
    {
	    if($size['width'] > 0 || $size['height'] > 0) {
			list($orgwidth, $orgheight, $type) = getimagesize($imagefile);
			
			$cropwidth = $size['width'] > 0 ? $size['width'] : $orgwidth*($size['height']/$orgheight);
			$cropheight = $size['height'] > 0 ? $size['height'] : $orgheight*($size['width']/$orgwidth);
			
			if($cropwidth > 0 && $cropheight > 0 && ($type == 1 || $type == 2 || $type == 3)) {
				switch($type) {
					case 1: $image = imagecreatefromgif($imagefile); break;
					case 2: $image = imagecreatefromjpeg($imagefile); break;
					case 3: $image = imagecreatefrompng($imagefile); break;
				}
				
				if($size['width'] > 0 && $size['height'] > 0) {
					$ratio = max($cropwidth/$orgwidth, $cropheight/$orgheight);
					$pos_x = intval(($orgwidth - $cropwidth / $ratio) / 2);
					$pos_y = intval(($orgheight - $cropheight / $ratio) / 2);
					$orgwidth = intval($cropwidth / $ratio);
					$orgheight = intval($cropheight / $ratio);
				}
				else{
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
				
				switch($type) {
					case 1: imagegif($newimage, $imagefile); break;
					case 2: imagejpeg($newimage, $imagefile, $this->jpgqual); break;
					case 3: imagepng($newimage, $imagefile, $this->pngcomp); break;
				}
			}
		}
	    return true;
    }
    
    function gettopicrows()
    {
    	$field = '';
        $where = '';
        if(isset($this->get['cat']) && in_array('catid', $this->fileconfig['topic'])) {
            $catid = $this->validnum($this->get['cat']);
            $field = ', catid';
            $where = " WHERE catid = '".$catid."'";
        }
        
        $rows = $this->moddb->query("SELECT id".$field.", sort FROM topics".$where." ORDER BY sort DESC LIMIT 1");
        $row = $rows->fetchArray(SQLITE3_ASSOC);
        
        return $row['sort'];
    }
    
    function getdatarows()
    {
        $field = '';
        $wheres = array();
        $fields = $this->fileconfig['data'];
        if(isset($this->get['cat']) && in_array('catid', $fields)) {
            $catid = $this->validnum($this->get['cat']);
            $field.= ', catid';
            $wheres[] = "catid = '".$catid."'";
        }
        if(isset($this->get['topic']) && in_array('topicid', $fields)) {
            $topicid = $this->validnum($this->get['topic']);
            $field.= ', topicid';
            $wheres[] = "topicid = '".$topicid."'";
        }
        $field.= ', sort';
        $wheres[] = "sort NOT NULL AND sort != ''";
        
        $where = (count($wheres) > 0) ? " WHERE ".implode(' AND ', $wheres) : '';
        
        $rows = $this->moddb->query("SELECT id".$field." FROM datas".$where." ORDER BY sort DESC LIMIT 1");
        $row = $rows->fetchArray(SQLITE3_ASSOC);
        return $row['sort'];
    }
	
	function getsubcatmenu()
	{
		$catsubs = array();
		
		if(count($this->subcatmenu) > 0) {
			$catsubs = $this->subcatmenu;
		}
		else {
			$dbsubs = $this->moddb->query("SELECT id, subcats FROM confs WHERE confs.id = '1' LIMIT 1");
			$subsarr = $dbsubs->fetchArray();
			if($this->is_serialized($subsarr['subcats'])) {
				$catsubs = unserialize($subsarr['subcats']);
				$this->subcatmenu = $catsubs;
			}
		}
		return $catsubs;
    }
	
	function loadsubcatmenu()
	{
		$submenu = array();
		
		if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
			if(count($this->subcatmenu) > 0) {
				$subcatmenu = $this->subcatmenu;
			}
			else {
				$subcatmenu = $this->getsubcatmenu();
			}
			
			if($this->basecat != '') {
				foreach($subcatmenu as $key => $item) {
					if($this->basecat == $item['id']) {
						$submenu = $item['sub'];
						break;
					}
				}
			}
			else {
				$submenu = $subcatmenu;
			}
		}
		
		return $submenu;
	}
}
    
    
/*************** Adminfunctions ***************/

class MMAdminClass extends MMConnectClass
{
    var $homepage;
    var $movewalkkey = '';
    var $langvars = array();
    
	
    function admindefine($define)
    {
    	$phrase = strtoupper('_'.$this->modname.'langadmin_'.$define.'_');
		return (defined($phrase)) ? constant($phrase) : $phrase;
    }
	
	function getsubeditorsgroup($groupid)
	{
		$this->connectdb();
        $data = $this->dbconfig;
        
        if($data['subeditors'] != '' && $data['subeditors'] != 'no' && in_array($data['subeditors'], $groupid))
        	return true;
        
        return false;
	}
    
    function getlanguages($select)
    {
        $langs = '';
        if(isset($this->langconf)) {
            foreach($this->langconf['lang'] as $lang => $value) {
                $langs.= '<option value="'.$lang.'"';
                if($select != '' && $select == $lang) $langs.= ' selected="selected"';
                $langs.= '>'.$value.'</option>'."\n";
            }
        }
        else {
            $langs.= '<option value="">-- '.$this->admindefine('field_nolanguages').' --</option>'."\n";
        }
        
        return $langs;
    }
    
    function setallusersselection($configgroup)
    {
        $result = '';
        if((is_array($configgroup) && in_array('all', $configgroup)) || count($configgroup) == 0 || $this->dbconfig == '') {
            $result = ' selected="selected"';
        }
        return $result;
    }
    
    function setnousersselection($editorgroup)
    {
        $result = '';
        if($editorgroup == 'no' || $editorgroup == '') {
            $result = ' selected="selected"';
        }
        return $result;
    }
    
    function getusergroupids($configgroup)
    {
        $groupids = '';
        if(file_exists($this->serverpath.'/content/access/users.db')) {
            $userdb = new SQLite3($this->serverpath.'/content/access/users.db');
            $groups = $userdb->query("SELECT id, name FROM groups ORDER BY id");
            if($this->getdbnumrows($groups) > 0) {
                while($group = $groups->fetchArray()) {
                    $groupids.= '<option value="'.$group['id'].'"';
                    if((is_array($configgroup) && !in_array('all', $configgroup) && in_array($group['id'], $configgroup)) || (!is_array($configgroup) && $configgroup != '' && $configgroup != 'no' && $configgroup == $group['id']))
                        $groupids.= ' selected="selected"';
                    $groupids.= '>GROUP-ID: '.$group['id'].' -&gt; '.stripslashes($group['name']).'</option>'."\n";
                }
            }
        }
        else {
            $groupids.= '<option value="">-- '.$this->admindefine('field_nousers').' --</option>'."\n";
        }
        
        return $groupids;
    }
    
    function getadminnamefromdb($userid)
    {
        if(file_exists($this->serverpath.'/content/access/users.db')) {
            $userdb = new SQLite3($this->serverpath.'/content/access/users.db');
            $users = $userdb->query("SELECT id, uname, FROM users WHERE id = ".$userid." LIMIT 1");
            if($this->getdbnumrows($users) > 0) {
                $user = $users->fetchArray();
				$username = $user['uname'];
            }
        }
        
        return $username;
    }
    
    function getlangflags($field)
    {
        $langvars = array();
        $langs = '';
        if(isset($this->langconf)) {
            $code = $this->langconf['code'];
            $all_langs = implode('|', $code);
            foreach($code as $lang) {
	            $langvars[] = $lang;
            	$click = ' onclick="showtext(\''.$field.'\', \''.$all_langs.'\', \''.$lang.'\')"';
            	$curlang = ($lang == $this->pagelang) ? ' currentlangflag' : '';
            	if(file_exists($this->serverpath.'/includes/language/icons/'.$lang.'.png'))
            		$langs.= '<img id="'.$field.$lang.'" class="inputlangflag'.$curlang.'" src="'.$this->homepage.'/includes/language/icons/'.$lang.'.png"'.$click.' />'."\n";
		        else
		            $langs.= '<div id="'.$field.$lang.'" class="textlangflag inputlangflag'.$curlang.'"'.$click.'>'.$lang.'</div>'."\n";
            }
		    $langs.= '<br />'."\n";
        }
        else {
	        $langvars[] = $this->pagelang;
        	if(file_exists($this->serverpath.'/includes/language/icons/'.$this->pagelang.'.png'))
        		$langs.= '<img class="currentlangflag" src="'.$this->homepage.'/includes/language/icons/'.$this->pagelang.'.png" />'."\n";
	        else
	            $langs.= '<div class="textlangflag currentlangflag">'.$this->pagelang.'</div>'."\n";
        }
        
        if(count($this->langvars) == 0)
        	$this->langvars = $langvars;
        
        return $langs;
    }
    
    function datafieldsforsort($what)
    {
        $sortfields = '<option value=""> </option>'."\n";
        $configsort = $this->dbconfig['sort'.$what.'field'];
        
		if($what == 'topic') {
			$sortfields.= '<option value="topic"';
			if($configsort == 'topic') {
				$sortfields.= ' selected="selected"';
			}
			$sortfields.= '>'.$this->admindefine('input_topic').'</option>'."\n";
		}
		
        if(($what == 'topic' && in_array('fromtime', $this->fileconfig['topic'])) || ($what == 'data' && in_array('fromtime', $this->fileconfig['data']))) {
            $sortfields.= '<option value="fromtime"';
            if($configsort == 'fromtime') {
                $sortfields.= ' selected="selected"';
            }
            $sortfields.= '>'.$this->admindefine('input_fromtime').'</option>'."\n";
        }
        
        if($what == 'data' || ($what == 'topic' && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base']))) {
			$fields = array();
			$fields = $this->getdbfieldfromfile('text', $fields);
			$fields = $this->getdbfieldfromfile('area', $fields);
			$fields = $this->getdbfieldfromfile('html', $fields);
			$fields = $this->getdbfieldfromfile('bbcode', $fields);
			$fields = $this->getdbfieldfromfile('number', $fields);
			$fields = $this->getdbfieldfromfile('date', $fields);
			$fields = $this->getdbfieldfromfile('user', $fields);
			$fields = $this->getdbfieldfromfile('hidden', $fields);
			
			foreach($fields as $field) {
				$sortfields.= '<option value="'.$field.'"';
				if($configsort == $field) {
					$sortfields.= ' selected="selected"';
				}
				$sortfields.= '>'.$this->admindefine('input_'.$field).'</option>'."\n";
			}
		}
        
        return $sortfields;
    }
	
    function getuploadbytes($size)
    {
		switch(substr($size, -1))
		{
			case 'K': case 'k': return (int)$size * 1024;
			case 'M': case 'm': return (int)$size * 1048576;
			case 'G': case 'g': return (int)$size * 1073741824;
			default: return $size;
		}
	}
	
    function getuploadmaxsize()
    {
		$upload_max_filesize = $this->getuploadbytes(ini_get('upload_max_filesize'));
		$post_max_size = $this->getuploadbytes(ini_get('post_max_size'));
		$memory_limit = $this->getuploadbytes(ini_get('memory_limit'));
		$maxsize = min($upload_max_filesize, $post_max_size, $memory_limit);
		
		return $maxsize;
	}
	
    function getuploadfiletypes()
    {
		$mimetypes = $this->dbconfig['savemime'];
		
		return json_encode($mimetypes);
    }
	
    function uploadlarge($field, $filename, $filetype, $dataid = '')
    {
		$newfilename = $this->validfile(rawurldecode($filename));
		$newfileext = strtolower(substr(strrchr($newfilename, '.'), 1));
		$filetype = rawurldecode($filetype);
		
		if($dataid != '') $dataid = $this->validnum($dataid);
		
		if($dataid != '') {
			$data = $this->moddb->query("SELECT id, ".$field." FROM datas WHERE id = '".$dataid."' LIMIT 1");
			if($this->getdbnumrows($data) > 0) {
				$dataarray = $data->fetchArray(SQLITE3_ASSOC);
				$uniqidfolder = '';
				$oldfilename = '';
				$uniqidfile = '';
				$oldfileext = '';
				foreach($dataarray as $key => $val) {
					if($this->searchfieldintypes($key) == 'file') {
						$file = unserialize($val);
						if($file != '') {
							$filepath = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$file[0].'/'.$file[2].'.'.$file[3];
							if(file_exists($filepath)) {
								$savedtypes = $this->dbconfig['savemime'][$key];
								if($savedtypes == '' || preg_match("(".$savedtypes.")", $filetype)) {
									$uniqidfolder = $file[0];
									$oldfilename = $file[1];
									$uniqidfile = $file[2];
									$oldfileext = $file[3];
								}
							}
						}
					}
				}
				
				if($uniqidfolder == '' && $uniqidfile == '') {
					$uniqidfolder = $this->createunique('files');
					$filepath = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$uniqidfolder;
					$this->makefolder('files/'.$uniqidfolder);
					
					$uniqidfile = $this->createunique('files/'.$uniqidfolder, $newfileext);
				}
				else {
					unlink($this->serverpath.'/modules/'.$this->modname.'/media/files/'.$uniqidfolder.'/'.$uniqidfile.'.'.$oldfileext);
				}
				
				if($oldfilename != $newfilename || $oldfileext != $newfileext) {
					$newdata = array($uniqidfolder, $newfilename, $uniqidfile, $newfileext);
					$update = $field.' = \''.$this->moddb->escapeString(serialize($newdata)).'\'';
					$this->moddb->query("UPDATE datas SET ".$update." WHERE datas.id = '".$dataid."'");
				}
			}
		}
		else {
			$uniqidfolder = $this->createunique('files');
			$filepath = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$uniqidfolder;
			$this->makefolder('files/'.$uniqidfolder);
			
	        $uniqidfile = $this->createunique('files/'.$uniqidfolder, $newfileext);
		}
		
		$newfilepath = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$uniqidfolder.'/'.$uniqidfile.'.'.$newfileext;
		
		$file = fopen($newfilepath, 'w');
		fclose($file);
		$oldumask = umask(0);
		chmod($newfilepath, $this->chmod[1]);
		umask($oldumask);
		
		$result = ($dataid != '') ? 'updated' : $uniqidfolder.'|'.$newfilename.'|'.$uniqidfile.'|'.$newfileext;
		
		$upload = fopen('php://input', 'r');
		while(!feof($upload)) {
			file_put_contents($newfilepath, fread($upload, 4096), FILE_APPEND);
		}
		fclose($upload);
		
		echo $result;
	}
    
    function getimageof($table, $id, $field)
    {
        $id = $this->validnum($id);
        if($id != '') {
            $datas = $this->moddb->query("SELECT id, ".$field." FROM ".$table." WHERE ".$table.".id = '".$id."' LIMIT 1");
            $data = $datas->fetchArray();
            $files = unserialize($data[$field]);
            $result = '';
            
            if($files != '') {
                if($table == 'cats') $imgfolder = 'cats';
                elseif($table == 'datas') $imgfolder = 'imgs';
                
                if(is_array($files[0])) {
                    $filescount = count($files);
                    for($i = 0; $i < $filescount; $i++) {
                        $filepath = $this->serverpath.'/modules/'.$this->modname.'/media/'.$imgfolder.'/'.$files[$i][0].'/'.$files[$i][1].'_box.'.$files[$i][2];
                        if(file_exists($filepath)) {
                            list($width, $height) = getimagesize($filepath);
                            $result.= '<div><img onclick="prevpopbox(\'media/'.$imgfolder.'/'.$files[$i][0].'/'.$files[$i][1].'_box.'.$files[$i][2].'\', \''.$width.'\', \''.$height.'\')" src="admin/icons/img.png" /><label for="del'.$field.'_'.$i.'"><img src="admin/icons/delete.png" title="'.$this->admindefine('button_delete').'" /></label><input type="checkbox" name="del'.$field.'_'.$i.'" id="del'.$field.'_'.$i.'" /></div>';
                        }
                        else {
                            $result.= '<div><img src="admin/icons/delete.png" title="'.$this->admindefine('field_dbwithoutfile').'" /><input type="checkbox" name="del'.$field.'_'.$i.'" checked="checked" style="display: none" /></div>';
                        }
                    }
                }
                else {
                    $filepath = $this->serverpath.'/modules/'.$this->modname.'/media/'.$imgfolder.'/'.$files[0].'/'.$files[1].'_box.'.$files[2];
                    if(file_exists($filepath)) {
                        list($width, $height) = getimagesize($filepath);
                        $result.= '<img onclick="prevpopbox(\'media/'.$imgfolder.'/'.$files[0].'/'.$files[1].'_box.'.$files[2].'\', \''.$width.'\', \''.$height.'\')" src="admin/icons/img.png" /><label for="del'.$field.'"><img src="admin/icons/delete.png" title="'.$this->admindefine('button_delete').'" /></label><input type="checkbox" name="del'.$field.'" id="del'.$field.'" />';
                    }
                    else {
                        $result.= '<img src="admin/icons/delete.png" title="'.$this->admindefine('field_dbwithoutfile').'" /><input type="checkbox" name="del'.$field.'" checked="checked" style="display: none" />';
                    }
                }
            }
            
            return $result;
        }
    }
    
    function getfileof($id, $field)
    {
        $id = $this->validnum($id);
        if($id != '') {
            $datas = $this->moddb->query("SELECT id, ".$field." FROM datas WHERE datas.id = '".$id."' LIMIT 1");
            $data = $datas->fetchArray();
            $file = unserialize($data[$field]);
            
            if($file != '') {
                $filepath = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$file[0].'/'.$file[2].'.'.$file[3];
                if(file_exists($filepath)) {
                    $result = '<a href="media/loader.php?file='.$field.'&amp;id='.$id.'" target="_blank"><img src="admin/icons/file.png" title="'.$file[1].'" /></a><label for="del'.$field.'"><img src="admin/icons/delete.png" title="'.$this->admindefine('button_delete').'" /></label><input type="checkbox" name="del'.$field.'" id="del'.$field.'" />';
                }
                else {
                    $result = '<img src="admin/icons/delete.png" title="'.$this->admindefine('field_dbwithoutfile').'" /><input type="checkbox" name="del'.$field.'" checked="checked" style="display: none" />';
                }
                return $result;
            }
        }
    }
    
    function getuploadtypes($name)
    {
	    $result = '';
	    $savedtypes = $this->dbconfig['savemime'][$name];
	    
	    if($savedtypes != '') {
	        $result.= '<div class="allowedtypes">';
		    $result.= $this->admindefine('field_filetype').': ';
		    $result.= strtoupper(str_replace('|', ', ', $savedtypes));
		    $result.= '</div>';
	    }
	    
	    return $result;
    }
	
	function checkchmoddb()
	{
		$output = '';
		if(!is_writeable($this->serverpath.'/modules/'.$this->modname.'/data/'.$this->modname.'.db')) {
			$output.= '<tr><td style="padding-left: 70px">/data/'.$this->modname.'.db</td></tr>'."\n";
		}
		
		return $output;
    }
	
	function checkchmods($dir)
	{
		$path = $this->serverpath.'/modules/'.$this->modname.'/'.$dir;
		$handle = opendir($path);
		$output = '';
		while(false !== ($file = readdir($handle))) {
			if($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'loader.php') {
				$dirfile = '/'.$dir.'/'.$file;
				if(is_dir($path.'/'.$file.'/')) {
					if(!is_writeable($path.'/'.$file.'/'))
						$output.= '<tr><td style="padding-left: 70px">'.$dirfile.'</td></tr>'."\n";
					else
						$output.= $this->checkchmods($dir.'/'.$file);
				}
				else {
					if(!is_writeable($path.'/'.$file))
						$output.= '<tr><td style="padding-left: 70px">'.$dirfile.'</td></tr>'."\n";
				}
			}
		}
		closedir($handle);

		return $output;
	}
    
    function saveconfig()
    {
        unset($this->post['saveconf']);
		
		if(isset($this->post['config']['catsperpage']))
			$this->post['config']['catsperpage'] = $this->validnum($this->post['config']['catsperpage']);
		if(isset($this->post['config']['topicsperpage']))
			$this->post['config']['topicsperpage'] = $this->validnum($this->post['config']['topicsperpage']);
		if(isset($this->post['config']['datasperpage']))
			$this->post['config']['datasperpage'] = $this->validnum($this->post['config']['datasperpage']);
		if(isset($this->post['config']['numbnewest']))
			$this->post['config']['numbnewest'] = $this->validnum($this->post['config']['numbnewest']);
		
        $update[] = 'config = \''.$this->moddb->escapeString(serialize($this->post['config'])).'\'';
        
        if(isset($this->post['select'])) {
            foreach($this->post['select'] as $keyselect => $valueselect) {
                $update[] = $keyselect.' = \''.$this->moddb->escapeString($valueselect).'\'';
            }
        }
        if(isset($this->post['checkbox'])) {
            foreach($this->post['checkbox'] as $keycheckbox => $valuecheckbox) {
                $update[] = $keycheckbox.' = \''.$this->moddb->escapeString($valuecheckbox).'\'';
            }
        }
        
        $updatefields = implode(', ', $update);
        $this->moddb->query("UPDATE confs SET ".$updatefields." WHERE confs.id = '1'");
    }
    
    function getoptionslist()
    {
	    $optionlist = '';
	    $options = $this->moddb->query("SELECT id, grpname, optvals, sort FROM options ORDER BY sort");
        $optionnumrows = $this->getdbnumrows($options);
        
        if($optionnumrows > 0) {
            $count = 0;
            while($option = $options->fetchArray(SQLITE3_ASSOC)) {
                $count++;
                $class = ($count % 2 == 0) ? 'even' : 'odd';
				if($this->is_serialized($option['grpname'])) {
					$option_grpname = unserialize($option['grpname']);
					$optionname = $option_grpname[$this->pagelang];
				}
				else {
		            $optionname = $option['grpname'];
				}
                $optionlist.= '<tr class="'.$class.'">
                <td class="start" title="ID: '.$option['id'].'">'.$optionname.'</td>
                <td><img src="admin/icons/edit.png" title="'.$this->admindefine('button_edit').'" class="inp" onclick="location.href=\'admin.php?page=options&option='.$option['id'].'\'" /><input type="image" class="img" name="delete[option]['.$option['id'].']" src="admin/icons/delete.png" title="'.$this->admindefine('button_delete').'" onclick="return confirm(\''.$this->admindefine('prompt_should').' '.$optionname.' '.$this->admindefine('prompt_realdel').'\')" /></td>
                <td class="end">';
                $optionlist.= ($option['sort'] == 1) ? '<img src="admin/icons/blank.png" />' : '<input type="image" class="img" name="up[option]['.$option['sort'].']" src="admin/icons/up.png" title="'.$this->admindefine('button_hoch').'" />';
                $optionlist.= ($option['sort'] == $optionnumrows) ? '<img src="admin/icons/blank.png" />' : '<input type="image" class="img" name="down[option]['.$option['sort'].']" src="admin/icons/down.png" title="'.$this->admindefine('button_runter').'" />';
                $optionlist.= '</td>
                </tr>'."\n";
            }
	        
			$optionlist.= '<tr class="bottom"><td colspan="3">&nbsp;</td></tr>'."\n";
        }
        
        return $optionlist;
    }
    
    function getoptionrows()
    {
		$rows = $this->moddb->query("SELECT id FROM options");
		return $this->getdbnumrows($rows);
    }
    
    function saveoption()
    {
        unset($this->post['saveoption']);
        
        if(isset($this->post['grpname'])) {
	        $keys = array();
	        $values = array();
	        
		    $keys[] = 'grpname';
		    if(is_array($this->post['grpname'])) {
				$nameval = array();
				foreach($this->post['grpname'] as $lang => $val) {
					$nameval[$lang] = $this->validinput('option', $val);
				}
				$values[] = $this->moddb->escapeString(serialize($nameval));
		    }
		    else {
			    $values[] = $this->moddb->escapeString($this->validinput('option', $this->post['grpname']));
			}
		    
	        if(isset($this->post['optvals'])) {
			    $keys[] = 'optvals';
			    $values[] = $this->moddb->escapeString($this->validinput('option', $this->post['optvals']));
	        }
	        
		    $keys[] = 'sort';
		    $values[] = $this->getoptionrows()+1;
	        
	        $this->moddb->query('INSERT INTO options ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')');
        }
        
        header("Location: admin.php?page=options");
    }
    
    function updateoption()
    {
        unset($this->post['updateoption']);
        
        $id = $this->validnum($this->get['option']);
        if($id != '' && isset($this->post['grpname'])) {
		    if(is_array($this->post['grpname'])) {
				$nameval = array();
				foreach($this->post['grpname'] as $lang => $val) {
					$nameval[$lang] = $this->validinput('option', $val);
				}
				$grpname = $this->moddb->escapeString(serialize($nameval));
		    }
		    else {
	        	$grpname = $this->validinput('option', $this->post['grpname']);
	        }
            $update[] = 'grpname = \''.$this->moddb->escapeString($grpname).'\'';
            
	        if(isset($this->post['optvals'])) {
	        	$optvals = $this->validinput('option', $this->post['optvals']);
	            $update[] = 'optvals = \''.$this->moddb->escapeString($optvals).'\'';
	        }
	        
	        $fields = implode(', ', $update);
	        $this->moddb->query("UPDATE options SET ".$fields." WHERE options.id = '".$id."'");
        }
        
        header("Location: admin.php?page=options");
    }
    
    function getoption()
    {
        $id = $this->validnum($this->get['option']);
        if($id != '') {
            $optiondata = '';
		    $option = $this->moddb->query("SELECT id, grpname, optvals FROM options WHERE id='".$id."' LIMIT 1");
            
	        if($this->getdbnumrows($option) >= 1) {
				$opt = $option->fetchArray(SQLITE3_ASSOC);
		        $optiondata = array();
	            foreach($opt as $key => $val) {
	                $optiondata[$key] = ($this->is_serialized($val)) ? unserialize($val) : $val;
	            }
	        }
            
            return $optiondata;
	    }
    }
    
    function checkoptions()
    {
		$optionlist = '';
		
        if(in_array('optionids', $this->fileconfig['data'])) {
			$options = $this->moddb->query("SELECT id, grpname, sort FROM options ORDER BY sort");
			
			if($this->getdbnumrows($options) == 0) {
				$optionlist = $this->admindefine('nooptiongroups');
			}
			else {
				$getids = array();
				
				if(
				  (isset($this->get['topic']) && $this->get['topic'] != 'new' && !isset($this->get['data'])) || 
				  (isset($this->get['data']) && $this->get['data'] != 'new')
				) {
					$dataid = isset($this->get['data']) ? $this->validnum($this->get['data']) : '';
					if($dataid == '' && array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic'])) {
						$topicid = (isset($this->get['topic'])) ? $this->validnum($this->get['topic']) : '';
						if($topicid != '') {
							$infos = $this->moddb->query("SELECT id, startid FROM topics WHERE topics.id = '".$topicid."'");
							$info = $infos->fetchArray();
							$dataid = $info['startid'];
						}
					}
					if($dataid != '') {
						$loadopts = $this->moddb->query("SELECT id, optionids FROM datas WHERE datas.id = '".$dataid."'");
						$loadopt = $loadopts->fetchArray();
						$optionids = $loadopt['optionids'];
						if($optionids != '')
							$getids = unserialize($optionids);
					}
				}
				
				$optionlines = array();
				while($option = $options->fetchArray()) {
					$optionline = '<input type="checkbox" name="optionids['.$option['id'].']" style="width: 15px"';
					if(count($getids) >= 1 && in_array($option['id'], $getids))
						$optionline.= ' checked="checked"';
					$optionline.= ' id="option_'.$option['id'].'" /><label for="option_'.$option['id'].'">';
					
					if($this->is_serialized($option['grpname'])) {
						$option_grpname = unserialize($option['grpname']);
						$grpname = $option_grpname[$this->pagelang];
					}
					else {
						$grpname = $option['grpname'];
					}
					
					$optionline.= $grpname.' '.$this->admindefine('button_show').'</label>';
					
					$optionlines[] = $optionline;
				}
				
				$optionlist = implode("\n<br />", $optionlines);
			}
		}
        
        return $optionlist;
    }
    
    function newoptimgfolder()
    {
    	unset($this->post['makeoptfolder']);
    	$this->makefolder('opts/'.$this->validfield($this->post['newoptfolder']));
    }
    
    function readoptimgfolders()
    {
    	$optimgfolders = '';
    	
		$directory = $this->serverpath.'/modules/'.$this->modname.'/media/opts';
		$handle = opendir($directory);
		while(($file = readdir($handle)) !== false) {
			if($file != "." && $file != ".." && !is_file($file)) {
				$optimgfolders.= '<option value="'.$file.'">'.$file.'</option>'."\n";
			}
		}
		closedir($handle);
		
		return $optimgfolders;
    }
    
    function uploadoptionimg()
    {
    	unset($this->post['uploadoptimgs']);
    	
        $folder = $this->validfield($this->post['optfolder']);
        for($i = 1; $i <= 5; $i++) {
            if($this->files['optimage']['name'][$i] != '') {
                $dateityp = $this->files['optimage']['type'][$i];
				if(preg_match("(jpg|jpeg|gif|png)", $dateityp)) {
                    $this->upload($this->files, array('optimage', $i), $folder, 'opt');
                }
            }
        }
    }
    
    function loadoptionimglist()
    {
    	$optimglist = '';
    	$count = 0;
    	
		$directory = $this->serverpath.'/modules/'.$this->modname.'/media/opts';
		$handle = opendir($directory);
		while(($dir = readdir($handle)) !== false) {
	    	$count++;
			if($dir != "." && $dir != ".." && !is_file($dir)) {
				$optimglist.= '<strong class="showoptfolder" onclick="showoptimgdiv('.$count.')">'.$dir.'</strong>'."\n";
				$optimglist.= '<div style="display: none" class="showoptimgs" id="showoptimg_'.$count.'">'."\n";
				$subdir = $directory.'/'.$dir;
				$subhandle = opendir($subdir);
				while(($file = readdir($subhandle)) !== false) {
					if($file != "." && $file != ".." && !is_dir($file)) {
						$optimglist.= '<img src="media/opts/'.$dir.'/'.$file.'" style="width: 50px" /><br /><input type="text" value="'.$dir.'/'.$file.'" onmousedown="select()" onclick="select()" /><br />'."\n";
					}
				}
				closedir($subhandle);
				$optimglist.= '</div>'."\n";
			}
		}
		closedir($handle);
		
		return $optimglist;
    }
    
    function getlistnav($page, $num, $next)
    {
        $cat = (isset($this->get['cat'])) ? '&cat='.$this->validnum($this->get['cat']) : '';
        $topic = (isset($this->get['topic'])) ? '&topic='.$this->validnum($this->get['topic']) : '';
        
        $nav = '<div style="text-align: center">';
        $nav.= (($num-1) > 0) ? '<a href="admin.php?page='.$page.$cat.$topic.'&num='.($num-1).'">&laquo;</a>' : '';
        $nav.= '<span style="margin: 0px 20px">'.$this->admindefine('navi_page').' '.$num.'</span>';
        $nav.= ($next != $num) ? '<a href="admin.php?page='.$page.$cat.$topic.'&num='.($num+1).'">&raquo;</a>' : '';
        $nav.= '</div>';
        
        return $nav;
    }
    
    function getcatdbfields()
    {
        $fields = implode(', ', $this->fileconfig['cat']);
        
        return $fields;
    }
    
    function getcatlist()
    {
        $catlist = '';
        $field = '';
        $dir = (isset($this->dbconfig['catsort'])) ? ' '.$this->dbconfig['catsort'] : '';
        $order = (in_array('sort', $this->fileconfig['cat'])) ? 'sort' : 'id';
        
        if(isset($this->dbconfig['catsperpage']) && $this->dbconfig['catsperpage'] != '')
            $catsperpage = $this->dbconfig['catsperpage'];
        elseif(isset($this->fileconfig['catsperpage']) && $this->fileconfig['catsperpage'] != '')
            $catsperpage = $this->fileconfig['catsperpage'];
        else
            $catsperpage = '';
        
	    $limit = '';
        $checknextcat = '0';
        $num = (isset($this->get['num'])) ? $this->validnum($this->get['num']) : 1;
        if($catsperpage != '') {
            $nextcat = $this->moddb->query("SELECT ".$order." FROM cats ORDER BY ".$order.$dir." LIMIT ".($num*$catsperpage).", 1");
            $checknextcat = $this->getdbnumrows($nextcat);
            $start = $num*$catsperpage-$catsperpage;
            $limit = " LIMIT ".$start.", ".$catsperpage;
        }
        
        $fields = $this->getcatdbfields();
        $cats = $this->moddb->query("SELECT id, ".$fields." FROM cats ORDER BY ".$order.$dir.$limit);
        $catnumrows = $this->getdbnumrows($cats);
        
        if($catnumrows >= 1) {
            $count = 0;
            while($cat = $cats->fetchArray()) {
                $count = ($count+1);
                $class = ($count % 2 == 0) ? 'even' : 'odd';
                $catlist.= '<tr class="'.$class.'">
                <td class="start" title="ID: '.$cat['id'].'">';
				if($this->is_serialized($cat['catname'])) {
					$cat_catname = unserialize($cat['catname']);
					$catname = $cat_catname[$this->pagelang];
				}
				else {
					$catname = $cat['catname'];
				}
                if(isset($this->fileconfig['topic']))
                    $catlist.= '<a href="admin.php?page=topics&cat='.$cat['id'].'">'.$catname.'</a>';
                else
                    $catlist.= '<a href="admin.php?page=datas&cat='.$cat['id'].'">'.$catname.'</a>';
                $catlist.= '</td>'."\n";
                $catlist.= '<td><img src="admin/icons/edit.png" title="'.$this->admindefine('button_edit').'" class="inp" onclick="location.href=\'admin.php?page=cats&cat='.$cat['id'].'\'" /><input type="image" class="img" name="delete[cat]['.$cat['id'].']" src="admin/icons/delete.png" title="'.$this->admindefine('button_delete').'" onclick="return confirm(\''.$this->admindefine('prompt_should').' '.$catname.' '.$this->admindefine('prompt_realdel').'\')" /></td>
                <td';
                if($order != 'sort')
					$catlist.= ' class="end"';
				$catlist.= '>';
                $catlist.= ($cat['onoff'] == 1) ? '<img src="admin/icons/on.png" title="'.$this->admindefine('button_online').'" />' : '<input type="image" class="img" name="on[cat]['.$cat['id'].']" src="admin/icons/out.png" title="'.$this->admindefine('button_seton').'" onmouseover="this.src=\'admin/icons/on.png\'" onmouseout="this.src=\'admin/icons/out.png\'" />';
                $catlist.= ($cat['onoff'] == '') ? '<img src="admin/icons/off.png" title="'.$this->admindefine('button_offline').'" />' : '<input type="image" class="img" name="off[cat]['.$cat['id'].']" src="admin/icons/out.png" title="'.$this->admindefine('button_setoff').'" onmouseover="this.src=\'admin/icons/off.png\'" onmouseout="this.src=\'admin/icons/out.png\'" />';
                $catlist.= '</td>'."\n";
                if($order == 'sort') {
                    $catlist.= '<td class="end">';
                    $catlist.= ($cat['sort'] == 1 || ($catnumrows == 1 && $num == 1)) ? '<img src="admin/icons/blank.png" />' : '<input type="image" class="img" name="up[cat]['.$cat['sort'].']" src="admin/icons/up.png" title="'.$this->admindefine('button_hoch').'" />';
                    $catlist.= ($cat['sort'] == $this->getcatrows() || ($catnumrows == 1 && $num == 1)) ? '<img src="admin/icons/blank.png" />' : '<input type="image" class="img" name="down[cat]['.$cat['sort'].']" src="admin/icons/down.png" title="'.$this->admindefine('button_runter').'" />';
                    $catlist.= '</td>'."\n";
                }
                $catlist.= '</tr>'."\n";
            }
            
            $catlist.= '<tr class="bottom">
            <td colspan="';
            $catlist.= ($order == 'sort') ? '4' : '3';
            $catlist.= '">';
            if($catsperpage != '') {
                $numbs = ($checknextcat == '0') ? $num : $num+1;
                $catlist.= $this->getlistnav('cats', $num, $numbs);
            }
            else {
                $catlist.= '&nbsp;';
            }
            $catlist.= '</td>
            </tr>'."\n";
        }
        return $catlist;
    }
    
    function getcat()
    {
        $loadcat = '';
        
        $catid = (isset($this->get['cat'])) ? $this->validnum($this->get['cat']) : '';
        $fields = $this->getcatdbfields();
        
        $cat = $this->moddb->query("SELECT id, ".$fields." FROM cats WHERE id = '".$catid."' LIMIT 1");
        if($this->getdbnumrows($cat) >= 1) {
			$dbcat = $cat->fetchArray(SQLITE3_ASSOC);
	        $loadcat = array();
            foreach($dbcat as $key => $val) {
                $loadcat[$key] = ($this->is_serialized($val)) ? unserialize($val) : $val;
            }
        }
        
        return $loadcat;
    }
    
    function getcatrows()
    {
        $rows = $this->moddb->query("SELECT id, sort FROM cats ORDER BY sort DESC LIMIT 1");
        $row = $rows->fetchArray(SQLITE3_ASSOC);
        return $row['sort'];
    }
    
    function savecat()
    {
        unset($this->post['savecat']);
        
        $fields = $this->fileconfig['cat'];
        
        $filekey = array();
        $filevalue = array();
        
        if(isset($this->files['catimage']) && $this->files['catimage']['name'] != '') {
            $dateityp = $this->files['catimage']['type'];
			if(preg_match("(jpg|jpeg|gif|png)", $dateityp)) {
	    		$catfolder = $this->createunique('cats');
	            $catname = $this->upload($this->files, 'catimage', $catfolder, 'cat');
	            if($catname != '') {
	                $filekey[] = 'catimage';
	                $filevalue[] = serialize($catname);
	            }
            }
        }
		
		$validpost = array();
		foreach($this->post as $key => $post) {
			if(in_array($key, $fields)) {
				if(is_array($post)) {
					$values = array();
					foreach($post as $lang => $val) {
						$values[$lang] = $this->validinput($key, $val);
					}
					$validpost[$key] = serialize($values);
				}
				else {
					$validpost[$key] = $this->validinput($key, $post);
				}
			}
		}
        
        $availkeys = array_keys($validpost);
        $availvalues = array_map('SQLite3::escapeString', array_values($validpost));
        
        $keys = array_merge($filekey, $availkeys);
        $values = array_merge($filevalue, $availvalues);
        
        if(in_array('sort', $fields)) {
            $keys[] = 'sort';
            $values[] = $this->getcatrows()+1;
        }
        if(in_array('onoff', $fields)) {
            $keys[] = 'onoff';
            $values[] = (isset($this->dbconfig['newcreated_cat']) && $this->dbconfig['newcreated_cat'] == 'online' ? '1' : '');
        }
        
        $this->moddb->query('INSERT INTO cats ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')');
        
        header("Location: admin.php?page=cats");
    }
                    
    function updatecat()
    {
        unset($this->post['editcat']);
        $catid = (isset($this->get['cat'])) ? $this->validnum($this->get['cat']) : '';
        
        if($catid != '') {
            $update = array();
			if(in_array('catimage', $this->fileconfig['cat'])) {
				$catdatas = $this->moddb->query("SELECT id, catimage FROM cats WHERE cats.id = '".$catid."' LIMIT 1");
				$catdata = $catdatas->fetchArray();
				$catinfo = unserialize($catdata['catimage']);
				$catfolder = $catinfo[0];
				$catfolderpath = $this->serverpath.'/modules/'.$this->modname.'/media/cats/'.$catfolder;
				if(isset($this->files['catimage']) && $this->files['catimage']['name'] != '') {
					$dateityp = $this->files['catimage']['type'];
					if(preg_match("(jpg|jpeg|gif|png)", $dateityp)) {
						if($catfolder == '' || !file_exists($catfolderpath))
							$catfolder = $this->createunique('cats');
						$catname = $this->upload($this->files, 'catimage', $catfolder, 'cat');
						if($catname != '') {
							$this->deletemediaimage('catimage', $catinfo);
							$update[] = 'catimage = \''.$this->moddb->escapeString(serialize($catname)).'\'';
						}
					}
				}
				else {
					if(isset($this->post['delcatimage'])) {
						$update[] = 'catimage = \'\'';
						if($catfolder != '' && file_exists($catfolderpath)) {
							$this->deletemediafolder('catimage', $catinfo);
						}
					}
				}
			}
            
            if(isset($this->post['delcatimage']))
                unset($this->post['delcatimage']);
            
			$validpost = array();
			foreach($this->post as $key => $post) {
				if(in_array($key, $this->fileconfig['cat'])) {
					if(is_array($post)) {
						$values = array();
						foreach($post as $lang => $val) {
							$values[$lang] = $this->validinput($key, $val);
						}
						$validpost[$key] = serialize($values);
					}
					else {
						$validpost[$key] = $this->validinput($key, $post);
					}
				}
			}
			
            $keys = array_keys($validpost);
            
            foreach($keys as $key) {
            	$update[] = $key.' = \''.$this->moddb->escapeString($validpost[$key]).'\'';
            }
            
            $updatefields = implode(', ', $update);
            $this->moddb->query("UPDATE cats SET ".$updatefields." WHERE cats.id = '".$catid."'");
        }
        
        header("Location: admin.php?page=cats");
    }
    
    function loadcatitemlist()
    {
        if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
            $catlines = '';
            $lang = in_array('lang', $this->fileconfig['cat']) ? ', lang' : '';
            $cats = $this->moddb->query("SELECT id, catname".$lang." FROM cats ORDER BY id");
            if($this->getdbnumrows($cats) >= 1) {
                while($cat = $cats->fetchArray()) {
					if($this->is_serialized($cat['catname'])) {
						$cat_catname = unserialize($cat['catname']);
						$catname = $cat_catname[$this->pagelang];
					}
					else {
		                $catname = $cat['catname'];
	                }
					$flag = '';
					if(isset($cat['lang']) && file_exists($this->serverpath.'/includes/language/icons/'.$cat['lang'].'.png'))
						$flag = '&nbsp; <img src="'.$this->homepage.'/includes/language/icons/'.$cat['lang'].'.png" /> ';
                    $catlines.= '<div class="checknewitem">'."\n".'ID: '.$cat['id'].' <input type="checkbox" name="newitem['.$cat['id'].']" id="newitem_'.$cat['id'].'" /><label for="newitem_'.$cat['id'].'">'.$flag.$catname.'</label>'."\n".'</div>'."\n";
                }
            }
            return $catlines;
        }
    }
    
    function newtosubcats()
    {
        if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
            if(isset($this->post['addnewitems']) && isset($this->post['newitem'])) {
                unset($this->post['addnewitems']);
                
                $subcats = $this->loadsubcatmenu();
                
                $items = $this->post['newitem'];
                
                foreach($items as $catid => $item) {
                    $subcats[] = array('id' => $catid, 'sub' => array());
                }
                
                $this->updatesubcats($subcats);
            }
        }
    }
    
    function updatesubcats($subcats)
    {
        $newsubcats = serialize($subcats);
        $this->moddb->query("UPDATE confs SET subcats = '".$this->moddb->escapeString($newsubcats)."' WHERE confs.id = '1'");
		$this->subcatmenu = $subcats;
    }
    
	function resortsubcats($subs)
	{
		$newsubs = array();
		
	    foreach($subs as $key => $item) {
	    	$newsubs[] = array(
				'id' => $item['id'],
				'sub' => ((count($item['sub']) >= 1) ? $this->resortsubcats($item['sub']) : $item['sub'])
			);
	    }
		
	    return $newsubs;
	}

	function actioneditsubcats($action, $subs, $search)
	{
	    if($action == 'delete') {
			$keys = '';
			$count = count($search);
			for($i = 0; $i < $count; $i++) {
				$keys.= "['".$search[$i]."']";
				if($i < $count-1)
					$keys.= "['sub']";
			}
		    eval("unset(\$subs".$keys.");");
		    $subs = $this->resortsubcats($subs);
		}
	    else if($action == 'posup') {
	    	$save = '';
	    	$before = '';
			$count = count($search);
			for($i = 0; $i < $count; $i++) {
				$save.= "['".$search[$i]."']";
				$before.= ($i == $count-1) ? "['".($search[$i]-1)."']" : "['".$search[$i]."']";
				if($i < $count-1) {
					$save.= "['sub']";
					$before.= "['sub']";
				}
			}
		    eval("
				if(\$search[\$count-1] >= 1) {
					\$setsave = \$subs".$save.";
					\$setbefore = \$subs".$before.";
					\$subs".$before." = \$setsave;
					\$subs".$save." = \$setbefore;
					\$this->movewalkkey = str_replace(array('sub', '[\'', '\']'), array('|', '', ''), \$before);
				}
				else {
					\$this->movewalkkey = \$before;
				}
			");
	    }
	    else if($action == 'posdown') {
	    	$save = '';
	    	$after = '';
			$count = count($search);
			for($i = 0; $i < $count; $i++) {
				$save.= "['".$search[$i]."']";
				$after.= ($i == $count-1) ? "['".($search[$i]+1)."']" : "['".($search[$i])."']";
				if($i < $count-1) {
					$save.= "['sub']";
					$after.= "['sub']";
				}
			}
		    eval("
				if(isset(\$subs".$after.")) {
					if(is_array(\$subs".$after.")) {
						\$setsave = \$subs".$save.";
						\$setafter = \$subs".$after.";
						\$subs".$after." = \$setsave;
						\$subs".$save." = \$setafter;
						\$this->movewalkkey = str_replace(array('sub', '[\'', '\']'), array('|', '', ''), \$after);
					}
					else {
						\$this->movewalkkey = \$after;
					}
				}
			");
	    }
	    else if($action == 'posin') {
	    	$save = '';
	    	$next = '';
			$count = count($search);
			for($i = 0; $i < $count; $i++) {
				$save.= "['".$search[$i]."']";
				$next.= ($i == $count-1) ? "['".($search[$i]+1)."']" : "['".$search[$i]."']";
				if($i < $count-1) {
					$save.= "['sub']";
					$next.= "['sub']";
				}
			}
		    eval("
				if(isset(\$subs".$next.") && is_array(\$subs".$next.")) {
					\$setsave = \$subs".$save.";
					unset(\$subs".$save.");
					array_unshift(\$subs".$next."['sub'], \$setsave);
					\$movewalkkey = str_replace(array('sub', '[\'', '\']'), array('|', '', ''), \$next);
					\$movewalk = explode('|', \$movewalkkey);
					for(\$i = 0; \$i < count(\$movewalk); \$i++) {
						if(\$i == count(\$movewalk)-1)
							\$movewalknew[] = \$movewalk[\$i]-1;
						else
							\$movewalknew[] = \$movewalk[\$i];
					}
					\$this->movewalkkey = implode('|', \$movewalknew).'|0';
				}
			");
		    $subs = $this->resortsubcats($subs);
	    }
	    else if($action == 'posout') {
	    	$save = '';
	    	$prev = '';
			$count = count($search);
			if($count > 1) {
				for($i = 0; $i < $count; $i++) {
					$save.= "['".$search[$i]."']";
					if($i <= $count-3)
						$prev.= "['".$search[$i]."']";
					else
						$prev.= "";
					
					if($i < $count-1) {
						$save.= "['sub']";
						if($i < $count-3)
							$prev.= "['sub']";
					}
				}
			    eval("
					if(\$count >= 2) {
						\$setsave = \$subs".$save.";
						unset(\$subs".$save.");
						if(\$count == 2) {
							array_unshift(\$subs, \$setsave);
						    \$this->movewalkkey = 0;
						}
						else if(\$count >= 3) {
							array_unshift(\$subs".$prev."['sub'], \$setsave);
						    \$this->movewalkkey = str_replace(array('sub', '[\'', '\']'), array('|', '', ''), \$prev).'|0';
						}
					}
				");
			    $subs = $this->resortsubcats($subs);
		    }
	    }
	    
		return $subs;
	}
    
    function getsubsarray($subs, $walkkeys = '')
    {
        if(is_array($subs) && count($subs) >= 1)
        {
            $result = '';
            foreach($subs as $key => $item)
            {
				$lang = in_array('lang', $this->fileconfig['cat']) ? ', lang' : '';
                $cats = $this->moddb->query("SELECT id, catname".$lang." FROM cats WHERE cats.id = '".$item['id']."' LIMIT 1");
                $cat = $cats->fetchArray();
                
                $walkkey = ($walkkeys != '') ? stripslashes($walkkeys).'|'.$key : (string)$key;
                $setload = (isset($this->post['moveitem']) && (string)$this->movewalkkey === $walkkey) ? 1 : 0;
				$jskey = str_replace('|', '_', $walkkey);
				
                $result.= '<div class="listitem">'."\n".'<input type="checkbox" name="moveitem['.$walkkey.']" id="moveitem_'.$jskey.'"';
                $result.= ($setload == 1) ? ' checked="checked"' : '';
                $result.= ' /><label for="moveitem_'.$jskey.'" class="';
                $result.= ($setload == 1) ? 'itemmover' : 'itemunsel';
				if($this->is_serialized($cat['catname'])) {
					$cat_catname = unserialize($cat['catname']);
					$catname = $cat_catname[$this->pagelang];
				}
				else {
		            $catname = $cat['catname'];
				}
				$flag = '';
				if(isset($cat['lang']) && file_exists($this->serverpath.'/includes/language/icons/'.$cat['lang'].'.png')) {
					$flag = '<img src="'.$this->homepage.'/includes/language/icons/'.$cat['lang'].'.png" /> ';
				}
				
				if(strlen($walkkey) == 1 && in_array('basecat', $this->fileconfig['base'])) {
					$result.= '" onclick="setdefitem(this)">'.$flag.$this->admindefine('basecatentry').' - ID: '.$cat['id'].' <span class="catitemid">('.$catname.')</span></label>'."\n".'</div>'."\n";
				}
				else {
					$result.= '" onclick="setdefitem(this)">'.$flag.$catname.' <span class="catitemid">(ID: '.$cat['id'].')</span></label>'."\n".'</div>'."\n";
				}
                
                if(is_array($item['sub']) && count($item['sub']) >= 1) {
                    $result.= '<div style="padding-left: 25px">'."\n";
                    foreach($item as $subkey => $sub) {
                        $result.= $this->getsubsarray($sub, $walkkey);
                    }
                    $result.= '</div>'."\n";
                }
            }
            return $result;
        }
    }
    
    function loadsubcats()
    {
        $subcats = $this->loadsubcatmenu();
        $sortierung = (count($subcats) > 0) ? $this->getsubsarray($subcats) : $this->admindefine('thereisnocat');
        
        return $sortierung;
    }
    
    function savesubcats()
    {
        if((isset($this->post['savesubcats']) || isset($this->post['unsetcatitem_x'])) && isset($this->post['moveitem'])) {
	        if(isset($this->post['savesubcats']))
	            $move = key($this->post['savesubcats']);
	        else if(isset($this->post['unsetcatitem_x']))
	            $move = 'delete';
            
            $subcats = $this->loadsubcatmenu();
            
            $moveitems = explode('|', key($this->post['moveitem']));
            
            $subcats = $this->actioneditsubcats($move, $subcats, $moveitems);
            
            $this->updatesubcats($subcats);
        }
    }
    
    function deletesubcatsitem($id)
    {
        $subcats = $this->loadsubcatmenu();
        if(count($subcats) > 0) {
		    $delsubs = $this->resortsubsdelid($subcats, $id);
		    $this->updatesubcats($delsubs);
        }
    }
    
	function resortsubsdelid($subs, $delid)
	{
		$newsubs = array();
		
	    foreach($subs as $key => $item) {
		    if($item['id'] != $delid) {
		    	$newsubs[] = array(
					'id' => $item['id'],
					'sub' => ((count($item['sub']) >= 1) ? $this->resortsubsdelid($item['sub'], $delid) : $item['sub'])
				);
			}
	    }
		
	    return $newsubs;
	}
    
    function gettopicsheadline()
    {
        $result = '';
        if(isset($this->get['cat']) && array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
	        $catid = $this->validnum($this->get['cat']);
	        $cats = $this->moddb->query("SELECT id, catname FROM cats WHERE cats.id = '".$catid."' LIMIT 1");
            $catrows = $this->getdbnumrows($cats);
            
            if($catrows > 0) {
                $cat = $cats->fetchArray();
                
				if($this->is_serialized($cat['catname'])) {
					$catlangs = unserialize($cat['catname']);
					$catname = $catlangs[$this->pagelang];
				}
				else {
		            $catname = $cat['catname'];
				}
                
    	        $result = ' - '.$catname;
            }
        }
        
        return $result;
    }
    
    function gettopiclist()
    {
        $topiclist = '';
        $field = '';
        $where = '';
        $joinwhere = '';
        
        if(in_array('catid', $this->fileconfig['topic'])) {
            $catid = (isset($this->get['cat'])) ? $this->validnum($this->get['cat']) : '';
            $field.= ', catid';
            $where = " WHERE catid = '".$catid."'";
            $joinwhere = " AND t.catid = '".$catid."'";
        }
        
        
        $loadbydatafield = '';
		$copyselect = '';
		$copyjoin = '';
        if(array_key_exists('sorttopicfield', $this->dbconfig) && $this->dbconfig['sorttopicfield'] != '') {
            if(in_array($this->dbconfig['sorttopicfield'], $this->fileconfig['topic'])) {
                $field.= ', '.$this->dbconfig['sorttopicfield'];
                $order = $this->dbconfig['sorttopicfield'];
				if(in_array('copyof', $this->fileconfig['topic'])) {
					$copyselect = ", COALESCE(copyt.".$this->dbconfig['sorttopicfield'].", t.".$this->dbconfig['sorttopicfield'].") AS ".$this->dbconfig['sorttopicfield'];
					$copyjoin = " LEFT JOIN topics copyt ON copyt.id = t.copyof";
					$copywhere = " WHERE t.catid = '".$catid."'";
				}
            }
            else {
                $loadbydatafield = $this->dbconfig['sorttopicfield'];
                $order = '';
				if(in_array('copyof', $this->fileconfig['topic'])) {
					$copyjoin = " LEFT JOIN topics copyt ON copyt.id = t.copyof";
					$copywhere = " WHERE t.catid = '".$catid."'";
				}
            }
        }
        elseif(in_array('sort', $this->fileconfig['topic'])) {
            $field.= ', sort';
            $order = 'sort';
        }
        else {
            $order = 'id';
        }
        
        $dir = (isset($this->dbconfig['topicsort']) && $this->dbconfig['topicsort'] != '') ? ' '.$this->dbconfig['topicsort'] : '';
        
        $topicsperpage = (isset($this->dbconfig['topicsperpage']) && $this->dbconfig['topicsperpage'] != '') ? $this->dbconfig['topicsperpage'] : '';
        
        $limit = '';
        $checknexttopic = 0;
        $num = (isset($this->get['num'])) ? $this->validnum($this->get['num']) : 1;
        if($topicsperpage != '') {
            if($loadbydatafield != '') {
				if(in_array('copyof', $this->fileconfig['topic'])) {
					$nexttopic = $this->moddb->query("SELECT DISTINCT t.id AS id, t.".implode(', t.', $this->fileconfig['topic']).$copyselect.", d.".$loadbydatafield." FROM topics t".$copyjoin." LEFT JOIN datas d ON d.id = t.startid OR d.id = copyt.startid".$copywhere." ORDER BY d.".$loadbydatafield.$dir.", id".$dir." LIMIT ".($num*$topicsperpage).", 1");
				}
				else {
					$nexttopic = $this->moddb->query("SELECT DISTINCT t.id, t.topic FROM topics t INNER JOIN datas d ON t.startid = d.id".$joinwhere." ORDER BY d.".$loadbydatafield.$dir.", t.id".$dir." LIMIT ".($num*$topicsperpage).", 1");
				}
            }
            else {
				if($copyselect != '') {
					$secondorder = $order != 'id' && $order != 'sort' ? ", t.id".$dir : "";
					$nexttopic = $this->moddb->query("SELECT t.id, t.".implode(', t.', $this->fileconfig['topic']).$copyselect." FROM topics t".$copyjoin.$copywhere." ORDER BY ".$order.$dir.$secondorder." LIMIT ".($num*$topicsperpage).", 1");
				}
				else {
					$secondorder = $order != 'id' && $order != 'sort' ? ", id".$dir : "";
					$nexttopic = $this->moddb->query("SELECT ".$order." FROM topics".$where." ORDER BY ".$order.$dir.$secondorder." LIMIT ".($num*$topicsperpage).", 1");
				}
            }
            $checknexttopic = $this->getdbnumrows($nexttopic);
            $start = $num*$topicsperpage-$topicsperpage;
            
            $limit = " LIMIT ".$start.", ".$topicsperpage;
        }
        
        if($loadbydatafield != '') {
            $fields = implode(', t.', $this->fileconfig['topic']);
			if(in_array('copyof', $this->fileconfig['topic'])) {
				$topics = $this->moddb->query("SELECT DISTINCT t.id AS id, t.".$fields.$copyselect.", d.".$loadbydatafield." FROM topics t LEFT JOIN topics copyt ON copyt.id = t.copyof LEFT JOIN datas d ON d.id = t.startid OR d.id = copyt.startid".$copywhere." ORDER BY d.".$loadbydatafield.$dir.", id".$dir.$limit);
			}
			else {
				$topics = $this->moddb->query("SELECT DISTINCT t.id, t.".$fields." FROM topics t INNER JOIN datas d ON t.startid = d.id".$joinwhere." ORDER BY d.".$loadbydatafield.$dir.", t.id".$dir.$limit);
			}
        }
        else {
			if($copyselect != '') {
				$secondorder = $order != 'id' && $order != 'sort' ? ", t.id".$dir : "";
				$fields = implode(', t.', $this->fileconfig['topic']);
				$topics = $this->moddb->query("SELECT t.id, t.".$fields.$copyselect." FROM topics t".$copyjoin.$copywhere." ORDER BY ".$order.$dir.$secondorder.$limit);
			}
			else {
				$secondorder = $order != 'id' && $order != 'sort' ? ", id".$dir : "";
				$fields = implode(', ', $this->fileconfig['topic']);
				$topics = $this->moddb->query("SELECT id, ".$fields." FROM topics ".$where." ORDER BY ".$order.$dir.$secondorder.$limit);
			}
        }
        $topicnumrows = $this->getdbnumrows($topics);
        
        if($topicnumrows >= 1) {
            $count = 0;
            while($topic = $topics->fetchArray()) {
				$iscopy = false;
				if(in_array('copyof', $this->fileconfig['topic']) && $topic['copyof'] != '') {
					$iscopy = true;
					$orgtopic = $this->moddb->query("SELECT id, catid, topic, onoff FROM topics WHERE id = '".$topic['copyof']."' LIMIT 1");
					$topic_copy = $orgtopic->fetchArray();
					$copyid = $topic_copy['id'];
					$topic['topic'] = $topic_copy['topic'];
				}
                $count = ($count+1);
                $class = ($count % 2 == 0) ? 'even' : 'odd';
                $topiclist.= '<tr class="'.$class.'">
                <td class="start" title="ID: '.$topic['id'].'">';
				if($this->is_serialized($topic['topic'])) {
					$topic_topicname = unserialize($topic['topic']);
					$topicname = $topic_topicname[$this->pagelang];
				}
				else {
		            $topicname = $topic['topic'];
				}
                if(array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
					if($iscopy)
						$topiclist.= $topicname.' <img src="admin/icons/getorg.png" title="'.$this->admindefine('button_getorg').'" class="inp org" onclick="location.href=\'admin.php?page=topics&cat='.$topic_copy['catid'].'&topic='.$topic_copy['id'].'\'" />';
					else
						$topiclist.= '<a href="admin.php?page=datas&cat='.$catid.'&topic='.$topic['id'].'">'.$topicname.'</a>';
				}
                else
                    $topiclist.= '<a href="admin.php?page=datas&topic='.$topic['id'].'">'.$topicname.'</a>';
                $topiclist.= '</td>'."\n";
                $topiclist.= '<td>';
                if(array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
					if($iscopy)
						$topiclist.= '<img src="admin/icons/copy.png" title="'.$this->admindefine('topiccopyof').' '.$copyid.'" />';
					else
						$topiclist.= '<img src="admin/icons/edit.png" title="'.$this->admindefine('button_edit').'" class="inp" onclick="location.href=\'admin.php?page=topics&cat='.$catid.'&topic='.$topic['id'].'\'" />';
				}
                else
                    $topiclist.= '<img src="admin/icons/edit.png" title="'.$this->admindefine('button_edit').'" class="inp" onclick="location.href=\'admin.php?page=topics&topic='.$topic['id'].'\'" />';
                $topiclist.= '<input type="image" class="img" name="delete[topic]['.$topic['id'].']" src="admin/icons/delete.png" title="'.$this->admindefine('button_delete').'" onclick="return confirm(\''.$this->admindefine('prompt_should').($iscopy ? ' '.$this->admindefine('prompt_copy').' ' : ' ').$topicname.' '.$this->admindefine('prompt_realdel').'\')" />';
                if(array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
					if($iscopy)
						$topiclist.= '<img src="admin/icons/blank.png" />';
					else
						$topiclist.= '<input type="image" class="img" name="change[topic]['.$topic['id'].']" src="admin/icons/change.png" title="'.$this->admindefine('button_changeto').'" onclick="return openchangewin(\'cat\')" />';
				}
				$topiclist.= '</td>
                <td';
                if($order != 'sort')
					$topiclist.= ' class="end"';
				$topiclist.= '>';
				/*
				*/
				if($iscopy && !isset($this->dbconfig['topiccopiesonoff'])) {
					$topiclist.= ($topic['onoff'] == 1) ?  '<img src="admin/icons/on.png" title="'.$this->admindefine('button_online').'" />' : '<img src="admin/icons/out.png" />';
					$topiclist.= ($topic['onoff'] == '') ? '<img src="admin/icons/off.png" title="'.$this->admindefine('button_offline').'" />' : '<img src="admin/icons/out.png" />';
				}
				else {
					$topiclist.= ($topic['onoff'] == 1) ?  '<img src="admin/icons/on.png" title="'.$this->admindefine('button_online').'" />' : '<input type="image" class="img" name="on[topic]['.$topic['id'].']" src="admin/icons/out.png" title="'.$this->admindefine('button_seton').'" onmouseover="this.src=\'admin/icons/on.png\'" onmouseout="this.src=\'admin/icons/out.png\'" />';
					$topiclist.= ($topic['onoff'] == '') ? '<img src="admin/icons/off.png" title="'.$this->admindefine('button_offline').'" />' : '<input type="image" class="img" name="off[topic]['.$topic['id'].']" src="admin/icons/out.png" title="'.$this->admindefine('button_setoff').'" onmouseover="this.src=\'admin/icons/off.png\'" onmouseout="this.src=\'admin/icons/out.png\'" />';
				}
                $topiclist.= '</td>'."\n";
                if($order == 'sort') {
                    $topiclist.= '<td class="end">';
                    $topiclist.= ($topic['sort'] == 1 || ($topicnumrows == 1 && $num == 1)) ? '<img src="admin/icons/blank.png" />' : '<input type="image" class="img" name="up[topic]['.$topic['sort'].']" src="admin/icons/up.png" title="'.$this->admindefine('button_hoch').'" />';
                    $topiclist.= ($topic['sort'] == $this->gettopicrows() || ($topicnumrows == 1 && $num == 1)) ? '<img src="admin/icons/blank.png" />' : '<input type="image" class="img" name="down[topic]['.$topic['sort'].']" src="admin/icons/down.png" title="'.$this->admindefine('button_runter').'" />';
                    $topiclist.= '</td>'."\n";
                }
                $topiclist.= '</tr>'."\n";
            }
            
            $topiclist.= '<tr class="bottom">
            <td colspan="';
            $topiclist.= ($order == 'sort') ? '4' : '3';
            $topiclist.= '">';
            if($topicsperpage != '') {
                $numbs = ($checknexttopic == '0') ? $num : $num+1;
                $topiclist.= $this->getlistnav('topics', $num, $numbs);
            }
            else {
                $topiclist.= '&nbsp;';
            }
            $topiclist.= '</td>
            </tr>'."\n";
        }
        return $topiclist;
    }
    
    function gettopic()
    {
        $loadtopic = '';
        
        $topicid = (isset($this->get['topic']) && $this->get['topic'] != '') ? $this->validnum($this->get['topic']) : '';
        if($topicid != '') {
            $topicfields = implode(', ', $this->fileconfig['topic']);
            $datafields = implode(', ', $this->fileconfig['data']);
            
            $topic = $this->moddb->query("SELECT id, ".$topicfields." FROM topics WHERE id = '".$topicid."' LIMIT 1");
            if($this->getdbnumrows($topic) > 0) {
                $topicarray = $topic->fetchArray(SQLITE3_ASSOC);
				if(in_array('copyof', $this->fileconfig['topic']) && $topicarray['copyof'] != '') {
					unset($this->get['topic']);
					$this->get['error'] = 'editcopy';
					header('location: admin.php?'.http_build_query($this->get));
					exit;
				}
                $topiclist = array();
	            foreach($topicarray as $key => $val) {
	                $topiclist[$key] = ($this->is_serialized($val)) ? unserialize($val) : $val;
	            }
                
                $data = $this->moddb->query("SELECT id, ".$datafields." FROM datas WHERE id = '".$topiclist['startid']."' LIMIT 1");
				$topiclist['dataid'] = $topiclist['startid'];
    	        if($this->getdbnumrows($data) > 0) {
                    $dataarray = $data->fetchArray(SQLITE3_ASSOC);
                    $loadtopic = array();
		            foreach($dataarray as $key => $val) {
		            	if($this->searchfieldintypes($key) == 'checkbox') {
			                $loadtopic[$key] = $val;
		            	}
						elseif($this->searchfieldintypes($key) == 'user') {
							$username = '';
							if(is_numeric($val)) {
								$username = $this->getadminnamefromdb($val);
							}
							else {
								if(preg_match('~###~i', $val)) {
									$user = explode('###', $val);
									$username = $user[0].' ('.$this->checkforlangdefine($user[1]).')';
								}
								else {
									$username = $this->checkforlangdefine($val);
								}
							}
							$loadtopic[$key] = ($username != '') ? $username : $this->checkforlangdefine(strtoupper('_'.$this->modname.'lang_unknownuser_'));
						}
		            	else {
			                $loadtopic[$key] = ($this->is_serialized($val)) ? unserialize($val) : $val;
			            }
		            }
                }
                $loadtopic['topic'] = $topiclist['topic'];
                if(in_array('fromtime', $this->fileconfig['topic'])) {
                    $loadtopic['fromtime'] = $topiclist['fromtime'];
                    $loadtopic['totime'] = $topiclist['totime'];
                }
				if(in_array('seotitle', $this->fileconfig['topic'])) {
                    $loadtopic['seotitle'] = $topiclist['seotitle'];
                    $loadtopic['seodesc'] = $topiclist['seodesc'];
                    $loadtopic['seokeys'] = $topiclist['seokeys'];
				}
            }
        }
        return $loadtopic;
    }
    
    function savetopic()
    {
        unset($this->post['savetopic']);
        
        $fields = $this->fileconfig['topic'];
		if(is_array($this->post['topic'])) {
			$topicvals = array();
			foreach($this->post['topic'] as $lang => $val) {
				$topicvals[$lang] = $this->validinput('topic', $val);
			}
			$topic = serialize($topicvals);
		}
		else {
			$topic = $this->validinput('topic', $this->post['topic']);
		}
		
        $keys[] = 'topic';
        $values[] = $this->moddb->escapeString($topic);
		
		if(in_array('seotitle', $fields)) {
			if(is_array($this->post['seotitle'])) {
				$topicseotitlevals = array();
				foreach($this->post['seotitle'] as $lang => $val) {
					$topicseotitlevals[$lang] = $this->validinput('seotitle', $val);
				}
				$topicseotitle = serialize($topicseotitlevals);
			}
			else {
				$topicseotitle = $this->validinput('seotitle', $this->post['seotitle']);
			}
			$keys[] = 'seotitle';
			$values[] = $this->moddb->escapeString($topicseotitle);
			
			if(!in_array('seotitle', $this->fileconfig['data']))
				unset($this->post['seotitle']);
			
			if(is_array($this->post['seodesc'])) {
				$topicseodescvals = array();
				foreach($this->post['seodesc'] as $lang => $val) {
					$topicseodescvals[$lang] = $this->validinput('seodesc', $val);
				}
				$topicseodesc = serialize($topicseodescvals);
			}
			else {
				$topicseodesc = $this->validinput('seodesc', $this->post['seodesc']);
			}
			$keys[] = 'seodesc';
			$values[] = $this->moddb->escapeString($topicseodesc);
			
			if(!in_array('seodesc', $this->fileconfig['data']))
				unset($this->post['seodesc']);
			
			if(is_array($this->post['seokeys'])) {
				$topicseokeysvals = array();
				foreach($this->post['seokeys'] as $lang => $val) {
					$topicseokeysvals[$lang] = $this->validinput('seokeys', $val);
				}
				$topicseokeys = serialize($topicseokeysvals);
			}
			else {
				$topicseokeys = $this->validinput('seokeys', $this->post['seokeys']);
			}
			$keys[] = 'seokeys';
			$values[] = $this->moddb->escapeString($topicseokeys);
			
			if(!in_array('seokeys', $this->fileconfig['data']))
				unset($this->post['seokeys']);
		}
        
        if(in_array('fromtime', $fields)) {
			$fromtime = $this->validinput('fromtime', $this->post['fromtime']);
            $keys[] = 'fromtime';
            $values[] = $this->moddb->escapeString(strtotime($fromtime));
            unset($this->post['fromtime']);
            
			$totime = $this->validinput('totime', $this->post['totime']);
            $keys[] = 'totime';
            $values[] = $this->moddb->escapeString(strtotime($totime));
            unset($this->post['totime']);
		}
        
        if(in_array('lang', $fields)) {
			$lang = $this->validinput('lang', $this->post['lang']);
            $keys[] = 'lang';
            $values[] = $this->moddb->escapeString($lang);
            unset($this->post['lang']);
        }
        
        $this->savedata();
        
        $location = '';
        if(isset($this->get['cat'])) {
            $catid = $this->validnum($this->get['cat']);
            $location = '&cat='.$catid;
            if(in_array('catid', $fields)) {
                $keys[] = 'catid';
                $values[] = $catid;
            }
        }
        if(in_array('sort', $fields)) {
            $keys[] = 'sort';
            $values[] = $this->gettopicrows()+1;
        }
        if(in_array('onoff', $fields)) {
            $keys[] = 'onoff';
			$values[] = (isset($this->dbconfig['newcreated_topic']) && $this->dbconfig['newcreated_topic'] == 'online' ? '1' : '');
        }
        $keys[] = 'startid';
        $values[] = $this->datastartid;
        
        $this->moddb->query('INSERT INTO topics ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')');
        $thistopicid = $this->moddb->lastInsertRowid();
        $this->moddb->query("UPDATE datas SET topicid = '".$thistopicid."' WHERE datas.id = '".$this->datastartid."'");
        
        header("Location: admin.php?page=topics".$location);
    }
    
    function copytopic()
    {
        $fields = $this->fileconfig['topic'];
        $copyof = $this->validnum($this->post['copyof']);
		$location = '';
		$catid = '';
		if(isset($this->get['cat'])) {
			$catid = $this->validnum($this->get['cat']);
			$location = '&cat='.$catid;
		}
        
		if(in_array('copyof', $fields) && $copyof != '' && $catid != '') {
	        $orgtopic = $this->moddb->query("SELECT id, ".implode(', ', $fields)." FROM topics WHERE topics.id = '".$this->moddb->escapeString($copyof)."' LIMIT 1");
            $topicrow = $this->getdbnumrows($orgtopic);
            
            if($topicrow > 0) {
                $topic = $orgtopic->fetchArray();
				if($topic['copyof'] != '') $copyof = $topic['copyof'];
				
				$parent = $topic['catid'];
				if(!$this->getparentlang('cats', $parent, $catid)) {
					$this->get['error'] = 'wronglang';
					header('location: admin.php?'.http_build_query($this->get));
					exit;
				}
				
				$keys[] = 'copyof';
				$values[] = $copyof;
				if(in_array('catid', $fields)) {
					$keys[] = 'catid';
					$values[] = $catid;
				}
				if(in_array('sort', $fields)) {
					$keys[] = 'sort';
					$values[] = $this->gettopicrows()+1;
				}
				if(in_array('onoff', $fields) && !isset($this->dbconfig['topiccopiesonoff'])) {
					$keys[] = 'onoff';
					$values[] = $topic['onoff'];
				}
				
				$this->moddb->query('INSERT INTO topics ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')');
			}
		}
		
		header("Location: admin.php?page=topics".$location);
    }
    
    function updatetopic()
    {
        unset($this->post['edittopic']);
        $topicid = (isset($this->get['topic'])) ? $this->validnum($this->get['topic']) : '';
        
        if($topicid != '') {
			$update = array();
			
            if(isset($this->get['cat'])) {
                $catid = $this->validnum($this->get['cat']);
                $location = '&cat='.$catid;
                if(in_array('catid', $this->fileconfig['topic'])) {
                    $where = " AND topics.catid = '".$catid."'";
                }
            }
            
			if(is_array($this->post['topic'])) {
				$values = array();
				foreach($this->post['topic'] as $lang => $val) {
					$values[$lang] = $this->validinput('topic', $val);
				}
				$topic = serialize($values);
			}
			else {
				$topic = $this->validinput('topic', $this->post['topic']);
			}
            $update[] = 'topic = \''.$this->moddb->escapeString($topic).'\'';
			
			if(in_array('seotitle', $this->fileconfig['topic'])) {
				if(is_array($this->post['seotitle'])) {
					$topicseotitlevals = array();
					foreach($this->post['seotitle'] as $lang => $val) {
						$topicseotitlevals[$lang] = $this->validinput('seotitle', $val);
					}
					$topicseotitle = serialize($topicseotitlevals);
				}
				else {
					$topicseotitle = $this->validinput('seotitle', $this->post['seotitle']);
				}
				$update[] = 'seotitle = \''.$this->moddb->escapeString($topicseotitle).'\'';
				unset($this->post['seotitle']);
				
				if(is_array($this->post['seodesc'])) {
					$topicseodescvals = array();
					foreach($this->post['seodesc'] as $lang => $val) {
						$topicseodescvals[$lang] = $this->validinput('seodesc', $val);
					}
					$topicseodesc = serialize($topicseodescvals);
				}
				else {
					$topicseodesc = $this->validinput('seodesc', $this->post['seodesc']);
				}
				$update[] = 'seodesc = \''.$this->moddb->escapeString($topicseodesc).'\'';
				unset($this->post['seodesc']);
				
				if(is_array($this->post['seokeys'])) {
					$topicseokeysvals = array();
					foreach($this->post['seokeys'] as $lang => $val) {
						$topicseokeysvals[$lang] = $this->validinput('seokeys', $val);
					}
					$topicseokeys = serialize($topicseokeysvals);
				}
				else {
					$topicseokeys = $this->validinput('seokeys', $this->post['seokeys']);
				}
				$update[] = 'seokeys = \''.$this->moddb->escapeString($topicseokeys).'\'';
				unset($this->post['seokeys']);
			}
			
            if(in_array('fromtime', $this->fileconfig['topic'])) {
				$fromtime = $this->validinput('fromtime', $this->post['fromtime']);
                $update[] = 'fromtime = \''.$this->moddb->escapeString(strtotime($fromtime)).'\'';
                unset($this->post['fromtime']);
                
				$totime = $this->validinput('totime', $this->post['totime']);
                $update[] = 'totime = \''.$this->moddb->escapeString(strtotime($totime)).'\'';
                unset($this->post['totime']);
            }
			
			if(in_array('lang', $this->fileconfig['topic'])) {
				$lang = $this->validinput('lang', $this->post['lang']);
				$update[] = 'lang = \''.$this->moddb->escapeString($lang).'\'';
				unset($this->post['lang']);
			}
			
            $this->updatedata();
            
            $updatefields = implode(', ', $update);
            $this->moddb->query("UPDATE topics SET ".$updatefields." WHERE topics.id = '".$topicid."'".$where);
        }
        
        header("Location: admin.php?page=topics".$location);
    }
    
    
    function gettopiclink($kind = '')
    {
		if($kind == '' || ($kind != 'new' && $kind != 'copy')) $kind = 'new';
    	$catpart = '';
        if(isset($this->get['cat']) && array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
	        $catid = $this->validnum($this->get['cat']);
	        $catpart = '&cat='.$catid;
        }
        
        $link = 'admin.php?page=topics'.$catpart.'&topic='.$kind;
        
        return $link;
    }
    
    function getdatasheadline()
    {
        $result = '';
        if(isset($this->get['topic']) && array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic'])) {
	        $topicid = $this->validnum($this->get['topic']);
	        $topics = $this->moddb->query("SELECT id, topic FROM topics WHERE topics.id = '".$topicid."' LIMIT 1");
            $topicrows = $this->getdbnumrows($topics);
            
            if($topicrows > 0) {
                $topic = $topics->fetchArray();
                
				if($this->is_serialized($topic['topic'])) {
					$topiclangs = unserialize($topic['topic']);
					$topicname = $topiclangs[$this->pagelang];
				}
				else {
		            $topicname = $topic['topic'];
				}
                
    	        $result = ' - '.$topicname;
            }
        }
        elseif(isset($this->get['cat']) && array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
	        $catid = $this->validnum($this->get['cat']);
	        $cats = $this->moddb->query("SELECT id, catname FROM cats WHERE cats.id = '".$catid."' LIMIT 1");
            $catrows = $this->getdbnumrows($cats);
            
            if($catrows > 0) {
                $cat = $cats->fetchArray();
                
				if($this->is_serialized($cat['catname'])) {
					$catlangs = unserialize($cat['catname']);
					$catname = $catlangs[$this->pagelang];
				}
				else {
		            $catname = $cat['catname'];
				}
                
    	        $result = ' - '.$catname;
            }
        }
        
        return $result;
    }
    
    function getdatalist()
    {
        $datalist = '';
        $field = '';
	    $topicstartid = '';
        $wheres = array();
        
        $catid = (isset($this->get['cat'])) ? $this->validnum($this->get['cat']) : '';
        $topicid = (isset($this->get['topic'])) ? $this->validnum($this->get['topic']) : '';
        
        if($topicid != '' && array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic']) && in_array('startid', $this->fileconfig['topic'])) {
			$getstartid = $this->moddb->query("SELECT id, startid FROM topics WHERE topics.id = '".$topicid."' LIMIT 1");
			$isstartid = $getstartid->fetchArray();
			$topicstartid = $isstartid['startid'];
	    }
        
        if(in_array('catid', $this->fileconfig['data'])) {
            $field.= ', catid';
            $wheres[] = "catid = '".$catid."'";
        }
        if(in_array('topicid', $this->fileconfig['data'])) {
            $field.= ', topicid';
            $wheres[] = "topicid = '".$topicid."'";
        }
        
		$copyselect = '';
		$copyjoin = '';
        if(array_key_exists('sortdatafield', $this->dbconfig) && $this->dbconfig['sortdatafield'] != '') {
			if(in_array('copyof', $this->fileconfig['data'])) {
				$copyselect = ", COALESCE(copyd.".$this->dbconfig['sortdatafield'].", d.".$this->dbconfig['sortdatafield'].") AS ".$this->dbconfig['sortdatafield'];
				$copyjoin = " LEFT JOIN datas copyd ON copyd.id = d.copyof";
			}
			else {
				$field.= ', '.$this->dbconfig['sortdatafield'];
			}
			$order = $this->dbconfig['sortdatafield'];
        }
        elseif(in_array('sort', $this->fileconfig['data'])) {
            $field.= ', sort';
            $order = 'sort';
        }
        else
            $order = 'id';
		
		if($copyselect != '')
			$where = count($wheres) > 0 ? " WHERE d.".implode(' AND d.', $wheres) : '';
		else
			$where = count($wheres) > 0 ? " WHERE ".implode(' AND ', $wheres) : '';
        
        $dir = (isset($this->dbconfig['datasort']) && $this->dbconfig['datasort'] != '') ? ' '.$this->dbconfig['datasort'] : '';
        $titlefield = (isset($this->dbconfig['title']) && $this->dbconfig['title'] != '') ? $this->dbconfig['title'] : '';
        
        $datasperpage = (isset($this->dbconfig['datasperpage']) && $this->dbconfig['datasperpage'] != '') ? $this->dbconfig['datasperpage'] : '';
        
        $limit = '';
        $checknextdata = '0';
        $num = (isset($this->get['num'])) ? $this->validnum($this->get['num']) : 1;
        if($datasperpage != '') {
            if(array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base']) && (in_array('sort', $this->fileconfig['data']) || $topicstartid != '')) {
				if(in_array('sort', $this->fileconfig['data'])) {
					if($copyselect != '')
						$checkwhere = $where." AND d.sort NOT NULL AND d.sort != ''";
					else
						$checkwhere = $where." AND sort NOT NULL AND sort != ''";
				}
				elseif($topicstartid != '') {
					if($copyselect != '')
						$checkwhere = $where." AND d.id != ".$topicstartid;
					else
						$checkwhere = $where." AND id != ".$topicstartid;
				}
			}
			else {
				$checkwhere = $where;
			}
			
			if($copyselect != '') {
				$secondorder = $order != 'id' && $order != 'sort' ? ", d.id".$dir : "";
				$nextdata = $this->moddb->query("SELECT d.".$order." FROM datas d".$checkwhere." ORDER BY d.".$order.$dir.$secondorder." LIMIT ".($num*$datasperpage).", 1");
			}
			else {
				$secondorder = $order != 'id' && $order != 'sort' ? ", id".$dir : "";
				$nextdata = $this->moddb->query("SELECT ".$order." FROM datas".$checkwhere." ORDER BY ".$order.$dir.$secondorder." LIMIT ".($num*$datasperpage).", 1");
			}
            $checknextdata = $this->getdbnumrows($nextdata);
            $start = $num*$datasperpage-$datasperpage;
            
            $limit = " LIMIT ".$start.", ".$datasperpage;
        }
        
        $fields = implode(', ', $this->fileconfig['data']);
        
        if($num == 1 && $topicstartid != '' && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base']) && in_array('topicid', $this->fileconfig['data'])) {
            $startdatas = $this->moddb->query("SELECT id, ".$fields." FROM datas WHERE id = ".$topicstartid);
            $startdatarows = $this->getdbnumrows($startdatas);
            
            if($startdatarows >= 1) {
                $startdata = $startdatas->fetchArray();
                $datalistcat = ($catid != '') ? '&cat='.$catid : '';
                $datalisttopic = ($topicid != '') ? '&topic='.$topicid : '';
                
				if($this->is_serialized($startdata[$titlefield])) {
					$data_titlefield = unserialize($startdata[$titlefield]);
					$dataname = $data_titlefield[$this->pagelang];
				}
				else {
		            $dataname = $startdata[$titlefield];
				}
                $title = ($dataname != '') ? $dataname : 'ID: '.$startdata['id'];
                $datalist.= '<tr class="first">'."\n";
                $datalist.= '<td class="start" title="ID: '.$startdata['id'].'">'.$title.'</td>'."\n";
                $datalist.= '<td>';
                $datalist.= '<img src="admin/icons/edit.png" title="'.$this->admindefine('button_edit').'" class="inp" onclick="location.href=\'admin.php?page=datas'.$datalistcat.$datalisttopic.'&data='.$startdata['id'].'\'" />';
                $datalist.= '<img src="admin/icons/blank.png" class="img" />';
                if(array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic'])) {
                    $datalist.= '<img src="admin/icons/blank.png" class="img" />';
                }
                else if(array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
                    $datalist.= '<img src="admin/icons/blank.png" class="img" />';
                }
				$datalist.= '</td>'."\n".'<td';
                if($order != 'sort')
					$datalist.= ' class="end"';
				$datalist.= '>';
                $datalist.= ($startdata['onoff'] == 1) ? '<img src="admin/icons/on.png" title="'.$this->admindefine('button_online').'" />' : '<img src="admin/icons/out.png" />';
                $datalist.= ($startdata['onoff'] == '') ? '<img src="admin/icons/off.png" title="'.$this->admindefine('button_offline').'" />' : '<img src="admin/icons/out.png" />';
                $datalist.= '</td>'."\n";
                if($order == 'sort') {
                    $datalist.= '<td class="end">';
                    $datalist.= '<img src="admin/icons/blank.png" />';
                    $datalist.= '<img src="admin/icons/blank.png" />';
                    $datalist.= '</td>'."\n";
                }
                $datalist.= '</tr>'."\n";
            }
        }
        
        $dataswhere = '';
		if(in_array('sort', $this->fileconfig['data']) && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base'])) {
			if($copyselect != '') {
				if($where != '')
					$dataswhere = $where." AND d.sort NOT NULL AND d.sort != ''";
				else
					$dataswhere = " WHERE d.sort NOT NULL AND d.sort != ''";
			}
			else {
				if($where != '')
					$dataswhere = $where." AND sort NOT NULL AND sort != ''";
				else
					$dataswhere = " WHERE sort NOT NULL AND sort != ''";
			}
		}
		else {
			$dataswhere = $where;
		}
		
        $topicstartidwhere = '';
        if(array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base']) && in_array('topicid', $this->fileconfig['data'])) {
			if($copyselect != '')
				$topicstartidwhere = " AND d.id != ".$topicstartid;
			else
				$topicstartidwhere = " AND id != ".$topicstartid;
        }
		
		if($copyselect != '') {
			$secondorder = $order != 'id' && $order != 'sort' ? ", d.id".$dir : "";
			$datas = $this->moddb->query("SELECT d.id, d.".implode(', d.', $this->fileconfig['data']).$copyselect." FROM datas d".$copyjoin.$dataswhere.$topicstartidwhere." ORDER BY ".$order.$dir.$secondorder.$limit);
		}
		else {
			$secondorder = $order != 'id' && $order != 'sort' ? ", id".$dir : "";
			$datas = $this->moddb->query("SELECT id, ".$fields.$field." FROM datas".$dataswhere.$topicstartidwhere." ORDER BY ".$order.$dir.$secondorder.$limit);
		}
        $datanumrows = $this->getdbnumrows($datas);
        
        if($datanumrows >= 1) {
            $count = 0;
            $datalistcat = ($catid != '') ? '&cat='.$catid : '';
            $datalisttopic = ($topicid != '') ? '&topic='.$topicid : '';
            while($data = $datas->fetchArray()) {
				$iscopy = false;
				if(in_array('copyof', $this->fileconfig['data']) && $data['copyof'] != '') {
					$iscopy = true;
					$hassort = in_array('sort', $this->fileconfig['data']) ? true : false;
					$dataid = $data['id'];
					$dataonoff = $data['onoff'];
					if($hassort) $datasort = $data['sort'];
					$orgdata = $this->moddb->query("SELECT id, ".$fields.$field." FROM datas WHERE id = '".$data['copyof']."' LIMIT 1");
					$data_org = $orgdata->fetchArray();
					$orgid = $data_org['id'];
					unset($data_org['id']);
					$data = $data_org;
					$data['id'] = $dataid;
					$data['onoff'] = $dataonoff;
					if($hassort) $data['sort'] = $datasort;
				}
                $count = ($count+1);
                $class = ($count % 2 == 0) ? 'even' : 'odd';
				if($this->is_serialized($data[$titlefield])) {
					$data_titlefield = unserialize($data[$titlefield]);
					$dataname = $data_titlefield[$this->pagelang];
				}
				else {
		            $dataname = $data[$titlefield];
				}
                $title = ($dataname != '') ? $dataname : 'ID: '.$data['id'];
                $datalist.= '<tr class="'.$class.'">'."\n";
                $datalist.= '<td class="start" title="ID: '.$data['id'].'">'.$title;
				if($iscopy) {
					$jumpcatid = '';
					$jumptopicid = '';
					if(in_array('catid', $this->fileconfig['data'])) $jumpcatid = '&cat='.$data['catid'];
					if(in_array('topicid', $this->fileconfig['data'])) {
						$jumptopicid = '&topic='.$data['topicid'];
						if(in_array('catid', $this->fileconfig['topic'])) {
							$getjumpcatid = $this->moddb->query("SELECT id, catid FROM topics WHERE id = '".$data['topicid']."' LIMIT 1");
							$get_jumpcatid = $getjumpcatid->fetchArray();
							$jumpcatid = '&cat='.$get_jumpcatid['catid'];
						}
					}
					$datalist.= ' <img src="admin/icons/getorg.png" title="'.$this->admindefine('button_getorg').'" class="inp org" onclick="location.href=\'admin.php?page=datas'.$jumpcatid.''.$jumptopicid.'&data='.$orgid.'\'" />';
				}
                $datalist.= '</td>'."\n";
                $datalist.= '<td>';
				if($iscopy)
					$datalist.= '<img src="admin/icons/copy.png" title="'.$this->admindefine('datacopyof').' '.$orgid.'" />';
				else
					$datalist.= '<img src="admin/icons/edit.png" title="'.$this->admindefine('button_edit').'" class="inp" onclick="location.href=\'admin.php?page=datas'.$datalistcat.$datalisttopic.'&data='.$data['id'].'\'" />';
                $datalist.= '<input type="image" class="img" name="delete[data]['.$data['id'].']" src="admin/icons/delete.png" title="'.$this->admindefine('button_delete').'" onclick="return confirm(\''.$this->admindefine('prompt_should').($iscopy ? ' '.$this->admindefine('prompt_copy').' ' : ' ').$dataname.' '.$this->admindefine('prompt_realdel').'\')" />';
                if(array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic'])) {
					if($iscopy)
						$datalist.= '<img src="admin/icons/blank.png" />';
					else
						$datalist.= '<input type="image" class="img" name="change[data]['.$data['id'].']" src="admin/icons/change.png" title="'.$this->admindefine('button_changeto').'" onclick="return openchangewin(\'topic\')" />';
				}
                else if(array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
					if($iscopy && !in_array('topicid', $this->fileconfig['data']))
						$datalist.= '<img src="admin/icons/blank.png" />';
					else
						$datalist.= '<input type="image" class="img" name="change[data]['.$data['id'].']" src="admin/icons/change.png" title="'.$this->admindefine('button_changeto').'" onclick="return openchangewin(\'cat\')" />';
				}
				$datalist.= '</td>
                <td';
                if($order != 'sort')
					$datalist.= ' class="end"';
				$datalist.= '>';
				
				if($iscopy && !isset($this->dbconfig['datacopiesonoff'])) {
					$datalist.= ($data['onoff'] == 1) ?  '<img src="admin/icons/on.png" title="'.$this->admindefine('button_online').'" />' : '<img src="admin/icons/out.png" />';
					$datalist.= ($data['onoff'] == '') ? '<img src="admin/icons/off.png" title="'.$this->admindefine('button_offline').'" />' : '<img src="admin/icons/out.png" />';
				}
				else {
					$datalist.= ($data['onoff'] == 1) ? '<img src="admin/icons/on.png" title="'.$this->admindefine('button_online').'" />' : '<input type="image" class="img" name="on[data]['.$data['id'].']" src="admin/icons/out.png" title="'.$this->admindefine('button_seton').'" onmouseover="this.src=\'admin/icons/on.png\'" onmouseout="this.src=\'admin/icons/out.png\'" />';
					$datalist.= ($data['onoff'] == '') ? '<img src="admin/icons/off.png" title="'.$this->admindefine('button_offline').'" />' : '<input type="image" class="img" name="off[data]['.$data['id'].']" src="admin/icons/out.png" title="'.$this->admindefine('button_setoff').'" onmouseover="this.src=\'admin/icons/off.png\'" onmouseout="this.src=\'admin/icons/out.png\'" />';
				}
                $datalist.= '</td>'."\n";
                if($order == 'sort') {
                    $datalist.= '<td class="end">';
                    $datalist.= ($data['sort'] == 1 || ($datanumrows == 1 && $num == 1)) ? '<img src="admin/icons/blank.png" />' : '<input type="image" class="img" name="up[data]['.$data['sort'].']" src="admin/icons/up.png" title="'.$this->admindefine('button_hoch').'" />';
                    $datalist.= ($data['sort'] == $this->getdatarows() || ($datanumrows == 1 && $num == 1)) ? '<img src="admin/icons/blank.png" />' : '<input type="image" class="img" name="down[data]['.$data['sort'].']" src="admin/icons/down.png" title="'.$this->admindefine('button_runter').'" />';
                    $datalist.= '</td>'."\n";
                }
                $datalist.= '</tr>'."\n";
            }
            
            $datalist.= '<tr class="bottom">
            <td colspan="';
            $datalist.= ($order == 'sort') ? '4' : '3';
            $datalist.= '">';
            if($datasperpage != '') {
                $numbs = ($checknextdata == '0') ? $num : $num+1;
                $datalist.= $this->getlistnav('datas', $num, $numbs);
            }
            else {
                $datalist.= '&nbsp;';
            }
            $datalist.= '</td>
            </tr>'."\n";
        }
        return $datalist;
    }
    
    function getdata()
    {
        $loaddata = '';
        
        $dataid = (isset($this->get['data'])) ? $this->validnum($this->get['data']) : '';
        
        if($dataid != '') {
	        $fields = implode(', ', $this->fileconfig['data']);
	        
	        $data = $this->moddb->query("SELECT id, ".$fields." FROM datas WHERE id = '".$dataid."' LIMIT 1");
	        if($this->getdbnumrows($data) >= 1) {
                $dataarray = $data->fetchArray(SQLITE3_ASSOC);
				if(in_array('copyof', $this->fileconfig['data']) && $dataarray['copyof'] != '') {
					unset($this->get['data']);
					$this->get['error'] = 'editcopy';
					header('location: admin.php?'.http_build_query($this->get));
					exit;
				}
                $loaddata = array();
				$loaddata['dataid'] = $dataid;
	            foreach($dataarray as $key => $val) {
	            	if($this->searchfieldintypes($key) == 'checkbox') {
		                $loaddata[$key] = $val;
	            	}
	            	elseif($this->searchfieldintypes($key) == 'user') {
						$username = '';
						if(is_numeric($val)) {
							$username = $this->getadminnamefromdb($val);
						}
						else {
							if(preg_match('~###~i', $val)) {
								$user = explode('###', $val);
								$username = $user[0].' ('.$this->checkforlangdefine($user[1]).')';
							}
							else {
								$username = $this->checkforlangdefine($val);
							}
						}
						$loaddata[$key] = ($username != '') ? $username : $this->checkforlangdefine(strtoupper('_'.$this->modname.'lang_unknownuser_'));
	            	}
	            	else {
		                $loaddata[$key] = ($this->is_serialized($val)) ? unserialize($val) : $val;
		            }
	            }
	        }
        }
        
        return $loaddata;
    }
    
    function savedata()
    {
        if(isset($this->post['savedata'])) {
            unset($this->post['savedata']);
        }
        
        $fields = $this->fileconfig['data'];
        $location = '';
        
        $setstart = 0;
        if(isset($this->post['topic'])) {
            unset($this->post['topic']);
            $setstart = 1;
        }
        
        $filekeys = array();
        $filevalues = array();
        
        $files = $this->getdbfieldfromfile('file');
        if(is_array($files)) {
            foreach($files as $file) {
                if(isset($this->files[$file]) && $this->files[$file]['name'] != '') {
	                $dateityp = $this->files[$file]['type'];
	                $savedtypes = $this->dbconfig['savemime'][$file];
					if($savedtypes == '' || preg_match("(".$savedtypes.")", $dateityp)) {
	            		$filefolder = $this->createunique('files');
	                    $filenames = $this->upload($this->files, $file, $filefolder, 'file');
	                    if($filenames != '') {
	                        $filekeys[] = $file;
	                        $filevalues[] = serialize($filenames);
	                    }
	                }
                }
				elseif(isset($this->post[$file])) {
	                $dateityp = $this->post[$file]['type'];
	                $savedtypes = $this->dbconfig['savemime'][$file];
					if($savedtypes == '' || preg_match("(".$savedtypes.")", $dateityp)) {
	                    $filenames = explode('|', $this->post[$file]['infos']);
	                    if($filenames != '') {
	                        $filekeys[] = $file;
	                        $filevalues[] = serialize($filenames);
	                    }
	                }
					unset($this->post[$file]);
				}
            }
        }
        
        $images = $this->getdbfieldfromfile('image');
        if(is_array($images)) {
            foreach($images as $image) {
                if(isset($this->files[$image]) && $this->files[$image]['name'] != '') {
	                $dateityp = $this->files[$image]['type'];
					if(preg_match("(jpg|jpeg|gif|png)", $dateityp)) {
	            		$imagefolder = $this->createunique('imgs');
	                    $imagenames = $this->upload($this->files, $image, $imagefolder, 'image');
	                    if($imagenames != '') {
	                        $filekeys[] = $image;
	                        $filevalues[] = serialize($imagenames);
	                    }
                    }
                }
            }
        }
        
        $multis = $this->getdbfieldfromfile('multi');
        if(is_array($multis)) {
            foreach($multis as $multi) {
				if(isset($this->files[$multi])) {
					$multicount = count($this->files[$multi]['name']);
					$multifolder = $this->createunique('imgs');
					$multinames = array();
					for($i = 1; $i <= $multicount; $i++) {
						if($this->files[$multi]['name'][$i] != '') {
							$dateityp = $this->files[$multi]['type'][$i];
							if(preg_match("(jpg|jpeg|gif|png)", $dateityp)) {
								$multinames[] = $this->upload($this->files, array($multi, $i), $multifolder, 'multi');
							}
						}
					}
					if(count($multinames) >= '1') {
						$filekeys[] = $multi;
						$filevalues[] = serialize($multinames);
					}
				}
            }
        }
		
		$validpost = array();
		
        $users = $this->getdbfieldfromfile('user');
        if(is_array($users)) {
            foreach($users as $user) {
				if((isset($_SESSION['loggedin']['userisadmin']) && $_SESSION['loggedin']['userisadmin'] == '1') || (isset($_SESSION['loggedin']['userisowner']) && $_SESSION['loggedin']['userisowner'] == '1')) {
					$validpost[$user] = strtoupper('_'.$this->modname.'lang_adminname_');
				}
				elseif(isset($_SESSION['userauth']['userid'])) {
					$validpost[$user] = $this->validinput($user, $_SESSION['userauth']['userid']);
				}
			}
		}
		
		$optionids = array();
		foreach($this->post as $key => $post) {
			if($key == 'optionids') {
				foreach($post as $p => $v)
					$optionids[] = $this->validnum($p);
			}
			else {
				if(in_array($key, $fields)) {
					if(is_array($post) && $this->searchfieldintypes($key) != 'checkbox') {
						$datavals = array();
						foreach($post as $lang => $val) {
							$datavals[$lang] = $this->validinput($key, $val);
						}
						$validpost[$key] = serialize($datavals);
					}
					else {
    					if($this->searchfieldintypes($key) == 'date' || $key == 'fromtime' || $key == 'totime')
    						$validpost[$key] = strtotime($this->validinput($key, $post));
                        else
    						$validpost[$key] = $this->validinput($key, $post);
					}
				}
			}
		}
		
		if(count($optionids) >= 1) {
			$validpost['optionids'] = serialize($optionids);
		}
        
        $availkeys = array_keys($validpost);
        $availvalues = array_map('SQLite3::escapeString', array_values($validpost));
                    
        $keys = array_merge($filekeys, $availkeys);
        $values = array_merge($filevalues, $availvalues);
        
        if(isset($this->get['cat'])) {
            $catid = $this->validnum($this->get['cat']);
            $location.= '&cat='.$catid;
            if(in_array('catid', $fields)) {
                $keys[] = 'catid';
                $values[] = $catid;
            }
        }
        if(isset($this->get['topic'])) {
            $topicid = $this->validnum($this->get['topic']);
            $location.= '&topic='.$topicid;
            if(in_array('topicid', $fields)) {
                $keys[] = 'topicid';
                $values[] = $topicid;
            }
        }
        if(in_array('sort', $fields)) {
            $keys[] = 'sort';
            $values[] = ($setstart == 1 && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base'])) ? '' : $this->getdatarows()+1;
        }
        if(in_array('onoff', $fields)) {
            $keys[] = 'onoff';
            if($setstart == 1) {
				$values[] = (isset($this->dbconfig['newcreated_topic']) && $this->dbconfig['newcreated_topic'] == 'online' ? '1' : '');
			}
			else {
				$values[] = (isset($this->dbconfig['newcreated_data']) && $this->dbconfig['newcreated_data'] == 'online' ? '1' : '');
			}
        }
        
        $this->moddb->query('INSERT INTO datas ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')');
        
        if($setstart == 1)
            $this->datastartid = $this->moddb->lastInsertRowid();
        else
            header("Location: admin.php?page=datas".$location);
    }
    
    function copydata()
    {
        $fields = $this->fileconfig['data'];
        $copyof = $this->validnum($this->post['copyof']);
		$location = '';
		$topicid = '';
		if(isset($this->get['topic'])) {
			$topicid = $this->validnum($this->get['topic']);
			$location.= '&topic='.$topicid;
		}
		$catid = '';
		if(isset($this->get['cat'])) {
			$catid = $this->validnum($this->get['cat']);
			$location.= '&cat='.$catid;
		}
        
		if(in_array('copyof', $fields) && $copyof != '' && ($catid != '' || $topicid != '')) {
	        $orgdata = $this->moddb->query("SELECT id, ".implode(', ', $fields)." FROM datas WHERE datas.id = '".$this->moddb->escapeString($copyof)."' LIMIT 1");
            $datarow = $this->getdbnumrows($orgdata);
            
            if($datarow > 0) {
                $data = $orgdata->fetchArray();
				if($data['copyof'] != '') {
					$copyof = $data['copyof'];
					
					$orgdata = $this->moddb->query("SELECT id, ".implode(', ', $fields)." FROM datas WHERE datas.id = '".$this->moddb->escapeString($copyof)."' LIMIT 1");
					$datarow = $this->getdbnumrows($orgdata);
					
					if($datarow > 0) $data = $orgdata->fetchArray();
				}
				
				$isdist = false;
				if($topicid != '') {
					$parenttopic = $this->moddb->query("SELECT id, startid FROM topics WHERE topics.id = '".$data['topicid']."' LIMIT 1");
					$topicrow = $this->getdbnumrows($parenttopic);
					if($topicrow > 0) {
						$topic = $parenttopic->fetchArray();
						if($topic['startid'] == $copyof && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base']) && file_exists($this->serverpath.'/modules/'.$this->modname.'/view/tpls/topicslist_startdata.tpl')) {
							$location.= '&error=copydist';
							$isdist = true;
						}
					}
				}
				
				$table = array_key_exists('topicid', $data) ? 'topics' : 'cats';
				$oldparent = array_key_exists('topicid', $data) ? $data['topicid'] : $data['catid'];
				$newparent = array_key_exists('topicid', $data) ? $topicid : $catid;
				if(!$this->getparentlang($table, $oldparent, $newparent)) {
					$this->get['error'] = 'wronglang';
					header('location: admin.php?'.http_build_query($this->get));
					exit;
				}
				
				if(!$isdist) {
					$keys[] = 'copyof';
					$values[] = $copyof;
					if(in_array('catid', $fields)) {
						$keys[] = 'catid';
						$values[] = $catid;
					}
					if(in_array('topicid', $fields)) {
						$keys[] = 'topicid';
						$values[] = $topicid;
					}
					if(in_array('sort', $fields)) {
						$keys[] = 'sort';
						$values[] = $this->getdatarows()+1;
					}
					if(in_array('onoff', $fields) && !isset($this->dbconfig['datacopiesonoff'])) {
						$keys[] = 'onoff';
						$values[] = $data['onoff'];
					}
					
					$this->moddb->query('INSERT INTO datas ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')');
				}
			}
		}
		
		header("Location: admin.php?page=datas".$location);
    }
    
    function updatedata()
    {
        $location = '';
        
        if(isset($this->get['cat'])) {
            $catid = $this->validnum($this->get['cat']);
            $location.= '&cat='.$catid;
        }
        if(isset($this->get['topic'])) {
            $topicid = $this->validnum($this->get['topic']);
            $location.= '&topic='.$topicid;
        }
        
        if(isset($this->post['editdata'])) {
            unset($this->post['editdata']);
            $dataid = (isset($this->get['data'])) ? $this->validnum($this->get['data']) : '';
        }
        else {
            unset($this->post['topic']);
            $datas = $this->moddb->query("SELECT id, startid FROM topics WHERE topics.id = '".$topicid."' LIMIT 1");
            $list = $datas->fetchArray();
            $selectid = $list['id'];
            $dataid = $list['startid'];
        }
        
        if($dataid != '') {
            $update = array();
            
            $files = $this->getdbfieldfromfile('file');
            if(is_array($files)) {
                foreach($files as $file) {
                    $filedatas = $this->moddb->query("SELECT id, ".$file." FROM datas WHERE datas.id = '".$dataid."' LIMIT 1");
                    $filedata = $filedatas->fetchArray();
                    $fileinfo = unserialize($filedata[$file]);
                	$filefolder = $fileinfo[0];
                	$filefolderpath = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$filefolder;
                    if(isset($this->files[$file]) && $this->files[$file]['name'] != '') {
			            $dateityp = $this->files[$file]['type'];
		                $savedtypes = $this->dbconfig['savemime'][$file];
						if($savedtypes == '' || preg_match("(".$savedtypes.")", $dateityp)) {
	                		if($filefolder == '' || !file_exists($filefolderpath))
	                    		$filefolder = $this->createunique('files');
	                        $filenames = $this->upload($this->files, $file, $filefolder, 'file');
	                        if($filenames != '') {
	                            $this->deletemediafile($fileinfo);
	                            $update[] = $file.' = \''.$this->moddb->escapeString(serialize($filenames)).'\'';
	                        }
	                    }
                    }
                    else {
                        if(isset($this->post['del'.$file])) {
                            $update[] = $file.' = \'\'';
                            $this->deletemediafile($fileinfo);
                            if($filefolder != '' && file_exists($filefolderpath)) {
                                rmdir($filefolderpath);
                            }
                        }
                    }
                    if(isset($this->post['del'.$file]))
                        unset($this->post['del'.$file]);
                }
            }
            
            $images = $this->getdbfieldfromfile('image');
            if(is_array($images)) {
                foreach($images as $image) {
                    $imagedatas = $this->moddb->query("SELECT id, ".$image." FROM datas WHERE datas.id = '".$dataid."' LIMIT 1");
                    $imagedata = $imagedatas->fetchArray();
                    $imageinfo = unserialize($imagedata[$image]);
                	$imagefolder = $imageinfo[0];
                	$imagefolderpath = $this->serverpath.'/modules/'.$this->modname.'/media/imgs/'.$imagefolder;
                    if(isset($this->files[$image]) && $this->files[$image]['name'] != '') {
			            $dateityp = $this->files[$image]['type'];
						if(preg_match("(jpg|jpeg|gif|png)", $dateityp)) {
	                		if($imagefolder == '' || !file_exists($imagefolderpath))
	                    		$imagefolder = $this->createunique('imgs');
	                        $imagenames = $this->upload($this->files, $image, $imagefolder, 'image');
	                        if($imagenames != '') {
	                            $this->deletemediaimage($image, $imageinfo);
	                            $update[] = $image.' = \''.$this->moddb->escapeString(serialize($imagenames)).'\'';
	                        }
	                    }
                    }
                    else {
                        if(isset($this->post['del'.$image])) {
                            $update[] = $image.' = \'\'';
                            if($imagefolder != '' && file_exists($imagefolderpath)) {
                                $this->deletemediafolder($image, $imageinfo);
                            }
                        }
                    }
                    if(isset($this->post['del'.$image]))
                        unset($this->post['del'.$image]);
                }
            }
            
            $multis = $this->getdbfieldfromfile('multi');
            if(is_array($multis)) {
                foreach($multis as $multi) {
                    $multidatas = $this->moddb->query("SELECT id, ".$multi." FROM datas WHERE datas.id = '".$dataid."' LIMIT 1");
                    $multidata = $multidatas->fetchArray();
                    $multiinfo = unserialize($multidata[$multi]);
                	$multifolderpath = $this->serverpath.'/modules/'.$this->modname.'/media/imgs/'.$multiinfo[0][0];
            		if($multiinfo[0][0] == '' || !file_exists($multifolderpath))
                		$multifolder = $this->createunique('imgs');
                	else
                    	$multifolder = $multiinfo[0][0];
                    $multinames = array();
                    $infocount = count($multiinfo);
                    for($i = 0; $i < $infocount; $i++) {
                        if(isset($this->post['del'.$multi.'_'.$i])) {
                            $this->deletemediaimage($multi, $multiinfo[$i]);
                            unset($multiinfo[$i]);
                            unset($this->post['del'.$multi.'_'.$i]);
                        }
                        else {
                            if($multiinfo[$i] != '')
                                $multinames[] = $multiinfo[$i];
                        }
                    }
					if(isset($this->files[$multi])) {
						$multicount = count($this->files[$multi]['name']);
						for($k = 1; $k <= $multicount; $k++) {
							if($this->files[$multi]['name'][$k] != '') {
								$dateityp = $this->files[$multi]['type'][$k];
								if(preg_match("(jpg|jpeg|gif|png)", $dateityp)) {
									$multinames[] = $this->upload($this->files, array($multi, $k), $multifolder, 'multi');
								}
							}
						}
					}
                    
                    if(count($multinames) >= '1' && $multinames[0] != '') {
                        $update[] = $multi.' = \''.$this->moddb->escapeString(serialize($multinames)).'\'';
                    }
                    else {
                        if(isset($this->post['del'.$multi])) {
                            unset($this->post['del'.$multi]);
                        }
                        $update[] = $multi.' = \'\'';
                        if($multiinfo[0][0] != '' && file_exists($multifolderpath)) {
                            rmdir($multifolderpath);
                        }
                    }
                }
            }
            
			$validpost = array();
			$optionids = array();
			foreach($this->post as $key => $post) {
				if($key == 'optionids') {
					foreach($post as $p => $v)
						$optionids[] = $this->validnum($p);
				}
				else {
					if(in_array($key, $this->fileconfig['data'])) {
						if(is_array($post) && $this->searchfieldintypes($key) != 'checkbox') {
							$values = array();
							foreach($post as $lang => $val) {
								$values[$lang] = $this->validinput($key, $val);
							}
							$validpost[$key] = serialize($values);
						}
						else {
        					if($this->searchfieldintypes($key) == 'date' || $key == 'fromtime' || $key == 'totime')
        						$validpost[$key] = strtotime($this->validinput($key, $post));
                            else
    							$validpost[$key] = $this->validinput($key, $post);
						}
					}
				}
			}
	        
            $checkboxes = $this->getdbfieldfromfile('checkbox');
            if(is_array($checkboxes)) {
                foreach($checkboxes as $checkbox) {
                    if(!isset($validpost[$checkbox]))
                        $validpost[$checkbox] = '';
                }
            }
			
			if(in_array('optionids', $this->fileconfig['data'])) {
    			$validpost['optionids'] = (count($optionids) >= 1) ? serialize($optionids) : '';
            }
            
            $keys = array_keys($validpost);
            foreach($keys as $key) {
                $update[] = $key.' = \''.$this->moddb->escapeString($validpost[$key]).'\'';
            }
            
            $updatefields = implode(', ', $update);
            $this->moddb->query("UPDATE datas SET ".$updatefields." WHERE datas.id = '".$dataid."'");
        }
        
        if(!isset($selectid))
            header("Location: admin.php?page=datas".$location);
    }
    
    function getdatalink($kind = '')
    {
		if($kind == '' || ($kind != 'new' && $kind != 'copy')) $kind = 'new';
    	$catpart = '';
        if(isset($this->get['cat']) && array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
	        $catid = $this->validnum($this->get['cat']);
	        $catpart = '&cat='.$catid;
        }
        
    	$topicpart = '';
        if(isset($this->get['topic']) && array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic'])) {
	        $topicid = $this->validnum($this->get['topic']);
	        $topicpart = '&topic='.$topicid;
        }
        
        $link = 'admin.php?page=datas'.$catpart.$topicpart.'&data='.$kind;
        
        return $link;
    }
	
	function getusername()
	{
		$username = '';
		
		if(isset($_SESSION['userauth']['username']))
			$username = $_SESSION['userauth']['username'];
		else
			$username = $this->checkforlangdefine(strtoupper('_'.$this->modname.'lang_adminname_'));
		
		if($username == '')
			$username = $this->checkforlangdefine(strtoupper('_'.$this->modname.'lang_unknownuser_'));
		
		return $username;
	}
    
    
    function online()
    {
        $key = key($this->post['on']);
        if($key == 'cat' || $key == 'topic' || $key == 'data') {
            $id = key($this->post['on'][$key]);
            $id = $this->validnum($id);
            $this->moddb->query("UPDATE ".$key."s SET onoff = '1' WHERE ".$key."s.id = '".$id."'");
			
            if(in_array('copyof', $this->fileconfig[$key]) && (($key == 'topic' && !isset($this->dbconfig['topiccopiesonoff'])) || ($key == 'data' && !isset($this->dbconfig['datacopiesonoff'])))) {
				$this->moddb->query("UPDATE ".$key."s SET onoff = '1' WHERE ".$key."s.copyof = '".$id."'");
			}
            
            if($key == 'topic' && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base'])) {
                $topics = $this->moddb->query("SELECT id, startid FROM topics WHERE topics.id = '".$id."' LIMIT 1");
                $topic = $topics->fetchArray();
                $startid = $topic['startid'];
                $this->moddb->query("UPDATE datas SET onoff = '1' WHERE datas.id = '".$startid."'");
            }
        }
    }
    
    function offline()
    {
        $key = key($this->post['off']);
        if($key == 'cat' || $key == 'topic' || $key == 'data') {
            $id = key($this->post['off'][$key]);
            $id = $this->validnum($id);
            $this->moddb->query("UPDATE ".$key."s SET onoff = '' WHERE ".$key."s.id = '".$id."'");
			
            if(in_array('copyof', $this->fileconfig[$key]) && (($key == 'topic' && !isset($this->dbconfig['topiccopiesonoff'])) || ($key == 'data' && !isset($this->dbconfig['datacopiesonoff'])))) {
				$this->moddb->query("UPDATE ".$key."s SET onoff = '' WHERE ".$key."s.copyof = '".$id."'");
			}
            
            if($key == 'topic' && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base'])) {
                $topics = $this->moddb->query("SELECT id, startid FROM topics WHERE topics.id = '".$id."' LIMIT 1");
                $topic = $topics->fetchArray();
                $startid = $topic['startid'];
                $this->moddb->query("UPDATE datas SET onoff = '' WHERE datas.id = '".$startid."'");
            }
        }
    }
    
    function posup()
    {
        $key = key($this->post['up']);
        if($key == 'option' || $key == 'cat' || $key == 'topic' || $key == 'data') {
            if($key == 'option') $lastpos = $this->getoptionrows()+1;
            if($key == 'cat') $lastpos = $this->getcatrows()+1;
            if($key == 'topic') $lastpos = $this->gettopicrows()+1;
            if($key == 'data') $lastpos = $this->getdatarows()+1;
            $where = '';
            if(isset($this->get['cat']) && in_array('catid', $this->fileconfig[$key])) {
                $catid = $this->validnum($this->get['cat']);
                $where.= " AND catid = '".$catid."'";
            }
            if(isset($this->get['topic']) && in_array('topicid', $this->fileconfig[$key])) {
                $topicid = $this->validnum($this->get['topic']);
                $where.= " AND topicid = '".$topicid."'";
            }
            
            $pos = key($this->post['up'][$key]);
            $pos = $this->validnum($pos);
            
            $this->moddb->query("UPDATE ".$key."s SET sort = '".$lastpos."' WHERE ".$key."s.sort = '".($pos-1)."'".$where);
            $this->moddb->query("UPDATE ".$key."s SET sort = '".($pos-1)."' WHERE ".$key."s.sort = '".$pos."'".$where);
            $this->moddb->query("UPDATE ".$key."s SET sort = '".$pos."' WHERE ".$key."s.sort = '".$lastpos."'".$where);
        }
    }
    
    function posdown()
    {
        $key = key($this->post['down']);
        if($key == 'option' || $key == 'cat' || $key == 'topic' || $key == 'data') {
            if($key == 'option') $lastpos = $this->getoptionrows()+1;
            if($key == 'cat') $lastpos = $this->getcatrows()+1;
            if($key == 'topic') $lastpos = $this->gettopicrows()+1;
            if($key == 'data') $lastpos = $this->getdatarows()+1;
            $where = '';
            if(isset($this->get['cat']) && in_array('catid', $this->fileconfig[$key])) {
                $catid = $this->validnum($this->get['cat']);
                $where.= " AND catid = '".$catid."'";
            }
            if(isset($this->get['topic']) && in_array('topicid', $this->fileconfig[$key])) {
                $topicid = $this->validnum($this->get['topic']);
                $where.= " AND topicid = '".$topicid."'";
            }
            
            $pos = key($this->post['down'][$key]);
            $pos = $this->validnum($pos);
            
            $this->moddb->query("UPDATE ".$key."s SET sort = '".$lastpos."' WHERE ".$key."s.sort = '".($pos+1)."'".$where);
            $this->moddb->query("UPDATE ".$key."s SET sort = '".($pos+1)."' WHERE ".$key."s.sort = '".$pos."'".$where);
            $this->moddb->query("UPDATE ".$key."s SET sort = '".$pos."' WHERE ".$key."s.sort = '".$lastpos."'".$where);
        }
    }
    
    function delete()
    {
        $key = key($this->post['delete']);
        if($key == 'option' || $key == 'cat' || $key == 'topic' || $key == 'data') {
            $id = key($this->post['delete'][$key]);
            $id = $this->validnum($id);
        
            $fields = array();
            $fields = $this->getdbfieldfromfile('file', $fields);
            $fields = $this->getdbfieldfromfile('image', $fields);
            $fields = $this->getdbfieldfromfile('multi', $fields);
            $mediafields = (is_array($fields) && count($fields) >= 1) ? ','.implode(',', $fields) : '';
            $pos = '';
            
            if($key == 'option') {
                $info = $this->moddb->query("SELECT id, sort FROM options WHERE options.id = '".$id."' LIMIT 1");
                $option = $info->fetchArray();
	            $pos = $option['sort'];
	            
                $this->moddb->query("DELETE FROM options WHERE options.id = '".$id."'");
            }
            
            if($key == 'cat') {
            	$dbfields = $this->getcatdbfields();
                $catsinfo = $this->moddb->query("SELECT id,".$dbfields." FROM cats WHERE cats.id = '".$id."' LIMIT 1");
                $catinfo = $catsinfo->fetchArray();
	            if(in_array('sort', $this->fileconfig[$key])) {
	            	$pos = $catinfo['sort'];
	            }
                if(array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat']) && in_array('catimage', $this->fileconfig['cat'])) {
                    $catfields = unserialize($catinfo['catimage']);
                    $this->deletemediafolder('catimage', $catfields);
                }
                if(array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic'])) {
                    $topicsinfo = $this->moddb->query("SELECT id, catid FROM topics WHERE topics.catid = '".$id."'");
                    
                    while($topicinfo = $topicsinfo->fetchArray()) {
                        $datasinfo = $this->moddb->query("SELECT id, topicid".$mediafields." FROM datas WHERE datas.topicid = '".$topicinfo['id']."'");
                        while($datainfo = $datasinfo->fetchArray()) {
                            foreach($fields as $field) {
                                if(in_array($field, $this->fileconfig['data'])) {
                                    $datafields = unserialize($datainfo[$field]);
                                    $filefields = $this->getdbfieldfromfile('file');
                                    if(is_array($filefields) && in_array($field, $filefields)) {
                                        $this->deletemediafile($datafields);
                                    	$filefolder = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$datafields[0];
                                        if($datafields[0] != '' && file_exists($filefolder)) {
                                            rmdir($filefolder);
                                        }
                                    }
                                    else {
                                        $this->deletemediafolder($field, $datafields);
                                    }
                                }
                            }
                            $this->moddb->query("DELETE FROM datas WHERE datas.id = '".$datainfo['id']."'");
							
							if(in_array('copyof', $this->fileconfig['data'])) {
								$sort = in_array('sort', $this->fileconfig['data']) ? ', sort' : '';
								$datacopies = $this->moddb->query("SELECT id, topicid, copyof".$sort." FROM datas WHERE datas.copyof = '".$datainfo['id']."'");
								$count = $this->getdbnumrows($datacopies);
								if($count != 0) {
									while($datacopy = $datacopies->fetchArray()) {
										$this->deletecopy('data', $datacopy);
									}
								}
							}
                        }
                        $this->moddb->query("DELETE FROM topics WHERE topics.id = '".$topicinfo['id']."'");
				
						if(in_array('copyof', $this->fileconfig['topic'])) {
							$sort = in_array('sort', $this->fileconfig['topic']) ? ', sort' : '';
							$topiccopies = $this->moddb->query("SELECT id, catid, copyof".$sort." FROM topics WHERE topics.copyof = '".$topicinfo['id']."'");
							$count = $this->getdbnumrows($topiccopies);
							if($count != 0) {
								while($topiccopy = $topiccopies->fetchArray()) {
									$this->deletecopy('topic', $topiccopy);
								}
							}
						}
                    }
                }
                else {
                    $datasinfo = $this->moddb->query("SELECT id, catid".$mediafields." FROM datas WHERE datas.catid = '".$id."'");
                    while($datainfo = $datasinfo->fetchArray()) {
                        foreach($fields as $field) {
                            if(in_array($field, $this->fileconfig['data'])) {
                                $datafields = unserialize($datainfo[$field]);
                                $filefields = $this->getdbfieldfromfile('file');
                                if(is_array($filefields) && in_array($field, $filefields)) {
                                    $this->deletemediafile($datafields);
                                	$filefolder = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$datafields[0];
                                    if($datafields[0] != '' && file_exists($filefolder)) {
                                        rmdir($filefolder);
                                    }
                                }
                                else {
                                    $this->deletemediafolder($field, $datafields);
                                }
                            }
                        }
                        $this->moddb->query("DELETE FROM datas WHERE datas.id = '".$datainfo['id']."'");
						
						if(in_array('copyof', $this->fileconfig['data'])) {
							$sort = in_array('sort', $this->fileconfig['data']) ? ', sort' : '';
							$datacopies = $this->moddb->query("SELECT id, catid, copyof".$sort." FROM datas WHERE datas.copyof = '".$datainfo['id']."'");
							$count = $this->getdbnumrows($datacopies);
							if($count != 0) {
								while($datacopy = $datacopies->fetchArray()) {
									$this->deletecopy('data', $datacopy);
								}
							}
						}
                    }
                }
                $this->moddb->query("DELETE FROM cats WHERE cats.id = '".$id."'");
                $this->deletesubcatsitem($id);
            }
            
            if($key == 'topic') {
                $topicsinfo = $this->moddb->query("SELECT id,".implode(',', $this->fileconfig[$key])." FROM topics WHERE topics.id = '".$id."' LIMIT 1");
                $topicinfo = $topicsinfo->fetchArray();
	            if(in_array('sort', $this->fileconfig[$key])) {
	            	$pos = $topicinfo['sort'];
	            }
                $datasinfo = $this->moddb->query("SELECT id, topicid".$mediafields." FROM datas WHERE datas.topicid = '".$id."'");
                while($datainfo = $datasinfo->fetchArray()) {
                    foreach($fields as $field) {
                        if(in_array($field, $this->fileconfig['data'])) {
                            $datafields = unserialize($datainfo[$field]);
                            $filefields = $this->getdbfieldfromfile('file');
                            if(is_array($filefields) && in_array($field, $filefields)) {
                                $this->deletemediafile($datafields);
                            	$filefolder = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$datafields[0];
                                if($datafields[0] != '' && file_exists($filefolder)) {
                                    rmdir($filefolder);
                                }
                            }
                            else {
                                $this->deletemediafolder($field, $datafields);
                            }
                        }
                    }
                    $this->moddb->query("DELETE FROM datas WHERE datas.id = '".$datainfo['id']."'");
					
					if(in_array('copyof', $this->fileconfig['data'])) {
						$sort = in_array('sort', $this->fileconfig['data']) ? ', sort' : '';
						$datacopies = $this->moddb->query("SELECT id, topicid, copyof".$sort." FROM datas WHERE datas.copyof = '".$datainfo['id']."'");
						$count = $this->getdbnumrows($datacopies);
						if($count != 0) {
							while($datacopy = $datacopies->fetchArray()) {
								$this->deletecopy('data', $datacopy);
							}
						}
					}
                }
                $this->moddb->query("DELETE FROM topics WHERE topics.id = '".$id."'");
				
	            if(in_array('copyof', $this->fileconfig[$key]) && in_array('catid', $this->fileconfig[$key])) {
					$sort = in_array('sort', $this->fileconfig[$key]) ? ', sort' : '';
					$topiccopies = $this->moddb->query("SELECT id, catid, copyof".$sort." FROM topics WHERE topics.copyof = '".$id."'");
					$count = $this->getdbnumrows($topiccopies);
					if($count != 0) {
						while($topiccopy = $topiccopies->fetchArray()) {
							$this->deletecopy('topic', $topiccopy);
						}
					}
	            }
            }
            
            if($key == 'data') {
                $datasinfo = $this->moddb->query("SELECT id,".implode(',', $this->fileconfig[$key])." FROM datas WHERE datas.id = '".$id."' LIMIT 1");
                $datainfo = $datasinfo->fetchArray();
	            if(in_array('sort', $this->fileconfig[$key])) {
	            	$pos = $datainfo['sort'];
	            }
                foreach($fields as $field) {
                    $datafields = unserialize($datainfo[$field]);
                    $filefields = $this->getdbfieldfromfile('file');
                    if(is_array($filefields) && in_array($field, $filefields)) {
                        $this->deletemediafile($datafields);
                    	$filefolder = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$datafields[0];
                        if($datafields[0] != '' && file_exists($filefolder)) {
                            rmdir($filefolder);
                        }
                    }
                    else {
                        $this->deletemediafolder($field, $datafields);
                    }
                }
                $this->moddb->query("DELETE FROM datas WHERE datas.id = '".$id."'");
				$this->newtopicstartid($id);
				
	            if(in_array('copyof', $this->fileconfig[$key]) && (in_array('catid', $this->fileconfig[$key]) || in_array('topicid', $this->fileconfig[$key]))) {
					$sort = in_array('sort', $this->fileconfig[$key]) ? ', sort' : '';
					$catid = in_array('catid', $this->fileconfig[$key]) ? ', catid' : '';
					$topicid = in_array('topicid', $this->fileconfig[$key]) ? ', topicid' : '';
					$datacopies = $this->moddb->query("SELECT id".$catid.$topicid.", copyof".$sort." FROM datas WHERE datas.copyof = '".$id."'");
					$count = $this->getdbnumrows($datacopies);
					if($count != 0) {
						while($datacopy = $datacopies->fetchArray()) {
							$this->deletecopy('data', $datacopy);
						}
					}
	            }
            }
            
            if($pos != '') {
			    $changesort = array($key => $pos);
	            $this->fillemptypos($changesort);
            }
        }
    }
    
    function deletecopy($field, $copy)
    {
        $this->moddb->query("DELETE FROM ".$field."s WHERE ".$field."s.id = '".$copy['id']."'");
        if(in_array('sort', $this->fileconfig[$field])) {
            $pos = $copy['sort'];
			$where = '';
			if(array_key_exists('topicid', $copy))
				$where.= " AND topicid = '".$copy['topicid']."'";
			if(array_key_exists('catid', $copy))
				$where.= " AND catid = '".$copy['catid']."'";
        
            $counts = $this->moddb->query("SELECT id, sort FROM ".$field."s WHERE ".$field."s.sort > '".$pos."'".$where);
			$count = $this->getdbnumrows($counts);
            if($count != 0) {
                $newpos = $pos;
                for($i = 0; $i <= $count; $i++) {
                    $this->moddb->query("UPDATE ".$field."s SET sort = '".($newpos-1)."' WHERE sort = '".$newpos."'".$where);
                    $newpos = ($newpos+1);
                }
            }
        }
    }
    
    function newtopicstartid($id)
    {
        if(isset($this->fileconfig['topic'])) {
			$hascopy = in_array('copyof', $this->fileconfig['topic']) ? true : false;
			$copyfield = $hascopy ? ', copyof' : '';
			$copyof = $hascopy ? " AND TRIM(topics.copyof) = ''" : '';
			
            $topicinfo = $this->moddb->query("SELECT id, startid".$copyfield." FROM topics WHERE topics.startid = '".$id."'".$copyof." LIMIT 1");
            if($this->getdbnumrows($topicinfo) > 0) {
				$sort = '';
				$order = '';
				if(in_array('sort', $this->fileconfig['data'])) {
					$sort = ', sort';
					$order = ' ORDER BY sort';
				}
                while($topic = $topicinfo->fetchArray()) {
					$datainfo = $this->moddb->query("SELECT id, topicid".$sort." FROM datas WHERE datas.topicid = '".$topic['id']."'".$order." LIMIT 1");
					if($this->getdbnumrows($datainfo) == 0) {
						$this->moddb->query("DELETE FROM topics WHERE topics.id = '".$topic['id']."'");
						if($hascopy)
							$this->moddb->query("DELETE FROM topics WHERE topics.copyof = '".$topic['id']."'");
					}
					else {
						$data = $datainfo->fetchArray();
						$this->moddb->query("UPDATE topics SET startid = '".$data['id']."' WHERE topics.id = '".$topic['id']."'");
						if($hascopy)
							$this->moddb->query("UPDATE topics SET startid = '".$data['id']."' WHERE topics.copyof = '".$topic['id']."'");
					}
				}
            }
        }
    }
    
    function deletemediafolder($type, $array)
    {
        if($type == 'catimage') $sub = 'cats';
        else $sub = 'imgs';
        
        if(is_array($array[0])) {
            foreach($array as $arr) {
                $this->deletemediaimage($type, $arr);
                $folderpath = $this->serverpath.'/modules/'.$this->modname.'/media/'.$sub.'/'.$arr[0];
                if($arr[0] != '' && file_exists($folderpath)) {
                    rmdir($folderpath);
                }
            }
        }
        else {
            $this->deletemediaimage($type, $array);
            $folderpath = $this->serverpath.'/modules/'.$this->modname.'/media/'.$sub.'/'.$array[0];
            if($array[0] != '' && file_exists($folderpath)) {
                rmdir($folderpath);
            }
        }
    }
    
    function deletemediaimage($type, $arr)
    {
        if($type == 'catimage') $sub = 'cats';
        else $sub = 'imgs';
        
        $imgsize = $this->fileconfig['imgsize'][$type];
        $sizes = array_keys($imgsize);
        
		foreach($sizes as $size) {
            $filepath = $this->serverpath.'/modules/'.$this->modname.'/media/'.$sub.'/'.$arr[0].'/'.$arr[1].'_'.$size.'.'.$arr[2];
            if($arr[0] != '' && file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }
    
    function deletemediafile($arr)
    {
        $filepath = $this->serverpath.'/modules/'.$this->modname.'/media/files/'.$arr[0].'/'.$arr[2].'.'.$arr[3];
        if($arr[0] != '' && file_exists($filepath)) {
            unlink($filepath);
        }
    }
    
    function fillemptypos($changesort)
    {
        $key = key($changesort);
        if($key == 'option' || (($key == 'cat' || $key == 'topic' || $key == 'data') && in_array('sort', $this->fileconfig[$key]))) {
            $pos = $changesort[$key];
            $where = '';
            
            if(isset($this->get['cat']) && in_array('catid', $this->fileconfig[$key])) {
                $catid = $this->validnum($this->get['cat']);
                $where.= " AND catid = '".$catid."'";
            }
            if(isset($this->get['topic']) && in_array('topicid', $this->fileconfig[$key])) {
                $topicid = $this->validnum($this->get['topic']);
                $where.= " AND topicid = '".$topicid."'";
            }
        
            $counts = $this->moddb->query("SELECT id, sort FROM ".$key."s WHERE ".$key."s.sort > '".$pos."'".$where);
			$count = $this->getdbnumrows($counts);
            if($count != 0) {
                $newpos = $pos;
                for($i = 0; $i <= $count; $i++) {
                    $this->moddb->query("UPDATE ".$key."s SET sort = '".($newpos-1)."' WHERE sort = '".$newpos."'".$where);
                    $newpos = ($newpos+1);
                }
            }
        }
    }
    
    function changeto()
    {
        $new = $this->validnum($this->post['changeto']);
        $key = key($this->post['change']);
		
        if($new != '' && (
		  ($key == 'data' && array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic']) && isset($this->get['topic']) && $new != $this->validnum($this->get['topic'])) || 
		  (($key == 'topic' || $key == 'data') && array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat']) && isset($this->get['cat']) && $new != $this->validnum($this->get['cat']))
		)) {
        	if($key == 'data' && isset($this->get['topic']) && array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic']))
        		$table = 'topics';
        	else if(($key == 'topic' || $key == 'data') && isset($this->get['cat']) && array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat']))
        		$table = 'cats';
			
			$copyof = $table == 'topics' && in_array('copyof', $this->fileconfig['topic']) ? ', copyof' : '';
			$gettable = $this->moddb->query("SELECT id".$copyof." FROM ".$table." WHERE id = '".$new."' LIMIT 1");
            $id = key($this->post['change'][$key]);
            $id = $this->validnum($id);
			
			$old = $table == 'topics' ? $this->validnum($this->get['topic']) : $this->validnum($this->get['cat']);
			
			if(!$this->getparentlang($table, $old, $new)) {
				$this->get['error'] = 'wronglang';
				header('location: admin.php?'.http_build_query($this->get));
				exit;
			}
			
	        if($this->getdbnumrows($gettable) > 0) {
				$checkcopy = $gettable->fetchArray();
				if(array_key_exists('copyof', $checkcopy) && $checkcopy['copyof'] != '') {
					$this->get['error'] = 'changetocopy';
					header('location: admin.php?'.http_build_query($this->get));
					exit;
				}
				if($id != '') {
					$oldsort = array();
					$newsort = '';
					if($key == 'topic') {
						if(array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic']) && in_array('sort', $this->fileconfig['topic'])) {
							$getoldsort = $this->moddb->query("SELECT id, sort FROM topics WHERE id = '".$id."'");
							$oldsort = $getoldsort->fetchArray();
							$rows = $this->moddb->query("SELECT id FROM topics WHERE catid = '".$new."'");
							$newsort = ', sort = \''.($this->getdbnumrows($rows)+1).'\'';
						}
						$this->moddb->query("UPDATE topics SET catid = '".$new."'".$newsort." WHERE id = '".$id."'");
					}
					else {
						if(array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic'])) {
							$getstartid = $this->moddb->query("SELECT id, startid FROM topics WHERE startid = '".$id."' LIMIT 1");
							if($this->getdbnumrows($getstartid) != 0) {
								$this->moddb->query("DELETE FROM topics WHERE topics.id = '".$this->validnum($this->get['topic'])."'");
								$redirect = 'admin.php?page=datas';
								$redirect.= (isset($this->get['cat']) && $this->validnum($this->get['cat']) != '') ? '&cat='.$this->validnum($this->get['cat']) : '';
								$redirect.= '&topic='.$new;
							}
							if(in_array('sort', $this->fileconfig['data'])) {
								$getoldsort = $this->moddb->query("SELECT id, sort FROM datas WHERE id = '".$id."'");
								$oldsort = $getoldsort->fetchArray();
								$rows = $this->moddb->query("SELECT id FROM datas WHERE topicid = '".$new."'");
								$newsort = ', sort = \''.($this->getdbnumrows($rows)+1).'\'';
							}
							$this->moddb->query("UPDATE datas SET topicid = '".$new."'".$newsort." WHERE id = '".$id."'");
						}
						elseif(array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
							if(in_array('sort', $this->fileconfig['data'])) {
								$getoldsort = $this->moddb->query("SELECT id, sort FROM datas WHERE id = '".$id."'");
								$oldsort = $getoldsort->fetchArray();
								$rows = $this->moddb->query("SELECT id FROM datas WHERE catid = '".$new."'");
								$newsort = ', sort = \''.($this->getdbnumrows($rows)+1).'\'';
							}
							$this->moddb->query("UPDATE datas SET catid = '".$new."'".$newsort." WHERE id = '".$id."'");
						}
					}
					if(array_key_exists('sort', $oldsort)) {
						$changesort = array($key => $oldsort['sort']);
						$this->fillemptypos($changesort);
					}
					if(isset($redirect)) {
						header("Location: ".$redirect);
					}
				}
			}
        }
    }
    
    function istopicstartid()
    {
		if($this->get['data'] == 'new')
			return false;
		
		$dataid = $this->validnum($this->get['data']);
		
		if($dataid != '') {
			$datas = $this->moddb->query("SELECT d.id AS id, d.topicid, t.id AS tid, t.startid FROM datas d LEFT JOIN topics t ON t.id = d.topicid WHERE d.id = '".$dataid."' LIMIT 1");
			$data = $datas->fetchArray();
			return $data['id'] == $data['startid'] ? true : false;
		}
		return true;
    }
    
    function getparentlang($table, $old, $new)
    {
		if($table == 'cats' && array_key_exists('cat', $this->fileconfig) && in_array('lang', $this->fileconfig['cat'])) {
			$getoldlang = $this->moddb->query("SELECT id, lang FROM cats WHERE id = '".$old."' LIMIT 1");
			$getnewlang = $this->moddb->query("SELECT id, lang FROM cats WHERE id = '".$new."' LIMIT 1");
	        if($this->getdbnumrows($getoldlang) > 0 && $this->getdbnumrows($getnewlang) > 0) {
				$oldlang = $getoldlang->fetchArray();
				$newlang = $getnewlang->fetchArray();
				return $oldlang['lang'] == $newlang['lang'] ? true : false;
			}
		}
		else {
			if(array_key_exists('cat', $this->fileconfig) && in_array('lang', $this->fileconfig['cat'])) {
				$oldcats = $this->moddb->query("SELECT id, catid FROM topics WHERE id = '".$old."' LIMIT 1");
				$newcats = $this->moddb->query("SELECT id, catid FROM topics WHERE id = '".$new."' LIMIT 1");
				if($this->getdbnumrows($oldcats) > 0 && $this->getdbnumrows($newcats) > 0) {
					$oldcat = $oldcats->fetchArray();
					$newcat = $newcats->fetchArray();
					$oldcatid = $oldcat['catid'];
					$newcatid = $newcat['catid'];
					
					$getoldlang = $this->moddb->query("SELECT id, lang FROM cats WHERE id = '".$oldcatid."' LIMIT 1");
					$getnewlang = $this->moddb->query("SELECT id, lang FROM cats WHERE id = '".$newcatid."' LIMIT 1");
					if($this->getdbnumrows($getoldlang) > 0 && $this->getdbnumrows($getnewlang) > 0) {
						$oldlang = $getoldlang->fetchArray();
						$newlang = $getnewlang->fetchArray();
						return $oldlang['lang'] == $newlang['lang'] ? true : false;
					}
				}
			}
			elseif(array_key_exists('topic', $this->fileconfig) && in_array('lang', $this->fileconfig['topic'])) {
				$getoldlang = $this->moddb->query("SELECT id, lang FROM topics WHERE id = '".$old."' LIMIT 1");
				$getnewlang = $this->moddb->query("SELECT id, lang FROM topics WHERE id = '".$new."' LIMIT 1");
				if($this->getdbnumrows($getoldlang) > 0 && $this->getdbnumrows($getnewlang) > 0) {
					$oldlang = $getoldlang->fetchArray();
					$newlang = $getnewlang->fetchArray();
					return $oldlang['lang'] == $newlang['lang'] ? true : false;
				}
			}
		}
		return true;
    }
}


/*************** Viewfunctions ***************/

class MMViewClass extends MMConnectClass
{
	var $metadata = array();
	var $datafilter = '';
	var $topicstartid = '';
    var $pager = array();
    var $catorder = '';
	var $menudeforder = false;
    var $curcat = '';
	var $lightbox_count = 1;
    var $userisadmin = false;
    var $postalert = '';
    var $postback = array();
    var $posttopicstart;
    var $postdatastartid;
	
    
    function viewdefine($define)
    {
        return constant(strtoupper('_'.$this->modname.'lang_'.$define.'_'));
    }
    
    function validorder($order)
    {
    	$order = preg_replace("/[^0-9_]/", "", $order);
    	return $order;
    }
    
    function getusernamefromid($userid)
    {
		$username = $this->viewdefine('unknownuser');
		
        if(file_exists($this->serverpath.'/content/access/users.db')) {
            $userdb = new SQLite3($this->serverpath.'/content/access/users.db');
            $users = $userdb->query("SELECT id, uname, status FROM users WHERE id = ".$userid." LIMIT 1");
            if($this->getdbnumrows($users) > 0) {
                $user = $users->fetchArray();
				if($user['status'] == 'enabled') {
					$username = $user['uname'];
                }
            }
        }
        
        return $username;
    }
    
    function setviewpager($pager, $next)
    {
        $cat = '';
        if(isset($this->get[$this->params['cat']]))
            $cat = '&amp;'.$this->params['cat'].'='.$this->validnum($this->get[$this->params['cat']]);
        if(isset($this->get[$this->params['order']]))
            $cat = '&amp;'.$this->params['order'].'='.$this->validorder($this->get[$this->params['order']]);
		
		$filter = '';
		if(isset($this->get['filter']) && $this->get['filter'] == 'query')
            $filter = '&amp;filter=query';
        
        $topic = '';
        if(isset($this->get[$this->params['topic']]))
            $topic = '&amp;'.$this->params['topic'].'='.$this->validnum($this->get[$this->params['topic']]);
        
        $prev = (($pager-1) > 0) ? 'index.php?page='.$this->getpage.$cat.$topic.$filter.'&amp;pager='.($pager-1) : '';
        $page = $pager;
        $next = ($next != 0) ? 'index.php?page='.$this->getpage.$cat.$topic.$filter.'&amp;pager='.($pager+1) : '';
        
        $this->pager = array(
            'prev' => $prev,
            'page' => $page,
            'next' => $next,
        );
    }
    
	function getpagernavi()
	{
		$pagerfile = $this->tplpath.'/pager.tpl';
		
		if(!file_exists($pagerfile) || ($this->pager['prev'] == '' && $this->pager['next'] == ''))
		{
			$pager = '';
		}
		else
		{
			ob_start();
			include $pagerfile;
			$pager = ob_get_contents();
			ob_end_clean();
			
			if($this->pager['prev'] != '') {
				$pager = str_replace('###BACK_PAGE###', $this->pager['prev'], $pager);
			}
			else {
				$pager = preg_replace('~<a(?:([^>]+))?(href=\"###BACK_PAGE###\")(?:([^>]+))?'.'>([^<]+)</a>~Umsi', '<span class="'.$this->modname.'pager">${4}</span>', $pager);
			}
			
			$pager = str_replace('###THIS_PAGE###', $this->pager['page'], $pager);
			
			if($this->pager['next'] != '') {
				$pager = str_replace('###NEXT_PAGE###', $this->pager['next'], $pager);
			}
			else {
				$pager = preg_replace('~<a(?:([^>]+))?(href=\"###NEXT_PAGE###\")(?:([^>]+))?'.'>([^<]+)</a>~Umsi', '<span class="'.$this->modname.'pager">${4}</span>', $pager);
			}
		}
        
        return $pager;
    }
	
	function bbcodereplace($string)
	{
		return preg_replace_callback('{\[(\w+)((=)(.+)|())\]((.|\n)*)\[/\1\]}Usi', array($this, 'bbcodecallback'), $string);
	}
	
	function bbcodecallback($matches)
	{
		$tag = trim($matches[1]);
		$inner_string = $matches[6];
		$argument = $matches[4];
		
		switch($tag)
		{
			case 'b':
				$replacement = '<strong>'.$inner_string.'</strong>';
				break;

			case 'i':
				$replacement = '<em>'.$inner_string.'</em>';
				break;

			case 'u':
				$replacement = '<u>'.$inner_string.'</u>';
				break;

			case 'color':
				$color = preg_match("[^[0-9a-fA-F]{3,6}$]", $argument) ? '#'.$argument : $argument;
				$replacement = '<span style="color: '.$color.'">'.$inner_string.'</span>';
				break;

			case 'img':
				$imgwidth = $this->fileconfig['imgsize']['bbcode']['width'];
				$imgstyle = '';
				if($imgwidth != '') {
					$unit = substr($imgwidth, -1) == '%' ? '%' : 'px';
					$imgwidth = preg_replace("/[^0-9]/", "", $imgwidth).$unit;
					$imgstyle = ' style="width: '.$imgwidth.'"';
				}
				$imgalt = strtolower(basename($inner_string));
				$imgalt = substr($imgalt, 0, strrpos($imgalt, '.'));
				$imgalt = str_replace(array('.', '-', '_'), ' ', $imgalt);
				$replacement = '<img src="'.$inner_string.'" alt="'.ucwords($imgalt).'"'.$imgstyle.' />';
				break;

			case 'quote':
				$replacement = '<blockquote>'.$inner_string.'</blockquote>';
				break;

			case 'url':
				$url = $argument ? $argument : $inner_string;
				$replacement = '<a href="'.$url.'" target="_blank">'.$inner_string.'</a>';
				break;

			case 'email':
				$address = $argument ? $argument : $inner_string;
				$replacement = '<a href="mailto: '.$address.'">'.$inner_string.'</a>';
				break;

			default:    // unknown
				$replacement = '['.$tag.']'.$inner_string.'[/'.$tag.']';
				break;
		}

		return $replacement;
	}
	
    function getcurrenturl()
    {
		$url = array();
		
		$url['cat'] = isset($this->get[$this->params['cat']]) && $this->get[$this->params['cat']] != '' ? '&amp;'.$this->params['cat'].'='.$this->validnum($this->get[$this->params['cat']]) : '';
		
		$url['order'] = isset($this->get[$this->params['order']]) && $this->get[$this->params['order']] != '' ? '&amp;'.$this->params['order'].'='.$this->validorder($this->get[$this->params['order']]) : '';
		
		$url['topic'] = isset($this->get[$this->params['topic']]) && $this->get[$this->params['topic']] != '' ? '&amp;'.$this->params['topic'].'='.$this->validnum($this->get[$this->params['topic']]) : '';
		
		$url['data'] = isset($this->get[$this->params['data']]) && $this->get[$this->params['data']] != '' ? '&amp;'.$this->params['data'].'='.$this->validnum($this->get[$this->params['data']]) : '';
		
		return $url;
	}
    
    function loadmodule()
    {
		$maintpl = $this->tplpath.'/main.tpl';
		
		if(array_key_exists('base', $this->fileconfig) && in_array('basecat', $this->fileconfig['base']) && $this->basecat == '') {
			$result = $this->viewdefine('nobasecat').': $_MMVAR[\''.$this->modname.'basecat\']';
		}
		else {
			if(isset($this->get['filter']) && $this->get['filter'] == 'reset') {
				$_SESSION[$this->modname.'datafilter'] = '';
				unset($_SESSION[$this->modname.'datafilter']);
			}
			
			if($this->datafilter != '' && is_array($this->datafilter) && count($this->datafilter) > 0) {
				$datafilter = array();
				if(array_key_exists('where', $this->datafilter)) {
					$datafilter['where'] = str_replace('"', '\'', $this->datafilter['where']);
				}
				if(array_key_exists('order', $this->datafilter)) {
					$filterorder = preg_replace("/[^a-z0-9]/", "", strtolower($this->datafilter['order']));
					$filterorder = trim($filterorder);
					if(in_array($filterorder, $this->fileconfig['data']))
						$datafilter['order'] = $filterorder;
				}
				if(array_key_exists('direction', $this->datafilter)) {
					$filterdir = strtoupper($this->datafilter['direction']);
					$filterdir = trim($filterdir);
					if($filterdir == 'ASC' || $filterdir == 'DESC')
						$datafilter['dir'] = $filterdir;
				}
				if(count($datafilter) > 0)
					$_SESSION[$this->modname.'datafilter'] = $datafilter;
				
				header('location: '.$_SERVER['REQUEST_URI']);
			}
			
			if(isset($this->get[$this->params['load']]) && ($this->get[$this->params['load']] == $this->params['topic'].'new' || $this->get[$this->params['load']] == $this->params['data'].'new')) {
				if($this->get[$this->params['load']] == $this->params['topic'].'new') {
					$result = $this->getnewtopic();
				}
				if($this->get[$this->params['load']] == $this->params['data'].'new') {
					$result = $this->getnewdata();
				}
			}
			elseif(isset($this->get[$this->params['data']])) {
				//Data
				$result = $this->getviewdata();
			}
			elseif(isset($this->get[$this->params['topic']])) {
				//Topic
				$result = $this->getviewtopic();
			}
			elseif(($this->basecat != '' && !isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])) || isset($this->get[$this->params['cat']]) || (isset($this->get[$this->params['order']]) && !isset($this->dbconfig['catonlymenu']))) {
				if(array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['cat'])) {
					//Topiclist
					$result = $this->getviewtopiclist();
				}
				else {
					//Datalist
					$result = $this->getviewdatalist();
				}
			}
			else {
				/* Root */
				if(array_key_exists('cat', $this->fileconfig) && is_array($this->fileconfig['cat']) && !isset($this->dbconfig['catonlymenu'])) {
					//Categorylist
					$catlist = $this->getviewcatlist();
					if(is_array($catlist)) {
						$result = $this->setviewcattotpl($catlist);
					}
					else {
						$result = $catlist;
					}
				}
				elseif(array_key_exists('topic', $this->fileconfig) && is_array($this->fileconfig['topic'])) {
					//Topiclist
					if(isset($this->dbconfig['catonlymenu']) && (!isset($this->get[$this->params['order']]) || $this->get[$this->params['order']] == '')) {
						if($this->basecat != '')
							$this->get[$this->params['order']] = $this->getorderfrombasecat($this->basecat);
						else
							$this->get[$this->params['order']] = '0';
					}
					$result = $this->getviewtopiclist();
				}
				elseif(array_key_exists('data', $this->fileconfig) && is_array($this->fileconfig['data'])) {
					//Datalist
					if(isset($this->dbconfig['catonlymenu']) && (!isset($this->get[$this->params['order']]) || $this->get[$this->params['order']] == '')) {
						if($this->basecat != '')
							$this->get[$this->params['order']] = $this->getorderfrombasecat($this->basecat);
						else
							$this->get[$this->params['order']] = '0';
					}
					$result = $this->getviewdatalist();
				}
				else {
					//Data
					$result = $this->getviewdata();
				}
				
				$this->loadmetadata();
			}
			
			$this->setseoindexing();
		}
    	
		ob_start();
		include $maintpl;
		$main = ob_get_contents();
		ob_end_clean();
		
    	$main = str_replace('###MAIN###', $result, $main);
        return $main;
    }
    
    function loadmetadata()
    {
		$viewdefinetitle = $this->viewdefine('metadata_title');
		$viewdefinedescription = $this->viewdefine('metadata_description');
		$viewdefinekeywords = $this->viewdefine('metadata_keywords');
		if($viewdefinetitle != '')
			$this->metadata['title'] = $viewdefinetitle;
		if($viewdefinedescription != '')
			$this->metadata['description'] = $viewdefinedescription;
		if($viewdefinekeywords != '')
			$this->metadata['keywords'] = $viewdefinekeywords;
    }
    
    function getbreadcrumb()
    {
		$breadcrumb = '';
		$breadcrumbfile = $this->serverpath.'/modules/'.$this->modname.'/view/tpls/breadcrumb.tpl';
		
        if(isset($this->get[$this->params['order']]) && file_exists($breadcrumbfile)) {
			$crumbs = array();
			$deforder = $this->validorder($this->get[$this->params['order']]);
			$deftopic = isset($this->get[$this->params['topic']]) ? $this->validnum($this->get[$this->params['topic']]) : '';
			$defdata = isset($this->get[$this->params['data']]) ? $this->validnum($this->get[$this->params['data']]) : '';
			
			$subs = $this->loadsubcatmenu();
			if(count($subs) > 0) {
				$getorders = explode('_', $deforder);
				$catorder = array();
				
				ob_start();
				include $breadcrumbfile;
				$breadcrumbtpl = ob_get_contents();
				ob_end_clean();

				foreach($getorders as $getorder) {
					$catorder[] = $this->validnum($getorder);
					
					$curcat = "[".implode("]['sub'][", $catorder)."]['id']";
					eval("\$catid = \$subs".$curcat.";");
					
					$cats = $this->moddb->query("SELECT id, catname FROM cats WHERE id = '".$catid."' LIMIT 1");
					$catnumrows = $this->getdbnumrows($cats);
					
					if($catnumrows > 0) {
						$crumbtpl = $breadcrumbtpl;
						$cat = $cats->fetchArray();
						$link = 'index.php?page='.$this->getpage.'&amp;'.$this->params['order'].'='.implode('_', $catorder);
						if($this->is_serialized($cat['catname'])) {
							$catlangs = unserialize($cat['catname']);
							$name = $catlangs[$this->pagelang];
						}
						else {
							$name = $cat['catname'];
						}
						
						$crumbtpl = str_replace('###LINK_BREADCRUMB###', $link, $crumbtpl);
						$crumbtpl = str_replace('###NAME_BREADCRUMB###', $name, $crumbtpl);
						$crumbs[] = $crumbtpl;
					}
				}
				
				if($deftopic != '') {
					$copyof = in_array('copyof', $this->fileconfig['topic']) ? ', copyof' : '';
					$topics = $this->moddb->query("SELECT id, topic".$copyof." FROM topics WHERE id = '".$deftopic."' LIMIT 1");
					$topicnumrows = $this->getdbnumrows($topics);
					
					if($topicnumrows > 0) {
						$crumbtpl = $breadcrumbtpl;
						$topic = $topics->fetchArray();
						if(in_array('copyof', $this->fileconfig['topic']) && $topic['copyof'] != '') {
							$topicid = $topic['id'];
							$topics = $this->moddb->query("SELECT id, topic FROM topics WHERE id = '".$topic['copyof']."' LIMIT 1");
							$topic = $topics->fetchArray();
							$topic['id'] = $topicid;
						}
						$link = 'index.php?page='.$this->getpage.'&amp;'.$this->params['order'].'='.implode('_', $catorder).'&amp;'.$this->params['topic'].'='.$deftopic;
						if($this->is_serialized($topic['topic'])) {
							$topiclangs = unserialize($topic['topic']);
							$name = $topiclangs[$this->pagelang];
						}
						else {
							$name = $topic['topic'];
						}
						
						$crumbtpl = str_replace('###LINK_BREADCRUMB###', $link, $crumbtpl);
						$crumbtpl = str_replace('###NAME_BREADCRUMB###', $name, $crumbtpl);
						$crumbs[] = $crumbtpl;
					}
				}
				
				if($defdata != '') {
					$headline = $this->dbconfig['title'];
					$copyof = in_array('copyof', $this->fileconfig['data']) ? ', copyof' : '';
					$datas = $this->moddb->query("SELECT id, ".$headline.$copyof." FROM datas WHERE id = '".$defdata."' LIMIT 1");
					$datanumrows = $this->getdbnumrows($datas);
					
					if($datanumrows > 0) {
						$crumbtpl = $breadcrumbtpl;
						$data = $datas->fetchArray();
						if(in_array('copyof', $this->fileconfig['data']) && $data['copyof'] != '') {
							$dataid = $data['id'];
							$datas = $this->moddb->query("SELECT id, ".$headline." FROM datas WHERE id = '".$data['copyof']."' LIMIT 1");
							$data = $datas->fetchArray();
							$data['id'] = $dataid;
						}
						$link = 'index.php?page='.$this->getpage.'&amp;'.$this->params['order'].'='.implode('_', $catorder);
						if($deftopic != '')
							$link.= '&amp;'.$this->params['topic'].'='.$deftopic;
						$link.= '&amp;'.$this->params['data'].'='.$defdata;
						if($this->is_serialized($data[$headline])) {
							$datalangs = unserialize($data[$headline]);
							$name = $datalangs[$this->pagelang];
						}
						else {
							$name = $data[$headline];
						}
						
						$crumbtpl = str_replace('###LINK_BREADCRUMB###', $link, $crumbtpl);
						$crumbtpl = str_replace('###NAME_BREADCRUMB###', $name, $crumbtpl);
						$crumbs[] = $crumbtpl;
					}
				}
			}
			
			if(count($crumbs) > 0) {
				$breadcrumb = implode($this->viewdefine('breadcrumb_flow'), $crumbs);
			}
		}
		
		return $breadcrumb;
    }
	
	function setseoindexing()
	{
		$catorder = isset($this->get[$this->params['order']]) ? $this->validorder($this->get[$this->params['order']]) : '';
		$catid = isset($this->get[$this->params['cat']]) ? $this->validnum($this->get[$this->params['cat']]) : '';
		$topicid = isset($this->get[$this->params['topic']]) ? $this->validnum($this->get[$this->params['topic']]) : '';
		$dataid = isset($this->get[$this->params['data']]) ? $this->validnum($this->get[$this->params['data']]) : '';
		$robots = '';
		$url = array();
		
		if($dataid != '') {
			$copyof = in_array('copyof', $this->fileconfig['data']) ? ', copyof' : '';
			$parenttopicid = in_array('topicid', $this->fileconfig['data']) ? ', topicid' : '';
			$parentcatid = in_array('catid', $this->fileconfig['data']) ? ', catid' : '';
			$datas = $this->moddb->query("SELECT id".$copyof." FROM datas WHERE id = '".$dataid."' LIMIT 1");
			$data = $datas->fetchArray(SQLITE3_ASSOC);
			if(in_array('copyof', $this->fileconfig['data']) && $data['copyof'] != '') {
				$copydatas = $this->moddb->query("SELECT id".$copyof.$parenttopicid.$parentcatid." FROM datas WHERE id = '".$data['copyof']."' LIMIT 1");
				$copydata = $copydatas->fetchArray(SQLITE3_ASSOC);
				$dataid = $copydata['id'];
				if(in_array('topicid', $this->fileconfig['data'])) $topicid = $copydata['topicid'];
				if(in_array('catid', $this->fileconfig['data'])) $catid = $copydata['catid'];
				$robots = 'noindex,follow';
			}
			else {
				$robots = 'index,follow';
			}
		}
		if(array_key_exists('topic', $this->fileconfig) && $topicid != '') {
			$copyof = in_array('copyof', $this->fileconfig['topic']) ? ', copyof' : '';
			$parentcatid = in_array('catid', $this->fileconfig['topic']) ? ', catid' : '';
			$topics = $this->moddb->query("SELECT id".$copyof." FROM topics WHERE id = '".$topicid."' LIMIT 1");
			$topic = $topics->fetchArray(SQLITE3_ASSOC);
			if(in_array('copyof', $this->fileconfig['topic']) && $topic['copyof'] != '') {
				$copytopics = $this->moddb->query("SELECT id".$copyof.$parentcatid." FROM topics WHERE id = '".$topic['copyof']."' LIMIT 1");
				$copytopic = $copytopics->fetchArray(SQLITE3_ASSOC);
				$topicid = $copytopic['id'];
				if(in_array('catid', $this->fileconfig['topic'])) $catid = $copytopic['catid'];
				if($robots == '')
					$robots = 'noindex,follow';
			}
			else {
				if($robots == '')
					$robots = 'index,follow';
			}
		}
		if(array_key_exists('cat', $this->fileconfig)) {
			if($catorder != '' && array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
			{
				if($this->curcat == '')
					$this->getcatsfromsubs();
				$catorderfind = $this->findfirstinorder($this->curcat);
				if($catorderfind == '') $catorderfind = '0';
				
				if($catorder != $catorderfind) {
					$catorder = $catorderfind;
					if($robots == '')
						$robots = 'noindex,follow';
				}
				else {
					if($robots == '')
						$robots = 'index,follow';
				}
			}
		}
		
		$url[] = 'page='.$this->getpage;
		if($catorder != '') {
			$url[] = $this->params['order'].'='.$catorder;
		}
		elseif($catid != '') {
			$url[] = $this->params['cat'].'='.$catid;
		}
		if($topicid != '') {
			$url[] = $this->params['topic'].'='.$topicid;
		}
		if($dataid != '') {
			$url[] = $this->params['data'].'='.$dataid;
		}
		
		if($catorder == '' && $catid == '' && $topicid == '' && $dataid == '')
			$robots = 'index,follow';
		
		if($robots != '')
			$this->metadata['robots'] = $robots;
		if(count($url) > 0)
			$this->metadata['canonical'] = 'index.php?'.implode('&amp;', $url);
	}
	
	function catsmenuli($arr, $page, $order = '')
	{
		$lis = '';
		foreach($arr as $key => $item) {
			$catfields = $this->fileconfig['cat'];
			$selects = array();
			$catwheres = array();
			if(in_array('onoff', $catfields)) {
				$selects[] = 'onoff';
				$wheres[] = "onoff = '1'";
			}
			if(in_array('lang', $catfields)) {
				$selects[] = 'lang';
				$wheres[] = "lang = '".$this->pagelang."'";
			}
			$select = (count($selects) > 0) ? ", ".implode(", ", $selects) : '';
			$where = (count($wheres) > 0) ? " AND ".implode(" AND ", $wheres) : '';
			
			$cats = $this->moddb->query("SELECT id, catname, catlink".$select." FROM cats WHERE id = '".$item['id']."'".$where." LIMIT 1");
			
			$catnumrows = $this->getdbnumrows($cats);
			
			if($catnumrows > 0) {
				$cat = $cats->fetchArray();
				$catorder = $order != '' ? $order.'_'.$key : (string)$key;
				
				if($this->is_serialized($cat['catlink'])) {
					$catlangs = unserialize($cat['catlink']);
					$catlink = $catlangs[$this->pagelang];
					if($catlink == '') {
						$catlangs = unserialize($cat['catname']);
						$catlink = $catlangs[$this->pagelang];
					}
				}
				else {
					$catlink = $cat['catlink'];
					if($catlink == '') {
						$catlink = $cat['catname'];
					}
				}
				
				$lis.= '<li class="'.$this->modname.'li '.$this->modname.'li_'.$catorder;
				if(isset($this->get[$this->params['order']]) && !$this->menudeforder && $this->get[$this->params['order']] == $catorder) {
					$this->menudeforder = true;
					$lis.= ' '.$this->modname.'li_default';
				}
				$lis.= '">';
				$lis.= '<a href="index.php?page='.$page.'&amp;'.$this->params['order'].'='.$catorder.'">'.$catlink.'</a>';
				if(count($item['sub']) > 0) {
					$lis.= "\n".'<ul class="'.$this->modname.'ul '.$this->modname.'ul_'.$catorder.'">'."\n".$this->catsmenuli($item['sub'], $page, $catorder).'</ul>'."\n";
				}
				$lis.= '</li>'."\n";
			}
		}
		return $lis;
    }
	
	function loadcatsformenu()
	{
		$catmenu = '';
		
		if(array_key_exists('base', $this->fileconfig)) {
			if(in_array('basecat', $this->fileconfig['base']) && $this->basecat == '') {
				return $this->viewdefine('nobasecat').': $_MMVAR[\''.$this->modname.'basecat\']';
			}
			if(in_array('subcats', $this->fileconfig['base'])) {
				$linkpage = '';
				if($this->modpage != '') {
					$getmodpage = preg_replace('/[^a-z0-9_]/', '', $this->modpage);
					if(file_exists($this->serverpath.'/content/pages/'.$getmodpage)) {
						$linkpage = $getmodpage;
					}
				}
				if($linkpage == '') {
					$linkpage = $this->getpage;
				}
				
				$subcats = $this->loadsubcatmenu();
				if(count($subcats) == 0) {
					if(array_key_exists('base', $this->fileconfig) && in_array('basecat', $this->fileconfig['base']) && $this->basecat == '') {
						$catmenu.= '$_MMVAR[\''.$this->modname.'basecat\'] '.$this->viewdefine('basecatnotfound');
					}
					else {
						$catmenu.= $this->viewdefine('noavailsubcats');
					}
				}
				else {
					$catmenu.= '<ul class="'.$this->modname.'ul">'."\n".$this->catsmenuli($subcats, $linkpage).'</ul>'."\n";
				}
			}
		}
		
		return $catmenu;
	}
	
	function loadnewestblock()
	{
		$linkpage = '';
		$newest = array();
		
		if(array_key_exists('base', $this->fileconfig) && in_array('basecat', $this->fileconfig['base']) && $this->basecat == '') {
			return $this->viewdefine('nobasecat').': $_MMVAR[\''.$this->modname.'basecat\']';
		}
		
		if(array_key_exists('base', $this->fileconfig) && in_array('newest', $this->fileconfig['base']))
		{
			$limit = (array_key_exists('numbnewest', $this->dbconfig) && $this->validnum($this->dbconfig['numbnewest']) > 0) ? $this->dbconfig['numbnewest'] : 20;
			
			$wheres = array();
			$selects = array();
			$joins = array();
			
			$datafields = $this->fileconfig['data'];
			$datawheres = array();
			if(array_key_exists('topic', $this->fileconfig) || array_key_exists('cat', $this->fileconfig)) {
				$selects[] = 'd.id';
				
				if(in_array('catid', $datafields))
					$selects[] = 'd.catid';
				
				if(in_array('topicid', $datafields))
					$selects[] = 'd.topicid';
				
				if(in_array('fromtime', $datafields))
					$datawheres[] = "((d.fromtime < ".time()." OR TRIM(d.fromtime) = '') AND (d.totime > ".time()." OR TRIM(d.totime) = ''))";
				
				if(in_array('onoff', $datafields))
					$datawheres[] = "d.onoff = '1'";
				
				if(in_array('lang', $datafields))
					$datawheres[] = "d.lang = '".$this->pagelang."'";
			}
			else {
				if(in_array('catid', $datafields))
					$selects[] = 'catid';
				
				if(in_array('topicid', $datafields))
					$selects[] = 'topicid';
				
				if(in_array('fromtime', $datafields))
					$datawheres[] = "((fromtime < ".time()." OR TRIM(fromtime) = '') AND (totime > ".time()." OR TRIM(totime) = ''))";
				
				if(in_array('onoff', $datafields))
					$datawheres[] = "onoff = '1'";
				
				if(in_array('lang', $datafields))
					$datawheres[] = "lang = '".$this->pagelang."'";
			}
			
			if(count($datawheres) > 0)
				$wheres[] = implode(" AND ", $datawheres);
			
			$topicwhere = '';
			if(array_key_exists('topic', $this->fileconfig)) {
				$topicfields = $this->fileconfig['topic'];
				$topicwheres = array();
				
				$joins[] = 'topics t';
				$selects[] = 't.topic';
				
				if(in_array('catid', $topicfields))
					$selects[] = 't.catid';
				
				$topicwheres[] = "t.id = d.topicid";
				
				if(in_array('fromtime', $topicfields)) {
					$selects[] = 't.fromtime, t.totime';
					$topicwheres[] = "((t.fromtime < ".time()." OR TRIM(t.fromtime) = '') AND (t.totime > ".time()." OR TRIM(t.totime) = ''))";
				}
				
				if(in_array('onoff', $topicfields)) {
					$selects[] = 't.onoff';
					$topicwheres[] = "t.onoff = '1'";
				}
				
				if(in_array('lang', $topicfields)) {
					$selects[] = 't.lang';
					$topicwheres[] = "t.lang = '".$this->pagelang."'";
				}
				
				if(count($topicwheres) > 0)
					$wheres[] = implode(" AND ", $topicwheres);
			}
			
			$catwhere = '';
			if(array_key_exists('cat', $this->fileconfig)) {
				$catfields = $this->fileconfig['cat'];
				$catwheres = array();
				
				$joins[] = 'cats c';
				$selects[] = 'c.catname';
				
				if(in_array('catid', $this->fileconfig['data']))
					$catwheres[] = "c.id = d.catid";
				
				if(array_key_exists('topic', $this->fileconfig) && in_array('catid', $this->fileconfig['topic']))
					$catwheres[] = "c.id = t.catid";
				
				if(in_array('onoff', $catfields)) {
					$selects[] = 'c.onoff';
					$catwheres[] = "c.onoff = '1'";
				}
				
				if(in_array('lang', $catfields)) {
					$selects[] = 'c.lang';
					$catwheres[] = "c.lang = '".$this->pagelang."'";
				}
				
				if($this->basecat != '' && in_array('basecat', $this->fileconfig['base'])) {
					if(in_array('subcats', $this->fileconfig['base'])) {
						$submenu = $this->loadsubcatmenu();
						if(count($submenu) > 0) {
							$subcatids = $this->getsubcatidsfrombasecat($submenu);
						}
						$subcatids[] = $this->basecat;
						sort($subcatids);
						$catwheres[] = 'c.id IN ('.implode(',', $subcatids).')';
					}
					else {
						$catwheres[] = 'c.id IN ('.$this->basecat.')';
					}
				}
				
				if(count($catwheres) > 0)
					$wheres[] = implode(" AND ", $catwheres);
			}
			
			$select = (count($selects) > 0) ? implode(", ", $selects) : '';
			$join = (count($joins) > 0) ? " INNER JOIN ".implode(", ", $joins) : '';
			$where = (count($wheres) > 0) ? " WHERE ".implode(" AND ", $wheres) : '';
			
			if(array_key_exists('topic', $this->fileconfig) || array_key_exists('cat', $this->fileconfig)) {
				$datas = $this->moddb->query("SELECT ".$select.", d.".implode(', d.', $datafields)." FROM datas d".$join.$where." ORDER BY d.id DESC LIMIT ".$limit);
			}
			else {
				$datas = $this->moddb->query("SELECT id, ".implode(', ', $datafields)." FROM datas".$where." ORDER BY id DESC LIMIT ".$limit);
			}
			
			$datanumrows = $this->getdbnumrows($datas);
			
			if($datanumrows > 0) {
				if($this->modpage != '') {
					$getmodpage = preg_replace('/[^a-z0-9_]/', '', $this->modpage);
					if(file_exists($this->serverpath.'/content/pages/'.$getmodpage))
						$linkpage = $getmodpage;
				}
				if($linkpage == '') {
					$linkpage = $this->getpage;
				}

				$set_link_data = file_exists($this->serverpath.'/modules/'.$this->modname.'/view/tpls/datafull.tpl') ? 1 : 0;
				
				while($data = $datas->fetchArray(SQLITE3_ASSOC))
				{
					$link_cat = '';
					$link_topic = '';
					$link_data = '';
					$catorder = '';
					
					$link_data = ($set_link_data == 1) ? '&amp;'.$this->params['data'].'='.$data['id'] : '';
					if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
					{
						if($this->basecat != '' && $data['catid'] == $this->basecat) {
							$catorder = '';
						}
						else {
							$catorder = $this->findfirstinorder($data['catid']);
							if($catorder == 'catid_notfound_insub') {
								continue;
							}
						}
					}
					
					if(array_key_exists('topic', $this->fileconfig) && in_array('topicid', $this->fileconfig['data']))
					{
						if($this->is_serialized($data['topic'])) {
							$topiclangs = unserialize($data['topic']);
							$data['topic'] = $topiclangs[$this->pagelang];
						}
						$link_topic = '&amp;'.$this->params['topic'].'='.$data['topicid'];
						
						if(array_key_exists('cat', $this->fileconfig) && in_array('catid', $this->fileconfig['topic']))
						{
							if($this->is_serialized($data['catname'])) {
								$catlangs = unserialize($data['catname']);
								$data['catname'] = $catlangs[$this->pagelang];
							}
							
							if($data['catid'] == $this->basecat)
								$data['catname'] = '';
							
							if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
							{
								if($this->basecat != '' && $catorder != '') {
									$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
								}
								else {
									if($catorder == '' && $this->basecat == '')
										$link_cat = '&amp;'.$this->params['order'].'=0';
									elseif($catorder != '')
										$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
								}
							}
							else
							{
								if($this->basecat == '')
									$link_cat = '&amp;'.$this->params['cat'].'='.$data['catid'];
							}
						}
					}
					elseif(array_key_exists('cat', $this->fileconfig) && in_array('catid', $this->fileconfig['data']))
					{
						if($this->is_serialized($data['catname'])) {
							$catlangs = unserialize($data['catname']);
							$data['catname'] = $catlangs[$this->pagelang];
						}
						
						if($data['catid'] == $this->basecat)
							$data['catname'] = '';
						
						if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
						{
							if($this->basecat != '' && $catorder != '') {
								$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
							}
							else {
								if($catorder == '' && $this->basecat == '')
									$link_cat = '&amp;'.$this->params['order'].'=0';
								elseif($catorder != '')
									$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
							}
						}
						else
						{
							if($this->basecat == '')
								$link_cat = '&amp;'.$this->params['cat'].'='.$data['catid'];
						}
					}
					
					$newesttpl = $this->loaddatatotpl($data, 'newest');
					$link_newest = 'index.php?page='.$linkpage.$link_cat.$link_topic.$link_data;
					$newest[] = str_replace('###LINK_NEWEST###', $link_newest, $newesttpl);
				}
			}
		}
		
		return implode("\n", $newest);
	}
	
	function getsubcatidsfrombasecat($submenu)
	{
		$catids = array();
		foreach($submenu as $key => $item) {
			$catids[] = $item['id'];
			if(count($item['sub']) > 0) {
				$subcatids = $this->getsubcatidsfrombasecat($item['sub']);
				$catids = array_merge($catids, $subcatids);
			}
		}
		
		return $catids;
	}
	
	function findfirstinorder($catid)
	{
	    $catorder = '';
		
		$catsubs = $this->loadsubcatmenu();
		if(count($catsubs) > 0) {
			$found = $this->findparentoforder($catsubs, $catid);
			if($found != '_false') {
				$catorder = str_replace('sub_', '', $found);
			}
			if($found == '_false' && $this->basecat != '') {
				$catorder = 'catid_notfound_insub';
			}
		}

		return $catorder;
	}
	
	function findparentoforder($array, $needle, $parent = '') {
		foreach($array as $key => $value) {
			if($key == 'id' && $value == $needle) {
				return $parent;
			}
			if(is_array($value)) {
				$pass = ($parent != '' ? $parent.'_' : '').$key;
				$found = $this->findparentoforder($value, $needle, $pass);
				if($found != '_false') {
					return $found;
				}
			}
		}
		return '_false';
	}
    
	function getcatsfromsubs()
	{
	    $catids = '';
		$subs = $this->loadsubcatmenu();
		
		if(is_array($subs) && count($subs) > 0) {
	        $catorder = array();
	        if(isset($this->get[$this->params['order']]) && $this->get[$this->params['order']] != '') {
	        	$getorders = explode('_', $this->validorder($this->get[$this->params['order']]));
	        	foreach($getorders as $getorder) {
	        		$catorder[] = $this->validnum($getorder);
	        	}
	        }
            
            if(count($catorder) >= 1) {
                $curcatkeys = "[".implode("]['sub'][", $catorder)."]['id']";
    			eval("\$cursubs = \$subs".$curcatkeys.";");
                $this->curcat = $cursubs;
            }
            
			$subcatkeys = (count($catorder) >= 1) ? "[".implode("]['sub'][", $catorder)."]['sub']" : '';
			eval("\$catsubs = \$subs".$subcatkeys.";");
            
	    	$catids = array();
	        foreach($catsubs as $key => $item) {
	        	$catids[] = array($item['id'], $key);
	        }
		}
		
        $this->catorder = $catids;
	}
	
	function getnewtopic()
	{
		$inputs = array();
		if(array_key_exists('types', $this->fileconfig) && array_key_exists('user', $this->fileconfig['types'])) {
			$userfields = explode(',', $this->fileconfig['types']['user']);
			if(is_array($userfields)) {
				foreach($userfields as $userfield) {
					if(isset($_SESSION['userauth']['username']))
						$inputs[$userfield] = $_SESSION['userauth']['username'];
					elseif($this->userisadmin)
						$inputs[$userfield] = $this->viewdefine('adminname');
					else
						$inputs[$userfield] = '';
				}
			}
		}
		
		$postdata = array();
		foreach($this->fileconfig['userinputs'] as $key) {
			$postdata[$key] = isset($this->postback[$key]) ? $this->validinput($key, $this->postback[$key]) : '';
		}
		$postdata['topic'] = isset($this->postback['topic']) ? $this->validinput('topic', $this->postback['topic']) : '';
		
		ob_start();
		$module = $this;
		$db_data = $inputs;
		$post_data = $postdata;
		include $this->tplpath.'/userinput.tpl';
		$userinputtpl = ob_get_contents();
		ob_end_clean();
		
		ob_start();
		$postalert = $this->postalert != '' ? $this->postalert : '';
		$module = $this;
		$db_data = $inputs;
		$post_data = $postdata;
		include $this->tplpath.'/topicnew.tpl';
		$topicnewtpl = ob_get_contents();
		ob_end_clean();
		
		$topicnewtpl = str_replace('###LOAD_INPUTDATA_TPL###', $userinputtpl, $topicnewtpl);
		
		$formurl = 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$topicnew = str_replace('###INPUT_FORMURL###', $formurl, $topicnewtpl);
        
		return $topicnew;
	}
	
	function getnewdata()
	{
		$inputs = array();
		if(array_key_exists('types', $this->fileconfig) && array_key_exists('user', $this->fileconfig['types'])) {
			$userfields = explode(',', $this->fileconfig['types']['user']);
			if(is_array($userfields)) {
				foreach($userfields as $userfield) {
					if(isset($_SESSION['userauth']['username']))
						$inputs[$userfield] = $_SESSION['userauth']['username'];
					elseif($this->userisadmin)
						$inputs[$userfield] = $this->viewdefine('adminname');
					else
						$inputs[$userfield] = '';
				}
			}
		}
		
		$postdata = array();
		foreach($this->fileconfig['userinputs'] as $key) {
			if($this->searchfieldintypes($key) == 'user' && $this->userisadmin && !isset($_SESSION['userauth']['userid']))
				$postdata[$key] = isset($this->postback[$key]) ? $this->validinput('username', $this->postback[$key]) : '';
			else
				$postdata[$key] = isset($this->postback[$key]) ? $this->validinput($key, $this->postback[$key]) : '';
		}
		
		ob_start();
		$module = $this;
		$db_data = $inputs;
		$post_data = $postdata;
		include $this->tplpath.'/userinput.tpl';
		$userinputtpl = ob_get_contents();
		ob_end_clean();
		
		ob_start();
		$postalert = $this->postalert != '' ? $this->postalert : '';
		$module = $this;
		$db_data = $inputs;
		$post_data = $postdata;
		include $this->tplpath.'/datanew.tpl';
		$datanewtpl = ob_get_contents();
		ob_end_clean();
		
		$datanewtpl = str_replace('###LOAD_INPUTDATA_TPL###', $userinputtpl, $datanewtpl);
		
		$formurl = 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$datanew = str_replace('###INPUT_FORMURL###', $formurl, $datanewtpl);
				
		return $datanew;
	}
    
    function getviewcatlist()
    {
        $catlist = '';
		$catfields = $this->fileconfig['cat'];
		$wheres = array();
		$checkfields = array();
		$order = '';
        $dir = '';
		
		if(in_array('onoff', $catfields)) {
    		$checkfields[] = 'onoff';
			$wheres[] = "onoff = '1'";
		}
		
		if(in_array('lang', $catfields)) {
    		$checkfields[] = 'lang';
			$wheres[] = "lang = '".$this->pagelang."'";
		}
		
		if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
            $this->getcatsfromsubs();
            if($this->catorder != '') {
    			$ids = array();
    			foreach($this->catorder as $sub) {
                    $ids[] = $sub[0];
    			}
                
    			$wheres[] = "id IN (".implode(', ', $ids).")";
            }
            else {
                return isset($this->get[$this->params['order']]) ? array() : $this->viewdefine('noavailsubcats');
            }
		}
		else {
            if(in_array('sort', $catfields)) {
    			$order = ' ORDER BY sort';
    		}
    		else {
    			$order = ' ORDER BY id';
    		}
        }
		
		$where = (count($wheres) >= 1) ? " WHERE ".implode(' AND ', $wheres) : "";
        
        $limit = '';
        $catsperpage = '';
		if(!array_key_exists('base', $this->fileconfig) || (array_key_exists('base', $this->fileconfig) && !in_array('subcats', $this->fileconfig['base']))) {
            $catsperpage = (isset($this->dbconfig['catsperpage']) && $this->dbconfig['catsperpage'] != '') ? $this->dbconfig['catsperpage'] : '';
            
            $dir = (isset($this->dbconfig['catsort'])) ? ' '.$this->dbconfig['catsort'] : '';
            $pager = (isset($this->get['pager'])) ? $this->validnum($this->get['pager']) : 1;
            
            $checknextcat = 0;
            if($catsperpage != '') {
                $nextcat = $this->moddb->query("SELECT ".implode(', ', $checkfields)." FROM cats".$where.$order.$dir." LIMIT ".($pager*$catsperpage).", 1");
                $checknextcat = $this->getdbnumrows($nextcat);
                $start = $pager*$catsperpage-$catsperpage;
                $limit = " LIMIT ".$start.", ".$catsperpage;
            }
        }
        
        $dbfields = implode(', ', $catfields);
        $cats = $this->moddb->query("SELECT id, ".$dbfields." FROM cats".$where.$order.$dir.$limit);
        $catnumrows = $this->getdbnumrows($cats);
        
        if($catnumrows == 0) {
    		if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
                $catlist = $this->viewdefine('noavailsubcats');
            else
                $catlist = $this->viewdefine('noavailcategory');
        }
        else {
            $results = array();
			$orders = array();
            while($cat = $cats->fetchArray(SQLITE3_ASSOC)) {
	            $result = array();
                foreach($cat as $key => $value) {
                	if($key != 'sort' && $key != 'onoff' && $key != 'lang') {
	                    if($key == 'id' && $this->catorder != '') {
							foreach($this->catorder as $sub) {
			                    if($sub[0] == $value) {
    								$orders[] = $sub[1];
                                    break;
                                }
							}
						}
	                    $result[$key] = $value;
					}
                }
                $results[] = $result;
                unset($result);
            }
            
            if(count($results) > 0 && count($orders) > 0)
    	        array_multisort($orders, SORT_ASC, $results);
            
            if($catsperpage != '') {
                $this->setviewpager($pager, $checknextcat);
            }
            
            $catlist = $results;
        }
        
        return $catlist;
    }
    
    function setviewcattotpl($catlist, $part = '')
    {
		$catdatatpl = $this->tplpath.'/'.$part.'cat.tpl';
		$listtpl = $this->tplpath.'/'.$part.'catslist.tpl';
        $db_data = array();
        $catdatas = array();
        $backcat = '';
		
		if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
			if($this->catorder != '') {
				$orders = array();
				if(isset($this->get[$this->params['order']]) && $this->get[$this->params['order']] != '') {
					$getorders = explode('_', $this->get[$this->params['order']]);
					foreach($getorders as $getorder) {
						$orders[] = $this->validnum($getorder);
					}
				}
				$backcat = 'index.php?page='.$this->getpage;
				if(count($orders) > 1 && $this->basecat == '') {
					$backorder = $orders;
					$dellast = array_pop($backorder);
					$backcat.= '&amp;'.$this->params['order'].'='.implode('_', $backorder);
				}
			}
		}
        
		if(count($catlist) > 0) {
			foreach($catlist as $cats => $cat) {
				$catid = $cat['id'];
				if($this->is_serialized($cat['catname'])) {
					$cat_catname = unserialize($cat['catname']);
					$db_data['catname'] = $cat_catname[$this->pagelang];
				}
				else {
					$db_data['catname'] = $cat['catname'];
				}
				
				if(in_array('cattext', $this->fileconfig['cat'])) {
					if($this->is_serialized($cat['cattext'])) {
						$cat_cattext = unserialize($cat['cattext']);
						$db_data['cattext'] = $cat_cattext[$this->pagelang];
					}
					else {
						$db_data['cattext'] = $cat['cattext'];
					}
				}
				
				if(in_array('catlink', $this->fileconfig['cat'])) {
					if($this->is_serialized($cat['catlink'])) {
						$cat_catlink = unserialize($cat['catlink']);
						$db_data['catlink'] = $cat_catlink[$this->pagelang];
					}
					else {
						$db_data['catlink'] = $cat['catlink'];
					}
				}
				
				$catlink = 'index.php?page='.$this->getpage;
				if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
					if($this->catorder != '') {
						$orders = array();
						if(isset($this->get[$this->params['order']]) && $this->get[$this->params['order']] != '') {
							$getorders = explode('_', $this->get[$this->params['order']]);
							foreach($getorders as $getorder) {
								$orders[] = $this->validnum($getorder);
							}
						}
						foreach($this->catorder as $sub) {
							if($sub[0] == $catid) {
								$orders[] = $sub[1];
								break;
							}
						}
						$catlink.= '&amp;'.$this->params['order'].'='.implode('_', $orders);
					}
				}
				else {
					$catlink.= '&amp;'.$this->params['cat'].'='.$cat['id'];
				}
				
				$cat_lightbox = '';
				$cat_viewimage = '';
				$cat_boximage = '';
				if(in_array('catimage', $this->fileconfig['cat'])) {
					if($this->is_serialized($cat['catimage'])) {
						$cat_catimage = unserialize($cat['catimage']);
						$cat_imgpath = 'modules/'.$this->modname.'/media/cats/';
						$cat_viewimage = $cat_imgpath.$cat_catimage[0].'/'.$cat_catimage[1].'_view.'.$cat_catimage[2];
						$cat_boximage = $cat_imgpath.$cat_catimage[0].'/'.$cat_catimage[1].'_box.'.$cat_catimage[2];
					}
					$db_data['catimage'] = $cat_viewimage != '' ? $cat_viewimage : '';
					if(array_key_exists('lightboxlistdata', $this->dbconfig) && array_key_exists('lightbox', $this->dbconfig) && array_key_exists('catimage', $this->dbconfig['lightbox']) && $this->dbconfig['lightbox']['catimage'] == 'on' && $cat_boximage != '') {
						if($this->dbconfig['lightboxlistdata'] == 'nodatas') {
							$cat_lightbox = '<a href="'.$cat_boximage.'" rel="lightbox">';
						}
						if($this->dbconfig['lightboxlistdata'] == 'onedatas') {
							$cat_lightbox = '<a href="'.$cat_boximage.'" rel="lightbox1">';
						}
						if($this->dbconfig['lightboxlistdata'] == 'alldatas') {
							$cat_lightbox = '<a href="'.$cat_boximage.'" rel="lightbox1">';
						}
					}
				}
				
				ob_start();
				include $catdatatpl;
				$catdata = ob_get_contents();
				ob_end_clean();
				
				$catdata = str_replace('###CATEGORY_LINK###', $catlink, $catdata);
				
				if($cat_viewimage == '') {
					$cat_lightbox = '';
				}
				if($cat_lightbox != '')
					$catdata = preg_replace('#<img(?:([^>]+))?(src=\"'.$cat_viewimage.'\")(?:([^>]+))?/>#Umsi', $cat_lightbox.'<img${1}${2}${3}/></a>', $catdata);
				
				$catdatas[] = $catdata;
			}
		}
        
        $pager = '';
        if(count($this->pager) > 0) {
            $pager = $this->getpagernavi();
        }
		
		ob_start();
		$module = $this;
		include $listtpl;
		$list = ob_get_contents();
		ob_end_clean();
        
    	$list = str_replace('###LOAD_PAGER###', $pager, $list);
    	$list = str_replace('###LOOP_'.strtoupper($part).'CATEGORIES###', implode("\n", $catdatas), $list);
        if(preg_match('~###BACK_CATEGORY_LINK###~i', $list)) {
			if($this->basecat != '' && in_array('basecat', $this->fileconfig['base']) && (!in_array('subcats', $this->fileconfig['base']) || (!isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])))) {
				$list = preg_replace('~<a(?:([^>]+))?href=\"###BACK_CATEGORY_LINK###\"(?:([^>]+))?>(?:([^<]+))?</a>~Umsi', '', $list);
			}
			else {
				$list = str_replace('###BACK_CATEGORY_LINK###', $backcat, $list);
			}
		}
        
        return $list;
    }
    
    
    function getviewtopiclist()
    {
		$matchlisttpl = file_get_contents($this->tplpath.'/topicslist.tpl');
		
		$topicfields = $this->fileconfig['topic'];
        $topiclist = array();
		$wheres = array();
		$joinwheres = array();
		$checkfields = array();
		$order = '';
		$subs = '';
		
        $breadcrumb = (array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && preg_match('~###BREADCRUMB###~i', $matchlisttpl)) ? $this->getbreadcrumb() : '';
        
        $loadsubcats = (array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && preg_match('~###LOAD_SUB_CATEGORIES###~i', $matchlisttpl)) ? 1 : 0;
		
		unset($matchlisttpl);
		
		$catjoin = '';
		$catcheck = '';
		$copyselect = '';
		$copyjoin = '';
		if(in_array('catid', $topicfields)) {
            $catid = '';
            if($this->basecat != '' && !isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])) {
                $catid = $this->basecat;
            }
            elseif(isset($this->get[$this->params['cat']])) {
                $catid = $this->validnum($this->get[$this->params['cat']]);
            }
            elseif(isset($this->get[$this->params['order']])) {
                if($this->curcat == '')
                    $this->getcatsfromsubs();
                $catid = $this->curcat;
            }
    		if($catid != '') {
        		$checkfields[] = 'catid';
    			$wheres[] = "catid = '".$catid."'";
                $joinwheres[] = "t.catid = '".$catid."'";
				$catjoin = ', cats c';
				$catcheck = ', c.id AS cid';
				$joinwheres[] = "c.id = t.catid";
				if(in_array('onoff', $this->fileconfig['cat'])) {
					$catcheck.= ', c.onoff AS conoff';
					$joinwheres[] = "c.onoff = '1'";
				}
    		}
    		
			if(in_array('seotitle', $this->fileconfig['cat']) || (array_key_exists('headlinetitle', $this->dbconfig) && $this->dbconfig['headlinetitle'] == 'show')) {
				$seofields = (in_array('seotitle', $this->fileconfig['cat'])) ? ', seotitle, seodesc, seokeys' : '';
				$catseos = $this->moddb->query("SELECT id, catname".$seofields." FROM cats WHERE id = '".$catid."' LIMIT 1");
				$catseonumrows = $this->getdbnumrows($catseos);
				
				if($catseonumrows > 0) {
					$catseo = $catseos->fetchArray(SQLITE3_ASSOC);
					if(in_array('seotitle', $this->fileconfig['cat'])) {
						if($this->is_serialized($catseo['seotitle'])) {
							$catseotitlelangs = unserialize($catseo['seotitle']);
							$catseotitle = $catseotitlelangs[$this->pagelang];
							$catseodesclangs = unserialize($catseo['seodesc']);
							$catseodesc = $catseodesclangs[$this->pagelang];
							$catseokeyslangs = unserialize($catseo['seokeys']);
							$catseokeys = $catseokeyslangs[$this->pagelang];
						}
						else {
							$catseotitle = $catseo['seotitle'];
							$catseodesc = $catseo['seodesc'];
							$catseokeys = $catseo['seokeys'];
						}
						if($catseotitle != '')
							$this->metadata['title'] = $catseotitle;
						if($catseodesc != '')
							$this->metadata['description'] = $catseodesc;
						if($catseokeys != '')
							$this->metadata['keywords'] = $catseokeys;
						
						unset($catseo['seotitle']);
						unset($catseo['seodesc']);
						unset($catseo['seokeys']);
					}
					elseif(array_key_exists('headlinetitle', $this->dbconfig) && $this->dbconfig['headlinetitle'] == 'show') {
						if($this->is_serialized($catseo['catname'])) {
							$catlangs = unserialize($catseo['catname']);
							$catname = $catlangs[$this->pagelang];
						}
						else {
							$catname = $catseo['catname'];
						}
						$this->metadata['title'] = $catname;
					}
				}
			}
        }
		else {
			$this->loadmetadata();
		}
		
		if(in_array('fromtime', $topicfields)) {
    		$checkfields[] = 'fromtime';
    		$checkfields[] = 'totime';
			$wheres[] = "((fromtime < ".time()." OR TRIM(fromtime) = '') AND (totime > ".time()." OR TRIM(totime) = ''))";
			$fromtowhere = "((t.fromtime < ".time()." OR TRIM(t.fromtime) = '') AND (t.totime > ".time()." OR TRIM(t.totime) = ''))";
			if(in_array('copyof', $topicfields)) {
				$joinwheres[] = "(".$fromtowhere." OR ((copyt.fromtime < ".time()." OR TRIM(copyt.fromtime) = '') AND (copyt.totime > ".time()." OR TRIM(copyt.totime) = '')))";
				$copyselect.= ", COALESCE(copyt.fromtime, t.fromtime) AS tfromtime";
				$copyselect.= ", COALESCE(copyt.totime, t.totime) AS ttotime";
			}
			else
				$joinwheres[] = $fromtowhere;
		}
		
		if(in_array('onoff', $topicfields)) {
    		$checkfields[] = 'onoff AS tonoff';
			$wheres[] = "onoff = '1'";
			$joinwheres[] = "t.onoff = '1'";
		}
		
		if(in_array('copyof', $topicfields)) {
    		$checkfields[] = 'copyof';
			$copyselect.= ", COALESCE(copyt.topic, t.topic) AS ttopic";
			$copyjoin = " LEFT JOIN topics copyt ON copyt.id = t.copyof";
		}
		
		if(in_array('lang', $topicfields)) {
    		$checkfields[] = 'lang';
			$wheres[] = "lang = '".$this->pagelang."'";
			$joinwheres[] = "t.lang = '".$this->pagelang."'";
		}
        
        $loadbydatafield = '';
		if(array_key_exists('sorttopicfield', $this->dbconfig) && $this->dbconfig['sorttopicfield'] != '') {
			if(in_array($this->dbconfig['sorttopicfield'], $topicfields)) {
				$checkfields[] = $this->dbconfig['sorttopicfield'];
				$order = (in_array('copyof', $topicfields) ? 't' : '').$this->dbconfig['sorttopicfield']; 
				if(in_array('copyof', $topicfields) && $this->dbconfig['sorttopicfield'] != 'fromtime' && $this->dbconfig['sorttopicfield'] != 'topic') {
					$copyselect.= ", COALESCE(copyt.".$this->dbconfig['sorttopicfield'].", t.".$this->dbconfig['sorttopicfield'].") AS t".$this->dbconfig['sorttopicfield'];
				}
			}
			else {
				$loadbydatafield = $this->dbconfig['sorttopicfield'];
				$order = '';
			}
		}
		elseif(in_array('sort', $topicfields)) {
			$order = ($catjoin != '' ? 't.' : '').'sort';
			
		}
		else {
			$order = ($catjoin != '' ? 't.' : '').'id';
		}
        
        $topicsperpage = (isset($this->dbconfig['topicsperpage']) && $this->dbconfig['topicsperpage'] != '') ? $this->dbconfig['topicsperpage'] : '';
		
		$where = (count($wheres) >= 1) ? " WHERE ".implode(' AND ', $wheres) : "";
		$joinwhere = (count($joinwheres) >= 1) ? implode(' AND ', $joinwheres) : "";
        $pager = (isset($this->get['pager'])) ? $this->validnum($this->get['pager']) : 1;
        $dir = (isset($this->dbconfig['topicsort'])) ? ' '.$this->dbconfig['topicsort'] : '';
		
        $limit = '';
        $checknexttopic = '0';
        if($topicsperpage != '') {
            if($loadbydatafield != '') {
				if($copyselect != '') {
					$nexttopic = $this->moddb->query("SELECT t.id AS id, t.onoff".$catcheck.$copyselect." FROM topics t LEFT JOIN topics copyt ON copyt.id = t.copyof LEFT JOIN datas d ON d.id = t.startid OR d.id = copyt.startid LEFT JOIN cats c ON c.id = t.catid WHERE ".$joinwhere." ORDER BY d.".$loadbydatafield.$dir.", id".$dir." LIMIT ".($pager*$topicsperpage).", 1");
				}
				else {
					$nexttopic = $this->moddb->query("SELECT t.id, t.topic".$catcheck." FROM topics t INNER JOIN datas d".$catjoin." WHERE t.startid = d.id AND ".$joinwhere." ORDER BY d.".$loadbydatafield.$dir.", t.id".$dir." LIMIT ".($pager*$topicsperpage).", 1");
				}
            }
            else {
				if($catjoin != '') {
					$secondorder = ($order != 't.sort' && $order != 't.id') ? ', t.id'.$dir : '';
					if($copyselect != '') {
						$nexttopic = $this->moddb->query("SELECT t.id, t.copyof".$catcheck.$copyselect." FROM topics t".$catjoin.$copyjoin." WHERE ".$joinwhere." ORDER BY ".$order.$dir.$secondorder." LIMIT ".($pager*$topicsperpage).", 1");
					}
					else {
						$nexttopic = $this->moddb->query("SELECT t.id, t.topic".$catcheck." FROM topics t INNER JOIN datas d".$catjoin." WHERE t.startid = d.id AND ".$joinwhere." ORDER BY ".$order.$dir.$secondorder." LIMIT ".($pager*$topicsperpage).", 1");
					}
				}
				else {
					$secondorder = ($order != 'sort' && $order != 'id') ? ', id'.$dir : '';
					$nexttopic = $this->moddb->query("SELECT ".implode(', ', $checkfields)." FROM topics".$where." ORDER BY ".$order.$dir.$secondorder." LIMIT ".($pager*$topicsperpage).", 1");
				}
            }
            $checknexttopic = $this->getdbnumrows($nexttopic);
            $start = $pager*$topicsperpage-$topicsperpage;
            $limit = " LIMIT ".$start.", ".$topicsperpage;
        }
        
        if($loadbydatafield != '') {
			$fields = implode(', t.', $topicfields);
			if($copyselect != '') {
				$topics = $this->moddb->query("SELECT t.id AS id, t.".$fields.$catcheck.$copyselect." FROM topics t LEFT JOIN topics copyt ON copyt.id = t.copyof LEFT JOIN datas d ON d.id = t.startid OR d.id = copyt.startid LEFT JOIN cats c ON c.id = t.catid WHERE ".$joinwhere." ORDER BY d.".$loadbydatafield.$dir.", id".$dir.$limit);
			}
			else {
				$topics = $this->moddb->query("SELECT t.id, t.".$fields.$catcheck." FROM topics t INNER JOIN datas d".$catjoin." WHERE t.startid = d.id AND ".$joinwhere." ORDER BY d.".$loadbydatafield.$dir.", t.id".$dir.$limit);
			}
        }
        else {
			if($catjoin != '') {
				$fields = implode(', t.', $topicfields);
				if($copyselect != '') {
					$secondorder = ($order != 't.sort' && $order != 't.id') ? ', id'.$dir : '';
					$topics = $this->moddb->query("SELECT t.id AS id, t.".$fields.$catcheck.$copyselect." FROM topics t".$catjoin.$copyjoin." WHERE ".$joinwhere." ORDER BY ".$order.$dir.$secondorder.$limit);
				}
				else {
					$secondorder = ($order != 't.sort' && $order != 't.id') ? ', t.id'.$dir : '';
					$topics = $this->moddb->query("SELECT t.id, t.".$fields.$catcheck." FROM topics t INNER JOIN datas d".$catjoin." WHERE t.startid = d.id AND ".$joinwhere." ORDER BY ".$order.$dir.$secondorder.$limit);
				}
			}
			else {
				$fields = implode(', ', $topicfields);
				$secondorder = ($order != 'sort' && $order != 'id') ? ', id'.$dir : '';
				$topics = $this->moddb->query("SELECT id, ".$fields." FROM topics".$where." ORDER BY ".$order.$dir.$secondorder.$limit);
			}
        }
        
        $topicnumrows = $this->getdbnumrows($topics);
        
        if($topicnumrows == 0) {
			if($this->basecat != '' && !isset($this->get[$this->params['order']]) && array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && !isset($this->dbconfig['catonlymenu'])) {
				$catlist = $this->getviewcatlist();
				if(is_array($catlist)) {
					return $this->setviewcattotpl($catlist);
				}
				else {
					return $catlist;
				}
			}
			else {
				$topiclist[] = $this->viewdefine('noavailtopic');
			}
        }
        else {
            $results = array();
            while($topic = $topics->fetchArray(SQLITE3_ASSOC)) {
				$iscopy = false;
				if(in_array('copyof', $this->fileconfig['topic']) && $topic['copyof'] != '') {
					$iscopy = true;
					$topic_id = $topic['id'];
					$copytopics = $this->moddb->query("SELECT id, ".implode(', ', $this->fileconfig['topic'])." FROM topics WHERE id = '".$topic['copyof']."' LIMIT 1");
					$topic = $copytopics->fetchArray(SQLITE3_ASSOC);
					$topic['id'] = $topic_id;
					$starttopic_topicid = $topic_id;
				}
                $topicdata = $this->loadtopictotpl($topic, 'topicdata');
				if(array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base'])) {
					$startdata = '';
					if(array_key_exists('startontopiclist', $this->dbconfig)) {
						foreach($topic as $key => $value) {
							if($key == 'startid' && $value != '') {
								$starttopic = $this->loadstarttopic($value);
								if(count($starttopic) > 0) {
									if($iscopy) $starttopic['topicid'] = $starttopic_topicid;
									$starttpl = file_exists($this->tplpath.'/topicslist_startdata.tpl') ? 'topicslist_startdata' : 'data';
									$startdata = $this->loaddatatotpl($starttopic, $starttpl);
								}
								break;
							}
						}
					}
					$topicdata = str_replace('###LOAD_TOPICDATA###', $startdata, $topicdata);
				}
                $topiclist[] = $topicdata;
            }
            if($topicsperpage != '') {
                $this->setviewpager($pager, $checknexttopic);
            }
        }
        
        $pager = '';
        if(count($this->pager) > 0) {
            $pager = $this->getpagernavi();
        }
		
		ob_start();
		$module = $this;
		include $this->tplpath.'/topicslist.tpl';
		$topiclisttpl = ob_get_contents();
		ob_end_clean();
		
        $topiclisttpl = str_replace('###BREADCRUMB###', $breadcrumb, $topiclisttpl);
        
        if($loadsubcats == 1) {
    		$subcatlist = $this->getviewcatlist();
            $subcats = (is_array($subcatlist) && count($subcatlist) > 0) ? $this->setviewcattotpl($subcatlist, 'sub') : $this->setviewcattotpl(array(), 'sub');
            $topiclisttpl = str_replace('###LOAD_SUB_CATEGORIES###', $subcats, $topiclisttpl);
        }
		
        if(preg_match('~###BACK_CATEGORY_LINK###~i', $topiclisttpl)) {
			if($this->basecat != '' && in_array('basecat', $this->fileconfig['base']) && (!in_array('subcats', $this->fileconfig['base']) || (!isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])))) {
				$topiclisttpl = preg_replace('~<a(?:([^>]+))?href=\"###BACK_CATEGORY_LINK###\"(?:([^>]+))?>(?:([^<]+))?</a>~Umsi', '', $topiclisttpl);
			}
			else {
				$topiclisttpl = str_replace('###BACK_CATEGORY_LINK###', 'index.php?page='.$this->getpage, $topiclisttpl);
			}
		}
        
    	$topiclisttpl = str_replace('###LOOP_TOPICDATA###', implode("\n", $topiclist), $topiclisttpl);
    	$topiclisttpl = str_replace('###LOAD_PAGER###', $pager, $topiclisttpl);
        
        if(preg_match('~###LOAD_TOPICINPUT###~i', $topiclisttpl)) {
			$topicnew = $this->getnewtopic();
        	$topiclisttpl = str_replace('###LOAD_TOPICINPUT###', $topicnew, $topiclisttpl);
        }
        elseif(preg_match('~###LINK_NEWTOPIC###~i', $topiclisttpl)) {
            $link_topicnew = 'index.php?page='.$this->getpage;
            if(isset($this->get[$this->params['cat']])) {
                $catid = $this->validnum($this->get[$this->params['cat']]);
                $link_topicnew.= '&amp;'.$this->params['cat'].'='.$catid;
            }
            elseif(isset($this->get[$this->params['order']])) {
                $catorder = $this->validorder($this->get[$this->params['order']]);
                $link_topicnew.= '&amp;'.$this->params['order'].'='.$catorder;
            }
            $link_topicnew.= '&amp;'.$this->params['load'].'='.$this->params['topic'].'new';
            
        	$topiclisttpl = str_replace('###LINK_NEWTOPIC###', $link_topicnew, $topiclisttpl);
        }
        
        return $topiclisttpl;
    }
    
    function loadstarttopic($startid)
    {
        $datafields = $this->fileconfig['data'];
        $dbfields = implode(', ', $datafields);
        $datas = $this->moddb->query("SELECT id, ".$dbfields." FROM datas WHERE id = ".$startid." LIMIT 1");
        
        $datanumrows = $this->getdbnumrows($datas);
        
        $data = '';
        
        if($datanumrows > 0) {
            $data = $datas->fetchArray(SQLITE3_ASSOC);
        }
        
        return $data;
    }
    
    function getviewtopic()
    {
		$filterwhere = '';
		$filterorder = '';
		$filterdir = '';
		if(isset($_SESSION[$this->modname.'datafilter']) && ((isset($this->get['filter']) && $this->get['filter'] == 'query') || (isset($this->dbconfig['filtermaintain']) && $this->dbconfig['filtermaintain'] == 'yes'))) {
			$filter = $_SESSION[$this->modname.'datafilter'];
			if(isset($filter['where'])) $filterwhere = $filter['where'];
			if(isset($filter['order'])) $filterorder = $filter['order'];
			if(isset($filter['dir'])) $filterdir = $filter['dir'];
		}
		
        $topicid = $this->validnum($this->get[$this->params['topic']]);
		$matchtopictpl = file_get_contents($this->tplpath.'/topic.tpl');
        
        $topicout = array();
        $topicdatalist = array();
        $startid = '';
        $catid = '';
        $loadbydatafield = '';
		
		$breadcrumb = (array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && preg_match('~###BREADCRUMB###~i', $matchtopictpl)) ? $this->getbreadcrumb() : '';
        
        $loadsubcats = (array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && preg_match('~###LOAD_SUB_CATEGORIES###~i', $matchtopictpl)) ? 1 : 0;
		
        $filterform = (array_key_exists('base', $this->fileconfig) && in_array('filter', $this->fileconfig['base']) && preg_match('~###LOAD_FILTER###~i', $matchtopictpl)) ? 1 : 0;
		
		unset($matchtopictpl);
        
		$topicfields = $this->fileconfig['topic'];
        $topicwheres = array();
        $jointopicwheres = array();
		$copytopicselect = '';
		$copytopicjoin = '';
		if(in_array('catid', $topicfields)) {
			if($this->basecat != '' && !isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])) {
				$catid = $this->basecat;
			}
            elseif(isset($this->get[$this->params['cat']])) {
                $catid = $this->validnum($this->get[$this->params['cat']]);
            }
            elseif(isset($this->get[$this->params['order']])) {
                if($this->curcat == '')
                    $this->getcatsfromsubs();
                $catid = $this->curcat;
            }
    		if($catid != '') {
    			$topicwheres[] = "catid = '".$catid."'";
                $jointopicwheres[] = "t.catid = '".$catid."'";
    		}
        }
		
		if(in_array('onoff', $topicfields)) {
			$topicwheres[] = "onoff = '1'";
			$jointopicwheres[] = "t.onoff = '1'";
		}
		
		if(in_array('fromtime', $topicfields)) {
			$topicwheres[] = "((fromtime < ".time()." OR TRIM(fromtime) = '') AND (totime > ".time()." OR TRIM(totime) = ''))";
			$fromtowhere = "((t.fromtime < ".time()." OR TRIM(t.fromtime) = '') AND (t.totime > ".time()." OR TRIM(t.totime) = ''))";
			if(in_array('copyof', $topicfields)) {
				$jointopicwheres[] = "(".$fromtowhere." OR ((copyt.fromtime < ".time()." OR TRIM(copyt.fromtime) = '') AND (copyt.totime > ".time()." OR TRIM(copyt.totime) = '')))";
				$copytopicselect.= ", COALESCE(copyt.fromtime, t.fromtime) AS tfromtime";
				$copytopicselect.= ", COALESCE(copyt.totime, t.totime) AS ttotime";
			}
			else
				$jointopicwheres[] = $fromtowhere;
		}
		
		if(in_array('copyof', $topicfields)) {
			$copytopicselect.= ", COALESCE(copyt.topic, t.topic) AS ttopic";
			$copytopicjoin = " LEFT JOIN topics copyt ON copyt.id = t.copyof";
		}
		
		if(in_array('lang', $topicfields)) {
			$topicwheres[] = "lang = '".$this->pagelang."'";
			$jointopicwheres[] = "t.lang = '".$this->pagelang."'";
		}
		
        $topicwhere = implode(" AND ", $topicwheres);
        $jointopicwhere = implode(" AND ", $jointopicwheres);
		
		if(array_key_exists('sorttopicfield', $this->dbconfig) && $this->dbconfig['sorttopicfield'] != '') {
            if(in_array($this->dbconfig['sorttopicfield'], $topicfields)) {
                $topicorder = $this->dbconfig['sorttopicfield'];
				if(in_array('copyof', $topicfields) && $this->dbconfig['sorttopicfield'] != 'fromtime' && $this->dbconfig['sorttopicfield'] != 'topic') {
					$copytopicselect.= ", COALESCE(copyt.".$this->dbconfig['sorttopicfield'].", t.".$this->dbconfig['sorttopicfield'].") AS t".$this->dbconfig['sorttopicfield'];
				}
            }
            else {
                $loadbydatafield = $this->dbconfig['sorttopicfield'];
                $topicorder = '';
            }
		}
        elseif(in_array('sort', $topicfields)) {
			$topicorder = 'sort';
			if(in_array('copyof', $topicfields))
				$copytopicselect.= ", COALESCE(copyt.sort, t.sort) AS tsort";
		}
		else {
    		$topicorder = 'id';
			if(in_array('copyof', $topicfields))
				$copytopicselect.= ", COALESCE(copyt.id, t.id) AS tid";
		}
		
		if($catid != '') {
			if($copytopicselect != '') {
				$topicdatas = $this->moddb->query("SELECT t.id AS id, t.".implode(', t.', $topicfields).$copytopicselect." FROM topics t ".$copytopicjoin." LEFT JOIN cats c ON c.id = t.catid WHERE t.id = ".$topicid." AND c.onoff = '1' AND ".$jointopicwhere." LIMIT 1");
			}
			else {
				$topicdatas = $this->moddb->query("SELECT t.id, t.".implode(', t.', $topicfields)." FROM topics t INNER JOIN cats c WHERE t.id = ".$topicid." AND c.id = ".$catid." AND c.onoff = '1' AND ".$jointopicwhere." LIMIT 1");
			}
		}
		else {
			$topicdatas = $this->moddb->query("SELECT id, ".implode(', ', $topicfields)." FROM topics WHERE id = ".$topicid." AND ".$topicwhere." LIMIT 1");
		}
        $topicnumrows = $this->getdbnumrows($topicdatas);
        
        if($topicnumrows == 0) {
            return $this->viewdefine('topicoff');
        }
        else {
            $topic = $topicdatas->fetchArray(SQLITE3_ASSOC);
			if(in_array('copyof', $topicfields) && $topic['copyof'] != '') {
				$copytopics = $this->moddb->query("SELECT id, ".implode(', ', $topicfields)." FROM topics WHERE id = ".$topic['copyof']." LIMIT 1");
				if(in_array('sort', $topicfields))
					$orgtopicsort = $topic['sort'];
				$topic = $copytopics->fetchArray(SQLITE3_ASSOC);
				if(in_array('sort', $topicfields))
					$topic['sort'] = $orgtopicsort;
			}
            foreach($topic as $key => $value) {
                if($key == 'topic') {
        			if($this->is_serialized($value)) {
        				$values = unserialize($value);
                        $topicout['topic'] = $values[$this->pagelang];
        			}
        			else {
                        $topicout['topic'] = $value;
        			}
                }
        		if($key == 'startid' && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base']) && 
				  (
					(!isset($this->dbconfig['filtermaintain']) && (!isset($this->get['filter']) || $this->get['filter'] == 'reset')) ||
					(isset($this->dbconfig['filtermaintain']) && $this->dbconfig['filtermaintain'] == 'yes' && !isset($_SESSION[$this->modname.'datafilter'])) || 
					(isset($this->dbconfig['startontopicfilter']) && $this->dbconfig['startontopicfilter'] == 'no') || 
					(isset($this->dbconfig['startontopic']) && $this->dbconfig['startontopic'] == 'show')
				  )
				) {
					$startid = $value;
                }
            }
			
    		if(in_array('seotitle', $this->fileconfig['topic'])) {
				if($this->is_serialized($topic['seotitle'])) {
					$topicseotitlelangs = unserialize($topic['seotitle']);
					$topicseotitle = $topicseotitlelangs[$this->pagelang];
					$topicseodesclangs = unserialize($topic['seodesc']);
					$topicseodesc = $topicseodesclangs[$this->pagelang];
					$topicseokeyslangs = unserialize($topic['seokeys']);
					$topicseokeys = $topicseokeyslangs[$this->pagelang];
				}
				else {
					$topicseotitle = $topic['seotitle'];
					$topicseodesc = $topic['seodesc'];
					$topicseokeys = $topic['seokeys'];
				}
				if($topicseotitle != '')
					$this->metadata['title'] = $topicseotitle;
				if($topicseodesc != '')
					$this->metadata['description'] = $topicseodesc;
				if($topicseokeys != '')
					$this->metadata['keywords'] = $topicseokeys;
				
				unset($topic['seotitle']);
				unset($topic['seodesc']);
				unset($topic['seokeys']);
			}
			elseif(array_key_exists('headlinetitle', $this->dbconfig) && $this->dbconfig['headlinetitle'] == 'show') {
				$this->metadata['title'] = $topicout['topic'];
			}
            
			ob_start();
			$module = $this;
            $db_data = $topicout;
			include $this->tplpath.'/topic.tpl';
            $topicdata = ob_get_contents();
        	ob_end_clean();
        }
        
        $limit = '';
        $checkfields = array();
        $wheres = array();
        $copywheres = array();
        $datafields = $this->fileconfig['data'];
		$copydataselect = '';
		$copydatajoin = '';
        
		if(in_array('catid', $datafields)) {
            $catid = '';
            if(isset($this->get[$this->params['cat']])) {
                $catid = $this->validnum($this->get[$this->params['cat']]);
            }
            elseif(isset($this->get[$this->params['order']])) {
                if($this->curcat == '')
                    $this->getcatsfromsubs();
                $catid = $this->curcat;
            }
    		if($catid != '') {
        		$checkfields[] = 'catid';
    			$wheres[] = "catid = '".$catid."'";
    			$copywheres[] = "d.catid = '".$catid."'";
    		}
        }
        
		if(in_array('topicid', $datafields) && $topicid != '') {
    		$checkfields[] = 'topicid';
			$wheres[] = "topicid = '".$topic['id']."'";
			$copywheres[] = "d.topicid = '".$topic['id']."'";
		}
        
		if(in_array('copyof', $datafields)) {
    		$checkfields[] = 'copyof';
			if($loadbydatafield != '' && $loadbydatafield != 'topic')
				$copydataselect.= ", COALESCE(copyd.".$loadbydatafield.", d.".$loadbydatafield.") AS d".$loadbydatafield;
			$copydatajoin.= " LEFT JOIN datas copyd ON copyd.id = d.copyof";
		}
		
		if(in_array('fromtime', $datafields)) {
    		$checkfields[] = 'fromtime';
    		$checkfields[] = 'totime';
			$wheres[] = "((fromtime < ".time()." OR TRIM(fromtime) = '') AND (totime > ".time()." OR TRIM(totime) = ''))";
			if(in_array('copyof', $datafields)) {
				$copywheres[] = "(((d.fromtime < ".time()." OR TRIM(d.fromtime) = '') AND (d.totime > ".time()." OR TRIM(d.totime) = '')) OR ((copyd.fromtime < ".time()." OR TRIM(copyd.fromtime) = '') AND (copyd.totime > ".time()." OR TRIM(copyd.totime) = '')))";
				$copydataselect.= ", COALESCE(copyd.fromtime, d.fromtime) AS dfromtime";
				$copydataselect.= ", COALESCE(copyd.totime, d.totime) AS dtotime";
			}
		}
		
		if(in_array('onoff', $datafields)) {
    		$checkfields[] = 'onoff';
			$wheres[] = "onoff = '1'";
			$copywheres[] = "d.onoff = '1'";
		}
		
		if(in_array('lang', $datafields)) {
    		$checkfields[] = 'lang';
			$wheres[] = "lang = '".$this->pagelang."'";
		}
        
		if($filterwhere != '') {
			if(in_array('copyof', $datafields)) {
				foreach($datafields as $datafield) {
					if(preg_match("/\b".$datafield."\b/i", $filterwhere)) {
						if(!in_array($datafield, $checkfields))
							$checkfields[] = $datafield;
						if($datafield != $filterorder && $datafield != 'fromtime')
							$copydataselect.= ", COALESCE(copyd.".$datafield.", d.".$datafield.") AS d".$datafield;
						$filterwhere = preg_replace("/\b".$datafield."\b/i", 'd.'.$datafield, $filterwhere);
					}
				}
				$copyfilterwhere = str_replace('d.', 'copyd.', $filterwhere);
				$copywheres[] = '(('.$filterwhere.') OR ('.$copyfilterwhere.'))';
			}
			else {
				foreach($datafields as $datafield) {
					if(preg_match("/\b".$datafield."\b/i", $filterwhere) && !in_array($datafield, $checkfields))
						$checkfields[] = $datafield;
				}
				$wheres[] = '('.$filterwhere.')';
			}
		}
        
        if($filterorder != '') {
			if(in_array($filterorder, $datafields)) {
				if(!in_array($filterorder, $checkfields))
					$checkfields[] = $filterorder;
			}
			$order = $filterorder;
			if(in_array('copyof', $datafields) && $filterorder != 'fromtime') {
				$copydataselect.= ", COALESCE(copyd.".$filterorder.", d.".$filterorder.") AS d".$filterorder;
			}
		}
        elseif(array_key_exists('sortdatafield', $this->dbconfig) && $this->dbconfig['sortdatafield'] != '') {
        	$order = $this->dbconfig['sortdatafield'];
			if(in_array('copyof', $datafields) && $this->dbconfig['sortdatafield'] != 'fromtime') {
				$copydataselect.= ", COALESCE(copyd.".$this->dbconfig['sortdatafield'].", d.".$this->dbconfig['sortdatafield'].") AS d".$this->dbconfig['sortdatafield'];
			}
		}
        elseif(in_array('sort', $datafields)) {
			$order = 'sort';
		}
		else {
    		$order = 'id';
		}
        
        $datasperpage = (isset($this->dbconfig['datasperpage']) && $this->dbconfig['datasperpage'] != '') ? $this->dbconfig['datasperpage'] : '';
		
		$where = (count($wheres) >= 1) ? " WHERE ".implode(' AND ', $wheres) : "";
		$copywhere = (count($copywheres) >= 1 && in_array('copyof', $datafields)) ? " WHERE ".implode(' AND ', $copywheres) : "";
		
        $pager = (isset($this->get['pager'])) ? $this->validnum($this->get['pager']) : 1;
        $dir = ($filterdir != '') ? ' '.$filterdir : (isset($this->dbconfig['datasort']) && $this->dbconfig['datasort'] != '' ? ' '.$this->dbconfig['datasort'] : '');
        
        $checknextdata = 0;
        if($datasperpage != '') {
			if(in_array('copyof', $datafields)) {
				$copyorder = ($order != 'id' && $order != 'sort' ? 'd' : 'd.').$order.$dir;
				if($order != 'id' && $order != 'sort') {
					$copyorder.= ', d.id'.$dir;
				}
				$startcopywhere = ($startid != '') ? ($copywhere != '' ? " AND " : " WHERE ")."d.id NOT IN (".$startid.")" : "";
				$nextdata = $this->moddb->query("SELECT d.id, d.".implode(', d.', $checkfields).$copydataselect." FROM datas d".$copydatajoin.$copywhere.$startcopywhere." ORDER BY ".$copyorder." LIMIT ".($pager*$datasperpage).", 1");
			}
			else {
				if($order != 'id' && $order != 'sort') {
					$dataorder = $order.$dir.', id'.$dir;
				}
				else {
					$dataorder = $order.$dir;
				}
				$startwhere = ($startid != '') ? ($where != '' ? " AND " : " WHERE ")."id NOT IN (".$startid.")" : "";
				$nextdata = $this->moddb->query("SELECT id, ".implode(', ', $checkfields)." FROM datas".$where.$startwhere." ORDER BY ".$dataorder." LIMIT ".($pager*$datasperpage).", 1");
			}
			
            $checknextdata = $this->getdbnumrows($nextdata);
            $start = $pager*$datasperpage-$datasperpage;
            $limit = " LIMIT ".$start.", ".$datasperpage;
        }
        
        $topicdataliststart = '';
        $dbfields = implode(', ', $datafields);
        $datawhere = '';
        $copydatawhere = '';

		$sorttopicvalue = '';
        if($startid != '') {
            $datawhere = " AND datas.id NOT IN (".$startid.")";
            $copydatawhere = " AND d.id NOT IN (".$startid.")";
			
			$startdatas = $this->moddb->query("SELECT id, ".$dbfields." FROM datas WHERE datas.id = ".$startid." LIMIT 1");
			$startdatanumrows = $this->getdbnumrows($startdatas);
			
			if($startdatanumrows > 0) {
				$startdata = $startdatas->fetchArray(SQLITE3_ASSOC);
				if(isset($this->dbconfig['sorttopicfield']) && $this->dbconfig['sorttopicfield'] != '' && $this->dbconfig['sorttopicfield'] != 'topic')
					$sorttopicvalue = $startdata[$this->dbconfig['sorttopicfield']];
				
				if($pager == 1 || (array_key_exists('startontopic', $this->dbconfig) && $this->dbconfig['startontopic'] == 'show')) {
					if(file_exists($this->tplpath.'/topic_startdata.tpl'))
						$topicdataliststart = $this->loaddatatotpl($startdata, 'topic_startdata');
					else
						$topicdataliststart = $this->loaddatatotpl($startdata, 'data');
				}
			}
        }
		
		if(in_array('copyof', $datafields)) {
			$copyorder = ($order != 'id' && $order != 'sort' ? 'd' : 'd.').$order.$dir;
			if($order != 'id' && $order != 'sort') {
				$copyorder.= ', d.id'.$dir;
			}
			$datas = $this->moddb->query("SELECT d.id AS id, d.".implode(', d.', $datafields).$copydataselect." FROM datas d".$copydatajoin.$copywhere.$copydatawhere." ORDER BY ".$copyorder.$limit);
		}
		else {
			if($order != 'id' && $order != 'sort') {
				$dataorder = $order.$dir.', id'.$dir;
			}
			else {
				$dataorder = $order.$dir;
			}
			$datas = $this->moddb->query("SELECT id, ".$dbfields." FROM datas".$where.$datawhere." ORDER BY ".$dataorder.$limit);
		}
        
        $datanumrows = $this->getdbnumrows($datas);
        if($datanumrows > 0) {
            while($data = $datas->fetchArray(SQLITE3_ASSOC)) {
				if(in_array('copyof', $datafields) && $data['copyof'] != '') {
					$copyid = $data['id'];
					if(in_array('catid', $datafields))
						$copycatid = $data['catid'];
					if(in_array('topicid', $datafields))
						$copytopicid = $data['topicid'];
					$copydata = $this->moddb->query("SELECT id, ".$dbfields." FROM datas WHERE datas.id = ".$data['copyof']." LIMIT 1");
					$data = $copydata->fetchArray(SQLITE3_ASSOC);
					$data['id'] = $copyid;
					if(in_array('catid', $datafields))
						$data['catid'] = $copycatid;
					if(in_array('topicid', $datafields))
						$data['topicid'] = $copytopicid;
				}
        		$topicdatalist[] = $this->loaddatatotpl($data, 'data');
            }
            if($datasperpage != '') {
                $this->setviewpager($pager, $checknextdata);
            }
        }
        
        if($topicdataliststart != '')
            array_unshift($topicdatalist, $topicdataliststart);
        
        $topicdata = str_replace('###LOOP_TOPICDATA###', implode("\n", $topicdatalist), $topicdata);
        
        $pager = '';
        if(count($this->pager) > 0) {
            $pager = $this->getpagernavi();
        }
        
        $topicdata = str_replace('###LOAD_PAGER###', $pager, $topicdata);
        $topicdata = str_replace('###BREADCRUMB###', $breadcrumb, $topicdata);
        
        if($loadsubcats == 1) {
    		$subcatlist = $this->getviewcatlist();
            $subcats = (is_array($subcatlist) && count($subcatlist) > 0) ? $this->setviewcattotpl($subcatlist, 'sub') : $this->setviewcattotpl(array(), 'sub');
            $topicdata = str_replace('###LOAD_SUB_CATEGORIES###', $subcats, $topicdata);
        }
        
		if($filterform == 1) {
			ob_start();
			$module = $this;
			include $this->tplpath.'/filter.tpl';
			$filtertpl = ob_get_contents();
			ob_end_clean();
			
			$params = $this->getcurrenturl();
			$reseturl = 'index.php?page='.$this->getpage.$params['cat'].$params['order'].$params['topic'].'&amp;filter=reset';
			$formurl = 'index.php?page='.$this->getpage.$params['cat'].$params['order'].$params['topic'];
			if(!isset($this->dbconfig['filtermaintain'])) $formurl.= '&amp;filter=query';
            $filtertpl = str_replace('###FILTER_RESETURL###', $reseturl, $filtertpl);
            $filtertpl = str_replace('###FILTER_FORMURL###', $formurl, $filtertpl);
            $topicdata = str_replace('###LOAD_FILTER###', $filtertpl, $topicdata);
		}
        
        if(preg_match('~###BACK_CATEGORY_LINK###~i', $topicdata)) {
			if($this->basecat != '' && in_array('basecat', $this->fileconfig['base']) && (!in_array('subcats', $this->fileconfig['base']) || (!isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])))) {
				$topicdata = preg_replace('~<a(?:([^>]+))?href=\"###BACK_CATEGORY_LINK###\"(?:([^>]+))?>(?:([^<]+))?</a>~Umsi', '', $topicdata);
			}
			else {
				$topicdata = str_replace('###BACK_CATEGORY_LINK###', 'index.php?page='.$this->getpage, $topicdata);
			}
		}
		
        $urlcat = '';
        if(isset($this->get[$this->params['cat']]) && $catid != '') {
            $urlcat = '&amp;'.$this->params['cat'].'='.$catid;
        }
        elseif(isset($this->get[$this->params['order']]) && array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
            $urlcat = '&amp;'.$this->params['order'].'='.$this->validorder($this->get[$this->params['order']]);
        }
        $link_topiclist = 'index.php?page='.$this->getpage.$urlcat;
        $topicdata = str_replace('###LINK_TOPIC_TO_LIST###', $link_topiclist, $topicdata);
        
        if(preg_match('~###LINK_PREV_TOPIC###~i', $topicdata) && preg_match('~###LINK_NEXT_TOPIC###~i', $topicdata)) {
            $prevtopic = '';
            $nexttopic = '';
			$checkdir = isset($this->dbconfig['topicsort']) && $this->dbconfig['topicsort'] != '' ? $this->dbconfig['topicsort'] : 'ASC';
            if($loadbydatafield != '') {
				if(in_array('copyof', $topicfields)) {
					$firstprevfound = "SELECT DISTINCT 
						t.id AS id, t.".implode(', t.', $topicfields).", d.".$loadbydatafield.", 
						COALESCE(copyt.id, t.id) AS tid, 
						COALESCE(copyt.startid, t.startid) AS tstartid 
						FROM topics t 
						LEFT JOIN topics copyt ON copyt.id = t.copyof 
						LEFT JOIN datas d ON d.id = tstartid 
						WHERE ".(in_array('catid', $topicfields) ? "t.catid = '".$catid."' AND " : '')."t.onoff = '1' AND d.".$loadbydatafield." ".($checkdir == 'ASC' ? '<' : '>')." '".$sorttopicvalue."' 
						ORDER BY d.".$loadbydatafield." ".($checkdir == 'ASC' ? 'DESC' : 'ASC')." LIMIT 1";
					
					$firstprevtopics = $this->moddb->query($firstprevfound);
					$firstprevtopic = '';
					if($this->getdbnumrows($firstprevtopics) > 0) {
						$firstprev = $firstprevtopics->fetchArray(SQLITE3_ASSOC);
						$firstprevtopic = $firstprev[$loadbydatafield];
					}
					
					$prevquery = "SELECT DISTINCT 
						t.id AS id, t.".implode(', t.', $topicfields).", d.".$loadbydatafield.", 
						COALESCE(copyt.id, t.id) AS tid, 
						COALESCE(copyt.startid, t.startid) AS tstartid 
						FROM topics t 
						LEFT JOIN topics copyt ON copyt.id = t.copyof 
						LEFT JOIN datas d ON d.id = tstartid 
						WHERE ".(in_array('catid', $topicfields) ? "t.catid = '".$catid."' AND " : '')."t.onoff = '1' ";
					if($firstprevtopic != '' && $firstprevtopic != $sorttopicvalue)
						$prevquery.= "AND (d.".$loadbydatafield." = '".$firstprevtopic."' 
						OR d.".$loadbydatafield." = '".$sorttopicvalue."') ";
					else
						$prevquery.= "AND d.".$loadbydatafield." = '".$sorttopicvalue."' ";
					$prevquery.= "ORDER BY d.".$loadbydatafield." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').", id ".($checkdir == 'ASC' ? 'DESC' : 'ASC');

					$prevtopics = $this->moddb->query($prevquery);
					if($this->getdbnumrows($prevtopics) > 0) {
						$lastid = '';
						$previd = false;
						while($prev = $prevtopics->fetchArray(SQLITE3_ASSOC)) {
							if($previd) {
								$prevtopic = $prev['id'];
								$previd = false;
								break;
							}
							if($prev['id'] == $topicid) {
								$previd = true;
							}
						}
					}
					unset($prevquery);
					
					$firstnextfound = "SELECT DISTINCT 
						t.id AS id, t.".implode(', t.', $topicfields).", d.".$loadbydatafield.", 
						COALESCE(copyt.id, t.id) AS tid, 
						COALESCE(copyt.startid, t.startid) AS tstartid 
						FROM topics t 
						LEFT JOIN topics copyt ON copyt.id = t.copyof 
						LEFT JOIN datas d ON d.id = tstartid 
						WHERE ".(in_array('catid', $topicfields) ? "t.catid = '".$catid."' AND " : '')."t.onoff = '1' 
						AND d.".$loadbydatafield." ".($checkdir == 'ASC' ? '>' : '<')." '".$sorttopicvalue."' 
						ORDER BY d.".$loadbydatafield." ".($checkdir == 'ASC' ? 'ASC' : 'DESC')." LIMIT 1";
					
					$firstnexttopics = $this->moddb->query($firstnextfound);
					$firstnexttopic = '';
					if($this->getdbnumrows($firstnexttopics) > 0) {
						$firstnext = $firstnexttopics->fetchArray(SQLITE3_ASSOC);
						$firstnexttopic = $firstnext[$loadbydatafield];
					}
					
					$nextquery = "SELECT DISTINCT 
						t.id AS id, t.".implode(', t.', $topicfields).", d.".$loadbydatafield.", 
						COALESCE(copyt.id, t.id) AS tid, 
						COALESCE(copyt.startid, t.startid) AS tstartid 
						FROM topics t 
						LEFT JOIN topics copyt ON copyt.id = t.copyof 
						LEFT JOIN datas d ON d.id = tstartid 
						WHERE ".(in_array('catid', $topicfields) ? "t.catid = '".$catid."' AND " : '')."t.onoff = '1' ";
					if($firstnexttopic != '' && $firstnexttopic != $sorttopicvalue)
						$nextquery.= "AND (d.".$loadbydatafield." = '".$firstnexttopic."' 
						OR d.".$loadbydatafield." = '".$sorttopicvalue."') ";
					else
						$nextquery.= "AND d.".$loadbydatafield." = '".$sorttopicvalue."' ";
					$nextquery.= "ORDER BY d.".$loadbydatafield." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').", id ".($checkdir == 'ASC' ? 'DESC' : 'ASC');
					
					$nexttopics = $this->moddb->query($nextquery);
					if($this->getdbnumrows($nexttopics) > 0) {
						$lastid = '';
						$nextid = false;
						while($next = $nexttopics->fetchArray(SQLITE3_ASSOC)) {
							if($lastid != '' && $next['id'] == $topicid) {
								$nexttopic = $lastid;
								$lastid = '';
								break;
							}
							$lastid = $next['id'];
						}
					}
					unset($nextquery);
				}
				else {
					$firstprevfound = "SELECT DISTINCT 
						t.id AS id, t.".implode(', t.', $topicfields).", d.".$loadbydatafield." 
						FROM topics t 
						LEFT JOIN datas d ON d.id = t.startid 
						WHERE ".(in_array('catid', $topicfields) ? "t.catid = '".$catid."' AND " : '')."t.onoff = '1' AND d.".$loadbydatafield." ".($checkdir == 'ASC' ? '<' : '>')." '".$sorttopicvalue."' 
						ORDER BY d.".$loadbydatafield." ".($checkdir == 'ASC' ? 'DESC' : 'ASC')." LIMIT 1";
					
					$firstprevtopics = $this->moddb->query($firstprevfound);
					$firstprevtopic = '';
					if($this->getdbnumrows($firstprevtopics) > 0) {
						$firstprev = $firstprevtopics->fetchArray(SQLITE3_ASSOC);
						$firstprevtopic = $firstprev[$loadbydatafield];
					}
					
					$prevquery = "SELECT DISTINCT 
						t.id AS id, t.".implode(', t.', $topicfields).", d.".$loadbydatafield." 
						FROM topics t 
						LEFT JOIN datas d ON d.id = t.startid 
						WHERE ".(in_array('catid', $topicfields) ? "t.catid = '".$catid."' AND " : '')."t.onoff = '1' ";
					if($firstprevtopic != '' && $firstprevtopic != $sorttopicvalue)
						$prevquery.= "AND (d.".$loadbydatafield." = '".$firstprevtopic."' 
						OR d.".$loadbydatafield." = '".$sorttopicvalue."') ";
					else
						$prevquery.= "AND d.".$loadbydatafield." = '".$sorttopicvalue."' ";
					$prevquery.= "ORDER BY d.".$loadbydatafield." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').", id ".($checkdir == 'ASC' ? 'DESC' : 'ASC');

					$prevtopics = $this->moddb->query($prevquery);
					if($this->getdbnumrows($prevtopics) > 0) {
						$lastid = '';
						$previd = false;
						while($prev = $prevtopics->fetchArray(SQLITE3_ASSOC)) {
							if($previd) {
								$prevtopic = $prev['id'];
								$previd = false;
								break;
							}
							if($prev['id'] == $topicid) {
								$previd = true;
							}
						}
					}
					unset($prevquery);
					
					
					$firstnextfound = "SELECT DISTINCT 
						t.id AS id, t.".implode(', t.', $topicfields).", d.".$loadbydatafield." 
						FROM topics t 
						LEFT JOIN datas d ON d.id = t.startid 
						WHERE ".(in_array('catid', $topicfields) ? "t.catid = '".$catid."' AND " : '')."t.onoff = '1' AND d.".$loadbydatafield." ".($checkdir == 'ASC' ? '>' : '<')." '".$sorttopicvalue."' 
						ORDER BY d.".$loadbydatafield." ".($checkdir == 'ASC' ? 'ASC' : 'DESC')." LIMIT 1";
					
					$firstnexttopics = $this->moddb->query($firstnextfound);
					$firstnexttopic = '';
					if($this->getdbnumrows($firstnexttopics) > 0) {
						$firstnext = $firstnexttopics->fetchArray(SQLITE3_ASSOC);
						$firstnexttopic = $firstnext[$loadbydatafield];
					}
					
					$nextquery = "SELECT DISTINCT 
						t.id AS id, t.".implode(', t.', $topicfields).", d.".$loadbydatafield." 
						FROM topics t 
						LEFT JOIN datas d ON d.id = t.startid 
						WHERE ".(in_array('catid', $topicfields) ? "t.catid = '".$catid."' AND " : '')."t.onoff = '1' ";
					if($firstnexttopic != '' && $firstnexttopic != $sorttopicvalue)
						$nextquery.= "AND (d.".$loadbydatafield." = '".$firstnexttopic."' 
						OR d.".$loadbydatafield." = '".$sorttopicvalue."') ";
					else
						$nextquery.= "AND d.".$loadbydatafield." = '".$sorttopicvalue."' ";
					$nextquery.= "ORDER BY d.".$loadbydatafield." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').", id ".($checkdir == 'ASC' ? 'DESC' : 'ASC');
					
					$nexttopics = $this->moddb->query($nextquery);
					if($this->getdbnumrows($nexttopics) > 0) {
						$lastid = '';
						$nextid = false;
						while($next = $nexttopics->fetchArray(SQLITE3_ASSOC)) {
							if($lastid != '' && $next['id'] == $topicid) {
								$nexttopic = $lastid;
								$lastid = '';
								break;
							}
							$lastid = $next['id'];
						}
					}
					unset($nextquery);
				}
            }
            else {
				if(in_array('copyof', $topicfields) && $topicorder != 'sort' && $topicorder != 'id') {
					$firstprevfound = "SELECT DISTINCT 
						t.id, t.".implode(', t.', $topicfields).$copytopicselect." 
						FROM topics t 
						LEFT JOIN topics copyt ON copyt.id = t.copyof 
						WHERE ".$jointopicwhere." 
						AND t".$topicorder." ".($checkdir == 'ASC' ? '<' : '>')." '".$topic[$topicorder]."' 
						ORDER BY t".$topicorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC')." LIMIT 1";
					
					$firstprevtopics = $this->moddb->query($firstprevfound);
					$firstprevtopic = '';
					if($this->getdbnumrows($firstprevtopics) > 0) {
						$firstprev = $firstprevtopics->fetchArray(SQLITE3_ASSOC);
						$firstprevtopic = $firstprev['t'.$topicorder];
					}
					
					$prevquery = "SELECT DISTINCT 
						t.id, t.".implode(', t.', $topicfields).$copytopicselect." 
						FROM topics t 
						LEFT JOIN topics copyt ON copyt.id = t.copyof 
						WHERE ".$jointopicwhere." ";
					if($firstprevtopic != '' && $firstprevtopic != $topic[$topicorder])
						$prevquery.= "AND (t".$topicorder." = '".$firstprevtopic."' OR t".$topicorder." = '".$topic[$topicorder]."') ";
					else
						$prevquery.= "AND t".$topicorder." = '".$topic[$topicorder]."' ";
					$prevquery.= "ORDER BY t".$topicorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').", t.id ".($checkdir == 'ASC' ? 'DESC' : 'ASC');
					
					
					$prevtopics = $this->moddb->query($prevquery);
					if($this->getdbnumrows($prevtopics) > 0) {
						$lastid = '';
						$previd = false;
						while($prev = $prevtopics->fetchArray(SQLITE3_ASSOC)) {
							if($previd) {
								$prevtopic = $prev['id'];
								$previd = false;
								break;
							}
							if($prev['id'] == $topicid) {
								$previd = true;
							}
						}
					}
					unset($prevquery);
					
					
					$firstnextfound = "SELECT DISTINCT 
						t.id, t.".implode(', t.', $topicfields).$copytopicselect." 
						FROM topics t 
						LEFT JOIN topics copyt ON copyt.id = t.copyof 
						WHERE ".$jointopicwhere." 
						AND t".$topicorder." ".($checkdir == 'ASC' ? '>' : '<')." '".$topic[$topicorder]."' 
						ORDER BY t".$topicorder." ".($checkdir == 'ASC' ? 'ASC' : 'DESC')." LIMIT 1";
					
					$firstnexttopics = $this->moddb->query($firstnextfound);
					$firstnexttopic = '';
					if($this->getdbnumrows($firstnexttopics) > 0) {
						$firstnext = $firstnexttopics->fetchArray(SQLITE3_ASSOC);
						$firstnexttopic = $firstnext['t'.$topicorder];
					}
					
					$nextquery = "SELECT DISTINCT 
						t.id, t.".implode(', t.', $topicfields).$copytopicselect." 
						FROM topics t 
						LEFT JOIN topics copyt ON copyt.id = t.copyof 
						WHERE ".$jointopicwhere." ";
					if($firstnexttopic != '' && $firstnexttopic != $topic[$topicorder])
						$nextquery.= "AND (t".$topicorder." = '".$firstnexttopic."' OR t".$topicorder." = '".$topic[$topicorder]."') ";
					else
						$nextquery.= "AND t".$topicorder." = '".$topic[$topicorder]."' ";
					$nextquery.= "ORDER BY t".$topicorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').", t.id ".($checkdir == 'ASC' ? 'DESC' : 'ASC');
					
					$nexttopics = $this->moddb->query($nextquery);
					if($this->getdbnumrows($nexttopics) > 0) {
						$lastid = '';
						$nextid = false;
						while($next = $nexttopics->fetchArray(SQLITE3_ASSOC)) {
							if($lastid != '' && $next['id'] == $topicid) {
								$nexttopic = $lastid;
								$lastid = '';
								break;
							}
							$lastid = $next['id'];
						}
					}
					unset($nextquery);
				}
				else {
					$firstprevfound = "SELECT 
						id, ".implode(', ', $topicfields)." 
						FROM topics 
						WHERE ".$topicwhere." 
						AND ".$topicorder." ".($checkdir == 'ASC' ? '<' : '>')." '".$topic[$topicorder]."' 
						ORDER BY ".$topicorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC')." 
						LIMIT 1";
					
					$firstprevtopics = $this->moddb->query($firstprevfound);
					$firstprevtopic = '';
					if($this->getdbnumrows($firstprevtopics) > 0) {
						$firstprev = $firstprevtopics->fetchArray(SQLITE3_ASSOC);
						$firstprevtopic = $firstprev[$topicorder];
					}
					$prevquery = "SELECT 
						id, ".implode(', ', $topicfields)." 
						FROM topics 
						WHERE ".$topicwhere." ";
					if($firstprevtopic != '' && $firstprevtopic != $topic[$topicorder])
						$prevquery.= "AND (".$topicorder." = '".$firstprevtopic."' OR ".$topicorder." = '".$topic[$topicorder]."') ";
					else
						$prevquery.= "AND ".$topicorder." = '".$topic[$topicorder]."' ";
					$prevquery.= "ORDER BY ".$topicorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').", id ".($checkdir == 'ASC' ? 'DESC' : 'ASC');
					
					$prevtopics = $this->moddb->query($prevquery);
					if($this->getdbnumrows($prevtopics) > 0) {
						$lastid = '';
						$previd = false;
						while($prev = $prevtopics->fetchArray(SQLITE3_ASSOC)) {
							if($previd) {
								$prevtopic = $prev['id'];
								$previd = false;
								break;
							}
							if($prev['id'] == $topicid) {
								$previd = true;
							}
						}
					}
					unset($prevquery);
					
					
					$firstnextfound = "SELECT 
						id, ".implode(', ', $topicfields)." 
						FROM topics 
						WHERE ".$topicwhere." 
						AND ".$topicorder." ".($checkdir == 'ASC' ? '>' : '<')." '".$topic[$topicorder]."' 
						ORDER BY ".$topicorder." ".($checkdir == 'ASC' ? 'ASC' : 'DESC')." 
						LIMIT 1";
					
					$firstnexttopics = $this->moddb->query($firstnextfound);
					$firstnexttopic = '';
					if($this->getdbnumrows($firstnexttopics) > 0) {
						$firstnext = $firstnexttopics->fetchArray(SQLITE3_ASSOC);
						$firstnexttopic = $firstnext[$topicorder];
					}
					
					$nextquery = "SELECT 
						id, ".implode(', ', $topicfields)." 
						FROM topics 
						WHERE ".$topicwhere." ";
					if($firstnexttopic != '' && $firstnexttopic != $topic[$topicorder])
						$nextquery.= "AND (".$topicorder." = '".$firstnexttopic."' OR ".$topicorder." = '".$topic[$topicorder]."') ";
					else
						$nextquery.= "AND ".$topicorder." = '".$topic[$topicorder]."' ";
					$nextquery.= "ORDER BY ".$topicorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').", id ".($checkdir == 'ASC' ? 'DESC' : 'ASC');
					
					$nexttopics = $this->moddb->query($nextquery);
					if($this->getdbnumrows($nexttopics) > 0) {
						$lastid = '';
						$nextid = false;
						while($next = $nexttopics->fetchArray(SQLITE3_ASSOC)) {
							if($lastid != '' && $next['id'] == $topicid) {
								$nexttopic = $lastid;
								$lastid = '';
								break;
							}
							$lastid = $next['id'];
						}
					}
					unset($nextquery);
				}
            }
            
            if($prevtopic == '') {
                $topicdata = preg_replace('~<a(?:([^>]+))?(href=\"###LINK_PREV_TOPIC###\")(?:([^>]+))?(>)(?:([^<]+))?</a>~Umsi', '', $topicdata);
            }
            else {
                $link_prevtopic = 'index.php?page='.$this->getpage.$urlcat.'&amp;'.$this->params['topic'].'='.$prevtopic;
                $topicdata = str_replace('###LINK_PREV_TOPIC###', $link_prevtopic, $topicdata);
            }
            
            if($nexttopic == '') {
                $topicdata = preg_replace('~<a(?:([^>]+))?(href=\"###LINK_NEXT_TOPIC###\")(?:([^>]+))?(>)(?:([^<]+))?</a>~Umsi', '', $topicdata);
            }
            else {
                $link_nexttopic = 'index.php?page='.$this->getpage.$urlcat.'&amp;'.$this->params['topic'].'='.$nexttopic;
                $topicdata = str_replace('###LINK_NEXT_TOPIC###', $link_nexttopic, $topicdata);
            }
            
            if(preg_match('~###LOAD_NEWDATA_TPL###~i', $topicdata)) {
				$datanew = $this->getnewdata();
            	$topicdata = str_replace('###LOAD_NEWDATA_TPL###', $datanew, $topicdata);
            }
            elseif(preg_match('~###LINK_NEWDATA###~i', $topicdata)) {
                $link_datanew = 'index.php?page='.$this->getpage;
                if(isset($this->get[$this->params['cat']])) {
                    $catid = $this->validnum($this->get[$this->params['cat']]);
                    $link_datanew.= '&amp;'.$this->params['cat'].'='.$catid;
                }
                elseif(isset($this->get[$this->params['order']])) {
                    $catorder = $this->validorder($this->get[$this->params['order']]);
                    $link_datanew.= '&amp;'.$this->params['order'].'='.$catorder;
                }
                if(isset($this->get[$this->params['topic']])) {
                    $topicid = $this->validnum($this->get[$this->params['topic']]);
                    $link_datanew.= '&amp;'.$this->params['topic'].'='.$topicid;
                }
                $link_datanew.= '&amp;'.$this->params['load'].'='.$this->params['data'].'new';
                
            	$topicdata = str_replace('###LINK_NEWDATA###', $link_datanew, $topicdata);
            }
        }
        
        return $topicdata;
    }
    
    function loadtopictotpl($source, $tplfile)
    {
		$topictpl = $this->tplpath.'/'.$tplfile.'.tpl';
        $topicout = array();
        $topicdata = '';
        
        $topicid = $source['id'];
        $catid = array_key_exists('catid', $source) ? $source['catid'] : '';
        
        foreach($source as $topickey => $topicvalue)
        {
            if($topickey == 'topic') {
    			if($this->is_serialized($topicvalue)) {
    				$topic_datafield = unserialize($topicvalue);
                    $topicout['topic'] = $topic_datafield[$this->pagelang];
    			}
    			else {
                    $topicout['topic'] = $topicvalue;
    			}
                
                $cat = '';
                if(isset($this->get[$this->params['cat']])) {
                    $cat = '&amp;'.$this->params['cat'].'='.$this->validnum($this->get[$this->params['cat']]);
                }
                elseif(isset($this->get[$this->params['order']]) && array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
                    $orders = array();
    	        	$getorders = explode('_', $this->get[$this->params['order']]);
        			foreach($getorders as $getorder) {
                        $orders[] = $this->validnum($getorder);
    	        	}
                    if($this->catorder != '' && $catid != '') {
            			foreach($this->catorder as $sub) {
                            if($sub[0] == $catid) {
        						$orders[] = $sub[1];
                                break;
                            }
        	        	}
                    }
                    $cat = '&amp;'.$this->params['order'].'='.implode('_', $orders);
                }
                $topiclink = 'index.php?page='.$this->getpage.$cat.'&amp;'.$this->params['topic'].'='.$topicid;
                
                ob_start();
                $db_data = $topicout;
				include $topictpl;
                $topicdata = ob_get_contents();
            	ob_end_clean();
				
                $topicdata = str_replace('###TOPIC_LINK###', $topiclink, $topicdata);
            }
        }
        
        return $topicdata;
    }
    
    function gettopicstartid($topicid)
    {
		$startid = '';
		
		$getstartid = $this->moddb->query("SELECT id, startid FROM topics WHERE topics.id = '".$topicid."' LIMIT 1");
		$checknumrows = $this->getdbnumrows($getstartid);
		if($checknumrows > 0) {
			$isstartid = $getstartid->fetchArray();
			$startid = $isstartid['startid'];
		}
		
		return $startid;
    }
    
    function getviewdata()
    {
		$catid = '';
		$topicid = '';
		$dataid = '';
        $datatpl = '';
		
		if($this->basecat != '' && !isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])) {
			$catid = $this->basecat;
		}
		elseif(isset($this->get[$this->params['cat']])) {
			$catid = $this->validnum($this->get[$this->params['cat']]);
		}
		elseif(isset($this->get[$this->params['order']]) && array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
			if($this->curcat == '')
				$this->getcatsfromsubs();
			$catid = $this->curcat;
		}
		
		if(isset($this->get[$this->params['topic']])) {
			$topicid = $this->validnum($this->get[$this->params['topic']]);
		}
		
		if(isset($this->get[$this->params['data']])) {
			$dataid = $this->validnum($this->get[$this->params['data']]);
		}
        if($dataid != '') {
            $datafields = $this->fileconfig['data'];
			
            $selects = array();
			$wheres = array();
			$joins = array();
			$copyselects = array();
			$copyjoins = array();
			
			$getdatawheres = array();
			if(array_key_exists('topic', $this->fileconfig) || array_key_exists('cat', $this->fileconfig)) {
				$selects[] = 'd.id AS id';
				$getdatawheres[] = "d.id = ".$dataid;
				
				if(in_array('fromtime', $datafields)) {
					$fromtodatawhere = "((d.fromtime < ".time()." OR TRIM(d.fromtime) = '') AND (d.totime > ".time()." OR TRIM(d.totime) = ''))";
					if(in_array('copyof', $datafields)) {
						$getdatawheres[] = "(".$fromtodatawhere." OR ((copyd.fromtime < ".time()." OR TRIM(copyd.fromtime) = '') AND (copyd.totime > ".time()." OR TRIM(copyd.totime) = '')))";
					}
					else {
						$getdatawheres[] = $fromtodatawhere;
					}
				}
				
				if(in_array('onoff', $datafields))
					$getdatawheres[] = "d.onoff = '1'";
				
				if(in_array('lang', $datafields))
					$getdatawheres[] = "d.lang = '".$this->pagelang."'";
				
				if(in_array('copyof', $datafields)) {
					if(in_array('fromtime', $datafields)) {
						$copyselects[] = "COALESCE(copyd.fromtime, d.fromtime) AS dfromtime";
						$copyselects[] = "COALESCE(copyd.totime, d.totime) AS dtotime";
					}
					$copyjoins[] = "LEFT JOIN datas copyd ON copyd.id = d.copyof";
				}
			}
			else {
				$getdatawheres[] = "id = '".$dataid."'";
				
				if(in_array('fromtime', $datafields)) {
					$getdatawheres[] = "((fromtime < ".time()." OR TRIM(fromtime) = '') AND (totime > ".time()." OR TRIM(totime) = ''))";
				}
				
				if(in_array('onoff', $datafields))
					$getdatawheres[] = "onoff = '1'";
				
				if(in_array('lang', $datafields))
					$getdatawheres[] = "lang = '".$this->pagelang."'";
			}
			
			if(count($getdatawheres) > 0)
				$wheres[] = implode(" AND ", $getdatawheres);
			
			
			if(array_key_exists('topic', $this->fileconfig)) {
				$topicfields = $this->fileconfig['topic'];
				$topicwheres = array();
				
				$joins[] = 'topics t';
				
				$selects[] = 't.id AS tid';
				$topicwheres[] = "t.id = ".$topicid;
				
				if(in_array('catid', $topicfields)) {
					$selects[] = 't.catid';
					$topicwheres[] = "t.catid = ".$catid;
				}
				
				if(in_array('fromtime', $topicfields)) {
					$selects[] = 't.fromtime';
					if(in_array('copyof', $topicfields))
						$selects[] = 'copyt.fromtime';
					$selects[] = 't.totime';
					if(in_array('copyof', $topicfields))
						$selects[] = 'copyt.totime';
					
					$fromtotopicwhere = "((t.fromtime < ".time()." OR TRIM(t.fromtime) = '') AND (t.totime > ".time()." OR TRIM(t.totime) = ''))";
					if(in_array('copyof', $topicfields)) {
						$copyselects[] = "COALESCE(copyt.fromtime, t.fromtime) AS tfromtime";
						$copyselects[] = "COALESCE(copyt.totime, t.totime) AS ttotime";
						$topicwheres[] = "(".$fromtotopicwhere." OR ((copyt.fromtime < ".time()." OR TRIM(copyt.fromtime) = '') AND (copyt.totime > ".time()." OR TRIM(copyt.totime) = '')))";
					}
					else {
						$topicwheres[] = $fromtotopicwhere;
					}
				}
				
				if(in_array('onoff', $topicfields)) {
					$selects[] = 't.onoff AS tonoff';
					$topicwheres[] = "t.onoff = '1'";
				}
				
				if(in_array('copyof', $topicfields))
					$selects[] = 't.copyof AS tcopyof';
				
				if(in_array('lang', $topicfields)) {
					$selects[] = 't.lang';
					if(in_array('copyof', $topicfields)) {
						$selects[] = 'copyt.lang';
						$copyselects[] = "COALESCE(copyt.lang, t.lang) AS tlang";
						$topicwheres[] = "tlang = '".$this->pagelang."'";
					}
					else {
						$topicwheres[] = "t.lang = '".$this->pagelang."'";
					}
				}
				
				if(in_array('copyof', $topicfields)) {
					$copyjoins[] = "LEFT JOIN topics t ON t.id = ".$topicid;
					$copyjoins[] = "LEFT JOIN topics copyt ON copyt.id = t.copyof";
				}
				else {
					$copyjoins[] = "LEFT JOIN topics t ON t.id = d.topicid";
				}
				
				if(count($topicwheres) > 0)
					$wheres[] = implode(" AND ", $topicwheres);
			}
			
			
			if(array_key_exists('cat', $this->fileconfig)) {
				$catfields = $this->fileconfig['cat'];
				$catwheres = array();
				
				$joins[] = 'cats c';
				$selects[] = 'c.id AS cid';
				
				if(in_array('catid', $datafields) || (array_key_exists('topic', $this->fileconfig) && in_array('catid', $this->fileconfig['topic']) && !in_array('copyof', $this->fileconfig['topic'])))
					$catwheres[] = "cid = ".$catid;
				
				if(in_array('onoff', $catfields)) {
					$selects[] = 'c.onoff AS conoff';
					$catwheres[] = "c.onoff = '1'";
				}
				
				if(in_array('lang', $catfields)) {
					$selects[] = 'c.lang';
					$catwheres[] = "c.lang = '".$this->pagelang."'";
				}
				
				if(array_key_exists('topic', $this->fileconfig)) {
					$copyjoins[] = "LEFT JOIN cats c ON c.id = t.catid";
				}
				else {
					$copyjoins[] = "LEFT JOIN cats c ON c.id = d.catid";
				}
				
				if(count($catwheres) > 0)
					$wheres[] = implode(" AND ", $catwheres);
			}
			
			$select = (count($selects) > 0) ? implode(", ", $selects) : '';
			$copyselect = (count($copyselects) > 0) ? ', '.implode(', ', $copyselects) : '';
			$copyjoin = (count($copyjoins) > 0) ? implode(' ', $copyjoins) : '';
			$join = (count($joins) > 0) ? " INNER JOIN ".implode(", ", $joins) : '';
			$where = (count($wheres) > 0) ? " WHERE ".implode(" AND ", $wheres) : '';
			
			if(array_key_exists('topic', $this->fileconfig) || array_key_exists('cat', $this->fileconfig)) {
				$datas = $this->moddb->query("SELECT ".$select.", d.".implode(', d.', $datafields).$copyselect." FROM datas d ".$copyjoin.$where." LIMIT 1");
			}
			else {
				$datas = $this->moddb->query("SELECT id, ".implode(', ', $datafields)." FROM datas".$where." LIMIT 1");
			}
            
            $datanumrows = $this->getdbnumrows($datas);
            
            $data = '';
            if($datanumrows == 0) {
				return $this->viewdefine('dataoff');
			}
			else {
                $data = $datas->fetchArray(SQLITE3_ASSOC);
				if(in_array('copyof', $datafields) && $data['copyof'] != '') {
					$dataid = $data['id'];
					$dataonoff = $data['onoff'];
					if(in_array('catid', $datafields))
						$datacatid = $data['catid'];
					if(in_array('topicid', $datafields))
						$datatopicid = $data['topicid'];
					if(in_array('sort', $datafields))
						$datasort = $data['sort'];
					$datas = $this->moddb->query("SELECT id, ".implode(', ', $datafields)." FROM datas WHERE id = '".$data['copyof']."' LIMIT 1");
					$data = $datas->fetchArray(SQLITE3_ASSOC);
					$data['id'] = $dataid;
					$data['onoff'] = $dataonoff;
					if(in_array('catid', $datafields))
						$data['catid'] = $datacatid;
					if(in_array('topicid', $datafields))
						$data['topicid'] = $datatopicid;
					if(in_array('sort', $datafields))
						$data['sort'] = $datasort;
				}
				if(in_array('seotitle', $this->fileconfig['data'])) {
					if($this->is_serialized($data['seotitle'])) {
						$dataseotitlelangs = unserialize($data['seotitle']);
						$dataseotitle = $dataseotitlelangs[$this->pagelang];
						$dataseodesclangs = unserialize($data['seodesc']);
						$dataseodesc = $dataseodesclangs[$this->pagelang];
						$dataseokeyslangs = unserialize($data['seokeys']);
						$dataseokeys = $dataseokeyslangs[$this->pagelang];
					}
					else {
						$dataseotitle = $data['seotitle'];
						$dataseodesc = $data['seodesc'];
						$dataseokeys = $data['seokeys'];
					}
					if($dataseotitle != '')
						$this->metadata['title'] = $dataseotitle;
					if($dataseodesc != '')
						$this->metadata['description'] = $dataseodesc;
					if($dataseokeys != '')
						$this->metadata['keywords'] = $dataseokeys;
					
					unset($data['seotitle']);
					unset($data['seodesc']);
					unset($data['seokeys']);
				}
				elseif(array_key_exists('headlinetitle', $this->dbconfig) && $this->dbconfig['headlinetitle'] == 'show') {
					if($this->is_serialized($data[$this->dbconfig['title']])) {
						$datalangs = unserialize($data[$this->dbconfig['title']]);
						$dataheadline = $datalangs[$this->pagelang];
					}
					else {
						$dataheadline = $data[$this->dbconfig['title']];
					}
					$this->metadata['title'] = $dataheadline;
				}
				
                $datatpl = $this->loaddatatotpl($data, 'datafull');
				
				$breadcrumb = (array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && preg_match('~###BREADCRUMB###~i', $datatpl)) ? $this->getbreadcrumb() : '';
                
                $loadsubcats = (array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && preg_match('~###LOAD_SUB_CATEGORIES###~i', $datatpl)) ? 1 : 0;
                
				$datatpl = str_replace('###BREADCRUMB###', $breadcrumb, $datatpl);
				
                if($loadsubcats == 1) {
            		$subcatlist = $this->getviewcatlist();
                    $subcats = (is_array($subcatlist) && count($subcatlist) > 0) ? $this->setviewcattotpl($subcatlist, 'sub') : $this->setviewcattotpl(array(), 'sub');
                    $datatpl = str_replace('###LOAD_SUB_CATEGORIES###', $subcats, $datatpl);
                }
				
				if(preg_match('~###BACK_CATEGORY_LINK###~i', $datatpl)) {
					if($this->basecat != '' && in_array('basecat', $this->fileconfig['base']) && (!in_array('subcats', $this->fileconfig['base']) || (!isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])))) {
						$datatpl = preg_replace('~<a(?:([^>]+))?href=\"###BACK_CATEGORY_LINK###\"(?:([^>]+))?>(?:([^<]+))?</a>~Umsi', '', $datatpl);
					}
					else {
						$datatpl = str_replace('###BACK_CATEGORY_LINK###', 'index.php?page='.$this->getpage, $datatpl);
					}
				}
                
                $cat = '';
				if($this->basecat == '' && isset($this->get[$this->params['cat']])) {
					$cat = '&amp;'.$this->params['cat'].'='.$catid;
				}
				elseif(isset($this->get[$this->params['order']]) && array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
					$orders = array();
					$getorders = explode('_', $this->get[$this->params['order']]);
					foreach($getorders as $getorder) {
						$orders[] = $this->validnum($getorder);
					}
					if($this->catorder != '' && $catid != '') { // && $catid != $this->basecat) {
						foreach($this->catorder as $sub) {
							if($sub[0] == $catid) {
								$orders[] = $sub[1];
								break;
							}
						}
					}
					$cat = '&amp;'.$this->params['order'].'='.implode('_', $orders);
				}
				
                $topic = '';
                if(isset($this->get[$this->params['topic']])) {
                    $topic = '&amp;'.$this->params['topic'].'='.$topicid;
                }
                if(preg_match('~###LINK_DATA_BACK###~i', $datatpl) && ($cat != '' || $this->basecat != '')) {
					$urlfilter = '';
					if(isset($this->get['filter']) && $this->get['filter'] == 'query' && !file_exists($this->tplpath.'/topic.tpl') && !isset($this->get[$this->params['topic']])) {
						$urlfilter = '&amp;filter=query';
					}
					$backlinkdata = 'index.php?page='.$this->getpage.$cat.$urlfilter;
					$datatpl = str_replace('###LINK_DATA_BACK###', $backlinkdata, $datatpl);
				}
                if((preg_match('~###LINK_TOPIC_BACK###~i', $datatpl) || (preg_match('~###LINK_DATA_BACK###~i', $datatpl) && $cat == '')) && $topic != '') {
					$urlfilter = '';
					if(isset($this->get[$this->params['topic']]) && isset($this->get['filter']) && $this->get['filter'] == 'query') {
						$urlfilter = '&amp;filter=query';
					}
					$backlinktopic = 'index.php?page='.$this->getpage.$cat.$topic.$urlfilter;
					if(preg_match('~###LINK_TOPIC_BACK###~i', $datatpl))
						$datatpl = str_replace('###LINK_TOPIC_BACK###', $backlinktopic, $datatpl);
					$backlinkdata = 'index.php?page='.$this->getpage.$urlfilter;
					if(preg_match('~###LINK_DATA_BACK###~i', $datatpl))
						$datatpl = str_replace('###LINK_DATA_BACK###', $backlinkdata, $datatpl);
				}
                
                if(preg_match('~###LINK_PREV_DATA###~i', $datatpl) && preg_match('~###LINK_NEXT_DATA###~i', $datatpl)) {
                    $checkfields = array();
                    $datawheres = array();
					$copyselects = array();
					$copyjoins = array();
                    $copywheres = array();
					
					if(in_array('copyof', $datafields)) {
                        $checkfields[] = 'id AS id';
						$copyselects[] = 'copyd.id AS copydid';
						$copyjoins[] = "LEFT JOIN datas copyd ON copyd.id = d.copyof";
					}
					else {
                        $checkfields[] = 'id';
					}
                    
                    if(in_array('catid', $datafields)) {
                        $checkfields[] = 'catid';
						$datawheres[] = 'catid = '.$data['catid'];
                    }
                    
                    if(in_array('topicid', $datafields)) {
                        $checkfields[] = 'topicid';
						$datawheres[] = 'topicid = '.$data['topicid'];
                    }
                    
            		if(in_array('fromtime', $datafields)) {
                		$checkfields[] = 'fromtime';
                		$checkfields[] = 'totime';
						if(in_array('copyof', $datafields)) {
							$copywheres[] = "(((d.fromtime < ".time()." OR TRIM(d.fromtime) = '') AND (d.totime > ".time()." OR TRIM(d.totime) = '')) OR ((copyd.fromtime < ".time()." OR TRIM(copyd.fromtime) = '') AND (copyd.totime > ".time()." OR TRIM(copyd.totime) = '')))";
							$copyselects[] = "COALESCE(copyd.fromtime, d.fromtime) AS dfromtime";
							$copyselects[] = "COALESCE(copyd.totime, d.totime) AS dtotime";
						}
						else {
							$datawheres[] = "((fromtime < ".time()." OR TRIM(fromtime) = '') AND (totime > ".time()." OR TRIM(totime) = ''))";
						}
            		}
            		
            		if(in_array('onoff', $datafields)) {
                		$checkfields[] = 'onoff';
            			$datawheres[] = "onoff = '1'";
            		}
            		
            		if(in_array('lang', $datafields)) {
                		$checkfields[] = 'lang';
            			$datawheres[] = "lang = '".$this->pagelang."'";
            		}
					
					$startid = '';
					$filterwhere = '';
					$filterorder = '';
					$filterdir = '';
					$link_filter = '';
					if(isset($_SESSION[$this->modname.'datafilter']) && ((isset($this->get['filter']) && $this->get['filter'] == 'query') || (isset($this->dbconfig['filtermaintain']) && $this->dbconfig['filtermaintain'] == 'yes'))) {
						$filter = $_SESSION[$this->modname.'datafilter'];
						if(isset($filter['where'])) $filterwhere = $filter['where'];
						if(isset($filter['order'])) $filterorder = $filter['order'];
						if(isset($filter['dir'])) $filterdir = $filter['dir'];
						if(!isset($this->dbconfig['filtermaintain']))
							$link_filter = '&amp;filter=query';
					}
					
					if(isset($this->get[$this->params['topic']]) && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base']) && (!isset($filter) && !isset($this->dbconfig['startontopicfilter']))) {
						$startid = $this->gettopicstartid($data['topicid']);
						
						if($startid != '') {
							$datawheres[] = "id NOT IN (".$startid.")";
						}
					}
					
					if($filterwhere != '') {
						if(in_array('copyof', $datafields)) {
							foreach($datafields as $datafield) {
								if(preg_match("/\b".$datafield."\b/i", $filterwhere)) {
									if(!in_array($datafield, $checkfields))
										$checkfields[] = $datafield;
									if($datafield != 'fromtime')
										$copyselects[] = "COALESCE(copyd.".$datafield.", d.".$datafield.") AS d".$datafield;
									$filterwhere = preg_replace("/\b".$datafield."\b/i", 'd.'.$datafield, $filterwhere);
								}
							}
							$copyfilterwhere = str_replace('d.', 'copyd.', $filterwhere);
							$copywheres[] = '(('.$filterwhere.') OR ('.$copyfilterwhere.'))';
						}
						else {
							foreach($datafields as $datafield) {
								if(preg_match("/\b".$datafield."\b/i", $filterwhere) && !in_array($datafield, $checkfields))
									$checkfields[] = $datafield;
							}
							$datawheres[] = '('.$filterwhere.')';
						}
					}
                    
					if($filterorder != '') {
						if(in_array($filterorder, $datafields) && !in_array($filterorder, $checkfields)) {
							$checkfields[] = $filterorder;
							if(in_array('copyof', $datafields) && $filterorder != 'fromtime')
								$copyselects[] = "COALESCE(copyd.".$filterorder.", d.".$filterorder.") AS d".$filterorder;
						}
						$dataorder = $filterorder;
					}
                    elseif(array_key_exists('sortdatafield', $this->dbconfig) && $this->dbconfig['sortdatafield'] != '') {
						if(!in_array($this->dbconfig['sortdatafield'], $checkfields)) {
							$checkfields[] = $this->dbconfig['sortdatafield'];
							if(in_array('copyof', $datafields))
								$copyselects[] = "COALESCE(copyd.".$this->dbconfig['sortdatafield'].", d.".$this->dbconfig['sortdatafield'].") AS d".$this->dbconfig['sortdatafield'];
						}
						$dataorder = $this->dbconfig['sortdatafield'];
            		}
                    elseif(in_array('sort', $datafields)) {
            			$checkfields[] = 'sort';
						$dataorder = 'sort';
            		}
            		else {
						$dataorder = 'id';
            		}
                    
					$iscopy = in_array('copyof', $datafields) ? 'd.' : '';
                    
					$dir = ($filterdir != '') ? ' '.$filterdir : (isset($this->dbconfig['datasort']) && $this->dbconfig['datasort'] != '' ? ' '.$this->dbconfig['datasort'] : '');
                    
					$copyselect = '';
					$copyjoin = '';
					if(in_array('copyof', $datafields)) {
						$copyselect = (count($copyselects) > 0) ? ', '.implode(', ', $copyselects) : '';
						$copyjoin = (count($copyjoins) > 0) ? implode(' ', $copyjoins) : '';
					}
                    $checkfield = count($checkfields) > 0 ? implode(', ', $checkfields) : '';
                    $datawhere = count($datawheres) > 0 ? " WHERE ".$iscopy.implode(' AND '.$iscopy, $datawheres) : '';
					$copywhere = count($copywheres) > 0 ? " ".($datawhere == '' ? 'WHERE' : 'AND')." ".implode(' AND ', $copywheres) : '';
                    
                    $isstartid = 0;
                    if(($dataorder == 'sort' || $startid != '') && in_array('topicid', $datafields) && array_key_exists('base', $this->fileconfig) && in_array('disttopicstart', $this->fileconfig['base']) && (!isset($filter) || (isset($filter) && !isset($this->dbconfig['startontopicfilter'])) || (isset($filter) && !isset($this->dbconfig['prevnextnavi']))))
					{
						$query = "SELECT id, topicid, onoff";
						if($startid == '' && $dataorder == 'sort') $query.= ", sort";
						$query.= " FROM datas WHERE topicid = ".$data['topicid'];
						if($startid == '' && $dataorder == 'sort') $query.= " AND TRIM(sort) = ''";
						if($startid != '') $query.= " AND id = ".$startid;
						
                        $prevstartdatas = $this->moddb->query($query);
                        $prevstartdatarows = $this->getdbnumrows($prevstartdatas);
                        if($prevstartdatarows > 0) {
                            $prevstartdata = $prevstartdatas->fetchArray(SQLITE3_ASSOC);
							if(isset($prevstartdata) && $prevstartdata['id'] == $data['id']) {
								$isstartid = 1;
							}
						}
                    }
                    
					$secondorder = ($dataorder != 'id' && $dataorder != 'sort' ? ', id' : '');
                    $prevdata = '';
                    $nextdata = '';
					if($startid == $dataid && !isset($this->dbconfig['startontopicfilter'])) {
						if(in_array('copyof', $datafields)) {
							$nextquery = "SELECT DISTINCT 
								d.".implode(', d.', $checkfields).$copyselect." 
								FROM datas d 
								".$copyjoin.$datawhere.$copywhere." 
								ORDER BY d".($dataorder == 'id' || $dataorder == 'sort' ? '.' : '').$dataorder.$dir.($secondorder != '' ? $secondorder.$dir : '')." 
								LIMIT 1";
						}
						else {
							$nextquery = "SELECT 
								".$checkfield." 
								FROM datas 
								".$datawhere." 
								ORDER BY ".$dataorder.$dir.($secondorder != '' ? $secondorder.$dir : '')." 
								LIMIT 1";
						}
					}
					else {
						$checkdir = ($filterdir != '') ? $filterdir : (isset($this->dbconfig['datasort']) && $this->dbconfig['datasort'] != '' ? $this->dbconfig['datasort'] : 'ASC');
						
						if(in_array('copyof', $datafields)) {
							if($dataorder == 'id' || $dataorder == 'sort') {
								$prevquery = "SELECT DISTINCT 
									d.".implode(', d.', $checkfields).$copyselect." 
									FROM datas d 
									".$copyjoin.$datawhere.$copywhere." ";
								$prevquery.= "AND d.id NOT IN (".$dataid.") ";
								$prevquery.= "AND d.".$dataorder." ".($checkdir == 'ASC' ? '<' : '>')." '".$data[$dataorder]."' ";
								$prevquery.= "ORDER BY d.".$dataorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC')." 
									LIMIT 1";
								
								$nextquery = "SELECT DISTINCT 
									d.".implode(', d.', $checkfields).$copyselect." 
									FROM datas d 
									".$copyjoin.$datawhere.$copywhere." ";
								$nextquery.= "AND d.id NOT IN (".$dataid.") ";
								$nextquery.= "AND d.".$dataorder." ".($checkdir == 'ASC' ? '>' : '<')." '".$data[$dataorder]."' ";
								$nextquery.= "ORDER BY d.".$dataorder." ".($checkdir == 'ASC' ? 'ASC' : 'DESC')." 
									LIMIT 1";
							}
							else {
								$firstprevfound = "SELECT DISTINCT 
									d.".implode(', d.', $checkfields).$copyselect." 
									FROM datas d 
									".$copyjoin.$datawhere.$copywhere." ";
								
								$firstprevfound.= "AND (d.".$dataorder." ".($checkdir == 'ASC' ? '<' : '>')." '".$data[$dataorder]."' OR copyd.".$dataorder." ".($checkdir == 'ASC' ? '<' : '>')." '".$data[$dataorder]."') ";
								
								$firstprevfound.= "ORDER BY d.".$dataorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC')." LIMIT 1";
								
								$firstprevdatas = $this->moddb->query($firstprevfound);
								$firstprevdata = '';
								if($this->getdbnumrows($firstprevdatas) > 0) {
									$firstprev = $firstprevdatas->fetchArray(SQLITE3_ASSOC);
									$firstprevdata = $firstprev['d'.$dataorder];
								}
								
								$prevquery = "SELECT DISTINCT 
									d.".implode(', d.', $checkfields).$copyselect." 
									FROM datas d 
									".$copyjoin.$datawhere.$copywhere." ";
								
								if($firstprevdata != '' && $firstprevdata != $data[$dataorder])
									$prevquery.= "AND ((d.".$dataorder." = '".$firstprevdata."' OR copyd.".$dataorder." = '".$firstprevdata."') OR (d.".$dataorder." = '".$data[$dataorder]."' OR copyd.".$dataorder." = '".$data[$dataorder]."')) ";
								else
									$prevquery.= "AND (d.".$dataorder." = '".$data[$dataorder]."' OR copyd.".$dataorder." = '".$data[$dataorder]."') ";
								
								$prevquery.= "ORDER BY d".$dataorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').($secondorder != '' ? $secondorder.' '.($checkdir == 'ASC' ? 'DESC' : 'ASC') : '');
								
								$firstnextfound = "SELECT DISTINCT 
									d.".implode(', d.', $checkfields).$copyselect." 
									FROM datas d 
									".$copyjoin.$datawhere.$copywhere." ";
								
								$firstnextfound.= "AND (d.".$dataorder." ".($checkdir == 'ASC' ? '>' : '<')." '".$data[$dataorder]."' OR copyd.".$dataorder." ".($checkdir == 'ASC' ? '>' : '<')." '".$data[$dataorder]."') ";
								
								$firstnextfound.= "ORDER BY d.".$dataorder." ".($checkdir == 'ASC' ? 'ASC' : 'DESC')." LIMIT 1";
								
								$firstnextdatas = $this->moddb->query($firstnextfound);
								$firstnextdata = '';
								if($this->getdbnumrows($firstnextdatas) > 0) {
									$firstnext = $firstnextdatas->fetchArray(SQLITE3_ASSOC);
									$firstnextdata = $firstnext['d'.$dataorder];
								}
								
								$nextquery = "SELECT DISTINCT 
									d.".implode(', d.', $checkfields).$copyselect." 
									FROM datas d 
									".$copyjoin.$datawhere.$copywhere." ";
								
								if($firstnextdata != '' && $firstnextdata != $data[$dataorder])
									$nextquery.= "AND ((d.".$dataorder." = '".$firstnextdata."' OR copyd.".$dataorder." = '".$firstnextdata."') OR (d.".$dataorder." = '".$data[$dataorder]."' OR copyd.".$dataorder." = '".$data[$dataorder]."')) ";
								else
									$nextquery.= "AND (d.".$dataorder." = '".$data[$dataorder]."' OR copyd.".$dataorder." = '".$data[$dataorder]."') ";
								
								$nextquery.= "ORDER BY d".$dataorder." ".($checkdir == 'ASC' ? 'DESC' : 'ASC').($secondorder != '' ? $secondorder.' '.($checkdir == 'ASC' ? 'DESC' : 'ASC') : '');
								
								$prevdatas = $this->moddb->query($prevquery);
								if($this->getdbnumrows($prevdatas) > 0) {
									$lastid = '';
									$previd = false;
									while($prev = $prevdatas->fetchArray(SQLITE3_ASSOC)) {
										if($previd) {
											$prevdata = $prev['id'];
											$previd = false;
											break;
										}
										if($prev['id'] == $dataid) {
											$previd = true;
										}
									}
								}
								unset($prevquery);
								
								$nextdatas = $this->moddb->query($nextquery);
								if($this->getdbnumrows($nextdatas) > 0) {
									$lastid = '';
									$nextid = false;
									while($next = $nextdatas->fetchArray(SQLITE3_ASSOC)) {
										if($lastid != '' && $next['id'] == $dataid) {
											$nextdata = $lastid;
											$lastid = '';
											break;
										}
										$lastid = $next['id'];
									}
								}
								unset($nextquery);
							}
						}
						else {
							if($dataorder == 'id' || $dataorder == 'sort') {
								$prevquery = "SELECT 
									".$checkfield." 
									FROM datas 
									".$datawhere." ";
								$prevquery.= "AND ".$dataorder." ".($checkdir == 'ASC' ? '<' : '>')." '".$data[$dataorder]."' 
									ORDER BY ".$dataorder.' '.($checkdir == 'ASC' ? 'DESC' : 'ASC').($secondorder != '' ? $secondorder.' '.($checkdir == 'ASC' ? 'DESC' : 'ASC') : '')." 
									LIMIT 1";
								
								$nextquery = "SELECT 
									".$checkfield." 
									FROM datas 
									".$datawhere." ";
								$nextquery.= "AND ".$dataorder." ".($checkdir == 'ASC' ? '>' : '<')." '".$data[$dataorder]."' 
									ORDER BY ".$dataorder.' '.($checkdir == 'ASC' ? 'ASC' : 'DESC').($secondorder != '' ? $secondorder.' '.($checkdir == 'ASC' ? 'ASC' : 'DESC') : '')." 
									LIMIT 1";
							}
							else {
								$prevquery = "SELECT 
									".$checkfield." 
									FROM datas 
									".$datawhere." ";
								$prevquery.= "AND (".$dataorder." ".($checkdir == 'ASC' ? '<' : '>')." '".$data[$dataorder]."' OR (".$dataorder." = '".$data[$dataorder]."' AND id ".($checkdir == 'ASC' ? '<' : '>')." ".$dataid.")) 
									ORDER BY ".$dataorder.' '.($checkdir == 'ASC' ? 'DESC' : 'ASC').($secondorder != '' ? $secondorder.' '.($checkdir == 'ASC' ? 'DESC' : 'ASC') : '')." 
									LIMIT 1";
								
								$nextquery = "SELECT 
									".$checkfield." 
									FROM datas 
									".$datawhere." ";
								$nextquery.= "AND (".$dataorder." ".($checkdir == 'ASC' ? '>' : '<')." '".$data[$dataorder]."' OR (".$dataorder." = '".$data[$dataorder]."' AND id ".($checkdir == 'ASC' ? '>' : '<')." ".$dataid.")) 
									ORDER BY ".$dataorder.' '.($checkdir == 'ASC' ? 'ASC' : 'DESC').($secondorder != '' ? $secondorder.' '.($checkdir == 'ASC' ? 'ASC' : 'DESC') : '')." 
									LIMIT 1";
							}
						}
					}
					
					if(isset($prevquery)) {
						$prevdatas = $this->moddb->query($prevquery);
						if($this->getdbnumrows($prevdatas) > 0) {
							$prev = $prevdatas->fetchArray(SQLITE3_ASSOC);
							$prevdata = $prev['id'];
						}
					}
					
					if(isset($nextquery)) {
						$nextdatas = $this->moddb->query($nextquery);
						if($this->getdbnumrows($nextdatas) > 0) {
							$next = $nextdatas->fetchArray(SQLITE3_ASSOC);
							$nextdata = $next['id'];
						}
					}
					
                    if($prevdata == '' && $dataid != $startid && !isset($this->dbconfig['prevnextnavi'])) {
						$prevdata = $startid;
					}
                    
                    if($prevdata == '') {
                        $datatpl = preg_replace('~<a(?:([^>]+))?(href=\"###LINK_PREV_DATA###\")(?:([^>]+))?(>)(?:([^<]+))?</a>~Umsi', '', $datatpl);
                    }
                    else {
                        $link_prevdata = 'index.php?page='.$this->getpage.$cat.$topic.'&amp;'.$this->params['data'].'='.$prevdata.$link_filter;
                        $datatpl = str_replace('###LINK_PREV_DATA###', $link_prevdata, $datatpl);
                    }
                    
                    if($nextdata == '' || ($startid == $dataid && isset($this->dbconfig['prevnextnavi']) && $this->dbconfig['prevnextnavi'] == 'none')) {
                        $datatpl = preg_replace('~<a(?:([^>]+))?(href=\"###LINK_NEXT_DATA###\")(?:([^>]+))?(>)(?:([^<]+))?</a>~Umsi', '', $datatpl);
                    }
                    else {
                        $link_nextdata = 'index.php?page='.$this->getpage.$cat.$topic.'&amp;'.$this->params['data'].'='.$nextdata.$link_filter;
                        $datatpl = str_replace('###LINK_NEXT_DATA###', $link_nextdata, $datatpl);
                    }
                }
            }
        }
        
        return $datatpl;
    }
    
    function getviewdatalist()
    {
		$filterwhere = '';
		$filterorder = '';
		$filterdir = '';
		if(isset($_SESSION[$this->modname.'datafilter']) && ((isset($this->get['filter']) && $this->get['filter'] == 'query') || (isset($this->dbconfig['filtermaintain']) && $this->dbconfig['filtermaintain'] == 'yes'))) {
			$filter = $_SESSION[$this->modname.'datafilter'];
			if(isset($filter['where'])) $filterwhere = $filter['where'];
			if(isset($filter['order'])) $filterorder = $filter['order'];
			if(isset($filter['dir'])) $filterdir = $filter['dir'];
		}
		
        $matchdatatpl = file_get_contents($this->tplpath.'/dataslist.tpl');
        $result = '';
		
        $breadcrumb = (array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && preg_match('~###BREADCRUMB###~i', $matchdatatpl)) ? $this->getbreadcrumb() : '';
        
        $loadsubcats = (array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && preg_match('~###LOAD_SUB_CATEGORIES###~i', $matchdatatpl)) ? 1 : 0;
		
        $filterform = (array_key_exists('base', $this->fileconfig) && in_array('filter', $this->fileconfig['base']) && preg_match('~###LOAD_FILTER###~i', $matchdatatpl)) ? 1 : 0;
		
		unset($matchdatatpl);
		
        if($loadsubcats == 1) {
    		$subcatlist = $this->getviewcatlist();
            $subcats = (is_array($subcatlist) && count($subcatlist) > 0) ? $this->setviewcattotpl($subcatlist, 'sub') : $this->setviewcattotpl(array(), 'sub');
        }
        
        $datafields = $this->fileconfig['data'];
        $catid = '';
        $cat = '';
        $checkfields = array();
		$checkcatfields = array();
        $datawheres = array();
        $joindatawheres = array();
		$copyselects = array();
		$copyjoins = array();
		if(in_array('catid', $datafields)) {
            if($this->basecat != '' && !isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])) {
                $catid = $this->basecat;
            }
            elseif(isset($this->get[$this->params['cat']])) {
                $catid = $this->validnum($this->get[$this->params['cat']]);
            }
            elseif(isset($this->get[$this->params['order']]) && array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
				if($this->curcat == '')
					$this->getcatsfromsubs();
				$catid = $this->curcat;
                $orders = array();
            	$getorders = explode('_', $this->get[$this->params['order']]);
    			foreach($getorders as $getorder) {
                    $orders[] = $this->validnum($getorder);
            	}
                if($this->catorder != '' && $catid != '') {
        			foreach($this->catorder as $sub) {
                        if($sub[0] == $catid) {
    						$orders[] = $sub[1];
                            break;
                        }
    	        	}
                }
                $cat = '&amp;'.$this->params['order'].'='.implode('_', $orders);
            }
			
			$checkcatfields[] = 'c.id AS cid';
			$checkfields[] = 'catid';
			$datawheres[] = 'catid = '.$catid;
			$joindatawheres[] = 'd.catid = '.$catid;
			
			if(in_array('copyof', $this->fileconfig['data'])) {
				$copyjoins[] = "LEFT JOIN cats c ON c.id = d.catid";
			}
			
			if(in_array('onoff', $this->fileconfig['cat'])) {
				$checkcatfields[] = 'c.onoff AS conoff';
				$joindatawheres[] = "c.onoff = '1'";
			}
			
			if(in_array('lang', $this->fileconfig['cat'])) {
				$checkcatfields[] = 'c.lang';
				$joindatawheres[] = "c.lang = '".$this->pagelang."'";
			}
			
			if(in_array('seotitle', $this->fileconfig['cat']) || (array_key_exists('headlinetitle', $this->dbconfig) && $this->dbconfig['headlinetitle'] == 'show')) {
				$seofields = (in_array('seotitle', $this->fileconfig['cat'])) ? ', seotitle, seodesc, seokeys' : '';
				$catseos = $this->moddb->query("SELECT id, catname".$seofields." FROM cats WHERE id = '".$catid."' LIMIT 1");
				$catseonumrows = $this->getdbnumrows($catseos);
				
				if($catseonumrows > 0) {
					$catseo = $catseos->fetchArray(SQLITE3_ASSOC);
					if(in_array('seotitle', $this->fileconfig['cat'])) {
						if($this->is_serialized($catseo['seotitle'])) {
							$catseotitlelangs = unserialize($catseo['seotitle']);
							$catseotitle = $catseotitlelangs[$this->pagelang];
							$catseodesclangs = unserialize($catseo['seodesc']);
							$catseodesc = $catseodesclangs[$this->pagelang];
							$catseokeyslangs = unserialize($catseo['seokeys']);
							$catseokeys = $catseokeyslangs[$this->pagelang];
						}
						else {
							$catseotitle = $catseo['seotitle'];
							$catseodesc = $catseo['seodesc'];
							$catseokeys = $catseo['seokeys'];
						}
						if($catseotitle != '')
							$this->metadata['title'] = $catseotitle;
						if($catseodesc != '')
							$this->metadata['description'] = $catseodesc;
						if($catseokeys != '')
							$this->metadata['keywords'] = $catseokeys;
						
						unset($catseo['seotitle']);
						unset($catseo['seodesc']);
						unset($catseo['seokeys']);
					}
					elseif(array_key_exists('headlinetitle', $this->dbconfig) && $this->dbconfig['headlinetitle'] == 'show') {
						if($this->is_serialized($catseo['catname'])) {
							$catlangs = unserialize($catseo['catname']);
							$catname = $catlangs[$this->pagelang];
						}
						else {
							$catname = $catseo['catname'];
						}
						$this->metadata['title'] = $catname;
					}
				}
			}
        }
		else {
			$this->loadmetadata();
		}
        
		if(in_array('copyof', $datafields)) {
            $checkfields[] = 'copyof';
			$checkfields[] = 'id AS id';
			$copyjoins[] = "LEFT JOIN datas copyd ON copyd.id = d.copyof";
		}
		
		if(in_array('fromtime', $datafields)) {
            $checkfields[] = 'fromtime';
            $checkfields[] = 'totime';
			$datawheres[] = "((fromtime < ".time()." OR TRIM(fromtime) = '') AND (totime > ".time()." OR TRIM(totime) = ''))";
			if(in_array('copyof', $datafields)) {
				$joindatawheres[] = "(((d.fromtime < ".time()." OR TRIM(d.fromtime) = '') AND (d.totime > ".time()." OR TRIM(d.totime) = '')) OR ((copyd.fromtime < ".time()." OR TRIM(copyd.fromtime) = '') AND (copyd.totime > ".time()." OR TRIM(copyd.totime) = '')))";
				$copyselects[] = "COALESCE(copyd.fromtime, d.fromtime) AS fromtime";
				$copyselects[] = "COALESCE(copyd.totime, d.totime) AS totime";
			}
			else {
				$joindatawheres[] = "((d.fromtime < ".time()." OR TRIM(d.fromtime) = '') AND (d.totime > ".time()." OR TRIM(d.totime) = ''))";
			}
		}
		
		if(in_array('onoff', $datafields)) {
            $checkfields[] = 'onoff';
			$datawheres[] = "onoff = '1'";
            $joindatawheres[] = "d.onoff = '1'";
		}
		
		if(in_array('lang', $datafields)) {
            $checkfields[] = 'lang';
			$datawheres[] = "lang = '".$this->pagelang."'";
            $joindatawheres[] = "d.lang = '".$this->pagelang."'";
		}
		
		if($filterwhere != '') {
			if(in_array('copyof', $datafields)) {
				foreach($datafields as $datafield) {
					if(preg_match("/\b".$datafield."\b/i", $filterwhere)) {
						if(!in_array($datafield, $checkfields))
							$checkfields[] = $datafield;
						$filterwhere = preg_replace("/\b".$datafield."\b/i", 'd.'.$datafield, $filterwhere);
					}
				}
				$joindatawheres[] = '('.$filterwhere.')';
			}
			else {
				foreach($datafields as $datafield) {
					if(preg_match("/\b".$datafield."\b/i", $filterwhere) && !in_array($datafield, $checkfields))
						$checkfields[] = $datafield;
				}
				$datawheres[] = '('.$filterwhere.')';
				$joindatawheres[] = '('.$filterwhere.')';
			}
		}
        
        if($filterorder != '') {
			if(in_array($filterorder, $datafields) && !in_array($filterorder, $checkfields)) {
				$checkfields[] = $filterorder;
				if(in_array('copyof', $datafields))
					$copyselects[] = "COALESCE(copyd.".$filterorder.", d.".$filterorder.") AS ".$filterorder;
			}
			$dataorder = $filterorder;
		}
        elseif(array_key_exists('sortdatafield', $this->dbconfig) && $this->dbconfig['sortdatafield'] != '') {
			if(!in_array($this->dbconfig['sortdatafield'], $checkfields)) {
				$checkfields[] = $this->dbconfig['sortdatafield'];
				if(in_array('copyof', $datafields))
					$copyselects[] = "COALESCE(copyd.".$this->dbconfig['sortdatafield'].", d.".$this->dbconfig['sortdatafield'].") AS ".$this->dbconfig['sortdatafield'];
			}
			$dataorder = $this->dbconfig['sortdatafield'];
		}
        elseif(in_array('sort', $datafields)) {
            $checkfields[] = 'sort';
			$dataorder = in_array('copyof', $datafields) ? 'd.sort' : 'sort';
		}
		else {
			$checkfields[] = 'id';
			$dataorder = in_array('copyof', $datafields) ? 'd.id' : 'id';
		}
        
        $datasperpage = (isset($this->dbconfig['datasperpage']) && $this->dbconfig['datasperpage'] != '') ? ' '.$this->dbconfig['datasperpage'] : '';
        $pager = (isset($this->get['pager'])) ? $this->validnum($this->get['pager']) : 1;
        $dir = ($filterdir != '') ? ' '.$filterdir : (isset($this->dbconfig['datasort']) && $this->dbconfig['datasort'] != '' ? ' '.$this->dbconfig['datasort'] : '');
        
        $dbfields = implode(', ', $datafields);
		$copyselect = '';
		$copyjoin = '';
		if(in_array('copyof', $datafields)) {
			$copyselect = (count($copyselects) > 0) ? ', '.implode(', ', $copyselects) : '';
			$copyjoin = (count($copyjoins) > 0) ? implode(' ', $copyjoins) : '';
		}
		$catfields = count($checkcatfields) > 0 ? ', '.implode(', ', $checkcatfields) : '';
        $datawhere = count($datawheres) > 0 ? " WHERE ".implode(' AND ', $datawheres) : '';
        $joindatawhere = count($joindatawheres) > 0 ? " WHERE ".implode(' AND ', $joindatawheres) : '';
        
        $limit = '';
        $checknextdata = 0;
        if($datasperpage != '') {
			if(in_array('copyof', $datafields)) {
				$nextdata = $this->moddb->query("SELECT d.".implode(', d.', $checkfields).$catfields.$copyselect." FROM datas d ".$copyjoin.$joindatawhere." ORDER BY ".$dataorder.$dir.", d.id".$dir." LIMIT ".($pager*$datasperpage).", 1");
			}
			else {
				$nextdata = $this->moddb->query("SELECT ".implode(', ', $checkfields)." FROM datas".$datawhere." ORDER BY ".$dataorder.$dir.", id".$dir." LIMIT ".($pager*$datasperpage).", 1");
			}
            
            $checknextdata = $this->getdbnumrows($nextdata);
            $start = $pager*$datasperpage-$datasperpage;
            $limit = " LIMIT ".$start.", ".$datasperpage;
        }
		if(in_array('catid', $datafields)) {
			if(in_array('copyof', $datafields)) {
				$datas = $this->moddb->query("SELECT d.id, d.".implode(', d.', $datafields).$catfields.$copyselect." FROM datas d ".$copyjoin.$joindatawhere." AND c.id = ".$catid." AND c.onoff = '1' ORDER BY ".$dataorder.$dir.", d.id".$dir.$limit);
			}
			else {
				$datas = $this->moddb->query("SELECT d.id, d.".implode(', d.', $datafields).$catfields." FROM datas d INNER JOIN cats c".$joindatawhere." AND c.id = ".$catid." AND c.onoff = '1' ORDER BY d.".$dataorder.$dir.", d.id".$dir.$limit);
			}
		}
		else {
			$datas = $this->moddb->query("SELECT id, ".$dbfields." FROM datas".$datawhere." ORDER BY ".$dataorder.$dir.$limit);
		}
        $datanumrows = $this->getdbnumrows($datas);
		
        $dataouts = array();
        if($datanumrows == 0) {
			if($this->basecat != '' && !isset($this->get[$this->params['order']]) && array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']) && !isset($this->dbconfig['catonlymenu'])) {
				$catlist = $this->getviewcatlist();
				if(is_array($catlist)) {
					return $this->setviewcattotpl($catlist);
				}
				else {
					return $catlist;
				}
			}
			else {
				$result = $this->viewdefine('noavaildata');
			}
        }
        else {
			ob_start();
			$module = $this;
			include $this->tplpath.'/dataslist.tpl';
			$datatpl = ob_get_contents();
			ob_end_clean();
			
			$datatpl = str_replace('###BREADCRUMB###', $breadcrumb, $datatpl);
			
            if($loadsubcats == 1) {
                $datatpl = str_replace('###LOAD_SUB_CATEGORIES###', $subcats, $datatpl);
            }
			
			if($filterform == 1) {
				ob_start();
				$module = $this;
				include $this->tplpath.'/filter.tpl';
				$filtertpl = ob_get_contents();
				ob_end_clean();
				
				$params = $this->getcurrenturl();
				$reseturl = 'index.php?page='.$this->getpage.$params['cat'].$params['order'].$params['topic'].'&amp;filter=reset';
				$formurl = 'index.php?page='.$this->getpage.$params['cat'].$params['order'].$params['topic'];
				if(!isset($this->dbconfig['filtermaintain'])) $formurl.= '&amp;filter=query';
				$filtertpl = str_replace('###FILTER_RESETURL###', $reseturl, $filtertpl);
				$filtertpl = str_replace('###FILTER_FORMURL###', $formurl, $filtertpl);
				$datatpl = str_replace('###LOAD_FILTER###', $filtertpl, $datatpl);
			}
            
            if(preg_match('~###BACK_CATEGORY_LINK###~i', $datatpl)) {
				if($this->basecat != '' && in_array('basecat', $this->fileconfig['base']) && (!in_array('subcats', $this->fileconfig['base']) || (!isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])))) {
					$datatpl = preg_replace('~<a(?:([^>]+))?href=\"###BACK_CATEGORY_LINK###\"(?:([^>]+))?>(?:([^<]+))?</a>~Umsi', '', $datatpl);
				}
				else {
					$datatpl = str_replace('###BACK_CATEGORY_LINK###', 'index.php?page='.$this->getpage.$cat, $datatpl);
				}
			}
            
            while($data = $datas->fetchArray(SQLITE3_ASSOC)) {
				if(in_array('copyof', $datafields) && $data['copyof'] != '') {
					$dataid = $data['id'];
					$dataonoff = $data['onoff'];
					if(in_array('catid', $datafields))
						$datacatid = $data['catid'];
					if(in_array('topicid', $datafields))
						$datatopicid = $data['topicid'];
					$copydatas = $this->moddb->query("SELECT id, ".implode(', ', $datafields)." FROM datas WHERE id = '".$data['copyof']."' LIMIT 1");
					$data = $copydatas->fetchArray(SQLITE3_ASSOC);
					$data['id'] = $dataid;
					$data['onoff'] = $dataonoff;
					if(in_array('catid', $datafields))
						$data['catid'] = $datacatid;
					if(in_array('topicid', $datafields))
						$data['topicid'] = $datatopicid;
				}
                $dataouts[] = $this->loaddatatotpl($data, 'data');
            }
            $dataout = (count($dataouts) > 0) ? implode("\n", $dataouts) : '';
            
            if($datasperpage != '') {
                $this->setviewpager($pager, $checknextdata);
            }
            
            $datatpl = str_replace('###LOOP_DATA###', $dataout, $datatpl);
            
            $pager = '';
            if(count($this->pager) > 0) {
                $pager = $this->getpagernavi();
            }
            $datatpl = str_replace('###LOAD_PAGER###', $pager, $datatpl);
            
            if(!array_key_exists('groups', $this->dbconfig) || $this->userisadmin || $this->getwritepermission()) {
                if(preg_match('~###LOAD_NEWDATA_TPL###~i', $datatpl)) {
                    $datanew = $this->getnewdata();
                	$datatpl = str_replace('###LOAD_NEWDATA_TPL###', $datanew, $datatpl);
                }
                elseif(preg_match('~###LINK_NEWDATA###~i', $datatpl)) {
                    $link_datanew = 'index.php?page='.$this->getpage;
                    if(isset($this->get[$this->params['cat']])) {
                        $catid = $this->validnum($this->get[$this->params['cat']]);
                        $link_datanew.= '&amp;'.$this->params['cat'].'='.$catid;
                    }
                    elseif(isset($this->get[$this->params['order']])) {
                        $catorder = $this->validorder($this->get[$this->params['order']]);
                        $link_datanew.= '&amp;'.$this->params['order'].'='.$catorder;
                    }
                    if(isset($this->get[$this->params['topic']])) {
                        $topicid = $this->validnum($this->get[$this->params['topic']]);
                        $link_datanew.= '&amp;'.$this->params['topic'].'='.$topicid;
                    }
                    $link_datanew.= '&amp;'.$this->params['load'].'='.$this->params['data'].'new';
                    
                	$datatpl = str_replace('###LINK_NEWDATA###', $link_datanew, $datatpl);
                }
            }
            
            $result = $datatpl;
        }
        
        return $result;
    }
    
    function loaddatatotpl($source, $tplfile)
    {
        $datatpl = $this->tplpath.'/'.$tplfile.'.tpl';
        $dataout = array();
        $data_lightbox = array();
		$lightbox_counter = 1;
        $multidatas = array();
        $checkdatas = array();
        $optiondatas = array();
        
        foreach($source as $datakey => $datavalue)
        {
    		if($tplfile == 'newest' && array_key_exists('base', $this->fileconfig) && in_array('newest', $this->fileconfig['base']) && ($datakey == 'catname' || $datakey == 'topic')) {
        		$dataout[$datakey] = $datavalue;
            }
    		if(in_array($datakey, $this->fileconfig['data'])) {
                if(($this->searchfieldintypes($datakey) == 'image' || $this->searchfieldintypes($datakey) == 'multi') && $lightbox_counter == 1) {
                    $this->lightbox_count++;
					$lightbox_counter = 0;
				}
                
				if($this->searchfieldintypes($datakey) == 'file') {
    				$downloadvalue = $this->is_serialized($datavalue) ? unserialize($datavalue) : array();
					
    				if(isset($downloadvalue[1]) && $downloadvalue[1] != '') {
            			$dataout[$datakey] = $downloadvalue[1];
            			$dataout['link_of_'.$datakey] = 'modules/'.$this->modname.'/media/loader.php?file='.$datakey.'&id='.$source['id'];
                    }
                    else {
            			$dataout[$datakey] = '';
            			$dataout['link_of_'.$datakey] = '';
                    }
                }
                elseif($this->searchfieldintypes($datakey) == 'image') {
    				if($this->is_serialized($datavalue)) {
    					$data_image = unserialize($datavalue);
    					$data_imgpath = 'modules/'.$this->modname.'/media/imgs/';
                        $data_boximage = $data_imgpath.$data_image[0].'/'.$data_image[1].'_box.'.$data_image[2];
						$imgtype = $tplfile == 'data' ? '_view' : '_full';
                        $data_viewimage = $data_imgpath.$data_image[0].'/'.$data_image[1].$imgtype.'.'.$data_image[2];
                        
        				if(array_key_exists('lightboxlistdata', $this->dbconfig) && array_key_exists('lightbox', $this->dbconfig) && array_key_exists($datakey, $this->dbconfig['lightbox']) && $this->dbconfig['lightbox'][$datakey] == 'on') {
            				if($this->dbconfig['lightboxlistdata'] == 'nodatas' && ($imgtype == '_full' || ($imgtype == '_view' && !array_key_exists('lightboxonlyfull', $this->dbconfig)))) {
                                $data_lightbox[$datakey] = '<a href="'.$data_boximage.'" rel="lightbox">###'.$data_viewimage;
                            }
            				if($this->dbconfig['lightboxlistdata'] == 'onedatas' && ($imgtype == '_full' || ($imgtype == '_view' && !array_key_exists('lightboxonlyfull', $this->dbconfig)))) {
                                $data_lightbox[$datakey] = '<a href="'.$data_boximage.'" rel="lightbox'.$this->lightbox_count.'">###'.$data_viewimage;
                            }
            				if($this->dbconfig['lightboxlistdata'] == 'alldatas') {
                                $data_lightbox[$datakey] = '<a href="'.$data_boximage.'" rel="lightbox2">###'.$data_viewimage;
                            }
                        }
                        
                        $dataout[$datakey] = $data_viewimage;
    				}
                    else {
                        $dataout[$datakey] = '';
                    }
                }
                elseif($this->searchfieldintypes($datakey) == 'multi') {
                	$multitplfile = $this->tplpath.'/multiimgs.tpl';
                    
    				if($this->is_serialized($datavalue)) {
            			$multi_images = unserialize($datavalue);
        				$multi_imgpath = 'modules/'.$this->modname.'/media/imgs/';
                        $multi_count = 0;
                        $multiout = array();
                        foreach($multi_images as $multi_image) {
                            $multi_boximage = $multi_imgpath.$multi_image[0].'/'.$multi_image[1].'_box.'.$multi_image[2];
							$imgtype = $tplfile == 'data' ? '_view' : '_full';
                            $multi_viewimage = $multi_imgpath.$multi_image[0].'/'.$multi_image[1].$imgtype.'.'.$multi_image[2];
                            
                            $multiout['multiimg'] = $multi_viewimage;
                            
                            $multi_lightbox = '';
            				if(array_key_exists('lightboxlistdata', $this->dbconfig) && array_key_exists('lightbox', $this->dbconfig) && array_key_exists($datakey, $this->dbconfig['lightbox']) && $this->dbconfig['lightbox'][$datakey] == 'on') {
                				if($this->dbconfig['lightboxlistdata'] == 'nodatas' && ($imgtype == '_full' || ($imgtype == '_view' && !array_key_exists('lightboxonlyfull', $this->dbconfig)))) {
                                    $multi_lightbox = '<a href="'.$multi_boximage.'" rel="lightbox">';
                                }
                				if($this->dbconfig['lightboxlistdata'] == 'onedatas' && ($imgtype == '_full' || ($imgtype == '_view' && !array_key_exists('lightboxonlyfull', $this->dbconfig)))) {
                                    $multi_lightbox = '<a href="'.$multi_boximage.'" rel="lightbox'.$this->lightbox_count.'">';
                                }
                				if($this->dbconfig['lightboxlistdata'] == 'alldatas') {
                                    $multi_lightbox = '<a href="'.$multi_boximage.'" rel="lightbox2">';
                                }
                            }
                            
                            ob_start();
                            $db_data = $multiout;
							include $multitplfile;
                            $multidata = ob_get_contents();
                        	ob_end_clean();
                            
                            if($multi_lightbox != '')
                                $multidata = preg_replace('#<img(?:([^>]+))?(src=\"'.$multi_viewimage.'\")(?:([^>]+))?/>#Umsi', $multi_lightbox.'<img${1}${2}${3}/></a>', $multidata);
                            
                            $multidatas[$datakey][] = $multidata;
                        }
						
                        $dataout[$datakey] = 1;
                    }
                    else {
                        $multidatas[$datakey] = '';
                    }
                }
                elseif($this->searchfieldintypes($datakey) == 'checkbox') {
                	$checktplfile = $this->tplpath.'/checkbox.tpl';
    				if($this->is_serialized($datavalue)) {
            			$checkboxdatas = unserialize($datavalue);
                        $checkout = array();
                        foreach($checkboxdatas as $checkboxkey => $checkboxdata) {
                            $checkout['check'][0] = (isset($checkboxkey) && $checkboxkey != '') ? (defined($checkboxkey) ? constant($checkboxkey) : $checkboxkey) : '';
                            $checkout['check'][1] = (isset($checkboxdata) && $checkboxdata != '') ? (defined($checkboxdata) ? constant($checkboxdata) : $checkboxdata) : '';
                            
                            ob_start();
                            $db_data = $checkout;
							include $checktplfile;
                            $checkdata = ob_get_contents();
                        	ob_end_clean();
                            
                            $checkdatas[$datakey][] = $checkdata;
                        }
                    }
                    else {
                        $checkdatas[$datakey] = '';
                    }
            	}
                elseif($this->searchfieldintypes($datakey) == 'select') {
        			$datafields = explode('|', $datavalue);
        			$dataout[$datakey][0] = (isset($datafields[0]) && $datafields[0] != '' && $datafields[0] != '---') ? (defined($datafields[0]) ? constant($datafields[0]) : $datafields[0]) : '';
        			$dataout[$datakey][1] = (isset($datafields[1]) && $datafields[1] != '' && $datafields[0] != '---') ? (defined($datafields[1]) ? constant($datafields[1]) : $datafields[1]) : '';
            	}
                elseif($datakey == 'optionids') {
                    if($datavalue != '') {
						if($this->is_serialized($datavalue)) {
							$optionids = unserialize($datavalue);
							$matchdatatpl = file_get_contents($datatpl);
							
							if(count($optionids) == 1 && preg_match('~###LOAD_OPTIONS_TPL###~i', $matchdatatpl) && file_exists($this->tplpath.'/options.tpl')) {
								$dataout['options'] = 1;
							}
							unset($matchdatatpl);
							
							foreach($optionids as $optionid) {
								$has_id = '';
								if(file_exists($this->tplpath.'/options_'.$optionid.'.tpl')) {
									$has_id = '_'.$optionid;
									$dataout['options'.$has_id] = 1;
								}
								$optionstpl = $this->tplpath.'/options'.$has_id.'.tpl';
								
								$optionsgroups = $this->moddb->query("SELECT id, optvals FROM options WHERE id = ".$optionid);
								$optiongrprows = $this->getdbnumrows($optionsgroups);
								
								$options = array();
								if($optiongrprows > 0) {
									$optionsgroup = $optionsgroups->fetchArray(SQLITE3_ASSOC);
									
									$optionlines = explode("\n", $optionsgroup['optvals']);
									foreach($optionlines as $optionline) {
										$optionout = array();
										
										$optionvals = explode('|', $optionline);
										$count = 0;
										foreach($optionvals as $optionval) {
											if(defined($optionval))
												$optionvalue = constant($optionval);
											elseif(file_exists($this->serverpath.'/modules/'.$this->modname.'/media/opts/'.$optionval))
												$optionvalue = 'modules/'.$this->modname.'/media/opts/'.$optionval;
											else
												$optionvalue = $optionval;
											
											$optionout['option'][$count] = $optionvalue;
											$count++;
										}
										
										ob_start();
										$db_data = $optionout;
										include $optionstpl;
										$option = ob_get_contents();
										ob_end_clean();
										
										$options[] = $option;
									}
								}
								$optiondatas[$optionid] = implode("\n", $options);
							}
						}
					}
            	}
                elseif($this->searchfieldintypes($datakey) == 'bbcode') {
        			if($this->is_serialized($datavalue)) {
        				$datafield = unserialize($datavalue);
                    	$dataout[$datakey] = $this->bbcodereplace(nl2br($datafield[$this->pagelang]));
        			}
        			else {
        				$dataout[$datakey] = $this->bbcodereplace(nl2br($datavalue));
        			}
            	}
                elseif($this->searchfieldintypes($datakey) == 'user') {
					if($datavalue == $this->validnum($datavalue) && $datavalue != '') {
						$dataout[$datakey] = $this->getusernamefromid($datavalue);
					}
					else {
						if(preg_match('~###~i', $datavalue)) {
							$user = explode('###', $datavalue);
							$dataout[$datakey] = $user[0].' ('.constant($user[1]).')';
						}
						else {
							$dataout[$datakey] = $this->checkforlangdefine($datavalue);
						}
					}
            	}
                else {
        			if($this->is_serialized($datavalue)) {
        				$datafield = unserialize($datavalue);
                    	$dataout[$datakey] = $datafield[$this->pagelang];
        			}
        			else {
        				$dataout[$datakey] = $datavalue;
        			}
                }
            }
        }
        
		ob_start();
		$module = $this;
        $db_data = $dataout;
		include $datatpl;
        $data = ob_get_contents();
    	ob_end_clean();
        
        
		if(preg_match('~###LINK_FULLDATA###~i', $data)) {
			$urlcat = '';
			if(isset($this->get[$this->params['cat']])) {
				$urlcat = '&amp;'.$this->params['cat'].'='.$this->validnum($this->get[$this->params['cat']]);
			}
			elseif(isset($this->get[$this->params['order']]) && array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base'])) {
				$urlcat = '&amp;'.$this->params['order'].'='.$this->validorder($this->get[$this->params['order']]);
			}
			if($urlcat == '' && array_key_exists('catid', $source) && $source['catid'] != '') {
				if($this->basecat == '' || ($this->basecat != '' && $source['catid'] != $this->basecat))
					$urlcat = '&amp;'.$this->params['cat'].'='.$source['catid'];
			}
			
			$topicid = '';
			$urltopic = '';
			if(isset($this->get[$this->params['topic']])) {
				$topicid = $this->validnum($this->get[$this->params['topic']]);
				$urltopic = '&amp;'.$this->params['topic'].'='.$topicid;
			}
			if($urltopic == '' && array_key_exists('topicid', $source) && $source['topicid'] != '') {
				$urltopic = '&amp;'.$this->params['topic'].'='.$source['topicid'];
			}
			
			$urlfilter = '';
			if(isset($this->get['filter']) && $this->get['filter'] == 'query') {
				$urlfilter = '&amp;filter=query';
			}
			
			if($topicid != '' && $this->topicstartid == '') {
				$this->topicstartid = $this->gettopicstartid($topicid);
			}
			
			if($this->topicstartid != '' && $source['id'] == $this->topicstartid && in_array('topicid', $this->fileconfig['data']) && ((isset($this->dbconfig['startontopicfilter']) && $this->dbconfig['startontopicfilter'] == 'no' && isset($_SESSION[$this->modname.'datafilter']) && ((isset($this->get['filter']) && $this->get['filter'] == 'query') || (isset($this->dbconfig['filtermaintain']) && $this->dbconfig['filtermaintain'] == 'yes'))) || (isset($this->dbconfig['prevnextnavi']) && $this->dbconfig['prevnextnavi'] == 'none'))) {
				$link_fulldata = '';
			}
			else {
				$link_fulldata = 'index.php?page='.$this->getpage.$urlcat.$urltopic.'&amp;'.$this->params['data'].'='.$source['id'].$urlfilter;
			}
			
			if($link_fulldata == '' || ($this->topicstartid == '' && isset($this->dbconfig['prevnextnavi']) && $this->dbconfig['prevnextnavi'] == 'none')) {
				$data = preg_replace('~<a(?:([^>]+))?(href=\"###LINK_FULLDATA###\")(?:([^>]+))?(>)(?:([^<]+))?</a>~Umsi', '', $data);
			}
			else {
				$data = str_replace('###LINK_FULLDATA###', $link_fulldata, $data);
			}
		}
        
        if(in_array('optionids', $this->fileconfig['data'])) {
            $nooptidtpls = array();
            if(count($optiondatas) > 0) {
                foreach($optiondatas as $key => $optiondata) {
                    $options = is_array($optiondata) ? implode("\n", $optiondata) : $optiondata;
                    if(preg_match('~###LOAD_OPTIONS_'.$key.'_TPL###~i', $data)) {
                        $data = str_replace('###LOAD_OPTIONS_'.$key.'_TPL###', $options, $data);
                    }
                    else {
                        $nooptidtpls[] = $options;
                    }
                }
            }
			else {
				$data = preg_replace('~###LOAD_OPTIONS_([0-9]+)_TPL###~si', '', $data);
			}
            $setidtpl = (count($nooptidtpls) > 0) ? implode("\n", $nooptidtpls) : '';
            $data = str_replace('###LOAD_OPTIONS_TPL###', $setidtpl, $data);
        }
        
        if(count($checkdatas) > 0) {
            foreach($checkdatas as $key => $checkdata) {
                $check = is_array($checkdata) ? implode("\n", $checkdata) : $checkdata;
                $data = str_replace('###LOAD_'.strtoupper($key).'_TPL###', $check, $data);
            }
        }
        
        if(count($multidatas) > 0) {
            foreach($multidatas as $key => $multidata) {
                $multi = is_array($multidata) ? implode("\n", $multidata) : $multidata;
                $data = str_replace('###LOAD_'.strtoupper($key).'_TPL###', $multi, $data);
            }
        }
        
        if(count($data_lightbox) > 0) {
            foreach($data_lightbox as $key => $values) {
                $value = explode('###', $values);
                $lightbox = $value[0];
                $boximage = $value[1];
                $data = preg_replace('#<img(?:([^>]+))?(src=\"'.$boximage.'\")(?:([^>]+))?/>#Umsi', $lightbox.'<img${1}${2}${3}/></a>', $data);
            }
        }
        
        return $data;
    }
    
    
    
    function getdownloadgroup($field)
    {
    	$sessgrpids = $this->sessgrpids;
        $saveload = $this->dbconfig['saveload'][$field][1];
        if(in_array('all', $saveload)) {
            return true;
        }
        if($sessgrpids != '') {
	        foreach($sessgrpids as $grpid) {
	            if(in_array($grpid, $saveload)) {
	                return true;
	                break;
	            }
            }
        }
        return false;
    }
    
    function downloadfile($id, $field)
    {
        $id = $this->validnum($id);
    	$field = $this->validfield($field);
        if($id != '') {
            $fields = explode(',', $this->fileconfig['types']['file']);
            if(in_array($field, $fields)) {
                $datas = $this->moddb->query("SELECT id, ".$field.", ".$field."_counter FROM datas WHERE datas.id = '".$id."' LIMIT 1");
                $data = $datas->fetchArray();
                $file = unserialize($data[$field]);
                
				if(file_exists($this->serverpath.'/modules/'.$this->modname.'/media/files/'.$file[0].'/'.$file[2].'.'.$file[3])) {
					if($data[$field.'_counter'] == '')
						$data[$field.'_counter'] = 0;
					$this->moddb->query("UPDATE datas SET ".$field."_counter = ".$data[$field.'_counter']." + 1 WHERE datas.id = '".$id."'");
				}
				
                return $file;
            }
        }
    }
    
    function readfilechunked($filename)
    { 
        $handle = fopen($filename, 'rb');
        if($handle === false) {
            return false;
        }
        while(!feof($handle)) { 
            print fread($handle, 4096);
        }
		fclose($handle); 
    }
    
	
    function getwritepermission()
    {
		if(array_key_exists('groups', $this->dbconfig)) {
			$ugrpids = $this->sessgrpids;
			$writegrps = $this->dbconfig['groups'];
			
			if(is_array($writegrps) && in_array('all', $writegrps)) {
				return true;
			}
			
			if(count($ugrpids) >= 1) {
				foreach($ugrpids as $ugrpid) {
					if(is_array($writegrps) && in_array($ugrpid, $writegrps)) {
						return true;
						break;
					}
				}
			}
		}
		
        return false;
    }
	
	function searchforquery()
	{
		$titlefield = (isset($this->dbconfig['title'])) ? $this->dbconfig['title'] : '';
		
		$contentfields = array();
		if(isset($this->fileconfig['types']['text'])) {
			$contentfields = array_merge($contentfields, explode(',', $this->fileconfig['types']['text']));
		}
		if(isset($this->fileconfig['types']['area'])) {
			$contentfields = array_merge($contentfields, explode(',', $this->fileconfig['types']['area']));
		}
		if(isset($this->fileconfig['types']['html'])) {
			$contentfields = array_merge($contentfields, explode(',', $this->fileconfig['types']['html']));
		}
		if(isset($this->fileconfig['types']['bbcode'])) {
			$contentfields = array_merge($contentfields, explode(',', $this->fileconfig['types']['bbcode']));
		}
		
		$result = array();
		$count = 0;
		
		if(preg_match("/ /", $this->searchquery)) {
			$this->searchquery = explode(' ', $this->searchquery);
		}
		
		
		$wheres = array();
		$selects = array();
		$joins = array();
		$likes = array();
		
		$datafields = $this->fileconfig['data'];
		$datawheres = array();
		if(array_key_exists('topic', $this->fileconfig) || array_key_exists('cat', $this->fileconfig)) {
			$selects[] = 'd.id';
			
			if(in_array('topicid', $datafields))
				$selects[] = 'd.topicid';
			
			if(in_array('catid', $datafields))
				$selects[] = 'd.catid';
			
			if(in_array('fromtime', $datafields)) {
				$datawheres[] = "((d.fromtime < ".time()." OR TRIM(d.fromtime) = '') AND (d.totime > ".time()." OR TRIM(d.totime) = ''))";
			}
			
			if(in_array('onoff', $datafields))
				$datawheres[] = "d.onoff = '1'";
			
			if(in_array('lang', $datafields))
				$datawheres[] = "d.lang = '".$this->pagelang."'";
		}
		else {
			if(in_array('topicid', $datafields))
				$selects[] = 'topicid';
			
			if(in_array('catid', $datafields)) 
				$selects[] = 'catid';
			
			if(in_array('fromtime', $datafields)) {
				$datawheres[] = "((fromtime < ".time()." OR TRIM(fromtime) = '') AND (totime > ".time()." OR TRIM(totime) = ''))";
			}
			
			if(in_array('onoff', $datafields))
				$datawheres[] = "onoff = '1'";
			
			if(in_array('lang', $datafields))
				$datawheres[] = "lang = '".$this->pagelang."'";
		}
		
		if(count($datawheres) > 0)
			$wheres[] = implode(" AND ", $datawheres);
		
		$topicwhere = '';
		if(array_key_exists('topic', $this->fileconfig)) {
			$topicfields = $this->fileconfig['topic'];
			$topicwheres = array();
			
			$joins[] = 'topics t';
			$topicwheres[] = "t.id = d.topicid";
			$selects[] = 't.topic';
			
			if(in_array('catid', $topicfields))
				$selects[] = 't.catid';
			
			if(in_array('fromtime', $topicfields)) {
				$selects[] = 't.fromtime';
				$selects[] = 't.totime';
				$topicwheres[] = "((t.fromtime < ".time()." OR TRIM(t.fromtime) = '') AND (t.totime > ".time()." OR TRIM(t.totime) = ''))";
			}
			
			if(in_array('onoff', $topicfields)) {
				$selects[] = 't.onoff';
				$topicwheres[] = "t.onoff = '1'";
			}
			
			if(in_array('lang', $topicfields)) {
				$selects[] = 't.lang';
				$topicwheres[] = "t.lang = '".$this->pagelang."'";
			}
			
			if(count($topicwheres) > 0)
				$wheres[] = implode(" AND ", $topicwheres);
		}
		
		$catwhere = '';
		if(array_key_exists('cat', $this->fileconfig)) {
			$catfields = $this->fileconfig['cat'];
			$catwheres = array();
			
			$joins[] = 'cats c';
			
			if(in_array('catid', $datafields))
				$catwheres[] = "c.id = d.catid";
			
			if(array_key_exists('topic', $this->fileconfig) && in_array('catid', $this->fileconfig['topic']))
				$catwheres[] = "c.id = t.catid";
			
			if(in_array('onoff', $catfields)) {
				$selects[] = 'c.onoff';
				$catwheres[] = "c.onoff = '1'";
			}
			
			if(in_array('lang', $catfields)) {
				$selects[] = 'c.lang';
				$catwheres[] = "c.lang = '".$this->pagelang."'";
			}
			
			if($this->basecat != '' && in_array('basecat', $this->fileconfig['base'])) {
				if(in_array('subcats', $this->fileconfig['base'])) {
					$submenu = $this->loadsubcatmenu();
					if(count($submenu) > 0) {
						$subcatids = $this->getsubcatidsfrombasecat($submenu);
					}
					$subcatids[] = $this->basecat;
					sort($subcatids);
					$catwheres[] = 'c.id IN ('.implode(',', $subcatids).')';
				}
				else {
					$catwheres[] = 'c.id IN ('.$this->basecat.')';
				}
			}
			
			if(count($catwheres) > 0)
				$wheres[] = implode(" AND ", $catwheres);
		}
		
		$joinfield = (array_key_exists('topic', $this->fileconfig) || array_key_exists('cat', $this->fileconfig)) ? 'd.' : '';
		if(is_array($this->searchquery)) {
			for($i = 0; $i < count($this->searchquery); $i++) {
				foreach($contentfields as $contentfield) {
					$likes[] = $joinfield.$contentfield." LIKE '%".$this->searchquery[$i]."%'";
				}
			}
		}
		else {
			foreach($contentfields as $contentfield) {
				$likes[] = $joinfield.$contentfield." LIKE '%".$this->searchquery."%'";
			}
		}
		
		$select = (count($selects) > 0) ? implode(", ", $selects).', ' : '';
		$join = (count($joins) > 0) ? " INNER JOIN ".implode(", ", $joins) : '';
		$where = (count($wheres) > 0) ? " WHERE ".implode(" AND ", $wheres) : '';
		$like = ($where != '' && count($likes) > 0) ? " AND " : " WHERE ";
		$like.= (count($likes) > 0) ? "(".implode(" OR ", $likes).")" : '';
		
		if(array_key_exists('topic', $this->fileconfig) || array_key_exists('cat', $this->fileconfig)) {
			$datas = $this->moddb->query("SELECT ".$select."d.".implode(', d.', $contentfields)." FROM datas d".$join.$where.$like." ORDER BY d.id DESC LIMIT ".$this->searchlimit);
		}
		else {
			$datas = $this->moddb->query("SELECT id, ".implode(', ', $contentfields)." FROM datas".$where.$like." ORDER BY id DESC LIMIT ".$this->searchlimit);
		}
		
		$datanumrows = $this->getdbnumrows($datas);
		
		if($datanumrows > 0) {
			$set_link_data = file_exists($this->serverpath.'/modules/'.$this->modname.'/view/tpls/datafull.tpl') ? 1 : 0;
			
			while($data = $datas->fetchArray(SQLITE3_ASSOC))
			{
				$link_cat = '';
				$link_topic = '';
				$link_data = '';
				$catorder = '';
				
				$link_data = ($set_link_data == 1) ? '&amp;'.$this->params['data'].'='.$data['id'] : '';
				if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
				{
					if($this->basecat != '' && $data['catid'] == $this->basecat) {
						$catorder = '';
					}
					else {
						$catorder = $this->findfirstinorder($data['catid']);
						if($catorder == 'catid_notfound_insub') {
							continue;
						}
					}
				}
				
				if(array_key_exists('topic', $this->fileconfig) && in_array('topicid', $this->fileconfig['data']))
				{
					if($this->is_serialized($data['topic'])) {
						$topiclangs = unserialize($data['topic']);
						$data['topic'] = $topiclangs[$this->pagelang];
					}
					$link_topic = '&amp;'.$this->params['topic'].'='.$data['topicid'];
					
					if(array_key_exists('cat', $this->fileconfig) && in_array('catid', $this->fileconfig['topic']))
					{
						if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
						{
							if($this->basecat != '' && $catorder != '') {
								$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
							}
							else {
								if($catorder == '' && $this->basecat == '')
									$link_cat = '&amp;'.$this->params['order'].'=0';
								elseif($catorder != '')
									$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
							}
						}
						else
						{
							if($this->basecat == '')
								$link_cat = '&amp;'.$this->params['cat'].'='.$data['catid'];
						}
					}
				}
				elseif(array_key_exists('cat', $this->fileconfig) && in_array('catid', $this->fileconfig['data']))
				{
					if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
					{
						if($this->basecat != '' && $catorder != '') {
							$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
						}
						else {
							if($catorder == '' && $this->basecat == '')
								$link_cat = '&amp;'.$this->params['order'].'=0';
							elseif($catorder != '')
								$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
						}
					}
					else
					{
						if($this->basecat == '')
							$link_cat = '&amp;'.$this->params['cat'].'='.$data['catid'];
					}
				}
				
				$title = ($set_link_data != 1 && array_key_exists('topic', $this->fileconfig)) ? $data['topic'] : '';
				$contents = array();
				$removebbcode = '#[[\/\!]*?[^\[\]]*?]#usi';
				foreach($contentfields as $contentfield) {
					if($title == '' && $set_link_data == 1 && $titlefield != '' && $contentfield == $titlefield) {
						if($this->is_serialized($data[$contentfield])) {
							$fieldlangs = unserialize($data[$contentfield]);
							$title = $fieldlangs[$this->pagelang];
						}
						else {
							$title = $data[$contentfield];
						}
					}
					else {
						if($this->is_serialized($data[$contentfield])) {
							$fieldlangs = unserialize($data[$contentfield]);
							$content = $fieldlangs[$this->pagelang];
						}
						else {
							$content = $data[$contentfield];
						}
						$content = strip_tags($content);
						$content = preg_replace($removebbcode, '', $content);
						
						if($content != '') {
							$contents[] = html_entity_decode($content);
						}
					}
				}
				
				$contentresult = implode(' ', $contents);
				
				$result[$count]['title'] = ($title == '') ? substr(0, 35, $contentresult) : html_entity_decode($title);
				$result[$count]['contents'] = $contentresult;
				$result[$count]['url'] = $this->modulepage.$link_cat.$link_topic.$link_data;
				
				$count++;
			}
			
			$this->searchresult = $result;
		}
	}
	
	function modsitemap()
	{
		$result = array();
		
		$datafields = $this->fileconfig['data'];
		$selects = array();
		$joins = array();
		$wheres = array();
		$datawheres = array();
		
		$multilang = '0';
		if(array_key_exists('code', $this->langconf) && array_key_exists('base', $this->fileconfig) && in_array('multilang', $this->fileconfig['base'])) {
			$multilang = '1';
		}
		
		if(array_key_exists('topic', $this->fileconfig) || array_key_exists('cat', $this->fileconfig)) {
			$selects[] = 'd.id';
			
			if(in_array('topicid', $datafields))
				$selects[] = 'd.topicid';
			
			if(in_array('catid', $datafields))
				$selects[] = 'd.catid';
			
			if(in_array('copyof', $datafields)) {
				$selects[] = 'd.copyof';
				$datawheres[] = "TRIM(d.copyof) = ''";
			}
			
			if(in_array('fromtime', $datafields)) {
				$selects[] = 'd.fromtime';
				$selects[] = 'd.totime';
				$datawheres[] = "((d.fromtime < ".time()." OR TRIM(d.fromtime) = '') AND (d.totime > ".time()." OR TRIM(d.totime) = ''))";
			}
			
			if(in_array('onoff', $datafields)) {
				$selects[] = 'd.onoff';
				$datawheres[] = "d.onoff = '1'";
			}
			
			if(in_array('lang', $datafields) && isset($this->pagelang)) {
				$selects[] = 'd.lang';
				$datawheres[] = "d.lang = '".$this->pagelang."'";
			}
		}
		else {
			if(in_array('topicid', $datafields))
				$selects[] = 'topicid';
			
			if(in_array('catid', $datafields)) 
				$selects[] = 'catid';
			
			if(in_array('copyof', $datafields)) {
				$selects[] = 'copyof';
				$datawheres[] = "TRIM(copyof) = ''";
			}
			
			if(in_array('fromtime', $datafields)) {
				$selects[] = 'fromtime';
				$selects[] = 'totime';
				$datawheres[] = "((fromtime < ".time()." OR TRIM(fromtime) = '') AND (totime > ".time()." OR TRIM(totime) = ''))";
			}
			
			if(in_array('onoff', $datafields)) {
				$selects[] = 'onoff';
				$datawheres[] = "onoff = '1'";
			}
			
			if(in_array('lang', $datafields) && isset($this->pagelang)) {
				$selects[] = 'lang';
				$datawheres[] = "lang = '".$this->pagelang."'";
			}
		}
		
		if(count($datawheres) > 0)
			$wheres[] = implode(" AND ", $datawheres);
		
		$topicwhere = '';
		if(array_key_exists('topic', $this->fileconfig)) {
			$topicfields = $this->fileconfig['topic'];
			$topicwheres = array();
			
			$joins[] = 'topics t';
			$topicwheres[] = "t.id = d.topicid";
			$selects[] = 't.topic';
			
			if(in_array('catid', $topicfields))
				$selects[] = 't.catid';
			
			if(in_array('fromtime', $topicfields)) {
				$selects[] = 't.fromtime';
				$selects[] = 't.totime';
				$topicwheres[] = "((t.fromtime < ".time()." OR TRIM(t.fromtime) = '') AND (t.totime > ".time()." OR TRIM(t.totime) = ''))";
			}
			
			if(in_array('onoff', $topicfields)) {
				$selects[] = 't.onoff';
				$topicwheres[] = "t.onoff = '1'";
			}
			
			if(in_array('lang', $topicfields) && isset($this->pagelang)) {
				$selects[] = 't.lang';
				$topicwheres[] = "t.lang = '".$this->pagelang."'";
			}
			
			if(count($topicwheres) > 0)
				$wheres[] = implode(" AND ", $topicwheres);
		}
		
		$catwhere = '';
		if(array_key_exists('cat', $this->fileconfig)) {
			$catfields = $this->fileconfig['cat'];
			$catwheres = array();
			
			$joins[] = 'cats c';
			
			if(in_array('catid', $datafields))
				$catwheres[] = "c.id = d.catid";
			
			if(array_key_exists('topic', $this->fileconfig) && in_array('catid', $this->fileconfig['topic']))
				$catwheres[] = "c.id = t.catid";
			
			if(in_array('onoff', $catfields)) {
				$selects[] = 'c.onoff';
				$catwheres[] = "c.onoff = '1'";
			}
			
			if(in_array('lang', $catfields) && isset($this->pagelang)) {
				$selects[] = 'c.lang';
				$catwheres[] = "c.lang = '".$this->pagelang."'";
			}
			
			if($this->basecat != '' && in_array('basecat', $this->fileconfig['base'])) {
				if(in_array('subcats', $this->fileconfig['base'])) {
					$submenu = $this->loadsubcatmenu();
					if(count($submenu) > 0) {
						$subcatids = $this->getsubcatidsfrombasecat($submenu);
					}
					$subcatids[] = $this->basecat;
					sort($subcatids);
					$catwheres[] = 'c.id IN ('.implode(',', $subcatids).')';
				}
				else {
					$catwheres[] = 'c.id IN ('.$this->basecat.')';
				}
			}
			
			if(count($catwheres) > 0)
				$wheres[] = implode(" AND ", $catwheres);
		}
		
		$select = (count($selects) > 0) ? implode(", ", $selects).' ' : '';
		$join = (count($joins) > 0) ? " INNER JOIN ".implode(", ", $joins) : '';
		$where = (count($wheres) > 0) ? " WHERE ".implode(" AND ", $wheres) : '';
		
		if(array_key_exists('topic', $this->fileconfig) || array_key_exists('cat', $this->fileconfig)) {
			$datas = $this->moddb->query("SELECT ".$select." FROM datas d".$join.$where." ORDER BY d.id");
		}
		else {
			$datas = $this->moddb->query("SELECT id FROM datas".$where." ORDER BY id");
		}
		
		$set_link_data = file_exists($this->serverpath.'/modules/'.$this->modname.'/view/tpls/datafull.tpl') ? 1 : 0;
		
		$parenturls = array();
		
		while($data = $datas->fetchArray(SQLITE3_ASSOC))
		{
			$link_data = '';
			$link_topic = '';
			$link_cat = '';
			$catorder = '';
			
			$link_data = ($set_link_data == 1) ? '&amp;'.$this->params['data'].'='.$data['id'] : '';
			if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
			{
				if($this->basecat != '' && $data['catid'] == $this->basecat) {
					$catorder = '';
				}
				else {
					$catorder = $this->findfirstinorder($data['catid']);
					if($catorder == 'catid_notfound_insub') {
						continue;
					}
				}
			}
			
			if(array_key_exists('topic', $this->fileconfig) && in_array('topicid', $this->fileconfig['data']))
			{
				$link_topic = '&amp;'.$this->params['topic'].'='.$data['topicid'];
				
				if(array_key_exists('cat', $this->fileconfig) && in_array('catid', $this->fileconfig['topic']))
				{
					if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
					{
						if($this->basecat != '' && $catorder != '') {
							$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
						}
						else {
							if($catorder == '' && $this->basecat == '')
								$link_cat = '&amp;'.$this->params['order'].'=0';
							elseif($catorder != '')
								$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
						}
					}
					else
					{
						if($this->basecat == '')
							$link_cat = '&amp;'.$this->params['cat'].'='.$data['catid'];
					}
					
					if($link_cat != '')
						$parenturls['cats'][] = $link_cat;
				}
				
				if($set_link_data == 1) {
					$parenturls['topics'][] = $link_cat.$link_topic;
				}
			}
			elseif(array_key_exists('cat', $this->fileconfig) && in_array('catid', $this->fileconfig['data']))
			{
				if(array_key_exists('base', $this->fileconfig) && in_array('subcats', $this->fileconfig['base']))
				{
					if($this->basecat != '' && $catorder != '') {
						$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
					}
					else {
						if($catorder == '' && $this->basecat == '')
							$link_cat = '&amp;'.$this->params['order'].'=0';
						elseif($catorder != '')
							$link_cat = '&amp;'.$this->params['order'].'='.$catorder;
					}
				}
				else
				{
					if($this->basecat == '')
						$link_cat = '&amp;'.$this->params['cat'].'='.$data['catid'];
				}
				
				if($link_cat != '')
					$parenturls['cats'][] = $link_cat;
			}
			
			if($link_data != '') {
				if($multilang == '1') {
					foreach($this->langconf['code'] as $code) {
						if(isset($this->modulepages[$code])) {
							$result[$code][] = $this->modulepages[$code].$link_cat.$link_topic.$link_data;
						}
					}
				}
				else {
					if(array_key_exists('code', $this->langconf) && isset($this->pagelang) && isset($this->modulepages[$this->pagelang])) {
						$result[$this->pagelang][] = $this->modulepages[$this->pagelang].$link_cat.$link_topic.$link_data;
					}
					else {
						if(isset($this->modulepages[0])) {
							$result[] = $this->modulepages[0].$link_cat.$link_topic.$link_data;
						}
					}
				}
			}
		}
		
		$uniqueparenturls = array();
		if(array_key_exists('cats', $parenturls))
			$uniqueparenturls['cats'] = array_unique($parenturls['cats']);
		if(array_key_exists('topics', $parenturls))
			$uniqueparenturls['topics'] = array_unique($parenturls['topics']);
		
		if(count($uniqueparenturls) > 0) {
			$resultparenturls = array();
			foreach($uniqueparenturls as $parents => $urls) {
				foreach($urls as $url) {
					if($multilang == '1') {
						foreach($this->langconf['code'] as $code) {
							if(isset($this->modulepages[$code])) {
								$resultparenturls[$code][] = $this->modulepages[$code].$url;
							}
						}
					}
					else {
						if(isset($this->modulepages[$this->pagelang])) {
							$resultparenturls[$this->pagelang][] = $this->modulepages[$this->pagelang].$url;
						}
						else {
							if(isset($this->modulepages[0])) {
								$resultparenturls[] = $this->modulepages[0].$url;
							}
						}
					}
				}
			}
			
			$this->parenturls = $resultparenturls;
		}
		
		$uniqueresult = array();
		if(array_key_exists('code', $this->langconf)) {
			foreach($this->langconf['code'] as $code) {
				if(array_key_exists($code, $result)) {
					$uniqueresult[$code] = array_unique($result[$code]);
				}
			}
		}
		else {
			$uniqueresult = array_unique($result);
		}
		
		return $uniqueresult;
	}
}
    
    
/*************** Inputfunctions ***************/
    

class MMInputClass extends MMViewClass
{
    function saveuserpost()
    {
    	if(isset($this->post[$this->modname.'postsave']) && (!array_key_exists('groups', $this->dbconfig) || $this->userisadmin || $this->getwritepermission())) {
            unset($this->post[$this->modname.'postsave']);
            foreach($this->fileconfig['required'] as $required) {
                if(array_key_exists($required, $this->post) && $this->post[$required] == '') {
                    $this->postalert = $this->viewdefine('please_fill');
                    break;
                }
            }
			if($this->postalert == '' && in_array('topic', $this->post) && $this->post['topic'] == '') {
				$this->postalert = $this->viewdefine('please_fill');
			}
            if($this->postalert != '') {
                $this->postback = $this->post;
            }
            else {
				$savetopic = '';
                foreach($this->post as $postkey => $postvalue) {
					if(in_array('topicid', $this->fileconfig['data']) && $postkey == 'topic') {
						$savetopic = $this->validinput('topic', $this->post['topic']);
					}
                    elseif(!in_array($postkey, $this->fileconfig['userinputs'])) {
						unset($this->post[$postkey]);
                    }
                }
				if($savetopic != '') {
					if(array_key_exists('topic', $this->post)) {
						unset($this->post['topic']);
						$this->posttopicstart = 1;
						$this->saveuserdata();
						$this->saveusertopic($savetopic);
					}
				}
				else {
					$this->saveuserdata();
				}
				
	            if(isset($this->get[$this->params['load']]) && ($this->get[$this->params['load']] == $this->params['topic'].'new' || $this->get[$this->params['load']] == $this->params['data'].'new')) {
					
					$domain = 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'];
					$request = $_SERVER['REQUEST_URI'];
					$redirect = '';
					
					if(preg_match('#index.php?page=#i', $request)) {
						$redirect.= $domain.'/index.php?page='.$this->getpage;
						if(isset($this->get[$this->params['order']]))
							$redirect.= '&amp;'.$this->params['order'].'='.$this->validorder($this->get[$this->params['order']]);
						if(isset($this->get[$this->params['cat']]))
							$redirect.= '&amp;'.$this->params['cat'].'='.$this->validnum($this->get[$this->params['cat']]);
						if(isset($this->get[$this->params['topic']]))
							$redirect.= '&amp;'.$this->params['topic'].'='.$this->validnum($this->get[$this->params['topic']]);
					}
					else {
						if(preg_match('#'.$this->params['load'].'-'.$this->params['topic'].'new#i', $request))
							$redirect.= $domain.str_replace('-'.$this->params['load'].'-'.$this->params['topic'].'new', '', $request);
						if(preg_match('#'.$this->params['load'].'-'.$this->params['data'].'new#i', $request))
							$redirect.= $domain.str_replace('-'.$this->params['load'].'-'.$this->params['data'].'new', '', $request);
					}
					
					if($redirect != '')
						header("Location: ".$redirect);
	            }
            }
        }
    }
    
    function saveusertopic($savetopic)
    {
		$fields = $this->fileconfig['topic'];
        
        $keys = array();
        $values = array();
        
        $keys[] = 'topic';
        $values[] = $this->moddb->escapeString($savetopic);
        if($this->basecat != '' && !isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])) {
            $catid = $this->basecat;
        }
        elseif(isset($this->get[$this->params['cat']])) {
            $catid = $this->validnum($this->get[$this->params['cat']]);
        }
        elseif(isset($this->get[$this->params['order']])) {
            if($this->curcat == '')
                $this->getcatsfromsubs();
            $catid = $this->curcat;
        }
		if(isset($catid) && in_array('catid', $fields)) {
			$keys[] = 'catid';
			$values[] = $catid;
		}
        if(in_array('sort', $fields)) {
            $keys[] = 'sort';
            $values[] = $this->gettopicrows()+1;
        }
        if(in_array('onoff', $fields)) {
            $keys[] = 'onoff';
            $values[] = $this->dbconfig['release'] == 'auto' ? '1' : '';
        }
        if(in_array('lang', $fields)) {
            $keys[] = 'lang';
            $values[] = $this->pagelang;
        }
        $keys[] = 'startid';
        $values[] = $this->postdatastartid;
        
        $this->moddb->query('INSERT INTO topics ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')');
        $thistopicid = $this->moddb->lastInsertRowid();
        $this->moddb->query("UPDATE datas SET topicid = '".$thistopicid."' WHERE datas.id = '".$this->postdatastartid."'");
    }
    
    function saveuserdata()
    {
		$fields = $this->fileconfig['data'];
        
        $filekeys = array();
        $filevalues = array();
		
        $files = $this->getdbfieldfromfile('file');
        if(is_array($files)) {
            foreach($files as $file) {
                if(isset($this->files[$file]) && $this->files[$file]['name'] != '') {
	                $dateityp = $this->files[$file]['type'];
	                $savedtypes = $this->dbconfig['savemime'][$file];
					if($savedtypes == '' || preg_match("(".$savedtypes.")", $dateityp)) {
	            		$filefolder = $this->createunique('files');
	                    $filenames = $this->upload($this->files, $file, $filefolder, 'file');
	                    if($filenames != '') {
	                        $filekeys[] = $file;
	                        $filevalues[] = serialize($filenames);
	                    }
	                }
                }
            }
        }
        
        $images = $this->getdbfieldfromfile('image');
        if(is_array($images)) {
            foreach($images as $image) {
                if(isset($this->files[$image]) && $this->files[$image]['name'] != '') {
	                $dateityp = $this->files[$image]['type'];
					if(preg_match("(jpg|jpeg|gif|png)", $dateityp)) {
	            		$imagefolder = $this->createunique('imgs');
	                    $imagenames = $this->upload($this->files, $image, $imagefolder, 'image');
	                    if($imagenames != '') {
	                        $filekeys[] = $image;
	                        $filevalues[] = serialize($imagenames);
	                    }
                    }
                }
            }
        }
        
        $multis = $this->getdbfieldfromfile('multi');
        if(is_array($multis)) {
            foreach($multis as $multi) {
				if(isset($this->files[$multi])) {
					$multicount = count($this->files[$multi]['name']);
					$multifolder = $this->createunique('imgs');
					$multinames = array();
					for($i = 1; $i <= $multicount; $i++) {
						if($this->files[$multi]['name'][$i] != '') {
							$dateityp = $this->files[$multi]['type'][$i];
							if(preg_match("(jpg|jpeg|gif|png)", $dateityp)) {
								$multinames[] = $this->upload($this->files, array($multi, $i), $multifolder, 'multi');
							}
						}
					}
					if(count($multinames) >= '1') {
						$filekeys[] = $multi;
						$filevalues[] = serialize($multinames);
					}
				}
			}
        }
		
		$validpost = array();
		
        $users = $this->getdbfieldfromfile('user');
        if(is_array($users)) {
            foreach($users as $user) {
				if(in_array($user, $fields)) {
					$userid = isset($_SESSION['userauth']['userid']) ? $_SESSION['userauth']['userid'] : '';
					if($userid == '') {
						if($this->userisadmin)
							$validpost[$user] = strtoupper('_'.$this->modname.'lang_adminname_');
						elseif(isset($this->post[$user]))
							$validpost[$user] = $this->validinput('username', $this->post[$user]).'###'.strtoupper('_'.$this->modname.'lang_guest_');
						unset($this->post[$user]);
					}
					else {
						if(isset($this->post[$user]))
							unset($this->post[$user]);
						$validpost[$user] = $this->validinput($user, $userid);
					}
				}
			}
		}
		
        $dates = $this->getdbfieldfromfile('date');
        if(is_array($dates)) {
            foreach($dates as $date) {
				if(in_array($date, $fields)) {
					if(isset($this->post[$date]) && $this->post[$date] != '') {
						$dateinput = $this->validinput($date, $this->post[$date]);
						unset($this->post[$date]);
						$datenums = explode('-', $dateinput);
						if(checkdate($datenums[1], $datenums[2], $datenums[0]))
						$validpost[$date] = strtotime($dateinput);
					}
					else {
						$validpost[$date] = '';
					}
				}
			}
		}
		
        $states = $this->getdbfieldfromfile('state');
        if(is_array($states)) {
            foreach($states as $state) {
				if(in_array($state, $fields)) {
					if(isset($this->post[$state]) && $this->post[$state] != '') {
						$stateinput = $this->validinput($state, $this->post[$state]);
						unset($this->post[$state]);
						$validpost[$state] = $stateinput;
					}
				}
			}
		}
		
		foreach($this->post as $key => $post) {
			if(in_array($key, $fields)) {
				$validpost[$key] = $this->validinput($key, $post);
			}
		}
        
        $availkeys = array_keys($validpost);
        $availvalues = array_map('SQLite3::escapeString', array_values($validpost));
        
        $keys = array_merge($filekeys, $availkeys);
        $values = array_merge($filevalues, $availvalues);
        
        if($this->basecat != '' && !isset($this->get[$this->params['cat']]) && !isset($this->get[$this->params['order']])) {
            $catid = $this->basecat;
        }
        elseif(isset($this->get[$this->params['cat']])) {
            $catid = $this->validnum($this->get[$this->params['cat']]);
        }
        elseif(isset($this->get[$this->params['order']])) {
            if($this->curcat == '')
                $this->getcatsfromsubs();
            $catid = $this->curcat;
        }
		if(isset($catid) && in_array('catid', $fields)) {
			$keys[] = 'catid';
			$values[] = $catid;
		}
        if($this->posttopicstart != 1 && isset($this->get[$this->params['topic']])) {
            $topicid = $this->validnum($this->get[$this->params['topic']]);
            if(in_array('topicid', $fields)) {
                $keys[] = 'topicid';
                $values[] = $topicid;
            }
        }
        if(in_array('sort', $fields)) {
            $keys[] = 'sort';
            $values[] = ($this->posttopicstart == 1) ? '' : $this->getdatarows()+1;
        }
        if(in_array('onoff', $fields)) {
            $keys[] = 'onoff';
            $values[] = $this->dbconfig['release'] == 'auto' ? '1' : '';
        }
        if(in_array('lang', $fields)) {
            $keys[] = 'lang';
            $values[] = $this->pagelang;
        }
        
        $this->moddb->query('INSERT INTO datas ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')');
        
        if($this->posttopicstart == 1)
            $this->postdatastartid = $this->moddb->lastInsertRowid();
    }
}



