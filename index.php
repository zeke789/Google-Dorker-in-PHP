 <?php 
header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time', 9192900);
define("BEGIN_DEBUG", "================ BEGIN_DEBUG ================".PHP_EOL);
define("END_DEBUG", PHP_EOL.PHP_EOL."======================= END_DEBUG =========================".PHP_EOL.PHP_EOL.PHP_EOL);

class Curl{
  private $ch;
  private $error;
  private $info;
  private $cookieFile;

  public function __construct($file_cook)
  {
    $this->cookieFile = $file_cook;
    $this->ch = curl_init();
    curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($this->ch,CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($this->ch,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($this->ch,CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($this->ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($this->ch, CURLOPT_HEADER, true);
  }

  function cookies($file_cook)
  {
    $this->cookieFIle = $file_cook;
    curl_setopt($this->ch,CURLOPT_COOKIEJAR,realpath($file_cook));
    curl_setopt($this->ch,CURLOPT_COOKIEFILE,realpath($file_cook));
  }
  
  function referer($ref){ curl_setopt($this->ch,CURLOPT_REFERER,$ref); }
  
  function httpcode(){ return curl_getinfo($this->ch,CURLINFO_HTTP_CODE); }
  
  function setUserAgent($agent){ curl_setopt($this->ch, CURLOPT_USERAGENT,$agent); }
  
  function httpHeader($headers){ curl_setopt($this->ch,CURLOPT_HTTPHEADER,$headers); }
  
  function closee(){ curl_close($this->ch);file_put_contents($this->cookieFile, ''); }

  function proxy($proxy,$type,$auth=null)
  { 
    if ($proxy != 'null') {
      $parts = explode(':', $proxy);
      $ip = $parts[0];  $port = $parts[1];
      curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, 1);
      curl_setopt($this->ch, CURLOPT_PROXY, $ip);
      curl_setopt($this->ch, CURLOPT_PROXYPORT, $port);
      switch ($type) {
        case 'socks4':
          curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4); break;
        case 'socks5':
          curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5); break;
        case 'http':
          curl_setopt($this->ch, CURLOPT_PROXYTYPE, 'HTTP'); break;
        default: break;
      }
      if ($auth == true) {
        $loginpassw  = $parts[2] . ":" . $parts[3];
        curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $loginpassw);
      }
    }
  } 
  function makeRequest($url,$post_data=false)
  {
    curl_setopt($this->ch,CURLOPT_URL,$url);
    curl_setopt($this->ch, CURLOPT_ENCODING, "UTF-8");
    if($post_data != false){
      curl_setopt($this->ch,CURLOPT_POST,1);
      curl_setopt($this->ch,CURLOPT_POSTFIELDS,$post_data);
    }else{
      curl_setopt($this->ch,CURLOPT_POST,0);
    }
    $data= curl_exec($this->ch);
    $this->error = curl_error ($this->ch);
    $this->info = curl_getinfo ($this->ch);
    return utf8_encode($data);
  }
}//end Curl class


function get_agent($z){ return explode(PHP_EOL,file_get_contents('useragents.txt'))[$z]; }

function getStr($cadena,$inicio,$final){
  $str= explode($inicio,$cadena);
  if (isset($str[1]) && !empty($str[1])) {
    $str= explode($final,$str[1]);
    return $str[0];
  }else{
    return 'errr';
  }
}

function getHeaders($agent){
  return 'Host: www.google.com
User-Agent: '.$agent.'
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3
Referer: https://www.google.com/
DNT: 1
Connection: keep-alive';
}

function makeProxyArray($p){
  foreach ($p as $proxy) { $multiplied[] = $p; } 
  return $multiplied[0];
}
function multiplyProxys($p){
  $count = count($p); $multiplied=[]; 
  switch (true) {
    case $count < 60:
      for ($i=0; $i < 500; $i++) { $arr2 = makeProxyArray($p); $multiplied = array_merge($multiplied,$arr2); }
      break;
    case $count < 110:
      for ($i=0; $i < 400; $i++) { $arr2 = makeProxyArray($p); $multiplied = array_merge($multiplied,$arr2);  }
      break;
    case $count > 109 && $count < 500:
      for ($i=0; $i < 320; $i++) { $arr2 = makeProxyArray($p); $multiplied = array_merge($multiplied,$arr2);  }
      break;
    case $count > 499 && $count < 1000:
      for ($i=0; $i < 120; $i++) { $arr2 = makeProxyArray($p); $multiplied = array_merge($multiplied,$arr2);  }
      break;
    case $count > 999 && $count < 3000:
      for ($i=0; $i < 40; $i++) { $arr2 = makeProxyArray($p); $multiplied = array_merge($multiplied,$arr2);  }
      break;
    case $count > 999 && $count < 3000:
      for ($i=0; $i < 10; $i++) { $arr2 = makeProxyArray($p); $multiplied = array_merge($multiplied,$arr2);  }
      break;
    default:
      for ($i=0; $i < 5; $i++) { $arr2 = makeProxyArray($p); $multiplied = array_merge($multiplied,$arr2);  }
      break;
  }
  return $multiplied;
}

function delayRequest($num){
  switch ($num) {
    case 1:
      sleep(rand(1,2));sleep(rand(1,2));
      break;
    case 2:
      sleep(rand(2,3));sleep(rand(1,3));sleep(rand(1,2));
      break;
    case 3:
      sleep(rand(2,4));sleep(rand(2,3));sleep(rand(2,3));sleep(rand(2,3));
      break;
    case 4:
      sleep(rand(2,5));sleep(rand(2,5));sleep(rand(2,5));sleep(rand(2,4));
      break;
    case 5:
      sleep(rand(2,5));sleep(rand(3,7));sleep(rand(3,5));sleep(rand(2,5));
      break;
    default:
      break;
  }
}

function saveErr1($htpcode,$i,$e,$proxy,$data){
  $err= BEGIN_DEBUG . $htpcode . PHP_EOL .'iteration_num_ix: '.$i.PHP_EOL.$proxy.PHP_EOL. " ===-- DATA => " .$data .PHP_EOL.END_DEBUG;
  file_put_contents('err/forbidden_or_null.txt',$err,FILE_APPEND);
}

function saveErrNoResult($htpcode,$i,$e,$data){
  $err=BEGIN_DEBUG.'http_code ' . $htpcode . PHP_EOL .'iteration_num_i '.$i.PHP_EOL.'proxy_num_e '. $e .PHP_EOL.$data.END_DEBUG;
  file_put_contents('err/no_results.txt', $err,FILE_APPEND);
}

function saveNextErr($httpcode,$i,$ix,$e,$data){
  $err= BEGIN_DEBUG.'http_code='. $httpcode . PHP_EOL .'iteration_num_i='.$i.PHP_EOL. 'iteration_num_ix='.$ix.PHP_EOL.'proxy_num_ex='. $e .PHP_EOL.$data.END_DEBUG;
  file_put_contents('err/next_error.txt',$err,FILE_APPEND);
}

function getUrls($data){ 
  preg_match_all('(class="kCrYT"><a href="(.*)">)siU', $data, $m1);
  if (count($m1[1]) === 0) { preg_match_all('(ZWRArf" href="(.*)">)siU', $data, $m1); }
  if (count($m1[1]) === 0) { preg_match_all('(<div class="r"><a href="(.*)" onmousedown)siU', $data, $m1); }
  //if (count($m1[1]) === 0) { preg_match_all('(another_pattern)siU', $data, $m1); }
  return $m1;
}

function save_urls($urls,$blackDomains){ 
  foreach ($urls[1] as $key => $value) { 
      $cleanUrl1 = getStr('xqpszbvcb-'.$value, 'xqpszbvcb-','&amp;sa=');   
      $cleanUrl2 = str_replace('/url?q=', '', $cleanUrl1);
      if (preg_match('/=/', urldecode($cleanUrl2))) { //if parameter found..
        $urlParsed = parse_url($cleanUrl2);
        $host = isset($urlParsed['host']) ? $urlParsed['host'] : ''; 
        preg_match('/(.*?)((\.co)?.[a-z]{2,4})$/i', $host, $m);
        $ext = isset($m[2]) ? $m[2]: '';
        $urlFirsts = substr(urldecode($cleanUrl2), 0,40);
        $searchStringMatch = strpos(file_get_contents('results.html'), $urlFirsts);
        if (preg_match('/tbs=qdr/', $cleanUrl2) || preg_match('/tbs=qdr/',  urldecode($cleanUrl2))) continue;
        if ( ($searchStringMatch == false || $searchStringMatch == 0) && !in_array($ext, $blackDomains) ) {
           file_put_contents('results.html', urldecode($cleanUrl2) .'<br>' . PHP_EOL, FILE_APPEND);
        }else{
          file_put_contents('results_others.html', urldecode($cleanUrl2) .'<br>' . PHP_EOL, FILE_APPEND);
        }   
      }  
  }
}

function getNextPage($data){
  $next1 = getStr($data,'<a class="pn" href="','" id="pnnex');
  if ($next1 != 'errr') return 'https://www.google.com' . $next1;
  
  $next2 = getStr($data,'<a class="fl" href="','"');  #preg_match_all('((.*))siU', $data, $next3);
  if ($next2 != 'errr') return   'https://www.google.com' .$next2; #.substr($next2, 0, strlen($next2) - 2);
  
  $next3 = getStr($data,'<a class="nBDE1b G5eFlf" href="','"');
  if ($next3 != 'errr') return 'https://www.google.com' . $next3;
  
  $next4 = getStr($data,'<a class="frGj1b" href="','">');
  if ($next4 != 'errr') return 'https://www.google.com' . $next4;
  
  return 'errr';
}

function haveResults($data){
    $errorTxts = [
      'did not match any documents','No se han encontrado resultados para','no obtuvo ningÃºn resultado','no obtuvo ningún resultado'
    ];
    foreach ($errorTxts as $errTxt) {
      if (preg_match('/$errTxt/', $data)) return false;
    }
    return true;
}

function saveEndTest($data,$proxy,$num){
  file_put_contents('err/end_test.txt', BEGIN_DEBUG.'num: '.$num .PHP_EOL . 'proxy: '.$proxy .PHP_EOL. $data . PHP_EOL. END_DEBUG,FILE_APPEND);
}

$pref = [ '%20-site%3Aorg%20-site%3Aedu%20-site%3Ajp%20inurl%3Ahttps', '%20inurl%3Ahttps' ]; 

    
      /******  ¡¡ REQUIRED PARAMETERS !!  ******/ 

$proxyFile = "./proxys.txt";
$proxyType = "http";  // socks4,socks5,http
$proxyAuth = true;  // boolean
$cookieFile = "./cook.txt";
$dorkFile = "./dorks.txt";
$blackDomains = ['.cn','.kr','.or.kr','.jp','.ru','.br','.tw','.de','.th','.nl','.fr','.cy','.com.cn','.co.cn','.co.kr','.com.kr','.co.jp','.co.tw'];
$pages = 30;  // number of pages to scrape per dork
$delay = 1; // delay requests, values 0 - 5 ( If you have few proxies leave it at 4  ) 


      /****** BEGIN   ******/ 
$e=0;
$dorks = explode(PHP_EOL, file_get_contents($dorkFile));
$proxys = multiplyProxys(explode(PHP_EOL, file_get_contents($proxyFile)));
for ($i=1; $i < count($dorks); $i++) {  // For each dork
  $pag=1;
  $sstr = $dorks[$i]; $search = str_replace(' ', '+', $sstr);
  $curl = new Curl($cookieFile);
  $ag = get_agent(rand(0,23));
  $curl->httpHeader(explode(PHP_EOL, getHeaders($ag)));
  $curl->setUserAgent($ag);
  $curl->cookies($cookieFile);
  $curl->proxy($proxys[$e],$proxyType,$proxyAuth); 
  $data = $curl->makeRequest('https://www.google.com/search?q='.$search.'&source=lnt&tbs=qdr:m&sa=X');
  $htpcode = $curl->httpcode();
  $curl->cookies('');$curl->closee();
  if ($htpcode == 429 || $htpcode == '429' || $htpcode == '403' || $htpcode == 403 || $htpcode == 0) {
      saveErr1($htpcode,$i,$e,'first&proxy='.$proxys[$e],$data);
      $i--;$e++;continue;
  }
  $urls = getUrls($data); 
  if ( count($urls[0]) === 0 ) {
    if (haveResults($data)) saveErrNoResult($htpcode,$i,$e,$data);
    $e++;continue;
  }else{ 
    save_urls($urls,$blackDomains);
  }
  
  $end = false;
  $start=0;
  for ($ix=0; $ix < $pages; $ix++) {  //for each page
      if ($end != true) {
          $curl = new Curl($cookieFile);
          $curl->cookies($cookieFile);
          $ag = get_agent(rand(0,23)); 
          $curl->httpHeader(explode(PHP_EOL, getHeaders($ag)));
          $curl->setUserAgent($ag);
          $curl->proxy($proxys[$e],$proxyType,$proxyAuth);
          $start += 10;
          $data = $curl->makeRequest('https://www.google.com/search?q='.$search.'&tbs=qdr:m&prmd=ivns&ei=rcpUXbfhFqaQggfCxLWoAw&start='.$start.'&sa=N');
          $httpcode=$curl->httpcode();
          $curl->cookies('');$curl->closee();
          
          if($httpcode == 0){ 
              saveErr1($htpcode,'ix'.$ix,$e,'proxy='.$proxys[$e],$data);
              $ix--;$e++; continue;
          }
          
          if ( !haveResults($data)  ) { // no results found
              $end = true; 
          }else{
              $urls = getUrls($data);
              save_urls($urls,$blackDomains); 
              $next = getNextPage($data);
              if ($next === 'errr') {
                if ($httpcode == 429 || $httpcode == 403 || $httpcode == 0) {
                  saveErr1($httpcode,'ix'.$ix, $e,'proxy='.$proxys[$e],$data);
                  $ix--;$e++; continue;
                }else{
                  saveEndTest($data,$proxys[$e],2);
                  $end = true; 
                }
              }
          }
         #file_put_contents('passed2.txt','$ix:'. $ix . '-'.PHP_EOL,FILE_APPEND);
         delayRequest($delay); $e++;
    }else{
      break;
    }
  }
  file_put_contents('passed_dork.txt', '$i: ' . $i . ' - dorK: ' . $dorks[$i] . PHP_EOL,FILE_APPEND);
  delayRequest($delay);
}  