<form id="poshtvan-option-panel-form" method="post" action="options.php">
    <?php settings_fields(\poshtvan\app\options::get_setting_group_name('woocommerce'));?>
    <?php if(!\poshtvan\app\tools::isWoocommerceActive()): ?>
        <span class="notice-wrapper">
            <div class="alert error">
                <?php esc_html_e('If you do not have WooCommerce installed, the settings on this page will not work properly.', 'poshtvan')?>
            </div>
        </span>
    <?php endif; ?>
    
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Woocommerce Orders', 'poshtvan')?></h3>
        <div class="option_field option_row_field">
            <span class="label"><?php esc_html_e('Show orders list in user ticket', 'poshtvan')?></span>
            <?php if(\poshtvan\app\tools::isWoocommerceActive()): ?>
                <p class="solid_checkbox">
                    <input <?php checked(\poshtvan\app\options::get_is_show_wc_orders_field_in_user_ticket())?> type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('show_wc_orders_field_in_user_ticket')); ?>" id="show_wc_orders_field_in_user_ticket" value="1">
                    <label for="show_wc_orders_field_in_user_ticket"><?php esc_html_e('Activate', 'poshtvan')?></label>
                </p>
            <?php else:
                $url = 'https://wordpress.org/plugins/woocommerce/';
                $link = '<a target="_blank" href="'.$url.'">'.__('Woocommerce', 'poshtvan').'</a>';
                $content = sprintf(esc_html__('At first, you need to install %s plugin', 'poshtvan'), $link);
                ?>
                <span><?php echo wp_kses($content, ['a' => ['href' => [], 'target' => []]]); ?></span>
            <?php endif; ?>
        </div>
        <div class="option_field option_row_field" mwdeps='show_wc_orders_field_in_user_ticket'>
            <span class="label"><?php esc_html_e('Send ticket just by choose order item', 'poshtvan')?></span>
            <?php if(\poshtvan\app\tools::isWoocommerceActive()): ?>
                <p class="solid_checkbox">
                    <input <?php checked(\poshtvan\app\options::get_is_send_ticket_just_by_wc_order_item())?> type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('send_ticket_just_by_wc_order_item')); ?>" id="send_ticket_just_by_wc_order_item" value="1">
                    <label for="send_ticket_just_by_wc_order_item"><?php esc_html_e('Activate', 'poshtvan')?></label>
                </p>
            <?php else:
                $url = 'https://wordpress.org/plugins/woocommerce/';
                $link = '<a target="_blank" href="'.$url.'">'.__('Woocommerce', 'poshtvan').'</a>';
                $content = sprintf(esc_html__('At first, you need to install %s plugin', 'poshtvan'), $link);
                ?>
                <span><?php echo wp_kses($content, ['a' => ['href' => [], 'target' => []]]); ?></span>
            <?php endif; ?>
        </div>
        <div class="option_field option_row_field" mwdeps='send_ticket_just_by_wc_order_item'>
            <span class="label"><?php esc_html_e('Activate steps in send new ticket', 'poshtvan')?></span>
            <?php if(\poshtvan\app\tools::isWoocommerceActive()): ?>
                <p class="solid_checkbox">
                    <input <?php checked(\poshtvan\app\options::get_is_steps_mode_in_new_ticket())?> type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('steps_mode_in_send_new_ticket'))?>" id="steps_mode_in_send_new_ticket" value="1">
                    <label for="steps_mode_in_send_new_ticket"><?php esc_html_e('Activate', 'poshtvan')?></label>
                </p>
            <?php else:
                $url = 'https://wordpress.org/plugins/woocommerce/';
                $link = '<a target="_blank" href="'.$url.'">'.__('Woocommerce', 'poshtvan').'</a>';
                $content = sprintf(esc_html__('At first, you need to install %s plugin', 'poshtvan'), $link);
                ?>
                <span><?php echo wp_kses($content, ['a' => ['href' => [], 'target' => []]]); ?></span>
            <?php endif; ?>
        </div>
        <div class="option_field option_row_field" mwdeps="show_wc_orders_field_in_user_ticket">
            <span class="label"><?php esc_html_e('Integrate with Woocommerce Subscription', 'poshtvan')?></span>
            <div class="solid_checkbox">
                <?php if(\poshtvan\app\subscriptions::is_active_plugin()):?>
                    <input <?php checked(\poshtvan\app\options::get_is_integrate_with_wc_subscriptions())?> value="1" type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('integrate_with_wc_subscriptions'))?>" id="integrate_with_wc_subscriptions">
                    <label for="integrate_with_wc_subscriptions"><?php esc_html_e('Activate', 'poshtvan')?></label>
                <?php else:
                    $url = 'https://woocommerce.com/products/woocommerce-subscriptions/';
                    $link = '<a target="_blank" href="'.$url.'">'.esc_html__('WC Subscription', 'poshtvan').'</a>';
                    $content = sprintf(esc_html__('At first, you need to install %s plugin', 'poshtvan'), $link);
                    ?>
                    <span><?php echo wp_kses($content, ['a' => ['href' => [], 'target' => []]]); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="option_section" mwdeps="show_wc_orders_field_in_user_ticket">
        <h3 class="option_section_title"><?php esc_html_e('Orders texts', 'poshtvan')?></h3>
        <div class="option_field" mwdeps='show_wc_orders_field_in_user_ticket'>
            <label for="ticket_validation_invalid_order_id_text"><?php esc_html_e('Invalid order number text', 'poshtvan')?></label>
            <input type="text" id="ticket_validation_invalid_order_id_text" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('ticket_validation_invalid_order_id_text'))?>" value="<?php echo esc_attr(\poshtvan\app\options::get_invalid_order_number_text())?>">
        </div>
        <div class="option_field" mwdeps='show_wc_orders_field_in_user_ticket'>
            <label for="ticket_validation_product_stopped_support_text"><?php esc_html_e('Product stopped support text', 'poshtvan')?></label>
            <input type="text" id="ticket_validation_product_stopped_support_text" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('ticket_validation_product_stopped_support_text')); ?>" value="<?php echo esc_attr(\poshtvan\app\options::get_product_stopped_support_text()); ?>">
        </div>
        <div class="option_field" mwdeps='show_wc_orders_field_in_user_ticket'>
            <label for="ticket_validation_product_stopped_left_days_support_text"><?php esc_html_e('Product stopped left days support text', 'poshtvan')?></label>
            <input type="text" id="ticket_validation_product_stopped_left_days_support_text" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('ticket_validation_product_stopped_left_days_support_text'))?>" value="<?php echo esc_attr(\poshtvan\app\options::get_product_stopped_left_days_support_text())?>">
        </div>
        <div class="option_field" mwdeps='show_wc_orders_field_in_user_ticket'>
            <label for="ticket_validation_expired_product_subscription_text"><?php esc_html_e('Product expired subscription text', 'poshtvan')?></label>
            <input type="text" id="ticket_validation_expired_product_subscription_text" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('ticket_validation_expired_product_subscription_text'))?>" value="<?php echo esc_attr(\poshtvan\app\options::get_product_expired_subscription_text())?>">
        </div>
    </div>
</form>