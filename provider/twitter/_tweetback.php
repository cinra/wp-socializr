<?php

/* ----------------------------------------------------------
	
	参考用。消去すること
	
---------------------------------------------------------- */

/* Todo: 以下をcron処理に変更すること */

define('CRON_SCHEDULE_HANDLER', 'wp_import_tweetback');
define('WP_ZAMST_DEBUG', false);

// Add Schedules

$crons = _get_cron_array();
$enabled = false;
foreach ( $crons as $time => $tasks ) {
  foreach ( $tasks as $procname => $task )
  {
    if ($procname === CRON_SCHEDULE_HANDLER)
    {
      $enabled = true; break;
    }
  }
  if ($enabled) break;
}

if (!$enabled) wp_schedule_single_event(time(), CRON_SCHEDULE_HANDLER);

unset($tasks);
unset($crons);

add_action(CRON_SCHEDULE_HANDLER, 'import_tweetback');

#add_action('init', 'import_tweetback');

function import_tweetback()
{
  
  require_once('twitteroauth/twitteroauth.php');

  $tw = new Tweetback;
  
  $tw->oauth(
    array(
      'consumer_key' => 'yeInztgUrjRzVoTrIyrS6Q',
      'consumer_secret' => 'Obc1pISl99kb4zHmxqWINX7A1UrmE5Jlls7akRj5rms',
      'access_token' => '4977011-b4QNpI8UqTTMzsXnjQSvmMGVrs3ydKF6r3iwZGzZ90',
      'access_secret' => 'NHRqkL2Ttv9fgnLAAsrL1jrSF0nwALE3NfwCectzNo',
    )
  );
  
  $tw->import();
  
  // cron設定
  $time_interval = 1 * 60 * 60;
  wp_schedule_single_event(time() + $time_interval, CRON_SCHEDULE_HANDLER);
}


class Tweetback
{
  
  public $tw, $consumer_key, $consumer_secret, $access_token, $access_secret;
  
  function import()
  {
    
    global $wpdb;
    
    $baseurl = 'zamst.jp';
    
    $tweets = $this->tw->get('search/tweets', array('q' => $baseurl, 'count' => 100, 'include_entities' => true, 'result_type' => 'mixed'));
    
    foreach($tweets->statuses as $tweet)
    {
      foreach ($tweet->entities->urls as $urls)
      {
        if (preg_match('('.quotemeta($baseurl).'.*)', strtolower($urls->expanded_url)))
        {
          $url = $urls->expanded_url;
          if (WP_ZAMST_DEBUG) $url = 'http://zamst.local/bukatsu/event/%E3%80%90%E3%83%88%E3%83%AC%E3%83%BC%E3%83%8A%E3%83%BC%E6%B4%BE%E9%81%A3%E3%80%91%E7%A5%9E%E5%A5%88%E5%B7%9D%E7%9C%8C%E7%AB%8B%E5%8E%9A%E6%9C%A8%E6%9D%B1%E9%AB%98%E6%A0%A1-%E3%83%90%E3%82%B9%E3%82%B1/';
          $tweet_pid = 'tw_'.$tweet->id_str;
          
          $post_id = url_to_postid($url);
          
          if ($post_id > 1 && !$wpdb->get_var("SELECT comment_ID FROM $wpdb->comments WHERE comment_author_email = '$tweet_pid' AND comment_type = 'twitter'"))
          {
            wp_insert_comment(array(
              'comment_post_ID' => $post_id,
              'comment_author' => $tweet->user->screen_name,
              'comment_author_email' => $tweet_pid,
              'comment_author_url' => '',
              'comment_content' => $tweet->text,
              'comment_type' => 'twitter',
              'comment_parent' => 0,
              'user_id' => 0,
              'comment_author_IP' => '127.0.0.1',
              'comment_agent' => '',
              'comment_date' => date('Y-m-d H:i:s', strtotime($tweet->created_at)),
              'comment_approved' => 1,
            ));
          }
          
        }
        
      }
      
    }
    
  }
  
  function oauth($tokens = null)
  {
    
    if ($tokens) $this->set_tokens($tokens);
    
    $this->tw = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token, $this->access_secret);
    
  }
  
  function set_tokens($tokens = array())
  {
    
    foreach ($tokens as $key => $val) $this->$key = $val;
    
  }
  
}