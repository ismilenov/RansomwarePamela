<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<style type="text/css">
<!--
    body {
        background-color: #ffffff;
        padding: 0px;
        margin: 0px;
        line-height: 15px;
    }
    td {
        font-family: Verdana, Arial, Helvetica, sans-serif; 
        font-size: 10px !important; 
        font-weight: normal; 
        color: #000000;
    }
//-->
</style>
</head>
<body>
<table style="margin: 30px" width="600" cellspacing="10" cellpadding="0">
  <tr>
	<td style="text-align: baseline" valign="bottom">
	  <b><?PHP echo $mailcontent['headline']; ?></b>
	  <img align="right" src="<?PHP echo $mailcontent['logo']; ?>">
	</td>
  </tr>
<?PHP foreach($mailcontent['text'] as $mailtext) { ?>
  <tr>
	<td>
	  <?PHP if(is_array($mailtext)) { ?>
		<div style="text-align: center">
			<?PHP echo $mailtext['highlight']; ?>
		</div>
	  <?PHP } else { ?>
		<?PHP echo $mailtext; ?>
	  <?PHP } ?>
	</td>
  </tr>
<?PHP } ?>
  <tr>
	<td style="text-align: center"><?PHP echo $mailcontent['footer']; ?></td>
  </tr>
</table>
</body>
</html>