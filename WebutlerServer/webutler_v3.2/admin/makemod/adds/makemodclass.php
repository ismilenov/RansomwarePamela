<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(preg_match('#/adds/makemodclass.php#i', $_SERVER['REQUEST_URI']))
    exit('no access');

class MakeModClass
{
    var $dbpath;
    var $chmod;
    var $alert = array();
    var $post;
    var $mmdb;
    var $modid;
    var $modname;
    var $viewtplfiles = array('main','cat','catslist','subcat','subcatslist','catsmenu','topicslist','topicdata','topicslist_startdata','topic','topic_startdata','topicnew','dataslist','data','datanew','datafull','userinput','breadcrumb','pager','checkbox','multiimgs','options','newest','filter');
    var $conforder = array();
    var $filevars = array();
    var $schedule = array();
    var $userinputs = array();
	var $notrequired = array();
    var $types = array();
    var $sublinks = array();
    var $modconf = array();
    var $adminvars = array();
    var $adminlangs = array();
    var $adminbricks = array();
    var $viewbricks = array();
    var $viewlangs = array();
    var $adminjs = array();
    var $viewjs = array();
    var $lightboxjs = '';
    var $titleisset = false;
    var $seotrclass;
    
    function connectdb()
    {
        $this->mmdb = new SQLite3($this->dbpath.'/makemod.db');
    }

    function dbnumrows($check)
    {
        $rows = 0;
        while($row = $check->fetchArray()) {
            $rows++;
        }
        return $rows;
    }
    
    function validfield($field)
    {
    	$field = strtolower($field);
    	$field = preg_replace("/[^a-z0-9]/", "", $field);
    	return $field;
    }
    
    function validnum($field)
    {
    	$field = preg_replace("/[^0-9]/", "", $field);
    	return $field;
    }
    
    function listmods()
    {
        $allmods = $this->mmdb->query("SELECT id, name FROM projects ORDER BY id");
        if($this->dbnumrows($allmods) > 0) {
            while($module = $allmods->fetchArray()) {
                $result[] = '<option value="'.$module['id'].'">'.$module['name'].'</option>';
            }
        }
        else {
            $result[] = '<option value=""> -- '._MAKEMODLANG_PAGE_EMPTY_.' -- </option>';
        }
        return $result;
    }
    
    function createmod()
    {
        $modulname = $this->validfield($this->post['createmod']);
        $exist = $this->mmdb->query("SELECT id, name FROM projects WHERE projects.name = '".$this->mmdb->escapeString($modulname)."' LIMIT 1");
        if($this->dbnumrows($exist) > 0) {
            $this->alert[] = _MAKEMODLANG_PAGE_LOAD_PROJECTEXISTS_;
        }
        elseif($modulname == 'cat') {
            $this->alert[] = $modulname.' '._MAKEMODLANG_PAGE_LOAD_MODNAMEWRONG_;
        }
        else {
            $todb = $this->mmdb->query("INSERT INTO projects (name) VALUES ('".$this->mmdb->escapeString($modulname)."')");
            if(!$todb) {
                $this->alert[] = _MAKEMODLANG_PAGE_LOAD_ERRORNEWMOD_;
            }
            else {
                $newmod = $this->mmdb->query("SELECT id, name FROM projects WHERE projects.name = '".$this->mmdb->escapeString($modulname)."' LIMIT 1");
                if($mod = $newmod->fetchArray()) {
                    $this->modid = $mod['id'];
                    $this->mmdb->query("BEGIN");
                    $this->mmdb->query("INSERT INTO admin (projectid,modcats,basecatids,cats,catopts,topics,bylang,multilang,byuser,catsort,catmenu,copytopictocat,topicsort,breaktopic,disttopicstart,copydatatocat,copydatatotopic,datasort,breakdata,options) VALUES ('".$this->mmdb->escapeString($mod['id'])."','','','','','','','','','','','','','','','','','','','')");
                    $this->mmdb->query("INSERT INTO view (projectid,newtopics,newdata,full,newlink,newest,filter) VALUES ('".$this->mmdb->escapeString($mod['id'])."','','','','','','')");
                    $this->mmdb->query("INSERT INTO listtpl (projectid,tpldata) VALUES ('".$this->mmdb->escapeString($mod['id'])."','')");
                    $this->mmdb->query("INSERT INTO fulltpl (projectid,tpldata) VALUES ('".$this->mmdb->escapeString($mod['id'])."','')");
                    $this->mmdb->query("INSERT INTO inputtpl (projectid,tpldata) VALUES ('".$this->mmdb->escapeString($mod['id'])."','')");
                    $this->mmdb->query("INSERT INTO newesttpl (projectid,tpldata) VALUES ('".$this->mmdb->escapeString($mod['id'])."','')");
                    $confdb = $this->mmdb->query("COMMIT");
                    if(!$confdb) {
                        $this->mmdb->query("DELETE FROM projects WHERE id = '".$this->mmdb->escapeString($mod['id'])."'");
                        $this->alert[] = _MAKEMODLANG_PAGE_LOAD_ERRORNEWMOD_;
                    }
                }
            }
        }
    }
    
    function loadmod()
    {
        $modid = $this->validnum($this->post['loadmod']);
        if($modid == '') {
            $this->alert[] = _MAKEMODLANG_PAGE_LOAD_MODNOSELECT_;
        }
        else {
            $loadmod = $this->mmdb->query("SELECT id, name FROM projects WHERE projects.id = '".$this->mmdb->escapeString($modid)."' LIMIT 1");
            if($this->dbnumrows($loadmod) == 0) {
                $this->alert[] = _MAKEMODLANG_PAGE_LOAD_MODNOTEXISTS_;
            }
            else {
                $this->modid = $modid;
            }
        }
    }
    
    function getmodname()
    {
        $modulname = $this->mmdb->query("SELECT id, name FROM projects WHERE projects.id = '".$this->mmdb->escapeString($this->modid)."' LIMIT 1");
        if($this->dbnumrows($modulname) != 0) {
            if($modul = $modulname->fetchArray()) {
                $this->modname = $modul['name'];
            }
        }
    }
    
    function listfieldtypes($postfield)
    {
        $fieldtypes = $this->mmdb->query("SELECT id, name FROM types ORDER BY id");
        if($this->dbnumrows($fieldtypes) > 0) {
            while($field = $fieldtypes->fetchArray()) {
                $option = '<option';
                if($postfield == $field['id'])
                    $option.= ' selected="selected"';
                $option.= ' value="'.$field['id'].'">'.constant($field['name']).'</option>';
                $result[] = $option;
            }
        }
        return $result;
    }
    
    function newfield()
    {
        $project = $this->mmdb->query("SELECT id FROM projects WHERE projects.id = '".$this->mmdb->escapeString($this->modid)."' LIMIT 1");
        if($this->dbnumrows($project) == 0) {
            $this->alert[] = _MAKEMODLANG_PAGE_LOAD_MODNOTEXISTS_;
        }
        else {
            $fieldname = $this->post['fieldname'];
            if($this->post['fieldinput'] == '') $this->post['fieldinput'] = $fieldname;
            $fieldinput = $this->validfield($this->post['fieldinput']);
            $fieldtype = $this->validnum($this->post['fieldtype']);
            
            $blocknames = array('id', 'config', 'saveconf', 'option', 'grpname', 'options', 'optionids', 'optimage', 'cat', 'catid', 'catname', 'catimage', 'cattext', 'catlink', 'catsort', 'menusort', 'subcats', 'scrollpos', 'savecat', 'editcat', 'topic', 'topicid', 'copytopictocat', 'topicsort', 'startid', 'savetopic', 'copytopic', 'edittopic', 'title', 'copydatatocat', 'copydatatotopic', 'datasort', 'dataid', 'savedata', 'copydata', 'editdata', 'breaktopic', 'disttopicstart', 'seotitle', 'seodesc', 'seokeys', 'copyof', 'breakdata', 'fromtime', 'totime', 'select', 'checkbox', 'lang', 'sort', 'onoff', 'on', 'off', 'up', 'down', 'change', 'changeto', 'multilang', $this->modname.'lang', 'group', 'user', 'username');
			
            if($fieldname == '' || $fieldinput == '' || $fieldtype == '') {
                $this->alert[] = _MAKEMODLANG_PAGE_DEFINES_FIELDSEMPTY_;
            }
            elseif(in_array($fieldinput, $blocknames)) {
                $this->alert[] = $fieldinput.' '._MAKEMODLANG_PAGE_DEFINES_FIELDWRONG_;
            }
            else {
                $check = $this->mmdb->query("SELECT id, projectid, field FROM fields WHERE fields.projectid = '".$this->mmdb->escapeString($this->modid)."' AND fields.field = '".$this->mmdb->escapeString($fieldinput)."' LIMIT 1");
                if($this->dbnumrows($check) >= 1) {
                    $this->alert[] = sprintf(_MAKEMODLANG_PAGE_DEFINES_FIELDEXISTS_, '&quot;'.$fieldinput.'&quot;');
                }
                else {
                    $sort = $this->fieldlastpos();
                    $todb = $this->mmdb->query("INSERT INTO fields (projectid,typeid,name,field,sort) VALUES ('".$this->mmdb->escapeString($this->modid)."','".$this->mmdb->escapeString($fieldtype)."','".$this->mmdb->escapeString($fieldname)."','".$this->mmdb->escapeString($fieldinput)."','".$this->mmdb->escapeString($sort)."')");
                    if(!$todb) {
                        $this->alert[] = _MAKEMODLANG_PAGE_DEFINES_ERRORNEWFIELD_;
                    }
                }
            }
        }
    }
    
    function fieldlastpos()
    {
        $allfields = $this->mmdb->query("SELECT id, projectid FROM fields WHERE fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $lastpos = $this->dbnumrows($allfields)+1;
        
        return $lastpos;
    }
    
    function movefieldup($id)
    {
        $lastpos = $this->fieldlastpos();
        
        $field = $this->mmdb->query("SELECT id, projectid, sort FROM fields WHERE fields.id = '".$this->mmdb->escapeString($id)."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $sort = $field->fetchArray();
        $pos = $this->validnum($sort['sort']);
        
        $this->mmdb->query("UPDATE fields SET sort = '".$lastpos."' WHERE fields.sort = '".($pos-1)."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $this->mmdb->query("UPDATE fields SET sort = '".($pos-1)."' WHERE fields.sort = '".$pos."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $this->mmdb->query("UPDATE fields SET sort = '".$pos."' WHERE fields.sort = '".$lastpos."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
    }
    
    function movefielddown($id)
    {
        $lastpos = $this->fieldlastpos();
        
        $field = $this->mmdb->query("SELECT id, projectid, sort FROM fields WHERE fields.id = '".$this->mmdb->escapeString($id)."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $sort = $field->fetchArray();
        $pos = $this->validnum($sort['sort']);
        
        $this->mmdb->query("UPDATE fields SET sort = '".$lastpos."' WHERE fields.sort = '".($pos+1)."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $this->mmdb->query("UPDATE fields SET sort = '".($pos+1)."' WHERE fields.sort = '".$pos."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $this->mmdb->query("UPDATE fields SET sort = '".$pos."' WHERE fields.sort = '".$lastpos."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
    }
    
    function deletefield($id)
    {
        $field = $this->mmdb->query("SELECT id, projectid, sort FROM fields WHERE fields.id = '".$this->mmdb->escapeString($id)."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $sort = $field->fetchArray();
        $pos = $this->validnum($sort['sort']);
        
        $todb = $this->mmdb->query("DELETE FROM fields WHERE id = '".$this->mmdb->escapeString($id)."' AND projectid = '".$this->mmdb->escapeString($this->modid)."'");
        if(!$todb) {
            $this->alert[] = _MAKEMODLANG_PAGE_DEFINES_ERRORDELFIELD_;
        }
        
        $count = $this->mmdb->query("SELECT id, projectid, sort FROM fields WHERE fields.sort > '".$pos."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        if($this->dbnumrows($count) != 0) {
            $newpos = $pos+1;
            for($i = 0; $i <= $this->dbnumrows($count); $i++) {
                $this->mmdb->query("UPDATE fields SET sort = '".($newpos-1)."' WHERE fields.sort = '".$newpos."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
                $newpos = ($newpos+1);
            }
        }
        
        $this->updatetplfield('listtpl', $id);
        $this->updatetplfield('fulltpl', $id);
        $this->updatetplfield('inputtpl', $id);
    }
    
    function imageoptions($id, $scal)
    {
        $fields = $this->mmdb->query("SELECT id, projectid, field FROM fields WHERE fields.id = '".$this->mmdb->escapeString($id)."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $field = $fields->fetchArray();
        $fieldname = $field['field'];
        
        $save["'".$fieldname."'"]['box']['width'] = $this->validnum($scal[$id]['box']['width']);
        $save["'".$fieldname."'"]['box']['height'] = $this->validnum($scal[$id]['box']['height']);
        $save["'".$fieldname."'"]['view']['width'] = $this->validnum($scal[$id]['view']['width']);
        $save["'".$fieldname."'"]['view']['height'] = $this->validnum($scal[$id]['view']['height']);
        $save["'".$fieldname."'"]['full']['width'] = $this->validnum($scal[$id]['full']['width']);
        $save["'".$fieldname."'"]['full']['height'] = $this->validnum($scal[$id]['full']['height']);
        
        $savescal = serialize($save);
        
        $todb = $this->mmdb->query("UPDATE fields SET options = '".$this->mmdb->escapeString($savescal)."' WHERE fields.id = '".$this->mmdb->escapeString($id)."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        if(!$todb) {
            $this->alert[] = _MAKEMODLANG_PAGE_DEFINES_SCALNOTSAVED_;
        }
    }
    
    function updatetplfield($tpl, $id)
    {
        $tpldatas = $this->mmdb->query("SELECT id, projectid, tpldata FROM ".$tpl." WHERE ".$tpl.".projectid = '".$this->mmdb->escapeString($this->modid)."'");
        if($tpldata = $tpldatas->fetchArray()) {
            $data = explode('|', $tpldata['tpldata']);
            foreach($data as $key => $val) {
                if($val == $id) unset($data[$key]);
            }
            $savedata = (count($data) >= 1) ? implode('|', $data) : '';
            
            $todb = $this->mmdb->query("UPDATE ".$tpl." SET tpldata = '".$this->mmdb->escapeString($savedata)."' WHERE ".$tpl.".projectid = '".$this->mmdb->escapeString($this->modid)."'");
            if(!$todb) {
                $this->alert[] = sprintf(_MAKEMODLANG_PAGE_TPLS_NODEL_, $tpl);
            }
        }
    }
    
    function loadfields($tpl)
    {
        $fields = $this->mmdb->query("SELECT id, projectid, typeid, name, field, options, sort FROM fields WHERE fields.projectid = '".$this->mmdb->escapeString($this->modid)."' ORDER BY sort");
        $fieldnumrows = $this->dbnumrows($fields);
        $result = '';
        if($fieldnumrows == 0) {
            $result.= '<tr>'."\n";
            $result.= '<td colspan="5">'._MAKEMODLANG_PAGE_TPLS_NOFIELDS_.'</td>'."\n";
            $result.= '</tr>'."\n";
        }
        else {
            if($tpl != 'define') {
                $tpldatas = $this->mmdb->query("SELECT id, projectid, tpldata FROM ".$tpl." WHERE ".$tpl.".projectid = '".$this->mmdb->escapeString($this->modid)."'");
                if($tpldata = $tpldatas->fetchArray()) {
                    $data = explode('|', $tpldata['tpldata']);
                }
            }
            $result.= '<tr class="listhead">'."\n";
            $result.= '<td><strong>'._MAKEMODLANG_PAGE_TPLSROW_IDENT_.'</strong></td>'."\n";
            $result.= '<td><strong>'._MAKEMODLANG_PAGE_TPLSROW_DBNAME_.'</strong></td>'."\n";
            $result.= '<td>';
            if($tpl == 'listtpl' || $tpl == 'fulltpl') $result.= '<strong>'._MAKEMODLANG_PAGE_TPLSROW_OUTPUT_.'</strong>';
            elseif($tpl == 'inputtpl') $result.= '<strong>'._MAKEMODLANG_PAGE_TPLSROW_INPUT_.'</strong>';
            else $result.= '<strong>'._MAKEMODLANG_PAGE_TPLSROW_DBTYPE_.'</strong>';
            $result.= '</td>'."\n";
            $result.= '<td class="last">&nbsp;</td>'."\n";
            $result.= '</tr>'."\n";
            $count = 0;
            while($field = $fields->fetchArray()) {
                $count = $count+1;
                $result.= '<tr';
                if($count % 2 == 0) $result.= ' class="listlines"';
                $result.= '>'."\n";
                $types = $this->mmdb->query("SELECT id, name, type FROM types WHERE types.id = '".$this->mmdb->escapeString($field['typeid'])."' LIMIT 1");
                if($type = $types->fetchArray()) {
                    $typename = $type['name'];
                    $typetype = $type['type'];
                }
                $options = unserialize($field['options']);
                $result.= '<td>'.$field['name'].'</td>'."\n";
                $result.= '<td>'.$field['field'].'</td>'."\n";
                $result.= '<td>'.constant($typename).'</td>'."\n";
                if($tpl == 'define') {
                    $result.= '<td class="last">
                        <table class="showfield" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                        <td style="padding-right: 10px">
                        <form method="post" action="index.php?'.str_replace('&saved', '', $_SERVER['QUERY_STRING']).'">'."\n";
                    if($count == 1) {
                        $result.= '<img src="adds/icons/noicon.png" />';
					}
                    else {
                        $result.= '<input type="image" title="'._MAKEMODLANG_PAGE_TPLS_MOVEUP_.'" name="fieldup['.$field['id'].']" src="adds/icons/up.png" />';
					}
                    if($count == $fieldnumrows) {
                        $result.= '<img src="adds/icons/noicon.png" />';
					}
                    else {
                        $result.= '<input type="image" title="'._MAKEMODLANG_PAGE_TPLS_MOVEDOWN_.'" name="fielddown['.$field['id'].']" src="adds/icons/down.png" />';
					}
                    $result.= "\n".'</form>
                        </td>
                        <td>
                        <form method="post" action="index.php?'.str_replace('&saved', '', $_SERVER['QUERY_STRING']).'" onsubmit="if(!this.ok) return false">
                        <input type="image" title="'._MAKEMODLANG_PAGE_TPLS_DELETE_.'" name="delete['.$field['id'].']" src="adds/icons/delete.png" onclick="this.form.ok=confirmdelete(\''.sprintf(_MAKEMODLANG_PAGE_TPLS_ASKDEL_, $field['name']).'\');" />
                        </form>
                        </td>';
                    if($typetype == 'image' || $typetype == 'multi') {
                        $result.= '<td>
                            <img src="adds/icons/opts.png" title="'._MAKEMODLANG_PAGE_TPLS_OPTIONS_.'" class="optsicon" onclick="document.getElementById(\'imageopts'.$field['id'].'\').style.display=\'inline\'" />
                            <div id="imageopts'.$field['id'].'" class="optsfields" style="display: none">
                            <form method="post" action="index.php?'.str_replace('&saved', '', $_SERVER['QUERY_STRING']).'">
                            <table border="0" cellspacing="6" cellpadding="0">
                              <tr>
                                <td colspan="2"><strong>'._MAKEMODLANG_PAGE_IMAGESCAL_.':</strong></td>
                                <td style="text-align: right"><img src="adds/icons/close.gif" class="optsicon" onclick="document.getElementById(\'imageopts'.$field['id'].'\').style.display=\'none\'" /></td>
                              </tr>
                              <tr>
                                <td>'._MAKEMODLANG_PAGE_LIGHTBOX_.'</td>
                                <td><input type="text" class="mminput" style="width: 25px" name="scal['.$field['id'].'][box][width]" value="'.$options['\''.$field['field'].'\'']['box']['width'].'" /> px '._MAKEMODLANG_PAGE_IMGWIDTH_.'</td>
                                <td><input type="text" class="mminput" style="width: 25px" name="scal['.$field['id'].'][box][height]" value="'.$options['\''.$field['field'].'\'']['box']['height'].'" /> px '._MAKEMODLANG_PAGE_IMGHEIGHT_.'</td>
                              </tr>
                              <tr>
                                <td>'._MAKEMODLANG_PAGE_LIST_.'</td>
                                <td><input type="text" class="mminput" style="width: 25px" name="scal['.$field['id'].'][view][width]" value="'.$options['\''.$field['field'].'\'']['view']['width'].'" /> px '._MAKEMODLANG_PAGE_IMGWIDTH_.'</td>
                                <td><input type="text" class="mminput" style="width: 25px" name="scal['.$field['id'].'][view][height]" value="'.$options['\''.$field['field'].'\'']['view']['height'].'" /> px '._MAKEMODLANG_PAGE_IMGHEIGHT_.'</td>
                              </tr>
                              <tr>
                                <td>'._MAKEMODLANG_PAGE_FULL_.'</td>
                                <td><input type="text" class="mminput" style="width: 25px" name="scal['.$field['id'].'][full][width]" value="'.$options['\''.$field['field'].'\'']['full']['width'].'" /> px '._MAKEMODLANG_PAGE_IMGWIDTH_.'</td>
                                <td><input type="text" class="mminput" style="width: 25px" name="scal['.$field['id'].'][full][height]" value="'.$options['\''.$field['field'].'\'']['full']['height'].'" /> px '._MAKEMODLANG_PAGE_IMGHEIGHT_.'</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td colspan="2"><input type="submit" class="mmbutton" style="width: 100px" value="'._MAKEMODLANG_PAGE_SAVE_.'" /></td>
                              </tr>
                            </table>
                            </form>
                            </div>
                            </td>';
                    }
                    $result.= '</tr>
                        </table>
                    </td>'."\n";
                }
                else {
                    $result.= '<td class="last">
                      <table class="showfield" border="0" cellspacing="0" cellpadding="0">
                    	<tr>
                    	  <td>';
					/*
					if($tpl == 'inputtpl' && $field['field'] == 'hidden') {
						$result.= '<input type="checkbox" disabled="disabled" />';
					}
					else {
					}
					*/
						$result.= '<input type="checkbox" name="tpldata['.$field['id'].']" id="'.$field['field'].'"';
						if(is_array($data) && in_array($field['id'], $data))
							$result.= ' checked="checked"';
						$result.= ' />';
					$result.= '</td>
                    	  <td><label for="'.$field['field'].'">'._MAKEMODLANG_PAGE_TPLS_CHECKSHOW_.'</label></td>
                        </tr>
                      </table>
                    </td>'."\n";
                }
                $result.= '</tr>'."\n";
            }
        }
        return $result;
    }
    
    function saveadminsets()
    {
        $savescal['cat']['box']['width'] = $this->validnum($this->post['catopts']['cat']['box']['width']);
        $savescal['cat']['box']['height'] = $this->validnum($this->post['catopts']['cat']['box']['height']);
        $savescal['cat']['view']['width'] = $this->validnum($this->post['catopts']['cat']['view']['width']);
        $savescal['cat']['view']['height'] = $this->validnum($this->post['catopts']['cat']['view']['height']);
        
        $modcats = (isset($this->post['modcats'])) ? '1' : '';
		$basecat = (isset($this->post['basecat'])) ? '1' : '';
        $cats = (isset($this->post['modcats']) && isset($this->post['cats'])) ? $this->post['cats'] : 'catname';
        $catopts = serialize($savescal);
        $topics = (isset($this->post['topics'])) ? '1' : '';
        $subedit = (isset($this->post['subedit'])) ? '1' : '';
        $bylang = (isset($this->post['bylang'])) ? '1' : '';
        $multilang = (isset($this->post['multilang'])) ? '1' : '';
        $byuser = (isset($this->post['byuser'])) ? '1' : '';
        //$catsort = (isset($this->post['catsort'])) ? '1' : '';
        //$catmenu = (isset($this->post['catmenu'])) ? '1' : '';
        $catmenu = (isset($this->post['subcats']) && isset($this->post['catmenu'])) ? '1' : '';
        
        if(isset($this->post['catsort']))
            $catsort = '1';
        elseif(isset($this->post['subcats']))
            $catsort = '2';
        else
            $catsort = '';
        
        if(isset($this->post['topicsort']))
            $topicsort = '1';
        elseif(isset($this->post['sorttopicfield']))
            $topicsort = '2';
        else
            $topicsort = '';
        
        if(isset($this->post['datasort']))
            $datasort = '1';
        elseif(isset($this->post['sortdatafield']))
            $datasort = '2';
        else
            $datasort = '';
        
        $breaktopic = (isset($this->post['breaktopic'])) ? '1' : '';
		$disttopicstart = (isset($this->post['disttopicstart'])) ? '1' : '';
		$copytopictocat = ($modcats == 1 && isset($this->post['copytopictocat'])) ? '1' : '';
		$copydatatocat = ($modcats == 1 && isset($this->post['copydatatocat']) && !isset($this->post['copytopictocat'])) ? '1' : '';
		$copydatatotopic = ($topics == 1 && isset($this->post['copydatatotopic'])) ? '1' : '';
        $breakdata = (isset($this->post['breakdata'])) ? '1' : '';
        $options = (isset($this->post['options'])) ? '1' : '';
        $autolightbox = (isset($this->post['autolightbox'])) ? '1' : '';
        $seo = (isset($this->post['seo'])) ? '1' : '';
        $seocats = ($seo == 1 && $modcats == 1 && isset($this->post['seocats'])) ? '1' : '';
        $seotopics = ($seo == 1 && $topics == 1 && isset($this->post['seotopics'])) ? '1' : '';
        $seodatas = ($seo == 1 && isset($this->post['seodatas'])) ? '1' : '';
        
        $todb = $this->mmdb->query("UPDATE admin SET modcats = '".$this->mmdb->escapeString($modcats)."', basecatids = '".$this->mmdb->escapeString($basecat)."', cats = '".$this->mmdb->escapeString($cats)."', catopts = '".$this->mmdb->escapeString($catopts)."', topics = '".$this->mmdb->escapeString($topics)."', subedit = '".$this->mmdb->escapeString($subedit)."', bylang = '".$this->mmdb->escapeString($bylang)."', multilang = '".$this->mmdb->escapeString($multilang)."', byuser = '".$this->mmdb->escapeString($byuser)."', catsort = '".$this->mmdb->escapeString($catsort)."', catmenu = '".$this->mmdb->escapeString($catmenu)."', copytopictocat = '".$this->mmdb->escapeString($copytopictocat)."', topicsort = '".$this->mmdb->escapeString($topicsort)."', breaktopic = '".$this->mmdb->escapeString($breaktopic)."', disttopicstart = '".$this->mmdb->escapeString($disttopicstart)."', copydatatocat = '".$this->mmdb->escapeString($copydatatocat)."', copydatatotopic = '".$this->mmdb->escapeString($copydatatotopic)."', datasort = '".$this->mmdb->escapeString($datasort)."', breakdata = '".$this->mmdb->escapeString($breakdata)."', options = '".$this->mmdb->escapeString($options)."', autolightbox = '".$this->mmdb->escapeString($autolightbox)."', seo = '".$this->mmdb->escapeString($seo)."', seocats = '".$this->mmdb->escapeString($seocats)."', seotopics = '".$this->mmdb->escapeString($seotopics)."', seodatas = '".$this->mmdb->escapeString($seodatas)."' WHERE admin.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        if(!$todb) {
            $this->alert[] = _MAKEMODLANG_PAGE_ADMIN_SAVEERROR_;
        }
    }
    
    function loadadminsets()
    {
        $tpldatas = $this->mmdb->query("SELECT id, projectid, modcats, basecatids, cats, catopts, topics, subedit, bylang, multilang, byuser, catsort, catmenu, copytopictocat, topicsort, breaktopic, disttopicstart, copydatatocat, copydatatotopic, datasort, breakdata, options, autolightbox, seo, seocats, seotopics, seodatas FROM admin WHERE admin.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        
        $data = array();
        
        if($tpldata = $tpldatas->fetchArray()) {
            if($tpldata['modcats'] == '1') {
                $data['modcats'] = ' checked="checked"';
            }
            if($tpldata['basecatids'] == '1') $data['basecat'] = ' checked="checked"';
            $data['cats'] = ($tpldata['cats'] != '') ? $tpldata['cats'] : 'catname';
            $data['catopts'] = unserialize($tpldata['catopts']);
            if($tpldata['topics'] == '1') $data['topics'] = ' checked="checked"';
            if($tpldata['subedit'] == '1') $data['subedit'] = ' checked="checked"';
            if($tpldata['bylang'] == '1') $data['bylang'] = ' checked="checked"';
            if($tpldata['multilang'] == '1') $data['multilang'] = ' checked="checked"';
            if($tpldata['byuser'] == '1') $data['byuser'] = ' checked="checked"';
            if($tpldata['catsort'] == '1') $data['catsort'] = ' checked="checked"';
            if($tpldata['catsort'] == '2') $data['subcats'] = ' checked="checked"';
            if($tpldata['catmenu'] == '1') $data['catmenu'] = ' checked="checked"';
            if($tpldata['copytopictocat'] == '1') $data['copytopictocat'] = ' checked="checked"';
            if($tpldata['topicsort'] == '1') $data['topicsort'] = ' checked="checked"';
            if($tpldata['topicsort'] == '2') $data['sorttopicfield'] = ' checked="checked"';
            if($tpldata['breaktopic'] == '1') $data['breaktopic'] = ' checked="checked"';
            if($tpldata['disttopicstart'] == '1') $data['disttopicstart'] = ' checked="checked"';
            if($tpldata['copydatatocat'] == '1') $data['copydatatocat'] = ' checked="checked"';
            if($tpldata['copydatatotopic'] == '1') $data['copydatatotopic'] = ' checked="checked"';
            if($tpldata['datasort'] == '1') $data['datasort'] = ' checked="checked"';
            if($tpldata['datasort'] == '2') $data['sortdatafield'] = ' checked="checked"';
            if($tpldata['breakdata'] == '1') $data['breakdata'] = ' checked="checked"';
            if($tpldata['options'] == '1') $data['options'] = ' checked="checked"';
            if($tpldata['autolightbox'] == '1') $data['autolightbox'] = ' checked="checked"';
            if($tpldata['seo'] == '1') $data['seo'] = ' checked="checked"';
            if($tpldata['seocats'] == '1') $data['seocats'] = ' checked="checked"';
            if($tpldata['seotopics'] == '1') $data['seotopics'] = ' checked="checked"';
            if($tpldata['seodatas'] == '1') $data['seodatas'] = ' checked="checked"';
        }
        
        return $data;
    }
    
    function savelayoutsets()
    {
        $newtopics = (isset($this->post['newtopics'])) ? '1' : '';
        $newdata = (isset($this->post['newdata'])) ? '1' : '';
        $full = (isset($this->post['full'])) ? '1' : '';
        $newlink = (isset($this->post['newlink'])) ? '1' : '';
        $newest = (isset($this->post['newest'])) ? '1' : '';
        $filter = (isset($this->post['filter'])) ? '1' : '';
        
        $todb = $this->mmdb->query("UPDATE view SET newtopics = '".$this->mmdb->escapeString($newtopics)."', newdata = '".$this->mmdb->escapeString($newdata)."', full = '".$this->mmdb->escapeString($full)."', newlink = '".$this->mmdb->escapeString($newlink)."', newest = '".$this->mmdb->escapeString($newest)."', filter = '".$this->mmdb->escapeString($filter)."' WHERE view.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        if(!$todb) {
            $this->alert[] = _MAKEMODLANG_PAGE_VIEW_SAVEERROR_;
        }
    }
    
    function loadlayoutsets()
    {
        $admindatas = $this->mmdb->query("SELECT id, projectid, modcats, basecatids, cats, topics, bylang, multilang, byuser, catsort, catmenu, copytopictocat, topicsort, breaktopic, disttopicstart, copydatatocat, copydatatotopic, datasort, breakdata, options FROM admin WHERE admin.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $admindata = $admindatas->fetchArray();
        
        $tpldatas = $this->mmdb->query("SELECT id, projectid, newtopics, newdata, full, newlink, newest, filter FROM view WHERE view.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        
        $data = array();
        
        if($tpldata = $tpldatas->fetchArray()) {
            $data['cats'] = ($admindata['modcats'] == '') ? 'no' : 'ok';
            $data['topics'] = ($admindata['topics'] == '') ? 'no' : 'ok';
            $data['newtopics'] = '';
            if($tpldata['newtopics'] == '1') $data['newtopics'].= ' checked="checked"';
            if($admindata['topics'] == '') $data['newtopics'].= 'disabled';
            if($tpldata['newdata'] == '1') $data['newdata'] = ' checked="checked"';
            if($tpldata['full'] == '1') $data['full'] = ' checked="checked"';
            if($tpldata['newlink'] == '1') $data['newlink'] = ' checked="checked"';
            if($tpldata['newest'] == '1') $data['newest'] = ' checked="checked"';
            if($tpldata['filter'] == '1') $data['filter'] = ' checked="checked"';
        }
        
        return $data;
    }
    
    function checkfulldata()
    {
        $tpldatas = $this->mmdb->query("SELECT id, projectid, full, newdata, newlink, newest, filter FROM view WHERE view.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        
        $data = array();
        
        if($tpldata = $tpldatas->fetchArray()) {
            if($tpldata['full'] == '1') $data['full'] = '1';
            if($tpldata['newdata'] == '1') $data['newdata'] = '1';
            if($tpldata['newlink'] == '1') $data['newlink'] = '1';
            if($tpldata['newest'] == '1') $data['newest'] = '1';
            if($tpldata['filter'] == '1') $data['filter'] = '1';
        }
        
        return $data;
    }
    
    function savetplsets($tpl, $tpldata = '')
    {
        $validdata = array();
        if($tpldata != '') {
            foreach($tpldata as $data) {
                $validdata[] = $this->validnum($data);
            }
        }
        $savedata = (count($validdata) >= 1) ? implode('|', $validdata) : '';
        
        $todb = $this->mmdb->query("UPDATE ".$tpl." SET tpldata = '".$this->mmdb->escapeString($savedata)."' WHERE ".$tpl.".projectid = '".$this->mmdb->escapeString($this->modid)."'");
        if(!$todb) {
            $this->alert[] = _MAKEMODLANG_PAGE_TPLS_SAVEERROR_;
        }
    }






//************ Moduldateien generieren ************//











    function makezipfile()
    {
        $this->copybasefiles('draft', $this->dbpath.'/'.$this->modname);
        $this->getdatafields();
        $this->createdbfile();
        $this->setconfig();
        $this->gettpllinks();
        $this->setfieldstotpl();
        $this->setlangdefines();
        $this->makelinkline();
        $this->setfieldstoadmin();
        $this->setmodnametofile('search.php');
        $this->setmodnametofile('sitemap.php');
        $this->setmodnametofile('media/loader.php');
        $this->setmodnametofiles();
        $this->setlangtotpl();
    }
    
    function copybasefiles($from, $to)
    {
		$umask = umask(0);
        mkdir($to, $this->chmod[0]);
   		umask($umask);
        $handle = opendir($from);
    	while(false !== ($file = readdir($handle))) {
            if(is_dir($from.'/'.$file.'/') && $file != '.' && $file != '..') {
                $this->copybasefiles($from.'/'.$file, $to.'/'.$file);
            }
            elseif(is_file($from.'/'.$file)) {
				if($from.'/'.$file != 'draft/.htaccess')
					copy($from.'/'.$file, $to.'/'.$file);
            }
        }
        closedir($handle);
    }
    
    function getdatafields()
    {
        $fields = $this->mmdb->query("SELECT id, projectid, typeid, name, field FROM fields WHERE fields.projectid = '".$this->mmdb->escapeString($this->modid)."' ORDER BY sort");
        
        while($field = $fields->fetchArray()) {
            $types = $this->mmdb->query("SELECT id, name, type FROM types WHERE types.id = '".$this->mmdb->escapeString($field['typeid'])."' LIMIT 1");
            $type = $types->fetchArray();
            
            if(isset($fieldvalues) && is_array($fieldvalues)) unset($fieldvalues);
            
            $fieldvalues = array();
            $fieldvalues[0] = $field['name'];
            $fieldvalues[1] = 'field_'.$field['field'];
            $fieldvalues[2] = strtoupper('_'.$this->modname.'lang_'.$field['field'].'_');
            $fieldvalues[3] = $type['type'];
            $fieldvalues[4] = $field['id'];
            $fieldvalues[5] = $type['name'];
            
            $filevars[] = $fieldvalues;
        }
        
        $first = array();
        $second = array();
        $third = array();
        $rest = array();
        
        foreach($filevars as $filevar => $var) {
            $this->filevars[] = $var;
            
            if($var[3] == 'image')
                $first[] = $var;
            elseif($var[3] == 'multi')
                $second[] = $var;
            elseif($var[3] == 'text')
                $third[] = $var;
            else
                $rest[] = $var;
        }
        
        $this->conforder = array_merge($first, $second, $third, $rest);
    }
    
    function createdbfile()
    {
        $queries = array();
        $fieldtypes = array();
        
        $this->schedule['cat'] = '';
        $this->schedule['topic'] = '';
        $this->schedule['data'] = '';
        $this->schedule['base'] = array();
        
        $this->adminvars['langs'] = '';
        $this->adminvars['options'] = '';
        $this->adminvars['cats'] = '';
        $this->adminvars['catsort'] = '';
        $this->adminvars['catmenu'] = '';
        $this->adminvars['topics'] = '';
        $this->adminvars['bigfiles'] = '';
        
        $this->modconf['breaktopic'] = '';
        $this->modconf['disttopicstart'] = '';
        $this->modconf['breakdata'] = '';
        $this->modconf['getlangs'] = '';
		$this->modconf['userinput'] = '';
		$this->modconf['userinput_hasfiles'] = '';
        $this->modconf['subeditor'] = '';
        $this->modconf['getusers'] = '';
        $this->modconf['setbyuser'] = '';
		$this->modconf['basecat'] = '';
        $this->modconf['subcats'] = '';
        $this->modconf['catsort'] = '';
        $this->modconf['catmenu'] = '';
        $this->modconf['copytopictocat'] = '';
        $this->modconf['topicsort'] = '';
        $this->modconf['copydatatocat'] = '';
        $this->modconf['copydatatotopic'] = '';
        $this->modconf['datasort'] = '';
		$this->modconf['paramloadnew'] = '';
        $this->modconf['newest'] = '';
        $this->modconf['filter'] = '';
        $this->modconf['fulldata'] = '';
        $this->modconf['autolightbox'] = '';
        $this->modconf['seocats'] = '';
        $this->modconf['seotopics'] = '';
        $this->modconf['seodatas'] = '';
        
        $confcreate[] = 'config TEXT DEFAULT \'\'';
        $conffield[] = 'config';
        $confvalue[] = '\'\'';
        $admindatas = $this->mmdb->query("SELECT id, projectid, modcats, basecatids, cats, topics, subedit, bylang, multilang, byuser, catsort, catmenu, copytopictocat, topicsort, breaktopic, disttopicstart, copydatatocat, copydatatotopic, datasort, breakdata, options, autolightbox, seocats, seotopics, seodatas FROM admin WHERE admin.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $admindata = $admindatas->fetchArray();
        
        $checkboxisset = false;
        $multiimgisset = false;
        foreach($this->filevars as $filevar) {
            if($filevar[3] == 'state') {
				$this->notrequired[] = $filevar[1];
            }
            if($filevar[3] == 'select') {
                $confcreate[] = $filevar[1].' TEXT DEFAULT \'\'';
                $conffield[] = $filevar[1];
                $confvalue[] = '\'\'';
				$this->notrequired[] = $filevar[1];
            }
            if($filevar[3] == 'checkbox') {
                $confcreate[] = $filevar[1].' TEXT DEFAULT \'\'';
                $conffield[] = $filevar[1];
                $confvalue[] = '\'\'';
				$this->notrequired[] = $filevar[1];
                $checkboxisset = true;
            }
            if($filevar[3] == 'multi') {
                $multiimgisset = true;
            }
            if($filevar[3] == 'file') {
				$this->adminvars['bigfiles'] = '1';
			}
        }
        if(!$checkboxisset) {
	        $this->deletetplfile('checkbox.tpl');
            $this->deletetplfromview('checkbox');
        }
        if(!$multiimgisset) {
	        $this->deletetplfile('multiimgs.tpl');
            $this->deletetplfromview('multiimgs');
        }
        if($admindata != '' && $admindata['modcats'] == '1') {
			if($admindata['basecatids'] == '1') {
				$this->schedule['base'][] = 'basecat';
				$this->modconf['basecat'] = '1';
			}
			if($admindata['catsort'] == '2') {
				$confcreate[] = 'subcats TEXT DEFAULT \'\'';
				$conffield[] = 'subcats';
				$confvalue[] = '\'\'';
				$this->modconf['subcats'] = 1;
			}
        }
        $confcreates = implode(', ', $confcreate);
        $conffields = implode(', ', $conffield);
        $confvalues = implode(', ', $confvalue);
        //$confvalues = '\''.implode('\', \'', $confvalue).'\'';
        $queries[] = "CREATE TABLE confs (id INTEGER PRIMARY KEY, ".$confcreates.")";
        $queries[] = "INSERT INTO confs (".$conffields.") VALUES (".$confvalues.")";
		
        $viewdatas = $this->mmdb->query("SELECT id, projectid, full, newest, newdata, newlink, filter FROM view WHERE view.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $viewdata = $viewdatas->fetchArray();
        if(isset($viewdata['newest']) && $viewdata['newest'] == '1') {
            $this->modconf['newest'] = '1';
			$this->schedule['base'][] = 'newest';
        }
        if(isset($viewdata['full']) && $viewdata['full'] == '1') {
            $this->modconf['fulldata'] = '1';
        }
        if(isset($viewdata['filter']) && $viewdata['filter'] == '1') {
            $this->modconf['filter'] = '1';
			$this->schedule['base'][] = 'filter';
        }
        if(isset($viewdata['newlink']) && $viewdata['newlink'] == '1') {
			$this->modconf['paramloadnew'] = 1;
		}
		$setbyuser = '0';
        if((isset($viewdata['newdata']) && $viewdata['newdata'] == '1') || (isset($viewdata['newlink']) && $viewdata['newlink'] == '1')) {
			$setbyuser = '1';
		}
        
        if($admindata != '') {
	        if($admindata['options'] == '1') {
		        $queries[] = "CREATE TABLE options (id INTEGER PRIMARY KEY, grpname TEXT DEFAULT '', optvals TEXT DEFAULT '', sort INTEGER DEFAULT '')";
	            $this->adminvars['options'] = 1;
	        }
            if($admindata['subedit'] == '1')
                $this->modconf['subeditor'] = 1;
            if($admindata['byuser'] == '1')
                $this->modconf['getusers'] = 1;
            if($setbyuser == '1')
				$this->modconf['setbyuser'] = 1;
            if($admindata['bylang'] == '1') {
				if($admindata['multilang'] == '1') {
	            	$this->schedule['base'][] = 'multilang';
			        $this->adminvars['langs'] = 'multi';
			    }
	            else {
	                $this->modconf['getlangs'] = 1;
			        $this->adminvars['langs'] = 'start';
                }
            }
            if($admindata['breakdata'] == '1') {
                $this->modconf['breakdata'] = 1;
                $datafields[] = 'fromtime INTEGER DEFAULT \'\'';
                $datafields[] = 'totime INTEGER DEFAULT \'\'';
                $dataschedule[] = 'fromtime';
                $dataschedule[] = 'totime';
            }
            if($admindata['modcats'] == '1') {
                $this->adminvars['cats'] = '1';
                $catdbfields = explode('_', $admindata['cats']);
                foreach($catdbfields as $catdbfield) {
		            $catfields[] = $catdbfield.' TEXT'.($catdbfield != 'catimage' ? ' COLLATE NOCASE' : '').' DEFAULT \'\'';
		            $catschedule[] = $catdbfield;
                }
		        if($admindata['catmenu'] == '1') {
		            $catfields[] = 'catlink TEXT DEFAULT \'\'';
		            $catschedule[] = 'catlink';
		            $this->modconf['catmenu'] = 1;
	                $this->adminvars['catmenu'] = 1;
		        }
		        else {
	                $this->deletetplfile('catsmenu.tpl');
                    $this->deletetplfromview('catsmenu');
		        }
				if($admindata['autolightbox'] == '1') {
					$this->modconf['autolightbox'] = 1;
					//$this->schedule['base'][] = 'lightbox';
		        }
				if($admindata['seocats'] == '1') {
		            $catfields[] = 'seotitle TEXT DEFAULT \'\'';
		            $catfields[] = 'seodesc TEXT DEFAULT \'\'';
		            $catfields[] = 'seokeys TEXT DEFAULT \'\'';
		            $catschedule[] = 'seotitle';
		            $catschedule[] = 'seodesc';
		            $catschedule[] = 'seokeys';
					$this->modconf['seocats'] = 1;
				}
                $catfields[] = 'onoff INTEGER DEFAULT \'\'';
		        $catschedule[] = 'onoff';
                if($admindata['bylang'] == '1' && $admindata['multilang'] != '1') {
	                $catfields[] = 'lang TEXT DEFAULT \'\'';
		            $catschedule[] = 'lang';
                }
                if($admindata['catsort'] == '1') {
                    $catfields[] = 'sort INTEGER DEFAULT \'\'';
		            $catschedule[] = 'sort';
                    $this->modconf['catsort'] = 1;
		        }
                if($admindata['catsort'] == '2') {
	                $this->schedule['base'][] = 'subcats';
                    $this->modconf['catsort'] = 2;
		        }
                else {
                    $this->deletetplfile('subcat.tpl');
                    $this->deletetplfile('subcatslist.tpl');
                    $this->deletetplfile('breadcrumb.tpl');
                    $this->deletetplfromview('subcat');
                    $this->deletetplfromview('subcatslist');
                    $this->deletetplfromview('breadcrumb');
                }
                $this->schedule['cat'] = $catschedule;
                $catpatterns = implode(', ', $catfields);
                $queries[] = 'CREATE TABLE cats (id INTEGER PRIMARY KEY, '.$catpatterns.')';
            }
            else {
	            $this->deletetplfile('catsmenu.tpl');
                $this->deletetplfile('cat.tpl');
                $this->deletetplfile('catslist.tpl');
                $this->deletetplfile('subcat.tpl');
                $this->deletetplfile('subcatslist.tpl');
                $this->deletetplfile('breadcrumb.tpl');
                $this->deletetplfromview('catsmenu');
                $this->deletetplfromview('cat');
                $this->deletetplfromview('catslist');
                $this->deletetplfromview('subcat');
                $this->deletetplfromview('subcatslist');
                $this->deletetplfromview('breadcrumb');
            }
			
            if($admindata['topics'] == '1') {
                $this->adminvars['topics'] = '1';
				if($admindata['modcats'] == '1') {
					$topicfields[] = 'catid INTEGER DEFAULT \'\'';
					$topicschedule[] = 'catid';
				}
                $topicfields[] = 'topic TEXT COLLATE NOCASE DEFAULT \'\'';
                $topicschedule[] = 'topic';
                if($admindata['breaktopic'] == '1') {
                    $this->modconf['breaktopic'] = 1;
                    $topicfields[] = 'fromtime INTEGER DEFAULT \'\'';
                    $topicschedule[] = 'fromtime';
                    $topicfields[] = 'totime INTEGER DEFAULT \'\'';
                    $topicschedule[] = 'totime';
                }
                if($admindata['copytopictocat'] == '1') {
					$this->modconf['copytopictocat'] = '1';
					$topicfields[] = 'copyof INTEGER DEFAULT \'\'';
					$topicschedule[] = 'copyof';
		        }
                if($admindata['disttopicstart'] == '1') {
					$this->modconf['disttopicstart'] = '1';
	                $this->schedule['base'][] = 'disttopicstart';
		        }
				else {
					$this->deletetplfile('topic_startdata.tpl');
					$this->deletetplfile('topicslist_startdata.tpl');
					$this->deletetplfromview('topic_startdata');
					$this->deletetplfromview('topicslist_startdata');
				}
				$topicfields[] = 'startid INTEGER DEFAULT \'\'';
				$topicschedule[] = 'startid';
				if($admindata['seotopics'] == '1') {
		            $topicfields[] = 'seotitle TEXT DEFAULT \'\'';
		            $topicfields[] = 'seodesc TEXT DEFAULT \'\'';
		            $topicfields[] = 'seokeys TEXT DEFAULT \'\'';
		            $topicschedule[] = 'seotitle';
		            $topicschedule[] = 'seodesc';
		            $topicschedule[] = 'seokeys';
					$this->modconf['seotopics'] = 1;
				}
                $topicfields[] = 'onoff INTEGER DEFAULT \'\'';
                $topicschedule[] = 'onoff';
                if($admindata['modcats'] == '' && $admindata['bylang'] == '1' && $admindata['multilang'] != '1') {
                    $topicfields[] = 'lang TEXT DEFAULT \'\'';
                    $topicschedule[] = 'lang';
                }
                if($admindata['topicsort'] == '1') {
                    $topicfields[] = 'sort INTEGER DEFAULT \'\'';
                    $topicschedule[] = 'sort';
                    $this->modconf['topicsort'] = 1;
		        }
                if($admindata['topicsort'] == '2')
                    $this->modconf['topicsort'] = 2;
                $datafields[] = 'topicid INTEGER DEFAULT \'\'';
                $dataschedule[] = 'topicid';
                $this->schedule['topic'] = $topicschedule;
                $topicpatterns = implode(', ', $topicfields);
                $queries[] = 'CREATE TABLE topics (id INTEGER PRIMARY KEY, '.$topicpatterns.')';
                
                $this->deletetplfile('dataslist.tpl');
                $this->deletetplfromview('dataslist');
            }
            else {
				if($admindata['modcats'] == '1') {
					$datafields[] = 'catid INTEGER DEFAULT \'\'';
					$dataschedule[] = 'catid';
				}
                //$datafields[] = 'title TEXT DEFAULT \'\'';
                $this->deletetplfile('topic.tpl');
                $this->deletetplfile('topicslist.tpl');
                $this->deletetplfile('topicdata.tpl');
                $this->deletetplfile('topic_startdata.tpl');
                $this->deletetplfile('topicslist_startdata.tpl');
                $this->deletetplfile('topicnew.tpl');
                $this->deletetplfromview('topic');
                $this->deletetplfromview('topicslist');
                $this->deletetplfromview('topicdata');
                $this->deletetplfromview('topic_startdata');
                $this->deletetplfromview('topicslist_startdata');
                $this->deletetplfromview('topicnew');
            }
	        if($admindata['options'] == '1') {
                $datafields[] = 'optionids TEXT DEFAULT \'\'';
                $dataschedule[] = 'optionids';
            }
            else {
                $this->deletetplfile('options.tpl');
                $this->deletetplfromview('options');
	        }
			if($admindata['copydatatocat'] == '1' || $admindata['copydatatotopic'] == '1') {
				if($admindata['copydatatocat'] == '1') $this->modconf['copydatatocat'] = '1';
				if($admindata['copydatatotopic'] == '1') $this->modconf['copydatatotopic'] = '1';
				$datafields[] = 'copyof INTEGER DEFAULT \'\'';
				$dataschedule[] = 'copyof';
			}
			if($admindata['seodatas'] == '1' && $this->modconf['fulldata'] == '1') {
				$datafields[] = 'seotitle TEXT DEFAULT \'\'';
				$datafields[] = 'seodesc TEXT DEFAULT \'\'';
				$datafields[] = 'seokeys TEXT DEFAULT \'\'';
				$dataschedule[] = 'seotitle';
				$dataschedule[] = 'seodesc';
				$dataschedule[] = 'seokeys';
				$this->modconf['seodatas'] = 1;
			}
            $datafields[] = 'onoff INTEGER DEFAULT \'\'';
            $dataschedule[] = 'onoff';
            if($admindata['modcats'] == '' && $admindata['topics'] == '' && $admindata['bylang'] == '1' && $admindata['multilang'] != '1') {
                $datafields[] = 'lang TEXT DEFAULT \'\'';
                $dataschedule[] = 'lang';
            }
            if($admindata['datasort'] == '1') {
                $datafields[] = 'sort INTEGER DEFAULT \'\'';
                $dataschedule[] = 'sort';
                $this->modconf['datasort'] = 1;
            }
            if($admindata['datasort'] == '2')
                $this->modconf['datasort'] = 2;
        }
		
        foreach($this->filevars as $filevar) {
			if($filevar[3] == 'file') {
				$datafields[] = $filevar[1].'_counter INTEGER DEFAULT \'\'';
				$dataschedule[] = $filevar[1].'_counter';
			}
			if($filevar[3] == 'date' || $filevar[3] == 'state') {
				$datafields[] = $filevar[1].' INTEGER DEFAULT \'\'';
				$dataschedule[] = $filevar[1];
			}
			elseif($filevar[3] == 'number') {
				$datafields[] = $filevar[1].' NUMERIC DEFAULT \'\'';
				$dataschedule[] = $filevar[1];
			}
			elseif($filevar[3] == 'text' || $filevar[3] == 'area' || $filevar[3] == 'html' || $filevar[3] == 'bbcode' || $filevar[3] == 'hidden') {
				$datafields[] = $filevar[1].' TEXT COLLATE NOCASE DEFAULT \'\'';
				$dataschedule[] = $filevar[1];
			}
			else {
				$datafields[] = $filevar[1].' TEXT DEFAULT \'\'';
				$dataschedule[] = $filevar[1];
			}
			$fieldtypes[$filevar[3]][] = $filevar[1];
        }
        
        $this->schedule['data'] = $dataschedule;
        $this->types = $fieldtypes;
        
        $datapatterns = implode(', ', $datafields);
        $queries[] = 'CREATE TABLE datas (id INTEGER PRIMARY KEY, '.$datapatterns.')';
        
        if($admindata['modcats'] == '1' && $admindata['topics'] == '1') {
            $this->sublinks['cat_topic'] = '1';
        }
        elseif($admindata['modcats'] == '1' && $admindata['topics'] == '') {
            $this->sublinks['cat_list'] = '1';
        }
        if($admindata['topics'] == '1') {
            $this->sublinks['topic_list'] = '1';
        }
        
        $makemoddb = new SQLite3($this->dbpath.'/'.$this->modname.'/data/'.$this->modname.'.db');
        $makemoddb->query("BEGIN");
        foreach($queries as $query) {
            $makemoddb->query($query);
        }
        $makemoddb->query("COMMIT");
    }
    
    function setlangdefines()
    {
        $viewlangdir = $this->dbpath.'/'.$this->modname.'/view/lang';
        $handle = opendir($viewlangdir);
    	while(false !== ($file = readdir($handle))) {
            if(!is_dir($viewlangdir.'/'.$file) && $file != '.' && $file != '..') {
                $langfile = $viewlangdir.'/'.$file;
				$savelangs = file_get_contents($langfile);
				$savelangs = str_replace('MODMAKERLANG', strtoupper($this->modname.'lang'), $savelangs);
				$savelangs = str_replace("?".">", "define('_".strtoupper($this->modname)."LANG_MODNAME_','".$this->modname."');\n\n?".">", $savelangs);
				$savelangs = str_replace('###VIEW_LANGS###', implode("\n", $this->viewlangs), $savelangs);
				
				file_put_contents($langfile, $savelangs);
            }
        }
        closedir($handle);
		
		/*
        $langfile = $this->dbpath.'/'.$this->modname.'/view/lang/'.$_SESSION['loggedin']['userlang'].'.php';
        $savelangs = file_get_contents($langfile);
        $savelangs = str_replace('MODMAKERLANG', strtoupper($this->modname.'lang'), $savelangs);
        $savelangs = str_replace("?".">", "define('_".strtoupper($this->modname)."LANG_MODNAME_','".$this->modname."');\n\n?".">", $savelangs);
        $savelangs = str_replace('###VIEW_LANGS###', implode("\n", $this->viewlangs), $savelangs);
        
        file_put_contents($langfile, $savelangs);
		*/
    }
    
    function setconfig()
    {
        $conffile = $this->dbpath.'/'.$this->modname.'/data/config.php';
        $orgconfig = file_get_contents($conffile);
        $scheduler = array();
		$params = array();
        $optsizes = array();
        $catsizes = array();
        $imgsizes = array();
        
        $admindatas = $this->mmdb->query("SELECT id, projectid, modcats, cats, catopts, topics, bylang, multilang, byuser, catsort, catmenu, copytopictocat, topicsort, breaktopic, disttopicstart, copydatatocat, copydatatotopic, datasort, breakdata, options, seocats, seotopics, seodatas FROM admin WHERE admin.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        if($admindata = $admindatas->fetchArray()) {
            if($admindata['options'] == '1') {
                $optsizes[] = "\$".$this->modname."_conf['imgsize']['optimage']['view']['width'] = \"80\";";
                $optsizes[] = "\$".$this->modname."_conf['imgsize']['optimage']['view']['height'] = \"80\";";
            }
            if($admindata['modcats'] == '1') {
                if($admindata['catsort'] == 2) {
                    $scheduler[] = "\$".$this->modname."_conf['catsperpage'] = \"20\";";
                }
                if($this->schedule['cat'] != '') {
                    $schedule_cat = implode("', '", $this->schedule['cat']);
                    $scheduler[] = "\$".$this->modname."_conf['cat'] = array('".$schedule_cat."');";
					if(count($this->schedule['base']) > 0 && in_array('subcats', $this->schedule['base']))
						$params[] = '\'order\' => \''.$this->modname.'order\'';
					else
						$params[] = '\'cat\' => \''.$this->modname.'cat\'';
                }
                $catfields = explode('_', $admindata['cats']);
                if(in_array('catimage', $catfields)) {
                    $size = unserialize($admindata['catopts']);
                    $catsizes[] = "\$".$this->modname."_conf['imgsize']['catimage']['box']['width'] = \"".$size['cat']['box']['width']."\";";
                    $catsizes[] = "\$".$this->modname."_conf['imgsize']['catimage']['box']['height'] = \"".$size['cat']['box']['height']."\";";
                    $catsizes[] = "\$".$this->modname."_conf['imgsize']['catimage']['view']['width'] = \"".$size['cat']['view']['width']."\";";
                    $catsizes[] = "\$".$this->modname."_conf['imgsize']['catimage']['view']['height'] = \"".$size['cat']['view']['height']."\";";
                }
                /*
                if(in_array('cattext', $catfields)) {
                }
                */
            }
            if($admindata['topics'] == '1') {
                if($this->schedule['topic'] != '') {
                    $schedule_topic = implode("', '", $this->schedule['topic']);
                    $scheduler[] = "\$".$this->modname."_conf['topic'] = array('".$schedule_topic."');";
					$params[] = '\'topic\' => \''.$this->modname.'topic\'';
                }
            }
            if($admindata['seocats'] == '1' || $admindata['seotopics'] == '1' || $admindata['seodatas'] == '1') {
				$this->types['seo'][] = 'seotitle';
				$this->types['seo'][] = 'seodesc';
				$this->types['seo'][] = 'seokeys';
			}
        }
        
        foreach($this->filevars as $filevar) {
            if($filevar[3] == 'image' || $filevar[3] == 'multi') {
                $viewdatas = $this->mmdb->query("SELECT id, projectid, typeid, name, field, options FROM fields WHERE fields.id = '".$this->mmdb->escapeString($filevar[4])."' AND fields.projectid = '".$this->mmdb->escapeString($this->modid)."'");
                if($viewdata = $viewdatas->fetchArray()) {
                    $size = unserialize($viewdata['options']);
					if(substr(key($size), -1) != substr($filevar[1], -1)) {
						$newsize = array();
						$newsize[$filevar[1]] = $size[key($size)];
						unset($size);
						$size = $newsize;
						unset($newsize);
					}
                }
                $imgsizes[] = "\$".$this->modname."_conf['imgsize']['".$filevar[1]."']['box']['width'] = \"".$size[$filevar[1]]['box']['width']."\";";
                $imgsizes[] = "\$".$this->modname."_conf['imgsize']['".$filevar[1]."']['box']['height'] = \"".$size[$filevar[1]]['box']['height']."\";";
                $imgsizes[] = "\$".$this->modname."_conf['imgsize']['".$filevar[1]."']['view']['width'] = \"".$size[$filevar[1]]['view']['width']."\";";
                $imgsizes[] = "\$".$this->modname."_conf['imgsize']['".$filevar[1]."']['view']['height'] = \"".$size[$filevar[1]]['view']['height']."\";";
                $imgsizes[] = "\$".$this->modname."_conf['imgsize']['".$filevar[1]."']['full']['width'] = \"".$size[$filevar[1]]['full']['width']."\";";
                $imgsizes[] = "\$".$this->modname."_conf['imgsize']['".$filevar[1]."']['full']['height'] = \"".$size[$filevar[1]]['full']['height']."\";";
            }
            if($filevar[3] == 'bbcode') {
                $imgsizes[] = "\n\$".$this->modname."_conf['imgsize']['bbcode']['width'] = \"200\";";
            }
        }
        
        if(count($this->schedule['base']) > 0) {
            $schedule_base = implode("', '", $this->schedule['base']);
            $scheduler[] = "\$".$this->modname."_conf['base'] = array('".$schedule_base."');";
        }
        
        if($this->schedule['data'] != '') {
            $schedule_data = implode("', '", $this->schedule['data']);
            $scheduler[] = "\$".$this->modname."_conf['data'] = array('".$schedule_data."');";
			$params[] = '\'data\' => \''.$this->modname.'data\'';
        }
		
		if(isset($this->modconf['paramloadnew']) && $this->modconf['paramloadnew'] == 1) {
			$params[] = '\'load\' => \''.$this->modname.'load\'';
		}
        
        $this->getuserinputs();
        if(count($this->userinputs) >= 1) {
            $inputpattern = implode("', '", $this->userinputs);
            $scheduler[] = "\$".$this->modname."_conf['userinputs'] = array('".$inputpattern."');";
			$requiredpatterns = array();
			foreach($this->userinputs as $userinputfield) {
				if(!in_array($userinputfield, $this->notrequired))
					$requiredpatterns[] = $userinputfield;
			}
            $requiredpattern = implode("', '", $requiredpatterns);
            $scheduler[] = "\$".$this->modname."_conf['required'] = array('".$requiredpattern."');";
        }
        
        if(count($params) >= 1) {
			$scheduler[] = "\$".$this->modname."_conf['urlparams'] = array(".implode(', ', $params).");";
		}
        
        $saveconf = '';
        foreach($scheduler as $schedule) {
            $saveconf.= $schedule."\n\n";
        }
        
        foreach($this->types as $typesarr => $arr) {
            $savetypes[] = "'".$typesarr."' => '".implode(',', $arr)."'";
        }
        $saveconf.= "\$".$this->modname."_conf['types'] = array(".implode(', ', $savetypes).");\n\n\n";
        
        //$viewtplfiles = implode("', '", $this->viewtplfiles);
        //$saveconf.= "\n\$".$this->modname."_conf['templates'] = array('".$viewtplfiles."');\n\n";
        
        foreach($optsizes as $optsize) {
            $saveconf.= $optsize."\n";
        }
        $saveconf.= "\n";
        foreach($catsizes as $catsize) {
            $saveconf.= $catsize."\n";
        }
        $saveconf.= "\n";
        foreach($imgsizes as $imgsize) {
            $saveconf.= $imgsize."\n";
        }
        
        $orgconfig = str_replace("###MODULENAME###", $this->modname, $orgconfig);
        $orgconfig = str_replace("###MODCONFIG###", $saveconf, $orgconfig);
        
        file_put_contents($conffile, $orgconfig);
    }
    
    function getuserinputs()
    {
        $datas = $this->mmdb->query("SELECT id, projectid, tpldata FROM inputtpl WHERE inputtpl.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        if($data = $datas->fetchArray()) {
            if($data['tpldata'] != '') {
                $fields = explode('|', $data['tpldata']);
                if(is_array($fields)) {
                    foreach($this->filevars as $filevar) {
                        if(in_array($filevar[4], $fields)) {
                            //if($filevar[3] != 'date' && $filevar[3] != 'user' && $filevar[3] != 'hidden')
                            //if($filevar[3] != 'hidden')
                                $this->userinputs[] = $filevar[1];
                        }
                    }
                }
            }
            else {
                return false;
            }
        }
    }
    
    function makeadminmenu()
    {
        $adminmenu = '';
        
        $adminmenu.= ($this->modconf['subeditor'] == '1') ? '<?PHP if((isset($###MODULENAME###_class->dbconfig[\'hideforsubs\']) && $###MODULENAME###_class->dbconfig[\'hideforsubs\'] != \'hide\') || $webutlermodadmin->checkadmin()) { ?'.'>' : '';
        $adminmenu.= '<td><a href="admin.php?page=conf"><?PHP echo _MODMAKERLANGADMIN_SETTINGS_; ?'.'></a></td>'."\n";
        $adminmenu.= ($this->modconf['subeditor'] == '1') ? '<?PHP } ?'.'>' : '';
        
        if($this->adminvars['options'] != '') {
	        $adminmenu.= '<td><a href="admin.php?page=options"><?PHP echo _MODMAKERLANGADMIN_OPTIONS_; ?'.'></a></td>'."\n";
        }
        
        if($this->adminvars['cats'] != '') {
            $adminmenu.= '<td><a href="admin.php?page=cats"><?PHP echo _MODMAKERLANGADMIN_CATEGORIES_; ?'.'></a></td>';
			/*
            if($this->modconf['subcats'] == 1)
                $adminmenu.= '<td><a href="admin.php?page=subcats"><?PHP echo _MODMAKERLANGADMIN_SUBCATEGORIES_; ?'.'></a></td>';
			*/
            $adminmenu.= "\n";
        }
        
        if($this->adminvars['topics'] != '') {
            if($this->adminvars['cats'] != '') {
                $adminmenu.= '<?PHP if($_GET[\'page\'] == \'topics\' || $_GET[\'page\'] == \'datas\') { ?'.'>
                    <td><a href="admin.php?page=topics&cat=<?PHP echo $_GET[\'cat\']; ?'.'>"><?PHP echo _MODMAKERLANGADMIN_TOPICS_; ?'.'></a></td>
                    <?PHP if($_GET[\'page\'] == \'datas\') { ?'.'>
                    <td><a href="admin.php?page=datas&cat=<?PHP echo $_GET[\'cat\']; ?'.'>&topic=<?PHP echo $_GET[\'topic\']; ?'.'>"><?PHP echo _MODMAKERLANGADMIN_DATAS_; ?'.'></a></td>
                    <?PHP } } ?'.'>'."\n";
            }
            else {
                $adminmenu.= '<td><a href="admin.php?page=topics"><?PHP echo _MODMAKERLANGADMIN_TOPICS_; ?'.'></a></td>
                    <?PHP if(isset($_GET[\'page\']) && $_GET[\'page\'] == \'datas\') { ?'.'>
                    <td><a href="admin.php?page=datas&topic=<?PHP echo $_GET[\'topic\']; ?'.'>"><?PHP echo _MODMAKERLANGADMIN_DATAS_; ?'.'></a></td>
                    <?PHP } ?'.'>'."\n";
            }
        }
        else {
            if($this->adminvars['cats'] != '') {
                $adminmenu.= '<?PHP if($_GET[\'page\'] == \'datas\') { ?'.'>
                    <td><a href="admin.php?page=datas&cat=<?PHP echo $_GET[\'cat\']; ?'.'>"><?PHP echo _MODMAKERLANGADMIN_DATAS_; ?'.'></a></td>
                    <?PHP } ?'.'>'."\n";
            }
            else {
                $adminmenu.= '<td><a href="admin.php?page=datas"><?PHP echo _MODMAKERLANGADMIN_DATAS_; ?'.'></a></td>'."\n";
            }
        }
		if($this->modconf['subeditor'] == '1') {
            $adminmenu.= '<td><a href="admin.php?logout=1"><?PHP echo _MODMAKERLANGADMIN_LOGOUT_; ?'.'></a></td>'."\n";
		}
        
        return $adminmenu;
    }
    
    function getlangselection()
    {
        if($this->modconf['getlangs'] == '1') {
            $langs[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_LANGUAGE_; ?'.'>';
            $langs[1] = '<select name="lang" size="1">
                <?PHP echo $###MODULENAME###_class->getlanguages(isset($db_data[\'lang\']) ? $db_data[\'lang\'] : \'\'); ?'.'>
                </select>';
            
            $source[] = $langs;
            return $source;
        }
        return NULL;
    }
    
    function getbreaktime()
    {
        if($this->modconf['breaktopic'] == '1' || $this->modconf['breakdata'] == '1') {
            $break[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_DISPLAY_; ?'.'>';
			$breakopen = '';
			$breakclose = '';
			if($this->modconf['breakdata'] == '1' && $this->adminvars['topics'] != '' && $this->modconf['disttopicstart'] == '1') {
				$breakopen = "\n".'<?PHP if($###MODULENAME###_class->istopicstartid()) {'."\n";
				$breakopen.= '	echo _MODMAKERLANGADMIN_FIELD_ISSTARTDATA_;'."\n";
				$breakopen.= '} else { ?'.'>'."\n";
				$breakclose = "\n".'<?PHP } ?'.'>'."\n";
			}
			$break[1] = $breakopen;
            $break[1].= '<span><?PHP echo _MODMAKERLANGADMIN_FIELD_DISPLAYFROM_; ?'.'>: <input type="text" name="fromtime" id="DPC_fromtime" value="<?PHP echo (isset($db_data[\'fromtime\']) && $db_data[\'fromtime\'] != \'\') ? strftime(\'%Y-%m-%d\', $db_data[\'fromtime\']) : \'\'; ?'.'>" style="width: 80px" size="10" maxlength="10" readonly="readonly" /></span>
                <span style="margin-left: 20px"><?PHP echo _MODMAKERLANGADMIN_FIELD_DISPLAYTO_; ?'.'>: <input type="text" name="totime" id="DPC_totime" value="<?PHP echo (isset($db_data[\'totime\']) && $db_data[\'totime\'] != \'\') ? strftime(\'%Y-%m-%d\', $db_data[\'totime\']) : \'\'; ?'.'>" style="width: 80px" size="10" maxlength="10" readonly="readonly" /><img src="admin/icons/delete.png" title="<?PHP echo _MODMAKERLANGADMIN_FIELD_CLEARDATE_; ?'.'>" class="cleardate" onclick="cleardatefields()" /></span>';
			$break[1].= $breakclose;
            
            $source[] = $break;
            
            $breakjs = '<script>'."\n".
                '/* <![CDATA[ */'."\n".
                '    var homepagepath = \'<?PHP echo $webutlermodadmin->config[\'homepage\']; ?'.'>\';'."\n".
                '/* ]]> */'."\n".
                '</script>'."\n".
                '<script src="<?PHP echo $webutlermodadmin->config[\'homepage\']; ?'.'>/includes/modexts/calendar/datepickercontrol.js"></script>';
            if(!in_array($breakjs, $this->adminjs))
                $this->adminjs[] = $breakjs;
            
            return $source;
        }
        return NULL;
    }
    
    function getmoduleconfig()
    {
        if($this->modconf['subeditor'] == '1') {
            $subeditors[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_LOGINFORSUBS_; ?'.'>';
            $subeditors[1] = '<strong class="isfor"><?PHP echo _MODMAKERLANGADMIN_FIELD_USERSASSUBS_; ?'.'></strong>
				<select name="config[subeditors]" size="1">
                <option value="no"<?PHP echo $###MODULENAME###_class->setnousersselection(isset($###MODULENAME###_class->dbconfig[\'subeditors\']) ? $###MODULENAME###_class->dbconfig[\'subeditors\'] : \'\'); ?'.'>><?PHP echo _MODMAKERLANGADMIN_FIELD_NOSUBEDITOR_; ?'.'></option>
                <?PHP echo $###MODULENAME###_class->getusergroupids(isset($###MODULENAME###_class->dbconfig[\'subeditors\']) ? $###MODULENAME###_class->dbconfig[\'subeditors\'] : \'\'); ?'.'>
                </select><br />
				<input type="checkbox" name="config[hideforsubs]" style="width: 15px" id="hideforsubs" value="hide"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'hideforsubs\']) && $###MODULENAME###_class->dbconfig[\'hideforsubs\'] == \'hide\') echo \' checked="checked"\'; ?'.'> /><label for="hideforsubs"><?PHP echo _MODMAKERLANGADMIN_FIELD_HIDEFORSUBS_; ?'.'></label>';
            $subeditors[2] = 'onlyadmin';
            $source[] = $subeditors;
        }
        
        $viewperpage[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_SETPERPAGE_; ?'.'>';
        $viewperpage[1] = '<table class="setperpage" border="0" cellspacing="0" cellpadding="0">';
		if($this->adminvars['cats'] != '' && $this->modconf['subcats'] != 1) {
            $viewperpage[1].= '<tr><td style="border-top: 0px"><strong><?PHP echo _MODMAKERLANGADMIN_CATEGORIES_; ?'.'>:</strong></td><td style="border-top: 0px"><input type="text" name="config[catsperpage]" style="width: 40px" value="<?PHP if(isset($###MODULENAME###_class->dbconfig[\'catsperpage\'])) echo $###MODULENAME###_class->dbconfig[\'catsperpage\']; ?'.'>" /> <?PHP echo _MODMAKERLANGADMIN_FIELD_DATAPERPAGE_; ?'.'></td></tr>';
		}
		if($this->adminvars['topics'] != '') {
            $topicborder = ($this->adminvars['cats'] != '' && $this->modconf['subcats'] != 1) ? '' : ' style="border-top: 0px"';
	        $viewperpage[1].= '<tr><td'.$topicborder.'><strong><?PHP echo _MODMAKERLANGADMIN_TOPICS_; ?'.'>:</strong></td><td'.$topicborder.'><input type="text" name="config[topicsperpage]" style="width: 40px" value="<?PHP if(isset($###MODULENAME###_class->dbconfig[\'topicsperpage\'])) echo $###MODULENAME###_class->dbconfig[\'topicsperpage\']; ?'.'>" /> <?PHP echo _MODMAKERLANGADMIN_FIELD_DATAPERPAGE_; ?'.'></td></tr>';
		}
        $databorder = (($this->adminvars['cats'] != '' && $this->modconf['subcats'] != 1) || $this->adminvars['topics'] != '') ? '' : ' style="border-top: 0px"';
        $viewperpage[1].= '<tr><td'.$databorder.'><strong><?PHP echo _MODMAKERLANGADMIN_DATAS_; ?'.'>:</strong></td><td'.$databorder.' width="100%"><input type="text" name="config[datasperpage]" style="width: 40px" value="<?PHP if(isset($###MODULENAME###_class->dbconfig[\'datasperpage\'])) echo $###MODULENAME###_class->dbconfig[\'datasperpage\']; ?'.'>" /> <?PHP echo _MODMAKERLANGADMIN_FIELD_DATAPERPAGE_; ?'.'></td></tr></table>';
        $source[] = $viewperpage;
		
		$newcreated[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_NEWCREATED_; ?'.'>';
		$newcreated[1] = '<table class="createdconf" border="0" cellspacing="0" cellpadding="0"><tbody>';
		if($this->adminvars['cats'] != '') {
			$newcreated[1].= '<tr><td><strong><?PHP echo _MODMAKERLANGADMIN_CATEGORIES_; ?'.'>:</strong></td><td>
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
				<td><input type="radio" name="config[newcreated_cat]" value="online" style="width: 12px" id="newcreated_cat_online"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'newcreated_cat\']) && $###MODULENAME###_class->dbconfig[\'newcreated_cat\'] == \'online\') echo \' checked="checked"\'; ?'.'> /><label for="newcreated_cat_online"><?PHP echo _MODMAKERLANGADMIN_BUTTON_ONLINE_; ?'.'></label></td>
				<td><input type="radio" name="config[newcreated_cat]" value="offline" style="width: 12px" id="newcreated_cat_offline"<?PHP if(!isset($###MODULENAME###_class->dbconfig[\'newcreated_cat\']) || (isset($###MODULENAME###_class->dbconfig[\'newcreated_cat\']) && $###MODULENAME###_class->dbconfig[\'newcreated_cat\'] == \'offline\')) echo \' checked="checked"\'; ?'.'> /><label for="newcreated_cat_offline"><?PHP echo _MODMAKERLANGADMIN_BUTTON_OFFLINE_; ?'.'></label></td>
				</tr>
				</table>
				</td>
				</tr>';
		}
		if($this->adminvars['topics'] != '') {
			$newcreated[1].= '<tr><td><strong><?PHP echo _MODMAKERLANGADMIN_TOPICS_; ?'.'>:</strong></td><td>
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
				<td><input type="radio" name="config[newcreated_topic]" value="online" style="width: 12px" id="newcreated_topic_online"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'newcreated_topic\']) && $###MODULENAME###_class->dbconfig[\'newcreated_topic\'] == \'online\') echo \' checked="checked"\'; ?'.'> /><label for="newcreated_topic_online"><?PHP echo _MODMAKERLANGADMIN_BUTTON_ONLINE_; ?'.'></label></td>
				<td><input type="radio" name="config[newcreated_topic]" value="offline" style="width: 12px" id="newcreated_topic_offline"<?PHP if(!isset($###MODULENAME###_class->dbconfig[\'newcreated_topic\']) || (isset($###MODULENAME###_class->dbconfig[\'newcreated_topic\']) && $###MODULENAME###_class->dbconfig[\'newcreated_topic\'] == \'offline\')) echo \' checked="checked"\'; ?'.'> /><label for="newcreated_topic_offline"><?PHP echo _MODMAKERLANGADMIN_BUTTON_OFFLINE_; ?'.'></label></td>
				</tr>
				</table>
				</td>
				</tr>';
		}
		$newcreated[1].= '<tr><td><strong><?PHP echo _MODMAKERLANGADMIN_DATAS_; ?'.'>:</strong></td><td>
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td><input type="radio" name="config[newcreated_data]" value="online" style="width: 12px" id="newcreated_data_online"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'newcreated_data\']) && $###MODULENAME###_class->dbconfig[\'newcreated_data\'] == \'online\') echo \' checked="checked"\'; ?'.'> /><label for="newcreated_data_online"><?PHP echo _MODMAKERLANGADMIN_BUTTON_ONLINE_; ?'.'></label></td>
			<td><input type="radio" name="config[newcreated_data]" value="offline" style="width: 12px" id="newcreated_data_offline"<?PHP if(!isset($###MODULENAME###_class->dbconfig[\'newcreated_data\']) || (isset($###MODULENAME###_class->dbconfig[\'newcreated_data\']) && $###MODULENAME###_class->dbconfig[\'newcreated_data\'] == \'offline\')) echo \' checked="checked"\'; ?'.'> /><label for="newcreated_data_offline"><?PHP echo _MODMAKERLANGADMIN_BUTTON_OFFLINE_; ?'.'></label></td>
			</tr>
			</table>
			</td>
			</tr>
			</tbody>
			</table>';
		$source[] = $newcreated;
        
        if(($this->modconf['catsort'] != '' && $this->modconf['catsort'] != '1' && $this->modconf['subcats'] == '') || ($this->adminvars['topics'] != '' && $this->modconf['topicsort'] != '1') || $this->modconf['datasort'] != 1) {
            $sorting[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_SORTING_; ?'.'>';
            $sorting[1] = '<table class="sortconf" border="0" cellspacing="0" cellpadding="0">';
            if($this->adminvars['cats'] != '' && $this->modconf['catsort'] != '1' && $this->modconf['subcats'] == '') {
                $sorting[1].= '<tr><td style="border-top: 0px"><strong><?PHP echo _MODMAKERLANGADMIN_CATEGORIES_; ?'.'>:</strong></td>
                    <td style="border-top: 0px"><input type="radio" name="config[catsort]" value="DESC" style="width: 12px" id="sorting_catdesc"<?PHP if(!isset($###MODULENAME###_class->dbconfig[\'catsort\']) || $###MODULENAME###_class->dbconfig[\'catsort\'] == \'DESC\') echo \' checked="checked"\'; ?'.'> /><label for="sorting_catdesc"><?PHP echo _MODMAKERLANGADMIN_FIELD_SORTNEWFIRST_; ?'.'></label></td>
                    <td width="100%" style="border-top: 0px"><input type="radio" name="config[catsort]" value="ASC" style="width: 12px" id="sorting_catasc"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'catsort\']) && $###MODULENAME###_class->dbconfig[\'catsort\'] == \'ASC\') echo \' checked="checked"\'; ?'.'> /><label for="sorting_catasc"><?PHP echo _MODMAKERLANGADMIN_FIELD_SORTOLDFIRST_; ?'.'></label></td>
                    </tr>';
            }
            
            if($this->adminvars['topics'] != '' && $this->modconf['topicsort'] != '1') {
                $topicrows = ($this->modconf['topicsort'] == 2) ? ' rowspan="2"' : '';
				$topicstylevars = array();
                $topicstylevars[] = ($this->adminvars['cats'] != '' && $this->modconf['catsort'] != '1' && $this->modconf['subcats'] == '') ? '' : 'border-top: 0px';
				$topicstylevars[] = ($this->modconf['topicsort'] == 2) ? 'padding-bottom: 0px' : '';
				$topicstyle = (count($topicstylevars) > 0) ? ' style="'.implode('; ', $topicstylevars).'"' : '';
                $sorting[1].= '<tr><td'.$topicrows.$topicstyle.'><strong><?PHP echo _MODMAKERLANGADMIN_TOPICS_; ?'.'>:</strong></td>
                    <td'.$topicstyle.'><input type="radio" name="config[topicsort]" value="DESC" style="width: 12px" id="sorting_topicdesc"<?PHP if(!isset($###MODULENAME###_class->dbconfig[\'topicsort\']) || $###MODULENAME###_class->dbconfig[\'topicsort\'] == \'DESC\') echo \' checked="checked"\'; ?'.'> /><label for="sorting_topicdesc"><?PHP echo _MODMAKERLANGADMIN_FIELD_SORTNEWFIRST_; ?'.'></label></td>
                    <td width="100%"'.$topicstyle.'><input type="radio" name="config[topicsort]" value="ASC" style="width: 12px" id="sorting_topicasc"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'topicsort\']) && $###MODULENAME###_class->dbconfig[\'topicsort\'] == \'ASC\') echo \' checked="checked"\'; ?'.'> /><label for="sorting_topicasc"><?PHP echo _MODMAKERLANGADMIN_FIELD_SORTOLDFIRST_; ?'.'></label></td>
                    </tr>';
                if($this->modconf['topicsort'] == 2) {
                    $sorting[1].= '<tr><td colspan="2" class="sortbyfield"><?PHP echo _MODMAKERLANGADMIN_FIELD_SORTBYFIELD_; ?'.'>:<select name="config[sorttopicfield]" size="1">
                            <?PHP echo $###MODULENAME###_class->datafieldsforsort(\'topic\'); ?'.'>
                        </select></td></tr>';
                }
            }
            
            if($this->modconf['datasort'] != '1') {
                $datarows = ($this->modconf['datasort'] == 2) ? ' rowspan="2"' : '';
				$datastylevars = array();
                $datastylevars[] = (($this->adminvars['cats'] != '' && $this->modconf['catsort'] != '1' && $this->modconf['subcats'] == '') || ($this->adminvars['topics'] != '' && $this->modconf['topicsort'] != '1')) ? '' : 'border-top: 0px';
				$datastylevars[] = ($this->modconf['datasort'] == 2) ? 'padding-bottom: 0px' : '';
				$datastyle = (count($datastylevars) > 0) ? ' style="'.implode('; ', $datastylevars).'"' : '';
                $sorting[1].= '<tr><td'.$datarows.$datastyle.'><strong><?PHP echo _MODMAKERLANGADMIN_DATAS_; ?'.'>:</strong></td>
                    <td'.$datastyle.'><input type="radio" name="config[datasort]" value="DESC" style="width: 12px" id="sorting_datadesc"<?PHP if(!isset($###MODULENAME###_class->dbconfig[\'datasort\']) || $###MODULENAME###_class->dbconfig[\'datasort\'] == \'DESC\') echo \' checked="checked"\'; ?'.'> /><label for="sorting_datadesc"><?PHP echo _MODMAKERLANGADMIN_FIELD_SORTNEWFIRST_; ?'.'></label></td>
                    <td width="100%"'.$datastyle.'><input type="radio" name="config[datasort]" value="ASC" style="width: 12px" id="sorting_dataasc"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'datasort\']) && $###MODULENAME###_class->dbconfig[\'datasort\'] == \'ASC\') echo \' checked="checked"\'; ?'.'> /><label for="sorting_dataasc"><?PHP echo _MODMAKERLANGADMIN_FIELD_SORTOLDFIRST_; ?'.'></label></td>
                    </tr>';
                if($this->modconf['datasort'] == 2) {
                    $sorting[1].= '<tr><td colspan="2" class="sortbyfield"><?PHP echo _MODMAKERLANGADMIN_FIELD_SORTBYFIELD_; ?'.'>:<select name="config[sortdatafield]" size="1">
                            <?PHP echo $###MODULENAME###_class->datafieldsforsort(\'data\'); ?'.'>
                        </select></td></tr>';
                }
            }
            
            $sorting[1].= '</table>';
            $source[] = $sorting;
        }
        
        if($this->adminvars['catmenu'] != '' && $this->adminvars['catmenu'] == '1') {
            $onlymenu[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_CATMENU_; ?'.'>';
            $onlymenu[1] = '<input type="checkbox" name="config[catonlymenu]" value="show" style="width: 12px" id="catonlymenu"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'catonlymenu\']) && $###MODULENAME###_class->dbconfig[\'catonlymenu\'] == \'show\') echo \' checked="checked"\'; ?'.'> /><label for="catonlymenu"><?PHP echo _MODMAKERLANGADMIN_FIELD_ONLYMENU_; ?'.'></label>';
            $source[] = $onlymenu;
        }
        
        if($this->adminvars['topics'] != '' && $this->modconf['disttopicstart'] == '1') {
            $startdata[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_STARTONTOPIC_; ?'.'>';
            $startdata[1] = '<input type="checkbox" name="config[startontopiclist]" value="show" style="width: 12px" id="showstartontopiclist"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'startontopiclist\']) && $###MODULENAME###_class->dbconfig[\'startontopiclist\'] == \'show\') echo \' checked="checked"\'; ?'.'> /><label for="showstartontopiclist"> <?PHP echo _MODMAKERLANGADMIN_FIELD_SHOWINLIST_; ?'.'></label><br />';
            $startdata[1].= '<input type="checkbox" name="config[startontopic]" value="show" style="width: 12px" id="showstartontopic"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'startontopic\']) && $###MODULENAME###_class->dbconfig[\'startontopic\'] == \'show\') echo \' checked="checked"\'; ?'.'> /><label for="showstartontopic"><?PHP echo _MODMAKERLANGADMIN_FIELD_SHOWINTOPIC_; ?'.'></label>';
			if($this->modconf['filter'] == '1') {
				$startdata[1].= '<br /><input type="checkbox" name="config[startontopicfilter]" value="no" style="width: 12px" id="startontopicfilter"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'startontopicfilter\']) && $###MODULENAME###_class->dbconfig[\'startontopicfilter\'] == \'no\') echo \' checked="checked"\'; ?'.'> /><label for="startontopicfilter"><?PHP echo _MODMAKERLANGADMIN_FIELD_SHOWINTOPICFILTER_; ?'.'></label>';
			}
			if($this->getfieldsbytpl('fulltpl')) {
				$startdata[1].= '<br /><input type="checkbox" name="config[prevnextnavi]" value="none" style="width: 12px" id="noneprevnextnavi"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'prevnextnavi\']) && $###MODULENAME###_class->dbconfig[\'prevnextnavi\'] == \'none\') echo \' checked="checked"\'; ?'.'> /><label for="noneprevnextnavi"><?PHP echo _MODMAKERLANGADMIN_FIELD_DATAPREVNEXTNAVI_; ?'.'></label>';
			}
            $source[] = $startdata;
        }
        
        if($this->modconf['copytopictocat'] == '1' || $this->modconf['copydatatocat'] == '1' || $this->modconf['copydatatotopic'] == '1') {
            $copiesonoff[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_COPYHANDLING_; ?'.'>';
			$copiesonoff[1] = '';
			if($this->modconf['copytopictocat'] == '1') {
				$copiesonoff[1].= '<input type="checkbox" name="config[topiccopiesonoff]" value="yes" style="width: 12px" id="topiccopiesonoff"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'topiccopiesonoff\']) && $###MODULENAME###_class->dbconfig[\'topiccopiesonoff\'] == \'yes\') echo \' checked="checked"\'; ?'.'> /><label for="topiccopiesonoff"><?PHP echo _MODMAKERLANGADMIN_FIELD_TOPICCOPIES_; ?'.'></label>';
			}
			if($this->modconf['copytopictocat'] == '1' && ($this->modconf['copydatatocat'] == '1' || $this->modconf['copydatatotopic'] == '1')) {
				$copiesonoff[1].= '<br />';
			}
			if($this->modconf['copydatatocat'] == '1' || $this->modconf['copydatatotopic'] == '1') {
				$copiesonoff[1].= '<input type="checkbox" name="config[datacopiesonoff]" value="yes" style="width: 12px" id="datacopiesonoff"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'datacopiesonoff\']) && $###MODULENAME###_class->dbconfig[\'datacopiesonoff\'] == \'yes\') echo \' checked="checked"\'; ?'.'> /><label for="datacopiesonoff"><?PHP echo _MODMAKERLANGADMIN_FIELD_DATACOPIES_; ?'.'></label>';
			}
            $source[] = $copiesonoff;
        }
        
        if($this->modconf['newest'] == '1') {
            $numbnewest[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_NUMBNEWEST_; ?'.'>';
            $numbnewest[1] = '<input type="text" value="<?PHP echo $###MODULENAME###_class->dbconfig[\'numbnewest\']; ?'.'>" style="width: 40px" name="config[numbnewest]" /> <?PHP echo _MODMAKERLANGADMIN_FIELD_NEWESTINBLOCK_; ?'.'>';
            $source[] = $numbnewest;
        }
        
        if($this->modconf['filter'] == '1') {
            $filtermaintain[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_FILTER_; ?'.'>';
            $filtermaintain[1] = '<input type="checkbox" name="config[filtermaintain]" value="yes" style="width: 12px" id="filtermaintain"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'filtermaintain\']) && $###MODULENAME###_class->dbconfig[\'filtermaintain\'] == \'yes\') echo \' checked="checked"\'; ?'.'> /><label for="filtermaintain"><?PHP echo _MODMAKERLANGADMIN_FIELD_FILTERMAINTAIN_; ?'.'></label>';
            $source[] = $filtermaintain;
        }
        
        if(($this->adminvars['cats'] != '' && $this->modconf['seocats'] != '1') || ($this->adminvars['topics'] != '' && $this->modconf['seotopics'] != '1') || ($this->modconf['fulldata'] == '1' && $this->modconf['seodatas'] != '1')) {
			$whatfields = array();
			if($this->adminvars['cats'] != '' && $this->modconf['seocats'] != '1')
				$whatfields[] = '_MODMAKERLANGADMIN_HEADLINETITLE_CAT_';
			if($this->adminvars['topics'] != '' && $this->modconf['seotopics'] != '1')
				$whatfields[] = '_MODMAKERLANGADMIN_HEADLINETITLE_TOPIC_';
			if($this->modconf['fulldata'] == '1' && $this->modconf['seodatas'] != '1')
				$whatfields[] = '_MODMAKERLANGADMIN_HEADLINETITLE_DATA_';
			
			$langfields = implode('.\', \'.', $whatfields);
			
            $seoheadline[0] = '<?PHP echo _MODMAKERLANGADMIN_HEADLINETITLE_SEO_; ?'.'>';
            $seoheadline[1] = '<input type="checkbox" name="config[headlinetitle]" value="show" style="width: 12px" id="showheadlinetitle"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'headlinetitle\']) && $###MODULENAME###_class->dbconfig[\'headlinetitle\'] == \'show\') echo \' checked="checked"\'; ?'.'> /><label for="showheadlinetitle"><?PHP echo _MODMAKERLANGADMIN_HEADLINETITLE_SHOW_.\' (\'.'.$langfields.'.\')\'; ?'.'></label>';
            $source[] = $seoheadline;
        }
        
        if($this->modconf['getusers'] == '1') {
            $getusers[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_PERMISSION_; ?'.'>';
            $getusers[1] = '<strong class="isfor"><?PHP echo _MODMAKERLANGADMIN_FIELD_SELUSERGROUP_; ?'.'>:</strong>
                <select name="config[groups][]" size="5" multiple="multiple">
                <option value="all"<?PHP echo $###MODULENAME###_class->setallusersselection(isset($###MODULENAME###_class->dbconfig[\'groups\']) ? $###MODULENAME###_class->dbconfig[\'groups\'] : \'\'); ?'.'>><?PHP echo _MODMAKERLANGADMIN_FIELD_NOUSERGROUP_; ?'.'></option>
                <?PHP echo $###MODULENAME###_class->getusergroupids(isset($###MODULENAME###_class->dbconfig[\'groups\']) ? $###MODULENAME###_class->dbconfig[\'groups\'] : \'\'); ?'.'>
                </select><br />
                <?PHP echo _MODMAKERLANGADMIN_FIELD_MULTISELECT_; ?'.'>'."\n";
            $source[] = $getusers;
		}
		
        if($this->modconf['setbyuser'] == '1') {
            $releaseusers[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_RELEASING_; ?'.'>';
            $releaseusers[1] = '<strong><?PHP echo _MODMAKERLANGADMIN_FIELD_USERINPUTS_; ?'.'>:</strong>
                <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td><input type="radio" name="config[release]" value="auto" style="width: 12px" id="release_auto"<?PHP if(!isset($###MODULENAME###_class->dbconfig[\'release\']) || $###MODULENAME###_class->dbconfig[\'release\'] == \'auto\') echo \' checked="checked"\'; ?'.'> /><label for="release_auto"><?PHP echo _MODMAKERLANGADMIN_FIELD_RELEASEDIRECT_; ?'.'></label></td>
                <td><input type="radio" name="config[release]" value="hand" style="width: 12px" id="release_hand"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'release\']) && $###MODULENAME###_class->dbconfig[\'release\'] == \'hand\') echo \' checked="checked"\'; ?'.'> /><label for="release_hand"><?PHP echo _MODMAKERLANGADMIN_FIELD_RELEASEBYHAND_; ?'.'></label></td>
                </tr>
                </table>';
            $source[] = $releaseusers;
        }
        
        $setlightbox = 'false';
		foreach($this->filevars as $filevar) {
			if($filevar[3] == 'image' || $filevar[3] == 'multi') {
				$setlightbox = 'true';
				break;
			}
		}
        
		$checkcatimg = $this->mmdb->query("SELECT id, projectid, cats FROM admin WHERE admin.projectid = '".$this->mmdb->escapeString($this->modid)."'");
		$check = $checkcatimg->fetchArray();
		$fields = explode('_', $check['cats']);
		if($setlightbox == 'false' && in_array('catimage', $fields)) {
			$setlightbox = 'true';
		}
        
        if($setlightbox == 'true' && $this->modconf['autolightbox'] == 1) {
            $lightbox[0] = '<?PHP echo _MODMAKERLANGADMIN_FIELD_BOXCOMMON_; ?'.'>';
            $lightbox[1] = '<input type="radio" name="config[lightboxlistdata]" value="nodatas" ';
			if($this->modconf['fulldata'] == '1')
				$lightbox[1].= 'onclick="checkonlyfull()" ';
			$lightbox[1].= 'style="width: 12px" id="nodatas"<?PHP if(!isset($###MODULENAME###_class->dbconfig[\'lightboxlistdata\']) || $###MODULENAME###_class->dbconfig[\'lightboxlistdata\'] == \'nodatas\') echo \' checked="checked"\'; ?'.'> /><label for="nodatas"><?PHP echo _MODMAKERLANGADMIN_FIELD_BOXSINGLE_; ?'.'></label><br />
                <input type="radio" name="config[lightboxlistdata]" value="onedatas" ';
			if($this->modconf['fulldata'] == '1')
				$lightbox[1].= 'onclick="checkonlyfull()" ';
			$lightbox[1].= 'style="width: 12px" id="onedatas"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'lightboxlistdata\']) && $###MODULENAME###_class->dbconfig[\'lightboxlistdata\'] == \'onedatas\') echo \' checked="checked"\'; ?'.'> /><label for="onedatas"><?PHP echo _MODMAKERLANGADMIN_FIELD_BOXDATASTEP_; ?'.'></label><br />
                <input type="radio" name="config[lightboxlistdata]" value="alldatas" ';
			if($this->modconf['fulldata'] == '1')
				$lightbox[1].= 'onclick="checkonlyfull()" <?PHP if(isset($###MODULENAME###_class->dbconfig[\'lightboxonlyfull\']) && $###MODULENAME###_class->dbconfig[\'lightboxonlyfull\'] == \'onlyfull\') echo \'disabled="disabled" \'; ?'.'>';
			$lightbox[1].= 'style="width: 12px" id="alldatas"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'lightboxlistdata\']) && $###MODULENAME###_class->dbconfig[\'lightboxlistdata\'] == \'alldatas\') echo \' checked="checked"\'; ?'.'> /><label for="alldatas"><?PHP echo _MODMAKERLANGADMIN_FIELD_BOXFULLSTEP_; ?'.'></label>';
			if($this->modconf['fulldata'] == '1') {
				$lightbox[1].= '<br /><input type="checkbox" name="config[lightboxonlyfull]" value="onlyfull" onclick="checkonlyfull()" style="width: 12px" id="onlyfull"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'lightboxonlyfull\']) && $###MODULENAME###_class->dbconfig[\'lightboxonlyfull\'] == \'onlyfull\') echo \' checked="checked"\'; ?'.'>  <?PHP if(isset($###MODULENAME###_class->dbconfig[\'lightboxlistdata\']) && $###MODULENAME###_class->dbconfig[\'lightboxlistdata\'] == \'alldatas\') echo \'disabled="disabled" \'; ?'.'> /><label for="onlyfull"><?PHP echo _MODMAKERLANGADMIN_FIELD_BOXONLYONFULL_; ?'.'></label>';
			}
            $source[] = $lightbox;
			
			if(in_array('catimage', $fields)) {
				$catlightbox[0] = '<?PHP echo _MODMAKERLANGADMIN_CATEGORY_; ?'.'>';
				$catlightbox[1] = '<input type="checkbox" name="config[lightbox][catimage]"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'lightbox\'][\'catimage\']) && $###MODULENAME###_class->dbconfig[\'lightbox\'][\'catimage\'] == \'on\') echo \' checked="checked"\'; ?'.'> style="width: 12px" id="lightbox_catimage" /><label for="lightbox_catimage"><?PHP echo _MODMAKERLANGADMIN_FIELD_BOXOPEN_; ?'.'></label>';
				$source[] = $catlightbox;
			}
            
			$this->lightboxjs = 'if(array_key_exists(\'lightbox\', $###MODULENAME###_class->dbconfig)) {'."\n";
			$this->lightboxjs.= '		$webutlercouple->autoheaderdata[] = \'<script src="\'.$webutlercouple->config[\'homepage\'].\'/includes/javascript/lightbox/lightbox_plus.js"></script>\';'."\n";
            $this->lightboxjs.= '	}'."\n";
			
            /*
            if(!in_array($lightboxjs, $this->viewjs))
                array_unshift($this->viewjs, $lightboxjs);
            */
        }
        
        return $source;
    }
    
    function setfieldstoadmin()
    {
        $adminmenu = $this->makeadminmenu();
        $admindata = $this->getfieldsbytpl('admindata');
        $moduleconfig = $this->getmoduleconfig();
        $adminconfig = $this->getfieldsbytpl('adminconf');
//print_r($adminconfig);
        $adminconf = array_merge($moduleconfig, $adminconfig);
        $confelements = $this->makeelementlines($adminconf, false);
        $catlistcolspan = ($this->modconf['catsort'] == '1') ? '4' : '3';
        $topiclistcolspan = ($this->modconf['topicsort'] == '1') ? '4' : '3';
        $datalistcolspan = ($this->modconf['datasort'] == '1') ? '4' : '3';
        
		$viewjs = (count($this->viewjs) >= 1) ? implode("\n", $this->viewjs) : '';
		$this->savetplfiles('view.php', '###INPUTJAVASCRIPT###', $viewjs);
        $this->savetplfiles('view.php', '###LIGHTBOXJAVASCRIPT###', $this->lightboxjs);
		
		if($this->modconf['userinput'] == 1) {
			$userinputinit = '(isset($_POST[\'###MODULENAME###postsave\'])) ? new MMInputClass : ';
			$userinputaction = '	if(isset($_POST[\'###MODULENAME###postsave\'])';
			if($this->modconf['getusers'] == 1)
				$userinputaction.= ' && ($###MODULENAME###_class->userisadmin || $###MODULENAME###_class->getwritepermission())';
			$userinputaction.= ') {'."\n";
			$userinputaction.= '		$###MODULENAME###_class->post = $_POST;'."\n";
			if($this->modconf['userinput_hasfiles'] == 1) {
				$userinputaction.= '		$###MODULENAME###_class->files = $_FILES;'."\n";
				$userinputaction.= '		$###MODULENAME###_class->pngcomp = $webutlercouple->config[\'png_compress\'];'."\n";
				$userinputaction.= '		$###MODULENAME###_class->jpgqual = $webutlercouple->config[\'jpg_quality\'];'."\n";
			}
			$userinputaction.= '		$###MODULENAME###_class->saveuserpost();'."\n";
			$userinputaction.= '	}'."\n";
		}
		else {
			$userinputinit = '';
			$userinputaction = '';
		}
		
		if($this->modconf['basecat'] == 1) {
			$modbasecat = '	if(isset($_MMVAR[\'###MODULENAME###basecat\']) && $_MMVAR[\'###MODULENAME###basecat\'] != \'\') {'."\n";
			$modbasecat.= '		$###MODULENAME###_class->basecat = $_MMVAR[\'###MODULENAME###basecat\'];'."\n";
			$modbasecat.= '		unset($_MMVAR[\'###MODULENAME###basecat\']);'."\n";
			$modbasecat.= '	}'."\n";
		}
		else {
			$modbasecat = '';
		}
		
		if($this->adminvars['catmenu'] == 1) {
			$catmenuopen = 'if(isset($_MMVAR[\'###MODULENAME###catmenu\']) && $_MMVAR[\'###MODULENAME###catmenu\'] == \'1\') {'."\n";
			$catmenuopen.= '	unset($_MMVAR[\'###MODULENAME###catmenu\']);'."\n";
			$catmenuopen.= '	$###MODULENAME###catmenu_class = new MMViewClass;'."\n";
			$catmenuopen.= '	$###MODULENAME###catmenu_class->getpage = $webutlercouple->getpage;'."\n";
			$catmenuopen.= '	$###MODULENAME###catmenu_class->modname = \'###MODULENAME###\';'."\n";
			if($this->modconf['basecat'] == 1) {
				$catmenuopen.= '	if(isset($_MMVAR[\'###MODULENAME###basecat\']) && $_MMVAR[\'###MODULENAME###basecat\'] != \'\') {'."\n";
				$catmenuopen.= '		$###MODULENAME###catmenu_class->basecat = $_MMVAR[\'###MODULENAME###basecat\'];'."\n";
				$catmenuopen.= '		unset($_MMVAR[\'###MODULENAME###basecat\']);'."\n";
				$catmenuopen.= '	}'."\n";
			}
			$catmenuopen.= '	$###MODULENAME###catmenu_class->serverpath = $webutlercouple->config[\'server_path\'];'."\n";
			$catmenuopen.= '	$###MODULENAME###catmenu_class->fileconfig = $###MODULENAME###_conf;'."\n";
			$catmenuopen.= '	$###MODULENAME###catmenu_class->pagelang = $###MODULENAME###_language;'."\n";
			$catmenuopen.= '	$###MODULENAME###catmenu_class->get = $_GET;'."\n";
			$catmenuopen.= '	if(isset($_MMVAR[\'###MODULENAME###modpage\']) && $_MMVAR[\'###MODULENAME###modpage\'] != \'\') {'."\n";
			$catmenuopen.= '		$###MODULENAME###catmenu_class->modpage = $_MMVAR[\'###MODULENAME###modpage\'];'."\n";
			$catmenuopen.= '		unset($_MMVAR[\'###MODULENAME###modpage\']);'."\n";
			$catmenuopen.= '	}'."\n";
			$catmenuopen.= '	$###MODULENAME###catmenu_class->connectdb();'."\n";
			$catmenuopen.= '	echo $###MODULENAME###catmenu_class->loadcatsformenu();'."\n";
		}
		else {
			$catmenuopen = '';
		}
		
		if($this->modconf['newest'] == 1) {
			$newestopen = ($this->adminvars['catmenu'] == 1) ? '}'."\n".'else' : '';
			$newestopen.= 'if(isset($_MMVAR[\'###MODULENAME###newest\']) && $_MMVAR[\'###MODULENAME###newest\'] == \'1\') {'."\n";
			$newestopen.= '	unset($_MMVAR[\'###MODULENAME###newest\']);'."\n";
			$newestopen.= '	$###MODULENAME###newest_class = new MMViewClass;'."\n";
			$newestopen.= '	$###MODULENAME###newest_class->getpage = $webutlercouple->getpage;'."\n";
			$newestopen.= '	$###MODULENAME###newest_class->modname = \'###MODULENAME###\';'."\n";
			if($this->modconf['basecat'] == 1) {
				$newestopen.= '	if(isset($_MMVAR[\'###MODULENAME###basecat\']) && $_MMVAR[\'###MODULENAME###basecat\'] != \'\') {'."\n";
				$newestopen.= '		$###MODULENAME###newest_class->basecat = $_MMVAR[\'###MODULENAME###basecat\'];'."\n";
				$newestopen.= '		unset($_MMVAR[\'###MODULENAME###basecat\']);'."\n";
				$newestopen.= '	}'."\n";
			}
			$newestopen.= '	$###MODULENAME###newest_class->serverpath = $webutlercouple->config[\'server_path\'];'."\n";
			$newestopen.= '	$###MODULENAME###newest_class->fileconfig = $###MODULENAME###_conf;'."\n";
			$newestopen.= '	$###MODULENAME###newest_class->pagelang = $###MODULENAME###_language;'."\n";
			$newestopen.= '	$###MODULENAME###newest_class->get = $_GET;'."\n";
			$newestopen.= '	if(isset($_MMVAR[\'###MODULENAME###modpage\']) && $_MMVAR[\'###MODULENAME###modpage\'] != \'\') {'."\n";
			$newestopen.= '		$###MODULENAME###newest_class->modpage = $_MMVAR[\'###MODULENAME###modpage\'];'."\n";
			$newestopen.= '		unset($_MMVAR[\'###MODULENAME###modpage\']);'."\n";
			$newestopen.= '	}'."\n";
			$newestopen.= '	$###MODULENAME###newest_class->connectdb();'."\n";
			$newestopen.= '	echo $###MODULENAME###newest_class->loadnewestblock();'."\n";
		}
		else {
			$newestopen = '';
		}
		
		if($this->adminvars['catmenu'] == 1 || $this->modconf['newest'] == 1) {
			$blockselse = '}'."\n".'else {'."\n";
			$blocksclose = '}';
		}
		else {
			$blockselse = '';
			$blocksclose = '';
		}
		
		$setfilter = '';
		if($this->modconf['filter'] == 1) {
			$setfilter.= '	if(isset($_POST[\'###MODULENAME###callfilter\'])) {'."\n";
			$setfilter.= '		$###MODULENAME###_class->datafilter = array('."\n";
			$setfilter.= '			/* filter settings here */'."\n";
			$setfilter.= '		);'."\n";
			$setfilter.= '	}'."\n";
		}
		
        $this->savetplfiles('view.php', '###CATMENUOPEN###', $catmenuopen);
        $this->savetplfiles('view.php', '###NEWESTOPEN###', $newestopen);
        $this->savetplfiles('view.php', '###BLOCKSELSE###', $blockselse);
        $this->savetplfiles('view.php', '###USERINPUTINIT###', $userinputinit);
        $this->savetplfiles('view.php', '###USERINPUTACTION###', $userinputaction);
        $this->savetplfiles('view.php', '###SETFILTER###', $setfilter);
        $this->savetplfiles('view.php', '###MODBASECAT###', $modbasecat);
        $this->savetplfiles('view.php', '###BLOCKSCLOSE###', $blocksclose);
        
        $confelems = '';
        if($confelements != '') {
            $confelems = '<?PHP if($_GET[\'page\'] == \'conf\' || $_GET[\'page\'] == \'install\') { ?'.'>'."\n";
			$confelems.= '<?PHP $checkchmods = \'\';
				if($_GET[\'page\'] == \'install\') {
					$checkchmoddb = $###MODULENAME###_class->checkchmoddb();
					clearstatcache();
					$checkchmodsmedia = $###MODULENAME###_class->checkchmods(\'media\');
					clearstatcache();
					$checkchmods = $checkchmoddb.$checkchmodsmedia;
				}
				if($checkchmods != \'\') { ?'.'>
					<table border="0" cellspacing="0" cellpadding="5">
						<tr class="top"><td><h3><?PHP echo str_replace(\'###CHMODMODNAME###\', $###MODULENAME###_class->modname, _MODMAKERLANGADMIN_SETCHMODFOR_); ?></h3></td></tr>
						<tr><td style="padding: 10px 25px 5px 25px"><strong><?PHP echo _MODMAKERLANGADMIN_WRONGCHMOD_; ?></strong></td></tr>
						<?PHP echo $checkchmods; ?'.'>
						<tr><td style="padding: 15px 0px 0px 25px"><?PHP echo str_replace(array(\'###CHMODFOLDER###\', \'###CHMODFILES###\'), array(\'<strong>\'.decoct($###MODULENAME###_class->chmod[0]).\'</strong>\', \'<strong>\'.decoct($###MODULENAME###_class->chmod[1]).\'</strong>\'), _MODMAKERLANGADMIN_SETRIGHTCHMODS_); ?></td></tr>
					</table>
				<?PHP } else { ?'.'>'."\n";
			if($this->modconf['subeditor'] == '1')
	            $confelems.= '<?PHP if((isset($###MODULENAME###_class->dbconfig[\'hideforsubs\']) && $###MODULENAME###_class->dbconfig[\'hideforsubs\'] != \'hide\') || $webutlermodadmin->checkadmin()) { ?'.'>'."\n";
			$confelems.= '<table border="0" cellspacing="0" cellpadding="5">
                <tr class="top">
                  <td colspan="2"><h2><?PHP echo $_GET[\'page\'] == \'install\' ? _MODMAKERLANGADMIN_INSTALL_ : _MODMAKERLANGADMIN_SETTINGS_; ?'.'></h2></td>
                </tr>
            	'.$confelements.'
                <tr class="bottom">
                  <td>&nbsp;</td><td><input type="submit" class="button" name="saveconf" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_SAVE_; ?'.'>" /></td>
                </tr>
              </table>'."\n";
			if($this->modconf['subeditor'] == '1')
				$confelems.= '<?PHP } else { echo _MODMAKERLANGADMIN_NOTAVAILFORSUBS_; } ?'.'>'."\n";
			$confelems.= '<?PHP } } ?'.'>'."\n";
        }
        
        $optimgspopup = '';
        $optionelems = '';
        if($this->adminvars['options'] == 1) {
        	$optimgspopup = '<?PHP if($_GET[\'page\'] == \'options\') { ?'.'>
	              <?PHP if(!isset($_GET[\'option\'])) { ?'.'>
		              <div id="uploadoptionimgs">
			            <?PHP $optimgfolders = $###MODULENAME###_class->readoptimgfolders(); ?'.'>
						<?PHP if($optimgfolders != \'\') { ?'.'>
			              <div id="uploadoptimages">
				              <form method="post" action="admin.php?page=options" enctype="multipart/form-data">
				              	  <strong><?PHP echo _MODMAKERLANGADMIN_OPTIONIMGSUPLOAD_; ?'.'></strong>
				                  <select name="optfolder" size="1">
				                  	<?PHP echo $optimgfolders; ?'.'>
								  </select>
                                  <div class="fakeupload"><input type="file" class="fileupload" size="37" name="optimage[1]" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>" /></div>
                                  <div class="fakeupload"><input type="file" class="fileupload" size="37" name="optimage[2]" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>" /></div>
                                  <div class="fakeupload"><input type="file" class="fileupload" size="37" name="optimage[3]" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>" /></div>
                                  <div class="fakeupload"><input type="file" class="fileupload" size="37" name="optimage[4]" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>" /></div>
                                  <div class="fakeupload"><input type="file" class="fileupload" size="37" name="optimage[5]" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>" /></div>
				                  <div class="addnewitems"><input type="submit" class="button" name="uploadoptimgs" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_UPLOAD_; ?'.'>" /></div>
				              </form>
			              </div>
						<?PHP } ?'.'>
			              <form method="post" action="admin.php?page=options">
			              	  <strong><?PHP echo _MODMAKERLANGADMIN_OPTIMGSUBFOLDER_; ?'.'></strong>
			                  <input type="text" name="newoptfolder" />
			                  <div class="addnewitems"><input type="submit" class="button" name="makeoptfolder" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_MAKEFOLDER_; ?'.'>" /></div>
			              </form>
		              </div>
	              <?PHP } else { ?'.'>
		              <div id="showoptionimgs" style="display: none">
		                  <?PHP echo $###MODULENAME###_class->loadoptionimglist(); ?'.'>
		              </div>
	              <?PHP } ?'.'>
              <?PHP } ?'.'>'."\n";
              
		    $optionelems = '<?PHP if($_GET[\'page\'] == \'options\' && !isset($_GET[\'option\'])) { ?'.'>
            	<div id="submenu">
					<a href="admin.php?page=options&option=new"><?PHP echo _MODMAKERLANGADMIN_NEWOPTION_; ?'.'></a> <span class="jslink" onclick="openoptionimgs()"><?PHP echo _MODMAKERLANGADMIN_OPTIONIMGSUPLOAD_; ?'.'></span>
				</div>
		        <?PHP $getoptionslist = $###MODULENAME###_class->getoptionslist();
                if($getoptionslist == \'\') {
				  	echo _MODMAKERLANGADMIN_NOAVAILOPTION_;
				} else { ?'.'>
		          <table border="0" cellspacing="0" cellpadding="5">
		            <tr class="top">
		              <td colspan="3"><h2><?PHP echo _MODMAKERLANGADMIN_OPTIONS_; ?'.'></h2></td>
		            </tr>
		            <?PHP echo $getoptionslist; ?'.'>
		          </table>
		      <?PHP } } ?'.'>'."\n\n";
		
		    $optionelems.= '<?PHP if($_GET[\'page\'] == \'options\' && isset($_GET[\'option\'])) {
		        if($_GET[\'option\'] != \'new\' && (!isset($db_data) || $db_data == \'\')) {
		            echo _MODMAKERLANGADMIN_NOEXISTOPTION_;
		        } else { ?'.'>
		      <table border="0" cellspacing="0" cellpadding="5">
		        <tr class="top">
		          <td colspan="2"><h2><?PHP echo ($_GET[\'option\'] == \'new\') ? _MODMAKERLANGADMIN_NEWOPTION_ : _MODMAKERLANGADMIN_EDITOPTION_; ?'.'></h2></td>
		        </tr>
				<tr class="even">
				  <td class="start"><?PHP echo _MODMAKERLANGADMIN_OPTIONGROUP_; ?'.'>:</td>
				  <td class="end">';
				if($this->adminvars['langs'] == 'multi' || $this->adminvars['langs'] == 'start') {
					$optionelems.= '<?PHP echo $###MODULENAME###_class->getlangflags(\'grpname\');
			        	foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
		                <input type="text" id="bylang_grpname_<?PHP echo $lang; ?'.'>"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'> name="grpname[<?PHP echo $lang; ?'.'>]" value="<?PHP if(isset($db_data[\'grpname\'][$lang])) echo $db_data[\'grpname\'][$lang]; ?'.'>" />
					  <?PHP } ?'.'>';
				}
				else {
					$optionelems.= '<input type="text" name="grpname" value="<?PHP if(isset($db_data[\'grpname\'])) echo $db_data[\'grpname\']; ?'.'>" />';
				}
				$optionelems.= '</td>
		        </tr>
				<tr class="odd">
				  <td class="start">
				  	<?PHP echo _MODMAKERLANGADMIN_OPTIONVALUES_; ?'.'>: <img src="admin/icons/info.png" id="optionsicon" onmouseover="document.getElementById(\'optionsinfo\').style.display = \'inline-block\'" onmouseout="document.getElementById(\'optionsinfo\').style.display = \'none\'" />
	            	<div id="optionsinfo">
						<?PHP echo _MODMAKERLANGADMIN_OPTIONEXPLAIN_; ?'.'>
		            </div>
				  </td>
				  <td class="end">
					<?PHP echo _MODMAKERLANGADMIN_OPTIONHEADLINE_; ?'.'><br />
					<textarea name="optvals" id="optionsvalues"><?PHP if(isset($db_data[\'optvals\'])) echo $db_data[\'optvals\']; ?'.'></textarea>';
			        if($this->adminvars['langs'] == 'multi' || $this->adminvars['langs'] == 'start') {
						$optionelems.= '<br />'."\n".'<?PHP echo _MODMAKERLANGADMIN_OPTIONWITHDEFINES_; ?'.'>'."\n";
					}
				$optionelems.= '</td>
		        </tr>
				<tr class="even">
				  <td class="start"><?PHP echo _MODMAKERLANGADMIN_OPTIONIMGS_; ?'.'>:</td>
				  <td class="end"><span class="jslink" id="listoptimglink" onclick="listoptionimgs(\'<?PHP echo _MODMAKERLANGADMIN_OPTIONIMGSOPEN_; ?'.'>\', \'<?PHP echo _MODMAKERLANGADMIN_OPTIONIMGSCLOSE_; ?'.'>\')"><?PHP echo _MODMAKERLANGADMIN_OPTIONIMGSOPEN_; ?'.'></span> &raquo;</td>
		        </tr>
		        <tr class="bottom">
		          <td>&nbsp;</td><td><input type="submit" class="button" name="<?PHP echo ($_GET[\'option\'] == \'new\') ? \'saveoption\' : \'editoption\'; ?'.'>" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_SAVE_; ?'.'>" /></td>
		        </tr>
		      </table>
		      <?PHP } } ?'.'>'."\n";
        }
        
        $adminstart = '';
        $catlistelems = '';
        $catelems = '';
        $subcatspopup = '';
        $subcatselems = '';
        if($this->adminvars['cats'] != '') {
            $adminstart = 'if($_GET[\'page\'] == \'\') $_GET[\'page\'] = \'cats\';'."\n";
            //$catlist = $this->getcategorylist();
            //$catlistelements = $this->makelistlines($catlist);
            $catlistelems = '<?PHP if($_GET[\'page\'] == \'cats\' && !isset($_GET[\'cat\'])) { ?'.'>
            	<div id="submenu">
					<a href="admin.php?page=cats&cat=new"><?PHP echo _MODMAKERLANGADMIN_NEWCATEGORY_; ?'.'></a>';
				if($this->modconf['subcats'] == 1)
					$catlistelems.= '<a href="admin.php?page=subcats"><?PHP echo _MODMAKERLANGADMIN_SUBCATEGORIES_; ?'.'></a>';
			$catlistelems.= '</div>
                <?PHP $getcatlist = $###MODULENAME###_class->getcatlist();
                if($getcatlist == \'\') {
                    echo _MODMAKERLANGADMIN_NOAVAILCATEGORY_;
                } else { ?'.'>
              <table border="0" cellspacing="0" cellpadding="5">
                <tr class="top">
                  <td colspan="'.$catlistcolspan.'"><h2><?PHP echo _MODMAKERLANGADMIN_CATEGORIES_; ?'.'></h2></td>
                </tr>
                <?PHP echo $getcatlist; ?'.'>
              </table>
              <?PHP } } ?'.'>'."\n";
            
            $catdata = $this->getcategoryfields();
			
			$catseo = ($this->modconf['seocats'] == '1') ? $this->getseoinputs('cat') : array();
			
            $catlang = $this->getlangselection();
            if(isset($catlang))
                $catlangdata = array_merge($catlang, $catdata);
            else
                $catlangdata = $catdata;
            $catelements = $this->makeelementlines($catlangdata, false);
			
            $catseoelems = count($catseo) > 0 ? $this->makeelementlines($catseo, false, false, $this->seotrclass) : '';
			
            $catelems = '<?PHP if($_GET[\'page\'] == \'cats\' && isset($_GET[\'cat\'])) {
                if($_GET[\'cat\'] != \'new\' && (!isset($db_data) || $db_data == \'\')) {
                    echo _MODMAKERLANGADMIN_NOEXISTCATEGORY_;
                } else { ?'.'>
              <table border="0" cellspacing="0" cellpadding="5">
                <tr class="top">
                  <td colspan="2"><h2><?PHP echo ($_GET[\'cat\'] == \'new\') ? _MODMAKERLANGADMIN_NEWCATEGORY_ : _MODMAKERLANGADMIN_EDITCATEGORY_; ?'.'></h2></td>
                </tr>
            	'.$catelements.$catseoelems.'
                <tr class="bottom">
                  <td>&nbsp;</td><td><input type="submit" class="button" name="<?PHP echo ($_GET[\'cat\'] == \'new\') ? \'savecat\' : \'editcat\'; ?'.'>" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_SAVE_; ?'.'>" /></td>
                </tr>
              </table>
              <?PHP } } ?'.'>'."\n";
              
              if($this->modconf['subcats'] == 1) {
                  $subcatspopup = '<?PHP if($_GET[\'page\'] == \'subcats\') { ?'.'>
                      <div id="catitemlist">
                      <form method="post" action="admin.php?page=subcats" onsubmit="return setscrollpos()">
                          <?PHP echo $###MODULENAME###_class->loadcatitemlist(); ?'.'>
                          <div class="addnewitems"><input type="hidden" name="scrollpos" id="scrollpos" /><input type="submit" class="button" name="addnewitems" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_INSERT_; ?'.'>" /></div>
                      </form>
                      </div>
                      <?PHP } ?'.'>';
                  $subcatselems = '<?PHP if($_GET[\'page\'] == \'subcats\') { ?'.'>
                      <div id="moveitembuttons">
                          <input type="image" name="savesubcats[posup]" class="mover" src="admin/icons/moveup.png" title="<?PHP echo _MODMAKERLANGADMIN_BUTTON_HOCH_; ?'.'>" /><input type="image" name="savesubcats[posdown]" class="mover" src="admin/icons/movedown.png" title="<?PHP echo _MODMAKERLANGADMIN_BUTTON_RUNTER_; ?'.'>" /><input type="image" name="savesubcats[posout]" class="mover" src="admin/icons/moveleft.png" title="<?PHP echo _MODMAKERLANGADMIN_BUTTON_LINKS_; ?'.'>" /><input type="image" name="savesubcats[posin]" class="mover" src="admin/icons/moveright.png" title="<?PHP echo _MODMAKERLANGADMIN_BUTTON_RECHTS_; ?'.'>" /><img src="admin/icons/plus.png" title="<?PHP echo _MODMAKERLANGADMIN_BUTTON_INSERT_; ?'.'>" class="movebutton" onclick="opencatitemlist()" /><input type="image" name="unsetcatitem" class="movebutton" src="admin/icons/delete.png" title="<?PHP echo _MODMAKERLANGADMIN_BUTTON_DELETE_; ?'.'>" />
                      </div>
                      <div id="moveitemlist">
                          <?PHP echo $###MODULENAME###_class->loadsubcats(); ?'.'>
                      </div>
                      <?PHP } ?'.'>';
              }
        }
        
        if($this->modconf['breaktopic'] == '1' || $this->modconf['breakdata'] == '1')
			$breaktime = $this->getbreaktime();
        
        $topiclistelems = '';
        $topicelems = '';
        if($this->adminvars['topics'] != '') {
            if($this->adminvars['cats'] == '') {
                $topiclang = $this->getlangselection();
                $topiclines = $topiclang;
                $adminstart = 'if($_GET[\'page\'] == \'\') $_GET[\'page\'] = \'topics\';'."\n";
            }
            
            if($this->modconf['breaktopic'] == 1) {
                if(isset($topiclang))
                    $topiclines = array_merge($topiclang, $breaktime);
                else
                    $topiclines = $breaktime;
            }
            
            /*
            $topiclist = $this->gettopiclist();
            $topiclistelements = $this->makelistlines($topiclist);
                    '.$topiclistelements.'
            */
			
            $topiclistelems = '<?PHP if($_GET[\'page\'] == \'topics\' && !isset($_GET[\'topic\'])) {
            	echo \'<div id="submenu">
					<a href="\'.$###MODULENAME###_class->gettopiclink(\'new\').\'">\'._MODMAKERLANGADMIN_NEWTOPIC_.\'</a>';
			if($this->modconf['copytopictocat'] == '1')
				$topiclistelems.= '<a href="\'.$###MODULENAME###_class->gettopiclink(\'copy\').\'">\'._MODMAKERLANGADMIN_COPYTOPIC_.\'</a>';
			$topiclistelems.= '</div>\'; ?'.'>'."\n";
                if($this->adminvars['cats'] != '')
                    $topiclistelems.= '<?PHP if(!isset($_GET[\'cat\'])) { echo _MODMAKERLANGADMIN_NOSELECTCATEGORY_; } else { ?'.'>'."\n";
                $topiclistelems.= '<?PHP $gettopiclist = $###MODULENAME###_class->gettopiclist();
					if($gettopiclist == \'\') {
                        echo _MODMAKERLANGADMIN_NOAVAILTOPIC_;
                    } else { ?'.'>
                  <table border="0" cellspacing="0" cellpadding="5">
                    <tr class="top">
                      <td colspan="'.$topiclistcolspan.'"><h2><?PHP echo _MODMAKERLANGADMIN_TOPICS_.$###MODULENAME###_class->gettopicsheadline(); ?'.'></h2></td>
                    </tr>
                    <?PHP echo $gettopiclist; ?'.'>
                  </table>
                  <?PHP } } ?'.'>'."\n";
                  if($this->adminvars['cats'] != '') $topiclistelems.= '<?PHP } ?'.'>'."\n";
            
			$topicseo = ($this->modconf['seotopics'] == '1') ? $this->getseoinputs('topic') : array();
			
            if(isset($topiclines))
                $topicelms = array_merge($topiclines, $admindata);
            else
                $topicelms = $admindata;
            $topicelements = $this->makeelementlines($topicelms, false, true);
			
            $topicseoelems = (count($topicseo) > 0) ? $this->makeelementlines($topicseo, false, false, $this->seotrclass) : '';
			
            $topicelems = '<?PHP if($_GET[\'page\'] == \'topics\' && isset($_GET[\'topic\'])) { ?'.'>'."\n";
            if($this->adminvars['cats'] != '')
                $topicelems.= '<?PHP if(!isset($_GET[\'cat\'])) { echo _MODMAKERLANGADMIN_NOSELECTCATEGORY_; } else { ?'.'>'."\n";
            $topicelems.= '<?PHP if($_GET[\'topic\'] != \'new\'';
			if($this->modconf['copytopictocat'] == '1')
				$topicelems.= ' && $_GET[\'topic\'] != \'copy\'';
			$topicelems.= ' && (!isset($db_data) || $db_data == \'\')) {
                    echo _MODMAKERLANGADMIN_NOEXISTTOPIC_;
                } else { ?'.'>
              <table border="0" cellspacing="0" cellpadding="5">
                <tr class="top">
                  <td colspan="2"><h2><?PHP 
				    if($_GET[\'topic\'] == \'new\') echo _MODMAKERLANGADMIN_NEWTOPIC_;'."\n";
					if($this->modconf['copytopictocat'] == '1')
						$topicelems.= 'elseif($_GET[\'topic\'] == \'copy\') echo _MODMAKERLANGADMIN_COPYTOPIC_;'."\n";
					$topicelems.= 'else echo _MODMAKERLANGADMIN_EDITTOPIC_;
				  ?'.'></h2></td>
                </tr>'."\n";
			if($this->modconf['copytopictocat'] == '1') {
				$topicelems.= '<?PHP if($_GET[\'topic\'] == \'copy\') { ?>
					<tr class="odd">
					  <td class="start"><?PHP echo _MODMAKERLANGADMIN_TOPIC_; ?>:</td>
					  <td class="end"><input type="text" name="copyof" value="" style="width: 40px" /> <?PHP echo _MODMAKERLANGADMIN_TOPICCOPYID_; ?></td>
					</tr>
					<?PHP } else { ?>'."\n";
			}
			$topicelems.= '<tr class="even">
                  <td class="start"><?PHP echo _MODMAKERLANGADMIN_TOPIC_; ?'.'>:</td>
                  <td class="end">';
	        if($this->adminvars['langs'] == 'multi') {
	        	$topicelems.= '<?PHP echo $###MODULENAME###_class->getlangflags(\'topic\');
	        	foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
                <input type="text" id="bylang_topic_<?PHP echo $lang; ?'.'>"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'> name="topic[<?PHP echo $lang; ?'.'>]" value="<?PHP if(isset($db_data[\'topic\'][$lang])) echo $db_data[\'topic\'][$lang]; ?'.'>" />
				<?PHP } ?'.'>';
	        }
	        else {
				$topicelems.= '<input type="text" name="topic" value="<?PHP if(isset($db_data[\'topic\'])) echo $db_data[\'topic\']; ?'.'>" />';
			}
			$topicelems.= '</td>
                </tr>
            	'.$topicelements.$topicseoelems."\n";
			if($this->modconf['copytopictocat'] == '1') $topicelems.= '<?PHP } ?>'."\n";
            $topicelems.= '<tr class="bottom">
                  <td>&nbsp;</td><td><input type="submit" class="button" name="<?PHP
				    if($_GET[\'topic\'] == \'new\') echo \'savetopic\';'."\n";
					if($this->modconf['copytopictocat'] == '1')
						$topicelems.= 'elseif($_GET[\'topic\'] == \'copy\') echo \'copytopic\';'."\n";
					$topicelems.= 'else echo \'edittopic\';
				  ?'.'>" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_SAVE_; ?'.'>" /></td>
                </tr>
              </table>
              <?PHP } } ?'.'>'."\n";
            if($this->adminvars['cats'] != '') $topicelems.= '<?PHP } ?'.'>'."\n";
        }
        
        if($this->adminvars['cats'] == '' && $this->adminvars['topics'] == '') {
            $datalang = $this->getlangselection();
            $datalines = $datalang;
            $adminstart = 'if($_GET[\'page\'] == \'\') $_GET[\'page\'] = \'datas\';'."\n";
        }
        
        if($this->modconf['breakdata'] == 1) {
            if(isset($datalang))
                $datalines = array_merge($datalang, $breaktime);
            else
                $datalines = $breaktime;
        }
        
        //$datalist = $this->getdatalist();
        //$listelements = $this->makelistlines($datalist);
        $listelems = '<?PHP if($_GET[\'page\'] == \'datas\' && !isset($_GET[\'data\'])) {
				echo \'<div id="submenu">
				<a href="\'.$###MODULENAME###_class->getdatalink(\'new\').\'">\'._MODMAKERLANGADMIN_NEWDATA_.\'</a>';
			if($this->modconf['copydatatocat'] == '1' || $this->modconf['copydatatotopic'] == '1') {
				$listelems.= '<a href="\'.$###MODULENAME###_class->getdatalink(\'copy\').\'">\'._MODMAKERLANGADMIN_COPYDATA_.\'</a>';
			}
			$listelems.= '</div>\';'."\n";
        $listnohead = '';
        if($this->adminvars['cats'] != '') {
            $listnohead.= 'if(!isset($_GET[\'cat\'])) { echo _MODMAKERLANGADMIN_NOSELECTCATEGORY_; }'."\n";
            if($this->adminvars['topics'] != '') {
                $listnohead.= 'elseif(!isset($_GET[\'topic\'])) { echo _MODMAKERLANGADMIN_NOSELECTTOPIC_; }'."\n";
            }
        }
        elseif($this->adminvars['cats'] == '' && $this->adminvars['topics'] != '') {
            $listnohead.= 'if(!isset($_GET[\'topic\'])) { echo _MODMAKERLANGADMIN_NOSELECTTOPIC_; }'."\n";
        }
            //'.$listelements.'
        $listelems.= ($listnohead != '') ? $listnohead.'else { ?'.'>'."\n" : ' ?'.'>'."\n";
        $listelems.= '<?PHP $getdatalist = $###MODULENAME###_class->getdatalist();
            if($getdatalist == \'\') {
                echo _MODMAKERLANGADMIN_NOAVAILDATA_;
            } else { ?'.'>
          <table border="0" cellspacing="0" cellpadding="5">
            <tr class="top">
              <td colspan="'.$datalistcolspan.'"><h2><?PHP echo _MODMAKERLANGADMIN_DATAS_.$###MODULENAME###_class->getdatasheadline(); ?'.'></h2></td>
            </tr>
            <?PHP echo $getdatalist; ?'.'>
          </table>'."\n";
        $listelems.= ($listnohead != '') ? '<?PHP } } } ?'.'>'."\n" : '<?PHP } } ?'.'>'."\n";
		
		$dataseo = ($this->modconf['seodatas'] == '1') ? $this->getseoinputs('data') : array();
		
        if(isset($datalines))
            $datalangelms = array_merge($datalines, $admindata);
        else
            $datalangelms = $admindata;
		
        $dataelements = $this->makeelementlines($datalangelms, false, true);
		
        $seodataelems = (count($dataseo) > 0) ? "\n".$this->makeelementlines($dataseo, false, false, $this->seotrclass) : '';
		
        $dataelems = '<?PHP if($_GET[\'page\'] == \'datas\' && isset($_GET[\'data\'])) {'."\n";
        $dataelems.= ($listnohead != '') ? $listnohead.'else { ?'.'>'."\n" : ' ?'.'>'."\n";
        $dataelems.= '<?PHP if($_GET[\'data\'] != \'new\'';
		if($this->modconf['copydatatocat'] == '1' || $this->modconf['copydatatotopic'] == '1')
			$dataelems.= ' && $_GET[\'data\'] != \'copy\'';
		$dataelems.= ' && (!isset($db_data) || $db_data == \'\')) {
                echo _MODMAKERLANGADMIN_NOEXISTDAT_;
            } else { ?'.'>'."\n";
          $dataelems.= '<table border="0" cellspacing="0" cellpadding="5">
            <tr class="top">
              <td colspan="2"><h2><?PHP 
			    if($_GET[\'data\'] == \'new\') echo _MODMAKERLANGADMIN_NEWDATA_;'."\n";
				if($this->modconf['copydatatocat'] == '1' || $this->modconf['copydatatotopic'] == '1')
					$dataelems.= 'elseif($_GET[\'data\'] == \'copy\') echo _MODMAKERLANGADMIN_COPYDATA_;'."\n";
				$dataelems.= 'else echo _MODMAKERLANGADMIN_EDITDATA_;
			  ?'.'></h2></td>
            </tr>'."\n";
            if($this->modconf['copydatatocat'] == '1' || $this->modconf['copydatatotopic'] == '1') {
            $dataelems.= '<?PHP if($_GET[\'data\'] == \'copy\') { ?>
				<tr class="odd">
				  <td class="start"><?PHP echo _MODMAKERLANGADMIN_DATA_; ?>:</td>
				  <td class="end"><input type="text" name="copyof" value="" style="width: 40px" /> <?PHP echo _MODMAKERLANGADMIN_DATACOPYID_; ?></td>
				</tr>
            <?PHP } else { ?>'."\n";
			}
        	$dataelems.= $dataelements.$seodataelems."\n";
            if($this->modconf['copydatatocat'] == '1' || $this->modconf['copydatatotopic'] == '1') $dataelems.= '<?PHP } ?>'."\n";
			$dataelems.= '<tr class="bottom">
              <td>&nbsp;</td><td><input type="submit" class="button" name="<?PHP
			    if($_GET[\'data\'] == \'new\') echo \'savedata\';'."\n";
				if($this->modconf['copydatatocat'] == '1' || $this->modconf['copydatatotopic'] == '1')
					$dataelems.= 'elseif($_GET[\'data\'] == \'copy\') echo \'copydata\';'."\n";
				$dataelems.= 'else echo \'editdata\';
			  ?'.'>" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_SAVE_; ?'.'>" /></td>
            </tr>
          </table>'."\n";
        $dataelems.= ($listnohead != '') ? '<?PHP } } } ?'.'>'."\n" : '<?PHP } } ?'.'>'."\n";
		
		$uploadpopup = '';
        if($this->adminvars['bigfiles'] != '') {
			$uploadpopup = '<?PHP if(isset($_GET[\'topic\']) || isset($_GET[\'data\'])) { ?'.'>'."\n";
			$uploadpopup.= '<div id="uploadlargepopup">
					<div id="uploadlarge_text">
						<strong><?PHP echo _MODMAKERLANGADMIN_ALERT_UPLOADLARGE_1_; ?'.'></strong>
						<?PHP echo _MODMAKERLANGADMIN_ALERT_UPLOADLARGE_2_; ?'.'>
						<?PHP if(!isset($db_data[\'dataid\'])) { echo \'<br /><span class="red">\'._MODMAKERLANGADMIN_ALERT_UPLOADLARGE_3_.\'</span>\'; } ?'.'>
					</div>
					<div id="uploadlarge_prozent"><?PHP echo _MODMAKERLANGADMIN_ALERT_UPLOADSTATE_; ?'.'>: <span id="uploadlarge_prozentbar">0 %</span></div>
					<div id="uploadlarge_progress"><div id="uploadlarge_progressbar"></div></div>
					<input type="button" id="uploadelarge_button" class="button" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_UPLOAD_; ?'.'>" /><input type="button" id="uploadelarge_cancel" class="button" value="<?PHP echo _MODMAKERLANGADMIN_BUTTON_CANCEL_; ?'.'>" />
				</div>'."\n";
			$uploadpopup.= '<?PHP } ?'.'>'."\n";
		}
        
        $adminjs = '';
        
        if($this->adminvars['cats'] != '' || $this->adminvars['topics'] != '') {
	        $changetoscript = '<script>'."\n".
	            '/* <![CDATA[ */'."\n".
	            '	 function openchangewin(level) {'."\n".
				'	    var to = (level == \'cat\') ? \'<?PHP echo _MODMAKERLANGADMIN_PROMPT_OFCAT_; ?'.'>\' : \'<?PHP echo _MODMAKERLANGADMIN_PROMPT_OFTOPIC_; ?'.'>\';'."\n".
				'	    check = prompt(\'<?PHP echo _MODMAKERLANGADMIN_PROMPT_NEWID_; ?'.'> \' + to + \' <?PHP echo _MODMAKERLANGADMIN_PROMPT_INSERT_; ?'.'>\', \'\');'."\n".
	            '	    if(!check) return false;'."\n".
	            '	    if(check == parseInt(check)) {'."\n".
	            '	    	var changeInput = document.createElement(\'input\');'."\n".
	            '	    	var addInput = document.forms.baseform.appendChild(changeInput);'."\n".
	            '	    	addInput.type = \'hidden\';'."\n".
	            '	    	addInput.name = \'changeto\';'."\n".
	            '	    	addInput.value = check;'."\n".
	            '	    	return true;'."\n".
	            '	    }'."\n".
	            '	    else {'."\n".
	            '	    	alert(\'<?PHP echo _MODMAKERLANGADMIN_PROMPT_ERROR_; ?'.'>\');'."\n".
	            '	    }'."\n".
	            '	    return false;'."\n".
	            '	 }'."\n".
	            '/* ]]> */'."\n".
	            '</script>';
	        $adminjs.= '<?PHP if(';
	        if($this->adminvars['cats'] != '' && $this->adminvars['topics'] != '')
		        $adminjs.= '$_GET[\'page\'] == \'topics\' || ';
	        $adminjs.= '$_GET[\'page\'] == \'datas\') { ?'.'>'."\n".$changetoscript."\n".'<?PHP } ?'.'>'."\n";
        }
        
        if($this->modconf['subcats'] == 1) {
            $subcatsscript = '<script>'."\n".
                '/* <![CDATA[ */'."\n".
                '    window.onload = function() {'."\n".
                '        moveitemlistheight(\'<?PHP if(isset($_POST[\'scrollpos\'])) echo $_POST[\'scrollpos\']; ?'.'>\');'."\n".
                '    }'."\n".
                '/* ]]> */'."\n".
                '</script>';
            $adminjs.= '<?PHP if($_GET[\'page\'] == \'subcats\') { ?'.'>'."\n".$subcatsscript."\n".'<?PHP } ?'.'>'."\n";
        }
        
        $adminscripts = (count($this->adminjs) >= 1) ? implode("\n", $this->adminjs) : '';
        
        $adminjs.= '<?PHP if(';
        if($this->adminvars['cats'] != '')
            $adminjs.= 'isset($_GET[\'cat\']) || ';
        if($this->adminvars['topics'] != '')
            $adminjs.= 'isset($_GET[\'topic\']) || ';
        $adminjs.= 'isset($_GET[\'data\'])) { ?'.'>'."\n".$adminscripts."\n".'<?PHP } ?'.'>'."\n";
		
		if($this->adminvars['cats'] != '' || $this->adminvars['topics'] != '' || $this->modconf['copydatatotopic'] == '1' || $this->modconf['copydatatocat'] == '1' || $this->modconf['copytopictocat'] == '1') {
			$copyget = array();
			if($this->adminvars['topics'] != '' || $this->modconf['copydatatotopic'] == '1' || $this->modconf['copydatatocat'] == '1')
				$copyget[] = '$_GET[\'page\'] == \'datas\'';
			if($this->adminvars['cats'] != '' || $this->modconf['copytopictocat'] == '1')
				$copyget[] = '$_GET[\'page\'] == \'topics\'';
			$setcopygets = count($copyget) > 1 ? '('.implode(' || ', $copyget).')' : implode('', $copyget);
			
			$adminjs.= '<?PHP if('.$setcopygets.' && isset($_GET[\'error\'])) { ?'.'>'."\n".
				'<script>'."\n".
				'/* <![CDATA[ */'."\n".
				'<?PHP if($_GET[\'error\'] == \'wronglang\') { ?'.'>'."\n".
				'	alert(\'<?PHP echo _MODMAKERLANGADMIN_WRONGLANG_; ?'.'>\');'."\n".
				'<?PHP } ?'.'>'."\n".
				'<?PHP if($_GET[\'error\'] == \'changetocopy\') { ?'.'>'."\n".
				'	alert(\'<?PHP echo _MODMAKERLANGADMIN_CHANGETOCOPY_; ?'.'>\');'."\n".
				'<?PHP } ?'.'>'."\n".
				'<?PHP if($_GET[\'error\'] == \'editcopy\') { ?'.'>'."\n".
				'	alert(\'<?PHP echo _MODMAKERLANGADMIN_EDITCOPY_; ?'.'>\');'."\n".
				'<?PHP } ?'.'>'."\n";
			if($this->modconf['copydatatotopic'] == '1' && $this->modconf['disttopicstart'] == '1') {
				$adminjs.= '<?PHP if($_GET[\'error\'] == \'copydist\') { ?'.'>'."\n".
					'	alert(\'<?PHP echo _MODMAKERLANGADMIN_COPYDIST_; ?'.'>\');'."\n".
					'<?PHP } ?'.'>'."\n";
			}
			$adminjs.= '	location.replace(\'admin.php?<?PHP unset($_GET[\'error\']); echo http_build_query($_GET); ?'.'>\');'."\n".
				'/* ]]> */'."\n".
				'</script>'."\n".
				'<?PHP } ?'.'>'."\n";
		}
        
        $hidesubopen = ($this->modconf['subeditor'] == '1') ? 'if((isset($###MODULENAME###_class->dbconfig[\'hideforsubs\']) && $###MODULENAME###_class->dbconfig[\'hideforsubs\'] != \'hide\') || $webutlermodadmin->checkadmin()) {' : '';
        $hidesubclose = ($this->modconf['subeditor'] == '1') ? '}' : '';
        
        $adminphpbricks = '';
        if($this->adminvars['bigfiles'] == '1') {
			$adminphpbricks.= 'if(isset($_GET[\'upload\']) && $_GET[\'upload\'] == \'file\') {'."\n";
			$adminphpbricks.= '	$###MODULENAME###_class->uploadlarge($_GET[\'field\'], $_GET[\'filename\'], $_GET[\'filetype\'], (isset($_GET[\'dataid\']) ? $_GET[\'dataid\'] : \'\'));'."\n";
			$adminphpbricks.= '	exit;'."\n";
			$adminphpbricks.= '}'."\n";
		}
        $adminphpbricks.= 'if(isset($_POST[\'on\'])) $###MODULENAME###_class->online();'."\n";
        $adminphpbricks.= 'if(isset($_POST[\'off\'])) $###MODULENAME###_class->offline();'."\n";
        if($this->modconf['catsort'] == 1 || $this->modconf['topicsort'] == 1 || $this->modconf['datasort'] == 1 || $this->adminvars['options'] == 1) {
            $adminphpbricks.= 'if(isset($_POST[\'up\'])) $###MODULENAME###_class->posup();'."\n";
            $adminphpbricks.= 'if(isset($_POST[\'down\'])) $###MODULENAME###_class->posdown();'."\n";
        }
        $adminphpbricks.= 'if(isset($_POST[\'delete\'])) $###MODULENAME###_class->delete();'."\n";
        $adminphpbricks.= 'if(isset($_POST[\'change\'])) $###MODULENAME###_class->changeto();'."\n";
		if($this->adminvars['options'] == 1) {
	        $adminphpbricks.= 'if(isset($_POST[\'makeoptfolder\'])) $###MODULENAME###_class->newoptimgfolder();'."\n";
	        $adminphpbricks.= 'if(isset($_POST[\'uploadoptimgs\'])) $###MODULENAME###_class->uploadoptionimg();'."\n";
	        $adminphpbricks.= 'if(isset($_POST[\'saveoption\'])) $###MODULENAME###_class->saveoption();'."\n";
	        $adminphpbricks.= 'if(isset($_POST[\'editoption\'])) $###MODULENAME###_class->updateoption();'."\n";
            $adminphpbricks.= 'if($_GET[\'page\'] == \'options\' && isset($_GET[\'option\']) && $_GET[\'option\'] != \'new\') $db_data = $###MODULENAME###_class->getoption();'."\n";
		}
        if($this->adminvars['cats'] != '') {
            $adminphpbricks.= 'if(isset($_POST[\'savecat\'])) $###MODULENAME###_class->savecat();'."\n";
            $adminphpbricks.= 'if(isset($_POST[\'editcat\'])) $###MODULENAME###_class->updatecat();'."\n";
            $adminphpbricks.= 'if($_GET[\'page\'] == \'cats\' && isset($_GET[\'cat\']) && $_GET[\'cat\'] != \'new\') $db_data = $###MODULENAME###_class->getcat();'."\n";
            if($this->modconf['subcats'] == 1) {
                $adminphpbricks.= 'if(isset($_POST[\'addnewitems\'])) $###MODULENAME###_class->newtosubcats();'."\n";
                $adminphpbricks.= 'if(isset($_POST[\'savesubcats\']) || isset($_POST[\'unsetcatitem_x\'])) $###MODULENAME###_class->savesubcats();'."\n";
            }
        }
        if($this->adminvars['topics'] != '') {
            $adminphpbricks.= 'if(isset($_POST[\'savetopic\'])) $###MODULENAME###_class->savetopic();'."\n";
			if($this->modconf['copytopictocat'] == 1)
				$adminphpbricks.= 'if(isset($_POST[\'copytopic\']) && $_POST[\'copyof\'] != \'\') $###MODULENAME###_class->copytopic();'."\n";
            $adminphpbricks.= 'if(isset($_POST[\'edittopic\'])) $###MODULENAME###_class->updatetopic();'."\n";
            $adminphpbricks.= 'if($_GET[\'page\'] == \'topics\' && isset($_GET[\'topic\']) && $_GET[\'topic\'] != \'new\') $db_data = $###MODULENAME###_class->gettopic();'."\n";
        }
        $adminphpbricks.= 'if(isset($_POST[\'savedata\'])) $###MODULENAME###_class->savedata();'."\n";
        if($this->modconf['copydatatocat'] == 1 || $this->modconf['copydatatotopic'] == '1')
			$adminphpbricks.= 'if(isset($_POST[\'copydata\']) && $_POST[\'copyof\'] != \'\') $###MODULENAME###_class->copydata();'."\n";
        $adminphpbricks.= 'if(isset($_POST[\'editdata\'])) $###MODULENAME###_class->updatedata();'."\n";
        $adminphpbricks.= 'if($_GET[\'page\'] == \'datas\' && isset($_GET[\'data\']) && $_GET[\'data\'] != \'new\') $db_data = $###MODULENAME###_class->getdata();'."\n";
        
        $subedit = $this->getsubeditlogins();
        
        $adminfile = $this->dbpath.'/'.$this->modname.'/admin.php';
        $content = file_get_contents($adminfile);
        $content = str_replace('###SUBEDITORS_LOGCHECK###', $subedit['check'], $content);
        $content = str_replace('###SUBEDITORS_LOGFILE###', $subedit['login'], $content);
        $content = str_replace('###CHECK_HIDESUBS_OPEN###', $hidesubopen, $content);
        $content = str_replace('###CHECK_HIDESUBS_CLOSE###', $hidesubclose, $content);
        $content = str_replace('###PHPBRICKS_ADMIN###', $adminphpbricks, $content);
        $content = str_replace('###STARTPAGE###', $adminstart, $content);
        $content = str_replace('###HEADERJAVASCRIPT###', $adminjs, $content);
        $content = str_replace('###MENU_ADMIN###', $adminmenu, $content);
        $content = str_replace('###CATPOPUP_ADMIN###', $subcatspopup, $content);
        $content = str_replace('###OPTPOPUP_ADMIN###', $optimgspopup, $content);
        $content = str_replace('###UPLOADPOPUP_ADMIN###', $uploadpopup, $content);
        $content = str_replace('###CONFVARS_ADMIN###', $confelems, $content);
        $content = str_replace('###OPTIONS_ADMIN###', $optionelems, $content);
        $content = str_replace('###CATLIST_ADMIN###', $catlistelems, $content);
        $content = str_replace('###CATDATA_ADMIN###', $catelems, $content);
        $content = str_replace('###CATSUBS_ADMIN###', $subcatselems, $content);
        $content = str_replace('###TOPICLIST_ADMIN###', $topiclistelems, $content);
        $content = str_replace('###TOPICDATA_ADMIN###', $topicelems, $content);
        $content = str_replace('###LIST_ADMIN###', $listelems, $content);
        $content = str_replace('###DATA_ADMIN###', $dataelems, $content);
        
        file_put_contents($adminfile, $content);
    }
    
    function getsubeditlogins()
    {
	    $subedit = array();
        
		if($this->modconf['subeditor'] == '1') {
        	$subedit['check'] = '(!$webutlermodadmin->checkadmin() && (!isset($_SESSION[\'###MODULENAME###log\']) || $_SESSION[\'###MODULENAME###log\'] != \'true\')) || isset($_GET[\'logout\'])';
	        $subedit['login'] = 'require_once $webutlermodadmin->config[\'server_path\'].\'/modules/###MODULENAME###/admin/login.php\';';
        }
        else {
	        $subedit['check'] = '!$webutlermodadmin->checkadmin()';
	        $subedit['login'] = 'exit(\'no access\');';
	        $loginfile = $this->dbpath.'/'.$this->modname.'/admin/login.php';
	        unlink($loginfile);
        }
        
        return $subedit;
    }
    
    function setmodnametofile($path)
    {
        $file = $this->dbpath.'/'.$this->modname.'/'.$path;
        $content = file_get_contents($file);
        
        $content = str_replace("###MODULENAME###", $this->modname, $content);
        
        file_put_contents($file, $content);
    }
    
    function getcategoryfields()
    {
        $admindatas = $this->mmdb->query("SELECT id, projectid, cats FROM admin WHERE admin.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $admindata = $admindatas->fetchArray();
        $catfields = explode('_', $admindata['cats']);
        $result = array();
        
        if($this->adminvars['langs'] == 'multi') {
        	$catnamevalue = '<?PHP echo $###MODULENAME###_class->getlangflags(\'catname\');
        	foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
            <input type="text" id="bylang_catname_<?PHP echo $lang; ?'.'>"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'> name="catname[<?PHP echo $lang; ?'.'>]" value="<?PHP if(isset($db_data[\'catname\'][$lang])) echo $db_data[\'catname\'][$lang]; ?'.'>" />
			<?PHP } ?'.'>';
        }
        else {
	        $catnamevalue = '<input type="text" name="catname" value="<?PHP if(isset($db_data[\'catname\'])) echo $db_data[\'catname\']; ?'.'>" />';
        }
        $result[] = array('<?PHP echo _MODMAKERLANGADMIN_CATEGORY_; ?'.'>', $catnamevalue);
        
		if($this->adminvars['catmenu'] == 1) {
	        if($this->adminvars['langs'] == 'multi') {
	        	$catlinkvalue = '<?PHP echo $###MODULENAME###_class->getlangflags(\'catlink\');
	        	foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
	            <input type="text" id="bylang_catlink_<?PHP echo $lang; ?'.'>"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'> name="catlink[<?PHP echo $lang; ?'.'>]" value="<?PHP if(isset($db_data[\'catlink\'][$lang])) echo $db_data[\'catlink\'][$lang]; ?'.'>" />
				<?PHP } ?'.'>';
	        }
	        else {
		        $catlinkvalue = '<input type="text" name="catlink" value="<?PHP if(isset($db_data[\'catlink\'])) echo $db_data[\'catlink\']; ?'.'>" />';
	        }
            $result[] = array('<?PHP echo _MODMAKERLANGADMIN_CATLINK_; ?'.'>', $catlinkvalue);
	    }
        
        if(in_array('cattext', $catfields)) {
	        if($this->adminvars['langs'] == 'multi') {
	        	$cattextvalue = '<?PHP echo $###MODULENAME###_class->getlangflags(\'cattext\');
	        	foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
                <div id="bylang_cattext_<?PHP echo $lang; ?'.'>" class="editorarea"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'>><textarea name="cattext[<?PHP echo $lang; ?'.'>]"><?PHP if(isset($db_data[\'cattext\'][$lang])) echo $db_data[\'cattext\'][$lang]; ?'.'></textarea>
                <script>
                /* <![CDATA[ */
                    CKEDITOR.replace( \'cattext[<?PHP echo $lang; ?'.'>]\', {
                        customConfig : \'html_config.js\',
                        language : \'<?PHP echo $###MODULENAME###_adminlang; ?'.'>\'
                    });
                /* ]]> */
                </script>
                </div>
				<?PHP } ?'.'>';
	        }
	        else {
	        	$cattextvalue = '<div class="editorarea"><textarea name="cattext"><?PHP if(isset($db_data[\'cattext\'])) echo $db_data[\'cattext\']; ?'.'></textarea>
                <script>
                /* <![CDATA[ */
                    CKEDITOR.replace( \'cattext\', {
                        customConfig : \'html_config.js\',
                        language : \'<?PHP echo $###MODULENAME###_adminlang; ?'.'>\'
                    });
                /* ]]> */
                </script>
                </div>';
	        }
            $result[] = array('<?PHP echo _MODMAKERLANGADMIN_CATDESCRIPT_; ?'.'>', $cattextvalue);
            $catjs = '<script src="<?PHP echo $webutlermodadmin->config[\'homepage\']; ?'.'>/includes/modexts/ckeditor/ckeditor.js"></script>';
            if(!in_array($catjs, $this->adminjs))
                $this->adminjs[] = $catjs;
        }
        if(in_array('catimage', $catfields)) {
            $result[] = array('<?PHP echo _MODMAKERLANGADMIN_CATIMAGE_; ?'.'>', '<div class="fakeupload"><input type="file" class="fileupload" size="53" name="catimage" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>" /></div><div class="filepreview"><?PHP echo $###MODULENAME###_class->getimageof(\'cats\', (isset($db_data[\'id\']) ? $db_data[\'id\'] : \'\'), \'catimage\'); ?'.'></div>');
	    }
	    
        return $result;
    }
    
    function setfieldstotpl()
    {
        $listtpl = $this->getfieldsbytpl('listtpl');
        if(!$listtpl) {
            $this->deletetplfile('data.tpl');
            $this->deletetplfromview('data');
        }
        else {
            $list = $this->makeelementlines($listtpl, true);
            if($this->adminvars['options'] == 1) {
                $list.= "###LOAD_OPTIONS_TPL###\n";
            }
            $this->savetplfiles('view/tpls/data.tpl', '###DB_VARS_DATA###', $list);
            if(in_array('topic_startdata', $this->viewtplfiles))
                $this->savetplfiles('view/tpls/topic_startdata.tpl', '###DB_VARS_TOPICSTARTDATA###', $list);
            if(in_array('topicslist_startdata', $this->viewtplfiles))
                $this->savetplfiles('view/tpls/topicslist_startdata.tpl', '###DB_VARS_TOPICSTARTDATA###', $list);
        }
        
        if(isset($this->sublinks['list_full']) && $this->sublinks['list_full'] == '1') {
            $fulltpl = $this->getfieldsbytpl('fulltpl');
            if(!$fulltpl) {
                $this->deletetplfile('datafull.tpl');
                $this->deletetplfromview('datafull');
            }
            else {
                $full = $this->makeelementlines($fulltpl, true);
				if($this->adminvars['options'] == 1) {
					$full.= "###LOAD_OPTIONS_TPL###\n";
				}
                $this->savetplfiles('view/tpls/datafull.tpl', '###DB_VARS_FULLDATA###', $full);
            }
        }
        else {
            $this->deletetplfile('datafull.tpl');
            $this->deletetplfromview('datafull');
        }
        
        if(!isset($this->sublinks['newtopic']) && !isset($this->sublinks['newdata']) && !isset($this->sublinks['newlink'])) {
            $this->deletetplfile('userinput.tpl');
            $this->deletetplfile('datanew.tpl');
            $this->deletetplfile('topicnew.tpl');
            $this->deletetplfromview('userinput');
            $this->deletetplfromview('datanew');
            $this->deletetplfromview('topicnew');
        }
        else {
            $inputtpl = $this->getfieldsbytpl('inputtpl');
            if(!$inputtpl) {
                $this->deletetplfile('userinput.tpl');
                $this->deletetplfile('datanew.tpl');
                $this->deletetplfile('topicnew.tpl');
                $this->deletetplfromview('userinput');
                $this->deletetplfromview('datanew');
                $this->deletetplfromview('topicnew');
            }
            else {
				$this->modconf['userinput'] = 1;
				$this->configuserinputtpls();
                $input = $this->makeelementlines($inputtpl, true);
                $this->savetplfiles('view/tpls/userinput.tpl', '###DB_VARS_INPUT###', $input);
            }
        }
        
	    $catstpl = $this->getfieldsforcat();
        if(is_array($catstpl)) {
            $this->savetplfiles('view/tpls/cat.tpl', '###DB_VARS_CATEGORY###', implode("\n", $catstpl));
            //if(file_exists($this->dbpath.'/'.$this->modname.'/view/tpls/subcat.tpl'))
            if(in_array('subcat', $this->viewtplfiles))
                $this->savetplfiles('view/tpls/subcat.tpl', '###DB_VARS_SUBCATEGORY###', implode("\n", $catstpl));
        }
        
		if($this->modconf['newest'] == '1') {
			$newesttpl = $this->getfieldsbytpl('newesttpl');
			if(!$newesttpl) {
				$this->deletetplfile('newest.tpl');
				$this->deletetplfromview('newest');
			}
			else {
				$newtop = '';
				if($this->adminvars['cats'] != '') {
					$newtop.= "\n".'<?PHP echo $db_data[\'catname\']; ?'.'><br />'."\n";
				}
				if($this->adminvars['topics'] != '') {
					$newtop.= "\n".'<?PHP echo $db_data[\'topic\']; ?'.'><br />'."\n";
				}
				
                $newest = $this->makeelementlines($newesttpl, true);
                $this->savetplfiles('view/tpls/newest.tpl', '###DB_VARS_INPUT###', $newtop.$newest);
			}
		}
		else {
			$this->deletetplfile('newest.tpl');
            $this->deletetplfromview('newest');
		}
        
		if($this->modconf['filter'] != '1') {
			$this->deletetplfile('filter.tpl');
            $this->deletetplfromview('filter');
		}
    }
    
    function configuserinputtpls()
    {
		$files = array('data', 'topic');
		foreach($files as $file) {
			$tplfile = $this->dbpath.'/'.$this->modname.'/view/tpls/'.$file.'new.tpl';
			if(file_exists($tplfile)) {
				$content = file_get_contents($tplfile);
				
				$upload = $this->modconf['userinput_hasfiles'] == 1 ? ' enctype="multipart/form-data"' : '';
				$content = str_replace('###ENCTYPE###', $upload, $content);
				if($this->modconf['getusers'] == '1') {
					$content = "\n<?PHP if(\$module->userisadmin || \$module->getwritepermission()) { ?".">\n".$content."\n<?PHP } ?".">\n";
				}
				
				file_put_contents($tplfile, $content);
			}
		}
    }
    
    function getfieldsforcat()
    {
        $catsdatas = $this->mmdb->query("SELECT id, projectid, modcats, cats FROM admin WHERE admin.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $catsdata = $catsdatas->fetchArray();
        if($catsdata['modcats'] == '1') {
	        $catfields = explode('_', $catsdata['cats']);
	        $result = array();
	        
	        $result[] = '<?PHP echo $db_data[\'catname\']; ?'.'><br />';
	        if(in_array('cattext', $catfields)) {
		        $result[] = '<?PHP echo $db_data[\'cattext\']; ?'.'><br />';
	        }
	        if(in_array('catlink', $catfields)) {
		        $result[] = '<?PHP echo $db_data[\'catlink\']; ?'.'><br />';
	        }
	        if(in_array('catimage', $catfields)) {
		        $result[] = '<?PHP if($db_data[\'catimage\'] != \'\') { ?'.'>'."\n".'	<img src="<?PHP echo $db_data[\'catimage\']; ?'.'>" /><br />'."\n".'<?PHP } ?>';
		    }
		    
	        return $result;
        }
        return false;
    }
    
    function getfieldsbytpl($tpl)
    {
        $elements = array();
        
        if($tpl == 'adminconf') {
            foreach($this->conforder as $ordervar) {
                $elements[] = $this->gethtmlelement($ordervar[3], $ordervar[0], $ordervar[1], $ordervar[5], $tpl);
            }
        }
        elseif($tpl == 'admindata') {
            foreach($this->filevars as $filevar) {
                $elements[] = $this->gethtmlelement($filevar[3], $filevar[0], $filevar[1], $filevar[5], $tpl);
            }
        }
        else {
            $tpldatas = $this->mmdb->query("SELECT id, projectid, tpldata FROM ".$tpl." WHERE ".$tpl.".projectid = '".$this->mmdb->escapeString($this->modid)."'");
            if($data = $tpldatas->fetchArray()) {
                if($data['tpldata'] != '') {
                    $fields = explode('|', $data['tpldata']);
                    if(is_array($fields)) {
                        foreach($this->filevars as $filevar) {
                            if(in_array($filevar[4], $fields)) {
								if($this->modconf['userinput_hasfiles'] == '' && $tpl == 'inputtpl' && in_array($filevar[3], array('file', 'image', 'multi'))) {
									$this->modconf['userinput_hasfiles'] = 1;
								}
                                $elements[] = $this->gethtmlelement($filevar[3], $filevar[2], $filevar[1], $filevar[5], $tpl, $filevar[0]);
                            }
                        }
                    }
                }
                else {
                    return false;
                }
            }
        }
        
        return $elements;
    }
    
    function deletetplfile($filename)
    {
        $file = $this->dbpath.'/'.$this->modname.'/view/tpls/'.$filename;
        if(file_exists($file))
            unlink($file);
    }
    
    function deletetplfromview($filename)
    {
        foreach($this->viewtplfiles as $key => $value) {
            if($value == $filename) {
                unset($this->viewtplfiles[$key]);
            }
        }
    }
    
    function makeelementlines($tpldata, $view, $setoptions = false, $seotrclass = '')
    {
        $html = '';
        $count = 0;
        foreach($tpldata as $data => $val) {
            $name = (isset($val[0])) ? $val[0] : '';
            $input = (isset($val[1])) ? $val[1] : '';
            $onlyadmin = (isset($val[2]) && $val[2] == 'onlyadmin') ? 1 : 0;
            if($input != '') {
                if($view == true) {
                    if($name != '')
                        $html.= "<?PHP echo ".$name."; ?".">\t\t";
                    
                    $html.= $input."\n\n";
                }
                else {
                    if($name != '') {
                        if($onlyadmin == 1) {
    						$html.= '<?PHP if($webutlermodadmin->checkadmin()) { ?'.'>'."\n".'<tr class="even">';
    					}
                        else {
    	                    $count = ($count+1);
							if($seotrclass != '') {
								$class = $seotrclass.' seofields';
								$this->seotrclass = '';
							}
							else {
								$class = ($count % 2 == 0) ? 'even' : 'odd';
								$this->seotrclass = ($class == 'odd') ? 'even' : 'odd';
							}
    						$html.= '<tr class="'.$class.'">';
    					}
                        $html.= '<td class="start">'.$name.':</td>'."\n";
                        $html.= '<td class="end">'.$input.'</td>
                        </tr>'."\n";
                        if($onlyadmin == 1) $html.= '<?PHP } ?'.'>';
                    }
                }
            }
        }
        if($setoptions == true) {
			if($this->adminvars['options'] == '1') {
				$this->seotrclass = $class;
				$optclass = ($class == 'odd') ? 'even' : 'odd';
				$html.= '<tr class="'.$optclass.'">'."\n".
					'<td class="start"><?PHP echo '.strtoupper('_'.$this->modname.'langadmin_options_').'; ?'.'>:</td>'."\n".
					'<td class="end"><?PHP echo $###MODULENAME###_class->checkoptions(); ?'.'></td>'."\n";
					'</tr>'."\n";
			}
		}
        
        return $html;
    }
    
    function savetplfiles($path, $search, $replace)
    {
        $file = $this->dbpath.'/'.$this->modname.'/'.$path;
        $content = file_get_contents($file);
        
    	if(is_array($search)) {
    		for($i = 0; $i < count($search); $i++) {
		        $content = str_replace($search[$i], $replace[$i], $content);
    		}
    	}
    	else {
	        $content = str_replace($search, $replace, $content);
        }
        
        file_put_contents($file, $content);
    }
    
    function adminlangforinputs($langvar, $value)
    {
    	$define = 'define(\''.strtoupper($langvar).'\',\''.$value.'\');';
    	if(!in_array($define, $this->adminlangs)) {
    		$this->adminlangs[] = $define;
    	}
    }
    
    function viewlangforinputs($langvar, $value)
    {
    	$define = 'define(\''.strtoupper($langvar).'\',\''.$value.'\');';
    	if(!in_array($define, $this->viewlangs)) {
    		$this->viewlangs[] = $define;
    	}
    }
    
    function getseoinputs($type)
    {
		$nametitle = 'seotitle';
		$namedesc = 'seodesc';
		$namekeys = 'seokeys';
		$admindefineseo = '_'.$this->modname.'langadmin_seoinput_metas_';
		$admindefinetitle = '_'.$this->modname.'langadmin_seoinput_metatitle_';
		$admindefinedesc = '_'.$this->modname.'langadmin_seoinput_metadesc_';
		$admindefinekeys = '_'.$this->modname.'langadmin_seoinput_metakeys_';
		
		$source = array();
		
		$source[$type.'seo'][0] = '<?PHP echo '.strtoupper($admindefineseo).'; ?'.'>';
		
		if($this->adminvars['langs'] == 'multi') {
			$source[$type.'seo'][1] = '<?PHP echo $###MODULENAME###_class->getlangflags(\''.$type.'seo\');
			foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
				<div id="bylang_'.$type.'seo_<?PHP echo $lang; ?'.'>"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'>>
					<input type="text" class="seoinput" name="'.$nametitle.'[<?PHP echo $lang; ?'.'>]" value="<?PHP if(isset($db_data[\''.$nametitle.'\'][$lang])) echo $db_data[\''.$nametitle.'\'][$lang]; ?'.'>" />
					<div class="seofield"><?PHP echo '.strtoupper($admindefinetitle).'; ?'.'></div>
					<textarea class="seoinput" name="'.$namedesc.'[<?PHP echo $lang; ?'.'>]"><?PHP if(isset($db_data[\''.$namedesc.'\'][$lang])) echo $db_data[\''.$namedesc.'\'][$lang]; ?'.'></textarea>
					<div class="seofield"><?PHP echo '.strtoupper($admindefinedesc).'; ?'.'></div>
					<textarea class="seoinput" name="'.$namekeys.'[<?PHP echo $lang; ?'.'>]"><?PHP if(isset($db_data[\''.$namekeys.'\'][$lang])) echo $db_data[\''.$namekeys.'\'][$lang]; ?'.'></textarea>
					<div class="seofield"><?PHP echo '.strtoupper($admindefinekeys).'; ?'.'></div>
				</div>
			<?PHP } ?'.'>';
		}
		else {
			$source[$type.'seo'][1] = '<input type="text" class="seoinput" name="'.$nametitle.'" value="<?PHP if(isset($db_data[\''.$nametitle.'\'])) echo $db_data[\''.$nametitle.'\']; ?'.'>" />
				<div class="seofield"><?PHP echo '.strtoupper($admindefinetitle).'; ?'.'></div>
				<textarea class="seoinput" name="'.$namedesc.'"><?PHP if(isset($db_data[\''.$namedesc.'\'])) echo $db_data[\''.$namedesc.'\']; ?'.'></textarea>
				<div class="seofield"><?PHP echo '.strtoupper($admindefinedesc).'; ?'.'></div>
				<textarea class="seoinput" name="'.$namekeys.'"><?PHP if(isset($db_data[\''.$namekeys.'\'])) echo $db_data[\''.$namekeys.'\']; ?'.'></textarea>
				<div class="seofield"><?PHP echo '.strtoupper($admindefinekeys).'; ?'.'></div>';
		}
		
		return $source;
    }
    
    function gethtmlelement($type, $field, $name, $typename, $tpl, $viewfieldlang = '')
    {
		$admindefine = '_'.$this->modname.'langadmin_input_'.$name.'_';
		$viewdefine = '_'.$this->modname.'lang_input_'.$name.'_';
        
        if($type == 'text') {
            if($tpl == 'adminconf') {
		    	$this->adminlangforinputs($admindefine, $field);
		        $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                $source[$type][1] = '<input type="radio" name="config[title]"<?PHP if(';
                if(!$this->titleisset)
					$source[$type][1].= '(!isset($###MODULENAME###_class->dbconfig[\'title\']) || $###MODULENAME###_class->dbconfig[\'title\'] == \'\') || ';
                $source[$type][1].= '(isset($###MODULENAME###_class->dbconfig[\'title\']) && $###MODULENAME###_class->dbconfig[\'title\'] == \''.$name.'\')) echo \' checked="checked"\'; ?'.'> value="'.$name.'" style="width: 12px" id="title_'.$name.'" /><label for="title_'.$name.'"><?PHP echo _MODMAKERLANGADMIN_FIELD_USEASTITLE_; ?'.'></label>';
                $this->titleisset = true;
            }
            elseif($tpl == 'admindata') {
		    	$this->adminlangforinputs($admindefine, $field);
		        $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
		        if($this->adminvars['langs'] == 'multi') {
		        	$source[$type][1] = '<?PHP echo $###MODULENAME###_class->getlangflags(\''.$name.'\');
		        	foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
	                <input type="text" id="bylang_'.$name.'_<?PHP echo $lang; ?'.'>"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'> name="'.$name.'[<?PHP echo $lang; ?'.'>]" value="<?PHP if(isset($db_data[\''.$name.'\'][$lang])) echo $db_data[\''.$name.'\'][$lang]; ?'.'>" />
					<?PHP } ?'.'>';
		        }
		        else {
	                $source[$type][1] = '<input type="text" name="'.$name.'" value="<?PHP if(isset($db_data[\''.$name.'\'])) echo $db_data[\''.$name.'\']; ?'.'>" />';
	            }
            }
            elseif($tpl == 'inputtpl') {
		    	$this->viewlangforinputs($viewdefine, $viewfieldlang);
		        /*
				$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
				*/
		        $source[$type][0] = strtoupper($viewdefine);
                $source[$type][1] = '<input type="text" name="'.$name.'" value="<?PHP echo $post_data[\''.$name.'\']; ?'.'>" /><br />';
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP echo $db_data[\''.$name.'\']; ?'.'><br />';
            }
        }
        elseif($type == 'area') {
            if($tpl == 'adminconf') {
                $source[$type][0] = '';
                $source[$type][1] = '';
            }
            elseif($tpl == 'admindata') {
		    	$this->adminlangforinputs($admindefine, $field);
		        $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
		        if($this->adminvars['langs'] == 'multi') {
		        	$source[$type][1] = '<?PHP echo $###MODULENAME###_class->getlangflags(\''.$name.'\');
		        	foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
	                <div id="bylang_'.$name.'_<?PHP echo $lang; ?'.'>"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'>><textarea name="'.$name.'[<?PHP echo $lang; ?'.'>]"><?PHP if(isset($db_data[\''.$name.'\'][$lang])) echo $db_data[\''.$name.'\'][$lang]; ?'.'></textarea></div>
					<?PHP } ?'.'>';
		        }
		        else {
	                $source[$type][1] = '<textarea name="'.$name.'"><?PHP if(isset($db_data[\''.$name.'\'])) echo $db_data[\''.$name.'\']; ?'.'></textarea>';
	            }
            }
            elseif($tpl == 'inputtpl') {
		    	$this->viewlangforinputs($viewdefine, $viewfieldlang);
		        /*
				$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
				*/
		        $source[$type][0] = strtoupper($viewdefine);
                $source[$type][1] = '<textarea name="'.$name.'"><?PHP echo $post_data[\''.$name.'\']; ?'.'></textarea><br />';
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP echo $db_data[\''.$name.'\']; ?'.'><br />';
            }
        }
        elseif($type == 'html') {
            if($tpl == 'adminconf') {
                $source[$type][0] = '';
                $source[$type][1] = '';
            }
            elseif($tpl == 'admindata' || $tpl == 'inputtpl') {
                if($tpl == 'admindata') {
			    	$this->adminlangforinputs($admindefine, $field);
			        $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
			        if($this->adminvars['langs'] == 'multi') {
			        	$source[$type][1] = '<?PHP echo $###MODULENAME###_class->getlangflags(\''.$name.'\');
			        	foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
		                <div id="bylang_'.$name.'_<?PHP echo $lang; ?'.'>" class="editorarea"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'>><textarea name="'.$name.'[<?PHP echo $lang; ?'.'>]"><?PHP if(isset($db_data[\''.$name.'\'][$lang])) echo $db_data[\''.$name.'\'][$lang]; ?'.'></textarea>
                        <script>
                        /* <![CDATA[ */
                            CKEDITOR.replace( \''.$name.'[<?PHP echo $lang; ?'.'>]\', {
                                customConfig : \'html_config.js\',
                                language : \'<?PHP echo $###MODULENAME###_adminlang; ?'.'>\'
                            });
                        /* ]]> */
                        </script>
                        </div>
						<?PHP } ?'.'>';
			        }
			        else {
	                    $source[$type][1] = '<div class="editorarea"><textarea name="'.$name.'"><?PHP if(isset($db_data[\''.$name.'\'])) echo $db_data[\''.$name.'\']; ?'.'></textarea>
                        <script>
                        /* <![CDATA[ */
                            CKEDITOR.replace( \''.$name.'\', {
                                customConfig : \'html_config.js\',
                                language : \'<?PHP echo $###MODULENAME###_adminlang; ?'.'>\'
                            });
                        /* ]]> */
                        </script>
                        </div>';
                    }
                }
                elseif($tpl == 'inputtpl') {
			    	$this->viewlangforinputs($viewdefine, $viewfieldlang);
			        /*
					$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
					*/
    		        $source[$type][0] = strtoupper($viewdefine);
                    $source[$type][1] = '<textarea name="'.$name.'"><?PHP echo $post_data[\''.$name.'\']; ?'.'></textarea><br />
                        <script>
                        /* <![CDATA[ */
                            CKEDITOR.replace( \''.$name.'\', {
                                customConfig : \'html_config.js\',
                                language : \'<?PHP echo (isset($_SESSION[\'language\']) && $_SESSION[\'language\'] != \'\' ? $_SESSION[\'language\'] : $webutlercouple->config[\'defaultlang\']); ?'.'>\'
                            });
                        /* ]]> */
                        </script>';
                }
                $ckejs = '<script src="';
				$ckejs.= ($tpl == 'admindata') ? '<?PHP echo $webutlermodadmin->config[\'homepage\']; ?'.'>' : '\'.$webutlercouple->config[\'homepage\'].\'';
				$ckejs.= '/includes/modexts/ckeditor/ckeditor.js"></script>';
				
                if($tpl == 'admindata' && !in_array($ckejs, $this->adminjs))
                    $this->adminjs[] = $ckejs;
                if($tpl == 'inputtpl' && !in_array($ckejs, $this->viewjs))
                    $this->viewjs[] = $ckejs;
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP echo $db_data[\''.$name.'\']; ?'.'><br />';
            }
        }
        elseif($type == 'bbcode') {
            if($tpl == 'adminconf') {
                $source[$type][0] = '';
                $source[$type][1] = '';
            }
            elseif($tpl == 'admindata' || $tpl == 'inputtpl') {
                if($tpl == 'admindata') {
			    	$this->adminlangforinputs($admindefine, $field);
			        $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
			        if($this->adminvars['langs'] == 'multi') {
			        	$source[$type][1] = '<?PHP echo $###MODULENAME###_class->getlangflags(\''.$name.'\');
			        	foreach($###MODULENAME###_class->langvars as $lang) { ?'.'>
		                <div id="bylang_'.$name.'_<?PHP echo $lang; ?'.'>" class="editorarea"<?PHP if($lang != $###MODULENAME###_adminlang) echo \' style="display: none"\'; ?'.'>><textarea name="'.$name.'[<?PHP echo $lang; ?'.'>]"><?PHP if(isset($db_data[\''.$name.'\'][$lang])) echo $db_data[\''.$name.'\'][$lang]; ?'.'></textarea>
                        <script>
                        /* <![CDATA[ */
                            CKEDITOR.replace( \''.$name.'[<?PHP echo $lang; ?'.'>]\', {
                                customConfig : \'bbcode_config.js\',
                                language : \'<?PHP echo $###MODULENAME###_adminlang; ?'.'>\'
                            });
                        /* ]]> */
                        </script>
                        </div>
						<?PHP } ?'.'>';
			        }
			        else {
	                    $source[$type][1] = '<div class="editorarea"><textarea name="'.$name.'"><?PHP if(isset($db_data[\''.$name.'\'])) echo $db_data[\''.$name.'\']; ?'.'></textarea>
                        <script>
                        /* <![CDATA[ */
                            CKEDITOR.replace( \''.$name.'\', {
                                customConfig : \'bbcode_config.js\',
                                language : \'<?PHP echo $###MODULENAME###_adminlang; ?'.'>\'
                            });
                        /* ]]> */
                        </script>
                        </div>';
	                }
                }
                elseif($tpl == 'inputtpl') {
			    	$this->viewlangforinputs($viewdefine, $viewfieldlang);
			        /*
					$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
					*/
    		        $source[$type][0] = strtoupper($viewdefine);
                    $source[$type][1] = '<textarea name="'.$name.'"><?PHP echo $post_data[\''.$name.'\']; ?'.'></textarea><br />
                        <script>
                        /* <![CDATA[ */
                            CKEDITOR.replace( \''.$name.'\', {
                                customConfig : \'bbcode_config.js\',
                                language : \'<?PHP echo (isset($_SESSION[\'language\']) && $_SESSION[\'language\'] != \'\' ? $_SESSION[\'language\'] : $webutlercouple->config[\'defaultlang\']); ?'.'>\'
                            });
                        /* ]]> */
                        </script>';
                }
                
                $ckejs = '<script src="';
				$ckejs.= ($tpl == 'admindata') ? '<?PHP echo $webutlermodadmin->config[\'homepage\']; ?'.'>' : '\'.$webutlercouple->config[\'homepage\'].\'';
				$ckejs.= '/includes/modexts/ckeditor/ckeditor.js"></script>';
				
                if($tpl == 'admindata' && !in_array($ckejs, $this->adminjs))
                    $this->adminjs[] = $ckejs;
                if($tpl == 'inputtpl' && !in_array($ckejs, $this->viewjs))
                    $this->viewjs[] = $ckejs;
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP echo $db_data[\''.$name.'\']; ?'.'><br />';
            }
        }
        elseif($type == 'user') {
            if($tpl == 'adminconf') {
                $source[$type][0] = '';
                $source[$type][1] = '';
			}
            elseif($tpl == 'admindata') {
		    	$this->adminlangforinputs($admindefine, $field);
			    $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                $source[$type][1] = '<?PHP echo isset($db_data[\''.$name.'\']) ? $db_data[\''.$name.'\'] : $###MODULENAME###_class->getusername(); ?'.'>';
            }
            elseif($tpl == 'inputtpl') {
		    	//$this->viewlangforinputs($viewdefine, $field);
				$langdefine = 'define(\''.strtoupper('_'.$this->modname.'lang_infield_'.$name.'_').'\',\''.$viewfieldlang.'\');';
				if(!in_array($langdefine, $this->viewlangs)) {
					$this->viewlangs[] = $langdefine;
				}
                $source[$type][0] = strtoupper('_'.$this->modname.'lang_infield_'.$name.'_');
				$source[$type][1] = '<?PHP if(isset($db_data[\''.$name.'\']) && $db_data[\''.$name.'\'] != \'\') { echo $db_data[\''.$name.'\']; } else { ?><input type="text" name="'.$name.'" value="<?PHP echo $post_data[\''.$name.'\']; ?>" /><?PHP } ?><br />';
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP echo $db_data[\''.$name.'\']; ?'.'><br />';
            }
        }
        elseif($type == 'date') {
            $dateformat = '_'.strtoupper($this->modname).'LANG_DATEFORMAT_';
            if($tpl == 'adminconf') {
                $source[$type][0] = '';
                $source[$type][1] = '';
            }
            elseif($tpl == 'admindata') {
			    $this->adminlangforinputs($admindefine, $field);
			    $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                $source[$type][1] = '<input type="text" name="'.$name.'" id="DPC_'.$name.'" value="<?PHP echo (isset($db_data[\''.$name.'\']) && $db_data[\''.$name.'\'] != \'\') ? strftime(\'%Y-%m-%d\', $db_data[\''.$name.'\']) : date(\'Y-m-d\'); ?'.'>" size="10" maxlength="10" style="width: 80px" readonly="readonly" /><img src="admin/icons/delete.png" title="<?PHP echo _MODMAKERLANGADMIN_FIELD_CLEARDATE_; ?'.'>" class="cleardate" onclick="cleardatefield(\''.$name.'\')" />';
                
                $datejs = '<script>'."\n".
                    '/* <![CDATA[ */'."\n".
                    '    var homepagepath = \'<?PHP echo $webutlermodadmin->config[\'homepage\']; ?'.'>\';'."\n".
                    '/* ]]> */'."\n".
                    '</script>'."\n".
                    '<script src="<?PHP echo $webutlermodadmin->config[\'homepage\']; ?'.'>/includes/modexts/calendar/datepickercontrol.js"></script>';
                if(!in_array($datejs, $this->adminjs))
                    $this->adminjs[] = $datejs;
            }
            elseif($tpl == 'inputtpl') {
			    $this->viewlangforinputs($viewdefine, $viewfieldlang);
			    /*
				$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
				*/
		        $source[$type][0] = strtoupper($viewdefine);
                $source[$type][1] = '<input type="text" name="'.$name.'" id="DPC_'.$name.'" value="<?PHP echo (isset($post_data[\''.$name.'\']) && $post_data[\''.$name.'\'] != \'\') ? $post_data[\''.$name.'\'] : \'\'; ?'.'>" size="10" maxlength="10" style="width: 80px" /><br />';
                
                $datejs = '<script>'."\n".
                    '/* <![CDATA[ */'."\n".
                    '    var homepagepath = \\\'\'.$webutlercouple->config[\'homepage\'].\'\\\';'."\n".
                    '/* ]]> */'."\n".
                    '</script>'."\n".
                    '<script src="\'.$webutlercouple->config[\'homepage\'].\'/includes/modexts/calendar/datepickercontrol.js"></script>';
                if(!in_array($datejs, $this->viewjs))
                    $this->viewjs[] = $datejs;
				
				/*
                $source[$type][1] = '<?PHP echo strftime('.$dateformat.', time()); ?'.'><input type="hidden" name="'.$name.'" value="<?PHP echo time(); ?'.'>" /><br />';
                //$source[$type][1] = '<?PHP echo date("'.$dateformat.'"); ?'.'>';
				*/
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP echo (isset($db_data[\''.$name.'\']) && $db_data[\''.$name.'\'] != \'\') ? strftime('.$dateformat.', $db_data[\''.$name.'\']) : \'\'; ?'.'><br />';
            }
        }
        elseif($type == 'number') {
            if($tpl == 'adminconf') {
                $source[$type][0] = '';
                $source[$type][1] = '';
            }
            elseif($tpl == 'admindata') {
			    $this->adminlangforinputs($admindefine, $field);
			    $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                $source[$type][1] = '<input type="text" class="numberfield" name="'.$name.'" value="<?PHP if(isset($db_data[\''.$name.'\'])) echo $db_data[\''.$name.'\']; ?'.'>" />';
            }
            elseif($tpl == 'inputtpl') {
    		    $this->viewlangforinputs($viewdefine, $viewfieldlang);
				$source[$type][0] = strtoupper($viewdefine);
                $source[$type][1] = '<input type="text" name="'.$name.'" value="<?PHP if(isset($post_data[\''.$name.'\'])) echo $post_data[\''.$name.'\']; ?'.'>" /><br />';
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP if(isset($db_data[\''.$name.'\'])) echo $db_data[\''.$name.'\']; ?'.'><br />';
            }
        }
        elseif($type == 'state') {
            if($tpl == 'adminconf') {
                $source[$type][0] = '';
                $source[$type][1] = '';
            }
            elseif($tpl == 'admindata') {
			    $this->adminlangforinputs($admindefine, $field);
			    $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                $source[$type][1] = '<input type="checkbox" name="'.$name.'" value="1"<?PHP if(isset($db_data[\''.$name.'\']) && $db_data[\''.$name.'\'] == \'1\') echo \' checked="checked"\'; ?'.'> style="width: auto" />';
            }
            elseif($tpl == 'inputtpl') {
			    $this->viewlangforinputs($viewdefine, $viewfieldlang);
		        $source[$type][0] = strtoupper($viewdefine);
                $source[$type][1] = '<input type="checkbox" name="'.$name.'" value="1"<?PHP if(isset($post_data[\''.$name.'\']) && $post_data[\''.$name.'\'] == \'1\') echo \' checked="checked"\'; ?'.'> /><br />';
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP if(isset($db_data[\''.$name.'\']) && $db_data[\''.$name.'\'] == \'1\') { ?'.'><?PHP } ?'.'><br />';
			}
        }
        elseif($type == 'select') {
            if($tpl == 'adminconf') {
				$this->adminlangforinputs(str_replace('_MAKEMODLANG_', '_'.$this->modname.'langadmin_', $typename), constant($typename));
			    $this->adminlangforinputs($admindefine, $field);
			    $source[$type][0] = '<?PHP echo '.str_replace('_MAKEMODLANG_', strtoupper('_'.$this->modname.'langadmin_'), $typename).'.\'<br />(\'.'.strtoupper($admindefine).'.\')\'; ?'.'>';
                //$source[$type][0] = $typename.'<br />('.$field.')';
                $source[$type][1] = '<strong><?PHP echo _MODMAKERLANGADMIN_FIELD_SELECTOPTIONS_; ?'.'>:</strong><br />
                    <?PHP echo _MODMAKERLANGADMIN_FIELD_SELECTPROTOTYPE_; ?'.'><br />
                    <textarea name="select['.$name.']" class="selschecks"><?PHP echo $###MODULENAME###_class->dbselects[\''.$name.'\']; ?'.'></textarea><br />
                    <?PHP echo _MODMAKERLANGADMIN_FIELD_SELECTLEEROPT_; ?'.'><br />'."\n";
			        if($this->adminvars['langs'] == 'multi') {
		                $source[$type][1].= '<?PHP echo _MODMAKERLANGADMIN_FIELD_OPTWITHDEFINES_; ?'.'><br />'."\n";
                	}
	                $source[$type][1].= '<?PHP echo _MODMAKERLANGADMIN_FIELD_OPTASSELECTED_; ?'.'>'."\n";
	                $source[$type][1].= '<div class="showasradioboxes">'."\n";
					$source[$type][1].= '<input type="checkbox" name="config['.$name.'asradio]"<?PHP if(isset($###MODULENAME###_class->dbconfig[\''.$name.'asradio\']) && $###MODULENAME###_class->dbconfig[\''.$name.'asradio\'] == \'on\') echo \' checked="checked"\'; ?'.'> style="width: 12px" id="'.$name.'asradio"><label for="'.$name.'asradio"><?PHP echo _MODMAKERLANGADMIN_FIELD_SHOWSELECTASRADIO_; ?'.'></label>';
	                $source[$type][1].= '</div>'."\n";
            }
            else {
	            if($tpl == 'admindata') {
    			    $this->adminlangforinputs($admindefine, $field);
	                $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                    $source[$type][1] = '<?PHP echo $###MODULENAME###_class->getselectfields(\''.$name.'\', (isset($db_data[\''.$name.'\']) ? $db_data[\''.$name.'\'] : \'\')); ?'.'>';
	            }
            	elseif($tpl == 'inputtpl') {
    		    	$this->viewlangforinputs($viewdefine, $viewfieldlang);
	                /*
					$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
					*/
    		        $source[$type][0] = strtoupper($viewdefine);
                    $source[$type][1] = '<?PHP echo $module->getselectfields(\''.$name.'\', (isset($db_data[\''.$name.'\']) ? $db_data[\''.$name.'\'] : \'\'), true); ?'.'><br />';
                }
	            else {
                	$langdefine = 'define(\''.strtoupper('_'.$this->modname.'lang_infield_'.$name.'_').'\',\''.$viewfieldlang.'\');';
                	if(!in_array($langdefine, $this->viewlangs)) {
                		$this->viewlangs[] = $langdefine;
                	}
	                /*
					$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
					*/
    		        $source[$type][0] = strtoupper('_'.$this->modname.'lang_infield_'.$name.'_');
                    $source[$type][1] = '<?PHP echo $db_data[\''.$name.'\'][0]; ?'.'> <?PHP echo $db_data[\''.$name.'\'][1]; ?'.'><br />';
                }
				//echo 'type: '.$type.'<br>field: '.$field.'<br>name: '.$name.'<br>typename: '.$typename.'<br>tpl: '.$tpl.'<br>source: '.$source[$type][1].'<br><br>';
            }
        }
        elseif($type == 'checkbox') {
            if($tpl == 'adminconf') {
				$this->adminlangforinputs(str_replace('_MAKEMODLANG_', '_'.$this->modname.'langadmin_', $typename), constant($typename));
			    $this->adminlangforinputs($admindefine, $field);
			    $source[$type][0] = '<?PHP echo '.str_replace('_MAKEMODLANG_', strtoupper('_'.$this->modname.'langadmin_'), $typename).'.\'<br />(\'.'.strtoupper($admindefine).'.\')\'; ?'.'>';
                //$source[$type][0] = $typename.'<br />('.$field.')';
                $source[$type][1] = '<strong><?PHP echo _MODMAKERLANGADMIN_FIELD_CHECKBOX_; ?'.'>:</strong><br />
                    <?PHP echo _MODMAKERLANGADMIN_FIELD_CHECKPROTOTYPE_; ?'.'><br />
                    <textarea name="checkbox['.$name.']" class="selschecks"><?PHP echo $###MODULENAME###_class->dbchecks[\''.$name.'\']; ?'.'></textarea><br />
                    <?PHP echo _MODMAKERLANGADMIN_FIELD_CHECKLEEROPT_; ?'.'><br />'."\n";
			        if($this->adminvars['langs'] == 'multi') {
		                $source[$type][1].= '<?PHP echo _MODMAKERLANGADMIN_FIELD_BOXWITHDEFINES_; ?'.'><br />'."\n";
                	}
	                $source[$type][1].= '<?PHP echo _MODMAKERLANGADMIN_FIELD_BOXASCHECKED_; ?'.'>';
            }
            else {
	            if($tpl == 'admindata') {
    			    $this->adminlangforinputs($admindefine, $field);
	                $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                    $source[$type][1] = '<?PHP echo $###MODULENAME###_class->getcheckboxfields(\''.$name.'\', (isset($db_data[\''.$name.'\']) ? $db_data[\''.$name.'\'] : \'\')); ?'.'>';
	            }
            	elseif($tpl == 'inputtpl') {
    		    	$this->viewlangforinputs($viewdefine, $viewfieldlang);
	                /*
					$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
					*/
    		        $source[$type][0] = strtoupper($viewdefine);
                    $source[$type][1] = '<?PHP echo $module->getcheckboxfields(\''.$name.'\', (isset($db_data[\''.$name.'\']) ? $db_data[\''.$name.'\'] : \'\'), true); ?'.'><br />';
                }
	            else {
	                $source[$type][0] = '';
                    $source[$type][1] = '###LOAD_'.strtoupper($name).'_TPL###';
                }
            }
        }
        elseif($type == 'hidden') {
            if($tpl == 'adminconf') {
                $source[$type][0] = '';
                $source[$type][1] = '';
            }
            elseif($tpl == 'admindata') {
			    $this->adminlangforinputs($admindefine, $field);
			    $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                $source[$type][1] = '<input type="text" name="'.$name.'" value="<?PHP if(isset($db_data[\''.$name.'\'])) echo $db_data[\''.$name.'\']; ?'.'>" />';
            }
            elseif($tpl == 'inputtpl') {
    		    $this->viewlangforinputs($viewdefine, $viewfieldlang);
				$source[$type][0] = strtoupper($viewdefine);
                $source[$type][1] = '<input type="hidden" name="'.$name.'" value="<?PHP if(isset($post_data[\''.$name.'\'])) echo $post_data[\''.$name.'\']; ?'.'>" /><br />';
				/*
			    $source[$type][0] = '';
                $source[$type][1] = '';
				*/
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP if(isset($db_data[\''.$name.'\'])) echo $db_data[\''.$name.'\']; ?'.'><br />';
            }
        }
        elseif($type == 'image') {
            if($tpl == 'adminconf') {
				if($this->modconf['autolightbox'] == 1) {
					$this->adminlangforinputs(str_replace('_MAKEMODLANG_', '_'.$this->modname.'langadmin_', $typename), constant($typename));
					$this->adminlangforinputs($admindefine, $field);
					$source[$type][0] = '<?PHP echo '.str_replace('_MAKEMODLANG_', strtoupper('_'.$this->modname.'langadmin_'), $typename).'.\'<br />(\'.'.strtoupper($admindefine).'.\')\'; ?'.'>';
					//$source[$type][0] = $typename.'<br />('.$field.')';
					$source[$type][1] = '<input type="checkbox" name="config[lightbox]['.$name.']"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'lightbox\'][\''.$name.'\']) && $###MODULENAME###_class->dbconfig[\'lightbox\'][\''.$name.'\'] == \'on\') echo \' checked="checked"\'; ?'.'> style="width: 12px" id="lightbox_'.$name.'" /><label for="lightbox_'.$name.'"><?PHP echo _MODMAKERLANGADMIN_FIELD_BOXOPEN_; ?'.'></label>';
				}
            }
            elseif($tpl == 'admindata' || $tpl == 'inputtpl') {
                if($tpl == 'admindata') {
				    $this->adminlangforinputs($admindefine, $field);
				    $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                    $source[$type][1] = '<div class="fakeupload"><input type="file" class="fileupload" size="53" name="'.$name.'" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>" /></div>';
                    $source[$type][1].= '<div class="filepreview">
                        <?PHP echo $###MODULENAME###_class->getimageof(\'datas\', (isset($db_data[\'id\']) ? $db_data[\'id\'] : \'\'), \''.$name.'\'); ?'.'>
                        </div>';
			    }
            	if($tpl == 'inputtpl') {
				    $this->viewlangforinputs($viewdefine, $viewfieldlang);
				    /*
					$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
					*/
    		        $source[$type][0] = strtoupper($viewdefine);
                    $source[$type][1] = '<input type="file" size="50" name="'.$name.'" /><br />';
            	}
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP if($db_data[\''.$name.'\'] != \'\') { ?'.'>'."\n".'	<img src="<?PHP echo $db_data[\''.$name.'\']; ?'.'>" /><br />'."\n".'<?PHP } ?>';
            }
        }
        elseif($type == 'multi') {
            if($tpl == 'adminconf') {
				if($this->modconf['autolightbox'] == 1) {
					$this->adminlangforinputs(str_replace('_MAKEMODLANG_', '_'.$this->modname.'langadmin_', $typename), constant($typename));
					$this->adminlangforinputs($admindefine, $field);
					$source[$type][0] = '<?PHP echo '.str_replace('_MAKEMODLANG_', strtoupper('_'.$this->modname.'langadmin_'), $typename).'.\'<br />(\'.'.strtoupper($admindefine).'.\')\'; ?'.'>';
					//$source[$type][0] = $typename.'<br />('.$field.')';
					$source[$type][1] = '<input type="checkbox" name="config[lightbox]['.$name.']"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'lightbox\'][\''.$name.'\']) && $###MODULENAME###_class->dbconfig[\'lightbox\'][\''.$name.'\'] == \'on\') echo \' checked="checked"\'; ?'.'> style="width: 12px" id="lightbox_'.$name.'" /><label for="lightbox_'.$name.'"><?PHP echo _MODMAKERLANGADMIN_FIELD_BOXOPENS_; ?'.'></label>';
				}
            }
            elseif($tpl == 'admindata' || $tpl == 'inputtpl') {
                if($tpl == 'admindata') {
				    $this->adminlangforinputs($admindefine, $field);
				    $source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
                    $source[$type][1] = '<div class="multinewimg">
                        <?PHP echo _MODMAKERLANGADMIN_FIELD_PLUSIMGUPLOAD_; ?'.'> <img src="admin/icons/plus.png" onclick="newmulti'.$name.'()" />
                        </div>
                        <div id="multi'.$name.'" class="multilines">
                        <div class="fakeupload"><input type="file" class="fileupload" size="53" name="'.$name.'[1]" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>" /></div>
                        </div>
                        <div class="filepreview">
                        <?PHP echo $###MODULENAME###_class->getimageof(\'datas\', (isset($db_data[\'id\']) ? $db_data[\'id\'] : \'\'), \''.$name.'\'); ?'.'>
                        </div>';
                    
                    $multiadminjs = '<script>'."\n".
                        '/* <![CDATA[ */'."\n".
                        'function newmulti'.$name.'() {'."\n".
                        '   var count = document.getElementById(\'multi'.$name.'\').getElementsByTagName(\'div\').length;'."\n".
                        '   var newFakediv = document.createElement(\'div\');'."\n".
                        '   var addFakediv = document.getElementById(\'multi'.$name.'\').appendChild(newFakediv);'."\n".
                        '   addFakediv.className = \'fakeupload\';'."\n\n".
                        '   var newFile = document.createElement(\'input\');'."\n".
                        '   var addFile = newFakediv.appendChild(newFile);'."\n".
                        '   addFile.type = \'file\';'."\n".
                        '   addFile.size = \'53\';'."\n".
                        '   addFile.className = \'fileupload\';'."\n".
                        '   addFile.name = \''.$name.'[\' + parseInt(count+1) + \']\';'."\n".
                        '   addFile.onchange = new Function(\'setuploadpath(this)\');'."\n\n".
                        '   var newFilefield = document.createElement(\'input\');'."\n".
                        '   var addFilefield = newFakediv.appendChild(newFilefield);'."\n".
                        '   addFilefield.type = \'text\';'."\n".
                        '   addFilefield.className = \'fakefield\';'."\n\n".
                        '   var newFilebutton = document.createElement(\'input\');'."\n".
                        '   var addFilebutton = newFakediv.appendChild(newFilebutton);'."\n".
                        '   addFilebutton.type = \'text\';'."\n".
                        '   addFilebutton.value = \'<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>\';'."\n".
                        '   addFilebutton.className = \'fakebutton\';'."\n".
                        '}'."\n".
                        '/* ]]> */'."\n".
                        '</script>';
                    if(!in_array($multiadminjs, $this->adminjs))
                        $this->adminjs[] = $multiadminjs;
			    }
            	if($tpl == 'inputtpl') {
				    $this->viewlangforinputs($viewdefine, $viewfieldlang);
				    /*
					$source[$type][0] = '<?PHP echo '.strtoupper($viewdefine).'; ?'.'>';
					*/
    		        $source[$type][0] = strtoupper($viewdefine);
                    $source[$type][1] = '<div class="multinewimg">
                        <?PHP echo _MODMAKERLANG_FIELD_PLUSIMGUPLOAD_; ?'.'> <img src="modules/<?PHP echo $module->modname; ?'.'>/admin/icons/plus.png" onclick="newmulti'.$name.'()" />
                        </div>
                        <div id="multi'.$name.'">
                        <input type="file" size="50" name="'.$name.'[1]" /><br />
                        </div>';
                    
                    $multiviewjs = '<script>'."\n".
                        '/* <![CDATA[ */'."\n".
                        'function newmulti'.$name.'() {'."\n".
                        '   var count = document.getElementById(\\\'multi'.$name.'\\\').getElementsByTagName(\\\'input\\\').length;'."\n".
                        '   var newFile = document.createElement(\\\'input\\\');'."\n".
                        '   var addFile = document.getElementById(\\\'multi'.$name.'\\\').appendChild(newFile);'."\n".
                        '   addFile.type = \\\'file\\\';'."\n".
                        '   addFile.size = \\\'50\\\';'."\n".
                        '   addFile.name = \\\''.$name.'[\\\' + parseInt(count+1) + \\\']\\\';'."\n".
                        '   var newBr = document.createElement(\\\'br\\\');'."\n".
                        '   var addBr = document.getElementById(\\\'multi'.$name.'\\\').appendChild(newBr);'."\n".
                        '}'."\n".
                        '/* ]]> */'."\n".
                        '</script>';
                    if(!in_array($multiviewjs, $this->viewjs))
                        $this->viewjs[] = $multiviewjs;
            	}
            }
            else {
                $source[$type][0] = '';
                $source[$type][1] = '###LOAD_'.strtoupper($name).'_TPL###';
            }
        }
        elseif($type == 'file') {
            if($tpl == 'adminconf') {
				$this->adminlangforinputs(str_replace('_MAKEMODLANG_', '_'.$this->modname.'langadmin_', $typename), constant($typename));
			    $this->adminlangforinputs($admindefine, $field);
			    $source[$type][0] = '<?PHP echo '.str_replace('_MAKEMODLANG_', strtoupper('_'.$this->modname.'langadmin_'), $typename).'.\'<br />(\'.'.strtoupper($admindefine).'.\')\'; ?'.'>';
                //$source[$type][0] = $typename.'<br />('.$field.')';
                $source[$type][1] = '<strong class="isfor"><?PHP echo _MODMAKERLANGADMIN_FIELD_ONLYMIMEFILE_; ?'.'>:</strong>
					<input type="text" name="config[savemime]['.$name.']" value="<?PHP if(isset($###MODULENAME###_class->dbconfig[\'savemime\'][\''.$name.'\'])) echo $###MODULENAME###_class->dbconfig[\'savemime\'][\''.$name.'\']; ?'.'>" /><br />
                	<?PHP echo _MODMAKERLANGADMIN_FIELD_MIMEEXPLAIN_1_; ?'.'><br />
                	<?PHP echo _MODMAKERLANGADMIN_FIELD_MIMEEXPLAIN_2_; ?'.'>
					<div class="uploadcontrol">
					<input type="checkbox" name="config[saveload]['.$name.'][0]"<?PHP if(isset($###MODULENAME###_class->dbconfig[\'saveload\'][\''.$name.'\'][0]) && $###MODULENAME###_class->dbconfig[\'saveload\'][\''.$name.'\'][0] == \'on\') echo \' checked="checked"\'; ?'.'> style="width: 12px" id="save_'.$name.'" /><label for="save_'.$name.'"><?PHP echo _MODMAKERLANGADMIN_FIELD_PROTECTFILE_; ?'.'></label>
                    </div>
                    <strong class="isfor"><?PHP echo _MODMAKERLANGADMIN_FIELD_DOWNLOADFORGROUP_; ?'.'>:</strong>
                    <select name="config[saveload]['.$name.'][1][]" size="5" multiple="multiple">
                    <option value="all"<?PHP echo $###MODULENAME###_class->setallusersselection(isset($###MODULENAME###_class->dbconfig[\'saveload\'][\''.$name.'\'][1]) ? $###MODULENAME###_class->dbconfig[\'saveload\'][\''.$name.'\'][1] : \'\'); ?'.'>><?PHP echo _MODMAKERLANGADMIN_FIELD_DOWNLOADFORALL_; ?'.'></option>
                    <?PHP echo $###MODULENAME###_class->getusergroupids(isset($###MODULENAME###_class->dbconfig[\'saveload\'][\''.$name.'\'][1]) ? $###MODULENAME###_class->dbconfig[\'saveload\'][\''.$name.'\'][1] : \'\'); ?'.'>
                    </select><br />
                    <?PHP echo _MODMAKERLANGADMIN_FIELD_MULTISELECT_; ?'.'>'."\n";
            }
            elseif($tpl == 'admindata') {
				$this->adminlangforinputs($admindefine, $field);
				$source[$type][0] = '<?PHP echo '.strtoupper($admindefine).'; ?'.'>';
				$source[$type][1] = '<?PHP echo $###MODULENAME###_class->getuploadtypes(\''.$name.'\'); ?'.'>';
				$source[$type][1].= '<div class="fakeupload"><input type="file" class="fileupload" size="53" name="'.$name.'" onchange="setuploadpath(this, \''.$name.'\'<?PHP echo ((isset($db_data[\'dataid\']) ? \', \'.$db_data[\'dataid\'] : \'\')); ?'.'>)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _MODMAKERLANGADMIN_CHOOSE_UPLOAD_; ?'.'>" /></div>
				<div class="filepreview">
				<?PHP echo $###MODULENAME###_class->getfileof((isset($db_data[\'id\']) ? $db_data[\'id\'] : \'\'), \''.$name.'\'); ?'.'>
				</div>';
				$source[$type][1].= '<div class="filedownloads">
				<?PHP echo (isset($db_data[\''.$name.'_counter\']) && $db_data[\''.$name.'_counter\'] != \'\' ? $db_data[\''.$name.'_counter\'] : \'0\').'.strtoupper('_'.$this->modname.'langadmin_output_downloadcounter_').'; ?'.'>
				</div>';
				
				$fileadminjs = '<?PHP if(isset($_GET[\'topic\']) || isset($_GET[\'data\'])) { ?'.'>'."\n".
					'<script>'."\n".
					'/* <![CDATA[ */'."\n".
					'	var uploadmaxsize = <?PHP echo $###MODULENAME###_class->getuploadmaxsize(); ?'.'>;'."\n".
					'	var uploadfiletypes = <?PHP echo $###MODULENAME###_class->getuploadfiletypes(); ?'.'>;'."\n".
					'	var uploadwrongmime = \'<?PHP echo '.strtoupper('_'.$this->modname.'langadmin_alert_uploadwrongmime_').'; ?'.'>\';'."\n".
					'	var uploadcomplete = \'<?PHP echo '.strtoupper('_'.$this->modname.'langadmin_alert_uploadcomplete_').'; ?'.'>\';'."\n".
					'/* ]]> */'."\n".
					'</script>'."\n".
					'<?PHP } ?'.'>'."\n";
				
				if(!in_array($fileadminjs, $this->adminjs))
					$this->adminjs[] = $fileadminjs;
			}
			elseif($tpl == 'inputtpl') {
				$this->viewlangforinputs($viewdefine, $viewfieldlang);
				$source[$type][0] = strtoupper($viewdefine);
				$source[$type][1] = '<input type="file" size="50" name="'.$name.'" /><br />';
			}
            else {
                $source[$type][0] = '';
                $source[$type][1] = '<?PHP if($module->getdownloadgroup(\''.$name.'\')) { ?'.'><a href="<?PHP echo $db_data[\'link_of_'.$name.'\']; ?'.'>"><?PHP echo $db_data[\''.$name.'\']; ?'.'></a><br /><?PHP echo (isset($db_data[\''.$name.'_counter\']) && $db_data[\''.$name.'_counter\'] != \'\' ? $db_data[\''.$name.'_counter\'] : \'0\').'.strtoupper('_'.$this->modname.'lang_field_downloadcounter_').'; ?'.'><?PHP } ?'.'><br />';
            }
        }
        return $source[$type];
    }
    
    function setmodnametofiles()
    {
        $this->savetplfiles('admin.php', '###MODULENAME###', $this->modname);
        $this->savetplfiles('view.php', '###MODULENAME###', $this->modname);
        $this->savetplfiles('view.php', '_MODMAKERLANG_', '_'.strtoupper($this->modname).'LANG_');
        
        $cssfile = $this->dbpath.'/'.$this->modname.'/view/';
        rename($cssfile.'cssfile.css', $cssfile.$this->modname.'.css');
    }
    
    function makelinkline()
    {
        $tplpath = $this->dbpath.'/'.$this->modname.'/view/tpls/';
        
        if(in_array('cat', $this->viewtplfiles)) {
            $link_catsdata = '';
            if(isset($this->sublinks['cat_topic']))
                $link_catsdata.= '<a href="###CATEGORY_LINK###"><?PHP echo _MODMAKERLANG_TOTOPICLIST_; ?'.'></a><br />';
            if(isset($this->sublinks['cat_list']))
                $link_catsdata.= '<a href="###CATEGORY_LINK###"><?PHP echo _MODMAKERLANG_TODATALIST_; ?'.'></a><br />';
            
            $this->savetplfiles('view/tpls/cat.tpl', '###LINK_TO_CAT###', $link_catsdata);
            
            if(in_array('subcat', $this->viewtplfiles))
                $this->savetplfiles('view/tpls/subcat.tpl', '###LINK_TO_SUB###', $link_catsdata);
        }
        
        if(in_array('catslist', $this->viewtplfiles) && $this->modconf['subcats'] == 1) {
            $this->savetplfiles('view/tpls/catslist.tpl', '###LOAD_PAGER###', '');
            
            if(in_array('subcatslist', $this->viewtplfiles)) {
                $link_backtocat = '<a href="###BACK_CATEGORY_LINK###"><?PHP echo _MODMAKERLANG_BACKTOMAINCATEGORY_; ?'.'></a><br />';
                $this->savetplfiles('view/tpls/subcatslist.tpl', '###BACK_TO_CATEGORY###', $link_backtocat);
            }
        }
        
        if(in_array('topicslist', $this->viewtplfiles)) {
            $loadsubcats_backtocat = '';
            if(in_array('subcatslist', $this->viewtplfiles))
                $loadsubcats_backtocat = '###BREADCRUMB###<br /><br />'."\n\n".'###LOAD_SUB_CATEGORIES###';
            elseif(in_array('cat', $this->viewtplfiles))
                $loadsubcats_backtocat = '<a href="###BACK_CATEGORY_LINK###"><?PHP echo _MODMAKERLANG_BACKTOCATEGORY_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/topicslist.tpl', '###LOADSUBCATS_OR_BACKTOCAT###', $loadsubcats_backtocat);
            
            $link_newtopic = '';
            if(isset($this->sublinks['newtopic']) && in_array('topicnew', $this->viewtplfiles)) {
                if(isset($this->sublinks['newdata']))
                    $link_newtopic = '###LOAD_TOPICINPUT###';
                elseif(isset($this->sublinks['newlink']))
                    $link_newtopic = '<a href="###LINK_NEWTOPIC###"><?PHP echo _MODMAKERLANG_NEWTOPIC_; ?'.'></a><br />';
            }
            
            if($this->modconf['getusers'] == '1' && $link_newtopic != '')
                $link_newtopic = "<?PHP if(\$module->userisadmin || \$module->getwritepermission()) { ?".">\n".$link_newtopic."\n<?PHP } ?".">";
        
            $this->savetplfiles('view/tpls/topicslist.tpl', '###LINK_OR_INPUT_NEWTOPIC###', $link_newtopic);
        }
        
        if(in_array('topic', $this->viewtplfiles)) {
            $loadsubcats_backtocat = '';
            if(in_array('subcatslist', $this->viewtplfiles))
                $loadsubcats_backtocat = '###BREADCRUMB###<br /><br />'."\n\n".'###LOAD_SUB_CATEGORIES###';
            elseif(in_array('cat', $this->viewtplfiles))
                $loadsubcats_backtocat = '<a href="###BACK_CATEGORY_LINK###"><?PHP echo _MODMAKERLANG_BACKTOCATEGORY_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/topic.tpl', '###LOADSUBCATS_OR_BACKTOCAT###', $loadsubcats_backtocat);
            
            $topicfield = '<?PHP echo $db_data[\'topic\']; ?'.'><br />';
        
            $this->savetplfiles('view/tpls/topic.tpl', '###DB_VARS_TOPIC###', $topicfield);
            
            $link_topiclist = '';
            if(isset($this->sublinks['topic_list']))
                $link_topiclist = '<a href="###LINK_TOPIC_TO_LIST###"><?PHP echo _MODMAKERLANG_BACKTOTOPICSLIST_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/topic.tpl', '###LINK_BACK_TO_LIST###', $link_topiclist);
            
            $link_topicprev = '<a href="###LINK_PREV_TOPIC###"><?PHP echo _MODMAKERLANG_PREV_TOPIC_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/topic.tpl', '###LINK_TOPIC_PREV###', $link_topicprev);
            
            $link_topicnext = '<a href="###LINK_NEXT_TOPIC###"><?PHP echo _MODMAKERLANG_NEXT_TOPIC_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/topic.tpl', '###LINK_TOPIC_NEXT###', $link_topicnext);
            
            $link_newdata = '';
            if(in_array('datanew', $this->viewtplfiles)) {
                if(isset($this->sublinks['newdata']))
                    $link_newdata = '###LOAD_NEWDATA_TPL###';
                    
                if(isset($this->sublinks['newlink']))
                    $link_newdata = '<a href="###LINK_NEWDATA###"><?PHP echo _MODMAKERLANG_NEWDATA_; ?'.'></a><br />';
            }
                
            if($this->modconf['getusers'] == '1' && $link_newdata != '')
                $link_newdata = "<?PHP if(\$module->userisadmin || \$module->getwritepermission()) { ?".">\n".$link_newdata."\n<?PHP } ?".">";
            
            $this->savetplfiles('view/tpls/topic.tpl', '###LINK_OR_INPUT_NEWTOPICDATA###', $link_newdata);
			
			if(!in_array('filter', $this->viewtplfiles)) {
				$this->savetplfiles('view/tpls/topic.tpl', '###LOAD_FILTER###', '');
			}
        }
        
        if(in_array('topicdata', $this->viewtplfiles)) {
            $topicfield = '<?PHP echo $db_data[\'topic\']; ?'.'><br />';
			
			if($this->modconf['disttopicstart'] == '1')
				$topicfield.= "\n\n".'###LOAD_TOPICDATA###';
        
            $this->savetplfiles('view/tpls/topicdata.tpl', '###DB_VARS_TOPIC###', $topicfield);
            
            //$this->savetplfiles('view/tpls/topicdata.tpl', '###LINK_OR_INPUT_NEWTOPICDATA###', $link_newdata);
            
            $link_totopic = '<a href="###TOPIC_LINK###"><?PHP echo _MODMAKERLANG_TOTOPIC_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/topicdata.tpl', '###LINK_TO_TOPIC###', $link_totopic);
        }
        
        if(in_array('dataslist', $this->viewtplfiles)) {
            $loadsubcats_backtocat = '';
            if(in_array('subcatslist', $this->viewtplfiles))
                $loadsubcats_backtocat = '###BREADCRUMB###<br /><br />'."\n\n".'###LOAD_SUB_CATEGORIES###';
            elseif(in_array('cat', $this->viewtplfiles))
                $loadsubcats_backtocat = '<a href="###BACK_CATEGORY_LINK###"><?PHP echo _MODMAKERLANG_BACKTOCATEGORY_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/dataslist.tpl', '###LOADSUBCATS_OR_BACKTOCAT###', $loadsubcats_backtocat);
            
            $link_newdata = '';
            if(in_array('datanew', $this->viewtplfiles)) {
                if(isset($this->sublinks['newdata']))
                    $link_newdata = '###LOAD_NEWDATA_TPL###';
                    
                if(isset($this->sublinks['newlink']))
                    $link_newdata = '<a href="###LINK_NEWDATA###"><?PHP echo _MODMAKERLANG_NEWDATA_; ?'.'></a><br />';
            }
                
            if($this->modconf['getusers'] == '1' && $link_newdata != '')
                $link_newdata = "<?PHP if(\$module->userisadmin || \$module->getwritepermission()) { ?".">\n".$link_newdata."\n<?PHP } ?".">";
            
            $this->savetplfiles('view/tpls/dataslist.tpl', '###LINK_OR_INPUT_NEWDATA###', $link_newdata);
			
			if(!in_array('filter', $this->viewtplfiles)) {
				$this->savetplfiles('view/tpls/dataslist.tpl', '###LOAD_FILTER###', '');
			}
        }
        
        if(in_array('data', $this->viewtplfiles)) {
            $link_listdata = '';
            if(isset($this->sublinks['list_full']) && in_array('datafull', $this->viewtplfiles))
                $link_listdata = '<a href="###LINK_FULLDATA###"><?PHP echo _MODMAKERLANG_TODATA_; ?'.'></a><br />';
            
            $this->savetplfiles('view/tpls/data.tpl', '###LINK_TO_FULL###', $link_listdata);
            
            if(in_array('topic_startdata', $this->viewtplfiles))
                $this->savetplfiles('view/tpls/topic_startdata.tpl', '###LINK_TO_FULL###', $link_listdata);
            if(in_array('topicslist_startdata', $this->viewtplfiles))
                $this->savetplfiles('view/tpls/topicslist_startdata.tpl', '###LINK_TO_FULL###', $link_listdata);
        }
        
        if(in_array('datafull', $this->viewtplfiles)) {
            $loadsubcats_backtocat = '';
            if(in_array('subcatslist', $this->viewtplfiles))
                $loadsubcats_backtocat = '###BREADCRUMB###<br /><br />'."\n\n".'###LOAD_SUB_CATEGORIES###';
            elseif(in_array('cat', $this->viewtplfiles))
                $loadsubcats_backtocat = '<a href="###BACK_CATEGORY_LINK###"><?PHP echo _MODMAKERLANG_BACKTOCATEGORY_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/datafull.tpl', '###LOADSUBCATS_OR_BACKTOCAT###', $loadsubcats_backtocat);
            
            $link_listback = '';
            if(in_array('cat', $this->viewtplfiles))
                $link_listback = '<a href="###LINK_DATA_BACK###"><?PHP echo _MODMAKERLANG_BACKTODATASLIST_; ?'.'></a><br />';
            if(in_array('topic', $this->viewtplfiles))
                $link_listback = '<a href="###LINK_DATA_BACK###"><?PHP echo _MODMAKERLANG_BACKTOTOPICSLIST_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/datafull.tpl', '###LINK_BACK_TO_LIST###', $link_listback);
			
            $link_topicback = '';
            if(in_array('topic', $this->viewtplfiles))
                $link_topicback = '<a href="###LINK_TOPIC_BACK###"><?PHP echo _MODMAKERLANG_BACKTOTOPIC_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/datafull.tpl', '###LINK_BACK_TO_TOPIC###', $link_topicback);
            
            $link_dataprev = '<a href="###LINK_PREV_DATA###"><?PHP echo _MODMAKERLANG_PREV_DATA_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/datafull.tpl', '###LINK_DATA_PREV###', $link_dataprev);
            
            $link_datanext = '<a href="###LINK_NEXT_DATA###"><?PHP echo _MODMAKERLANG_NEXT_DATA_; ?'.'></a><br />';
            $this->savetplfiles('view/tpls/datafull.tpl', '###LINK_DATA_NEXT###', $link_datanext);
        }
		
        if(in_array('newest', $this->viewtplfiles)) {
            $link_newest = '<a href="###LINK_NEWEST###"><?PHP echo _MODMAKERLANG_NEWEST_; ?'.'></a><br />';
            
            $this->savetplfiles('view/tpls/newest.tpl', '###LINK_TO_NEWEST###', $link_newest);
        }
    }
    
    function gettpllinks()
    {
        $viewdatas = $this->mmdb->query("SELECT id, projectid, newtopics, newdata, full, newlink FROM view WHERE view.projectid = '".$this->mmdb->escapeString($this->modid)."'");
        $viewdata = $viewdatas->fetchArray();
        
        if(isset($this->sublinks['topic_list']) && $this->sublinks['topic_list'] == '1' && isset($viewdata['newtopics']) && $viewdata['newtopics'] == '1') {
            $this->sublinks['newtopic'] = '1';
        }
        if(isset($viewdata['newdata']) && $viewdata['newdata'] == '1') {
            $this->sublinks['newdata'] = '1';
        }
        if(isset($viewdata['full']) && $viewdata['full'] == '1') {
            $this->sublinks['list_full'] = '1';
        }
        if(isset($viewdata['newlink']) && $viewdata['newlink'] == '1') {
            $this->sublinks['newlink'] = '1';
        }
    }
    
    function setlangtotpl()
    {
        $dir = $this->dbpath.'/'.$this->modname.'/view/tpls';
        $handle = opendir($dir);
    	while(false !== ($file = readdir($handle))) {
            if(!is_dir($dir.'/'.$file) && $file != '.' && $file != '..') {
                $contentfile = $dir.'/'.$file;
                $content = file_get_contents($contentfile);
                $content = str_replace('MODMAKERLANG', strtoupper($this->modname.'lang'), $content);
                
				file_put_contents($contentfile, $content);
            }
        }
        closedir($handle);
        
        $adminlangdir = $this->dbpath.'/'.$this->modname.'/admin/lang';
        $handle = opendir($adminlangdir);
    	while(false !== ($file = readdir($handle))) {
            if(!is_dir($adminlangdir.'/'.$file) && $file != '.' && $file != '..') {
                $adminlang = $adminlangdir.'/'.$file;
				$content = file_get_contents($adminlang);
				$content = str_replace('###MODULENAME###', strtoupper($this->modname), $content);
				$content = str_replace('MODMAKERLANG', strtoupper($this->modname.'lang'), $content);
				$content = str_replace('###ADMIN_LANGS###', implode("\n", $this->adminlangs), $content);
				
				file_put_contents($adminlang, $content);
            }
        }
        closedir($handle);
		/*
        $adminlang = $this->dbpath.'/'.$this->modname.'/admin/lang/'.$_SESSION['loggedin']['userlang'].'.php';
        $content = file_get_contents($adminlang);
        $content = str_replace('###MODULENAME###', strtoupper($this->modname), $content);
        $content = str_replace('MODMAKERLANG', strtoupper($this->modname.'lang'), $content);
        $content = str_replace('###ADMIN_LANGS###', implode("\n", $this->adminlangs), $content);
        
		file_put_contents($adminlang, $content);
		*/
        
        $adminfile = $this->dbpath.'/'.$this->modname.'/admin.php';
        $content = file_get_contents($adminfile);
        $content = str_replace('MODMAKERLANG', strtoupper($this->modname.'lang'), $content);
        
		file_put_contents($adminfile, $content);
        
        if($this->modconf['subeditor'] == '1') {
	        $loginlang = $this->dbpath.'/'.$this->modname.'/admin/login.php';
	        $content = file_get_contents($loginlang);
	        $content = str_replace('###MODULENAME###', $this->modname, $content);
	        $content = str_replace('MODMAKERLANG', strtoupper($this->modname.'lang'), $content);
	        
			file_put_contents($loginlang, $content);
		}
    }
    
    function deletebasefiles($folder)
    {
        $dir = $this->dbpath;
        $handle = opendir($dir.'/'.$folder);
    	while(false !== ($file = readdir($handle))) { 
    		if($file != '.' && $file != '..') {
                $filepath = $dir.'/'.$folder.'/'.$file;
    		    if(is_dir($filepath.'/')) {
                    $this->deletebasefiles($folder.'/'.$file);
                    @rmdir($filepath);
          	    }
          	    else {
                    unlink($filepath);
          	    }
    		}
    	}
        closedir($handle);
        @rmdir($dir.'/'.$folder);
    }
}


