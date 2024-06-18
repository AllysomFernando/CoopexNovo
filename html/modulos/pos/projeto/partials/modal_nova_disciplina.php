<div class="modal hide fade" id="nova_disciplina_modal" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="modulos/pos/projeto/api/routes/nova-disciplina.php" class="needs-validation" id="form-disciplina" method="POST">
      <input type="hidden" name="cadastrado_por" class="form-control" id="id_pessoa" placeholder="" value="<?php echo isset($dados->curso->id_pessoa) ? $dados->curso->id_pessoa : $_SESSION['coopex']['usuario']['id_pessoa'] ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nova Disciplina</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="col-xl mb-3">
            <label class="form-label" for="disciplina">Nome <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="novo_disciplina_nome" id="novo_disciplina_nome" required>
            <div class="invalid-feedback">
              Este campo não pode estar vazio
            </div>
          </div>

          <div class="col-xl form-row d-flex justify-content-center align-items-end flex-nowrap mb-3">
            <div class="col-xl mr-2">
              <label class="form-label" for="validationCustom03">Docentes <span class="text-danger">*</span></label>
              <select id="docentes_select" class="custom-select2-field form-control mr-3">
              </select>
              <div class="invalid-feedback">
                Selecione os docentes do curso
              </div>
            </div>
            <div class="btn btn-primary mr-2" id="adicionarAdisciplina" data-toggle="tooltip" title="Adicionar o docente a esta disciplina"><i class="fal fa-indent"></i></div>
            <div class="btn btn-success" data-toggle="modal" data-toggle="tooltip" data-target="#novo_docente_modal" title="Cadastrar um novo docente"><i class="fal fa-plus"></i></div>
          </div>

          <div class="col-xl mb-3" style="max-height: 300px; overflow-y: scroll">
            <ul class="list-group" id="docentes_list">
            </ul>
            <textarea class="d-none" name="docentes" id="docentes_log" rows="10" cols="100">[]</textarea>
          </div>

          <div class="col-xl mb-3">
            <label class="form-label" for="novo_disciplina_ch">Carga Horária <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="novo_disciplina_ch" id="novo_disciplina_ch" required>
            <div class="invalid-feedback">
              Defina a carga horária
            </div>
          </div>

          <div class="col-xl">
            <label class="form-label" for="novo_disciplina_ementa">Ementa <span class="text-danger">*</span></label>
            <textarea class="form-control" name="novo_disciplina_ementa" id="novo_disciplina_ementa" cols="30" rows="10" style="resize: none" required></textarea>
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