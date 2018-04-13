<?php
	$dbhost = 'localhost';
	$dbconnect = parse_ini_file('connect.ini');
	$mysqli = new mysqli($dbhost, $dbconnect['username'], $dbconnect['password'], $dbconnect['dbname']);
	
	if ($mysqli->connect_error) {
		exit('There was an error connecting to the database.');
	}
?>

<div id="wrap">
	<table id="dataTable">
		<thead>
			<tr>
				<th colspan="7"><h2 class="heading">Participant</h2></th>
			</tr>
			
			<tr>
				<th onclick="sortTable(0)" class="clickable" style="word-wrap: break-word; max-width: 350px;">ID</th>
				<th onclick="sortTable(1)" class="clickable">Email</th>
				<th onclick="sortTable(2)" class="clickable">Submitted</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				$ID = $_GET["ID"];
				$num = 1;
				
				echo "<script>var DID = $ID;</script>";
				
				$DIDArr = [];
				$deptArr = [];
				
				$departments = mysqli_query($mysqli, "SELECT d.ID, d.Name FROM Department d WHERE d.SID = (SELECT d.SID FROM Department d WHERE d.ID = $ID) AND d.ID <> $ID;");
				
				while ($drow = mysqli_fetch_array($departments)) {
					array_push($DIDArr, $drow['Name']);
					array_push($deptArr, $drow['ID']);
				}
				
				$results = mysqli_query($mysqli, "SELECT * FROM Participant p WHERE p.DID = $ID");
				while($row = mysqli_fetch_array($results)) {
					$rowNum = $row['ID'];
			?>
			
			<tr id="Row<?php echo $num; ?>">
				<td style="word-wrap: break-word; max-width: 350px;" id="IDRow<?php echo $num; ?>"><?php echo $row['ID']?></td>
				<td id="EmailRow<?php echo $num; ?>"><?php echo $row['Email']?></td>
				<td id="SubmittedRow<?php echo $num;; ?>"><?php echo $row['Submitted']?></td>
				<td class="btnGroup">
					<input type="button" class="btn2" id="EditButton<?php echo $num; ?>" value="Edit" style="display:inline-block;" onclick="EditRow(<?php echo $num; ?>)">
					<input type="button" class="btn2" id="SaveButton<?php echo $num; ?>" value="Save" style="display:none;" onClick="SaveRow(<?php echo $num; ?>)">
					<input type="button" class="btn3" id="CancelButton<?php echo $num; ?>" value="Cancel" style="display:none;" onClick="CancelRow(<?php echo $num; ?>)">
					<input type="button" class="btn3" id="DeleteButton<?php echo $num; ?>" value="Delete" style="display:inline-block;" onClick="DeleteRow(<?php echo $num; ?>)">
				</td>
				<td class="btnGroup">
					<input type="button" class="dropbtn btn" id="MoveButton<?php echo $num; ?>" value="Move Participant" style="display:inline-block;" onclick="ShowDropdown(<?php echo $num; ?>)">
					<div id="dropdown<?php echo $num?>" class="dropdown-content">
						<?php
							for ($i = 0; $i < count($deptArr); $i++) {
								$DID = $deptArr[$i];
								$dept = $DIDArr[$i];
								
								echo "<a href='#' title='Move Participant to $dept' onclick='MoveParticipant($num, \"$dept\", $DID); return false;'>$dept</a>";
							}
							
							
						?>
					</div>
				</td>
				<td class="btnGroup">
					<input type="button" class="btn" id="GenerateButton<?php echo $num; ?>" value="Send Reminder Email" style="display:inline-block;" onclick="SendReminderEmail(<?php echo $num; ?>)">
				</td>
			</tr>
			
			<?php
					$num++;
				}
				
				echo "<script>var num = $num;</script>";
			?>
			
			<tr>
				<td><input type="text" placeholder="Automatically Generated" id="NewID" disabled></td>
				<td><input type="text" placeholder="Enter Email Address" id="NewEmail"></td>
				<td><input type="text" placeholder="0" id="NewSubmitted" disabled></td>
				<td><div align="center"><input type="button" class="btn" id="AddButton" value="Add Participant" onClick="AddRow()"></div></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
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
	
	window.onclick = function(event) {
		if (!event.target.matches('.dropbtn')) {
			var dropdowns = document.getElementsByClassName("dropdown-content");
			
			for (var i = 0; i < dropdowns.length; i++) {
				var openDropdown = dropdowns[i];
				
				if (openDropdown.classList.contains('show')) {
					openDropdown.classList.remove('show');
				}
			}
		}
	}
	
	document.getElementById("wrap").addEventListener("scroll",function() {
		var translate = "translate(0,"+this.scrollTop+"px)";
		this.querySelector("thead").style.transform = translate;
	});
	
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
	
	function AddRow() {
		// Prompt user for confirmation
		if (!confirm('Create a new participant with the entered data?')) {
			return;
		}
		
		var newEmail = document.getElementById("NewEmail").value;
		
		$.ajax({
			type: "POST",
			url: "createparticipant.php",
			data: 	{
						DID: DID,
						email: newEmail
					},
			success: function(data) {
				// Create new row on table
				var table=document.getElementById("dataTable");
				var tableLen = (table.rows.length)-1;
				var row = table.insertRow(tableLen).outerHTML=""+
					"<tr id='Row"+num+"'>"+
						"<td style='word-wrap: break-word; max-width: 350px;' id='IDRow"+num+"'>"+data+"</td>"+
						"<td id='EmailRow"+num+"'>"+newEmail+"</td>"+
						"<td id='SubmittedRow"+num+"'>0</td>"+
						"<td class='btnGroup'>"+
							"<input type='button' class='btn2' id='EditButton"+num+"' value='Edit' style='display:inline-block;' onclick='EditRow("+num+")'>"+
							"<input type='button' class='btn2' id='SaveButton"+num+"' value='Save' style='display:none;' onClick='SaveRow("+num+")'>"+
							"<input type='button' class='btn3' id='CancelButton"+num+"' value='Cancel' style='display:none;' onClick='CancelRow("+num+")'>"+
							"<input type='button' class='btn3' id='DeleteButton"+num+"' value='Delete' style='display:inline-block;' onClick='DeleteRow("+num+")'>"+
						"</td>"+
						"<td class='btnGroup'>"+
							"<input type='button' class='btn' id='GenerateButton"+num+"' value='Send Reminder Email' style='display:inline-block;' onclick='SendReminderEmail("+num+")'>"+
						"</td>"+
						"<td class='btnGroup'>"+
							"<input type='button' class='btn' id='GenerateButton"+num+"' value='Send Reminder Email' style='display:inline-block;' onclick='SendReminderEmail("+num+")'>"+
						"</td>"+
					"</tr>";
				
				// Clear new entry textboxes
				document.getElementById("NewEmail").value = "";
				
				// Increment ID number
				num++;
			}
		});
	}
	
	function ShowDropdown(num) {
		document.getElementById("dropdown"+num).classList.toggle("show");
	}
	
	function MoveParticipant(num, dept, DID) {
		// Prompt user for confirmation
		if (document.getElementById("SubmittedRow"+num).innerHTML == "1") {
			alert("Once a participant has submitted, they cannot be moved.");
			return;
		}
		
		// Prompt user for confirmation
		if (!confirm('Move the participant to the '+dept+' department?')) {
			return;
		}
		
		var PID = document.getElementById("IDRow"+num).innerHTML;
		
		// Build the DELETE SQL statement
		var sql = "UPDATE Participant p SET p.DID = "+DID+" WHERE p.ID = '"+PID+"';";
		
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
			
			for (var i = 2; i < (rows.length - 2); i++) {
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