<?php
	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');
	require_once('listenerFunctions.php'); //functions used to get results from the database	
	
		
	function dbRequest($response)
	{	
		extract(parse_ini_file('dbVar.ini')); //creates variables from an array created from an .ini file		
		
		$testCon=mysqli_connect("$dbAddr", "$dbUser","$dbPass","$db"); //mysqli connection to db

		/*foreach ($response as $res) {
			$res=mysqli_real_escape_string($testCon,$res);
		}*/
		
	
		echo "vardump #1 ahoy \n";
		var_dump($response);
		echo "\n";
		writelog("receiving rabbit message");
		$Function = $response['function'];	 
		
		writelog("establishing database connection");
		$testCon=mysqli_connect($dbAddr, $dbUser,$dbPass,$db); //create a connection to the database. in the real version
		//there'd be some beefing up of the security. The variables are in another file just in case this one is accessed.
		
		$err=mysqli_connect_error(); //function returns error id value from last connection
		if (isset($err)) //if there's an error
		{
			$conErr=mysqli_connect_error(); //assign the error message
			$errMsg="Error: mysqli connection failed: ". $conErr ;
			writelog($errMsg);
		}
		
		switch ($Function) 
		{ //Ben, I'm writing the cases as their Function names; I'll rewrite this after I get it to you
		
			case "login":
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
		
			case "updateRecord":
					

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
			
			case "rateDoc":
				$user=$response['username'];
				$rating=$response['rating'];
				rateDoctor($user,$rating,$testCon);
				break;
			
			case "docNote":
				$email=$response['email'];
				$note=$response['note'];
				$username=$response['userID'];
				$dbResults=addNote($email, $username,$note,$testCon);	
				break;
	
			case "myPatients":
				$user=$response['username'];
				$license=$response['license'];
				$dbResults=patientList($user,$license,$testCon);
				break;
			
			case "setDoc":
				$user=$response['username'];
				$email=$response['email'];
				$dbResults=setDoc($user,$email,$testCon);
				break;
	
			case "error":
				$err=$response['log'];
				$ferr= "error:" . $err;
				writelog($err);
				break;

			case "docViewPat":
				$email=$response['email'];
				$dbResults=docViewPat($email,$testCon);
				break;
	
			case "logtest":
				writelog('test');	
				$s=var_dump($response);
				writelog($s);
				break;

			case "functest":
				$q="select * from patientRecords LIMIT 1";
				$query=mysqli_query($testCon,$q);
				$a="checking checkQ";
				checkQ($query, $a, $testCon);
				var_dump(mysqli_fetch_assoc($query));
				break;
		}
		
		if(isset($dbResults))
		return $dbResults;
		writelog("responding and closing function \n\n");
		if (($Function!='error') AND isset($dbResults) )
			return $dbResults;
	}
			
	$server = new rabbitMQServer("testRabbitMQ.ini", "Login_T_DB");
	$server->process_requests('dbRequest');
	
	

?>
