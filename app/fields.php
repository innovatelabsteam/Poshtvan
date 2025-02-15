<?php
namespace poshtvan\app;
class fields
{
    private static function get_db(&$tbl_name=false)
    {
        global $wpdb;
        $tbl_name = $wpdb->prefix . 'mihanticket_fields';
        return $wpdb;
    }
    static function get_fields()
    {
        $db = self::get_db($tbl_name);
        $sql = "SELECT * From {$tbl_name} ORDER BY priority";
        return $db->get_results($sql);
    }
    static function add_new_field($data=[])
    {
        if(!$data)
        {
            return false;
        }
        $db = self::get_db($tbl_name);
        $insert_res = $db->insert(
            $tbl_name,
            $data
        );
        return $insert_res ? $db->insert_id : false;
    }
    static function update_fields($id, $data=[], &$error=false)
    {
        if(!$id || !$data)
        {
            return false;
        }
        $db = self::get_db($tbl_name);
        $update_res = $db->update(
            $tbl_name,
            $data,
            ['id' => $id]
        );
        $error = $db->last_error;
        return $update_res;
    }
    static function delete_field($id)
    {
        if(!$id)
        {
            return false;
        }
        $db = self::get_db($tbl_name);
        return $db->delete(
            $tbl_name,
            ['id' => $id]
        );
    }
    static function save_extra_fields_meta($ticket_id, $value)
    {
        if(!$ticket_id || !$value)
        {
            return false;
        }
        return ticket_meta::update_meta($ticket_id, 'extra_fields', $value);
    }
    static function get_extra_fields_value($ticket_id)
    {
        if(!$ticket_id)
        {
            return false;
        }
        return ticket_meta::get_meta($ticket_id, 'extra_fields');
    }
}