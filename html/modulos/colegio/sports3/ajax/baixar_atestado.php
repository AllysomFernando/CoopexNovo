<?php
require_once("../../../../php/config.php");
require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
extract($_GET);

//$date = date("Y-m-d");

$sql = "UPDATE `colegio`.`matricula`
        SET `id_situacao_atestado` = 1, `data_atestado` = NOW()
        WHERE `id_pessoa` = $id_pessoa";
$res = $coopex->query($sql);

$sql = "UPDATE `colegio`.`atestado`
        SET `id_situacao_atestado` = 1, `data_atestado` = NOW()
        WHERE `id_pessoa` = $id_pessoa";
$res = $coopex->query($sql);

$sql = "SELECT id_atestado FROM `colegio`.`atestado`
        WHERE `id_pessoa` = $id_pessoa";
$res = $coopex->query($sql);
$res->fetch(PDO::FETCH_OBJ);

if (!$res->rowCount()) {
        $sql = "INSERT INTO `colegio`.`atestado`
        SET `id_situacao_atestado` = 1, `data_atestado` = NOW(), `id_pessoa` = $id_pessoa, `extensao` = 'sec'";
        $res = $coopex->query($sql);
}
