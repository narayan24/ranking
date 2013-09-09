<?php
class Results extends Database
{
  public function getClients() {
    $query = 'SELECT * FROM clients';
    $result = $this->query($query);

    $clients = array();
    while($obj = $result->fetch_object()) {
      $clients[$obj->clients_id] = $obj->name;
    }

    return $clients;
  }

  public function addKeyword($keyword, $prio, $domain) {
    global $basedir;

    $query = 'INSERT INTO keywords (keyword, prio) VALUES ("' . $keyword . '", ' . $prio . ')';
    $result = $this->query($query);
    
    if($result) {
      $keywords_id = $this->insert_id;
      $query = 'INSERT INTO ' .
                  'domains_has_keywords (' .
                    'domains_id, ' .
                    'keywords_id' .
                  ') VALUES (' .
                    $domain . ', ' .
                    $this->insert_id .
                  ')';
      $result2 = $this->query($query);
      if(!$result2) {
        return false;
      }
    } else {
      return false;
    }

    $call = 'php ' . $basedir . '/bin/crawl.php keyword=' . $keywords_id;
    exec($call);
    return true;
  }

  public function delKeyword($keywords_id) {
    $query = 'DELETE FROM keywords WHERE keywords_id = ' . $keywords_id;
    $result = $this->query($query);

    if($result) {
      $query = 'DELETE FROM results WHERE keywords_id = ' . $keywords_id;
      $result2 = $this->query($query);
      if(!$result2) {
        return false;
      }
    } else {
      return false;
    }

    return true;
  }

  public function fetchDomainData($client) {
    $query = 'SELECT d.domains_id AS did, c.*, d.*, k.* FROM clients c LEFT JOIN clients_has_domains chd ON c.clients_id = chd.clients_id LEFT JOIN domains d ON d.domains_id = chd.domains_id LEFT JOIN domains_has_keywords dhk ON dhk.domains_id = d.domains_id LEFT JOIN keywords k ON k.keywords_id = dhk.keywords_id WHERE c.clients_id = ' . (string)$client . ' ORDER BY d.domains_id ASC, k.prio DESC';
    $result = $this->query($query);

    $data = array();
    $client = array();
    while($obj = $result->fetch_object()) {
      $client_name = $obj->name;
      $data[$obj->domain]['keywords'][$obj->keywords_id] = $obj->keyword;
      $data[$obj->domain]['prios'][$obj->keywords_id] = $obj->prio;
      $data[$obj->domain]['id'] = $obj->did;
      $data[$obj->domain]['domain_min_sleep'] = $obj->domain_min_sleep;
      $data[$obj->domain]['domain_max_sleep'] = $obj->domain_max_sleep;
      $data[$obj->domain]['keyword_min_sleep'] = $obj->keyword_min_sleep;
      $data[$obj->domain]['keyword_max_sleep'] = $obj->keyword_max_sleep;
      $data[$obj->domain]['pages_to_crawl'] = $obj->pages_to_crawl;
    }

    return $data;
  }

  public function updateConfig($did, $p2c = NULL, $dmaxs = NULL, $dmins = NULL, $kmaxs = NULL, $kmins = NULL) {
    $query = 'UPDATE domains SET ';

    $go = false;
    if($p2c != NULL) {
      $query.= 'pages_to_crawl = ' . $p2c . ' ';
    }

    //TODO: update further settings in db like dmaxs, dmins, etc.

    $query.= ' WHERE domains_id = ' . $did;
    $result = $this->query($query);

    if(!$result) {
      return false;
    }

    return true;
  }
}
?>
