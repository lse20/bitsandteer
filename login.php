<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

        require_once('rabbitMQLib.inc');
	
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
        
	$user = $_POST['username'];
        $pass = $_POST['password'];
	$accType = $_POST['accType'];
	/*$user = $argv[1];
	$pass = $argv[2];
	//$medID = $argv[4];
	$accType = $argv[3];*/
        
        if($accType == "doctor" && isset($_POST['medID']))
        {
                $func = "docAuth";
		$medID = $_POST['medID'];
        }
	elseif($accType == "doctor" && !isset($_POST['medID']))
	{
		header("refresh 1; login.html");
		$msg = "if you are a doctor, you must put in your Medical License Number";
		die($msg);
	}
        else
        {
                $func = "patAuth";
		$medID = NULL;
        }

        $request = array("function"=>$func, "user"=>$user, "pass"=>$pass, "accType"=>$accType, "medID"=>$medID);
        $client->send_request($request);

        echo "SUCCESSSSS!  *in Dexter's voice*".PHP_EOL;

	function yesOrNo($response)
	{
		//$jdec = json_decode($response->body, true);
		if($response["dlogin"] == true)
		{
			$_SESSION['sData'] = $sData;
			header("refresh 1; doctorPortal.html");
			exit();
		}
		if($response["plogin"] == true)
		{
			$_SESSION['sData'] = $sData;
			header("refresh 1; patientPortal.html");
			exit();
		}
		else
		{
			header("refresh 1; login.html");
			$msg = "No such user or wrong password.  Please try again.";
			exit();
		}
	}
	
	$server = new rabbitMQServer("testRabbitMQ.ini","testServer");
	$server->process_requests('yesOrNo');
	exit();
?>

