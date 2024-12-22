<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

if(!class_exists('SQLite3')) {
    echo 'no SQLite3';
	return false;
}

require_once dirname(dirname(dirname(__FILE__))).'/includes/loader.php';
require_once $webutler_config['server_path'].'/includes/mmclass.php';

$webutlermodadmin = new WebutlerAdminClass;
$webutlermodadmin->config = $webutler_config;
$webutlermodadmin->langconf = $webutler_langconf;
$webutlermodadmin->moduleslist = $webutler_moduleslist;

if(isset($_SESSION['newslang']))
	$news_adminlang = $_SESSION['newslang'];
elseif(isset($_SESSION['loggedin']['userlang']))
	$news_adminlang = $_SESSION['loggedin']['userlang'];
else
	$news_adminlang = $webutlermodadmin->config['defaultlang'];

$news_adminlangfile = $webutlermodadmin->config['server_path'].'/modules/news/admin/lang/'.$news_adminlang.'.php';
if(file_exists($news_adminlangfile))
	require_once $news_adminlangfile;

$news_viewlangfile = $webutlermodadmin->config['server_path'].'/modules/news/view/lang/'.$news_adminlang.'.php';
if(file_exists($news_viewlangfile))
	require_once $news_viewlangfile;

if(!isset($news_conf))
	require_once $webutlermodadmin->config['server_path'].'/modules/news/data/config.php';

$news_class = new MMAdminClass;
$news_class->modname = 'news';
$news_class->serverpath = $webutlermodadmin->config['server_path'];
$news_class->homepage = $webutlermodadmin->config['homepage'];
$news_class->chmod = $webutlermodadmin->config['chmod'];
$news_class->pngcomp = $webutlermodadmin->config['png_compress'];
$news_class->jpgqual = $webutlermodadmin->config['jpg_quality'];
$news_class->langconf = $webutlermodadmin->langconf;
$news_class->fileconfig = $news_conf;

if(!$webutlermodadmin->checkadmin()) {
	exit('no access');
	exit;
}

if(!isset($_GET['page'])) $_GET['page'] = '';

$news_viewlangfile = $webutlermodadmin->config['server_path'].'/modules/news/view/lang/'.$news_adminlang.'.php';
if(file_exists($news_viewlangfile))
	require_once $news_viewlangfile;

$news_class->pagelang = $news_adminlang;
$news_class->post = $_POST;
$news_class->files = $_FILES;
$news_class->get = $_GET;
$news_class->connectdb();


    if(isset($_POST['saveconf'])) {
        $news_class->saveconfig();
        header("Location: admin.php?page=conf");
    }
    else {
        if($news_class->dbconfig == '' && $_GET['page'] != 'install')
    		header("Location: admin.php?page=install");
        elseif($news_class->dbconfig != '' && $_GET['page'] == 'install')
    		header("Location: admin.php");
    }


if(isset($_POST['newslang'])) {
	$_SESSION['newslang'] = preg_replace("/[^a-z]/", "", $_POST['newslang']);
    header("Location: admin.php".(($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : ''));
	exit;
}

if(isset($_POST['on'])) $news_class->online();
if(isset($_POST['off'])) $news_class->offline();
if(isset($_POST['delete'])) $news_class->delete();
if(isset($_POST['change'])) $news_class->changeto();
if(isset($_POST['savedata'])) $news_class->savedata();
if(isset($_POST['editdata'])) $news_class->updatedata();
if($_GET['page'] == 'datas' && isset($_GET['data']) && $_GET['data'] != 'new') $db_data = $news_class->getdata();


if($_GET['page'] == '') $_GET['page'] = 'datas';


?>
<!DOCTYPE html>
<html lang="<?PHP echo $news_adminlang; ?>">
<head>
<title><?PHP echo _NEWSLANGADMIN_TITLE_.' '.strtoupper(_NEWSLANGADMIN_MODNAME_); ?></title>
	<meta charset="UTF-8" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="robots" content="noindex,nofollow" />
	<link href="<?PHP echo $webutlermodadmin->config['homepage']; ?>/modules/news/admin/admin.css" rel="stylesheet" type="text/css" />
	<?PHP if(isset($_GET['data'])) { ?>
<script>
/* <![CDATA[ */
    var homepagepath = '<?PHP echo $webutlermodadmin->config['homepage']; ?>';
/* ]]> */
</script>
<script src="<?PHP echo $webutlermodadmin->config['homepage']; ?>/includes/modexts/calendar/datepickercontrol.js"></script>
<script src="<?PHP echo $webutlermodadmin->config['homepage']; ?>/includes/modexts/ckeditor/ckeditor.js"></script>
<?PHP } ?>

	<script src="<?PHP echo $webutlermodadmin->config['homepage']; ?>/modules/news/admin/admin.js"></script>
</head>
<body>
<div id="adminpage">
  <?PHP echo $webutlermodadmin->getmodulesheadermenu('news', $news_adminlang); ?>
  <h1 id="adminheadline"><img src="<?PHP echo $webutlermodadmin->config['homepage']; ?>/admin/system/images/webutler_s.gif" align="right" /><?PHP echo _NEWSLANGADMIN_TITLE_.' '.strtoupper(_NEWSLANGADMIN_MODNAME_); ?></h1>
  <?PHP if($_GET['page'] != 'install') { ?>
  <table id="adminmenu" border="0" cellspacing="0" cellpadding="5">
	<tr>
      <td><a href="admin.php?page=conf"><?PHP echo _NEWSLANGADMIN_SETTINGS_; ?></a></td>
<td><a href="admin.php?page=datas"><?PHP echo _NEWSLANGADMIN_DATAS_; ?></a></td>

	</tr>
  </table>
  <?PHP } ?>
  <div id="admincontent">
      
      
      
    <form method="post" name="baseform" action="admin.php<?PHP echo ($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : ''; ?>" enctype="multipart/form-data">
      <?PHP if($_GET['page'] == 'conf' || $_GET['page'] == 'install') { ?>
<?PHP $checkchmods = '';
				if($_GET['page'] == 'install') {
					$checkchmoddb = $news_class->checkchmoddb();
					clearstatcache();
					$checkchmodsmedia = $news_class->checkchmods('media');
					clearstatcache();
					$checkchmods = $checkchmoddb.$checkchmodsmedia;
				}
				if($checkchmods != '') { ?>
					<table border="0" cellspacing="0" cellpadding="5">
						<tr class="top"><td><h3><?PHP echo str_replace('###CHMODMODNAME###', $news_class->modname, _NEWSLANGADMIN_SETCHMODFOR_); ?></h3></td></tr>
						<tr><td style="padding: 10px 25px 5px 25px"><strong><?PHP echo _NEWSLANGADMIN_WRONGCHMOD_; ?></strong></td></tr>
						<?PHP echo $checkchmods; ?>
						<tr><td style="padding: 15px 0px 0px 25px"><?PHP echo str_replace(array('###CHMODFOLDER###', '###CHMODFILES###'), array('<strong>'.decoct($news_class->chmod[0]).'</strong>', '<strong>'.decoct($news_class->chmod[1]).'</strong>'), _NEWSLANGADMIN_SETRIGHTCHMODS_); ?></td></tr>
					</table>
				<?PHP } else { ?>
<table border="0" cellspacing="0" cellpadding="5">
                <tr class="top">
                  <td colspan="2"><h2><?PHP echo $_GET['page'] == 'install' ? _NEWSLANGADMIN_INSTALL_ : _NEWSLANGADMIN_SETTINGS_; ?></h2></td>
                </tr>
            	<tr class="odd"><td class="start"><?PHP echo _NEWSLANGADMIN_FIELD_SETPERPAGE_; ?>:</td>
<td class="end"><table class="setperpage" border="0" cellspacing="0" cellpadding="0"><tr><td style="border-top: 0px"><strong><?PHP echo _NEWSLANGADMIN_DATAS_; ?>:</strong></td><td style="border-top: 0px" width="100%"><input type="text" name="config[datasperpage]" style="width: 40px" value="<?PHP if(isset($news_class->dbconfig['datasperpage'])) echo $news_class->dbconfig['datasperpage']; ?>" /> <?PHP echo _NEWSLANGADMIN_FIELD_DATAPERPAGE_; ?></td></tr></table></td>
                        </tr>
<tr class="even"><td class="start"><?PHP echo _NEWSLANGADMIN_FIELD_NEWCREATED_; ?>:</td>
<td class="end"><table class="createdconf" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td><strong><?PHP echo _NEWSLANGADMIN_DATAS_; ?>:</strong></td><td>
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td><input type="radio" name="config[newcreated_data]" value="online" style="width: 12px" id="newcreated_data_online"<?PHP if(isset($news_class->dbconfig['newcreated_data']) && $news_class->dbconfig['newcreated_data'] == 'online') echo ' checked="checked"'; ?> /><label for="newcreated_data_online"><?PHP echo _NEWSLANGADMIN_BUTTON_ONLINE_; ?></label></td>
			<td><input type="radio" name="config[newcreated_data]" value="offline" style="width: 12px" id="newcreated_data_offline"<?PHP if(!isset($news_class->dbconfig['newcreated_data']) || (isset($news_class->dbconfig['newcreated_data']) && $news_class->dbconfig['newcreated_data'] == 'offline')) echo ' checked="checked"'; ?> /><label for="newcreated_data_offline"><?PHP echo _NEWSLANGADMIN_BUTTON_OFFLINE_; ?></label></td>
			</tr>
			</table>
			</td>
			</tr>
			</tbody>
			</table></td>
                        </tr>
<tr class="odd"><td class="start"><?PHP echo _NEWSLANGADMIN_FIELD_SORTING_; ?>:</td>
<td class="end"><table class="sortconf" border="0" cellspacing="0" cellpadding="0"><tr><td rowspan="2" style="border-top: 0px; padding-bottom: 0px"><strong><?PHP echo _NEWSLANGADMIN_DATAS_; ?>:</strong></td>
                    <td style="border-top: 0px; padding-bottom: 0px"><input type="radio" name="config[datasort]" value="DESC" style="width: 12px" id="sorting_datadesc"<?PHP if(!isset($news_class->dbconfig['datasort']) || $news_class->dbconfig['datasort'] == 'DESC') echo ' checked="checked"'; ?> /><label for="sorting_datadesc"><?PHP echo _NEWSLANGADMIN_FIELD_SORTNEWFIRST_; ?></label></td>
                    <td width="100%" style="border-top: 0px; padding-bottom: 0px"><input type="radio" name="config[datasort]" value="ASC" style="width: 12px" id="sorting_dataasc"<?PHP if(isset($news_class->dbconfig['datasort']) && $news_class->dbconfig['datasort'] == 'ASC') echo ' checked="checked"'; ?> /><label for="sorting_dataasc"><?PHP echo _NEWSLANGADMIN_FIELD_SORTOLDFIRST_; ?></label></td>
                    </tr><tr><td colspan="2" class="sortbyfield"><?PHP echo _NEWSLANGADMIN_FIELD_SORTBYFIELD_; ?>:<select name="config[sortdatafield]" size="1">
                            <?PHP echo $news_class->datafieldsforsort('data'); ?>
                        </select></td></tr></table></td>
                        </tr>
<tr class="even"><td class="start"><?PHP echo _NEWSLANGADMIN_FIELD_NUMBNEWEST_; ?>:</td>
<td class="end"><input type="text" value="<?PHP echo $news_class->dbconfig['numbnewest']; ?>" style="width: 40px" name="config[numbnewest]" /> <?PHP echo _NEWSLANGADMIN_FIELD_NEWESTINBLOCK_; ?></td>
                        </tr>
<tr class="odd"><td class="start"><?PHP echo _NEWSLANGADMIN_HEADLINETITLE_SEO_; ?>:</td>
<td class="end"><input type="checkbox" name="config[headlinetitle]" value="show" style="width: 12px" id="showheadlinetitle"<?PHP if(isset($news_class->dbconfig['headlinetitle']) && $news_class->dbconfig['headlinetitle'] == 'show') echo ' checked="checked"'; ?> /><label for="showheadlinetitle"><?PHP echo _NEWSLANGADMIN_HEADLINETITLE_SHOW_.' ('._NEWSLANGADMIN_HEADLINETITLE_DATA_.')'; ?></label></td>
                        </tr>
<tr class="even"><td class="start"><?PHP echo _NEWSLANGADMIN_INPUT_FIELD_HEADLINE_; ?>:</td>
<td class="end"><input type="radio" name="config[title]"<?PHP if((!isset($news_class->dbconfig['title']) || $news_class->dbconfig['title'] == '') || (isset($news_class->dbconfig['title']) && $news_class->dbconfig['title'] == 'field_headline')) echo ' checked="checked"'; ?> value="field_headline" style="width: 12px" id="title_field_headline" /><label for="title_field_headline"><?PHP echo _NEWSLANGADMIN_FIELD_USEASTITLE_; ?></label></td>
                        </tr>
<tr class="odd"><td class="start"><?PHP echo _NEWSLANGADMIN_INPUT_FIELD_IMGALT_; ?>:</td>
<td class="end"><input type="radio" name="config[title]"<?PHP if((isset($news_class->dbconfig['title']) && $news_class->dbconfig['title'] == 'field_imgalt')) echo ' checked="checked"'; ?> value="field_imgalt" style="width: 12px" id="title_field_imgalt" /><label for="title_field_imgalt"><?PHP echo _NEWSLANGADMIN_FIELD_USEASTITLE_; ?></label></td>
                        </tr>

                <tr class="bottom">
                  <td>&nbsp;</td><td><input type="submit" class="button" name="saveconf" value="<?PHP echo _NEWSLANGADMIN_BUTTON_SAVE_; ?>" /></td>
                </tr>
              </table>
<?PHP } } ?>

      
      
      
      
      
      
      <?PHP if($_GET['page'] == 'datas' && !isset($_GET['data'])) {
				echo '<div id="submenu">
				<a href="'.$news_class->getdatalink('new').'">'._NEWSLANGADMIN_NEWDATA_.'</a></div>';
 ?>
<?PHP $getdatalist = $news_class->getdatalist();
            if($getdatalist == '') {
                echo _NEWSLANGADMIN_NOAVAILDATA_;
            } else { ?>
          <table border="0" cellspacing="0" cellpadding="5">
            <tr class="top">
              <td colspan="3"><h2><?PHP echo _NEWSLANGADMIN_DATAS_.$news_class->getdatasheadline(); ?></h2></td>
            </tr>
            <?PHP echo $getdatalist; ?>
          </table>
<?PHP } } ?>

      <?PHP if($_GET['page'] == 'datas' && isset($_GET['data'])) {
 ?>
<?PHP if($_GET['data'] != 'new' && (!isset($db_data) || $db_data == '')) {
                echo _NEWSLANGADMIN_NOEXISTDAT_;
            } else { ?>
<table border="0" cellspacing="0" cellpadding="5">
            <tr class="top">
              <td colspan="2"><h2><?PHP 
			    if($_GET['data'] == 'new') echo _NEWSLANGADMIN_NEWDATA_;
else echo _NEWSLANGADMIN_EDITDATA_;
			  ?></h2></td>
            </tr>
<tr class="odd"><td class="start"><?PHP echo _NEWSLANGADMIN_INPUT_FIELD_HEADLINE_; ?>:</td>
<td class="end"><input type="text" name="field_headline" value="<?PHP if(isset($db_data['field_headline'])) echo $db_data['field_headline']; ?>" /></td>
                        </tr>
<tr class="even"><td class="start"><?PHP echo _NEWSLANGADMIN_INPUT_FIELD_DATE_; ?>:</td>
<td class="end"><input type="text" name="field_date" id="DPC_field_date" value="<?PHP echo (isset($db_data['field_date']) && $db_data['field_date'] != '') ? strftime('%Y-%m-%d', $db_data['field_date']) : date('Y-m-d'); ?>" size="10" maxlength="10" style="width: 80px" readonly="readonly" /><img src="admin/icons/delete.png" title="<?PHP echo _NEWSLANGADMIN_FIELD_CLEARDATE_; ?>" class="cleardate" onclick="cleardatefield('field_date')" /></td>
                        </tr>
<tr class="odd"><td class="start"><?PHP echo _NEWSLANGADMIN_INPUT_FIELD_IMAGE_; ?>:</td>
<td class="end"><div class="fakeupload"><input type="file" class="fileupload" size="53" name="field_image" onchange="setuploadpath(this)" /><input type="text" class="fakefield" /><input type="text" class="fakebutton" value="<?PHP echo _NEWSLANGADMIN_CHOOSE_UPLOAD_; ?>" /></div><div class="filepreview">
                        <?PHP echo $news_class->getimageof('datas', (isset($db_data['id']) ? $db_data['id'] : ''), 'field_image'); ?>
                        </div></td>
                        </tr>
<tr class="even"><td class="start"><?PHP echo _NEWSLANGADMIN_INPUT_FIELD_IMGALT_; ?>:</td>
<td class="end"><input type="text" name="field_imgalt" value="<?PHP if(isset($db_data['field_imgalt'])) echo $db_data['field_imgalt']; ?>" /></td>
                        </tr>
<tr class="odd"><td class="start"><?PHP echo _NEWSLANGADMIN_INPUT_FIELD_TEASER_; ?>:</td>
<td class="end"><textarea name="field_teaser"><?PHP if(isset($db_data['field_teaser'])) echo $db_data['field_teaser']; ?></textarea></td>
                        </tr>
<tr class="even"><td class="start"><?PHP echo _NEWSLANGADMIN_INPUT_FIELD_TEXT_; ?>:</td>
<td class="end"><div class="editorarea"><textarea name="field_text"><?PHP if(isset($db_data['field_text'])) echo $db_data['field_text']; ?></textarea>
                        <script>
                        /* <![CDATA[ */
                            CKEDITOR.replace( 'field_text', {
                                customConfig : 'html_config.js',
                                language : '<?PHP echo $news_adminlang; ?>'
                            });
                        /* ]]> */
                        </script>
                        </div></td>
                        </tr>

<tr class="bottom">
              <td>&nbsp;</td><td><input type="submit" class="button" name="<?PHP
			    if($_GET['data'] == 'new') echo 'savedata';
else echo 'editdata';
			  ?>" value="<?PHP echo _NEWSLANGADMIN_BUTTON_SAVE_; ?>" /></td>
            </tr>
          </table>
<?PHP } } ?>

    </form>
  </div>
</div>
</body>
</html>
