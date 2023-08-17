<?php session_start();
	if(isset($_SESSION['coopex']['usuario'])){
		//if(isset($_SERVER['HTTP_REFERER'])){
		if(1==1){	
			if(strlen($_GET['q']) >= 3){

				include "../../php/config.php";
				include "../../php/mysql.php"; 

				$nome = $_GET['q'];

				echo $sql = "SELECT
							id_pessoa AS id,
							nome AS text,
							nome AS nome,
							'Público Externo' AS tipo 
						FROM
							coopex_usuario.evento_pessoa 
						WHERE
							nome LIKE '%$nome%' 
							AND id_usuario = 0 
						ORDER BY
							nome 
							LIMIT 10";
				$res = mssql_query($sql);
				
				$json = '{
				  "total_count": '.mssql_num_rows($res);
				
				if(mssql_num_rows($res)){
					$json .= ',
					  "incomplete_results": false,
					  "items":';
				} else {
					$json .= ',
					  "incomplete_results": false}';
				}
				
				if(mssql_num_rows($res) > 0){
					while($row = mssql_fetch_assoc($res)){
						
						$aux = explode(" - ", $row['curso']);
						//echo count($aux);
						if(count($aux)){
							if(isset($aux[1])){
								$row['curso'] = $aux[1];
							}
						}
						
						$row['usuario'] = trim($row['usuario']);
						
						if($row['tipo'] == "ALUNO"){
							$row['tipo_descricao'] = $row['sexo'] == "M" ? "ALUNO" : "ALUNA";
							$row['tipo_descricao'] .= $row['curso'] ? " - ".$row['curso'] : " ESPECIAL";
						} else if($row['tipo'] == "PROFESSOR"){
							$row['tipo_descricao'] = $row['sexo'] == "M" ? "PROFESSOR" : "PROFESSORA";
						} else if($row['tipo'] == "TECNICO"){
							$row['tipo_descricao'] = $row['sexo'] == "M" ? "COLABORADOR" : "COLABORADORA";
						}
						
						$row['sexo'] = strtolower($row['sexo']);
						
						$result[] = array_map("utf8_encode", $row);
					}
				}
				
				if(mssql_num_rows($res)){
					$json .= json_encode($result)."}";
				}
				echo $json;
			}
		}
	}
