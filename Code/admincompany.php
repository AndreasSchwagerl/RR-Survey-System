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
				<th colspan="6"><h2 class="heading">Company</h2></th>
			</tr>
			
			<tr>
				<th>&nbsp;</th>
				<th onclick="sortTable(1)" class="clickable">Name</th>
				<th onclick="sortTable(2)" class="clickable">Email</th>
				<th onclick="sortTable(3)" class="clickable">Phone</th>
				<th onclick="sortTable(4)" class="clickable">Address</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				$results = mysqli_query($mysqli, "SELECT * FROM Company");
				while($row = mysqli_fetch_array($results)) {
					$rowNum = $row['ID'];
			?>
			
			<tr id="Row<?php echo $row['ID']; ?>">
				<td class="btnSelect"><input type="button" class="btn" id="SelectButton<?php echo $row['ID']; ?>" value="Select" onclick="location.href='http://rrsurvey.net/admin.php?state=survey&ID=<?php echo $row['ID']; ?>';"></td>
				<td id="NameRow<?php echo $row['ID']; ?>"><?php echo $row['Name']?></td>
				<td id="EmailRow<?php echo $row['ID']; ?>"><?php echo $row['Email']?></td>
				<td id="PhoneRow<?php echo $row['ID']; ?>"><?php echo $row['Phone']?></td>
				<td id="AddressRow<?php echo $row['ID']; ?>"><?php echo $row['Address']?></td>
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
				<td>&nbsp;</td>
				<td><input type="text" placeholder="Enter Company Name" id="NewName"></td>
				<td><input type="text" placeholder="Enter Email Address" id="NewEmail"></td>
				<td><input type="text" placeholder="Enter Phone Number" id="NewPhone"></td>
				<td><input type="text" placeholder="Enter Company Address" id="NewAddress"></td>
				<td><div align="center"><input type="button" class="btn" id="AddButton" value="Add Company" onClick="AddRow()"></div></td>
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
		var name = document.getElementById("NameRow"+num);
		var email = document.getElementById("EmailRow"+num);
		var phone = document.getElementById("PhoneRow"+num);
		var address = document.getElementById("AddressRow"+num);
		
		var nameData = name.innerHTML;
		var emailData = email.innerHTML;
		var phoneData = phone.innerHTML;
		var addressData = address.innerHTML;
		
		// Change elements from label to textbox
		name.innerHTML = "<input type='text' id='NameText"+num+"' value='"+nameData+"'>";
		email.innerHTML = "<input type='text' id='EmailText"+num+"' value='"+emailData+"'>";
		phone.innerHTML = "<input type='text' id='PhoneText"+num+"' value='"+phoneData+"'>";
		address.innerHTML = "<input type='text' id='AddressText"+num+"' value='"+addressData+"'>";
		
		// Change visibility of survey management buttons
		document.getElementById("EditButton"+num).style.display="none";
		document.getElementById("DeleteButton"+num).style.display="none";
		document.getElementById("SaveButton"+num).style.display="inline-block";
		document.getElementById("CancelButton"+num).style.display="inline-block";
		
		// Create temporary value object
		var temp = {
			name: nameData,
			email: emailData,
			phone: phoneData,
			address: addressData
		};
		
		// Store temporary values in array
		tempArr.push(temp);
	}
	
	function SaveRow(num) {
		// Prompt user for confirmation
		if (!confirm('Save the changes to the entry?')) {
			return;
		}
		
		var name = document.getElementById("NameText"+num).value;
		var email = document.getElementById("EmailText"+num).value;
		var phone = document.getElementById("PhoneText"+num).value;
		var address = document.getElementById("AddressText"+num).value;
		
		// Build the UPDATE SQL statement
		var sql = "UPDATE Company SET Name='" + name + "', Email='" + email + "', Phone='" + phone + "', Address='" + address + "' WHERE ID=" + num + ";";
		
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
				document.getElementById("NameRow"+num).innerHTML=name;
				document.getElementById("EmailRow"+num).innerHTML=email;
				document.getElementById("PhoneRow"+num).innerHTML=phone;
				document.getElementById("AddressRow"+num).innerHTML=address;
				
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
		document.getElementById("NameRow"+num).innerHTML=temp['name'];
		document.getElementById("EmailRow"+num).innerHTML=temp['email'];
		document.getElementById("PhoneRow"+num).innerHTML=temp['phone'];
		document.getElementById("AddressRow"+num).innerHTML=temp['address'];
		
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
		var sql = "DELETE FROM Company WHERE ID=" + num + ";";
		
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
		
		var newName = document.getElementById("NewName").value;
		var newEmail = document.getElementById("NewEmail").value;
		var newPhone = document.getElementById("NewPhone").value;
		var newAddress = document.getElementById("NewAddress").value;
		
		// Build the INSERT SQL statement
		var sql = "INSERT INTO Company (Name, Email, Phone, Address) VALUES ('" + newName + "', '" + newEmail + "', '" + newPhone + "', '" + newAddress + "');";
		
		$.ajax({
			type: "POST",
			url: "querydatabase.php",
			data: 	{
						sql: sql,
						type: "i"
					},
			success: function(data) {
				// Create new row on table
				var table=document.getElementById("dataTable");
				var tableLen = (table.rows.length)-1;
				var ID = data;
				var row = table.insertRow(tableLen).outerHTML=""+
					"<tr id='Row"+ID+"'>"+
						"<td><input type='button' class='btn' id='SelectButton"+ID+"' value='Select'></td>"+
						"<td id='NameRow"+ID+"'>"+newName+"</td>"+
						"<td id='EmailRow"+ID+"'>"+newEmail+"</td>"+
						"<td id='PhoneRow"+ID+"'>"+newPhone+"</td>"+
						"<td id='AddressRow"+ID+"'>"+newAddress+"</td>"+
						"<td><input type='button' class='btn2' id='EditButton"+ID+"' value='Edit' style='display:inline-block;' onClick='EditRow("+ID+");'>"+
							 "<input type='button' class='btn2' id='SaveButton"+ID+"' value='Save' style='display:none;' onClick='SaveRow("+ID+");'>"+
							 "<input type='button' class='btn3' id='CancelButton"+ID+"' value='Cancel' style='display:none;' onClick='CancelRow("+ID+");'>"+
							 "<input type='button' class='btn3' id='DeleteButton"+ID+"' value='Delete' style='display:inline-block;' onClick='DeleteRow("+ID+");'>"+
						"</td>"+
					"</tr>";
				
				// Clear new entry textboxes
				document.getElementById("NewName").value = "";
				document.getElementById("NewEmail").value = "";
				document.getElementById("NewPhone").value = "";
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