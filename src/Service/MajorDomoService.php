<?php
namespace cds\Service;


use cds\Config\Config;

class MajorDomoService
{
    private $mailService;

    public function __construct()
    {
        $this->mailService = new MailService();
    }

    public function subscribe($email)
    {
        $message = sprintf(Config::get()['MajorDomoService'] ['majordomo_subscribe_command'], $email);

        return $this->mailService->sendEmail(
            Config::get()['MajorDomoService'] ['mail_from'],
            Config::get()['MajorDomoService'] ['majordomo_to'],
            Config::get()['MajorDomoService'] ['majordomo_subscribe_subject'],
            $message
        ) && $this->mailService->sendEmail(
            Config::get()['MajorDomoService'] ['mail_from'],
            $email,
            Config::get()['MajorDomoService'] ['subscribe_success_email_subject'],
            Config::get()['MajorDomoService'] ['subscribe_success_email_msg']
        );
    }

    public function unsubscribe($email)
    {
        $message = sprintf(Config::get()['MajorDomoService'] ['majordomo_unsubscribe_command'], $email);

        return $this->mailService->sendEmail(
            Config::get()['MajorDomoService'] ['mail_from'],
            Config::get()['MajorDomoService'] ['majordomo_to'],
            Config::get()['MajorDomoService'] ['majordomo_unsubscribe_subject'],
            $message
        ) && $this->mailService->sendEmail(
            Config::get()['MajorDomoService'] ['mail_from'],
            $email,
            Config::get()['MajorDomoService'] ['unsubscribe_success_email_subject'],
            Config::get()['MajorDomoService'] ['unsubscribe_success_email_msg']
        );
    }
}