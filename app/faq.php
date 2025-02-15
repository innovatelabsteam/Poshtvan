<?php
namespace poshtvan\app;
class faq
{
    static function registerPostType()
    {
        register_post_type(
            'mihanticket_faq',
            array(
                'labels' => array(
                    'name' => __('FAQ', 'poshtvan'),
                ),
                'public' => false,
                'show_ui' => true,
                'has_archive' => false,
                'show_in_menu' => 'poshtvan',
                'supports' => [null],
            )
        );
    }
    static function adminFaqMetaBoxes()
    {
        add_meta_box('mwtc_faq_product', __('Product', 'poshtvan'), [__CLASS__, 'adminFaqProductMetaBoxeContent'], null);
        add_meta_box('mwtc_faq_items', __('FAQ Items', 'poshtvan'), [__CLASS__, 'adminFaqItemsMetaBoxeContent']);
    }

    static function enqueueAdminScripts()
    {
        $screen = get_current_screen();
        if(is_object($screen))
        {
            if($screen->post_type == 'mihanticket_faq')
            {
                assets::enqueue_style('admin_faq_style', 'admin.faq');
                assets::enqueue_script('admin_faq_script', 'admin.faq', ['jquery', 'jquery-ui-sortable']);

                $data = [
                    'au' => admin_url('admin-ajax.php'),
                    'msg' => [
                        'items_min_error' => __('The number of items cannot be less than 1', 'poshtvan'),
                    ],
                ];

                wp_enqueue_editor();

                assets::localize_script('admin_faq_script', 'mwtc_faq', $data);
            }
        }
    }
    static function adminFaqProductMetaBoxeContent($post)
    {
        $products = product::getAllProducts();
        $faqProductID = get_post_meta($post->ID, 'mwtc_faq_product_id', true);
        ?>
        <?php wp_nonce_field('mwtc_faq_product_metabox', 'mwtc_product_metabox_nonce')?>
        <p>
            <label for="mwtc_faq_product_id"><?php esc_html_e('Product', 'poshtvan')?></label>
            <select name="mwtc_faq_product_id" id="mwtc_faq_product_id">
                <option value="0"><?php esc_html_e('Choose product', 'poshtvan')?></option>
                <?php foreach($products as $item): ?>
                    <option <?php selected($faqProductID, $item->get_id())?> value="<?php echo esc_attr($item->get_id())?>"><?php echo esc_html($item->get_title())?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }
    static function adminFaqItemsMetaBoxeContent($post)
    {
        $items = get_post_meta($post->ID, 'mwtc_faq_items', true);
        $view = files::get_file_path('views.admin.faq.items');
        $view ? include_once $view : null;
    }
    static function storeFaqMetaboxData($postID)
    {
        if(!wp_verify_nonce($_POST['mwtc_product_metabox_nonce'], 'mwtc_faq_product_metabox'))
        {
            return;
        }
        if(!current_user_can('edit_post', $postID))
        {
            return;
        }
        if(wp_is_post_revision($postID))
        {
            return;
        }
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        {
            return;
        }
        $faqProductID = isset($_POST['mwtc_faq_product_id']) && $_POST['mwtc_faq_product_id'] ? intval($_POST['mwtc_faq_product_id']) : false;
        if($faqProductID)
        {
            $updateFaqProductRes = update_post_meta($postID, 'mwtc_faq_product_id', $faqProductID);
            if($updateFaqProductRes)
            {
                $product = wc_get_product($faqProductID);
                wp_update_post([
                    'ID' => $postID,
                    'post_title' => $product->get_title(),
                ]);
            }
        }else{
            delete_post_meta($postID, 'mwtc_faq_product_id');
        }

        $faqItemsData = isset($_POST['faq-data']) && $_POST['faq-data'] ? $_POST['faq-data'] : false;
        if($faqItemsData)
        {
            update_post_meta($postID, 'mwtc_faq_items', $faqItemsData);
        }else{
            delete_post_meta($postID, 'mwtc_faq_items');
        }
    }

    static function getProductFaqItems($productID)
    {
        $args = [
            'post_type' => 'mihanticket_faq',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_key' => 'mwtc_faq_product_id',
            'meta_value' => intval($productID),
            'meta_compare' => '=',
        ];

        $faqPosts = get_posts($args);
        wp_reset_postdata();
        $items = [];
        foreach($faqPosts as $post)
        {
            $value = get_post_meta($post->ID, 'mwtc_faq_items', true);
            $items = $value ? array_merge($items, $value) : $items;
        }
        return $items;
    }
}
