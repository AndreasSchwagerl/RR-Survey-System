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
				<th colspan="8"><h2 class="heading">Survey</h2></th>
			</tr>
			
			<tr>
				<th>&nbsp;</th>
				<th onclick="sortTable(1)" class="clickable">ID</th>
				<th onclick="sortTable(2)" class="clickable">Start Date</th>
				<th onclick="sortTable(3)" class="clickable">End Date</th>
				<th onclick="sortTable(4)" class="clickable">Completion</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				$ID = $_GET["ID"];
				
				$results = mysqli_query($mysqli, "SELECT s.ID, s.StartDate, s.EndDate, CONCAT(CAST((SELECT DISTINCT COUNT(*) FROM Participant p INNER JOIN Department d ON p.DID = d.ID WHERE submitted = 1 AND d.SID = s.ID) AS char(10)), ' out of ', CAST((SELECT DISTINCT COUNT(*) FROM Participant p INNER JOIN Department d ON p.DID = d.ID WHERE d.SID = s.ID) AS char(10))) AS `Completion` FROM Survey s WHERE s.CID = $ID");
				while($row = mysqli_fetch_array($results)) {
					$rowNum = $row['ID'];
			?>
			
			<tr id="Row<?php echo $row['ID']; ?>">
				<td class="btnSelect"><input type="button" class="btn" id="SelectButton<?php echo $row['ID']; ?>" value="Select" onclick="location.href='http://rrsurvey.net/admin.php?state=department&ID=<?php echo $row['ID']; ?>';"></td>
				<td id="IDRow<?php echo $row['ID']; ?>"><?php echo $row['ID']?></td>
				<td id="StartRow<?php echo $row['ID']; ?>"><?php echo $row['StartDate']?></td>
				<td id="EndRow<?php echo $row['ID']; ?>"><?php echo $row['EndDate']?></td>
				<td id="CompletionRow<?php echo $row['ID']; ?>"><?php echo $row['Completion']?></td>
				<td class="btnGroup">
					<input type="button" class="btn2" id="EditButton<?php echo $row['ID']; ?>" value="Edit" style="display:inline-block;" onclick="EditRow(<?php echo $row['ID']; ?>)">
					<input type="button" class="btn2" id="SaveButton<?php echo $row['ID']; ?>" value="Save" style="display:none;" onClick="SaveRow(<?php echo $row['ID']; ?>)">
					<input type="button" class="btn3" id="CancelButton<?php echo $row['ID']; ?>" value="Cancel" style="display:none;" onClick="CancelRow(<?php echo $row['ID']; ?>)">
					<input type="button" class="btn3" id="DeleteButton<?php echo $row['ID']; ?>" value="Delete" style="display:inline-block;" onClick="DeleteRow(<?php echo $row['ID']; ?>)">
				</td>
				<td class="btnGroup">
                    <input type="button" class="btn" id="GenerateReportButton<?php echo $row['ID']; ?>" value="Generate Report" style="display:inline-block;" onclick="GenerateReport(<?php echo $row['ID']; ?>)">
				</td>
				<td class="btnGroup">
                    <input type="button" class="btn" id="GenerateEmailButton<?php echo $row['ID']; ?>" value="Send Reminder Emails" style="display:inline-block;" onclick="SendReminderEmails(<?php echo $row['ID']; ?>)">
				</td>
			</tr>
			
			<?php
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
		var start = document.getElementById("StartRow"+num);
		var end = document.getElementById("EndRow"+num);
		
		var startData = start.innerHTML;
		var endData = end.innerHTML;
		
		// Change elements from label to textbox
		start.innerHTML = "<input type='date' id='dtpStart"+num+"' value='"+startData+"'>";
		end.innerHTML = "<input type='date' id='dtpEnd"+num+"' value='"+endData+"'>";
		
		// Change visibility of survey management buttons
		document.getElementById("EditButton"+num).style.display="none";
		document.getElementById("DeleteButton"+num).style.display="none";
		document.getElementById("SaveButton"+num).style.display="inline-block";
		document.getElementById("CancelButton"+num).style.display="inline-block";
		
		// Create temporary value object
		var temp = {
			start: startData,
			end: endData,
		};
		
		// Store temporary values in array
		tempArr.push(temp);
	}
	
	function SaveRow(num) {
		// Prompt user for confirmation
		if (!confirm('Save the changes to the entry?')) {
			return;
		}
		
		var start = document.getElementById("dtpStart"+num).value;
		var end = document.getElementById("dtpEnd"+num).value;
		
		// Build the UPDATE SQL statement
		var sql = "UPDATE Survey SET StartDate='" + start + "', EndDate='" + end + "' WHERE ID=" + num + ";";
		
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
				document.getElementById("StartRow"+num).innerHTML=start;
				document.getElementById("EndRow"+num).innerHTML=end;
				
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
		document.getElementById("StartRow"+num).innerHTML=temp['start'];
		document.getElementById("EndRow"+num).innerHTML=temp['end'];
		
		// Change visibility of survey management buttons
		document.getElementById("EditButton"+num).style.display="inline-block";
		document.getElementById("DeleteButton"+num).style.display="inline-block";
		document.getElementById("SaveButton"+num).style.display="none";
		document.getElementById("CancelButton"+num).style.display="none";
	}
	
	function DeleteRow(num) {
		// Prompt user for confirmation
		if (!confirm('Delete the entry?')) {
			return;
		}
		
		// Build the DELETE SQL statement
		var sql = "DELETE FROM Survey WHERE ID=" + num + ";";
		
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
	
	function GenerateReport(num) {
		var comp = document.getElementById("CompletionRow"+num).innerHTML;
		
		// Prompt user for confirmation
        if (comp.startsWith("0 out of ")) {
            alert("Cannot generate a report for a survey with zero responses.");
			return;
        }

        var request = new XMLHttpRequest();
        request.open('POST', 'generatereport.php', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.responseType = 'blob';

        request.onload = function() {
            // Only handle status code 200
            if(request.status === 200) {


                // The actual download
                var blob = new Blob([request.response], { type: 'application/vnd.ms-excel' });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "Survey"+num;
                document.body.appendChild(link);

                link.click();

                document.body.removeChild(link);
            }

            // some error handling should be done here...
        };
        request.send("ID="+num.toString());
	}
	
	function SendReminderEmails(num) {
        // Prompt user for confirmation
        if (!confirm('Send reminder emails to all participants of this survey who have not yet submitted?')) {
            return;
        }

        // Send the reminder emails
        $.ajax({
            type: "POST",
            url: "reminderemail.php",
            data:     {
                        level: 0,
                        ID: num
                    },
            success: function() {
                alert("The reminder emails were sent.");
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