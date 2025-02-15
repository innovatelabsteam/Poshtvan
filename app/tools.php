<?php
namespace poshtvan\app;
class tools
{
    private static $_plugin_version=null;
    static function isEnglish($input)
    {
        return strlen($input) == mb_strlen($input);
    }
    static function truncate($text, $limit=3)
    {
        $words = explode(' ', $text);
        if($limit < count($words))
        {
            $text = implode(' ', array_slice($words, 0, $limit));
        }
        return $text;
    }
    static function get_plugin_version()
    {
        if(self::$_plugin_version)
        {
            return self::$_plugin_version;
        }
        $file_data = get_file_data(POSHTVAN_APP, ['version' => 'version']);
        if(!isset($file_data['version']))
        {
            return false;
        }
        self::$_plugin_version = $file_data['version'];
        return $file_data['version'];
    }
    static function getPersianDate($timestamp, $format='d F Y - H:i')
    {
        if(!$timestamp)
        {
            return false;
        }
        if(!function_exists('jdate'))
        {
            include POSHTVAN_DIR_PATH . 'libs' . DIRECTORY_SEPARATOR . 'jdf.php';
        }
        return jdate($format, $timestamp, null, 'local');
    }
    static function getDate($timestamp)
    {
        $is_solar_calendar = options::get_tickets_date_type() === 'solar';
        return $is_solar_calendar ? self::getPersianDate($timestamp) : gmdate('d F Y - H:i', $timestamp);
    }
    static function is_active_plugin($plugin_name)
    {
        return in_array($plugin_name, apply_filters('active_plugins', get_option('active_plugins')));
    }
    static function isWoocommerceActive()
    {
        return tools::is_active_plugin('woocommerce/woocommerce.php');
    }
    static function checkIsPoshtvanPageExists()
    {
        return get_page_by_path('poshtvan') ? true : false;
    }
    static function convertNumberLocale($value, $localeToEn=true)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $en = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        return $localeToEn ? str_replace($persian, $en, $value) : str_replace($en, $persian, $value);
    }
    static function sanitizeArrayValues($array)
    {
        return array_map(function($value){
            return sanitize_text_field($value);
        }, $array);
    }
}
