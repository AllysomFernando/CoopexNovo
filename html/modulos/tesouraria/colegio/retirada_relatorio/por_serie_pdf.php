<?php 

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("/var/www/html/php/sqlsrv.php");

    $id_serie = $_GET['id'];

    $sql2 = "SELECT
                nome, id_pessoa, serie
            FROM
                tesouraria.retirada
            INNER JOIN tesouraria.material USING (id_material)
            INNER JOIN tesouraria.serie USING (id_serie)
            INNER JOIN coopex.pessoa USING (id_pessoa)
            WHERE
                id_serie = $id_serie
                AND YEAR ( data_retirada ) = 2024 
            GROUP BY
                id_pessoa
            ORDER BY
                nome";
    $pessoa = $coopex->query($sql2);
    $row_pessoa = $pessoa->fetch(PDO::FETCH_OBJ);
    
?>

<link rel="stylesheet" media="screen, print" href="css/page-invoice.css">

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Tesouraria</a></li>
        <li class="breadcrumb-item">Colégio</li>
        <li class="breadcrumb-item active">Declaração de Retirada</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-plus-circle'></i> Tesouraria
            <small>
                Relatrório de Retirada por Série
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
                            <?=utf8_encode($row_pessoa->serie)?><br>
                            <strong><?=$pessoa->rowCount() . "</strong> alunos retiraram material"?>        
                        </div>
                    </div>
                    <h3 class="fw-300 display-4 fw-500  keep-print-font pt-4 l-h-n m-0">
                        RELATÓRIO DE RETIRADA DE MATERIAL - POR SÉRIE
                    </h3>
                    <div class="text-dark fw-700 h1 mb-g keep-print-font">
                        
                    </div>
                </div>
            </div>

            <?
                $pessoa = $coopex->query($sql2);
                while($row_pessoa = $pessoa->fetch(PDO::FETCH_OBJ)){
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="border-top-0 table-scale-border-bottom fw-700"><?=utf8_encode($row_pessoa->nome)?></th>
                                    <th class="text-center border-top-0 table-scale-border-bottom fw-700">Qtd</th>
                                    <th class="text-right border-top-0 table-scale-border-bottom fw-700">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?
                                $sql2 = "SELECT
                                            *
                                        FROM
                                            tesouraria.retirada
                                        INNER JOIN tesouraria.material USING (id_material)
                                        WHERE
                                            id_serie = $id_serie
                                            AND YEAR ( data_retirada ) = 2024 
                                        AND id_pessoa = $row_pessoa->id_pessoa";
                                $material2 = $coopex->query($sql2);
                                while($row2 = $material2->fetch(PDO::FETCH_OBJ)){
                            ?>
                                <tr>
                                    <td width="50%" class="text-left strong"><?=$row2->material?></td>
                                    <td width="10%" class="text-center">1</td>
                                    <td width="40%" class="text-right"><?=converterDataHora($row2->data_retirada)?></td>
                                </tr>
                            <?
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?
                }
            ?>
        </div>
    </div>
</main>
                   