<?php

class socialDigger extends pluginers_helper {
  protected $siteurl;
  protected $pagesource;
  protected $statdata = array();
  
  public function __construct() {
  }
  
  public function collect_data($siteurl, $pagesource, $fbaccesstoken) {
    $this->log('start collecting social data: '.$siteurl);
    $this->siteurl = $siteurl;
    $this->pagesource = $pagesource;
    $this->twitter();
    $this->facebook($fbaccesstoken);
    $this->pinterest();
    //$this->disqus();
    //$this->linkedin();
      $this->log(print_r($this->statdata,TRUE));
    $this->log('end collecting social data');
    return $this->statdata;
  }
  
  public function disqus() {
    if (preg_match_all('/disqus_shortname\s+?=\s+?[(\'|\")](.*)[(\'|\")]/i', $this->pagesource, $matches)) {
      //print_r($matches[1][0]);
      $disqus_name = $matches[1][0];
      if (preg_match_all('/disqus_url\s+?=\s+?[(\'|\")](.*)[(\'|\")]/i', $this->pagesource, $matches)) {
        $method = 2;
        //print_r($matches[1][0]);
        $disqus_ident = $matches[1][0];
      }
      elseif (preg_match_all('/disqus_identifier\s+?=\s+?[(\'|\")](.*)[(\'|\")]/i', $this->pagesource, $matches)) {
        $method = 1;
        //print_r($matches[1][0]);
        $disqus_ident = $matches[1][0];
      }
      elseif (preg_match_all('/disqus_identifier\s+?=\s+?(\d+)/i', $this->pagesource, $matches)) {
        $method = 1;
        //print_r($matches[1][0]);
        $disqus_ident = $matches[1][0];
      }
      $disqus_json = $this->curl('http://'.$disqus_name.'.disqus.com/count-data.js?'.$method.'='.urlencode($disqus_ident));
      //echo 'http://'.$disqus_name.'.disqus.com/count-data.js?'.$method.'='.urlencode($disqus_ident);
      if (preg_match('/"id":"(.*)","comments":(\d+)/i', $disqus_json, $matches)) {
        //print_r($matches[2]);
        $this->statdata['disqus_comments'] = $matches[2];
      }
    }
  }
  
  public function linkedin() {
    $data = $this->curl('https://www.linkedin.com/countserv/count/share?url='.urlencode($this->siteurl), false);
    $data = str_replace(array('IN.Tags.Share.handleCount(', ');'), '', $data);
    $data = json_decode($data);
    if(!empty($data->count)){
      $this->statdata['linkedin_share'] = $data->count;
    }
  }
  
  public function pinterest() {
    $data = $this->curl('http://widgets.pinterest.com/v1/urls/count.json?source=6&url='.$this->siteurl);
    $data = str_replace(array('receiveCount(', ')'), '', $data);
    $data = json_decode($data);
    if(!empty($data->count)){
      $this->statdata['pinterest_share'] = $data->count;
    }
  }
  
  public function facebook($fbaccesstoken) {
    if(empty($fbaccesstoken)){
      $fbaccesstoken = 'EAAFfHlS2THQBAK6QvE5wtzq0Os46S4XcGtQLCQcQbeybN4wSBz678CFyvgdZAwwxaSJLsZC91oHDVVL7d6Bx5bKDPGbOPf2ZCCNmEXLb0AS7KfqHAkHZBx1LP8U7IOK7NTGnrHL2nGb0thHNZC11vh7S4o00yUUoZD';
    }

    $data = $this->curl('https://graph.facebook.com/'.$this->siteurl.'/?fields=engagement&access_token='.$fbaccesstoken);
    $data = json_decode($data, true);
    if(isset($data['engagement']['share_count'])){
      $this->statdata['fb_share'] = $data['engagement']['share_count'];
    }
    if(isset($data['engagement']['reaction_count'])){
      $this->statdata['fb_likes'] = $data['engagement']['reaction_count'];
    }
    if(isset($data['engagement']['comment_count'])){
      $this->statdata['fb_comments'] = $data['engagement']['comment_count'];
    }
  }
  
  public function twitter() {
    $data = $this->curl('http://opensharecount.com/count.json?url='.urlencode($this->siteurl));
    $data = json_decode($data, true);
    if(isset($data['count'])){
      $this->statdata['twitter_share'] = $data['count'];
    }
  }
  
  public function googlePlus(){
    $data = $this->curl('https://plusone.google.com/_/+1/fastbutton?url='.$this->siteurl);
    if (preg_match_all('/\<div id\=\"aggregateCount\" class=\"Oy\">([0-9,.]+)\<\/div\>/i', $data, $matches)){
      $matches[1][0] = str_replace(array(',','.',' '), '', $matches[1][0]);
      $matches[1][0] = str_replace(array('K','k'), '000', $matches[1][0]);
      if($matches[1][0] > 0){
        $this->statdata['gplus_share'] = $matches[1][0];
      }
    }
  }
  
}