<!--

	/*require_once('rabbitMQLib.inc');

	$client = new rabbitMQClient('testRabbitMQ.ini','testServer');
	
	$searchVar = parse_str($_POST['search']);

	function receiveResults($results)
	{
		$msg = var_dump($results);
		return $msg;
	}

	$server = new rabbitMQServer('testRabbitMQ.ini','testServer');
	$server->proccess_request('receiveResults');
?>*/-->

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
* {box-sizing: border-box;}

body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.topnav {
  overflow: hidden;
  background-color: #e9e9e9;
}

.topnav a {
  float: left;
  display: block;
  color: black;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.topnav a.active {
  background-color: #2196F3;
  color: white;
}

.topnav .search-container {
  float: right;
}

.topnav input[type=text] {
  padding: 6px;
  margin-top: 8px;
  font-size: 17px;
  border: none;
}

.topnav .search-container button {
  float: right;
  padding: 6px 10px;
  margin-top: 8px;
  margin-right: 16px;
  background: #ddd;
  font-size: 17px;
  border: none;
  cursor: pointer;
}

.topnav .search-container button:hover {
  background: #ccc;
}

@media screen and (max-width: 600px) {
  .topnav .search-container {
    float: none;
  }
  .topnav a, .topnav input[type=text], .topnav .search-container button {
    float: none;
    display: block;
    text-align: left;
    width: 100%;
    margin: 0;
    padding: 14px;
  }
  .topnav input[type=text] {
    border: 1px solid #ccc;  
  }
}
</style>
</head>
<body>

<div class="topnav">
  <a class="active" href="patientPortal.html">Home</a>
  <a href="#about">About</a>
  <a href="#contact">Contact</a>
  <div class="search-container">
    <form action="dsearch.php">
      <input type="text" placeholder="Search for Doctors.." name="search">
      <button type="submit"><i class="fa fa-search"></i></button>
    </form>
        <select name = "searchType">
           <option value="" selected>Search All</option>
           <option value="byFName" name="byFName">by First Name</option>
           <option value="byLName" name="byLName">by Last Name</option>
           <option value="bySpec" name="bySpec">by Specialization</option>
        </select>
  </div>
</div>
<table frame = "box" style="width:100%">
<caption>List of Doctors</caption><br.
<thead>
	<tr>
		<th>Doctor Name</th>
		<th>Doctor Last name</th>
		<th>Sex<th>
		<th>Specialization</th>
		<th>Email</th>
		<th>Telephone Number</th>
		<th>Office Address</th>
		<th>Add Doctor</th>
	</tr>
</thead>

</body>
<script>			
</script>
</html>
