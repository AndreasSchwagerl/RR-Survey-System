<?php
	// Get SQL query from POST
	$sql = $_POST["sql"];
	$type = $_POST["type"];
	
	// Get database connection information
	$dbhost = 'localhost';
	$dbconnect = parse_ini_file('connect.ini');
	
	// Initiate mysqli connection
	$mysqli = new mysqli($dbhost, $dbconnect['username'], $dbconnect['password'], $dbconnect['dbname']);
	
	// Check for a connection error and handle if necessary
	if ($mysqli->connect_error) {
		echo "false";
		return;
	}
	
	// Run SQL query
	mysqli_query($mysqli, $sql);
	
	if ($type == 'i') {
		$ID = mysqli_insert_id($mysqli);
		echo $ID;
	}
?>