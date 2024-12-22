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
<table style="margin: 30px" width="500" cellspacing="10" cellpadding="0">
  <tr>
	<td colspan="2" valign="bottom" style="text-align: baseline">
	  <b><?PHP echo $mailcontent['usersubject']; ?></b>
	  <img align="right" src="<?PHP echo $mailcontent['logo']; ?>">
	</td>
  </tr>
<?PHP foreach($mailcontent['text'] as $mailtext) { ?>
  <tr>
	<td valign="top" width="100"><?PHP echo $mailtext['name']; ?>:</td>
	<td valign="top"><?PHP echo $mailtext['value']; ?></td>
  </tr>
<?PHP } ?>
<?PHP if($mailcontent['footer'] != '') { ?>
  <tr>
	<td colspan="2" style="padding-top: 20px; text-align: center"><?PHP echo $mailcontent['footer']; ?></td>
  </tr>
<?PHP } ?>
</table>
</body>
</html>














