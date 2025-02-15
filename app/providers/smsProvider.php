<?php
namespace poshtvan\app\providers;

use poshtvan\app\options;
use poshtvan\app\providers\smsProviders\melipayamak;
use poshtvan\app\providers\smsProviders\sabanovin;
use poshtvan\app\providers\smsProviders\wpsms;

class smsProvider
{
    private static $_providers = [];
    private static function loadProviderClass($provider=false)
    {
        $all = self::get_providers_data();
        $provider = $provider ? $provider : options::getActiveSmsProvider();
        if(isset($all[$provider]['class']) && class_exists($all[$provider]['class']))
        {
            return true;
        }
        $path = isset($all[$provider]['path']) ? $all[$provider]['path'] : false;
        if($path && file_exists($path) && is_readable($path))
        {
            include_once $path;
        }
    }
    public static function validate_provider($provider)
    {
        $_providers = self::get_providers_data();
        self::loadProviderClass($provider);
        return in_array($provider, array_keys($_providers)) && isset($_providers[$provider]['class']) && $_providers[$provider]['class'] && class_exists($_providers[$provider]['class']) ? true : false;
    }
    static function send_message($to, $msg, $patternID='')
    {
        $_providers = self::get_providers_data();
        $active_provider = options::getActiveSmsProvider();
        if(!self::validate_provider($active_provider))
        {
            return false;
        }
        $args = [$to, $msg];
        if($patternID)
        {
            $args[] = $patternID;
        }
        return call_user_func_array([$_providers[$active_provider]['class'], "send"], $args);
    }
    static function validate_send_message($response)
    {
        $_providers = self::get_providers_data();
        $active_provider = options::getActiveSmsProvider();
        if(!self::validate_provider($active_provider))
        {
            return false;
        }
        if(isset($_providers[$active_provider]['class']) && method_exists($_providers[$active_provider]['class'], 'validate_send_message'))
        {
            return call_user_func([$_providers[$active_provider]['class'], "validate_send_message"], $response);
        }
    }
    private static function get_providers_data()
    {
        self::$_providers = [
            'melipayamak' => [
                'title' => esc_html__("Meli Payamak", 'poshtvan'),
                'class' => melipayamak::class
            ],
            'sabanovin' => [
                'title' => __('Sabanovin', 'poshtvan'),
                'class' => sabanovin::class,
            ],
            'wpsms' => [
                'title' => 'WP-SMS',
                'class' => wpsms::class,
            ],
        ];
        return apply_filters('poshtvan_sms_providers', self::$_providers);
    }
    static function get_providers_title()
    {
        $all_providers = self::get_providers_data();
        $providers = [];
        foreach($all_providers as $provider_slug => $providers_data)
        {
            $providers[$provider_slug] = $providers_data['title'];
        }
        return $providers;
    }
    static function show_provider_settings($provider=false)
    {
        $provider = $provider ? $provider : options::getActiveSmsProvider();
        if(!self::validate_provider($provider))
        {
            return false;
        }
        $_providers = self::get_providers_data();
        if(isset($_providers[$provider]['class']) && method_exists($_providers[$provider]['class'], 'render_settings'))
        {
            return call_user_func([$_providers[$provider]['class'], 'render_settings']);
        }
    }
    static function add_provider_settings($setting_group)
    {
        $_providers = self::get_providers_data();
        foreach($_providers as $provider_key => $provider_data)
        {
            self::loadProviderClass($provider_key);
            $settings = isset($_providers[$provider_key]['class']) && method_exists($_providers[$provider_key]['class'], 'get_provider_settings') ? call_user_func([$_providers[$provider_key]['class'], 'get_provider_settings']) : false;
            if($settings && is_array($settings))
            {
                foreach($settings as $setting)
                {
                    if(is_array($setting) && isset($setting['setting_name']) && isset($setting['sanitize_callback']))
                    {
                        register_setting(options::get_setting_group_name($setting_group), $setting['setting_name'], ['sanitize_callback' => $setting["sanitize_callback"]]);
                    }elseif(is_string($setting)){
                        register_setting(options::get_setting_group_name($setting_group), $setting);
                    }
                }
            }
        }
    }
}