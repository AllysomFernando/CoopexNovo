<?php 

   /* ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/

    require_once("/var/www/html/php/sqlsrv.php");

    $id_material = $_GET['id'];

    $sql2 = "SELECT
                id_pessoa, material, id_serie, serie
            FROM
                tesouraria.retirada 
            INNER JOIN tesouraria.material using (id_material) 
            INNER JOIN tesouraria.serie using (id_serie) 
            WHERE
                ativo = 1
                AND YEAR ( data_retirada ) = 2024    
            AND    
                id_material = $id_material";
    $material = $coopex->query($sql2);

    $aux = [];
    
	while($row_material = $material->fetch(PDO::FETCH_OBJ)){
        $aux[] = $row_material->id_pessoa;
        $id_serie = $row_material->id_serie;
        $serie = $row_material->serie;
        $material_nome = $row_material->material;
    }

    if($material->rowCount()){
        $id_pessoa = implode(',', $aux);
    } else {
        $id_pessoa = 1;
        $id_serie = 1;
    }
    

    

    $sql = "SELECT DISTINCT
                pes_id_pessoa,
                pes_nm_pessoa 
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
                iap_id_periodo_letivo = 5000000244 
                AND ser_id_serie = $id_serie 
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
                    AND ttf_st_situacao IN ( 'P', 'L', 'G', 'R', 'S' ) 
                ) 
                
                AND EXISTS ( SELECT 1 FROM academico..MTR_matricula WHERE mtr_id_periodo_letivo = pel_id_periodo_letivo AND mtr_id_registro_curso = rca_id_registro_curso AND mtr_id_situacao_matricula = 1000000002 
                ) 
                AND pes_id_pessoa NOT IN ( $id_pessoa )";
	$res = mssql_query($sql);
       
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
                            <strong><?=utf8_encode($serie)?></strong><br>
                            <?=utf8_encode($material_nome)?><br><br>
                            <strong><?=mssql_num_rows($res) . "</strong> não alunos retiraram material"?>        
                        </div>
                    </div>
                    <h3 class="fw-300 display-5 fw-500  keep-print-font pt-4 l-h-n m-0">
                        RELATÓRIO DE RETIRADA DE MATERIAL - POR MATERIAL
                    </h3>
                    <div class="text-dark fw-700 h1 mb-g keep-print-font">
                        
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="border-top-0 table-scale-border-bottom fw-700">ALUNOS</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?
                                while($row = mssql_fetch_assoc($res)){
                            ?>
                                <tr>
                                    <td width="50%" class="text-left strong"><?=utf8_encode($row['pes_nm_pessoa'])?></td>
                                </tr>
                            <?
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                <hr>    
           
        </div>
    </div>
</main>
                   