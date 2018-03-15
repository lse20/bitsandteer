<?php

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
			$file = fopen("errorLog.txt","w");//opens the error log file for writing
			fwrite($file, $errmsg);//writes the error into the log file
			fclose($file);//closes the file
		}

		while($row = $mysqli_fetch_assoc($result))
		{
			$patientListArray[] = $row;
		}
		return $patientListArray;
	}
	
	function loginType($user, $pass, $connection, $accType, $license)//changed the name here for ease of reference|case:  login|also added in the license number
	{
	
		switch ($acctype)
		{
			case "patient":
				$q="select name,height,weight, from patientRecords where username='$user' and password='$pass';";
				break;
		
			case "doctor":
				$q="select name,license,reviews from Doctors where username='$user' and password ='$pass' and license = '$license';";
				break;
		}

		$query=mysqli_query($connection, $q);
	
		if (!$query)
		{
			$errMsg = "credential failure." . $query . mysqli_error($connection);
			echo $errmsg;//prints to console the error
			$file = fopen("errorLog.txt","w");//opens the error log file for writing
			fwrite($file, $errmsg);//writes the error into the log file
			fclose($file);//closes the file
		}
	
		$resA= array();
	
		while ($row=mysqli_fetch_assoc($query))
		{
			$resA[] = $row;
		}
	
		return $resA;
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
			$file = fopen("errorLog.txt","w");//opens the error log file for writing
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
			$file = fopen("errorLog.txt","w");//opens the error log file for writing
			fwrite($file, $errmsg);//writes the error into the log file
			fclose($file);//closes the file
		}		
	}

	function addReview($firstName, $lastName, $inputText,$con, $accType) 
	{

		if($accType=="patient")
		{
			//$query='select review from Doctors where name='$name';'
			$query="UPDATE Doctors SET review= CONCAT(review, '$inputText') where firstName='$firstName' AND lastName='$lastName';";
			if (mysqli_query($con, $query))
			{
				echo "success";
			}
			else
			{
				$errMsg = "Error:" . $query . "<br>" . mysqli_error($con);
				echo $errmsg;//prints to console the error
				$file = fopen("errorLog.txt","w");//opens the error log file for writing
				fwrite($file, $errmsg);//writes the error into the log file
				fclose($file);//closes the file
			}
		}
		else if($accType=="doctor")
		{
			$query="UPDATE patientRecords SET drNote= CONCAT(drNote, '$inputText') where firstName='$firstName' AND lastName='$lastName';";
			if (mysqli_query($con, $query))
			{
				echo "success";
			}
			else
			{
				$errMsg = "Error:" . $query . "<br>" . mysqli_error($con);
				echo $errmsg;//prints to console the error
				$file = fopen("errorLog.txt","w");//opens the error log file for writing
				fwrite($file, $errmsg);//writes the error into the log file
				fclose($file);//closes the file
			}
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
                        $file = fopen("errorLog.txt","w");//opens the error log file for writing
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

	function viewDoc($firstname, $lastname, $con) 
	{
		//I think it's better if we use license number as the unique key for doctors since in rare cases doctors can have the same names -ben
		$query="SELECT firstName,lastName,specialization,rating,review,email,phone,location FROM Doctors WHERE firstName='$firstname' and lastName='$lastname';";
		$viewRes=array();
		$result=mysqli_query($con,$query);
		if($result) 
		{
			echo "success\n";
		}
		else
		{
			$errMsg = "Connection failure." . $query . "<br>" . mysqli_error($con);
			echo $errmsg;//prints to console the error
			$file = fopen("errorLog.txt","w");//opens the error log file for writing
			fwrite($file, $errmsg);//writes the error into the log file
			fclose($file);//closes the file
		}
		
		while($row=mysqli_fetch_assoc($result))
		{
			$viewRes[]=$row;
		}
		print_r($viewRes);
		//return $viewRes;
	}
	
	function updateRecords($accType, $changeCol, $changeVal)
	{
		//added this new function to let Doc and Pat to update their information
		if($accType == "patient")
		{
			
			$query="UPDATE patientRecords SET $changeCol='$changeVal' where username='$username';";//dunno if I wrote that one correctly; sorry -ben
			$viewRes=array();
			$result=mysqli_query($con,$query);
		}
		if($accType == "doctor")
		{
			$query="UPDATE Doctors SET $changeCol='$changeVal' where lNo ='$lNo';";//dunno if I wrote that one correctly; sorry -ben
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
			echo $errmsg;//prints to console the error
			logger("errorLog.txt", $errMSg);
		}
	}
	
	function logger($fName, $errorMsg) 
	{
		$file = fopen("$fName","w");//opens the error log file for writing
		fwrite($file, $errmsg);//writes the error into the log file
		fclose($file);//closes the file

	}
	

?>
