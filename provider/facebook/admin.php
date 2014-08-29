<?php

$prefix = WP_SOCIALIZR_PREFIX.'facebook_';

?>

<input type="hidden" name="<?php echo $prefix.'use_comment'?>" value="0">

<h3 class="title">Facebook</h3>

<table class="form-table">
  <tbody>
    <tr valign="top">
      <?php $key = $prefix.'use_counter'?>
      <th scope="row">いいね！数<br /><small>（Share＋Comment）</small></th>
      <td>
        <select id="<?php echo $key?>" name="<?php echo $key?>">
          <option value="0"<?php if(get_option($key, 0) == 0):?> selected='selected'<?php endif?>>取得しない</option>
          <option value="1"<?php if(get_option($key, 0) == 1):?> selected='selected'<?php endif?>>取得する</option>
        </select>
      </td>
    </tr>
    <?php /* tr valign="top">
      <?php $key = $prefix.'consumer_key'?>
      <th scope="row"><label for="<?php echo $key?>">Consumer Key</label></th>
      <td><input name="<?php echo $key?>" type="text" id="<?php echo $key?>" value="<?php echo get_option($key)?>" class="regular-text"></td>
    </tr>
    <tr valign="top">
      <?php $key = $prefix.'consumer_secret'?>
      <th scope="row"><label for="<?php echo $key?>">Consumer Secret</label></th>
      <td><input name="<?php echo $key?>" type="text" id="<?php echo $key?>" value="<?php echo get_option($key)?>" class="regular-text"></td>
    </tr>
    <tr valign="top">
      <?php $key = $prefix.'access_token'?>
      <th scope="row"><label for="<?php echo $key?>">Access Token</label></th>
      <td><input name="<?php echo $key?>" type="text" id="<?php echo $key?>" value="<?php echo get_option($key)?>" class="regular-text"></td>
    </tr>
    <tr valign="top">
      <?php $key = $prefix.'access_secret'?>
      <th scope="row"><label for="<?php echo $key?>">Access Secret</label></th>
      <td><input name="<?php echo $key?>" type="text" id="<?php echo $key?>" value="<?php echo get_option($key)?>" class="regular-text"></td>
    </tr */?>
  </tbody>
</table>

<hr />