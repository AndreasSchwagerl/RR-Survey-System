<?php


$SID = $_POST["ID"];

//include the file that loads the PhpSpreadsheet classes
require '/home2/rrsurvey/public_html/vendor/autoload.php';

//include the classes needed to create and write .xlsx file
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get database connection information
$dbhost = 'localhost';
$dbconnect = parse_ini_file('../connect.ini');

// Initiate mysqli connection
$mysqli = new mysqli($dbhost, $dbconnect['username'], $dbconnect['password'], $dbconnect['dbname']);

// Check for a connection error and handle if necessary
if ($mysqli->connect_error) {
    // Handle error
}

//Fetch questions from database matching $SID
$questions = mysqli_query($mysqli, "SELECT q.QL, q.QR FROM SurveyQuestion sq LEFT JOIN Question q ON sq.QID = q.ID WHERE sq.SID = '$SID'");


$responses=mysqli_query($mysqli,"SELECT d.Name, sq.Order, r.Response
FROM ((Survey s INNER JOIN Department d ON s.ID = d.SID)
INNER JOIN SurveyQuestion sq ON s.ID = sq.SID)
INNER JOIN Response r ON r.QID = sq.QID AND r.DID = d.ID
WHERE s.ID = $SID");

$questionArray = array();
$responseArray=array();
$lqArray=array();
$rqArray=array();
$count=0;


if ($questions->num_rows > 0) {
    // output data of each row
    while($row = $questions->fetch_assoc()) {
        $questionArray[] = $row;
    }
} else {
    echo "0 results";
}

if ($responses->num_rows > 0) {
    // output data of each row
    while($row = $responses->fetch_assoc()) {
        $responseArray[] = $row;
    }
} else {
    echo "0 results";
}

mysqli_close($mysqli);

$questionJSON=json_decode(json_encode($questionArray),true);
$responseJSON=json_decode(json_encode($responseArray),true);


$newArr=array();
$newArr2=array();
$lqArray=array();
$rqArray=array();

foreach($questionJSON as $data => $val) {
    foreach ($val as $key2 => $val2) {
        $newArr[] = $val2;

    }
}

foreach($responseJSON as $data => $val) {
    $strBuild="";
    foreach ($val as $key2 => $val2) {
        $strBuild = $strBuild.$val2.',';
    }
    $strBuild=substr($strBuild,0,-1);
    $newArr2[]=$strBuild;
}


$i = 0;
foreach($newArr as $key => $value){
    if($i%2 == 0){
        $lqArray[$key] = $value;
    }else{
        $rqArray[$key] = $value;
    }
    $i++;
}

$colMarker=array('B','C','D','E','F','G');

//create new spreadsheet
$spreadsheet = new Spreadsheet();

fillHeaders($lqArray,$rqArray,$spreadsheet);

//Set first sheet to first department encountered
$spreadsheet->getActiveSheet()->setTitle(strtok($newArr2[0],','));



//Populate Cells
foreach($newArr2 as $data => $val) {
    $strSplit=explode(",",$val);  //Split up each response block separated by commas

    if (is_null($spreadsheet->getSheetByName($strSplit[0]))){   //If department name doesn't have an associated sheet, create a new one and set as active
        $newWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $strSplit[0]);
        $spreadsheet->addSheet($newWorkSheet);
        $spreadsheet->setActiveSheetIndexByName($strSplit[0]);
        fillHeaders($lqArray,$rqArray,$spreadsheet);
    }
    else{
        $spreadsheet->setActiveSheetIndexByName($strSplit[0]);
    }


    $spreadsheet->getActiveSheet()

        ->setCellValue($colMarker[($strSplit[2]-1)].($strSplit[1]+1),
            $spreadsheet->getActiveSheet()->getCell($colMarker[($strSplit[2]-1)].($strSplit[1]+1))->getValue()+1 ) //Increment the matching response cell by 1
    ;

}

//Function fills out headers and generates the question rows, also formats the column width and style
function fillHeaders($arr1,$arr2, $spreadsheet){

    //Set Headers
    $lqColArray = array_chunk($arr1, 1);
    $rqColArray = array_chunk($arr2, 1);

    $spreadsheet->getActiveSheet()
        ->setCellValue('A1', 'Left Question')
        ->setCellValue('B1', '1 (Fully Agree)')
        ->setCellValue('C1', '2 (Mostly Agree')
        ->setCellValue('D1', '3 (Partly Agree')
        ->setCellValue('E1', '4 (Partly Agree')
        ->setCellValue('F1', '5 (Mostly Agree')
        ->setCellValue('G1', '5 (Fully Agree')
        ->setCellValue('H1', 'Right Question')
    ;

    //Write questions to cells
    $spreadsheet->getActiveSheet()
        ->fromArray(
            $lqColArray,   // The data to set
            NULL,           // Array values with this value will not be set
            'A2'            // Top left coordinate of the worksheet range where
        //    we want to set these values (default is A1)
        );
    $spreadsheet->getActiveSheet()
        ->fromArray(
            $rqColArray,   // The data to set
            NULL,           // Array values with this value will not be set
            'H2'            // Top left coordinate of the worksheet range where
        //    we want to set these values (default is A1)
        );
}


//make object of the Xlsx class to save the excel file
$writer = new Xlsx($spreadsheet);
$fxls ='surveyreport.xlsx';

// We'll be outputting an excel file
header('Content-type: application/vnd.ms-excel');

// It will be called file.xls
header('Content-Disposition: attachment; filename="report.xlsx"');

// Write file to the browser
$writer->save('php://output');
?>