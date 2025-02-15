<?php

namespace poshtvan\app;

class admin_menu
{
    const TICKET_LIST_PAGE_SLUG = 'poshtvan';
    static function init()
    {
        self::ticket_menu();
        self::fields_menu();
        self::ai_menu();
        self::settings_menu();
    }
    static function settings_menu()
    {
        // handle caps
        $title = esc_html__('Settings', 'poshtvan');
        $settings_page = add_submenu_page('poshtvan', $title, $title, 'manage_options', 'poshtvan_settings', [__CLASS__, 'settings_c']);
        add_action("load-{$settings_page}", ['\poshtvan\app\assets', 'load_admin_settings_assets']);
        add_action('poshtvan_tab_ai_event', ['\poshtvan\app\providers\AiProviders\Hooshina', 'handle_return_from_ai']);
    }
    static function settings_c()
    {
        $menu_items = [
            'general' => [
                'title' => __('General', 'poshtvan'),
                'icon' => 'note',
            ],
            'tickets' => [
                'title' => __('Tickets', 'poshtvan'),
                'icon' => 'note',
            ],
            'notifications' => [
                'title' => __('Nofications', 'poshtvan'),
                'icon' => 'bell',
            ],
            'sms-notifications' => [
                'title' => __('Sms notifications', 'poshtvan'),
                'icon' => 'bell',
            ],
            'woocommerce' => [
                'title' => __('Woocommerce', 'poshtvan'),
                'icon' => 'shop',
            ],
            'ai' => [
                'title' => __('Artificial Intelligence Bot', 'poshtvan'),
                'icon' => 'hooshina',
            ],
        ];
        if (options::get_is_steps_mode_in_new_ticket()) {
            $menu_items['steps'] = ['title' => __('Steps', 'poshtvan'), 'icon' => 'steps'];
        }
        $menu_items['tools'] = [
            'title' => __('Tools', 'poshtvan'),
            'icon' => 'setting-4',
        ];
        $view = files::get_file_path('views.admin.settings.settings');
        $view ? include_once $view : null;
    }
    static function ticket_menu()
    {
        $title = __('Poshtvan', 'poshtvan');
        $new_ticket_counter = tickets::get_new_tickets_count();
        if ($new_ticket_counter) {
            $title = sprintf($title . '<span class="awaiting-mod" style="margin: 1px 8px">%d</span>', $new_ticket_counter);
        }
        $menuIcon = base64_encode(file_get_contents(POSHTVAN_DIR_PATH . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'admin-menu-logo.svg'));
        $ticket_list_page = add_menu_page($title, $title, roles::SUPPORTER_CAP_NAME, self::TICKET_LIST_PAGE_SLUG, [__CLASS__, 'ticket_c'], 'data:image/svg+xml;base64,' . $menuIcon, 25);
        add_submenu_page('poshtvan', $title, $title, roles::SUPPORTER_CAP_NAME, 'poshtvan', [__CLASS__, 'ticket_c'], -1);
        add_action("load-{$ticket_list_page}", ['\poshtvan\app\assets', 'load_admin_ticket_assets']);
    }
    static function ticket_c()
    {
        $view = files::get_file_path('views.admin.ticket.tickets');
        $view ? include $view : null;
    }
    static function fields_menu()
    {
        $title = esc_html__('Fields', 'poshtvan');
        $fields_menu_page = add_submenu_page('poshtvan', $title, $title, 'manage_options', 'poshtvan_fields', [__CLASS__, 'fields_c']);
        add_action("load-{$fields_menu_page}", ['\poshtvan\app\assets', 'admin_load_fields_menu_assets']);
    }
    public static function ai_menu()
    {
        $title = esc_html__('Artificial Intelligence', 'poshtvan');
        add_submenu_page('poshtvan', $title, $title, 'manage_options', 'admin.php?page=poshtvan_settings&tab=ai');
    }
    static function fields_c()
    {
        if (isset($_POST['new_item'])) {
            \poshtvan\app\form\fields::handle_new_field();
        }
        $view = files::get_file_path('views.admin.fields.fields');
        $view ? include_once $view : false;
    }

    static function handleToolsMenuSubmission()
    {
        if (isset($_POST['create_poshtvan_page'])) {
            $poshtvanPageArgs = array(
                'post_title' =>    __('Poshtvan', 'poshtvan'),
                'post_content' => '[poshtvan_list]',
                'post_status' => 'publish',
                'post_name' => 'poshtvan',
                'post_type' => 'page'
            );
            $pageID = wp_insert_post($poshtvanPageArgs);
            if ($pageID) {
                $link = get_permalink($pageID);
                $message = sprintf(__('Poshtvan page successfully created. You can %s and go to Poshtvan page.', 'poshtvan'), sprintf("<a target='_blank' href='%s'>%s</a>", esc_url($link), __('Click Here', 'poshtvan')));
                notice::add_notice('admin-panel-tools-menu', $message, 'success');
            } else {
                notice::add_notice('admin-panel-tools-menu', __("Poshtvan page cannot be create in your site, please create it manualy and put [poshtvan] shortcode as it's content.", 'poshtvan'), 'error');
            }
        }
        
        if(isset($_POST['create_poshtvan_database_tables']))
        {
            // create tickets table
            bootstrap::create_tickets_table();

            // create ticket meta table
            bootstrap::create_ticketmeta_table();

            // create fields table
            bootstrap::create_fields_table();

            notice::add_notice('admin-panel-tools-menu', __('The creation of database tables is finished', 'poshtvan'), 'success');
        }

        if(isset($_POST['delete_poshtvan_attachment_files']))
        {
            // delete all attachment files
            $poshtvanDeleteRes = file_uploader::deleteUploadDir();
            $mihanticketDeleteRes = file_uploader::deleteUploadDir('mihanticket');
            if(!$poshtvanDeleteRes && !$mihanticketDeleteRes)
            {
                notice::add_notice('admin-panel-tools-menu', __('No files were found to delete', 'poshtvan'));
            }else{
                notice::add_notice('admin-panel-tools-menu', __('All files have been deleted successfully', 'poshtvan'), 'success');
            }
        }
        
        if(isset($_POST['delete_poshtvan_all_tickets']))
        {
            // delete all tickets from poshtvan
            tickets::truncateTable();
            ticket_meta::truncateTable();
            notice::add_notice('admin-panel-tools-menu', __('Tickets has been successfully removed from Poshtvan', 'poshtvan'), 'success');
        }
    }
}
