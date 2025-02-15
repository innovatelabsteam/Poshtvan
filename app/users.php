<?php

namespace poshtvan\app;

class users
{
    static function get_user_data($uid)
    {
        return get_userdata($uid);
    }
    static function get_dsiplay_name($uid)
    {
        #TODO: check this method
        // if($uid === "0")
        // {
        //     return esc_html__('Supporter Operator', 'poshtvan');
        // }
        // if(user_can($uid,'support_mihanwp_tickets')){
        //     return esc_html__('Supporter Operator', 'poshtvan');
        // }
        $user_data = self::get_user_data($uid);
        return $user_data ? $user_data->display_name : false;
    }
    static function get_email($uid)
    {
        #TODO: check this method
        // if($uid === "0")
        // {
        //     return esc_html__('Supporter Operator', 'poshtvan');
        // }
        $user_data = self::get_user_data($uid);
        return $user_data ? $user_data->user_email : false;
    }
    static function get_default_avatar_url()
    {
        $args = ['size' => 120, 'default' => 'mm', 'force_default' => true];
        $avatar_url = get_avatar_url(1, $args);
        return $avatar_url;
    }
    static function get_operator_avatar()
    {
        $avatar_image_id = options::get_operator_avatar_image_id();
        $avatar['url'] = $avatar_image_id ? wp_get_attachment_image_url($avatar_image_id) : self::get_default_avatar_url();
        $avatar['attachment_id'] = $avatar_image_id ? $avatar_image_id : '';
        return $avatar;
    }
    static function get_operator_display_name($uid)
    {
        $display_name = options::get_operator_display_name();
        if (!$display_name) {
            $display_name = self::get_dsiplay_name($uid);
        }
        return $display_name;
    }
    static function is_supporter($uid)
    {
        return user_can($uid, roles::SUPPORTER_CAP_NAME);
    }
    static function getPhoneNumber($uid)
    {
        $user = get_user_by('id', $uid);
        if(!$user)
        {
            return false;
        }
        // get phone driver option
        $driver = options::getUsersPhoneDriver();
        switch ($driver) {
            case 'woocommerce':
                $phone = get_user_meta($uid, 'billing_phone', true);
                return $phone ? phone::getValidatedPhoneNumber($phone) : false;
                break;
            case 'mihanpanel':
            default:
                if (get_user_meta($uid, 'mw_user_has_valid_phone', true)) {
                    $userPhone = get_user_meta($uid, 'mw_user_phone', true);
                    return $userPhone ? phone::getValidatedPhoneNumber($userPhone) : false;
                }
        }
    }
}
