<?php
namespace poshtvan\app\providers\smsProviders;

use poshtvan\app\phone;

class sabanovin implements providerInterface
{
    private static function getBaseUrl()
    {
        $api = self::getApiKey();
        $url = 'https://api.sabanovin.com/v1/'.$api.'/';
        return $url;
    }
    static function getCreditRaw()
    {
        $url = self::getBaseUrl() . 'account/balance.json';
        $remote = wp_remote_get($url);
        $response = json_decode(wp_remote_retrieve_body($remote));
        return wp_remote_retrieve_response_code($remote) === 200 && $response->status->code == 200 ? $response->entry->balance : false;
    }

    static function getGatewayNumber()
    {
        return get_option('poshtvan_sabanovin_gateway_number');
    }
    static function getApiKey()
    {
        return get_option('poshtvan_sabanovin_api_key');
    }
    static function getCredit()
    {
        $credit = self::getCreditRaw();
        return is_int($credit) ? sprintf(__("%s Toman", 'poshtvan'), number_format($credit)) : 0;
    }
    static function render_settings()
    {
        $gatewayNumber = self::getGatewayNumber();
        $apiKey = self::getApiKey();
        $credit = self::getCredit();
        ?>
        <div class="option_section">
            <h3 class="option_section_title"><?php esc_html_e('Mellipayamak settings', 'poshtvan')?></h3>
            <div class="option_field option_row_field">
                <label><?php printf("%s: ", esc_html__("Account Credit", 'poshtvan')) ?></label>
                <span class="number-value"><?php echo esc_html($credit)?></span>
            </div>
            <div class="option_field option_row_field flex-label">
                <label for="mw_sabanovin_gateway_number"><?php esc_html_e("Gateway Number", 'poshtvan'); ?></label>
                <input dir="auto" type="text" value="<?php echo esc_attr($gatewayNumber); ?>" name="poshtvan_sabanovin_gateway_number" id="mw_sabanovin_gateway_number">
            </div>
            <div class="option_field option_row_field flex-label">
                <label for="mw_sabanovin_api_key"><?php esc_html_e("Gateway API Key", 'poshtvan'); ?></label>
                <input dir="auto" type="text" value="<?php echo esc_attr($apiKey); ?>" name="poshtvan_sabanovin_api_key" id="mw_sabanovin_api_key">
            </div>
            <p class="option_section_description"><?php esc_html_e("If you are using pattern feature in your sms-provider, just enter Poshtvan pre-defined variables in new line according the variables order defined in sms provider patterns for sms content field value; like this:", 'poshtvan'); ?></p>
            <p class="option_section_description"><?php printf("%s", "[[user_login]]"); ?></p>
            <p class="option_section_description"><?php printf("%s", "[[display_name]]"); ?></p>
        </div>
        <?php
    }

    static function get_provider_settings()
    {
        return [
            'poshtvan_sabanovin_gateway_number',
            'poshtvan_sabanovin_api_key',
        ];
    }
        
    static function send($to, $msg, $patternID=null)
    {
        $gateway = self::getGatewayNumber();
        $to = phone::getValidatedPhoneNumber($to);
        $msg = sanitize_textarea_field($msg);
        $request = self::getBaseUrl() . "sms/send.json?gateway={$gateway}&to={$to}&text={$msg}";
        $remote = wp_remote_get($request);
        $response_code = wp_remote_retrieve_response_code($remote);
        return $response_code == 200 ? json_decode(wp_remote_retrieve_body($remote)) : $response_code;
    }

    static function validate_send_message($response)
    {
        if(!isset($response->status) || !$response->status)
        {
            return false;
        }
        $res['status'] = $response->status->code;
        $res['msg'] = $response->status->message;
        return $res;
    }
}