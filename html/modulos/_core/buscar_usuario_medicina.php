<?php session_start();
	if(isset($_SESSION['coopex']['usuario'])){
		if(isset($_SERVER['HTTP_REFERER'])){
			if(strlen($_GET['q']) >= 3){

				include "../../php/config.php";
				include "../../php/mysql.php";

				$nome = $_GET['q'];

				$sql = "SELECT
							id_professor as id, nome as text, 'Professor' as tipo, periodo as usuario,
							'Medicina' as tipo_descricao
						FROM
							medicina.professor
						INNER JOIN medicina.periodo	USING (id_periodo)
						WHERE
							nome LIKE '%$nome%'";
				$res = $coopex->query($sql);
				
				$json = '{
				  "total_count": '. $res->rowCount();
				
				if($res->rowCount()){
					$json .= ',
					  "incomplete_results": false,
					  "items":';
				} else {
					$json .= ',
					  "incomplete_results": false}';
				}
				
				if($res->rowCount()){
					while($row = $res->fetch(PDO::FETCH_ASSOC)){
						$result[] = array_map("utf8_encode", $row);

					}
				}
				
				if($res->rowCount()){
					$json .= json_encode($result)."}";
				}
				echo $json;
			}
		}
	}
?>