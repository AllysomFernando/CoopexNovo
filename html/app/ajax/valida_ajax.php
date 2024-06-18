<?php
include $_SERVER['DOCUMENT_ROOT'] ."/app/inc/config.php";

if(!isset($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'], "$url")){
    echo "Acesso não autorizado!";
    exit;
}
