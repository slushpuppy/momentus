<?php
/*
Plugin Name: Infinity
Plugin URI:
Description: Infinity is a plugin that gives you the power to build and post current, viral and trending content easily. You can find content through keyword search or RSS feeds.
Author: Rashvin Singh
Version: 1.0.23
Author URI:
*/


require __DIR__.'/lib/plugin-update-checker/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'http://licensebeast.com/update/blogxpress/blogxpressen.json',
    __FILE__,
    'plugineer-blogxpress'
);

define('pluginers_dir', plugin_dir_path(__FILE__));
define('pluginers_logfile', pluginers_dir.'log.txt');
define('pluginers_lib', pluginers_dir.'/lib');
define('pluginers_imgpath', plugins_url('/images', __FILE__));
define('pluginers_csspath', plugins_url('/css', __FILE__));
define('pluginers_jspath', plugins_url('/js', __FILE__));
define('SMPUBAPVERSION', 4);
define('pluginers_env', 'development');//development, production
define('pluginers_lang','en');
include(pluginers_dir.'/class.helper.php');
if (pluginers_lang == 'en') {
    include(pluginers_dir.'/class.controller.en.php');

} else {
    include(pluginers_dir.'/class.controller.php');

}
include(pluginers_dir.'/class.modules.php');
include(pluginers_dir.'/class.autoupdate.php');
include(pluginers_dir.'/class.crawlers.php');
include(pluginers_dir.'/class.publisher.php');

register_activation_hook(__FILE__, 'pluginers_install');
register_uninstall_hook(__FILE__, 'pluginers_uninstall');

add_action('init', 'pluginers_start');
add_action('wpmu_new_blog', 'pluginers_new_blog_installed', 99, 6);
add_filter('cron_schedules', array('pluginers_controller', 'register_cron'));

function pluginers_start(){
    $pluginers_controller = new pluginers_controller();
    $pluginers_crawler = new pluginers_crawlers();

    $pluginers_version = get_option('pluginers_version');
    if($pluginers_version != SMPUBAPVERSION){
        pluginers_upgrade($pluginers_version);
    }

    register_taxonomy('pluginers_cats', null, array('label' => 'Category', 'public' => false, 'rewrite' => false, 'hierarchical' => false));

    add_action('admin_menu', array($pluginers_controller, 'build_menus'), 99);
    add_action('admin_enqueue_scripts', 'pluginers_scripts');
    add_action('pluginers_cron_fewdays', array($pluginers_controller, 'check_update_notify'), 99);
    add_action('pluginers_crawler_cron', array($pluginers_crawler, 'start_crawling'), 99);
    add_action('pluginers_autopub_cron', array($pluginers_crawler, 'publish_auto_items'), 99);
    add_action('pluginers_social_counters', array($pluginers_crawler, 'social_counters'), 99);
    add_action('pluginers_clean_archive', array($pluginers_crawler, 'clean_archive'), 99);
    add_action('pluginers_cron_fewdays', array($pluginers_crawler, 'clean_query_sources'), 99);
}

function pluginers_scripts(){
    wp_register_script('pluginers-mainscript', pluginers_jspath.'/pluginers-function.js', array('jquery'), SMPUBAPVERSION);
    wp_register_script('pluginers-plugins', pluginers_jspath.'/pluginers-plugins.js', array('jquery','jquery-ui-widget'), SMPUBAPVERSION);
    wp_register_style('pluginers-mainstyle', pluginers_csspath.'/autoload-style.css', array(), SMPUBAPVERSION);
    wp_register_style('pluginers-style', pluginers_csspath.'/pluginers-style.css', array(), SMPUBAPVERSION);
    wp_enqueue_style('pluginers-mainstyle');
    if(is_rtl()){
        wp_register_style('pluginers-rtl', pluginers_csspath.'/pluginers-style-rtl.css', array(), SMPUBAPVERSION);
    }
    if(get_bloginfo('version') > 3.7){
        wp_register_style('pluginers-fix38', pluginers_csspath.'/autoload-style38.css', array(), SMPUBAPVERSION);
        wp_enqueue_style('pluginers-fix38');
    }
}

function pluginers_new_blog_installed($blog_id, $user_id, $domain, $path, $site_id, $meta) {
    pluginers_install($blog_id);
}

function pluginers_install($blog_id = false){
    if($blog_id !== false){
        switch_to_blog($blog_id);
    }
    if(get_option('pluginers_version') > 0){
        if($blog_id !== false){
            restore_current_blog();
        }
        return;
    }
    global $wpdb;
    $wpdb->hide_errors();
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $innodb = $wpdb->get_var('SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE="InnoDB"');
    if($innodb == 'NO'){
        $dbEngine = 'MyISAM';
    }
    else{
        $dbEngine = 'InnoDB';
    }
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."pub_autopilot_posts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `rssid` int(11) NOT NULL,
    `md5url` varchar(32) NOT NULL,
    `publishdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `tracker_update` int(8) NOT NULL,
    `autopub_date` varchar(15) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `md5url` (`md5url`)
  ) ENGINE=$dbEngine DEFAULT CHARSET=utf8;";
    dbDelta($sql);
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."pub_autopilot_posts_data` (
    `postid` int(11) NOT NULL,
    `rssid` int(11) NOT NULL,
    `url` text NOT NULL,
    `subject` varchar(300) NOT NULL,
    `content` text NOT NULL,
    `media` TEXT NOT NULL,
    PRIMARY KEY (`postid`)
  ) ENGINE=$dbEngine DEFAULT CHARSET=utf8;";
    dbDelta($sql);
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."pub_autopilot_posts_interaction` (
    `postid` int(11) NOT NULL,
    `rssid` int(11) NOT NULL,
    `twitter_share` int(11) NOT NULL,
    `fb_likes` int(11) NOT NULL,
    `fb_share` int(11) NOT NULL,
    `gplus_share` int(11) NOT NULL,
    `pinterest_share` int(11) NOT NULL,
    `fb_comments` int(11) NOT NULL,
    `disqus_comments` int(11) NOT NULL,
    `linkedin_share` int(11) NOT NULL,
    `rank` int(11) NOT NULL,
    PRIMARY KEY (`postid`)
  ) ENGINE=$dbEngine DEFAULT CHARSET=utf8;";
    dbDelta($sql);
    $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."pub_autopilot_rss` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `catid` int(11) NOT NULL,
    `engineid` tinyint(1) NOT NULL,
    `name` varchar(150) NOT NULL,
    `type` enum('rss','query') NOT NULL,
    `rsslink` TEXT NOT NULL,
    `query` varchar(100) NOT NULL,
    `field` tinyint(1) NOT NULL,
    `lang` varchar(15) NOT NULL,
    `country` varchar(15) NOT NULL,
    `date_range` varchar(20) NOT NULL,
    `read_status` tinyint(1) NOT NULL,
    `read_error` text NOT NULL,
    `items_count` int(11) NOT NULL,
    `category` varchar(255) NOT NULL,
    `tags` varchar(255) NOT NULL,
    `author_id` VARCHAR(100) NOT NULL,
    `post_type` varchar(20) NOT NULL,
    `post_type_tax` VARCHAR(20) NOT NULL,
    `post_status` varchar(20) NOT NULL,
    `post_pubfire` int(11) NOT NULL,
    `copyrights` tinyint(1) NOT NULL,
    `replace` TEXT NOT NULL,
    `truncate_limit` VARCHAR(50) NOT NULL,
    `truncate_replace` VARCHAR(200) NOT NULL,
    `schedule_time` TEXT NOT NULL,
    `autopublish` tinyint(1) NOT NULL,
    `template_id` INT NOT NULL,
    `featured_image` tinyint(1) NOT NULL,
    `ignore_public` tinyint(1) NOT NULL,
    `last_update` varchar(20) NOT NULL,
    `postdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `social_tracker` tinyint(1) NOT NULL,
    `smart_grabber` tinyint(1) NOT NULL,
    `smart_grabber_ai` BOOLEAN NOT NULL,
    `grabber_allow_tags` TEXT NOT NULL,
    `grabber_restrict` text NOT NULL,
    `rewrite` tinyint(1) NOT NULL,
    `rewrite_title` tinyint(1) NOT NULL,
    `alt_content` VARCHAR(20) NOT NULL,
    `ignore_nocontent` tinyint(1) NOT NULL,
    `ignore_noimages` tinyint(1) NOT NULL,
    `ignore_novideos` tinyint(1) NOT NULL,
    `original_date` tinyint(1) NOT NULL,
    `remove_links` tinyint(1) NOT NULL,
    `status` tinyint(1) NOT NULL,
    `amazon_products_position` TEXT,
    `amazon_tags` TEXT,
    `before_html` TEXT,
    `after_html` TEXT,
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
    dbDelta($sql);
    $setting = array(
        'purchase_code' => '',
        'fb_access_token' => '',
        'crawler_cron_time' => '1',
        'autopub_cron_time' => 0.5,
        'crawler_cron_time_old' => 0.5,
        'autopub_cron_time_old' => '1',
        'save_images_locally' => '0',
        'keep_archive' => '40',
        'data_per_page' => '50',
        'publish_post_type' => 'post',
        'publish_post_status' => 'draft',
        'publish_cats' => '',
        'publish_tags' => '',
        'publish_author_id' => '',
        'update_social_counter' => '1',
        'last_google_key' => 4,
        'google_keys' => array('','','','',''),
        'copyrights_string' => 'All copyrights for this article are reserved to {sourcelink}',
        'rewrite_provider' => ''
    );
    add_option('pluginers_options', $setting);
    add_option('pluginers_version', SMPUBAPVERSION);
    if($blog_id !== false){
        restore_current_blog();
    }
}

function pluginers_upgrade($version){
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    global $wpdb;
    $wpdb->hide_errors();
    if($version == 1.0){
        $setting = get_option('pluginers_options');
        $setting['crawler_cron_time_old'] = $setting['crawler_cron_time'];
        update_option('pluginers_options', $setting);
        $version = 1.1;
    }
    if($version == 1.1){
        $setting = get_option('pluginers_options');
        $setting['autopub_cron_time'] = 0.5;
        $setting['autopub_cron_time_old'] = 0;
        $setting['purchase_code'] = '';
        $setting['google_keys'] = array($setting['google_key'],'','','','');
        $setting['last_google_key'] = 4;
        update_option('pluginers_options', $setting);
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `featured_image` BOOLEAN NOT NULL AFTER `autopublish`");
        $version = 1.2;
    }
    if($version == 1.2){
        $setting = get_option('pluginers_options');
        $setting['rewrite_provider'] = '';
        update_option('pluginers_options', $setting);
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `rewrite` BOOLEAN NOT NULL AFTER `smart_grabber`, ADD `rewrite_title` BOOLEAN NOT NULL AFTER `rewrite`, ADD `ignore_noimages` BOOLEAN NOT NULL AFTER `rewrite_title`, ADD `ignore_novideos` BOOLEAN NOT NULL AFTER `ignore_noimages`, ADD `original_date` BOOLEAN NOT NULL AFTER `ignore_novideos`;");
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `engineid` BOOLEAN NOT NULL AFTER `catid`;");
        $wpdb->query("UPDATE `".$wpdb->prefix."pub_autopilot_rss` SET `engineid`='2'");
        $version = 2.0;
    }
    if($version == 2.0){
        $version = 2.1;
    }
    if($version == 2.1){
        $version = 2.2;
    }
    if($version <= 2.2){
        $version = 3.0;
    }
    if($version == 3.0){
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `grabber_restrict` TEXT NOT NULL AFTER `smart_grabber`;");
        $version = 3.1;
    }
    if($version == 3.1){
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `engineid` BOOLEAN NOT NULL AFTER `catid`;");
        $version = 3.2;
    }
    if($version == 3.2){
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `replace` TEXT NOT NULL AFTER `copyrights`, ADD `truncate_limit` VARCHAR(50) NOT NULL AFTER `replace`, ADD `truncate_replace` VARCHAR(200) NOT NULL AFTER `truncate_limit`;");
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `post_type_tax` VARCHAR(20) NOT NULL AFTER `post_type`;");
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `schedule_time` TEXT NOT NULL AFTER `truncate_replace`");
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `template_id` INT NOT NULL AFTER `autopublish`");
        $wpdb->query("UPDATE `".$wpdb->prefix."pub_autopilot_rss` SET `post_type_tax`='category'");
        $version = 3.3;
    }
    if($version == 3.3){
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` CHANGE `rsslink` `rsslink` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `author_id` VARCHAR(100) NOT NULL AFTER `tags`, ADD `alt_content` VARCHAR(20) NOT NULL AFTER `rewrite_title`;");
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_posts_data` ADD `media` TEXT NOT NULL ;");
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` CHANGE `last_update` `last_update` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
        $version = 3.4;
    }
    if($version == 3.4){
        $setting = get_option('pluginers_options');
        $setting['fb_access_token'] = '';
        update_option('pluginers_options', $setting);
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `remove_links` BOOLEAN NOT NULL AFTER `original_date`;");
        $version = 3.5;
    }
    if($version == 3.5){
        $version = 3.6;
    }
    if($version == 3.6){
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `ignore_nocontent` BOOLEAN NOT NULL AFTER `alt_content`");
        $version = 3.7;
    }
    if($version == 3.7){
        $version = 3.8;
    }
    if($version == 3.8){
        $wpdb->query("ALTER TABLE `".$wpdb->prefix."pub_autopilot_rss` ADD `smart_grabber_ai` BOOLEAN NOT NULL AFTER `smart_grabber`, ADD `grabber_allow_tags` TEXT NOT NULL AFTER `smart_grabber_ai`;");
        $wpdb->query("UPDATE `".$wpdb->prefix."pub_autopilot_rss` SET smart_grabber_ai='1',grabber_allow_tags='p , br , ul , li , a , img , strong , b , em , hr , i , ol , s , table , tbody , th , tr , td , u , dt , dl , strike , span , blockquote , dd , small , code , pre , tfoot , address , cite , h6 , h5 , h4 , h3 , h2 , h1'");
        $version = 4;
    }
    update_option('pluginers_version', $version);
}

function pluginers_uninstall(){
    global $wpdb;
    if(is_multisite()){
        $blogs = $wpdb->get_results("SELECT blog_id FROM $wpdb->blogs");
        if($blogs){
            foreach($blogs as $blog){
                switch_to_blog($blog->blog_id);
                pluginers_uninstall_code();
            }
            restore_current_blog();
        }
    }
    else{
        pluginers_uninstall_code();
    }
}

function pluginers_uninstall_code(){
    global $wpdb;
    $wpdb->hide_errors();
    $wpdb->query("DROP TABLE `".$wpdb->prefix."pub_autopilot_posts`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."pub_autopilot_posts_data`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."pub_autopilot_posts_interaction`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."pub_autopilot_rss`");
    delete_option('pluginers_options');
    delete_option('pluginers_version');
    wp_clear_scheduled_hook('pluginers_autopub_cron');
    wp_clear_scheduled_hook('pluginers_crawler_cron');
    wp_clear_scheduled_hook('pluginers_few_days');
    wp_clear_scheduled_hook('pluginers_social_counters');
    wp_clear_scheduled_hook('pluginers_clean_archive');
}