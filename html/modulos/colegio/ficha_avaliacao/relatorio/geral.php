<?php
$id_menu = 82;
$chave = "id_ficha_avaliacao";
$query_partial;

if (strlen($_GET["id"]) < 10) {
    $query_partial = "id_ficha_avaliacao = " . $_GET["id"];
} else {
    $query_partial = "id_pessoa = " . $_GET["id"];
}


if (isset($_GET['id'])) {
    $$chave = $_GET['id'];
    $sql = "SELECT
				*
			FROM
				colegio.ficha_avaliacao
			INNER JOIN coopex.pessoa USING (id_pessoa)	
			WHERE " . $query_partial . " AND excluido = 0 ORDER BY data_cadastro";

    $res = $coopex->query($sql);
    $row = $res->fetch(PDO::FETCH_OBJ);
} else {
    $$chave = 0;
}

require_once("php/mysql.php");
require_once("php/utils.php");

require_once("php/sqlsrv.php");

    $id_pessoa = $row->id_pessoa;

$sql2 = "SELECT
            pes_id_pessoa,
            rtrim(alu_nu_matricula) AS ra,
            rtrim(pes_nm_pessoa) AS nome,
            rtrim(crs_nm_resumido) AS curso,
            ser_ds_serie AS serie,
            sap_ds_situacao AS situacao,
            rca_id_registro_curso,
            ser_id_serie,
            sexo,
            Nasc
            FROM
            registro..PES_pessoa
            INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
            INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
            INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
            INNER JOIN integracao..view_integracao_usuario ON id_pessoa = pes_id_pessoa 
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
            )
            ORDER BY
            crs_nm_resumido,
            ser_ds_serie,
            pes_nm_pessoa";

$res2 = mssql_query($sql2);
$row2 = mssql_fetch_assoc($res2);

$sexo = ($row2['sexo'] == 'M') ? 'masculino' : 'feminino';

$ano = date("Y m j");
$nasc = $row2['Nasc'];

$datetime = DateTime::createFromFormat('M j Y h:i:s:uA', $nasc);
$ano_nascimento = intval($datetime->format('Y'));
$mes_nascimento = intval($datetime->format('m'));
$dia_nascimento = intval($datetime->format('j'));
$idade = $ano - $ano_nascimento;

$idade = $ano - $ano_nascimento;

if ($mes_nascimento > intval(date('m')) || ($mes_nascimento === intval(date('m')) && $dia_nascimento > intval(date('j')))) {
    $idade--;
}


?>
<link rel="stylesheet" media="screen, print" href="css/page-invoice.css">
<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Colégio</a></li>
        <li class="breadcrumb-item">Ficha de Avaliação</li>
        <li class="breadcrumb-item active">Ficha de Avaliação</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-plus-circle'></i> Ficha de Avaliação
            <small>
                Relatório de Ficha de Avaliação
            </small>
        </h1>
    </div>
    <div class="container">
        <div data-size="A4">
            <div class="row">
                <div class="col-sm-12">
                    <div class="d-flex align-items-center mb-5">
                        <h2 class="keep-print-font fw-500 mb-0 text-primary flex-1 position-relative">
                            Sistema Coopex
                            <small class="text-muted mb-0 fs-xs">
                                Relatório de Ficha de Avaliação
                            </small>
                            <!-- barcode demo only -->

                        </h2>
                    </div>
                    <h3 class="fw-300 display-4 fw-500 color-primary-600 keep-print-font pt-4 l-h-n m-0">
                        <?= $row->nome ?>
                    </h3>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 d-flex">
                    <div class="table-responsive">
                        <table class="table table-clean table-sm align-self-end">
                            <tbody>
                                <tr>
                                    <td>
                                        RA:
                                    <td><strong><?php echo utf8_encode($row2['ra']) ?></strong></td>
                                    </td>

                                </tr>
                                <tr>

                                </tr>
                                <tr>
                                    <td>
                                        Sexo:
                                    <td><strong><?php echo mb_convert_case($sexo, MB_CASE_TITLE,'UTF-8' ) ?></strong></td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Idade:
                                    <td><strong><?php echo $idade ?></strong></td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Serie:
                                    <td><strong><?php echo utf8_encode($row2['serie']) ?></strong></td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Curso:
                                    <td><strong><?php echo utf8_encode($row2['curso']) ?></strong></td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- <pre> -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-stripped text-center">
                            <thead>
                                <tr>
                                    <td></td>
                                    <?
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                        <th><?= converterData($row->data_cadastro) ?></th>
                                    <?
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-left font-weight-bold">Estimativa de Excesso de Peso</td>
                                    <?
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {

                                        $peso = $row->massa_corporal;
                                        $altura = $row->estatura;

                                        $imc = $peso / ($altura * $altura);
                                        $imc = number_format($imc, 2);
                                      
                                        $sql6 = "SELECT $sexo FROM colegio.imc WHERE idade = $idade";
                                        $result = $coopex->query($sql6);
                                        if ($result && $result->rowCount() > 0) {
                                            $row_imc = $result->fetch(PDO::FETCH_ASSOC);

                                            $imcSelected = $row_imc[$sexo];

                                            if ($imc == $imcSelected || $imc <= $imcSelected) {
                                                $color = "style = 'color: green'";
                                                $faixa_referencia = 'Bom';
                                            } else {
                                                $color = "style = 'color: orange'";
                                                $faixa_referencia = 'Alerta';
                                            }
                                        } else {

                                            echo "Erro: idade não encontrada no banco de dados!";
                                        }
                                    ?>
                                        <td><?php echo '<strong>' . $imc . '</strong>' . '<p ' . $color . '> (' . $faixa_referencia . ')</p>'; ?></td>
                                    <?
                                    }
                                    ?>

                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold">Estimativa de Excesso de Gordura Visceral</td>
                                    <?
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {
                                        $altura = $row->estatura;
                                        $cintura = $row->perimetro_cintura;
      
                                        $rce = $cintura / $altura;
                                        $rce = number_format($rce, 2);
                                   
                                        $sql7 = "SELECT $sexo FROM colegio.rce WHERE idade = $idade";
                                        $result1 = $coopex->query($sql7);
                                        if ($result1 && $result1->rowCount() > 0) {
                                            $row_imc1 = $result1->fetch(PDO::FETCH_ASSOC);

                                            $rceSelected = $row_imc1[$sexo];

                                            if ($rce == $rceSelected  || $rce < $rceSelected) {
                                                $color = "style = 'color: green'";
                                                $faixa_referencia = 'Bom';
                                            } else {
                                                $color = "style = 'color: orange'";
                                                $faixa_referencia = 'Alerta';
                                            }
                                        } else {
                                            echo "Erro: idade não encontrada no banco de dados!";
                                        }

                                    ?>
                                        <td><?php echo   '<strong>' . $rce . '</strong>' . '<p ' . $color . '> (' . $faixa_referencia . ')</p>'; ?></td>
                                    <?
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold">Aptidão Cardiorrespiratória</td>
                                    <?
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {
                                        $caminhada = $row->seis_minutos;
                                    
                                        $sql8 = "SELECT $sexo FROM colegio.teste_corrida WHERE idade = $idade";
                                        $result8 = $coopex->query($sql8);
                                        if ($result8 && $result8->rowCount() > 0) {
                                            $row_caminhada = $result8->fetch(PDO::FETCH_ASSOC);

                                            $rowSelectedCaminhada = $row_caminhada[$sexo];

                                            if ($caminhada == $rowSelectedCaminhada) {
                                                $color = "style = 'color: green'";
                                            } else {
                                                $color = "style = 'color: orange'";
                                            }
                                        } else {
                                            echo "Erro: idade não encontrada no banco de dados!";
                                        }


                                        $sqlFeminino6 = "SELECT fraco_$sexo, razoavel_min_$sexo, razoavel_max_$sexo, bom_min_$sexo, bom_max_$sexo, 
                                            m_bom_min_$sexo, m_bom_max_$sexo, excelencia_$sexo FROM colegio.teste_corrida WHERE idade = $idade";
                                        $result6 = $coopex->query($sqlFeminino6);
                                        $row6 = $result6->fetch(PDO::FETCH_OBJ);
                                        $fraco = $row6->{"fraco_" . $sexo};
                                        $razoavel_min = $row6->{"razoavel_min_" . $sexo};
                                        $razoavel_max = $row6->{"razoavel_max_" . $sexo};
                                        $bom_min = $row6->{"bom_min_" . $sexo};
                                        $bom_max = $row6->{"bom_max_" . $sexo};
                                        $muito_bom_min = $row6->{"m_bom_min_" . $sexo};
                                        $muito_bom_max = $row6->{"m_bom_max_" . $sexo};
                                        $excelencia = $row6->{"excelencia_" . $sexo};


                                        if ($caminhada <= $fraco) {
                                            $faixa_referencia = "Fraco";
                                        } elseif ($caminhada <= $razoavel_max) {
                                            $faixa_referencia = "Razoável";
                                        } elseif ($caminhada <= $bom_max) {
                                            $faixa_referencia = "Bom";
                                        } elseif ($caminhada <= $muito_bom_max) {
                                            $faixa_referencia = "Muito Bom";
                                        } else {
                                            $faixa_referencia = "Excelente";
                                        }
                                    ?>

                                        <td><?php echo  '<strong>' . $caminhada . '</strong>' . '<p ' . $color . '> (' . $faixa_referencia . ')</p>'; ?></td>

                                    <?
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold">Flexibilidade</td>
                                    <?
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {
                                        $sentar_alcancar = $row->sentar_alcacar;
                    
                                        $sql9 = "SELECT $sexo FROM colegio.sentar_alcancar WHERE idade = $idade";
                                        $result9 = $coopex->query($sql9);
                                        if ($result9 && $result9->rowCount() > 0) {
                                            $row_sentar_alcancar = $result9->fetch(PDO::FETCH_ASSOC);

                                            $rowSentarAlcancar = $row_sentar_alcancar[$sexo];

                                            if ($sentar_alcancar == $rowSentarAlcancar || $sentar_alcancar >= $rowSentarAlcancar) {
                                                $color = "style = 'color: green'";
                                            } else {
                                                $color = "style = 'color: orange'";
                                            }
                                        } else {
                                            echo "Erro: idade não encontrada no banco de dados!";
                                        }

                                        $sqlFeminino6 = "SELECT fraco_$sexo, razoavel_min_$sexo, razoavel_max_$sexo, bom_min_$sexo, bom_max_$sexo, 
                                            m_bom_min_$sexo, m_bom_max_$sexo, excelencia_$sexo FROM colegio.sentar_alcancar WHERE idade = $idade";
                                        $result6 = $coopex->query($sqlFeminino6);
                                        $row6 = $result6->fetch(PDO::FETCH_OBJ);
                                        $fraco = $row6->{"fraco_" . $sexo};
                                        $razoavel_min = $row6->{"razoavel_min_" . $sexo};
                                        $razoavel_max = $row6->{"razoavel_max_" . $sexo};
                                        $bom_min = $row6->{"bom_min_" . $sexo};
                                        $bom_max = $row6->{"bom_max_" . $sexo};
                                        $muito_bom_min = $row6->{"m_bom_min_" . $sexo};
                                        $muito_bom_max = $row6->{"m_bom_max_" . $sexo};
                                        $excelencia = $row6->{"excelencia_" . $sexo};

                                        if ($sentar_alcancar <= $fraco) {
                                            $faixa_referencia = "Fraco";
                                        } elseif ($sentar_alcancar <= $razoavel_max) {
                                            $faixa_referencia = "Razoável";
                                        } elseif ($sentar_alcancar <= $bom_max) {
                                            $faixa_referencia = "Bom";
                                        } elseif ($sentar_alcancar <= $muito_bom_max) {
                                            $faixa_referencia = "Muito Bom";
                                        } else {
                                            $faixa_referencia = "Excelente";
                                        }
                                    ?>
                                        <td><?php echo  '<strong>' . $sentar_alcancar . '</strong>' . '<p ' . $color . '> (' . $faixa_referencia . ')</p>'; ?></td>

                                    <?
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold">Resistência Muscular Localizada</td>
                                    <?
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {
                                        $abdominal = $row->abdominal;

                                        $sql10 = "SELECT $sexo FROM colegio.abdominal WHERE idade = $idade";
                                        $result10 = $coopex->query($sql10);
                                        if ($result10 && $result10->rowCount() > 0) {
                                            $row_abdominal = $result10->fetch(PDO::FETCH_ASSOC);

                                            $rowAbdominal = $row_abdominal[$sexo];

                                            if ($abdominal == $rowAbdominal || $abdominal >= $rowAbdominal) {
                                                $color = "style = 'color: green'";
                                            } else {
                                                $color = "style = 'color: orange'";
                                            }
                                        } else {
                                            echo "Erro: idade não encontrada no banco de dados!";
                                        }


                                        $sqlFeminino6 = "SELECT fraco_$sexo, razoavel_min_$sexo, razoavel_max_$sexo, bom_min_$sexo, bom_max_$sexo, 
                                        m_bom_min_$sexo, m_bom_max_$sexo, excelencia_$sexo FROM colegio.abdominal WHERE idade = $idade";

                                        $result6 = $coopex->query($sqlFeminino6);
                                        $row6 = $result6->fetch(PDO::FETCH_OBJ);
                                        $fraco = $row6->{"fraco_" . $sexo};
                                        $razoavel_min = $row6->{"razoavel_min_" . $sexo};
                                        $razoavel_max = $row6->{"razoavel_max_" . $sexo};
                                        $bom_min = $row6->{"bom_min_" . $sexo};
                                        $bom_max = $row6->{"bom_max_" . $sexo};
                                        $muito_bom_min = $row6->{"m_bom_min_" . $sexo};
                                        $muito_bom_max = $row6->{"m_bom_max_" . $sexo};
                                        $excelencia = $row6->{"excelencia_" . $sexo};


                                        if ($abdominal <= $fraco) {
                                            $faixa_referencia = "Fraco";
                                        } elseif ($abdominal <= $razoavel_max) {
                                            $faixa_referencia = "Razoável";
                                        } elseif ($abdominal <= $bom_max) {
                                            $faixa_referencia = "Bom";
                                        } elseif ($abdominal <= $muito_bom_max) {
                                            $faixa_referencia = "Muito Bom";
                                        } else {
                                            $faixa_referencia = "Excelente";
                                        }
                                    ?>
                                        <td><?php echo  '<strong>' . $abdominal . '</strong>' . '<p ' . $color . '> (' . $faixa_referencia . ')</p>'; ?></td>
                                    <?
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold">Potência de Membros Superiores</td>
                                    <?
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {

                                        $arremesso = $row->arremesso_medicineball;
                    
                                        $sql11 = "SELECT $sexo FROM colegio.arremesso_medicineball WHERE idade = $idade";
                                        $result11 = $coopex->query($sql11);
                                        if ($result11 && $result11->rowCount() > 0) {
                                            $row_arremesso = $result11->fetch(PDO::FETCH_ASSOC);

                                            $rowArremesso = $row_arremesso[$sexo];

                                            if ($arremesso == $rowArremesso || $arremesso >= $rowArremesso) {
                                                $color = "style = 'color: green'";
                                            } else {
                                                $color = "style = 'color: orange'";
                                            }
                                        } else {
                                            echo "Erro: idade não encontrada no banco de dados!";
                                        }

                                        $sqlFeminino6 = "SELECT fraco_$sexo, razoavel_min_$sexo, razoavel_max_$sexo, bom_min_$sexo, bom_max_$sexo, m_bom_min_$sexo, 
                                        m_bom_max_$sexo, excelencia_$sexo FROM colegio.arremesso_medicineball WHERE idade = $idade";

                                        $result6 = $coopex->query($sqlFeminino6);
                                        $row6 = $result6->fetch(PDO::FETCH_OBJ);
                                        $fraco = $row6->{"fraco_" . $sexo};
                                        $razoavel_min = $row6->{"razoavel_min_" . $sexo};
                                        $razoavel_max = $row6->{"razoavel_max_" . $sexo};
                                        $bom_min = $row6->{"bom_min_" . $sexo};
                                        $bom_max = $row6->{"bom_max_" . $sexo};
                                        $muito_bom_min = $row6->{"m_bom_min_" . $sexo};
                                        $muito_bom_max = $row6->{"m_bom_max_" . $sexo};
                                        $excelencia = $row6->{"excelencia_" . $sexo};


                                        if ($arremesso <= $fraco) {
                                            $faixa_referencia = "Fraco";
                                        } elseif ($arremesso <= $razoavel_max) {
                                            $faixa_referencia = "Razoável";
                                        } elseif ($arremesso <= $bom_max) {
                                            $faixa_referencia = "Bom";
                                        } elseif ($arremesso <= $muito_bom_max) {
                                            $faixa_referencia = "Muito Bom";
                                        } else {
                                            $faixa_referencia = "Excelente";
                                        }
                                    ?>
                                        <td><?php echo  '<strong>' . $arremesso . '</strong>' . '<p ' . $color . '> (' . $faixa_referencia . ')</p>'; ?></td>
                                    <?
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold">Potência de Membros Inferiores</td>
                                    <?
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {
                                        $salto = $row->salto_distancia;

                                        $sqlFeminino6 = "SELECT fraco_$sexo, razoavel_min_$sexo, razoavel_max_$sexo, 
                                        bom_min_$sexo, bom_max_$sexo, m_bom_min_$sexo, m_bom_max_$sexo, excelencia_$sexo FROM colegio.salto_horizontal WHERE idade = $idade";

                                        $result6 = $coopex->query($sqlFeminino6);
                                        $row6 = $result6->fetch(PDO::FETCH_OBJ);
                                        $fraco = $row6->{"fraco_" . $sexo};
                                        $razoavel_min = $row6->{"razoavel_min_" . $sexo};
                                        $razoavel_max = $row6->{"razoavel_max_" . $sexo};
                                        $bom_min = $row6->{"bom_min_" . $sexo};
                                        $bom_max = $row6->{"bom_max_" . $sexo};
                                        $muito_bom_min = $row6->{"m_bom_min_" . $sexo};
                                        $muito_bom_max = $row6->{"m_bom_max_" . $sexo};
                                        $excelencia = $row6->{"excelencia_" . $sexo};


                                        if ($salto <= $fraco) {
                                            $faixa_referencia = "Fraco";
                                            $color = "style = 'color: orange'";
                                        } elseif ($salto <= $razoavel_max) {
                                            $faixa_referencia = "Razoável";
                                            $color = "style = 'color: orange'";
                                        } elseif ($salto <= $bom_max) {
                                            $faixa_referencia = "Bom";
                                            $color = "style = 'color: green'";
                                        } elseif ($salto <= $muito_bom_max) {
                                            $faixa_referencia = "Muito Bom";
                                            $color = "style = 'color: green'";
                                        } else {
                                            $faixa_referencia = "Excelente";
                                            $color = "style = 'color: green'";
                                        }
                                    ?>
                                        <td><?php echo  '<strong>' . $salto . '</strong>' . '<p ' . $color . '> (' . $faixa_referencia . ')</p>'; ?></td>
                                    <?
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold">Agilidade</td>
                                    <?
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {
                                        $quadrado = $row->quadrado;

                                        $sqlFeminino6 = "SELECT fraco_$sexo, razoavel_min_$sexo, razoavel_max_$sexo, bom_min_$sexo, bom_max_$sexo, 
                                        m_bom_min_$sexo, m_bom_max_$sexo, excelencia_$sexo FROM colegio.quadrado WHERE idade = $idade";

                                        $result6 = $coopex->query($sqlFeminino6);
                                        $row6 = $result6->fetch(PDO::FETCH_OBJ);
                                        $fraco = $row6->{"fraco_" . $sexo};
                                        $razoavel_min = $row6->{"razoavel_min_" . $sexo};
                                        $razoavel_max = $row6->{"razoavel_max_" . $sexo};
                                        $bom_min = $row6->{"bom_min_" . $sexo};
                                        $bom_max = $row6->{"bom_max_" . $sexo};
                                        $muito_bom_min = $row6->{"m_bom_min_" . $sexo};
                                        $muito_bom_max = $row6->{"m_bom_max_" . $sexo};
                                        $excelencia = $row6->{"excelencia_" . $sexo};

                                        if ($quadrado <= $fraco) {
                                            $faixa_referencia = "Fraco";
                                            $color = "style = 'color: orange'";
                                        } elseif ($quadrado <= $razoavel_max) {
                                            $faixa_referencia = "Razoável";
                                            $color = "style = 'color: orange'";
                                        } elseif ($quadrado <= $bom_max) {
                                            $faixa_referencia = "Bom";
                                            $color = "style = 'color: green'";
                                        } elseif ($quadrado <= $muito_bom_max) {
                                            $faixa_referencia = "Muito Bom";
                                            $color = "style = 'color: green'";
                                        } else {
                                            $faixa_referencia = "Excelente";
                                            $color = "style = 'color: green'";
                                        }
                                    ?>
                                        <td><?php echo  '<strong>' . $quadrado . '</strong>' . '<p ' . $color . '> (' . $faixa_referencia . ')</p>'; ?></td>
                                    <?
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td class="text-left font-weight-bold">Velocidade</td>
                                    <?php
                                    $res = $coopex->query($sql);
                                    while ($row = $res->fetch(PDO::FETCH_OBJ)) {
                                        $corrida = $row->corrida_metros;
                                      
                                        $sql11 = "SELECT $sexo FROM colegio.teste_corrida WHERE idade = $idade";
                                        $result11 = $coopex->query($sql11);
                                        if ($result11 && $result11->rowCount() > 0) {
                                            $row_corrida = $result11->fetch(PDO::FETCH_ASSOC);

                                            $rowCorrida = $row_corrida[$sexo];

                                            if ($corrida == $rowCorrida || $corrida >= $rowCorrida) {
                                                $color = "style='color: green'";
                                            } else {
                                                $color = "style='color: orange'";
                                            }
                                        } else {
                                            echo "Erro: idade não encontrada no banco de dados!";
                                        }

                                        $sqlFeminino6 = "SELECT fraco_$sexo, razoavel_min_$sexo, razoavel_max_$sexo, bom_min_$sexo, bom_max_$sexo, 
                                        m_bom_min_$sexo, m_bom_max_$sexo, excelencia_$sexo FROM colegio.teste_corrida WHERE idade = $idade";

                                        $result6 = $coopex->query($sqlFeminino6);
                                        $row6 = $result6->fetch(PDO::FETCH_OBJ);

                                        $fraco = $row6->{"fraco_" . $sexo};
                                        $razoavel_min = $row6->{"razoavel_min_" . $sexo};
                                        $razoavel_max = $row6->{"razoavel_max_" . $sexo};
                                        $bom_min = $row6->{"bom_min_" . $sexo};
                                        $bom_max = $row6->{"bom_max_" . $sexo};
                                        $muito_bom_min = $row6->{"m_bom_min_" . $sexo};
                                        $muito_bom_max = $row6->{"m_bom_max_" . $sexo};
                                        $excelencia = $row6->{"excelencia_" . $sexo};

                                        if ($corrida <= $fraco) {
                                            $faixa_referencia = "Fraco";
                                            $referencia1 = $fraco;
                                        } elseif ($corrida <= $razoavel_max) {
                                            $faixa_referencia = "Razoável";
                                            $referencia1 = $razoavel_min;
                                            $referencia2 = $razoavel_max;
                                        } elseif ($corrida <= $bom_max) {
                                            $faixa_referencia = "Bom";
                                            $referencia1 = $bom_min;
                                            $referencia2 = $bom_max;
                                        } elseif ($corrida <= $muito_bom_max) {
                                            $faixa_referencia = "Muito Bom";
                                            $referencia1 = $muito_bom_min;
                                            $referencia2 = $muito_bom_max;
                                        } else {
                                            $faixa_referencia = "Excelente";
                                            $referencia1 = $excelencia;
                                        }
                                    ?>
                                        <td><?php echo '<strong>' . $corrida . '</strong>' . '<p ' . $color . '> (' . $faixa_referencia . ')</p>'; ?></td>

                                    <?php
                                    }
                                    ?>
                                </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>

        </div>
</main>