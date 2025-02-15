<?php if($ticket_list && is_array($ticket_list)):
    foreach($ticket_list as $item):
        $status_data = \poshtvan\app\tickets::status_convert($item->status);
    ?>
        <div class="item status-<?php echo esc_attr($item->status); ?>" mwtc_id="<?php echo esc_attr($item->id); ?>">
            <div class="status <?php echo esc_attr($status_data['name']); ?>"><span><?php echo esc_html($status_data['title']); ?></span></div>
            <div class="ticket_id">#<?php echo intval($item->id)?></div>
            <div class="title"><?php echo esc_html($item->subject); ?></div>
            <?php
                $replies_view = \poshtvan\app\files::get_file_path('views.user.ticket.replies');
                $replies_view ? include $replies_view : null;
            ?>
        </div>
<?php endforeach; endif;?>
