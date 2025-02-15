<?php
namespace poshtvan\app;
class bootstrap
{
    private static $_wpdb;
    static function init()
    {
        hooks::init();
        ajax::init();
        // init subscriptions
        subscriptions::init();
    }
    static function handle_db_table_creation()
    {
        // create database tables
        $current_db_version = self::get_db_version();
        if($current_db_version < POSHTVAN_SOURCE_VERSION)
        {
            if($current_db_version < 1)
            {
                self::create_tickets_table();
                self::create_ticketmeta_table();
            }
            if($current_db_version < 2)
            {
                // create fields table
                self::create_fields_table();
            }
            if($current_db_version < 3 && $current_db_version !== 0)
            {
                // alter status column in tickets table
                // do it just when poshtvan is not install for first time
                self::modifyStatusColumnInTicketTable();
            }
            self::update_db_version(POSHTVAN_SOURCE_VERSION);
        }
    }
    static function get_db_version()
    {
        return intval(get_option('mihanticket_db_version'));
    }
    static function update_db_version($value)
    {
        return update_option('mihanticket_db_version', $value);
    }
    static function dbdelta($query)
    {
        if(!$query)
        {
            return false;
        }
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbdelta($query);
    }
    private static function get_wpdb()
    {
        if(!self::$_wpdb)
        {
            global $wpdb;
            self::$_wpdb = $wpdb;
        }
        return self::$_wpdb;
    }
    private static function get_charset_collate()
    {
        $wpdb = self::get_wpdb();
        return $wpdb->has_cap('collation') ? $wpdb->get_charset_collate() : '';
    }
    static function get_tickets_table_name()
    {
        $wpdb = self::get_wpdb();
        return $wpdb->prefix . 'mihanticket_tickets';
    }
    static function get_ticketmeta_table_name()
    {
        $wpdb = self::get_wpdb();
        return $wpdb->prefix . 'mihanticket_ticketmeta';
    }
    static function get_ticket_fields_table_name()
    {
        $wpdb = self::get_wpdb();
        return $wpdb->prefix . 'mihanticket_fields';
    }
    static function create_tickets_table()
    {
        $tbl_name = self::get_tickets_table_name();
        $charset_collate = self::get_charset_collate();
        $query = "CREATE TABLE IF NOT EXISTS {$tbl_name} (
            id bigint NOT NULL AUTO_INCREMENT,
            user_id int(11) NOT NULL,
            subject varchar(200) DEFAULT NULL,
            content longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 1,
            parent_ticket_id bigint DEFAULT NULL,
            created_date datetime NOT NULL DEFAULT current_timestamp(),
            update_date datetime NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY status (status),
            KEY parent_ticket_id (parent_ticket_id)
            ) ENGINE=InnoDB {$charset_collate};";
        self::dbdelta($query);
    }
    static function create_ticketmeta_table()
    {
        $tbl_name = self::get_ticketmeta_table_name();
        $charset_collate = self::get_charset_collate();
        $query = "CREATE TABLE IF NOT EXISTS {$tbl_name} (
            meta_id bigint NOT NULL AUTO_INCREMENT,
            ticket_id bigint NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext DEFAULT NULL,
            PRIMARY KEY  (meta_id),
            KEY ticket_id (ticket_id),
            KEY meta_key (meta_key)
            ) ENGINE=InnoDB {$charset_collate};";
        self::dbdelta($query);
    }
    static function create_fields_table()
    {
        $tbl_name = self::get_ticket_fields_table_name();
        $charset_collate = self::get_charset_collate();
        $query = "CREATE TABLE IF NOT EXISTS {$tbl_name} (
            id int(11) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            label tinytext NOT NULL,
            required tinyint(4) NOT NULL,
            type tinyint(4) NOT NULL,
            priority int(11) NOT NULL,
            PRIMARY KEY  (id)
            ) ENGINE=InnoDB {$charset_collate};";
        self::dbdelta($query);
    }
    static function redirect_after_activate()
    {
        $menu = admin_url('admin.php?page=poshtvan_settings');
        wp_safe_redirect($menu);
        exit();
    }
    static function modifyStatusColumnInTicketTable()
    {
        // ALTER TABLE `tableName` CHANGE `status` `status` VARCHAR(20) NOT NULL DEFAULT '1';
        // ALTER TABLE `tableName` MODIFY `status` VARCHAR(20) NOT NULL DEAFULT '1';
        $wpdb = self::get_wpdb();
        $tableName = self::get_tickets_table_name();
        $command = "ALTER TABLE {$tableName} MODIFY `status` VARCHAR(20) NOT NULL DEFAULT '1'";
        $wpdb->query($command);
    }
}