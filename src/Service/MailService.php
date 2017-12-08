<?php

namespace cds\Service;

use cds\Config\Config;
use Mail;
use PEAR;

class MailService
{
    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $message
     * @return bool
     */
    public function sendEmail($from, $to, $subject, $message)
    {
        if (Config::get()['MailService']['environment'] !== 'PROD') {
            $from = Config::get()['MailService']['test_from_surrogate'];
            $to = Config::get()['MailService']['test_to_surrogate'];
        }

        if (
            !filter_var($from, FILTER_VALIDATE_EMAIL) ||
            !filter_var($to, FILTER_VALIDATE_EMAIL)
        ) {
            error_log('Could not send email, because addresses are invalid:  from ' . $from . " to " . $to);
            return false;
        }

        $headers = array(
            'From' => $from,
            'To' => $to,
            'Subject' => $subject
        );

        $smtp = Mail::factory('smtp', array(
            'host' => Config::get()['MailService']['server_url'],
            'port' => Config::get()['MailService']['server_port'],
            'auth' => true,
            'username' => Config::get()['MailService']['user'],
            'password' => Config::get()['MailService']['pass']
        ));

        // Send the mail
        $result = $smtp->send($to, $headers, $message);

        if (PEAR::isError($result)) {
            error_log("Error while sending mail: " . $result->getMessage());
        }

        return $result;
    }
}