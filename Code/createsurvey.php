<!DOCTYPE html>
<html>
	<head>
		<style>
			.container {
            	margin: 0 auto;
            	max-width: 1200px;
            }
            
            .button {
                display: table-cell;
            }
            
            .maincolumn {
				float: left;
				width: 50%;
			}
            
            .leftcolumn {
            	float: left;
				width: 45%;
            }
            
            .rightcolumn {
            	float: right;
				width: 45%;
            }
            
            .buttoncolumn {
            	float: left;
				width: 10%
            }
            
            .table{
            	width: 100%;
    			display: table;
    		}
			
			fieldset {
				margin: 8px;
				border: 1px solid silver;
				padding: 8px;    
				border-radius: 4px;
			}
			
			legend {
				padding: 2px;
			}
			
			::-webkit-clear-button {
				display: none;
				-webkit-appearance: none;
			}
		</style>
	</head>
	
	<script>
		// This JavaScript function is used in the PHP code.
		
		var questArr1 = [];
		var questArr2 = [];
		
		function AddToQuestArr(id, ql, qr) {
			// Store the question data in a named index array.
			var quest = {
				ID: id,
				QL: ql,
				QR: qr
			};
				
            // Add the named index array to the question array.
			questArr1.push(quest);
		}
	</script>
	
	<?php
		// Get database connection information
		$dbhost = 'localhost';
		$dbconnect = parse_ini_file('connect.ini');
		
		// Initiate mysqli connection
		$mysqli = new mysqli($dbhost, $dbconnect['username'], $dbconnect['password'], $dbconnect['dbname']);
		
		// Check for a connection error and handle if necessary
		if ($mysqli->connect_error) {
			exit('There was an error connecting to the database.');
		}
	?>
	
	<body>
		<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
			<?php 
				$comp = $_POST['comp'];
				echo $comp;
			?>
			
			<form align="center">
				<h1>The survey has been generated</h1>
			</form>
		<?php else : ?>
			<form action="" onsubmit="return ValidateForm()" method = "post">
				<table>
					<thead>
						<tr>
							<th><h2 class="heading">Create New Survey</h1></th>
						</tr>
					</thead>
				</table>
				<br>
				<div class="container">
					<div class="maincolumn">
						<fieldset>
						<legend>Step 1: Select Company</legend>
							<select id="ddlCompany" size="1" style="min-width:100%;">
								<option value="0">Select a Company</option>
								
								<?php
									// Get the survey questions
									$result = mysqli_query($mysqli, "SELECT c.ID, c.Name FROM Company c ORDER BY c.Name ASC");
									$rowNum = mysqli_num_rows($result);
													
									// Loop through each question, generating table rows
									while($row = mysqli_fetch_array($result)) {
								?>
										<option value="<?php echo $row['ID']?>"><?php echo $row['Name']?></option>
								<?php
									}
								?>
							</select>
						</fieldset>
						
						<fieldset>
						<legend>Step 2: Select Date Range</legend>
							Start Date:
							<input type="date" id="dtpStart" style="width:98%" value="<?php echo date('Y-m-d');?>" min="<?php echo date('Y-m-d');?>">
							<br><br>
							End Date:
							<input type="date" id="dtpEnd" style="width:98%" value="<?php echo date('Y-m-d', strtotime('tomorrow'));?>" min="<?php echo date('Y-m-d', strtotime('tomorrow'));?>">
						</fieldset>
						
						<fieldset>
						<legend>Step 3: Add Departments & Emails</legend>
							<select id="lstDept" onchange="ChangeDepartment()" size="5" style="min-width:100%" disabled></select>
							<br><br>
							Department Name:
							<input type="text" id="txtDeptName" oninput="UpdateDepartment()" style="width:98%" disabled>
							<br><br>
							Participant Emails (Comma Delimited):
							<textarea rows="5" id="txtPartEmail" oninput="UpdatePartEmail()" style="width:98%; resize:none;" disabled></textarea>
							<br><br>
							<div class="table">
								<div class="button">
									<input type="button" id="btnNew" value="New Entry" onclick="NewDepartment()" style="width:95%; height:30px;">
									<input type="button" id="btnAdd" value="Add Entry" onclick="AddDepartment()" style="width:95%; height:30px; display:none;">
								</div>
								<div class="button">
									<input type="button" id="btnFinish" value="Finish" onclick="FinishNewDepartment()" style="width:95%; height:30px; display:none;">
								</div>
								<div class="button">
									<input type="button" id="btnRemove" value="Remove" onclick="RemoveDepartment()" style="width:95%; height:30px;" disabled>
								</div>
							</div>
						</fieldset>
					</div>
					<div class="maincolumn">
						<fieldset>
						<legend>Step 4: Select Questions</legend>
							<div class="container">
								<div class="leftcolumn">
									<select id="lstQuest1" size="16" style="min-width:95%" onchange="QuestionDataBinding(1)">
										<?php
											// Get the survey questions
											$result = mysqli_query($mysqli, "SELECT * FROM Question ORDER BY ID ASC");
											$rowNum = mysqli_num_rows($result);
															
											// Loop through each question, generating table rows
											while($row = mysqli_fetch_array($result)) {
										?>
												<option value="<?php echo $row['ID']?>">Question <?php echo $row['ID']?></option>
										<?php
												echo "<script> AddToQuestArr(" .$row['ID']. ", '" .str_replace('\'', '\\\'', $row['QL']). "', '" .str_replace('\'', '\\\'', $row['QR']). "'); </script>";
											}
										?>
									</select>
								</div>
								<div class="buttoncolumn">
									<input type="button" id="btnMoveUp" value="ʌ" style="width:80%; margin-right:5px; margin-bottom:5px; height:38px;" onclick="MoveUD('lstQuest2', 'up')"><br>
									<input type="button" id="btnMoveOver" value=">" style="width:80%; margin-right:5px; margin-bottom:5px; height:38px;" onclick="MoveLR('lstQuest1', 'lstQuest2', 'single', 1)"><br>
									<input type="button" id="btnMoveOverAll" value="≫" style="width:80%; margin-right:5px; margin-bottom:5px; height:38px;" onclick="MoveLR('lstQuest1', 'lstQuest2', 'all', 1)"><br>
									<input type="button" id="btnMoveBackAll" value="≪" style="width:80%; margin-right:5px; margin-bottom:5px; height:38px;" onclick="MoveLR('lstQuest2', 'lstQuest1', 'all', 2)"><br>
									<input type="button" id="btnMoveBack" value="<" style="width:80%; margin-right:5px; margin-bottom:5px; height:38px;" onclick="MoveLR('lstQuest2', 'lstQuest1', 'single', 2)"><br>
									<input type="button" id="btnMoveDown" value="v" style="width:80%; margin-right:5px; margin-bottom:5px; height:38px;" onclick="MoveUD('lstQuest2', 'down')">
								</div>
								<div class="rightcolumn">
									<select id="lstQuest2" size="16" style="min-width:100%" onchange="QuestionDataBinding(2)">
										
									</select>
								</div>
							</div>
							<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
							<div class="leftcolumn">
								Left Statement: <textarea rows="4" id="txtLeft1" style="width:95%; resize:none;" readonly></textarea><br>
								Right Statement: <textarea rows="4" id="txtRight1" style="width:95%; resize:none;" readonly></textarea>
							</div>
							<div class="buttoncolumn">
							
							</div>
							<div class="rightcolumn">
								Left Statement: <textarea rows="4" id="txtLeft2" style="width:95%; resize:none;" readonly></textarea><br>
								Right Statement: <textarea rows="4" id="txtRight2" style="width:95%; resize:none;" readonly></textarea>
							</div>
						</fieldset>
						<input type="submit" id="btnSubmit" value="Submit" style="width:94%; height:40px; margin-left:15px; margin-top:5px;">
					</div>
				</div>
			</form>
		<?php endif; ?>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script>
        	var deptArr = [];
			
            function NewDepartment() {
            	// Disable oninput functionality on textboxes.
                document.getElementById('txtDeptName').oninput = "";
                document.getElementById('txtPartEmail').oninput = "";
            
            	// Ensure that the listbox is disabled.
                document.getElementById('lstDept').disabled = true;
                
            	// Ensure that textbox controls are enabled.
            	document.getElementById('txtDeptName').disabled = false;
                document.getElementById('txtPartEmail').disabled = false;
                
                // Clear the listbox selection.
                document.getElementById('lstDept').value = "";
                
                // Clear the textboxes.
                document.getElementById('txtDeptName').value = "";
                document.getElementById('txtPartEmail').value = "";
                
                // Hide the management controls.
                document.getElementById('btnNew').style.display = "none";
                document.getElementById('btnRemove').style.display = "none";
                
                // Show the new entry controls.
                document.getElementById('btnAdd').style.display = "inline";
                document.getElementById('btnFinish').style.display = "inline";
            }
            
        	function AddDepartment() {
                var txtDeptName = document.getElementById("txtDeptName");
                var txtPartEmail = document.getElementById("txtPartEmail");
                
                // Ensure that the textboxes are not empty.
                if (txtDeptName.value.trim() == "" && txtPartEmail.value.trim() == "") {
                	alert("You must provide a department name and participant emails.");
                    return;
                }
                
                // Store the user entered data in a named index array.
                var dept = {
					deptName: txtDeptName.value,
					partEmail: txtPartEmail.value
				};
				
                // Add the named index array to the department array.
				deptArr.push(dept);
                
                // Create an option and add it to the listbox.
				var opt = document.createElement('option');
				opt.text = txtDeptName.value;
				document.getElementById('lstDept').add(opt);
                
                // Clear the textboxes.
                txtDeptName.value = "";
				txtPartEmail.value = "";
            }
            
            function FinishNewDepartment() {
                // Re-enable oninput functionality on textboxes.
                document.getElementById('txtDeptName').oninput = function(){UpdateDepartment()};
                document.getElementById('txtPartEmail').oninput = function(){UpdatePartEmail()};
                
                if (lstDept.length > 0) {
                	// Enable management controls.
                	document.getElementById('lstDept').disabled = false;
                    document.getElementById('btnRemove').disabled = false;
                    
                    // Select the first listbox entry.
                    lstDept.selectedIndex = 0;
                    document.getElementById('lstDept').onchange();
                } else {
                	// Disable and empty textbox controls.
                	document.getElementById('txtDeptName').disabled = true;
                	document.getElementById('txtPartEmail').disabled = true;
                    
                    document.getElementById('txtDeptName').value = "";
                	document.getElementById('txtPartEmail').value = "";
                }
                
                // Hide the new entry controls.
                document.getElementById('btnAdd').style.display = "none";
                document.getElementById('btnFinish').style.display = "none";
                
                // Show management controls.
                document.getElementById('btnNew').style.display = "inline";
                document.getElementById('btnRemove').style.display = "inline";
            }
            
            function ChangeDepartment() {
            	var index = document.getElementById('lstDept').selectedIndex;
                
                // Set textbox values to corresponding array value.
                document.getElementById('txtDeptName').value = deptArr[index].deptName;
                document.getElementById('txtPartEmail').value = deptArr[index].partEmail;
            }
            
            function UpdateDepartment() {
            	var deptName = document.getElementById('txtDeptName').value;
                var index = document.getElementById('lstDept').selectedIndex;
                
                // Change value in listbox and array.
            	document.getElementById('lstDept').options[index].text = deptName;
				deptArr[index].deptName = deptName;
            }
            
            function UpdatePartEmail() {
                var partEmail = document.getElementById('txtPartEmail').value;
                var index = document.getElementById('lstDept').selectedIndex;
                
                // Change value in array.
				deptArr[index].partEmail = partEmail;
            }
            
            function RemoveDepartment() {
				var lstDept = document.getElementById('lstDept');
                var index = lstDept.selectedIndex;
                
                // Remove entry from listbox and array.
				deptArr.splice(index, 1);
                lstDept.options.remove(index);
                
				if ((index > 0 && index != lstDept.length) || (index == 0 && lstDept.length > 0)) {
                	// Select the first listbox entry.
					lstDept.selectedIndex = index;
					lstDept.onchange();
				} else if (index > 0 && index == lstDept.length) {
					lstDept.selectedIndex = index - 1;
					lstDept.onchange();
				} else {
                	// Clear the textboxes.
					document.getElementById('txtDeptName').value = "";
					document.getElementById('txtPartEmail').value = "";
                    
                    // Disable management controls.
                    document.getElementById('lstDept').disabled = true;
                    document.getElementById('txtDeptName').disabled = true;
					document.getElementById('txtPartEmail').disabled = true;
                	document.getElementById('btnRemove').disabled = true;
				}
			}
			
			function MoveUD(lstID, direction) {
				var lst = document.getElementById(lstID);
				var index = lst.selectedIndex;
				
				// If there is no selected index, exit the function.
				if (index == -1)
					return;
				
				var increment;
				
				// If direction is up, increment by -1.
				// If direction is down, increment by 1.
				// If direction is neither, exit the function.
				if (direction == 'up')
					increment = -1;
				else if (direction == 'down')
					increment = 1;
				else
					return;
				
				// If the first or last index is selected, exit the function.
				if (index + increment < 0 || index + increment > lst.options.length - 1)
					return;
				
				// Get temporary values;
				var quest = questArr2[index];
				var value = lst.options[index].value;
				var text = lst.options[index].text;
				
				// Move the destination values to the current index;
				questArr2[index] = questArr2[index + increment];
				lst.options[index].value = lst.options[index + increment].value;
				lst.options[index].text = lst.options[index + increment].text;
				
				// Move the current index values to the destination;
				questArr2[index + increment] = quest;
				lst.options[index + increment].value = value;
				lst.options[index + increment].text = text;
				
				// Select the destination index;
				lst.selectedIndex = index + increment;
				lst.onchange();
			}
			
			function MoveLR(srcID, destID, amt, srcArrNum) {
				var src = document.getElementById(srcID);
				var dest = document.getElementById(destID);
				var index = src.selectedIndex;
				
				// If there is no selected index, exit the function.
				if (index == -1)
					return;
				
				var srcArr, destArr;
				
				// From the source array number, set the source and destination array.
				if (srcArrNum == 1) {
					srcArr = questArr1;
					destArr = questArr2;
				} else if (srcArrNum == 2) {
					srcArr = questArr2;
					destArr = questArr1;
				} else
					return;
				
				// If amount is 'single', transfer one option.
				// If amount is 'all', transfer all options.
				if (amt == 'single')
					TransferOption(src, dest, srcArr, destArr, index)
				else if (amt == 'all') {
					while (src.length > 0)
						TransferOption(src, dest, srcArr, destArr, 0);
				} else
					return;
				
				// Sort the questions in the first listbox and array.
				SortQuestions();
				
				// Set the selected index on the source list.
				if (index == src.length)
					src.selectedIndex = index - 1;
				else if (src.length > 0)
					src.selectedIndex = index;
				
				// Populate text boxes.
				QuestionDataBinding(1);
				QuestionDataBinding(2);
			}
			
			function TransferOption(src, dest, srcArr, destArr, index) {
				// Transfer from source array to destination array.
				destArr.push(srcArr[index]);
				srcArr.splice(index, 1);
				
				// Transfer from source list to destination list.
				dest.options.add(src.options[index]);
			}
			
			function SortQuestions() {
				var selectList = $('#lstQuest1 option');
				
				// Sort the first listbox.
				selectList.sort(function(a,b){
					a = a.value;
					b = b.value;
					
					return a - b;
				});
				
				// Apply changes to the listbox.
				$('#lstQuest1').html(selectList);
				
				// Sort the first array.
				questArr1.sort((a, b) => parseFloat(a.ID) - parseFloat(b.ID));
			}
			
			function QuestionDataBinding(num) {
				var arr, lst = document.getElementById('lstQuest' + num);
				var index = lst.selectedIndex;
				
				// If the listbox is empty, clear the textboxes and exit the function.
				// If the listbox is not empty, but the index is -1, select index 0.
				if (lst.length == 0) {
					document.getElementById('txtLeft' + num).value = "";
					document.getElementById('txtRight' + num).value = "";
					return;
				} else if (index == -1) {
					lst.selectedIndex = 0;
					index = 0;
				}
				
				// Create reference to appropriate array.
				if (num == 1)
					arr = questArr1;
				else if (num == 2)
					arr = questArr2;
                
                // Set textbox values to corresponding array value.
                document.getElementById('txtLeft' + num).value = arr[index].QL;
                document.getElementById('txtRight' + num).value = arr[index].QR;
			}
			
			// Function is run on page load to select index 0 (if available) in the first listbox.
			QuestionDataBinding(1);
			
			function ValidateForm() {
				var comp = document.getElementById('ddlCompany'),
					startDate = document.getElementById('dtpStart').value,
					endDate = document.getElementById('dtpEnd').value,
					start = new Date(startDate),
					end = new Date(endDate),
					dept = document.getElementById('lstDept');
				
				// If a company has not been selected, prompt for company.
				// If the start date is after the end date, prompt for correction.
				// If no departments have been added, prompt for departments.
				if (comp.value == '0') {
					alert("Please select a company.");
					return false;
				} else if (start.valueOf() >= end.valueOf()) {
					alert("The start date must come before the end date.");
					return false;
				} else if (dept.length == 0) {
					alert("Please add one or more departments.");
					return false;
				}
				
				// Loop through the department array.
				for (i = 0; i < deptArr.length; i++) {
					
					// If the department name is blank, prompt for a department name.
					// If the email list is blank, prompt for emails.
					// If the email list contains invalid text, inform the user.
					if (deptArr[i].deptName.trim() == "") {
						alert("Blank department names are not permitted.");
						return false;
					} else if (deptArr[i].partEmail.trim() == "") {
						alert("The " + deptArr[i].deptName + " department has a blank email list.");
						return false;
					} else if (!ValidateEmails(deptArr[i].partEmail)) {
						alert("There is an error in the " + deptArr[i].deptName + " department's email list.");
						return false;
					}
				}
				
				var quest = document.getElementById('lstQuest2');
				
				// If a question has not been selected, prompt for questions.
				if (quest.length == 0) {
					alert("Please select one or more questions.");
					return false;
				}
				
				// Submit the survey to the database and generate emails.
				Submit();
				
				// If form passes all criteria, return true.
				return true;
			}
			
			function ValidateEmails(str) {
				var valid = true;
				var emails = str.split(','); // Split string by comma and insert into array.
				var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				
				// Loop through the email array.
				for (var i = 0; i < emails.length; i++) {
					
					// If email is blank or fails regex check, return false.
					if (emails[i] === "" || !regex.test(emails[i].replace(/\s/g, ""))) {
						valid = false;
					}
				}
				
				// If all emails are valid, return true.
				return valid;
			}
			
			function Submit() {
				var CID = document.getElementById('ddlCompany').value;
				var start = document.getElementById('dtpStart').value;
				var end = document.getElementById('dtpEnd').value;
				
				var dArr = [];
				var eArr = [];
				var qArr = [];
				
				for (i = 0; i < deptArr.length; i++) {
					dArr.push(deptArr[i].deptName);
					eArr.push(deptArr[i].partEmail);
				}
				
				for (i = 0; i < questArr2.length; i++) {
					qArr.push(questArr2[i].ID);
				}
				
				$.ajax({
					type: "POST",
					url: "submitsurvey.php",
					data: 	{
								CID: CID,
								start: start,
								end: end,
								dArr: dArr,
								eArr: eArr,
								qArr: qArr
							},
					success: function() {
						return true;
					}
				});
			}
        </script>
	</body>
</html>