<?php

	function listDoctors($connect) 
	{
	//printing a list of doctors is easy. this code is written to assume
	//you have connected to the db and passed that connection as $connect.
	//then you assign that query to a result, create an array to store it
	//in. iterate over the rows with a while loop until assigning a row	
	//results in NULL, which closes the loop.  Either print it here to test, 
	//or return the array through rabbit. 

		$query= "select name from Doctors;";
		$result=mysqli_query($connect, $query);
		$printResult= array();		


		while ($row=mysqli_fetch_assoc($result))
		{
			$printResult[]=$row;
		}

		//print_r($printResult);
		return $printResult;
	}
	
	function patientList($connect, $user) //NEEDS UPDATE READ BELOW
	//due to USERNAME CHANGES there must be a second query 
	// first query: find doctor license # from doctor table
	// second: search patientRecords for patients where $doctor=the license
	// from the first query
	{
		$query="select name from patientRecords where doctor='$user';";		
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
		return $patientArray;
	}
	
	function uAuth($user, $pass, $connection, $accType)
	{
	
		switch ($acctype)
		{
			case "patient":
				$q="select name,height,weight, from patientRecords where username='$user' and password='$pass';";
				break;
		
			case "doctor":
				$q="select name,license,reviews from Doctors where username='$user' and password ='$pass';";
				break;
		}

		$query=mysqli_query($connection, $q);
	
		if (!$query)
		{
			echo "credential failure." . $query . mysqli_error($connection);
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
			echo "connection failure". $query . "<br>". mysqli_error($con);
		}
	}

	function addPatient($user, $pass, $firstName, $lastName, $age, $height, $weight, $sex, $diagnosis, $drNote, $doctor, $prescription, $con)
	{
		$name=$firstName . " " . $lastName;
		$query= "INSERT INTO Doctors (username, password, firstName, lastName, name, age, height, weight, sex, diagnosis, drNote, doctor, prescription) VALUES ('$user', '$pass', '$firstName', '$lastName', '$name', $age, '$height', '$weight', '$sex', '$diagnosis', '$drNote', $doctor, '$prescription');";
		$q=mysqli_query($con, $query);
		
		if( mysqli_query($con, $query))
		{
			echo "connection failure" . $query . "<br>" . mysqli_error($con);
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
				echo "Error:" . $query . "<br>" . mysqli_error($con);
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
				echo "Error:" . $query . "<br>" . mysqli_error($con);
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

	/*function updateMe($user, $accType,
	
	*/
	
	/*function docSearch($special, $location, $rating, $con) 
	{
		
		$searchRes=array();
		$query="select
	}*/
	function viewDoc($firstname, $lastname, $con) 
	{
		$query="SELECT firstName,lastName,specialization,rating,review,email,phone,location FROM Doctors WHERE firstName='$firstname' and lastName='$lastname';";
		$viewRes=array();
		$result=mysqli_query($con,$query);
		if($result) 
		{
			echo "success\n";
		}
		else
		{
			echo "Connection failure." . $query . "<br>" . mysqli_error($con);
		}
		
		while($row=mysqli_fetch_assoc($result))
		{
			$viewRes[]=$row;
		}
		print_r($viewRes);
		//return $viewRes;
	}
	

?>
