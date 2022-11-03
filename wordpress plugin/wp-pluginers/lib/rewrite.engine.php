<?php

class pluginers_wp_rewrite extends pluginers_controller {
  private $content;
  private $helper;
  private $contprof_sessid;
  
  public function __construct() {
    $this->helper = new pluginers_helper();
  }
  
  public function spin($content) {
    if(empty(self::$settings['rewrite_provider'])){
      error_log('you need to choose one of the rewrite providers');
      return $content;
    }
    $this->content = stripslashes($content);
    switch(self::$settings['rewrite_provider']){
      case 'wordai':
        $this->wordai();
        break;
      case 'spinrewriter':
        $this->spinrewriter();
        break;
      case 'chimprewriter':
        $this->chimprewriter();
        break;
      case 'contentprofessor':
        $this->contentprofessor();
        break;
      case 'spinnerchief':
        $this->spinnerchief();
        break;
    }
    return addslashes($this->content);
  }
  
  private function wordai() {
    if(empty(self::$settings['rewrite_api']['wordai']['email']) || empty(self::$settings['rewrite_api']['wordai']['pass'])){
      error_log('you need to set WordAi server all settings to connect with its server successfully');
      return;
    }
    $postfields = array(
    's' => $this->content,
    'quality' => 'Very Readable',
    'email' => self::$settings['rewrite_api']['wordai']['email'],
    'pass' => self::$settings['rewrite_api']['wordai']['pass'],
    'output' => 'json',
    'returnspin' => 'true',
    );
    $result = json_decode($this->helper->curl('http://wordai.com/users/turing-api.php', false, http_build_query($postfields)), true);
    if($this->helper->curl_status != 200){
      $this->content = false;
      error_log('WordAi\'s server returns unkown error');
      return;
    }
    if($result['status'] == 'Success'){
      $this->content = $result['text'];
    }
    elseif($result['status'] == 'Failure'){
      $this->content = false;
      error_log('WordAi\'s server returns error: '.$result['error']);
    }
    else{
      $this->content = false;
      error_log('WordAi\'s server returns unkown error');
    }
  }
  
  private function spinrewriter() {
    if(empty(self::$settings['rewrite_api']['spinrewriter']['email_address']) || empty(self::$settings['rewrite_api']['spinrewriter']['api_key'])){
      error_log('you need to set Spin Rewriter server all settings to connect with its server successfully');
      return;
    }
    $postfields = array(
    'text' => $this->content,
    'email_address' => self::$settings['rewrite_api']['spinrewriter']['email_address'],
    'api_key' => self::$settings['rewrite_api']['spinrewriter']['api_key'],
    'protected_terms' => self::$settings['rewrite_api']['spinrewriter']['protected_terms'],
    'action' => 'unique_variation',
    );
    $result = json_decode($this->helper->curl('http://www.spinrewriter.com/action/api', false, http_build_query($postfields)), true);
    if($this->helper->curl_status != 200){
      $this->content = false;
      error_log('Spin Rewriter\'s server returns unkown error');
      return;
    }
    if($result['status'] == 'OK'){
      $this->content = $result['response'];
    }
    elseif($result['status'] == 'ERROR'){
      $this->content = false;
      error_log('Spin Rewriter\'s server returns error: '.$result['response']);
    }
    else{
      $this->content = false;
      error_log('Spin Rewriter\'s server returns unkown error');
    }
  }
  
  private function chimprewriter() {
    if(empty(self::$settings['rewrite_api']['chimprewriter']['aid']) || empty(self::$settings['rewrite_api']['chimprewriter']['email']) || empty(self::$settings['rewrite_api']['chimprewriter']['apikey'])){
      error_log('you need to set ChimpRewriter server all settings to connect with its server successfully');
      return;
    }
    $postfields = array(
    'text' => $this->content,
    'aid' => self::$settings['rewrite_api']['chimprewriter']['aid'],
    'email' => self::$settings['rewrite_api']['chimprewriter']['email'],
    'apikey' => self::$settings['rewrite_api']['chimprewriter']['apikey'],
    'protectedterms' => self::$settings['rewrite_api']['chimprewriter']['protectedterms'],
    'quality' => '5',
    'rewrite' => '1',
    );
    $result = json_decode($this->helper->curl('https://api.chimprewriter.com/ChimpRewrite', false, http_build_query($postfields)), true);
    if($this->helper->curl_status != 200){
      $this->content = false;
      error_log('ChimpRewriter\'s server returns unkown error');
      return;
    }
    if($result['status'] == 'success'){
      $this->content = $result['output'];
    }
    else{
      $this->content = false;
      error_log('ChimpRewriter\'s server returns error');
    }
  }
  
  private function contentprofessor() {
    if(empty(self::$settings['rewrite_api']['contentprofessor']['login']) || empty(self::$settings['rewrite_api']['contentprofessor']['password'])){
      error_log('you need to set ContentProfessor server all settings to connect with its server successfully');
      return;
    }
    if(self::$settings['rewrite_api']['contentprofessor']['free'] == 1){
      $apiurl = 'http://www.contentprofessor.com/member_free/api';
    }
    else{
      $apiurl = 'http://www.contentprofessor.com/member_pro/api';
    }
    $postfields = array(
    'login' => self::$settings['rewrite_api']['contentprofessor']['login'],
    'password' => self::$settings['rewrite_api']['contentprofessor']['password'],
    );
    $result = json_decode($this->helper->curl($apiurl.'/get_session?format=json', false, http_build_query($postfields)), true);
    if($this->helper->curl_status != 200){
      $this->content = false;
      error_log('ContentProfessor\'s server returns unkown error');
      return;
    }
    if(!empty($this->contprof_sessid) || !empty($result['result']['data']['session'])){
      if(empty($this->contprof_sessid)){
        $this->contprof_sessid = $result['result']['data']['session'];
      }
      $postfields = array(
      'text' => strip_tags($this->content),
      'session' => $this->contprof_sessid,
      'language' => self::$settings['rewrite_api']['contentprofessor']['lang'],
      'quality' => 'better',
      'limit' => '1',
      'format' => 'json',
      );
      $result = json_decode($this->helper->curl($apiurl.'/include_synonyms?'.http_build_query($postfields)), true);
      if($result['result']['data']['text']){
        preg_match_all('/\{(?:(?!}).)+\}/', strip_tags($result['result']['data']['text']), $matches);
        if(!empty($matches[0])){
          foreach($matches[0] as $match){
            $match = explode('|', $match);
            $this->content = str_replace($match[0], $match[1], $this->content);
          }
        }
        else{
          error_log('ContentProfessor\'s server returns unknown error');
          $this->content = false;
        }
      }
      elseif($result['result']['error']['description']){
        error_log('ContentProfessor\'s server returns error: '.$result['result']['error']['description']);
      }
    }
    elseif(!empty($result['result']['error']['description'])){
      $this->content = false;
      error_log('ContentProfessor\'s server returns error: '.$result['result']['error']['description']);
    }
    else{
      $this->content = false;
      error_log('ContentProfessor\'s server returns unkown error');
    }
  }
  
  private function spinnerchief() {
    if(empty(self::$settings['rewrite_api']['spinnerchief']['username']) || empty(self::$settings['rewrite_api']['spinnerchief']['password']) || empty(self::$settings['rewrite_api']['spinnerchief']['apikey'])){
      error_log('you need to set SpinnerChief server all settings to connect with its server successfully');
      return;
    }
    $postfields = array(
    'username' => self::$settings['rewrite_api']['spinnerchief']['username'],
    'password' => self::$settings['rewrite_api']['spinnerchief']['password'],
    'apikey' => self::$settings['rewrite_api']['spinnerchief']['apikey'],
    'spintype' => '1',
    'wordquality' => '9',
    'thesaurus' => strtolower(self::$settings['rewrite_api']['spinnerchief']['lang']),
    'usehurricane' => '0',
    'tagprotect' => empty(self::$settings['rewrite_api']['spinnerchief']['tagprotect'])? 'null' : htmlspecialchars_decode(self::$settings['rewrite_api']['spinnerchief']['tagprotect']),
    'protectwords' => self::$settings['rewrite_api']['spinnerchief']['protectwords']
    );
    $result = $this->helper->curl('http://api.spinnerchief.com:443/'.http_build_query($postfields), false, $this->content);
    if($this->helper->curl_status != 200){
      $this->content = false;
      error_log('SpinnerChief\'s server returns unkown error');
      return;
    }
    if(substr($result, 0, 6) == 'error='){
      error_log('SpinnerChief\'s server returns error: '.$result);
    }
    else{
      $this->content = $result;
    }
  }
  
}