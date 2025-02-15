<div class="mihanticket <?php echo is_rtl() ? 'mwtc_rtl' : 'mwtc_ltr';?>">
    <?php
    $new_ticket_view = apply_filters('poshtvan/views/new_ticket', \poshtvan\app\files::get_file_path('views.user.ticket.new'));
    $new_ticket_view ? include $new_ticket_view : null;
    ?>
    <div class="mihanticket-alerts-wrapper"></div>
    <div class="mihanticket-list">
        <div class="items"><p class="mihanticket-wait"><?php esc_html_e('Please Wait...', 'poshtvan')?></p></div>
    </div>
    <div mwtc_offset="0" class="load-more"><span class="action"><?php esc_html_e('Load More', 'poshtvan')?></span></div>
</div>
