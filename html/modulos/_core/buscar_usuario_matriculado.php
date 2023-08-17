<?php session_start();
	if(isset($_SESSION['coopex']['usuario'])){
		if(isset($_SERVER['HTTP_REFERER'])){
			if(strlen($_GET['q']) >= 3){

				include "../../php/config.php";
				include "../../php/sqlsrv.php";

				$nome = $_GET['q'];
				$id_periodo_letivo = $_GET['id_periodo_letivo'];
				$id_curso = $_GET['id_curso'];

				//ACADEMICO MATRICULADO
				$sql = "SELECT DISTINCT TOP
							10 pes_id_pessoa AS id,
							pes_nm_pessoa AS text,
							'ALUNO' AS tipo,
							crs_nm_curso AS curso,
							usr_ds_nickname AS usuario,
							pef_cd_sexo AS sexo,
							CRS_ID_CURSO,
							tel_cd_ddd,
							tel_nu_telefone,
							pes_ds_email
						FROM
							academico..PEL_periodo_letivo,
							registro..PES_pessoa
							INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
							INNER JOIN academico..usr_usuario ON alu_id_pessoa = usr_id_pessoa
							INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
							INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
							INNER JOIN academico..COL_colegiado ON col_id_colegiado = crs_id_unidade
							INNER JOIN academico..pef_pessoa_fisica ON pef_id_pessoa = pes_id_pessoa
							INNER JOIN registro..TEL_telefone ON tel_id_pessoa = pes_id_pessoa
							INNER JOIN academico..PAC_pacote ON pac_id_turma_curso = rca_id_turma_curso 
						WHERE
							pel_id_periodo_letivo = '$id_periodo_letivo' 
							AND pes_nm_pessoa LIKE '%$nome%' 
							AND CRS_ID_CURSO = '$id_curso' 
							AND tel_ch_preferencial = 'S' 
							AND EXISTS (
							SELECT
								1 
							FROM
								financeiro..cta_contrato_academico,
								financeiro..ctr_contrato,
								financeiro..CPL_contrato_periodo_letivo 
							WHERE
								cta_id_contrato = ctr_id_contrato 
								AND ctr_id_cliente = rca_id_aluno 
								AND cpl_id_periodo_letivo = pel_id_periodo_letivo 
								AND cpl_id_contrato = cta_id_contrato 
							)";
				

				//ACADEMICO GERAL
				/*$sql = "SELECT DISTINCT TOP
							10 pes_id_pessoa AS id,
							pes_nm_pessoa AS text,
							'ALUNO' AS tipo,
							crs_nm_curso AS curso,
							usr_ds_nickname AS usuario,
							pef_cd_sexo AS sexo,
							CRS_ID_CURSO,
							tel_cd_ddd,
							tel_nu_telefone,
							pes_ds_email 
						FROM
							academico..PEL_periodo_letivo,
							registro..PES_pessoa
							INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
							INNER JOIN academico..usr_usuario ON alu_id_pessoa = usr_id_pessoa
							INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
							INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
							INNER JOIN academico..COL_colegiado ON col_id_colegiado = crs_id_unidade
							INNER JOIN academico..pef_pessoa_fisica ON pef_id_pessoa = pes_id_pessoa
							INNER JOIN registro..TEL_telefone ON tel_id_pessoa = pes_id_pessoa
							INNER JOIN academico..PAC_pacote ON pac_id_turma_curso = rca_id_turma_curso 
						WHERE
							pel_id_periodo_letivo = '$id_periodo_letivo' 
							AND pes_nm_pessoa LIKE '%$nome%' 
							AND CRS_ID_CURSO = '$id_curso' 
							AND tel_ch_preferencial = 'S' 
							";*/

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

						$aux = explode(" ", $row['text']);
						$nome_academico = ucfirst(strtolower($aux[0]));

						$_SESSION['ficha_financeira']['whatsapp'] = "(".trim($row['tel_cd_ddd']).") ".trim($row['tel_nu_telefone']);
						$_SESSION['ficha_financeira']['email'] = $row['pes_ds_email'];
						$_SESSION['ficha_financeira']['nome_academico'] = $nome_academico;

					
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