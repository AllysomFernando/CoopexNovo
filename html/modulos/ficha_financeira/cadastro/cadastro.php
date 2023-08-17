<?php
// session_start();
require_once("php/sqlsrv.php");
require_once("php/mysql.php");

require_once("modulos/ficha_financeira/funcoes_sagres.php");

//print_r($_SERVER);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_menu = 29;
$chave   = "id_ficha_financeira";

unset($_SESSION['ficha_financeira']);

$_SESSION['ficha_financeira']['carga_horaria']                              = 0;
$_SESSION['ficha_financeira']['carga_horaria_pacote']                       = 0;
$_SESSION['ficha_financeira']['carga_horaria_disciplinas_pacote']           = 0;
$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_pacote']      = 0;
$_SESSION['ficha_financeira']['carga_horaria_disciplinas_fora_pacote']      = 0;
$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_fora_pacote'] = 0;
$_SESSION['ficha_financeira']['desconto_dp']                                = 0;
$_SESSION['ficha_financeira']['calculo']['ch_dp']                           = 0;

if (isset($_GET['id'])) {
  $$chave = $_GET['id'];

  $_SESSION['ficha_financeira']['id_ficha_financeira'] = $_GET['id'];

  //CARREGA DADOS DA FICHA FINANCEIRA
  $sql = "SELECT
					*,
					DATE_FORMAT( data_cadastro, '%d/%m/%Y - %H:%i:%s' ) AS data_cadastro 
				FROM
					ficha_financeira.ficha_financeira
					INNER JOIN coopex.pessoa USING ( id_pessoa )
				WHERE
					ficha_financeira.ficha_financeira.excluido = 0
					AND id_ficha_financeira = " . $_GET['id'];
  $res = $coopex->query($sql);

  if ($res->rowCount()) {
    $dados = $res->fetch(PDO::FETCH_OBJ);
  } else {
    $sql = "SELECT
					*,
					DATE_FORMAT( data_cadastro, '%d/%m/%Y - %H:%i:%s' ) AS data_cadastro 
				FROM
					ficha_financeira.ficha_financeira
					INNER JOIN coopex.pessoa USING ( id_pessoa )
				WHERE
					ficha_financeira.ficha_financeira.excluido = 1
					AND id_ficha_financeira = " . $_GET['id'];
    $res = $coopex->query($sql);
    if ($res->rowCount()) {
      $sql = "SELECT
							nome 
						FROM
							coopex.log
							INNER JOIN coopex.pessoa USING ( id_pessoa ) 
						WHERE
							tabela = 'ficha_financeira.ficha_financeira' 
							AND operacao = 3 
							AND id_registro = " . $_GET['id'];
      $res = $coopex->query($sql);
      $dados = $res->fetch(PDO::FETCH_OBJ);
      echo "<h1>Ficha financeira excluída por: $dados->nome</h1>";
      exit;
    }
  }
} else {
  $$chave = 0;
}

//print_r($dados);

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/core.js"></script>

<style type="text/css">
  .table th,
  .table td {
    vertical-align: middle !important;
  }

  .valor_destaque {
    background-color: #000;
    color: #fff;
  }
</style>

<script type="text/javascript">
  function alocar_horario(dia, hora_inicio, hora_termino, codigo, disciplina) {
    //alert("#"+dia+"_"+hora_inicio+" - "+codigo+"<br>"+disciplina);

    if (hora_termino < 1300) {
      $("#horario_manha").show();
    } else if (hora_inicio > 1300 && hora_inicio < 1900) {
      $("#horario_tarde").show();
    } else if (hora_inicio >= 1930) {
      $("#horario_noite").show();
    }

    if (hora_inicio == 1900 && hora_termino == 2040) {
      $("#" + dia + "_" + hora_inicio).attr("rowspan", 2);
      $("#" + dia + "_" + 1950).remove();

    } else if (hora_inicio == 2050 && hora_termino == 2230) {
      $("#" + dia + "_" + hora_inicio).attr("rowspan", 2);
      $("#" + dia + "_" + 2140).remove();
    }

    if ($("#" + dia + "_" + hora_inicio).html()) {
      $("#" + dia + "_" + hora_inicio).addClass('bg-danger-500');
    }


    $("#" + dia + "_" + hora_inicio).append("<p>" + codigo + "<br>" + disciplina + "</p>");
  }

  function alocar_horario_quinzenal(data, dia, hora_inicio, hora_termino, codigo, disciplina, local) {
    //alert("#"+dia+"_"+hora_inicio+" - "+codigo+"<br>"+disciplina);
    $("#horario_quinzenal").show();
    $("#quadro_horario_quinzenal").append('<tr><td class="text-center"><strong>' + data + '</strong></td><td><strong>' + codigo + " - " + disciplina + '</strong></td><td class="text-center"><strong>' + dia + '</strong></td><td class="text-center"><strong>' + hora_inicio + '</strong></td><td class="text-center"><strong>' + hora_termino + '</strong></td><td class="text-center"><strong>' + local + '</strong></td></tr>');
  }
</script>

<main id="js-page-content" role="main" class="page-content">

  <?php
  if (!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1])) {
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
    <li class="breadcrumb-item"><a href="javascript:void(0);">Ficha Financeira</a></li>
    <li class="breadcrumb-item active">Cadastro</li>
    <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu ?>c</span></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-repeat'></i> Ficha Financeira
      <small>
        Cadastro de Ficha Financeira
      </small>
    </h1>

  </div>

  <?php
  $desabilitar_edicao_carga_horaria = false;
  $desabilitar_edicao = false;
  if (isset($_GET['id'])) {
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
            <span class="h5">Status de aprovação da ficha financeira</span>
            <!-- <div class="progress">
						<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
					</div> -->
            <br><br>
            <ol>
              <?php
              $sql2 = "SELECT
										*,
										DATE_FORMAT( data_cadastro, '%d/%m/%Y - %H:%i:%s' ) AS data_cadastro 
									FROM
										ficha_financeira.ficha_financeira_etapa
										INNER JOIN ficha_financeira.etapa USING ( id_etapa ) 
									WHERE
										id_ficha_financeira = " . $_GET['id'];
              $res2 = $coopex->query($sql2);

              $enviado_tesouraria = false;

              while ($etapa = $res2->fetch(PDO::FETCH_OBJ)) {
                if ($etapa->id_etapa == 5) {
                  $enviado_tesouraria = true;
                }

                $login = $etapa->enviado_por ? '(' . trim($etapa->enviado_por) . ')' : "";

                $etapa->etapa = utf8_encode($etapa->etapa);
                echo "<li><b>$etapa->data_cadastro</b> - $etapa->etapa <strong>$login</strong></li>";
              }
              ?>
            </ol>
          </div>
        </div>
      </div>
    </div>
  <?php
  }
  ?>

  <iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>

  <form class="needs-validation" method="post" target="dados" action="modulos/ficha_financeira/cadastro/cadastro_dados.php">
    <input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>">
    <div class="row">
      <div class="col-xl-12">
        <div id="panel-2" class="panel">
          <div class="panel-hdr">
            <h2>
              1. Ficha Financeira
            </h2>
            <div class="panel-toolbar">
              <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
              <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            </div>
          </div>
          <div class="panel-container show">

            <div class="panel-content p-0">
              <div class="panel-content">
                <div class="form-row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="validationCustom03">Curso <span class="text-danger">*</span></label>
                    <?php
                    $where = "";

                    /*$id_faculdade = ($_SESSION['coopex']['usuario']['id_pessoa'] == '5000225543') ? "1100000002" : "1000000002";

											$campus = $_SESSION['coopex']['usuario']['pessoa']->id_campus ? " and departamento.id_campus = ".$_SESSION['coopex']['usuario']['pessoa']->id_campus : "";

											if($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 1 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 13 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 8){
												$where .= " AND graduacao = 1 and id_campus = $id_faculdade";
											} else {
												$where .= " AND graduacao = 1 AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
											}*/

                    $campus = $_SESSION['coopex']['usuario']['pessoa']->id_campus ? " and graduacao = 1 and departamento.id_campus = " . $_SESSION['coopex']['usuario']['pessoa']->id_campus : "";

                    #VERIFICA SE O TIPO DE USUÁRIO POSSUI PERMISSÃO PARA ACESSAR TODOS OS REGISTROS
                    if (in_array($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'], array(1, 2, 3, 8, 9, 11, 13))) {
                      $where  = " AND 1=1 ";
                    } else {
                      $id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
                      $where  = "AND (id_pessoa = $id_pessoa
																OR id_departamento IN (SELECT id_departamento FROM coopex.departamento_pessoa WHERE graduacao = 1 and id_pessoa = $id_pessoa)) ";
                    }

                    if ($_SESSION['coopex']['usuario']['id_pessoa'] == 5000208750) {
                      $where .= " and departamento.id_campus = 1100000002";
                    }

                    if ($_SESSION['coopex']['usuario']['id_pessoa'] == 5000216706) {
                      $where .= " and id_etapa = 11";
                    }

                    if (isset($_GET['id'])) {
                      $sql = "SELECT
														id_departamento,
														departamento,
														campus.campus
													FROM
														coopex.departamento
														INNER JOIN coopex.departamento_pessoa USING ( id_departamento )
														INNER JOIN coopex.campus USING ( id_campus )
														WHERE 
														
															id_departamento = $dados->id_curso
													GROUP BY
														id_departamento
													ORDER BY
														departamento";
                    } else {
                      $sql = "SELECT
														id_departamento,
														departamento,
														campus.campus
													FROM
														coopex.departamento
														INNER JOIN coopex.departamento_pessoa USING ( id_departamento )
														INNER JOIN coopex.campus USING ( id_campus )
														WHERE 1=1
														$where $campus
													GROUP BY
														id_departamento
													ORDER BY
														departamento";
                    }

                    $curso = $coopex->query($sql);
                    ?>

                    <?php
                    if (isset($_GET['id'])) {
                      $row = $curso->fetch(PDO::FETCH_OBJ)
                    ?>
                      <input type="text" class="form-control" value="<?php echo isset($row->id_departamento) ? utf8_encode($row->departamento) : "" ?>">
                    <?  } else { ?>
                      <select id="id_curso" name="id_curso" class="select2 form-control" required="">
                        <option value="">Selecione o Curso</option>
                        <?php
                        while ($row = $curso->fetch(PDO::FETCH_OBJ)) {
                        ?>
                          <option value="<?php echo $row->id_departamento ?>"><?php echo utf8_encode($row->departamento) ?></option>
                        <?php
                        }
                        ?>
                      </select>
                    <?php
                    }
                    ?>

                    <div class="invalid-feedback">
                      Selecione o curso
                    </div>
                  </div>

                  <div class="col-md-3 mb-3">
                    <label class="form-label" for="validationCustom03">Grades do Curso <span class="text-danger">*</span></label>
                    <?php
                    if (isset($_GET['id'])) {
                      $sql = "SELECT
															crr_id_curriculo AS id_curriculo,
															pel_ds_compacta AS grade 
														FROM
															academico..CRR_curriculo
															INNER JOIN academico..PEL_periodo_letivo ON crr_id_periodo_letivo_inicio = pel_id_periodo_letivo 
														WHERE
															crr_id_curriculo = " . $dados->id_grade;
                      $res = mssql_query($sql);
                      $row = mssql_fetch_object($res);
                    ?>
                      <input type="text" class="form-control" value="<?php echo isset($row->id_curriculo) ? $row->grade : "" ?>">
                    <?php
                    } else {
                    ?>
                      <select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao  ? 'disabled=""' : "" ?> id="id_grade" name="id_grade" onchange="$('#disciplina').val($(this).select2('data')[0].text);" disabled="" class="select2 form-control" required="">
                        <option value="">Selecione a Grade</option>
                      </select>
                      <!-- <input type="hidden" name="disciplina" id="disciplina" value="<?php echo isset($dados->disciplina) ? utf8_encode($dados->disciplina) : "" ?>"> -->
                      <div class="invalid-feedback">
                        Selecione a Grade
                      </div>
                    <?php
                    }
                    ?>
                  </div>


                  <div class="col-md-3 mb-3">
                    <label class="form-label" for="validationCustom03">Semestre Letivo <span class="text-danger">*</span></label>
                    <?php
                    if (isset($_GET['id'])) {
                      $sql = "SELECT
															pel_id_periodo_letivo as id_semestre,
															pel_ds_historico as semestre
														FROM
															academico..PEL_periodo_letivo 
														WHERE
															pel_id_periodo_letivo = " . $dados->id_semestre;
                      $res = mssql_query($sql);
                      $row = mssql_fetch_object($res);
                    ?>
                      <input type="text" class="form-control" id="id_semestre_letivo" value="<?php echo isset($row->id_semestre) ? $row->semestre : "" ?>">
                    <?php
                    } else {
                    ?>
                      <select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao  ? 'disabled=""' : "" ?> id="id_semestre" name="id_semestre" disabled="" class="select2 form-control" required="">
                        <option value="">Selecione o Semestre</option>
                      </select>
                      <!-- <input type="hidden" name="semestre" id="semestre" value="<?php echo isset($dados->disciplina) ? utf8_encode($dados->disciplina) : "" ?>"> -->
                      <div class="invalid-feedback">
                        Selecione o Semestre
                      </div>
                    <?php
                    }
                    ?>
                  </div>

                </div>

                <div class="form-row">
                  <div class="col-md-12 mb-3">
                    <div class="form-group">
                      <label class="form-label" for="select2-ajax">
                        Acadêmico
                      </label>
                      <?php
                      if (isset($_GET['id'])) {
                        get_aluno($dados->id_pessoa, $dados->id_semestre, $dados->id_curso);
                      ?>
                        <input type="text" id="nome_academico_input" class="form-control" value="<?php echo isset($dados->id_pessoa) ? utf8_encode($dados->nome) : "" ?>">

                        <input type="hidden" name="id_pessoa" value="<?php echo isset($dados->id_pessoa) ? utf8_encode($dados->id_pessoa) : "" ?>">
                      <?php
                      } else {
                      ?>
                        <select id="id_pessoa" disabled="" name="id_pessoa" data-placeholder="Acadêmico" class="js-consultar-usuario form-control"></select>
                      <?php
                      }
                      ?>
                    </div>
                  </div>
                </div>

                <div class="form-row">
                  <div class="col-md-12 mb-3">
                    <table class="table table-bordered table-hover table-striped w-100">
                      <thead>
                        <tr>
                          <td class="text-center"><strong>RA</strong></td>
                          <td class="text-center"><strong>Turno</strong></td>
                          <td class="text-center"><strong>Turma</strong></td>
                          <td class="text-center"><strong>Turma</strong></td>
                          <td class="text-center"><strong>Valor Hora</strong></td>
                          <td class="text-center"><strong>Valor Mensalidade</strong></td>
                          <td class="text-center"><strong>Valor Semestre</strong></td>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                          <td class="text-center"><strong id="ra">
                              <?= isset($_GET['id']) ? $_SESSION['ficha_financeira']['ra'] : "-" ?></strong>
                          </td>
                          <td class="text-center"><strong id="turno">
                              <?= isset($_GET['id']) ? $_SESSION['ficha_financeira']['turno'] : "-" ?></strong></td>
                          <td class="text-center">
                            <strong id="turma"><?= isset($_GET['id']) ? utf8_encode($_SESSION['ficha_financeira']['link_de_turma']) : "-" ?></strong>
                          </td>

                          <td class="text-center">
                            <select id="id_turma" name="id_turma" class="select2 form-control">
                              <?

                              if (isset($_GET['id'])) {

                                $sql = "SELECT
																			pac_id_pacote,
																			pac_ds_pacote 
																		FROM
																			academico..PAC_pacote 
																		WHERE
																			pac_id_periodo_letivo = $dados->id_semestre 
																			AND pac_id_curso = $dados->id_curso";
                                $res = mssql_query($sql);

                                $array = null;
                                if (mssql_num_rows($res) > 0) {
                                  while ($row = mssql_fetch_assoc($res)) {
                                    $selecionado = '';
                                    if ($dados->id_turma == $row['pac_id_pacote']) {
                                      echo $selecionado = 'selected=""';
                                    }
                              ?>
                                    <option <?= isset($dados->id_turma) ? $selecionado : "" ?> value="<?= $row['pac_id_pacote'] ?>"><?= utf8_encode($row['pac_ds_pacote']) ?></option>
                              <?
                                  }
                                }
                              }
                              ?>
                            </select>
                          </td>
                          <td class="text-center">
                            <strong id="valor_hora">R$ <?= isset($_GET['id']) ? number_format($_SESSION['ficha_financeira']['valor_hora'], 2, ',', '.') : "0,00" ?></strong>

                          </td>
                          <td class="text-center">
                            <strong id="valor_mensalidade">R$ <?= isset($_GET['id']) ? number_format($_SESSION['ficha_financeira']['valor_semestre'] / 6, 2, ',', '.') : "0,00" ?></strong>
                          </td>
                          <td class="text-center">
                            <strong id="valor_semestre">R$ <?= isset($_GET['id']) ? number_format($_SESSION['ficha_financeira']['valor_semestre'], 2, ',', '.') : "0,00" ?></strong>
                            <br>

                            <?
                            if (isset($_GET['id'])) {
                              if ($dados->id_curso == 1000000140) {
                            ?>
                                <select name="valor_mensalidade" id="valor_fisio" onchange="definir_valor_mensalidade_fisioterapia()" class="form-control">

                                  <script type="text/javascript">
                                    $(document).ready(function() {
                                      definir_valor_mensalidade_fisioterapia();
                                    });
                                  </script>
                                  <?
                                  $sql2 = "SELECT
																			valor_mensalidade
																		FROM
																			ficha_financeira.valor_hora 
																		WHERE
																			id_departamento = 1000000140 
																			AND id_turno = 3";
                                  $res2 = $coopex->query($sql2);
                                  while ($valor = $res2->fetch(PDO::FETCH_OBJ)) {
                                  ?>
                                    <option <?= $dados->valor_mensalidade == $valor->valor_mensalidade ? 'selected=""' : '' ?> value="<?= $valor->valor_mensalidade ?>"><?= $valor->valor_mensalidade ?></option>
                                  <?
                                  }
                                  ?>
                                </select>
                            <?
                              }
                            }
                            ?>
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                  <!-- <div class="col-md-12 mb-3">
                    <div  class="custom-control custom-checkbox" id="aprovacao_check">
                      <input <?php echo isset($dados->ze_antonio) ? "checked" : "" ?> type="checkbox" class="custom-control-input" id="invalidCheck2" value="1" name="ze_antonio">
                      <label class="custom-control-label" for="invalidCheck2">José Antônio</label>
                    </div>
                  </div> -->

                  <div class="col-md-12 mb-3 mt-2">
                    <label class="form-label" for="validationCustom03">Observações</label>
                    <textarea name="observacao" class="form-control col-md-12"><?php echo isset($dados->id_pessoa) ? utf8_encode($dados->observacao) : "" ?></textarea>
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
              2. Disciplinas do Pacote
            </h2>
            <div class="panel-toolbar">
              <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
              <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            </div>
          </div>
          <div class="panel-container show">
            <div class="panel-content p-0">

              <div class="panel-content">


                <div class="form-row" id="cronograma_container">
                  <div class="col-xl-12">
                    <!-- datatable start -->
                    <table id="diciplinas_pacote" class="table table-bordered table-hover table-striped w-100">
                      <thead>
                        <tr>
                          <td><strong>Código</strong></td>
                          <td><strong>Disciplina</strong></td>
                          <td class="text-center"><strong>Horas</strong></td>
                          <td class="text-center"><strong>Incluir</strong></td>
                        </tr>
                      </thead>

                      <tbody>

                      </tbody>

                      <tfoot>
                        <tr style="background-color: #ddd">
                          <td class="text-right" colspan="2"><strong>TOTAL</strong></td>
                          <td class="text-center"><strong id="total_diciplinas_pacote">0</strong></td>
                          <td class="text-center"><strong></strong></td>
                        </tr>
                      </tfoot>
                    </table>
                    <!-- datatable end -->
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="row" style="display: none;">
      <div class="col-xl-12">
        <div id="panel-2" class="panel">
          <div class="panel-hdr">
            <h2>
              3. Disciplinas em Dependência
            </h2>
            <div class="panel-toolbar">
              <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
              <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            </div>
          </div>
          <div class="panel-container show">
            <div class="panel-content p-0">

              <div class="panel-content">


                <div class="form-row" id="cronograma_container">
                  <div class="col-xl-12">
                    <!-- datatable start -->
                    <table id="diciplinas_dp" class="table table-bordered table-hover table-striped w-100">
                      <thead>
                        <tr>
                          <td><strong>Código</strong></td>
                          <td><strong>Disciplina</strong></td>
                          <td class="text-center"><strong>Horas</strong></td>
                        </tr>
                      </thead>

                      <tbody>
                      </tbody>

                      <tfoot>
                        <tr>
                          <td><strong></strong></td>
                          <td class="text-right"><strong>TOTAL</strong></td>
                          <td class="text-center"><strong id="total_diciplinas_dp">0</strong></td>
                        </tr>
                      </tfoot>
                    </table>
                    <!-- datatable end -->
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <?
    if ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] != 3) {
    ?>

      <div class="row">
        <div class="col-xl-12">
          <div id="panel-2" class="panel">
            <div class="panel-hdr">
              <h2>
                3. Quadro de Horários (em fase de testes)
              </h2>
              <div class="panel-toolbar">
                <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
              </div>
            </div>
            <div class="panel-container show">
              <div class="panel-content p-0">

                <div class="panel-content">

                  <div class="form-row">
                    <div class="col-xl-12" id="horario_manha" style="display: none;">
                      <!-- datatable start -->
                      <label class="form-label" for="select2-ajax">
                        Manhã
                      </label>
                      <table id="quadro_manha" class="table table-bordered table-striped w-100">
                        <thead>
                          <tr>
                            <td class="text-center"><strong>Horário</strong></td>
                            <td class="text-center"><strong>Segunda</strong></td>
                            <td class="text-center"><strong>Terça</strong></td>
                            <td class="text-center"><strong>Quarta</strong></td>
                            <td class="text-center"><strong>Quinta</strong></td>
                            <td class="text-center"><strong>Sexta</strong></td>
                            <td class="text-center"><strong>Sábado</strong></td>
                          </tr>
                        </thead>

                        <tbody>
                          <tr class="text-center">
                            <td>07:30<br>08:20</td>
                            <td id="SEG_0730"></td>
                            <td id="TER_0730"></td>
                            <td id="QUA_0730"></td>
                            <td id="QUI_0730"></td>
                            <td id="SEX_0730"></td>
                            <td id="SAB_0730"></td>
                          </tr>
                          <tr class="text-center">
                            <td>08:20<br>09:10</td>
                            <td id="SEG_0820"></td>
                            <td id="TER_0820"></td>
                            <td id="QUA_0820"></td>
                            <td id="QUI_0820"></td>
                            <td id="SEX_0820"></td>
                            <td id="SAB_0820"></td>
                          </tr>
                          <tr style="background-color: #ddd; line-height: 10px; color: #999; text-align: center">
                            <td>09:10<br>09:20</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                          </tr>
                          <tr class="text-center">
                            <td>09:20<br>10:10</td>
                            <td id="SEG_0920"></td>
                            <td id="TER_0920"></td>
                            <td id="QUA_0920"></td>
                            <td id="QUI_0920"></td>
                            <td id="SEX_0920"></td>
                            <td id="SAB_0920"></td>
                          </tr>
                          <tr class="text-center">
                            <td>10:10<br>11:00</td>
                            <td id="SEG_1010"></td>
                            <td id="TER_1010"></td>
                            <td id="QUA_1010"></td>
                            <td id="QUI_1010"></td>
                            <td id="SEX_1010"></td>
                            <td id="SAB_1010"></td>
                          </tr>
                          <tr class="text-center">
                            <td>11:00<br>11:50</td>
                            <td id="SEG_1100"></td>
                            <td id="TER_1100"></td>
                            <td id="QUA_1100"></td>
                            <td id="QUI_1100"></td>
                            <td id="SEX_1100"></td>
                            <td id="SAB_1100"></td>
                          </tr>
                          <tr class="text-center">
                            <td>11:50<br>12:40</td>
                            <td id="SEG_1150"></td>
                            <td id="TER_1150"></td>
                            <td id="QUA_1150"></td>
                            <td id="QUI_1150"></td>
                            <td id="SEX_1150"></td>
                            <td id="SAB_1150"></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <div class="col-xl-12" id="horario_tarde" style="display: none;">
                      <!-- datatable start -->
                      <label class="form-label" for="select2-ajax">
                        Tarde
                      </label>
                      <table id="quadro_tarde" class="table table-bordered table-striped w-100">
                        <thead>
                          <tr>
                            <td class="text-center"><strong>Horário</strong></td>
                            <td class="text-center"><strong>Segunda</strong></td>
                            <td class="text-center"><strong>Terça</strong></td>
                            <td class="text-center"><strong>Quarta</strong></td>
                            <td class="text-center"><strong>Quinta</strong></td>
                            <td class="text-center"><strong>Sexta</strong></td>
                            <td class="text-center"><strong>Sábado</strong></td>
                          </tr>
                        </thead>

                        <tbody>
                          <tr class="text-center">
                            <td>13:30<br>14:20</td>
                            <td id="SEG_1330"></td>
                            <td id="TER_1330"></td>
                            <td id="QUA_1330"></td>
                            <td id="QUI_1330"></td>
                            <td id="SEX_1330"></td>
                            <td id="SAB_1330"></td>
                          </tr>
                          <tr class="text-center">
                            <td>14:20<br>15:10</td>
                            <td id="SEG_1420"></td>
                            <td id="TER_1420"></td>
                            <td id="QUA_1420"></td>
                            <td id="QUI_1420"></td>
                            <td id="SEX_1420"></td>
                            <td id="SAB_1420"></td>
                          </tr>
                          <tr style="background-color: #ddd; line-height: 10px; color: #999; text-align: center">
                            <td>15:10<br>15:20</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                          </tr>
                          <tr class="text-center">
                            <td>15:20<br>16:10</td>
                            <td id="SEG_1520"></td>
                            <td id="TER_1520"></td>
                            <td id="QUA_1520"></td>
                            <td id="QUI_1520"></td>
                            <td id="SEX_1520"></td>
                            <td id="SAB_1520"></td>
                          </tr>
                          <tr class="text-center">
                            <td>16:10<br>17:00</td>
                            <td id="SEG_1610"></td>
                            <td id="TER_1610"></td>
                            <td id="QUA_1610"></td>
                            <td id="QUI_1610"></td>
                            <td id="SEX_1610"></td>
                            <td id="SAB_1610"></td>
                          </tr>
                          <tr class="text-center">
                            <td>17:10<br>17:50</td>
                            <td id="SEG_1710"></td>
                            <td id="TER_1710"></td>
                            <td id="QUA_1710"></td>
                            <td id="QUI_1710"></td>
                            <td id="SEX_1710"></td>
                            <td id="SAB_1710"></td>
                          </tr>
                          <tr class="text-center">
                            <td>17:50<br>18:40</td>
                            <td id="SEG_1750"></td>
                            <td id="TER_1750"></td>
                            <td id="QUA_1750"></td>
                            <td id="QUI_1750"></td>
                            <td id="SEX_1750"></td>
                            <td id="SAB_1750"></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <div class="col-xl-12" id="horario_noite" style="display: none;">
                      <!-- datatable start -->
                      <label class="form-label" for="select2-ajax">
                        Noite
                      </label>
                      <table id="quadro_noite" class="table table-bordered table-striped w-100">
                        <thead>
                          <tr>
                            <td class="text-center"><strong>Horário</strong></td>
                            <td class="text-center"><strong>Segunda</strong></td>
                            <td class="text-center"><strong>Terça</strong></td>
                            <td class="text-center"><strong>Quarta</strong></td>
                            <td class="text-center"><strong>Quinta</strong></td>
                            <td class="text-center"><strong>Sexta</strong></td>
                            <td class="text-center"><strong>Sábado</strong></td>
                          </tr>
                        </thead>

                        <tbody>
                          <tr class="text-center">
                            <td>19:00<br>19:50</td>
                            <td id="SEG_1900"></td>
                            <td id="TER_1900"></td>
                            <td id="QUA_1900"></td>
                            <td id="QUI_1900"></td>
                            <td id="SEX_1900"></td>
                            <td id="SAB_1900"></td>
                          </tr>
                          <tr class="text-center">
                            <td>19:50<br>20:40</td>
                            <td id="SEG_1950"></td>
                            <td id="TER_1950"></td>
                            <td id="QUA_1950"></td>
                            <td id="QUI_1950"></td>
                            <td id="SEX_1950"></td>
                            <td id="SAB_1950"></td>
                          </tr>
                          <tr style="background-color: #ddd; line-height: 15px; color: #999; text-align: center">
                            <td>20:40<br>20:50</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                            <td id="">Intervalo</td>
                          </tr>
                          <tr class="text-center">
                            <td>20:50<br>21:40</td>
                            <td id="SEG_2050"></td>
                            <td id="TER_2050"></td>
                            <td id="QUA_2050"></td>
                            <td id="QUI_2050"></td>
                            <td id="SEX_2050"></td>
                            <td id="SAB_2050"></td>
                          </tr>
                          <tr class="text-center">
                            <td>21:40<br>22:30</td>
                            <td id="SEG_2140"></td>
                            <td id="TER_2140"></td>
                            <td id="QUA_2140"></td>
                            <td id="QUI_2140"></td>
                            <td id="SEX_2140"></td>
                            <td id="SAB_2140"></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <div class="col-xl-12" id="horario_quinzenal" style="display: none;">
                      <!-- datatable start -->
                      <label class="form-label" for="select2-ajax">
                        Horário Quinzenal
                      </label>
                      <table id="quadro_noite" class="table table-bordered table-striped w-100">
                        <thead>
                          <tr>
                            <td class="text-center"><strong>Data</strong></td>
                            <td class="text-center"><strong>Disciplina</strong></td>
                            <td class="text-center"><strong>Dia</strong></td>
                            <td class="text-center"><strong>Início</strong></td>
                            <td class="text-center"><strong>Término</strong></td>
                            <td class="text-center"><strong>Local</strong></td>
                          </tr>
                        </thead>

                        <tbody id="quadro_horario_quinzenal">

                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?
    }
    ?>

    <div class="row">
      <div class="col-xl-12">
        <div id="panel-2" class="panel">
          <div class="panel-hdr">
            <h2>
              4. Disciplinas da Ficha Financeira
            </h2>
            <div class="panel-toolbar">
              <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
              <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            </div>
          </div>
          <div class="panel-container show">
            <div class="panel-content p-0">

              <div class="panel-content">

                <div class="form-row">
                  <div class="col-md-12 mb-3">
                    <div class="form-group">
                      <label class="form-label" for="select2-ajax">
                        Incluir disciplinas fora do pacote
                      </label>
                      <select <?= isset($_GET['id']) ? '' : 'disabled=""' ?> id="id_disciplina" data-placeholder="Selecione a disciplina..." class="js-consultar-disciplina form-control"></select>

                    </div>
                  </div>
                </div>

                <div class="form-row" id="cronograma_container" style="display:<?php echo $dados->id_parecer_reducao == "1" ? "none" : "" ?>;">

                  <div class="col-xl-12">
                    <!-- datatable start -->
                    <table id="diciplinas_ficha" class="table table-bordered table-hover table-striped w-100">
                      <thead>
                        <tr>
                          <td><strong>Código</strong></td>
                          <td><strong>Disciplina</strong></td>
                          <td><strong>Equivalência</strong></td>
                          <td><strong>Turma</strong></td>
                          <td class="text-center"><strong>Horas</strong></td>
                          <?
                          if ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 1 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 2 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 13) {
                          ?>
                            <td class="text-center"><strong>DP</strong></td>
                          <?
                          }
                          ?>
                          <td class="text-center"><strong>Remover</strong></td>
                        </tr>
                      </thead>

                      <tbody>
                        <?
                        $equivalencia = "";
                        if (isset($_GET['id'])) {
                          $sql = "SELECT
																*
															FROM
																ficha_financeira.ficha_financeira_disciplinas 
																INNER JOIN ficha_financeira.ficha_financeira USING ( id_ficha_financeira ) 
															WHERE
																excluido = 0 
																AND
																	id_ficha_financeira = " . $_GET['id'];

                          $curso = $coopex->query($sql);
                          while ($row = $curso->fetch(PDO::FETCH_OBJ)) {
                            $sql = "SELECT
                                      atc_id_atividade,
                                      atc_cd_atividade,
                                      atc_nm_atividade
                                    FROM
                                      academico..ATC_atividade_curricular
                                    WHERE
                                      atc_id_atividade = " . $row->id_disciplina;
                            $res = mssql_query($sql);

                            $disciplina = mssql_fetch_object($res);


                            $_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['id_disciplina']  = $row->id_disciplina;
                            $_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['carga_horaria']  = $row->carga_horaria;
                            $_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['fora_pacote']  = $row->fora_do_pacote;
                            $_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['dp']        = $row->dp;
                            $_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['valor_desconto']  = $row->valor_desconto;
                            $_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['equivalencia']  = $row->id_equivalencia;
                            $_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['id_classe']      = $row->id_classe;

                       
                            if ($row->fora_do_pacote > 0) {
                              if ($row->dp) {
                                if ($row->id_equivalencia) {
                                  $sql3 = "SELECT
                                              atc_id_atividade,
                                              atc_cd_atividade,
                                              atc_nm_atividade
                                            FROM
                                              academico..ATC_atividade_curricular
                                            WHERE
                                              atc_id_atividade = " . $row->id_equivalencia;
                                  $res3 = mssql_query($sql3);
                                  $row3 = mssql_fetch_object($res3);
                                  $equivalencia = $row3->atc_cd_atividade . " - " . utf8_encode($row3->atc_nm_atividade);
                                }
                                $equivalencia .= "<br>Dependência";
                              } else {
                                if ($row->id_equivalencia) {
                                  $sql3 = "SELECT
																				atc_id_atividade,
																				atc_cd_atividade,
																				atc_nm_atividade
																			FROM
																				academico..ATC_atividade_curricular
																			WHERE
																				atc_id_atividade = " . $row->id_equivalencia;
                                  $res3 = mssql_query($sql3);
                                  $row3 = mssql_fetch_object($res3);
                                  $equivalencia = $row3->atc_cd_atividade . " - " . utf8_encode($row3->atc_nm_atividade);
                                } else {
                                  $equivalencia = "";
                                }
                              }
                            } else {
                              if ($row->dp) {
                                $equivalencia = "Disciplina do pacote<br>Dependência";
                              } else {
                                $equivalencia = "Disciplina do pacote";
                              }
                            }

                        ?>
                            <tr id="grade_montada_<?= $disciplina->atc_id_atividade ?>">
                              <td><strong><?= $disciplina->atc_cd_atividade ?></strong></td>
                              <td><strong><?= utf8_encode($disciplina->atc_nm_atividade) ?></strong></td>
                              <td><strong><?= $equivalencia ?></strong></td>
                              <td>
                                <select id="id_turma_disciplina_<?= $disciplina->atc_id_atividade ?>" onchange="definir_turma('<?= $disciplina->atc_id_atividade ?>')" class="form-control select2">
                                  <?
                                  print_r($disciplina);

                                  $sql10 = "SELECT
                                              cla_id_classe, cla_ds_classe
                                            FROM
                                              academico..CLA_CLASSE 
                                            WHERE
                                              cla_id_periodo_letivo = $dados->id_semestre 
                                              AND cla_id_atividade_curricular = $disciplina->atc_id_atividade
                                              AND cla_st_tipo_classe <> 3";
                                  $res10 = mssql_query($sql10);
                                  while ($row10 = mssql_fetch_object($res10)) {
                                    $selecionado = '';
                                    //echo $row->id_classe." - ".$row10->cla_id_classe;
                                    if ($row->id_classe) {
                                      if ($row->id_classe == $row10->cla_id_classe) {
                                        $selecionado = 'selected=""';
                                      }
                                    } else {
                                      $id_classe = get_classe($dados->id_semestre, $disciplina->atc_id_atividade, $dados->id_pessoa);
                                      if ($id_classe == $row10->cla_id_classe) {
                                        $selecionado = 'selected=""';
                                      }
                                    }
                                    //$_SESSION['ficha_financeira']['disciplinas'][$disciplina->atc_id_atividade]['id_classe']  = $row10->cla_id_classe;
                                  ?>
                                    <option <?= $selecionado ?> value="<?= $row10->cla_id_classe ?>"><?= utf8_encode($row10->cla_ds_classe) ?></option>
                                  <?
                                  }
                                  ?>
                                </select>
                                <?
                                if ($row->id_classe) {
                                  $horario = get_horario($disciplina->atc_id_atividade, $row->id_classe, $dados->id_semestre);
                                  if (count($horario)) {
                                    foreach ($horario as $key => $value) {
                                      if (isset($value->HRC_DS_DIA_SEMANA)) {
                                        if ($value->HRC_DS_DIA_SEMANA == $value->HRC_NM_DIA_SEMANA) {
                                ?>
                                          <script type="text/javascript">
                                            alocar_horario('<?= trim($value->HRC_DS_DIA_SEMANA) ?>', '<?= $value->HRC_HR_INICIO ?>', '<?= $value->HRC_HR_TERMINO ?>', '<?= trim($disciplina->atc_cd_atividade) ?>', '<?= trim(utf8_encode($disciplina->atc_nm_atividade)) ?>');
                                          </script>
                                        <?
                                        } else {
                                          $inicio = substr($value->HRC_HR_INICIO, 0, 2) . ":" . substr($value->HRC_HR_INICIO, 2, 2);
                                          $termino = substr($value->HRC_HR_TERMINO, 0, 2) . ":" . substr($value->HRC_HR_TERMINO, 2, 2);
                                        ?>
                                          <script type="text/javascript">
                                            alocar_horario_quinzenal('<?= trim($value->HRC_NM_DIA_SEMANA) ?>', '<?= trim($value->HRC_DS_DIA_SEMANA) ?>', '<?= $inicio ?>', '<?= $termino ?>', '<?= trim($disciplina->atc_cd_atividade) ?>', '<?= trim(utf8_encode($disciplina->atc_nm_atividade)) ?>', '<?= trim(utf8_encode($value->HRC_DS_SALA)) ?>');
                                          </script>
                                <?
                                        }
                                      }
                                    }
                                  }
                                }
                                ?>
                              </td>
                              <td class="text-center"><strong><?= $row->carga_horaria ?></strong></td>
                              <?
                              if ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 1 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 2 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 13) {
                              ?>
                                <td class="text-center">
                                  <?
                                  if ($row->dp) {
                                  ?>
                                    <input <?php echo isset($dados->ze_antonio) ? "" : "" ?> onchange="definir_desconto_dp('<?= $disciplina->atc_id_atividade ?>')" id="desconto_dp<?= $disciplina->atc_id_atividade ?>" value="<?= intval($row->valor_desconto) ?>" title="<?= $disciplina->atc_cd_atividade ?>" class="form-control">
                                  <?
                                  } else {
                                  ?>
                                    <button onclick="tornar_dp('<?= trim($disciplina->atc_id_atividade) ?>')" type="button" class="btn btn-default">DP</button>
                                  <?
                                  }
                                  ?>
                                </td>
                              <?
                              }
                              ?>
                              <td class="text-center"><strong><a onclick="remover_disciplina_pacote('<?= trim($disciplina->atc_id_atividade) ?>')" id="bt_remover<?= $disciplina->atc_id_atividade ?>" href="javascript:void(0);" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a></strong></td>
                            </tr>

                        <?
                          }
                        }
                        ?>


                      </tbody>

                      <tfoot>
                        <tr style="background-color: #ddd">
                          <td class="text-right" colspan="5"><strong>CARGA HORÁRIA DA FICHA</strong></td>
                          <td class="text-center" colspan="2"><strong id="ch_total_ficha">0</strong></td>
                        </tr>
                        <?
                        if ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 1 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 2) {
                        ?>
                          <tr style="background-color: #eee">
                            <td class="text-right" colspan="5"><strong>VALOR TOTAL DP</strong></td>
                            <td class="text-center" colspan="2">
                              <strong>
                                <span id="valor_dp">0,00</span>
                                <input id="valor_dp_semestre_input" type="hidden">
                              </strong>
                            </td>
                          </tr>
                          <tr style="background-color: #eee">
                            <td class="text-right" colspan="5"><strong>VALOR TOTAL SEM DP</strong></td>
                            <td class="text-center celula_destaque" colspan="2" id="destaque_valor_semestre_sem_dp">
                              <strong>
                                <span id="valor_semestre_sem_dp">0,00</span>
                                <input id="valor_total_semestre_input" type="hidden">
                              </strong>
                            </td>
                          </tr>
                          <tr style="background-color: #eee">
                            <td class="text-right" colspan="5"><strong>VALOR TOTAL COM DESCONTO DE DP</strong></td>
                            <td class="text-center" colspan="2"><strong><span id="valor_total_semestre_com_desconto">0,00</span></strong></td>
                          </tr>
                        <?
                        }
                        ?>
                        <tr style="background-color: #ccc">
                          <td class="text-right" colspan="5"><strong>VALOR TOTAL</strong></td>
                          <td class="text-center celula_destaque" colspan="2" id="destaque_valor_total_semestre">
                            <strong>
                              <span id="valor_total_semestre">0,00</span>
                            </strong>
                          </td>
                        </tr>

                        <tr style="background-color: #bbb">
                          <td class="text-right" colspan="5"><strong>PREVISÃO DA MENSALIDADE</strong></td>
                          <td class="text-center" colspan="2"><strong><span id="valor_previsao_mensalidade">0,00</span></strong></td>
                        </tr>
                      </tfoot>
                    </table>
                    <!-- datatable end -->
                  </div>
                </div>
                <?
                if ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 1 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 2) {
                  if (isset($_GET['id'])) {
                ?>
                    <div class="form-row" id="cronograma_container" style="display:<?php echo $dados->id_parecer_reducao == "1" ? "none" : "" ?>;">
                      <div class="col-xl-12">
                        <!-- datatable start -->
                        <label class="form-label" for="select2-ajax">
                          Pagamentos Mensalidade
                        </label>
                        <table id="" class="table table-bordered table-hover table-striped w-100">
                          <thead>
                            <tr>
                              <td class="text-center"><strong>1º</strong></td>
                              <td class="text-center"><strong>2º</strong></td>
                              <td class="text-center"><strong>3º</strong></td>
                              <td class="text-center"><strong>4º</strong></td>
                              <td class="text-center"><strong>5º</strong></td>
                              <td class="text-center"><strong>6º</strong></td>
                            </tr>
                          </thead>

                          <tbody>
                            <tr>
                              <td><input id="pagamento1" title="1" autocomplete="off" name="valores_valor[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_1, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento2" title="2" autocomplete="off" name="valores_valor[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_2, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento3" title="3" autocomplete="off" name="valores_valor[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_3, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento4" title="4" autocomplete="off" name="valores_valor[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_4, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento5" title="5" autocomplete="off" name="valores_valor[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_5, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento6" title="6" autocomplete="off" name="valores_valor[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_6, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                            </tr>
                            <tr>
                              <td><strong id="convalidacao_adaptacao"><?= isset($dados->diferenca2) && $dados->diferenca2 >= 0 ? "Adaptação" : "Convalidação" ?></strong></td>
                              <td><input id="diferenca2" title="2" autocomplete="off" name="valores_valor_diferenca[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->diferenca2, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="diferenca3" title="3" autocomplete="off" name="valores_valor_diferenca[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->diferenca3, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="diferenca4" title="4" autocomplete="off" name="valores_valor_diferenca[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->diferenca4, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="diferenca5" title="5" autocomplete="off" name="valores_valor_diferenca[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->diferenca5, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="diferenca6" title="6" autocomplete="off" name="valores_valor_diferenca[]" class="form-control moeda moeda_calculo" type="text" value="<?= isset($_GET['id']) ? number_format($dados->diferenca6, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                            </tr>
                          </tbody>

                        </table>


                        <label class="form-label" for="select2-ajax">
                          Pagamentos DP
                        </label>
                        <table id="" class="table table-bordered table-hover table-striped w-100">
                          <thead>
                            <tr>
                              <td class="text-center"><strong>1º</strong></td>
                              <td class="text-center"><strong>2º</strong></td>
                              <td class="text-center"><strong>3º</strong></td>
                              <td class="text-center"><strong>4º</strong></td>
                              <td class="text-center"><strong>5º</strong></td>
                              <td class="text-center"><strong>6º</strong></td>
                            </tr>
                          </thead>

                          <tbody>
                            <tr>
                              <td><input id="pagamento1dp" title="1" autocomplete="off" name="valores_valordp[]" class="form-control moeda moeda_dp" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_1dp, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento2dp" title="2" autocomplete="off" name="valores_valordp[]" class="form-control moeda moeda_dp" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_2dp, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento3dp" title="3" autocomplete="off" name="valores_valordp[]" class="form-control moeda moeda_dp" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_3dp, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento4dp" title="4" autocomplete="off" name="valores_valordp[]" class="form-control moeda moeda_dp" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_4dp, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento5dp" title="5" autocomplete="off" name="valores_valordp[]" class="form-control moeda moeda_dp" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_5dp, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                              <td><input id="pagamento6dp" title="6" autocomplete="off" name="valores_valordp[]" class="form-control moeda moeda_dp" type="text" value="<?= isset($_GET['id']) ? number_format($dados->parcela_6dp, 2, ',', '.') : '0' ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
                            </tr>
                          </tbody>


                        </table>

                        <table id="" class="table table-bordered table-hover table-striped w-100">
                          <thead>
                            <tr>
                              <td><strong id="descricao_diferenca">Reembolso</strong></td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><strong id="reembolso"><?= isset($_GET['id']) ? number_format($dados->reembolso, 2, ',', '.') : '0' ?></strong></td>
                              <input id="valor_reembolso" name="reembolso" type="hidden" value="">
                            </tr>
                          </tbody>
                        </table>
                        <label class="form-label" for="select2-ajax">
                          Obervações Tesouraria
                        </label>
                        <textarea class="form-control" name="tesouraria"><?= isset($dados->tesouraria) ? $dados->tesouraria : "" ?></textarea>
                        <!-- datatable end -->
                      </div>
                    </div>


                <?
                  }
                }
                ?>
              </div>
            </div>
          </div>
          <a name="notificar">
            <div class="panel-container show">

              <div class="panel-content p-0">
                <div class="panel-content">

                  <div class="custom-control custom-switch">
                    <input type="hidden" id="select_choque_autorizado_hidden" name="choque_autorizado" value="<?php echo isset($dados->choque_autorizado) && $dados->choque_autorizado ? "true" : "false" ?>">
                    <input onchange="$('#select_choque_autorizado_hidden').val(this.checked)" <?php echo isset($dados->choque_autorizado) && $dados->choque_autorizado ? "checked" : "" ?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_choque_autorizado">
                    <label class="custom-control-label" for="select_choque_autorizado">Choque de horário autorizado</label>
                  </div>

                  <div class="custom-control custom-switch mt-4">
                    <input type="hidden" id="select_pre_requisito_autorizado_hidden" name="pre_requisito_autorizado" value="<?php echo isset($dados->pre_requisito_autorizado) && $dados->pre_requisito_autorizado ? "true" : "false" ?>">
                    <input onchange="$('#select_pre_requisito_autorizado_hidden').val(this.checked)" <?php echo isset($dados->pre_requisito_autorizado) && $dados->pre_requisito_autorizado ? "checked" : "" ?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_pre_requisito_autorizado">
                    <label class="custom-control-label" for="select_pre_requisito_autorizado">Pré-requisito autorizado</label>
                  </div>

                </div>
              </div>

              <div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row justify-content-between">
                <?
                if (isset($_GET['id']) && ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 10 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 12 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 1 || $_SESSION['coopex']['usuario']['id_pessoa'] == 1000188516)) {
                ?>
                  <button type="button" onclick="alterar_link_whatsapp()" class="btn btn-default col" data-toggle="modal" data-target="#default-example-modal-lg-center">Notificar Acadêmico</button>
                  <?
                  if (isset($_GET['id'])) {
                  ?>
                    <button type="button" onclick="clonar()" class="btn btn-danger col ml-4">Clonar Ficha Financeira</button>
                  <?
                  }
                }

                if (isset($_GET['id']) && ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3)) {
                  $sql = "SELECT
                            *
                          FROM
                            ficha_financeira.ficha_financeira_etapa
                          WHERE
                            id_etapa = 5
                          AND id_ficha_financeira = " . $_GET['id'];
                  $enviado = $coopex->query($sql);
                  if ($enviado->rowCount()) {
                  ?>
                    <button type="button" class="btn btn-default col" data-toggle="modal" data-target="#default-example-modal-lg-center">Notificar Acadêmico</button>
                    <button type="button" class="btn btn-info col ml-4" onclick="encaminhar_naae()">Solicitar Desconto de DPs</button>
                    <button type="button" class="btn btn-success col ml-4" onclick="finalizar_ficha_financeira()">Finalizar Ficha Financeira</button>
                    <button type="button" class="btn btn-info col ml-4" onclick="encaminhar_cres()">Encaminhar para o CRES/FIES</button>
                    <!-- <button type="button" class="btn btn-warning ml-4" onclick="aguardando_dp()">Aguardando desconto de DPS</button> -->
                  <?
                  } else {
                  ?>
                    <h3 class="text-danger"><strong>Aguarde! Ficha não enviada para a Tesouraria pela Secretaria Acadêmica</strong></h3>
                  <?
                  }
                }

                if (
                  $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 13
                  || $_SESSION['coopex']['usuario']['id_pessoa'] == '5000207002'
                  || $_SESSION['coopex']['usuario']['id_pessoa'] == '1000188516'
                ) {
                  ?>
                  <button onclick="enviar_para_tesouraria()" class="btn btn-success col-2" type="button">Enviar para a Tesouraria</button>
                  <button onclick="enviar_para_coordenacao()" class="btn btn-danger ml-4 col-2" type="button">Devolver para a Coordenação</button>
                  <textarea placeholder="Mensagem para a Coordenação" id="obs" class="form-control ml-4 col"></textarea>
                <?
                }

                if ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 16) {
                ?>
                  <button onclick="deferir_dp()" class="btn btn-success col-2" type="text">Deferir Desconto de DPs</button>
                  <button onclick="indeferir_dp()" class="btn btn-danger ml-4 col-2" type="text">Indeferir Desconto de DPs</button>
                  <textarea placeholder="Mensagem para a Tesouraria" id="obs" class="form-control ml-4 col"></textarea>
                <?
                }

                $tipos_usuario_permitidos = array(1, 2, 3, 13);
                if (in_array($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'], $tipos_usuario_permitidos)) {
                ?>
                  <button class="btn btn-primary ml-4 col-2" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
                  <?
                } else {
                  if (isset($_GET['id'])) {
                    $sql = "SELECT
											* 
										FROM
											ficha_financeira.ficha_financeira_etapa 
										WHERE
											( id_etapa = 5 ) 
											AND id_ficha_financeira = " . $_GET['id'];
                    $res = $coopex->query($sql);
                    if (!$res->rowCount()) {
                  ?>
                      <button class="btn btn-primary ml-4 col-2" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
                    <?
                    }
                  } else {
                    ?>
                    <button class="btn btn-primary ml-4 col-2" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
                <?
                  }
                }
                ?>
              </div>

            </div>
        </div>
      </div>
    </div>



    <!-- <textarea class="d-none" name="cronograma" id="cronograma" rows="10" cols="100"></textarea> -->
  </form>



  <?
  if (isset($_GET['id'])) {
    $id_registro = $_GET['id'];
    $sql = "SELECT
					* 
				FROM
					ficha_financeira.ficha_financeira_etapa 
				WHERE
					id_ficha_financeira = $id_registro 
					AND contato <> ''
					AND forma_contato = 1
				ORDER BY
					data_cadastro DESC";
    $res = $coopex->query($sql);
    if ($res->rowCount()) {
      $whats = $res->fetch(PDO::FETCH_OBJ);
      $whats = $whats->contato;
    } else {
      $whats = $_SESSION['ficha_financeira']['whatsapp'];
    }

    $sql = "SELECT
					* 
				FROM
					ficha_financeira.ficha_financeira_etapa 
				WHERE
					id_ficha_financeira = $id_registro 
					AND contato <> ''
					AND forma_contato = 2
				ORDER BY
					data_cadastro DESC";
    $res = $coopex->query($sql);
    if ($res->rowCount()) {
      $email = $res->fetch(PDO::FETCH_OBJ);
      $email = $email->contato;
    } else {
      $email = $_SESSION['ficha_financeira']['email'];
    }
  }


  ?>

  <input type="hidden" id="numero_whatsapp" value="<?= $whats ?>">
  <input type="hidden" id="contato_email" value="<?= $email ?>">

  <link rel="stylesheet" media="screen, print" href="css/fa-brands.css">
  <link rel="stylesheet" media="screen, print" href="css/fa-regular.css">

  <div class="modal fade" id="default-example-modal-lg-center" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Notificar Acadêmico</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Notificar por Whatsapp</label>
            <div class="input-group input-group-lg bg-white shadow-inset-2">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
                  <i class="fab fa-whatsapp" style="font-size: 24px"></i>
                </span>
              </div>
              <input type="text" onkeyup="alterar_link_whatsapp()" class="form-control border-left-0 bg-transparent pl-0" id="whatsapp" value="<?= $whats ?>">
              <div class="input-group-append">
                <?
                //echo $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'];
                if ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3) {

                ?>
                  <a onclick="notificar_aluno_tesouraria()" class="btn btn-default waves-effect waves-themed" type="button">Enviar</a>
                <?
                } else {
                ?>
                  <a id="link_whatsapp" href="" target="_blank" onclick="notificar_aluno_coordenacao()" class="btn btn-default waves-effect waves-themed" type="button">Enviar</a>
                <?
                }
                ?>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Notificar por e-mail</label>
            <div class="input-group input-group-lg bg-white shadow-inset-2">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
                  <i class="fal fa-at" style="font-size: 24px"></i>
                </span>
              </div>
              <input type="text" class="form-control border-left-0 bg-transparent pl-0" id="email" value="<?= $email ?>">
              <div class="input-group-append">
                <button onclick="<?= $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3 ? "notificar_aluno_tesouraria_email()" : "notificar_aluno_coordenacao_email()" ?>" class="btn btn-default waves-effect waves-themed" type="button">Enviar</button>
              </div>
            </div>
          </div>

          <input type="hidden" id="nome_academico">


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="https://www2.fag.edu.br/coopex/js/jquery.maskMoney.min.js" type="text/javascript"></script>
<script>
  var id_registro_ficha = 0;

  function alterar_link_whatsapp(id_registro = '') {
    id_registro_ficha = id_registro;

    <?
    if (isset($_GET['id'])) {
      echo "id_registro = " . $_GET['id'] . ";";
    }
    ?>
    var texto = $("#nome_academico").val();
    texto += "<?= urlencode("\n\nDeclaração de aceite para alteração de disciplinas e previsão de mensalidades:\nO(A) ACADÊMICO(A) que esta subscreve manifesta, desde já, a ciência de que as alterações de disciplinas na grade curricular do presente semestre poderão acarretar em mudanças no valor das mensalidades.\nPara aprovar acesse o link:\n"); ?>"
    texto += "https://coopex.fag.edu.br/ficha_financeira/aprovacao/" + id_registro;

    var link = "https://api.whatsapp.com/send?phone=55" + $("#whatsapp").val() + "&text=" + texto;
    console.log(link);
    <?
    if ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3) {
    } else {
      echo '$("#link_whatsapp").attr("href", link);';
    }
    ?>

  }

  function moeda(valor) {
    valor = valor.replace('R$ ', '');
    valor = valor.replace('.', '');
    valor = valor.replace(',', '.');
    return parseFloat(valor);
  }

  function moeda2(valor) {
    //console.log(typeof(valor));
    if (typeof(valor) == 'string') {
      valor = valor.replace(',', '.');
      return parseFloat(valor);
    }
  }

  function calculo_pagamento(id, valor) {
    valor = moeda(valor);
    total = moeda($("#valor_total_semestre_input").val());

    var soma = 0;
    var parcela = 0;
    for (var i = 1; i <= id; i++) {
      soma += moeda($("#pagamento" + i).val());
      parcela = i;
    }

    saldo = total - soma;
    valor_parcela = saldo / (6 - parcela);
    valor_reembolso = soma - total;

    for (var i = (parseInt(id) + 1); i <= 6; i++) {
      var valor = valor_parcela.toFixed(2).replace(".", ",");

      if (valor_parcela <= 0) {
        $("#pagamento" + i).val("0");
        $("#valor_reembolso").val(valor_reembolso.toFixed(2).replace(".", ","));
        $("#reembolso").html("R$ " + valor_reembolso.toFixed(2).replace(".", ","));
      } else {
        $("#pagamento" + i).val(valor);
        $("#diferenca" + i).val(valor);
        $("#valor_reembolso").val("");
        $("#reembolso").html("");
      }
    }


    for (var i = parseInt(id) + 1; i <= 6; i++) {
      valor1 = moeda($("#pagamento" + parseInt(id)).val());
      valor2 = moeda($("#pagamento" + i).val());

      valor = valor2 - valor1;

      if (valor >= 0) {
        $("#convalidacao_adaptacao").html("Adaptação");
      } else {
        $("#convalidacao_adaptacao").html("Convalidação");
      }

      $("#diferenca" + i).val(valor.toFixed(2).replace(".", ","));

    }

  }

  function calculo_pagamento_dp(id, valor) {
    valor = moeda(valor);
    total = moeda($("#valor_dp_semestre_input").val());

    var soma = 0;
    var parcela = 0;
    for (var i = 1; i <= id; i++) {
      soma += moeda($("#pagamento" + i + "dp").val());
      parcela = i;
    }

    saldo = total - soma;
    valor_parcela = saldo / (6 - parcela);
    valor_reembolso = soma - total;

    for (var i = (parseInt(id) + 1); i <= 6; i++) {
      var valor = valor_parcela.toFixed(2).replace(".", ",");

      if (valor_parcela <= 0) {
        $("#pagamento" + i + "dp").val("0");
      } else {
        $("#pagamento" + i + "dp").val(valor);
      }
    }
  }


  //MENSAGEM DE CADASTRO OK
  function cadastroOK(operacao, id_registro) {
    var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
    Swal.fire({
      type: "success",
      title: msg,
      showConfirmButton: false,
      timer: 1500,
      onClose: () => {
        window.location.href = "/ficha_financeira/cadastro/" + id_registro + "#notificar";
        $('#default-example-modal-lg-center').modal();
      }
    });
  }

  //MENSAGEM DE FALHA NO CADASTRO
  function cadastroFalha(operacao) {
    var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
    Swal.fire({
      type: "error",
      title: msg,
      showConfirmButton: false,
      timer: 1500
    });
  }

  //CARREGAS AS GRADES DO CURSO
  function carrega_grade(id_grade = '') {

    $("#id_grade").attr("disabled", true);

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_grade.php", {
        id_curso: $("#id_curso").val()
      })
      .done(function(json) {
        $("#id_grade").empty();
        $("#id_grade").append("<option value=''>Seleciona a Grade</option>");
        $.each(json, function(i, item) {
          $("#id_grade").append('<option value="' + item.id_curriculo + '">' + item.grade + '</option>');
          $('#id_grade option[value=' + item.id_curriculo + ']').attr('selected', 'selected');
          carrega_semestre(item.id_curriculo);
        });
        if (id_grade) {
          $('#id_grade option[value=' + id_grade + ']').attr('selected', 'selected');
          <?php
          if (isset($_GET['id'])) {
            if (isset($dados->id_semestre)) {
              echo "carrega_semestre(" . $dados->id_semestre . ");";
            }
          }
          ?>
        }
        $("#id_grade").attr("disabled", false);
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }


  //CARREGAS AS GRADES DO CURSO
  function carrega_turma(id_turma) {
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_turma.php", {
        id_semestre: $("#id_semestre").val(),
        id_curso: $("#id_curso").val()
      })
      .done(function(json) {
        $.each(json, function(i, item) {
          selecionado = item.id_pacote == id_turma ? 'selected=""' : "";
          $("#id_turma").append('<option ' + selecionado + ' value="' + item.id_pacote + '">' + item.pacote + '</option>');
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  //CARREGAS OS SEMESTRE LETIVOS DO CURSO
  function carrega_semestre(id_semestre = '') {

    var id_grade = $('#id_grade').select2('data')
    id_grade = id_grade[0].text;

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_semestre.php", {
        id_grade: id_grade
      })
      .done(function(json) {
        $("#id_semestre").empty();
        $("#id_semestre").append("<option value=''>Seleciona o Semestre</option>");
        $.each(json, function(i, item) {
          $("#id_semestre").append('<option value="' + item.id_periodo_letivo + '">' + item.periodo_letivo + '</option>');
          $('#id_semestre option[value=' + item.id_periodo_letivo + ']').attr('selected', 'selected');
        });
        if (id_semestre) {
          $('#id_semestre option[value=' + id_semestre + ']').attr('selected', 'selected');
        }
        $("#id_semestre").attr("disabled", false);
        habilita_academico();

      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }



  //CARREGAS AS DISCIPLINAS EQUIVALENTE MEDIANTE PESQUISA DO USUÁRIO
  function carrega_disciplina_equivalente() {
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_disciplina_geral.php", {
        id_curso: $("#id_curso").val()
      })
      .done(function(json) {
        $("#id_equivalente").empty();
        $("#id_equivalente").append("<option value=''>Seleciona a Disciplina</option>");
        $.each(json, function(i, item) {

          $("#id_equivalente").append('<option value="' + item.atc_id_atividade + '">' + item.atc_nm_atividade + '</option>');
          $("#id_equivalente").attr("disabled", false);
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function ativar_select2() {
    //SELECT DISCIPLINA	
    $(".js-consultar-disciplina-equivalente").select2({
      ajax: {
        url: "modulos/ficha_financeira/cadastro/ajax/carrega_disciplina_equivalente.php",
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return {
            q: params.term, // search term
            page: params.page
          };
        },
        processResults: function(data, params) {
          params.page = params.page || 1;

          return {
            results: data.items,
            pagination: {
              more: (params.page * 30) < data.total_count
            }
          };
        },
        cache: true
      },
      escapeMarkup: function(markup) {
        return markup;
      },
      minimumInputLength: 2,
      templateResult: formatoDisciplina,
      templateSelection: formatoTextoDisciplina
    });

    $(".js-consultar-disciplina-equivalente").change(function() {
      var aux = this.value;
      str = aux.split(":");

      definir_equivalencia(this.title, str[0], str[1], str[2]);
    });
  }

  function definir_equivalencia(id_disciplina, id_equivalente, ch, id_unidade_responsavel) {

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/definir_equivalencia.php", {
        id_disciplina: id_disciplina,
        id_equivalente: id_equivalente,
        ch: ch,
        id_unidade_responsavel,
        id_unidade_responsavel
      })
      .done(function(json) {
        if (json) {
          $("#carga_horaria_equivalente" + id_disciplina).html(json);
          carrega_valor_ficha();
        }

      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function definir_valor_mensalidade_fisioterapia() {

    valor_desconto = $("#valor_fisio").val();

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/definir_valor_mensalidade_fisioterapia.php", {
        valor_desconto: valor_desconto
      })
      .done(function(json) {
        if (json) {
          carrega_valor_ficha();
        }
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function definir_desconto_dp(id_disciplina) {

    valor_desconto = ($("#desconto_dp" + id_disciplina).val());

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/definir_desconto_dp.php", {
        id_disciplina: id_disciplina,
        valor_desconto: valor_desconto
      })
      .done(function(json) {
        carrega_valor_ficha();
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function definir_turma(id_disciplina, id_classe = '') {

    if (!id_classe) {
      id_classe = $("#id_turma_disciplina_" + id_disciplina).val();
    }

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/definir_turma.php", {
        id_disciplina: id_disciplina,
        id_classe: id_classe
      })
      .done(function(json) {

      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function tornar_dp(id_disciplina) {

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/tornar_dp.php", {
        id_disciplina: id_disciplina,
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>
      })
      .done(function(json) {
        document.location.reload(true);
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function clonar() {

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/clonar.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>
      })
      .done(function(json) {
        var msg = "Ficha clonada com sucesso!<br><br>Direcionando para a nova ficha...";
        Swal.fire({
          type: "success",
          title: msg,
          showConfirmButton: false,
          timer: 2000,
          onClose: () => {
            window.location.href = "/ficha_financeira/cadastro/" + json;
          }
        });

      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });

  }

  function habilita_academico() {
    $("#id_pessoa").attr("disabled", false);
    $("#id_disciplina").attr("disabled", false);
  }

  $(document).ready(function() {

    $.ajaxSetup({
      async: false
    });

    $(".moeda_calculo").keyup(function() {
      calculo_pagamento(this.title, this.value);
    });
    $(".moeda_dp").keyup(function() {
      calculo_pagamento_dp(this.title, this.value);
    });


    $('.moeda').maskMoney();

    //CARREGA OS DADOS DOS SELECTS DEPENDENTES QUANDO EDITAR O REGISTRO
    <?php
    if (isset($_GET['id'])) {
      if (isset($dados->id_semestre)) {
        echo "carrega_pacote($dados->id_turma);";
      }
    }
    ?>

    $(":input").inputmask();
    $('.select2').select2();

    $("#id_curso").change(function() {
      carrega_grade();
    });

    $("#id_turma").change(function() {
      id_turma = $("#id_turma").val();
      console.log(id_turma);
      carrega_pacote(id_turma);
    });

    $("#id_pessoa").change(function() {
      carrega_pacote();
      carrega_dp();
    });

    $("#id_grade").change(function() {
      carrega_semestre();
    });

    $("#id_semestre").change(function() {
      habilita_academico();
    });

    $("#id_disciplina").change(function() {
      id_disciplina = $("#id_disciplina").val()
      incluir_disciplina_fora(id_disciplina);
    });

    //SELECT DISCIPLINA	
    $(".js-consultar-disciplina").select2({
      ajax: {
        url: "modulos/ficha_financeira/cadastro/ajax/carrega_disciplina_geral.php",
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return {
            q: params.term, // search term
            page: params.page
          };
        },
        processResults: function(data, params) {
          params.page = params.page || 1;

          return {
            results: data.items,
            pagination: {
              more: (params.page * 30) < data.total_count
            }
          };
        },
        cache: true
      },
      escapeMarkup: function(markup) {
        return markup;
      },
      minimumInputLength: 2,
      templateResult: formatoDisciplina,
      templateSelection: formatoTextoDisciplina
    });

    var total_global = 0;
    //SELECT USUÁRIO
    $(".js-consultar-usuario").select2({
      ajax: {
        url: "modulos/_core/buscar_usuario_matriculado.php",
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return {
            q: params.term, // search term
            page: params.page,
            id_periodo_letivo: $("#id_semestre").val(),
            id_curso: $("#id_curso").val()
          };
        },
        processResults: function(data, params) {
          params.page = params.page || 1;

          return {
            results: data.items,
            pagination: {
              more: (params.page * 30) < data.total_count
            }
          };
        },
        cache: true
      },
      placeholder: 'Buscar no banco de dados',
      escapeMarkup: function(markup) {
        return markup;
      }, // let our custom formatter work
      minimumInputLength: 3,
      templateResult: formatoUsuario,
      templateSelection: formatoTextoUsuario
    });


  });

  // Example starter JavaScript for disabling form submissions if there are invalid fields
  (function() {
    'use strict';
    window.addEventListener('load', function() {
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.getElementsByClassName('needs-validation');
      // Loop over them and prevent submission
      var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    }, false);
  })();

  function carrega_pacote(id_turma = '') {

    <?
    if (isset($_GET['id'])) {
    ?>

      id_semestre = <?= $dados->id_semestre ?>;
      id_pessoa = <?= $dados->id_pessoa ?>;
      id_turma = id_turma;



      var id_periodo = $('#id_semestre_letivo').val();

      var ch_total = 0;
      var json_disciplinas;

      id_curso = <?= $dados->id_curso ?>;

    <?
    } else {
    ?>
      id_semestre = $("#id_semestre").val();
      id_pessoa = $("#id_pessoa").val();

      var id_periodo = $('#id_semestre').select2('data')
      id_periodo = id_periodo[0].text;
      id_turma = $("#id_turma").val();

      var ch_total = 0;
      var json_disciplinas;

      id_curso = $("#id_curso").val();
    <?
    }
    ?>
    let id_ficha_financeira = <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>;
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_pacote.php", {
        id_semestre: id_semestre,
        id_pessoa: id_pessoa,
        id_periodo: id_periodo,
        id_curso: id_curso,
        id_turma: id_turma,
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>

      })
      .done(function(json) {
        $("#diciplinas_pacote tbody").empty();
        console.log("id_semestre:" + id_semestre);
        console.log("id_pessoa:" + id_pessoa);
        console.log("id_periodo:" + id_periodo);
        console.log("id_curso:" + id_curso);
        console.log("id_ficha_financeira:" + id_ficha_financeira);
        $.each(json, function(i, item) {
          ch_total += parseInt(item.atc_qt_horas);

          $("#diciplinas_pacote").append('<tr id="grade_original_' + item.atc_cd_atividade + '"><td><strong>' + item.atc_cd_atividade + '</strong></td><td>' + item.atc_nm_atividade + '</td><td class="text-center">' + item.atc_qt_horas + '</td><td class="text-center"><a style="display:none" id="bt_incluido' + item.atc_id_atividade + '" href="javascript:void(0);" class="btn btn-default btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-check"></i></a><a onclick=incluir_disciplina("' + item.atc_id_atividade + '",0) id="bt_incluir' + item.atc_id_atividade + '" href="javascript:void(0);" class="btn btn-primary  btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-plus"></i></a></div></td></tr>');

          if ($("#bt_remover" + item.atc_id_atividade).length) {
            $("#bt_incluido" + item.atc_id_atividade).show();
            $("#bt_incluir" + item.atc_id_atividade).hide();
          }
        });

        carrega_valor_ficha();
        alterar_link_whatsapp();

        $("#total_diciplinas_pacote").html(ch_total);

        <?
        if (!isset($_GET['id'])) {
        ?>
          carrega_dados();
        <?
        }
        ?>
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }


  function carrega_dp() {

    <?
    if (isset($_GET['id'])) {
    ?>
      id_semestre = <?= $dados->id_semestre ?>;
      id_pessoa = <?= $dados->id_pessoa ?>;
      id_periodo = $('#id_semestre_letivo').val();
      id_curso = <?= $dados->id_curso ?>;
    <?
    } else {
    ?>
      id_semestre = $("#id_semestre").val();
      id_pessoa = $("#id_pessoa").val();
      id_periodo = $('#id_semestre').select2('data');
      id_periodo = id_periodo[0].text;
      id_curso = $("#id_curso").val();
    <?
    }
    ?>

    var ch_total = 0;
    var json_disciplinas;


    $.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_dp.php", {
        id_semestre: id_semestre,
        id_pessoa: id_pessoa,
        id_periodo: id_periodo,
        id_curso: id_curso
      })
      .done(function(json) {
        $("#diciplinas_dp tbody").empty();

        $.each(json, function(i, item) {
          ch_total += parseInt(item.atc_qt_horas);

          $("#diciplinas_dp").append('<tr id="grade_original_' + item.atc_cd_atividade + '"><td><strong>' + item.atc_cd_atividade + '</strong></td><td>' + item.atc_nm_atividade + '</td><td class="text-center">' + item.atc_qt_horas + '</td></tr>');
        });

        $("#total_diciplinas_dp").html(ch_total);
        carrega_dados();

      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }


  function horario(id_disciplina, id_classe) {
    /*
        <?
        if (isset($_GET['id'])) {
        ?>
          id_semestre = <?= $dados->id_semestre ?>;
        <?
        } else {
        ?>
          id_semestre = $("#id_semestre").val();
        <?
        }
        ?>

        $.getJSON("modulos/ficha_financeira/cadastro/ajax/consulta_horario.php", {
            id_semestre: id_semestre,
            id_disciplina: id_disciplina,
            id_classe: id_classe
          })
          .done(function(json) {
            console.dir(json);

            $.each(json, function(i, item) {
              alocar_horario(item.HRC_DS_DIA_SEMANA, item.HRC_HR_INICIO, item.HRC_HR_TERMINO, item.atc_cd_atividade, item.atc_nm_atividade)
            });

          })
          .fail(function(jqxhr, textStatus, error) {
            var err = textStatus + ", " + error;
            console.log("Request Failed: " + err);
          });*/
  }

  function incluir_disciplina(id) {

    $("#bt_incluir" + id).hide();
    $("#bt_incluido" + id).show();

    <?
    if (isset($_GET['id'])) {
    ?>
      id_semestre = <?= $dados->id_semestre ?>;
      id_pessoa = <?= $dados->id_pessoa ?>;
      id_periodo = $('#id_semestre_letivo').val();
      id_curso = <?= $dados->id_curso ?>;
      //id_turma 	= <?= isset($dados->id_turma) ? $dados->id_turma : 0 ?>;
      id_turma = $("#id_turma").val();
    <?
    } else {
    ?>
      id_semestre = $("#id_semestre").val();
      id_pessoa = $("#id_pessoa").val();
      id_periodo = $('#id_semestre').select2('data');
      id_periodo = id_periodo[0].text;
      id_curso = $("#id_curso").val();
      id_turma = $("#id_turma").val();
    <?
    }
    ?>

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_pacote_inclusao.php", {
        id_semestre: id_semestre,
        id_pessoa: id_pessoa,
        id_periodo: id_periodo,
        id_disciplina: id,
        id_curso: id_curso,
        id_turma: id_turma
      })
      .done(function(json) {

        $.each(json, function(i, item) {

          origem = '<select id="id_turma_disciplina_' + item.atc_id_atividade + '" onchange="definir_turma(' + item.atc_id_atividade + ')" class="form-control select2">';

          $("#diciplinas_ficha tbody").append('<tr id="grade_montada_' + item.atc_id_atividade + '"><td>' + item.atc_cd_atividade + '</td><td>' + item.atc_nm_atividade + '</td><td>Disciplina do Pacote</td><td>' + origem + '</td><td class="text-center">' + item.atc_qt_horas + '</td><td class="text-center">' + item.dp + '</td><td class="text-center"><a onclick=remover_disciplina_pacote("' + item.atc_id_atividade + '") id="bt_remover' + item.atc_id_atividade + '" href="javascript:void(0);" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a></div></td></tr>');

          $.each(item.classe, function(j, classe) {
            var aux = classe;
            str = aux.split(":")
            //console.log(str[0], item.classe_padrao);

            selecionado = item.classe_padrao == str[0] ? 'selected=""' : "";
            $("#id_turma_disciplina_" + item.atc_id_atividade).append('<option ' + selecionado + ' value="' + str[0] + '">' + str[1] + '</option>');
          })

          horario(item.atc_id_atividade, item.classe_padrao);
          definir_turma(item.atc_id_atividade, item.classe_padrao);

        });

        carrega_valor_ficha();

      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function incluir_disciplina_fora(id) {

    <?
    if (isset($_GET['id'])) {
    ?>
      id_semestre = <?= $dados->id_semestre ?>;
      id_pessoa = <?= $dados->id_pessoa ?>;
      id_periodo = $('#id_semestre_letivo').val();
      id_curso = <?= $dados->id_curso ?>;
    <?
    } else {
    ?>
      id_semestre = $("#id_semestre").val();
      id_pessoa = $("#id_pessoa").val();
      id_periodo = $('#id_semestre').select2('data');
      id_periodo = id_periodo[0].text;
      id_curso = $("#id_curso").val();
    <?
    }
    ?>

    $("#bt_incluir" + id).hide();
    $("#bt_incluido" + id).show();

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_fora_pacote_inclusao.php", {
        id_disciplina: id,
        id_pessoa: id_pessoa,
        id_curso: id_curso,
        id_semestre: id_semestre
      })
      .done(function(json) {

        $.each(json, function(i, item) {
          obrigatorio = "";

          if (item.disciplina_pacote) {
            obrigatorio = "";
          } else if (id_curso == 1000000007 || id_curso == 1000000099) {
            obrigatorio = "";
          } else if (id_curso == 1000000058 || id_curso == 1000000059) {
            obrigatorio = "";
          } else {
            obrigatorio = "required";
          }

          equivalente = '<select ' + obrigatorio + ' title="' + item.atc_id_atividade + '" data-placeholder="Selecione a disciplina..." class="js-consultar-disciplina-equivalente form-control">';
          dp = '<input <?php echo isset($dados->ze_antonio) ? "readonly" : "" ?> onchange="definir_desconto_dp(' + item.atc_id_atividade + ')" id="desconto_dp' + item.atc_id_atividade + '" title="' + item.atc_id_atividade + '"  class="form-control desconto_dp">';
          classe = '<select id="id_turma_disciplina_' + item.atc_id_atividade + '" onchange="definir_turma(' + item.atc_id_atividade + ')" class="form-control select2">';

          $("#diciplinas_ficha tbody").append('<tr id="grade_montada_' + item.atc_id_atividade + '"><td>' + item.atc_cd_atividade + '</td><td>' + item.atc_nm_atividade + '</td><td>' + equivalente + '</td><td>' + classe + '</td><td class="text-center" id="carga_horaria_equivalente' + item.atc_id_atividade + '">' + item.atc_qt_horas + '</td><td class="text-center" id="dp' + item.atc_id_atividade + '">' + dp + '</td><td class="text-center"><a onclick=remover_disciplina_fora_pacote("' + item.atc_id_atividade + '") id="bt_remover' + item.atc_id_atividade + '" href="javascript:void(0);" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a></div></td></tr>');

          $.each(item.classe, function(j, classe) {
            var aux = classe;

            str = aux.split(":")
            //console.log(str[0],item.classe_padrao);
            //alert(str[1]);

            horario(item.atc_id_atividade, str[0]);

            selecionado = item.classe_padrao == str[0] ? 'selected=""' : "";
            $("#id_turma_disciplina_" + item.atc_id_atividade).append('<option ' + selecionado + ' value="' + str[0] + '">' + str[1] + '</option>');
          })
        });

        carrega_valor_ficha();
        ativar_select2();

      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }


  function remover_disciplina_pacote(id) {

    $("#bt_incluir" + id).show();
    $("#bt_incluido" + id).hide();
    $("#grade_montada_" + id).remove();

    <?
    if (isset($_GET['id'])) {
    ?>
      id_semestre = <?= $dados->id_semestre ?>;
      id_pessoa = <?= $dados->id_pessoa ?>;
      id_periodo = $('#id_semestre_letivo').val();
      id_curso = <?= $dados->id_curso ?>;
      id_turma = <?= isset($dados->id_turma) ? $dados->id_turma : 0 ?>;
    <?
    } else {
    ?>
      id_semestre = $("#id_semestre").val();
      id_pessoa = $("#id_pessoa").val();
      id_periodo = $('#id_semestre').select2('data');
      id_periodo = id_periodo[0].text;
      id_curso = $("#id_curso").val();
      id_turma = $("#id_turma").val();
    <?
    }
    ?>


    $.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_pacote_remocao.php", {
        id_semestre: id_semestre,
        id_pessoa: id_pessoa,
        id_periodo: id_periodo,
        id_disciplina: id,
        id_curso: id_curso,
        id_turma: id_turma
      })
      .done(function(json) {

        $.each(json, function(i, item) {
          carrega_valor_ficha();
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function remover_disciplina_fora_pacote(id) {
    $("#bt_incluir" + id).show();
    $("#bt_incluido" + id).hide();
    $("#grade_montada_" + id).remove();

    id_semestre = $("#id_semestre").val();
    id_pessoa = $("#id_pessoa").val();

    var id_periodo = $('#id_semestre').select2('data')
    id_periodo = id_periodo[0].text;
    id_curso = $("#id_curso").val();

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_fora_pacote_remocao.php", {
        id_semestre: id_semestre,
        id_pessoa: id_pessoa,
        id_periodo: id_periodo,
        id_disciplina: id,
        id_curso: id_curso
      })
      .done(function(json) {
        $.each(json, function(i, item) {
          console.log(item);
          carrega_valor_ficha();
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }


  function carrega_dados() {

    $.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_dados.php", {})
      .done(function(json) {
        $.each(json, function(i, item) {
          $("#valor_semestre").html("R$ " + item.valor_semestre);
          $("#valor_hora").html("R$ " + item.valor_hora);
          $("#valor_mensalidade").html("R$ " + item.valor_mensalidade);
          $("#ra").html(item.ra);
          $("#turno").html(item.turno);
          $("#turma").html(item.turma);

          $("#whatsapp").val(item.whatsapp);
          $("#email").val(item.email);
          $("#nome_academico").val(item.nome_academico);

          carrega_turma(item.id_turma);
          alterar_link_whatsapp();

        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function carrega_valor_ficha() {
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_valor_ficha.php", {})
      .done(function(json) {
        $.each(json, function(i, item) {
          $("#ch_total_ficha").html(item.carga_horaria);
          //$("#valor_pacote").html("R$ " + item.valor_pacote);
          //$("#valor_fora_pacote").html("R$ " + item.valor_fora_pacote);
          $("#valor_total_semestre").html("R$ " + item.valor_total_semestre);
          $("#valor_semestre_sem_dp").html("R$ " + item.valor_semestre_sem_dp);
          $("#valor_dp").html("R$ " + item.valor_dp);
          $("#valor_total_semestre_input").val(item.valor_convalidacao);
          $("#valor_dp_semestre_input").val(item.valor_dp);
          $("#valor_previsao_mensalidade").html("R$ " + item.valor_previsao_mensalidade);

          $("#valor_total_semestre_com_desconto").html("R$ " + item.valor_total_semestre_com_desconto);

          $(".celula_destaque").removeClass("valor_destaque");
          $("#" + item.destaque).addClass("valor_destaque");
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function enviar_para_tesouraria() {
    var nome = $("#nome_academico_input").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/enviar_para_tesouraria.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>,
        nome: nome
      })
      .done(function(json) {
        Swal.fire({
          type: "success",
          title: "Notificação enviada para a Tesouraria",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {
            window.history.back();
          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function deferir_dp() {
    var nome = $("#nome_academico_input").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/deferir_dp.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>,
        nome: nome
      })
      .done(function(json) {
        Swal.fire({
          type: "success",
          title: "Notificação enviada para a Tesouraria",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {
            window.history.back();
          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function indeferir_dp() {
    var nome = $("#nome_academico_input").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/indeferir_dp.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>,
        nome: nome
      })
      .done(function(json) {
        Swal.fire({
          type: "success",
          title: "Notificação enviada para a Tesouraria",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {
            window.history.back();
          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function enviar_para_coordenacao() {
    var obs = $("#obs").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/enviar_para_coordenacao.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>,
        obs: obs
      })
      .done(function(json) {
        Swal.fire({
          type: "success",
          title: "Notificação enviada para a Coordenação",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {
            window.history.back();
          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function notificar_aluno_tesouraria() {
    var whats = $("#whatsapp").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/notificar_aluno_tesouraria.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>,
        whats: whats
      })
      .done(function(json) {

        if (json) {
          $('#default-example-modal-lg-center').modal('toggle');
          alert("Notificação enviada com sucesso pelo WhatsApp");
        } else {
          alert("Falha ao enviar pelo WhatsApp, confira o número!");
        }

        /*window.history.back();*/
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function notificar_aluno_coordenacao() {
    var whats = $("#whatsapp").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/notificar_aluno_coordenacao.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>,
        whats: whats
      })
      .done(function(json) {
        window.history.back();
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function notificar_aluno_tesouraria_email() {
    var contato_email = $("#email").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/notificar_aluno_tesouraria_email.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>,
        email: contato_email
      })
      .done(function(json) {
        $('#default-example-modal-lg-center').modal('toggle');
        Swal.fire({
          type: "success",
          title: "Notificação enviada com sucesso",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {

          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function finalizar_ficha_financeira() {
    var email = $("#email").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/finalizar_ficha_financeira.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>
      })
      .done(function(json) {
        Swal.fire({
          type: "success",
          title: "Ficha finalizada com sucesso",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {
            /*window.history.back();*/
          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function encaminhar_cres() {
    var email = $("#email").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/encaminhar_cres.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>
      })
      .done(function(json) {
        Swal.fire({
          type: "success",
          title: "Status definido com sucesso",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {
            /*window.history.back();*/
          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function encaminhar_naae() {
    var email = $("#email").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/encaminhar_naae.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>
      })
      .done(function(json) {
        Swal.fire({
          type: "success",
          title: "Status definido com sucesso",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {
            /*window.history.back();*/
          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function aguardando_dp() {
    var email = $("#email").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/aguardando_dp.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>
      })
      .done(function(json) {
        Swal.fire({
          type: "success",
          title: "Status definido com sucesso",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {
            /*window.history.back();*/
          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }

  function notificar_aluno_coordenacao_email() {
    var email = $("#email").val();
    $.getJSON("modulos/ficha_financeira/cadastro/ajax/notificar_aluno_coordenacao_email.php", {
        id_ficha_financeira: <?= isset($_GET['id']) ? $_GET['id'] : 0 ?>,
        email: email
      })
      .done(function(json) {
        $('#default-example-modal-lg-center').modal('toggle');
        Swal.fire({
          type: "success",
          title: "Notificação enviada com sucesso",
          showConfirmButton: false,
          timer: 1500,
          onClose: () => {
            window.history.back();
          }
        });
      })
      .fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err);
      });
  }
</script>