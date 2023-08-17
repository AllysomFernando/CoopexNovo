  <?php

  define('SERVER', '10.0.0.33');
  define('DBNAME', 'coopex_usuario');
  define('USER', 'fernando');
  define('PASSWORD', 'jklp13SA');

  global $coopex;
  // Configura uma conexão com o banco de dados
  $opcoes = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8');
  $conexao = new PDO("mysql:host=" . SERVER . "; dbname=" . DBNAME, USER, PASSWORD, $opcoes);
  ?>