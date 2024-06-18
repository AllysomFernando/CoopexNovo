<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../../../../php/mysql.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pessoa = $_POST['id_pessoa'];
    $id_menu_permissao = $_POST['id_menu_permissao'];
    try {
        $sql = "INSERT INTO menu_permissao_usuario (id_pessoa, id_menu_permissao) VALUES (:id_pessoa, :id_menu_permissao)";
        $stmt = $coopex->prepare($sql);
        $stmt->bindParam(':id_pessoa', $id_pessoa);
        $stmt->bindParam(':id_menu_permissao', $id_menu_permissao);
        $stmt->execute();
        echo $stmt->rowCount();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}


