<?php





//include the file that loads the PhpSpreadsheet classes
require '/vendor/autoload.php';

//include the classes needed to create and write .xlsx file
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



//Connect to DB
$servername = "rrsurvey";
$username = "rrsurvey";
$password = "450512Rg!";
$dbname = "rrsurvey_db";

$surveyID=$_GET["ID"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Define Question array
$sql = "";
$result = $conn->query($sql);

$leftQuestions=array();
$rightQuestions=array();
$responseRow=array();

if ($result->num_rows > 0) {
    // grab questions to add to cells
    while($row = $result->fetch_assoc()) {
        array_push($leftQuestions, $row["QL"]);
        array_push($rightQuestions, $row["QR"]);
    }


//object of the Spreadsheet class to create the excel data
$spreadsheet = new Spreadsheet();

//edit data in excel cells
$spreadsheet->setActiveSheetIndex(0);
//TODO: Fill Cells with response data


//Save excel file
$writer = new Xlsx($spreadsheet);
$fxls ='excel-file_1.xlsx';
$writer->save($fxls);

?>