<?php

/**
 * Entidade Docente
 */

class Docente {
  public $id_docente;
  public $id_titulacao;
  public $nome;
  public $cpf;
  public $ies;
  public $cidade;
  public $valor_hora;
  public $descricao;
  public $curriculo;
  public $foto;
  public $certificado;
  public $nacionalidade;
  public $termo_aceite;
  public $termo_uso_imagem;
  public $excluido;

  /**
   * Instância um Docente
   *
   * Esta classe é um modelo representativo da entidade docente no banco de dados.
   * Cria uma instância de uma Docente
   *
   * @param int $id_docente Id do docente na tabela
   * @param int $id_titulacao Id da titulacao (Fk: titulacao)
   * @param string $nome Nome do docente
   * @param string $cpf Cpf do docente
   * @param string $ies IES (Instituições de Educação Superior) do docente
   * @param string $cidade Cidade do docente
   * @param string $descricao Descricao breve do docente
   * @param string $curriculo Curriculo do docente (podendo conter tags html)
   * @param string $foto Nome do arquivo da foto do docente. Caso o docente não possua uma foto, este campo é definido como `blank.jpg`
   * @param string $certificado Nome do arquivo de certificação do docente.
   * @param string $nacionalidade Nacionalidade do docente - `BR`, `PT`, `UK`, `CA`, `US`
   * @param string $termo_aceite Nome do arquivo do termo de aceite do docente.
   * @param string $termo_uso_imagem Nome do arquivo do termo de uso de imagem do docente.
   * @param string $excluido Define se o docente foi excluido ou não
   * @return Docente
   **/
  public function build($id_docente, $id_titulacao, $nome, $cpf, $ies, $cidade, $descricao, $curriculo, $foto, $certificado, $nacionalidade, $termo_aceite, $termo_uso_imagem, $excluido) {
    $this->id_docente = $id_docente;
    $this->id_titulacao = $id_titulacao;
    $this->nome = $nome;
    $this->cpf = $cpf;
    $this->ies = $ies;
    $this->cidade = $cidade;
    $this->descricao = $descricao;
    $this->curriculo = $curriculo;
    $this->foto = $foto;
    $this->certificado = $certificado;
    $this->nacionalidade = $nacionalidade;
    $this->termo_aceite = $termo_aceite;
    $this->termo_uso_imagem = $termo_uso_imagem;
    $this->excluido = $excluido;
    return $this;
  }
}