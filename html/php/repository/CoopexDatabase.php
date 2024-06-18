<?php

require_once 'AbstractDatabaseClient.php';

class CoopexDatabase extends AbstractDatabaseClient
{

  public $table;

  public function __construct()
  {
    parent::__construct('mysql', 'coopex', 'fernando', 'indioveio', '10.0.0.41');
  }
}
