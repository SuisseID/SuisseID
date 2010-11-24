<?php
include '../conf/Config.php';

if($suisseID->processSamlResponse())
{
	$sIdNo = '<span style="color: #800080;">'.$suisseID->personSuisseIDNo.'</span>';
	
	if(count($suisseID->personAttributes) > 0)
	{
		$headerText = htmlentities('Für SuisseID ').$sIdNo.htmlentities(' wurden folgende Attribute zurückgegeben:');	
		$bodyValue 	= '';
	
		foreach($suisseID->personAttributes as $key => $record)
		{
			$bodyValue .=  '<tr>
					 			<td style="border: 1px solid #C0C0C0; padding: 6px 10px 5px 10px; width: 140px;">'.$record['FriendlyName'].'</td>
					 			<td style="border: 1px solid #C0C0C0; padding: 6px 10px 5px 10px; width: 240px;">'.$record['cdata'].'</td>
				 	 		</tr>';
		}
	}
	else
	{
		$headerText = htmlentities('User mit der SuisseID ').$sIdNo.htmlentities(' wurde erfolgreich authentisiert');
		$bodyValue	= '';
	}
}
else 
{
	$headerText = htmlentities("Abfrage konnte nicht durchgeführt werden. Fehlercode:");
	$bodyValue	= '<tr><td colspan="2" style="border: 1px solid #C0C0C0; padding: 6px 10px 5px 10px; width: 140px;">'.htmlentities($suisseID->statusText).'</td></tr>';	
}





?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>SuisseID Response Page</title>
<style type="text/css">
.auto-style1 {
	border: 1px solid #C0C0C0;
	background-color: #E3E3E3;
	font-size: 12px;
	font-family: arial, helvetica, sans-serif;
	color: #333;
	border-collapse: collapse;
}
.auto-style2 {
	background-color: #F3F3F3;
}


</style>
</head>

<body>

<p><br />
</p>
<table align="center" class="auto-style1" style="width: 380px;" cellpadding="0" cellspacing="1">
	<tr>
		<td colspan="2" style="border: 1px solid #C0C0C0; padding: 10px; font-weight: bold;" class="auto-style2"><?php echo $headerText; ?></td>
	</tr>
	
	<?php echo $bodyValue; ?>
		
</table>

</body>

</html>
