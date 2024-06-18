<?php ?>
<!DOCTYPE html>
<html lang="en">

<?php
$curso_area = '';
$coordenador_titulacao = "";

foreach ($template_data->areas as $area) {
  if ($area->isSelected($template_data->curso->id_area)) {
    $curso_area = $area->area;
  }
}

foreach ($template_data->titulacoes as $titulacao) {
  if ($template_data->coordenador->titulacao == $titulacao->id_titulacao) {
    $coordenador_titulacao = $titulacao->titulacao;
  }
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projeto de pós graduação</title>
</head>

<body>
  <h1>Projeto de pós graduação</h1>

  <div>
    <h2>Proposto Por</h2>
    <ul>
      <li><strong>Nome: </strong><?php echo $template_data->cadastrado_por->nome ?></li>
      <li><strong>E-mail: </strong><?php echo $template_data->cadastrado_por->email ?></li>
      <li><strong>CPF: </strong><?php echo $template_data->cadastrado_por->cpf ?></li>
    </ul>
  </div>

  <div>
    <div>
      <h2>Dados do Curso</h2>
      <ul>
        <li><strong>Nome: </strong><?php echo $template_data->curso->nome ?></li>
        <li><strong>Área do curso: </strong><?php echo $curso_area ?></li>
        <li><strong>Campus: </strong><?php echo $template_data->curso->campus_nome ?></li>
        <li><strong>Local: </strong><?php echo $template_data->realizacao->local ?></li>
      </ul>
    </div>
    <div>
      <h3>Proponentes</h3>
      <ul>
        <li>
          <strong>Instituição: </strong> <?php echo $template_data->proponentes->instituicao ?>
        </li>
        <li>
          <strong>Coordenação Instituicional: </strong> <?php echo $template_data->proponentes->coordenacao_institucional ?>
        </li>
      </ul>
      <div>
        <h4>Coordenador</h4>
        <ul>
          <li>
            <strong>Nome: </strong> <?php echo $template_data->coordenador->nome ?>
          </li>
          <li>
            <strong>Cpf: </strong> <?php echo $template_data->coordenador->cpf ?>
          </li>
          <li>
            <strong>Titulação: </strong> <?php echo $coordenador_titulacao ?>
          </li>
        </ul>
      </div>
      <div>
        <h4>Parceiros</h4>
        <?php
        if ($template_data->parceiros && count($template_data->parceiros) > 0) { ?>
          <ul>
            <?php foreach ($template_data->parceiros as $parceiro) { ?>

              <li><?php echo $parceiro->nome ?> - <?php echo $parceiro->cpf ?></li>

            <?php } ?>
          </ul>
        <?php } else { ?>
          Nenhum parceiro cadastrado
        <?php } ?>
      </div>
    </div>
    <div>
      <h3>Operacionalização</h3>
      <ul>
        <li><strong>Carga horária do curso: </strong><?php echo $template_data->curso->carga_horaria ?>h</li>
        <li><strong>Número de vagas: </strong><?php echo $template_data->curso->numero_vagas ?></li>
        <li><strong>Valor da hora/aula para o professor: </strong>
          <ul>
            <li>Especialista: R$ <?php echo $template_data->valores->especialista ?></li>
            <li>Mestre: R$ <?php echo $template_data->valores->mestre ?></li>
            <li>Doutor: R$ <?php echo $template_data->valores->doutor ?></li>
          </ul>
        </li>
        <li><strong>Período de realização: </strong><?php echo $template_data->realizacao->periodo ?></li>
        <li><strong>Dias da semana: </strong><?php echo $template_data->realizacao->dias_semana ?></li>
        <li><strong>Horário: </strong><?php echo $template_data->realizacao->horario ?></li>
      </ul>
    </div>
    <div>
      <h2>Descrição</h2>
      <div>
        <h3>Público Alvo</h3>
        <p>
          <?php echo nl2br($template_data->descricao->publico_alvo) ?>
        </p>
      </div>
      <div>
        <h3>Perfil do Aluno</h3>
        <p>
          <?php echo nl2br($template_data->descricao->perfil_aluno) ?>
        </p>
      </div>
      <div>
        <h3>Pilares do curso</h3>
        <p>
          <?php echo nl2br($template_data->descricao->pilares_curso) ?>
        </p>
      </div>
      <div>
        <h3>Processo de seleção</h3>
        <p>
          <?php echo nl2br($template_data->descricao->processo_selecao) ?>
        </p>
      </div>
    </div>
    <div>
      <h2>Justificativa de Oferta do curso</h2>
      <p>
        <?php echo nl2br($template_data->justificativa->descricao) ?>
      </p>
      <div>
        <h3>Contribuição do curso</h3>
        <p>
          <?php echo nl2br($template_data->justificativa->contribuicao) ?>
        </p>
      </div>
    </div>
    <div>
      <h2>Objetivos</h2>
      <div>
        <h3>Gerais</h3>
        <p>
          <?php echo nl2br($template_data->objetivos->geral) ?>
        </p>
      </div>
      <div>
        <h3>Específicos</h3>
        <p>
          <?php echo nl2br($template_data->objetivos->especifico) ?>
        </p>
      </div>
    </div>
  </div>
</body>

</html>