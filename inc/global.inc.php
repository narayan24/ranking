<?php
require_once(dirname(__FILE__) . '/../config.inc.php');

define('BASE', realpath(dirname(__FILE__) . '/../'));

define('HTMLBASE', str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', BASE));

// Autoloader
function __autoload($class_name) {
  require_once(BASE . '/inc/' . $class_name . '.class.php');
}

$res = new Results(
  $database_config['host'],
  $database_config['user'],
  $database_config['pass'],
  $database_config['name']
);

$ranking = new Ranking();
?>