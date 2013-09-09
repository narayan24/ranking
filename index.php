<?php
require_once(dirname(__FILE__) . '/inc/header.inc.php');
?>

    <div class="row-fluid">
      <div class="span12">
        <div class="btn-group">
          <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
          Domain wählen
          <span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
<?php
$__domain = '';
if(isset($_GET['d'])) {
  $__domain = $_GET['d'];
}

foreach($ranking->getDomainsByClientId($__client) as $domain) {
  if($__domain == '') {
    $__domain = $domain->domains_id;
  }

  $active = '';  
  if($__domain == $domain->domains_id) {
    $active = ' class="active"';
  }
  echo '<li' . $active . '><a href="?c=' . $__client . '&d=' . $domain->domains_id . '">' . $domain->domain . '</a></li>';
}
?>
          </ul>
        </div>
        
        <div id="container" style="width:100%;height:240px;"></div>
<?php
$days = 14;
$unix_from = time() - (60*60*24*$days);
$unix_to = time();
$diff = ($unix_to - $unix_from) / 60 / 60 / 24;
$from = date('Y-m-d', $unix_from);
$to = date('Y-m-d', $unix_to);
$graph = array();
$keywords = $ranking->getKeywordsByDomainId($__domain);
foreach($keywords as $keyword) {
  $results = $ranking->getResults($keyword->keywords_id, $from, $to);
  for($i=0; $i<$diff; $i++) {
    $timestamp = $unix_from+(($i+1)*60*60*24);
    if(!isset($graph['google'][$timestamp])) {
      $graph['google'][$timestamp] = 0;
      $graph['bing'][$timestamp] = 0;
    }
    $graph['google'][$timestamp]+= (int)str_replace('>', '', $results[$i]->pos_google);
    $graph['bing'][$timestamp]+= (int)str_replace('>', '', $results[$i]->pos_bing);
  }
}

foreach($graph as $se => $data) {
  foreach($data as $key => $dayval) {
    $graph[$se][$key] = $dayval/count($keywords);
  }
}

$graphJSGoogle = '';
$graphJSBing = '';
foreach($graph as $se => $data) {
  $graphJSCategories = '';
  foreach($data as $key => $dayval) {
     switch($se) {
      case 'google':  $graphJSGoogle.= $dayval . ','; break;
      case 'bing':    $graphJSBing.= $dayval . ',';   break;
    }
    $graphJSCategories.= '\'' . date('d.m.Y', $key) . '\',';
  }
}

$graphJSGoogle = ereg_replace(',$', '', $graphJSGoogle);
$graphJSBing = ereg_replace(',$', '', $graphJSBing);
$graphJSCategories = ereg_replace(',$', '', $graphJSCategories);
?>
        <script>
        var chart1; // globally available
        $(document).ready(function() {
          chart1 = new Highcharts.Chart({
            chart: {
               renderTo: 'container',
               type: 'spline'
            },
            title: {
               text: 'Search engine ranking for <?=$ranking->getDomainById($__domain)->domain?>'
            },
            yAxis: {
               title: {
                  text: 'Average position'
               }
            },
            xAxis: {
               categories: [<?=$graphJSCategories?>],
            },
            series: [{
               name: 'Google',
               data: [<?=$graphJSGoogle?>]
            }, {
               name: 'Bing',
               data: [<?=$graphJSBing?>]
            }]
          });
        });
        </script>
        <ul class="nav nav-tabs">
          <li class="active"><a href="#google" data-toggle="tab"><img src="gfx/icon_google.png" />&nbsp;Google</a></li>
          <li><a href="#bing" data-toggle="tab"><img src="gfx/icon_bing.png" />&nbsp;Bing</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="google">
            <table class="table table-condensed table-striped table-hover table-bordered">
<?php
echo '<thead>' . "\n";
echo '<th>Keyword</th>' . "\n";
for($i=$diff; $i>0; $i--) {
  echo '<th>' . date('d.m.', time() - (($i-1)*60*60*24)) . '</th>' . "\n";
}
echo '<thead>' . "\n";
echo '<tbody>' . "\n";

$tmp = array();
$keywords = $ranking->getKeywordsByDomainId($__domain);
foreach($keywords as $keyword) {
  echo '<tr>';
  echo '<td><span class="label label-info">' . $keyword->keyword . '</span></td>';
  $results = $ranking->getResults($keyword->keywords_id, $from, $to);
  for($i=0; $i<$diff; $i++) {
    if(preg_match('/>/', $results[$i]->pos_google)) {
      $class = 'text-error';
    } else if($results[$i]->pos_google < 10) {
      $class = 'text-success';
    } else if($results[$i]->pos_google < 20) {
      $class = 'text-info';
    } else if($results[$i]->pos_google < 100) {
      $class = 'text-warning';
    } else {
      $class = 'muted';
    }
    
    $realVal = (int)str_replace('>', '', $results[$i]->pos_google);
    if(!isset($tmp[$keyword->keywords_id])) {
      $tmp[$keyword->keywords_id] = 0;
    }
    if($tmp[$keyword->keywords_id] == 0) {
      $icon = 'icon-minus';
    } else if($tmp[$keyword->keywords_id] > $realVal) {
      $icon = 'icon-arrow-up';
    } else if($tmp[$keyword->keywords_id] < $realVal) {
      $icon = 'icon-arrow-down';
    } else {
      $icon = 'icon-minus';
    }
    $tmp[$keyword->keywords_id] = $realVal;
    echo '<td><strong class="' . $class . '"><i class="' . $icon . '"></i>&nbsp;' . $results[$i]->pos_google . '</strong></td>' . "\n";
  }
  echo '</tr>';
  echo '</tbody>';
}
?>
            </table>
          </div>
          <div class="tab-pane" id="bing">
            <table class="table table-condensed table-striped table-hover table-bordered">
<?php
echo '<thead>' . "\n";
echo '<th>Keyword</th>' . "\n";
for($i=$diff; $i>0; $i--) {
  echo '<th>' . date('d.m.', time() - (60*60*24*$i+1)) . '</th>' . "\n";
}
echo '<thead>' . "\n";
echo '<tbody>' . "\n";

$tmp = array();
$keywords = $ranking->getKeywordsByDomainId($__domain);
foreach($keywords as $keyword) {
  echo '<tr>';
  echo '<td><span class="label label-info">' . $keyword->keyword . '</span></td>';
  $results = $ranking->getResults($keyword->keywords_id, $from, $to);
  for($i=0; $i<$diff; $i++) {
    if(preg_match('/>/', $results[$i]->pos_bing)) {
      $class = 'text-error';
    } else if($results[$i]->pos_bing < 10) {
      $class = 'text-success';
    } else if($results[$i]->pos_bing < 20) {
      $class = 'text-info';
    } else if($results[$i]->pos_bing < 100) {
      $class = 'text-warning';
    } else {
      $class = 'muted';
    }

    $realVal = (int)str_replace('>', '', $results[$i]->pos_bing);
    if(!isset($tmp[$keyword->keywords_id])) {
      $tmp[$keyword->keywords_id] = 0;
    }
    if($tmp[$keyword->keywords_id] == 0) {
      $icon = 'icon-minus';
    } else if($tmp[$keyword->keywords_id] > $realVal) {
      $icon = 'icon-arrow-up';
    } else if($tmp[$keyword->keywords_id] < $realVal) {
      $icon = 'icon-arrow-down';
    } else {
      $icon = 'icon-minus';
    }
    $tmp[$keyword->keywords_id] = $realVal;
    echo '<td><strong class="' . $class . '"><i class="' . $icon . '"></i>&nbsp;' . $results[$i]->pos_bing . '</span></td>' . "\n";
  }
  echo '</tr>';
  echo '</tbody>';
}
?>  
            </table>
          </div>
        </div>
      </div>
    </div>


<?php
require_once(dirname(__FILE__) . '/inc/footer.inc.php');
?>
