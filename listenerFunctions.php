<?php

require_once('rabbitMQLib.inc');
extract(parse_ini_file('log.ini'));
	
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
	
	function patientList($user,$license,$con)//I'm going to write this code based on the assumption that this case is:  a doctor is trying pull his/her list of patients from the database -ben
	{
		$q="SELECT username FROM Doctors WHERE username='$user' AND license='$license';";
		$query=mysqli_query($con,$q);
		if(!$query)
                {
                        $errmsg = "connection failure" .  mysqli_error($con);
			writelog($errmsg);
			var_dump($errmsg);
			return false;
                }
		$check=mysqli_fetch_assoc($query);
		if(!isset($check))
			return false;
		var_dump($check);
		$q = "SELECT firstName,lastName,email,age,sex,address,height,weight FROM patientRecords WHERE doctor='$license';";//selects the name of all the patients who are tied to the doctor that has logged in
		$query = mysqli_query($con, $q);
		if(!$query)
		{
			$errMsg = "connection failure" . $query . "<br>" . mysqli_error($con);
			writelog($errMsg);
			return false;
		}
		while($row=mysqli_fetch_assoc($query))
			$results[]=$row;
		return $results;
	}
	
	function dLogin($user, $pass, $con, $license)//changed the name here for ease of reference|case:  login|also added in the license number
	{
	
		$q="select username from Doctors where username='$user' and password ='$pass' and license = '$license';";
		$query=mysqli_query($con, $q);
	
		if (!$query)
		{
			$errMsg = "credential failure." . $query . mysqli_error($con);
			echo "connection error. contact support. $errMsg";//prints to console the error
			writelog($errMsg);
			return "3";
		}
		else 
		{
			$r=mysqli_fetch_assoc($query);
			var_dump($r);
			if ($user==$r['username'])
			{
				echo $r['username'];
				return "0";
			}
		}
	}

	function pLogin($user, $pass, $con)
	{
		$q="SELECT username from patientRecords WHERE username='$user' AND password='$pass';";
		$query=mysqli_query($con, $q);
		
		if(!$query)
		{
			$errMsg="credentials are incorrect: " . $query . mysqli_error($con);
			echo "connection error, contact support $errMsg";
			writelog($errMsg);
			return "3";
		}
		else
		{
			$r=mysqli_fetch_assoc($query);
			var_dump($r);
			if($user==$r['username'])
			{
				return "1";
			}
			else
				return "3";
		}
	}
	
	function login($user, $pass, $con) 
	{
		writelog(" Login attempt for user: $user "); //record 
		$q="SELECT user,type FROM uAuth WHERE user='$user' AND pass='$pass';";
		echo "\n $q \n";
		$query=mysqli_query($con,$q);

		if(!$query) 
		{
			$errMsg = " login failure: " . mysqli_error($query);
			echo $errMsg . "$user" . "$pass";
			writelog($errMsg);
			return $errMsg;
		}

		$r=mysqli_fetch_assoc($query);
		if(!isset($r['type']))
			return "2";
		var_dump($r);
		$blah=$r['type'];
		$userID=$r['user'];
		$arr=array("0"=>$blah, "userID"=>$userID);
	
		return $arr;
	}

	function addDoctor($user, $pass, $license, $firstName, $lastName, $gender, $special, $email,$phone, $location, $con)
	{
		$name= $firstName . " " . $lastName;
		$q= "INSERT INTO uAuth (user, pass, type, login, failedLog) VALUES ('$user', '$pass', 'd', 0, 0);";
		$query=mysqli_query($con,$q);
 		
		if(!$query)
		{
			$errMsg= "Doctor registration step1 failed: " . mysqli_error($con);
			echo $errMsg;
			writelog($errMsg);
			return false; 
		}
		$q= "INSERT INTO Doctors (username, password, license, firstName, lastName, name, gender, specialization, rating, review, email, phone, location) VALUES ('$user', '$pass', $license, '$firstName','$lastName', '$name', '$gender', '$special', 0, '', '$email', $phone, '$location');";
		
		$query=mysqli_query($con,$q);
		if(!$query) 
		{
			$errMsg = " Doctor registration failed step2 ".  "<br>". mysqli_error($con);
			echo $errMsg;//prints to console the error
			writelog($errMsg);
			$q= "DELETE FROM uAuth WHERE user='$user';";
			$query=mysqli_query($con,$q);
			if(!$query)
			{
				$errMsg="Error deleting from uAuth" . mysqli_error($con);
				echo "\n" . $errMsg . "\n";
				writelog($errMsg);
			}
			return false;
		}
		else
			return true;
	}

	function addPatient($user, $pass, $email, $firstName, $lastName, $age, $height, $weight, $sex, $diagnosis, $address, $con)
	{
		$name=$firstName . " " . $lastName;
		$q="INSERT INTO uAuth (user, pass, type) VALUES ('$user', '$pass', 'p');";
		$query=mysqli_query($con, $q);
		
		if(!$query)
		{
			$errMsg= " adding to uAuth error" . $query . "<br>" . mysqli_error($con);
			echo $errMsg;
			writelog($errMsg);
		}
		else
		{
			$q= "INSERT INTO patientRecords (username, firstName, lastName, name, email, age, height, weight, sex, diagnosis, drNote, doctor, prescription, address) VALUES ('$user', '$firstName', '$lastName', '$name', '$email', $age, '$height', '$weight', '$sex', '$diagnosis', '', 0, '', '$address');";
			$query=mysqli_query($con, $q);
			echo "hi\n";
			echo "$q \n";
			if(!$query)
			{
				$errMsg = "connection failure" . mysqli_error($con) . " ";
				var_dump($errMsg);
				writelog($errMsg);
				$q="DELETE FROM uAuth where user='$user'";
				$query=mysqli_query($con,$q);
				return false;
			}
			echo "returning true";
			return true;
		}
		return true;			
	}

	function addReview($user,$review, $con)
	{	
		$q="SELECT doctor FROM patientRecords WHERE username='$user';";
		$docN=mysqli_query($con,$q);	
		$r=mysqli_fetch_assoc($docN);
		echo "add review vardump";
		var_dump($r);
		$license=$r['doctor'];
		echo $review;
		echo "\n Doc license: " . $license . "\n";
		$q="UPDATE Doctors SET review=CONCAT(review, '$review') WHERE license=$license;";
//		UPDATE patientRecords SET drNote= CONCAT(drNote, '$note') WHERE email='$email';";

		echo "\n $q \n ";	
		$query=mysqli_query($con, $q);
		if(!$query)
		{
			$errMsg= "Error: " . $q . "<br>" . mysqli_error($con) . "\n";
			echo $errMsg;
			echo "There was an error adding your review. Please try again later.";
                        writelog($errMsg);
		}
		else
		{
			echo "Your review has been added. Thank you. \n";
		}
	}

	function addNote($email,$user, $note, $con)
	{
		$q="SELECT license FROM Doctors where username='$user';";
		var_dump($q);
		$query=mysqli_query($con,$q);
		if(!$query)
		{
                        $errMsg= "Error; " .  mysqli_error($con) ;
                        writelog($errMsg);
                        return "There was an error recording your note.";
                }
		var_dump($query);
		$drow=mysqli_fetch_assoc($query);
		$docNum=$drow['license'];
		var_dump($docNum);
		var_dump($email);
		$q="SELECT username FROM patientRecords WHERE doctor='$docNum' AND email='$email'; ";
		$query=mysqli_query($con,$q); 
	
		if(!$query)
		{
                        $errMsg= "Error; " . mysqli_error($con) . "\n.";
                        writelog($errMsg);
                        return "There was an error recording your note.";
                }
		$r=mysqli_fetch_assoc($query);
		if(!isset($r))
			return "You don't have a patient with that email.";
		$ao=$note;
		$bo=$email;
		echo $ao . " " . $bo; 
		
		$q="UPDATE patientRecords SET drNote= CONCAT(drNote, '$note') WHERE email='$email';";
		$query=mysqli_query($con,$q);
				
		if(!$query) 
		{
			$errMsg= "Error adding note; " . $query . "<br>" . mysqli_error($con) . "\n.";
                        writelog($errMsg);
			return "There was an error recording your note.";
		}
		else
		{
			return "Note recorded";
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
	
	function setDoc($user, $email, $con)
	{
		$q="SELECT license FROM Doctors where email='$email';";
		$query=mysqli_query($con,$q);
		
		if(!$query)
                {
                        $errMsg = "\nsetDoc Query failure. " . mysqli_error($con);
                        var_dump($errMsg);//prints to console the error
                        writelog($errMsg);
			return false;
                }
		
		$res=mysqli_fetch_assoc($query);
		$lic=$res['license'];
		var_dump($lic);
		var_dump($user);	
		$q="UPDATE patientRecords SET doctor=$lic WHERE username='$user';";
		$query=mysqli_query($con,$q);

		 if(!$query)
                {
                        $errMsg = "\nUpdate doc failed." . mysqli_error($con) . " ";
                        var_dump($errMsg);//prints to console the error
                        writelog($errMsg);
			return false;
                }
		return true;
	}
		
	function docSearch($sType, $sVar, $con) 
	{
		echo "$sVar";
		if($sType=="byLastName")
			$q="select firstName,lastName,specialization, phone,email,location,gender,rating,review from Doctors where lastName='$sVar';";
	//first name, last name, specialization, telephone, email, address, gender, rating, review, 
		elseif($sType=="bySpec")
			{
			$q="select firstName,lastName,specialization, phone,email,location,gender,rating,review from Doctors where specialization='$sVar';";
			} 
		else
			{
				$errMsg="Incorrect doctor search type\n";
				echo $errMsg;
				writelog($errMsg);
				return $errMsg;
			}
		var_dump($sVar);
		$query=mysqli_query($con, $q);
	
		if(!$query)
		{
			$errMsg = "\nDoctor search Query failure." . $query . "<br>" . mysqli_error($con);
                        var_dump($errMsg);//prints to console the error
			writelog($errMsg); 
		}
		
		while($r=mysqli_fetch_assoc($query))
			$searchRes[]=$r;
		writelog("doc search complete");
		return $searchRes;
	}

	function viewDoc($user, $con) 
	{
		//I think it's better if we use license number as the unique key for doctors since in rare cases doctors can have the same names -ben
		$query="SELECT firstName,lastName,license,gender,specialization,rating,review,email,phone,location FROM Doctors WHERE username='$user';";
		$viewRes=array();
		$result=mysqli_query($con,$query);
		if(!$result)
		{
			$errMsg = "Connection failure." . $query . "<br>" . mysqli_error($con);
			echo "Something went wrong. Try again later, or contact technical support";
			writelog($errMsg);
		}
		
		$row=mysqli_fetch_assoc($result);
		var_dump($row);
		return $row;
	}
	
	function updateRecords($user, $sVar, $changeCol, $changeVal, $con)
	{
		//added this new function to let Doc and Pat to update their information
		
		$q="select type from uAuth where user='$user';";
		$query=mysqli_query($con,$q);
		checkQ($query, "obtaining type ", $con);
		$r=mysqli_fetch_assoc($query);
		$accType=$r['type'];

		if($accType == "p")
		{		
			$query="UPDATE patientRecords SET $changeCol='$changeVal' where username='$sVar';";//dunno if I wrote that one correctly; sorry -ben
			$result=mysqli_query($con,$query);
		}
		if($accType == "d")
		{
			$query="UPDATE Doctors SET $changeCol='$changeVal' where email='$sVar';";//dunno if I wrote that one correctly; sorry -ben
			$result=mysqli_query($con,$query);
		}
		
		$operation="Attempting to update records: ";
		$update=checkQ($con, $operation, $result);
		return $update;
	}
	

	//checks the mysqli_query parameter to see if it executed. if not, writes a log with message $ftype 
	//denoting the current operation and the mysqli error from $mcon. returns true or false for 
	//successful query or failed query respectively
	
	function checkQ($query, $ftype, $mCon)
	{
		if(!$query) //mysqli_query() will return false if it failed. !$query will be true and execute the following
			{
				$log= $ftype . mysqli_error($mCon) . " ";
				writelog($log);
				return false;
			}
		$log= $ftype . " was successful";
		writelog($log);
		return true;
	}

	function writelog($logMsg) //todo: test a file times 
	{
                $file = fopen("/home/lou/working/log.txt","a");//opens the error log file for writing
                $time= date('m/d/Y h:i:s a', time()); //formats date/time of server
                $fmsg= "" . $time . ": " . $logMsg . "\n"; //formats the logmsg and 
                fwrite($file, $fmsg);//writes the error into the log file
                fclose($file);//closes the file
	}
	
	function viewPatInfo($user, $con)
	{
		echo  "\na".$user."b you have the satans motherfucker\n";
		//$q="SELECT firstName,lastName,email,age,height,weight,sex,diagnosis,drNote,doctor,prescription FROM patientRecords WHERE username='$user';";
		//remove pass from patient and doctor tables;
		$q="select firstName,lastName,email,age,height,weight,sex,diagnosis,drNote,doctor,prescription FROM patientRecords WHERE username='$user';";
		$query=mysqli_query($con, $q);
		if(!$query)
		{
			$errMsg= "Query failure." . $q . "<br>" . mysqli_error($con);
			echo $errMsg;
			echo "Failed to connect. Contact support.";
			writelog($errMsg);
		}
		$r=mysqli_fetch_assoc($query);
		var_dump($r);
		$doc=$r['doctor'];
		$q="select name FROM Doctors WHERE license='$doc';";
                $query=mysqli_query($con, $q);
                if(!$query)
                {
                        $errMsg= "Query failure." . mysqli_error($con);
                        echo $errMsg;
                        echo "Failed to connect. Contact support.";
                        writelog($errMsg);
		}
		$dName=mysqli_fetch_assoc($query);
		$r['doctor']=$dName['name'];
		return $r;
	}	

	function docViewPat($email, $con)
        {
                $q="select firstName,lastName,email,age,height,weight,sex,diagnosis,drNote,doctor,prescription,address FROM patientRecords WHERE email='$email';";
                $query=mysqli_query($con, $q);
                if(!$query)
                {
                        $errMsg= "Query failure." . $q . "<br>" . mysqli_error($con);
                        echo $errMsg;
                        echo "Failed to connect. Contact support.";
                        writelog($errMsg);
                }
                $r=mysqli_fetch_assoc($query);
                var_dump($r);
                $doc=$r['doctor'];
                $q="select name FROM Doctors WHERE license='$doc';";
                $query=mysqli_query($con, $q);
                if(!$query)
                {
                        $errMsg= "Query failure." . mysqli_error($con);
                        echo $errMsg;
                        echo "Failed to connect. Contact support.";
                        writelog($errMsg);
                }
                $dName=mysqli_fetch_assoc($query);
                $r['doctor']=$dName['name'];
                return $r;
        }



	function rateDoctor($user, $rating, $con)
	{
		//$q=mysqli_prepare("select doctor from patientRecords where username='?'");'
		$q="select doctor from patientRecords where username='$user'";
		$query=mysqli_query($con,$q);
		checkQ($query, "Rate doc: get doc info:", $con);
		$docR=mysqli_fetch_assoc($query);
		$lic=$docR['doctor'];
		
		$q="INSERT INTO rate (rateID, patient, license, rating) VALUES (DEFAULT, '$user', $lic, $rating);";
		$query=mysqli_query($con,$q);
		checkQ($query, "Rate doc: updating ratings", $con);
		
		$q="SELECT SUM(rating) FROM rate where license=$lic;";
		$query=mysqli_query($con,$q);
		checkQ($query, "Get sum ", $con);
		$sumR=mysqli_fetch_assoc($query);
		var_dump($sumR);
		$sum=$sumR['SUM(rating)'];
		
		$q="SELECT COUNT(*) FROM rate WHERE license=$lic;";
		$query=mysqli_query($con,$q);
		checkQ($query, "get count", $con);
		$countA=mysqli_fetch_assoc($query);
		var_dump($countA);
		$count=$countA['COUNT(*)'];
		$res=$sum/$count;
		
		$q="UPDATE Doctors SET rating=$res WHERE license=$lic;";
		$query=mysqli_query($con,$q);
		checkQ($query,"new rating",$con);
			
				
	}
?>
