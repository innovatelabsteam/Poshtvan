<?php

namespace poshtvan\app;

class config
{
    static function load_text_domain()
    {
        load_plugin_textdomain('poshtvan', false, POSHTVAN_LANG);
    }

    static function postTypes()
    {
        self::registerQuickResponsesPostType();
        faq::registerPostType();
    }
    static function registerQuickResponsesPostType()
    {
        register_post_type(
            'mihanticketresponse',
            array(
                'labels' => array(
                    'name' => __('Canned Responses', 'poshtvan'),
                    'singular_name' => __('Canned Response', 'poshtvan')
                ),
                'public' => false,
                'show_ui' => true,
                'has_archive' => false,
                'show_in_menu' => 'poshtvan'
            )
        );
    }
    static function filterNewTicketViewFile($viewFile)
    {
        if (options::get_is_steps_mode_in_new_ticket()) {
            $viewFile = \poshtvan\app\files::get_file_path('views.user.ticket.new-steps-mode');
        }
        return $viewFile;
    }

    static function handlePoshtvanHealthNotice()
    {
        $tables = [
            bootstrap::get_tickets_table_name(),
            bootstrap::get_ticketmeta_table_name(),
            bootstrap::get_ticket_fields_table_name(),
        ];

        $missingTables = [];

        global $wpdb;
        foreach ($tables as $table) {
            $command = "SELECT 1 FROM `{$table}` limit 1";
            if ($wpdb->query($command) === false) {
                $missingTables[] = $table;
            }
        }
        if (!$missingTables) {
            return false;
        }

        $missingTables = implode(', ', $missingTables);
        $message = sprintf(__('This tables are not exists in your database ( %s ), To avoid causing problems, you can recreate the deleted tables from the Poshtvan > Settings > Tools > Create Deleted Tables section.', 'poshtvan'), '<strong>' . $missingTables . '</strong>');
        $args['link'] = admin_url('admin.php?page=poshtvan_settings&tab=tools');
        notice::renderAdminNotice($message, $args);
    }
    static function filterTicketCustomStatusList($list)
    {
        $customStatus = options::getCustomTicketStatus();
        if ($customStatus && is_array($customStatus)) {
            foreach ($customStatus as $statusItem) {
                $list[$statusItem['slug']] = [
                    'name' => $statusItem['slug'],
                    'title' => $statusItem['name'],
                ];
            }
        }
        return $list;
    }
}
