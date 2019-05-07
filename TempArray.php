<?php

$lines = file('temp.txt');
//date_default_timezone_set('Europe/Lisbon');
$response = array();
//$Temperature = new Temp($_GET['Temp']);

foreach($lines as $line){
	$temp = new Temp($line);
	//if($temp >= $Temperature ){
		array_push($response, $temp);
	//}
}

echo(json_encode($response));