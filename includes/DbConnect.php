<?php

class DbConnect
{

  private $connstring = '(DESCRIPTION =
      (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = 172.25.1.172)(PORT = 1521))
      )
      (CONNECT_DATA = (SID=clty))
  )';
  private $user = 'incenprg';
  private $pass = 'incen#prg12';

  public function connect()
  {
    $conn = new PDO("oci:dbname=" . $this->connstring, $this->user, $this->pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $conn;
  }
}
