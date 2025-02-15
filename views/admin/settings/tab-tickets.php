<div>
    <div class="notice-wrapper">
        <?php
        \poshtvan\app\notice::show_notices('admin-panel-tickets-menu');
        \poshtvan\app\notice::show_notices('admin-panel-tickets-menu', 'cookie');
        ?>
    </div>
</div>
<form id="poshtvan-option-panel-form" method="post" action="options.php">
    <?php settings_fields(\poshtvan\app\options::get_setting_group_name('tickets')); ?>
    <div class="option_section" id="ticket_custom_status_field">
        <h3 class="option_section_title"><?php esc_html_e('Ticket status', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('You can add extra statuses for your ticket system', 'poshtvan') ?></p>
        <p class="option_section_description">
            <span class="btn-item" id="add_new_ticket_status"><?php esc_html_e('Add new', 'poshtvan') ?></span>
        </p>
        <div class="option_section no-padding" id="custom_status_fields_wrapper">
            <?php
            $ticketCustomStatus = \poshtvan\app\options::getCustomTicketStatus();
            if ($ticketCustomStatus && is_array($ticketCustomStatus)) :
                foreach ($ticketCustomStatus as $statusItem) :
            ?>
                    <div class="option_field option_row_field no-padding">
                        <input type="text" value="<?php echo esc_attr($statusItem['slug']); ?>" placeholder="<?php esc_attr_e('Slug', 'poshtvan') ?>" name="mwtc_poshtvan_ticket_custom_status[slug][]">
                        <input type="text" value="<?php echo esc_attr($statusItem['name']); ?>" placeholder="<?php esc_attr_e('Name', 'poshtvan') ?>" name="mwtc_poshtvan_ticket_custom_status[name][]">
                        <span class="icon"><span class="dashicons dashicons-trash delete-field"></span></span>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>
    <div class="option_section" id="auto_ticket_field">
        <h3 class="option_section_title"><?php esc_html_e('Auto Tickets', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Send ticket to user after change ticket status', 'poshtvan') ?></p>

        <div class="option_field option_row_field">
            <label><?php esc_html_e('Choose Operator', 'poshtvan') ?></label>
            <?php
            $operatorUsersList = \poshtvan\app\options::getOperatorUsersList();
            $autoTicketOperatorUser = \poshtvan\app\options::getAutoTicketOperatorUser();

            ?>
            <select class="select2" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('auto_ticket_operator_user')) ?>" id="auto_ticket_operator_user">
                <?php if ($operatorUsersList) : ?>
                    <option value="0" disabled selected><?php esc_html_e('Choose Operator', 'poshtvan'); ?></option>
                    <?php foreach ($operatorUsersList as $operatorUser) : ?>
                        <option <?php selected($autoTicketOperatorUser, $operatorUser->ID) ?> value="<?php echo esc_attr($operatorUser->ID) ?>"><?php echo esc_html(sprintf('%s (%s)', $operatorUser->user_login, $operatorUser->display_name)) ?></option>
                    <?php endforeach; ?>
                <?php else : ?>
                    <option value="0" disabled selected><?php esc_html_e("Didn't find any operator", 'poshtvan'); ?></option>
                <?php endif; ?>
            </select>
        </div>
        
        <p class="option_section_description">
            <span class="btn-item" id="add_new_auto_ticket_item"><?php esc_html_e('Add new', 'poshtvan') ?></span>
        </p>
        <div class="option_section no-padding" id="auto_ticket_items_fields_wrapper">
            <?php
            $autoTicketItems = \poshtvan\app\options::getAutoTicketItems();
            $ticketStatusList = \poshtvan\app\tickets::getAutoTicketStatusList();
            if($autoTicketItems):
                foreach($autoTicketItems as $autoTicketItemKey => $autoTicketItemData): ?>
                    <?php foreach($autoTicketItemData as $autoTicketItemContent): ?>
                        <div class="option_field option_row_field no-padding">
                            <select name="mwtc_poshtvan_auto_ticket_item[status][]">
                                <?php if ($ticketStatusList) : ?>
                                    <?php foreach ($ticketStatusList as $statusItem) : ?>
                                        <option <?php selected($autoTicketItemKey, $statusItem['name'])?> value="<?php echo esc_attr($statusItem['name']) ?>"><?php echo esc_html($statusItem['title']); ?></option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <option value="0"><?php esc_html_e('No any items found', 'poshtvan') ?></option>
                                <?php endif; ?>
                            </select>
                            <textarea name="mwtc_poshtvan_auto_ticket_item[content][]" placeholder="<?php echo esc_html('Ticket content', 'poshtvan') ?>"><?php echo esc_textarea($autoTicketItemContent); ?></textarea>
                            <span class="icon"><span class="dashicons dashicons-trash delete-field"></span></span>
                        </div>
                    <?php endforeach; ?>

            <?php endforeach; endif; ?>
        </div>
    </div>
</form>