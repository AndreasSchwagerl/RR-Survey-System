<?php
	$dbhost = 'localhost';
	$dbconnect = parse_ini_file('../connect.ini');
	$mysqli = new mysqli($dbhost, $dbconnect['username'], $dbconnect['password'], $dbconnect['dbname']);
	
	if ($mysqli->connect_error) {
		exit('There was an error connecting to the database.');
	}
?>

<div id="wrap">
	<table id="dataTable">
		<thead>
			<tr>
				<th colspan="6"><h2 class="heading">Participant</h2></th>
			</tr>
			
			<tr>
				<th onclick="sortTable(0)" class="clickable">ID</th>
				<th onclick="sortTable(1)" class="clickable">Email</th>
				<th onclick="sortTable(2)" class="clickable">Submitted</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				$ID = $_GET["ID"];
				$num = 1; 
				
				$results = mysqli_query($mysqli, "SELECT * FROM Participant p WHERE p.DID = $ID");
				while($row = mysqli_fetch_array($results)) {
					$rowNum = $row['ID'];
			?>
			
			<tr id="Row<?php echo $num; ?>">
				<td id="IDRow<?php echo $num; ?>"><?php echo $row['ID']?></td>
				<td id="EmailRow<?php echo $num; ?>"><?php echo $row['Email']?></td>
				<td id="SubmittedRow<?php echo $num;; ?>"><?php echo $row['Submitted']?></td>
				<td class="btnGroup">
					<input type="button" class="btn2" id="EditButton<?php echo $num;; ?>" value="Edit" style="display:inline-block;" onclick="EditRow(<?php echo $num; ?>)">
					<input type="button" class="btn2" id="SaveButton<?php echo $num;; ?>" value="Save" style="display:none;" onClick="SaveRow(<?php echo $num; ?>)">
					<input type="button" class="btn3" id="CancelButton<?php echo $num;; ?>" value="Cancel" style="display:none;" onClick="CancelRow(<?php echo $num; ?>)">
					<input type="button" class="btn3" id="DeleteButton<?php echo $num;; ?>" value="Delete" style="display:inline-block;" onClick="DeleteRow(<?php echo $num; ?>)">
				</td>
				<td class="btnGroup">
					<input type="button" class="btn" id="GenerateButton<?php echo $num;; ?>" value="Send Reminder Email" style="display:inline-block;" onclick="SendReminderEmail(<?php echo $num; ?>)">
				</td>
			</tr>
			
			<?php
					$num++;
				}
			?>
		</tbody>
	</table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
	var tempArr = [];
	
	function resize() {
		// Offset for admin page layout
		var height = window.innerHeight - 145;
		document.getElementById("wrap").style.height = height + "px";
	}
	
	resize();
	
	window.onresize = function() {
		resize();
	};
	
	function EditRow(num) {
		var email = document.getElementById("EmailRow"+num);
		
		var emailData = email.innerHTML;
		
		// Change elements from label to textbox
		email.innerHTML = "<input type='text' id='EmailText"+num+"' value='"+emailData+"'>";
		
		// Change visibility of survey management buttons
		document.getElementById("EditButton"+num).style.display="none";
		document.getElementById("DeleteButton"+num).style.display="none";
		document.getElementById("SaveButton"+num).style.display="inline-block";
		document.getElementById("CancelButton"+num).style.display="inline-block";
		
		// Create temporary value object
		var temp = {
			email: emailData,
		};
		
		// Store temporary values in array
		tempArr.push(temp);
	}
	
	function SaveRow(num) {
		// Prompt user for confirmation
		if (!confirm('Save the changes to the entry?')) {
			return;
		}
		
		// Get participant ID and email from the table
		var ID = document.getElementById("IDRow"+num).innerHTML;
		var email = document.getElementById("EmailText"+num).value;
		
		// Build the UPDATE SQL statement
		var sql = "UPDATE Participant SET Email='" + email + "' WHERE ID='" + ID + "';";
		
		// Send SQL statement with AJAX
		$.ajax({
			type: "POST",
			url: "querydatabase.php",
			data: 	{
						sql: sql,
						type: "u"
					},
			success: function() {
				// Change elements from textbox back to label
				document.getElementById("EmailRow"+num).innerHTML=email;
				
				// Change visibility of survey management buttons
				document.getElementById("EditButton"+num).style.display="inline-block";
				document.getElementById("DeleteButton"+num).style.display="inline-block";
				document.getElementById("SaveButton"+num).style.display="none";
				document.getElementById("CancelButton"+num).style.display="none";
				
				// Remove temporary values from array
				tempArr.shift();
			}
		});
	}
	
	function CancelRow(num) {
		// Retreive temporary values from array
		var temp = tempArr.shift();
		
		// Change elements from textbox back to label
		document.getElementById("EmailRow"+num).innerHTML=temp['email'];
		
		// Change visibility of survey management buttons
		document.getElementById("EditButton"+num).style.display="inline-block";
		document.getElementById("DeleteButton"+num).style.display="inline-block";
		document.getElementById("SaveButton"+num).style.display="none";
		document.getElementById("CancelButton"+num).style.display="none";
	}
	
	function DeleteRow(num) {
		// Prompt user for confirmation
		if (document.getElementById("SubmittedRow"+num).innerHTML == "1") {
			alert("Once a participant has submitted, they cannot be deleted individually.");
			return;
		}
		
		if (!confirm('Delete the entry?')) {
			return;
		}
		
		// Get the Participant ID from the table
		var ID = document.getElementById("IDRow"+num).innerHTML;
		
		// Build the DELETE SQL statement
		var sql = "DELETE FROM Participant WHERE ID='" + ID + "';";
		
		$.ajax({
			type: "POST",
			url: "querydatabase.php",
			data: 	{
						sql: sql,
						type: "d"
					},
			success: function() {
				// Remove row from table
				document.getElementById("Row"+num+"").outerHTML="";
			}
		});
	}
	
	function SendReminderEmail(num) {
		// Check if the participant has already submitted, and notify the user if necessary
		if (document.getElementById("SubmittedRow"+num).innerHTML == "1") {
			alert("This participant has already submitted.");
			return;
		}
		
		// Prompt user for confirmation
		if (!confirm('Send a reminder email to this participant?')) {
			return;
		}
		
		// Get Participant ID from the table
		var ID = document.getElementById("IDRow"+num).innerHTML;
		
		// Send the reminder email
		$.ajax({
			type: "POST",
			url: "reminderemail.php",
			data: 	{
						level: 2,
						ID: ID
					},
			success: function() {
				alert("The reminder email was sent.");
			}
		});
	}
	
	function sortTable(num) {
		var rows, sort, x, y;
		
		var table = document.getElementById("dataTable");
		var sorting = true;
		var count = 0;
		var dir = "asc";
		
		while (sorting) {
			sorting = false;
			rows = table.getElementsByTagName("TR");
			
			for (var i = 2; i < (rows.length - 1); i++) {
				sort = false;

				x = rows[i].getElementsByTagName("TD")[num];
				y = rows[i + 1].getElementsByTagName("TD")[num];
				
				if (dir == "asc") {
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
						sort= true;
						break;
					}
				} else if (dir == "desc") {
					if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
						sort= true;
						break;
					}
				}
			}
			
			if (sort) {
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				sorting = true;
				count++;
			} else {
				if (count == 0 && dir == "asc") {
					dir = "desc";
					sorting = true;
				}
			}
		}
	}
</script>