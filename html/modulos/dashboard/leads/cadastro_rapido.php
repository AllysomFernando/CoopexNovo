<?php

$id_menu = 113;
$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
   
$sql = "SELECT fp.nome AS nome_aluno, fp.numero, fp.email, fp.campus, cc.curso, cc.id_curso, fp.data, fp.contato, pes.nome FROM leads_form.pessoas fp INNER JOIN coopex.curso cc ON fp.curso = cc.id_curso INNER JOIN coopex.departamento_pessoa dp ON fp.curso = dp.id_departamento INNER JOIN coopex.pessoa pes ON pes.id_pessoa = dp.id_pessoa  WHERE dp.id_pessoa = $id_pessoa ORDER BY nome DESC";

$row = $coopex->query($sql);

?>

<iframe class="d-none" name="dados" src=""></iframe>
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/date-eu.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Leads</a></li>
        <li class="breadcrumb-item active">Consulta</li>
        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>
    <div class="subheader">
        <h1 class="subheader-title col-6">
            <i class='subheader-icon fal fa-repeat'></i> Leads
            <small>
                Gerenciamento de Leads
            </small>
        </h1>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">

                <div class="panel-container show">
                    <div class="panel-content">
                        <!-- datatable start -->
                        <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Número</th>
                                    <th>E-mail</th>
                                    <th>Campus</th>
                                    <th>Curso</th>
                                    <th>Dia do cadastro</th>
                                    <th>Contato Preferido</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($res = $row->fetch(PDO::FETCH_OBJ)) {
                                ?>
                                    <tr>
                                        <td><?php echo texto($res->nome_aluno) ?></td>
                                        <td class="pointer"><?php echo texto($res->numero) ?></td>
                                        <td class="pointer"><?php echo $res->email ?></td>
                                        <td><?php echo ($res->campus == 1) ? 'Cascavel' : 'Toledo'; ?></td>
                                        <td><?php echo texto($res->curso) ?></td>
                                        <td><?php echo converterData($res->data) ?></td>
                                        <td><?php echo texto($res->contato) ?></td>
                                        <td style="width: 130px">
                                            <?php
                                            if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])) {
                                            ?>
                                                <a href="https://api.whatsapp.com/send?phone=<?php echo converterTelefone($res->numero); ?>&text=Olá,%20seja%20muito%20bem-vindo(a)!%20Me%20chamo%20<?php echo urlencode($res->nome); ?>%20e%20estou%20à%20frente%20da%20coordenação%20do%20curso%20de%20<?php echo urlencode($res->curso); ?>.%20Estou%20aqui%20para%20esclarecer%20qualquer%20pergunta%20que%20você%20tenha%20sobre%20o%20curso%20e%20as%20oportunidades%20profissionais%20na%20área.%20Como%20posso%20ser%20útil%20hoje?" class="btn btn-sm btn-icon btn-outline-success rounded-circle mr-2" title="Enviar Whatsapp">
                                                    <i class="fas fa-phone"></i>
                                                </a>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])) {
                                            ?>
                                                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo $res->email; ?>&su=Coordenação%20<?php echo urlencode($res->curso); ?>%20Centro%20FAG&body=Olá,%20seja%20muito%20bem-vindo(a)!%20Me%20chamo%20<?php echo urlencode($res->nome); ?>%20e%20estou%20à%20frente%20da%20coordenação%20do%20curso%20de%20<?php echo urlencode($res->curso); ?>.%20Estou%20aqui%20para%20esclarecer%20qualquer%20pergunta%20que%20você%20tenha%20sobre%20o%20curso%20e%20as%20oportunidades%20profissionais%20na%20área.%20Como%20posso%20ser%20útil%20hoje?" class="btn btn-sm btn-icon btn-outline-warning rounded-circle mr-2" title="Enviar Email">
                                                    <i class="fal fa-envelope"></i>
                                                </a>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
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

<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>
    $(document).ready(function() {
        $('#dt-basic-example').dataTable({
            responsive: true,
            columnDefs: [{
                targets: 5,
                render: function(data, type, row) {
                    var date = data.split("/");
                    return type === 'sort' ? date[2] + date[1] + date[0] : data;
                }
            }],
            order: [
                [5, 'desc']
            ]
        });
    });

    function exclusaoOK() {
        Swal.fire({
            type: "success",
            title: "Registro excluido com sucesso!",
            showConfirmButton: false,
            timer: 1500,
            onClose: () => {
                document.location.reload(true)
            }
        });
    }

    function exclusaoFalha() {
        Swal.fire({
            type: "error",
            title: "Falha ao excluir registro",
            showConfirmButton: true
        });
    }
</script>
</body>

</html>