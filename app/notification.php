<?php
namespace poshtvan\app;

use poshtvan\app\providers\smsProvider;

class notification
{
    static function filter_notification_content($content, $user_id, $ticket_id, $date)
    {
        $search = ['[[username]]', '[[display_name]]', '[[ticket_id]]', '[[date]]'];
        $user = get_user_by('id', $user_id);
        if(!$user)
        {
            return $content;
        }
        $display_name = $user->display_name;
        $username = $user->user_login;
        $date = tools::getDate(strtotime($date));
        $replacement = [$username, $display_name, $ticket_id, $date];
        $content = str_replace($search, $replacement, $content);
        return $content;
    }
    static function filterSmsNotificationContent($content, $userID, $ticket_id, $date, $operatorUserID='')
    {
        $search = ['[[username]]', '[[display_name]]', '[[ticket_id]]', '[[date]]', '[[ticket_operator_name]]'];
        $user = get_user_by('id', $userID);
        if(!$user)
        {
            return $content;
        }
        $display_name = $user->display_name;
        $username = $user->user_login;
        $date = tools::getDate(strtotime($date));
        $replacement = [$username, $display_name, $ticket_id, $date];
        if($operatorUserID)
        {
            $operator = get_user_by('id', $operatorUserID);
            if($operator && $operator->display_name)
            {
                $replacement[] = $operator->display_name;
            }
            
        }
        $content = str_replace($search, $replacement, $content);
        return $content;
    }
    static function handleNewTicketNotification($ticket_id, $owner_id, $ticket_parent_id, $date)
    {
        $data = [
            'ticket_id' => $ticket_id,
            'owner_id' => $owner_id,
            'ticket_parent_id' => $ticket_parent_id,
        ];
        if($ticket_parent_id)
        {
            // reply
            $ticket_parent_owner_id = tickets::get_ticket_owner($ticket_parent_id);
            if($ticket_parent_owner_id === $owner_id)
            {
                // ticket submit by user => notify supporter
                if(options::is_send_email_to_admin_after_submit_reply())
                {
                    self::send_reply_notify_to_supporter($owner_id, $ticket_parent_id, $date);
                }
                if(options::isAdminSmsNotificationActiveOnSubmitTicketReply())
                {
                    self::sendReplyNotificationToOperator($owner_id, $ticket_parent_id, $date);
                }
            }else{
                // ticket submit by supporter => notify user
                if(options::is_send_email_to_user_after_submit_reply())
                {
                    self::send_reply_notify_to_owner($ticket_parent_owner_id, $ticket_parent_id, $date);
                }
                if(options::isUserSmsNotificationActiveOnSubmitTicketReply())
                {
                    self::sendReplyNotificationToOwner($ticket_parent_owner_id, $ticket_parent_id, $date, $owner_id);
                }
            }
        }else{
            // handle new ticket notifications

            // send notification to user
            // handle email notification
            if(options::is_send_email_to_user_after_submit_new_ticket())
            {
                self::send_new_ticket_notify_to_owner($owner_id, $ticket_id, $date);
            }
            if(options::isUserSmsNotificationActiveOnSubmitNewTicket())
            {
                // send sms to user
                self::sendNewTicketSmsNotificationToOwner($owner_id, $ticket_id, $date);
            }

            // send notification to admin
            if(options::is_send_email_to_admin_after_submit_new_ticket())
            {
                self::send_new_ticket_notify_to_supporter($owner_id, $ticket_id, $date);
            }
            // handle sms notification
            if(options::isAdminSmsNotificationActiveOnSubmitNewTicket())
            {
                // send sms to operator
                self::sendNewTicketSmsNotificationToOperator($owner_id, $ticket_id, $date);
            }
        }
    }
    
    static function send_new_ticket_notify_to_owner($user_id, $ticket_id, $date)
    {
        $content = options::get_after_new_ticket_user_email_content();
        $content = self::filter_notification_content($content, $user_id, $ticket_id, $date);
        $user = get_user_by('id', $user_id);
        if(!$user)
        {
            return false;
        }
        $email = $user->user_email;
        $subject = options::get_after_new_ticket_user_email_subject();
        email::send_email($email, $subject, $content);
    }
    static function send_reply_notify_to_owner($user_id, $ticket_id, $date)
    {
        $content = options::get_after_reply_user_email_content();
        $content = self::filter_notification_content($content, $user_id, $ticket_id, $date);
        $user = get_user_by('id', $user_id);
        if(!$user)
        {
            return false;
        }
        $email = $user->user_email;
        $subject = options::get_after_reply_user_email_subject();
        email::send_email($email, $subject, $content);
    }
    static function send_new_ticket_notify_to_supporter($user_id, $ticket_id, $date)
    {
        $content = options::get_after_new_ticket_admin_email_content();
        $content = self::filter_notification_content($content, $user_id, $ticket_id, $date);

        $email = options::get_receive_email_notifications_email_address();
        $subject = options::get_after_new_ticket_admin_email_subject();
        email::send_email($email, $subject, $content);
    }
    static function send_reply_notify_to_supporter($user_id, $ticket_id, $date)
    {
        $content = options::get_after_reply_admin_email_content();
        $content = self::filter_notification_content($content, $user_id, $ticket_id, $date);

        $email = options::get_receive_email_notifications_email_address();
        $subject = options::get_after_reply_admin_email_subject();
        email::send_email($email, $subject, $content);
    }

    // sms notifications
    static function sendNewTicketSmsNotificationToOwner($userID, $ticketID, $date)
    {
        $content = options::getNewTicketUserSmsNotificationContent();
        $content = self::filterSmsNotificationContent($content, $userID, $ticketID, $date);
        if(!$content)
        {
            return false;
        }
        $userPhone = users::getPhoneNumber($userID);
        if(!$userPhone)
        {
            return false;
        }
        $patternID = options::getUserNewTicketSmsNoficationPatternID();
        return smsProvider::send_message($userPhone, $content, $patternID);
    }
    static function sendReplyNotificationToOwner($userID, $ticketID, $date, $ticketOperatorID)
    {
        $content = options::getReplyTicketUserSmsNotificationContent();
        $content = self::filterSmsNotificationContent($content, $userID, $ticketID, $date, $ticketOperatorID);
        if(!$content)
        {
            return false;
        }
        $userPhone = users::getPhoneNumber($userID);
        if(!$userPhone)
        {
            return false;
        }
        $patternID = options::getUserReplyTicketSmsNoficationPatternID();
        return smsProvider::send_message($userPhone, $content, $patternID);
    }

    static function sendNewTicketSmsNotificationToOperator($userID, $ticketID, $date)
    {
        $content = options::getNewTicketAdminSmsNotificationContent();
        $content = self::filterSmsNotificationContent($content, $userID, $ticketID, $date);
        if(!$content)
        {
            return false;
        }
        $operatorUserID = options::getActiveOperatorSmsNotificationReceiver();
        $operatorUser = get_user_by('id', $operatorUserID);
        if(!$operatorUser)
        {
            return false;
        }
        $operatorUserPhone = users::getPhoneNumber($operatorUserID);
        if(!$operatorUserPhone)
        {
            return false;
        }
        $patternID = options::getOperatorNewTicketSmsNoficationPatternID();
        return smsProvider::send_message($operatorUserPhone, $content, $patternID);
    }
    static function sendReplyNotificationToOperator($userID, $ticketID, $date)
    {
        $content = options::getReplyTicketAdminSmsNotificationContent();
        $content = self::filterSmsNotificationContent($content, $userID, $ticketID, $date);
        if(!$content)
        {
            return false;
        }
        $operatorUserID = options::getActiveOperatorSmsNotificationReceiver();
        $operatorUser = get_user_by('id', $operatorUserID);
        if(!$operatorUser)
        {
            return false;
        }
        $operatorUserPhone = users::getPhoneNumber($operatorUserID);
        if(!$operatorUserPhone)
        {
            return false;
        }
        $patternID = options::getOperatorReplyTicketSmsNoficationPatternID();
        return smsProvider::send_message($operatorUserPhone, $content, $patternID);
    }
}