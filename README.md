MailSendGrid
============

sends mails using SendGrid and MailQueue.

Usage of Simple Mail
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

