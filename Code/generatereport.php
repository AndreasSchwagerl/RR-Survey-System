<?php


$SID = $_POST["ID"];


//include the file that loads the PhpSpreadsheet classes
require '/home2/rrsurvey/public_html/vendor/autoload.php';

//include the classes needed to create and write .xlsx file
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

//Decode JSON objects
$questionJSON=json_decode(json_encode($questionArray),true);
$responseJSON=json_decode(json_encode($responseArray),true);


//Column markers map to cells
$colMarker=array('B','C','D','E','F','G');

//Create new spreadsheet
$spreadsheet = new Spreadsheet();
$spreadsheet->getActiveSheet()->setTitle('All');
//Create Headers
$headers=array("Left Question","1 (Fully Agree)","2 (Mostly Agree)","3 (Partly Agree)","4 (Partly Agree)","5 (Mostly Agree","6 (Fully Agree)","Right Question","AVG");
//Create array to hold spreadsheet data
$dataArray=array();
$deptArray=array('All');

array_push($dataArray,$headers);
for($i=0;$i<sizeof($questionJSON);$i++){
    $placeholder=array('Question '.($i+1),0,0,0,0,0,0,'Question '.($i+1),0);

    for($x=0;$x<sizeof($responseJSON);$x++){
        if($responseJSON[$x]["Order"]==($i)+1){
            $index=$responseJSON[$x]["Response"];
            $placeholder[$index]+=1;
        }

        //Build department list on first pass
        $dept=str_replace(' ','',$responseJSON[$x]["Name"]);

        if(in_array($dept,$deptArray)==false){
            array_push($deptArray,$dept);
        }

    }


    $average=(($placeholder[1]*1)+($placeholder[2]*2)+($placeholder[3]*3)+
        ($placeholder[4]*4)+($placeholder[5]*5+($placeholder[6]*6)))/(array_sum($placeholder));
    $average=number_format((float)$average, 2, '.', '');

    $placeholder[8]=$average;
    array_push($dataArray,$placeholder);
}

$spreadsheet->setActiveSheetIndex(0);
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray($dataArray);

$sheetTitle=''.$deptArray[0];
$dataSeriesLabels = [
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$B$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$C$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$D$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$E$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$F$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$G$1', null, 1),
];



$len=''.(sizeof($questionJSON))+1;
$xAxisTickValues = [
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$A$2:$A$'.$len, null, $len),	//	Q1 to Q4
];



$dataSeriesValues = [
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$B$2:$B$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$C$2:$C$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$D$2:$D$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$E$2:$E$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$F$2:$F$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$G$2:$G$'.$len, null, $len),
];





$series = new DataSeries(
    \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART,		// plotType
    \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_STACKED,	// plotGrouping
    range(0, count($dataSeriesValues)-1),			// plotOrder
    $dataSeriesLabels,								// plotLabel
    $xAxisTickValues,								// plotCategory
    $dataSeriesValues								// plotValues
);

//  Set up a layout object for the Pie chart
$layout = new \PhpOffice\PhpSpreadsheet\Chart\Layout();
$layout->setShowVal(true);
$layout->setShowPercent(true);

//  Set the series in the plot area
$plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea($layout, [$series]);
//  Set the chart legend
$legend = new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_RIGHT, null, false);

$title = new \PhpOffice\PhpSpreadsheet\Chart\Title('Total Results');

//  Create the chart
$chart = new Chart(
    'chart', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    null   // yAxisLabel    - Pie charts don't have a Y-Axis
);

//Set the position where the chart should appear in the worksheet
//Set dynamic width
$colNum=$len+2;
while ($colNum!= 0) {
    $colNum--;
    $sb=$sb.chr(ord('A')+($colNum % 26));
    $colNum /=26;
    $colNum=floor($colNum);
}

$widthMarker= ''.strrev($sb);
$chart->setTopLeftPosition('K2');
$chart->setBottomRightPosition($widthMarker.'23');

//Add the chart to the worksheet
$worksheet->addChart($chart);

$cell_st =[
    'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
];
$spreadsheet->getActiveSheet()->getStyle('B1:I'.((sizeof($responseArray))+1))->applyFromArray($cell_st);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);


for($z=1;$z<sizeof($deptArray);$z++){

    
    $dataArray=array();
    //Set Department sheets
    $newWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $deptArray[$z]);
    $spreadsheet->addSheet($newWorkSheet);
    $spreadsheet->setActiveSheetIndexByName($deptArray[$z]);

    $dataArray=array();
    array_push($dataArray,$headers);

    for($i=0;$i<sizeof($questionJSON);$i++){
        $placeholder=array('Question '.($i+1),0,0,0,0,0,0,'Question '.($i+1),0);

        for($x=0;$x<sizeof($responseJSON);$x++){
            $name=str_replace(' ','',$responseJSON[$x]["Name"]);
            if($name==$deptArray[$z]){
                if($responseJSON[$x]["Order"]==($i)+1){
                    $index=$responseJSON[$x]["Response"];
                    $placeholder[$index]+=1;
                }
            }
        }


        $average=(($placeholder[1]*1)+($placeholder[2]*2)+($placeholder[3]*3)+
            ($placeholder[4]*4)+($placeholder[5]*5+($placeholder[6]*6)))/(array_sum($placeholder));
        $average=number_format((float)$average, 2, '.', '');

        $placeholder[8]=$average;
        array_push($dataArray,$placeholder);


}

$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray($dataArray);
$sheetTitle=''.$deptArray[$z];

$dataSeriesLabels = [
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$B$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$C$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$D$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$E$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$F$1', null, 1),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$G$1', null, 1),
];



$len=''.(sizeof($questionJSON))+1;
$xAxisTickValues = [
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $sheetTitle.'!$A$2:$A$'.$len, null, $len),	//	Q1 to Q4
];



$dataSeriesValues = [
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$B$2:$B$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$C$2:$C$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$D$2:$D$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$E$2:$E$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$F$2:$F$'.$len, null, $len),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $sheetTitle.'!$G$2:$G$'.$len, null, $len),
];





$series = new DataSeries(
    \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART,		// plotType
    \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_STACKED,	// plotGrouping
    range(0, count($dataSeriesValues)-1),			// plotOrder
    $dataSeriesLabels,								// plotLabel
    $xAxisTickValues,								// plotCategory
    $dataSeriesValues								// plotValues
);

//  Set up a layout object for the Pie chart
$layout = new \PhpOffice\PhpSpreadsheet\Chart\Layout();
$layout->setShowVal(true);
$layout->setShowPercent(true);

//  Set the series in the plot area
$plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea($layout, [$series]);
//  Set the chart legend
$legend = new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_RIGHT, null, false);

$title = new \PhpOffice\PhpSpreadsheet\Chart\Title(''.$sheetTitle.' Results');

//  Create the chart
$chart = new Chart(
    'chart', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    null   // yAxisLabel    - Pie charts don't have a Y-Axis
);

//Set the position where the chart should appear in the worksheet
//Get width of questions


$chart->setTopLeftPosition('K2');
$chart->setBottomRightPosition($widthMarker.'23');

//Add the chart to the worksheet
$worksheet->addChart($chart);

//Style Sheet
$cell_st =[
    'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
];
$spreadsheet->getActiveSheet()->getStyle('B1:I'.((sizeof($responseArray))+1))->applyFromArray($cell_st);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);

}

$spreadsheet->setActiveSheetIndex(0);

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=abc.xlsx");
$writer->save('php://output','Xlsx');

?>