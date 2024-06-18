<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../controller/CursoController.php";
require_once __DIR__ . "/../../factory/RepositoryFactory.php";
require_once __DIR__ . '/../../../../../../php/repository/CoopexPessoaRepository.php';
require_once __DIR__ . '/../../../../../../php/pdf/PdfTemplateHandler.php';

$id_menu = 41;

$tipo_usuario = trim($_SESSION["coopex"]["usuario"]["sistema"]["id_tipo_usuario"]);
$possuiPermissao = isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1]);
$isAdmin = $_SESSION['coopex']['usuario']['sistema']['tipo_usuario'] == "ADMINISTRADOR";

$factory = new RepositoryFactory();
$coopex_pessoa = new CoopexPessoaRepository();
$curso_controller = new CursoController();
$enviado_aprovacao = false;

$titulacoes = $factory->titulacao->getAll();
$areas = $factory->area->getAll();

try {
  $$chave = $_GET['id'];

  $dados = $curso_controller->getCursoById($$chave);
  $pessoa = $coopex_pessoa->getByIdPessoa($dados->curso->id_pessoa);
  $enviado_aprovacao = isset($dados->enviado_aprovacao) && $dados->enviado_aprovacao == true;
  $isProponente = $dados->curso->id_pessoa == $_SESSION['coopex']['usuario']['id_pessoa'];
  $canAccess = ($tipo_usuario == "17" || $tipo_usuario == "21" || $tipo_usuario == "1") && $possuiPermissao;
} catch (Exception $e) {
  echo 'Projeto inválido';
  die;
}
$margins = new stdClass();
$margins->top = 10;
$margins->bottom = 10;
$margins->left = 10;
$margins->right = 10;

$template_handler = new PdfTemplateHandler('Projeto', 'A4', 'fullpage', $margins);

$template_data = $dados;
$template_data->cadastrado_por = $pessoa;
$template_data->titulacoes = $titulacoes;
$template_data->areas = $areas;

$page_sobre = $template_handler->loadPageFile('pos/detalhes-projeto/sobre.php', $template_data);
$template_handler->addPage($page_sobre);
$template_handler->preparePDF();
$template_handler->showPDF('Documento');