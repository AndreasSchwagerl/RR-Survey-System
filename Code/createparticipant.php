<?php
	function GenerateRandomID($len) {
		$ID = "";
		$chars = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));
		
		for($i = 0; $i < $len; $i++) {
			$ID .= $chars[mt_rand(0, count($chars) - 1)];
		}
		return $ID;
	}
	
	function GenerateEmailString($PID, $comp, $end) {
		$eml = parse_ini_file('../emailscript.ini');
		$str = $eml['email'];
		$str = str_replace('[Company]', "$comp", $str);
		$str = str_replace('[Date]', date("M jS", strtotime($end)), $str);
		$str = str_replace('[URL]', "http://www.rrsurvey.net/survey.php?ID=$PID", $str);
		
		return $str;
	}
	
	// Get data from POST
	$DID = $_POST["DID"];
	$email = $_POST["email"];
	
	// Set the mail headers
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: R&R Survey <do-not-reply@rrsurvey.net>' . "\r\n";
	$headers .= 'Reply-To: <do-not-reply@rrsurvey.net>' . "\r\n";
	$headers .= 'Return-Path: <do-not-reply@rrsurvey.net>' . "\r\n";
	
	// Get email subject
	$eml = parse_ini_file('../emailscript.ini');
	$subject = $eml['subject'];
	
	// Get database connection information
	$dbhost = 'localhost';
	$dbconnect = parse_ini_file('../connect.ini');
	
	// Initiate mysqli connection
	$mysqli = new mysqli($dbhost, $dbconnect['username'], $dbconnect['password'], $dbconnect['dbname']);
	
	// Check for a connection error and handle if necessary
	if ($mysqli->connect_error) {
		echo "false";
		return;
	}
	
	// Get company name and survey end date
	$results = mysqli_query($mysqli, "SELECT c.Name, s.EndDate FROM (Company c INNER JOIN Survey s ON c.ID = s.CID) INNER JOIN Department d ON s.ID = d.SID WHERE d.ID = $DID");
	while($row = mysqli_fetch_array($results)) {
		$comp = $row['Name'];
		$end = $row['EndDate'];
	}
	
	// Create a new participant entry
	$PID = GenerateRandomID(100); // Generate unique participant ID; 62^100, 1.73e^179 possibilities.
	$insert = mysqli_query($mysqli, "INSERT INTO Participant (ID, DID, Email, Submitted) VALUES ('$PID', '$DID', '$email', 0);");
	
	// Send out an email to the new participant
	mail($email, $subject, GenerateEmailString($PID, $comp, $end), $headers, "-fdo-not-reply@rrsurvey.net");
	
	// Return the generated Participant ID
	echo $PID;
?>