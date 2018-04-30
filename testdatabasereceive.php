<?php

	require_once('phpDBFunctions.php');

	function testFunctions($msg)
	{
		var_dump($msg);
		$Function = $msg['function'];
		
		switch($Function)
		{
			case 'pLogin':
				$user = $msg['username'];
				$pass = $msg['password'];
				pLogin($user, $pass, $con);
			default:
				$msg = "did not work";
				$client = new rabbitMQClient('testRabbitMQ.ini','testServer');
				$client->publish($msg);
		}
	}
	$server = new rabbitMQServer('testRabbitMQ.ini','testServer');
	$server->process_requests('testFunctions');
?>
