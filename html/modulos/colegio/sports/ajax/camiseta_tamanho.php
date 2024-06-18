<?php
require_once("../../../../php/config.php");
require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
extract($_GET);

echo $sql = "UPDATE `colegio`.`sports` SET `id_camiseta_tamanho` = '$tamanho' WHERE `id_sports` = '$id_sports'";
$res = $coopex->query($sql);
