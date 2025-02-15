<?php
namespace poshtvan\app\providers\smsProviders;
interface providerInterface
{
    static function send($to, $msg, $patternID=null);
    static function render_settings();
    static function get_provider_settings();
    static function validate_send_message($response);
}