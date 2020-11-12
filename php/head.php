<?php $ver="10-05-2020v1"; ?>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css?<?php echo $ver; ?>">
	<link rel="shortcut icon" href="icon/icon.png"/>
	<link rel="stylesheet" href="fontawesome/css/all.min.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.bundle.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
	setcookie("test","test",time()+3600,"/");
	if(count($_COOKIE) > 0) {
	   
	} 
	else { 
		header("Location: ../nocookiebrowser.php");
		die();
	}

?>