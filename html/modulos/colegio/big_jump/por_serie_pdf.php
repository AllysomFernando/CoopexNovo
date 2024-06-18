<?php 

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("/var/www/html/php/sqlsrv.php");

    $id_serie = $_GET['id'];

    $sql2 = "SELECT
                id_pessoa 
            FROM
                colegio.big_jump
            WHERE
                id_pessoa IS NOT NULL";
    $pessoa = $coopex->query($sql2);
    $id_pessoa = [];
    while($row_pessoa = $pessoa->fetch(PDO::FETCH_OBJ)){
        $id_pessoa[] = $row_pessoa->id_pessoa;
    }
    $id_pessoa = implode(",", $id_pessoa);

   $sql = "SELECT
                tcu_id_turma_curso,
                tcu_ds_turma_curso
            FROM
                registro..PES_pessoa
                INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
                INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
                INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
                INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view ON rca_id_registro_curso = sap_id_registro_curso
                INNER JOIN academico..PEL_periodo_letivo ON sap_id_periodo_letivo = pel_id_periodo_letivo
                INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
                INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
                INNER JOIN academico..TUE_turma_unidade_ensino ON tue_id_serie = ser_id_serie
                INNER JOIN academico..TCU_turmas_curso ON tue_id_turma_curso = tcu_id_turma_curso 
            WHERE
                iap_id_periodo_letivo = 5000000244 
                AND tue_id_periodo_letivo = pel_id_periodo_letivo 
                AND rca_id_turma_curso = tue_id_turma_curso 
                AND ser_id_serie = $id_serie 
                AND pes_id_pessoa IN ($id_pessoa)
                GROUP BY
                    tcu_id_turma_curso,
                    tcu_ds_turma_curso 
                ORDER BY
                    tcu_ds_turma_curso";
    $res = mssql_query($sql);
	        
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
                            <strong><?=date("d/m/Y H:i:s") ?></strong>      
                        </div>
                    </div>
                    <h3 class="fw-300 display-4 fw-500  keep-print-font pt-4 l-h-n m-0">
                        RELATÓRIO DE AUTORIZAÇÃO - POR SÉRIE
                    </h3>
                    <div class="text-dark fw-700 h1 mb-g keep-print-font">
                        
                    </div>
                </div>
            </div>

            <?
                while($row = mssql_fetch_assoc($res)){
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="border-top-0 table-scale-border-bottom fw-700"><?=utf8_encode($row['tcu_ds_turma_curso'])?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?
                                $sql2 = "SELECT
                                            pes_id_pessoa,
                                            rtrim( alu_nu_matricula ) AS ra,
                                            rtrim( pes_nm_pessoa ) AS nome,
                                            rtrim( crs_nm_resumido ) AS curso,
                                            ser_ds_serie AS serie,
                                            sap_ds_situacao AS situacao,
                                            rca_id_registro_curso,
                                            ser_id_serie,
                                            tcu_ds_turma_curso 
                                        FROM
                                            registro..PES_pessoa
                                            INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
                                            INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
                                            INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
                                            INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view ON rca_id_registro_curso = sap_id_registro_curso
                                            INNER JOIN academico..PEL_periodo_letivo ON sap_id_periodo_letivo = pel_id_periodo_letivo
                                            INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
                                            INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
                                            INNER JOIN academico..TUE_turma_unidade_ensino ON tue_id_serie = ser_id_serie
                                            INNER JOIN academico..TCU_turmas_curso ON tue_id_turma_curso = tcu_id_turma_curso 
                                        WHERE
                                            iap_id_periodo_letivo = 5000000244 --and sap_ds_situacao = 'Sem Status'
                                            
                                            AND tue_id_periodo_letivo = pel_id_periodo_letivo 
                                            AND rca_id_turma_curso = tue_id_turma_curso 
                                            AND pes_id_pessoa IN ( $id_pessoa ) 
                                            AND tcu_id_turma_curso = ".$row['tcu_id_turma_curso']." ORDER BY nome";
                                $res2 = mssql_query($sql2);
                                while($row2 = mssql_fetch_assoc($res2)){
                            ?>
                                <tr>
                                    <td  class="text-left strong"><?=utf8_encode($row2['nome'])?></td>
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
                   