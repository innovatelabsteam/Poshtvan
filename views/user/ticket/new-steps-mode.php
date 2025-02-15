<div class="new-ticket">
  <div class="new-ticket-toolbar">
    <span mwtc_close_mode_text="<?php esc_attr_e('Close', 'poshtvan'); ?>" mwtc_open_mode_text="<?php esc_attr_e('Send Support Request', 'poshtvan') ?>" class="action-btn open-new-ticket-form"><?php esc_html_e('Send Support Request', 'poshtvan') ?></span>
  </div>
  <div class="form">
    <?php if ($before_new_ticket_message = \poshtvan\app\options::get_new_ticket_top_message_box_text()) : ?>
      <p class="alert alert-<?php echo esc_attr(\poshtvan\app\options::get_new_ticket_top_message_box_type()); ?>"><?php echo esc_html($before_new_ticket_message); ?></p>
    <?php endif; ?>
    <div class="mihanticket-step-list">
      <span class="active" id="mwtc-step-choose-product">
        <span class="label">1</span>
        <span class="value"><?php esc_html_e('Choose order', 'poshtvan') ?></span>
      </span>
      <span id="mwtc-step-faq-items">
        <span class="label">2</span>
        <span class="value"><?php esc_html_e('FAQ', 'poshtvan') ?></span>
      </span>
      <span id="mwtc-step-ticket-subject">
        <span class="label">3</span>
        <span class="value"><?php esc_html_e('Search', 'poshtvan') ?></span>
      </span>
      <span id="mwtc-step-final-step">
        <span class="label">4</span>
        <span class="value"><?php esc_html_e('Submit new ticket', 'poshtvan') ?></span>
      </span>
    </div>
    <div class="mihanticket-step active" step="choose-product">
      <span class="mwtc-section-title"><?php echo esc_html(\poshtvan\app\options::get_choose_order_step_section_title()); ?></span>
      <?php do_action('poshtvan/render_step/choose_order');?>
    </div>
    <div class="mihanticket-step" step="faq-items"></div>
    <div class="mihanticket-step" step="ticket-subject">
      <span class="mwtc-section-title"><?php echo esc_html(\poshtvan\app\options::get_search_step_section_title())?></span>

        <?php
        $hasAi = \poshtvan\app\options::ai_chat_is_activated();
        $helperText = \poshtvan\app\options::get_ai_helper_text();
        if($hasAi): ?>
            <?php
            $path = \poshtvan\app\files::get_file_path('views.user.ticket.ai-chat-widget');
            if ($path){
                include_once $path;
            }
            ?>
            <?php if (!\poshtvan\app\options::hide_support_from_ai()): ?>
                <div class="support-from-ai">
                    <span><?php echo sprintf(esc_html__('Powered by %s', 'poshtvan'), "<a href='https://hooshina.com' rel='nofollow' target='_blank'>". esc_html__('Hooshina', 'poshtvan') ."</a>") ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($helperText)): ?>
                <div class="pv-bot-helper-text"><?php echo wp_kses(nl2br($helperText), ['br' => []]) ?></div>
            <?php endif; ?>
        <?php else: ?>
            <p>
                <label><?php esc_attr_e('Subject', 'poshtvan') ?> *</label>
                <input autocomplete="off" type="text" name="ticket-subject" placeholder="<?php esc_attr_e('Type the error or problem you are having', 'poshtvan') ?>">
            </p>
        <?php endif; ?>

      <div class="ticket-search-result-wrapper">
        <div class="bottom-btn-wrapper">
            <?php if ($hasAi): ?>
                <span step="final-step" class="mwtc-btn change-step-btn next-step-btn"><?php echo esc_html(\poshtvan\app\options::get_smart_bot_next_button_title()) ?></span>
            <?php endif; ?>
            <span step="faq-items" class="mwtc-btn change-step-btn prev-step-btn"><?php esc_html_e('Back', 'poshtvan') ?></span>
        </div>
      </div>
    </div>

    <div class="mihanticket-step" step="final-step">
      <span class="mwtc-section-title"><?php echo esc_html(\poshtvan\app\options::get_final_step_section_title())?></span>
      <div class="extra_fields">
        <?php
        do_action('poshtvan/new_ticket/before_render_fields');
        ?>
      </div>
        <?php if ($hasAi): ?>
            <p>
                <label><?php esc_attr_e('Subject', 'poshtvan') ?> *</label>
                <input autocomplete="off" type="text" name="ticket-subject">
            </p>
        <?php endif; ?>
      <p>
        <label><?php esc_attr_e('Description', 'poshtvan') ?> *</label>
        <?php \poshtvan\app\form\fields::renderNewTicketContentField()?>
      </p>
      <div class="file_field">
        <input class="default_input_style uploading_file" id="uploading_file" type="file" name="ticket_attachment">
        <label for="uploading_file"><?php esc_html_e('Choose or drag your file here', 'poshtvan')?></label>
        <span class="progress_bar"></span>
      </div>
      <div class="bottom-toolbar bottom-btn-wrapper">
        <span class="mwtc-btn primary-btn action-btn submit-new-ticket"><span class="btn-text"><?php esc_html_e('Submit Ticket', 'poshtvan') ?></span></span>

        <span step="ticket-subject" class="mwtc-btn change-step-btn prev-step-btn"><?php esc_html_e('Back', 'poshtvan') ?></span>
      </div>
    </div>
  </div>
</div>