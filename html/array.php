<?php
// Seu array de dados
$dados = [
    ["total" => 10, "crs_nm_resumido" => "Curso 1", "ser_ds_serie" => "Serie A", "crs_id_curso" => 1, "tcu_ds_turma_curso" => "Turma 1", "tcu_ch_matutino" => 1],
    ["total" => 15, "crs_nm_resumido" => "Curso 1", "ser_ds_serie" => "Serie B", "crs_id_curso" => 1, "tcu_ds_turma_curso" => "Turma 2", "tcu_ch_matutino" => 0],
    ["total" => 20, "crs_nm_resumido" => "Curso 2", "ser_ds_serie" => "Serie A", "crs_id_curso" => 2, "tcu_ds_turma_curso" => "Turma 1", "tcu_ch_matutino" => 1],
    ["total" => 25, "crs_nm_resumido" => "Curso 2", "ser_ds_serie" => "Serie B", "crs_id_curso" => 2, "tcu_ds_turma_curso" => "Turma 2", "tcu_ch_matutino" => 1]
];

// Inicialize um array para armazenar os totais agrupados
$totaisAgrupados = [];

// Percorra os dados para agrupar os totais pela coluna crs_nm_resumido e tcu_ch_matutino
foreach ($dados as $dado) {
    $chave = $dado['crs_nm_resumido'] . '-' . $dado['tcu_ch_matutino'];
    if (!isset($totaisAgrupados[$chave])) {
        $totaisAgrupados[$chave] = 0;
    }
    $totaisAgrupados[$chave] += $dado['total'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Totais Agrupados</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Tabela de Totais Agrupados</h2>
    <table>
        <thead>
            <tr>
                <th>Curso</th>
                <th>Período</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($totaisAgrupados as $chave => $total): ?>
                <?php list($curso, $periodo) = explode('-', $chave); ?>
                <tr>
                    <td><?= $curso ?></td>
                    <td><?= $periodo == 1 ? 'Matutino' : 'Noturno' ?></td>
                    <td><?= $total ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>