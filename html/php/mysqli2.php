<?php	
	$BD['base']   = "coopex";
	$BD['user']   = "fernando";
	$BD['pass']   = "indioveio";
	$BD['server'] = "host=localhost";

	$coopex = new PDO("mysql:dbname=coopex;host=localhost", 'fernando', 'indioveio');

	global $link; 
	$link = mysqli_connect($BD['server'], $BD['user'], $BD['pass'], $BD['base']);

	if (!$link) {
	    echo "Error: Unable to connect to MySQL." . PHP_EOL;
	    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	    exit;
	}
?>
