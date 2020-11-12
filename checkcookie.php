<?php

	session_start();

	if(count($_COOKIE) > 0){
		$_SESSION["cookie_status"]=true;
		header("Location: login.php");
	   	die();
	} 
	else{ 
		header("Location: nocookiebrowser.php");
		die();
	}

?>