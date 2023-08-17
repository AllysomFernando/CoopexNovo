  <?php

  define('SERVER', '10.0.0.41');
  define('DBNAME', 'coopex_reoferta');
  define('USER', 'fernando');
  define('PASSWORD', 'indioveio');

  // Configura uma conexão com o banco de dados
  $opcoes = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8');
  $coopex = new PDO("mysql:host=" . SERVER . "; dbname=" . DBNAME, USER, PASSWORD, $opcoes);
  ?>