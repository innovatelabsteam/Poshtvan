<div>
    <div class="notice-wrapper">
        <?php
        do_action('poshtvan_tab_ai_event');
        \poshtvan\app\notice::show_notices('admin-panel-ai-menu');
        \poshtvan\app\notice::show_notices('admin-panel-ai-menu', 'cookie');
        ?>
    </div>
</div>
<form id="poshtvan-option-panel-form" method="post" action="options.php">
    <?php if (\poshtvan\app\options::get_is_steps_mode_in_new_ticket()): ?>
        <?php settings_fields(\poshtvan\app\options::get_setting_group_name('ai')); ?>
        <div class="option_section" id="ai_status_field">
            <h3 class="option_section_title"><?php esc_html_e('Artificial Intelligence Guide', 'poshtvan') ?></h3>
            <p class="option_section_description"><?php esc_html_e('You can add artificial intelligence chat feature to the ticket registration form.', 'poshtvan') ?></p>
            <p class="option_section_description">
                <?php
                $isConnected = \poshtvan\app\providers\AiProviders\AiChatService::use()->isConnected();
                if ($isConnected): ?>
                    <a href="#" class="ai-connection-btn disconnect-btn" id="disconnect-hooshina-btn">
                        <?php esc_html_e('Revoke Connection', 'poshtvan') ?>
                        <span class="pv-loader" style="display:none"></span>
                    </a>
                <?php else: ?>
                    <a href="#" class="ai-connection-btn" id="connect-hooshina-btn">
                        <?php esc_html_e('Connect with Hooshina', 'poshtvan') ?>
                        <span class="pv-loader" style="display:none"></span>
                    </a>
                <?php endif; ?>
            </p>
        </div>
        <?php if ($isConnected):
            $walletType = \poshtvan\app\options::get_ai_current_wallet();
            $balance = \poshtvan\app\providers\AiProviders\AiChatService::use()->getWalletBalance();
            ?>
            <div class="option_section">
                <h3 class="option_section_title"><?php esc_html_e('Wallet', 'poshtvan') ?></h3>
                <p class="option_section_description"><?php esc_html_e('Choose wallet', 'poshtvan') ?></p>
                <div class="option_field option_row_field">
                    <p class="solid_checkbox">
                        <input <?php checked($walletType, 'en'); ?> type="radio" id="hooshina_wallet_type_en" value="en" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('ai_hooshina_wallet')) ?>">
                        <label for="hooshina_wallet_type_en"><?php esc_html_e('Dollar', 'poshtvan') ?></label>
                    </p>
                    <p class="solid_checkbox">
                        <input <?php checked($walletType, 'fa'); ?> type="radio" id="hooshina_wallet_type_fa" value="fa" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('ai_hooshina_wallet')) ?>">
                        <label for="hooshina_wallet_type_fa"><?php esc_html_e('Rial', 'poshtvan') ?></label>
                    </p>
                </div>

                <?php if (is_object($balance) && isset($balance->data)): ?>
                    <h3 class="option_section_title"><?php esc_html_e('Wallet Balance', 'poshtvan') ?></h3>
                    <div class="wallets-balance">
                        <?php if ($balance->data->IRT): ?>
                            <div class="wallet-balance">
                                <span class="wallet-value"><?php echo number_format($balance->data->IRT) ?></span>
                                <span><?php esc_html_e('Toman', 'poshtvan') ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($balance->data->USD): ?>
                            <div class="wallet-balance">
                                <span class="wallet-value"><?php echo number_format($balance->data->USD, 2) ?></span>
                                <span><?php esc_html_e('Dollar', 'poshtvan') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="option_section">
                <h3 class="option_section_title"><?php esc_html_e('Help text', 'poshtvan')?></h3>
                <p class="option_section_description"><?php esc_html_e('The text below the ai bot box is displayed.', 'poshtvan') ?></p>
                <div class="option_field option_row_field fit_label">
                    <label for="ai_helper_text"><?php esc_html_e('Text', 'poshtvan') ?></label>
                    <textarea name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('ai_helper_text')) ?>" id="helper_text" cols="30" rows="10"><?php echo esc_textarea(\poshtvan\app\options::get_ai_helper_text()) ?></textarea>
                </div>
            </div>

            <div class="option_section">
                <h3 class="option_section_title"><?php esc_html_e('Support Hooshina', 'poshtvan') ?></h3>
                <p class="option_section_description"><?php esc_html_e('Support text is displayed below the chat box.', 'poshtvan') ?></p>
                <div class="option_field option_row_field">
                    <p class="solid_checkbox">
                        <input <?php checked(\poshtvan\app\options::hide_support_from_ai(), 1); ?> type="checkbox" id="hide_support_from_ai" value="1" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('hide_support_from_ai')); ?>">
                        <label for="hide_support_from_ai"><?php esc_html_e('Deactivation', 'poshtvan') ?></label>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="notice-wrapper">
            <span class="alert error"><?php esc_html_e('To use ai, the multi-step mode of the ticket form must be active.', 'poshtvan') ?></span>
        </div>
    <?php endif; ?>
</form>
