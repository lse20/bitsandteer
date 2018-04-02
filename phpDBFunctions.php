<?php
require_once('dbVar.php');
require_once('rabbitMQLib.inc');

	
	function listDoctors($connect)//case:  func is equal to pulling the list ALL doctors from the dsearch -ben 
	{
	//printing a list of doctors is easy. this code is written to assume
	//you have connected to the db and passed that connection as $connect.
	//then you assign that query to a result, create an array to store it
	//in. iterate over the rows with a while loop until assigning a row	
	//results in NULL, which closes the loop.  Either print it here to test, 
	//or return the array through rabbit. -lou

		$query= "select name from Doctors;";
		$result=mysqli_query($connect, $query);//stores pulling information to a variable -ben
		$printResult= array();		
 

		while ($row=mysqli_fetch_assoc($result))//pulls information to look like an array -ben
		{
			$printResult[]=$row;//sets the selected row within the printResult array -ben
		}

		//print_r($printResult);//uncomment to test
		return $printResult;
	}
	
	function patientList($connect, $user, $medID)//I'm going to write this code based on the assumption that this case is:  a doctor is trying pull his/her list of patients from the database -ben
	//uncomment your code if I am wrong 
	//NEEDS UPDATE READ BELOW
	//due to USERNAME CHANGES there must be a second query 
	// first query: find doctor license # from doctor table
	// second: search patientRecords for patients where $doctor=the license
	// from the first query -lou
	{ 
		/*$query="select name from patientRecords where doctor='$user';";
		$result=mysqli_query($connect, $query);
		if(!$result)
		{
			echo "connection failure" . $query . "<br>" . mysqli_error($connect);
		}

		$patientArray=array();

		while ($row=$mysqli_fetch_assoc($result))
		{
			$patientArray[]=$row;
		}
		//print_r($patientArray);
		return $patientArray;*/

		$query1 = "select name from patientRecords where doctor='$medID';";//selects the name of all the patients who are tied to the doctor that has logged in
		$result = mysqli_query($connect, $query);
		if(!$result)
		{
			$errmsg = "connection failure" . $query . "<br>" . mysqli_error($connect);//creates the error message and sets it to a variable
			echo $errmsg;//prints to console the error
			$file = fopen("$errLog","w");//opens the error log file for writing
			fwrite($file, $errmsg);//writes the error into the log file
			fclose($file);//closes the file
		}

		while($row = $mysqli_fetch_assoc($result))
		{
			$patientListArray[] = $row;
		}
		return $patientListArray;
	}
	
	function dLogin($user, $pass, $con, $license)//changed the name here for ease of reference|case:  login|also added in the license number
	{
	
		$q="select name,license,reviews from Doctors where username='$user' and password ='$pass' and license = '$license';";

		$query=mysqli_query($con, $q);
	
		if (!$query)
		{
			$errMsg = "credential failure." . $query . mysqli_error($con);
			echo "connection error. contact support. $errMsg";//prints to console the error
			logger($errMsg);
		}	
	
		$resA= array();
	
		while ($row=mysqli_fetch_assoc($query))
		{
			$resA[] = $row;
		}	
		return $resA;	
	}

	function pLogin($user, $pass, $con)
	{
		$q="select name,height,weight,age,doctor,drNote from patientRecords where username='$user' and password='$pass';";
		$query=mysqli_query($con, $q);

		if(!$query)
		{
			$errMsg="credentials are incorrect: " . $query . mysqli_error($con);
			echo "connection error, contact support $errMsg";
			logger($errMsg);
		}
		
		$resA=array();
		
		while ($row=mysqli_fetch_assoc($query))
		{
			$resA[]=$row;
		}
		$client = new rabbitMQClient('testRabbitMQ.ini','testServer');
		$client -> send_requests($resA);
	}
	function addDoctor($user, $pass, $license, $firstName, $lastName, $gender, $special, $rating, $review, $email,$phone, $location, $con)
	{
		$name= $firstName . " " . $lastName;
		$query= "INSERT INTO Doctors (username, password, license, firstName, lastName, name, gender, specialization, rating, review, email, phone, location) VALUES ($user, '$pass', $license, '$firstName','$lastName', '$name', '$gender', '$special', $rating, '$review', '$email', $phone, '$location');";
		
		$q=mysqli_query($con,$query);

		if(!$q) 
		{
			$errMsg = "connection failure". $query . "<br>". mysqli_error($con);
			echo $errmsg;//prints to console the error
			$file = fopen("$errLog","w");//opens the error log file for writing
			fwrite($file, $errmsg);//writes the error into the log file
			fclose($file);//closes the file
		}
	}

	function addPatient($user, $pass, $firstName, $lastName, $age, $height, $weight, $sex, $diagnosis, $drNote, $doctor, $prescription, $con)
	{
		$name=$firstName . " " . $lastName;
		$query= "INSERT INTO Doctors (username, password, firstName, lastName, name, age, height, weight, sex, diagnosis, drNote, doctor, prescription) VALUES ('$user', '$pass', '$firstName', '$lastName', '$name', $age, '$height', '$weight', '$sex', '$diagnosis', '$drNote', $doctor, '$prescription');";
		$q=mysqli_query($con, $query);
		
		if( mysqli_query($con, $query))
		{
			$errMsg = "connection failure" . $query . "<br>" . mysqli_error($con);
			echo $errmsg;//prints to console the error
			$file = fopen("$errLog","w");//opens the error log file for writing
			fwrite($file, $errmsg);//writes the error into the log file
			fclose($file);//closes the file
		}		
	}

	function addReview($email, $review, $con)
	{
		$query="UPDATE Doctors SET review= CONCAT(review,'$review') WHERE email='$email';";
		
		if(mysqli_query($con, $query))
		{
			echo "Your review has been added. Thank you. \n";
		}
		else
		{
			$errMsg= "Error: " . $query . "<br>" . mysqli_error($con) . "\n";
			echo "There was an error adding your review. Please try again later.";
			logger($errMsg);
		}
	}

	function addNote($email, $note, $con)
	{
		$query="UPDATE patientRecords SET drNote= CONCAT(drNote, '$note') WHERE email='$email';";
		
		if(mysqli_query($con, $query)) 
		{
			echo "Your notes have been recorded.";
		}
		else
		{
			$errMsg= "Error; " . $query . "<br>" . mysqli_error($con) . "\n.";
			echo "There was an error recording your notes. Please try again later.";
			logger($errMsg);
		}
	}



	function viewRecords($firstName, $lastName, $accType, $user, $con) 
	{
		if ($accType=="doctor")
		{
			$query="SELECT drNote from patientRecords where firstName='$firstName' and lastName='$lastName';";
		}	
		else if($accType=="patient")
		{
			$query="SELECT drNote FROM patientRecords WHERE username='$user';";
		}	
			$result=mysqli_query($con,$query);
			$records=array();
			if($result) 
			{
				while($row=mysqli_fetch_assoc($result))
				{
					$records[]=$row;
				}
			}
			print_r($records);
			//return $records;
	}
	
	function docSearch($sType, $sText, $con) 
	{
		$q="SELECT name,$sType FROM Doctors WHERE $sType='$sText'";
		$query=mysqli_connect($con, $q);
		
		if(!$query)
		{
			$errMsg = "Query failure." . $query . "<br>" . mysqli_error($con);
                        echo "Failed to find any results. Try another search?";//prints to console the error
                        $file = fopen("$errLog","w");//opens the error log file for writing
                        fwrite($file, $errmsg);//writes the error into the log file
                        fclose($file);//closes the file
		}
		$searchRes=array();
		
		while($row=mysqli_fetch_assoc($query))
		{
			$searchRes[]=$row;
		}
		
		return $searchRes;
	}

	function viewDoc($email, $con) 
	{
		//I think it's better if we use license number as the unique key for doctors since in rare cases doctors can have the same names -ben
		$query="SELECT firstName,lastName,specialization,rating,review,email,phone,location FROM Doctors WHERE license='$email';";
		$viewRes=array();
		$result=mysqli_query($con,$query);
		if(!$result)
		{
			$errMsg = "Connection failure." . $query . "<br>" . mysqli_error($con);
			echo "Something went wrong. Try again later, or contact technical support";
			logger($errMsg);
		}
		
		while($row=mysqli_fetch_assoc($result))
		{
			$viewRes[]=$row;
		}
		//print_r($viewRes);
		return $viewRes;
	}
	
	function updateRecords($accType, $sVar, $changeCol, $changeVal)
	{
		//added this new function to let Doc and Pat to update their information
		if($accType == "patient")
		{
			
			$query="UPDATE patientRecords SET $changeCol='$changeVal' where username='$sVar';";//dunno if I wrote that one correctly; sorry -ben
			$viewRes=array();
			$result=mysqli_query($con,$query);
		}
		if($accType == "doctor")
		{
			$query="UPDATE Doctors SET $changeCol='$changeVal' where email='$sVar';";//dunno if I wrote that one correctly; sorry -ben
			$viewRes=array();
			$result=mysqli_query($con,$query);
		}
		if($result) 
		{
			echo "success\n";
		}
		else
		{
			$errMsg = "Connection failure." . $query . "<br>" . mysqli_error($con);
			echo "Update failed. Contact support.";
			logger($errLog, $errMSg);
		}
	}
	
	function logger($errorMessage) //todo: make this concat the file 
	{
		$file = fopen("errorLog.txt","w");//opens the error log file for writing
		fwrite($file, $errorMessage);//writes the error into the log file
		fclose($file);//closes the file
	}
	
	function viewPatInfo($user, $con)
	{
		$retArr=array();
		$q="SELECT name,email,height,weight,sex,diagnosis,drNote,doctor,prescription FROM patientRecords WHERE username='$user';";
		$query=mysqli_query($con, $q);
		
		if(!$query)
		{
			$errMsg= "Query failure." . $q . "<br>" . mysqli_error($con);
			echo "Failed to connect. Contact support.";
			logger($errMsg);
		}
		
		while($row=mysqli_fetch_assoc($query))
		{
			$retArr[]=$row;
		}
		//$retArr[]=mysqli_fetch_row($query);
		return $retArr;
	}	

	function rateDoctor($email, $rating, $con)
	{
		$sumR=0;
		$aRate=array();
		$q="SELECT rating FROM Doctors WHERE email='$email';";
		$query=mysqli_query($con, $q);
		if(!$query)
		{
			echo "There was an error. Contact support.";
			$errMsg= "Query Error: " . $query . "<br>" . mysqli_error($con);
			logger($errMsg);
		}
		
		while($row=mysqli_fetch_assoc($query))
		{
			$aRate[]=$row['rating'];
		}
		$oldR=$aRate[0];
		$oldR=$oldR+$rating;
		$oldR=intdiv($oldR,2);
		
		$q="UPDATE Doctors SET rating=$oldR WHERE email='$email';";
		$query=mysqli_query($con,$q);
		if(!$query)
		{
			echo "There was an error. Contact support.";
			$errMsg= "Query Error: " . $query . "<br>" . mysqli_error($con);
			logger($errMsg);
		}
		
	}
?>
