<?php
/**
 * Plugin Name: Poshtvan
 * Description: Ticket management plugin
 * Author: MihanWp
 * Author URI: https://mihanwp.com/
 * Plugin URI: https://mihanwp.com/poshtvan/
 * Version: 6.1
 * License: GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * domain path: /lang
 * Text Domain: poshtvan
 */

use poshtvan\app\bootstrap;

defined('ABSPATH') || exit('No Access');
final class PoshtvanApp
{
    private static $_instance;
    static function get_instance()
    {
        if(!self::$_instance)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    function __construct()
    {
        if(class_exists('MihanTicketApp'))
        {
            $this->disableMihanticket();
        }
        $this->defines();
        $this->autoload();
        bootstrap::init();
    }
    function disableMihanticket()
    {
        $all = get_option('active_plugins');
        if(in_array('mihanticket/mihanticket.php', $all))
        {
            deactivate_plugins('mihanticket/mihanticket.php');
        }
    }
    function autoload()
    {
        try{
            spl_autoload_register([$this, 'handle_autoload']);
        } catch(\Exception $e){
            echo esc_html($e->getMessage());
        }
    }
    function handle_autoload($className)
    {
        if(strpos($className, 'poshtvan') !== false)
        {
            $className = str_replace('poshtvan\\', '', $className);
            $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
            $className = POSHTVAN_DIR_PATH . $className . '.php';
            if(file_exists($className) && is_readable($className))
            {
                include_once $className;
            }
        }
    }
    function defines()
    {
        define('POSHTVAN_APP', __FILE__);
        define('POSHTVAN_DIR_PATH', plugin_dir_path(POSHTVAN_APP));
        define('POSHTVAN_DIR_URL', plugin_dir_url(POSHTVAN_APP));
        define('POSHTVAN_LANG', basename(POSHTVAN_DIR_PATH) . '/lang');
        define('POSHTVAN_SOURCE_VERSION', 3);
    }
}
PoshtvanApp::get_instance();
