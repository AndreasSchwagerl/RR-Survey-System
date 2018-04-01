<?php session_start(); ?>

<html>
	<head>
		<style>
			#navbar {
				width:100%;
			}
			
			#navbar #holder{
				
				height:80px;
				border-bottom:1px solid #000;
				width:100%;
				padding-left:1px;
			}
			
			#navbar #holder ul {
				
				list-style:none;
				margin:0;
				padding:0;
			}
			
			#navbar #holder ul li a{
				text-decoration:none;
				float:left;
				margin-right:5px;
				font-family:"Arial Black",sans-serif;
				color:#FFFFFF;
				border:1px solid #000;
				border-bottom:none;
				padding:20px;
				width:200px;
				text-align:center;
				display:block;
				background:#000;
				-webkit-border-top-right-radius:15px;
				-webkit-border-top-left-radius:15px;
				-moz-border-radius-topleft:15px;
				-moz-border-radius-topright:15px;
				
			}
			
			#navbar #holder ul li a:hover {
				
				background:#646363;
				color:#FFF;
				
			}
			
			#holder ul li a#onlink{
				background:#FFF;
				color:#000;
				border-bottom:1px solid #FFF;
			}
			
			#holder ul li a#onlink:hover{
				
				background:#000;
				color:#FF0000;
				
			}
			
			h1.heading  {
				margin-top: 0;
				margin-bottom: 0;
			}
			
			h2.heading  {
				margin-top: 0;
				margin-bottom: 0;
			}
			
			table {
				border-spacing: 0;
				border-left: 1px solid #000;
				width: 100%;
			}
			
			th {
				background-color: black;
				border-bottom: 1px solid #FFF;
				color: #FFF;
				padding: 10px 25px;
			}
			
			th:not(:last-child) {
				border-right: 1px solid #FFF;
			}
			
			.clickable {
				cursor: pointer;
			}
			
			#wrap {
				float: left;
				overflow: auto;
				height: 100%;
				width: 100%
			}
			
			.btn {
				font-size: 1em;
				height: 25px;
				width: 100%;
			}
			
			.btn2 {
				float: left;
				font-size: 1em;
				height: 25px;
				width: 48%;
			}
			
			.btn3 {
				float: right;
				font-size: 1em;
				height: 25px;
				width: 48%;
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
			
			tr:nth-child(even) {
				background-color: #eee;
			}
			
			tr:nth-child(odd) {
			   background-color:#fff;
			}
			
			.btnSelect {
				width: 100px;
			}
			
			.btnGroup {
				width: 200px;
			}
		</style>
	</head>
	
	<body>
		<?php
			if (empty($_SESSION['login'])) {
				require 'login.php';
				exit;
			}
		?>
		
		<div id ="navbar">
			<div id ="holder" style="height: 65px">
				<ul>
				<li><a href="admin.php?state=company">Manage Surveys</a></li>
				<li><a href="admin.php?state=new_survey">Create New Survey</a></li>
				<li><a href="admin.php?state=question">Manage Questions</a></li>
			</div>
		</div>
		
		<table>
			<thead>
				<tr>
					<th><h1 class="heading">Survey Control Panel</h1></th>
				</tr>
			</thead>
		</table>
		
		<?php
			// Get state from hyperlink
			$state = $_GET["state"];
			
			if ($state == "company") {
				require 'admincompany.php';
			} else if ($state == "survey") {
				require 'adminsurvey.php';
			} else if ($state == "department") {
				require 'admindepartment.php';
			} else if ($state == "participant") {
				require 'adminparticipant.php';
			} else if ($state == "question") {
				require 'adminquestion.php';
			} else if ($state == "new_survey") {
				require 'createsurvey.php';
			} else {
				echo '<script type="text/javascript">window.location = "admin.php?state=company"</script>';
			}
		?>
	</body>
</html>