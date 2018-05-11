<?php

	require_once('listenerFunctions.php');
	//require_once('dbVar.ini');
	function tAB($stuff)
	{
		echo "\n" . $stuff . "\n";
		extract(parse_ini_file('dbVar.ini'));
		$con=mysqli_connect("$dbAddr","$dbUser","$dbPass","$db");
		if(!$con)
			return logger(var_dump($con));
		$q="select name from Doctors;";
		$query=mysqli_query($con,$q);
		while($r=mysqli_fetch_assoc($query))
			$arr[]=$r;
		var_dump($arr);
		print_r($arr);
		$nStuff=mysqli_real_escape_string($con,$stuff);
		echo "\n" . $nStuff . "\n";
		logger($nStuff);
	}
	
	tAB("O'Brien");

?>
