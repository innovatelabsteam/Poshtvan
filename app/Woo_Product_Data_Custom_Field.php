<?php
namespace poshtvan\app;

class Woo_Product_Data_Custom_Field {
    private static $fields = [];

    public static function init(){
        add_action('woocommerce_product_options_general_product_data', [__CLASS__, 'register_fields']); 
        add_action('woocommerce_process_product_meta', [__CLASS__, 'save_fields']);
    }

    public static function get_fields(){
        static::$fields = array(
            '_product_stop_support' => array(
                'label' => __('Product stop support', 'poshtvan'),
                'type' => 'checkbox',
            ),
            '_product_stop_support_after_days_status' => array(
                'label' => __('Product stop support after ago days', 'poshtvan'),
                'type' => 'checkbox',
            ),
            '_product_stop_support_after_days' => array(
                'label' => __('Support ago days', 'poshtvan'),
                'type' => 'text',
            ),
        );

        return static::$fields;
    }

    public static function register_fields(){
        global $woocommerce, $post;
        $fields = static::get_fields();
        echo '<div class="product_custom_field">';
        foreach($fields as $key => $value){
            if(isset($value['type'])){
                if(!isset($value['id'])) $value['id'] = $key;

                if($value['type'] == 'text'){
                    woocommerce_wp_text_input($value);
                } elseif($value['type'] == 'textarea'){
                    woocommerce_wp_textarea_input($value);
                } elseif($value['type'] == 'checkbox') {
                    woocommerce_wp_checkbox($value);
                } elseif($value['type'] == 'hidden') {
                    woocommerce_wp_hidden_input($value);
                } elseif($value['type'] == 'select') {
                    woocommerce_wp_select($value);
                } elseif($value['type'] == 'radio') {
                    woocommerce_wp_radio($value);
                }
            }
        }
        echo '</div>';
    }

    public static function save_fields($post_id){
        $fields = static::get_fields();
        foreach($fields as $key => $value){
            if(isset($value['type'])){
                if(in_array($value['type'], ['radio', 'checkbox'])){
                    $meta_value = isset($_POST[$key]);
                    update_post_meta($post_id, $key, $meta_value);
                }

                $meta_value = isset($_POST[$key]) ? $_POST[$key] : '';
                update_post_meta($post_id, $key, $meta_value);
            }
        }
    }
}