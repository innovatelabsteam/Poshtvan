<div class="item-content" mwtc_item="<?php echo esc_attr($ticket_data->id); ?>">
    <div class="topbar">
        <a target="blank" class="user-email"><?php echo esc_html(\poshtvan\app\users::get_email($ticket_data->user_id));?></a>
        <div class="status">
            <select name="ticket-status">
                <?php
                $ticket_status = \poshtvan\app\tickets::get_status_list();
                $cu_status  = \poshtvan\app\tickets::status_convert($ticket_data->status);
                $cu_status = isset($cu_status['name']) ? $cu_status['name'] : false;
                foreach($ticket_status as $status):
                ?>
                    <option <?php selected($cu_status, $status['name'])?> value="<?php echo esc_attr($status['name']); ?>"><?php echo esc_html($status['title']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="ticket_url">
            <span mwtc_ticket_url="<?php echo esc_url(add_query_arg(['page' => \poshtvan\app\admin_menu::TICKET_LIST_PAGE_SLUG, 'ticket_id' => $ticket_data->id], admin_url('admin.php')));?>"><?php esc_html_e('Copy ticket URL', 'poshtvan')?></span>
        </div>
        <div class="solve"><span><?php esc_html_e('Problem Solved', 'poshtvan'); ?></span></div>
    </div>
    <div class="ticket-content-wrapper">
        <?php do_action('poshtvan/admin/start_ticket_content', $ticket_data->id, $ticket_data); ?>
        <?php if(\poshtvan\app\options::get_is_show_wc_orders_field_in_user_ticket()):?>
            <?php
                $ticketOrderID = \poshtvan\app\Ticket_Data::get_order_id($ticket_data->id);
                $ticketOrderProductID = \poshtvan\app\Ticket_Data::get_product_id($ticket_data->id);
            ?>
            <div class="product-item">
                <span class="label"><?php esc_html_e('This ticket submitted for:', 'poshtvan')?></span>
                <span class="value"><?php echo intval(\poshtvan\app\product::get_product_name($ticketOrderProductID)) . ' ' . intval($ticketOrderProductID)?></span>
            </div>
            <?php
            $condition_checker = \poshtvan\app\Ticket_Conditions::condition_checker($ticketOrderID);
            if($condition_checker->error):
            ?>
            <div class="ticket-product-notice">
                <span><?php echo esc_html($condition_checker->message) ?></span>
            </div>
            <?php endif; ?>
            <?php if(\poshtvan\app\orders::is_order_accessible()): ?>
                <div class="order-list">
                    <span class="label"><?php esc_html_e('User Orders', 'poshtvan')?></span>
                    <?php
                    $orders = \poshtvan\app\orders::get_orders($ticket_data->user_id);
                    $selectedOrderItemOption = $ticketOrderID . '-' . $ticketOrderProductID;
                    if($orders):?>
                        <select>
                        <?php foreach($orders as $orderID => $orderItems): ?>
                            <?php foreach($orderItems as $value): ?>
                                <?php if(intval($orderID) && (is_array($value) && isset($value['title']) && isset($value['product_id']))): ?>
                                    <option <?php selected($selectedOrderItemOption, $orderID . '-' . $value['product_id'])?> value="<?php echo intval($orderID) . '-' . intval($value['product_id'])?>" data-product-id="<?php echo intval($value['product_id']) ?>"><?php echo esc_html($value['title']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        </select>
                    <?php endif;?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php \poshtvan\app\form\fields::render_admin_ticket_extra_fields($ticket_data->id) ?>
        <div class="ticket">
            <div class="user-data">
                <span class="date"><?php echo esc_html(\poshtvan\app\tools::getDate(strtotime($ticket_data->created_date))); ?></span>
            </div>
            <span class="ticket-title"><?php printf('<b>%s: </b>', esc_html__('Title', 'poshtvan')); echo isset($ticket_data->subject) ? esc_html($ticket_data->subject) : false;?></span>
            <span class="ticket-content"><?php echo isset($ticket_data->content) ? wp_kses(nl2br($ticket_data->content), ['br' => []])  : false;?></span>
            <?php $attachment_base_dir = \poshtvan\app\ticket_meta::get_meta($ticket_data->id, 'attachment_name');
            if($attachment_base_dir):?>
            <div>
                <span class="attachment_file"><a target='_blank' href="<?php echo esc_url(\poshtvan\app\file_uploader::getUploadedFileUrl($attachment_base_dir));?>"><?php esc_html_e('Attachment File', 'poshtvan')?></a></span>
            </div>
            <?php endif; ?>
        </div>
        <div class="replies">
            <?php if(isset($replies) && is_array($replies)):
                foreach($replies as $reply_item):
                    $is_supporter = \poshtvan\app\users::is_supporter($reply_item->user_id);
                ?>
                    <div reply-id="<?php echo intval($reply_item->id); ?>" class="item <?php if($is_supporter){ echo 'other';} else { echo 'own';}?>">
                        <div class="user-data">
                            <span class="display-name"><?php echo esc_html(\poshtvan\app\users::get_dsiplay_name($reply_item->user_id))?></span>
                            <span class="date"><?php echo esc_html(\poshtvan\app\tools::getDate(strtotime($reply_item->update_date))); ?></span>
                            <?php if(\poshtvan\app\tickets::get_ticket_owner($reply_item->id) == get_current_user_id()):?>
                                <span class="edit-ticket"><?php esc_html_e('Edit', 'poshtvan')?></span>
                            <?php endif; ?>
                        </div>
                        <?php if($is_supporter): ?>
                        <p><?php echo esc_html(\poshtvan\app\options::get_replies_prefix_text())?></p>
                        <?php endif; ?>
                        <p class="ticket-content-text"><?php echo wp_kses(nl2br($reply_item->content), ['br' => []]); ?></p>
                        <?php if($is_supporter): ?>
                        <p><?php echo esc_html(\poshtvan\app\options::get_replies_suffix_text())?></p>
                        <?php endif; ?>
                        <?php $attachment_base_dir = \poshtvan\app\ticket_meta::get_meta($reply_item->id, 'attachment_name');
                        if($attachment_base_dir):?>
                            <span class="attachment_file"><a target='_blank' href="<?php echo esc_url(\poshtvan\app\file_uploader::getUploadedFileUrl($attachment_base_dir));?>"><?php esc_html_e('Attachment File', 'poshtvan')?></a></span>
                        <?php endif; ?>
                    </div>
            <?php endforeach; endif; ?>
        </div>
        <div class="new-reply">
            <div class="file_field">
                <input type="file" name="ticket_attachment">
            </div>
          <div class="mihanticket-responses">
            <?php 
            $the_resquery = new WP_Query(
                array(
                    'post_type'=> 'mihanticketresponse',
                    'posts_per_page' => -1
                )
            );
            if($the_resquery->have_posts() ) : 
                while ( $the_resquery->have_posts() ) : 
                    $the_resquery->the_post(); 
                    ?>
                    <div class="mihanticket-response">
                        <span class="title"><?php the_title();?></span>
                        <div class="content"><?php echo esc_html(wp_strip_all_tags( get_the_content() ));?></div>
                    </div>
                    <?php
                endwhile; 
            wp_reset_postdata(); 
            else: 
            endif;
            ?>
            </div>
            <textarea id="replytextareaid" name="submit-new-reply" rows="5"></textarea>
            <span class="action-btn submit-reply"><?php esc_html_e("Submit Reply", 'poshtvan')?></span>
        </div>
    </div>
</div>