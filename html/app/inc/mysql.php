<?php	
	global $coopex;
	global $coopex_antigo;
	global $google;

	try {

		$coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');
		$coopex_antigo = new PDO("mysql:dbname=coopex_usuario;host=10.0.0.33", 'fernando', 'jklp13SA');
		$google = new PDO("mysql:dbname=sis;host=sis.fag.edu.br", 'fernando', 'jklp13SA');
		$fagid360 = new PDO("mysql:dbname=fagid360;host=10.0.0.216", 'fernando', 'indioveio');

		// google
		//$db = new PDO("mysql:dbname=sis;host=35.199.113.214", 'fernando', 'jklp13SA');
		//$radius = new PDO("mysql:dbname=radius;host=177.53.200.253", 'fernando', 'indioveio');
		

		//$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//$radius->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$coopex->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$coopex_antigo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$google->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

	} catch (PDOException $e) {
		echo 'Exception -> ';
		var_dump($e->getMessage());
		exit;
	}
?>