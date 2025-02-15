<form id="poshtvan-option-panel-form" method="post" action="options.php">
    <?php settings_fields(\poshtvan\app\options::get_setting_group_name('notifications'));?>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Admin emails', 'poshtvan')?></h3>
        <p class="option_section_description"><?php esc_html_e('Email address that receive notification emails', 'poshtvan')?></p>
        <div class="option_field option_row_field">
            <label for="receive_email_notifications_email_address"><?php esc_html_e('Email address', 'poshtvan')?></label>
            <input type="email" value="<?php echo esc_attr(\poshtvan\app\options::get_receive_email_notifications_email_address())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('receive_email_notifications_email_address'))?>" id="receive_email_notifications_email_address">
        </div>
    </div>
    <div class="option_section">
        <h3><?php esc_html_e('Notify after submit new ticket', 'poshtvan')?></h3>
        <div class="option_field option_row_field">
            <span class="label"><?php esc_html_e('Send email to admin', 'poshtvan')?></span>
            <p class="solid_checkbox">
                <input <?php checked(\poshtvan\app\options::is_send_email_to_admin_after_submit_new_ticket())?> type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('send_email_to_admin_after_new_ticket'))?>" id="send_email_to_admin_after_new_ticket" value="1">
                <label for="send_email_to_admin_after_new_ticket"><?php esc_html_e('Activate', 'poshtvan')?></label>
            </p>
        </div>
        <div class="option_field option_row_field">
            <label for="after_new_ticket_admin_email_subject"><?php esc_html_e('Subject of email', 'poshtvan')?></label>
            <input type="text" value="<?php echo esc_attr(\poshtvan\app\options::get_after_new_ticket_admin_email_subject())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('after_new_ticket_admin_email_subject'))?>" id="after_new_ticket_admin_email_subject">
        </div>
        <div class="option_field editor_clean_design">
            <label for="after_new_ticket_admin_email_content"><?php esc_html_e('Content of email', 'poshtvan')?></label>
            <?php
            wp_editor(
                \poshtvan\app\options::get_after_new_ticket_admin_email_content(),
                \poshtvan\app\options::get_setting_name('after_new_ticket_admin_email_content'),
                [
                        'textarea_rows' => 10,
                ]
            );
            ?>
        </div>
        <div class="option_field no-y-padding">
            <p class="option_section_description no-y-padding"><?php esc_html_e('You can use this', 'poshtvan')?></p>
            <p class="option_section_description no-y-padding">
                <span><?php esc_html_e('Username', 'poshtvan')?>: </span><span>[[username]]</span>
                <span><?php esc_html_e('Display name', 'poshtvan')?>: </span><span>[[display_name]]</span>
                <span><?php esc_html_e('Ticket ID', 'poshtvan')?>: </span><span>[[ticket_id]]</span>
                <span><?php esc_html_e('Date', 'poshtvan')?>: </span><span>[[date]]</span>
            </p>
        </div>
    </div>
    <div class="option_section">
        <h3><?php esc_html_e('Notify after submit reply', 'poshtvan')?></h3>
        <div class="option_field option_row_field">
            <span class="label"><?php esc_html_e('Send email to admin', 'poshtvan')?></span>
            <div>
                <input value="1" <?php checked(\poshtvan\app\options::is_send_email_to_admin_after_submit_reply(), 1)?> type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('send_email_to_admin_after_submit_reply'))?>" id="send_email_to_admin_after_submit_reply">
                <label for="send_email_to_admin_after_submit_reply"><?php esc_html_e('Activate', 'poshtvan')?></label>
            </div>
        </div>
        <div class="option_field option_row_field">
            <label for="after_reply_admin_email_subject"><?php esc_html_e('Subject of email', 'poshtvan')?></label>
            <input type="text" value="<?php echo esc_attr(\poshtvan\app\options::get_after_reply_admin_email_subject())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('after_reply_admin_email_subject'))?>" id="after_reply_admin_email_subject">
        </div>
        <div class="option_field editor_clean_design">
            <label for="after_reply_admin_email_content"><?php esc_html_e('Content of email', 'poshtvan')?></label>
            <?php
            wp_editor(
                \poshtvan\app\options::get_after_reply_admin_email_content(),
                \poshtvan\app\options::get_setting_name('after_reply_admin_email_content'),
                [
                        'textarea_rows' => 10,
                ]
            );
            ?>
        </div>
        <div class="option_field no-y-padding">
            <p class="option_section_description no-y-padding"><?php esc_html_e('You can use this', 'poshtvan')?></p>
            <p class="option_section_description no-y-padding">
                <span><?php esc_html_e('Username', 'poshtvan')?>: </span><span>[[username]]</span>
                <span><?php esc_html_e('Display name', 'poshtvan')?>: </span><span>[[display_name]]</span>
                <span><?php esc_html_e('Ticket ID', 'poshtvan')?>: </span><span>[[ticket_id]]</span>
                <span><?php esc_html_e('Date', 'poshtvan')?>: </span><span>[[date]]</span>
            </p>
        </div>
    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('User emails', 'poshtvan')?></h3>
        <h3><?php esc_html_e('Notify after submit new ticket', 'poshtvan')?></h3>
        <div class="option_field option_row_field">
            <span class="label"><?php esc_html_e('Send email to user', 'poshtvan')?></span>
            <div>
                <input <?php checked(\poshtvan\app\options::is_send_email_to_user_after_submit_new_ticket())?> type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('send_email_to_user_after_new_ticket'))?>" id="send_email_to_user_after_new_ticket" value="1">
                <label for="send_email_to_user_after_new_ticket"><?php esc_html_e('Activate', 'poshtvan')?></label>
            </div>
        </div>
        <div class="option_field option_row_field">
            <label for="after_new_ticket_user_email_subject"><?php esc_html_e('Subject of email', 'poshtvan')?></label>
            <input type="text" value="<?php echo esc_attr(\poshtvan\app\options::get_after_new_ticket_user_email_subject())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('after_new_ticket_user_email_subject'))?>" id="after_new_ticket_user_email_subject">
        </div>
        <div class="option_field editor_clean_design">
            <label for="after_new_ticket_user_email_content"><?php esc_html_e('Content of email', 'poshtvan')?></label>
            <?php
            wp_editor(
                \poshtvan\app\options::get_after_new_ticket_user_email_content(),
                \poshtvan\app\options::get_setting_name('after_new_ticket_user_email_content'),
                [
                        'textarea_rows' => 10,
                ]
            );
            ?>
        </div>
        <div class="option_field no-y-padding">
            <p class="option_section_description no-y-padding"><?php esc_html_e('You can use this', 'poshtvan')?></p>
            <p class="option_section_description no-y-padding">
                <span><?php esc_html_e('Username', 'poshtvan')?>: </span><span>[[username]]</span>
                <span><?php esc_html_e('Display name', 'poshtvan')?>: </span><span>[[display_name]]</span>
                <span><?php esc_html_e('Ticket ID', 'poshtvan')?>: </span><span>[[ticket_id]]</span>
                <span><?php esc_html_e('Date', 'poshtvan')?>: </span><span>[[date]]</span>
            </p>
        </div>
    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Notify after submit reply', 'poshtvan')?></h3>
        <div class="option_field option_row_field">
            <span class="label"><?php esc_html_e('Send email to user', 'poshtvan')?></span>
            <div>
                <input value="1" <?php checked(\poshtvan\app\options::is_send_email_to_user_after_submit_reply(), 1)?> type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('send_email_to_user_after_submit_reply'))?>" id="send_email_to_user_after_submit_reply">
                <label for="send_email_to_user_after_submit_reply"><?php esc_html_e('Activate', 'poshtvan')?></label>
            </div>
        </div>
        <div class="option_field option_row_field">
            <label for="after_reply_user_email_subject"><?php esc_html_e('Subject of email', 'poshtvan')?></label>
            <input type="text" value="<?php echo esc_attr(\poshtvan\app\options::get_after_reply_user_email_subject())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('after_reply_user_email_subject'))?>" id="after_reply_user_email_subject">
        </div>
        <div class="option_field editor_clean_design">
            <label for="after_reply_user_email_content"><?php esc_html_e('Content of email', 'poshtvan')?></label>
            <?php
            wp_editor(
                \poshtvan\app\options::get_after_reply_user_email_content(),
                \poshtvan\app\options::get_setting_name('after_reply_user_email_content'),
                [
                        'textarea_rows' => 10,
                ]
            );
            ?>
        </div>
        <div class="option_field no-y-padding">
            <p class="option_section_description no-y-padding"><?php esc_html_e('You can use this', 'poshtvan')?></p>
            <p class="option_section_description no-y-padding">
                <span><?php esc_html_e('Username', 'poshtvan')?>: </span><span>[[username]]</span>
                <span><?php esc_html_e('Display name', 'poshtvan')?>: </span><span>[[display_name]]</span>
                <span><?php esc_html_e('Ticket ID', 'poshtvan')?>: </span><span>[[ticket_id]]</span>
                <span><?php esc_html_e('Date', 'poshtvan')?>: </span><span>[[date]]</span>
            </p>
        </div>
    </div>
</form>