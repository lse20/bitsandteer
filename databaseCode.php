<?php

	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');
	require_once('phpDBFunctions.php'); //functions used to get results from the database
	require_once('dbVar.php'); //login details for the database
	//	
	function process_request($response)
	{	
		//assign variables from forms to usable php variables
		
		//Ben, please double check these for me. Feel free to edit this as needed. 
		$client= new rabbitMQClient("testRabbitMQ.ini", "testServer");
		$Function = $response['function'];	
	//	$Function = $response['function'];
	//	$user=$response['username']; //=$Req[1]
	//	$pass=$response['password']; 
		/*
		$rePass=$response['rePass'];
		$accType=$response['status'];
		$eMail=$response['email'];
		$telNo=$response['telNo'];
		$firstName=$response['fname'];
		$lastName=$response['lname'];
		$license=$response['lNo'];//ask Ben wtf this is
		$specNo=$response['spec']; //specialization
		$address=$response['address'];
		$sex=$response['sex'];
		*/ 

		$dbResults=array();
		if($response['function']=="lou")
		{
			$user=$response['user'];
			$pass=$response['pass'];
			
		}

		$testCon=mysqli_connect("127.0.0.1", "root","toor","it490"); //create a connection to the database. in the real version
		//there'd be some beefing up of the security. The variables are in another file just in case this one is accessed.
		
		$err=mysqli_connect_errno(); //function returns error id value from last connection
		
		if ($err) //if there's an error
		{
			$conErr=mysqli_connect_error($testCon); //assign the error message
			printf("Connection failed %s\n", $err , $con); //print error id and message
		}
		
		switch ($Function) 
		{ //Ben, I'm writing the cases as their Function names; I'll rewrite this after I get it to you
		
			case "auth":
				$dbResults=uAuth($user, $pass, $testCon, $accType);
				break;

			case "pLogin":
				$user=$response["user"];
				$pass=$response["pass"];
				pLogin($user, $pass, $testCon);
				break;
			
			case "listDoctors": //returns into an array list of all doctors 
				$dbResults=listDoctors($testCon);
				break;

			case "displayDoc": //I think I also need to check status here but unsure on multiple checks with switch
				$dbResults=patientList($testCon, $user); //this function is being rewritten
				break;

			case "dRegister": //minor rewrite but not much	
				addDoctor($user, $pass, $license, $firstName, $lastName, $gender, $specialization, $rating, "", $email, $phone, $location, $testCon);
				break;

			case "pRegister":
				addPatient($user, $pass, $firstName, $lastName, $age, $height, $weight, $sex, $diagnosis, $drNote, $doctor, $prescription, $testCon);
				break;
			
			case "wDocRev"://also minor rewrite needed. this function is for reviewing doctors OR adding notes to patients
				addReview($firstName, $lastName, $inputText, $testCon, $accType);
				break;

			case "viewRecords": //don't think it needs a rewrite but will check
				viewRecords($firstName, $lastName, $user, $testCon);
				break;

			case "viewDoc":
				viewDoc($firstName, $lastName, $testCon);
				break;
		}
		$client->send_request($dbResults);
	}
			
	$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
	$server->process_requests('process_request');


?>
