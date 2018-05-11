<?php

	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');
	require_once('listenerFunctions.php');
	
	function versionSwitch($msg)
	{
		extract(parse_ini_file('dbVar.ini'));
		$PASSWORD = "$mypass";
		$function = $msg['function'];
		switch($function)
		{
			case "promoteWebQA":
				var_dump($msg);		
				$path = "/home/lou/working/versioncontrol";
				$name = $msg['name'] . ".tar";
				var_dump($name);
				$destination = ":/var/www/html/web/rmqp/htmlPart";
				$ipaddress = "ben2@192.168.1.107";
				pushVersion($name, $path, $destination, $ipaddress);
				//add a line of code here that adds $name to the database and the path
				$con=mysqli_connect($dbAddr, $dbUser, $dbPass, $db);
				if(mysqli_connect_error())
				{
					writelog('error connecting to db');
					return false;
				}
				$q='SELECT ver FROM vCon ORDER BY id DESC LIMIT 1;';
				$query=mysqli_query($con,$q);
				{
					writelog('version select sql error');
					return false;
				}
				extract(mysqli_fetch_assoc($query));
				$ver=$ver+1;
				$query=mysqli_query($con,$q);
				$q="INSERT INTO vCon (name, path, status,ver) VALUES ('$name', '$path','good',$ver);";
				$query=mysqli_query($con,$q);
				if(!$query)
				{
					writelog('sql update error');
					return false;
				}
				
				break;
			case "promoteAPIQA":
				$path = "/home/lou/working/versioncontrol/APIVersion";
				$name = $msg['name']. ".tar";
				$destination = ":home/username/public";
				$ipaddress = "user@IPADDRESS";
				pushVersion($name, $path, $destination, $ipaddress);
				break;
			case "rollBack":
				return rollback($msg['defVer'], $msg['ipaddress'], $msg['username'], $msg['destination']);
			default:
				echo "not a valid input.";
		}
	}

	function pushVersion($name, $path, $destination, $ipaddress)
	{
		echo "$ipaddress \n";
		$str="sudo scp " . $path. "/$name". " $ipaddress" . $destination;
		echo $str . "\n";
		shell_exec("echo 'toor' | $str ");
		$info = array('function'=>'promoteWebQA');
		$client = new rabbitMQClient('webSideRMQP.ini','version_push');
		$client->publish($info);
	}
	
	function rollback($defective, $ipaddress, $username, $destination)
	{
		//defective==name
		//path==dest or ip
		//status=always bad
		writelog('Defective version detected, initiating rollback');
		//add a code here that marks $defective (which is the name of the defective version) bad in the database;
		extract(parse_ini_file('dbVar.ini'));
		$con=mysqli_connect($dbAddr, $dbUser, $dbPass, $db);
		
		if(mysqli_connect_error())
		{
			writelog('Failed to connect to db');
			return false;
		}
		$q="SELECT name,ver FROM vCon WHERE name='$defective';";
		$query=mysqli_query($con,$q);
		extract($mysqli_fetch_assoc($con,$q));
		if(!$query)
		{
			writelog('No such version on record, creating version name');
			return false;
		}
		$q="UPDATE vCon SET status='bad' WHERE name='$defective';";
		$query=mysqli_query($con,$q);
		if(!$query)
		{
			writelog('status setting sql error');
			return false;
		}
		//add a code here that searches the database and pulls the name of the latest non defective version and set it to $name and set $path the path;
		$ver=$ver-1;
		$q="SELECT name,path FROM vCon WHERE ver='$ver';";
		shell_exec("echo $mypass | sudo scp ".$path."/$name"." $ipaddress".$destination);
		return true;
	}

	$server = new rabbitMQServer('webSideRMQP.ini','version_control');
	$server->process_requests('versionSwitch');
?>
