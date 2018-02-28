<?php

	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');
	/*use PhpAmqpLib\Connection\AMQPStreamConnection;
	$connection = new AMQPStreamConnection('192.168.1.101', '5672', 'guest');
	$channel = $connection->channel();
	$channel->queue_declare('Web_T_DB', false, false, false, false);
	//$Req=RMQ.listen();
	//$array=json_decode($Req);*/
	
	//$user= "blah";
	function process_requests($response)
	{	
		$jdec = json_decode($response->body, true);
		$Function = $jdec['function'];
		$user=$jdec['user']; //=$Req[1]
		$pass=$jdec['pass'];
		$status=$jdec['status'];

		echo $user." ".$pass." ".$Function." ".$status."\n".$jdec;

		//$echo "".$user;
		$testCon=mysqli_connect("127.0.0.1", "root","toor","it490"); //create a connection to the database. in the real version
		//the parameters for the db will not be so insecure 

		if ($testCon) 
		{
			echo "Success!\n";
		}
		else 
		{
			echo "Failure!\n";
        	}

		echo $Function;

		if($Function == "auth")
		{
			$x = "select * from uAuth where user='$user' and pass='$pass' and status='$status'"; // this returns true if user/pass exist in db. otherwise false.
			if(mysqli_query($x) )
			{ //see previous comment. if statement with parameter of the mysqli query
				$output="Login successful.";
				echo $output;
			}	
			else 
			{
				$output="Login unsuccessful, check your credentials.";
				echo $output;
			}
		}		
		else
		{
			//return "failure.";
			echo "failure";
		}
	}
	//json_encode($output);
	/*$channel->basic_consume('Web_T_DB', '', false, true, false, false, $callback);
	while(count($channel->callbacks))
	{
		$channel->wait();
		echo "Fuck you php";
	}
	
	$channel->close();*/		
	$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
	$server->process_requests('process_requests');
	exit();
?>
