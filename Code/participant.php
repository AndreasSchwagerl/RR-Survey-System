<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Untitled 1</title>
<link rel="stylesheet" href="http://localhost/Survey/tabMenu.css"/>

</head>

<body>
<div id ="navbar">
<div id ="holder" style="height: 64px">

<ul>
<li><a href="http://localhost/Survey/manageSurvey.php" id="onlink">Manage Surveys</a></li>
<li><a href="http://localhost/Survey/createNewSurvey.php" >Create New Survey</a></li>
<li><a href="http://localhost/Survey/manageQuestions.php" >Manage Questions</a></li>
</ul>

</div>
</div>


<?php




// connect to the database

include('connect-db.php');



// get results from database

$result = mysql_query("SELECT * FROM participant")

or die(mysql_error());



// display data in table




echo "<table border='1' cellpadding='10'>";

echo "<tr> <th>ID</th> <th>Email</th> <th>Submitted</th> <th></th> <th></th><th></th><th></th></tr>";



// loop through results of database query, displaying them in the table

while($row = mysql_fetch_array( $result )) {



// echo out the contents of each row into a table

echo "<tr>";



echo '<td>' . $row['ID'] . '</td>';

echo '<td>' . $row['Email'] . '</td>';

echo '<td>' . $row['Submitted'] . '</td>';


echo '<td><a href="edit.php?ID=' . $row['ID'] . '"><input type="submit" name="submit" value="Edit"></a></td>';

echo '<td><a href="delete.php?ID=' . $row['ID'] . '"><input type="submit" name="submit" value="Delete"></a></td>';

echo '<td><a href="MoveParticipant.php?ID=' . $row['ID'] . '"><input type="submit" name="submit" value="Move Participant"></a></td>';

echo '<td><a href="ReminderEmails.php?ID=' . $row['ID'] . '"><input type="submit" name="submit" value="Send Reminder Emails"></a></td>';

echo "</tr>";

}



// close table>

echo "</table>";

?>
</body>
</html>
