
<?php
	session_start();
	
	if(isset($_SESSION["email"])){
		//User already logged in redirects to home page
		header("Location: home.php");
		die();
	}
?>


<html>
<head>
	<title>Control Budget</title>
	<?php require "php/head.php"; ?>
</head>
<style>
	body{
		background: url(background/imghome.jpg) no-repeat center center fixed; 
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;
		background-color: white;	
	}
</style>
<body>

	<?php  require "php/nav.php"; ?>


	<div class="container">
		<div class="index">
			<div class="indextext">
				We help you plan your budget
			</div><br>
			<div>
				<a href="login.php" class="btn btn-info">Start Today <i class="fas fa-arrow-circle-right"></i></a>
			</div>
		</div>
	</div>

	<?php  require "php/footer.php"; ?>

</body>
</html>