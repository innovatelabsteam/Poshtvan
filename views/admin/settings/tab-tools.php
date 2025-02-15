<?php \poshtvan\app\admin_menu::handleToolsMenuSubmission(); ?>
<div>
    <div class="notice-wrapper">
        <?php \poshtvan\app\notice::show_notices('admin-panel-tools-menu');?>
    </div>
</div>
<form id="poshtvan-option-panel-form" method="post">
    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Poshtvan page', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Create page that contains Poshtvan shortcode', 'poshtvan') ?></p>
        <div class="option_field option_row_field">
            <label for="create_poshtvan_page"><?php esc_html_e('Create Poshtvan page', 'poshtvan') ?></label>
            <?php if (!\poshtvan\app\tools::checkIsPoshtvanPageExists()) : ?>
                <input name="create_poshtvan_page" type="submit" value="<?php esc_html_e('Create', "poshtvan") ?>">
            <?php else : ?>
                <div class="option_field_description">
                    <p class="description"><?php esc_html_e('There is a page with Poshtvan slug on your site.', "poshtvan") ?></p>
                    <p class="description"><?php esc_html_e("If you want to create new page, first delete last page change it's slug", "poshtvan") ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e("Create database tables", "poshtvan"); ?></h3>
        <div class="option_field option_row_field">
            <label><?php esc_html_e("Create Poshtvan default tables in database", "poshtvan"); ?></label>
            <input name="create_poshtvan_database_tables" type="submit" value="<?php esc_attr_e('Create', 'poshtvan') ?>">
        </div>
        <p class="option_section_description"><?php esc_html_e('Just use this when some tables were dropped from database.', 'poshtvan') ?></p>
    </div>

    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Delete Attachment Files', 'poshtvan')?></h3>
        <div class="option_field option_row_field">
            <label><?php esc_html_e('Delete all attachment files from poshtvan', 'poshtvan')?></label>
            <input
                type="submit"
                value="<?php esc_attr_e('Delete All', 'poshtvan')?>"
                onclick="if(!confirm('<?php esc_attr_e('Are you sure you want to delete all attachment files from poshtvan? This is irreversible action!', 'poshtvan')?>')){return false}"
                name="delete_poshtvan_attachment_files">
        </div>
        <p class="option_section_description"><?php esc_html_e('This option delete all attachment files from poshtvan', 'poshtvan')?></p>
    </div>

    <div class="option_section">
        <h3 class="option_section_title"><?php esc_html_e('Delete all tickets', 'poshtvan')?></h3>
        <div class="option_field option_row_field">
            <label><?php esc_html_e('Delete all tickets from poshtvan', 'poshtvan')?></label>
            <input
                type="submit"
                value="<?php esc_attr_e('Delete All', 'poshtvan')?>"
                onclick="if(!confirm('<?php esc_attr_e('Are you sure you want to delete all tickets from poshtvan? This is irreversible action!', 'poshtvan')?>')){return false}"
                name="delete_poshtvan_all_tickets">
        </div>
        <p class="option_section_description"><?php esc_html_e('This option delete all tickets and tickets meta data from poshtvan', 'poshtvan')?></p>
    </div>
</form>