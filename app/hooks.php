<?php
namespace poshtvan\app;

class hooks
{
    static function init()
    {
        register_activation_hook(POSHTVAN_APP, [__CLASS__, 'activation_hook']);
        register_deactivation_hook(POSHTVAN_APP, [__CLASS__, 'deactivation_hook']);
        add_action('admin_init', [__CLASS__, 'handlePoshtvanFreeVersionProcess']);
        add_action('plugins_loaded', ['\poshtvan\app\bootstrap', 'handle_db_table_creation']);

        if(\poshtvan\app\product::is_accessible()){
            add_action('admin_init', ['\poshtvan\app\Woo_Product_Data_Custom_Field', 'init']);
        }
        add_action('admin_notices', ['\poshtvan\app\config', 'handlePoshtvanHealthNotice']);
        add_action('admin_init', [__CLASS__, 'manual_handle_activation_hook']);
        add_action('plugin_loaded', ['\poshtvan\app\config', 'load_text_domain']);
        add_action('init', ['\poshtvan\app\config', 'postTypes']);
        add_action('add_meta_boxes_mihanticket_faq', ['\poshtvan\app\faq', 'adminFaqMetaBoxes']);
        add_action('save_post_mihanticket_faq', ['\poshtvan\app\faq', 'storeFaqMetaboxData']);
        add_action('admin_menu', ['\poshtvan\app\admin_menu', 'init']);
        add_action('init', ['\poshtvan\app\shortcodes', 'init']);
        add_action('poshtvan/shortcode/before_ticket_list', ['\poshtvan\app\assets', 'load_shortcode_assets_mihanticket_list']);
        if(options::get_is_steps_mode_in_new_ticket())
        {
            // filter new ticket view
            add_filter('poshtvan/views/new_ticket', ['\poshtvan\app\config', 'filterNewTicketViewFile']);
        }else{
            add_action('poshtvan/new_ticket/before_render_fields', ['\poshtvan\app\orders', 'render_orders_field']);
        }
        add_action('poshtvan/new_ticket/before_verification', ['\poshtvan\app\orders', 'verify_order_stopped_support']);
        add_action('poshtvan/new_ticket/before_verification', ['\poshtvan\app\orders', 'verify_new_ticket_fields']);
        add_action('poshtvan/new_ticket/after_submit', ['\poshtvan\app\orders', 'handle_order_field_after_submit_new_ticket']);

        // handle extra fields
        add_action('poshtvan/new_ticket/before_render_fields', ['\poshtvan\app\form\fields', 'render_extra_fields_in_user_new_ticket']);
        add_action('poshtvan/new_ticket/before_verification', ['\poshtvan\app\form\fields', 'handle_user_ticket_extra_fields_verification']);
        add_action('poshtvan/new_ticket/after_submit', ['\poshtvan\app\form\fields', 'handle_user_ticket_extra_fields_save_value']);

        // handle file uploading verification
        add_action('poshtvan/new_ticket/before_verification', ['\poshtvan\app\tickets', 'verifyUserTicketFileUploadingMimeType']);
        add_action('poshtvan/reply_ticket/before_submit_reply', ['\poshtvan\app\tickets', 'verifyUserTicketFileUploadingMimeType']);
        add_action('poshtvan/admin_ticket/verification', ['\poshtvan\app\tickets', 'verifyAdminTicketFileUploadingMimeType']);

        add_action('poshtvan/reply_ticket/before_submit_reply', ['\poshtvan\app\orders', 'verify_order_stopped_support']);

        // register settings
        add_action('admin_init', ['\poshtvan\app\options', 'register_settings']);
        add_action('poshtvan/register_sms_provider_setting', ['\poshtvan\app\providers\smsProvider', 'add_provider_settings']);
        add_action('poshtvan/panel/sms_provider_settings', ['\poshtvan\app\providers\smsProvider', 'show_provider_settings']);
        
        // after update options
        add_action('update_option_' . options::get_setting_name('roles_access_to_ticket_list'), ['\poshtvan\app\options', 'after_update_roles_access_to_ticket_list'], 10, 3);
        add_filter('pre_update_option_' . options::get_setting_name('sms_pattern_id_operator_new_ticket_value'), ['\poshtvan\app\options', 'beforeUpdateOperatorNewTicketSmsNoficationPatternID'], 10, 3);
        add_filter('pre_update_option_' . options::get_setting_name('sms_pattern_id_operator_reply_ticket_value'), ['\poshtvan\app\options', 'beforeUpdateOperatorReplyTicketSmsNoficationPatternID'], 10, 3);
        add_filter('pre_update_option_' . options::get_setting_name('sms_pattern_id_user_new_ticket_value'), ['\poshtvan\app\options', 'beforeUpdateUserNewTicketSmsNoficationPatternID'], 10, 3);
        add_filter('pre_update_option_' . options::get_setting_name('sms_pattern_id_user_reply_ticket_value'), ['\poshtvan\app\options', 'beforeUpdateUserReplyTicketSmsNoficationPatternID'], 10, 3);

        // handle notification
        add_action('poshtvan/after_submit_ticket', ['\poshtvan\app\notification', 'handleNewTicketNotification'], 10, 4);
        add_action('poshtvan_after_change_ticket_status_by_operator', ['\poshtvan\app\tickets', 'handleSendAutoTicketProcessAfterOperatorChangeTicketStatus'], 10, 2);
        add_action('poshtvan_after_change_ticket_status_to_user_solved', ['\poshtvan\app\tickets', 'handleSendAutoTicketProcessAfterUserSolvedTicket'], 10, 2);

        add_action('admin_enqueue_scripts', ['\poshtvan\app\faq', 'enqueueAdminScripts']);

        add_filter('pre_update_option_mwtc_poshtvan_ticket_custom_status', ['\poshtvan\app\options', 'beforeUpdateCustomTicketStatusProcess'], 10, 3);
        add_filter('pre_update_option_mwtc_poshtvan_auto_ticket_item', ['\poshtvan\app\options', 'beforeUpdateAutoTicketItemsProcess'], 10, 3);
        add_filter('poshtvan/tickets/ticket_status_list', ['\poshtvan\app\config', 'filterTicketCustomStatusList']);

        add_action('pv_event_hooshina_connection_checker', ['\poshtvan\app\providers\AiProviders\Hooshina', 'handle_event_check_connection_status']);
        add_action('admin_init', ['\poshtvan\app\providers\AiProviders\Hooshina', 'handle_register_cron']);

        add_action('poshtvan/render_step/choose_order', ['\poshtvan\app\orders', 'renderOrderRadoiButtons']);
    }
    static function activation_hook()
    {
        update_option('mwtc_handle_activation_hook', true);
        roles::add_supporter_cap('administrator');
    }
    static function deactivation_hook()
    {
        roles::remove_all_roles_caps();
    }
    static function manual_handle_activation_hook()
    {
        if(!get_option('mwtc_handle_activation_hook'))
        {
            return false;
        }
        delete_option('mwtc_handle_activation_hook');
        bootstrap::redirect_after_activate();
    }

    static function handlePoshtvanFreeVersionProcess()
    {
        if(!get_option('mw_poshtvan_free_version_process'))
        {
            delete_option('mwtc_license_key');
            delete_option('mwtc_license_status');
            wp_clear_scheduled_hook('mw_mihanticket_check_license');
            update_option('mw_poshtvan_free_version_process', 1);
        }
    }
}