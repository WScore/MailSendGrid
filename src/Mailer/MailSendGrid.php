<?php
namespace Wscore\MailSendgrid\Mailer;

use SendGrid;
use SendGrid\Mail\Mail;
use SendGrid\Response;

/**
 * Simple mailer class for old mail_jp argument.
 */
class MailSendGrid
{
    private string $apiKey;
    private SendGrid $sender;

    public function __construct(string $apiKey, SendGrid $sender)
    {
        $this->apiKey = $apiKey;
        $this->sender = $sender;
    }

    public static function forge(string $apiKey): MailSendGrid
    {
        return new self($apiKey, new SendGrid($apiKey));
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function makeMail(string $to, string $subject, string $body, array $options = array()): Mail
    {
        $email = new SendGrid\Mail\Mail();
        foreach ($this->listEmails($to) as $mail) {
            $email->addTo($mail);
        }
        $email->setSubject($subject);
        $email->addContent('text/plain', $body);
        foreach ($options as $key => $value) {
            $key = strtolower($key);
            if (!$value) continue;
            if ($key === 'from') {
                if (is_array($value)) {
                    $email->setFrom($value['address'], $value['name']);
                } else {
                    $email->setFrom($value);
                }
            } elseif( $key === 'reply') {
                if (is_array($value)) {
                    $email->setReplyTo($value['address'], $value['name']);
                } else {
                    $email->setReplyTo($value);
                }
            } elseif( $key === 'cc') {
                foreach ($this->listEmails($value) as $mail) {
                    $email->addCc($mail);
                }
            } elseif( $key === 'bcc') {
                foreach ($this->listEmails($value) as $mail) {
                    $email->addBcc($mail);
                }
            }
        }
        return $email;
    }

    private function listEmails(string $emails): array
    {
        $list = explode(',', $emails);
        foreach ($list as $key => $mail) {
            $list[$key] = strtolower(trim($mail));
        }
        return $list;
    }

    public function mail(string $to, string $subject, string $body, array $options = array()): Response
    {
        $email = $this->makeMail($to, $subject, $body, $options);
        return $this->send($email);
    }

    public function send(Mail $email): Response
    {
        return $this->sender->send($email);
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}