<?php

class DocenteController
{

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

  public function uploadFotoPerfil($file)
  {
    try {
      $sanitized_file = $this->sanitizeFile($file);
      $filename = $sanitized_file["name"];
      $tempname = $sanitized_file["tmp_name"];

      $folder = __DIR__ . "/../../../../../images/pos/professores/foto/" . $filename;

      if (move_uploaded_file($tempname, $folder)) {
        return $sanitized_file;
      }
    } catch (\Throwable $th) {
      throw new RuntimeException($th->getMessage());
    }
  }

  private function sanitizeFile($file)
  {
    $foto = $file;
    $nome_foto = explode(".", $foto['name']);
    $extensao_foto = count($nome_foto) - 1;
    $foto['name'] = time() . "." . $nome_foto[$extensao_foto];

    return $foto;
  }

  public function uploadCertificado($certificado)
  {
    $sanitized_file = $this->sanitizeFile($certificado);
    $filename = $sanitized_file["name"];
    $tempname = $sanitized_file["tmp_name"];
    $folder = __DIR__ . "/../../../../../arquivos/pos/professores/certificados/" . $filename;

    if (move_uploaded_file($tempname, $folder)) {
      return $sanitized_file;
    }

    return false;
  }

  public function uploadTermoAceite($certificado)
  {
    $sanitized_file = $this->sanitizeFile($certificado);
    $filename = $sanitized_file["name"];
    $tempname = $sanitized_file["tmp_name"];
    $folder = __DIR__ . "/../../../../../arquivos/pos/professores/termo-aceite/" . $filename;

    if (move_uploaded_file($tempname, $folder)) {
      return $sanitized_file;
    }

    return false;
  }

  public function uploadTermoUsoImagem($certificado)
  {
    $sanitized_file = $this->sanitizeFile($certificado);
    $filename = $sanitized_file["name"];
    $tempname = $sanitized_file["tmp_name"];
    $folder = __DIR__ . "/../../../../../arquivos/pos/professores/termo-uso-imagem/" . $filename;

    if (move_uploaded_file($tempname, $folder)) {
      return $sanitized_file;
    }

    return false;
  }
}
