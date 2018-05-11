<?php

	require_once('rabbitMQLib.inc');
	
//	$user = $argv[1];
//	$pass = $argv[2];
//	$func = $argv[3];

	$client = new rabbitMQClient('testRabbitMQ.ini','testServer');
	
	$tmsg = array('username' => '1', 'password' => 'tab', 'rev'=>'I hate this', 'function' => 'wRev');
	$client -> publish($tmsg);	

	function yesOrNo($tmsg)
	{
		var_dump($tmsg);
	}

	//$server = new rabbitMQServer('testRabbitMQ.ini','testServer');
	//$server -> process_requests('yesOrNo');
?>
