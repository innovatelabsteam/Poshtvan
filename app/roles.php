<?php
namespace poshtvan\app;
class roles
{
    const SUPPORTER_CAP_NAME = 'mihanticket_supporter';
    static function add_supporter_cap($role_name)
    {
        $role = get_role($role_name);
        if(!$role)
        {
            return false;
        }
        return $role->add_cap(self::SUPPORTER_CAP_NAME);
    }
    static function remove_supporter_cap($role_name)
    {
        $role = get_role($role_name);
        if(!$role)
        {
            return false;
        }
        return $role->remove_cap(self::SUPPORTER_CAP_NAME);
    }
    static function remove_all_roles_caps()
    {
        self::remove_supporter_cap('administrator');
        $roles_accessed_to_ticket_list = options::get_roles_access_to_ticket_list();
        foreach($roles_accessed_to_ticket_list as $role)
        {
            self::remove_supporter_cap($role);
        }
    }
    static function get_roles_name()
    {
        $roles = wp_roles()->get_names();
        if(isset($roles['administrator']))
        {
            unset($roles['administrator']);
        }
        return $roles;
    }
}