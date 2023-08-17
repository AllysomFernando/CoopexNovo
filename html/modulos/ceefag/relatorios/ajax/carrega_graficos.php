<?php session_start();
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require_once("../../../../php/mysql.php");
require_once("conecta.php");

print_r($user);
?>


<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">


<style>
  table tr td {
    vertical-align: middle !important;
  }
</style>
<div style="display: flex; flex-direction: column;">
  <section style="display: flex; flex-direction: row;width: 100%;">
    <div style="display:flex;width : 100%; justify-content: center;flex-direction: row;align-items: center;gap:5%">
      <div style="width: 30%;">
        <p style="text-align: center"><strong>Vagas X Farol</strong></p>
        <canvas id="myChart"></canvas>
      </div>
      <div style="width: 45%;">
        <p style="text-align: center;"><strong>Vagas X Recrutador</strong></p>
        <canvas id="myChart2"></canvas>
      </div>

    </div>


  </section>
  <section style="display: flex; flex-direction: row;width: 100%;margin-top: 5%;">
    <div style="display:flex;width : 100%; justify-content: center;flex-direction: row;align-items: center;gap:5%">
      <div style="width: 45%;">
        <p style="text-align: center"><strong>Vagas X Area</strong></p>
        <canvas id="vagasArea"></canvas>
      </div>
      <div style="width: 45%;">
        <p style="text-align: center;"><strong>Vagas X Perfil</strong></p>
        <canvas id="perfil"></canvas>
      </div>

    </div>

  </section>
  <section style="display: flex; flex-direction: row;width: 100%;margin-top: 5%;">

    <div style="width: 45%;">
      <p style="text-align: center"><strong>Vagas X Status</strong></p>
      <canvas id="status"></canvas>
    </div>


  </section>
</div>

<div class="modal fade" id="pagamentos_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" id="pagamentos_modal_conteudo">
  </div>
</div>

<script>
  $(document).ready(function() {

    $(":input").inputmask();

    $('.data').datepicker({
      todayHighlight: true,
      orientation: "bottom left",
      locale: "pt-BR"
    });

    $.ajax({
      type: 'POST',
      url: 'https://coopex.fag.edu.br/modulos/rh/relatorios/ajax/bases.php',
      data: {
        grafico: "recrutador",

      },
      dataType: 'json',
      success: function(response) {

        console.log(response)
        const ctx1 = document.getElementById('myChart2');

        new Chart(ctx1, {
          type: 'bar',
          data: {
            labels: response[0],
            datasets: [{
              axis: 'y',
              label: 'Vagas',
              fill: 'false',
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
              ],
              data: response[1],
              borderWidth: 1
            }]
          },
          options: {
            indexAxis: 'y',
          }
        })

      }
    });
    $.ajax({
      type: 'POST',
      url: 'https://coopex.fag.edu.br/modulos/rh/relatorios/ajax/bases.php',
      data: {
        grafico: "farol",
      },
      dataType: 'json',
      success: function(response) {

        console.log(response)
        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
          type: 'pie',
          data: {
            labels: response[0],
            datasets: [{
              axis: 'y',
              label: 'Vagas',
              fill: 'false',
              data: response[1],
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        })

      }
    });
    // Vendas X Area
    $.ajax({
      type: 'POST',
      url: 'https://coopex.fag.edu.br/modulos/rh/relatorios/ajax/bases.php',
      data: {
        grafico: "areas",
      },
      dataType: 'json',
      success: function(response) {

        console.log(response)
        const ctx = document.getElementById('vagasArea');

        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: response[0],
            datasets: [{

              label: 'Vagas',
              fill: 'false',
              backgroundColor: [
                'rgba(1, 60, 28, 0.6)',
              ],
              data: response[1],
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        })

      }
    });
    // Vendas X perfil
    $.ajax({
      type: 'POST',
      url: 'https://coopex.fag.edu.br/modulos/rh/relatorios/ajax/bases.php',
      data: {
        grafico: "perfil",
      },
      dataType: 'json',
      success: function(response) {

        console.log(response)
        const ctx = document.getElementById('perfil');

        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: response[0],
            datasets: [{

              label: 'Vagas',
              fill: 'false',
              backgroundColor: [
                'rgba(128, 21, 27, 0.8)',
              ],
              data: response[1],
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        })

      }
    });
    // Vendas X perfil
    $.ajax({
      type: 'POST',
      url: 'https://coopex.fag.edu.br/modulos/rh/relatorios/ajax/bases.php',
      data: {
        grafico: "status",
      },
      dataType: 'json',
      success: function(response) {

        console.log(response)
        const ctx = document.getElementById('status');

        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: response[0],
            datasets: [{
    
              label: 'Vagas',
              fill: 'false',
              backgroundColor: [
                'rgba(0, 38, 66, 0.8)',
              ],
              data: response[1],
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        })

      }
    });



  });

  function inapto(id) {

    console.log("passou");
    $.ajax({
      type: 'POST',
      url: '/modulos/rh/vagas/ajax/cadastro_dados.php',
      data: {
        acao: 'inapto',
        id_candidato: id,
      },
      dataType: 'json',
      success: function(response) {

        window.location.reload(true);
      }

    });
  };

  function apto(id) {

    console.log("passou");
    $.ajax({
      type: 'POST',
      url: '/modulos/rh/vagas/ajax/cadastro_dados.php',
      data: {
        acao: 'apto',
        id_candidato: id,
      },
      dataType: 'json',
      success: function(response) {

        window.location.reload(true);
      }

    });
  };
</script>