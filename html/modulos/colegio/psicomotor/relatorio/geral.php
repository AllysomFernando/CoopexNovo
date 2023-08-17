<?php

require_once("php/mysql.php");
require_once("php/utils.php");
require_once("php/sqlsrv.php");
require_once __DIR__ . "/define_cores.php";

$id_menu = 83;
$chave = "id_ficha_psicomotor";
$query_partial;

if (strlen($_GET["id"]) < 10) {
    $query_partial = "id_ficha_psicomotor = " . $_GET["id"];
} else {
    $query_partial = "id_pessoa = " . $_GET["id"];
}


if (isset($_GET["id"])) {
    $$chave = $_GET["id"];
    $sql = "SELECT * FROM colegio.ficha_psicomotor INNER JOIN coopex.pessoa USING (id_pessoa)	
			WHERE " . $query_partial . " AND excluido = 0 ORDER BY data_cadastro";

    $res = $coopex->query($sql);
    $row = $res->fetchAll(PDO::FETCH_OBJ);
} else {
    $$chave = 0;
}

$id_pessoa = $row[0]->id_pessoa;

$sql2 = "SELECT
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
            iap_id_periodo_letivo = 5000000241 --and sap_ds_situacao = 'Sem Status'
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
            -- AND mtr_id_periodo_letivo = 5000000241
        )";
$res2 = mssql_query($sql2);
$row2 = mssql_fetch_assoc($res2);

?>
<link rel="stylesheet" media="screen, print" href="css/page-invoice.css">
<main id="js-page-content" role="main" class="page-content">
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-plus-circle'></i> Ficha de Psicomotor
            <small>
                Relatório de Ficha de Psicomotor
            </small>
        </h1>
    </div>
    <div class="container" id="ficha-completa">
        <div data-size="A4">
            <div class="row">
                <div class="col-sm-12">
                    <div class="d-flex align-items-center mb-5">
                        <h2 class="keep-print-font fw-500 mb-0 text-primary flex-1 position-relative">
                            Sistema Coopex
                            <small class="text-muted mb-0 fs-xs">
                                Relatório de Ficha de Psicomotor
                            </small>
                            <!-- barcode demo only -->

                        </h2>
                    </div>
                    <h3 class="fw-300 display-4 fw-500 color-primary-600 keep-print-font pt-4 l-h-n m-0">
                        <?php echo $row[0]->nome ?>
                    </h3>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 d-flex">
                    <div class="table-responsive">
                        <table class="table table-clean table-sm align-self-end">
                            <tbody>
                                <tr>
                                    <td class="h4">
                                        RA:
                                    <td class="h4 font-weight-normal"><?php echo utf8_encode($row2['ra']) ?></td>
                                    </td>

                                </tr>
                                <tr>
                                    <td class="h4">
                                        Curso:
                                    <td class="h4 font-weight-normal"><?php echo utf8_encode($row2['curso']) ?></td>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h4">
                                        Serie:
                                    <td class="h4 font-weight-normal"><?php echo utf8_encode($row2['serie']) ?></td>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-stripped text-center">
                            <thead>
                                <tr>
                                    <td></td>
                                    <?php foreach ($row as &$element) { ?>
                                        <th class="h4"><?php echo converterData($element->data_cadastro) ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-left font-weight-bold h4">Peso (Kg)</td>
                                    <?php foreach ($row as &$element) { ?>
                                    <td class="h4 font-weight-normal"><?php echo $element->peso ?></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold h4">Altura</td>
                                    <?php foreach ($row as &$element) { ?>
                                        <td class="h4 font-weight-normal"><?php echo $element->altura . "m" ?></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold h4">Coordenação Viso Manual</td>
                                    <?php foreach ($row as &$element) { ?>
                                        <td class="h4 font-weight-normal" <?php echo defineCores($element->coord_viso_manual) ?>><?php echo $element->coord_viso_manual ?></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold h4">Controle Postural</td>
                                    <?php foreach ($row as &$element) { ?>
                                        <td class="h4 font-weight-normal" <?php echo defineCores($element->coord_viso_manual) ?>><?php echo $element->controle_postural ?></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold h4">Organização Perceptiva</td>
                                    <?php foreach ($row as &$element) { ?>
                                        <td class="h4 font-weight-normal" <?php echo defineCores($element->coord_viso_manual) ?>><?php echo $element->organizacao_perceptiva ?></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold h4">Coordenação Dinâmica</td>
                                    <?php foreach ($row as &$element) { ?>
                                        <td class="h4 font-weight-normal" <?php echo defineCores($element->coord_viso_manual) ?>><?php echo $element->coord_dinamica ?></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold h4">Controle do Próprio Corpo</td>
                                    <?php foreach ($row as &$element) { ?>
                                        <td class="h4 font-weight-normal" <?php echo defineCores($element->coord_viso_manual) ?>><?php echo $element->controle_proprio_corpo ?></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold h4">Linguagem</td>
                                    <?php foreach ($row as &$element) { ?>
                                        <td class="h4 font-weight-normal" <?php echo defineCores($element->coord_viso_manual) ?>><?php echo $element->linguagem ?></td>
                                    <?php } ?>
                                </tr>
                            </tbody>
                        </table>
                        <h4>M = Média / F = Fácil / D = Difícil</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>