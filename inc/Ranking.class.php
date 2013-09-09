<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ranking
 *
 * @author alexander.wuest
 */
class Ranking {
  private $db;
  
  function __construct() {
    include(dirname(__FILE__) . '/../config.inc.php');
    $this->db = new Database(
      $database_config['host'],
      $database_config['user'],
      $database_config['pass'],
      $database_config['name']
    );
  }
  
  function getClients() {
    $query = 'SELECT * FROM clients';
    $result = $this->db->query($query);
    
    $clients = array();
    while($client = $result->fetch_object()) {
      array_push($clients, $client);
    }
    
    return $clients;
  }
  
  function getClientNameById($id) {
    $query = 'SELECT name FROM clients WHERE clients_id = "' . $id . '"';
    $result = $this->db->query($query);
    
    $client = $result->fetch_object();
    
    return $client->name;
  }
  
  function getDomainById($id) {
    $query = 'SELECT * FROM domains WHERE domains_id = "' . $id . '"';
    $result = $this->db->query($query);
    
    $domain = $result->fetch_object();
    
    return $domain;
  }
  
  function getDomainsByClientId($id) {
    $query = 'SELECT * FROM clients_has_domains chd LEFT JOIN domains d ON d.domains_id = chd.domains_id WHERE chd.clients_id = "' . $id . '"';
    $result = $this->db->query($query);
    
    $domains = array();
    while($domain = $result->fetch_object()) {
      array_push($domains, $domain);
    }
    
    return $domains;
  }
  
  function getKeywordsByDomainId($id) {
    $query = 'SELECT * FROM domains_has_keywords dhk LEFT JOIN keywords k ON k.keywords_id = dhk.keywords_id WHERE dhk.domains_id = "' . $id . '" ORDER BY prio DESC';
    $result = $this->db->query($query);
    
    $keywords = array();
    while($keyword = $result->fetch_object()) {
      array_push($keywords, $keyword);
    }
    
    return $keywords;
  }
  
  function getResults($id, $from, $to) {
    $query = 'SELECT * FROM results WHERE date <= "' . $to . '" AND date >= "' . $from . '" AND keywords_id = "' . $id . '"';
    $result = $this->db->query($query);
    
    $results = array();
    while($res = $result->fetch_object()) {
      array_push($results, $res);
    }
    
    return $results;
  }
  
  function getLatestPosition($id) {
    $query = 'SELECT * FROM results WHERE keywords_id = "' . $id . '" ORDER BY date DESC';
    $result = $this->db->query($query);
    
    $res = $result->fetch_object();
    return $res;
  }
}

?>
