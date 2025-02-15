<?php
namespace poshtvan\app\providers\AiProviders;

class AiChatService
{
    public static function use()
    {
        return new Hooshina();
    }
}