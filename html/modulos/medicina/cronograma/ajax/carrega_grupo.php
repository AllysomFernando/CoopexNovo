<?php session_start();
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$id_periodo = $_GET['id_periodo'];

	$sql = "SELECT
				DATE_FORMAT(pre_inscricao_data_inicial, '%d/%m/%Y') as pre_inscricao_data_inicial,
				DATE_FORMAT(pre_inscricao_data_final, '%d/%m/%Y') as pre_inscricao_data_final,
				DATE_FORMAT(inscricao_data_inicial, '%d/%m/%Y') as inscricao_data_inicial,
				DATE_FORMAT(inscricao_data_final, '%d/%m/%Y') as inscricao_data_final
			FROM
				coopex_reoferta.periodo
			WHERE id_periodo = $id_periodo";

	$periodo = $coopex->query($sql);
	$row = $periodo->fetch(PDO::FETCH_OBJ);
	echo json_encode($row);

?>


<div id="js_list_accordion" class="accordion accordion-hover accordion-clean js-list-filter">
    <div class="card border-top-left-radius-0 border-top-right-radius-0">
        <div class="card-header">
            <a href="javascript:void(0);" class="card-title" data-toggle="collapse" data-target="#js_list_accordion-a" aria-expanded="true" data-filter-tags="settings">
                <i class="fal fa-cog width-2 fs-xl"></i>
                Settings
                <span class="ml-auto">
                    <span class="collapsed-reveal">
                        <i class="fal fa-chevron-up fs-xl"></i>
                    </span>
                    <span class="collapsed-hidden">
                        <i class="fal fa-chevron-down fs-xl"></i>
                    </span>
                </span>
            </a>
        </div>
        <div id="js_list_accordion-a" class="collapse show" data-parent="#js_list_accordion" style="">
            <div class="card-body">
                <select name="id_docente" data-placeholder="Selecione o docente da disciplina" class="js-consultar-usuario form-control" >
					<?php
						if(isset($dados->id_docente)){
							$id_docente = $dados->id_docente;
							$sql = "SELECT DISTINCT
										id_pessoa,
										nome
									FROM
										integracao..view_integracao_usuario 
									WHERE
										id_pessoa IN ($id_docente)";
							$res = mssql_query($sql);

						 	while($row = mssql_fetch_assoc($res)){
					?>
							<option  value="<?php echo $row['id_pessoa']?>"><?php echo trim(utf8_encode($row['nome']))?></option>
					<?php
					 		}
					?>
					<?php	
					 	}
					?>
				</select>
            </div>
        </div>
    </div>

</div>
<span class="filter-message js-filter-message"></span>
</div>