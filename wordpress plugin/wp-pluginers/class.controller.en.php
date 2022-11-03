<?php

class pluginers_controller extends pluginers_helper{
  public static $settings;

  public function __construct(){
    $this->cron_setup();
    $this->get_plugin_setting();
  }

  public static function settings(){
    if($_POST){

      if(pluginers_env != 'demo'){
        self::saveOptions();
      }
    }
    else{
      wp_enqueue_script('suggest');
      self::loadpage('settings', 1);
    }
  }

  public static function loadpage($template, $noheader=0, $params=0){
    self::load_jsplugins();



/* Prints script in footer. This 'initialises' the meta boxes */

$noheader = ($noheader == 0)?'':'&noheader=1';
    $pageurl = admin_url().'admin.php?page=pluginers_'.$template.$noheader;
     if ($template == "settings") {
          if (pluginers_lang == 'en') {
              $template = "settings.en";
          }
      }
    include(pluginers_dir.'/pages/'.$template.'.php');
  }

  public static function load_jsplugins(){
    wp_enqueue_style('pluginers-style');
    if(is_rtl()){
      wp_enqueue_style('pluginers-rtl');
    }
    wp_enqueue_script('pluginers-mainscript');
    wp_enqueue_script('pluginers-plugins');
    wp_enqueue_script('postbox');
  }

  public static function saveOptions(){
    $newsetting = array();
    foreach($_POST AS $key=>$value){
      if(!in_array($key, array('submit','post_category'))){
        $newsetting[$key] = $value;
        unset(self::$settings[$key]);
      }
    }
    if(!empty($_POST['publish_author_id'])){
      $poster = get_user_by('login', $_POST['publish_author_id']);
      if(empty($poster)){
        self::jsonPrint(0, 'Wrong Author Username.');
      }
    }
    $checkbox = array('status','save_images_locally','update_social_counter','ignore_novideos','ignore_nocontent','ignore_noimages','original_date');
    foreach($checkbox AS $inptname){
      if(!isset($_POST[$inptname])){
        self::$settings[$inptname] = 0;
      }
      else{
        self::$settings[$inptname] = 1;
      }
    }
    if (!empty($_POST['post_category'])){
      $categories = array();
      foreach($_POST['post_category'] as $catid)
        $categories[] = $catid;
      $category = implode(',', $categories);
    }
    else{
      $category = '';
    }
    unset(self::$settings['publish_cats']);
    $newsetting['publish_cats'] = $category;
    self::$settings = array_map('wp_slash', self::$settings);
    self::$settings = array_merge($newsetting, self::$settings);

    update_option('pluginers_options', self::$settings);
    echo 1;
    die();
  }
  
  public function build_menus(){
    /*add_menu_page('Settings', 'Publisher APilot', 'delete_pages', 'pluginers_settings', array('pluginers_controller', 'settings'), 'div', 6);
    add_submenu_page('pluginers_settings', 'Manage Categories', 'Manage Categories', 'delete_pages', 'pluginers_manage_cats', array('pluginers_modules', 'manage_cats'));
    add_submenu_page('pluginers_settings', 'Manage Sources', 'Manage Sources', 'delete_pages', 'pluginers_manage_rss', array('pluginers_modules', 'manage_rss'));
    add_submenu_page('pluginers_settings', 'Manage RSS Content', 'RSS Content', 'delete_pages', 'pluginers_feeds_content', array('pluginers_modules', 'feeds_content'));
    add_submenu_page('pluginers_settings', 'Manage Internet Content', 'Internet Content', 'delete_pages', 'pluginers_internet_content', array('pluginers_modules', 'internet_content'));
    add_submenu_page('pluginers_settings', 'System Auto Update', 'Auto Update', 'delete_pages', 'pluginers_auto_update', array('pluginers_autoupdate', 'auto_update'));
    add_submenu_page('pluginers_settings', 'System Error Log', 'System Error Log', 'delete_pages', 'pluginers_error_log', array('pluginers_modules', 'error_log'));*/
      add_menu_page('Infinity', 'Infinity', 'delete_pages', 'pluginers_settings', array('pluginers_controller', 'settings'), 'dashicons-admin-site', 6);
      add_submenu_page('pluginers_settings', 'Settings', 'Settings', 'delete_pages', 'pluginers_settings', array('pluginers_controller', 'settings'));

      add_submenu_page('pluginers_settings', 'Campaign', 'Campaign', 'delete_pages', 'pluginers_manage_rss', array('pluginers_modules', 'manage_rss'));
      add_submenu_page(null, 'Campaign Types', 'Campaign Types', 'delete_pages', 'pluginers_manage_cats', array('pluginers_modules', 'manage_cats'));
      add_submenu_page('pluginers_settings', 'Setup RSS Post', 'Setup RSS Post', 'delete_pages', 'pluginers_feeds_content', array('pluginers_modules', 'feeds_content'));
      add_submenu_page('pluginers_settings', 'Setup Internet Post', 'Setup Internet Post', 'delete_pages', 'pluginers_search_content', array('pluginers_modules', 'search_feeds_content'));
      add_submenu_page('pluginers_settings', 'License', 'License', 'delete_pages', 'pluginers_license', array('pluginers_modules', 'license'));
      /*add_submenu_page('pluginers_settings', 'Tutorial', 'Tutorial', 'delete_pages', 'pluginers_training', array('pluginers_modules', 'training'));*/
      add_submenu_page('pluginers_settings', 'Support', 'Support', 'delete_pages', 'pluginers_support', array('pluginers_modules', 'support'));
      add_submenu_page('pluginers_settings', 'Logs', 'Logs', 'delete_pages', 'pluginers_logs', array('pluginers_modules', 'logs'));
  }

  public static function register_cron($schedules){
    if(empty(self::$settings['crawler_cron_time'])){
      self::$settings['crawler_cron_time'] = 1;
      self::$settings['autopub_cron_time'] = 0.5;
    }
    $schedules['pluginers_crawler_cron'] = array(
      'interval' => self::$settings['crawler_cron_time']*3600,
      'display' => __('Publisher Autopilot crawlers cron')
    );
    $schedules['pluginers_autopub_cron'] = array(
      'interval' => self::$settings['autopub_cron_time']*3600,
      'display' => __('Publisher Autopilot auto publish cron')
    );
    $schedules['pluginers_few_days'] = array(
      'interval' => 259200,
      'display' => __('Once every 3 days')
    );
    return $schedules;
  }

  public function cron_setup(){
    $settings = get_option('pluginers_options');
    if(! wp_next_scheduled('pluginers_crawler_cron')){
      wp_schedule_event(mktime(date('H'),date('i'),59,date('m'),date('d'),date('Y')), 'pluginers_crawler_cron', 'pluginers_crawler_cron');
	}
    elseif($settings['crawler_cron_time'] != $settings['crawler_cron_time_old']){
      wp_clear_scheduled_hook('pluginers_crawler_cron');
      $settings['crawler_cron_time_old'] = $settings['crawler_cron_time'];
      update_option('pluginers_options', $settings);
      wp_schedule_event(mktime(date('H'),date('i'),59,date('m'),date('d'),date('Y')), 'pluginers_crawler_cron', 'pluginers_crawler_cron');
    }
    if(! wp_next_scheduled('pluginers_autopub_cron')){
      wp_schedule_event(mktime(date('H'),date('i'),59,date('m'),date('d'),date('Y')), 'pluginers_autopub_cron', 'pluginers_autopub_cron');
	}
    elseif($settings['autopub_cron_time'] != $settings['autopub_cron_time_old']){
      wp_clear_scheduled_hook('pluginers_autopub_cron');
      $settings['autopub_cron_time_old'] = $settings['autopub_cron_time'];
      update_option('pluginers_options', $settings);
      wp_schedule_event(mktime(date('H'),date('i'),59,date('m'),date('d'),date('Y')), 'pluginers_autopub_cron', 'pluginers_autopub_cron');
    }
    if(! wp_next_scheduled('pluginers_cron_fewdays')){
      wp_schedule_event(mktime(15,0,0,date('m'),date('d'),date('Y')), 'pluginers_few_days', 'pluginers_cron_fewdays');
	}
    if(! wp_next_scheduled('pluginers_social_counters')){
      wp_schedule_event(mktime(0,0,0,date('m'),date('d'),date('Y')), 'hourly', 'pluginers_social_counters');
	}
    if(! wp_next_scheduled('pluginers_clean_archive')){
      wp_schedule_event(mktime(3,0,0,date('m'),date('d'),date('Y')), 'daily', 'pluginers_clean_archive');
	}
    if(get_transient('pluginers_update_notice') !== false){
      add_action('admin_notices', array('pluginers_controller', 'update_notice'));
    }
  }

  public function check_update_notify(){
    if(function_exists('curl_init')){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'http://smartiolabs.com/update/wp_publisher_autopilot');
      curl_setopt($ch, CURLOPT_REFERER, 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
      $data = json_decode(curl_exec($ch));
      curl_close($ch);
      if($data !== NULL){
        if($data->version > SMPUBAPVERSION){
          set_transient('pluginers_update_notice', $data, 60);
        }
      }
    }
  }

  public static function update_notice(){
    $data = get_transient('pluginers_update_notice');
    delete_transient('pluginers_update_notice');
    echo '<div class="update-nag"><p><a href="'.$data->link.'" target="_blank">'.$data->plugin.' '.$data->version.'</a> is available! Please update your system using the <a href="'.admin_url().'admin.php?page=pluginers_auto_update">auto update page</a>.</p></div>';
  }
  
  public function get_option($index){
    return self::$settings[$index];
  }

  public function get_plugin_setting(){
    self::$settings = get_option('pluginers_options');
    self::$settings = array_map('wp_unslash', self::$settings);
  }

}