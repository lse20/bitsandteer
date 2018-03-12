<?php

	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');
	require_once('phpDBFunctions.php'); //functions used to get results from the database
	require_once('dbVar.php'); //login details for the database
	//	
	function process_requests($response)
	{	
		
		$Function = $response['function'];
		$user=$response['username']; //=$Req[1]
		$pass=$response['password'];
		$rePass=$response['rePass'];
		$status=$response['status'];
		$eMail=$response['email'];
		$telNo=$response['telNo'];
		$firstName=$response['fname'];
		$lastName=$response['lname'];
		$license=$response['lNo'];//ask Ben wtf this is
		$specNo=$response['spec']; //specialization
		$address=$response['address'];
		$sex=$response['sex'];


		//$echo "".$user;
		

		$testCon=mysqli_connect("$dbAddr", "$dbUser","$dbPass","$db"); //create a connection to the database. in the real version
		//there'd be some beefing up of the security. The variables are in another file just in case this one is accessed.
		
		$err=mysqli_connect_errno(); //function returns error id value from last connection
		
		if ($err) //if there's an error
		{
			$conErr=mysqli_connect_error($testCon); //assign the error message
			printf("Connection failed %s\n", $err , $con); //print error id and message
		}
		
		switch ($Function) { //Ben, I'm writing the cases as their Function names; I'll rewrite this after I get it to you
		
		case "auth"
			//
			break;
		
		case "listDoctors" 
			listDoctors($testCon);
			break;
		}

		case "patientList" //I think I also need to check status here but unsure on multiple checks with switch
			patientList($testCon, $user); //this function is being rewritten
			break;

		case "addDoctor" //minor rewrite but not much
			addDoctor($user, $pass, $license, $firstName, $lastName, $name, $gender, $specialization, $rating, "", $email, $phone, $location);
			break;
	
		case "addReview" //also minor rewrite needed. this function is for reviewing doctors OR adding notes to patients
			addReview($firstName, $lastName, $inputText, $testCon, $status);
			break;

		case "viewRecords" //don't think it needs a rewrite but will check
			viewRecords($firstName, $lastName, $user, $testCon);
			break;

		case "viewDoc"
			viewDoc($firstName, $lastName, $testCon);
			break;
		/*
		if($Function == "auth") 
		{
			$x = "select * from uAuth1 where user='$user' and pass='$pass' and status='$status'"; // this returns true if user/pass exist in db. otherwise false.
			$result=mysqli_query($testCon, $x);
			if($result )
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
		echo "Fuck yeah!";
		*/
	}
			
	$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
	$server->process_requests('process_requests');


?>
