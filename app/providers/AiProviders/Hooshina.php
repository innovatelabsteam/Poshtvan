<?php
namespace poshtvan\app\providers\AiProviders;

use poshtvan\app\notice;
use poshtvan\app\notification;
use poshtvan\app\options;

class Hooshina
{
    private static $appName = 'Hooshina App';
    private static $baseUrl = 'https://app.hooshina.com/';

    private static $connectionOptionKey = 'hooshina_connection_token';

    private static function getBaseUrl()
    {
        return self::$baseUrl;
    }

    private static function getApiBaseUrl()
    {
        return self::$baseUrl . 'api/';
    }

    private static function getAppUuid()
    {
        return get_option('hooshina_app_user_uuid');
    }

    private static function updateAppUuid($uuid)
    {
        $currentUuid = self::getAppUuid();

        if ($currentUuid == $uuid)
            return false;

        return update_option('hooshina_app_user_uuid', $uuid);
    }

    private static function generateApplicationPassword()
    {
        if(!class_exists('WP_Application_Passwords'))
            return false;

        $userId = get_current_user_id();
        $appExists = \WP_Application_Passwords::application_name_exists_for_user($userId, self::$appName);

        if ($appExists){
            $data = \WP_Application_Passwords::get_user_application_password($userId, self::getAppUuid());
            if(is_array($data) && isset($data['password'])){
                return $data['password'];
            }
            return false;
        }

        $data = \WP_Application_Passwords::create_new_application_password($userId, array('name' => self::$appName));

        if (is_wp_error($data))
            return false;

        if (isset($data[1]) && isset($data[1]['uuid']))
            $uuid = self::updateAppUuid($data[1]['uuid']);

        if (isset($data[0]))
            return $data[0]; // password

        return false;
    }

    public static function getConnectUrl()
    {
        $appPass = self::generateApplicationPassword();
        if (!$appPass)
            return false;

        $data = [
            'auth_token' => $appPass,
            'from' => admin_url('admin.php?page=poshtvan_settings&tab=ai')
        ];

        return add_query_arg('data', base64_encode(wp_json_encode($data)), (self::getBaseUrl() . 'connect/app'));
    }

    private static function requestByAction($token, $action = 'verify')
    {
        $url = self::getApiBaseUrl() . 'connect/' . $action;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Referer' => site_url()
        ];

        $response = wp_remote_get($url, [
            'headers' => $headers,
            'timeout' => 90,
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($action !== 'revoke' && $status_code !== 200) {
            return false;
        }

        return wp_remote_retrieve_body($response);
    }

    public static function verifyConnection($token)
    {
        return self::requestByAction($token);
    }

    public static function revokeConnection($token)
    {
        $revoke = self::requestByAction($token, 'revoke');

        if ($revoke){
            delete_option(self::$connectionOptionKey);
        }

        return $revoke;
    }

    public static function isConnected()
    {
        $data = self::getCurrentConnectionData();
        return is_object($data) && isset($data->siteKey) ? $data->siteKey : false;
    }

    public static function getCurrentConnectionData()
    {
        $data = get_option(self::$connectionOptionKey);

        if (empty($data))
            return false;

        $tokenData = json_decode($data);
        if (!$tokenData || !is_object($tokenData))
            return false;

        return $tokenData;
    }

    private static function getCurrentPageUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        return $protocol . $host . $uri;
    }

    public static function getCurrentWallet()
    {
        return options::get_ai_current_wallet();
    }

    public function getWalletBalance()
    {
        $tokenData = self::getCurrentConnectionData();
        if (!$tokenData)
            return false;

        $url = self::getApiBaseUrl() . 'user/total-credit';

        $headers = [
            'Authorization' => 'Bearer ' . $tokenData->auth,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Referer' => site_url()
        ];

        $response = wp_remote_get($url, [
            'headers' => $headers,
            'timeout' => 90,
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code !== 200) {
            return false;
        }

        return json_decode(wp_remote_retrieve_body($response));
    }

    public function sendMessage($message)
    {
        $tokenData = self::getCurrentConnectionData();
        if (!$tokenData)
            return false;

        $url = self::getApiBaseUrl() . 'support/helper';

        $headers = [
            'Authorization' => 'Bearer ' . $tokenData->auth,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Referer' => site_url()
        ];

        $response = wp_remote_post($url, [
            'headers' => $headers,
            'timeout' => 90,
            'body' => wp_json_encode(array(
                'prompt' => $message,
                'locale' => self::getCurrentWallet()
            )),
            'sslverify'   => false,
            'data_format' => 'body',
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code !== 200) {
            return false;
        }

        return json_decode(wp_remote_retrieve_body($response));
    }

    public static function handle_return_from_ai()
    {
        $currentUrl = self::getCurrentPageUrl();
        $mainToken = $_GET['token'] ?? null;

        if (empty($mainToken))
            return false;

        $tokenData = json_decode(base64_decode($mainToken));

        if (!$tokenData || !is_object($tokenData) || (!isset($tokenData->auth) || !isset($tokenData->siteKey))){
            notice::add_notice('admin-panel-ai-menu', __('The access token is invalid.', 'poshtvan'), 'error', 'cookie');
            return false;
        }

        $verify = self::verifyConnection($tokenData->auth);
        if (!$verify){
            delete_option(self::$connectionOptionKey);
            notice::add_notice('admin-panel-ai-menu', __('Access to ai was not approved.', 'poshtvan'), 'error', 'cookie');
            return false;
        }

        update_option(self::$connectionOptionKey, base64_decode($mainToken));

        notice::add_notice('admin-panel-ai-menu', __('Access to ai has been successfully activated.', 'poshtvan'), 'success', 'cookie');

        wp_redirect(remove_query_arg('token', $currentUrl));
    }

    public static function handle_event_check_connection_status()
    {
        $tokenData = self::getCurrentConnectionData();
        if (!$tokenData)
            return false;

        if (!isset($tokenData->auth) || self::verifyConnection($tokenData->auth)){
            return false;
        }

        self::revokeConnection($tokenData->auth);
        delete_option(self::$connectionOptionKey);
    }

    public static function handle_register_cron()
    {
        if (!wp_next_scheduled('pv_event_hooshina_connection_checker')) {
            $hour = wp_rand(0, 23);
            $minutes = wp_rand(0, 59);
            $timestamp = strtotime("tomorrow {$hour}:{$minutes}:00");
            wp_schedule_event($timestamp, 'daily', 'pv_event_hooshina_connection_checker');
        }
    }
}