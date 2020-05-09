<?php $ver="09-05-2020v1"; ?>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo $ver; ?>">
	<link rel="shortcut icon" type="image/jpg" href="icon/icon.png"/>
	<link rel="stylesheet" href="fontawesome/css/all.min.css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript">
		$(function () {
 		 	$('[data-toggle="tooltip"]').tooltip()
		})
		$(function () {
 		 	$('[data-toggle="popover"]').popover()
		})
	</script>
	<noscript><meta http-equiv="refresh" content="0; URL=./nojavascriptbrowser.php"></noscript>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8">
<?php
	setcookie("test","test",time()+3600,"/");
	if(count($_COOKIE) > 0) {
	   
	} else { ?>
	    <meta http-equiv="refresh" content="0; URL=./nocookiebrowser.php">
	<?php }
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

?>