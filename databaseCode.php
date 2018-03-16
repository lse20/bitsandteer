<?php
	require_once('rabbitMQLib.inc');
	require_once('phpDBFunctions.php'); //functions used to get results from the database
	require_once('dbVar.php'); //login details for the database
	
	$client = new rabbitMQClient('testRabbitMQ.ini','testServer');	
	function dbInteraction($response)
	{	
		var_dump($response);
		$accType = $response['accType'];
		//assign variables from forms to usable php variables
		
		//Ben, please double check these for me. Feel free to edit this as needed.
		/*if($response['accType'] == "")
		{
			$msg = "error:  no function mentioned";
			echo $msg;
			$file = fopen("errorLog.txt","w");
			fwrite($file, $errmsg);
			fclose($file);
		}*/
		if($response['accType'] == "doctor")
		{	
			//$Function = $response['function'];
			$user=$response['user']; //=$Req[1]
			$pass=$response['pass'];
			//$rePass=$response['rePass'];
			$email=$response['email'];
			$telNo=$response['telNo'];
			$fName=$response['fName'];
			$lName=$response['lName'];
			$lNo=$response['lNo'];//ask Ben wtf this is
			$spec=$response['spec']; //specialization
			$address=$response['address'];
			$sex=$response['sex'];
			$func=$response['func'];
		}
		elseif($response['accType'] == "patient")
		{
			$user=$response['user'];
			$pass=$response['pass'];
			$email=$response['email'];
			$fName=$response['fName'];
			$lName=$response['lName'];
			$age=$response['age'];
			$height=$response['height'];
			$weight=$response['weight'];
			$sex=$response['sex'];
			$mHist=$response['mHist'];
			$func=$response['func'];
		}
		elseif($response['func'] == "dlogin")
		{
			$user=$response['user'];
			$pass=$response['pass'];
			$accType=$response['accType'];
			$lNo=$response['lNo'];
		}
		else
		{
			$user=$response['user'];
			$pass=$response['pass'];
			$accType=$response['accType'];
		}
		//soft checked it; looks good.  Losing sanity, will hard check after I get some sleep -ben

		$dbResults=array();

		//$echo "".$user;
		
		//open a connection to the mysql server running on the same machine this php is running on

		$testCon=mysqli_connect("$dbAddr", "$dbUser","$dbPass","$db"); //create a connection to the database. in the real version
		//there'd be some beefing up of the security. The variables are in another file just in case this one is accessed.
		
		$err=mysqli_connect_errno(); //function returns error id value from last connection
		
		if ($err) //if there's an error
		{
			$conErr=mysqli_connect_error($testCon); //assign the error message
			//check the dbfunction for how to set up the error log.  I don't want to mess to much with this code here myself -ben
			printf("Connection failed %s\n", $err , $testCon); //print error id and message
		}
		
		switch ($Function) 
		{ //Ben, I'm writing the cases as their Function names; I'll rewrite this after I get it to you
		
			/*case "":
				$dbResults=loginType($user, $pass, $testCon, $accType);
				break;
*/
			case "dlogin"://passes username, password, and license number to authenticate the doctor.  Returns true if credential are good, false if they don't exist.
				$dbResults=docLogin($user, $pass, $testCon, $lNo);
				client->send_request($dbResults);
				break;
			
			case "plogin"://same as doctor but doesn't use license number
				$dbResults=patientLogin($user, $pass, $testCon);
				client->send_request($dbResults);
				break;
			
			case "searchDoctors": //receives a search type (ie: spec) and a search word (ie: gynecology) and searches the doctor's table for everyone that matches the two parameters.   Returns an array of 0-whatever number of doctors that meets the criteria and their information and sends it back to the front end
				$dbResults=searchDoctors($searchType, $searchWord, $testCon);
				client->send_request($dbResults);
				break;//client->send_request(true);;

			case "displayDoc": //displays the list of patients attached to the doctor.  Searches the Doctor's specific row by the doctor's unique email
				$dbResults=patientList($testCon, $email); //this function is being rewritten
				client->send_request($dbResults);
				break;

			case "dRegister": //registers the information of Doctors.  Simple, straight forward
				$bool=array('dReg'=>'false');
				if(addDoctor($user, $pass, $lNo, $fName, $lName, $sex, $spec, "", $email, $telNum, $address, $testCon))
					$bool=array('operation'=>'true');//client->send_request(true);
				client->send_request($bool);
				break;

			case "pRegister"://registers the information of Patients.  Same as above
				$bool=array('pReg'=>'false');
				if(addPatient($user, $pass, $fName, $lName, $age, $height, $weight, $sex, $mHist, $testCon))
					$bool=array('operation'=>'true');//client->send_request(true);
				client->send_request($bool);
				break;
			
			case "wDocRev"://adds a Doctor Review.  Searches for the Doctor's table via a unique key email and adds a review to their Doctor Reivew cell
				$bool=array('operation'=>'false');
				if(addReview($email, $rev, $testCon))
					$bool=array('operation'=>'true');//client->send_request(true);
				client->send_request($bool);
				break;
			
			case "wPatNote"://adds a Patient Note.  Same as above; unique key is the $user, adds the note to the Doctor's note column
				$bool=array('operation'=>'false');
				if(addNote($user, $note, $testCon))
					$bool=array('operation'=>'true');//client->send_request(true);
				client->send_request($bool);
				break;

			case "displayPatient": //displays patient's information.  Sends the logged in patient's information to the front end to display.  Searches the patient's row via the username
				$dbResults = viewPatInfo($user, $testCon);
				client->send_request($dbResults);
				break;

			case "rateDoc": //sets a rating to a doctor.  Uses unique key email to find the Doctor row and sets an int between 0 and 5 to them.  Every additional rating is then averaged.
				$bool=array('operation'=>'false');
				if(rateDoc($email, $rating, $con))
					$bool=array('operation'=>'true');//client->send_request(true);
				client->send_request($bool);
				break;

			case "updateInfo": //updates information for either account types.  First defines the account type to set the right tables, $searchVar will either contain an email if account type is doctor; username if it is a patient account.  $changeCol searches for the column that needs to be changed and $changeVal is that new information. -edited
				$bool=array('operation'=>'false');
				if(updateRecords($accType, $searchVar, $changeCol, $changeVal))
					$bool=array('operation'=>'true');//client->send_request(true);
				client->send_array($bool);
				break;
		}
	}
			
	$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
	$server->process_requests('dbInteraction');


?>
