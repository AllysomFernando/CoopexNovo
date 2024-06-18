<?php 

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

  ?>

<link rel="stylesheet" media="screen, print" href="css/page-invoice.css">

<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Colégio</a></li>
        <li class="breadcrumb-item">Sports School</li>
        <li class="breadcrumb-item active">Camisetas</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-plus-circle'></i> Sports School
            <small>
            Camisetas
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
                    <h3 class="fw-300 display-5 fw-500  keep-print-font pt-4 l-h-n m-0">
                        SPORTS SCHOOL: RELAÇÃO DE CAMISETAS
                    </h3>
                    <div class="text-dark fw-700 h1 mb-g keep-print-font">
                        
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table mt-5">
                            <thead>
                                <tr>
                                    <th class="text-left border-top-0 table-scale-border-bottom fw-700">Nome na Camiseta</th>
                                    <th class="text-center border-top-0 table-scale-border-bottom fw-700">Tamanho</th>
                                    <th class="text-center border-top-0 table-scale-border-bottom fw-700">Pago</th>
                                    <th class="text-left border-top-0 table-scale-border-bottom fw-700">Responsável</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?
                                $sql2 = "SELECT
                                            nome_camiseta,
                                            tamanho,
                                            pagamento,
                                            responsavel 
                                        FROM
                                            colegio.sports
                                            INNER JOIN colegio.camiseta_tamanho USING ( id_camiseta_tamanho ) 
                                        ORDER BY
                                            nome_camiseta ASC";
                                $material2 = $coopex->query($sql2);
                                while($row2 = $material2->fetch(PDO::FETCH_OBJ)){
                            ?>
                                <tr>
                                    <td class="text-left fw-700" style="text-transform: uppercase;"><?=utf8_encode($row2->nome_camiseta)?></td>
                                    <td class="text-center strong"><?=$row2->tamanho?></td>
                                    <td class="text-center"><?=$row2->pagamento ? "<strong>SIM</strong>" : "NÃO"?></td>
                                    <td class="text-left" style="text-transform: uppercase;"><?=utf8_encode($row2->responsavel)?></td>
                                </tr>
                            <?
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>
</main>
                   