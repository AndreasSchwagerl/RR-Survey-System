<?php

/*

DELETE.PHP

Deletes a specific entry from the 'employee' table

*/



// connect to the database

include('connect-db.php');



// check if the 'id' variable is set in URL, and check that it is valid

if (isset($_GET['ID']) && is_numeric($_GET['ID']))

{

// get id value

$ID = $_GET['ID'];



// delete the entry

$result = mysql_query("DELETE FROM company WHERE ID=$ID")

or die(mysql_error());



// redirect back to the view page

header("Location: manageSurvey.php");

}

else

// if id isn't set, or isn't valid, redirect back to view page

{

header("Location: manageSurvey.php");

}



?>