<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("/var/www/html/php/sqlsrv.php");

$id_pessoa = $_GET['id'];

$sql = "SELECT
                pes_id_pessoa,
                rtrim(alu_nu_matricula) AS ra,
                rtrim(pes_nm_pessoa) AS nome,
                rtrim(crs_nm_resumido) AS curso,
                ser_ds_serie AS serie,
                sap_ds_situacao AS situacao,
                rca_id_registro_curso,
                ser_id_serie
            FROM
                registro..PES_pessoa
            INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
            INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
            INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
            INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view ON rca_id_registro_curso = sap_id_registro_curso
            INNER JOIN academico..PEL_periodo_letivo ON sap_id_periodo_letivo = pel_id_periodo_letivo
            INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
            INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
            WHERE
                iap_id_periodo_letivo = 5000000244 --and sap_ds_situacao = 'Sem Status'
            AND pes_id_pessoa = $id_pessoa
            AND EXISTS (
                SELECT
                    1
                FROM
                    financeiro..cta_contrato_academico,
                    financeiro..ctr_contrato,
                    financeiro..CPL_contrato_periodo_letivo,
                    financeiro..prc_parcela,
                    financeiro..ttf_titulo_financeiro
                WHERE
                    cta_id_contrato = ctr_id_contrato
                AND ctr_id_cliente = rca_id_aluno
                AND cpl_id_periodo_letivo = pel_id_periodo_letivo
                AND cpl_id_contrato = cta_id_contrato
                AND prc_id_contrato = cta_id_contrato
                AND ttf_id_parcela = prc_id_parcela
                AND ttf_st_situacao IN ('P', 'L', 'G', 'R', 'S')
            ) --Em Compensação, liberado, Pago, Renegociado e Sem valo */
            AND EXISTS (
                SELECT
                    1
                FROM
                    academico..MTR_matricula
                WHERE
                    mtr_id_periodo_letivo = pel_id_periodo_letivo
                AND mtr_id_registro_curso = rca_id_registro_curso
                AND mtr_id_situacao_matricula = 1000000002
                AND mtr_id_periodo_letivo = 5000000244
            )
            ORDER BY
                crs_nm_resumido,
                ser_ds_serie,
                pes_nm_pessoa";
$res = mssql_query($sql);
$row = mssql_fetch_assoc($res);

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
                Declaração de Retirada de Camisetas
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
                    </div>
                    <h3 class="fw-300 display-4 fw-500  keep-print-font pt-4 l-h-n m-0">
                        DECLARAÇÃO DE RETIRADA DE CAMISETA - ESCOLA DE ESPORTES
                    </h3>
                    <div class="text-dark fw-700 h1 mb-g keep-print-font">

                    </div>
                </div>
            </div>

            <?
            $sql2 = "SELECT
                *
            FROM
                colegio.sports 
                INNER JOIN colegio.camiseta_tamanho USING ( id_camiseta_tamanho ) 
            WHERE
                id_pessoa = $id_pessoa";
            $material2 = $coopex->query($sql2);
            $row2 = $material2->fetch(PDO::FETCH_OBJ);
            ?>

            <div class="row">
                <div class="p-3" style="font-size: 18px;">
                    Declaro que, na presente data, recebi do COLÉGIO FAG (Fundação Assis Gurgacz - CNPJ
                    02.203.539/0001-73) as Camisetas da Escola de Esporte relacionadas abaixo, do aluno(a) <strong><?= utf8_encode($row['nome']) ?></strong>,
                    matriculado(a) na série: <strong><?= utf8_encode($row['serie']) ?></strong>.<br><br>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table mt-5">
                            <thead>
                                <tr>
                                    <th class="border-top-0 table-scale-border-bottom fw-700">Camiseta</th>
                                    <th class="border-top-0 table-scale-border-bottom fw-700">Tamanho</th>
                                    <th class="text-right border-top-0 table-scale-border-bottom fw-700">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                               <?
                                    if($row2->retirada){
                               ?>
                                    <tr>
                                        <td>Treino (Personalizada)</td>
                                        <td class="text-left strong"><?= $row2->tamanho ?></td>

                                        <td class="text-right"><?= converterDataHora($row2->data_retirada) ?></td>
                                    </tr>
                               <?
                                    }

                                    if($row2->retirada_uniforme){
                               ?>
                                    <tr>
                                        <td>Uniforme (Padrão)</td>
                                        <td class="text-left strong"><?= $row2->tamanho ?></td>

                                        <td class="text-right"><?= converterDataHora($row2->data_retirada_uniforme) ?></td>
                                    </tr>
                               <?
                                    }
                               ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col">
                    COLÉGIO FAG
                    <br><br><br><br>
                    __________________________________________________
                    <br><br><br>
                    CNPJ: 02.203.539/0001-73
                </div>

                <div class="col text-right">
                    RESPONSÁVEL
                    <br><br><br><br>
                    __________________________________________________
                    <br><br><br>
                    CPF: __________________________________________________
                </div>
            </div>




        </div>
    </div>
</main>