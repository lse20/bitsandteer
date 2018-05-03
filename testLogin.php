<?php

	require_once('rabbitMQLib.inc');
	
	$user = $argv[1];
	$pass = $argv[2];
	$func = $argv[3];

	$client = new rabbitMQClient('testRabbitMQ.ini','testServer');
	
	$msg = array('username' => $user, 'password' => $pass, 'function' => $func);
	$client -> send_request($msg);	

	function yesOrNo($msg)
	{
		var_dump($msg);
	}

	$server = new rabbitMQServer('testRabbitMQ.ini','testServer');
	$server -> process_requests('yesOrNo');
?>
