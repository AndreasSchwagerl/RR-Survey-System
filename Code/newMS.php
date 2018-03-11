<?php

/*

NEW.PHP

Allows user to create a new entry in the database

*/



// creates the new record form

// since this form is used multiple times in this file, I have made it a function that is easily reusable

function renderForm($ID, $Name, $Address, $Phone, $Email,$error)

{

?>



<html>

<head>

<title>New Record</title>

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

<div>

<strong>ID: *</strong> <input type="text" name="ID" value="<?php echo $ID; ?>" /><br/>

<strong>Name: *</strong> <input type="text" name="Name" value="<?php echo $Name; ?>" /><br/>

<strong>Address: *</strong> <input type="text" name="Address" value="<?php echo $Address; ?>" /><br/>

<strong>Phone: *</strong> <input type="text" name="Phone" value="<?php echo $Phone; ?>" /><br/>

<strong>Email: *</strong> <input type="text" name="Email" value="<?php echo $Email; ?>" /><br/>



<p>* required</p>

<input type="submit" name="submit" value="Submit">

</div>

</form>

</body>

</html>

<?php

}









// connect to the database

include('connect-db.php');



// check if the form has been submitted. If it has, start to process the form and save it to the database

if (isset($_POST['submit']))

{

// get form data, making sure it is valid

$ID = mysql_real_escape_string(htmlspecialchars($_POST['ID']));

$Name = mysql_real_escape_string(htmlspecialchars($_POST['Name']));

$Address = mysql_real_escape_string(htmlspecialchars($_POST['Address']));

$Phone = mysql_real_escape_string(htmlspecialchars($_POST['Phone']));

$Email = mysql_real_escape_string(htmlspecialchars($_POST['Email']));





// check to make sure both fields are entered

if ($ID == '' || $Name == ''|| $Address == ''|| $Phone == ''|| $Email == '')

{

// generate error message

$error = 'ERROR: Please fill in all required fields!';



// if either field is blank, display the form again

renderForm($ID, $Name, $Address, $Phone, $Email,$error);

}

else

{

// save the data to the database

mysql_query("INSERT company SET ID='$ID', Name='$Name',Address='$Address', Phone='$Phone', Email='$Email'")

or die(mysql_error());



// once saved, redirect back to the view page

header("Location: manageSurvey.php");

}

}

else

// if the form hasn't been submitted, display the form

{

renderForm('','','','','','','','');

}

?>