<?php

/* ----------------------------------------------------------
	
	Hatena Plugin for WP Socializr
	
---------------------------------------------------------- */

class Hatena_Crawler extends Crawler
{
  
  public static function get_comment($provider, $id = null)
  {
    
    if (!$id) return false;
    
    $url = (!defined('WP_SOCIALIZR_DEBUG')) ? get_permalink($id) : WP_SOCIALIZR_DEBUG;
    #$url = str_replace(array('http://', 'https://'), '', $url);
    
    $api_url = 'http://b.hatena.ne.jp/entry/jsonlite/?url=';
    $api_url .= urlencode($url);
    
    $result = json_decode(file_get_contents($api_url));
    
    #echo '<pre>';print_r($result);echo '</pre>';exit;
    
    if ($result && isset($result->bookmarks))
    {
      self::$comments = array();
      $comment_approval = get_option(WP_SOCIALIZR_PREFIX.$provider.'_comment_approval', 0);
      foreach ($result->bookmarks as $bm)
      {
        if ($bm->comment != '')
        {
          $comment_id = $bm->user.'_'.strtotime($bm->timestamp);
          self::$comments[$comment_id] = array(
            'comment' => array(
              'comment_post_ID' => $id,
              'comment_author' => $bm->user,
              'comment_author_email' => $bm->user,
              'comment_author_url' => 'http://b.hatena.ne.jp/'.$bm->user,
              'comment_date' => date('Y-m-d H:i:s', strtotime($bm->timestamp)),
              'comment_content' => $bm->comment,
              'comment_approved' => $comment_approval,
              'comment_type' => $provider,
            ),
            'meta' => array(
              $provider.'_id' => $comment_id,
            ),
          );
        }
      }
      
      parent::get_comment($provider, $id);
      
    }
    
  }
  
  public static function count($provider, $id = null)
  {
    
    if (!$id) return false;
    
    $url = (!defined('WP_SOCIALIZR_DEBUG')) ? get_permalink($id) : WP_SOCIALIZR_DEBUG;
    
    $hb = file_get_contents('http://api.b.st-hatena.com/entry.count?url='.urlencode($url));
    
    self::$count = json_decode($hb);
    
    parent::count($provider, $id);
    
  }
  
}