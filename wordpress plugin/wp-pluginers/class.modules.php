<?php

class pluginers_modules extends pluginers_controller {

  public static $wpdateformat;
  public static $settings;

  public function __construct() {
    parent::__construct();
  }
  
  public static function error_log() {
    if(!empty($_POST['clear'])){
      @unlink(pluginers_dir.'/cron_log.log');
    }
    $error_log = '';
    if(file_exists(pluginers_dir.'/cron_log.log')){
      $error_log = file_get_contents(pluginers_dir.'/cron_log.log');
    }
    include(pluginers_dir.'/pages/error_log.php');
  }
  
  public static function internet_content() {
    $_GET['type'] = 'query';
    self::feeds_content();
  }
  
  public static function feeds_content() {
    global $wpdb;
    self::load_jsplugins();
    $pagname = $_GET['page'];
    $pageurl = admin_url().'admin.php?page=pluginers_feeds_content';
    self::$wpdateformat = get_option('date_format').' '.get_option('time_format');
    if (isset($_GET['delete'])) {
      $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts WHERE id='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_data WHERE postid='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_interaction WHERE postid='$_GET[id]'");
      wp_redirect($_SERVER['HTTP_REFERER']);
      exit;
    }
    elseif (!empty($_GET['doaction']) || !empty($_GET['doaction2'])) {
      if(!empty($_GET['doaction2'])){
        $_GET['doaction'] = $_GET['doaction2'];
      }
      if(empty($_GET['ids'])){
        wp_redirect($_SERVER['HTTP_REFERER']);
      }
      $ids = implode(',', $_GET['ids']);


        if($_GET['doaction'] == 'publish'){

        $wp_publisher = new pluginers_wp_publisher();

        $wp_publisher->publish_now($ids,true);
      }
      elseif($_GET['doaction'] == 'delete'){
        $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts WHERE id IN ($ids)");
        $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_data WHERE postid IN ($ids)");
        $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_interaction WHERE postid IN ($ids)");
      }
      self::redirect($_SERVER['HTTP_REFERER']);
    }
    elseif (isset($_GET['quickview'])) {
      $feed = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."pub_autopilot_posts_data WHERE postid='$_GET[quickview]'", 'ARRAY_A');
      include(pluginers_dir.'/pages/feeds_quick_view.php');
      exit;
    }
    else {
      $where = array();
      $order = 'pub_autopilot_posts.id DESC';
      if(!empty($_GET['type'])){
        $where[] = $wpdb->prefix."pub_autopilot_rss.type='$_GET[type]'";
      }
      else{
        $_GET['type'] = 'rss';
        $where[] = $wpdb->prefix."pub_autopilot_rss.type='rss'";
      }
      $sources = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."pub_autopilot_rss WHERE type='$_GET[type]' ORDER BY name ASC");
      if(!empty($_GET['source'])){
        $where[] = $wpdb->prefix."pub_autopilot_rss.id='$_GET[source]'";
      }
      if(!empty($_GET['category'])){
        $sourceIDs = $wpdb->get_var("SELECT GROUP_CONCAT(id SEPARATOR ',') FROM ".$wpdb->prefix."pub_autopilot_rss WHERE catid='$_GET[category]'");
        $where[] = $wpdb->prefix."pub_autopilot_rss.id IN ($sourceIDs)";
      }
      if(!empty($_GET['date'])){
        $date = explode('to', $_GET['date']);
        $where[] = $wpdb->prefix."pub_autopilot_posts.publishdate BETWEEN '".trim($date[0])."' AND '".trim($date[1])."'";
      }
      if(!empty($_GET['query'])){
        $where[] = $wpdb->prefix."pub_autopilot_posts_data.subject LIKE '%$_GET[query]%'";
      }
      if(!empty($_GET['publish_status'])){
        switch($_GET['publish_status']){
          case 'not_published':
            $where[] = $wpdb->prefix.'pub_autopilot_posts.autopub_date IS NULL';
            break;
          case 'published':
            $where[] = $wpdb->prefix.'pub_autopilot_posts.autopub_date IS NOT NULL';
            break;
        }
      }
      if(!empty($_GET['sortby'])){
        switch($_GET['sortby']){
          case 'topranks':
            $order = 'pub_autopilot_posts_interaction.rank DESC';
            break;
          case 'fb_share':
            $order = 'pub_autopilot_posts_interaction.fb_share DESC';
            break;
          case 'gplus_share':
            $order = 'pub_autopilot_posts_interaction.gplus_share DESC';
            break;
          case 'twt_share':
            $order = 'pub_autopilot_posts_interaction.twitter_share DESC';
            break;
          case 'linked_share':
            $order = 'pub_autopilot_posts_interaction.linkedin_share DESC';
            break;
          case 'pinter_share':
            $order = 'pub_autopilot_posts_interaction.pinterest_share DESC';
            break;
          case 'fb_likes':
            $order = 'pub_autopilot_posts_interaction.fb_likes DESC';
            break;
          case 'fb_comments':
            $order = 'pub_autopilot_posts_interaction.fb_comments DESC';
            break;
          case 'disqus_comments':
            $order = 'pub_autopilot_posts_interaction.disqus_comments DESC';
            break;
        }
      }
      if(!empty($where)){
        $where = 'WHERE '.implode(' AND ', $where);
      }
      else{
        $where = '';
      }
      $_REQUEST['perpage'] = self::$settings['data_per_page'];
      $sql = self::Paging("SELECT ".$wpdb->prefix."pub_autopilot_posts.*,".$wpdb->prefix."pub_autopilot_posts_data.url,".$wpdb->prefix."pub_autopilot_posts_data.subject
      ,".$wpdb->prefix."pub_autopilot_posts_interaction.*,".$wpdb->prefix."pub_autopilot_rss.name AS source FROM ".$wpdb->prefix."pub_autopilot_posts
      INNER JOIN ".$wpdb->prefix."pub_autopilot_posts_data ON(".$wpdb->prefix."pub_autopilot_posts_data.postid=".$wpdb->prefix."pub_autopilot_posts.id)
      INNER JOIN ".$wpdb->prefix."pub_autopilot_posts_interaction ON(".$wpdb->prefix."pub_autopilot_posts_interaction.postid=".$wpdb->prefix."pub_autopilot_posts.id)
      INNER JOIN ".$wpdb->prefix."pub_autopilot_rss ON(".$wpdb->prefix."pub_autopilot_rss.id=".$wpdb->prefix."pub_autopilot_posts.rssid) $where
      ORDER BY ".$wpdb->prefix.$order);
      $feeds = $wpdb->get_results($sql);
      $paging_args = array(
      'base' => preg_replace('/&?callpage=([0-9]+)/', '', $_SERVER['REQUEST_URI']).'%_%',
      'format' => '&callpage=%#%',
      'total' => self::$paging['pages'],
      'current' => self::$paging['page'],
      'show_all' => false,
      'end_size' => 3,
      'mid_size' => 2,
      'prev_next' => true,
      'prev_text' => __('« Previous'),
      'next_text' => __('Next »')
      );
      add_thickbox();
      $cats = get_terms('pluginers_cats', array('hide_empty' => false));
      include(pluginers_dir.'/pages/feeds_content.php');
    }
  }

  public static function manage_rss() {
    global $wpdb;
    self::load_jsplugins();
    self::$wpdateformat = get_option('date_format').' '.get_option('time_format');
    $pageurl = admin_url().'admin.php?page=pluginers_manage_rss';
    $pagname = 'pluginers_manage_rss';
    if ($_POST) {
      if (empty($_POST['name']) || empty($_POST['engineid'])) {
        self::jsonPrint(0, 'Field name is required.');
      }
      if(!empty($_POST['author_id'])){
        $poster = get_user_by('login', $_POST['author_id']);
        if(empty($poster)){
          self::jsonPrint(0, 'Wrong Author Username.');
        }
      }
      $replace = array();
      if (is_array($_POST['replace']['search'])) {
          foreach($_POST['replace']['search'] as $key => $value){
              if(!empty($_POST['replace']['search'][$key]) && !empty($_POST['replace']['word'][$key])){
                  $replace['search'][$key] = $_POST['replace']['search'][$key];
                  $replace['word'][$key] = $_POST['replace']['word'][$key];
              }
          }
      }

      $checkboxes = array('status','copyrights','autopublish','featured_image','ignore_public','social_tracker','smart_grabber','original_date','rewrite','rewrite_title','ignore_nocontent','ignore_novideos', 'ignore_noimages', 'remove_links', 'smart_grabber_ai');
      foreach($checkboxes as $checkbox){
        if (!empty($_POST[$checkbox]))
          $_POST[$checkbox] = 1;
        else
          $_POST[$checkbox] = 0;
      }
      
      $categories = array();
      if(!empty($_POST['post_category'])){
        foreach($_POST['post_category'] as $catid){
          $categories[] = $catid;
        }
        $category = implode(',', $categories);
      }
      elseif(!empty($_POST['tax_input'])){
        foreach($_POST['tax_input'] as $tax){
          if (!empty($tax)){
            foreach($tax as $catid){
              $categories[] = $catid;
            }
            $category = implode(',', $categories);
          }
          else{
            $category = '';
          }
        }
      }
      else{
        $category = '';
      }


      $data = array(
      'catid' => (!isset($_POST['catid']))? '' : $_POST['catid'],
      'name' => (!isset($_POST['name']))? '' : $_POST['name'],
      'type' => (!isset($_POST['type']))? '' : $_POST['type'],
      'query' => (!isset($_POST['query']))? '' : $_POST['query'],
      'field' => (!isset($_POST['field']))? '' : $_POST['field'],
      'lang' => (!isset($_POST['lang']))? '' : $_POST['lang'],
      'country' => (!isset($_POST['country']))? '' : $_POST['country'],
      'date_range' => (!isset($_POST['date_range']))? '' : $_POST['date_range'],
      'rsslink' => (!isset($_POST['rsslink']))? '' : urlencode($_POST['rsslink']),
      'post_type_tax' => (!isset($_POST['post_custom_type_cat']))? '' : $_POST['post_custom_type_cat'],
      'post_type' => (!isset($_POST['post_custom_type']))? '' : $_POST['post_custom_type'],
      'post_status' => (!isset($_POST['post_status']))? '' : $_POST['post_status'],
      'post_pubfire' => (!isset($_POST['post_pubfire']))? '' : $_POST['post_pubfire'],
      'tags' => (!isset($_POST['tags']))? '' : $_POST['tags'],
      'grabber_allow_tags' => (!isset($_POST['grabber_allow_tags']))? '' : $_POST['grabber_allow_tags'],
      'author_id' => (!isset($_POST['author_id']))? '' : $_POST['author_id'],
      'status' => (!isset($_POST['status']))? '' : $_POST['status'],
      'alt_content' => (!isset($_POST['alt_content']))? '' : $_POST['alt_content'],
      'category' => $category,
      'replace' => (empty($replace))? '' : serialize($replace),
      'schedule_time' => ($_POST['post_status'] == 'schedule')? serialize($_POST['schedule_time']) : '',
      'truncate_limit' => (!isset($_POST['truncate_limit']))? '' : $_POST['truncate_limit'],
      'truncate_replace' => (!isset($_POST['truncate_replace']))? '' : $_POST['truncate_replace'],
      'copyrights' => (!isset($_POST['copyrights']))? '' : $_POST['copyrights'],
      'autopublish' => (!isset($_POST['autopublish']))? '' : $_POST['autopublish'],
      'featured_image' => (!isset($_POST['featured_image']))? '' : $_POST['featured_image'],
      'ignore_public' => (!isset($_POST['ignore_public']))? '' : $_POST['ignore_public'],
      'smart_grabber_ai' => (!isset($_POST['smart_grabber_ai']))? '' : $_POST['smart_grabber_ai'],
      'social_tracker' => (!isset($_POST['social_tracker']))? '' : $_POST['social_tracker'],
      'template_id' => (!isset($_POST['template_id']))? '' : $_POST['template_id'],
      'rewrite' => (!isset($_POST['rewrite']))? '' : $_POST['rewrite'],
      'rewrite_title' => (!isset($_POST['rewrite_title']))? '' : $_POST['rewrite_title'],
      'ignore_nocontent' => (!isset($_POST['ignore_nocontent']))? '' : $_POST['ignore_nocontent'],
      'ignore_novideos' => (!isset($_POST['ignore_novideos']))? '' : $_POST['ignore_novideos'],
      'ignore_noimages' => (!isset($_POST['ignore_noimages']))? '' : $_POST['ignore_noimages'],
      'engineid' => (!isset($_POST['engineid']))? '' : $_POST['engineid'],
      'original_date' => (!isset($_POST['original_date']))? '' : $_POST['original_date'],
      'remove_links' => (!isset($_POST['remove_links']))? '' : $_POST['remove_links'],
      'smart_grabber' => (!isset($_POST['smart_grabber']))? '' : $_POST['smart_grabber'],
      'grabber_restrict' => serialize(array('type' => $_POST['grabber_container'], 'name' => $_POST['grabber_element_type'], 'value' => $_POST['grabber_element_name'])),
          'before_html' => (!isset($_POST['before_html']))? '' : $_POST['before_html'],
          'after_html' => (!isset($_POST['after_html']))? '' : $_POST['after_html'],
      );


        if ($_POST['add_amazon_products'] && isset($_POST['add_amazon_products']))
      {
          $amazon_tags = array_filter(preg_split("/[\r\n]/si",$_POST['add_amazon_tags']));
          $data['amazon_products_position'] = $_POST['add_amazon_products_position'];
          $data['amazon_tags'] = json_encode($amazon_tags);
      } else {
            $data['amazon_products_position'] = '';
            $data['amazon_tags'] = '';
        }
      if(pluginers_env == 'demo'){
        $data['autopublish'] = 0;
      }
      if ($_POST['id'] > 0) {
        $wpdb->update($wpdb->prefix.'pub_autopilot_rss', $data, array('id' => $_POST['id']));
      }
      else {
        $wpdb->insert($wpdb->prefix.'pub_autopilot_rss', $data);
      }
      echo 1;
      exit;
    }
    elseif (isset($_GET['loadtaxs'])) {
      if(empty($_GET['smiopub_post_type'])){
        echo '';
        exit;
      }
      $html = '<option value=""></option>';
      $taxonomy_objects = get_object_taxonomies($_GET['smiopub_post_type'], 'objects');
      foreach ($taxonomy_objects as $type => $object){
        $html .= '<option value="'.$type.'">'.$type.'</option>';
      }
      echo $html;
      exit;
    }
    elseif (isset($_GET['loadcats'])) {
      if(empty($_GET['smiopub_object_name'])){
        echo '';
        exit;
      }
      wp_terms_checklist(0, array('taxonomy' => $_GET['smiopub_object_name']));
      exit;
    }
    elseif (isset($_GET['delete'])) {
      $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_rss WHERE id='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts WHERE rssid='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_data WHERE rssid='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_interaction WHERE rssid='$_GET[id]'");
      wp_redirect($pageurl);
    }
    elseif (isset($_GET['id'])) {
      if ($_GET['id'] == -1) {
        $allow_tags = 'p , br , ul , li , a , img , strong , b , em , hr , i , ol , s , table , tbody , th , tr , td , u , dt , dl , strike , span , blockquote , dd , small , code , pre , tfoot , address , cite , h6 , h5 , h4 , h3 , h2 , h1';
        $source = array('id' => 0, 'catid' => 0, 'name' => '', 'rsslink' => '', 'post_type' => 'post', 'post_type_tax' => 'category', 'post_status' => '', 'alt_content' => '', 'template_id' => '', 'type' => 'rss', 'query' => '', 'field' => '1', 'lang' => '', 'country' => '', 'date_range' => 'y1', 'category' => '', 'tags' => '', 'author_id' => '', 'copyrights' => 0, 'autopublish' => 0, 'featured_image' => 0, 'ignore_public' => 1, 'status' => 1, 'social_tracker' => 0, 'smart_grabber' => 0, 'rewrite' => 0, 'rewrite_title' => 0, 'rewrite_title' => 0, 'ignore_nocontent' => 0, 'ignore_novideos' => 0, 'ignore_noimages' => 0, 'engineid' => 0, 'original_date' => 0, 'post_pubfire' => 0, 'smart_grabber_ai' => 1, 'grabber_allow_tags' => $allow_tags, 'remove_links' => 0, 'grabber_container' => '', 'grabber_element_type' => '', 'grabber_element_name' => '', 'truncate_limit' => '', 'truncate_replace' => '');
      }
      else {
        $source = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."pub_autopilot_rss WHERE id='$_GET[id]'", 'ARRAY_A');
        $source = stripslashes_deep($source);
        $source['rsslink'] = urldecode($source['rsslink']);
        $source['schedule_time'] = unserialize($source['schedule_time']);
        if(empty($source['grabber_restrict'])){
          $source['grabber_container'] = '';
          $source['grabber_element_type'] = '';
          $source['grabber_element_name'] = '';
        }
        else{
          $restrict = unserialize($source['grabber_restrict']);
          $source['grabber_container'] = $restrict['type'];
          $source['grabber_element_type'] = $restrict['name'];
          $source['grabber_element_name'] = $restrict['value'];
        }
        $source['replace'] = unserialize($source['replace']);
      }
      /**
      if ($_POST['add_amazon_products'] && isset($_POST['add_amazon_products']))
      {
      $amazon_tags = array_filter(preg_split("/[\r\n]/si",$_POST['add_amazon_tags']));
      $data['amazon_products_position'] = $_POST['add_amazon_products_position'];
      $data['amazon_tags'] = json_encode($amazon_tags);
       **/
      if ($source['amazon_products_position'] != '') {
          $source['add_amazon_products'] = 1;
          $source['add_amazon_products_position'] = $source['amazon_products_position'];
         $tags = \json_decode($source['amazon_tags']);
         if ($tags != FALSE) {
             $source['add_amazon_tag'] = implode("\r\n",$tags);
         }
      }
        self::options();
        $has_amazon = (isset(self::$settings['amazon_access_key'],self::$settings['amazon_secret_key'],self::$settings['amazon_associate_tag']) && self::$settings['amazon_access_key'] != "" && self::$settings['amazon_secret_key'] != "" && self::$settings['amazon_associate_tag'] != "");
      $cats = get_terms('pluginers_cats', array('hide_empty' => false));
      include(pluginers_dir.'/pages/rss_form.php');
      exit;
    }
    else {
      $where = array();
      if(!empty($_GET['catid'])){
        $where[] = "catid='$_GET[catid]'";
      }
      if(!empty($_GET['read_status'])){
        $read_status = ($_GET['read_status'] == 'pending')? 0 : $_GET['read_status'];
        $where[] = "read_status='$read_status'";
      }
      if(!empty($_GET['query'])){
        $where[] = "name LIKE '%$_GET[query]%'";
      }
      if(!empty($_GET['status'])){
        $status = ($_GET['status'] == 'inactive')? 0 : $_GET['status'];
        $where[] = "status='$status'";
      }
      if(!empty($where)){
        $where = 'WHERE '.implode(' AND ', $where);
      }
      else{
        $where = '';
      }
      $sql = self::Paging("SELECT * FROM ".$wpdb->prefix."pub_autopilot_rss $where ORDER BY name ASC");
      $sources = $wpdb->get_results($sql);
      $cats = get_terms('pluginers_cats', array('hide_empty' => false));
      $paging_args = array(
      'base' => preg_replace('/&?callpage=([0-9]+)/', '', $_SERVER['REQUEST_URI']).'%_%',
      'format' => '&callpage=%#%',
      'total' => self::$paging['pages'],
      'current' => self::$paging['page'],
      'show_all' => false,
      'end_size' => 3,
      'mid_size' => 2,
      'prev_next' => true,
      'prev_text' => __('« Previous'),
      'next_text' => __('Next »')
      );
      wp_enqueue_script('suggest');
      include(pluginers_dir.'/pages/rss_manage.php');
    }
  }

  public static function manage_cats() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=pluginers_manage_cats';
    if ($_POST) {
      if (empty($_POST['name'])) {
        self::jsonPrint(0, 'Field name is required.');
      }
      if ($_POST['id'] > 0) {
        wp_update_term($_POST['id'], 'pluginers_cats', array('name' => $_POST['name'], 'description' => (!isset($_POST['description']))? '' : $_POST['description']));
      } else {
        wp_insert_term($_POST['name'], 'pluginers_cats', array('description' => (!isset($_POST['description']))? '' : $_POST['description']));
      }
      echo 1;
      exit;
    }
    elseif (isset($_GET['delete'])) {
      $wpdb->query("UPDATE ".$wpdb->prefix."pub_autopilot_rss SET catid='0' WHERE catid='$_GET[id]'");
      wp_delete_term($_GET['id'], 'pluginers_cats');
      wp_redirect($pageurl);
    }
    elseif (isset($_GET['id'])) {
      if ($_GET['id'] == -1) {
        $cat = new stdClass();
        $cat->name = '';
        $cat->description = '';
      }
      else {
        $cat = get_term($_GET['id'], 'pluginers_cats');
      }
      include(pluginers_dir.'/pages/cats_form.php');
      exit;
    }
    else {
      $cats = get_terms('pluginers_cats', array('hide_empty' => false));
      include(pluginers_dir.'/pages/cats_manage.php');
    }
  }

  public static function training () {
      include(pluginers_dir . 'pages/training.php');

  }
  public static function support() {
          if (pluginers_lang == 'en') {
              include(pluginers_dir . 'pages/support.en.php');
          } else
      include(pluginers_dir . 'pages/support.php');
  }
  public static function logs() {
      include(pluginers_dir . 'pages/logs.php');
  }
  public static function search_feeds_content() {
      global $wpdb;
      self::load_jsplugins();
      $pagname = $_GET['page'];
      $pageurl = admin_url().'admin.php?page=pluginers_search_content';
      self::$wpdateformat = get_option('date_format').' '.get_option('time_format');
      if (isset($_GET['delete'])) {
          $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts WHERE id='$_GET[id]'");
          $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_data WHERE postid='$_GET[id]'");
          $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_interaction WHERE postid='$_GET[id]'");
          wp_redirect($_SERVER['HTTP_REFERER']);
          exit;
      }
      elseif (!empty($_GET['doaction']) || !empty($_GET['doaction2'])) {
          if(!empty($_GET['doaction2'])){
              $_GET['doaction'] = $_GET['doaction2'];
          }
          if(empty($_GET['ids'])){
              wp_redirect($_SERVER['HTTP_REFERER']);
          }
          $ids = implode(',', $_GET['ids']);
          if($_GET['doaction'] == 'publish'){
              $wp_publisher = new pluginers_wp_publisher();
              $wp_publisher->publish_now($ids);
          }
          elseif($_GET['doaction'] == 'delete'){
              $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts WHERE id IN ($ids)");
              $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_data WHERE postid IN ($ids)");
              $wpdb->query("DELETE FROM ".$wpdb->prefix."pub_autopilot_posts_interaction WHERE postid IN ($ids)");
          }
          self::redirect($_SERVER['HTTP_REFERER']);
      }
      elseif (isset($_GET['quickview'])) {
          $feed = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."pub_autopilot_posts_data WHERE postid='$_GET[quickview]'", 'ARRAY_A');
          include(pluginers_dir.'/pages/feeds_quick_view.php');
          exit;
      }
      else {
          $where = array();
          $order = 'pub_autopilot_posts.id DESC';
          if(!empty($_GET['type'])){
              $where[] = $wpdb->prefix."pub_autopilot_rss.type='$_GET[type]'";
          }
          else{
              $_GET['type'] = 'query';
              $where[] = $wpdb->prefix."pub_autopilot_rss.type='query'";
          }
          $sources = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."pub_autopilot_rss WHERE type='$_GET[type]' ORDER BY name ASC");
          if(!empty($_GET['source'])){
              $where[] = $wpdb->prefix."pub_autopilot_rss.id='$_GET[source]'";
          }
          if(!empty($_GET['category'])){
              $sourceIDs = $wpdb->get_var("SELECT GROUP_CONCAT(id SEPARATOR ',') FROM ".$wpdb->prefix."pub_autopilot_rss WHERE catid='$_GET[category]'");
              $where[] = $wpdb->prefix."pub_autopilot_rss.id IN ($sourceIDs)";
          }
          if(!empty($_GET['date'])){
              $date = explode('to', $_GET['date']);
              $where[] = $wpdb->prefix."pub_autopilot_posts.publishdate BETWEEN '".trim($date[0])."' AND '".trim($date[1])."'";
          }
          if(!empty($_GET['query'])){
              $where[] = $wpdb->prefix."pub_autopilot_posts_data.subject LIKE '%$_GET[query]%'";
          }
          if(!empty($_GET['publish_status'])){
              switch($_GET['publish_status']){
                  case 'not_published':
                      $where[] = $wpdb->prefix.'pub_autopilot_posts.autopub_date IS NULL';
                      break;
                  case 'published':
                      $where[] = $wpdb->prefix.'pub_autopilot_posts.autopub_date IS NOT NULL';
                      break;
              }
          }
          if(!empty($_GET['sortby'])){
              switch($_GET['sortby']){
                  case 'topranks':
                      $order = 'pub_autopilot_posts_interaction.rank DESC';
                      break;
                  case 'fb_share':
                      $order = 'pub_autopilot_posts_interaction.fb_share DESC';
                      break;
                  case 'gplus_share':
                      $order = 'pub_autopilot_posts_interaction.gplus_share DESC';
                      break;
                  case 'twt_share':
                      $order = 'pub_autopilot_posts_interaction.twitter_share DESC';
                      break;
                  case 'linked_share':
                      $order = 'pub_autopilot_posts_interaction.linkedin_share DESC';
                      break;
                  case 'pinter_share':
                      $order = 'pub_autopilot_posts_interaction.pinterest_share DESC';
                      break;
                  case 'fb_likes':
                      $order = 'pub_autopilot_posts_interaction.fb_likes DESC';
                      break;
                  case 'fb_comments':
                      $order = 'pub_autopilot_posts_interaction.fb_comments DESC';
                      break;
                  case 'disqus_comments':
                      $order = 'pub_autopilot_posts_interaction.disqus_comments DESC';
                      break;
              }
          }
          if(!empty($where)){
              $where = 'WHERE '.implode(' AND ', $where);
          }
          else{
              $where = '';
          }
          $_REQUEST['perpage'] = self::$settings['data_per_page'];
          $sql = self::Paging("SELECT ".$wpdb->prefix."pub_autopilot_posts.*,".$wpdb->prefix."pub_autopilot_posts_data.url,".$wpdb->prefix."pub_autopilot_posts_data.subject
      ,".$wpdb->prefix."pub_autopilot_posts_interaction.*,".$wpdb->prefix."pub_autopilot_rss.name AS source FROM ".$wpdb->prefix."pub_autopilot_posts
      INNER JOIN ".$wpdb->prefix."pub_autopilot_posts_data ON(".$wpdb->prefix."pub_autopilot_posts_data.postid=".$wpdb->prefix."pub_autopilot_posts.id)
      INNER JOIN ".$wpdb->prefix."pub_autopilot_posts_interaction ON(".$wpdb->prefix."pub_autopilot_posts_interaction.postid=".$wpdb->prefix."pub_autopilot_posts.id)
      INNER JOIN ".$wpdb->prefix."pub_autopilot_rss ON(".$wpdb->prefix."pub_autopilot_rss.id=".$wpdb->prefix."pub_autopilot_posts.rssid) $where
      ORDER BY ".$wpdb->prefix.$order);
          $feeds = $wpdb->get_results($sql);
          $paging_args = array(
              'base' => preg_replace('/&?callpage=([0-9]+)/', '', $_SERVER['REQUEST_URI']).'%_%',
              'format' => '&callpage=%#%',
              'total' => self::$paging['pages'],
              'current' => self::$paging['page'],
              'show_all' => false,
              'end_size' => 3,
              'mid_size' => 2,
              'prev_next' => true,
              'prev_text' => __('« Previous'),
              'next_text' => __('Next »')
          );
          add_thickbox();
          $cats = get_terms('pluginers_cats', array('hide_empty' => false));
          include(pluginers_dir.'/pages/feeds_content.php');
      }
  }

    public static function log2($message, $newline=false) {
        if(pluginers_env == 'development'){
            $message = date('d/m/y H:i:s').' : '.$message;
            if($newline){
                $message .= "\n==============================================";
            }
            $message .= "\n";
            error_log($message, 3, pluginers_logfile);
        }
    }

    public static function license() {
        global $wpdb;
        self::load_jsplugins();
        self::options();
        $license_status = (self::$settings['license_key_status']) ? self::$settings['license_key_status'] :'Not Activated';
        $license_status_color = ($license_status == 'Activated') ? 'green':'red';
        if (isset(self::$settings['license_key']))
            $license_key = self::$settings['license_key'];
            else
        $license_key = '';
        $toActivate = true;
        if (!empty($_POST['license']))
        {
            $license_key = $_POST['license'];
            self::$settings['license_key'] = $license_key;
            $toActivate = true;
            if (self::$settings['license_key_usage_id'])
            {
                $result = wp_remote_get('http://licensebeast.com/engine/applications/nexus/interface/licenses/?check&key=' . $license_key . '&setIdentifier=' . md5(get_bloginfo('url') . $license_key) . '&usage_id=' . self::$settings['license_key_usage_id']);
                if (is_array($result))
                {
                    $json = json_decode($result['body']);

                    if ($json && $json->status == "ACTIVE")
                    {
                        echo 1;
                        die();
                    }
                    static::log2(print_r($result, TRUE) . '--' . 'http://licensebeast.com/engine/applications/nexus/interface/licenses/?check&key=' . $license_key . '&setIdentifier=' . md5(get_bloginfo('url') . $license_key) . '&usage_id=' . self::$settings['license_key_usage_id']);


                }
            }
                $result = wp_remote_get('http://licensebeast.com/engine/applications/nexus/interface/licenses/?activate&key=' . $license_key . '&setIdentifier=' . md5(get_bloginfo('url') . $license_key));
                if (!is_array($result) || is_wp_error($result))
                {
                    self::$settings['license_key'] = $license_key;
                    self::$settings['license_key_status'] = 'Not Activated';
                    echo "Error reaching validation server. Error:(" . $result->get_error_code() . ") " . $result->get_error_message();
                }
                $json = json_decode($result['body']);

                if ($json && $json->response == "OKAY")
                {
                    self::$settings['license_key_status'] = "Activated";
                    self::$settings['license_key_usage_id'] = $json->usage_id;
                    echo 1;
                }

                else
                {
                    static::log2($result. '--'.'http://licensebeast.com/engine/applications/nexus/interface/licenses/?activate&key=' . $license_key . '&setIdentifier=' . md5(get_bloginfo('url') . $license_key));
                    echo "Invalid License key";
                    self::$settings['license_key'] = $license_key;
                    self::$settings['license_key_status'] = 'Not Activated';
                    unset(self::$settings['license_key_usage_id']);
                }

                update_option('pluginers_options', self::$settings);

            die();
        }
        $pageurl = admin_url().'admin.php?page=pluginers_license';
        include(pluginers_dir . 'pages/license.php');
    }
    public static function options() {
        self::$settings = get_option('pluginers_options');
    }
}