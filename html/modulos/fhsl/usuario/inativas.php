<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">
  <ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0);">RH</a></li>
    <li class="breadcrumb-item active">Vagas</li>

    <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 01</code></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-barcode-read'></i> Vagas
      <small>
        Relatorio de candidatos por vagas
      </small>
    </h1>
    <div class="subheader-title col-6 text-right" id="btn_cadastro">
      <a href="rh/vagas/cadastro">
        <button type="button" onclick="cadastro()" class="btn btn-lg btn-primary waves-effect waves-themed">
          <span class="ni ni-plus mr-3"></span>
          Cadastrar Vagas
        </button>
      </a>


    </div>
  </div>
  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <h2>
            Vagas - Inativas
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <div class="form-group" id="select">
              <label class="form-label" for="select2-ajax">
                Seleciona a vaga desejada
              </label>
              <select onChange="" data-placeholder="Selecione a vaga..." class="js-consultar-usuario form-control" id="select2-ajax"></select>
            </div>

            <div id="resultado">
            </div>
            <div id="tabela_inicical">
            </div>
            <div id="form">
            </div>


          </div>
        </div>
      </div>

    </div>

  </div>

</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<script>
  $(document).ready(function() {

    $("#tabela_inicical").load("modulos/rh/vagas/ajax/carrega_vagas_inativa.php");



    $(function() {


      $('.select2').select2();

      $(".js-consultar-usuario").select2({
        ajax: {
          url: "modulos/_core/buscar_vaga.php",
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              q: params.term, // search term
              page: params.page
            };
            console.log(params);
          },
          processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            console.log(data);
            params.page = params.page || 1;

            return {
              results: data.items,
              pagination: {
                more: (params.page * 30) < data.total_count
              }
            };
          },
          cache: true
        },
        placeholder: 'Search for a repository',
        // escapeMarkup: function(markup) {
        //   return markup;
        // }, // let our custom formatter work
        // minimumInputLength: 3,
        // templateResult: formatoUsuario,
        // templateSelection: formatoTextoUsuario
      });

      function formatRepo(repo) {
        // if (repo.loading) {
        //   return repo.text;
        // }

        var markup = "<div class='select2-result-repository clearfix d-flex'>" +
          "<div class='select2-result-repository__avatar mr-2'><img src='https://www2.fag.edu.br/coopex3/img/demo/avatars/avatar-M.png' class='width-2 height-2 mt-1 rounded-circle' /></div>" +
          "<div class='select2-result-repository__meta'>" +
          "<div><span class='select2-result-repository__title fs-lg fw-500'>" + repo.disciplina1 + "</span>" + " (" + repo.id + ")</div>";


        markup += "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>" + repo.nome + "</div>";

        markup += "</div></div>";

        return markup;
        console.log(repo)
      }

      // function formatRepoSelection(repo) {
      //   return repo.nome || '';
      // }

    });


  });

  $('#select2-ajax').on('select2:select', function(e) {
    var data = e.params.data;
    console.log(data.id);
    $("#tabela_inicical").hide();
    $("#resultado").load("modulos/rh/vagas/ajax/consulta.php?vaga=" + data.id);

  });
</script>