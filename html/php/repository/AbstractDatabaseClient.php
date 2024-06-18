<?php

abstract class AbstractDatabaseClient
{
  public $client;
  public $database;
  public $user;
  public $password;
  public $host;
  public $driver;

  public function __construct($driver, $database, $user, $password, $host)
  {
    $this->database = $database;
    $this->user = $user;
    $this->password = $password;
    $this->host = $host;
    $this->driver = $driver;
  }

  public function connect() {
    $pdo = $this->makePDOInstance();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $this->client = $pdo;
  }

  private function makePDOInstance() {
    if ($this->driver == 'mysql') {
      $pdo = new PDO("mysql:dbname={$this->database};host={$this->host}", $this->user, $this->password);
      $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
      $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER SET utf8");

      return $pdo;
    }

    if ($this->driver == 'sqlserver') {
      $pdo = new PDO("dblib:host={$this->host};dbname={$this->database}", $this->user, $this->password);
      // $pdo->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);

      return $pdo;
    }
  }

  public function disconnect() {
    $this->client = null;
  }
  
}
