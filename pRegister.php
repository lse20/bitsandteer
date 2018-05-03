<!DOCTYPE html>

<html>
<body>

<h1>Quick Help Doctor</h1>
<h2>Patient Register Page</h2>

<form action ="pRegister.php" method="post">
        Username:  <input type = "text" name="username"  required><br>
        Password:  <input type = "password" name="password" required><br>
        Retype Password:  <input type = "password" name="rePass" required><br>
        Email: <input type = "email" name="email" required><br>
        First Name: <input type = "text" name = "fName" required><br>
        Last Name: <input type = "text" name = "lName" required><br>
        Age: <input type = "number" name = "age"><br>
        Height (cm): <input type = "number" name = "height" required><br>
        Weight (kg): <input type = "number" name = "weight" required><br>
        Medical History: <textarea rows="4" cols"50" type = "text" name = "mHist" placeholder = "past medical history"></textarea><br>
        <select name = "sex" required>
        Sex: <option value="" selected>Select Your Sex</option>
	     <option value="m" name = "m">Male</option>
             <option value="f" name = "f">Female</option>
        </select><br>
	<input type = "submit" value="Register!">
</form><br>
<form action="dRegister.php">
	<input type = "submit" value="Doctor Register">
</form>
<form action="login.html">
	<input type = "submit" value="Log In">
</form>
<script>

<?php
	require_once('rabbitMQLib.inc');

	$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
	$user = $_POST['username'];
	$pass = $_POST['password'];
	$rePass = $_POST['rePass'];
	$eMail = $_POST['email'];
	$fName = $_POST['fName'];
	$lName = $_POST['lName'];
	$age = $_POST['age'];
	$hght = $_POST['height'];
	$weight = $_POST['weight'];
	$mHist = $_POST['mHist'];
	$sex = $_POST['sex'];
	$func = "pRegister";

	if($pass !== $rePass)
	{
		header("refresh 1; pRegister.php");
		$msg = "Passwords do not match!";
		die($msg);
	}
	else
	{
		$pData = array('user'=>$user, 'pass'=>$pass,'email'=>$eMail, 'fName'=>$fName, 'lName'=>$lName, 'age'=>$age, 'height'=>$hght, 'weight'=>$weight, 'mhist'=>$mHist, 'sex'=>$sex, 'func'=>$func);
		$client->send_request($pData);
		header("refresh 1; login.html");
	}
?>
</script>
</body>
</html>

