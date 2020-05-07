
<?php
	session_start();
	$_SESSION["webpage"]=htmlspecialchars($_SERVER["PHP_SELF"]);
	//Checking if user is already logged in
	if(isset($_SESSION["email"])){
		//User already logged in redirects to home page
		header("Location: home.php");
		die();
	}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Control Budget</title>
	<?php require "php/head.php"; ?>
	<meta name="keywords" content="budget control,budget,how to control budget">
	<meta name="description" content="">
</head>
<style type="text/css">
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
				We help you to plan your budget
			</div><br>
			<div>
				<a href="login.php" class="btn btn-info">Start Today <i class="fas fa-arrow-circle-right"></i></a>
			</div>
		</div>
	</div>




	<?php  require "php/footer.php"; ?>




</body>
</html>