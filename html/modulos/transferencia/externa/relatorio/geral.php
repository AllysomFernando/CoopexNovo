<?php
$id_menu = 87;
?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<main id="js-page-content" role="main" class="page-content naoimprimir">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Transferência</a></li>
        <li class="breadcrumb-item">Relatórios</li>
        <li class="breadcrumb-item active">Externa</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-credit-card-front'></i> Ficha Transferência
            <small>
                Ficha Transferência
            </small>
        </h1>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>
                        Relatório
                    </h2>
                </div>
                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <select id="tipo" class="select2 form-control">
                                    <option value="0">Selecione o Tipo</option>
                                    <option value="1">Geral</option>
                                    <option value="2">Resultados</option>
                                    <option value="3">Cursos por Instituição</option>

                                </select>
                                <div class="invalid-feedback">
                                    Selecione o tipo
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label" for="validationCustom03">Período</label>
                                <?php
                                $sql = "SELECT DISTINCT CONCAT(year(data_cadastro), '-', IF(month(data_cadastro) < 7, 1, 2)) AS data_formatada
                                FROM transferencia.transferencia_externa ORDER BY data_formatada DESC";
                                $periodos = $coopex->query($sql);

                                ?>
                                <select id="id_periodo" class="select2 form-control">
                                    <option value="0">Selecione o Período</option>
                                    <?php
                                    while ($row = $periodos->fetch(PDO::FETCH_OBJ)) {

                                    ?>
                                        <option value="<?php echo $row->data_formatada ?>">
                                            <?php echo $row->data_formatada ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Selecione o período da reoferta
                                </div>
                            </div>
                        </div>


                        <button onclick="carregar_relatorio();" class="btn btn-primary ml-auto" type="button">Gerar Relatório</button>
                    </div>
                </div>
            </div>
            <div id="resultado_relatorio_c">
                <div id="resultado_relatorio" class="mt-8"></div>
            </div>
        </div>

</main>



<script src="js/formplugins/select2/select2.bundle.js"></script>

<script>
    function carregar_relatorio() {
        var tipo = $("#tipo").val();
        var id_periodo = $("#id_periodo").val();
        console.log(id_periodo);


        var url = "modulos/transferencia/externa/relatorio/transferencia_ajax.php?tipo=" + tipo + "&id_periodo=" + id_periodo;

        $("#resultado_relatorio").load(url, function() {
            $.scrollTo('#resultado_relatorio_c', 1000, {
                easing: 'easeOutQuart'
            });
        });
    }

    $(document).ready(function() {
        $('.select2').select2();
    });
</script>