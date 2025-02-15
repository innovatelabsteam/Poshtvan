<?php
namespace poshtvan\app;

class Ticket_Data {
    public static function get_product_id($ticket_id){
        return (int) \poshtvan\app\ticket_meta::get_meta($ticket_id, 'woocommerce_order_id');
    }

    public static function get_order_id($ticket_id){
        return (int) \poshtvan\app\ticket_meta::get_meta($ticket_id, 'woo_order_id');
    }
}