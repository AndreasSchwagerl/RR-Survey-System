<!DOCTYPE html>
<html>
	<head>
		<style>
			.column {
				float: left;
				width: 50%;
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
		</style>
	</head>
	
	<?php
		// Get database connection information
		$dbhost = 'localhost';
		$dbconnect = parse_ini_file('../connect.ini');
		
		// Initiate mysqli connection
		$mysqli = new mysqli($dbhost, $dbconnect['username'], $dbconnect['password'], $dbconnect['dbname']);
		
		// Check for a connection error and handle if necessary
		if ($mysqli->connect_error) {
			exit('There was an error connecting to the database.');
		}
	?>
	
	<body>
		<div class="row">
			<div class="column">
            	<fieldset>
				<legend>Company</legend>
                	<select id="" size="1" style="min-width:100%;">
                    	<?php
							// Get the survey questions
							$result = mysqli_query($mysqli, "SELECT c.Name FROM Company c ORDER BY c.Name ASC");
							$rowNum = mysqli_num_rows($result);
											
							// Loop through each question, generating table rows
							while($row = mysqli_fetch_array($result)) {
						?>
							<option value="<?php echo $row['Name']?>"><?php echo $row['Name']?></option>
						<?php
							}
						?>
                    </select>
                </fieldset>
                
				<fieldset>
				<legend>Departments</legend>
					<select id="deptList" onchange="DepartmentChange()" size="5" style="min-width:100%;"></select>
                    <br>
                    <br>
                    Department Name:
                    <input type="text" id="txtDeptName" style="width:98%">
                    <br>
                    <br>
                    Participant Emails:
                    <textarea rows="8" id="txtPartEmail" style="width:98%; resize:none;"></textarea>
                    <br>
                    <br>
                    <input type="button" id="btnUpdateDept" value="Update" onclick="UpdateDepartment()" style="width:47%"> <input type="button" id="btnRemoveDept" value="Remove" onclick="RemoveDepartment()" style="width:47%">
				</fieldset>
			</div>
			<div class="column">
				<fieldset>
				<legend>Add Department</legend>
                    Department Name:
                    <input type="text" id="txtAddDeptName" style="width:99%">
                    <br>
                    <br>
                    Participant Emails (Comma Delimited):
                    <textarea rows="8" id="txtAddPartEmail" style="width:98%; resize:none;"></textarea>
                    <br>
                    <br>
                    <input type="button" id="btnAddDept" value="Add Department" onclick="AddDepartment()" style="width:100%">
				</fieldset>
			</div>
		</div>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script>
        	var deptArr = [];
            var selector = document.getElementById('deptList');
			
        	function AddDepartment() {
            	var dept = {
					deptName: document.getElementById("txtAddDeptName").value,
					partEmail: document.getElementById("txtAddPartEmail").value
				};
				
				deptArr.push(dept);
                
				var opt = document.createElement('option');
				opt.text = document.getElementById('txtAddDeptName').value;
				selector.add(opt);
            }
            
            function DepartmentChange() {
            	document.getElementById('txtDeptName').value = deptArr[document.getElementById('deptList').selectedIndex].deptName;
                document.getElementById('txtPartEmail').value = deptArr[document.getElementById('deptList').selectedIndex].partEmail;
            }
            
            function UpdateDepartment() {
            	var deptName = document.getElementById('txtDeptName').value;
                var partEmail = document.getElementById('txtPartEmail').value;
                var index = document.getElementById('deptList').selectedIndex;
            	
            	document.getElementById('deptList').options[index].text = deptName;
				deptArr[index].deptName = deptName;
                deptArr[index].partEmail = partEmail;
            }
            
            function RemoveDepartment() {
				var deptList = document.getElementById('deptList');
                var index = deptList.selectedIndex;
                
				deptArr.splice(index, 1);
                deptList.options.remove(index);
                
				if (index > 0) {
					deptList.selectedIndex = index-1;
					deptList.onchange();
				} else if (index == 0 && deptList.length > 0) {
					deptList.selectedIndex = index;
					deptList.onchange();
				} else {
					document.getElementById('txtDeptName').value = "";
					document.getElementById('txtPartEmail').value = "";
				}
			}
        </script>
	</body>
</html>