<?php
namespace poshtvan\app;
class shortcodes
{
    static function init()
    {
        self::ticket_list();
    }
    static function ticket_list()
    {
        add_shortcode('mihanticket_list', [__CLASS__, 'ticket_list_c']);
        add_shortcode('poshtvan_list', [__CLASS__, 'ticket_list_c']);
    }
    static function ticket_list_c()
    {
        do_action('poshtvan/shortcode/before_ticket_list');
        if(!is_user_logged_in())
        {
            ?>
            <div class="poshtvan <?php echo is_rtl() ? 'poshtvan_rtl' : 'poshtvan_ltr';?>">
                <div class="alert error"><?php esc_html_e('You must logged-in first before can submit new ticket.', 'poshtvan')?></div>
            </div>
            <?php
            return false;
        }
        ob_start();
        $ticket_list_view = files::get_file_path('views.user.ticket.tickets');
        $ticket_list_view ? include $ticket_list_view : null;
        return ob_get_clean();
    }
}