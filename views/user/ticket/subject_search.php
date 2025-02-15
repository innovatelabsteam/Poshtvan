<?php if ($posts) : ?>
    <span class="mwtc-search-result-header"><?php esc_html_e('Search result', 'poshtvan') ?></span>
    <div class="mwtc-search-result-items">
        <?php foreach ($posts as $postItem):
            $postPermalink = get_the_permalink($postItem->ID);
            $postTitle = $postItem->post_title;
            $postThumbnail = get_the_post_thumbnail($postItem->ID, 'medium');
            ?>
            <a target="_blank" href="<?php echo esc_attr($postPermalink) ?>" class="mwtc-result-item">
                <span class="mwtc-title"><?php echo esc_html($postTitle); ?></span>
                <span class="mwtc-thumbnail"><?php echo wp_kses_post($postThumbnail)?></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <span class="mwtc-search-notice"><?php esc_html_e('The bot did not find anything. You can change the topic or register a new ticket', 'poshtvan')?></span>
<?php endif; ?>
<div class="bottom-btn-wrapper">
    <span step="final-step" class="mwtc-btn change-step-btn next-step-btn"><?php echo esc_html(\poshtvan\app\options::get_smart_bot_next_button_title()) ?></span>
    <span step="faq-items" class="mwtc-btn change-step-btn prev-step-btn"><?php esc_html_e('Back', 'poshtvan')?></span>
</div>