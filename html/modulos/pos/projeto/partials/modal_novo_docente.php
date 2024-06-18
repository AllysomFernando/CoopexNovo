<div class="modal hide fade" id="novo_docente_modal" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="modulos/pos/projeto/api/routes/novo-docente.php" class="needs-validation" id="form-docente" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Novo Docente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body mb-12">
          <div class="col-xl mb-3">
            <label class="form-label" for="docente_nome">Nome<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="docente_nome" id="docente_nome" placeholder="Nome do docente" required>
            <div class="invalid-feedback">
              Este campo não pode estar vazio
            </div>
          </div>
          <div class="col-xl mb-3">
            <label class="form-label" for="docente_descricao">Descrição <span class="text-danger">*</span></label>
            <input maxlength="200" type="text" class="form-control" name="docente_descricao" id="docente_descricao" placeholder="Escreva uma descrição do docente (até 200 caracteres)" value="" required>
          </div>
          <div class="col-xl mb-3">
            <label class="form-label" for="docente_ies">IES <span class="text-danger">*</span></label>
            <input maxlength="200" type="text" class="form-control" name="docente_ies" id="docente_ies" placeholder="A instituição de ensino em que o docente se formou" value="" required>
          </div>
          <div class="col-xl mb-3">
            <label class="form-label" for="docente_cidade">Cidade <span class="text-danger">*</span></label>
            <input maxlength="200" type="text" class="form-control" name="docente_cidade" id="docente_cidade" placeholder="Cidade onde mora o docente" value="" required>
          </div>
          <div class="col-xl form-row d-flex align-items-end justify-content-center flex-nowrap">
            <div class="col-xl mb-3">
              <label class="form-label" for="docente_cpf">CPF <span class="text-danger">*</span></label>
              <input type="text" class="form-control cpf" name="docente_cpf" id="docente_cpf" placeholder="Cpf do docente" required>
              <div class="invalid-feedback">
                Este campo não pode estar vazio
              </div>
            </div>
            <div class="col-xl mb-3">
              <label class="form-label" for="docente_titulacao">Titulação <span class="text-danger">*</span></label>
              <select id="docente_titulacao" name="docente_titulacao" class="select2 form-control" required>
                <option value="">Selecione a titulação do docente</option>
                <?php foreach ($titulacoes as $titulacao) { ?>
                  <option <?php echo isset($dados) && $titulacao->isSelected($dados->coordenador->titulacao) ? "selected" : "" ?> value="<?php echo $titulacao->id_titulacao ?>">
                    <?php echo $titulacao->titulacao ?>
                  </option>
                <?php
                }
                ?>
              </select>
              <div class="invalid-feedback">
                Selecione a área do curso
              </div>
            </div>
          </div>
          <div class="col-xl mb-3">
            <label class="form-label">Foto de perfil <span class="text-danger">*</span></label>
            <div class="custom-file">
              <input type="file" class="form-control" id="docente_foto" accept=".jpg,.jpeg,.png" name="docente_foto" required>
            </div>
          </div>
          <div class="col-xl mb-3">
            <label class="form-label">Certificado</label>
            <div class="custom-file">
              <input type="file" class="form-control" id="docente_certificado" accept=".pdf" name="docente_certificado">
              <span class="help-block">
                Anexe uma certificação do docente
              </span>
            </div>
          </div>
          <div class="col-xl mb-3">
            <label class="form-label">Termo de aceite <span class="text-danger">*</span></label>
            <div class="custom-file">
              <input type="file" class="form-control" id="docente_aceite" accept=".pdf" name="docente_aceite" required>
              <span class="help-block">
                Anexe o termo de aceite assinado pelo docente
              </span>
            </div>
          </div>
          <div class="col-xl mb-3">
            <label class="form-label">Termo de autorização do uso de imagem <span class="text-danger">*</span></label>
            <div class="custom-file">
              <input type="file" class="form-control" id="docente_uso_imagem" accept=".pdf" name="docente_uso_imagem" required>
              <span class="help-block">
                Anexe o termo de autorização do uso de imagem assinado pelo docente
              </span>
            </div>
          </div>
          <div class="col-xl">
            <label class="form-label" for="docente_curriculo">Currículo</label>
            <textarea class="js-summernote" id="docente_curriculo" name="docente_curriculo"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function validateFileSize(id, maxFileSize) {
    const input = document.querySelector(id);
    const fileSize = input.files[0].size / 1024 / 1024;
    if (fileSize > maxFileSize) {
      alert(`File size exceeds ${maxFileSize} MB`);
      return
    }
  }

  document.querySelector("#docente_certificado").addEventListener('change', () => {
    validateFileSize("#docente_certificado", 5)
  })
</script>