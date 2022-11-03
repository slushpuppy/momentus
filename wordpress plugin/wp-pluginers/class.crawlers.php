<?php

use PicoFeed\Reader\Reader;

use andreskrey\Readability\Configuration;
use andreskrey\Readability\ParseException;
use andreskrey\Readability\Readability;

class pluginers_crawlers extends pluginers_controller {
  
  public function __construct() {
    parent::__construct();
  }
  
  function __destruct() {
    @ini_set('log_errors', 0);
  }
  
  private function setup_env(){
    //set_time_limit(0);
    //ini_set('memory_limit', '512M');
    //ini_set('log_errors', 1);
    define('SEARCH_DATA_LIMIT', 100);
    //ini_set('display_errors', 0);
    if(pluginers_env == 'development'){
    //  ini_set('error_reporting', E_ALL);
    }
    else{
   //   ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);
    }
    //ini_set('error_log', pluginers_dir.'/cron_log.log');
    //global $wpdb;
    //$wpdb->show_errors();
  }

  public function publish_auto_items() {
    $this->log('start auto publish cron...');
    $this->setup_env();
    global $wpdb;
    $itemids = array();
    $sources = $wpdb->get_results("SELECT id,post_pubfire FROM ".$wpdb->prefix."pub_autopilot_rss WHERE autopublish='1' AND status='1'");
    if($sources){
      foreach($sources as $source){
        $ids = $wpdb->get_var("SELECT GROUP_CONCAT(".$wpdb->prefix."pub_autopilot_posts.id separator ',') FROM ".$wpdb->prefix."pub_autopilot_posts
        INNER JOIN ".$wpdb->prefix."pub_autopilot_posts_interaction ON(".$wpdb->prefix."pub_autopilot_posts_interaction.postid=".$wpdb->prefix."pub_autopilot_posts.id)
        WHERE ".$wpdb->prefix."pub_autopilot_posts.autopub_date IS NULL AND ".$wpdb->prefix."pub_autopilot_posts.rssid='$source->id' AND ".$wpdb->prefix."pub_autopilot_posts_interaction.rank>=$source->post_pubfire");
        if(!empty($ids)){
          $itemids[] = rtrim($ids, ',');
        }
      }
      if(!empty($itemids)){
        $itemids = implode(',', $itemids);
        $itemids = str_replace(',,', ',', $itemids);
        $itemids = rtrim($itemids, ',');
        $wp_publisher = new pluginers_wp_publisher();
        $wp_publisher->publish_now($itemids);
      }
    }
    $this->log('end auto publish cron...', true);
  }
  
  public function clean_query_sources() {
    $this->log('start clean query sources results...');
    $this->setup_env();
    global $wpdb;
    $feeds = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."pub_autopilot_rss WHERE type='query'");
    if($feeds){
      foreach($feeds as $feed){
        $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts WHERE rssid='$feed->id'");
        $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_data WHERE rssid='$feed->id'");
        $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_interaction WHERE rssid='$feed->id'");
        $wpdb->query("UPDATE ".$wpdb->prefix."pub_autopilot_rss SET items_count='0' WHERE id='$feed->id'");
      }
    }
    $this->log('end clean query sources results...', true);
  }
  
  public function clean_archive() {
    $this->log('start clean archive cron...');
    $this->setup_env();
    global $wpdb;
    $feeds = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."pub_autopilot_posts WHERE publishdate < NOW()-INTERVAL ".self::$settings['keep_archive']." DAY");
    if($feeds){
      foreach($feeds as $feed){
        $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts WHERE id='$feed->id'");
        $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_data WHERE postid='$feed->id'");
        $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_interaction WHERE postid='$feed->id'");
      }
    }
    $this->log('end clean archive cron...', true);
    $this->update_rss_counters();
  }
  
  public function update_rss_counters() {
    global $wpdb;
    $counters = $wpdb->get_results("SELECT COUNT(`rssid`) AS counts,`rssid` FROM `".$wpdb->prefix."pub_autopilot_posts` GROUP BY `rssid`");
    if($counters){
      foreach($counters as $counter){
        $wpdb->update($wpdb->prefix.'pub_autopilot_rss', array('items_count' => $counter->counts), array('id' => $counter->rssid));
      }
    }
  }
  
  public function social_counters() {
    $this->log('start social counter tracker cron...');
    $this->setup_env();
    if(self::$settings['update_social_counter'] == 0)exit;
    require(pluginers_lib.'/social.class.php');
    global $wpdb;
    $feeds = $wpdb->get_results("SELECT ".$wpdb->prefix."pub_autopilot_posts_data.*,".$wpdb->prefix."pub_autopilot_rss.social_tracker FROM ".$wpdb->prefix."pub_autopilot_posts
    INNER JOIN ".$wpdb->prefix."pub_autopilot_posts_data ON(".$wpdb->prefix."pub_autopilot_posts.id=".$wpdb->prefix."pub_autopilot_posts_data.postid)
    INNER JOIN ".$wpdb->prefix."pub_autopilot_rss ON(".$wpdb->prefix."pub_autopilot_rss.id=".$wpdb->prefix."pub_autopilot_posts.rssid)
    WHERE ".$wpdb->prefix."pub_autopilot_posts.publishdate > NOW()-INTERVAL 30 DAY ORDER BY ".$wpdb->prefix."pub_autopilot_posts.tracker_update ASC LIMIT 0,1000");
    if($feeds){
      $socialEngine = new socialDigger();

        shuffle($feeds);
      foreach($feeds as $feed){
        $wpdb->update($wpdb->prefix.'pub_autopilot_posts', array('tracker_update' => current_time('timestamp')), array('id' => $feed->postid));
        if($feed->social_tracker == 0){
          continue;
        }
        $feed->url = urldecode($feed->url);
        $pagesource = $this->curl($feed->url);
        if(empty($pagesource)){
          continue;
        }
        $socialstat = $socialEngine->collect_data($feed->url, $pagesource, self::$settings['fb_access_token']);
        if(!empty($socialstat)){
          $wpdb->update($wpdb->prefix.'pub_autopilot_posts_interaction', $socialstat, array('postid' => $feed->postid));
          $wpdb->query("UPDATE ".$wpdb->prefix."pub_autopilot_posts_interaction SET rank=`twitter_share`+`fb_likes`+`fb_share`+`gplus_share`+`pinterest_share`+`linkedin_share` WHERE postid='$feed->postid'");
        }
      }
    }
    $this->log('end social counter tracker cron...', true);
  }
  
  public function start_crawling() {
     // error_log(print_r(3243243,TRUE),0);
    $this->log('start main crawling cron...');
    $this->setup_env();
     // error_log(print_r(3243,TRUE),0);
    require(pluginers_lib.'/readability/vendor/autoload.php');
    //require(pluginers_lib.'/Readability.php');
    //require(pluginers_lib.'/JSLikeHTMLElement.php');
    require(pluginers_lib.'/SimplePie/autoloader.php');
    require(pluginers_lib.'/SimplePie/idn/idna_convert.class.php');
    require(pluginers_lib.'/search.class.php');
    require(pluginers_lib.'/PicoFeed/autoload.php');
    
    global $wpdb;
    $feed = new SimplePie();
    $reader = new Reader;
      $license_key = self::$settings['license_key'];
      $result = wp_remote_get( 'http://licensebeast.com/engine/applications/nexus/interface/licenses/?check&key='.$license_key.'&setIdentifier='.md5(get_bloginfo('url').$license_key).'&usage_id='.self::$settings['license_key_usage_id']);
      if (!is_array( $result )) {
          $this->log('Invalid license key', true);
          die();
      }
      $json =  json_decode($result['body']);

      if (!$json || $json->status != "ACTIVE") {
          $this->log('Invalid license key', true);
          die();
      }


      if(self::$settings['last_google_key'] >= 4){
      $index = 0;
    }
    else{
      $index = self::$settings['last_google_key']+1;
      if(empty(self::$settings['google_keys'][$index])){
        $index = 0;
      }
    }
    self::$settings['last_google_key'] = $index;
    update_option('pluginers_options', array_map('wp_slash', self::$settings));

      $internet = new internetSearch(self::$settings['google_keys'][$index]);
    $rss_sites = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."pub_autopilot_rss WHERE status='1' ORDER BY last_update ASC");
    if($rss_sites){
        shuffle($rss_sites);
      foreach($rss_sites as $rss_site){

          $wpdb->query("UPDATE ".$wpdb->prefix."pub_autopilot_rss SET last_update=NOW() WHERE id='$rss_site->id'");
        
        if($rss_site->type == 'rss'){
          switch($rss_site->engineid) {
            case 1:
              try {

                $this->log('try get feeds for rss link '.urldecode($rss_site->rsslink));

                $resource = $reader->download(urldecode($rss_site->rsslink));
                  //error_log(print_r($resource,TRUE),0);
                $parser = $reader->getParser($resource->getUrl(), $resource->getContent(), $resource->getEncoding());
                //$parser->enableContentGrabber();
                $feeds = $parser->execute();
                $items = $feeds->items;
              } catch (Exception $e) {
                 // error_log(print_r($e,TRUE),0);
                $this->log('failed to get the feeds with error: '.htmlspecialchars($e->getMessage()));
                $wpdb->update($wpdb->prefix.'pub_autopilot_rss', array('read_status' => '2', 'read_error' => htmlspecialchars($e->getMessage())), array('id' => $rss_site->id));
              }
              break;
            case 2:
              try {
                $this->log('try get feeds for rss link '.urldecode($rss_site->rsslink));
                $feed->set_feed_url(urldecode($rss_site->rsslink));
                $feed->enable_cache(false);
                $feed->force_feed(true);
                $feed->set_timeout(300);
                $feed->init();
                $feed->handle_content_type();
                $items = $feed->get_items();
                if($feed->error()){
                  $this->log('failed to get the feeds with error: '.htmlspecialchars($feed->error()));
                  $wpdb->update($wpdb->prefix.'pub_autopilot_rss', array('read_status' => '2', 'read_error' => htmlspecialchars($feed->error())), array('id' => $rss_site->id));
                }
              } catch (Exception $e) {
                $this->log('failed to get the feeds with error: '.$e->getMessage());
                $wpdb->update($wpdb->prefix.'pub_autopilot_rss', array('read_status' => '2', 'read_error' => $e->getMessage()), array('id' => $rss_site->id));
                continue 2;
              }
              break;
          }
        }
        else{
          $this->log('start internet search about '.stripslashes($rss_site->query));
          if($rss_site->items_count >= SEARCH_DATA_LIMIT){
            $this->log('end search already results have been fetched before');
            continue;
          }
          $items = $internet->init(stripslashes($rss_site->query), $rss_site);
          if(empty($items)){
            $this->log('end search with no result');
            continue;
          }
        }
        
        $newitems = 0;
        if(!empty($items)){
          foreach($items as $item){
            $rss_site->grabber_allow_tags = explode(',', $rss_site->grabber_allow_tags);
            if(!empty($rss_site->grabber_allow_tags)){
              $rss_site->grabber_allow_tags = '<'.implode('><', array_map('trim', $rss_site->grabber_allow_tags)).'>';
            }
            
            if($rss_site->type == 'rss'){
              switch($rss_site->engineid) {
                case 1:
                  $this->log('get page content for link '.$item->getUrl());
                  $rssurl = urldecode($item->getUrl());
                  $rsscontent = $item->getContent();
                  $md5rssurl = md5($item->getUrl());
                  $subject = $item->getTitle();
                  $mediaurl = $item->getEnclosureUrl();
                  $date = $item->getDate()->format('Y-m-d H:i:s');
                  break;
                case 2:
                  $this->log('get page content for link '.$item->get_permalink());
                  $rssurl = urldecode($item->get_permalink());
                  $rsscontent = $item->get_content();
                  $md5rssurl = md5($item->get_permalink());
                  $subject = $item->get_title();
                  if($enclosure = $item->get_enclosure()){
                    $mediaurl = $enclosure->get_thumbnail();
                  }
                  else{
                    $mediaurl = '';
                  }
                  $date = $item->get_date('Y-m-d H:i:s');
                  break;
              }
            }
            else{
              $this->log('get page content for link '.$item['url']);
              $rssurl = $item['url'];
              $rsscontent = $item['content'];
              $md5rssurl = md5($item['url']);
              $subject = $item['title'];
            }

            $pagesource = $this->curl($rssurl);
            if($this->curl_status != 200){
              continue;
            }
            if(!empty($this->curl_last_url)){
              $rssurl = $this->curl_last_url;
            }
            $fixedrssurl = explode('#', $rssurl);
            if(count($fixedrssurl) > 1){
              $fixedrssurl = $fixedrssurl[0];
              $fixedpagesource = $this->curl($fixedrssurl);
              if($this->curl_status == 200){
                $rssurl = $fixedrssurl;
                $pagesource = $fixedpagesource;
              }
              unset($fixedrssurl);
              unset($fixedpagesource);
            }

            if($rss_site->smart_grabber == 1){
              $this->log('start fetch the readability content');
              unset($readability);
              unset($configuration);
              
              if(!empty($rss_site->grabber_restrict)){
                $grabber_restrict = unserialize($rss_site->grabber_restrict);
                if(!empty($grabber_restrict['type']) && !empty($grabber_restrict['name']) && !empty($grabber_restrict['value'])){
                  unset($dom);
                  $dom = new DOMDocument();
                  $dom->loadHTML($pagesource);
                  $xpath = new DOMXPath($dom);
                  $entries = $xpath->query('//'.$grabber_restrict['type'].'[@'.$grabber_restrict['name'].'="'.$grabber_restrict['value'].'"]');
                  $pagesource = '';
                  if($entries->length > 0){
                    for($i=0;$i<$entries->length;$i++){
                      $pagesource .= $dom->saveXML($entries->item($i));
                    }
                    $pagesource = str_replace('&#13;', '', $pagesource);
                  }
                }
              }
              
              $configuration = new Configuration();
              $configuration->setFixRelativeURLs(true)->setOriginalURL($rssurl);
              if($rss_site->smart_grabber_ai == 0){
                $configuration->setCleanConditionally(false);
              }

              $readability = new Readability($configuration);
              try {
                $readability->parse($pagesource);
                $result = true;
              } catch (ParseException $e) {
                $this->log('Error processing text: '.$e->getMessage);
                $result = false;
              }
            }
            else{
              $this->log('the readability content feature disabled for this rss');
              $result = false;
            }

            if($result){
              $this->log('got the readability content successfully');
              $content = $this->fixImagesRoot(strip_tags($readability->getContent(), $rss_site->grabber_allow_tags), $rssurl);
            }
            elseif(!empty($rsscontent)){
              $this->log('failed in reading the readability content');
              $content = $this->fixImagesRoot($rsscontent, $rssurl);
            }
            else{
              $this->log('failed in reading the readability content and no rss content');
              $content = '';
            }

            if(!empty($rssurl) && !empty($subject)){
              $this->log('got all content for this url successfully');
              $oldrssid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."pub_autopilot_posts WHERE md5url='$md5rssurl' AND rssid='$rss_site->id'");
              if($oldrssid > 0){
                continue;
              }
              $post = array();
              $post['rssid'] = $rss_site->id;
              $post['md5url'] = $md5rssurl;
              $post['tracker_update'] = current_time('timestamp');
              if($rss_site->original_date == 1 && !empty($date)){
                $post['publishdate'] = $date;
              }

              $data = array();
              $data['rssid'] = $rss_site->id;
              $data['url'] = urlencode($rssurl);
              $data['media'] = urlencode($mediaurl);
              $data['subject'] = addslashes($subject);
              
              if(!empty($rss_site->alt_content)){
                switch($rss_site->alt_content){
                  case 'title':
                    $content = $subject;
                    break;
                  case 'link':
                    $content = $rssurl;
                    break;
                  case 'media':
                    $content = $mediaurl;
                    break;
                }
              }
              
              if(!empty($rss_site->replace)){
                $rss_site->replace = unserialize($rss_site->replace);
                foreach($rss_site->replace['search'] as $key => $value){
                  $search[$key] = $rss_site->replace['search'][$key];
                  $replace[$key] = $rss_site->replace['word'][$key];
                }
                $content = str_replace($search, $replace, $content);
              }
              if(!empty($rss_site->truncate_limit)){
                $content = self::snippetString(strip_tags($content), $rss_site->truncate_limit, $rss_site->truncate_replace);
              }
              
              $data['content'] = addslashes($content);

                if (strlen(trim($data['content'])) < 200) continue;
              if ($rss_site->after_html) {
                  $data['content'] = $data['content'].$rss_site->after_html;
              }
                if ($rss_site->before_html) {
                    $data['content'] = $rss_site->before_html.$data['content'];
                }
                if (isset(self::$settings['amazon_access_key']) && !empty(self::$settings['amazon_access_key']))
                {
                    $tags = explode("\r\n",$rss_site->add_amazon_tag);
                    require_once __DIR__.'/lib/Amazon/advertising.php';
                    $aws = new AWSProductAdvertising(self::$settings['amazon_access_key'],self::$settings['amazon_secret_key'],self::$settings['amazon_associate_tag']);
                    $arr = $aws->searchKeyword($tags[rand(0,sizeof($tags) -1)]);
                    $amazonHTML = $aws->getHtml($arr);
                    switch ($rss_site->add_amazon_products_position ) {
                        case 'before':
                            $data['content'] = $amazonHTML.$data['content'];
                            break;
                        case 'end':
                            $data['content'] = $data['content'].$amazonHTML;
                            break;
                        case 'inbetween':
                                $data = $aws->so_25888630_ad_between_paragraphs($data['content'],$amazonHTML);
                            break;
                    }
                }


                $has_amazon = (isset(self::$settings['amazon_access_key'],self::$settings['amazon_secret_key'],self::$settings['amazon_associate_tag']) && self::$settings['amazon_access_key'] != "" && self::$settings['amazon_secret_key'] != "" && self::$settings['amazon_associate_tag'] != "");
              if ($rss_site->amazon_products_position != '' && $has_amazon) {
                  require_once (__DIR__.'/lib/Amazon/advertising.php');
                  $amazon = new AWSProductAdvertising(self::$settings['amazon_access_key'],self::$settings['amazon_secret_key'],self::$settings['amazon_associate_tag']);
                  $html = $amazon->getHtml($rss_site->add_amazon_tag);
                  switch($rss_site->amazon_products_position) {
                      case 'before':
                          $data['content'] = $html .$data['content'];
                          break;
                      case 'after':
                          $data['content'] =  $data['content'].$html;
                          break;
                      case 'inbetween':
                          $paras = explode("<p>",$data['content']);
                          $middleIndex = count($paras) / 2;
                          array_splice($paras,$middleIndex,0,$html);
                          $data['content'] =  implode('<p>',$data['content']);

                          break;
                  }
              }

              $wpdb->insert($wpdb->prefix.'pub_autopilot_posts', $post);
              $postid = $wpdb->insert_id;
              $data['postid'] = $postid;
              $wpdb->insert($wpdb->prefix.'pub_autopilot_posts_data', $data);
              $wpdb->insert($wpdb->prefix.'pub_autopilot_posts_interaction', array('rssid' => $rss_site->id, 'postid' => $postid));
              $newitems++;
              $this->log('insert all content for this url successfully', true);
            }
          }
        }
        $wpdb->query("UPDATE ".$wpdb->prefix."pub_autopilot_rss SET items_count=items_count+$newitems,read_status='1' WHERE id='$rss_site->id'");
      }
    }
    $this->log('end main crawling cron...', true);
  }

}