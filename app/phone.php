<?php
namespace poshtvan\app;
class phone
{
    static function getValidatedPhoneNumber($phone)
    {
        $phone = Tools::convertNumberLocale($phone);
        if(!is_numeric($phone))
        {
            return false;
        }
        $pattern = '/^(0|98|\+98)?(90|91|92|93|99)([0-9]{1})([0-9]{3})([0-9]{4})$/';
        $validation = preg_match($pattern, $phone, $matches);
        if(!$validation)
        {
            return false;
        }
        return preg_replace('/^(0|98|\+98)/', '', $phone);
    }
}