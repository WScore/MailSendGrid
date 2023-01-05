<?php
namespace Wscore\MailSendgrid\Que;

use SendGrid\Mail\Mail;
use WScore\MailQueue\Mail\MailData;
use WScore\MailQueue\Sender\SenderInterface;
use WScore\MailQueue\Sender\SendErrorException;
use Wscore\MailSendgrid\Mailer\MailSendGrid;

class QueSend implements SenderInterface
{
    private MailSendGrid $sendGrid;

    public function __construct(MailSendGrid $sendGrid)
    {
        $this->sendGrid = $sendGrid;
    }

    public static function forge(string $apiKey): QueSend
    {
        return new self(MailSendGrid::forge($apiKey));
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function send(MailData $mailData): bool
    {
        $email = new Mail();
        $email->setFrom($mailData->getFrom()->getAddress(), $mailData->getFrom()->getName());
        if ($mailData->getReplyTo()) {
            $email->setReplyTo($mailData->getReplyTo()->getAddress(), $mailData->getReplyTo()->getName());
        }
        $email->setSubject($mailData->getSubject());
        $email->addContent('text/plain', $mailData->getText());

        foreach ($mailData->getTo() as $to) {
            $email->addTo($to->getAddress(), $to->getName());
        }
        foreach ($mailData->getCc() as $cc) {
            $email->addCc($cc->getAddress(), $cc->getName());
        }
        foreach ($mailData->getBcc() as $bcc) {
            $email->addBcc($bcc->getAddress(), $bcc->getName());
        }

        $response = $this->sendGrid->send($email);
        if ($response->statusCode() >= 300 ) {
            throw SendErrorException::failed($response->body());
        }

        return true;
    }
}