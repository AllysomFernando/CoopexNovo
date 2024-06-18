<?php
	require_once("../../../../../php/config.php");
	require_once("../../../../../php/mysql.php");
	require_once("../../../../../php/utils.php");

	$id_pessoa = $_POST['id_pessoa'];
	$observacao = $_POST['observacao'];

	$sql = "REPLACE INTO tesouraria.observacao (id_pessoa, observacao) VALUES ($id_pessoa, '$observacao')";
	$res = $coopex->query($sql);

	gravarLog('tesouraria.observacao', $id_pessoa, 1, $sql, json_encode($_POST));
?>
<script type="text/javascript">
	
	alert("Observação salva");

</script>