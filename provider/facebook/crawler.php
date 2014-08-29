<?php

/* ----------------------------------------------------------
	
	Facebook Plugin for WP Socializr
	
---------------------------------------------------------- */

class Facebook_Crawler extends Crawler
{
  
  public static function count($provider, $id = null)
  {
    
    if (!$id) return false;
    
    $url = (!defined('WP_SOCIALIZR_DEBUG')) ? get_permalink($id) : WP_SOCIALIZR_DEBUG;
    
    $fb = file_get_contents('http://graph.facebook.com/'.urlencode($url));
    
    self::$return = json_decode($fb);
    
    if (self::$return)
    {
      self::$count = 0;
      if (self::$return->shares) self::$count += self::$return->shares;
      if (self::$return->comments) self::$count += self::$return->comments;
    }
    
    parent::count($provider, $id);
    
  }
  
}