<?php
require_once("php/sqlsrv.php");
require_once("php/mysql.php");


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_menu = 82;
$chave = "id_ficha_avaliacao";

unset($_SESSION['ficha_avaliacao']);

if (isset($_GET['id'])) {
  $$chave = $_GET['id'];

  $_SESSION['ficha_avaliacao']['id_pessoa'] = $_GET['id'];

  //CARREGA DADOS DA FICHA AVALIACAO
   $sql = "SELECT
					  *,
					  DATE_FORMAT( data_cadastro, '%d/%m/%Y - %H:%i:%s' ) AS data_cadastro 
				  FROM
					  colegio.ficha_avaliacao
					INNER JOIN coopex.pessoa USING ( id_pessoa )
				  WHERE
					 colegio.ficha_avaliacao.excluido = 0
					AND id_pessoa = " . $_GET['id'];
  $res = $coopex->query($sql);
  if ($res->rowCount()) {
    $dados = $res->fetch(PDO::FETCH_OBJ);
  } else {
    $sql = "SELECT
					*,
					DATE_FORMAT( data_cadastro, '%d/%m/%Y - %H:%i:%s' ) AS data_cadastro 
				FROM
					colegio.ficha_avaliacao
					INNER JOIN coopex.pessoa USING ( id_pessoa )
				WHERE
					colegio.ficha_avaliacao.excluido = 1
					AND id_ficha_avaliacao = " . $_GET['id'];
    $res = $coopex->query($sql);
    if ($res->rowCount()) {
      $sql = "SELECT
							nome 
						FROM
							coopex.log
							INNER JOIN coopex.pessoa USING ( id_pessoa ) 
						WHERE
							tabela = 'colegio.ficha_avaliacao' 
							AND operacao = 3 
							AND id_registro = " . $_GET['id'];
      $res = $coopex->query($sql);
      $dados = $res->fetch(PDO::FETCH_OBJ);
      echo "<h1>Ficha Avaliação excluída por: $dados->nome</h1>";
      exit;
    }
  }
} else {
  $$chave = 0;
}

?>

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
    <li class="breadcrumb-item"><a href="javascript:void(0);">Ficha Avaliação</a></li>
    <li class="breadcrumb-item active">Cadastro</li>
    <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu ?>c</span></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-repeat'></i> Ficha Avaliação
      <small>
        Cadastro de Ficha Avaliação
      </small>
    </h1>

  </div>
  <?php
  $desabilitar_edicao_carga_horaria = false;
  $desabilitar_edicao = false;
  if (isset($_GET['id'])) {
  ?>
  <?php
  }
  ?>

  <iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>
  <form class="needs-validation"  method="post" target="dados" action="modulos/colegio/ficha_avaliacao/cadastro.php">
    <input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>">
    <div class="row">
      <div class="col-xl-12">
        <div id="panel-2" class="panel">
          <div class="panel-hdr">
            <h2>
              1. Ficha Avaliação
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
                  <div class="panel-container show">
                  <div class="panel-content">
                    <div class="form-group">
                      <label class="form-label" for="select2-ajax">
                        Selecione o usuário do Sagres
                      </label>
                      <select onChange="" data-placeholder="Selecio o aluno..." class="js-consultar-usuario form-control" id="select2-ajax"></select>
                    </div>
  
                    <div id="titulos_em_aberto_resultado">
                    <div class="form-row">
                      
                    </div>
                  </div>
                </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> 

 <?php include "scripts/scripts.php" ?>