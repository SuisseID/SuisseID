<?php 

include '../conf/Config.php';
$suisseID->checkRedirection();

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<title>SuisseID Redirector</title>
<style type="text/css">
body {
	font-size: 12px;
	font-family: arial, helvetica, sans-serif;
	color: #333;
}
p {
	margin: 1em;
}
.comments {
	background-color: #e3e3e3;
	border-top: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	padding: 5px;
}
#mydiv {
	position: absolute;
	top: 50%;
	left: 50%;
	width: 300px;
	height: 75px;
	margin-top: -9em; /*set to a negative number 1/2 of your height*/;
	margin-left: -15em; /*set to a negative number 1/2 of your width*/;
	border: 1px solid #ccc;
	background-color: #f3f3f3;
}
</style>
</head>

<body onload="document.forms[0].submit()">

<?php echo $suisseID->getSamlRedirectorForms(false, $_GET['selectedidp'], false, true); ?>

</body>

</html>
