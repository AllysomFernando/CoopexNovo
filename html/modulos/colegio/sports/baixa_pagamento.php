<pre>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../../php/config.php");
require_once("../../../php/mysql.php");
require_once("../../../php/utils.php");

$sql = "UPDATE `colegio`.`matricula_boleto` SET `pago` = 1 WHERE `id_pessoa` = 5000234465 LIMIT 1";
$res = $coopex->query($sql);