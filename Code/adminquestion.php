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
				<th colspan="4"><h2 class="heading">Questions</h2></th>
			</tr>
			
			<tr>
				<th onclick="sortTable(0)" class="clickable">ID</th>
				<th onclick="sortTable(1)" class="clickable">Left Statement</th>
				<th onclick="sortTable(2)" class="clickable">Right Statement</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				$results = mysqli_query($mysqli, "SELECT * FROM Question");
				while($row = mysqli_fetch_array($results)) {
					$rowNum = $row['ID'];
			?>
			
			<tr id="Row<?php echo $row['ID']; ?>">
				<td id="IDRow<?php echo $row['ID']; ?>"><?php echo $row['ID']?></td>
				<td id="QLRow<?php echo $row['ID']; ?>"><?php echo $row['QL']?></td>
				<td id="QRRow<?php echo $row['ID']; ?>"><?php echo $row['QR']?></td>
				<td class="btnGroup">
					<input type="button" class="btn2" id="EditButton<?php echo $row['ID']; ?>" value="Edit" style="display:inline-block;" onclick="EditRow(<?php echo $row['ID']; ?>)">
					<input type="button" class="btn2" id="SaveButton<?php echo $row['ID']; ?>" value="Save" style="display:none;" onClick="SaveRow(<?php echo $row['ID']; ?>)">
					<input type="button" class="btn3" id="CancelButton<?php echo $row['ID']; ?>" value="Cancel" style="display:none;" onClick="CancelRow(<?php echo $row['ID']; ?>)">
					<input type="button" class="btn3" id="DeleteButton<?php echo $row['ID']; ?>" value="Delete" style="display:inline-block;" onClick="DeleteRow(<?php echo $row['ID']; ?>)">
				</td>
			</tr>
			
			<?php
				}
			?>
			
			<tr>
				<td><input type="text" placeholder="Enter Question ID" id="NewID"></td>
				<td><input type="text" placeholder="Enter Left Statement" id="NewQL"></td>
				<td><input type="text" placeholder="Enter Right Statement" id="NewQR"></td>
				<td><div align="center"><input type="button" class="btn" id="AddButton" value="Add Question" onClick="AddRow()"></div></td>
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
	
	document.getElementById("wrap").addEventListener("scroll",function() {
		var translate = "translate(0,"+this.scrollTop+"px)";
		this.querySelector("thead").style.transform = translate;
	});
	
	function EditRow(num) {
		var ID = document.getElementById("IDRow"+num);
		var QL = document.getElementById("QLRow"+num);
		var QR = document.getElementById("QRRow"+num);
		
		var IDData = ID.innerHTML;
		var QLData = QL.innerHTML;
		var QRData = QR.innerHTML;
		
		// Change elements from label to textbox
		ID.innerHTML = "<input type='text' id='IDText"+num+"' value='"+IDData+"'>";
		QL.innerHTML = "<input type='text' id='QLText"+num+"' value='"+QLData+"'>";
		QR.innerHTML = "<input type='text' id='QRText"+num+"' value='"+QRData+"'>";
		
		// Change visibility of survey management buttons
		document.getElementById("EditButton"+num).style.display="none";
		document.getElementById("DeleteButton"+num).style.display="none";
		document.getElementById("SaveButton"+num).style.display="inline-block";
		document.getElementById("CancelButton"+num).style.display="inline-block";
		
		// Create temporary value object
		var temp = {
			ID: IDData,
			QL: QLData,
			QR: QRData,
		};
		
		// Store temporary values in array
		tempArr.push(temp);
	}
	
	function SaveRow(num) {
		// Prompt user for confirmation
		if (!confirm('Save the changes to the entry?')) {
			return;
		}
		
		var ID = document.getElementById("IDText"+num).value;
		var QL = document.getElementById("QLText"+num).value;
		var QR = document.getElementById("QRText"+num).value;
		
		// Build the UPDATE SQL statement
		var sql = "UPDATE Question SET ID='" + ID + "', QL='" + QL + "', QR='" + QR + "' WHERE ID=" + num + ";";
		
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
				document.getElementById("IDRow"+num).innerHTML=ID;
				document.getElementById("QLRow"+num).innerHTML=QL;
				document.getElementById("QRRow"+num).innerHTML=QR;
				
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
		document.getElementById("IDRow"+num).innerHTML=temp['ID'];
		document.getElementById("QLRow"+num).innerHTML=temp['QL'];
		document.getElementById("QRRow"+num).innerHTML=temp['QR'];
		
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
		var sql = "DELETE FROM Question WHERE ID=" + num + ";";
		
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
		if (!confirm('Create a new entry with the entered data?')) {
			return;
		}
		
		var newID = document.getElementById("NewID").value;
		var newQL = document.getElementById("NewQL").value;
		var newQR = document.getElementById("NewQR").value;
		
		// Build the INSERT SQL statement
		var sql = "INSERT INTO Question (ID, QL, QR) VALUES ('" + newID + "', '" + newQL + "', '" + newQR + "');";
		
		$.ajax({
			type: "POST",
			url: "querydatabase.php",
			data: 	{
						sql: sql,
						type: "i"
					},
			success: function() {
				// Create new row on table
				var table = document.getElementById("dataTable");
				var tableLen = (table.rows.length)-1;
				var ID = newID;
				var row = table.insertRow(tableLen).outerHTML=""+
					"<tr id='Row"+ID+"'>"+
						"<td id='IDRow"+ID+"'>"+newID+"</td>"+
						"<td id='QLRow"+ID+"'>"+newQL+"</td>"+
						"<td id='QRRow"+ID+"'>"+newQR+"</td>"+
						"<td><input type='button' class='btn2' id='EditButton"+ID+"' value='Edit' style='display:inline-block;' onClick='EditRow("+ID+");'>"+
							 "<input type='button' class='btn2' id='SaveButton"+ID+"' value='Save' style='display:none;' onClick='SaveRow("+ID+");'>"+
							 "<input type='button' class='btn3' id='CancelButton"+ID+"' value='Cancel' style='display:none;' onClick='CancelRow("+ID+");'>"+
							 "<input type='button' class='btn3' id='DeleteButton"+ID+"' value='Delete' style='display:inline-block;' onClick='DeleteRow("+ID+");'>"+
						"</td>"+
					"</tr>";
				
				// Clear new entry textboxes
				document.getElementById("NewID").value = "";
				document.getElementById("NewQL").value = "";
				document.getElementById("NewQR").value = "";
				document.getElementById("NewAddress").value = "";
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
					if (num == 0) {
						if (parseInt(x.innerHTML) > parseInt(y.innerHTML)) {
							sort= true;
							break;
						}
					} else {
						if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
							sort= true;
							break;
						}
					}
				} else if (dir == "desc") {
					if (num == 0) {
						if (parseInt(x.innerHTML) < parseInt(y.innerHTML)) {
							sort= true;
							break;
						}
					} else {
						if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
							sort= true;
							break;
						}
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