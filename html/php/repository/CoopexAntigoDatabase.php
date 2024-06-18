<?php

require_once 'AbstractDatabaseClient.php';

class CoopexAntigoDatabase extends AbstractDatabaseClient {

  public function __construct() {
    parent::__construct('mysql', 'coopex_usuario', 'fernando', 'jklp13SA', '10.0.0.33');
  }

}