<div class="wrap admin-mihan-ticket <?php echo is_rtl() ? 'mwtc_rtl' : 'mwtc_ltr'; ?>">
    <div class="mihanticket-alerts-wrapper"></div>
    <span class="waiting-msg"><?php esc_html_e('Please Wait...', 'poshtvan') ?></span>
    <div class="mwtc_wrapper">
        <div class="sidebar">
            <div class="search">
                <input type="text" name="search-ticket" dir="auto" autocomplete="off" placeholder="<?php esc_attr_e('Search', 'poshtvan') ?>">
                <span class="filter-icon"><img src="<?php echo esc_url(\poshtvan\app\assets::get_img_url('option-panel.filter', 'svg')) ?>" alt=""></span>
            </div>
            <span class="filters-section">
                <span class="filter-items-wrapper">
                    <?php $ticketStatusList = \poshtvan\app\tickets::get_status_list(); ?>
                    <span class="filter-items">
                        <span class="filter-item">
                            <input checked id="status-all-mode" type="checkbox" name="filter-ticket-status" value="all">
                            <label for="status-all-mode"><?php esc_html_e('All', 'poshtvan') ?></label>
                        </span>
                        <?php if ($ticketStatusList) : ?>
                            <?php foreach ($ticketStatusList as $statusItem) : ?>
                                <span class="filter-item">
                                    <input id="<?php echo esc_attr(sprintf('filter-status-%s', $statusItem['name'])); ?>" type="checkbox" name="filter-ticket-status" value="<?php echo esc_attr(\poshtvan\app\tickets::get_status_code($statusItem['name'])) ?>">
                                    <label for="<?php echo esc_attr(sprintf('filter-status-%s', $statusItem['name'])); ?>"><?php echo esc_html($statusItem['title']); ?></label>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </span>
                    <span class="apply-filters"><?php esc_html_e('Apply', 'poshtvan') ?></span>
                </span>
            </span>
            <div class="search-result"></div>
            <div class="items-wrapper"></div>
            <div mwtc_offset="0" class="load-more active"><?php esc_html_e('Load More', 'poshtvan') ?></div>
        </div>
        <div class="content">

        </div>
    </div>
</div>