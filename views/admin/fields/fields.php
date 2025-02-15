<div class="mwtc_admin_wrapper">
    <div class="notice-wrapper">
        <div class="notices">
            <?php \poshtvan\app\notice::show_notices('fields-new-record')?>
        </div>
        <div class="hide alert info update-confirm">
            <div class="update-confirm-msg">
                <span><?php esc_html_e('Do you want to save changes?', 'poshtvan')?></span>
                <span class="mw_ajax_update_fields_data"><?php esc_html_e('Yes', 'poshtvan')?></span>
            </div>
        </div>
    </div>
    <div class="title">
        <h2><?php esc_html_e('Fields', 'poshtvan')?></h2>
        <span id="new-item-btn"><?php esc_html_e('New Item', 'poshtvan')?></span>
    </div>
    <div id="new-record-form" class="hide">
        <form method="post">
            <div class="item">
                <label for="field-name"><?php printf('%s (%s)', esc_html__('Field Name', 'poshtvan'), esc_html__('English', 'poshtvan'))?></label>
                <input autocomplete="off" type="text" id="field-name" name="field_name" placeholder="<?php esc_html_e('Field Name', 'poshtvan')?>">
            </div>
            <div class="item">
                <label for="field-label"><?php esc_html_e('Field Label', 'poshtvan')?></label>
                <input autocomplete="off" type="text" id="field-label" name="field_label" placeholder="<?php esc_html_e('Label', 'poshtvan')?>">
            </div>
            <div class="item">
                <label for="type"><?php esc_html_e('Type', 'poshtvan')?></label>
                <select name="type" id="type">
                    <option value="1"><?php esc_html_e('Text', 'poshtvan')?></option>
                </select>
            </div>
            <div class="item">
                <label for="is-required"><?php esc_html_e('Is Required?', 'poshtvan')?></label>
                <select id="is-required" name="is_required">
                    <option value="0"><?php esc_html_e('Not required', 'poshtvan')?></option>
                    <option value="1"><?php esc_html_e('Required', 'poshtvan')?></option>
                </select>
            </div>
            <div class="item submit-field">
                <input type="submit" name="new_item" value="<?php esc_html_e('Submit', 'poshtvan')?>">
            </div>
        </form>
    </div>
    <div class="field-items">
        <div class="mw_table">
            <div class="mw_head">
                <div class="mw_row">
                    <div class="mw_th"></div>
                    <div class="mw_th"><?php printf('%s (%s)', esc_html__('Field Name', 'poshtvan'), esc_html__('English', 'poshtvan'))?></div>
                    <div class="mw_th"><?php esc_html_e('Label', 'poshtvan')?></div>
                    <div class="mw_th"><?php esc_html_e('Type', 'poshtvan')?></div>
                    <div class="mw_th"><?php esc_html_e('Required', 'poshtvan')?></div>
                    <div class="mw_th"></div>
                </div>
            </div>
            <div class="mw_body mw_sortable">
                <?php \poshtvan\app\form\fields::show_fields();?>
            </div>
        </div>
    </div>
</div>