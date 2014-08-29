<?php

$prefix = WP_SOCIALIZR_PREFIX.'hatena_';

?>

<h3 class="title">Hatena Bookmark</h3>

<table class="form-table">
  <tbody>
    <tr valign="top">
      <?php $key = $prefix.'use_counter'?>
      <th scope="row">はてブ数</th>
      <td>
        <select id="<?php echo $key?>" name="<?php echo $key?>">
          <option value="0"<?php if(get_option($key, 0) == 0):?> selected='selected'<?php endif?>>取得しない</option>
          <option value="1"<?php if(get_option($key, 0) == 1):?> selected='selected'<?php endif?>>取得する</option>
        </select>
      </td>
    </tr>
    <tr valign="top">
      <?php $key = $prefix.'use_comment'?>
      <th scope="row">はてブコメント</th>
      <td>
        <select id="<?php echo $key?>" name="<?php echo $key?>">
          <option value="0"<?php if(get_option($key, 0) == 0):?> selected='selected'<?php endif?>>取得しない</option>
          <option value="1"<?php if(get_option($key, 0) == 1):?> selected='selected'<?php endif?>>取得する</option>
        </select>
      </td>
    </tr>
    <tr valign="top">
      <?php $key = $prefix.'comment_approval'?>
      <th scope="row">コメントを即時反映</th>
      <td>
        <select id="<?php echo $key?>" name="<?php echo $key?>">
          <option value="0"<?php if(get_option($key, 0) == 0):?> selected='selected'<?php endif?>>しない</option>
          <option value="1"<?php if(get_option($key, 0) == 1):?> selected='selected'<?php endif?>>する</option>
        </select>
      </td>
    </tr>
  </tbody>
</table>

<hr />