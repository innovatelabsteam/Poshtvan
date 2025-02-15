<?php
$date_type = \poshtvan\app\options::get_tickets_date_type();
?>
<form id="poshtvan-option-panel-form" method="post" action="options.php">
    <?php settings_fields(\poshtvan\app\options::get_setting_group_name('sms-notifications')); ?>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Phone settings', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Choose driver and configuration for getting users and operators phone number', 'poshtvan') ?></p>
        <div class="option_field option_row_field">
            <label><?php esc_html_e('Users phone driver', 'poshtvan') ?></label>
            <?php
            $driversList = \poshtvan\app\options::getUserPhoneDriversList();
            $activeDriver = \poshtvan\app\options::getUsersPhoneDriver();
            ?>
            <select name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_notification_users_phone_driver')) ?>" id="sms_notification_users_phone_driver">
                <?php if ($driversList) : ?>
                    <option value="0" disabled selected><?php esc_html_e('Select driver', 'poshtvan'); ?></option>
                    <?php foreach ($driversList as $driverKey => $driverName) : ?>
                        <option <?php selected($activeDriver, $driverKey) ?> value="<?php echo esc_attr($driverKey) ?>"><?php echo esc_html($driverName) ?></option>
                    <?php endforeach; ?>
                <?php else : ?>
                    <option value="0" disabled selected><?php esc_html_e("Don't found any driver", 'poshtvan'); ?></option>
                <?php endif; ?>
            </select>
        </div>

        <p class="option_section_description"><?php esc_html_e('Which operator must get admin notifications?', 'poshtvan') ?></p>
        <div class="option_field option_row_field">
            <label><?php esc_html_e('Choose Operator', 'poshtvan') ?></label>
            <?php
            $opertarosNotificationReceiverList = \poshtvan\app\options::getOperatorUsersList();
            $activeOperatorNotificationReceiver = \poshtvan\app\options::getActiveOperatorSmsNotificationReceiver();
            ?>
            <select class="select2" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_notification_operator_receiver')) ?>" id="sms_notification_operator_receiver">
                <?php if ($opertarosNotificationReceiverList) : ?>
                    <option value="0" disabled selected><?php esc_html_e('Choose Operator', 'poshtvan'); ?></option>
                    <?php foreach ($opertarosNotificationReceiverList as $operatorUser) : ?>
                        <option <?php selected($activeOperatorNotificationReceiver, $operatorUser->ID) ?> value="<?php echo esc_attr($operatorUser->ID) ?>"><?php echo esc_html(sprintf('%s (%s)', $operatorUser->user_login, $operatorUser->display_name)) ?></option>
                    <?php endforeach; ?>
                <?php else : ?>
                    <option value="0" disabled selected><?php esc_html_e("Didn't find any operator", 'poshtvan'); ?></option>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Sms providers settings', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Choose you sms provider from list', 'poshtvan') ?></p>
        <div class="option_field option_row_field">
            <label><?php esc_html_e("Select Provider", "poshtvan"); ?></label>
            <?php
            $providers = \poshtvan\app\providers\smsProvider::get_providers_title();
            $active_provider = \poshtvan\app\options::getActiveSmsProvider();
            ?>
            <select name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('active_sms_provider')) ?>" id="active_sms_provider">
                <?php if ($providers) : ?>
                    <option value="0" disabled selected><?php esc_html_e('Select SMS Provider', 'poshtvan'); ?></option>
                    <?php foreach ($providers as $provider_slug => $provider_title) : ?>
                        <option <?php selected($active_provider, $provider_slug); ?> value="<?php echo esc_attr($provider_slug); ?>"><?php echo esc_html($provider_title); ?></option>
                    <?php endforeach; ?>
                <?php else : ?>
                    <option value="0" disabled selected><?php esc_html_e("Don't found any SMS provider!", 'poshtvan'); ?></option>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div class="poshtvan_sms_provider_settings">
        <?php do_action('poshtvan/panel/sms_provider_settings'); ?>
    </div>

    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('New ticket Operator notification', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Send SMS to operator after submit new ticket', 'poshtvan') ?></p>

        <div class="option_field option_row_field">
            <p class="solid_checkbox">
                <input type="checkbox" <?php checked(\poshtvan\app\options::isAdminSmsNotificationActiveOnSubmitNewTicket()) ?> id="send_sms_to_operator_after_submit_new_ticket" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('send_sms_to_operator_after_submit_new_ticket')) ?>" value="1">
                <label for="send_sms_to_operator_after_submit_new_ticket"><?php esc_html_e('Activate', 'poshtvan') ?></label>
            </p>
        </div>

        <div class="option_field" mwdeps="send_sms_to_operator_after_submit_new_ticket">
            <p class="solid_checkbox">
                <input type="checkbox" <?php checked(\poshtvan\app\options::isPatternModeActiveForOperatorNewTicketSmsNotification())?> value="1" id="sms_pattern_id_operator_new_ticket" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_pattern_id_operator_new_ticket'))?>">
                <label for="sms_pattern_id_operator_new_ticket"><?php esc_html_e('Using sms provider Pattern-ID', 'poshtvan')?></label>
            </p>
        </div>
        <div mwdeps="sms_pattern_id_operator_new_ticket" class="option_field option_row_field flex-label">
            <label for="sms_pattern_id_operator_new_ticket_value"><?php esc_html_e("Pattern ID", 'poshtvan'); ?></label>
            <input dir="auto" value="<?php echo esc_attr(\poshtvan\app\options::getOperatorNewTicketSmsNoficationPatternID())?>" type="text" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_pattern_id_operator_new_ticket_value'))?>" id="sms_pattern_id_operator_new_ticket_value">
        </div>
        <p mwdeps="sms_pattern_id_operator_new_ticket" class="option_section_description bottom-description"><?php esc_html_e('If Your SMS-Provider support pattern you can enter your Pattern-ID here', 'poshtvan')?></p>
        
        <div mwdeps="send_sms_to_operator_after_submit_new_ticket" class="option_field editor_clean_design">
            <label for="admin_sms_content_after_submit_new_ticket"><?php esc_html_e('New ticket sms content', 'poshtvan') ?></label>
            <?php
            wp_editor(
                \poshtvan\app\options::getNewTicketAdminSmsNotificationContent(),
                \poshtvan\app\options::get_setting_name('admin_sms_content_after_submit_new_ticket'),
                [
                    'textarea_rows' => 10,
                ]
            );
            ?>
        </div>
        <p mwdeps="send_sms_to_operator_after_submit_new_ticket" class="option_section_description"><?php esc_html_e("You can use this in your sms content:", "poshtvan"); ?></p>
        <p mwdeps="send_sms_to_operator_after_submit_new_ticket" class="option_section_description list-item">
            <span class="item">
                <span><?php esc_html_e("Username", "poshtvan"); ?>: </span><span>[[username]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e("User Display Name", "poshtvan"); ?>: </span><span>[[display_name]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e("Ticket number", "poshtvan"); ?>: </span><span>[[ticket_id]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e('Ticket Operator Name', 'poshtvan') ?>: </span><span>[[ticket_operator_name]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e('Date', 'poshtvan') ?>: </span><span>[[date]]</span>
            </span>
        </p>
        <p mwdeps="send_sms_to_operator_after_submit_new_ticket" class="option_section_description"><?php esc_html_e('In some case of sms provider system you shoud put Laghv11 in sms content', 'poshtvan') ?></p>

    </div>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('New reply Operator notification', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Send SMS to operator after submit ticket reply', 'poshtvan') ?></p>

        <div class="option_field option_row_field">
            <p class="solid_checkbox">
                <input type="checkbox" <?php checked(\poshtvan\app\options::isAdminSmsNotificationActiveOnSubmitTicketReply()) ?> id="send_sms_to_operator_after_submit_ticket_reply" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('send_sms_to_operator_after_submit_ticket_reply')) ?>" value="1">
                <label for="send_sms_to_operator_after_submit_ticket_reply"><?php esc_html_e('Activate', 'poshtvan') ?></label>
            </p>
        </div>

        <div class="option_field" mwdeps="send_sms_to_operator_after_submit_ticket_reply">
            <p class="solid_checkbox">
                <input <?php checked(\poshtvan\app\options::isPatternModeActiveForOperatorReplyTicketSmsNotification())?> type="checkbox" value="1" id="sms_pattern_id_operator_reply_ticket" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_pattern_id_operator_reply_ticket'))?>">
                <label for="sms_pattern_id_operator_reply_ticket"><?php esc_html_e('Using sms provider Pattern-ID', 'poshtvan')?></label>
            </p>
        </div>
        <div mwdeps="sms_pattern_id_operator_reply_ticket" class="option_field option_row_field flex-label">
            <label for="sms_pattern_id_operator_reply_ticket_value"><?php esc_html_e("Pattern ID", 'poshtvan'); ?></label>
            <input dir="auto" value="<?php echo esc_attr(\poshtvan\app\options::getOperatorReplyTicketSmsNoficationPatternID())?>" type="text" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_pattern_id_operator_reply_ticket_value'))?>" id="sms_pattern_id_operator_reply_ticket_value">
        </div>
        <p mwdeps="sms_pattern_id_operator_reply_ticket" class="option_section_description bottom-description"><?php esc_html_e('If Your SMS-Provider support pattern you can enter your Pattern-ID here', 'poshtvan')?></p>

        
        <div mwdeps="send_sms_to_operator_after_submit_ticket_reply" class="option_field editor_clean_design">
            <label for="admin_sms_content_after_submit_ticket_reply"><?php esc_html_e('Submit reply sms content', 'poshtvan') ?></label>
            <?php
            wp_editor(
                \poshtvan\app\options::getReplyTicketAdminSmsNotificationContent(),
                \poshtvan\app\options::get_setting_name('admin_sms_content_after_submit_ticket_reply'),
                [
                    'textarea_rows' => 10,
                ]
            );
            ?>
        </div>
        <p mwdeps="send_sms_to_operator_after_submit_ticket_reply" class="option_section_description"><?php esc_html_e("You can use this in your sms content:", "poshtvan"); ?></p>
        <p mwdeps="send_sms_to_operator_after_submit_ticket_reply" class="option_section_description list-item">
            <span class="item">
                <span><?php esc_html_e("Username", "poshtvan"); ?>: </span><span>[[username]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e("User Display Name", "poshtvan"); ?>: </span><span>[[display_name]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e("Ticket number", "poshtvan"); ?>: </span><span>[[ticket_id]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e('Ticket Operator Name', 'poshtvan') ?>: </span><span>[[ticket_operator_name]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e('Date', 'poshtvan') ?>: </span><span>[[date]]</span>
            </span>
        </p>
        <p mwdeps="send_sms_to_operator_after_submit_ticket_reply" class="option_section_description"><?php esc_html_e('In some case of sms provider system you shoud put Laghv11 in sms content', 'poshtvan') ?></p>

    </div>

    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('New ticket User notification', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Send SMS to user after submit new ticket', 'poshtvan') ?></p>

        <div class="option_field option_row_field">
            <p class="solid_checkbox">
                <input type="checkbox" <?php checked(\poshtvan\app\options::isUserSmsNotificationActiveOnSubmitNewTicket()) ?> id="send_sms_to_user_after_submit_new_ticket" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('send_sms_to_user_after_submit_new_ticket')) ?>" value="1">
                <label for="send_sms_to_user_after_submit_new_ticket"><?php esc_html_e('Activate', 'poshtvan') ?></label>
            </p>
        </div>

        <div class="option_field" mwdeps="send_sms_to_user_after_submit_new_ticket">
            <p class="solid_checkbox">
                <input <?php checked(\poshtvan\app\options::isPatternModeActiveForUserNewTicketSmsNotification())?> type="checkbox" value="1" id="sms_pattern_id_user_new_ticket" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_pattern_id_user_new_ticket'))?>">
                <label for="sms_pattern_id_user_new_ticket"><?php esc_html_e('Using sms provider Pattern-ID', 'poshtvan')?></label>
            </p>
        </div>
        <div mwdeps="sms_pattern_id_user_new_ticket" class="option_field option_row_field flex-label">
            <label for="sms_pattern_id_user_new_ticket_value"><?php esc_html_e("Pattern ID", 'poshtvan'); ?></label>
            <input dir="auto" value="<?php echo esc_attr(\poshtvan\app\options::getUserNewTicketSmsNoficationPatternID())?>" type="text" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_pattern_id_user_new_ticket_value'))?>" id="sms_pattern_id_user_new_ticket_value">
        </div>
        <p mwdeps="sms_pattern_id_user_new_ticket" class="option_section_description bottom-description"><?php esc_html_e('If Your SMS-Provider support pattern you can enter your Pattern-ID here', 'poshtvan')?></p>
        
        <div mwdeps="send_sms_to_user_after_submit_new_ticket" class="option_field editor_clean_design">
            <label for="user_sms_content_after_submit_new_ticket"><?php esc_html_e('New ticket sms content', 'poshtvan') ?></label>
            <?php
            wp_editor(
                \poshtvan\app\options::getNewTicketUserSmsNotificationContent(),
                \poshtvan\app\options::get_setting_name('user_sms_content_after_submit_new_ticket'),
                [
                    'textarea_rows' => 10,
                ]
            );
            ?>
        </div>
        <p mwdeps="send_sms_to_user_after_submit_new_ticket" class="option_section_description"><?php esc_html_e("You can use this in your sms content:", "poshtvan"); ?></p>
        <p mwdeps="send_sms_to_user_after_submit_new_ticket" class="option_section_description list-item">
            <span class="item">
                <span><?php esc_html_e("Username", "poshtvan"); ?>: </span><span>[[username]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e("User Display Name", "poshtvan"); ?>: </span><span>[[display_name]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e("Ticket number", "poshtvan"); ?>: </span><span>[[ticket_id]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e('Ticket Operator Name', 'poshtvan') ?>: </span><span>[[ticket_operator_name]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e('Date', 'poshtvan') ?>: </span><span>[[date]]</span>
            </span>
        </p>
        <p mwdeps="send_sms_to_user_after_submit_new_ticket" class="option_section_description"><?php esc_html_e('In some case of sms provider system you shoud put Laghv11 in sms content', 'poshtvan') ?></p>

    </div>

    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('New reply User notification', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Send SMS to user after submit new ticket', 'poshtvan') ?></p>
        
        <div class="option_field option_row_field">
            <p class="solid_checkbox">
                <input type="checkbox" <?php checked(\poshtvan\app\options::isUserSmsNotificationActiveOnSubmitTicketReply()) ?> id="send_sms_to_user_after_submit_ticket_reply" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('send_sms_to_user_after_submit_ticket_reply')) ?>" value="1">
                <label for="send_sms_to_user_after_submit_ticket_reply"><?php esc_html_e('Activate', 'poshtvan') ?></label>
            </p>
        </div>
        <div class="option_field" mwdeps="send_sms_to_user_after_submit_ticket_reply">
            <p class="solid_checkbox">
                <input <?php checked(\poshtvan\app\options::isPatternModeActiveForUserReplyTicketSmsNotification())?> type="checkbox" value="1" id="sms_pattern_id_user_reply_ticket" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_pattern_id_user_reply_ticket'))?>">
                <label for="sms_pattern_id_user_reply_ticket"><?php esc_html_e('Using sms provider Pattern-ID', 'poshtvan')?></label>
            </p>
        </div>
        <div mwdeps="sms_pattern_id_user_reply_ticket" class="option_field option_row_field flex-label">
            <label for="sms_pattern_id_user_reply_ticket_value"><?php esc_html_e("Pattern ID", 'poshtvan'); ?></label>
            <input dir="auto" value="<?php echo esc_attr(\poshtvan\app\options::getUserReplyTicketSmsNoficationPatternID())?>" type="text" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('sms_pattern_id_user_reply_ticket_value'))?>" id="sms_pattern_id_user_reply_ticket_value">
        </div>
        <p mwdeps="sms_pattern_id_user_reply_ticket" class="option_section_description bottom-description"><?php esc_html_e('If Your SMS-Provider support pattern you can enter your Pattern-ID here', 'poshtvan')?></p>

        <div mwdeps="send_sms_to_user_after_submit_ticket_reply" class="option_field editor_clean_design">
            <label label="user_sms_content_after_submit_ticket_reply"><?php esc_html_e('Submit reply sms content', 'poshtvan') ?></label>
            <?php
            wp_editor(
                \poshtvan\app\options::getReplyTicketUserSmsNotificationContent(),
                \poshtvan\app\options::get_setting_name('user_sms_content_after_submit_ticket_reply'),
                [
                    'textarea_rows' => 10,
                ]
            );
            ?>
        </div>
        <p mwdeps="send_sms_to_user_after_submit_ticket_reply" class="option_section_description"><?php esc_html_e("You can use this in your sms content:", "poshtvan"); ?></p>
        <p mwdeps="send_sms_to_user_after_submit_ticket_reply" class="option_section_description list-item">
            <span class="item">
                <span><?php esc_html_e("Username", "poshtvan"); ?>: </span><span>[[username]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e("User Display Name", "poshtvan"); ?>: </span><span>[[display_name]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e("Ticket number", "poshtvan"); ?>: </span><span>[[ticket_id]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e('Ticket Operator Name', 'poshtvan') ?>: </span><span>[[ticket_operator_name]]</span>
            </span>
            <span class="item">
                <span><?php esc_html_e('Date', 'poshtvan') ?>: </span><span>[[date]]</span>
            </span>
        </p>
        <p mwdeps="send_sms_to_user_after_submit_ticket_reply" class="option_section_description"><?php esc_html_e('In some case of sms provider system you shoud put Laghv11 in sms content', 'poshtvan') ?></p>
    </div>
</form>