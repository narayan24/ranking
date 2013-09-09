<?php
require(dirname(__FILE__) . '/../inc/global.inc.php');

$delay = true;
$domain = $keyword = '';
if(isset($_SERVER['argv'][1])) {
  foreach($_SERVER['argv'] as $param) {
    if(ereg('=', $param)) {
      $pars = split('=', $param);
      $pars[0] = trim($pars[0]);
      $pars[1] = trim($pars[1]);
      switch($pars[0]) {
        case 'domain':
          $domain = $pars[1];
          $delay = false;
        break;

        case 'keyword':
          $keyword = $pars[1];
          $delay = false;
        break;

        default:

          echo 'Error! Unknown parameter given! Exiting!

Usage: php ./crawl.php [param=[param_id]

Possible parameters are:
  domain=[domains_id]
  keyword=[keywords_id]
';
          exit;
        break;
      }
    }
  }
}

$crawler = new Crawler();

// fetch all domains, keywords, settings from database
$query = 'SELECT * FROM domains d LEFT JOIN domains_has_keywords dhk ON dhk.domains_id = d.domains_id LEFT JOIN keywords k ON dhk.keywords_id = k.keywords_id';
if($keyword != '') {
  $query.= ' WHERE k.keywords_id = ' . $keyword;
}
if($domain != '') {
  $query.= ' WHERE d.domains_id = ' . $domain;
}
$result = $res->query($query);

$domains = array();
while($obj =$result->fetch_object()) {
  $config[$obj->domain]['pages_to_crawl'] = $obj->pages_to_crawl;
  $config[$obj->domain]['domain_min_sleep'] = $obj->domain_min_sleep;
  $config[$obj->domain]['domain_max_sleep'] = $obj->domain_max_sleep;
  $config[$obj->domain]['keyword_min_sleep'] = $obj->keyword_min_sleep;
  $config[$obj->domain]['keyword_max_sleep'] = $obj->keyword_max_sleep;
  $domains[$obj->domain][$obj->keywords_id] = $obj->keyword;
}

// run through domains array and fetch results from SERPs
foreach($domains as $domain => $keywords) {

  // to prevent the search engines from activating fraud detection -> sleep a while
  if($delay) {
    sleep(rand($config[$domain]['domain_min_sleep'],$config[$domain]['domain_max_sleep']));
  }

  if($crawler_settings['output'] === true) {
    echo '-------------------' . "\n" . 'Domain: ' . utf8_encode($domain) . "\n";
  }

  $p2c = $config[$domain]['pages_to_crawl'];

  foreach($keywords as $id => $key) {
    $hitcount = 0;        // $hitcount describes the current position in the SERP

    // GOOGLE
    $found_g = $crawler->getGooglePosition($key,
                                           $domain,
                                           $p2c,
                                           $config[$domain]['keyword_min_sleep'],
                                           $config[$domain]['keyword_max_sleep'],
                                           $delay);

    $found_b = $crawler->getBingPosition($key,
                                         $domain,
                                         $p2c,
                                         $config[$domain]['keyword_min_sleep'],
                                         $config[$domain]['keyword_max_sleep'],
                                         $delay);
    if($found_b === false) {
      $found_b = '>' . (100*$p2c);
    }
    if($found_g === false) {
      $found_g = '>' . (100*$p2c);
    }

    // save result in database
    $query = 'INSERT INTO results (date, keywords_id, pos_google, pos_bing) VALUES (NOW(), ' . $id . ', "' . $found_g . '", "' . $found_b . '")';

    $res->query($query);

    // output for sending mail when running as cronjob or on system shell
    if($crawler_settings['output'] === true) {
      if($found_g === false) {
        echo 'Did not find keyword ' . utf8_encode($key) . ' in Google in the first ' . (100 * $p2c) . ' positions' . "\n";
      } else {
        echo 'Found keyword ' . utf8_encode($key) . ' in Google for website ' . utf8_encode($domain) . ' on result position ' . $found_g . "\n";
      }

      if($found_b === false) {
        echo 'Did not find keyword ' . utf8_encode($key) . ' in Bing in the first ' . (100 * $p2c) . ' positions' . "\n";
      } else {
        echo 'Found keyword ' . utf8_encode($key) . ' in Bing for website ' . utf8_encode($domain) . ' on result position ' . $found_b . "\n";
      }
    }

    // sleep between SERP calls to prevent search engines from activating fraud detection
    if($delay) {
      sleep(rand($config[$domain]['keyword_min_sleep'],$config[$domain]['keyword_max_sleep']));
    }
  }
}
?>
