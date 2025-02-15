<?php
namespace poshtvan\app\providers\smsProviders;

use poshtvan\app\phone;

class melipayamak implements providerInterface
{
    static function base_url()
    {
        return 'https://rest.payamak-panel.com/api/SendSMS/';
    }
    static function send($to, $msg, $patternID=false)
    {
        return $patternID ? self::send_with_pattern_mode($to, $msg, $patternID) : self::simple_send($to, $msg);
    }
    static function send_with_pattern_mode($to, $msg, $pattern_id)
    {
        $url = self::base_url() . 'BaseServiceNumber';
        $to = phone::getValidatedPhoneNumber($to);
        $username = self::get_username();
        $password = self::get_password();
        if(!$username || !$password || !$msg || !$to)
        {
            return false;
        }
        $parameters = explode(PHP_EOL, $msg);
        $parameters = array_map('trim', $parameters);
        $parameters = array_filter($parameters);
        $parameters = implode(';', $parameters);
        $args = [
            "body" => [
                'username' => $username,
                'password' => $password,
                'to' => $to,
                'text' => $parameters,
                'bodyId' => $pattern_id
            ]
        ];
        $remote = wp_remote_post($url, $args);
        $response_code = wp_remote_retrieve_response_code($remote);
        return $response_code == 200 ? json_decode(wp_remote_retrieve_body($remote)) : $response_code;
    }
    static function simple_send($to, $msg){
        $url = self::base_url() . 'SendSMS';
        $to = phone::getValidatedPhoneNumber($to);
        $msg = sanitize_text_field($msg);
        $username = self::get_username();
        $password = self::get_password();
        $gateway_number = self::get_gateway_number();
        $args = [
            'body' => [
                'username' => $username,
                'password' => $password,
                'to' => $to,
                'from' => $gateway_number,
                'text' => $msg,
                'isFlash' => false
            ]
        ];
        $remote = wp_remote_post($url, $args);
        $response_code = wp_remote_retrieve_response_code($remote);
        return $response_code == 200 ? json_decode(wp_remote_retrieve_body($remote)) : $response_code;
    }
    static function get_credit()
    {
        $url = self::base_url() . 'GetCredit';
        $username = self::get_username();
        $password = self::get_password();
        $args = [
            "body" => [
                "username" => $username,
                "password" => $password
            ]
        ];
        $remote = wp_remote_post($url, $args);
        $response = json_decode(wp_remote_retrieve_body($remote));
        return wp_remote_retrieve_response_code($remote) == 200 && $response->RetStatus == 1 ? intval($response->Value) : false;
    }
    static function get_username()
    {
        return get_option('poshtvan_melipayamak_username');
    }
    static function get_password()
    {
        return get_option('poshtvan_melipayamak_password');
    }
    static function get_gateway_number()
    {
        return get_option('poshtvan_melipayamak_gateway_number');
    }
    static function render_settings(){
        $username = self::get_username();
        $password = self::get_password();
        $gateway_number = self::get_gateway_number();
        $credit = self::get_credit();
        ?>
        <div class="option_section">
            <h3 class="option_section_title"><?php esc_html_e('Mellipayamak settings', 'poshtvan')?></h3>
            <div class="option_field option_row_field">
                <label><?php printf("%s: ", esc_html__("Account Credit", 'poshtvan')) ?></label>
                <span class="number-value"><?php echo esc_html($credit)?></span>
            </div>
            <div class="option_field option_row_field flex-label">
                <label for="mw_melipayamak_username"><?php esc_html_e("Username", 'poshtvan'); ?></label>
                <input dir="auto" value="<?php echo esc_attr($username); ?>" type="text" name="poshtvan_melipayamak_username" id="mw_melipayamak_username">
            </div>
            <div class="option_field option_row_field flex-label">
                <label for="mw_melipayamak_password"><?php esc_html_e("Password", 'poshtvan'); ?></label>
                <input dir="auto" type="text" value="<?php echo esc_attr($password); ?>" name="poshtvan_melipayamak_password" id="mw_melipayamak_password">
            </div>
            <div class="option_field option_row_field flex-label">
                <label for="mw_melipayamak_gateway_number"><?php esc_html_e("Gateway Number", 'poshtvan'); ?></label>
                <input dir="auto" type="text" value="<?php echo esc_attr($gateway_number); ?>" name="poshtvan_melipayamak_gateway_number" id="mw_melipayamak_gateway_number">
            </div>
            <p class="option_section_description"><?php esc_html_e("If you are using pattern feature in your sms-provider, just enter Poshtvan pre-defined variables in new line according the variables order defined in sms provider patterns for sms content field value; like this:", 'poshtvan'); ?></p>
            <p class="option_section_description"><?php printf("%s", "[[user_login]]"); ?></p>
            <p class="option_section_description"><?php printf("%s", "[[display_name]]"); ?></p>
        </div>
        <?php
    }
    static function get_provider_settings(){
        return [
            'poshtvan_melipayamak_username',
            'poshtvan_melipayamak_password',
            'poshtvan_melipayamak_gateway_number',
        ];
    }
    static function validate_send_message($response)
    {
        if(!isset($response->RetStatus))
        {
            return false;
        }
        $res['status'] = $response->RetStatus == 1 ? 200 : $response->RetStatus;
        $res['msg'] = $response->StrRetStatus;
        return $res;
    }
}