<?php 
	require_once("php/config.php");
	unset($_SESSION['coopex']);
	header("Location: $_url");
?>	