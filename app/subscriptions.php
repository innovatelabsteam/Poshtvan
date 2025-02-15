<?php
namespace poshtvan\app;
class subscriptions
{
    static function init()
    {
        if(!self::is_active())
        {
            return false;
        }
        add_filter('poshtvan/orders/get_orders/before_append_to_orders_list', [__CLASS__, 'handle_order_list_append_permission'], 10, 3);
        add_filter('poshtvan/orders/get_orders/orders_list', [__CLASS__, 'handle_subscriptions_item_in_order_list'], 10, 3);
    }
    static function is_active()
    {
        /**
         * check is subscription plugin active
         * and subscription integrate is on in Poshtvan options
         */
        return self::is_active_plugin() && options::get_is_integrate_with_wc_subscriptions();
    }
    static function is_active_plugin()
    {
        /**
         * check is subscription plugin active
         */
        return tools::is_active_plugin('woocommerce-subscriptions/woocommerce-subscriptions.php');    
    }

    public static function is_subscription($product){
        return \WC_Subscriptions_Product::is_subscription($product);
    }
    static function handle_order_list_append_permission($permission, $product, $order)
    {
        return $permission && self::is_subscription($product) ? self::is_active_subscription($order->get_user_id(), $product->get_id(), $order->get_id()) : $permission;
    }
    # TODO: check & optimized
    public static function is_active_subscription($user_id, $product_id, $order_id){
        $status = false;
        if($user_id){
            $is_product_subscription = \wcs_user_has_subscription($user_id, $product_id);
            if($is_product_subscription){
                $user_subscriptions = \wcs_get_users_subscriptions($user_id);
                if($user_subscriptions){
                    foreach($user_subscriptions as $subscriptionID => $order_type){
                        $order = \wc_get_order($subscriptionID);
                        if($order->get_parent_id() == $order_id && $order->get_status() == 'active'){
                            $status = $order->get_status();
                            break;
                        }
                    }
                }
            }
        }

        return $status;
    }
    static function handle_subscriptions_item_in_order_list($available_orders, $user_id, $all_orders)
    {
        foreach($all_orders as $order)
            {
                if(!$order->has_status('completed'))
                {
                    continue;
                }
                $subscriptions = wcs_get_subscriptions_for_order($order);
                foreach($subscriptions as $subscription)
                {
                    if(!$subscription->has_status('active'))
                    {
                        continue;
                    }
                    $data = $subscription->get_data();
                    $line_items = $data['line_items'];
                    foreach($line_items as $item)
                    {
                        $available_orders[$order->get_id()][$item['product_id']] = array(
                            'title'=>$item['name'],
                            'product_id'=>$item['product_id']
                        );
                    }
                }
            }
            return $available_orders;
    }
}