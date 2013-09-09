<?php
require(dirname(__FILE__) . '/../inc/header.inc.php');
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
      </div>
    </div>

<?php
// POST var action decides what to do
if(isset($_POST['action'])) {
  switch($_POST['action']) {
    case 'add':
      $res->addKeyword($_POST['keyword'], $_POST['prio'], $_POST['domain']);
    break;

    case 'del':
      $res->delKeyword($_POST['keywords_id']);
    break;

    case 'conf':
      $res->updateConfig($_POST['domains_id'], $_POST['pages']);
    break;
  }

}

// fetch all domains from database
$data = $res->fetchDomainData($__client);

foreach($data as $domain => $domain_data) {
  if($domain_data['id'] == $__domain) {
    echo '<h1>' . $domain . '</h1>
    <p>
      <form method="post">
      <input type="hidden" name="action" value="conf" />
      <input type="hidden" name="client" value="' . $__client . '" />
      <input type="hidden" name="domains_id" value="' . $domain_data['id'] . '" />
      Pages (1 page = 100 result positions): <input type="text" name="pages" value="' . $domain_data['pages_to_crawl'] . '" size="3" />
      <input type="submit" class="btn" value="save config" />
      </form>
    </p>
    
    <table class="table table-striped table-condensed">
    <thead>
      <tr>
        <th>Keyword</th>
        <th>Priority</th>
        <th>Latest positions</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    ' . "\n";

    // fetch all keywords per domain
    foreach($domain_data['keywords'] as $kid => $keyword) {
      $latest = $ranking->getLatestPosition($kid);
      echo '
      <tr>
        <td>' . $keyword . '</td>
        <td>' . $domain_data['prios'][$kid] . '</td>
        <td><img src="../gfx/icon_google.png" /> <strong>' . $latest->pos_google . '</strong> / <img src="../gfx/icon_bing.png" /> <strong>' . $latest->pos_bing . '</strong></td>
        <td>
          <form method="post">
          <input type="hidden" name="action" value="del" />
          <input type="hidden" name="client" value="' . $__client . '" />
          <input type="hidden" name="keywords_id" value="' . $kid . '" />
          <input type="submit" class="btn" value="delete keyword" />
          </form>
        </td>
      </tr>' . "\n";
    }

    // functionality to add new keywords to domain
    echo '
    </tbody>
    </table>
    <form method="post">
    <input type="hidden" name="action" value="add" />
    <input type="hidden" name="client" value="' . $__client . '" />
    <input type="hidden" name="domain" value="' . $domain_data['id'] . '" />
    New keyword: <input type="text" name="keyword" value="" />
    Prio: <input type="text" size="4" name="prio" value="0" />
    <input type="submit" value="save" />
  </form>';
  }
}

require_once(dirname(__FILE__) . '/../inc/footer.inc.php');
?>
