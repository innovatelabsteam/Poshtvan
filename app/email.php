<?php
namespace poshtvan\app;
class email
{
    static function send_email($email, $subject, $content)
    {
        if(!$email)
        {
            return false;
        }
        $email_args = [
            'to' => $email,
            'subject' => $subject,
            'message' => $content,
            'headers' => 'Content-Type: text/html'
        ];
        return wp_mail(
            $email_args['to'],
            $email_args['subject'],
            $email_args['message'],
            $email_args['headers']
        );
    }
}