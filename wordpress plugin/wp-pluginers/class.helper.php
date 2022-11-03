<?php

class pluginers_helper {
  public $ParseOutput;
  public static $returnValue;
  public static $staticResult;
  public static $paging;
  public $curl_status = 200;
  public $curl_last_url = '';

  public function __construct(){}
  
  public function fixImagesRoot($content, $url){
    $counter = 0;
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $tags = $doc->getElementsByTagName('img');
    if(!empty($tags)){
      foreach ($tags as $tag) {
        $imglink = $tag->getAttribute('src');
        $imglink = str_replace(array('\'', '"'), '', stripslashes($imglink));
        if(strpos($imglink, 'http://') === false && strpos($imglink, 'https://') === false && strpos($imglink, './') === false && strpos($imglink, '../') === false){
          str_replace($imglink, $url.$imglink, $content);
          $counter++;
        }
      }
    }
    return $content;
  }
  
  public function log($message, $newline=false) {
    if(pluginers_env == 'development'){
      $message = date('d/m/y H:i:s').' : '.$message;
      if($newline){
        $message .= "\n==============================================";
      }
      $message .= "\n";
      error_log($message, 3, pluginers_logfile);
    }
  }
  
  public function curl($url, $ssl = false, $postfields = false) {
    $this->curl_last_url = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.6 (KHTML, like Gecko) Chrome/16.0.897.0 Safari/535.6');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    if ($ssl !== false) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($ch, CURLOPT_CAINFO, pluginers_lib.'/cacert.pem');
    } else {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    if(!empty($postfields)){
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    }
    if(defined('WP_PROXY_HOST')){
      curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
      curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
      curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      if(defined('WP_PROXY_USERNAME')){
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
      }
    }
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, pluginers_lib.'/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, pluginers_lib.'/cookie.txt');
    $result = curl_exec($ch);
    $this->curl_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $this->curl_last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return $result;
  }

  public static function Paging($sql){
    global $wpdb;
  	if(isset($_REQUEST['perpage'])) $limit = $_REQUEST['perpage'];
  	else $limit = 20;
  	if(isset($_REQUEST['callpage'])) $currentpage = $_REQUEST['callpage'];
  	else $currentpage = 1;

    if(preg_match('/group by ([a-zA-Z0-9`*(),._\n\r]+)\s?/i', $sql, $match)){
      $cselect = 'DISTINCT('.$match[1].')';
      $countsql = preg_replace('/group by ([a-zA-Z0-9`*(),._\n\r\s]+)\s?/i', '', $sql);
    }
    else{
      $cselect = '*';
      $countsql = $sql;
    }
    $countsql = preg_replace('/select ([a-zA-Z0-9`*(),._\n\r\s]+) from/i', 'SELECT COUNT('.$cselect.') FROM', $countsql);
    $count = $wpdb->get_var($countsql);
    if($wpdb->num_rows > 1)
        $count = $wpdb->num_rows;
    if($count == 0)
        return;
  	$pages = $count/$limit;
  	$pages = ceil($pages);

  	if($currentpage < $pages)
  		self::$paging['stillmore'] = 1;
  	else{
  		$currentpage = $pages;
  		self::$paging['stillmore'] = 0;
  	}
  	if($currentpage == 1){
  		self::$paging['previous'] = 0;
  		self::$paging['next'] = $currentpage+1;
  	}
  	elseif($currentpage == $pages){
  		self::$paging['previous'] = $currentpage-1;
  		self::$paging['next'] = 0;
  	}
  	else{
  		self::$paging['previous'] = $currentpage-1;
  		self::$paging['next'] = $currentpage+1;
  	}

    self::$paging['result'] = $count;
    self::$paging['pages'] = $pages;
    self::$paging['perpage'] = $limit;
    self::$paging['page'] = $currentpage;

  	if($currentpage > 0) $currentpage--;
  	$from = $currentpage*$limit;
  	return $sql." LIMIT $from,$limit";
  }

  public function output($respond, $result){
    if(!$this->ParseOutput){
      $this->ParseOutput = true;
      if(is_array($result))
        return $result;
      else
        return array();
    }
    self::jsonPrint($respond, $result);
  }
  
  public static function snippetString($string, $wordscount, $endoftext) {
    $retval = $string;
    $string = preg_replace('/(?<=\S,)(?=\S)/', ' ', $string);
    $string = str_replace("\n", ' ', $string);
    $array = explode(' ', $string);
    if (count($array) <= $wordscount) {
      $retval = $string;
    } else {
      array_splice($array, $wordscount);
      $retval = implode(' ', $array).' '.$endoftext;
    }
    return $retval;
  }

  public static function jsonPrint($respond, $result){
    $json = array();
  	if(is_array($result)){
  		$json['respond'] = $respond;
        $json['message'] = '';
        $json['result'] = $result;
  	}
  	else{
  		$json['respond'] = $respond;
  		$json['message'] = $result;
        $json['result'] = array();
  	}
    if(self::$returnValue == 'cronjob'){
      if($respond == 0){
        pluginers_cronsend::writeLog($json['message']);
        die();
      }
      else{
        return;
      }
    }
    elseif(self::$returnValue){
      self::$staticResult = array('respond' => $respond, 'result' => $result);
      return true;
    }
    header('Content-Type: application/json');
  	echo json_encode($json);
  	die();
  }

  public function fetchPrintResult(){
    return self::$staticResult;
  }
  
  public function CheckParams($params, $or=false){
    if(! is_array($params)){
        $this->output(0, 'Parameters `'.$params.'` is required');
    }
    $indexes = '';
    foreach($params AS $param){
        if(!isset($_REQUEST[$param]) OR empty($_REQUEST[$param])){
            if($or) $indexes[] = $param;
            else $this->output(0, 'Parameter `'.$param.'` is required, All required parameters are `'.implode($params, '`,`').'`');
        }
        elseif($or) return;
    }
    if($or){
        $this->output(0, 'Parameters `'.implode($params, '`,`').'` at least one of them is required');
    }
  }
  
  public static function redirect($link){
    echo '<script>window.location="'.$link.'"</script>';
  }

}