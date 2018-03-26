<?php
// We'll be outputting a excel document
header('Content-type:http://localhost/Survey/test.xlsx');

// Document is called report.xlsx
header('Content-Disposition: attachment; filename="report.xlsx"');

// The excel source is in test.xlsx
readfile('test.xlsx');
?> 

