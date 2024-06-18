<?
require_once("inc/sqlsrv.php");

//inicia o processo de login
function login($usuario, $senha)
{
    if (isset($_SESSION['app'])) {
        unset($_SESSION['app']);
    }

    $sql = "SELECT
                USR_ID_PESSOA AS id_pessoa,
                USR_DS_NICKNAME AS usuario,
                USR_DS_SENHA AS senha,
                USR_DS_NOME AS nome,
                USR_DS_EMAIL AS email,
                FORMAT(USR_DT_NASCIMENTO, 'yyyy-MM-dd') AS nascimento,
                USR_DS_ENDERECO AS endereco,
                USR_DS_BAIRRO AS bairro,
                USR_DS_CIDADE AS cidade,
                USR_SG_ESTADO AS uf,
                USR_NU_CEP AS cep,
                USR_NU_DDD AS ddd,
                USR_NU_TELEFONE AS telefone,
                aue_id_aluno AS id_aluno 
            FROM
                academico..usr_usuario a
                INNER JOIN academico..AUE_aluno_unidade_ensino b ON a.USR_ID_PESSOA = b.aue_id_responsavel 
            WHERE
                USR_DS_NICKNAME = '$usuario'";
    $res = mssql_query($sql);

    // verifica se existe um usuário como responsável
    if (mssql_num_rows($res)) {
        while ($row = mssql_fetch_object($res)) {
            $responsavel = $row;
            $dependente[] = $row->id_aluno;
        }
        // se existir faz o login como responsável
        return login_responsavel($responsavel, $senha, $dependente);
    } else {
        // se existir faz o login como aluno
        return login_aluno($usuario, $senha);
    }
}

// faz login como responsável
function login_responsavel($responsavel, $senha, $dependente)
{
    if ($responsavel->senha == md5($senha)) {
        $_SESSION['app']['tipo_login'] = 1;
        $_SESSION['app']['responsavel'] = $responsavel;

        //carrega os alunos dependentes
        return carrear_aluno($dependente);
    } else {
        $array = array(
            "erro" => 3,
            "message" => "Senha inválida!"
        );
        return $array;
    }
}

function carrear_aluno($id_pessoa)
{
    for ($i = 0; $i < count($id_pessoa); $i++) {
        $sql = "SELECT
					pes_id_pessoa AS id_pessoa,
					pes_nm_pessoa AS nome,
					crs_id_curso AS id_curso,
					ser_id_serie AS id_serie,
                    ser_ds_serie AS serie,
					tcu_id_turma_curso AS id_turma, 
                    tcu_ds_turma_curso AS turma,
                    USR_NU_CPF AS cpf,
                    USR_NU_IDENTIDADE AS rg,
                    FORMAT(USR_DT_NASCIMENTO, 'yyyy-MM-dd') AS nascimento,
                    pes_ds_email AS email,
                    aue_id_responsavel AS id_responsavel,
                    aue_nm_responsavel AS nome_responsavel,
                    aue_ch_gemeo AS gemeo,
                    aue_nm_responsavel_nis_pai AS nome_pai,
                    aue_nm_mae AS nome_mae,
                    alu_nu_matricula AS ra,
                    alu_ch_destro AS destro,
                    USR_CD_SEXO AS sexo,
                    crs_nm_resumido AS curso 
				FROM
					academico..HIS_historico_ingresso_saida a
					INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
					INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_registro_curso = a.his_id_registro_curso
					INNER JOIN academico..CRS_curso ON rca_id_curso = crs_id_curso
					INNER JOIN academico..COL_colegiado ON crs_id_unidade = col_id_colegiado
					INNER JOIN academico..FAC_faculdade ON fac_id_faculdade = col_id_faculdade
					INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view SAP0 ON rca_id_registro_curso = SAP0.sap_id_registro_curso
					INNER JOIN academico..PEL_periodo_letivo PEL0 ON PEL0.pel_id_periodo_letivo = SAP0.sap_id_periodo_letivo
					INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
					INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
					INNER JOIN registro..PES_pessoa ON PES_pessoa.pes_id_pessoa = rca_id_aluno
					INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
					INNER JOIN academico..TCU_turmas_curso ON tcu_id_turma_curso = rca_id_turma_curso
					INNER JOIN academico..AUE_aluno_unidade_ensino ON aue_id_aluno = pes_id_pessoa
                    INNER JOIN academico..usr_usuario ON USR_ID_PESSOA = PES_ID_PESSOA  
				WHERE
					pel_ds_compacta = '20240' 
					AND fac_id_faculdade = 1000000006 
					AND iap_id_periodo_letivo = SAP0.sap_id_periodo_letivo 
					AND PES_ID_PESSOA = " . $id_pessoa[$i];

        $res = mssql_query($sql);
        $row = mssql_fetch_object($res);
        $_SESSION['app']['aluno'][] = $row;

        if($i == 0){
            $_SESSION['app']['id_pessoa'] = $row->id_pessoa;
            $_SESSION['app']['pessoa_ativa'] = 0;
        }
    }

    $array = array(
        "erro" => 1,
        "message" => "Logado com sucesso!"
    );
    return $array;
}

//faz login como aluno
function login_aluno($usuario, $senha)
{

    $sql = "SELECT 
                USR_ID_PESSOA AS id_pessoa,
                USR_DS_NICKNAME AS usuario,
                USR_DS_SENHA AS senha
            FROM
                academico..usr_usuario a
            WHERE
                USR_DS_NICKNAME = '$usuario'";
    $res = mssql_query($sql);

    if (mssql_num_rows($res)) {
        $row = mssql_fetch_object($res);
        if ($row->senha == md5($senha)) {
            $aluno[] = $row->id_pessoa;
            $_SESSION['app']['tipo_login'] = 2;
            //carrega o aluno
            return carrear_aluno($aluno);
        } else {
            $array = array(
                "erro" => 3,
                "message" => "Senha inválida!"
            );
            return $array;
        }
    } else {
        $array = array(
            "erro" => 2,
            "message" => "Usuário não encontrado!"
        );
        return $array;
    }
}
