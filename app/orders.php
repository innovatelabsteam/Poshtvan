<?php
namespace poshtvan\app;

class orders
{
    private static $_orders = [];
    static function is_order_accessible()
    {
        // check is woocommerce active
        return tools::isWoocommerceActive();
    }
    static function verify_new_ticket_fields()
    {
        $order_value = isset($_POST['mihanticket_woocommerce_order']) && sanitize_text_field(intval($_POST['mihanticket_woocommerce_order'])) ? intval($_POST['mihanticket_woocommerce_order']) : false;
        $productID = isset($_POST['product_id']) && sanitize_text_field(intval($_POST['product_id'])) ? intval($_POST['product_id']) : false;
        if($order_value && $productID)
        {
            $available_items = self::get_orders();
            if(in_array($order_value, array_keys($available_items)))
            {
                return true;
            }
            error::add_error('submit_new_ticket/fields_verification', esc_html__('You can not submit new ticket for this product', 'poshtvan'));
            return false;
        }
        if(tools::isWoocommerceActive())
        {
            if(!options::get_is_send_ticket_just_by_wc_order_item())
            {
                return true;
            }
            error::add_error('submit_new_ticket/fields_verification', esc_html__('Please choose order item', 'poshtvan'));
            return false;
        }
        return true;
    }
    static function handle_order_field_after_submit_new_ticket($ticket_id)
    {
        $order_value = isset($_POST['mihanticket_woocommerce_order']) && sanitize_text_field(intval($_POST['mihanticket_woocommerce_order'])) ? intval($_POST['mihanticket_woocommerce_order']) : false;
        $productID = isset($_POST['product_id']) && sanitize_text_field(intval($_POST['product_id'])) ? intval($_POST['product_id']) : false;
        if(!$order_value || !$productID){
            return false;
        }
        ticket_meta::update_meta($ticket_id, 'woo_order_id', $order_value); // orderID
        ticket_meta::update_meta($ticket_id, 'woocommerce_order_id', $productID); // productID
    }
    static function get_orders($user_id = false)
    {
        if(!self::is_order_accessible())
        {
            return false;
        }
        if(self::$_orders){
            return self::$_orders;
        }
        $user_id = $user_id ? intval($user_id) : get_current_user_id();
        $args = ['posts_per_page' => -1, 'customer_id' => $user_id];
        $orders = wc_get_orders($args);
        if($orders){
            foreach($orders as $order){
                if(!$order->has_status('completed')){
                    continue;
                }
                $items = $order->get_items();
                foreach($items as $item)
                {
                    $pr = $item->get_product();
                    if(!$pr)
                    {
                        continue;
                    }
                    
                    $permission = apply_filters('poshtvan/orders/get_orders/before_append_to_orders_list', true, $pr, $order);
                    if(!$permission){
                        continue;
                    }
                    if(intval($order->get_id()) && $pr->get_title() && $item->get_product_id()){
                        if(!\poshtvan\app\product::is_support_fully_stopped($item->get_product_id(), $order->get_id())){
                            if ( class_exists( 'WC_Subscriptions_Product' ) && \WC_Subscriptions_Product::is_subscription( $item->get_product_id() ) ) {
                                continue;
                            }
                            self::$_orders[$order->get_id()][$pr->get_ID()] = [
                                'title' => $pr->get_title() . ' - #' . $order->get_id(),
                                'product_id' => $item->get_product_id(),
                            ];
                        }
                    }
                }
            }
        }
        self::$_orders = apply_filters('poshtvan/orders/get_orders/orders_list', self::$_orders, $user_id, $orders);
        return self::$_orders;
    }
    static function render_orders_field($label = '', $class = '')
    {
        if(!self::is_order_accessible() || !options::get_is_show_wc_orders_field_in_user_ticket())
        {
            return false;
        }
        $is_required = options::get_is_send_ticket_just_by_wc_order_item();
        $orders = self::get_orders();
        $label = (!empty($label)) ? $label : esc_attr__('Orders', 'poshtvan');
        ?>
        <p class="<?php echo esc_attr($class) ?>">
            <label><?php echo esc_html($label) ?><?php echo $is_required ? ' *' : '';?></label>
            <select name="mihanticket_woocommerce_order" class="mihanticket-select" id="mihanticket_woocommerce_order">
                <?php if($orders): ?>
                    <option value="0" selected disabled><?php esc_html_e('Choose your item', 'poshtvan')?></option>
                    <?php foreach($orders as $orderID => $orderItems): ?>
                        <?php foreach($orderItems as $value):?>
                            <?php if(intval($orderID) && (is_array($value) && isset($value['title']) && isset($value['product_id']))): ?>
                                <option value="<?php echo esc_attr($orderID) ?>" data-product-id="<?php echo esc_attr($value['product_id']) ?>"><?php echo esc_html($value['title']); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="0"><?php esc_html_e('No any items found', 'poshtvan')?></option>
                <?php endif; ?>
            </select>
            <script>
                jQuery(document).ready(function($){
                    let selects = $(document).find('.mihanticket-select');

                    if(selects.length){
                        selects.select2({width: '100%'});
                    }
                });
            </script>
        </p>
        <?php
    }

    static function renderOrderRadoiButtons($label = '', $class = '')
    {
        if(!self::is_order_accessible() || !options::get_is_show_wc_orders_field_in_user_ticket())
        {
            return false;
        }
        $is_required = options::get_is_send_ticket_just_by_wc_order_item();
        $orders = self::get_orders();
        $label = (!empty($label)) ? $label : esc_attr__('Orders', 'poshtvan');
        ?>
        <div class="mwtc-orders-list">
            <?php foreach($orders as $orderID => $orderItems): ?>
                <?php foreach($orderItems as $order): ?>
                    <?php
                    $product = $order && is_array($order) && isset($order['product_id']) ? wc_get_product($order['product_id']) : false;
                    if(!$product)
                    {
                        continue;
                    }
                    ?>
                    <label for="mwtc-order-item-<?php echo esc_attr($orderID) . '-' . esc_attr($product->get_ID())?>">
                        <input type="radio" id="mwtc-order-item-<?php echo esc_attr($orderID) . '-' . esc_attr($product->get_ID())?>" name="mihanticket_woocommerce_order" value="<?php echo esc_attr($orderID) . '-' . esc_attr($product->get_ID())?>" orderid="<?php echo esc_attr($orderID)?>" prid="<?php echo esc_attr($product->get_ID())?>">
                        <span class="mwtc-order-item">
                            <span class="mwtc-title"><?php echo esc_html($product->get_title()); ?><br><span class="item-order-id"><?php echo esc_html__('Order ID: ','poshtvan') . intval($orderID);?></span></span>
                            <span class="mwtc-thumbnail">
                                <?php echo wp_kses_post($product->get_image())?>
                            </span>
                        </span>
                    </label>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <?php
    }

    public static function verify_order_stopped_support(){
        $order_id = isset($_POST['mihanticket_woocommerce_order']) && sanitize_text_field(intval($_POST['mihanticket_woocommerce_order'])) ? intval($_POST['mihanticket_woocommerce_order']) : false;
        $ticket_id = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : false;

        $ticket_meta = ticket_meta::get_meta($ticket_id, 'woo_order_id');

        $order_id = ($order_id) ? $order_id : ($ticket_id ? $ticket_meta : 0);

        $condition_checker = \poshtvan\app\Ticket_Conditions::condition_checker($order_id);

        if($condition_checker->error){
            error::add_error('submit_new_ticket/fields_verification', $condition_checker->message);
            return false;
        }
    }
}