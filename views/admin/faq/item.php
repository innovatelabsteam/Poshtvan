<?php
$fieldID = isset($isNew) && isset($fieldID) ? $fieldID : $itemID;
$titleValue = isset($isNew) ? __('Default title', 'poshtvan') : $item['faq-item-name'];
$contentValue = isset($isNew) ? __('Default content', 'poshtvan') : $item['faq-item-content'];
?>
<div class="faq-item">
    <div class="faq-item-title">
        <span class="move-icon">
            <span class="dashicons dashicons-menu-alt"></span>
        </span>
        <span class="value"><?php echo $titleValue ? esc_html($titleValue) : esc_html__('Title', 'poshtvan') ?></span>
        <span class="trash-icon">
            <span class="dashicons dashicons-trash"></span>
        </span>
        <span class="state-icon">
            <span class="dashicons dashicons-arrow-down-alt2"></span>
        </span>
    </div>
    <div class="faq-item-content">
        <div class="input-item">
            <label for="faq-item-name"><?php esc_html_e('Item title', 'poshtvan') ?></label>
            <input type="text" class="faq-item-title-field" name="faq-data[<?php echo esc_attr($fieldID) ?>][faq-item-name]" value="<?php echo esc_attr($titleValue); ?>">
        </div>
        <div class="input-item">
            <label for="faq-item-content-<?php echo esc_attr($fieldID); ?>"><?php esc_html_e('Content', 'poshtvan') ?></label>
            <?php if (isset($isNew)) : ?>
                <textarea name="faq-data[<?php echo esc_attr($fieldID) ?>][faq-item-content]" id="faq-item-content-<?php echo esc_attr($fieldID) ?>" cols="30" rows="10"><?php echo esc_textarea($contentValue); ?></textarea>
            <?php else :
                wp_editor($contentValue, 'faq-item-content-' . $fieldID, ['textarea_name' => 'faq-data[' . $fieldID . '][faq-item-content]']);
            endif;
            ?>
        </div>
    </div>
</div>