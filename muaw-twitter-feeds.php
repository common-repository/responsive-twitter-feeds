<?php
/*
Plugin Name: Responsive Twitter Feeds
Plugin URI: http://makeusawebsite.lk/mplugins/responsive-twitter-feeds/
Description: A plugin to show latest tweets on your wordpress website.
Version: 1.0
Author: Ajith K Ranatunga
Author URI: http://www.makeusawebsite.lk/author/ajithkranatunga/
License: GPL2
Text Domain: muawtw
have used https://github.com/J7mbo/twitter-api-php/blob/master/TwitterAPIExchange.php
*/
if(!class_exists('MuawTsSettingsPage')){
    require_once('MuawTsSettingsPage.php');
}
if(!class_exists('TwitterAPIExchange')) {
    require_once('TwitterAPIExchange.php');
}
if(!class_exists('MUAW_TweetGenerator')) {
    require_once('MUAW_TweetGenerator.php');
}

function muaw_twf_styles(){
    /** default twitter feed style */
    wp_enqueue_style('muaw_twf_style', plugins_url( '/css/style.css', __FILE__ ));
}
add_action('wp_enqueue_scripts', 'muaw_twf_styles');

if( is_admin() )
    $muts_settings_page = new MuawTsSettingsPage();

function muaw_show_twitter_posts(){
    $message = '';
    $options  = get_option('muts_option');
    $user = $options['twitter_username'];
    $tw_count = $options['no_of_posts_to_show'];
    $settings = array(
        'oauth_access_token' => $options['muts_access_token'],
        'oauth_access_token_secret' => $options['muts_access_token_secret'],
        'consumer_key' => $options['muts_consumer_key'],
        'consumer_secret' => $options['muts_consumer_secret']
    );
    if($settings['oauth_access_token']=='' || $settings['oauth_access_token_secret']=='' || $settings['consumer_key']=='' || $settings['consumer_secret']=='' ){
        $message = 'Authentication Failed, Check your settings...';
    }
    if($user == ''){
        $message = 'Twitter Username Has Not Set...';
    }
    if($tw_count=='' || $tw_count == 0){
        $tw_count = 5;
    }
    $tg = new MUAW_TweetGenerator();
    $tw_string = $tg->muawtw_get_tweets($settings, $user, $tw_count, false, $message);

    echo $tw_string;
}

add_shortcode('muaw_twitter_tweets', 'muaw_show_twitter_posts');