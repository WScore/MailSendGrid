MailSendGrid
============

sends mails using SendGrid and MailQueue.

Usage
-----

### simple usage

```php
use Wscore\MailSendgrid\Mailer\MailSendGrid;

MailSendGrid::forge('api key for SendGrid')
    ->mail(
        'mail-to@example.com',
        'subject of the mail',
        'body text of the mail',
        [
            'from' => 'from@example.com',
            'cc' => 'cc1@example.com, cc2@example.com',
            'bcc' => 'bcc1@example.com, bcc2@example.com',
        ]
    );
```

### For MailQueue

save mails

```php
use Wscore\MailSendgrid\Que\QueMails;

$que = QueMails::forge(new \PDO(DSN), 'mail_mags');
$que->queue(
        'mail-to@example.com',
        'subject of the mail',
        'body text of the mail',
        [
            'from' => 'from@example.com',
            'cc' => 'cc1@example.com, cc2@example.com',
            'bcc' => 'bcc1@example.com, bcc2@example.com',
        ]
);

// get queID to send mails
$queID = $que->getQueId();
```

then, send mails

```php
use WScore\MailQueue\Queue\SendQueue;use Wscore\MailSendgrid\Que\QueSend;

$queue = QueSend::forge('api key for SendGrid');
$sender = SendQueue::forgeWithSender($queue, new \PDO(DSN), 'mail_mags');
$sender->sendQueId(queID);
```