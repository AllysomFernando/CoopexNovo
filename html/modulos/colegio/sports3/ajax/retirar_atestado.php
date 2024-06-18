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
        SET `id_situacao_atestado` = 0, `data_atestado` = null
        WHERE `id_pessoa` = $id_pessoa";
$res = $coopex->query($sql);

$sql = "UPDATE `colegio`.`atestado`
        SET `id_situacao_atestado` = 0, `data_atestado` = null
        WHERE `id_pessoa` = $id_pessoa";
$res = $coopex->query($sql);
