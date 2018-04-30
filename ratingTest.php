<?php
require_once('phpDBFunctions.php');
require_once('dbVar.php');

	$rateHouse=2;
	$em="ghouse@deepshit.com";
	$link= mysqli_connect($dbAddr, $dbUser, $dbPass, $db);
	if(!$link)
	{
		echo " " . mysqli_connect_errno($link) . "\n" . mysqli_connect_error($link) . "\n";
	}
	//rateDoctor($em, $rateHouse, $link);
	$arr=array();
	$arr=viewPatInfo('1',$link);
	print_r($arr);



?>

