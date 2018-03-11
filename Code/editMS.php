<?php

/*

EDIT.PHP

Allows user to edit specific entry in database

*/



// creates the edit record form

// since this form is used multiple times in this file, I have made it a function that is easily reusable

function renderForm($ID, $Name, $Address,$Phone,$Email, $error)

{

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

<title>Edit Record</title>

</head>

<body>

<?php

// if there are any errors, display them

if ($error != '')

{

echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';

}

?>



<form action="" method="post">

<input type="hidden" name="ID" value="<?php echo $ID; ?>"/>

<div>

<p><strong>ID:</strong> <?php echo $ID; ?></p>

<strong>ID: *</strong> <input type="text" name="ID" value="<?php echo $ID; ?>"/><br/>

<strong>Name: *</strong> <input type="text" name="Name" value="<?php echo $Name; ?>"/><br/>

<strong>Address: *</strong> <input type="text" name="Address" value="<?php echo $Address; ?>"/><br/>

<strong>Phone: *</strong> <input type="text" name="Phone" value="<?php echo $Phone; ?>"/><br/>

<strong>Email: *</strong> <input type="text" name="Email" value="<?php echo $Email; ?>"/><br/>
<p>* Required</p>

<input type="submit" name="submit" value="Submit">

</div>

</form>

</body>

</html>

<?php

}







// connect to the database

include('connect-db.php');



// check if the form has been submitted. If it has, process the form and save it to the database

if (isset($_POST['submit']))

{

// confirm that the 'id' value is a valid integer before getting the form data

if (is_numeric($_POST['ID']))

{

// get form data, making sure it is valid

$ID = $_POST['ID'];

$ID = mysql_real_escape_string(htmlspecialchars($_POST['ID']));

$Name = mysql_real_escape_string(htmlspecialchars($_POST['Name']));

$Address = mysql_real_escape_string(htmlspecialchars($_POST['Address']));

$Phone = mysql_real_escape_string(htmlspecialchars($_POST['Phone']));

$Email = mysql_real_escape_string(htmlspecialchars($_POST['Email']));



// check that firstname/lastname fields are both filled in

if ($ID == '' || $Name == ''|| $Address == ''|| $Phone == ''|| $Email == '')

{

// generate error message

$error = 'ERROR: Please fill in all required fields!';



//error, display form

renderForm($ID, $Name, $Address,$Phone,$Email, $error);

}

else

{

// save the data to the database

mysql_query("UPDATE company SET ID='$ID', Name='$Name',Address='$Address',Phone='$Phone',Email='$Email' WHERE ID='$ID'")

or die(mysql_error());



// once saved, redirect back to the view page

header("Location: manageSurvey.php");

}

}

else

{

// if the 'id' isn't valid, display an error

echo 'Error!';

}

}

else

// if the form hasn't been submitted, get the data from the db and display the form

{



// get the 'id' value from the URL (if it exists), making sure that it is valid (checing that it is numeric/larger than 0)

if (isset($_GET['ID']) && is_numeric($_GET['ID']) && $_GET['ID'] > 0)

{

// query db

$ID = $_GET['ID'];

$result = mysql_query("SELECT * FROM company WHERE ID=$ID")

or die(mysql_error());

$row = mysql_fetch_array($result);



// check that the 'id' matches up with a row in the databse

if($row)

{



// get data from db

$ID = $row['ID'];

$Name = $row['Name'];

$Address = $row['Address'];

$Phone = $row['Phone'];

$Email = $row['Email'];



// show form

renderForm($ID, $Name, $Address, $Phone,$Email,'');

}

else

// if no match, display result

{

echo "No results!";

}

}

else

// if the 'id' in the URL isn't valid, or if there is no 'id' value, display an error

{

echo 'Error!';

}

}

?>