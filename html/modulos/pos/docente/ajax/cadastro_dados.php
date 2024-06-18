<pre>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../../../php/config.php");
require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");
require_once("./DocenteController.php");

$controller = new DocenteController($coopex);

$id_menu = 109; #ID DO MENU
$tabela = "pos.docente"; #TABELA PRINCIPAL
$chave = "id_docente"; #CAMPO CHAVE DA TABELA

$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";

verificarPermissao($id_menu, $tabela, $chave, $id_registro);

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {
	if (isset($_POST['excluir_registro'])) {
		excluirRegistro($tabela, $chave, $_POST['excluir_registro']);
	} else {

		if (isset($_FILES["foto_docente"]) && !empty($_FILES["foto_docente"]["name"])) {
			$foto = $_FILES["foto_docente"];
			$nome_foto = explode(".", $foto['name']);
			$extensao_foto = count($nome_foto) - 1;
			$foto['name'] = time() . "." . $nome_foto[$extensao_foto];
			$compress = $controller->saveImage($foto);
		} else {
			$foto['name'] = "blank.jpg";
		}

		if ($id_registro) {
			$controller->atualizarDocente($id_registro, $_POST['docente_nome'], $_POST['docente_titulacao'], $_POST['docente_descricao'], $_POST['docente_curriculo'], $_POST['nacionalidade'], $foto['name']);

			echo "<script>parent.cadastroOK(2)</script>";
		} else {

			$data = $controller->cadastrarDocente($_POST['docente_nome'], $_POST['docente_titulacao'], $_POST['docente_descricao'], $_POST['docente_curriculo'], $_POST['nacionalidade'], $foto['name']);

			echo "<script>parent.cadastroOK(1)</script>";
		}

	}
}

?>