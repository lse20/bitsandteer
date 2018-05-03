#!/usr/bin/php
<?php

	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');
	/*use the above code for local testing*/

	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	
	if(!isset($argv[1]))
	{
		$msg = "Please enter a username\n";
		exit($msg);
	}
	if(!isset($argv[2]))
	{
		$msg = "Please enter a password\n";
		exit($msg);
	}
	if(!isset($argv[3]))
	{
		$msg = "Please clarify a function\n";
		exit($msg);
	}

	$user = $argv[1];
        $pass = $argv[2];
        $func = $argv[3];
        $stat = false; //change to true if you want to change status

	
	//alternate connection test [alt]//
	/*$connection = new AMPQStreamConnection('192.168.1.101', 5672, 'admin', 'guest');
	$channel = $connection->channel();
	$channel->queue_declare('databaseTest_T_Web', false, false, false, false);*/
	
	$inputSent = array("function" => $func, "user" => $user, "pass" => $pass, "status" => $stat);
	$jencode = json_encode($inputSent);
	$response = $client->send_request($jencode);

	echo "SUCCESSSS!  *in Dexter's voice*".PHP_EOL;

	//alternate connection test [alt]//
	/*$sent = new AMQPMessage($inputSent, array('delivery_mode' => 2));
	$channel->basic_publish($sent, '', 'databaseTest_T_Web');
	$channel->close();
	$connection->close();*/

?>
