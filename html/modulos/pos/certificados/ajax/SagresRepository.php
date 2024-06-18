<?php

require_once __DIR__ . '/../../../../php/repository/AbstractDatabaseClient.php';

class SagresRepository extends AbstractDatabaseClient
{

	public function __construct()
	{
		parent::__construct('sqlserver', 'portal3', 'integracao', 'FAGintegracao20anos', '10.0.0.150:49320');
	}

	public function getPessoaInfoByCpf($cpf)
	{
		$this->connect();
		$sql = "SELECT
					pes_id_pessoa,
					pes_nm_pessoa,
					pef_cd_sexo,
					esc_ds_estado_civil,
					FORMAT( pef_dt_nascimento, 'dd/MM/yyyy' ) AS pef_dt_nascimento,
					pef_nu_identidade,
					pef_tp_documento_militar,
					pef_nu_titulo_eleitor,
					pef_nm_cidade_nascimento,
					ncl_ds_nacionalidade,
					pef_nm_pai,
					pef_nm_mae,
					ra
				FROM
					registro..PES_pessoa
					INNER JOIN registro..PEF_pessoa_fisica ON pes_id_pessoa = pef_id_pessoa
					INNER JOIN registro..ESC_estado_civil ON esc_id_estado_civil = pef_id_estado_civil
					INNER JOIN registro..NCL_nacionalidade ON pef_id_nacionalidade = ncl_id_nacionalidade
					INNER JOIN integracao..view_integracao_usuario_egresso ON pes_nu_cpf_cgc = cpf
				WHERE
					pes_nu_cpf_cgc = :cpf";
		$stmt = $this->client->prepare($sql);
		$stmt->bindParam(':cpf', $cpf);
		$stmt->execute();
		$res = $stmt->fetch(PDO::FETCH_OBJ);
		$this->disconnect();

		return $res;
	}

	public function getTccInfoByCpfAndDataEmissao($cpf, $data_emissao)
	{
		$this->connect();
		$sql = "SELECT
					rca_id_registro_curso,
					pes_id_pessoa,
					rca_id_registro_curso,
					RTRIM( pes_nm_pessoa ) AS Nome,
					RTRIM( alu_nu_matricula ) AS RA,
					hat_id_historico_atividade,
					RTRIM( tae_ds_tipo_atividade ) AS Atividade,
					hat_ds_atividade,
					( SELECT TOP ( 1 ) pes_nm_pessoa FROM registro..PES_pessoa WHERE pes_id_pessoa = hat_id_professor ) AS orientador,
					CONVERT ( CHAR ( 10 ), hat_dt_inicio, 103 ) AS dataInicio,
					CONVERT ( CHAR ( 10 ), hat_dt_fim, 103 ) AS dataTermino,
					CONVERT ( TEXT, rca_ds_observacao ) AS obs
				FROM
					academico..ALU_aluno
						INNER JOIN registro..PES_pessoa ON pes_id_pessoa = alu_id_pessoa
						INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = alu_id_pessoa
						INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
						LEFT OUTER JOIN academico..HAT_historico_atividade ON hat_id_registro_curso = rca_id_registro_curso
						LEFT OUTER JOIN academico..TAE_tipo_ativ_extracurricular ON tae_id_tipo_atividade = hat_id_tipo_atividade
				WHERE
						pes_nu_cpf_cgc = :cpf
				AND tae_ds_tipo_atividade = 'Monografia'
				ORDER BY ABS((YEAR(hat_dt_fim) - YEAR(:data_emissao)) - (MONTH(hat_dt_fim) - MONTH(:data_emissao))) ASC";

		$stmt = $this->client->prepare($sql);
		$stmt->bindParam(':cpf', $cpf);
		$stmt->bindParam(':data_emissao', $data_emissao);
		$stmt->execute();
		$res = $stmt->fetch(PDO::FETCH_OBJ);
		$this->disconnect();

		return $res;
	}

	public function getDisciplinasByIdCurso($id_curso)
	{
		$this->connect();

		$sql = "SELECT DISTINCT rca_id_registro_curso,
    crs_nm_resumido,
    crs_nm_curso,
cla_ds_classe,
atc_id_atividade,
atc_nm_atividade,
atc_qt_horas,
hse_vl_resultado,
crs_id_tp_curso
FROM academico..RCA_registro_curso_aluno
INNER JOIN academico..HSE_historico_escolar ON hse_id_registro_curso = rca_id_registro_curso
INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
INNER JOIN academico..ATC_atividade_curricular ON atc_id_atividade = hse_id_atividade
INNER JOIN academico..CLA_CLASSE ON cla_id_atividade_curricular = atc_id_atividade
INNER JOIN academico..PEL_periodo_letivo ON hse_id_periodo_fim = pel_id_periodo_letivo
WHERE hse_vl_resultado IS NOT NULL
AND ( crs_nm_resumido LIKE :id_curso)
    ORDER BY crs_nm_curso
    OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY";

		$stmt = $this->client->prepare($sql);
		$stmt->bindValue(':id_curso', $id_curso . '%');
		$stmt->execute();
		$res = $stmt->fetchAll(PDO::FETCH_OBJ);
		$this->disconnect();

		return $res;
	}
}
