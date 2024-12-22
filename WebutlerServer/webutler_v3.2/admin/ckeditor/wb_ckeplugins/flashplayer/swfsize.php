<?PHP

/**************************************
    Webutler V3.2 - www.webutler.de
    Copyright (c) 2008 - 2016
    Autor: Sven Zinke
    Free for any use
    Lizenz: GPL
**************************************/

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/includes/loader.php';

$webutlerckplugin = new WebutlerAdminClass;
$webutlerckplugin->config = $webutler_config;

if(!$webutlerckplugin->checkadmin())
    exit('no access');


header("Pragma:no-cache");
header("Cache-Control:private,no-store,no-cache,must-revalidate");

if($_GET['movie'] != '')
{
	$dateiname = $_GET['movie'];
	$endung = substr( $dateiname, ( strrpos($dateiname, '.') + 1 ) ) ;
	if($endung == 'swf')
    {
		$file = $webutlerckplugin->config['server_path'].'/'.$dateiname;
		if(file_exists($file))
        {
			$format = GetImageSize($file);
			$wh = explode(' ', $format[3]);
			$swfwidth = preg_replace('/[^0-9]/', '', $wh[0]);
			$swfheight = preg_replace('/[^0-9]/', '', $wh[1]);
		}
	}
?>
<!DOCTYPE html>
<html lang="<?PHP echo $_SESSION['loggedin']['userlang']; ?>">
<head>
<title></title>
<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow">
<script>
/* <![CDATA[ */
	var win;
	if(document.all)
		win = parent ;
	else
		win = top ;
//opener
    var CKEopener = win.CKEDITOR;

	if (CKEopener)
	{
        CKEopener.tools.callFunction( <?php echo $_GET['CKEditorFuncNum']; ?>,
        {
            newWidth : <?php echo $swfwidth; ?>,
            newHeight : <?php echo $swfheight; ?>
        });
	}
/* ]]> */
</script>
</head>
<body></body>
</html>
<?PHP } ?>
