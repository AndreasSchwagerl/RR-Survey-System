<!DOCTYPE html>
<html>
	<body>
		<?php
			function GenerateRandomID($len) {
				$ID = "";
				$chars = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));
				
				for($i = 0; $i < $len; $i++) {
					$ID .= $chars[mt_rand(0, count($chars) - 1)];
				}
				return $ID;
			}
			
			function GenerateEmailString($PID) {
				$str = "<a href='http://www.rrsurvey.net/survey.php?ID=$PID'>Click Here</a>";
				
				return $str;
			}
		?>
		<?php
			// Get data from POST
			//		CID: Company ID
			//		start: Start Date
			//		end: End Date
			//		dArr: Department Array
			//		eArr: Email Array
			//		qArr: Question Array
			$CID = $_POST["CID"];
			$start = $_POST["start"];
			$end = $_POST["end"];
			$dArr = $_POST["dArr"];
			$eArr = $_POST["eArr"];
			$qArr = $_POST["qArr"];
			
			// Set the mail headers
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: R&R Survey <do-not-reply@rrsurvey.net>' . "\r\n";
			$headers .= 'Reply-To: <do-not-reply@rrsurvey.net>' . "\r\n";
			$headers .= 'Return-Path: <do-not-reply@rrsurvey.net>' . "\r\n";
			
			// Get database connection information
			$dbhost = 'localhost';
			$dbconnect = parse_ini_file('connect.ini');
			
			// Initiate mysqli connection
			$mysqli = new mysqli($dbhost, $dbconnect['username'], $dbconnect['password'], $dbconnect['dbname']);
			
			// Check for a connection error and handle if necessary
			if ($mysqli->connect_error) {
				return;
			}
			
			// Insert new Survey entry and get Survey ID
			$insert = mysqli_query($mysqli, "INSERT INTO Survey (CID, StartDate, EndDate) VALUES ('$CID', '$start', '$end');");
			$SID = mysqli_insert_id($mysqli);
			
			// Insert new QuestionSurvey Entries
			$count = 1;
			foreach($qArr as $QID) {
				$insert = mysqli_query($mysqli, "INSERT INTO SurveyQuestion (`SID`, `QID`, `Order`) VALUES ('$SID', '$QID', '$count');");
				$count++;
			}
			
			// Insert new Department entries and get Department ID
			foreach($dArr as $dept) {
				$insert = mysqli_query($mysqli, "INSERT INTO Department (SID, Name) VALUES ('$SID', '$dept');");
				$DID = mysqli_insert_id($mysqli);
				
				// Remove first element from eArr, then create an array from the comma delimited list
				$emails = explode(', ', array_shift($eArr));
				
				// Insert new Participant Entries
				foreach($emails as $email) {
					$PID = GenerateRandomID(100); // Generate unique participant ID; 62^100, 1.73e^179 possibilities.
					$insert = mysqli_query($mysqli, "INSERT INTO Participant (ID, DID, Email, Submitted) VALUES ('$PID', '$DID', '$email', 0);");
					
					mail($email, "Please Complete Survey By " .$end, GenerateEmailString($PID), $headers, "-fdo-not-reply@rrsurvey.net");
				}
			}
		?>
	</body>
</html>