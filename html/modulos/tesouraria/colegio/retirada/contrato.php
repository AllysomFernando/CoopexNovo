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


    $sql_end = "SELECT
                    * 
                FROM
                    academico..END_endereco 
                WHERE
                    end_id_pessoa = $id_pessoa";
    $res_end = mssql_query($sql_end);
    $row_end = mssql_fetch_assoc($res_end);                

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
                Declaração de Retirada de Material
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
                    CONTRATO DE FORNECIMENTO DE MATERIAL DIDÁTICO PARA USO INDIVIDUAL
                    </h3>
                    <div class="text-dark fw-700 h1 mb-g keep-print-font">
                        
                    </div>
                </div>
            </div>

            <?
                $sql = "SELECT
                            ttf_ds_titulo AS material,
                            ttf_vl_face AS valor,
                            ttf_st_situacao AS situacao,
                            FORMAT (
                                ttf_dt_cadastro,
                                'yyyy-MM-dd HH:mm:ss'
                            ) AS data_pagamento
                        FROM
                            financeiro..TTF_titulo_financeiro
                        WHERE
                            ttf_id_tipo_titulo = 1000000566
                        AND ttf_dt_referencia >= '2024-01-01 00:00:00.000'
                        AND ttf_st_situacao IN ('P', 'L', 'G', 'R', 'S')
                        AND ttf_id_cliente_fornecedor = $id_pessoa";
                $res_val = mssql_query($sql);
                $row_val = mssql_fetch_assoc($res_val);

            ?>

            <div class="row">
                <div class="p-3" style="font-size: 16px;">
                <strong>ALUNO(A)</strong><br>
                <table class="table">
                    <tr>
                        <td>Nome</td>
                        <td colspan="3"><strong><?=utf8_encode($row['nome'])?></strong></td>
                    </tr>
                    <tr>
                        <td>Ensino</td>
                        <td><strong><?=utf8_encode($row['curso'])?></strong></td>
                        <td>Ano/Série</td>
                        <td><strong><?=utf8_encode($row['serie'])?></strong></td>
                    </tr>
                    <tr>
                        <td>Endereço</td>
                        <td><strong><?=utf8_encode($row_end['end_nm_logradouro'])?></strong></td>
                        <td>Número</td>
                        <td><strong><?=utf8_encode($row_end['end_nu_imovel'])?></strong></td>
                    </tr>
                    <tr>
                        <td>Bairro</td>
                        <td><strong><?=utf8_encode($row_end['end_nm_bairro'])?></strong></td>
                        <td>Cidade</td>
                        <td><strong><?=utf8_encode($row_end['end_nm_cidade'])?></strong></td>
                    </tr>
                </table>
                <strong>CONTRATANTE</strong>:<br>
                <table class="table">
                    <tr>
                        <td>Nome</td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td>CPF</td>
                        <td></td>
                        <td>RG</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
               
                <strong>FUNDAÇÃO ASSIS GURGACZ</strong>, pessoa jurídica de direito privado, inscrita no CNPJ sob nº 02.203.539/0001-73, com sede na Avenida das Torres, nº 500, nesta cidade de Cascavel, Estado do Paraná, mantenedora do <strong>COLÉGIO FAG</strong>, doravante denominada <strong>COLÉGIO</strong>, e, de outro lado, o (a) <strong>CONTRATANTE</strong>, acima qualificado (a), pelo presente Contrato de Fornecimento de Material Didático para Uso Individual, têm justo e contratado o seguinte:<br><br>

                <strong>CLÁUSULA PRIMEIRA</strong>: O objeto do presente instrumento é o fornecimento de material didático POLIEDRO e BILÍNGUE do <strong>COLÉGIO</strong> ao (a) <strong>CONTRATANTE</strong>, relativos ao ano/série indicado no preâmbulo, a fim de possibilitar o devido acompanhamento, desenvolvimento e participação do <strong>ALUNO (A)</strong> em suas atividades escolares e em seu processo didático-pedagógico.<br><br>

<strong>CLÁUSULA SEGUNDA</strong>: A entrega dos materiais didáticos ocorrerá de forma fracionada e/ou de acordo com as atividades pedagógicas e o calendário do <strong>COLÉGIO</strong>, mediante assinatura de recibo.<br><br>

<strong>Parágrafo Único</strong>: Ajustam as partes, desde já, que a entrega dos materiais estará condicionada à adimplência do <strong>CONTRATANTE</strong> no pagamento das parcelas descritas na Cláusula Terceira, de modo que, na eventual falta de pagamento de quaisquer delas, os materiais permanecerão retidos até a integral regularização do débito.<br><br>

<strong>CLÁUSULA TERCEIRA</strong>: Em contraprestação ao fornecimento dos materiais didáticos POLIEDRO, o (a) <strong>CONTRATANTE</strong> pagará ao <strong>COLÉGIO</strong> o valor total de R$ ___________________, o qual poderá ser dividido, mediante cartão de crédito ou cheque, em até 08 (oito) parcelas idênticas, mensais e consecutivas, vencendo a primeira na data de assinatura deste contrato.<br><br>
<? $row_val = mssql_fetch_assoc($res_val); ?>
<strong>Parágrafo Primeiro</strong>: Em se tratando de turma BILÍNGUE, além do pagamento acima, o (a) <strong>CONTRATANTE</strong> pagará ao <strong>COLÉGIO</strong> o valor total de R$ ___________________, o qual poderá ser dividido, mediante cartão de crédito ou cheque, em até 08 (oito) parcelas idênticas, mensais e consecutivas, vencendo a primeira na data de assinatura deste contrato.<br><br>

<strong>Parágrafo Segundo</strong>: Somente serão considerados quitados os pagamentos feitos em cheques após a compensação bancária dos mesmos. O recebimento das prestações de forma diversa do ora pactuado constituirá mera tolerância, que não afetará de forma alguma as cláusulas e condições desta composição, nem importará em modificação do ajustado.<br><br><br><br>

<strong>Parágrafo Terceiro</strong>: Caso o pagamento de qualquer parcela seja efetuado após o vencimento, o valor vencido será acrescido de multa de 2%, mais juros de 0,33% por dia de atraso e aplicação de correção monetária pelo INPC, da data do vencimento até o dia do efetivo pagamento.<br><br>

<strong>CLÁUSULA QUARTA: O (A) CONTRATANTE</strong> declara-se ciente de que o valor pago pela aquisição do material objeto deste contrato, não se confunde e nem faz parte do valor da anuidade escolar, a qual cobre, exclusivamente, a contrapartida da prestação dos serviços educacionais e atividades pedagógicas.<br><br>

<strong>CLÁUSULA QUINTA</strong>: O <strong>COLÉGIO</strong>, após a entrega efetiva do material ao Aluno(a) ou Responsável, não se responsabiliza por eventual descarte, perda, dano ou extravio do material didático, sendo o(a) <strong>CONTRATANTE</strong> integralmente responsável pela sua guarda e cuidado, devendo arcar com os valores da aquisição de um novo material em caso de inutilização do material entregue.<br><br>

<strong>Parágrafo Único</strong>: Na hipótese em que o (a) <strong>CONTRATANTE</strong> noticie eventual perda ou extravio do material, este poderá adquiri-lo novamente junto ao <strong>COLÉGIO</strong>. Entretanto, o <strong>COLÉGIO</strong> não fica obrigado a oferecer as mesmas condições relativas a preço e prazo da compra tradicional, dirimindo tais questões de acordo com a demanda do <strong>CONTRATANTE</strong>.<br><br>

<strong>CLÁUSULA SEXTA</strong>: Estabelecem as partes que o presente instrumento possui força de título executivo extrajudicial, nos termos do artigo 784 do Código de Processo Civil.<br><br> 

<strong>CLÁUSULA SÉTIMA</strong>: Estabelecem as partes que o foro competente para solucionar quaisquer dúvidas pela execução deste Contrato, é o do local do fornecimento dos materiais, qual seja, a Comarca de Cascavel, Estado do Paraná, com renúncia expressa a qualquer outro por mais especial e privilegiado que seja.<br><br>

E, por estarem justos e contratados, assinam o presente instrumento em 02 (duas) vias de igual teor e forma, para que se produzam todos os efeitos legais.



                    
                </div>
              
            </div>

            <div class="p-3 text-center" style="font-size: 16px;">
            <?php
                setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

                $dataAtual = strftime('%d de %B de %Y');

                echo "Cascavel, $dataAtual";
            ?>

            </div>  

            <div class="row mt-5">
                <div class="col">
                    <strong>TESTEMUNHAS</strong>
                    <br><br><br><br>
                    NOME: ________________________________________
                    <br><br><br>
                    CPF: ___________________________________________
                </div>

                <div class="col text-right">
                    <strong>CONTRATANTE</strong>
                    <br><br><br><br>
                    NOME: ________________________________________
                    <br><br><br>
                    CPF: __________________________________________
                </div>
            </div>

   


        </div>
    </div>
</main>
                   