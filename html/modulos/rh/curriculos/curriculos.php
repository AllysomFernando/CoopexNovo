<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">
  <ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0);">RH</a></li>
    <li class="breadcrumb-item active">Curriculos</li>

    <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 01</code></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-barcode-read'></i> Curriculos
      <small>
        Relatorio de candidatos por curriculos
      </small>
    </h1>
  </div>
  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <h2>
            Curriculos
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">

            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <button class="btn btn-outline-primary " onclick="loadTotal()" type="button">Todos</button>
                <button class="btn btn-outline-primary " onclick="loadSuperior()" type="button">Ensino Superior</button>
                <button class="btn btn-outline-primary" onclick="loadMedio()" type="button">Ensino Médio</button>
              </div>

            </div>
            <!-- <div class="form-group" id="select">
              <label class="form-label" for="select2-ajax">
                Seleciona a vaga desejada
              </label>
              <select onChange="" data-placeholder="Selecione a vaga..." class="js-consultar-usuario form-control" id="select2-ajax"></select>
            </div> -->

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

    loadTotal();



  });

  function loadMedio() {

    $("#tabela_inicical").load("modulos/rh/curriculos/ajax/carrega_medio.php");
  }

  function loadSuperior() {

    $("#tabela_inicical").load("modulos/rh/curriculos/ajax/carrega_superior.php");
  }

  function loadTotal() {

    $("#tabela_inicical").load("modulos/rh/curriculos/ajax/carrega_todos.php");
  }



  $('#select2-ajax').on('select2:select', function(e) {
    var data = e.params.data;
    console.log(data.id);
    $("#tabela_inicical").hide();
    $("#resultado").load("modulos/rh/vagas/ajax/consulta.php?vaga=" + data.id);

  });
</script>