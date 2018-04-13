<html>
	<body>
		<?php
			function GenerateReminderEmailString($PID) {
				$str = "<a href='http://www.rrsurvey.net/survey.php?ID=$PID'>Click Here</a>";
				
				return $str;
			}
		?>
		
		<?php
			// Set the mail headers
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: R&R Survey <do-not-reply@rrsurvey.net>' . "\r\n";
			$headers .= 'Reply-To: <do-not-reply@rrsurvey.net>' . "\r\n";
			$headers .= 'Return-Path: <do-not-reply@rrsurvey.net>' . "\r\n";

			// Get level from POST
			//		level 0: Survey
			//		level 1: Department
			//		level 2: Participant
			$level = $_POST["level"];
			
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
			
			// If level is 0, send a reminder email to each participant in the survey who has not yet submitted
			// If level is 1, send a reminder email to each participant in the department who has not yet submitted
			// If level is 2, send a reminder email to the specified participant
			if ($level == 0) {
				// Get the Survey ID from POST
				$SID = $_POST["ID"];
				
				$result = mysqli_query($mysqli, "SELECT p.email, p.ID, s.EndDate FROM (Survey s INNER JOIN Department d ON s.ID = d.SID) INNER JOIN Participant p ON d.ID = p.DID WHERE s.ID = $SID AND p.Submitted = 0");
				
				// Loop through each participant, sending reminder emails
				while($row = mysqli_fetch_array($result)) {
					mail($row['email'], "REMINDER: Please Complete Survey By " .$row['EndDate'], GenerateReminderEmailString($row['ID']), $headers, "-fdo-not-reply@rrsurvey.net");
				}
			} else if ($level == 1) {
				// Get the Department ID from POST
				$DID = $_POST["ID"];
				
				$result = mysqli_query($mysqli, "SELECT p.email, p.ID, s.EndDate FROM (Survey s INNER JOIN Department d ON s.ID = d.SID) INNER JOIN Participant p ON d.ID = p.DID WHERE d.ID = $DID AND p.Submitted = 0");
				
				// Loop through each participant, sending reminder emails
				while($row = mysqli_fetch_array($result)) {
					mail($row['email'], "REMINDER: Please Complete Survey By " .$row['EndDate'], GenerateReminderEmailString($row['ID']), $headers, "-fdo-not-reply@rrsurvey.net");
				}
			} else if ($level == 2) {
				// Get the Participant ID and email from POST
				$PID = $_POST["ID"];
				
				$result = mysqli_query($mysqli, "SELECT p.email, s.EndDate FROM (Survey s INNER JOIN Department d ON s.ID = d.SID) INNER JOIN Participant p ON d.ID = p.DID WHERE p.ID = '$PID'");
				
				// Ensure that a participant exists, then send a reminder email
				while($row = mysqli_fetch_array($result)) {
					mail($row['email'], "REMINDER: Please Complete Survey By " .$row['EndDate'], GenerateReminderEmailString($PID), $headers, "-fdo-not-reply@rrsurvey.net");
				}
			}
		?>
	</body>
</html>