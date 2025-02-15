<?php if($tickets && is_array($tickets)):
    foreach($tickets as $ticket_item):
        $status = \poshtvan\app\tickets::status_convert($ticket_item->status);?>
        <div class="item" mwtc_id="<?php echo esc_attr($ticket_item->id); ?>">
            <span class="status <?php echo esc_html($status['name']); ?>"></span>
            <span class="title"><?php echo intval($ticket_item->id) . ' - ' . esc_html(\poshtvan\app\tools::truncate($ticket_item->subject)); ?></span>
        </div>
<?php endforeach; endif; ?>