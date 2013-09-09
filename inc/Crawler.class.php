<?php
class Crawler
{
  private $cookie = '/tmp/cookie.txt';

//  function __construct() {
//    unlink($this->cookie);
//  }
  private function fetchRandomProxy($aExclude = array()) {
    $proxyList[6]['ip'] = "109.169.40.229";
    $proxyList[6]['port'] = 80;
    $proxyList[7]['ip'] = "95.222.24.234";
    $proxyList[7]['port'] = 80;
    $proxyList[8]['ip'] = "88.250.173.22";
    $proxyList[8]['port'] = 80;

    $proxyNr = array_rand($proxyList);

    $proxy = array(
      'ip' => $proxyList[$proxyNr]['ip'],
      'port' => $proxyList[$proxyNr]['port'],
    );

    return $proxy;
  }

  private function getContents($url, $ref) {

    $proxy = $this->fetchRandomProxy();

    if(file_exists($this->cookie)) {
      unlink($this->cookie);
    }

    $hCh = curl_init();

    curl_setopt($hCh, CURLOPT_URL, $url);
    curl_setopt($hCh, CURLOPT_RETURNTRANSFER, true);
    if($ref != '') {
      curl_setopt($hCh, CURLOPT_REFERER, $ref);
    }
    curl_setopt($hCh, CURLOPT_RETURNTRANSFER, TRUE);
    //curl_setopt($hCh, CURLOPT_PROXYPORT, $proxy['port']);
    //curl_setopt($hCh, CURLOPT_PROXY, $proxy['ip']);
    //curl_setopt($hCh, CURLOPT_PROXYTYPE, 'HTTP');
    curl_setopt($hCh, CURLOPT_COOKIEJAR, $this->cookie);
    curl_setopt($hCh, CURLOPT_COOKIEFILE, $this->cookie);

    $headers = array(
      'Referer: ' . $ref,
      'Accept  text/html,application/xhtml+xml,application/xml;q=0.9,*' . '/*;q=0.8',
      'Accept-Encoding gzip, deflate',
      'Accept-Language de-de',
      'Connection  keep-alive',
      'Cookie  GDSESS=ID=f429ade12c78d3f2:TM=1358159329:C=c:IP=2a03:4000:1::-:S=ADSvE-cU-vLkXujoOhRjTnbiHk6OAtUejQ; PREF=ID=8c5de2762ce1e912:U=639bf2b6a7fc2269:FF=0:TM=1358159339:LM=1358159620:S=2boSrt_0dWBc1rcs; NID=67=nGIigpq0lnWLyuYfth3R7e9YifankTWzcK13j50MQtNfuAppd0u2mkQdZQrXkinygazm1jfKixcrV3vOzbXSwmHFaBFgXRj2EEn_iOPQsLFwa0RKxA_7kecCR6fdGcPi',
      'Host  www.google.de',
      'User-Agent  Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20130819 Firefox/17.0 FirePHP/0.7.1',
      'x-insight activate',
    ); 

    // fetch HTTP answer
    $sContent = curl_exec($hCh);

    return $sContent;
  }

  public function getGooglePosition($key, $domain, $p2c, $min_sleep, $max_sleep, $delay) {
    $found = false;

    $hitcount = 0;

    for($i=0; $i<$p2c; $i++) {

      $start = 100 * $i;

      $url = 'https://www.google.de/search?hl=de&as_q=' . urlencode(trim($key)) . '&as_epq=&as_oq=&as_eq=&as_nlo=&as_nhi=&lr=&cr=&as_qdr=all&as_sitesearch=&as_occt=any&safe=off&as_filetype=&as_rights=&start=' . $start . '&num=100';
      //$result = file_get_contents($url);
      $result = $this->getContents($url, 'http://www.google.de');

      $hit = preg_split('/class="r"><a /', $result, -1, PREG_SPLIT_OFFSET_CAPTURE);

      foreach($hit as $entry){
        $hitcount++;
        preg_match("/href=\"\/url\?q=http:\/\/*([^\/]+)/", $entry[0], $t, PREG_OFFSET_CAPTURE);
        if($hitcount > 1){
          $position = $hitcount-1;
          if(isset($t[1]) && $domain == $t[1][0]) {
            $found = $position;
            break;
          }
        }
      }

      // no need to crawl on if we found already what we were searching for
      if($found !== false) {
        break;
      }

      // sleeping to prevent fraud detection from search engines
      if($delay) {
        sleep(rand($min_sleep, $max_sleep));
      }
    }

    return $found;
  }

  public function getBingPosition($key, $domain, $p2c, $min_sleep, $max_sleep, $delay) {
    $found = false;

    $hitcount = 0;

    for($i=0; $i<($p2c * 2); $i++) {

      $start = (50 * $i) + 1;

      $url = 'http://www.bing.com/search?q=' . urlencode(trim($key)) . '&firts=0&count=50&first=' . $start;

      //$result = file_get_contents($url);
      $result = $this->getContents($url, 'http://www.bing.com/');

      $hit = preg_split('/<li class="sa_wr">/', $result, -1, PREG_SPLIT_OFFSET_CAPTURE);

      foreach($hit as $entry){
        $hitcount++;
        preg_match("/href=\"http:\/\/([^\/]+)/", $entry[0], $t, PREG_OFFSET_CAPTURE);
        if($hitcount > 1){
          $position = $hitcount-1;
          if(isset($t[1]) && $domain == $t[1][0]) {
            $found = $position;
            break;
          }
        }
      }

      // no need to crawl on if we found already what we were searching for
      if($found !== false) {
        break;
      }

      // sleeping to prevent fraud detection from search engines
      if($delay) {
        sleep(rand($min_sleep, $max_sleep));
      }
    }

    return $found;
  }
}
?>
