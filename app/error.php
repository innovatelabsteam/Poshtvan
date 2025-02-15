<?php
namespace poshtvan\app;
class error
{
    private static $_errors = [];

    static function add_error($code = '', $message = '')
    {
        self::$_errors[$code][] = $message;
    }
    static function has_error()
    {
        return self::$_errors ? true : false;
    }
    static function get_error_codes()
    {
        if(!self::has_error())
        {
            return [];
        }
        return array_keys(self::$_errors);
    }
    static function get_error_code()
    {
        $codes = self::get_error_codes();
        if(!$codes)
        {
            return false;
        }
        return $codes[0];
    }
    static function get_error_messages($code = '')
    {
        if(!self::has_error())
        {
            return [];
        }
        if(!$code)
        {
            $all_messages = [];
            foreach(self::$_errors as $error_code => $error_message)
            {
                $all_messages[] = $error_message;
            }
            return $all_messages;
        }
        if(isset(self::$_errors[$code]))
        {
            return self::$_errors[$code];
        }
        return [];
    }
    static function get_error_message($code = '')
    {
        if(!$code)
        {
            $code = self::get_error_code();
        }
        $messages = self::get_error_messages($code);
        return $messages ? $messages[0] : '';
    }
    static function remove($code)
    {
        unset(self::$_errors[$code]);
    }
}