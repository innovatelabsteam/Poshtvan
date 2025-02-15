<div class="inner-data">
    <div class="ticket-header">
        <div class="action-bar">
            <div class="action-btn back-btn"><?php esc_html_e('Back', 'poshtvan'); ?></div>
            <?php if($item->status != 3):?>
              <div class="action-btn solve-btn <?php echo $item->status == \poshtvan\app\tickets::STATUS_CODE_ANSWERED ? 'solved' : ''; ?>"><?php esc_html_e('Problem Solved', 'poshtvan') ?></div>
            <?php endif;?>
        </div>
        <div class="meta-data">
            <?php if(\poshtvan\app\options::get_is_show_wc_orders_field_in_user_ticket()):
                $product_title = \poshtvan\app\product::get_product_name(\poshtvan\app\ticket_meta::get_meta($item->id, 'woocommerce_order_id'));
                if($product_title):
                ?>
                    <span class="product-item">
                        <span class="label"><?php esc_html_e('Product', 'poshtvan')?></span>
                        <span class="value"><?php echo esc_html($product_title)?></span>
                    </span>
            <?php endif;endif; ?>
            <?php \poshtvan\app\form\fields::render_user_ticket_extra_fields($item->id)?>
        </div>
    </div>
    <div class="content">
      <div class="mihanticket-main-content">
      <div class="user-data">
          <?php
          $is_supporter = \poshtvan\app\users::is_supporter($item->user_id);
          $avatar = $is_supporter ? \poshtvan\app\users::get_operator_avatar()['url'] : get_avatar($item->user_id,50);
          $display_name = $is_supporter ? \poshtvan\app\users::get_operator_display_name($item->user_id) : \poshtvan\app\users::get_dsiplay_name($item->user_id);
          if($is_supporter){
            #TODO: check image size
            #TODO: check loading attr
            echo '<img alt="'.esc_attr($display_name).'" src="'.esc_url($avatar).'" class="avatar" height="120" width="120" loading="lazy">';
          } else {
            echo get_avatar($item->user_id,50);
          }
          ?>
          <span class="name"><?php echo esc_html($display_name); ?></span>
      </div>
        <div class="main-content-value">
            <p><?php echo wp_kses(nl2br($item->content), ['br' => []]); ?></p>
            <span class="date"><?php echo esc_html(\poshtvan\app\tools::getDate(strtotime($item->created_date)));?></span>
            <?php $attachment_base_dir = \poshtvan\app\ticket_meta::get_meta($item->id, 'attachment_name');
            if($attachment_base_dir):?>
            <div>
                <span class="attachment_file"><a target='_blank' href="<?php echo esc_url(\poshtvan\app\file_uploader::getUploadedFileUrl($attachment_base_dir));?>"><?php esc_html_e('Attachment File', 'poshtvan')?></a></span>
            </div>
            <?php endif; ?>
        </div>
      </div>
        <div class="replies"></div>
        <div class="new-reply">
            <?php 
			$enable_reply = \poshtvan\app\options::get_is_show_wc_orders_field_in_user_ticket() && \poshtvan\app\options::get_is_send_ticket_just_by_wc_order_item() ? \poshtvan\app\Ticket_Data::get_order_id($item->id) : true;
            if($enable_reply):
                $ticketOrderID = \poshtvan\app\Ticket_Data::get_order_id($item->id);
                $condition_checker = \poshtvan\app\Ticket_Conditions::condition_checker($ticketOrderID);
                if(!$condition_checker->error): 
                ?>
                <div class="file_field">
                    <input id="uploading_file" class="uploading_file" type="file" name="ticket_attachment">
                    <label for="uploading_file"><?php esc_html_e('Choose or drag your file here', 'poshtvan')?></label>
                    <span class="progress_bar"></span>
                </div>
                <textarea name="new_reply" id="new_reply_<?php echo intval($item->id); ?>" rows="5"></textarea>
                <span class="action-btn submit-reply"><?php esc_html_e('Submit Reply', 'poshtvan')?></span>
                <?php else: ?> 
                    <span class="alert alert-danger"><?php echo esc_html($condition_checker->message) ?></span>
                <?php endif; 
            else:
                \poshtvan\app\orders::render_orders_field(esc_attr__('Select the order related to this ticket again', 'poshtvan'), 'mihanticket-select-order-reply');
                ?>
                <span class="action-btn mihanticket-action-btn submit-ticket-order" data-ticket-id="<?php echo intval($item->id) ?>"><?php esc_html_e('Submit ticket order number', 'poshtvan')?></span>
            <?php endif; ?>
        </div>
    </div>
</div>
