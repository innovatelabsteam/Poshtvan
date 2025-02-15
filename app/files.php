<?php
namespace poshtvan\app;
class files
{
    static function get_file_path($file_name, $extension = 'php')
    {
        if(!$file_name)
        {
            return false;
        }
        $file_name = str_replace('.', DIRECTORY_SEPARATOR, $file_name);
        $file_name = POSHTVAN_DIR_PATH . $file_name . '.' . $extension;
        return file_exists($file_name) && is_readable($file_name) ? $file_name : false;
    }
    static function get_file_url($file_name, $extension = null)
    {
        if(!$file_name)
        {
            return false;
        }
        $file_name = str_replace('.', '/', $file_name);
        $file_name = str_replace("#", '.', $file_name);
        if($extension)
        {
            $file_name .= '.' . $extension;
        }
        $file_name = POSHTVAN_DIR_URL . $file_name;
        return $file_name;
    }
}