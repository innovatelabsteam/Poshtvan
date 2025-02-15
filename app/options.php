<?php
namespace poshtvan\app;
class options
{
    static function get_setting_group_name($group_name)
    {
        return 'mihanticket_option_panel_' . $group_name;
    }
    static function get_setting_name($name)
    {
        return 'mwtc_' . $name;
    }
    static function register_setting($group_name, $option_name, $args = [])
    {
        $group_name = self::get_setting_group_name($group_name);
        $option_name = self::get_setting_name($option_name);
        register_setting($group_name, $option_name, $args);
    }
    static function get_option_value($option_name, $default=false)
    {
        return get_option(self::get_setting_name($option_name), $default);
    }
    static function get_replies_prefix_text()
    {
        return self::get_option_value('replies_prefix_text');
    }
    static function get_replies_suffix_text()
    {
        return self::get_option_value('replies_suffix_text');
    }
    static function get_operator_display_name()
    {
        return self::get_option_value('operator_display_name');
    }
    static function get_operator_avatar_image_id()
    {
        return self::get_option_value('operator_avatar_image_id');
    }
    static function get_roles_access_to_ticket_list()
    {
        $list = self::get_option_value('roles_access_to_ticket_list');
        return $list && is_array($list) ? $list : [];
    }
    static function get_tickets_date_type()
    {
        return self::get_option_value('tickets_date_type', 'solar');
    }

    /**
     * return megabyte value
     */
    static function get_file_uploading_max_size()
    {
        return self::get_option_value('file_uploading_max_size', 5);
    }
    static function get_file_uploading_max_size_bytes()
    {
        return self::get_file_uploading_max_size() * 1024 * 1024;
    }
    static function getFileUploadingValidTypes()
    {
        return (array)self::get_option_value('file_uploading_mime_types_whitelist', ['image']);
    }
    static function get_new_ticket_top_message_box_text()
    {
        return self::get_option_value('new_ticket_top_message_box_text');
    }
    static function get_new_ticket_top_message_box_type()
    {
        $value = self::get_option_value('new_ticket_top_message_box_type');
        return $value ? $value : 'info';
    }
    static function after_update_roles_access_to_ticket_list($old_value, $new_value, $option_name)
    {
        // remove previous roles caps
        $roles = roles::get_roles_name();
        foreach($roles as $role_key => $role_name)
        {
            roles::remove_supporter_cap($role_key);
        }
        foreach($new_value as $new_role)
        {
            roles::add_supporter_cap($new_role);
        }
    }

    // start tickets tab
    static function getCustomTicketStatus()
    {
        return self::get_option_value('poshtvan_ticket_custom_status');
    }
    static function beforeUpdateCustomTicketStatusProcess($value, $oldValue, $option)
    {
        if(!$value || !is_array($value))
        {
            return;
        }
        $new = [];
        $notices = [];
        foreach($value['slug'] as $key => $slugValue)
        {
            if(!tools::isEnglish($slugValue))
            {
                $notices['english_chars'] = true;
                continue;
            }
            $new[] = [
                'slug' => str_replace(' ', '', $slugValue),
                'name' => $value['name'][$key],
            ];
        }
        if($notices)
        {
            notice::add_notice('admin-panel-tickets-menu', __('Slug value must be entered in english letters', 'poshtvan'), 'error', 'cookie');
        }
        return $new;
    }
    static function getAutoTicketOperatorUser()
    {
        return self::get_option_value('auto_ticket_operator_user');
    }
    static function getAutoTicketItems()
    {
        return self::get_option_value('poshtvan_auto_ticket_item');
    }
    static function beforeUpdateAutoTicketItemsProcess($value, $oldValue, $option)
    {
        if(!$value || !is_array($value))
        {
            return;
        }
        $new = [];
        foreach($value['status'] as $key => $statusValue)
        {
            if(!isset($value['content'][$key]) || !$value['content'][$key])
            {
                continue;
            }
            $new[$statusValue][] = $value['content'][$key];
        }
        return $new;
    }
    // end tickets tab
    
    // start notification tab options
    static function is_send_email_to_user_after_submit_new_ticket()
    {
        return self::get_option_value('send_email_to_user_after_new_ticket');
    }
    static function get_after_new_ticket_user_email_subject()
    {
        return self::get_option_value('after_new_ticket_user_email_subject');
    }
    static function get_after_new_ticket_user_email_content()
    {
        return self::get_option_value('after_new_ticket_user_email_content');
    }
    static function is_send_email_to_user_after_submit_reply()
    {
        return self::get_option_value('send_email_to_user_after_submit_reply');
    }
    static function get_after_reply_user_email_subject()
    {
        return self::get_option_value('after_reply_user_email_subject');
    }
    static function get_after_reply_user_email_content()
    {
        return self::get_option_value('after_reply_user_email_content');
    }
    static function get_receive_email_notifications_email_address()
    {
        return self::get_option_value('receive_email_notifications_email_address');
    }
    static function is_send_email_to_admin_after_submit_new_ticket()
    {
        return self::get_option_value('send_email_to_admin_after_new_ticket');
    }
    static function get_after_new_ticket_admin_email_subject()
    {
        return self::get_option_value('after_new_ticket_admin_email_subject');
    }
    static function get_after_new_ticket_admin_email_content()
    {
        return self::get_option_value('after_new_ticket_admin_email_content');
    }
    static function is_send_email_to_admin_after_submit_reply()
    {
        return self::get_option_value('send_email_to_admin_after_submit_reply');
    }
    static function get_after_reply_admin_email_subject()
    {
        return self::get_option_value('after_reply_admin_email_subject');
    }
    static function get_after_reply_admin_email_content()
    {
        return self::get_option_value('after_reply_admin_email_content');
    }

    // end notification tab options

    // start sms-notification tab options
    static function getUserPhoneDriversList()
    {
        return [
            'mihanpanel' => __('MihanPanel', 'poshtvan'),
            'woocommerce' => __('Woocommerce', 'poshtvan'),
        ];
    }
    static function getUsersPhoneDriver()
    {
        return self::get_option_value('sms_notification_users_phone_driver');
    }
    static function getOperatorUsersList()
    {
        $args = [
            'capability' => roles::SUPPORTER_CAP_NAME,
        ];
        return get_users($args);
    }
    static function getActiveOperatorSmsNotificationReceiver()
    {
        return self::get_option_value('sms_notification_operator_receiver');
    }
    static function getActiveSmsProvider()
    {
        return self::get_option_value('active_sms_provider');
    }
    static function isAdminSmsNotificationActiveOnSubmitNewTicket()
    {
        return (bool)self::get_option_value('send_sms_to_operator_after_submit_new_ticket');
    }
    static function isAdminSmsNotificationActiveOnSubmitTicketReply()
    {
        return (bool)self::get_option_value('send_sms_to_operator_after_submit_ticket_reply');
    }
    static function getNewTicketAdminSmsNotificationContent()
    {
        return self::get_option_value('admin_sms_content_after_submit_new_ticket');
    }
    static function getReplyTicketAdminSmsNotificationContent()
    {
        return self::get_option_value('admin_sms_content_after_submit_ticket_reply');
    }
    static function isUserSmsNotificationActiveOnSubmitNewTicket()
    {
        return (bool)self::get_option_value('send_sms_to_user_after_submit_new_ticket');
    }
    static function isUserSmsNotificationActiveOnSubmitTicketReply()
    {
        return (bool)self::get_option_value('send_sms_to_user_after_submit_ticket_reply');
    }
    static function getNewTicketUserSmsNotificationContent()
    {
        return self::get_option_value('user_sms_content_after_submit_new_ticket');
    }
    static function getReplyTicketUserSmsNotificationContent()
    {
        return self::get_option_value('user_sms_content_after_submit_ticket_reply');
    }
    static function isPatternModeActiveForOperatorNewTicketSmsNotification()
    {
        return (bool)self::get_option_value('sms_pattern_id_operator_new_ticket');
    }
    static function getOperatorNewTicketSmsNoficationPatternID()
    {
        return self::get_option_value('sms_pattern_id_operator_new_ticket_value');
    }
    static function beforeUpdateOperatorNewTicketSmsNoficationPatternID($value, $oldValue, $optionName)
    {
        return self::isPatternModeActiveForOperatorNewTicketSmsNotification() ? $value : false;
    }

    static function isPatternModeActiveForOperatorReplyTicketSmsNotification()
    {
        return (bool)self::get_option_value('sms_pattern_id_operator_reply_ticket');
    }
    static function getOperatorReplyTicketSmsNoficationPatternID()
    {
        return self::get_option_value('sms_pattern_id_operator_reply_ticket_value');
    }
    static function beforeUpdateOperatorReplyTicketSmsNoficationPatternID($value, $oldValue, $optionName)
    {
        return self::isPatternModeActiveForOperatorReplyTicketSmsNotification() ? $value : false;
    }

    static function isPatternModeActiveForUserNewTicketSmsNotification()
    {
        return (bool)self::get_option_value('sms_pattern_id_user_new_ticket');
    }
    static function getUserNewTicketSmsNoficationPatternID()
    {
        return self::get_option_value('sms_pattern_id_user_new_ticket_value');
    }
    static function beforeUpdateUserNewTicketSmsNoficationPatternID($value, $oldValue, $optionName)
    {
        return self::isPatternModeActiveForUserNewTicketSmsNotification() ? $value : false;
    }

    static function isPatternModeActiveForUserReplyTicketSmsNotification()
    {
        return (bool)self::get_option_value('sms_pattern_id_user_reply_ticket');
    }
    static function getUserReplyTicketSmsNoficationPatternID()
    {
        return self::get_option_value('sms_pattern_id_user_reply_ticket_value');
    }
    static function beforeUpdateUserReplyTicketSmsNoficationPatternID($value, $oldValue, $optionName)
    {
        return self::isPatternModeActiveForUserReplyTicketSmsNotification() ? $value : false;
    }

    // end sms-notification tab options
    
    // start woocommerce tab options
    static function get_is_show_wc_orders_field_in_user_ticket()
    {
        return tools::isWoocommerceActive() ? self::get_option_value('show_wc_orders_field_in_user_ticket') : false;
    }
    static function get_is_send_ticket_just_by_wc_order_item()
    {
        return tools::isWoocommerceActive() ? self::get_option_value('send_ticket_just_by_wc_order_item') : false;
    }
    static function get_is_steps_mode_in_new_ticket()
    {
        return tools::isWoocommerceActive() ? self::get_option_value('steps_mode_in_send_new_ticket') : false;
    }
    static function get_is_integrate_with_wc_subscriptions()
    {
        return self::get_option_value('integrate_with_wc_subscriptions');
    }

    static function get_invalid_order_number_text()
    {
        return self::get_option_value('ticket_validation_invalid_order_id_text');
    }

    static function get_product_stopped_support_text()
    {
        return self::get_option_value('ticket_validation_product_stopped_support_text');
    }

    static function get_product_stopped_left_days_support_text()
    {
        return self::get_option_value('ticket_validation_product_stopped_left_days_support_text');
    }

    static function get_product_expired_subscription_text()
    {
        return self::get_option_value('ticket_validation_expired_product_subscription_text');
    }
    // end woocommerce tab options

    // start form steps tab options
    static function get_choose_order_step_section_title()
    {
        return self::get_option_value('choose_order_step_section_title', __('What product do you need help with?', 'poshtvan'));
    }
    static function get_faq_step_section_title()
    {
        return self::get_option_value('faq_step_section_title', __('Frequently asked questions about this product', 'poshtvan'));
    }
    static function get_search_step_section_title()
    {
        return self::get_option_value('search_step_section_title', __('Responsive intelligent robot', 'poshtvan'));
    }
    static function get_final_step_section_title()
    {
        return self::get_option_value('final_step_section_title', __('Describe your problem', 'poshtvan'));
    }
    // end form steps tab options

    static function get_ai_helper_text()
    {
        return self::get_option_value('ai_helper_text');
    }

    static function get_smart_bot_next_button_title()
    {
        return self::get_option_value('smart_bot_next_button_title', __('I did not find the answer to my question', 'poshtvan'));
    }

    public static function get_ai_current_wallet()
    {
        $locale = strtolower(get_locale());

        if(strpos($locale, '_') !== false){
            $locale = explode('_', $locale);
            $locale = $locale[array_key_first($locale)];
        }

        return self::get_option_value('ai_hooshina_wallet', $locale);
    }

    public static function ai_chat_is_activated()
    {
        return \poshtvan\app\providers\AiProviders\AiChatService::use()->isConnected() && self::get_is_steps_mode_in_new_ticket();
    }

    public static function hide_support_from_ai()
    {
        return self::get_option_value('hide_support_from_ai', 0);
    }

    static function register_settings()
    {
        self::register_setting('general', 'operator_avatar_image_id');
        self::register_setting('general', 'operator_display_name');
        self::register_setting('general', 'roles_access_to_ticket_list');
        self::register_setting('general', 'tickets_date_type');
        self::register_setting('general', 'file_uploading_max_size');
        self::register_setting('general', 'file_uploading_mime_types_whitelist');
        self::register_setting('general', 'new_ticket_top_message_box_text');
        self::register_setting('general', 'new_ticket_top_message_box_type');
        self::register_setting('general', 'replies_prefix_text');
        self::register_setting('general', 'replies_suffix_text');

        self::register_setting('tickets', 'poshtvan_ticket_custom_status');
        self::register_setting('tickets', 'poshtvan_auto_ticket_item');
        self::register_setting('tickets', 'auto_ticket_operator_user');


        self::register_setting('notifications', 'send_email_to_user_after_new_ticket');
        self::register_setting('notifications', 'after_new_ticket_user_email_subject');
        self::register_setting('notifications', 'after_new_ticket_user_email_content');
        self::register_setting('notifications', 'send_email_to_user_after_submit_reply');
        self::register_setting('notifications', 'after_reply_user_email_subject');
        self::register_setting('notifications', 'after_reply_user_email_content');
        self::register_setting('notifications', 'receive_email_notifications_email_address');
        self::register_setting('notifications', 'send_email_to_admin_after_new_ticket');
        self::register_setting('notifications', 'after_new_ticket_admin_email_subject');
        self::register_setting('notifications', 'after_new_ticket_admin_email_content');
        self::register_setting('notifications', 'send_email_to_admin_after_submit_reply');
        self::register_setting('notifications', 'after_reply_admin_email_subject');
        self::register_setting('notifications', 'after_reply_admin_email_content');
        
        self::register_setting('sms-notifications', 'sms_notification_users_phone_driver');
        self::register_setting('sms-notifications', 'sms_notification_operator_receiver');
        self::register_setting('sms-notifications', 'active_sms_provider');
        do_action('poshtvan/register_sms_provider_setting', 'sms-notifications');
        self::register_setting('sms-notifications', 'send_sms_to_operator_after_submit_new_ticket');
        self::register_setting('sms-notifications', 'admin_sms_content_after_submit_new_ticket');
        self::register_setting('sms-notifications', 'send_sms_to_operator_after_submit_ticket_reply');
        self::register_setting('sms-notifications', 'admin_sms_content_after_submit_ticket_reply');
        self::register_setting('sms-notifications', 'send_sms_to_user_after_submit_new_ticket');
        self::register_setting('sms-notifications', 'user_sms_content_after_submit_new_ticket');
        self::register_setting('sms-notifications', 'send_sms_to_user_after_submit_ticket_reply');
        self::register_setting('sms-notifications', 'user_sms_content_after_submit_ticket_reply');
        self::register_setting('sms-notifications', 'sms_pattern_id_operator_new_ticket');
        self::register_setting('sms-notifications', 'sms_pattern_id_operator_new_ticket_value');
        self::register_setting('sms-notifications', 'sms_pattern_id_operator_reply_ticket');
        self::register_setting('sms-notifications', 'sms_pattern_id_operator_reply_ticket_value');
        self::register_setting('sms-notifications', 'sms_pattern_id_user_new_ticket');
        self::register_setting('sms-notifications', 'sms_pattern_id_user_new_ticket_value');
        self::register_setting('sms-notifications', 'sms_pattern_id_user_reply_ticket');
        self::register_setting('sms-notifications', 'sms_pattern_id_user_reply_ticket_value');

        self::register_setting('woocommerce', 'show_wc_orders_field_in_user_ticket');
        self::register_setting('woocommerce', 'send_ticket_just_by_wc_order_item');
        self::register_setting('woocommerce', 'steps_mode_in_send_new_ticket');
        self::register_setting('woocommerce', 'integrate_with_wc_subscriptions');
        self::register_setting('woocommerce', 'ticket_validation_invalid_order_id_text');
        self::register_setting('woocommerce', 'ticket_validation_product_stopped_support_text');
        self::register_setting('woocommerce', 'ticket_validation_product_stopped_left_days_support_text');
        self::register_setting('woocommerce', 'ticket_validation_expired_product_subscription_text');

        self::register_setting('form_steps', 'choose_order_step_section_title');
        self::register_setting('form_steps', 'faq_step_section_title');
        self::register_setting('form_steps', 'search_step_section_title');
        self::register_setting('form_steps', 'final_step_section_title');
        self::register_setting('form_steps', 'smart_bot_next_button_title');

        self::register_setting('ai', 'ai_helper_text');
        self::register_setting('ai', 'ai_hooshina_wallet');
        self::register_setting('ai', 'hide_support_from_ai');
    }
}