<?php

	require_once('rabbitMQLib.inc');

	$client = new rabbitMQClient('testRabbitMQ.ini','testServer'); //replace this with your .ini file. ask me tomorrow. this is a client/sender
	
	$client -> send_request($tmsg);	//send_request sends the parameter to the recipient and then waits for a response.

//if you want to send a message one way, use publish();

	function yesOrNo($tmsg) //this function happens every time your listener receives a message
	{
		var_dump($tmsg);
	}

	$server = new rabbitMQServer('testRabbitMQ.ini','testServer'); //this is a listener
	$server -> process_requests('yesOrNo'); //process_requests(function) executions the function with a $response array. ask tomorrow. 
?>
