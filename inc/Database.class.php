<?php
class Database extends Mysqli
{
  function __construct($host, $user, $pass, $name) {
    parent::__construct($host, $user, $pass, $name);
    parent::set_charset('utf8');
  }
}
?>
