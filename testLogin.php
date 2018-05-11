<?php

	require_once('rabbitMQLib.inc');
	
//	$user = $argv[1];
//	$pass = $argv[2];
//	$func = $argv[3];

	$client = new rabbitMQClient('testRabbitMQ.ini','testServer');
	
	$tmsg = array('userID' => 'haha', 'password' => 'test','email' => 'ahah@njit.edu','note'=> 'tittiesandbeer', 'function' => 'docNote');
	$client -> send_request($tmsg);	

	function yesOrNo($tmsg)
	{
		var_dump($tmsg);
	}

	$server = new rabbitMQServer('testRabbitMQ.ini','testServer');
	$server -> process_requests('yesOrNo');
?>
