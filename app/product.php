<?php
namespace poshtvan\app;
class product
{
    static function is_accessible()
    {
        return tools::isWoocommerceActive();
    }
    static function get_product($product_id)
    {
        return self::is_accessible() && $product_id ? wc_get_product($product_id) : false;
    }
    static function get_product_name($product_id)
    {
        if(!self::is_accessible() || !$product_id)
        {
            return false;
        }
        $product = self::get_product($product_id);
        return $product ? $product->get_title() : false;
    }

    public static function is_stopped_support($product_id){
        return (bool) get_post_meta($product_id, '_product_stop_support', true);
    }

    public static function stopped_support_ago_days($product_id){
        return (int) get_post_meta($product_id, '_product_stop_support_after_days', true);
    }

    public static function is_stopped_support_ago_days_status($order_id, $user_id = 0){
        $user_id = ($user_id) ? intval($user_id) : get_current_user_id();

        if(!$user_id) return false;

        $order = \wc_get_order($order_id);

        if(!$order) return false;

        if($order->get_customer_id() == $user_id){
            $items = $order->get_items();
            if($order->has_status('completed')) {
                foreach($items as $item){
                    if($order->get_id() == $order_id){
                        $status = (bool) get_post_meta($item->get_product_id(), '_product_stop_support_after_days_status', true);
                        $days = static::stopped_support_ago_days($item->get_product_id());
                        if($status && intval($days)){
                            $now = time();
                            $ago = strtotime($order->get_date_created()->date('Y-m-d H:i:s'));
                            $diff = $now - $ago;
                            $ago_days = floor($diff / (60 * 60 * 24));
                            $ago_hours = round(($diff - $ago_days * 60 * 60 * 24) / (60 * 60));
                            return $ago_days > $days;
                        }
                    }
                }
            }
        }

        return false;
    }
    
    public static function is_support_fully_stopped($product_id, $order_id = 0){
        if($product_id){
            $product = \wc_get_product($product_id);
            if(($product && $product->get_status() != 'publish') || self::is_stopped_support($product_id) || ($order_id && self::is_stopped_support_ago_days_status($order_id))){
                return true;
            }
        }
        return false;
    }
    static function getAllProducts()
    {
        if(!self::is_accessible())
        {
            return false;
        }
        $args = [
            'status' => 'publish',
            'limit' => -1,
        ];
        return wc_get_products($args);
    }
}