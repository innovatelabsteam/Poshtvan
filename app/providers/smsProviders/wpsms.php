<?php

namespace poshtvan\app\providers\smsProviders;

class wpsms implements providerInterface
{
    static function send($to, $msg, $patternID = null)
    {
        $to = !is_array($to) ? [$to] : $to;
        return self::isWpSmsAccessible() ? wp_sms_send($to, $msg) : false;
    }
    private static function isWpSmsAccessible()
    {
        return function_exists('wp_sms_send');
    }
    static function render_settings()
    {
        if (self::isWpSmsAccessible()) : ?>
            <div class="option_section">
                <div class="option_field">
                    <p style="background: #0191d7;color: white;padding: 15px 15px;border-radius: 5px;"><?php esc_html_e('Config your sms provider settings from WpSms dashboard', 'poshtvan') ?></p>
                </div>
            </div>
        <?php else : ?>
            <div class="option_section">
                <div class="option_field">
                    <p style="background: #0191d7;color: white;padding: 15px 15px;border-radius: 5px;"><?php esc_html_e('Please activate WP-SMS plugin then config your sms provider settings from WP-SMS dashboard', 'poshtvan') ?></p>
                </div>
            </div>
<?php endif;
    }

    static function get_provider_settings()
    {
    }

    static function validate_send_message($response)
    {
        if (is_wp_error($response)) {
            return [
                'msg' => esc_html__('Has error in send sms', 'poshtvan'),
                'status' => 400
            ];
        }
        return ['status' => 200, 'msg' => esc_html__('Sms send successfully', 'poshtvan')];
    }
}
