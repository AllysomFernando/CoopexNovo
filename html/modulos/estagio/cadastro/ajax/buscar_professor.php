<?php session_start();
	if(isset($_SESSION['coopex']['usuario'])){
		if(isset($_SERVER['HTTP_REFERER'])){
			if(strlen($_GET['q']) >= 3){

				include "../../../../php/config.php";
				include "../../../../php/sqlsrv.php";

				$nome = $_GET['q'];

				$sql = "SELECT TOP(10)
						id_pessoa as id,
						nome as text,
						nome as nome,
						a.tipo,
						curso,
						usuario,
						sexo
					FROM
						integracao..view_integracao_usuario a
						LEFT JOIN integracao..view_integracao_cursos b ON ( a.id_curso = b.id_curso )
					WHERE
						nome LIKE '%$nome%'
            AND
            a.tipo = 'PROFESSOR'
						ORDER BY a.nome
            ";
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
?>