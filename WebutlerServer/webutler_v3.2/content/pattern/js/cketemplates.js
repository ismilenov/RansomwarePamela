<?PHP

header('Content-Type: application/javascript; charset=utf-8');

$root = dirname(dirname(dirname(dirname(__FILE__))));
$path = $root.'/content/pattern';
require_once $path.'/patterninfos.php';

$outs = array();
$notplprev = 'no_tpl_prev.gif';

foreach($infos as $info)
{
	if(file_exists($path.'/files/'.$info['file']))
	{
		$lines = file($path.'/files/'.$info['file']);
		$html = array();
		foreach($lines as $line)
		{
			$check = trim($line);
			if(!empty($check))
				$html[] = rtrim($line);
		}
		$out = "\n\t\ttitle: '".$info['title']."',\n";
		$out.= "\t\timage: '".($info['image'] != '' && file_exists($root.'/content/media/image/tpl_icons/'.$info['image']) ? $info['image'] : $notplprev)."',\n";
		$out.= "\t\tdescription: '".($info['description'] != '' ? $info['description'] : '&nbsp;')."',\n";
		$out.= "\t\thtml:\t'".implode("' + \n\t\t\t'", $html)."'\n";
		$outs[] = $out;
	}
}

echo "CKEDITOR.addTemplates( 'usertemplates',
{
	imagesPath : '../../content/media/image/tpl_icons/',
	templates : [{".implode("\t},\n\t{", $outs)."\t}]
});";

