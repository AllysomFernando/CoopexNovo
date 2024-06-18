<?php	
    $BD_MS['base']   = "integracao";
	$BD_MS['user']   = "integracao";
	$BD_MS['pass']   = "FAGintegracao20anos";
	$BD_MS['server'] = "10.0.0.150:49320";


    $id3 =  mssql_connect($BD_MS['server'], $BD_MS['user'], $BD_MS['pass']);

    if(!$id3){
	echo "Não foi possível estabelecer uma conexão com o gerenciador mssql. Favor contactar o administrador.";
	exit;
    }

    $con3 = mssql_select_db($BD_MS['base'], $id3);
    if(!$con3){
	echo "Não foi possível estabelecer uma conexão com o gerenciador mssql. Favor contactar o administrador.";
	exit;
    }	
?>