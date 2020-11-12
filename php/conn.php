<?php

	$server="localhost";
	$username="root";
	$password="";
	$dbname="control_budget";

	$conn = new mysqli($server, $username, $password, $dbname);
	if ($conn->connect_error) {
	    die();
	}

?>