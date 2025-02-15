<?php
namespace poshtvan\app\form;

use poshtvan\app\error;
use poshtvan\app\fields as AppFields;
use poshtvan\app\notice;

class fields
{
    private static $_notice_code='fields-new-record';
    static function handle_new_field()
    {
        $fieldName = isset($_POST['field_name']) && $_POST['field_name'] ? sanitize_text_field($_POST['field_name']) : false;
        $label = isset($_POST['field_label']) && $_POST['field_label'] ? sanitize_text_field($_POST['field_label']) : false;
        $type = isset($_POST['type']) && $_POST['type'] ? intval($_POST['type']) : false;
        $is_required = isset($_POST['is_required']) && $_POST['is_required'] ? intval($_POST['is_required']) : false;
        if(!$fieldName || !$label)
        {
            notice::add_notice(self::$_notice_code, esc_html__('Please complete all fields', 'poshtvan'));
            return false;
        }
        if(!\poshtvan\app\tools::isEnglish($fieldName))
        {
            notice::add_notice(self::$_notice_code, __('Field name must be entered in English letters', 'poshtvan'));
            return false;
        }
        $data = [
            'name' => $fieldName,
            'label' => $label,
            'type' => $type,
            'required' => $is_required
        ];
        $res = AppFields::add_new_field($data);
        if($res)
        {
            notice::add_notice(self::$_notice_code, esc_html__('New field is successfully added', 'poshtvan'), 'success');
        }else{
            notice::add_notice(self::$_notice_code, esc_html__('Sorry, Field is not added successfully', 'poshtvan'));
        }
    }
    static function show_fields()
    {
        $fields = AppFields::get_fields();
        if(!$fields)
        {
            ?>
            <div class="mw_row_wide">
                <p><?php esc_html_e('No any fields found', 'poshtvan')?></p>
            </div>
            <?php
            return false;
        }
        foreach($fields as $field):
        ?>
            <form class="mw_field_item">
                <div class="mw_row">
                    <div class="mw_td">
                        <span class="dashicons dashicons-menu"></span>
                        <input type="hidden" name="id" value="<?php echo esc_attr($field->id);?>">
                    </div>
                    <div class="mw_td">
                        <input autocomplete="off" type="text" name="field_name" value="<?php echo esc_attr($field->name)?>">
                    </div>
                    <div class="mw_td">
                        <input autocomplete="off" type="text" name="field_label" value="<?php echo esc_attr($field->label)?>">
                    </div>
                    <div class="mw_td">
                        <select name="type">
                            <option value="1"><?php esc_html_e('Text', 'poshtvan')?></option>
                        </select>
                    </div>
                    <div class="mw_td">
                        <select name="is_required">
                            <option <?php selected($field->required, 0)?> value="0"><?php esc_html_e('Not required', 'poshtvan')?></option>
                            <option <?php selected($field->required, 1)?> value="1"><?php esc_html_e('Required', 'poshtvan')?></option>
                        </select>
                    </div>
                    <div class="remove mw_td">
                        <span class="dashicons dashicons-no"></span>
                    </div>
                </div>
            </form>
        <?php
        endforeach;
    }
    static function render_extra_fields_in_user_new_ticket()
    {
        $all_fields = AppFields::get_fields();
        if(!$all_fields || !is_array($all_fields))
        {
            return false;
        }
        foreach($all_fields as $field):
        ?>
        <p>
            <label><?php echo esc_html($field->label) ?><?php echo $field->required ? ' *' : '';?></label>
            <input type="text" name="<?php echo esc_attr($field->name);?>" id="<?php echo esc_attr($field->id)?>">
        </p>
        <?php
        endforeach;
    }
    static function handle_user_ticket_extra_fields_verification()
    {
        $all_fields = AppFields::get_fields();
        if(!$all_fields || !is_array($all_fields))
        {
            return false;
        }
        foreach($all_fields as $field)
        {
            if(!isset($_POST[$field->name]) && $field->required)
            {
                error::add_error('submit_new_ticket/fields_verification', sprintf('%s: %s', esc_html__('Missing value for this field', 'poshtvan'), $field->label));
            }
        }
    }
    static function handle_user_ticket_extra_fields_save_value($ticket_id)
    {
        $all_fields = AppFields::get_fields();
        if(!$all_fields || !is_array($all_fields))
        {
            return false;
        }
        $field_meta = [];
        foreach($all_fields as $field)
        {
            if(isset($_POST[$field->name]))
            {
                $field_meta[$field->name] = $_POST[$field->name];
            }
        }
        if(!$field_meta)
        {
            return false;
        }
        $field_meta = serialize($field_meta);
        return AppFields::save_extra_fields_meta($ticket_id, $field_meta);
    }
    static function render_user_ticket_extra_fields($ticket_id)
    {
        $fields_meta = AppFields::get_extra_fields_value($ticket_id);
        $fields_meta = is_serialized($fields_meta) ? unserialize($fields_meta) : $fields_meta;
        $all_fields = AppFields::get_fields();
        foreach($all_fields as $field):
            if(isset($fields_meta[$field->name])):?>
                <span class="extra-field">
                    <span class="label"><?php esc_html($field->label)?></span>
                    <span class="value"><?php esc_html($fields_meta[$field->name])?></span>
                </span>
            <?php endif;
        endforeach;
    }
    static function render_admin_ticket_extra_fields($ticket_id)
    {
        $all_fields = AppFields::get_fields();
        $meta_fields_data = AppFields::get_extra_fields_value($ticket_id);
        $meta_fields_data = is_serialized($meta_fields_data) ? unserialize($meta_fields_data) : false;
        if($all_fields):?>
        <div class="extra-fields">
            <?php foreach($all_fields as $field):
                if(isset($meta_fields_data[$field->name])): ?>
                <div>
                    <span class="label"><?php esc_html($field->label)?>: </span>
                    <span class="value"><?php esc_html($meta_fields_data[$field->name])?></span>
                </div>
            <?php endif; endforeach;?>
        </div>
        <?php
        endif;
    }
    static function renderNewTicketContentField()
    {
        // wp_editor(null, 'ticket-content', ['textarea_name' => 'ticket-content']);
        ?>
        <textarea name="ticket-content" id="ticket-content" cols="30" rows="10"></textarea>
        <?php
    }
    static function sanitizeTicketContentField($content)
    {
        $content = urldecode($content);
        return sanitize_textarea_field($content);
    }
}