<?php

	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');

	$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
	$user = $_POST['username'];
	$pass = $_POST['password'];
	$func = "register";
	
	if($_POST["state"] == "patient")
	{
		$state = "patient";
	}
	elseif($_POST["state"] == "doctor")
	{
		$state = "doctor";
	}
	else
	{
		header('Location:  localhost:80/html/web/rmqp/register.php');
		$msg = "Please Select an Account Type";
		$erMsg = "No Account Type";
		exit($erMsg);
	}
	
	$request = array("function"=>$func, "user"=>$user, "pass" => $pass, "state" => $state);
	$jencode = json_encode($request);
	$response = $client->send_request($jencode);
?>
