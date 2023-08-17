<?php
	require_once("php/sqlsrv.php");

	$id_menu = 24;
	$chave	 = "id_reoferta";

	$id_pessoa = 1000164981;
	$_GET['id'] = 645;
	$id_reoferta = 645;

	$sql = "SELECT
				DATE_FORMAT( a.pre_inscricao_data_inicial, '%d/%m/%Y' ) AS pre_inscricao_data_inicial,
				DATE_FORMAT( a.pre_inscricao_data_final, '%d/%m/%Y' ) AS pre_inscricao_data_final,
				DATE_FORMAT( a.inscricao_data_inicial, '%d/%m/%Y' ) AS inscricao_data_inicial,
				DATE_FORMAT( a.inscricao_data_final, '%d/%m/%Y' ) AS inscricao_data_final,
				departamento,
				disciplina,
				nome,
				carga_horaria,
				local,
				SPLIT_STRING ( disciplina, ' ', 1 ) AS codigo_disciplina,
				periodo_letivo,
				reoferta_minimo,
				date(now( )) BETWEEN a.pre_inscricao_data_inicial 
				AND a.pre_inscricao_data_final AS intervalo_prematricula,
				date(now( )) BETWEEN a.inscricao_data_inicial 
				AND a.inscricao_data_final AS intervalo_matricula 
			FROM
				coopex_reoferta.reoferta a
				INNER JOIN coopex_reoferta.carga_horaria USING ( id_carga_horaria )
				INNER JOIN coopex_reoferta.periodo USING ( id_periodo )
				INNER JOIN coopex.departamento USING ( id_departamento )
				LEFT JOIN coopex.pessoa ON coopex.pessoa.id_pessoa = a.id_docente 
			WHERE
				id_reoferta = ".$id_reoferta;
			$res = $coopex->query($sql);
			$reoferta = $res->fetch(PDO::FETCH_OBJ);
		


	
	#VERIFICA AS DATAS DE POSSÍVEIS CHOQUES DE HORÁRIO ENTRE REOFERTAS
	$tempo = [];
	$sql = "SELECT
				* 
			FROM
				coopex_reoferta.cronograma 
			WHERE
				id_reoferta = $id_reoferta 
			ORDER BY
				data_reoferta";
	$res_cronograma = $coopex->query($sql);

	if($res_cronograma->rowCount()){
		echo $sql = "SELECT
					cronograma.* 
				FROM
					coopex_reoferta.matricula
					INNER JOIN coopex_reoferta.cronograma USING ( id_reoferta ) 
				WHERE
					id_pessoa = $id_pessoa 
					AND id_reoferta <> $id_reoferta 
				ORDER BY
					data_reoferta";
		$res_cronograma_matricula = $coopex->query($sql);
		if($res_cronograma_matricula->rowCount()){
			
			while($cronograma = $res_cronograma->fetch(PDO::FETCH_OBJ)){
				$sql = "SELECT
							cronograma.* 
						FROM
							coopex_reoferta.matricula
							INNER JOIN coopex_reoferta.cronograma USING ( id_reoferta ) 
						WHERE
							id_pessoa = $id_pessoa 
							AND id_reoferta <> $id_reoferta
							AND data_reoferta = '$cronograma->data_reoferta'
						ORDER BY
							data_reoferta";
				$res_cronograma_matricula = $coopex->query($sql);
				
				$res2 = $coopex->query($sql);
				$data_reoferta = $res2->fetch(PDO::FETCH_OBJ);


				//print_r($data_reoferta);

				if((strtotime(@$data_reoferta->horario_inicio) >= strtotime(@$cronograma->horario_termino))){
									
				} else if((strtotime(@$data_reoferta->horario_termino) <= strtotime(@$cronograma->horario_inicio))){
					
				} else if((strtotime($data_reoferta->horario_inicio) >= strtotime($cronograma->horario_inicio)) && 
				  (strtotime($data_reoferta->horario_termino) <= strtotime($cronograma->horario_termino))){
					
					$total = gmdate('H:i:s', strtotime( $data_reoferta->horario_termino ) - strtotime($data_reoferta->horario_inicio ));
					$tempo[] = $total;
				} else if((strtotime($data_reoferta->horario_inicio) <= strtotime($cronograma->horario_inicio)) && 
				  		 (strtotime($data_reoferta->horario_termino) <= strtotime($cronograma->horario_termino))){
					
					$total = gmdate('H:i:s', strtotime( $data_reoferta->horario_termino ) - strtotime($cronograma->horario_inicio ));
					$tempo[] = $total;
				} else if((strtotime($data_reoferta->horario_inicio) <= strtotime($cronograma->horario_inicio)) && 
				  		 (strtotime($data_reoferta->horario_termino) >= strtotime($cronograma->horario_termino))){
					
					$total = gmdate('H:i:s', strtotime( $cronograma->horario_termino ) - strtotime($cronograma->horario_inicio ));
					$tempo[] = $total;
				} else if((strtotime($data_reoferta->horario_inicio) >= strtotime($cronograma->horario_inicio)) && 
				  		 (strtotime($data_reoferta->horario_termino) >= strtotime($cronograma->horario_termino))){
					
					$total = gmdate('H:i:s', strtotime( $cronograma->horario_termino ) - strtotime($data_reoferta->horario_inicio ));
					$tempo[] = $total;
				} 

			}
print_r($tempo);
			$segundos = 0;

			foreach ( $tempo as $temp ){
				list( $h, $m, $s ) = explode( ':', $temp );

				$segundos += $h * 3600;
				$segundos += $m * 60;
				$segundos += $s;
			}
			echo "Choque -> ".$choque_de_horario_tempo = $segundos * 100 / ($reoferta->carga_horaria * 60 * 60);

			if($choque_de_horario_tempo<=26){
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `choque_de_horario` = 1 WHERE
							id_reoferta = ".$id_reoferta." 
							AND id_pessoa = ".$id_pessoa;
				//$coopex->query($sql);
			} else {
				$sql = "SELECT
							id_pre_matricula 
						FROM
							coopex_reoferta.matricula_autorizacao 
						WHERE
							id_autorizacao = 4 
							AND id_pre_matricula = ".$pre->id_pre_matricula;
				$matricula_autorizacao = $coopex->query($sql);
				if($matricula_autorizacao->rowCount() == 0){
					$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `choque_de_horario` = 0 WHERE
							id_reoferta = ".$id_reoferta." 
							AND id_pessoa = ".$id_pessoa;
					//$coopex->query($sql);
					$permissao_para_matricula = false;
				}
			}

		} else {
			echo "sem choque";
		}
	}
		

?>	