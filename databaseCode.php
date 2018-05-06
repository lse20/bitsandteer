<?php
	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');
	require_once('listenerFunctions.php'); //functions used to get results from the database	
	
	
	function dbRequest($response)
	{	
		//assign variables from forms to usable php variables
		echo "vardump #1 ahoy \n";
		var_dump($response);
		echo "\n";
		logger("receiving rabbit message");
		$Function = $response['function'];	 
		
		
		logger("establishing database connection");
		$testCon=mysqli_connect("127.0.0.1", "root","toor","it490"); //create a connection to the database. in the real version
		//there'd be some beefing up of the security. The variables are in another file just in case this one is accessed.
		
		$err=mysqli_connect_errno(); //function returns error id value from last connection
		if ($err) //if there's an error
		{
			$conErr=mysqli_connect_error($testCon); //assign the error message
			printf("Connection failed %s\n", $err , $con); //print error id and message
			logger($conErr);
		}
		
		switch ($Function) 
		{ //Ben, I'm writing the cases as their Function names; I'll rewrite this after I get it to you
		
			case "dLogin":
				$user=$response["username"];
				$pass=$response["password"];
				echo "\n hi hi";
				$dbResults=login($user, $pass, $testCon);
				echo "test after login\n";
				break;

			case "pLogin":
				$user=$response["username"];
				$pass=$response["password"];
				echo $user . " " . $pass . "\n";
				$dbResults=login($user, $pass, $testCon);
				break;
			
			case "listDoctors": //returns into an array list of all doctors 
				$dbResults=listDoctors($testCon);
				break;

			case "displayDoc": //I think I also need to check status here but unsure on multiple checks with switch
				$user=$response["username"];
				$dbResults=viewDoc($user, $testCon); //this function is being rewritten
				break;

			case "dRegister": //minor rewrite but not much
				echo "test d case";
				$user=$response['username'];
				$pass=$response['password'];
				$license=$response['lNo'];
				$firstName=$response['fName'];
				$lastName=$response['lName'];
				$gender=$response['sex'];
				$specialization=$response['spec'];
				$email=$response['email'];
				$phone=$response['telNo'];
				$location=$response['address'];
				$dbResults=addDoctor($user, $pass, $license, $firstName, $lastName, $gender, $specialization, $email, $phone, $location, $testCon);	
				return $dbResults;
				break;

			case "pRegister":
				$user=$response['user'];
				$pass=$response['pass'];
                                $email=$response['email'];
                                $firstName=$response['fName'];
                                $lastName=$response['lName'];
                                $gender=$response['sex'];
                                $age=$response['age'];
				$height=$response['height'];
				$weight=$response['weight'];
                                $diagnosis=$response['mHist'];
                                $location=$response['address'];
				$dbResults[0]=addPatient($user, $pass,$email, $firstName, $lastName, $age, $height, $weight, $gender, $diagnosis, $location, $testCon);
				break;
			
			case "wRev"://also minor rewrite needed. this function is for reviewing doctors OR adding notes to patients
				$user=$response['username'];
				$inputText=$response['rev'];	
				$dbResults=addReview($user, $inputText, $testCon);
				break;

			case "viewRecords": //don't think it needs a rewrite but will check
				viewRecords($firstName, $lastName, $user, $testCon);
				break;

			case "viewDoc":
				viewDoc($firstName, $lastName, $testCon);
				break;
			
			case "docSearch":
				$searchVar=$response['searchVar'];
				$searchType=$response['searchType'];
				$dbResults=docSearch($searchType ,$searchVar , $testCon);
				break;

			case "displayPatient":
				$user=$response['username'];
				$dbResults=viewPatInfo($user,$testCon);
				break;
			
			case "docRate":
				$user=$response['username'];
				return true;
				break;
	
			case "error":
				$err=$response['log'];
				$ferr= "error:" . $err;
				logger($err);
				break;

		}
		var_dump($dbResults);
		logger("responding and closing function \n\n");
		if (($Function!='error') )
			return $dbResults;
	}
			
	$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
	$server->process_requests('dbRequest');


?>
