<?php
namespace Wscore\MailSendgrid\Que;

use WScore\MailQueue\Mail\MailData;
use WScore\MailQueue\Queue\QueueDba;
use WScore\MailQueue\Queue\SaveQueue;

class QueMails
{
    public static string $mail_table = 'mail_queue';

    private SaveQueue $save;

    public function __construct(SaveQueue $save)
    {
        $this->save = $save;
    }

    public static function forge(\PDO $pdo, string $que_table = null): QueMails
    {
        $que_table = $que_table ?? self::$mail_table;
        $queID = date('YmdHi-').uniqid();

        $dba = new QueueDba($pdo, $que_table);
        $save = new SaveQueue($dba);
        $save->withQueId($queID);

        return new self($save);
    }

    public function queue(string $to, string $subject, string $body, array $options = [])
    {
        $email = $this->createMail($to, $subject, $body, $options);
        $this->save->save($email);
    }

    public function getQueId(): ?string
    {
        return $this->save->getQueId();
    }

    public function createMail(string $to, string $subject, string $body, array $options = []): MailData
    {
        $email = new MailData();
        $email->addTo($to);
        $email->setSubject($subject);
        $email->setText($body);
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
}