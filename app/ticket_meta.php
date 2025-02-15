<?php
namespace poshtvan\app;
class ticket_meta
{
    private static function get_db(&$tbl_name)
    {
        global $wpdb;
        $tbl_name = $wpdb->prefix . 'mihanticket_ticketmeta';
        return $wpdb;
    }
    static function get_meta($ticket_id, $meta_key)
    {
        if(!$ticket_id)
        {
            return false;
        }
        $db = self::get_db($tbl_name);
        $query = "SELECT meta_value FROM {$tbl_name} WHERE ticket_id=%d AND meta_key=%s";
        return $db->get_var($db->prepare($query, $ticket_id, $meta_key));
    }
    private static function get_meta_id($ticket_id, $meta_key)
    {
        if(!$ticket_id)
        {
            return false;
        }
        $db = self::get_db($tbl_name);
        $query = "SELECT meta_id FROM {$tbl_name} WHERE ticket_id=%d AND meta_key=%s";
        return $db->get_var($db->prepare($query, $ticket_id, $meta_key));
    }
    static function update_meta($ticket_id, $meta_key, $value)
    {
        if(!$ticket_id)
        {
            return false;
        }
        $db = self::get_db($tbl_name);
        $id = self::get_meta_id($ticket_id, $meta_key);
        if(!$id)
        {
            return self::add_meta($ticket_id, $meta_key, $value);
        }
        return $db->update(
            $tbl_name,
            ['ticket_id' => $ticket_id, 'meta_key' => $meta_key, 'meta_value' => $value],
            ['meta_id' => $id],
            ['%d', '%s', '%s'],
            ['%d']
        );
    }
    static function add_meta($ticket_id, $meta_key, $value)
    {
        if(!$ticket_id)
        {
            return false;
        }
        $db = self::get_db($tbl_name);
        return $db->insert(
            $tbl_name,
            ['ticket_id' => $ticket_id, 'meta_key' => $meta_key, 'meta_value' => $value],
            ['%d', '%s', '%s']
        );
    }

    static function truncateTable()
    {
        $db = self::get_db($tblName);
        return $db->query("TRUNCATE TABLE {$tblName}");
    }
}
