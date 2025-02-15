<?php if($replies && is_array($replies)):
    foreach($replies as $reply_item):
      $is_supporter = \poshtvan\app\users::is_supporter($reply_item->user_id);
      $avatar = $is_supporter ? \poshtvan\app\users::get_operator_avatar()['url'] : get_avatar($reply_item->user_id,50);
      $display_name = $is_supporter ? \poshtvan\app\users::get_operator_display_name($reply_item->user_id) : \poshtvan\app\users::get_dsiplay_name($reply_item->user_id);
      ?>
      <div class="mihanticket-replies <?php if($is_supporter){ echo 'other';} else { echo 'own';} ?>">
      <div class="user-data <?php if($is_supporter){ echo 'other';} else { echo 'own';} ?>">
          <?php
          if($is_supporter){
            #TODO: check image size
            #TODO: check loading attr
            echo '<img alt="'.esc_attr($display_name).'" src="'.esc_url($avatar).'" class="avatar" height="120" width="120" loading="lazy">';
          } else {
            echo get_avatar($reply_item->user_id,50);
          }
          ?>
          <span class="name"><?php echo esc_html($display_name);?></span>
      </div>
        <div class="item <?php if($is_supporter){ echo 'other';} else { echo 'own';} ?>">
            <?php if($is_supporter):?>
              <p><?php echo esc_html(\poshtvan\app\options::get_replies_prefix_text())?></p>
            <?php endif; ?>
            <p><?php echo wp_kses(nl2br($reply_item->content), ['br' => []]); ?></p>
            <?php if($is_supporter):?>
              <p><?php echo esc_html(\poshtvan\app\options::get_replies_suffix_text())?></p>
            <?php endif; ?>
            <?php $attachment_base_dir = \poshtvan\app\ticket_meta::get_meta($reply_item->id, 'attachment_name');
            if($attachment_base_dir):?>
              <span class="attachment_file"><a target='_blank' href="<?php echo esc_url(\poshtvan\app\file_uploader::getUploadedFileUrl($attachment_base_dir));?>"><?php esc_html_e('Attachment File', 'poshtvan')?></a></span>
            <?php endif; ?>
            <span class="date"><?php echo esc_html(\poshtvan\app\tools::getDate(strtotime($reply_item->update_date))); ?></span>
        </div>
      </div>
<?php endforeach; endif; ?>
