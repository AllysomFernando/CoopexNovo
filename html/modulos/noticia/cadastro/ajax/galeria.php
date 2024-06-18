<pre>
<?php session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	function load_json($arquivo){
        $json = file_get_contents("$arquivo.json");
        return $data = json_decode($json);
    }

    $inicio = 18;


    $limite = $inicio * 500;

	echo $sql = "SELECT
				*
			FROM
				noticia_2022
			WHERE
				id_noticia > 352842
			LIMIT $limite,
			 500";
	$res = $coopex->query($sql);

	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$noticia = load_json("../../../../../../json/noticias/$row->id_noticia");
		foreach ($noticia->galeria as $key => $galeria) {
			print_r($galeria);
			$sql = "INSERT INTO `noticia_galeria`(`id_noticia`, `imagem`, `legenda`, `credito`)
					VALUES ($row->id_noticia, '$galeria->imagem', '$galeria->legenda', '$galeria->credito')";
			$coopex->query($sql);
		}
	}






	/*$variacao = (($valor_atual * 100) / $row->valor);
	$variacao = $variacao - 100;*/

	/*if($row->valor != $valor_atual){
		$sql = "INSERT INTO `catve_db`.`cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (1, $valor_atual, $variacao, now())";
		$coopex->query($sql);
	}*/
	


?>