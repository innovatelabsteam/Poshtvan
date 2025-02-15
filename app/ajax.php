<?php

namespace poshtvan\app;

use poshtvan\app\providers\AiProviders\AiChatService;
use poshtvan\app\providers\smsProvider;

class ajax
{
    private static function send_res($res)
    {
        die(wp_json_encode($res));
    }
    static function init()
    {
        add_action('wp_ajax_mwtc_submit_new_ticket', [__CLASS__, 'add_new_ticket']);
        add_action('wp_ajax_show_user_ticket_list', [__CLASS__, 'show_user_ticket_list']);
        add_action('wp_ajax_get_ticket_replies', [__CLASS__, 'get_ticket_replies']);
        add_action('wp_ajax_mwtc_submit_reply', [__CLASS__, 'submit_reply']);
        add_action('wp_ajax_mwtc_ticket_solved', [__CLASS__, 'user_ticket_solved']);
        add_action('wp_ajax_poshtvan_resubmit_ticket_order_number', [__CLASS__, 'resubmit_ticket_order_id']);

        add_action('wp_ajax_mwtc_get_product_faq_items', [__CLASS__, 'get_product_faq_items']);
        add_action('wp_ajax_mwtc_search_subject', [__CLASS__, 'search_subject_in_website_content']);

        // admin requests
        add_action('wp_ajax_mwtc_load_sidebar_items', [__CLASS__, 'load_admin_sidebar_items']);
        add_action('wp_ajax_mwtc_admin_search_in_ticket', [__CLASS__, 'admin_search_in_tickets']);
        add_action('wp_ajax_mwtc_ticket_item_content', [__CLASS__, 'admin_load_ticket_item_content']);
        add_action('wp_ajax_mwtc_admin_submit_new_reply', [__CLASS__, 'admin_submit_new_reply']);
        add_action('wp_ajax_mwtc_admin_change_ticket_status', [__CLASS__, 'admin_change_ticket_status']);
        add_action('wp_ajax_mwtc_admin_edit_ticket', [__CLASS__, 'admin_edit_ticket']);

        add_action('wp_ajax_mwtc_admin_fields_update', [__CLASS__, 'admin_update_fields_items']);
        add_action('wp_ajax_mwtc_admin_delete_field_item', [__CLASS__, 'admin_delete_field_item']);

        add_action('wp_ajax_mwtc_admin_faq_get_new_item_view', [__CLASS__, 'admin_faq_get_new_item_view']);

        add_action('wp_ajax_mw_poshtvan_get_sms_provider_settings', [__CLASS__, 'admin_get_sms_provider_settings']);

        add_action('wp_ajax_pv_connect_to_hooshina', [__CLASS__, 'handle_connect_to_hooshina']);
        add_action('wp_ajax_pv_disconnect_hooshina', [__CLASS__, 'handle_disconnect_hooshina']);
        add_action('wp_ajax_pv_chat_with_ai', [__CLASS__, 'handle_chat_with_ai']);
    }
    static function admin_delete_field_item()
    {
        $res['status'] = 400;
        $res['msg'] = esc_html__('Has Error!', 'poshtvan');
        $field_id = isset($_POST['field_id']) ? intval(sanitize_text_field($_POST['field_id'])) : false;
        if (!$field_id) {
            $res['msg'] = esc_html__('Invalid request', 'poshtvan');
            self::send_res($res);
        }
        $delete_res = fields::delete_field($field_id);
        if (!$delete_res) {
            $res['msg'] = esc_html__('Has error while deleting field item', 'poshtvan');
            self::send_res($res);
        }
        $res['msg'] = esc_html__('Successfully deleting', 'poshtvan');
        $res['status'] = 200;
        self::send_res($res);
    }
    static function admin_update_fields_items()
    {
        $res['status'] = 400;
        $res['msg'] = esc_html__('Has Error!', 'poshtvan');
        $fields_data = isset($_POST['fields']) && $_POST['fields'] ? $_POST['fields'] : false;
        if (!$fields_data) {
            $res['msg'] = esc_html__('Missing some fields', 'poshtvan');
            self::send_res($res);
        }
        $errors = [];
        foreach ($fields_data as $index => $item) {
            $parse_data = [];
            parse_str($item, $parse_data);
            $id = isset($parse_data['id']) ? sanitize_text_field($parse_data['id']) : false;
            $name = isset($parse_data['field_name']) ? sanitize_text_field($parse_data['field_name']) : false;
            $label = isset($parse_data['field_label']) ? sanitize_text_field($parse_data['field_label']) : false;
            if (!$id) {
                $errors[] = esc_html__('ID not found for some of fields', 'poshtvan');
                continue;
            }
            if (!$name || !$label) {
                $errors[] = sprintf('%s: [ %s ]', esc_html__('Has error in update this field with this id', 'poshtvan'), $id);
                continue;
            }
            if(!\poshtvan\app\tools::isEnglish($name))
            {
                $errors[] = sprintf('%s [ %s ]', esc_html__('Field name must be entered in English letters', 'poshtvan'), $name);
                continue;
            }
            $is_require = isset($parse_data['is_required']) ? intval($parse_data['is_required']) : false;
            $type = isset($_POST['type']) ? intval($_POST['type']) : false;

            $data = [];
            $data['name'] = $name ? $name : false;
            $data['label'] = $label ? $label : false;
            $data['required'] = $is_require ? $is_require : false;
            $data['type'] = $type ? $type : false;
            $data['priority'] = $index;

            fields::update_fields($id, $data, $error);
            if ($error) {
                $errors[] = sprintf('%s: %s', esc_html__('Has error in update this field', 'poshtvan'), $name);
            }
        }
        if (!empty($errors)) {
            $res['msg'] = $errors;
        } else {
            $res['status'] = 200;
            $res['msg'] = esc_html__('Update successfully', 'poshtvan');
        }
        self::send_res($res);
    }
    static function admin_edit_ticket()
    {
        self::check_nonce();
        $res['status'] = 400;
        $res['msg'] = esc_html__("Has error.", 'poshtvan');
        $ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : false;
        $uid = get_current_user_id();
        if (tickets::get_ticket_owner($ticket_id) !== $uid) {
            $res['msg'] = esc_html__('Sorry, you are not the ticket owner.', 'poshtvan');
            self::send_res($res);
        }
        $new_content = isset($_POST['new_content']) ? \poshtvan\app\form\fields::sanitizeTicketContentField($_POST['new_content']) : false;
        $update_res = tickets::update_ticket($ticket_id, $new_content);
        if (!$update_res) {
            $res['msg'] = esc_html__('Ticket not edited', 'poshtvan');
            self::send_res($res);
        }
        $res['msg'] = esc_html__('Successfully edited.', 'poshtvan');
        $res['status'] = 200;
        $res['new_content'] = $new_content;
        self::send_res($res);
    }
    static function user_ticket_solved()
    {
        self::check_nonce();
        $res['status'] = 400;
        $res['msg'] = esc_html__("Has error.", 'poshtvan');
        $ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : false;
        $uid = get_current_user_id();
        if (tickets::get_ticket_owner($ticket_id) !== $uid) {
            $res['msg'] = esc_html__('Sorry, you are not the ticket owner.', 'poshtvan');
            self::send_res($res);
        }

        do_action('poshtvan_before_change_ticket_status_to_user_solved', $ticket_id, $uid);
        $change_res = tickets::change_ticket_status($ticket_id, tickets::STATUS_CODE_ANSWERED, $uid);
        if (!$change_res) {
            $res['msg'] = esc_html__('Has error in change status operation.', 'poshtvan');
            self::send_res($res);
        }
        do_action('poshtvan_after_change_ticket_status_to_user_solved', $ticket_id, $uid);

        $res['msg'] = esc_html__('Ticket Solved!', 'poshtvan');
        $res['status'] = 200;
        $res['status_data'] = tickets::status_convert(tickets::STATUS_CODE_ANSWERED);
        self::send_res($res);
    }
    static function admin_change_ticket_status()
    {
        self::check_nonce();
        $res['status'] = 400;
        $res['msg'] = esc_html__("Has error.", 'poshtvan');
        $status = isset($_POST['status']) ? $_POST['status'] : false;
        $status_code = tickets::get_status_code($status);
        $ticket_id = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : false;
        if (!$status || !$ticket_id) {
            $res['msg'] = esc_html__('Invalid ticket-ID or ticket status', 'poshtvan');
            self::send_res($res);
        }

        do_action('poshtvan_before_change_ticket_status_by_operator', $ticket_id, $status_code);
        $change_status = tickets::change_ticket_status($ticket_id, $status_code);
        if ($change_status) {
            $res['status'] = 200;
            $res['msg'] = esc_html__('Ticket status has successfully changed', 'poshtvan');
            do_action('poshtvan_after_change_ticket_status_by_operator', $ticket_id, $status_code);
        }
        self::send_res($res);
    }
    static function load_admin_sidebar_items()
    {
        self::check_nonce();
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $filters = isset($_POST['filters']) ? tools::sanitizeArrayValues($_POST['filters']) : false;
        $tickets = tickets::get_tickets(tickets::TICKET_LIST_LIMIT, $offset, $filters);
        if (!$tickets) {
            $res['items'] = false;
            $res['end'] = esc_html__('No more tickets', 'poshtvan');
            self::send_res($res);
        }
        $ticket_sidebar_items = files::get_file_path('views.admin.ticket.ticket-sidebar');
        ob_start();
        $ticket_sidebar_items ? include $ticket_sidebar_items : null;
        $res['items'] = ob_get_clean();
        $res['new_offset'] = $offset + tickets::TICKET_LIST_LIMIT;
        self::send_res($res);
    }
    static function admin_search_in_tickets()
    {
        self::check_nonce();
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : false;
        $res['items'] = false;
        if (!$search) {
            self::send_res($res);
        }
        $tickets = tickets::search_in_ticket($search);
        if (!$tickets) {
            self::send_res($res);
        }
        $ticket_sidebar_items = files::get_file_path('views.admin.ticket.ticket-sidebar');
        ob_start();
        $ticket_sidebar_items ? include $ticket_sidebar_items : null;
        $res['items'] = ob_get_clean();
        self::send_res($res);
    }
    static function admin_load_ticket_item_content()
    {
        self::check_nonce();
        $res['status'] = 400;
        $res['msg'] = esc_html__("Has error.", 'poshtvan');
        $ticket_id = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : false;
        if (!$ticket_id) {
            $res['msg'] = esc_html__("TicketID is required!", 'poshtvan');
            self::send_res($res);
        }
        $ticket_data = tickets::get_ticket_data(true, ['id', 'subject', 'user_id', 'content', 'status', 'created_date'], ['id' => '%d'], [$ticket_id]);
        if (!$ticket_data) {
            $res['msg'] = esc_html__('Ticket not found!', 'poshtvan');
            self::send_res($res);
        }
        $replies = tickets::get_ticket_replies($ticket_id);
        $ticket_content_view = files::get_file_path('views.admin.ticket.ticket-content');
        ob_start();
        $ticket_content_view ? include $ticket_content_view : null;
        $res['content'] = ob_get_clean();
        $res['msg'] = null;
        $res['status'] = 200;
        self::send_res($res);
    }
    static function admin_submit_new_reply()
    {
        self::check_nonce();
        $res['status'] = 400;
        $res['msg'] = esc_html__("Has error.", 'poshtvan');
        $ticket_id = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : false;
        if (!$ticket_id) {
            $res['msg'] = esc_html__("TicketID is required!", 'poshtvan');
            self::send_res($res);
        }

        do_action('poshtvan/admin_ticket/verification');
        $error = error::get_error_messages('submit_admin_ticket/fields_verification');
        if ($error) {
            $res['msg'] = $error;
            self::send_res($res);
        }
        
        $uid = get_current_user_id();
        $content = isset($_POST['ticket_content']) ? \poshtvan\app\form\fields::sanitizeTicketContentField($_POST['ticket_content']) : false;
        if (!$content) {
            $res['msg'] = esc_html__("Ticket content is required!", 'poshtvan');
            self::send_res($res);
        }
        $insert_id = tickets::add_new($uid, null, $content, $ticket_id);
        if (!$insert_id) {
            $res['msg'] = esc_html__("Has error in insert reply.", 'poshtvan');
            self::send_res($res);
        }
        $fileData = isset($_FILES['fileField']) ? $_FILES['fileField'] : false;
        if ($fileData) {
            tickets::add_attachment($insert_id, $fileData);
        }
        // update ticket status
        tickets::change_ticket_status($ticket_id, tickets::STATUS_CODE_ANSWERED);
        $res['status'] = 200;
        $res['msg'] = esc_html__("Your Reply Sent.", 'poshtvan');
        self::send_res($res);
    }
    private static function check_nonce($action = 'mwtc_ticket_actions', $query_args = 'nonce')
    {
        check_ajax_referer($action, $query_args);
    }
    static function add_new_ticket()
    {
        self::check_nonce();
        $res['status'] = 400;
        $res['msg'] = esc_html__("Please complete the form!", 'poshtvan');
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : false;
        $content = isset($_POST['content']) ? \poshtvan\app\form\fields::sanitizeTicketContentField($_POST['content']) : false;
        do_action('poshtvan/new_ticket/before_verification');
        $error = error::get_error_messages('submit_new_ticket/fields_verification');
        if ($error) {
            $res['msg'] = $error;
            self::send_res($res);
        }
        if (!$title || !$content) {
            self::send_res($res);
        }
        $uid = get_current_user_id();
        # TODO: handle order id
        $insert_id = tickets::add_new($uid, $title, $content);
        if (!$insert_id) {
            $res['msg'] = esc_html__('Has error in submit new ticket process.', 'poshtvan');
            self::send_res($res);
        }
        $fileData = isset($_FILES['fileField']) ? $_FILES['fileField'] : false;
        if ($fileData) {
            tickets::add_attachment($insert_id, $fileData);
        }
        do_action('poshtvan/new_ticket/after_submit', $insert_id);
        $res['status'] = 200;
        $res['msg'] = esc_html__('Your ticket successfully submit.', 'poshtvan');
        self::send_res($res);
    }
    static function show_user_ticket_list()
    {
        self::check_nonce();
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $ticket_list = tickets::get_user_ticket_list(get_current_user_id(), tickets::TICKET_LIST_LIMIT, $offset);
        if (!$ticket_list) {
            $res['items'] = false;
            $res['end'] = esc_html__('No more tickets', 'poshtvan');
            self::send_res($res);
        }
        $view = files::get_file_path('views.user.ticket.ticket-item');
        ob_start();
        $view ? include $view : false;
        $res['items'] = ob_get_clean();
        $res['new_offset'] = $offset + tickets::TICKET_LIST_LIMIT;
        self::send_res($res);
    }
    static function get_ticket_replies()
    {
        self::check_nonce();
        $ticket_id = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : false;
        $replies = tickets::get_ticket_replies($ticket_id, get_current_user_id());
        $replies_view = files::get_file_path('views.user.ticket.reply-item');
        ob_start();
        $replies_view ? include $replies_view : false;
        $res['replies'] = ob_get_clean();
        $res['ticket_status'] = tickets::get_ticket_status_data($ticket_id);
        self::send_res($res);
    }
    static function submit_reply()
    {
        self::check_nonce();
        $res['status'] = 400;
        $res['msg'] = esc_html__("Please Complete Form.", 'poshtvan');
        $content = isset($_POST['content']) ? \poshtvan\app\form\fields::sanitizeTicketContentField($_POST['content']) : false;
        $ticket_id = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : false;
        do_action('poshtvan/reply_ticket/before_submit_reply');
        $error = error::get_error_messages('submit_new_ticket/fields_verification');
        if ($error) {
            $res['msg'] = $error;
            self::send_res($res);
        }
        if (!$ticket_id || !$content) {
            self::send_res($res);
        }
        $uid = get_current_user_id();
        $owner = tickets::get_ticket_owner($ticket_id);
        if ($owner !== $uid) {
            $res['msg'] = esc_html__('Sorry, you are not the ticket owner.', 'poshtvan');
            self::send_res($res);
        }
        $insert_id = tickets::add_new($uid, null, $content, $ticket_id);
        if (!$insert_id) {
            $res['msg'] = esc_html__("Has error in insert reply.", 'poshtvan');
            self::send_res($res);
        }
        $fileData = isset($_FILES['fileField']) ? $_FILES['fileField'] : false;
        if ($fileData) {
            tickets::add_attachment($insert_id, $fileData);
        }
        tickets::change_ticket_status($ticket_id, tickets::STATUS_CODE_PENDING, $uid);
        $res['status'] = 200;
        $res['msg'] = esc_html__("Successfully insert reply", 'poshtvan');
        self::send_res($res);
    }

    public static function resubmit_ticket_order_id()
    {
        self::check_nonce();
        $result = [];
        $ticket_id = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : false;
        $order_id = isset($_POST['order_id']) ? intval(sanitize_text_field($_POST['order_id'])) : false;
        $product_id = isset($_POST['product_id']) ? intval(sanitize_text_field($_POST['product_id'])) : false;
        $condition_checker = \poshtvan\app\Ticket_Conditions::condition_checker($order_id);

        if ($condition_checker->error) {
            $result['msg'] = $condition_checker->message;
            $result['has_error'] = true;
            $result['type'] = 'error';
            self::send_res($result);
        }

        $update_meta = \poshtvan\app\ticket_meta::update_meta($ticket_id, 'woo_order_id', $order_id);
        if ($update_meta) {
            $result['status'] = 200;
            $result['type'] = 'success';
            $result['msg'] = esc_html__('The order number has been submitted.', 'poshtvan');
            $update_meta = \poshtvan\app\ticket_meta::update_meta($ticket_id, 'woocommerce_order_id', $product_id);
        } else {
            $result['status'] = 500;
            $result['has_error'] = true;
            $result['type'] = 'error';
            $result['msg'] = esc_html__('Failed, try again.', 'poshtvan');
        }
        self::send_res($result);
    }
    static function get_product_faq_items()
    {
        self::check_nonce();
        $res = [
            'status' => 400,
        ];
        $productID = isset($_POST['product_id']) && $_POST['product_id'] ? intval($_POST['product_id']) : false;
        if (!$productID) {
            $res['msg'] = __('Missing product id', 'poshtvan');
            self::send_res($res);
        }
        $faqItems = faq::getProductFaqItems($productID);
        $faqView = files::get_file_path('views.user.ticket.faq');

        ob_start();
        $faqView ? include $faqView : null;
        $res['faq_view'] = ob_get_clean();
        $res['status'] = 200;

        self::send_res($res);
    }
    static function search_subject_in_website_content()
    {
        self::check_nonce();
        $res = [
            'status' => 400,
        ];
        $search = isset($_POST['search']) && $_POST['search'] ? sanitize_text_field($_POST['search']) : false;
        if (!$search) {
            $res['msg'] = __('Missing search query text', 'poshtvan');
            self::send_res($res);
        }
        $posts = posts::searchInPosts($search, ['post', 'docs']);
        $searchView = files::get_file_path('views.user.ticket.subject_search');
        ob_start();
        $searchView ? include $searchView : null;
        $res['view'] = ob_get_clean();

        $res['status'] = 200;
        self::send_res($res);
    }
    static function admin_faq_get_new_item_view()
    {
        // self::check_nonce();
        $res = [
            'status' => 400,
        ];
        $fieldID = isset($_POST['new_id']) ? sanitize_text_field($_POST['new_id']) : false;
        if(!$fieldID)
        {
            $res['msg'] = __('Missing field id value', 'poshtvan');
            self::send_res($res);
        }
        $isNew = true;
        $view = \poshtvan\app\files::get_file_path('views.admin.faq.item');
        ob_start();
        $view ? include $view : null;
        $res['field_view'] = ob_get_clean();

        $res['status'] = 200;
        self::send_res($res);
    }

    static function admin_get_sms_provider_settings()
    {
        $provider = isset($_POST['provider']) && $_POST['provider'] ? sanitize_text_field($_POST['provider']) : false;
        if (!$provider) {
            esc_html_e('Provider must send!', 'poshtvan');
        }
        if (!smsProvider::validate_provider($provider)) {
            esc_html_e('Provider is not valid.', 'poshtvan');
            die();
        }
        smsProvider::show_provider_settings($provider);
        die();
    }

    public static function handle_connect_to_hooshina()
    {
        $connectUrl = AiChatService::use()->getConnectUrl();

        if (is_wp_error($connectUrl) || empty($connectUrl))
            wp_send_json_error();

        wp_send_json_success(['redirect' => $connectUrl]);
    }

    public static function handle_disconnect_hooshina()
    {
        $data = AiChatService::use()->getCurrentConnectionData();

        if (is_wp_error($data) || empty($data))
            wp_send_json_error();

        $revoke = AiChatService::use()->revokeConnection($data->auth);
        if (is_wp_error($revoke) || empty($revoke))
            wp_send_json_error();

        notice::add_notice('admin-panel-ai-menu', __('Ai chat disabled.', 'poshtvan'), 'success', 'cookie');

        wp_send_json_success();
    }

    public static function handle_chat_with_ai()
    {
        if (!options::ai_chat_is_activated())
            wp_send_json_error(['msg' => __('This service is currently unavailable.', 'poshtvan')]);

        $message = sanitize_text_field($_POST['chat_message']);

        if (empty($message))
            wp_send_json_error(['msg' => __('Please type question...', 'poshtvan')]);

        $data = AiChatService::use()->sendMessage($message);

        if (is_wp_error($data) || !is_object($data))
            wp_send_json_error(['msg' => __('No response received.', 'poshtvan')]);

        if (!isset($data->data))
            wp_send_json_error(['msg' => __('The response is invalid, please try again.', 'poshtvan')]);

        wp_send_json_success(['answer' => $data->data->content]);
    }
}
