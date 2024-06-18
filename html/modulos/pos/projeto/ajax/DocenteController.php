<?php

class DocenteController
{
  public $db;
  public $docentes_selecionados = [];

  function __construct($conexao)
  {
    $this->db = $conexao;
  }

  public function buscarTodosDocentes()
  {
    $sql = "SELECT d.id_docente, d.id_titulacao, d.nome, d.ies FROM pos.docente d WHERE d.nome IS NOT NULL ORDER BY d.nome";

    $res = $this->db->query($sql);
    return $res->fetchAll(PDO::FETCH_OBJ);
  }

  public function adicionarSelecionado($id_docente)
  {
    $sql = "SELECT * FROM pos.docente WHERE id_docente = $id_docente";
    $res = $this->db->query($sql);
    $docente = $res->fetch(PDO::FETCH_OBJ);

    array_push($this->docentes_selecionados, $docente);
  }

  private function compressImage($source_path, $destination_path, $quality)
  {
    $info = getimagesize($source_path);

    if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/JPG' || $info['mime'] == 'image/jpg') {
      $image = imagecreatefromjpeg($source_path);
    } elseif ($info['mime'] == 'image/png') {
      $image = imagecreatefrompng($source_path);
    }

    imagejpeg($image, $destination_path, $quality);

    return $destination_path;
  }
}
