<!DOCTYPE html>

<html>
<body>

<h1>Quick Help Doctor</h1>
<h2>Doctor Register Page</h2>

<form action="dRegister.php" method="post">
        Username: <input type = "text" name = "username" required><br>
        Password: <input type = "password" name="password" required><br>
        Retype Password: <input type="password" name="rePass" required><br>
        E-mail: <input type = "email" name = "email" required><br>
        Telephone No: <input type = "tel" name = "telNo"><br>
        First Name: <input type = "text" name="fName" required><br>
        Last Name: <input type = "text" name="lName" required><br>
        License No.: <input type = "text" name="lNo" required><br>
        Specialization: <textarea type = "text" rows = "4" cols = "50" name = "spec" placeholder = "what was your area of expertise in?" required></textarea><br>
        Address of Office: <textarea type = "text" rows = "2" cols = "50" name = "address" required></textarea><br>
        <select name = "sex" required>
        Sex: <option value = "" selected>Select Your Sex</option>
             <option value = "m" name = "m">Male</option>
             <option value = "f" name = "f">Female</option>
        </select><br>
        <input type="submit" value="Register!">
</form><br>
<form action="pRegister.php">
        <input type = "submit" value="Patient Register">
</form><br>
<form action="login.html">
	<input type = "submit" value="Login">
</form><br>
<script>
<?php
        require_once('rabbitMQLib.inc');

        $client = new rabbitMQClient("testRabbitMQ.ini","testServer");
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $rePass = $_POST['rePass'];
        $eMail = $_POST['email'];
        $telNum = $_POST['telNo'];
        $fName = $_POST['fName'];
        $lName = $_POST['lName'];
        $lNo = $_POST['lNo'];
        $spec = $_POST['spec'];
        $address = $_POST['address'];
        $sex = $_POST['sex'];
        $func = "dRegister";

        if($pass !== $rePass)
        {
                header("refresh 1; dRegister.php");
                $msg = "Passwords do not match!";
		die($msg);
        }
	else
	{
        	$dData = array('user'=>$user, 'pass'=>$pass, 'email'=>$eMail, 'fName'=>$fName, 'lName'=>$lName, 'telNo'=>$telNum, 'lNo'=>$lNo, 'spec'=>$spec, 'address'=>$address, 'sex'=>$sex, 'func'=>$func);
        	$client->send_request($dData);
        	header("refresh 1; login.html");
	}
?>
</script>
</body>
</html>

