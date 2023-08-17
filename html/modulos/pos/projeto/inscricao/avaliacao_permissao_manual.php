<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../../php/mysql.php");
require_once("../../../php/utils.php");

$sql = "SELECT
			id_pessoa,
			id_reoferta 
		FROM
			coopex_reoferta.pre_matricula";
$res = $coopex->query($sql);
while($row = $res->fetch(PDO::FETCH_OBJ)){
	//echo $row->id_pessoa;
	avaliacao_reoferta($row->id_pessoa, $row->id_reoferta);
}

//avaliacao_reoferta(1000186848, 771);


//

?>