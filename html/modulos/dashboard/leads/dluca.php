<?php

$id_menu = 127;

//TOTAL
$sql = "SELECT COUNT(*) as total FROM dluca.avaliacao";
$res = $coopex->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$total = $row->total;

$sql = "SELECT avaliacao.frequencia, avaliacao.variedade, avaliacao.sabor, avaliacao.bebida, avaliacao.atendimento,avaliacao.preco, avaliacao.tempo, avaliacao.observacao, avaliacao.consumido FROM dluca.avaliacao";

$res = $coopex->query($sql);
$results = $res->fetchAll(PDO::FETCH_OBJ);
$questions = [
    'frequencia' => 'Com que frequência você visita o Terrazo D\'Luca?',
    'variedade' => 'Como você avaliaria a variedade das opções disponíveis no nosso cardápio?',
    'sabor' => 'Como você avaliaria os pratos que você experimentou?',
    'bebida' => 'A temperatura do seu café/bebida/alimento estava de acordo com o pedido?',
    'atendimento' => 'Como você avaliaria a qualidade do atendimento recebido?',
    'preco' => 'Como você avaliaria a relação preço / qualidade?',
    'tempo' => 'Como você avaliaria a relação tempo do pedido / entrega do produto?'
];

$data = [];
foreach ($results as $row) {
    foreach ($questions as $key => $question) {
        if (!isset($data[$key])) {
            $data[$key] = [];
        }
        if (!isset($data[$key][$row->$key])) {
            $data[$key][$row->$key] = 0;
        }
        $data[$key][$row->$key]++;
    }
}
?>
<style>
    .debug {
        border: solid red 1px;
    }

    chart-container {
        position: relative;
        margin: auto;
        height: 400px;
        width: 100%;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 20px;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
</style>

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">COOPEX</a></li>
        <li class="breadcrumb-item">BI</li>
        <li class="breadcrumb-item active">DLuca Dashboard</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>



    <div class="row mt-3">
        <div class="col-sm-6 col-xl-2">
            <div class="p-3 bg-success text-white rounded overflow-hidden position-relative mb-g">
                <div class="">
                    <h3 class="display-4 d-block l-h-n m-0 fw-500">
                        <?= $total ?>
                        <small class="m-0 l-h-n"><strong>Avaliações realizadas</strong></small>
                    </h3>
                </div>
                <i class="fal fa-check-circle position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4" style="font-size: 6rem;"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <?php foreach ($questions as $key => $question) : ?>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><?= htmlspecialchars($question, ENT_QUOTES, 'UTF-8') ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chart-<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="panel-container show">
        <div class="card-header">
            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-observacoes-produtos" aria-expanded="false" aria-controls="collapse-observacoes-produtos">
                Observações e Produtos Consumidos
            </button>
        </div>
        <div id="collapse-observacoes-produtos" class="collapse">
            <div class="card-body">
                <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dt-basic-example">
                    <thead>
                        <tr>
                            <th>Observações</th>
                            <th>Produtos Consumidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row) : ?>
                            <tr>
                                <td><?= !empty($row->observacao) ? utf8_encode($row->observacao) : 'Nenhuma observação foi feita.' ?></td>
                                <td><?= !empty($row->consumido) ? utf8_encode($row->consumido) : 'Nenhum produto foi citado.' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>
    $(document).ready(function() {

        $('.dt-basic-example').dataTable({
            responsive: true

        });
    });
    const colorMapping = {
        '0': '#FF6347',
        '1': '#FFD700',
        '2': '#67c18c',
        '3': '#15AD40',
        '4': '#02b574',
        '5': '#00CED1',
        '6': '#4682B4',
        '7': '#D2B48C',
        '8': '#8A2BE2'
    };

    const labelsMapping = {
        '0': 'Ruim',
        '1': 'Regular',
        '2': 'Bom',
        '3': 'Muito Bom',
        '4': 'Excelente',
        '5': 'Uma vez por semana',
        '6': 'Rotineiramente',
        '7': 'Esta é minha primeira visita',
        '8': 'Diariamente'
    };

    const data = <?= json_encode($data, JSON_HEX_TAG); ?>;

    Object.keys(data).forEach(key => {
        const ctx = document.getElementById(`chart-${key}`).getContext('2d');
        const labels = Object.keys(data[key]).map(k => labelsMapping[k] || k);
        const values = Object.values(data[key]);

        const datasets = labels.map((label, index) => ({
            label: `${label}: ${values[index]}`,
            data: [values[index]],
            backgroundColor: colorMapping[Object.keys(labelsMapping).find(k => labelsMapping[k] === label)],
            borderColor: colorMapping[Object.keys(labelsMapping).find(k => labelsMapping[k] === label)],
            borderWidth: 1
        }));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [''],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        color: '#000',
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value, context) => {
                            return value;
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>