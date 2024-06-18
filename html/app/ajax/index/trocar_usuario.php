<?php

include $_SERVER['DOCUMENT_ROOT'] . "/app/ajax/valida_ajax.php";


$_SESSION['app']['id_pessoa'] = $_GET['id_pessoa'];
$_SESSION['app']['pessoa_ativa'] = $_GET['pessoa_ativa'];