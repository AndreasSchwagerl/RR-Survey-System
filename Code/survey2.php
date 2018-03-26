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
	

</head>

<head>	
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


	<?php

		include('connect-db.php');

		// get results from database

		$result = mysql_query("SELECT * FROM survey")

		or die(mysql_error());
?>
	
	<body>
		<div id="wrap">
			<table id="dataTable">
				<colgroup>
					<col style="width:150px">
					<col style="width:15%">
					<col style="width:35%">
					<col style="width:35%">
					<col style="width:25%">
					<col style="width:200px">
					<col style="width:200px">
					<col style="width:200px">
				</colgroup>
				
				<thead>
					<tr>
						<th colspan="8"><h1 class="heading">Survey Control Panel</h1></th>
					</tr>
					
					<tr>
						<th colspan="8"><h2 class="heading">Survey</h2></th>
					</tr>
						
					<tr>
						<th>&nbsp;</th>
						<th>ID</th>
						<th>StartDate</th>
						<th>EndDate</th>
						<th>Completion</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
					
				<tbody>
					<?php
						$results = mysql_query("SELECT * FROM survey");
						while($row = mysql_fetch_array($results)) {
							$rowNum = $row['ID'];
					?>
						
					<tr id="Row<?php echo $row['ID']; ?>">
						<td><a href="department2"><input type="button" class="btn" id="SelectButton<?php echo $row['ID']; ?>" value="Select"></td>
						<td id="IDRow<?php echo $row['ID']; ?>"><?php echo $row['ID']?></td>
						<td id="StartDateRow<?php echo $row['ID']; ?>"><?php echo $row['StartDate']?></td>
						<td id="EndDateRow<?php echo $row['ID']; ?>"><?php echo $row['EndDate']?></td>
						<td id="CompletionRow<?php echo $row['ID']; ?>"><?php echo $row['Completion']?></td>
						<td><a href="report.php"><input type='button' class='btn3' id='GenerateReport"+tableIndex+"' value='Generate Report' style='display:inline-block;'></td>
						<td><input type="button" class="btn2" id="EditButton<?php echo $row['ID']; ?>" value="Edit" style="display:inline-block;" onclick="EditRow(<?php echo $row['ID']; ?>)"><input type="button" class="btn2" id="SaveButton<?php echo $row['ID']; ?>" value="Save" style="display:none;" onClick="SaveRow(<?php echo $row['ID']; ?>)"><input type="button" class="btn2" id="DeleteButton<?php echo $row['ID']; ?>" value="Delete" style="display:inline-block;" onClick="DeleteRow(<?php echo $row['ID']; ?>)"></td>
						<td><input type='submit' class='btn3' id='SendReminderEmails"+tableIndex+"' value='Send Reminder Emails' style='display:inline-block;'></td>
						
				</tr>
						
					<?php
						}
					?>
						
					
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
				var sd = document.getElementById("StartDateRow"+num);
				var ed = document.getElementById("EndDateRow"+num);
				var completion = document.getElementById("CompletionRow"+num);
				
				
				var idData = id.innerHTML;
				var sdData = sd.innerHTML;
				var edData = ed.innerHTML;
				var completionData = completion.innerHTML;
				
				
				id.innerHTML = "<input type='text' id='IDText"+num+"' value='"+idData+"'>";
				sd.innerHTML = "<input type='text' id='StartDateText"+num+"' value='"+sdData+"'>";
				ed.innerHTML = "<input type='text' id='EndDateText"+num+"' value='"+edData+"'>";
				completion.innerHTML = "<input type='text' id='CompletionText"+num+"' value='"+completionData+"'>";
				
			}
			
			function SaveRow(num)
			{	
				var idVal = document.getElementById("IDText"+num).value;
				var sdVal = document.getElementById("StartDateText"+num).value;
				var edVal = document.getElementById("EndDateText"+num).value;
				var completionVal = document.getElementById("CompletionText"+num).value;
				

				document.getElementById("IDRow"+num).innerHTML=idVal;
				document.getElementById("StartDateRow"+num).innerHTML=sdVal;
				document.getElementById("EndDateRow"+num).innerHTML=edVal;
				document.getElementById("CompletionRow"+num).innerHTML=completionVal;
				
				
				document.getElementById("EditButton"+num).style.display="inline-block";
				document.getElementById("SaveButton"+num).style.display="none";
			}
			
			function DeleteRow(num)
			{
				document.getElementById("Row"+num+"").outerHTML="";
			}
			
			
		</script>
	</body>
</html>