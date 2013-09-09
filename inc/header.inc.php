<?php
require(dirname(__FILE__) . '/global.inc.php');
$clients = $ranking->getClients();
$__client = $clients[0]->clients_id;
if(isset($_GET['c'])) {
  $__client = $_GET['c'];
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>ranking analysis tool</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="<?=HTMLBASE?>/inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="<?=HTMLBASE?>/inc/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="shortcut icon" href="<?=HTMLBASE?>/gfx/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=HTMLBASE?>/gfx/mobile-icon_144.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?=HTMLBASE?>/gfx/mobile-icon_114.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?=HTMLBASE?>/gfx/mobile-icon_72.png">
    <link rel="apple-touch-icon-precomposed" href="<?=HTMLBASE?>/gfx/mobile-icon.png" />
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="<?=HTMLBASE?>/inc/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=HTMLBASE?>/js/highcharts/highcharts.js" type="text/javascript"></script>
<!--    <script src="<?=HTMLBASE?>/js/highcharts_init.js" type="text/javascript"></script>-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?=HTMLBASE?>">ranking analysis tool</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
               <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <?=$ranking->getClientNameById($__client)?>
                  <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
<?php
foreach($ranking->getClients() as $client) {
  echo '<li><a href="?c=' . $client->clients_id . '">' . $client->name . '</a></li>' . "\n";
}
?>
                </ul>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    
    <div class="container">