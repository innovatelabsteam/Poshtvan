<?php
$date_type = \poshtvan\app\options::get_tickets_date_type();
?>
<form id="poshtvan-option-panel-form" method="post" action="options.php">
    <?php settings_fields(\poshtvan\app\options::get_setting_group_name('general')); ?>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Operators', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Choose image that you want to use as an avatar of operator.', 'poshtvan') ?></p>
        <div class="option_field option_row_field">
            <span class="label" for="operator_avatar"><?php esc_html_e('Operator Avatar', 'poshtvan') ?></span>
            <?php $operator_avatar = \poshtvan\app\users::get_operator_avatar(); ?>
            <div id="open_operator_avatar_media" class="avatar-preview-section">
                <img mwtc_default="<?php echo esc_url(\poshtvan\app\users::get_default_avatar_url()); ?>" src="<?php echo esc_url($operator_avatar['url']); ?>" alt="">
            </div>
            <div <?php echo !$operator_avatar['attachment_id'] ? 'style="display:none;"' : ''; ?> id="remove_avatar"><span><?php esc_html_e('Remove', 'poshtvan') ?></span></div>
            <input type="hidden" value="<?php echo esc_attr($operator_avatar['attachment_id']); ?>" id="operator_avatar_image_id" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('operator_avatar_image_id')) ?>">
        </div>
    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Operator display name', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('You can use this name instead of operator user display name.', 'poshtvan') ?></p>

        <div class="option_field option_row_field">
            <label for="operator_display_name"><?php esc_html_e('Operator display name', 'poshtvan') ?></label>
            <input type="text" id="operator_display_name" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('operator_display_name')) ?>" value="<?php echo esc_attr(\poshtvan\app\options::get_operator_display_name()) ?>">
        </div>
    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Roles', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Which of the roles has access to ticket list?', 'poshtvan') ?></p>
        <div class="option_field">
            <span class="label"><?php esc_html_e('Select roles', 'poshtvan') ?></span>
            <div>
                <?php
                $roles = \poshtvan\app\roles::get_roles_name();
                $white_list_roles = \poshtvan\app\options::get_roles_access_to_ticket_list();
                foreach ($roles as $role_key => $role) :
                ?>
                    <p class="solid_checkbox">
                        <input <?php echo in_array($role_key, $white_list_roles) ? 'checked' : ''; ?> type="checkbox" id="<?php echo esc_attr('mwtc_role_' . $role_key); ?>" value="<?php echo esc_attr($role_key); ?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('roles_access_to_ticket_list[]')); ?>">
                        <label for="<?php echo esc_attr('mwtc_role_' . $role_key); ?>"><?php echo esc_html(translate_user_role($role)) ?></label>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Date type', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Choose date type', 'poshtvan') ?></p>
        <div class="option_field option_row_field">
            <p class="solid_checkbox">
                <input <?php checked($date_type, 'solar'); ?> type="radio" id="tickets_date_type_solar" value="solar" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('tickets_date_type')) ?>">
                <label for="tickets_date_type_solar"><?php esc_html_e('Solar Calendar', 'poshtvan') ?></label>
            </p>
            <p class="solid_checkbox">
                <input <?php checked($date_type, 'gregorian'); ?> type="radio" id="tickets_date_type_gregorian" value="gregorian" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('tickets_date_type')) ?>">
                <label for="tickets_date_type_gregorian"><?php esc_html_e('Gregorian Calendar', 'poshtvan') ?></label>
            </p>
        </div>
    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('File uploading', 'poshtvan')?></h3>
        <p class="option_section_description"><?php esc_html_e('File uploading max size', 'poshtvan') ?></p>
        <div class="option_field">
            <label for="file_uploading_max_size"><?php esc_html_e('Enter value in MegaByte format', 'poshtvan') ?></label>
            <input type="number" value="<?php echo esc_attr(\poshtvan\app\options::get_file_uploading_max_size()) ?>" id="file_uploading_max_size" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('file_uploading_max_size')) ?>">
        </div>
        <div class="option_field">
            <span class="label"><?php esc_html_e('Allowed file types', 'poshtvan')?></span>
            <p class="option_field_description"><?php esc_html_e('What types of files can users upload?', 'poshtvan')?></p>
            <div class="option_field option_row_field">
                <?php
                $fileTypeWhiteList = \poshtvan\app\file_uploader::getFileTypesName();
                $selectedValidTypes = \poshtvan\app\options::getFileUploadingValidTypes();
                foreach($fileTypeWhiteList as $key => $title): ?>
                    <p class="solid_checkbox">
                        <label for="file_uploading_mime_types_whitelist_<?php echo esc_attr($key)?>_type"><?php echo esc_html($title); ?></label>
                        <input <?php checked(is_array($selectedValidTypes) && in_array($key, $selectedValidTypes));?> type="checkbox" id="file_uploading_mime_types_whitelist_<?php echo esc_attr($key)?>_type" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('file_uploading_mime_types_whitelist[]'))?>" value="<?php echo esc_attr($key)?>">
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="option_section">
        <div class="option_section_title"><?php esc_html_e('Messages', 'poshtvan')?></div>
        <div class="option_field option_row_field fit_label">
            <label for="new_ticket_top_message_box_text"><?php esc_html_e('Before new ticket message', 'poshtvan') ?></label>
            <textarea name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('new_ticket_top_message_box_text')) ?>" id="new_ticket_top_message_box_text" cols="30" rows="10"><?php echo esc_textarea(\poshtvan\app\options::get_new_ticket_top_message_box_text()) ?></textarea>
        </div>
        <div class="option_field">
            <span class="label"><?php esc_html_e('Before new ticket message box type', 'poshtvan') ?></span>
            <?php
            $before_new_ticket_message_box_type = \poshtvan\app\options::get_new_ticket_top_message_box_type();
            ?>
            <div class="option_field option_row_field">
                <p class="solid_checkbox">
                    <input <?php checked('success', $before_new_ticket_message_box_type) ?> type="radio" value="success" id="new_ticket_top_message_box_type-success" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('new_ticket_top_message_box_type')) ?>">
                    <label for="new_ticket_top_message_box_type-success"><?php esc_html_e('Success', 'poshtvan') ?></label>
                </p>
                <p class="solid_checkbox">
                    <input <?php checked('info', $before_new_ticket_message_box_type) ?> type="radio" value="info" id="new_ticket_top_message_box_type-info" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('new_ticket_top_message_box_type')) ?>">
                    <label for="new_ticket_top_message_box_type-info"><?php esc_html_e('info', 'poshtvan') ?></label>
                </p>
                <p class="solid_checkbox">
                    <input <?php checked('warning', $before_new_ticket_message_box_type) ?> type="radio" id="new_ticket_top_message_box_type-warning" value="warning" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('new_ticket_top_message_box_type')); ?>">
                    <label for="new_ticket_top_message_box_type-warning"><?php esc_html_e('Warning', 'poshtvan') ?></label>
                </p>
                <p class="solid_checkbox">
                    <input <?php checked('error', $before_new_ticket_message_box_type) ?> type="radio" value="error" id="new_ticket_top_message_box_type-error" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('new_ticket_top_message_box_type')) ?>">
                    <label for="new_ticket_top_message_box_type-error"><?php esc_html_e('Error', 'poshtvan') ?></label>
                </p>
            </div>
        </div>
    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Prefix text for replies', 'poshtvan')?></h3>
        <p class="option_section_description"><?php esc_html_e('Show this text as a prefix of operator replies.', 'poshtvan') ?></p>
        <div class="option_field option_row_field fit_label">
            <label for="replies_prefix_text"><?php esc_html_e('Prefix text for replies', 'poshtvan') ?></label>
            <textarea name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('replies_prefix_text')) ?>" id="replies_prefix_text" cols="30" rows="10"><?php echo esc_textarea(\poshtvan\app\options::get_replies_prefix_text()) ?></textarea>
        </div>
    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Suffix text for replies', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Show this text as a suffix of operator replies.', 'poshtvan') ?></p>
        <div class="option_field option_row_field fit_label">
            <label for="replies_suffix_text"><?php esc_html_e('Suffix text for replies', 'poshtvan') ?></label>
            <textarea name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('replies_suffix_text')); ?>" id="replies_suffix_text" cols="30" rows="10"><?php echo esc_textarea(\poshtvan\app\options::get_replies_suffix_text()) ?></textarea>
        </div>
    </div>
</form>