<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	require_once("php/sqlsrv.php");

	$id_menu = 24;
	$chave	 = "id_reoferta";

	$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];

	$id_reoferta = $_GET['id'];

	$dependente_disciplina = false;
	$matriculado_no_periodo = false;
	//$maximo_reofertas_periodo = false;
	$reofertas_cursadas_no_periodo = 0;
	$choque_de_horario = false;
	$choque_de_horario_tempo = 0;
	$pendencia_financeira = false;

	$permissao_para_matricula = true;

	if(isset($_GET['id'])){

		#CARREGA DADOS DA REOFERTA
		$sql = "SELECT
					DATE_FORMAT( a.pre_inscricao_data_inicial, '%d/%m/%Y' ) AS pre_inscricao_data_inicial,
					DATE_FORMAT( a.pre_inscricao_data_final, '%d/%m/%Y' ) AS pre_inscricao_data_final,
					DATE_FORMAT( a.inscricao_data_inicial, '%d/%m/%Y' ) AS inscricao_data_inicial,
					DATE_FORMAT( a.inscricao_data_final, '%d/%m/%Y' ) AS inscricao_data_final,
					date(a.pre_inscricao_data_final) > date(now()) as pre_matricula_expirada,
					date(a.inscricao_data_final) > date(now()) as matricula_expirada,
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
					id_reoferta = ".$_GET['id'];
		$res = $coopex->query($sql);
		$reoferta = $res->fetch(PDO::FETCH_OBJ);

		$periodo_letivo = $reoferta->periodo_letivo;

		#VERIFICA SE O ACADÊMICO ESTÁ PRÉ-MATRICULADO
		$sql = "SELECT
					*,
					DATE_FORMAT( data_pre_matricula, '%d/%m/%Y' ) AS data_pre_matricula,
					DATE_FORMAT( data_pagamento, '%d/%m/%Y' )	  AS data_pagamento
				FROM
					coopex_reoferta.pre_matricula 
				WHERE
					id_reoferta = ".$_GET['id']." 
					AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];

		$pre = $coopex->query($sql);
		$pre_matriculado = $pre->rowCount() ? true : false;





		#VERIFICA SE O ACADÊMICO ESTÁ MATRICULADO
		$sql = "SELECT
					*,
					DATE_FORMAT( data_matricula, '%d/%m/%Y' ) AS data_matricula,
					DATE_FORMAT( data_pagamento, '%d/%m/%Y' )	  AS data_pagamento
				FROM
					coopex_reoferta.matricula 
				WHERE
					id_reoferta = ".$_GET['id']." 
					AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];

		$matricula = $coopex->query($sql);
		$matriculado = $matricula->rowCount() ? true : false;

		if($matriculado){
			$matricula = $matricula->fetch(PDO::FETCH_OBJ);

			#VERIFICA SE JÁ EXISTE BOLETO GERADO PARA A MATRÍCULA
			$sql = "SELECT
				data_vencimento,
				data_emissao,
				DATE_FORMAT( data_vencimento, '%d/%m/%Y' ) 		AS data_vencimento_f,
				DATE_FORMAT( data_emissao, '%d/%m/%Y' ) 		AS data_emissao_f,
				valor
			FROM
				coopex_reoferta.matricula_boleto 
			WHERE
				id_matricula = ".$matricula->id_matricula." ORDER BY data_vencimento DESC";
			$boleto_matricula = $coopex->query($sql);
			if($boleto_matricula->rowCount()){
				$row_boleto_matricula = $boleto_matricula->fetch(PDO::FETCH_OBJ);
			}
		}


		#SE ESTIVER PRÉ-MATRICULADO
		if($pre_matriculado){
			$pre = $pre->fetch(PDO::FETCH_OBJ);

			if($pre->pago){
				$permissao_para_matricula = true;
			} else {
				$permissao_para_matricula = false;
			}

			#VERIFICA SE JÁ EXISTE BOLETO GERADO PARA A PRÉ-MATRÍCULA
			$sql = "SELECT
				data_vencimento,
				data_emissao,
				DATE_FORMAT( data_vencimento, '%d/%m/%Y' ) 		AS data_vencimento_f,
				DATE_FORMAT( data_emissao, '%d/%m/%Y' ) 		AS data_emissao_f,
				valor
			FROM
				coopex_reoferta.pre_matricula_boleto 
			WHERE
				id_pre_matricula = ".$pre->id_pre_matricula." ORDER BY data_vencimento DESC";
			$boleto = $coopex->query($sql);
			if($boleto->rowCount()){
				$row_boleto = $boleto->fetch(PDO::FETCH_OBJ);
			}


			#VERFIFICA SE O ALUNO TEM REPROVAÇÃO NA DISCIPLINA OU NAS EQUIVALENTES
			$sql = "SELECT
						id_disciplina
					FROM
						coopex_reoferta.disciplina_equivalente 
					WHERE
						id_reoferta = ".$_GET['id'];
			$res = $coopex->query($sql);
			$aux = [];
			while($equivalente = $res->fetch(PDO::FETCH_ASSOC)){
				$aux[] = trim($equivalente['id_disciplina']);
			}
			$equivalente = "'".implode("','", $aux)."'";
			$codigo_disciplina = "'".$reoferta->codigo_disciplina."'";
			
			$sql = "SELECT
						* 
					FROM
						academico..view_reofertas_reprovacoes rep 
					WHERE
						id_aluno = $id_pessoa 
						AND cd_disciplina IN ($codigo_disciplina,$equivalente)";
			$res = mssql_query($sql);
			if(mssql_num_rows($res)){
				$dependente_disciplina = true;
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `dependente_disciplina` = 1 WHERE
								id_reoferta = ".$_GET['id']." 
								AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
				$coopex->query($sql);
			} else {
				$permissao_para_matricula = false;
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `dependente_disciplina` = 0 WHERE
								id_reoferta = ".$_GET['id']." 
								AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
				#$coopex->query($sql);
			}
			#--VERFIFICA SE O ALUNO TEM REPROVAÇÃO NA DISCIPLINA OU NAS EQUIVALENTES

			#VERIFICA SE TEM MATRÍCULA NO PERÍODO
			$sql = "SELECT DISTINCT
						pell.pel_ds_compacta PERIODO_LETIVO,
						rca_id_aluno ID_ALUNO 
					FROM
						academico..RCA_registro_curso_aluno WITH ( NOLOCK ),
						academico..CRS_curso,
						academico..COL_colegiado,
						academico..PEL_periodo_letivo pell 
					WHERE
						col_id_colegiado = crs_id_unidade 
						AND crs_id_tp_curso IN ( '1000000002', '1000000003', '1000000004', '1100000001', '1100000002', '2100000006' ) 
						AND col_id_faculdade IN ( '1000000002', '1000000004', '1100000002' ) 
						AND crs_id_curso = rca_id_curso 
						AND rca_id_forma_saida IS NULL 
						AND rca_id_aluno = $id_pessoa 
						AND EXISTS (
						SELECT
							1 
						FROM
							academico..mtr_matricula,
							academico..STM_situacao_matricula 
						WHERE
							mtr_id_registro_curso = rca_id_registro_curso 
							AND mtr_id_situacao_matricula = stm_id_situacao 
							AND stm_st_situacao = 'C' 
						AND mtr_id_periodo_letivo = pell.pel_id_periodo_letivo 
						AND pell.pel_ds_compacta IN ( '$periodo_letivo' ))";
			$res = mssql_query($sql);
			if(mssql_num_rows($res)){
				$matriculado_no_periodo = true;
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `matriculado_no_periodo` = 1 WHERE
								id_reoferta = ".$_GET['id']." 
								AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
				$coopex->query($sql);
			} else {
				$permissao_para_matricula = false;
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `matriculado_no_periodo` = 0 WHERE
								id_reoferta = ".$_GET['id']." 
								AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
			}
			#--VERIFICA SE TEM MATRÍCULA NO PERÍODO


			#VERIFICA SE TEM PENDENCIA FINANCEIRA
			$sql = "SELECT
							*
						FROM
							financeiro..ttf_titulo_financeiro WITH ( NOLOCK ) 
						WHERE
							ttf_id_tipo_titulo NOT IN (1000000078,1000000075,5000000621,5000000625,1000000067,1000000066,1000000065,1000000064,1000000089,1000000088,1000000090,1000000091,1000000084,1000000352,1000000370,1000000063,1000000063,1000000073 ) 
							AND ttf_st_situacao = 'A' 
							AND ttf_id_cliente_fornecedor = $id_pessoa
 							AND ttf_dt_vencimento < getdate() - 1";
			$res = mssql_query($sql);
			if(mssql_num_rows($res)){
				$pendencia_financeira = true;
				$permissao_para_matricula = false;
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `pendencia_financeira` = 0 WHERE
								id_reoferta = ".$_GET['id']." 
								AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
				$coopex->query($sql);
			} else {
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `pendencia_financeira` = 1 WHERE
								id_reoferta = ".$_GET['id']." 
								AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
				$coopex->query($sql);
			}
			#--VERIFICA SE TEM MATRÍCULA NO PERÍODO


			#VERIFICA SE JÁ CURSOU MAIS DE 3 REOFERTAS NO PERÍODO LETIVO
			$sql = "SELECT
						id_matricula 
					FROM
						coopex_reoferta.matricula a
						INNER JOIN coopex_reoferta.reoferta USING ( id_reoferta )
						INNER JOIN coopex_reoferta.periodo USING ( id_periodo ) 
					WHERE
						pago = 1
						AND periodo_letivo = $periodo_letivo 
						AND a.id_pessoa = $id_pessoa";
			$res = $coopex->query($sql);
			$reofertas_cursadas_no_periodo = $res->rowCount();

			/*$sql = "SELECT
						* 
					FROM
						coopex_cascavel.reoferta_matricula
						INNER JOIN coopex_cascavel.reoferta USING ( id_reoferta )
						INNER JOIN coopex_cascavel.reoferta_periodo USING ( id_periodo ) 
					WHERE
						id_usuario = $id_pessoa 
						AND periodo = $periodo_letivo";
			$res = $coopex_antigo->query($sql);
			$reofertas_cursadas_no_periodo += $res->rowCount();	*/

			//$reofertas_cursadas_no_periodo = 0;
			// echo $reofertas_cursadas_no_periodo;		

			/*if($res->rowCount()){
				$maximo_reofertas_periodo = true;
			}*/
			if($reofertas_cursadas_no_periodo<3){
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `reofertas_cursadas_no_periodo` = 1 WHERE
								id_reoferta = ".$_GET['id']." 
								AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
				$coopex->query($sql);
			} else {
				$permissao_para_matricula = false;
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `reofertas_cursadas_no_periodo` = 0 WHERE
								id_reoferta = ".$_GET['id']." 
								AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
				$coopex->query($sql);

			}
			#--VERIFICA SE JÁ CURSOU MAIS DE 3 REOFERTAS NO PERÍODO LETIVO



			#VERIFICA AS DATAS DE POSSÍVEIS CHOQUES DE HORÁRIO ENTRE REOFERTAS
			$tempo = [];
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
					$sql = "SELECT
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

						//print_r($tempo);

						$segundos = 0;

						foreach ( $tempo as $temp ){
							list( $h, $m, $s ) = explode( ':', $temp );

							$segundos += $h * 3600;
							$segundos += $m * 60;
							$segundos += $s;
						}
						$choque_de_horario_tempo = $segundos * 100 / ($reoferta->carga_horaria * 60 * 60);

						if($choque_de_horario_tempo<=26){
							$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `choque_de_horario` = 1 WHERE
										id_reoferta = ".$id_reoferta." 
										AND id_pessoa = ".$id_pessoa;
							$coopex->query($sql);
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
								$coopex->query($sql);
								$permissao_para_matricula = false;
							}
						}

					} else {
						$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `choque_de_horario` = 1 WHERE
									id_reoferta = ".$id_reoferta." 
									AND id_pessoa = ".$id_pessoa;
						$coopex->query($sql);
					}
				}


			//echo ">".$permissao_para_matricula;
			if($permissao_para_matricula && $pre->pago){ 
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `permissao_matricula` = 1 WHERE
						id_reoferta = ".$_GET['id']." 
						AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
				$coopex->query($sql);
			} else {
				$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `permissao_matricula` = 0 WHERE
						id_reoferta = ".$_GET['id']." 
						AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
				#$coopex->query($sql);
			}
		}
	}



	//print_r($reoferta);
	//avaliacao_reoferta($id_pessoa, $_GET['id']);

	$sql = "SELECT * FROM coopex_reoferta.pre_matricula WHERE id_reoferta = ".$_GET['id']." AND id_pessoa = $id_pessoa";
	$res = $coopex->query($sql);
	$pre_autorizacao = $res->fetch(PDO::FETCH_OBJ);

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">

	<?php
		if(!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1])){
	?>
	<div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
		<div class="d-flex align-items-center">
			<div class="alert-icon">
				<span class="icon-stack icon-stack-md">
					<i class="base-7 icon-stack-3x color-danger-900"></i>
					<i class="fal fa-times icon-stack-1x text-white"></i>
				</span>
			</div>
			<div class="flex-1">
				<span class="h5 color-danger-900">Seu usuário não possui permissão para acessar esta tela</span>
			</div>
			<a href="javascript:solicitarPermissao()" class="btn btn-outline-danger btn-sm btn-w-m">Solicitar acesso</a>
		</div>
	</div>
	<?php		
			exit;
		}
	?>

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Reoferta</a></li>
		<li class="breadcrumb-item active">Inscrição</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Reofertas
			<small>
				Pré-matricula e Matrícula
			</small>
		</h1>
	</div>

	<?php
		if(isset($_GET['id'])){
	?>
	<div class="alert alert-primary">
		<div class="d-flex flex-start w-100">
			<div class="mr-2 hidden-md-down">
				<span class="icon-stack icon-stack-lg">
					<i class="base base-2 icon-stack-3x opacity-100 color-primary-500"></i>
					<i class="base base-2 icon-stack-2x opacity-100 color-primary-300"></i>
					<i class="fal fa-info icon-stack-1x opacity-100 color-white"></i>
				</span>
			</div>
			<div class="d-flex flex-fill">
				<div class="flex-fill">
					<span class="h5">TERMO DE ACEITE</span><br>
					Declaro que estou ciente:<br><br>
					<ul>
						<li>Conf. Art. 33, § 6º da Resolução nº 100/2019 - CEPEG - O aluno, que tiver a inscrição na Reoferta homologada e não realizar a matrícula da mesma estará impedido de cursar disciplina(s) em Reoferta pelo período de dois semestres letivos consecutivos.</li>
						<li>Não será feito ressarcimento em caso de desistência;</li>
						<li>Não haverá ressarcimentos de valores referente a pré-matrícula da reofera;</li>
						<li>Será cobrado o valor da diferença, caso a reoferta não obtiver o número mínimo de inscritos;</li>
						<li>O número máximo de reofertas permitidas no periodo letivo são de 03 (três);</li>
						<li>O boleto será gerado para a data final da matrícula;</li>
						<li>Não será aceito pagamento fora do prazo;</li>
						<li>A Instituição de Ensino poderá, eventualmente, autorizar matrículas extemporâneas. Nestas hipóteses, o valor da disciplina em regime de reoferta será idêntico ao dos acadêmicos matriculados regularmente, não havendo recalculo de valores (Art. 33, § 7º da Resolução nº 100/2019 - CEPEG).</li>
						<li>Não deve haver choque de horário entre Reofertas.</li><hr>
					</ul>
					<?php
						if($reoferta->intervalo_prematricula || $pre_matriculado){
					?>
					<div class="custom-control custom-checkbox">
						<input <?php echo $pre_matriculado ? 'checked="" disabled' : ''?>  type="checkbox" class="custom-control-input" id="termo_de_aceite" value="1" name="termo_de_aceite">
						<label class="custom-control-label" for="termo_de_aceite">Li e concordo com os termos</label>
					</div>
					<?php
						}
					?>	
					<br>
				</div>
			</div>
		</div>
	</div>
	<?php
		}
	?>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 100%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>
	<div class="row">
		<div class="col-xl-12">
		  	<div id="panel-2" class="panel">
			  	<div class="panel-hdr">
					<h2>
						1. Pré-matrícula
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content p-0">
						<div class="panel-content">
							<div class="form-row form-group">
								<div class="col-md-12 mb-3">
									<?php
										if(!$pre_matriculado){
									?>
									<div class="panel-tag">
										O valor da pré-matricula é de <strong>R$ 70,00</strong>.<br>
										A realização da pré-matricula não garante a matrícula na reoferta, para que seja possível realizar a matrícula devem ser atendidos todos os requisitos descritos no <strong>TERMO DE ACEITE</strong>.
									</div>
									<?php
										} else {
									?>
									<div class="panel-tag">
										Você realizou sua pré-matricula nesta reoferta em: <strong><?php echo $pre->data_pre_matricula;?></strong><br>
	                                <?php
	                                	if($boleto->rowCount()){
	                                		if($pre->pago){
	                                			echo "<h4 class='color-success-900'>Pagamento do boleto identificado em: <strong>".$pre->data_pagamento."</strong></h4>";
	                                		} else if($row_boleto->data_vencimento < date("Y-m-d")){
	                                			echo "<h4 class='color-danger-900'>Seu boleto emitido em: <strong>".$row_boleto->data_emissao_f."</strong> venceu em: <strong>".$row_boleto->data_vencimento_f."</strong></h4>";
	                                		} else {
												echo "Seu boleto emitido em: <strong>".$row_boleto->data_emissao_f."</strong> vencerá em: <strong>".$row_boleto->data_vencimento_f."</strong>";
	                                		}
	                                	} else {
											echo "Seu boleto ainda não foi gerado";
	                                	}
									?>    
	                                </div>
	                                <?php		
	                                	}
	                                ?>
                            	</div>

                            	<?php
									if(!$pre_matriculado){
								?>
								<div class="col-md-2 mb-3">
									<label class="form-label" for="validationCustom02">Inicio da Pré-matrícula </label>
									<input type="text" class="form-control form-control-lg" value="<?php echo ($reoferta->pre_inscricao_data_inicial);?>" readonly>
								</div>
								<div class="col-md-2 mb-3">
									<label class="form-label" for="validationCustom02">Fim da Pré-matrícula </label>
									<input  type="text" name="data_vencimento" class="form-control form-control-lg" value="<?php echo ($reoferta->pre_inscricao_data_final);?>" readonly>
								</div>
								<?php
									}
								?>
									<?php
										if($pre_matriculado){
											if($boleto->rowCount()){
												if(!$pre->pago){
									?>
									<a target="_blank" href="https://coopex.fag.edu.br/boleto/reoferta/prematricula/<?php echo $pre->id_pre_matricula?>">
										<div class="col mb-3">
											<label class="form-label" for="validationCustom02">&nbsp</label><br>
											<button  type="button" class="btn btn-lg btn-secondary waves-effect waves-themed" >
	                                            <span class="fal fa-print mr-1"></span>
	                                            Imprimir boleto
	                                        </button>
										</div>
									</a>
									<?php
												}
											} else {
									?>
									<a target="_blank" href="https://coopex.fag.edu.br/boleto/reoferta/prematricula/<?php echo $pre->id_pre_matricula?>">
										<div class="col mb-3">
											<label class="form-label" for="validationCustom02">&nbsp</label><br>
											<button  type="button" class="btn btn-lg btn-success waves-effect waves-themed" >
	                                            <span class="fal fa-print mr-1"></span>
	                                            Gerar Boleto
	                                        </button>
										</div>
									</a>
									<?php		
											}
										} else {
											if($reoferta->intervalo_prematricula){
									?>
									<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscricao/inscricao_dados_pre.php">
										<input type="hidden" name="id_reoferta" value="<?php echo $_GET['id']?>">
										<input  type="hidden" name="data_vencimento" class="form-control periodo_diferente" value="<?php echo ($reoferta->pre_inscricao_data_final);?>" >
										<input type="hidden" name="id_pessoa" value="<?php echo $_SESSION['coopex']['usuario']['id_pessoa']?>">
										<div class="col mb-3">
											<label class="form-label" for="validationCustom02">&nbsp</label><br>
											<button id="botao_pre_matricula" type="submit" class="btn btn-lg btn-primary waves-effect waves-themed" disabled="">
	                                            <span class="fal fa-lock mr-1"></span>
	                                            Pré-matricular
	                                        </button>
										</div>
									</form>	
									<?php
											} else {
												$frase = $reoferta->pre_matricula_expirada == 0 ? "Prazo de pré-matricula expirado" : "Aguardando abertura do período de pré-matrícula";

									?>
										<div class="col mb-3">
											<label class="form-label" for="validationCustom02">&nbsp</label><br>
											<button id="botao_pre_matricula" type="submit" class="btn btn-lg btn-primary waves-effect waves-themed" disabled="">
	                                            <span class="fal fa-lock mr-1"></span>
	                                           <?php echo $frase?>
	                                        </button>
										</div>
									<?php			
											}		
										}
									?>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>



	<div class="row">
		<div class="col-xl-12">
		  	<div id="panel-2" class="panel">
			  	<div class="panel-hdr">
					<h2>
						2. Matrícula
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content p-0">
						<div class="panel-content">
							<?php
								if(!$matriculado){
							?>
							<div class="form-row form-group">
								<div class="col-md-12 mb-3">
									<?php
										if(isset($pre->pago) && $pre->pago == 0){
									?>
									<div class="panel-tag">
										A realização da matrícula está condicionada a quitação do valor da pré-matricula e do cumprimento dos requisitos descritos no <strong>TERMO DE ACEITE</strong>.
									</div>
									<?php
										} else if(isset($pre->pago) && $pre->pago == 1){
									?>
									<div class="panel-tag">
										A realização da matrícula está condicionada ao cumprimento dos requisitos descritos no <strong>TERMO DE ACEITE</strong>.
									</div>
									<div class="border bg-light rounded-top">
                                        <div id="js_list_accordion" class="accordion accordion-hover accordion-clean js-list-filter">
                                            <?php
												if($pre->pago){
											?>
                                            <div class="card border-top-left-radius-0 border-top-right-radius-0">
                                                <div class="card-header">
                                                    <a class="card-title collapsed" >
                                                        <span class="color-success-900"><i class="fal colo fa-check width-2 fs-xl"></i>
                                                        Pagamento da Pré-matrícula (OK)</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                            	} else {
                                            		$permissao_para_matricula = false;
                                            	}
                                            	//$dependente_disciplina = false;
                                            	if($dependente_disciplina || $pre_autorizacao->dependente_disciplina){
                                            ?>
                                            <div class="card border-top-left-radius-0 border-top-right-radius-0">
                                                <div class="card-header">
                                                    <a class="card-title collapsed" >
                                                        <span class="color-success-900"><i class="fal colo fa-check width-2 fs-xl"></i>
                                                        Dependente da disciplina (OK)</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                            	} else {
                                            		$permissao_para_matricula = false;
                                            ?>
                                        	<div class="card">
                                                <div class="card-header">
                                                    <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#js_list_accordion-b" aria-expanded="false" data-filter-tags="merge">
                                                        <span class="color-danger-900"><i class="fal fa-times width-2 fs-xl"></i>
                                                        Não dependente da disciplina</span>
                                                        <span class="ml-auto">
                                                            <span class="collapsed-reveal">
                                                                <i class="fal fa-chevron-up fs-xl"></i>
                                                            </span>
                                                            <span class="collapsed-hidden">
                                                                <i class="fal fa-chevron-down fs-xl"></i>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </div>
                                                <div id="js_list_accordion-b" class="collapse" data-parent="#js_list_accordion-b" style="">
                                                    <div class="card-body">
                                                        Você não é dependente desta disciplina, neste caso é necessária autorização da coordenação do seu curso para realização da sua matrícula nesta reoferta.
                                                        <br><button data-toggle="modal" data-target="#modal" onclick="solicitar_revisao(1, 'Não dependente da disciplina')" id="btAutorizacao1" class="btn btn-warning pos-right mt-3">Solicitar Revisão</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php		
                                            	}
                                            ?>

                                            <?php	
                                            	//$matriculado_no_periodo = false;
                                            	if($matriculado_no_periodo || $pre_autorizacao->matriculado_no_periodo){
                                            ?>
                                            <div class="card border-top-left-radius-0 border-top-right-radius-0">
                                                <div class="card-header">
                                                    <a class="card-title collapsed" >
                                                        <span class="color-success-900"><i class="fal colo fa-check width-2 fs-xl"></i>
                                                        Matriculado no período (OK)</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                            	} else {
                                            		$permissao_para_matricula = false;
                                            ?>
                                        	<div class="card">
                                                <div class="card-header">
                                                    <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#js_list_accordion-c" aria-expanded="false" data-filter-tags="merge">
                                                        <span class="color-danger-900"><i class="fal fa-times width-2 fs-xl"></i>
                                                        Não matrículado no período de <?php echo $periodo_letivo;?></span>
                                                        <span class="ml-auto">
                                                            <span class="collapsed-reveal">
                                                                <i class="fal fa-chevron-up fs-xl"></i>
                                                            </span>
                                                            <span class="collapsed-hidden">
                                                                <i class="fal fa-chevron-down fs-xl"></i>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </div>
                                                <div id="js_list_accordion-c" class="collapse" data-parent="#js_list_accordion-c" style="">
                                                    <div class="card-body">
                                                        Você não está matriculado no período de <strong><?php echo $periodo_letivo;?></strong>, procure a secretaria acadêmica para verificar sua situação.
                                                        <br><button onclick="solicitar_revisao(2, 'Não matrículado no período')" id="btAutorizacao2" class="btn btn-warning pos-right mt-3">Solicitar Revisão</button> 
                                                    </div>
                                                </div>
                                            </div>
                                            <?php		
                                            	}
                                            ?>

                                           	<?php
                                            	//$reofertas_cursadas_no_periodo = 5;
                                            	if($reofertas_cursadas_no_periodo<3 || $pre_autorizacao->reofertas_cursadas_no_periodo){
                                            ?>
                                            <div class="card border-top-left-radius-0 border-top-right-radius-0">
                                                <div class="card-header">
                                                    <a class="card-title collapsed" >
                                                        <span class="color-success-900"><i class="fal colo fa-check width-2 fs-xl"></i>
                                                        Número máximo de reofertas cursadas no período (OK)</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                            	} else {
                                            		$permissao_para_matricula = false;
                                            ?>
                                        	<div class="card">
                                                <div class="card-header">
                                                    <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#js_list_accordion-d" aria-expanded="false" data-filter-tags="merge">
                                                        <span class="color-danger-900"><i class="fal fa-times width-2 fs-xl"></i>
                                                        Número máximo de reofertas excedido no período letivo de <?php echo $periodo_letivo?></span>
                                                        <span class="ml-auto">
                                                            <span class="collapsed-reveal">
                                                                <i class="fal fa-chevron-up fs-xl"></i>
                                                            </span>
                                                            <span class="collapsed-hidden">
                                                                <i class="fal fa-chevron-down fs-xl"></i>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </div>
                                                <div id="js_list_accordion-d" class="collapse" data-parent="#js_list_accordion-d" style="">
                                                    <div class="card-body">
                                                        Você já possui matrícula em <strong><?php echo $reofertas_cursadas_no_periodo;?></strong> reofertas no período letivo de <strong><?php echo $periodo_letivo;?></strong>, procure a secretaria acadêmica para verificar sua situação.
                                                    </div>
                                                    <br><button onclick="solicitar_revisao(3, 'Número máximo de reofertas excedido no período letivo')" id="btAutorizacao3" class="btn btn-warning pos-right mt-3">Solicitar Revisão</button>
                                                </div>
                                            </div>
                                            <?php
                                            	}
                                            ?>


                                            <?php
                                             	//$choque_de_horario_tempo = 35.12;
                                            	if($choque_de_horario_tempo<=25 || $pre_autorizacao->choque_de_horario){
                                            ?>
                                            <div class="card border-top-left-radius-0 border-top-right-radius-0">
                                                <div class="card-header">
                                                    <a class="card-title collapsed" >
														<span class="color-success-900"><i class="fal colo fa-check width-2 fs-xl"></i>
															Choque de horário (OK)</span>
													</a>
												</div>
                                            </div>
                                            <?php
                                            	} else {
                                            		$permissao_para_matricula = false;
                                            ?>
                                        	<div class="card">
                                                <div class="card-header">
                                                    <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#js_list_accordion-e" aria-expanded="false" data-filter-tags="merge">
                                                        <span class="color-danger-900"><i class="fal fa-times width-2 fs-xl"></i>
                                                        <?php echo number_format($choque_de_horario_tempo, 2, '.', '');?>% de choque de horário com outras reofertas</span>

                                                        <span class="ml-auto">
                                                            <span class="collapsed-reveal">

                                                                <i class="fal fa-chevron-up fs-xl"></i>
                                                            </span>
                                                            <span class="collapsed-hidden">
                                                                <i class="fal fa-chevron-down fs-xl"></i>
                                                            </span>
                                                        </span>

                                                    </a>
                                                </div>
                                                <div id="js_list_accordion-e" class="collapse" data-parent="#js_list_accordion-e" style="">
                                                    <div class="card-body">
                                                        Esta reoferta possui choque de horário com outras reofertas que você está matriculado, o máximo de choque de horário permitido é de <strong>25%</strong>, este reoferta possui <strong><?php echo number_format($choque_de_horario_tempo, 2, '.', '');?>%</strong> de choque de horário.<br>É necessária autorização da coordenação para matricular nesta reoferta.
                                                        <br><button onclick="solicitar_revisao(4, 'Choque de Horário')" id="btAutorizacao4" class="btn btn-warning pos-right mt-3">Solicitar Revisão</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php		
                                            	}
                                            ?>



                                            <?php
                                            	if(!$pendencia_financeira || $pre_autorizacao->pendencia_financeira){
                                            ?>
                                            <div class="card border-top-left-radius-0 border-top-right-radius-0">
                                                <div class="card-header">
                                                    <a class="card-title collapsed" >
                                                        <span class="color-success-900"><i class="fal colo fa-check width-2 fs-xl"></i>
                                                        Pendência financeira (OK)</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                            	} else {
                                            		$permissao_para_matricula = false;
                                            ?>
                                        	<div class="card">
                                                <div class="card-header">
                                                    <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#js_list_accordion-f" aria-expanded="false" data-filter-tags="merge">
                                                        <span class="color-danger-900"><i class="fal fa-times width-2 fs-xl"></i>
                                                        Pendência financeira</span>
                                                        <span class="ml-auto">
                                                            <span class="collapsed-reveal">
                                                                <i class="fal fa-chevron-up fs-xl"></i>
                                                            </span>
                                                            <span class="collapsed-hidden">
                                                                <i class="fal fa-chevron-down fs-xl"></i>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </div>
                                                <div id="js_list_accordion-f" class="collapse" data-parent="#js_list_accordion-f" style="">
                                                    <div class="card-body">
                                                        Você possui pendências financeiras que impedem a matrícula na reoferta.<br>Procure a tesouraria para verificar sua situação.
                                                        <br><button onclick="solicitar_revisao(5, 'Pendência financeira')" id="btAutorizacao5" class="btn btn-warning pos-right mt-3">Solicitar Revisão</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            	}
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                    	}
                                    ?>
								</div>
								<div class="col-md-2 mb-3">
									<label class="form-label">Inicio da Matrícula </label>
									<input type="text" class="form-control form-control-lg" value="<?php echo ($reoferta->inscricao_data_inicial);?>" readonly>
								</div>
								<div class="col-md-2 mb-3">
									<label class="form-label">Fim da Matrícula </label>
									<input  type="text" class="form-control form-control-lg" value="<?php echo ($reoferta->inscricao_data_final);?>" readonly>
								</div>
								<?php
								if(!$reoferta->intervalo_matricula){
									$frase = $reoferta->matricula_expirada == 0 ? "Prazo de matricula expirado" : "Aguardando abertura do período de matrícula";
								?>
									<div class="col mb-3">
										<label class="form-label">&nbsp</label><br>
										<button type="submit" class="btn btn-lg btn-primary waves-effect waves-themed" disabled="">
											<span class="fal fa-lock mr-1"></span><?php echo $frase?>
										</button>
									</div>
								<?php									
								} else if(@$pre->permissao_matricula == 1 && $pre->pago == 1){ 
							
								?>
								<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscricao/inscricao_dados_matricula.php">
									<input type="hidden" name="id_reoferta" value="<?php echo $_GET['id']?>">
									<input  type="hidden" name="data_vencimento" class="form-control periodo_diferente" value="<?php echo ($reoferta->inscricao_data_final);?>" >
									<input  type="hidden" name="reoferta_minimo" class="form-control periodo_diferente" value="<?php echo ($reoferta->reoferta_minimo);?>" >
									<input  type="hidden" name="carga_horaria" class="form-control periodo_diferente" value="<?php echo ($reoferta->carga_horaria);?>" >
									<div class="col mb-3">
										<label class="form-label">&nbsp</label><br>
										<button type="submit" class="btn btn-lg btn-primary btn-lg waves-effect waves-themed">
											<span class="fal fa-check mr-1"></span>Matricular
										</button>
									</div>
								</form>
								<?php
									} else {
								?>
									<div class="col mb-3">
										<label class="form-label">&nbsp</label><br>
										<button type="submit" class="btn btn-lg btn-primary waves-effect waves-themed" disabled="">
											<span class="fal fa-lock mr-1"></span>Matricular
										</button>
									</div>
								<?php
									}
								?>


							</div>
							<?php
								} else {
							?>
							<div class="form-row form-group">
								<div class="col-md-12 mb-3">
									<div class="panel-tag">
										Você realizou sua matrícula nesta reoferta em: <strong><?php echo $matricula->data_matricula;?></strong><br>
	                                <?php
	                                	if($boleto_matricula->rowCount()){
	                                		 if($matricula->pago){
	                                			echo "<h4 class='color-success-900'>Pagamento do boleto identificado em: <strong>".$matricula->data_pagamento."</strong></h4>";
	                                		} else
	                                		if($row_boleto_matricula->data_vencimento < date("Y-m-d")){
	                                			echo "<h4 class='color-danger-900'>Seu boleto emitido em: <strong>".$row_boleto_matricula->data_emissao_f."</strong> venceu em: <strong>".$row_boleto->data_vencimento_f."</strong></h4>";
	                                		} else {
												echo "Seu boleto emitido em: <strong>".$row_boleto_matricula->data_emissao_f."</strong> vencerá em: <strong>".$row_boleto_matricula->data_vencimento_f."</strong>";
	                                		}
	                                	} else {
											echo "Seu boleto ainda não foi gerado";
	                                	}
									?>    
	                                </div>
									<?php
										if($matriculado){
											if($boleto_matricula->rowCount()){
												if(!$matricula->pago){
									?>
											<?php
												
												#desespero
										
											
													$query_botao = "SELECT * FROM coopex_reoferta.pre_matricula
																	WHERE id_reoferta = ".$_GET['id']." AND 
																	id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa']."";
													
													$res_botao = $coopex->query($query_botao);
													$libera_boleto = $res_botao->fetch(PDO::FETCH_OBJ);
													
													
												if($libera_boleto->permissao_matricula == 1){
												?>
									<a target="_blank" href="https://coopex.fag.edu.br/boleto/reoferta/matricula/<?php echo $matricula->id_matricula?>">
										<div class="col mb-3">
											<label class="form-label" for="validationCustom02">&nbsp</label><br>
											<button  type="button" class="btn btn-lg btn-secondary waves-effect waves-themed" >
	                                            <span class="fal fa-print mr-1"></span>
												
												
												
												
												
	                                            Imprimir boleto
												
	                                        </button>
										</div>
									</a>
									<?php
													
													
												}else{
													echo '<div class="col mb-3">
											<label class="form-label" for="validationCustom02">&nbsp</label><br>
											<button  type="button" class="btn btn-lg btn-secondary waves-effect waves-themed" >
	                                            <span class="fal fa-print mr-1"></span>
												
												
												
												
												
	                                            Sem Permissão para Matrícula
												
	                                        </button>
										</div>';
												}
												}
											} else {
												
												$query_botao = "SELECT * FROM coopex_reoferta.pre_matricula
																	WHERE id_reoferta = ".$_GET['id']." AND 
																	id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa']."";
													
													$res_botao = $coopex->query($query_botao);
													$libera_boleto = $res_botao->fetch(PDO::FETCH_OBJ);
													
													
												if($libera_boleto->permissao_matricula == 1){
									?>
									<a target="_blank" href="https://coopex.fag.edu.br/boleto/reoferta/matricula/<?php echo $matricula->id_matricula?>">
										<div class="col mb-3">
											<label class="form-label" for="validationCustom02">&nbsp</label><br>
											<button  type="button" class="btn btn-lg btn-success waves-effect waves-themed" >
	                                            <span class="fal fa-print mr-1"></span>
	                                            Gerar Boleto
	                                        </button>
										</div>
									</a>
									<?php		}else{
													echo '<div class="col mb-3">
											<label class="form-label" for="validationCustom02">&nbsp</label><br>
											<button  type="button" class="btn btn-lg btn-secondary waves-effect waves-themed" >
	                                            <span class="fal fa-print mr-1"></span>
												
												
												
												
												
	                                            Sem Permissão para Matrícula
												
	                                        </button>
										</div>';
												}
											}
										}
									?>
								</div>
							</div>
							<?php		
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xl-12">
		  <div id="panel-2" class="panel">
			 	<div class="panel-hdr">
					<h2>
						Detalhes da Reoferta
					</h2>
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content p-0">
						<div class="panel-content">
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<label class="form-label" for="validationCustom03">Disciplina </label>
									<input readonly="" type="text" class="form-control" value="<?php echo texto($reoferta->disciplina);?>" readonly>
								</div>
								<div class="col-md-1 mb-3">
									<label class="form-label" for="validationCustom02">Carga Horária</label>
									<input type="text" class="form-control"  value="<?php echo $reoferta->carga_horaria;?>" readonly>
									<div class="valid-feedback">
										OK!
									</div>
								</div>
								<div class="col-md-5 mb-3">
									<label class="form-label" for="validationCustom03">Curso </label>
									<input readonly="" type="text" class="form-control" value="<?php echo texto($reoferta->departamento);?>" readonly>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<label class="form-label" for="select2-ajax">
											Professor(a)
										</label>
										<input type="text" class="form-control" name="local"  value="<?php echo texto($reoferta->nome)?>" readonly>

									</div>	
								</div>
								<div class="col-md-6 mb-3">
									<label class="form-label" for="validationCustom02">Local da Reoferta </label>
									<input type="text" class="form-control" name="local"  value="<?php echo texto($reoferta->local)?>" readonly>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xl-12">
		  <div id="panel-2" class="panel">
			<div class="panel-hdr">
					<h2>
						Cronograma da reoferta
					</h2>
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content p-0">
						<div class="panel-content">
							<div class="form-row">
								<div class="col-xl-12">
								<!-- datatable start -->
									<table id="cronograma_tabela" class="table table-bordered table-hover table-striped w-100"></table>
								<!-- datatable end -->
								</div>
							</div>
						</div>
					</div>
				</div>			
			</div>
		</div>
	</div>
	
	<textarea class="d-none" name="cronograma" id="cronograma" cols="100"></textarea>
</main>


<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>

<iframe class="d-none" name="dados"></iframe>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscricao/solicitar_revisao.php">
    	<input type="hidden" name="id_autorizacao" id="id_autorizacao">
	    <input type="hidden" name="id_pessoa" id="id_pessoa" value="<?php echo $pre->id_pessoa?>">
	    <input type="hidden" name="id_reoferta" value="<?php echo $_GET['id']?>">
	    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
	        <div class="modal-content">
	            <div class="panel-hdr">
                    <h2 id="descricao">
                    </h2>
                    <div class="panel-toolbar">
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
		                </button>
                    </div>
                </div>
	            <div class="modal-body">
	                <div class="form-row">
						<div class="col-md-12 mb-3">
							<label class="form-label" for="validationCustom02">Justificativa <span class="text-danger"></span></label>
							<textarea required="" id="justificativa" type="text" name="justificativa" class="form-control" placeholder="Descreva a justificativa"></textarea>
						</div>
					</div>
	            </div>
	            <div class="modal-footer modal-footer pt-3 pb-3 border-faded border-left-0 border-right-0 border-bottom-0">
	                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
	                <button type="submit" class="btn btn-primary">Solicitar</button>
	            </div>
	        </div>
	    </div>
	</form>
</div>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script>

	function solicitacao_revisaoOK(){ 
		
		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal .close").click();

		Swal.fire({
			type: "success",
			title: "Solicitação enviada com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				//document.location.reload(true)
			}
		});
	}

	function solicitacao_revisaoFalha(){ 
		
		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal_matricula .close").click();

		Swal.fire({
			type: "success",
			title: "Falha ao enviar solicitação!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				//document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE CADASTRO OK
	function prematriculaOK(operacao){ 
		var msg = "Pré-matrícula realizada sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function prematriculaFalha(operacao){ 
		var msg = "Não foi possível realizar a Pré-matrícula";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	//MENSAGEM DE CADASTRO OK
	function matriculaOK(operacao){ 
		var msg = "Pré-matrícula realizada sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function matriculaFalha(operacao){ 
		var msg = "Não foi possível realizar a Pré-matrícula";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	//MENSAGEM DE CADASTRO OK
	function cadastroOK(operacao){ 
		var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				<?php
					if(!isset($_GET['id'])){
						echo "window.history.back();";
					} else {
						echo "document.location.reload(true);";
					}
				?>
				
				//document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function cadastroFalha(operacao){ 
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	function solicitar_revisao(id_autorizacao, descricao){
		
		$("#id_autorizacao").val(id_autorizacao);
		$("#descricao").html(descricao);
		
		/*
		$.ajax({
			url : "modulos/reoferta/inscricao/solicitar_revisao.php",
			type : 'post',
			data : {
				id_pessoa : data.id,
				id_reoferta :'<?php echo $id_reoferta?>',
				data_vencimento : '<?php echo converterData($row_pre_total->data_vencimento)?>'
			},
			beforeSend : function(){
			$("#resultado").html("ENVIANDO...");
		}
		})
		.done(function(msg){
			document.location.reload(true)
		})
		.fail(function(jqXHR, textStatus, msg){
			alert(msg);
		});*/
	}


	$(document).ready(function(){
		$("#termo_de_aceite").change(function() {

			if($("#termo_de_aceite").prop('checked')){
				$("#botao_pre_matricula").attr("disabled", false);
				$("#botao_pre_matricula span").removeClass("fa-lock");
				$("#botao_pre_matricula span").addClass("fa-check");
				
			} else {
				$("#botao_pre_matricula").attr("disabled", true);
				$("#botao_pre_matricula span").removeClass("fa-check");
				$("#botao_pre_matricula span").addClass("fa-lock");
			}
		});

		// Column Definitions
		var columnSet = [
		{
			title: "ID",
			id: "id_cronograma",
			data: "id_cronograma",
			placeholderMsg: "Gerado automáticamente",
			"visible": false,
			"searchable": false,
			type: "readonly"
		},
		{
			title: "Data",
			id: "data_reoferta",
			data: "data_reoferta",
			type: "date",
			"visible": false,
			"searchable": false,
			pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
			placeholderMsg: "dd-mm-yyyy",
			errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
		},
		{
			title: "Data",
			id: "data_reoferta",
			data: "data_reoferta",
			type: "date",
			pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
			placeholderMsg: "dd-mm-yyyy",
			errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
		},
		{
			title: "Horário de Início",
			id: "horario_inicio",
			data: "horario_inicio",
			type: "time",
			pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
			placeholderMsg: "yyyy-mm-dd",
			errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
		},
		{
			title: "Horário de Término",
			id: "horario_termino",
			data: "horario_termino",
			type: "time",
			pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
			placeholderMsg: "yyyy-mm-dd",
			errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
		}]


		/* start data table */
		var myTable = $('#cronograma_tabela').dataTable(
		{
			ajax: "modulos/reoferta/inscricao/ajax/cronograma.php?id_reoferta="+<?php echo $_GET['id']?>,
			columns: columnSet,
			responsive: true,
			paging: false,
			order: [[ 1, "asc" ]],
			columnDefs: [{
				targets: 2,
				render: function(data, type, full, meta){
					return moment(data).format('DD/MM/YYYY');
				}
			}]
		});
	});



</script>