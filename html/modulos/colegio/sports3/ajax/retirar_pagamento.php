<?php
require_once("../../../../php/config.php");
require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
extract($_GET);

$date = date("Y-m-d");

$sql = "UPDATE `colegio`.`sports`
        SET `pagamento` = 0, `data_pagamento` = null, `tesouraria` = 0
        WHERE `id_sports` = $id_sports";
$res = $coopex->query($sql);
