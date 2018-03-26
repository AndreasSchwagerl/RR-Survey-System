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
				font-size: 1em;
				height: 25px;
				width: 100%;
			}
			
			.btn2 {
				font-size: 1em;
				height: 25px;
				width: 50%;
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
				table-layout: fixed;
				width: 100%;
			}
			
			td {
				border-bottom: 1px solid #000;
				border-right: 1px solid #000;
				color: #000;
				padding: 10px 25px;
			}
			
			table td input {
				width: 100%;
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
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<link rel="stylesheet" href="http://localhost/Survey/tabMenu2.css"/>

</head>

<body>
<div id ="navbar">
<div id ="holder" style="height: 65px">

<ul id="nav">
<li><a href="company" id="onlink">Manage Surveys</a></li>
<li><a href="createNewSurvey" >Create New Survey</a></li>
<li><a href="manageQuestions" >Manage Questions</a></li>
</ul>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="http://localhost/Survey/general.js"></script>
</div>
</div>
	</head>
	
	<?php

		include('connect-db.php');

		// get results from database

		$result = mysql_query("SELECT * FROM company")

		or die(mysql_error());
?>
	
	<body>
		<div id="wrap">
			<table id="dataTable">
				<colgroup>
					<col style="width:150px">
					<col style="width:25%">
					<col style="width:25%">
					<col style="width:25%">
					<col style="width:25%">
					<col style="width:250px">
					<col style="width:250px">
				</colgroup>
				
				<thead>
					<tr>
						<th colspan="7"><h1 class="heading">Survey Control Panel</h1></th>
					</tr>
					
					<tr>
						<th colspan="7"><h2 class="heading">Company</h2></th>
					</tr>
						
					<tr>
						<th>&nbsp;</th>
						<th>ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Address</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
					
				<tbody>
					<?php
						$results = mysql_query("SELECT * FROM company");
						while($row = mysql_fetch_array($results)) {
							$rowNum = $row['ID'];
					?>
						
					<tr id="Row<?php echo $row['ID']; ?>">
						<td><a href="survey2"><input type="button" class="btn" id="SelectButton<?php echo $row['ID']; ?>" value="Select"></td>
						<td id="IDRow<?php echo $row['ID']; ?>"><?php echo $row['ID']?></td>
						<td id="NameRow<?php echo $row['ID']; ?>"><?php echo $row['Name']?></td>
						<td id="EmailRow<?php echo $row['ID']; ?>"><?php echo $row['Email']?></td>
						<td id="PhoneRow<?php echo $row['ID']; ?>"><?php echo $row['Phone']?></td>
						<td id="AddressRow<?php echo $row['ID']; ?>"><?php echo $row['Address']?></td>
						<td><input type="button" class="btn2" id="EditButton<?php echo $row['ID']; ?>" value="Edit" style="display:inline-block;" onclick="EditRow(<?php echo $row['ID']; ?>)"><input type="button" class="btn2" id="SaveButton<?php echo $row['ID']; ?>" value="Save" style="display:none;" onClick="SaveRow(<?php echo $row['ID']; ?>)"><input type="button" class="btn2" id="DeleteButton<?php echo $row['ID']; ?>" value="Delete" style="display:inline-block;" onClick="DeleteRow(<?php echo $row['ID']; ?>)"></td>
					</tr>
						
					<?php
						}
					?>
						
					<tr>
						<td>&nbsp;</td>
						<td><input type="text" placeholder="Enter Company ID" id="NewID"></td>
						<td><input type="text" placeholder="Enter Company Name" id="NewName"></td>
						<td><input type="text" placeholder="Enter Email Address" id="NewEmail"></td>
						<td><input type="text" placeholder="Enter Phone Number" id="NewPhone"></td>
						<td><input type="text" placeholder="Enter Company Address" id="NewAddress"></td>
						<td><div align="center"><input type="button" class="btn" id="AddButton" value="Add Company" onClick="AddRow()"></div></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script>
			function resize()
			{
				var height = window.innerHeight - 2;
				document.getElementById("wrap").style.height = height + "px";
			}
			
			resize();
			
			window.onresize = function() {
				resize();
			};
			
			function EditRow(num)
			{
				document.getElementById("EditButton"+num).style.display="none";
				document.getElementById("SaveButton"+num).style.display="inline-block";
				
				var id = document.getElementById("IDRow"+num);
				var name = document.getElementById("NameRow"+num);
				var email = document.getElementById("EmailRow"+num);
				var phone = document.getElementById("PhoneRow"+num);
				var address = document.getElementById("AddressRow"+num);
				
				var idData = id.innerHTML;
				var nameData = name.innerHTML;
				var emailData = email.innerHTML;
				var phoneData = phone.innerHTML;
				var addressData = address.innerHTML;
				
				id.innerHTML = "<input type='text' id='IDText"+num+"' value='"+idData+"'>";
				name.innerHTML = "<input type='text' id='NameText"+num+"' value='"+nameData+"'>";
				email.innerHTML = "<input type='text' id='EmailText"+num+"' value='"+emailData+"'>";
				phone.innerHTML = "<input type='text' id='PhoneText"+num+"' value='"+phoneData+"'>";
				address.innerHTML = "<input type='text' id='AddressText"+num+"' value='"+addressData+"'>";
			}
			
			function SaveRow(num)
			{	
				var idVal = document.getElementById("IDText"+num).value;
				var nameVal = document.getElementById("NameText"+num).value;
				var emailVal = document.getElementById("EmailText"+num).value;
				var phoneVal = document.getElementById("PhoneText"+num).value;
				var addressVal = document.getElementById("AddressText"+num).value;

				document.getElementById("IDRow"+num).innerHTML=idVal;
				document.getElementById("NameRow"+num).innerHTML=nameVal;
				document.getElementById("EmailRow"+num).innerHTML=emailVal;
				document.getElementById("PhoneRow"+num).innerHTML=phoneVal;
				document.getElementById("AddressRow"+num).innerHTML=addressVal;

				
				
				document.getElementById("EditButton"+num).style.display="inline-block";
				document.getElementById("SaveButton"+num).style.display="none";
			}
			
			function DeleteRow(num)
			{
				document.getElementById("Row"+num+"").outerHTML="";
			}
			
			function AddRow()
			{
				var newID = document.getElementById("NewID").value;
				var newName = document.getElementById("NewName").value;
				var newEmail = document.getElementById("NewEmail").value;
				var newPhone = document.getElementById("NewPhone").value;
				var newAddress = document.getElementById("NewAddress").value;
				
				var table=document.getElementById("dataTable");
				var tableLen = (table.rows.length)-1;
				var tableIndex = (table.rows.length)-3;
				var row = table.insertRow(tableLen).outerHTML=""+
					"<tr id='Row"+tableIndex+"'>"+
						"<td><a href="survey2"><input type='button' class='btn' id='SelectButton"+tableIndex+"' value='Select'></td>"+
						"<td id='IDRow"+tableIndex+"'>"+newID+"</td>"+
						"<td id='NameRow"+tableIndex+"'>"+newName+"</td>"+
						"<td id='EmailRow"+tableIndex+"'>"+newEmail+"</td>"+
						"<td id='PhoneRow"+tableIndex+"'>"+newPhone+"</td>"+
						"<td id='AddressRow"+tableIndex+"'>"+newAddress+"</td>"+
						"<td><input type='button' class='btn2' id='EditButton"+tableIndex+"' value='Edit' style='display:inline-block;' onClick='EditRow("+tableIndex+");'><input type='button' class='btn2' id='SaveButton"+tableIndex+"' value='Save' style='display:none;' onClick='SaveRow("+tableIndex+");'><input type='button' class='btn2' id='DeleteButton"+tableIndex+"' value='Delete' style='display:inline-block;' onClick='DeleteRow("+tableIndex+");'></td>"+
					"</tr>";
				document.getElementById("NewID").value = "";
				document.getElementById("NewName").value = "";
				document.getElementById("NewEmail").value = "";
				document.getElementById("NewPhone").value = "";
				document.getElementById("NewAddress").value = "";
			}

		</script>

	</body>
</html>