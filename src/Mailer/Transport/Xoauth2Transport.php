<?php

use Cake\Mailer\Message;
use Cake\Mailer\Transport\SmtpTransport;

class Xoauth2Transport extends SmtpTransport
{
    public function send(Message $message): array
    {
        // Do something.
        return [];
    }
}
