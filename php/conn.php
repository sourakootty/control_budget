<?php

	$server="localhost";
	$username="root";
	$password="";
	$dbname="control_budget";

	$conn = new mysqli($serve, $username, $password, $dbname);
	if ($conn->connect_error) {
	    die();
	}

?>