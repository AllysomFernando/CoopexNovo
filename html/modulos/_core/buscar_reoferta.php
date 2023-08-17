<?php session_start();
if (isset($_SESSION['coopex']['usuario'])) {
  if (isset($_SERVER['HTTP_REFERER'])) {
    if (strlen($_GET['q']) >= 3) {

      include "../../php/config.php";
      require_once 'conecta.php';

      $nome = $_GET['q'];

      $sql = "SELECT
        a.id_reoferta as id,
        a.disciplina as text,
        a.data_cadastro as nome
      FROM
        reoferta AS a
      WHERE
	    a.disciplina LIKE :pesquisa";
      $stm = $coopex->prepare($sql);
      $stm->bindValue(':pesquisa', '%' . $nome . '%');
      $stm->execute();
      // $stm->store_result();
      $dados = $stm->fetchAll(PDO::FETCH_OBJ);

      $json = '{
				  "total_count": ' .  count($dados);

      if (count($dados)) {
        $json .= ',
					  "incomplete_results": false,
					  "items":';
      } else {
        $json .= ',
					  "incomplete_results": false}';
      }

      // if ($num_rows > 0) {
      foreach ($dados as $row) {
        $result[] = array_map("utf8_encode", $row);
      }
      // }

      if (count($dados)) {
        $json .= json_encode($dados) . "}";
      }
      $result = array('Retorno' => $dados);
      echo $json;
    }
  }
}
