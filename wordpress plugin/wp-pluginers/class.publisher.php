<?php

class pluginers_wp_publisher extends pluginers_controller {
  private $featured_image_id;
  private $item_url;
  private $image_count = 0;
  private $video_count = 0;

  public function __construct() {
    parent::__construct();
  }
  
  public function publish_now($itemids,$force=false) {
    global $wpdb;
    $rss_table = $wpdb->prefix.'pub_autopilot_rss';
    $feeds = $wpdb->get_results("SELECT ".$wpdb->prefix."pub_autopilot_posts_data.*,".$wpdb->prefix."pub_autopilot_posts.publishdate,$rss_table.ignore_public,$rss_table.category
    ,$rss_table.tags,$rss_table.copyrights,$rss_table.template_id,$rss_table.schedule_time,$rss_table.post_type,$rss_table.name,$rss_table.author_id
    ,$rss_table.post_status,$rss_table.featured_image,$rss_table.rewrite,$rss_table.rewrite_title
    ,$rss_table.ignore_noimages,$rss_table.ignore_novideos,$rss_table.ignore_nocontent,$rss_table.original_date,$rss_table.remove_links
    FROM ".$wpdb->prefix."pub_autopilot_posts
    INNER JOIN ".$wpdb->prefix."pub_autopilot_posts_data ON(".$wpdb->prefix."pub_autopilot_posts.id=".$wpdb->prefix."pub_autopilot_posts_data.postid)
    INNER JOIN $rss_table ON(".$wpdb->prefix."pub_autopilot_posts.rssid=$rss_table.id)
    WHERE ".$wpdb->prefix."pub_autopilot_posts.id IN($itemids)".(($force == false) ? "AND ".$wpdb->prefix."pub_autopilot_posts.autopub_date IS NULL" : ''));

    if($feeds){
      if(!empty(self::$settings['publish_author_id'])){
        $author = get_user_by('login', self::$settings['publish_author_id']);
      }
      require_once(ABSPATH.'wp-admin/includes/media.php');
      require_once(ABSPATH.'wp-admin/includes/file.php');
      require_once(ABSPATH.'wp-admin/includes/image.php');
      require(pluginers_lib.'/rewrite.engine.php');
      $rewrite = new pluginers_wp_rewrite();
      foreach($feeds as $feed){
        $this->item_url = urldecode($feed->url);
        if($feed->ignore_nocontent == 1 && empty($feed->content)){
          continue;
        }
        if($feed->ignore_novideos == 1 || $feed->ignore_noimages == 1){
          $this->count_graphic_elm($feed->content);
        }
        if($feed->ignore_novideos == 1 && intval($this->video_count) < 1){
          continue;
        }
        if($feed->ignore_noimages == 1 && intval($this->image_count) < 1){
          continue;
        }
        if($feed->remove_links == 1){
          $feed->content = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $feed->content);
        }
        if($feed->copyrights == 1){
          $feed->content .= '<br>'.str_replace('{sourcelink}', '<a href="'.urldecode($feed->url).'" target="_blank">'.$feed->name.'</a>', self::$settings['copyrights_string']);
        }
        if(self::$settings['save_images_locally'] == 1){
          $feed->content = $this->save_images_locally($feed->content, false);
        }
        elseif($feed->featured_image == 1){
          $feed->content = $this->save_images_locally($feed->content, true);
        }
        if($feed->rewrite == 1){
          $feed->content = $rewrite->spin($feed->content);
          if($feed->content === false){
            continue;
          }
        }
        if($feed->rewrite_title == 1){
          $feed->subject = $rewrite->spin($feed->subject);
        }
        $arg = array(
          'post_title'    => wp_strip_all_tags($feed->subject),
          'post_content'  => $feed->content,
          'post_author'   => (empty($author->ID))? 1 : $author->ID,
        );
        if($feed->original_date == 1){
          $arg['post_date'] = $feed->publishdate;
          $arg['post_date_gmt'] = $feed->publishdate;
        }
        if($feed->ignore_public == 1){
          if(!empty($feed->tags)){
            $arg['tags_input'] = explode(',', $feed->tags);
          }
          if(!empty($feed->author_id)){
            $rssauthor = get_user_by('login', $feed->author_id);
            $arg['post_author'] = (empty($rssauthor->ID))? 1 : $rssauthor->ID;
          }
          if($feed->post_type == 'post' && !empty($feed->category)){
            $arg['post_category'] = explode(',', $feed->category);
          }
          $arg['post_type'] = $feed->post_type;
          if($feed->post_status == 'schedule'){
            $arg['post_date'] = self::formatScheduleTime(unserialize($feed->schedule_time));
            $arg['post_status'] = 'future';
          }
          else{
            $arg['post_status'] = $feed->post_status;
          }
        }
        else{
          if(!empty(self::$settings['publish_cats'])){
            $arg['post_category'] = explode(',', self::$settings['publish_cats']);
          }
          if(!empty(self::$settings['publish_tags'])){
            $arg['tags_input'] = explode(',', self::$settings['publish_tags']);
          }
          $arg['post_type'] = self::$settings['publish_post_type'];
          $arg['post_status'] = self::$settings['publish_post_status'];

        }
        if ($force) {
            $arg['post_status'] = 'publish';
        }
        $post_id = wp_insert_post($arg);
        if($feed->ignore_public == 1){
          if($feed->post_type != 'post' && !empty($feed->category)){
            $cats = explode(',', $feed->category);
            foreach($cats as $cat){
              $wpdb->insert( $wpdb->term_relationships, array( 'object_id' => $post_id, 'term_taxonomy_id' => $cat ) );
            }
          }
        }
        if($feed->featured_image == 1 && !empty($this->featured_image_id)){
          //add_post_meta($post_id, '_thumbnail_id', $this->featured_image_id, true);
            set_post_thumbnail($post_id,$this->featured_image_id);
        }
        elseif(!empty($feed->media)){
          $attach = $this->storeImage($feed->media);
          if(!empty($attach['attachid'])){
            add_post_meta($post_id, '_thumbnail_id', $attach['attachid'], true);
          }
        }
        if(!empty($feed->template_id)){
          $template_metakeys = get_post_meta($feed->template_id);
          foreach($template_metakeys as $metakey => $value){
            add_post_meta($post_id, $metakey, $value[0], true);
          }
          $template_post = get_post($feed->template_id);
          wp_update_post(array('ID' => $post_id, 'comment_status' => $template_post->comment_status

          ));

                set_post_format($post_id, get_post_format($feed->template_id));
        }
      }
      $wpdb->query("UPDATE ".$wpdb->prefix."pub_autopilot_posts SET autopub_date='".current_time('timestamp')."' WHERE id IN ($itemids)");
    }
  }
  
  private function count_graphic_elm($content) {
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $this->image_count = $doc->getElementsByTagName('img')->length;
    $this->video_count = $doc->getElementsByTagName('iframe')->length;
  }
  
  private function save_images_locally($content, $firstimageonly = false) {
    $this->log('start storing content images locally...');
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $tags = $doc->getElementsByTagName('img');
    $counter = 0;
    if(!empty($tags)){
      foreach ($tags as $tag) {
        $imglink = $tag->getAttribute('src');
        $imglink = str_replace(array('\'', '"'), '', stripslashes($imglink));
        $wpstore = $this->storeImage($imglink);
        if($wpstore === false){
          continue;
        }
        if($counter == 0){
          $this->featured_image_id = $wpstore['attachid'];
        }
        $content = str_replace(addslashes($imglink), $wpstore['wplink'], $content);
        if($firstimageonly){
          break;
        }
        $counter++;
      }
    }
    $this->log('end storing content '.$counter.' images locally...');
    return $content;
  }
  
  private function formatScheduleTime($time) {
    if(!empty($time['day'])){
      $day = $time['day'];
    }
    else{
      $day = date('d', current_time('timestamp'));
    }
    if(!empty($time['month'])){
      $month = $time['month'];
    }
    else{
      $month = date('m', current_time('timestamp'));
    }
    if(!empty($time['year'])){
      $year = $time['year'];
    }
    else{
      $year = date('Y', current_time('timestamp'));
    }
    if(!empty($time['hour'])){
      $hour = $time['hour'];
    }
    else{
      $hour = date('H', current_time('timestamp'));
    }
    if(!empty($time['minute'])){
      $minute = $time['minute'];
    }
    else{
      $minute = date('i', current_time('timestamp'));
    }
    return date('Y-m-d H:i:s', mktime($hour, $minute, 0, $month, $day, $year));
  }
  
  private function storeImage($imglink) {
    $imglink = urldecode($imglink);
    if(strpos($imglink, 'http://') === false && strpos($imglink, 'https://') === false){
      $imglink = str_replace(array($imglink, 'https://'), array($this->item_url.'/'.$imglink, 'http://'), $imglink);
    }
    $tmp = download_url($imglink);
    if(is_wp_error($tmp)){
      @unlink($tmp);
      return false;
    }
    $file_array = array();
    preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $imglink, $matches);
    $file_array['name'] = basename($matches[0]);
    $file_array['tmp_name'] = $tmp;
    $attachid = media_handle_sideload($file_array, 0);
    if(is_wp_error($attachid)){
      @unlink($tmp);
      return false;
    }
    $upload_guid = wp_get_attachment_image_src($attachid, 'large');
    return array('attachid' => $attachid, 'orglink' => $imglink, 'wplink' => $upload_guid[0]);
  }

}