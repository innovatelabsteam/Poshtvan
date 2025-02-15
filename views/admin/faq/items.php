<div class="faq-toolbar">
    <span id="faq-add-new-item">
        <span class="icon"><span class="dashicons dashicons-plus"></span></span>
        <span class="value"><?php esc_html_e('Add new', 'poshtvan')?></span>
    </span>
</div>
<div class="faq-items-wrapper">
    <?php foreach($items as $itemID => $item): ?>
        <?php include \poshtvan\app\files::get_file_path('views.admin.faq.item')?>
    <?php endforeach; ?>
</div>