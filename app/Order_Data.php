<?php
namespace poshtvan\app;

class Order_Data {
    public static function get_product_id($order_id){
        $order = \wc_get_order($order_id);

        if($order){
            $items = $order->get_items();
            if($items) {
                foreach($items as $item){
                    if($order->get_id() == $order_id){
                        return $item->get_product_id();
                    }
                }
            }
        }

        return false;
    }
}