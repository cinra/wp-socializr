<?php
/*
Plugin Name: WP Socializr
Description: This Plugin measures up the influences of your WP site in social networks.
Version: 0.1
Plugin URI:
Author: HAMADA, Satoshi
Author URI: http://www.cinra.co.jp
License:
Text Domain: wp-socializr
*/

/*  Copyright 2014 HAMADA, Satoshi (email : tkcs@pelepop.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('WP_SOCIALIZR_DEBUG', /*false*/'http://comic.omocoro.jp' );

define('WP_SOCIALIZR_PREFIX', 'wp_socializr_');
define('WP_SOCIALIZR_INTERVAL', 1 * 60 * 60);

interface socializrTemplate
{

  public static function count($provider, $id = null);
  public static function get_comment($provider, $id = null);

}

class Crawler implements socializrTemplate {

  static $return;
  static $count;
  static $comments;

  public static function count($provider, $id = null)
  {

    if (self::$count) update_post_meta($id, $provider.'_count', self::$count);

  }

  public static function get_comment($provider, $id = null)
  {

    if (self::$comments)
    {

      global $wpdb;

      if (!is_array(self::$comments)) self::$comments = array(self::$comments);

      $meta_key = $provider.'_id';
      
      error_log($meta_key);

      foreach (self::$comments as $key => $comment)
      {

        $c = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM wp_commentmeta WHERE meta_key = %s AND meta_value = %s", $meta_key, $key));
        
        error_log($wpdb->prepare("SELECT count(*) FROM wp_commentmeta WHERE meta_key = %s AND meta_value = %s", $meta_key, $key));
        
        if ($c == 0)
        {
          
          error_log('new comment');
          
          $comment_id = wp_insert_comment($comment['comment']);
          if ($comment_id && isset($comment['meta']) && $comment['meta'])
          {
            foreach ($comment['meta'] as $k => $v)
            {
              add_comment_meta($comment_id, $k, $v);
            }
          }
        }
      }

      wp_update_comment_count($id);

    }
  }
}

class Socializr
{

  private $basepath;
  var     $providerPaths;

  function __construct()
  {

    $this->basepath = __DIR__;
    $this->providerPaths = glob($this->basepath.'/provider/*', GLOB_ONLYDIR);

  }

}

/* ----------------------------------------------------------

	Crawling

---------------------------------------------------------- */

define('CRON_SCHEDULE_HANDLER', 'wp_socializr_crawl');

// Add Schedules

$crons = _get_cron_array();
#echo '<pre>';echo time().'<br />';print_r($crons);echo '</pre>';exit;
$is_crawl = false;
foreach ($crons as $time => $tasks)
{
  foreach ($tasks as $procname => $task)
  {
    if ($procname === CRON_SCHEDULE_HANDLER)
    {
      $is_crawl = true;
      break;
    }
  }
  if ($is_crawl) break;
}

if (!$is_crawl) wp_schedule_single_event(time(), CRON_SCHEDULE_HANDLER);

unset($tasks);
unset($crons);

add_action(CRON_SCHEDULE_HANDLER, 'wp_socializr_crawl');

function wp_socializr_crawl()
{

  $s = new Socializr();

  $posts = get_posts(array(
    'posts_per_page' => 15,
    'order' => 'ASC',
    'orderby' => 'modified',
  ));

  if ($posts)
  {
    foreach ($posts as $post)
    {

      foreach ($s->providerPaths as $path)
      {
        
        $basename = basename($path);
        $classname = ucfirst($basename).'_Crawler';

        include_once($path.'/crawler.php');
        
        if (get_option(WP_SOCIALIZR_PREFIX.$basename.'_use_counter')) $classname::count($basename, $post->ID);
        if (get_option(WP_SOCIALIZR_PREFIX.$basename.'_use_comment')) $classname::get_comment($basename, $post->ID);

      }

      wp_update_post(array(
        'ID' => $post->ID,
      ));
    }
  }

  // cron設定
  $time_interval = get_option('wp_socializr_interval', WP_SOCIALIZR_INTERVAL);
  wp_schedule_single_event(time() + $time_interval, CRON_SCHEDULE_HANDLER);
}

#wp_socializr_crawl();

/* ----------------------------------------------------------

	get_social_count()

---------------------------------------------------------- */

if (!function_exists('get_social_count'))
{
  function get_social_count($provider, $id = null)
  {
    if (!$id)
    {
      global $post;
      $id = $post->ID;
    }

    $count = get_post_meta($id, $provider.'_count', true);

    return ($count) ? $count : 0;
  }

  function the_social_count($provider, $id = null)
  {
    echo get_social_count($provider, $id);
  }
}

/* ----------------------------------------------------------

	Admin Panel

---------------------------------------------------------- */

add_action('admin_menu', function() {

  add_options_page('WP Socializr', 'WP Socializr', 8, __FILE__, 'wp_socializr_admin');

});

function wp_socializr_admin()
{

$prefix = WP_SOCIALIZR_PREFIX;

$s = new Socializr();

if ($_POST['is_submit'] == 1)
{
  #echo '<pre>';print_r($_POST);echo '</pre>';exit;
  foreach ($_POST as $k => $v)
  {
    if (preg_match('(^'.$prefix.')', $k))
    {
      (get_option($k) !== false) ? update_option($k, $v) : add_option($k, $v, null, 'no');
    }
  }
}

echo '<div class="wrap">';

echo "<h2>WP Socializr</h2>";

?>

<form name="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
<input type="hidden" name="is_submit" value="1">

<table class="form-table">
  <tbody>
    <tr valign="top">
      <?php $key = $prefix.'interval'?>
      <th scope="row"><label for="<?php echo $key?>">クローリング間隔（秒）</label></th>
      <td><input name="<?php echo $key?>" type="text" id="<?php echo $key?>" value="<?php echo get_option($key, WP_SOCIALIZR_INTERVAL)?>" class="regular-text"></td>
    </tr>
  </tbody>
</table>

<hr>

<?php if ($s->providerPaths):foreach ($s->providerPaths as $path):?>
<?php include_once($path.'/admin.php')?>
<?php endforeach;endif?>

<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="変更を保存"></p>

</form>
</div>

<?php

}
