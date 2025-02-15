<?php
namespace poshtvan\app;

class Ticket_Conditions {
    private static $result = [];
    private static $error = false;
    private static $message = '';

    private static $product;
    private static $product_id = 0;
    private static $order;
    private static $order_id = 0;

    private static function clear_result(){
        static::$result = [];
        static::$error = false;
        static::$message = '';
    }

    private static function send_result($msg, $error = false){
        self::clear_result();
        static::$error = $error;
        static::$message = $msg;
    }

    public static function get_result(){
        static::$result = (object) [
            'error' => static::$error,
            'message' => static::$message
        ];
        return static::$result;
    }

    private static function condition_vriables($object_id){
        if(\poshtvan\app\product::is_accessible() && \poshtvan\app\options::get_is_show_wc_orders_field_in_user_ticket()){
            $product_id = Order_Data::get_product_id($object_id);
            if($product_id){
                static::$product_id = $product_id;
                static::$order_id = $object_id;
            } else {
                static::$product_id = \poshtvan\app\Ticket_Data::get_product_id($object_id);
                static::$order_id = \poshtvan\app\Ticket_Data::get_order_id($object_id);
            }

            static::$product = \wc_get_product(self::$product_id);
            static::$order = \wc_get_order(self::$order_id);
        }
    }

    /**
     * Ticket check conditions
     *
     * @param integer $object_id - ticket_id OR order_id
     * @return object|array
     */
    public static function condition_checker($object_id = 0){
        self::clear_result();

        if(!intval($object_id)) return self::get_result();

        static::condition_vriables($object_id);

        # --------- Start Conditions

        if(\poshtvan\app\product::is_accessible() && \poshtvan\app\options::get_is_show_wc_orders_field_in_user_ticket() && self::$product_id && self::$order_id){
            $is_stopped_support = \poshtvan\app\product::is_stopped_support(self::$product_id);
            $is_stopped_ago_days = \poshtvan\app\product::is_stopped_support_ago_days_status(self::$order_id);
    
            if(self::$product){
                if(self::$product->get_status() != 'publish'){
                    $text = \poshtvan\app\options::get_invalid_order_number_text();
                    $text = (!empty($text)) ? $text : esc_html__('The selected order number is invalid.', 'poshtvan');
                    static::send_result($text, true);
                }
            }
    
            if(!self::$order || !self::$order->has_status('completed')){
                $text = \poshtvan\app\options::get_invalid_order_number_text();
                $text = (!empty($text)) ? $text : esc_html__('The selected order number is invalid.', 'poshtvan');
                static::send_result($text, true);
            }
    
            if(self::$product_id && $is_stopped_support){
                $text = \poshtvan\app\options::get_product_stopped_support_text();
                $text = (!empty($text)) ? $text : esc_html__('Sorry, this product is no longer supported.', 'poshtvan');
                static::send_result($text, true);
            }
    
           if($is_stopped_ago_days){
                $text = \poshtvan\app\options::get_product_stopped_left_days_support_text();
                $text = (!empty($text)) ? $text : esc_html__('Sorry, the support for this product is only {{support_days}} days and the support time has passed.', 'poshtvan');
                $text = str_replace('{{support_days}}', \poshtvan\app\product::stopped_support_ago_days(self::$product_id), $text);
                static::send_result($text, true);
           }

           if(\poshtvan\app\subscriptions::is_active()){
                if(\poshtvan\app\subscriptions::is_subscription(self::$product)){
                    $is_product_subscription = \wcs_user_has_subscription(\get_current_user_id(), self::$product_id);
                    $active_order_subscription = \poshtvan\app\subscriptions::is_active_subscription(\get_current_user_id(), self::$product_id, self::$order_id);
                    if($is_product_subscription && $active_order_subscription != 'active'){
                        $text = \poshtvan\app\options::get_product_expired_subscription_text();
                        $text = (!empty($text)) ? $text : esc_html__('Sorry, this product is expired subscription.', 'poshtvan');
                        static::send_result($text, true);
                    }
                }
           }
        }

        # --------- End Conditions

        return static::get_result();
    }
}