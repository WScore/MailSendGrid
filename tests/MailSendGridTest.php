<?php

namespace Wscore\MailSendgrid\Mailer;

use PHPUnit\Framework\TestCase;

class MailSendGridTest extends TestCase
{
    public function testMakeMail()
    {
        $sender = MailSendGrid::forge('test');
        $mail = $sender->makeMail('test@example.com', 'test', 'test body', [
            'from' => 'from@example.com',
            'reply' => 'reply@example.com',
        ]);
        $this->assertEquals('from@example.com', $mail->getFrom()->getEmailAddress());
        $this->assertEquals('reply@example.com', $mail->getReplyTo()->getEmailAddress());
    }

    public function testMakeMailWithName()
    {
        $sender = MailSendGrid::forge('test');
        $mail = $sender->makeMail('test@example.com', 'test', 'test body', [
            'from' => ['address' => 'from@example.com', 'name' => 'From Name'],
            'reply' => ['address' => 'reply@example.com', 'name' => 'Reply Name'],
        ]);
        $this->assertEquals('From Name', $mail->getFrom()->getName());
        $this->assertEquals('Reply Name', $mail->getReplyTo()->getName());
    }
}
