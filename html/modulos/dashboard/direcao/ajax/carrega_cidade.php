
<?php session_start();
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$uf = $_GET['uf'];
	$vestibular = $_GET['vestibular'];

	//$_SESSION['ficha_financeira']['id_periodo_letivo'] = $id_periodo;

	$sql = "SELECT
				ds_cidade,
				count(*) AS total 
			FROM
				$vestibular.pessoa 
			WHERE
				sg_estado = '$uf'
			GROUP BY
				ds_cidade 
			ORDER BY
				total DESC";

	$res = $coopex_antigo->query($sql);
	while($row = $res->fetch(PDO::FETCH_OBJ)){
?>
	<tr>
		<td><?=utf8_encode($row->ds_cidade)?></td>
		<td><?=$row->total?></td>
	</tr>	
<?		
	}
?>