<?php

/* ----------------------------------------------------------
	
	Twitter Plugin for WP Socializr
	
---------------------------------------------------------- */

class Twitter_Crawler extends Crawler
{
  
  public static function get_comment($provider, $id = null)
  {
    
    if (!$id) return false;
    
    $url = (!defined('WP_SOCIALIZR_DEBUG')) ? get_permalink($id) : WP_SOCIALIZR_DEBUG;
    $url = str_replace(array('http://', 'https://'), '', $url);
    
    require_once('twitteroauth/twitteroauth.php');
    
    $tw = new TwitterOAuth(
      get_option(WP_SOCIALIZR_PREFIX.'twitter_consumer_key'), 
      get_option(WP_SOCIALIZR_PREFIX.'twitter_consumer_secret'), 
      get_option(WP_SOCIALIZR_PREFIX.'twitter_access_token'), 
      get_option(WP_SOCIALIZR_PREFIX.'twitter_access_secret')
    );
    
    $tweets = $tw->get('search/tweets', array(
      'include_entities' => true,
      'count' => 30,
      'q' => urlencode($url),
    ));
    
    if ($tweets)
    {
      self::$comments = array();
      $comment_approval = get_option(WP_SOCIALIZR_PREFIX.$provider.'_comment_approval', 0);
      foreach ($tweets->statuses as $tweet)
      {
        $comment_id = $tweet->id_str;
        self::$comments[$comment_id] = array(
          'comment' => array(
            'comment_post_ID' => $id,
            'comment_author' => $tweet->user->name,
            'comment_author_email' => $tweet->user->screen_name,
            'comment_author_url' => 'http://twitter.com/'.$tweet->user->screen_name,
            'comment_date' => date('Y-m-d H:i:s', strtotime($tweet->created_at)),
            'comment_content' => $tweet->text,
            'comment_approved' => $comment_approval,
            'comment_type' => $provider,
          ),
          'meta' => array(
            $provider.'_id' => $comment_id,
            $provider.'_profile_image_url' => $tweet->user->profile_image_url,
            $provider.'_profile_image_url_https' => $tweet->user->profile_image_url_https,
          ),
        );
      }
      #echo '<pre>';print_r(self::$comments);echo '</pre>';exit;
      
      parent::get_comment($provider, $id);
      
    }
    
    
    
  }
  
  public static function count($provider, $id = null)
  {
    
    if (!$id) return false;
    
    $url = (!defined('WP_SOCIALIZR_DEBUG')) ? get_permalink($id) : WP_SOCIALIZR_DEBUG;
    
    $tw = file_get_contents('http://urls.api.twitter.com/1/urls/count.json?url='.urlencode($url));
    
    self::$return = json_decode($tw);
    
    if (self::$return)
    {
      self::$count = (self::$return->count) ? self::$return->count : 0;
    }
    
    /*require_once('twitteroauth/twitteroauth.php');
    
    $tw = new TwitterOAuth(
      get_option(WP_SOCIALIZR_PREFIX.'twitter_consumer_key'), 
      get_option(WP_SOCIALIZR_PREFIX.'twitter_consumer_secret'), 
      get_option(WP_SOCIALIZR_PREFIX.'twitter_access_token'), 
      get_option(WP_SOCIALIZR_PREFIX.'twitter_access_secret')
    );
    
    $tweets = $tw->get('search/tweets', array(
      'include_entities' => true,
      #'count' => 10,
      'q' => urlencode($query),
    ));
    
    foreach ($tweets as $tweet)
    {
      
    }
    
    echo '<pre>';print_r($tweets);echo '</pre>';exit;*/
    
    parent::count($provider, $id);
    
  }
  
}