<form id="poshtvan-option-panel-form" method="post" action="options.php">
    <?php settings_fields(\poshtvan\app\options::get_setting_group_name('form_steps'));?>
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Steps title', 'poshtvan')?></h3>
        <p class="option_section_description"><?php esc_html_e('Enter title for each steps in new ticket process', 'poshtvan')?></p>
        <div class="option_field">
            <label for="choose_order_step_section_title"><?php esc_html_e('Choose order step title', 'poshtvan')?></label>
            <input type="text" value="<?php echo esc_attr(\poshtvan\app\options::get_choose_order_step_section_title())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('choose_order_step_section_title'))?>" id="choose_order_step_section_title">
        </div>
        <div class="option_field">
            <label for="faq_step_section_title"><?php esc_html_e('FAQ step title', 'poshtvan')?></label>
            <input type="text" value="<?php echo esc_attr(\poshtvan\app\options::get_faq_step_section_title())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('faq_step_section_title'))?>" id="faq_step_section_title">
        </div>
        <div class="option_field">
            <label for="search_step_section_title"><?php esc_html_e('Search step title', 'poshtvan')?></label>
            <input type="text" value="<?php echo esc_attr(\poshtvan\app\options::get_search_step_section_title())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('search_step_section_title'))?>" id="search_step_section_title">
        </div>
        <div class="option_field">
            <label for="final_step_section_title"><?php esc_html_e('Smart robot next step button', 'poshtvan')?></label>
            <input type="text" value="<?php echo esc_attr(\poshtvan\app\options::get_smart_bot_next_button_title())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('smart_bot_next_button_title'))?>" id="smart_bot_next_button_title">
        </div>
        <div class="option_field">
            <label for="final_step_section_title"><?php esc_html_e('Final step title', 'poshtvan')?></label>
            <input type="text" value="<?php echo esc_attr(\poshtvan\app\options::get_final_step_section_title())?>" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('final_step_section_title'))?>" id="final_step_section_title">
        </div>
    </div>
</form>