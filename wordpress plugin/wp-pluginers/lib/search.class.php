<?php

class internetSearch extends pluginers_helper {
  private $items = array();
  private $counter = 0;
  private $nextpage = 1;
  private $query;
  private $apikey;
  private $params;
  
  public function __construct($apikey) {
    $this->apikey = $apikey;
  }
  
  public function init($query, $options) {
    if(empty($this->apikey)){
      return false;
    }
    $this->query = $query;
    $this->params = array();
    if(!empty($options->lang)){
      $this->params[] = 'lr='.$options->lang;
    }
    if(!empty($options->country)){
      $this->params[] = 'gl='.$options->country;
    }
    if(!empty($options->date_range)){
      $this->params[] = 'dateRestrict='.$options->date_range;
    }
    switch ($options->field){
      case 1:
        $this->params[] = 'cx=011361879343322229431:3fgl3vnmqyi';
        break;
      case 2:
        $this->params[] = 'cx=011361879343322229431:pudqvvwpnyi';
        break;
      case 3:
        $this->params[] = 'cx=011361879343322229431:y-fk7iwkek8';
        break;
      case 4:
        $this->params[] = 'cx=011361879343322229431:uf9pb7c_g5c';
        break;
      case 5:
        $this->params[] = 'cx=011361879343322229431:xo0mbxy9iou';
        break;
      case 6:
        $this->params[] = 'cx=011361879343322229431:cwt7xekigmm';
        break;
      case 7:
        $this->params[] = 'cx=011361879343322229431:akh7la-b67a';
        break;
      case 8:
        $this->params[] = 'cx=011361879343322229431:8xndd_8u1zo';
        break;
      case 9:
        $this->params[] = 'cx=011361879343322229431:ttpc2eenriy';
        break;
      case 10:
        $this->params[] = 'cx=011361879343322229431:uchkgtyve9i';
        break;
    }
    $this->params[] = 'key='.$this->apikey;
    $this->params[] = 'q='.urlencode($this->query);
    $this->params[] = 'num=10';
    $this->params = implode('&', $this->params);
    $this->start_search();
    return $this->items;
  }
  
  public function start_search() {
    $this->log('start reading from '.'https://www.googleapis.com/customsearch/v1?'.$this->params.'&start='.$this->nextpage);
    $result = json_decode($this->curl('https://www.googleapis.com/customsearch/v1?'.$this->params.'&start='.$this->nextpage), true);
    if(!empty($result['items'])){
      foreach($result['items'] as $item){
        $this->items[$this->counter]['title'] = $item['title'];
        $this->items[$this->counter]['content'] = $item['snippet'];
        $this->items[$this->counter]['url'] = $item['link'];
        $this->counter++;
      }
      if($this->counter >= SEARCH_DATA_LIMIT){
        return true;
      }
      if(!empty($result['queries']['nextPage'][0]['startIndex'])){
        $this->nextpage = $result['queries']['nextPage'][0]['startIndex'];
      }
      else{
        return true;
      }
      $this->start_search();
    }
    return true;
  }
  
}

?>