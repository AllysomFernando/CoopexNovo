<?php session_start();
if (isset($_SESSION['coopex']['usuario'])) {
  if (isset($_SERVER['HTTP_REFERER'])) {
    if (strlen($_GET['q']) >= 3) {

      include "../../php/config.php";
      require_once 'conect_ant.php';

      $nome = $_GET['q'];
      $evento = $_GET['evento'];

      $sql = "select a.id_inscricao as id, b.nome text,b.cpf from evento_inscricao as a
INNER JOIN evento_pessoa as b on a.id_pessoa = b.id_pessoa
INNER JOIN evento_valores as c on a.id_valor = c.id_valor
where a.id_evento = :id_evento and (b.nome like :pesq1 or b.cpf like :pesq2 or b.email like :pesq) ";

      $stm = $coopex->prepare($sql);
      $stm->bindValue(':pesq1', '%' . $nome . '%');
      $stm->bindValue(':pesq2', '%' . $nome . '%');
      $stm->bindValue(':pesq', '%' . $nome . '%');
      $stm->bindValue(':id_evento', $evento);
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
