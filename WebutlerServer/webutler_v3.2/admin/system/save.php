<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(__FILE__))).'/includes/loader.php';

$webutlersave = new WebutlerAdminClass;
$webutlersave->config = $webutler_config;

if(!$webutlersave->checkadmin())
    exit('no access');

$webutlersave->htmlsource = $webutler_htmlsource;
$webutlersave->offlinepages = $webutler_offlinepages;
$webutlersave->mailaddresses = $webutler_mailaddresses;
$webutlersave->langconf = $webutler_langconf;
$webutlersave->categories = $webutler_categories;
$webutlersave->linkhighlite = $webutler_linkhighlite;
$webutlersave->moduleslist = $webutler_moduleslist;

$webutlersave->verifygetpage();

require_once $webutlersave->config['server_path'].'/admin/system/lang/'.$_SESSION['loggedin']['userlang'].'.php';


// Editbox auf/zu
if(isset($_POST['editboxzustand']))
{
    if($_POST['editboxzustand'] == 'show' || $_POST['editboxzustand'] == 'hide')
        $_SESSION['loggedin']['editboxzustand'] = $_POST['editboxzustand'];
}

// Seite hinzufügen
if(isset($_POST['savenewfile']))
{
    $filename = $webutlersave->filenamesigns($_POST['filename']);
    $layout = $webutlersave->checkfilenamesigns($_POST['layout'], 'tpl');
    $copy = $webutlersave->filenamesigns($_POST['copy']);
	
	if($filename != '')
	{
	    $newfile = $webutlersave->config['server_path'].'/content/pages/'.$filename;
        if(!is_writeable($webutlersave->config['server_path']."/content/pages/"))
        {
            echo '<span class="red">'.sprintf(_WBLANGADMIN_POPUPWIN_WRITEABLE_FOLDER_, '/content/pages').'</span>';
            exit;
        }
	    
		if(file_exists($newfile))
		{
            echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_NAMEEXISTS_.'</span>';
            exit;
		}
		else
		{
            if(isset($_POST['newfrom']))
				copy($webutlersave->config['server_path'].'/content/pages/'.$copy, $newfile);
			elseif($layout != '' && file_exists($webutlersave->config['server_path'].'/content/layouts/'.$layout))
				copy($webutlersave->config['server_path'].'/content/layouts/'.$layout, $newfile);
            else
				copy($webutlersave->config['server_path'].'/admin/system/dummy.tpl', $newfile);
			
            $webutlersave->setchmodaftersave($newfile);
			
            $webutlersave->setpageto_offlinearray($filename);
            
            if(isset($_POST['pagelang']) && $_POST['pagelang'] != '')
                $webutlersave->setpagetolang($filename, preg_replace('/[^a-z]/', '', $_POST['pagelang']));
            
			if(isset($_POST['pagecategory']) && $_POST['pagecategory'] != '')
				$webutlersave->savepagetocat($filename, $webutlersave->checknewcatname($_POST['pagecategory']));
            
			if(isset($_POST['check_auto']))
			{
                $menu_auto = $webutlersave->filenamesigns($_POST['menu_auto']);
				$menufile = $webutlersave->config['server_path'].'/content/menus/'.$menu_auto;
                if(!is_writeable($menufile))
                {
                    echo '<span class="red">'.sprintf(_WBLANGADMIN_POPUPWIN_WRITEABLE_FILE_, '/content/pages/'.$menu_auto).'</span>';
                    exit;
                }
                
				$name_auto = htmlspecialchars($_POST['name_auto']);
				
				$pos_auto = preg_replace('/[^0-9]/', '', $_POST['pos_auto']);
				if($pos_auto == '' || $pos_auto <= 0)
					$pos_auto = 1;
				
				$menulink = '<li><a href="index.php?page='.$filename.'">'.$name_auto.'</a></li>';
				$menuname = file_get_contents($menufile);
				
				$menuplus = array();
				$counter = 0;
				$results = explode("<li", $menuname);
				$lianzahl = count($results)-1;
				
				foreach ($results as $result)
				{
				    if($counter != 0)
						$result = "<li".$result;
						
					if($counter == $pos_auto)
					{
						$menuplus[] = $menulink."\n".$result;
					}
					else
					{
						if($counter == $lianzahl && $pos_auto >= $lianzahl)
						{
							$menuplus[] = str_replace("</ul>", $menulink."\n</ul>", $result);
						}
						else
						{
							$menuplus[] = $result;
						}
					}
					$counter = $counter+1;
				}
				
				$new_menu = implode("", $menuplus);
				
				$webutlersave->makebakfiles($menufile);
				
                file_put_contents($menufile, $new_menu);
        		
                $webutlersave->setchmodaftersave($menufile);
			}
			
            echo '<span class="green">'._WBLANGADMIN_POPUPWIN_PAGE_ISSAVED_.'</span>###'._WBLANGADMIN_POPUPWIN_ROUTE_NEWPAGE_.' '.$filename.'###'.$webutlersave->getsavelocationurl($filename);
            exit;
		}
	}
	else
	{
        echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_NONAME_.'</span>';
        exit;
	}
}

// Seite on-/offline
if(isset($_POST['setmodus']))
{
    $filename = $webutlersave->filenamesigns($_POST['filename']);
    $modusfile = $webutlersave->config['server_path'].'/content/pages/'.$filename;
	if(file_exists($modusfile))
	{
        if($_POST['setmodus'] == 'on')
        {
            if(in_array($filename, $webutlersave->offlinepages))
            {
                $webutlersave->delpagefrom_offlinearray($filename);
                if(!$webutlersave->accesspage($filename))
				{
                    echo 'noaccess';
                    //echo $_SESSION['loggedin']['userlang'];
                }
            }
        }
        elseif($_POST['setmodus'] == 'off')
        {
            $webutlersave->setpageto_offlinearray($filename);
            //echo $_SESSION['loggedin']['userlang'];
        }
	}
}

// Seite rückgängig
if(isset($_GET['version']) && $_GET['version'] == 'getlast')
{
    $iswriteable = $webutlersave->iswriteable('/content/pages');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
    $filepath = $webutlersave->config['server_path'].'/content/pages/'.$webutlersave->getpage;
	$lastfile = file_exists($filepath.'.tmp') ? $filepath.'.tmp' : $filepath;
	if(file_exists($lastfile))
	{
    	if(file_exists($lastfile.'.bak')) 
    	{
            unlink($lastfile);
            rename($lastfile.'.bak', $lastfile);
        }
    	$steps = $webutlersave->config['schritte_zurueck']-1;
        $bakext = '';
        $i = 0;
    	while($i <= $steps)
    	{
            $bakext.= '.bak';
        	if(file_exists($lastfile.$bakext.'.bak')) 
        	{
                rename($lastfile.$bakext.'.bak', $lastfile.$bakext);
        	}
        	else
        	{
                break;
        	}
            $i++;
    	}
        
        header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
	}
    else
	{
        header('Location: '.$webutlersave->config['homepage'].'/');
    }
}

// Seite publizieren
if(isset($_POST['dopublicpage']))
{
    $pagename = $webutlersave->filenamesigns($_POST['dopublicpage']);
    $pagefile = $webutlersave->config['server_path'].'/content/pages/'.$pagename;
	
	if(!file_exists($pagefile.'.tmp'))
	{
		echo 'error###'._WBLANGADMIN_POPUPWIN_TEMPFILE_NOTEXISTS_;
	}
	else
	{
		$iswriteable = $webutlersave->iswriteable('/content/pages');
		if($iswriteable != '')
		{
			echo 'error###content/pages '._WBLANGADMIN_POPUPWIN_TEMPFILE_NOTWRITEABLE_;
			exit;
		}
		$iswriteable = $webutlersave->iswriteable('/content/pages/'.$pagename);
		if($iswriteable != '')
		{
			echo 'error###content/pages/'.$pagename.' '._WBLANGADMIN_POPUPWIN_TEMPFILE_NOTWRITEABLE_;
			exit;
		}
		
		$webutlersave->savetemp2real($pagefile);
		
		echo 'ok###'._WBLANGADMIN_POPUPWIN_TEMPFILE_PUBLIC_;
	}
}

// Temp-Seite löschen
if(isset($_POST['deltemppage']))
{
	$pagename = $webutlersave->filenamesigns($_POST['deltemppage']);
    $pagefile = $webutlersave->config['server_path'].'/content/pages/'.$pagename;
	
	if(!file_exists($pagefile.'.tmp'))
	{
		echo 'error###'._WBLANGADMIN_POPUPWIN_TEMPFILE_NOTEXISTS_;
	}
	else
	{
		$iswriteable = $webutlersave->iswriteable('/content/pages');
		if($iswriteable != '')
		{
			echo 'error###content/pages '._WBLANGADMIN_POPUPWIN_TEMPFILE_NOTWRITEABLE_;
			exit;
		}
		$iswriteable = $webutlersave->iswriteable('/content/pages/'.$pagename.'.tmp');
		if($iswriteable != '')
		{
			echo 'error###content/pages/'.$pagename.' '._WBLANGADMIN_POPUPWIN_TEMPFILE_NOTWRITEABLE_;
			exit;
		}
		
		unlink($pagefile.'.tmp');
		$webutlersave->deletetempfiles($pagefile.'.tmp');
		
		echo 'ok###DELETED';
	}
}

// Seite umbenennen
if(isset($_POST['saverename']))
{
	$oldpagename = $webutlersave->filenamesigns($_POST['oldpagename']);
    $newpagename = $webutlersave->filenamesigns($_POST['newpagename']);
    
    $oldpagefile = $webutlersave->config['server_path'].'/content/pages/'.$oldpagename;
    $newpagefile = $webutlersave->config['server_path'].'/content/pages/'.$newpagename;
    
	if(file_exists($oldpagefile))
	{
    	if($newpagename == '')
    	{
            echo '<span class="red">'._WBLANGADMIN_WIN_RENAME_SAVE_NONEW_.'</span>';
        }
        else
    	{
        	if(file_exists($newpagefile))
        	{
                echo '<span class="red">'._WBLANGADMIN_WIN_RENAME_SAVE_NEWEXISTS_.'</span>';
            }
            else
        	{
                $iswriteable = $webutlersave->iswriteable('/content/pages/'.$oldpagename, 'ajax');
                if($iswriteable != '')
                {
                    echo $iswriteable;
                    exit;
                }
                
                $webutlersave->renamefileinmenus($oldpagename, $newpagename);
                $webutlersave->changenameinconfigfile($oldpagename, $newpagename);
                $webutlersave->changenameinofflinefile($oldpagename, $newpagename);
                $webutlersave->changenameinlanguagefile($oldpagename, $newpagename);
                $webutlersave->changenameincategoriesfile($oldpagename, $newpagename);
                
                rename($oldpagefile, $newpagefile);
				if(file_exists($oldpagefile.'.tmp')) 
				{
					rename($oldpagefile.'.tmp', $newpagefile.'.tmp');
				}
            	$steps = $webutlersave->config['schritte_zurueck']-1;
                $bak = '';
                $i = 0;
            	while($i <= $steps)
            	{
                    $bak.= '.bak';
                	if(file_exists($oldpagefile.$bak) || file_exists($oldpagefile.'.tmp'.$bak)) 
                	{
						if(file_exists($oldpagefile.$bak)) 
						{
							rename($oldpagefile.$bak, $newpagefile.$bak);
						}
						if(file_exists($oldpagefile.'.tmp'.$bak)) 
						{
							rename($oldpagefile.'.tmp'.$bak, $newpagefile.'.tmp'.$bak);
						}
                	}
                	else
                	{
                        break;
                	}
                    $i++;
            	}
                
				$urllang = ($webutlersave->config['languages'] == '1' && $webutlersave->config['langfolder'] == '1') ? '1' : '0';
				
                echo '<span class="green">'._WBLANGADMIN_WIN_RENAME_SAVE_ISSAVEED_.'</span>###'._WBLANGADMIN_WIN_RENAME_SAVE_RELOAD_.'###'.$webutlersave->getsavelocationurl($newpagename, ($urllang == '1' ? true : false));
                //echo '<span class="green">'._WBLANGADMIN_WIN_RENAME_SAVE_ISSAVEED_.'</span>###'._WBLANGADMIN_WIN_RENAME_SAVE_RELOAD_.'###'.$webutlersave->getsavelocationurl($newpagename);
            }
        }
    }
}

// Seite löschen
if(isset($_POST['deletefile']))
{
    $delfilename = $webutlersave->filenamesigns($_POST['delfile']);
    $iswriteable = $webutlersave->iswriteable('/content/pages/'.$delfilename, 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
	$delfile = $webutlersave->config['server_path']."/content/pages/".$delfilename;
	if(file_exists($delfile.'.tmp')) 
	{
		$iswriteable = $webutlersave->iswriteable('/content/pages/'.$delfilename.'.tmp', 'ajax');
		if($iswriteable != '')
		{
			echo $iswriteable;
			exit;
		}
	}
    
    $webutlersave->renamefileinmenus($delfilename);
    $webutlersave->changenameinconfigfile($delfilename);
    $webutlersave->changenameinofflinefile($delfilename);
    $webutlersave->changenameinlanguagefile($delfilename);
    $webutlersave->changenameincategoriesfile($delfilename);
	
    unlink($delfile);
	if(file_exists($delfile.'.tmp')) 
	{
		unlink($delfile.'.tmp');
	}
	$webutlersave->deletetempfiles($delfile.'.tmp');
    $bakext = '';
    $i = 0;
	while ($i < $webutlersave->config['schritte_zurueck'])
	{
        $bakext.= '.bak';
		if(file_exists($delfile.$bakext))
		{
			$iswriteable = $webutlersave->iswriteable('/content/pages/'.$delfilename.$bakext, 'ajax');
			if($iswriteable != '')
			{
				echo $iswriteable;
				exit;
			}
			unlink($delfile.$bakext);
		}
    	else
		{
			break;
		}
        $i++;
	}
	
	if($_POST['locationpage'] == $delfilename)
    {
        echo '<span class="green">'._WBLANGADMIN_POPUPWIN_DEFPAGE_ISDELETED_.'</span>###'._WBLANGADMIN_POPUPWIN_ROUTE_FRONTPAGE_.'###'.$webutlersave->config['homepage'].'/';
	}
	else
    {
        echo '<span class="green">'._WBLANGADMIN_POPUPWIN_PAGE_ISDELETED_.'</span>';
	}
}

// Bearbeitung Inhalt
if(isset($_POST['content_1'])) 
{
    $iswriteable = $webutlersave->iswriteable('/content/pages');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    $iswriteable = $webutlersave->iswriteable('/content/pages/'.$webutlersave->getpage);
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
	if(isset($_POST['saveastemp']) && $_POST['saveastemp'] == 1) {
		$pagefile = $webutlersave->config['server_path'].'/content/pages/'.$webutlersave->getpage;
		
		if(file_exists($pagefile.'.tmp')) {
			$content = $pagefile.'.tmp';
			$savefile = $content;
			$webutlersave->makebakfiles($content);
		}
		else {
			$content = $pagefile;
			$savefile = $content.'.tmp';
		}
		
		$oldcontent = preg_split('#<body#', file_get_contents($content));
		
		$postmetas = preg_split('#<body#', $_POST['metas']);
		$newmetas = $postmetas[0];
		
		$newtext = $newmetas.'<body'.$oldcontent[1];
		
		$hascontent = substr_count($newtext, '<!-- begin_content -->');
		if($hascontent > 0) {
			$savecontent = '';
			
			$startpoint = '<!-- begin_content -->';
			$endpoint = '<!-- end_content -->';
			$htmlsources = preg_split('#'.preg_quote($startpoint).'.*?'.preg_quote($endpoint).'#si', $newtext);
			
			foreach($htmlsources as $count => $htmlsource)
			{
				$savecontent.= $htmlsource;
				if($count < $hascontent) {
					$savecontent.= $startpoint."\n".$_POST['content_'.($count+1)]."\n".$endpoint;
				}
			}
		}
		else {
			$savecontent = $newtext;
		}
		
		file_put_contents($savefile, $savecontent);
		
		$webutlersave->setchmodaftersave($savefile);
		
		header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
	}
	else {
		$content = $webutlersave->config['server_path'].'/content/pages/'.$webutlersave->getpage;
		$webutlersave->makebakfiles($content);
		
		$oldcontent = preg_split('#<body#', file_get_contents($webutlersave->check_temp_fileexists($content)));
		
		$postmetas = preg_split('#<body#', $_POST['metas']);
		$newmetas = $postmetas[0];
		
		$newtext = $newmetas.'<body'.$oldcontent[1];
		
		$hascontent = substr_count($newtext, '<!-- begin_content -->');
		if($hascontent > 0) {
			$savecontent = '';
			
			$startpoint = '<!-- begin_content -->';
			$endpoint = '<!-- end_content -->';
			$htmlsources = preg_split('#'.preg_quote($startpoint).'.*?'.preg_quote($endpoint).'#si', $newtext);
			
			foreach($htmlsources as $count => $htmlsource)
			{
				$savecontent.= $htmlsource;
				if($count < $hascontent) {
					$savecontent.= $startpoint."\n".$_POST['content_'.($count+1)]."\n".$endpoint;
				}
			}
		}
		else {
			$savecontent = $newtext;
		}
		
		file_put_contents($content, $savecontent);
		if(file_exists($content.'.tmp')) {
			unlink($content.'.tmp');
		}
		
		$webutlersave->setchmodaftersave($content);
		
		header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
	}
}


// Bearbeitung Fullpage
if(isset($_POST['fulleditor']))  
{
	$iswriteable = $webutlersave->iswriteable('/content/pages');
	if($iswriteable != '')
	{
		echo $iswriteable;
		exit;
	}
	$iswriteable = $webutlersave->iswriteable('/content/pages/'.$webutlersave->getpage);
	if($iswriteable != '')
	{
		echo $iswriteable;
		exit;
	}
	
	$posteddata = $_POST['fulleditor'];
	
// Bearbeitung Fullpage zwischenspeichern
	if(isset($_POST['saveastemp']))  
	{
		if($posteddata == '')
		{
			$posteddata = "\n";
		}
		else 
		{
			$posteddata = trim($posteddata);
			$posteddata = $webutlersave->deletelastpfromsource($posteddata);

			$posteddata = preg_replace('#<link([^>]*?)admin/system/css/editor.css(.*?)>#si', '', $posteddata);
			$posteddata = preg_replace('#<link([^>]*?)content/columns/columns.css(.*?)>#si', '', $posteddata);
			
			$stylefiles = $webutlersave->stylefiles();
			
			foreach($stylefiles as $stylefile) {
				if(preg_match('#'.$stylefile['file'].'#i', $posteddata))
					$posteddata = preg_replace('#<link([^>]*?)'.$stylefile['file'].'(\?t=[0-9]+)(.*?)>#si', '<link${1}'.$stylefile['file'].'${3}>', $posteddata);
			}
			
			$menus = $webutlersave->config['server_path'].'/content/menus';
			$handle = opendir ($menus);
			while (false !== ($menufile = readdir ($handle)))
			{ 
				if( $menufile != '.' && $menufile != '..' )
				{
					$posteddata = preg_replace('#(<!-- begin_menu_'.$menufile.' -->)(.*?)(<!-- end_menu_'.$menufile.' -->)#Usi', '$1$3', $posteddata);
					$posteddata = str_replace("<!-- begin_menu_".$menufile." -->", "<!-- begin_menu_".$menufile." -->\n<!-- menu_".$menufile." -->\n", $posteddata);
				}
			}
			closedir($handle);
			
			$blocks = $webutlersave->config['server_path'].'/content/blocks';
			$handle = opendir ($blocks);
			while (false !== ($blockfile = readdir ($handle)))
			{ 
				if( $blockfile != '.' && $blockfile != '..' )
				{
					$posteddata = preg_replace('#(<!-- begin_block_'.$blockfile.' -->)(.*?)(<!-- end_block_'.$blockfile.' -->)#Usi', '$1$3', $posteddata);
					$posteddata = str_replace("<!-- begin_block_".$blockfile." -->", "<!-- begin_block_".$blockfile." -->\n<!-- block_".$blockfile." -->\n", $posteddata);
				}
			}
			closedir($handle);
		}
		
		$content = $webutlersave->config['server_path'].'/content/pages/'.$webutlersave->getpage.'.tmp';
		$webutlersave->makebakfiles($content);
		
		file_put_contents($content, $posteddata);
		
		$webutlersave->setchmodaftersave($content);
		
		header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
	}
	else {
		$content = $webutlersave->config['server_path'].'/content/pages/'.$webutlersave->getpage;
		$webutlersave->makebakfiles($content);
		
		if($posteddata == '')
		{
			$posteddata = "\n";
		}
		else 
		{
			$contentfile = file_get_contents($content);
			$posteddata = trim($posteddata);
			$posteddata = $webutlersave->deletelastpfromsource($posteddata);

			$posteddata = preg_replace('#<link([^>]*?)admin/system/css/editor.css(.*?)>#si', '', $posteddata);
			$posteddata = preg_replace('#<link([^>]*?)content/columns/columns.css(.*?)>#si', '', $posteddata);
			
			$stylefiles = $webutlersave->stylefiles();
			
			foreach($stylefiles as $stylefile) {
				if(preg_match('#'.$stylefile['file'].'#i', $posteddata))
					$posteddata = preg_replace('#<link([^>]*?)'.$stylefile['file'].'(\?t=[0-9]+)(.*?)>#si', '<link${1}'.$stylefile['file'].'${3}>', $posteddata);
			}
				
			$iswriteable = $webutlersave->iswriteable('/content/menus');
			if($iswriteable != '')
			{
				echo $iswriteable;
				exit;
			}
			$menus = $webutlersave->config['server_path'].'/content/menus';
			$handle = opendir ($menus);
			while (false !== ($menufile = readdir ($handle)))
			{ 
				if( $menufile != '.' && $menufile != '..' )
				{
					$iswriteable = $webutlersave->iswriteable('/content/menus/'.$menufile);
					if($iswriteable != '')
					{
						echo $iswriteable;
						exit;
					}
					
					if(preg_match('#<!-- begin_menu_'.$menufile.' -->#Usi', $contentfile))
					{
						$menuframe = preg_replace('#(.*?)(<!-- begin_menu_'.$menufile.' -->)(.*?)(<!-- end_menu_'.$menufile.' -->)(.*?)#Usi', '$3', $posteddata);
						
						$menu = $menus.'/'.$menufile;
						$oldmenu = file_get_contents($menu);
						
						if($webutlersave->checkbeforebak($menuframe, $oldmenu) == '1') {
							unset($oldmenu);
							$webutlersave->makebakfiles($menu);
							
							file_put_contents($menu, $menuframe);
							
							$webutlersave->setchmodaftersave($menu);
						}
						else
							unset($oldmenu);
					}
					
					$posteddata = preg_replace('#(<!-- begin_menu_'.$menufile.' -->)(.*?)(<!-- end_menu_'.$menufile.' -->)#Usi', '$1$3', $posteddata);
					$posteddata = str_replace("<!-- begin_menu_".$menufile." -->", "<!-- begin_menu_".$menufile." -->\n<!-- menu_".$menufile." -->\n", $posteddata);
				}
			}
			closedir($handle);
				
			$iswriteable = $webutlersave->iswriteable('/content/blocks');
			if($iswriteable != '')
			{
				echo $iswriteable;
				exit;
			}
			$blocks = $webutlersave->config['server_path'].'/content/blocks';
			$handle = opendir ($blocks);
			while (false !== ($blockfile = readdir ($handle)))
			{ 
				if( $blockfile != '.' && $blockfile != '..' )
				{
					$iswriteable = $webutlersave->iswriteable('/content/blocks/'.$blockfile);
					if($iswriteable != '')
					{
						echo $iswriteable;
						exit;
					}
									
					if(preg_match('#<!-- begin_block_'.$blockfile.' -->#Usi', $contentfile))
					{
						$blockframe = preg_replace('#(.*?)(<!-- begin_block_'.$blockfile.' -->)(.*?)(<!-- end_block_'.$blockfile.' -->)(.*?)#Usi', '$3', $posteddata);
						
						$block = $blocks.'/'.$blockfile;
						$oldblock = file_get_contents($block);
						
						if($webutlersave->checkbeforebak($blockframe, $oldblock) == '1') {
							unset($oldblock);
							$webutlersave->makebakfiles($block);
							
							file_put_contents($block, $blockframe);
							
							$webutlersave->setchmodaftersave($block);
						}
						else
							unset($oldblock);
					}
					
					$posteddata = preg_replace('#(<!-- begin_block_'.$blockfile.' -->)(.*?)(<!-- end_block_'.$blockfile.' -->)#Usi', '$1$3', $posteddata);
					$posteddata = str_replace("<!-- begin_block_".$blockfile." -->", "<!-- begin_block_".$blockfile." -->\n<!-- block_".$blockfile." -->\n", $posteddata);
				}
			}
			closedir($handle);
		}
		
		file_put_contents($content, $posteddata);
		$webutlersave->setchmodaftersave($content);
		
		header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
	}
}

// Bearbeitung Menü
if(isset($_POST['menueditor'])) 
{	
    $iswriteable = $webutlersave->iswriteable('/content/menus');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    $getmenu = $webutlersave->filenamesigns($_GET['menu']);
    $iswriteable = $webutlersave->iswriteable('/content/menus/'.$getmenu);
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
	//$posteddata = stripslashes($_POST['menueditor']);
	$posteddata = $_POST['menueditor'];
	
	if($posteddata == '')
	{
		$posteddata = "\n";
	}
	else 
	{
		$posteddata = str_replace("\r", "", $posteddata);
		$posteddata = trim($posteddata);
		$posteddata = $webutlersave->deletelastpfromsource($posteddata);
	}
	
	$menu = $webutlersave->config['server_path'].'/content/menus/'.$getmenu;
	
	$oldmenu = file_get_contents($menu);
	
	if($webutlersave->checkbeforebak($posteddata, $oldmenu) == '1') {
		unset($oldmenu);
        $webutlersave->makebakfiles($menu);
        
        file_put_contents($menu, $posteddata);
    	
        $webutlersave->setchmodaftersave($menu);
	}
	else
		unset($oldmenu);
	
	header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
}

// Bearbeitung Block
if(isset($_POST['blockeditor'])) 
{	
    $iswriteable = $webutlersave->iswriteable('/content/blocks');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    $getblock = $webutlersave->filenamesigns($_GET['block']);
    $iswriteable = $webutlersave->iswriteable('/content/blocks/'.$getblock);
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
	//$posteddata = stripslashes($_POST['blockeditor']);
	$posteddata = $_POST['blockeditor'];
	
	if($posteddata == '')
	{
		$posteddata = "\n";
	}
	else 
	{
		$posteddata = str_replace("\r", "", $posteddata);
		$posteddata = trim($posteddata);
		$posteddata = $webutlersave->deletelastpfromsource($posteddata);
	}
	
	$block = $webutlersave->config['server_path']."/content/blocks/".$getblock;
	
	$oldblock = file_get_contents($block);
	
	if($webutlersave->checkbeforebak($posteddata, $oldblock) == '1') {
		unset($oldblock);
        $webutlersave->makebakfiles($block);
        
        file_put_contents($block, $posteddata);
    	
        $webutlersave->setchmodaftersave($block);
	}
	else
		unset($oldblock);
	
	header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
}

// Einstellungen
if(isset($_POST['saveuser']))
{
    $baseconfig = $webutlersave->config['server_path'].'/settings/baseconfig.php';
	
    $iswriteable = $webutlersave->iswriteable('/settings/baseconfig.php');
    if($iswriteable != '')
    {
        //echo $iswriteable;
        echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_OPENCONF_.'</span>';
        exit;
	}
    
	$username = isset($_POST['username']) ? strip_tags(trim($_POST['username'])) : '';
	$username = preg_replace('/[^a-zA-Z0-9._-]/', '', $username);
	$userpass1 = strip_tags($_POST['userpass1']);
	$userpass2 = strip_tags($_POST['userpass2']);
    $userlang = isset($_POST['userlang']) ? preg_replace('/[^a-z]/', '', strtolower($_POST['userlang'])) : '';
	$startseite = isset($_POST['startseite']) ? $webutlersave->filenamesigns($_POST['startseite']) : '';
	$imgsmallsizewidth = isset($_POST['imgsmallsizewidth']) ? "'".preg_replace('/[^0-9]/', '', $_POST['imgsmallsizewidth'])."'" : "''";
	$imgsmallsizeheight = isset($_POST['imgsmallsizeheight']) ? "'".preg_replace('/[^0-9]/', '', $_POST['imgsmallsizeheight'])."'" : "''";
	$imgboxsizewidth = isset($_POST['imgboxsizewidth']) ? "'".preg_replace('/[^0-9]/', '', $_POST['imgboxsizewidth'])."'" : "''";
	$imgboxsizeheight = isset($_POST['imgboxsizeheight']) ? "'".preg_replace('/[^0-9]/', '', $_POST['imgboxsizeheight'])."'" : "''";
	
	if($username == '')
	{
        echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_NOUSER_.'</span>';
        exit;
	}
	elseif($username != trim($_POST['username']))
	{
        echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_WRONGSIGNS_.'</span>';
        exit;
	}
	
    if($username != $webutlersave->config['user_name'] && !isset($_SESSION['loggedin']['userisadmin']))
	{
		$_SESSION['loggedin']['username'] = md5($username);
	}
	
	if($userpass1 == '' && $userpass2 == '')
	{
		$userpass = $webutlersave->config['user_pass'];
	}
	elseif($userpass1 == $userpass2)
	{
		if(!preg_match('#^[a-zA-Z0-9\#\+\-_\*\@\%\&\=\!\?]+$#', $userpass1))
		{
			echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_WRONGSIGNS_.'<br />'._WBLANGADMIN_POPUPWIN_SAVE_WRONGPASSSIGNS_.'</span>';
			exit;
		}
		else
		{
			$userpass = md5($webutlersave->config['salt_key1'].$userpass1.$webutlersave->config['salt_key2']);
			if(!isset($_SESSION['loggedin']['userisadmin']))
				$_SESSION['loggedin']['userpass'] = $userpass;
		}
	}
	else
	{
        echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_WRONGPASS_.'</span>';
        exit;
	}
    
	$buf = file_get_contents($baseconfig);
	$buf = preg_replace('#(\$webutler_config\[\'user_name\'\] = ")([^"]*)(";)#Usi', '${1}'.$username.'$3', $buf);
	$buf = preg_replace('#(\$webutler_config\[\'user_pass\'\] = ")([^"]*)(";)#Usi', '${1}'.$userpass.'$3', $buf);
	$buf = preg_replace('#(\$webutler_config\[\'user_lang\'\] = ")([^"]*)(";)#Usi', '${1}'.$userlang.'$3', $buf);
	$buf = preg_replace('#(\$webutler_config\[\'startseite\'\] = ")([^"]*)(";)#Usi', '${1}'.$startseite.'$3', $buf);
	$buf = preg_replace('#(\$webutler_config\[\'imgsmallsize\'\] = array\()([^\)]*)(\);)#Usi', '${1}'.$imgsmallsizewidth.', '.$imgsmallsizeheight.'${3}', $buf);
	$buf = preg_replace('#(\$webutler_config\[\'imgboxsize\'\] = array\()([^\)]*)(\);)#Usi', '${1}'.$imgboxsizewidth.', '.$imgboxsizeheight.'${3}', $buf);
    
    if(!isset($_SESSION['loggedin']['userisadmin']))
        $_SESSION['loggedin']['userlang'] = $userlang;
    
    file_put_contents($baseconfig, $buf);
	
    $webutlersave->setchmodaftersave($baseconfig);
	
    echo '<span class="green">'._WBLANGADMIN_POPUPWIN_SAVE_CONFSAVEOK_.'</span>';
}

// Fehlerseite
if(isset($_POST['savenewerror']))
{
	$errorfile = $webutlersave->filenamesigns($_POST['errorfile']);
	
	if($errorfile != '' && file_exists($webutlersave->config['server_path'].'/content/pages/'.$errorfile))
	{
        $webutlersave->renamefileinmenus($errorfile);
        $webutlersave->changenameinconfigfile($webutlersave->config['ownerrorpage'], $errorfile);
        $webutlersave->changenameinofflinefile($errorfile);
        $webutlersave->changenameinlanguagefile($errorfile);
        $webutlersave->changenameincategoriesfile($errorfile);
		
        echo '<span class="green">'._WBLANGADMIN_POPUPWIN_SAVE_CONFSAVEOK_.'</span>';
        exit;
	}
}

// Bearbeitung Spalten-CSS
if(isset($_POST['columnstyle']))
{
    $iswriteable = $webutlersave->iswriteable('/content/columns/source/csscolumns.php');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
	
    $iswriteable = $webutlersave->iswriteable('/content/columns/columns.css');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
	$columnscss = $webutlersave->config['server_path'].'/content/columns/source/csscolumns.php';
	$newcode = $_POST['codemirror_editorsource'];
	file_put_contents($columnscss, $newcode);
    $webutlersave->setchmodaftersave($columnscss);
	
	ob_start();
	header("Content-Type: text/css");
	include $columnscss;
	$csssource = ob_get_contents();
	ob_end_clean();
    
	$columns = $webutlersave->config['server_path'].'/content/columns/columns.css';
	//file_put_contents($columns, $csssource);
	$minsource = $webutlersave->minifycsssource($csssource);
	file_put_contents($columns, $minsource);
    $webutlersave->setchmodaftersave($columns);
	
	header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
}

// Bearbeitung Layout-Datei
if(isset($_POST['layoutfile']))
{
    $filename = $webutlersave->checkfilenamesigns($_GET['file'], 'tpl');
    $iswriteable = $webutlersave->iswriteable('/content/layouts/'.$filename);
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
	$layoutfile = $webutlersave->config['server_path'].'/content/layouts/'.$filename;
	$newcode = $_POST['codemirror_editorsource'];
	
	file_put_contents($layoutfile, $newcode);
	
    $webutlersave->setchmodaftersave($layoutfile);
	
	header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
}

// Bearbeitung CSS-Datei
if(isset($_POST['stylefile']))
{
    $ext = substr($_GET['file'], strrpos($_GET['file'], '.') + 1);
    if($ext == 'css') {
        $filename = $webutlersave->checkfilenamesigns($_GET['file'], $ext);
		
        $iswriteable = $webutlersave->iswriteable('/content/style/source/'.$filename);
        if($iswriteable != '')
        {
            echo $iswriteable;
            exit;
        }
		
		$iswriteable = $webutlersave->iswriteable('/content/style/'.$filename);
		if($iswriteable != '')
		{
			echo $iswriteable;
			exit;
		}
        
    	$newcode = $_POST['codemirror_editorsource'];
    	
    	$sourcefile = $webutlersave->config['server_path'].'/content/style/source/'.$filename;
    	$stylefile = $webutlersave->config['server_path'].'/content/style/'.$filename;
		
    	file_put_contents($sourcefile, $newcode);
        $webutlersave->setchmodaftersave($sourcefile);
		
		$minsource = $webutlersave->minifycsssource($newcode);
		file_put_contents($stylefile, $minsource);
        $webutlersave->setchmodaftersave($stylefile);
    	
    	header("Location: ".$webutlersave->getsavelocationurl($webutlersave->getpage));
    }
}


// neue Vorlage
if(isset($_POST['savenewpattern']))
{
	$patterninfos = $webutlersave->config['server_path'].'/content/pattern/patterninfos.php';
	
    $iswriteable = $webutlersave->iswriteable('/content/pattern/files', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
	$patternfile = $webutlersave->filenamesigns($_POST['patternfile']);
    $duplicatepattern = $webutlersave->checkfilenamesigns($_POST['duplicatepattern'], 'tpl');
	
    if($patternfile != '' && file_exists($patterninfos)) 
    {
	    $iswriteable = $webutlersave->iswriteable('/content/pattern/patterninfos.php', 'ajax');
        if($iswriteable != '')
        {
            echo $iswriteable;
            exit;
        }
		
		$newpatternfile = $webutlersave->config['server_path'].'/content/pattern/files/'.$patternfile.'.tpl';
		if(file_exists($newpatternfile))
		{
			echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_NAMEEXISTS_.'</span>';
			exit;
		}
		else
		{
			if($duplicatepattern != '')
			{
				copy($webutlersave->config['server_path'].'/content/pattern/files/'.$duplicatepattern, $newpatternfile);
			}
			else
			{
				file_put_contents($newpatternfile, '');
			}
			$webutlersave->setchmodaftersave($newpatternfile);
			
			require_once $patterninfos;
			
			$oldarray = array();
			foreach($infos as $info)
			{
				$oldinfo = "\t\tarray(\n";
				$oldvars = array();
				foreach($info as $k => $i)
				{
					$oldvars[] = "\t\t\t'".$k."' => '".$i."'";
				}
				$oldinfo.= implode(",\n", $oldvars)."\n";
				$oldinfo.= "\t\t);";
				
				$oldarray[] = $oldinfo;
			}
			
			$newinfos = "<"."?PHP\n";
			$newinfos.= "\t\$infos = array(\n";
			$newinfos.= implode(",\n", $oldarray).",\n";
			$newinfos.= "\t\tarray(\n";
			$newinfos.= "\t\t\t'file' => '".$patternfile.".tpl',\n";
			$newinfos.= "\t\t\t'title' => '',\n";
			$newinfos.= "\t\t\t'image' => '',\n";
			$newinfos.= "\t\t\t'description' => ''\n";
			$newinfos.= "\t\t)\n";
			$newinfos.= "\t);\n";
			
			file_put_contents($patterninfos, $newinfos);
			$webutlersave->setchmodaftersave($patterninfos);
			
			echo '<span class="green">'._WBLANGADMIN_POPUPWIN_PATTERN_ISSAVED_.'</span>';
			exit;
		}
	}
	else
	{
        echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_NOLOGIN_.'</span>';
        exit;
	}
}

// Vorlage bearbeiten
if(isset($_POST['savepattern']))
{
	$patterninfos = $webutlersave->config['server_path'].'/content/pattern/patterninfos.php';
	
    $iswriteable = $webutlersave->iswriteable('/content/pattern/files', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
	
	$iswriteable = $webutlersave->iswriteable('/content/pattern/patterninfos.php', 'ajax');
	if($iswriteable != '')
	{
		echo $iswriteable;
		exit;
	}
    
    $patternname = $webutlersave->checkfilenamesigns($_POST['patternfile'], 'tpl');
    $patterntitle = htmlspecialchars($_POST['title']);
	$ext = substr($_POST['image'], strrpos($_POST['image'], '.') + 1);
	$patternimage = $webutlersave->checkfilenamesigns($_POST['image'], $ext);
    $patterndesc = htmlspecialchars($_POST['description']);
    $patternsource = $_POST['source'];
	
	$patternfile = $webutlersave->config['server_path'].'/content/pattern/files/'.$patternname;
	
    if($patternname != '' && file_exists($patternfile) && file_exists($patterninfos)) 
    {
		file_put_contents($patternfile, $patternsource);
		$webutlersave->setchmodaftersave($patternfile);
		
		require_once $patterninfos;
		
		$array = array();
		foreach($infos as $info)
		{
			$oldinfo = "\t\tarray(\n";
			$oldvars = array();
			if($info['file'] == $patternname)
			{
				$oldvars[] = "\t\t\t'file' => '".$patternname."'";
				$oldvars[] = "\t\t\t'title' => '".$patterntitle."'";
				$oldvars[] = "\t\t\t'image' => '".$patternimage."'";
				$oldvars[] = "\t\t\t'description' => '".$patterndesc."'";
			}
			else
			{
				foreach($info as $k => $i)
				{
					$oldvars[] = "\t\t\t'".$k."' => '".$i."'";
				}
			}
			$oldinfo.= implode(",\n", $oldvars)."\n";
			$oldinfo.= "\t\t)";
			
			$array[] = $oldinfo;
		}
		
		$newinfos = "<"."?PHP\n";
		$newinfos.= "\t\$infos = array(\n";
		$newinfos.= implode(",\n", $array)."\n";
		$newinfos.= "\t);\n";
		
		file_put_contents($patterninfos, $newinfos);
		$webutlersave->setchmodaftersave($patterninfos);
		
		header('Location: '.$webutlersave->getsavelocationurl($webutlersave->getpage));
	}
}

// Vorlage löschen
if(isset($_POST['deletepattern']))
{
	$patterninfos = $webutlersave->config['server_path'].'/content/pattern/patterninfos.php';
	
	$iswriteable = $webutlersave->iswriteable('/content/pattern/patterninfos.php', 'ajax');
	if($iswriteable != '')
	{
		echo $iswriteable;
		exit;
	}
	
    $delpattern = $webutlersave->checkfilenamesigns($_POST['delpattern'], 'tpl');
	$delpatternfile = $webutlersave->config['server_path']."/content/pattern/files/".$delpattern;	
    if(file_exists($delpatternfile)) 
    {
	    $iswriteable = $webutlersave->iswriteable('/content/pattern/files/'.$delpattern, 'ajax');
        if($iswriteable != '')
        {
            echo $iswriteable;
            exit;
        }
        
        unlink($delpatternfile);
		
		require_once $patterninfos;
		
		$array = array();
		foreach($infos as $info)
		{
			if($info['file'] != $delpattern)
			{
				$oldinfo = "\t\tarray(\n";
				$oldvars = array();
				foreach($info as $k => $i)
				{
					$oldvars[] = "\t\t\t'".$k."' => '".$i."'";
				}
				$oldinfo.= implode(",\n", $oldvars)."\n";
				$oldinfo.= "\t\t)";
				
				$array[] = $oldinfo;
			}
		}
		
		$newinfos = "<"."?PHP\n";
		$newinfos.= "\t\$infos = array(\n";
		$newinfos.= implode(",\n", $array)."\n";
		$newinfos.= "\t);\n";
		
		file_put_contents($patterninfos, $newinfos);
		$webutlersave->setchmodaftersave($patterninfos);
		
        echo '<span class="green">'._WBLANGADMIN_POPUPWIN_PATTERN_ISDELETED_.'</span>';
    }
}

// neues Template
if(isset($_POST['savenewtpl']))
{
    $iswriteable = $webutlersave->iswriteable('/content/layouts', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
	$tplname = $webutlersave->filenamesigns($_POST['tplname']);
    $duplicatetpl = $webutlersave->checkfilenamesigns($_POST['duplicatetpl'], 'tpl');
	
	if($tplname != '')
	{
	    $newtplfile = $webutlersave->config['server_path'].'/content/layouts/'.$tplname.'.tpl';
		if(file_exists($newtplfile))
		{
            echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_NAMEEXISTS_.'</span>';
            exit;
		}
		elseif($duplicatetpl != '')
		{
			copy($webutlersave->config['server_path'].'/content/layouts/'.$duplicatetpl, $newtplfile);
			
            $webutlersave->setchmodaftersave($newtplfile);
			
            echo '<span class="green">'._WBLANGADMIN_POPUPWIN_LAYOUT_ISSAVED_.'</span>';
            exit;
		}
		else
		{
			copy($webutlersave->config['server_path'].'/admin/system/dummy.tpl', $newtplfile);
			
            $webutlersave->setchmodaftersave($newtplfile);
			
            echo '<span class="green">'._WBLANGADMIN_POPUPWIN_LAYOUT_ISSAVED_.'</span>';
            exit;
		}
	}
	else
	{
        echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_NOLOGIN_.'</span>';
        exit;
	}
}

// Template löschen
if(isset($_POST['delnewtpl']))
{
    $tplname = $webutlersave->checkfilenamesigns($_POST['deltpl'], 'tpl');
	$deltpl = $webutlersave->config['server_path']."/content/layouts/".$tplname;	
    if(file_exists($deltpl)) 
    {
	    $iswriteable = $webutlersave->iswriteable('/content/layouts/'.$tplname, 'ajax');
        if($iswriteable != '')
        {
            echo $iswriteable;
            exit;
        }
        
        unlink($deltpl);
        echo '<span class="green">'._WBLANGADMIN_POPUPWIN_LAYOUT_ISDELETED_.'</span>';
    }
}

// neues Menü
if(isset($_POST['savenewmenu']))
{
    $iswriteable = $webutlersave->iswriteable('/content/menus', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
	
	$menuname = $webutlersave->filenamesigns($_POST['menuname']);
	$duplicatemenu = $webutlersave->filenamesigns($_POST['duplicatemenu']);
	
	$menufile = $webutlersave->config['server_path'].'/content/menus/'.$menuname;
	
	if($menuname != '')
    {
	    if(file_exists($menufile))
        {
            echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_MENUEXISTS_.'</span>';
            exit;
		}
		elseif($duplicatemenu != '')
		{
			copy($webutlersave->config['server_path'].'/content/menus/'.$duplicatemenu, $menufile);
			
            $webutlersave->setchmodaftersave($menufile);
		
            echo '<span class="green">'._WBLANGADMIN_POPUPWIN_MENU_ISSAVED_.'</span>';
		}
		else
		{
			$menu = "<ul>\n	<li>\n		<a href=\"index.php\">"._WBLANGADMIN_POPUPWIN_SAVE_DEFAULTLINK_."</a>\n	</li>\n</ul>\n";
            
        	file_put_contents($menufile, $menu);
			
            $webutlersave->setchmodaftersave($menufile);
		
            echo '<span class="green">'._WBLANGADMIN_POPUPWIN_MENU_ISSAVED_.'</span>';
		}
	}
	else
    {
        echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_MENUNAME_.'</span>';
        exit;
	}
}

// Menü löschen
if(isset($_POST['delnewmenu']))
{
    $delmenufile = $webutlersave->filenamesigns($_POST['delmenu']);
	$delmenu = $webutlersave->config['server_path'].'/content/menus/'.$delmenufile;	
    if(file_exists($delmenu))
    {
	    $iswriteable = $webutlersave->iswriteable('/content/menus/'.$delmenufile, 'ajax');
        if($iswriteable != '')
        {
            echo $iswriteable;
            exit;
        }
        
        unlink ($delmenu);
		$bakext = '';
		$i = 0;
		while ($i < $webutlersave->config['schritte_zurueck'])
		{
		    $bakext.= '.bak';
			if(file_exists($delmenu.$bakext))
		    {
			    $iswriteable = $webutlersave->iswriteable('/content/menus/'.$delmenufile.$bakext);
		        if($iswriteable != '')
		        {
		            echo $iswriteable;
		            exit;
		        }
		        unlink($delmenu.$bakext);
		    }
			else break;
		    $i++;
		}
        
        echo '<span class="green">'._WBLANGADMIN_POPUPWIN_MENU_ISDELETED_.'</span>';
    }
}

// Menü rückgängig
if(isset($_POST['lastmenu']))
{
    $menu = $webutlersave->filenamesigns($_POST['menu']);
    $menufile = $webutlersave->config['server_path'].'/content/menus/'.$menu;
	if(file_exists($menufile))
	{
	    $iswriteable = $webutlersave->iswriteable('/content/menus/'.$menu, 'ajax');
        if($iswriteable != '')
        {
            echo $iswriteable;
            exit;
        }
        else
        {
        	if(file_exists($menufile.'.bak')) 
        	{
                unlink($menufile);
                rename($menufile.'.bak', $menufile);
            }
        	$steps = $webutlersave->config['schritte_zurueck']-1;
            $bakext = '';
            $i = 0;
        	while($i <= $steps)
        	{
                $bakext.= '.bak';
            	if(file_exists($menufile.$bakext.'.bak')) 
            	{
                    rename($menufile.$bakext.'.bak', $menufile.$bakext);
            	}
            	else
            	{
                    break;
            	}
                $i++;
        	}
            
            echo '<span class="green">'._WBLANGADMIN_WIN_EDITMENU_REMENU_.'</span>';
        }
	}
    else
	{
        echo '<span class="red">'._WBLANGADMIN_WIN_EDITMENU_NOMENU_.'</span>';
    }
}

// neuer Block
if(isset($_POST['savenewblock']))
{
    $iswriteable = $webutlersave->iswriteable('/content/blocks', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
	
	$blockname = $webutlersave->filenamesigns($_POST['blockname']);
	$duplicateblock = $webutlersave->filenamesigns($_POST['duplicateblock']);
	
	$blockfile = $webutlersave->config['server_path'].'/content/blocks/'.$blockname;
	
	if($blockname != '')
	{
	    if(file_exists($blockfile))
		{
            echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_BLOCKEXISTS_.'</span>';
            exit;
		}
		elseif($duplicateblock != '')
		{
			copy($webutlersave->config['server_path']."/content/blocks/".$duplicateblock, $blockfile);
			
            $webutlersave->setchmodaftersave($blockfile);

            echo '<span class="green">'._WBLANGADMIN_POPUPWIN_BLOCK_ISSAVED_.'</span>';
		}
		else
		{
			$block = "<div>\n"._WBLANGADMIN_POPUPWIN_SAVE_DEFAULTBLOCK_."\n</div>\n";
            
        	file_put_contents($blockfile, $block);
			
            $webutlersave->setchmodaftersave($blockfile);

            echo '<span class="green">'._WBLANGADMIN_POPUPWIN_BLOCK_ISSAVED_.'</span>';
		}
	}
	else
	{
        echo '<span class="red">'._WBLANGADMIN_POPUPWIN_SAVE_BLOCKNAME_.'</span>';
        exit;
	}
}

// Block löschen
if(isset($_POST['delnewblock']))
{
    $delblock = $webutlersave->filenamesigns($_POST['delblock']);
	$delblockfile = $webutlersave->config['server_path'].'/content/blocks/'.$delblock;	
    if(file_exists($delblockfile)) 
    {
	    $iswriteable = $webutlersave->iswriteable('/content/blocks/'.$delblock, 'ajax');
        if($iswriteable != '')
        {
            echo $iswriteable;
            exit;
        }
        
        unlink($delblockfile);
		$bakext = '';
		$i = 0;
		while($i < $webutlersave->config['schritte_zurueck'])
		{
		    $bakext.= '.bak';
			if(file_exists($delblockfile.$bakext))
		    {
			    $iswriteable = $webutlersave->iswriteable('/content/blocks/'.$delblock.$bakext, 'ajax');
		        if($iswriteable != '')
		        {
		            echo $iswriteable;
		            exit;
		        }
		        unlink($delblockfile.$bakext);
		    }
			else break;
		    $i++;
		}
        
        echo '<span class="green">'._WBLANGADMIN_POPUPWIN_BLOCK_ISDELETED_.'</span>';
    }
}

// Block rückgängig
if(isset($_POST['lastblock']))
{
    $block = $webutlersave->filenamesigns($_POST['block']);
    $blockfile = $webutlersave->config['server_path'].'/content/blocks/'.$_POST['block'];
	if(file_exists($blockfile))
	{
	    $iswriteable = $webutlersave->iswriteable('/content/blocks/'.$block, 'ajax');
        if($iswriteable != '')
        {
            echo $iswriteable;
            exit;
        }
        else
        {
        	if(file_exists($blockfile.'.bak')) 
        	{
                unlink($blockfile);
                rename($blockfile.'.bak', $blockfile);
            }
        	$steps = $webutlersave->config['schritte_zurueck']-1;
            $bakext = '';
            $i = 0;
        	while($i <= $steps)
        	{
                $bakext.= '.bak';
            	if(file_exists($blockfile.$bakext.'.bak')) 
            	{
                    rename($blockfile.$bakext.'.bak', $blockfile.$bakext);
            	}
            	else
            	{
                    break;
            	}
                $i++;
        	}
            
            echo '<span class="green">'._WBLANGADMIN_WIN_EDITBLOCK_REBLOCK_.'</span>';
        }
	}
    else
	{
        echo '<span class="red">'._WBLANGADMIN_WIN_EDITBLOCK_NOBLOCK_.'</span>';
    }
}

// Spalten einfügen
if(isset($_POST['insertcolumnsatposition']) && $_POST['insertcolumnsatposition'] == 1)
{
    $getpage = $webutlersave->filenamesigns($_POST['getpage']);
	
    $iswriteable = $webutlersave->iswriteable('/content/pages');
    if($iswriteable != '')
    {
        echo 'error###content/pages '._WBLANGADMIN_POPUPWIN_COLUMNS_NOTWRITEABLE_;
        exit;
    }
	
	$pagefile = $webutlersave->config['server_path'].'/content/pages/'.$getpage;
    
	$file = '';
	$savefile = '';
	if(file_exists($pagefile.'.tmp'))
	{
		$file = $pagefile.'.tmp';
		$savefile = $file;
		$webutlersave->makebakfiles($file);
	}
	else {
		$file = $pagefile;
		$savefile = $file.'.tmp';
	}
	
	$columnsconfig = json_decode(rawurldecode($_POST['columnsconfig']), true);
	$columnscss = isset($_POST['columnscss']) ? json_decode(rawurldecode($_POST['columnscss']), true) : '';
	$columneditors = json_decode(rawurldecode($_POST['columneditors']), true);
    $rowclass = is_array($columnscss) && array_key_exists('row', $columnscss) ? preg_replace('/[^\pL\pN\_\- ]/', '', $columnscss['row']) : '';
    $colsclass = is_array($columnscss) && array_key_exists('cols', $columnscss) ? preg_replace('/[^\pL\pN\_\- ]/', '', $columnscss['cols']) : '';
    $divindex = preg_replace('/[^0-9]/', '', $_POST['divindex']);
    $margin = preg_replace('/[^0-9]/', '', $_POST['margin']);
    $position = $_POST['position'];
	
	if(($position == 'before' || $position == 'after') && $file != '' && $savefile != '' && $divindex != '')
	{
		$content = file_get_contents($file);
		$columns = $webutlersave->createcolumnstemplate($columnsconfig, $rowclass, $columneditors, $colsclass);
		if($margin != '')
			$columns = $webutlersave->setmargintoptocolumns($columns, $margin);
		
		$html = preg_split('#<\/?body[^>]*?'.'>#i', $content);
		$newcontent = $webutlersave->savecolumnstopage($content, $columns, $position, $divindex);
		
		file_put_contents($savefile, $html[0].$newcontent.$html[2]);
		$webutlersave->setchmodaftersave($savefile);
		
		echo 'ok###'._WBLANGADMIN_POPUPWIN_COLUMNS_INSERTED_;
	}
	else
	{
		echo 'error###'._WBLANGADMIN_POPUPWIN_COLUMNS_NOTINSERTED_;
	}
}

// Spalten löschen
if(isset($_POST['deletecolumnsfrompage']) && $_POST['deletecolumnsfrompage'] == 1)
{
    $getpage = $webutlersave->filenamesigns($_POST['getpage']);
	
    $iswriteable = $webutlersave->iswriteable('/content/pages');
    if($iswriteable != '')
    {
        echo 'error###content/pages '._WBLANGADMIN_POPUPWIN_COLUMNS_NOTWRITEABLE_;
        exit;
    }
	
	$pagefile = $webutlersave->config['server_path'].'/content/pages/'.$getpage;
    
	if(file_exists($pagefile.'.tmp'))
	{
		$file = $pagefile.'.tmp';
		$savefile = $file;
		$webutlersave->makebakfiles($file);
	}
	else {
		$file = $pagefile;
		$savefile = $file.'.tmp';
	}
	
    $divindex = preg_replace('/[^0-9]/', '', $_POST['divindex']);
	if($divindex != '')
	{
		$content = file_get_contents($file);
		$html = preg_split('#<\/?body[^>]*?'.'>#i', $content);
		$newcontent = $webutlersave->deletecolumnsfrompage($content, $divindex);
		
		file_put_contents($savefile, $html[0].$newcontent.$html[2]);
		$webutlersave->setchmodaftersave($savefile);
		
		echo 'ok###'._WBLANGADMIN_POPUPWIN_COLUMNS_DELETED_;
	}
	else
	{
		echo 'error###'._WBLANGADMIN_POPUPWIN_COLUMNS_NOTDELETED_;
	}
}

// Files im /access-Verzeichnis
if(isset($_POST['checkfileexists']))
{
    $iswriteable = $webutlersave->iswriteable('/content/access', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
    if($_POST['checkfileexists'] == 'categories')
	{
        $file = $webutlersave->config['server_path'].'/content/access/categories.php';
        
    	$config = "<?PHP\n\n";
    	$config.= "\$webutler_categories['cats'] = array();\n";
    	$config.= "\$webutler_categories['pages'] = array();\n\n";
    	//$config.= "? >\n";
    }
    
    if($_POST['checkfileexists'] == 'forms')
	{
        $file = $webutlersave->config['server_path'].'/content/access/mailaddresses.php';
        
    	$config = "<?PHP\n\n";
    	$config.= "\$webutler_mailadresses = array();\n\n";
    	//config.= "? >\n";
    }
    
    if($_POST['checkfileexists'] == 'langs')
	{
        $file = $webutlersave->config['server_path'].'/content/access/languages.php';
        
		$config = "<?PHP\n\n";
		$config.= "\$webutler_langconf['code'] = array();\n";
		$config.= "\$webutler_langconf['lang'] = array();\n";
		$config.= "\$webutler_langconf['homes'] = array();\n\n";
		//$config.= "? >\n";
    }
    
    if($_POST['checkfileexists'] == 'offline')
	{
        $file = $webutlersave->config['server_path'].'/content/access/offline.php';
        
		$config = "<?PHP\n\n";
		$config.= "\$webutler_offlinepages = array();\n\n";
		//$config.= "? >\n";
    }
    
    if($_POST['checkfileexists'] == 'linkhighlite')
	{
        $file = $webutlersave->config['server_path'].'/content/access/linkhighlite.php';
        
		$config = "<?PHP\n\n";
		$config.= "\$webutler_linkhighlite['files'] = array();\n\n";
		$config.= "\$webutler_linkhighlite['folders'] = array();\n\n";
		//$config.= "? >\n";
    }
	
    if(!file_exists($file))
	{
        file_put_contents($file, $config);
		
        $webutlersave->setchmodaftersave($file);
	}
}

// Kategorien
if(isset($_POST['savepagecategory']))
{
    $iswriteable = $webutlersave->iswriteable('/content/access/categories.php', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
    $locationpage = $webutlersave->filenamesigns($_POST['locationpage']);
    $newcategory = $webutlersave->checknewcatname($_POST['newcategory']);
    $oldcategory = $webutlersave->checknewcatname($_POST['oldcategory']);
	
	$urllang = ($webutlersave->config['languages'] == '1' && $webutlersave->config['langfolder'] == '1') ? '1' : '0';
	
	echo $webutlersave->savepagetocat($locationpage, $newcategory, $oldcategory, true).'###'._WBLANGADMIN_POPUPWIN_ROUTE_RELOAD_.'###'.$webutlersave->getsavelocationurl($locationpage, ($urllang == '1' ? true : false));
}

if(isset($_POST['savenewcategories']) || isset($_POST['savedelcategories']))
{
    $iswriteable = $webutlersave->iswriteable('/content/access/categories.php', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
    $categoriesfile =  $webutlersave->config['server_path'].'/content/access/categories.php';
    
	$config = "<?PHP\n\n";
    
    if(isset($_POST['savenewcategories']) && isset($_POST['newcat']))
    {
        if(is_array($_POST['newcat']))
        {
        	foreach($webutlersave->langconf['code'] as $langkey)
            {
                $newcat = $webutlersave->checknewcatname($_POST['newcat'][$langkey]);
                
                if(!array_key_exists($langkey, $webutlersave->categories['cats']))
                    $webutlersave->categories['cats'][$langkey] = array();
                
                if($newcat != '' && !in_array($newcat, $webutlersave->categories['cats'][$langkey]))
                	$webutlersave->categories['cats'][$langkey][] = $newcat;
                
                $values = (count($webutlersave->categories['cats'][$langkey]) >= 1) ? "'".implode("','", $webutlersave->categories['cats'][$langkey])."'" : '';
        		$config.= "\$webutler_categories['cats']['".$langkey."'] = array(".$values.");\n";
        	}
        }
        else
        {
            reset($webutlersave->categories['cats']);
            $checkforlangkey = key($webutlersave->categories['cats']);
            
            if($checkforlangkey != '' && is_array($webutlersave->categories['cats'][$checkforlangkey]))
            	$webutlersave->categories['cats'] = $webutlersave->categories['cats'][$checkforlangkey];
            
            $newcat = $webutlersave->checknewcatname($_POST['newcat']);
            
            if($newcat != '' && !in_array($newcat, $webutlersave->categories['cats']))
            	$webutlersave->categories['cats'][] = $newcat;
            
            $values = (count($webutlersave->categories['cats']) >= 1) ? "'".implode("','", $webutlersave->categories['cats'])."'" : '';
    		$config.= "\$webutler_categories['cats'] = array(".$values.");\n";
        }
        
        $config.= "\n\$webutler_categories['pages'] = array(";
        if(count($webutlersave->categories['pages']) >= 1)
        {
            $values = array();
            foreach($webutlersave->categories['pages'] as $cat => $pages)
            {
            	$values[] = "  '".$cat."' => array('".implode("','", $pages)."')";
            }
            $config.= "\n".implode(",\n", $values)."\n";
        }
        $config.= ");\n\n";
    }
    
    if(isset($_POST['savedelcategories']) && isset($_POST['delcat']))
    {
        $delcat = '';
        if(is_array($_POST['delcat']))
        {
            foreach($webutlersave->categories['cats'] as $langkey => $categories)
            {
            	$config.= "\$webutler_categories['cats']['".$langkey."'] = array(";
                
                $delcat = $_POST['delcat'][$langkey];
                if($delcat != '')
                {
                    $values = array();
                	foreach($categories as $category)
                    {
                    	if($category != $delcat)
                    		$values[] = $category;
                	}
                }
                else
                {
                    $values = $categories;
                }
                
            	if(count($values) >= 1)
            		$config.= "'".implode("','", $values)."'";
                
            	$config.= ");\n";
            }
        }
        else
        {
    		$config.= "\$webutler_categories['cats'] = array(";
            
            $delcat = $_POST['delcat'];
            if($delcat != '')
            {
                $values = array();
            	foreach($webutlersave->categories['cats'] as $category)
                {
                	if($category != $delcat)
                		$values[] = $category;
            	}
            }
            else
            {
                $values = $webutlersave->categories['cats'];
            }
            
        	if(count($values) >= 1)
        		$config.= "'".implode("','", $values)."'";
            
    		$config.= ");\n";
        }
        
        $config.= "\n\$webutler_categories['pages'] = array(";
        
        if(count($webutlersave->categories['pages']) >= 1)
        {
            $values = array();
            foreach($webutlersave->categories['pages'] as $cat => $pages)
            {
                if($delcat == '' || ($delcat != '' && $cat != $delcat))
                	$values[] = "  '".$cat."' => array('".implode("','", $pages)."')";
            }
            
            $config.= "\n".implode(",\n", $values)."\n";
        }
        
        $config.= ");\n\n";
    }
	
    //$config.= "? >\n";
    
	file_put_contents($categoriesfile, $config);
	
    $webutlersave->setchmodaftersave($categoriesfile);
    
    echo '<span class="green">'._WBLANGADMIN_WIN_CATEGORIES_SAVED_.'</span>';
}

// Link Highlite
if(isset($_POST['savehighlitefile']))
{
    $class = preg_replace('/[^a-z0-9-_]/', '', $_POST['highlitefileclass']);
    $menu = $webutlersave->filenamesigns($_POST['highlitefilemenu']);
    $parents = isset($_POST['highlitefileparents']) && $_POST['highlitefileparents'] == 'on' ? 'yes' : 'no';
    if($class == '' || $menu == '')
    {
        echo '<span class="red">'._WBLANGADMIN_WIN_HIGHLITES_FIELD_EMPTY_.'</span>';
    }
    else {
        $save = true;
        foreach($webutlersave->linkhighlite['files'] as $key => $array)
        {
            if($array[1] == $menu)
            {
                $save = false;
                break;
            }
        }
        if(!$save)
        {
            echo '<span class="red">'._WBLANGADMIN_WIN_HIGHLITES_FILE_NOTSAVEED_.'</span>';
        }
        else
        {
            $webutlersave->linkhighlite['files'][] = array($class, $menu, $parents);
            $webutlersave->savehighlitelinks();
            echo '<span class="green">'._WBLANGADMIN_WIN_HIGHLITES_FILE_ISSAVEED_.'</span>';
        }
    }
}

if(isset($_POST['delhighlitefile']))
{
    $key = preg_replace('/[^0-9]/', '', $_POST['delhighlitefile']);
    if($key != '')
    {
        unset($webutlersave->linkhighlite['files'][$key]);
        $webutlersave->savehighlitelinks();
        echo '<span class="green">'._WBLANGADMIN_WIN_HIGHLITES_FILE_ISDELETED_.'</span>';
    }
}

if(isset($_POST['savehighlitefolder']))
{
    $class = preg_replace('/[^a-z0-9-_]/', '', $_POST['highlitefolderclass']);
    $cat = $webutlersave->checknewcatname($_POST['highlitefoldercat']);
    $menu = $webutlersave->filenamesigns($_POST['highlitefoldermenu']);
    $current = isset($_POST['highlitefoldercurrent']) && $_POST['highlitefoldercurrent'] == 'on' ? 'yes' : 'no';
    if($class == '' || $cat == '' || $menu == '')
    {
        echo '<span class="red">'._WBLANGADMIN_WIN_HIGHLITES_FIELD_EMPTY_.'</span>';
    }
    else
    {
        $save = true;
        foreach($webutlersave->linkhighlite['folders'] as $key => $array)
        {
            if($array[1] == $cat && $array[2] == $menu)
            {
                $save = false;
                break;
            }
        }
        if(!$save)
        {
            echo '<span class="red">'._WBLANGADMIN_WIN_HIGHLITES_FOLDER_NOTSAVEED_.'</span>';
        }
        else
        {
            $webutlersave->linkhighlite['folders'][] = array($class, $cat, $menu, $current);
            $webutlersave->savehighlitelinks();
            echo '<span class="green">'._WBLANGADMIN_WIN_HIGHLITES_FOLDER_ISSAVEED_.'</span>';
        }
    }
}

if(isset($_POST['delhighlitefolder']))
{
    $key = preg_replace('/[^0-9]/', '', $_POST['delhighlitefolder']);
    if($key != '')
    {
        unset($webutlersave->linkhighlite['folders'][$key]);
        $webutlersave->savehighlitelinks();
        echo '<span class="green">'._WBLANGADMIN_WIN_HIGHLITES_FOLDER_ISDELETED_.'</span>';
    }
}

// Formulare
if(isset($_POST['formdelsubmit']))
{
    if(array_key_exists($_POST['delete'], $webutlersave->mailaddresses))
    {
        unset($webutlersave->mailaddresses[$_POST['delete']]);
        $webutlersave->saveaddresses();
        echo '<span class="green">'._WBLANGADMIN_WIN_FORMS_ISDELETED_.'</span>';
    }
}

if(isset($_POST['savenewform']) || isset($_POST['saveeditform']))
{
    $iswriteable = $webutlersave->iswriteable('/content/access/mailaddresses.php', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
    $addressfile = $webutlersave->config['server_path'].'/content/access/mailaddresses.php';
    
    $langs = '';
    if($webutlersave->config['languages'] == '1' && array_key_exists('code', $webutlersave->langconf) && count($webutlersave->langconf['code']) > 0)
	{
        $langs = $webutlersave->langconf['code'];
    }
    
	$sendto = '';
    if(isset($_POST['sendto']))
	{
        $sendnum = substr($_POST['sendto'], 6);
		$sendnum = preg_replace('/[^0-9]/', '', $sendnum);
		$sendto = $sendnum != '' ? 'sendto'.$sendnum : '';
    }
	
    if($sendto == '')
    {
        if(count($webutlersave->mailaddresses) > 0 && array_key_exists('sendto1', $webutlersave->mailaddresses))
        {
            $i = 1;
        	while($i++)
            {
            	if(!array_key_exists('sendto'.$i , $webutlersave->mailaddresses))
                {
                    $sendto = 'sendto'.$i;
                    break;
            	}
        	}
        }
        else
        {
            $sendto = 'sendto1';
        }
    }
	
	$canbesaved = 'true';
	if($langs != '' && isset($_POST['bestaetigung']))
	{
		foreach($langs as $lang)
		{
			if($_POST['bestaetigungsbetreff'][$lang] == '') {
				$canbesaved = 'false';
				break;
			}
		}
	}
	
	if($_POST['empfaengermail'] != '')
	{
		$post_empfaengermail = $webutlersave->validatemail($_POST['empfaengermail']);
		if($post_empfaengermail == 'false') {
			echo '<span class="red">'._WBLANGADMIN_WIN_FORMS_WRONGMAILADDRESS_.'</span>###error';
			exit;
		}
	}
	
	if($_POST['empfaenger'] == '' || $_POST['empfaengermail'] == '' || $_POST['empfaengername'] == '' || $_POST['empfaengerbetreff'] == '' || ($langs == '' && isset($_POST['bestaetigung']) && $_POST['bestaetigungsbetreff'] == '') || $canbesaved == 'false')
	{
        echo '<span class="red">'._WBLANGADMIN_WIN_FORMS_FIELDEMPTY_.'</span>###error';
        exit;
	}
	else
	{
		$webutlersave->mailaddresses[$sendto]['empfaenger'] = htmlspecialchars($_POST['empfaenger'], ENT_QUOTES);
		$webutlersave->mailaddresses[$sendto]['empfaengermail'] = $post_empfaengermail;
		$webutlersave->mailaddresses[$sendto]['empfaengername'] = htmlspecialchars($_POST['empfaengername'], ENT_QUOTES);
		$webutlersave->mailaddresses[$sendto]['empfaengerbetreff'] = htmlspecialchars($_POST['empfaengerbetreff'], ENT_QUOTES);
		$webutlersave->mailaddresses[$sendto]['bestaetigung'] = isset($_POST['bestaetigung']) ? '1' : '0';
		if($langs != '')
		{
			if(!isset($webutlersave->mailaddresses[$sendto]['bestaetigungsbetreff']) || !is_array($webutlersave->mailaddresses[$sendto]['bestaetigungsbetreff']))
				$webutlersave->mailaddresses[$sendto]['bestaetigungsbetreff'] = array();
			
			if(!isset($webutlersave->mailaddresses[$sendto]['sentalert']) || !is_array($webutlersave->mailaddresses[$sendto]['sentalert']))
				$webutlersave->mailaddresses[$sendto]['sentalert'] = array();
			
			foreach($langs as $lang)
			{
				$webutlersave->mailaddresses[$sendto]['bestaetigungsbetreff'][$lang] = htmlspecialchars($_POST['bestaetigungsbetreff'][$lang], ENT_QUOTES);
				$webutlersave->mailaddresses[$sendto]['sentalert'][$lang] = isset($_POST['sentalert'][$lang]) ? htmlspecialchars($_POST['sentalert'][$lang], ENT_QUOTES) : '';
			}
		}
		else
		{
			$webutlersave->mailaddresses[$sendto]['bestaetigungsbetreff'] = htmlspecialchars($_POST['bestaetigungsbetreff'], ENT_QUOTES);
			$webutlersave->mailaddresses[$sendto]['sentalert'] = isset($_POST['sentalert']) ? htmlspecialchars($_POST['sentalert'], ENT_QUOTES) : '';
		}
		
		
		$webutlersave->saveaddresses();
		echo '<span class="green">'._WBLANGADMIN_WIN_FORMS_ISSAVED_.'</span>';
		exit;
    }
}

// Sprachen
if(isset($_POST['savelangcode']) || isset($_POST['savelanghomes']) || isset($_POST['langposup']) || isset($_POST['langposdown']))
{
    $iswriteable = $webutlersave->iswriteable('/content/access/languages.php', 'ajax');
    if($iswriteable != '')
    {
        echo $iswriteable;
        exit;
    }
    
	$langfile = $webutlersave->config['server_path'].'/content/access/languages.php';
    $result = '';
    $langconfig = file_get_contents($langfile);
    
    if(isset($_POST['langposup']) || isset($_POST['langposdown']))
	{
		$codes = $webutlersave->langconf['code'];
        foreach($codes as $key => $value)
		{
            if($_POST['langposup'] == $value)
			{
                $dummy = $codes[$key-1];
                $codes[$key-1] = $value;
                $codes[$key] = $dummy;
                break;
            }
            if($_POST['langposdown'] == $value)
			{
                $dummy = $codes[$key+1];
                $codes[$key+1] = $value;
                $codes[$key] = $dummy;
                break;
            }
        }
        $codes = implode("','", $codes);
        $langconfig = preg_replace('#(\$webutler_langconf\[\'code\'\] = array\()([^\)]*)(\);)#Usi', '${1}\''.$codes.'\'$3', $langconfig);
    }
    elseif(isset($_POST['savelangcode']))
	{
		$codes = $webutlersave->langconf['code'];
		$langs = $webutlersave->langconf['lang'];
		$homes = $webutlersave->langconf['homes'];
		$newlangs = array();
		$newhomes = array();
        
        if(($_POST['newcode'] != '' && $_POST['newlang'] == '') || ($_POST['newcode'] == '' && $_POST['newlang'] != ''))
        {
            echo '<span class="red">'._WBLANGADMIN_WIN_LANGUAGE_FIELDEMPTY_.'</span>';
            exit;
        }
        else
        {
            foreach($codes as $code => $value)
			{
                if(isset($_POST['delete_'.$value]))
				{
                    unset($codes[$code]);
                    unset($langs[$value]);
                    unset($homes[$value]);
                    $langconfig = preg_replace('#(\$webutler_langconf\[\'pages\'\]\[\''.$value.'\'\] = array\()([^\)]*)(\);\n)#Usi', '', $langconfig);
                }
            }
            foreach($langs as $lang => $value)
			{
                $newlangs[] = addslashes("'".$lang."' => '".$value."'");
            }
            foreach($homes as $home => $value)
			{
                $newhomes[] = addslashes("'".$home."' => '".$value."'");
            }
			
			$new_pages_langcode = '';
            if($_POST['newcode'] != '' && $_POST['newlang'] != '')
			{
                $postnewcode = preg_replace("/[^a-z]/", "", $_POST['newcode']);
				$postnewlang = htmlspecialchars($_POST['newlang'], ENT_QUOTES);
                $codes[] = $postnewcode;
                $newlangs[] = addslashes("'".$postnewcode."' => '".$postnewlang."'");
                //$langconfig = str_replace("\n? >", "\$webutler_langconf['pages']['".$postnewcode."'] = array();\n\n? >", $langconfig);
                $new_pages_langcode = "\$webutler_langconf['pages']['".$postnewcode."'] = array();\n";
            }

            $codes = count($codes) > 0 ? "'".implode("','", $codes)."'" : '';
            //$codes = implode(',', $codes);
            $langs = stripslashes(implode(',', $newlangs));
            $homes = stripslashes(implode(',', $newhomes));
            
        	$langconfig = preg_replace('#(\$webutler_langconf\[\'code\'\] = array\()([^\)]*)(\);)#Usi', '${1}'.$codes.'$3', $langconfig);
        	$langconfig = preg_replace('#(\$webutler_langconf\[\'lang\'\] = array\()([^\)]*)(\);)#Usi', '${1}'.$langs.'$3', $langconfig);
        	$langconfig = preg_replace('#(\$webutler_langconf\[\'homes\'\] = array\()([^\)]*)(\);)#Usi', '${1}'.$homes.'$3', $langconfig);
        	$langconfig = $langconfig.$new_pages_langcode;
        	
            $result.= '<span class="green">'._WBLANGADMIN_WIN_LANGUAGE_ISSAVED_.'</span>';
        }
    }
    elseif(isset($_POST['savelanghomes']))
	{
		$codes = $webutlersave->langconf['code'];
		$newhomes = array();
        foreach($codes as $code => $value)
		{
            if(isset($_POST['homes_'.$value]))
			{
                $newhomes[] = addslashes("'".$value."' => '".$_POST['homes_'.$value]."'");
            }
        }
        $homes = stripslashes(implode(',', $newhomes));
		
    	$langconfig = preg_replace('#(\$webutler_langconf\[\'homes\'\] = array\()([^\)]*)(\);)#Usi', '${1}'.$homes.'$3', $langconfig);
		
        $result.= '<span class="green">'._WBLANGADMIN_WIN_LANGUAGE_STARTPAGES_.'</span>';
    }
    
	file_put_contents($langfile, $langconfig);
	
    $webutlersave->setchmodaftersave($langfile);
    
    if($result != '')
        echo $result;
}

if(isset($_POST['savepagelang']))
{
    $changelang = preg_replace("/[^a-z]/", "", $_POST['changelang']);
    $locationpage = $webutlersave->filenamesigns($_POST['locationpage']);
    $webutlersave->delpagefromlang($locationpage);
    $webutlersave->setpagetolang($locationpage, $changelang);
	
	$urllang = ($webutlersave->config['languages'] == '1' && $webutlersave->config['langfolder'] == '1') ? '1' : '0';
    
    echo '<span class="green">'._WBLANGADMIN_POPUPWIN_LANGUAGE_ISSAVED_.'</span>###'._WBLANGADMIN_POPUPWIN_ROUTE_RELOAD_.'###'.$webutlersave->getsavelocationurl($locationpage, ($urllang == '1' ? true : false));
}





