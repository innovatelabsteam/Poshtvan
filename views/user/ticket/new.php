<div class="new-ticket">
    <div class="new-ticket-toolbar">
        <span mwtc_close_mode_text="<?php esc_attr_e('Close', 'poshtvan'); ?>" mwtc_open_mode_text="<?php esc_attr_e('Send Support Request', 'poshtvan')?>" class="action-btn open-new-ticket-form"><?php esc_html_e('Send Support Request', 'poshtvan')?></span>
    </div>
    <div class="form">
    <?php if($before_new_ticket_message = \poshtvan\app\options::get_new_ticket_top_message_box_text()):?>
      <p class="alert alert-<?php echo esc_attr(\poshtvan\app\options::get_new_ticket_top_message_box_type());?>"><?php echo esc_html($before_new_ticket_message); ?></p>
    <?php endif; ?>
        <div class="extra_fields">
          <?php
          do_action('poshtvan/new_ticket/before_render_fields');
          ?>
        </div>
        <p>
        <label><?php esc_html_e('Subject', 'poshtvan') ?> *</label>
        <input autocomplete="off" type="text" name="ticket-subject" placeholder="<?php esc_attr_e('Ticket Subject', 'poshtvan')?>">
        </p>
        <p>
          <label><?php esc_html_e('Description', 'poshtvan') ?> *</label>
          <?php \poshtvan\app\form\fields::renderNewTicketContentField()?>
        </p>
        <div class="file_field">
          <input class="default_input_style uploading_file" id="uploading_file" type="file" name="ticket_attachment">
          <label for="uploading_file"><?php esc_html_e('Choose or drag your file here', 'poshtvan')?></label>
          <span class="progress_bar"></span>
        </div>
        <div class="bottom-toolbar default-mode">
            <span class="action-btn submit-new-ticket"><span class="btn-text"><?php esc_html_e('Submit Ticket', 'poshtvan')?></span></span>
        </div>
    </div>
</div>