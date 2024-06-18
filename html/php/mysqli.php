<?php	
	$BD['base']   = "coopex";
	$BD['user']   = "fernando";
	$BD['pass']   = "indioveio";
	$BD['server'] = "localhost";

	global $link; 
	$link = mysqli_connect($BD['server'], $BD['user'], $BD['pass'], $BD['base']);

	if (!$link) {
	    echo "Error: Unable to connect to MySQL." . PHP_EOL;
	    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	    exit;
	}
?>
