<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



?>

<link rel="stylesheet" media="screen, print" href="css/page-invoice.css">

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Colégio</a></li>
        <li class="breadcrumb-item">Big Jump</li>
        <li class="breadcrumb-item active">Relatório de Autorização</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-plus-circle'></i> Tesouraria
            <small>
                Relatório de Autorização por Série
            </small>
        </h1>
    </div>
    <div class="container">
        <div data-size="A4">
            <div class="row">
                <div class="col-sm-12">
                    <div class="d-flex align-items-center mb-5">
                        <h2 class="keep-print-font fw-500 mb-0 text-primary flex-1 position-relative">
                            <img src="https://colegiofag.com.br/assets/images/logo_sistema.png">
                            <small class="text-muted mb-0 fs-xs">

                            </small>
                            <!-- barcode demo only -->

                        </h2>
                        <div class="text-right">
                            <strong><?= date("d/m/Y H:i:s") ?></strong>
                        </div>
                    </div>
                    <h3 class="fw-300 display-4 fw-500  keep-print-font pt-4 l-h-n m-0">
                        RELATÓRIO DE PRÉ-MATRÍCULAS - POR GRUPO
                    </h3>
                    <div class="text-dark fw-700 h1 mb-g keep-print-font">

                    </div>
                </div>
            </div>

            <?
            $sql2 = "SELECT
                                * 
                            FROM
                                colegio.modalidade 
                            ORDER BY
                                modalidade";
            $res = $coopex->query($sql2);
            while ($row = $res->fetch(PDO::FETCH_OBJ)) {
            ?>
                <div class="row">

                    <div class="col-sm-12">

                        <div class="table-responsive">
                            <h1><?= utf8_encode($row->modalidade) ?></h1>
                            <?
                            $sql2 = "SELECT
                                        * 
                                    FROM
                                        colegio.grupo
                                        INNER JOIN colegio.grupo_serie USING (id_grupo)
                                WHERE
                                    id_modalidade = $row->id_modalidade
                                    GROUP BY id_grupo";
                            $res2 = $coopex->query($sql2);
                            
                            while ($row2 = $res2->fetch(PDO::FETCH_OBJ)) {
                            ?>
                                <table class="table">
                                    <thead>
                                     
                                        
                                            <th class="border-top-0 table-scale-border-bottom fw-700"><?= $row2->id_grupo . " - " . utf8_encode($row2->grupo) . " ($row2->vagas) vagas" ?></th>
                                            <th width=100 class="border-top-0 table-scale-border-bottom fw-700">Turno</th>
                                            <th width=100 class="border-top-0 table-scale-border-bottom fw-700">Pago</th>
                                            <th width=100 class="border-top-0 table-scale-border-bottom fw-700">Data Pagamento</th>
                                            <th width=100 class="border-top-0 table-scale-border-bottom fw-700">Data Matrícula</th>
                                            
                                       
                                    </thead>
                                    <tbody>
                                        <?
                                        if (isset($row2->id_turno)) {
                                            $condicao_turno = $row2->id_turno == 3 ? "" : "AND id_turno = $row2->id_turno";
                                        }

                                        $sql3 = "SELECT
                                                    nome,
                                                    id_turno,
                                                    id_serie,
                                                    id_modalidade,
                                                    id_grupo,
                                                    turno,
                                                    pagamento,
                                                    date(data_pagamento) AS data_pagamento,
                                                    date(data_cadastro) AS data_cadastro,
                                                    fila
                                                FROM
                                                    colegio.sports
                                                    INNER JOIN coopex.pessoa USING ( id_pessoa )
                                                    INNER JOIN colegio.modalidade_aluno USING ( id_sports )
                                                    INNER JOIN colegio.grupo_serie USING ( id_serie ) 
                                                    INNER JOIN colegio.turno USING ( id_turno ) 
                                                WHERE
                                                    id_grupo = $row2->id_grupo 
                                                    AND id_modalidade = $row2->id_modalidade 
                                                    $condicao_turno
                                                GROUP BY
                                                    id_pessoa,
                                                    id_modalidade 
                                                ORDER BY
                                                    pagamento desc, data_cadastro ASC";
                                        $res3 = $coopex->query($sql3);
                                        $i = 1;
                                        while ($row3 = $res3->fetch(PDO::FETCH_OBJ)) {
                                        ?>
                                            <tr class="<?=$row3->pagamento ? "font-weight-bold" : "" ?>">
                                                <td class="text-left strong"><?= $i . " - " . utf8_encode($row3->nome) ?></td>
                                                <td class="text-left strong"><?= utf8_encode($row3->turno) ?></td>
                                                <td class="text-left strong"><?= $row3->pagamento == 1 ? "SIM" : "-"; ?></td>
                                                <td class="text-left strong"><?= $row3->data_pagamento ? converterData($row3->data_pagamento) : "-"; ?></td>
                                                <td class="text-left strong"><?= converterData($row3->data_cadastro) ?></td>
                                                
                                            </tr>
                                        <?
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            <?
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?
            }
            ?>
        </div>
    </div>
</main>