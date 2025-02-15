<?php
if(!isset($menu_items) || !$menu_items)
{
    return false;
}
$white_list = array_keys($menu_items);
$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], $white_list) ? $_GET['tab'] : $white_list[0];
?>
<div class="mwtc_admin_wrapper mwtc_admin_settings_wrapper <?php echo is_rtl() ? 'mwtc-rtl' : 'mwtc-ltr'; ?>">
    <div class="mwtc_sidebar_wrapper">
        <div class="sidebar">
            <div class="mw_logo_section">
                <div class="mw_logo">
                    <img src="<?php echo esc_attr(\poshtvan\app\assets::get_img_url('poshtvan-logo', 'svg')); ?>" width="100" height="100" alt="Poshtvan Logo">
                </div>
                <div class="mw_hello">
                    <span><?php printf('%s %s', esc_html__('Welcome to Poshtvan', 'poshtvan'), '👋')?></span>
                </div>
            </div>
            <div class="mw_menu_section">
                <ul>
                    <?php foreach($menu_items as $key => $menu): ?>
                        <li <?php echo $active_tab === $key ? 'class="active"' : '';?>>
                            <a href="<?php echo esc_url(add_query_arg(['tab' => $key]))?>">
                                <span class="menu-icon" tab="<?php echo esc_attr($menu['icon'])?>"></span>
                                <span class="menu-name"><?php echo esc_html($menu['title']);?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="content_wrapper">
        <div class="content">
            <?php
            $view = \poshtvan\app\files::get_file_path('views.admin.settings.tab-' . $active_tab);
            $view ? include_once $view : null;
            ?>
        </div>
        <div class="submit-row">
            <?php submit_button(__('Save Changes', 'poshtvan'), 'primary', 'submit', true, ['form' => 'poshtvan-option-panel-form']); ?>
        </div>
    </div>
</div>