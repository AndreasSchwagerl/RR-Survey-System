<!DOCTYPE html>
<html>
	<head>
		<style>
			#wrap {
				float: left;
				overflow: auto;
				height: 100%;
				border: 1px solid #000;
			}
			
			.btn {
				height: 50px;
				width: 100%;
				font-size: 1.5em;
			}
			
			h1.heading  {
				margin-top: 0;
				margin-bottom: 0;
			}
			
			body {
				margin: 0;
			}
			
			table {
				border-spacing: 0;
				width: 100%;
			}
			
			td {
				border-bottom: 1px solid #000;
				border-right: 1px solid #000;
				color: #000;
				padding: 10px 25px;
			}
			
			th {
				background-color: black;
				
				border-right: 1px solid #FFF;
				border-bottom: 1px solid #FFF;
				color: #FFF;
				padding: 10px 25px;
			}
			
			table tr:first-child th {
				border-top: 1px solid #000;
			}
			
			table tr:last-child th {
				border-bottom: 1px solid #000;
			}
			
			table tr th:first-child {
				border-left: 1px solid #000;
			}
			
			table tr th:last-child {
				border-right: 1px solid #000;
			}
			
			tr:nth-child(even) {
				background-color: #eee;
			}
			
			tr:nth-child(odd) {
			   background-color:#fff;
			}
		</style>
	</head>
	
	<?php
		// Get ID from hyperlink
		$ID = $_GET["ID"];
		
		// Get database connection information
		$dbhost = 'localhost';
		$dbconnect = parse_ini_file('../connect.ini');
		
		// Initiate mysqli connection
		$mysqli = new mysqli($dbhost, $dbconnect['username'], $dbconnect['password'], $dbconnect['dbname']);
		
		// Check for a connection error and handle if necessary
		if ($mysqli->connect_error) {
			exit('There was an error connecting to the database.');
		}
		
		// Create a prepared SQL statement and execute it
		$stmt = $mysqli->prepare('SELECT p.Submitted, d.ID, s.ID FROM (Participant p LEFT JOIN Department d ON p.DID = d.ID) LEFT JOIN Survey s ON d.SID = s.ID WHERE p.ID = ?');
		$stmt->bind_param('s', $ID);
		$stmt->execute();
		$stmt->store_result();
	?>
	
	<body>
		<?php if ($stmt != false) : 
			if($stmt->num_rows === 0) : ?>
				<form align="center">
					<h1>Invalid ID</h1>
				</form>
			<?php else :
				// Get results from SQL statement and bind fields to variables
				$stmt->bind_result($submitted, $DID, $SID);
				$stmt->fetch();
				
				// If the participant has submitted, show notification
				if($submitted == 1) : ?>
					<form align="center">
						<h1>The Survey Has<br>Already Been Submitted</h1>
					</form>
				<?php else :
					// If POST was called, submit the survey
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						// Get the number of questions to loop through
						$numQuestions = mysqli_fetch_object(mysqli_query($mysqli, "SELECT Count(*) AS C FROM SurveyQuestion s LEFT JOIN Question q ON s.QID = q.ID WHERE SID = $SID"))->C;
						
						// Create response entries in the database
						for ($i = 1; $i <= $numQuestions; $i++) {
							$insert = mysqli_query($mysqli, "INSERT INTO Response (DID, QID, Response) VALUES ('$DID', '$i', '" .(int)$_POST['sub' .$i]. "');");
						}
						
						// Update the participant entry; Submitted = TRUE
						$update = mysqli_query($mysqli, "UPDATE Participant SET Submitted = 1 WHERE ID = '$ID';");
					}
					
					// If update was successful, show notification
					if ($update != false) : ?>
						<form align="center">
							<h1>The survey has been submitted.<br>Thank you for your participation.</h1>
						</form>
					<?php else : ?>
						<form action="" onsubmit="return validateForm()" method = "post">
							<div id="wrap">
								<table>
									<colgroup>
										<col style="width:75px">
										<col style="width:50%">
										<col style="width:100px">
										<col style="width:100px">
										<col style="width:100px">
										<col style="width:100px">
										<col style="width:100px">
										<col style="width:100px">
										<col style="width:50%">
									</colgroup>
									
									<thead>
										<tr>
											<th colspan="9"><h1 class="heading">Survey</h1></th>
										</tr>
										
										<tr>
											<th colspan="2">&nbsp;</th>
											<th colspan="3">Left Statement</th>
											<th colspan="3">Right Statement</th>
											<th colspan="1">&nbsp;</th>
										</tr>
										
										<tr>
											<th>#</th>
											<th>&nbsp;</th>
											<th>Fully Agree</th>
											<th>Mostly Agree</th>
											<th>Partly Agree</th>
											<th>Partly Agree</th>
											<th>Mostly Agree</th>
											<th>Fully Agree</th>
											<th>&nbsp;</th>
										</tr>
									</thead>
									
									<tbody>
										<?php
											// Get the survey questions
											$result = mysqli_query($mysqli, "SELECT s.Order, q.QL, q.QR FROM SurveyQuestion s LEFT JOIN Question q ON s.QID = q.ID WHERE SID = $SID ORDER BY s.Order ASC");
											$rowNum = mysqli_num_rows($result);
											
											// Loop through each question, generating table rows
											while($row = mysqli_fetch_array($result)) {
										?>
											<tr>
												<td><?php echo $row['Order']?></td>
												<td><?php echo $row['QL']?></td>
												<td><div align="center"><input type="radio" name="sub<?php echo $row['ID']; ?>" value=1 <?php if($_POST['sub' .$row['ID']] == 1) { echo 'checked="checked"';} ?>></div></td>
												<td><div align="center"><input type="radio" name="sub<?php echo $row['ID']; ?>" value=2 <?php if($_POST['sub' .$row['ID']] == 2) { echo 'checked="checked"';} ?>></div></td>
												<td><div align="center"><input type="radio" name="sub<?php echo $row['ID']; ?>" value=3 <?php if($_POST['sub' .$row['ID']] == 3) { echo 'checked="checked"';} ?>></div></td>
												<td><div align="center"><input type="radio" name="sub<?php echo $row['ID']; ?>" value=4 <?php if($_POST['sub' .$row['ID']] == 4) { echo 'checked="checked"';} ?>></div></td>
												<td><div align="center"><input type="radio" name="sub<?php echo $row['ID']; ?>" value=5 <?php if($_POST['sub' .$row['ID']] == 5) { echo 'checked="checked"';} ?>></div></td>
												<td><div align="center"><input type="radio" name="sub<?php echo $row['ID']; ?>" value=6 <?php if($_POST['sub' .$row['ID']] == 6) { echo 'checked="checked"';} ?>></div></td>
												<td><?php echo $row['QR']?></td>
											</tr>
										<?php
											}
										?>
									</tbody>
									
									<tfoot>
										<tr>
											<td colspan="8"><div align="center">Thank you for your participation.<br>Please click the Submit button to complete the survey.</div></td>
											<td colspan="1"><div align="center"><input type="submit" name="submit" class="btn"/></div></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</form>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		
		<script>
			function resize() {
				var heights = window.innerHeight - 2;
				document.getElementById("wrap").style.height = heights + "px";
			}
			
			resize();
			
			window.onresize = function() {
				resize();
			};
			
			function validateForm() {
				var radio;
				var check = false;
				
				for (i = 1; i <= <?php echo $rowNum ?>; i++) {
					radio = document.getElementsByName("sub" + i);
					
					for (j = 0; j < radio.length; j++) {
						if (radio[j].checked) {
							check = true;
							break;
						}
					}
					
					if (!check) {
						alert("You have not made a selection for statement #" + i + ".\n" + "Please complete the survey before submitting.");
						return false
					}
					
					check = false;
				}
				
				return true;
			}
			
			document.getElementById("wrap").addEventListener("scroll",function() {
				var translate = "translate(0,"+this.scrollTop+"px)";
				this.querySelector("thead").style.transform = translate;
			});
		</script>
	</body>
</html>