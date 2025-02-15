<span class="mwtc-section-title"><?php echo esc_html(\poshtvan\app\options::get_faq_step_section_title()); ?></span>
<div class="mwtc-faq-items">
    <?php if ($faqItems) : ?>
        <?php foreach($faqItems as $item): ?>
            <div class="mwtc-faq-item">
                <span class="mwtc-faq-item-title">
                    <span class="mwtc-faq-icon">
                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                    </span>
                    <span class="mwtc-value"><?php echo esc_html($item['faq-item-name']); ?></span>
                </span>
                <span class="mwtc-faq-item-content"><?php echo wp_kses_post($item['faq-item-content']); ?></span>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <span><?php esc_html_e('There is nothing faq items', 'poshtvan')?></span>
    <?php endif; ?>
</div>
<div class="bottom-btn-wrapper">
    <span step="ticket-subject" class="mwtc-btn change-step-btn next-step-btn"><?php esc_html_e("I did not find the answer to my question", 'poshtvan')?></span>
    <span step="choose-product" class="mwtc-btn change-step-btn prev-step-btn"><?php esc_html_e('Back', 'poshtvan')?></span>
</div>