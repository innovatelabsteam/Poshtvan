<?php
namespace poshtvan\app;
use poshtvan\app\providers\AiProviders\AiChatService;

class assets
{
    static function get_css_url($file_name)
    {
        if(!$file_name)
        {
            return false;
        }
        if(filter_var($file_name, FILTER_VALIDATE_URL))
        {
            return $file_name;
        }
        $file_name = 'assets.css.' . $file_name;
        return files::get_file_url($file_name, 'css');
    }
    static function get_js_url($file_name)
    {
        if(!$file_name)
        {
            return false;
        }
        if(filter_var($file_name, FILTER_VALIDATE_URL))
        {
            return $file_name;
        }
        $file_name = 'assets.js.' . $file_name;
        return files::get_file_url($file_name, 'js');
    }
    static function get_img_url($file_name, $extension='png')
    {
        if(!$file_name)
        {
            return false;
        }
        if(filter_var($file_name, FILTER_VALIDATE_URL))
        {
            return $file_name;
        }
        $file_name = 'assets.img.' . $file_name;
        return files::get_file_url($file_name, $extension);

    }
    static function get_handle_name($name)
    {
        if(!$name)
        {
            return false;
        }
        return 'mw_ticket_' . $name;
    }
    static function enqueue_script($name, $src, $deps = ['jquery'], $version=null, $in_footer=true)
    {
        $name = self::get_handle_name($name);
        $src = self::get_js_url($src);
        $version = $version ? $version : tools::get_plugin_version();
        wp_enqueue_script($name, $src, $deps, $version, $in_footer);
    }
    static function enqueue_style($name, $src,$version=null, $deps=null)
    {
        $name = self::get_handle_name($name);
        $src = self::get_css_url($src);
        $version = $version ? $version : tools::get_plugin_version();
        wp_enqueue_style($name, $src, $deps, $version);
    }
    static function localize_script($name, $object_name, $data)
    {
        $name = self::get_handle_name($name);
        wp_localize_script($name, $object_name, $data);
    }
    private static function get_messages()
    {
        return [
            'invalid_file_size' => esc_html__('Invalid File Size', 'poshtvan'),
            'successfully_copied' => esc_html__('Successfully copied', 'poshtvan'),
            'waiting' => esc_html__('Please Wait...', 'poshtvan'),
            'loading' => esc_html__('Loading', 'poshtvan'),
            'has_error' => esc_html__('Has error', 'poshtvan'),
            'invalid_order' => esc_html__('Invalid order number', 'poshtvan'),
        ];
    }
    static function load_admin_ticket_assets()
    {
        self::enqueue_style('admin-ticket', 'admin.ticket');
        if(is_rtl()){
          self::enqueue_style('admin-ticket-rtl', 'admin.ticket-rtl');
        }
        self::enqueue_script('admin-ticket', 'admin-ticket');
        $data = [
            'au' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mwtc_ticket_actions'),
            'max_allowed_file_size' => options::get_file_uploading_max_size_bytes(),
            'messages' => self::get_messages(),
        ];
        self::localize_script('admin-ticket', 'mwtc', $data);
    }
    static function load_shortcode_assets_mihanticket_list()
    {
        self::enqueue_style('select2', 'select2');
        self::enqueue_style('user-ticket-list', 'user.ticket');
        if(options::get_is_steps_mode_in_new_ticket())
        {
            wp_enqueue_style('dashicons');
        }
        if(is_rtl()){
          self::enqueue_style('user-ticket-rtl', 'user.ticket-rtl');
        }
        if(options::get_is_steps_mode_in_new_ticket())
        {
            self::enqueue_script('user-ticket-list', 'user-ticket-steps-mode');
        }else{
            self::enqueue_script('user-ticket-list', 'user-ticket');
        }
        self::enqueue_script('select2', 'select2');
        $data = [
            'au' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mwtc_ticket_actions'),
            'max_allowed_file_size' => options::get_file_uploading_max_size_bytes(),
            'messages' => self::get_messages()
        ];
        self::localize_script('user-ticket-list', 'mwtc', $data);

        if(options::ai_chat_is_activated()){
            self::enqueue_style('user-chat-widget', 'user.ai-chat-widget');
			self::enqueue_script('marked-lib', 'marked');
            self::enqueue_script('user-chat-widget', 'ai-chat-widget');
            $data = [
                'au' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('pv_ai_nonce'),
                'messages' => self::get_messages()
            ];
            self::localize_script('user-chat-widget', 'pv_data', $data);
        }
    }
    static function load_admin_settings_assets()
    {
        $settings_css = self::get_css_url('admin.settings');
        $settings_js = self::get_js_url('admin-settings');

        self::enqueue_style('admin_settings', $settings_css);
        self::enqueue_style('select2', 'select2');
        self::enqueue_script('select2', 'select2');
        self::load_media_uploader();
        self::enqueue_script('admin_settings', $settings_js);

        $data = [
            'texts' => [
                'name' => __('Name', 'poshtvan'),
                'slug' => __('Slug', 'poshtvan'),
                'delete_field_alert' => __('Are you sure you want to delete this field?', 'poshtvan'),
                'ticket_content' => __('Ticket content', 'poshtvan'),
                'sms_provider_settings_loading' => __('Loading SMS provider settings', 'poshtvan')
            ],
            'data' => [
                'ticket_status_list' => tickets::getAutoTicketStatusList(),
            ],
        ];
        self::localize_script('admin_settings', 'poshtvan_settings', $data);
    }
    static function load_media_uploader()
    {
        wp_enqueue_media();
    }
    static function admin_load_fields_menu_assets()
    {
        $fields_css = self::get_css_url('admin.fields');
        $admin_js = self::get_js_url('admin.fields');
        $jquery_ui = self::get_js_url('admin.jquery_ui');
        self::enqueue_style('admin-fields', $fields_css);
        self::enqueue_script('jquery-ui', $jquery_ui);
        self::enqueue_script('admin-fields', $admin_js, ['jquery', self::get_handle_name('jquery-ui')]);
        $data = [
            'au' => admin_url('admin-ajax.php')
        ];
        self::localize_script('admin-fields', 'mwtc', $data);
    }
}