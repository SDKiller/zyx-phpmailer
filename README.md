zyx-phpmailer
=============

PHPMailer integration for Yii 2 framework
-----------------------------------------

[![Latest Stable Version](https://poser.pugx.org/zyx/zyx-phpmailer/v/stable.png)](https://packagist.org/packages/zyx/zyx-phpmailer)
[![Latest Unstable Version](https://poser.pugx.org/zyx/zyx-phpmailer/v/unstable.png)](https://packagist.org/packages/zyx/zyx-phpmailer)
[![Total Downloads](https://poser.pugx.org/zyx/zyx-phpmailer/downloads.png)](https://packagist.org/packages/zyx/zyx-phpmailer)
[![License](https://poser.pugx.org/zyx/zyx-phpmailer/license.png)](https://packagist.org/packages/zyx/zyx-phpmailer)



This extension adds integration of popular **[PHPMailer](https://github.com/PHPMailer/PHPMailer)** library -
a _"full-featured email creation and transfer class for PHP"_ - to **[Yii 2 framework](https://github.com/yiisoft/yii2)**.

Although extension classes implement `yii\mail\MailerInterface` and `yii\mail\MessageInterface`, some methods of Yii 2
`BaseMailer` and `BaseMessage` are overriden - mainly because of PHPMailer-specific issues.

Nevertheless - the behavior of the extension should remain expected and predictable and comply interfaces mentioned above
(I believe so). If not - feel free to report [issues](https://github.com/SDKiller/zyx-phpmailer/issues).


REQUIREMENTS
------------

You should generally follow [Yii 2 requirements](https://github.com/yiisoft/yii2/blob/master/README.md).
The minimum is that your Web server supports PHP 5.4.0.


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

During the extension installation [PHPMailer](https://github.com/PHPMailer/PHPMailer) library will be also installed.

You have two options here - to install latest **stable** release of **PHPMailer** (as of now it is 5.2.8 - many significant issues were solved and improvements made in this release) or install latest development version from **dev-master**.


So, either run

- for PHPMailer stable:

```
php composer.phar require --prefer-dist zyx/zyx-phpmailer "@stable"
```

- for dev:

```
php composer.phar require --prefer-dist zyx/zyx-phpmailer "dev-master"
```


or add respectively:

```
"zyx/zyx-phpmailer": "@stable"
```

or

```
"zyx/zyx-phpmailer": "dev-master"
```

to the require section of your composer.json.


**Note:** this can be affected by 'minimum-stability' settings in your project root composer.json file. If you see error
trying to install 'dev-master' branch of PHPMailer you should explicitly point to it, adding

```
"phpmailer/phpmailer": "dev-master"
```

to the require section of composer.json in your project root.



### Install from an Archive File

You may install extension manually.
For that purpose you have to download archive file and extract it into `vendor/zyx` directory of your project.

You'll also need to download [PHPMailer](https://github.com/PHPMailer/PHPMailer) library and extract it to
`vendor/phpmailer` directory of your project.

**Note:** due to naming agreement 3rd-party extensions and libraries are kept under `vendor` directory.

One of the benefits of installing via Composer is automation of autoload setup.
If you install extension and PHPMailer library from archive files, you'll have to setup autoload paths manually.

**Note:** currently PHPMailer does not support namespaces (as its minimum requirements is php 5.0), but provides an
SPL-compatible autoloader, and...
> the preferred way of loading the library - just `require '/path/to/PHPMailerAutoload.php';` and everything should work

For further information refer to PHPMailer documentation.


CONFIGURING
------------


To use this extension, you should add some settings in your application configuration file.
It may be like the following:

```
return [
//....
	'components' => [
        'mail' => [
            'class'            => 'zyx\phpmailer\Mailer',
            'viewPath'         => '@common/mail',
            'useFileTransport' => false,
            'config'           => [
                'mailer'     => 'smtp',
                'host'       => 'smtp.yandex.ru',
                'port'       => '465',
                'smtpsecure' => 'ssl',
                'smtpauth'   => true,
                'username'   => 'mysmtplogin@example.ru',
                'password'   => 'mYsmTpPassword',
            ],
        ],
	],
];
```

This is the tipical configuration for sending email via smtp.
If you are familiar with PHPMailer, you can see that 'config' array holds settings, similar to corresponding
PHPMailer properties. They will be populated when Mailer is initialized.

You can for example define

```'mailer' => 'mail'``` or

```'mailer' => 'sendmail', 'sendmail' => '/path/to/sendmail'```  - just like in PHPMailer.

You may also configure some default message settings for your application in 'messageConfig' array.
They will be populated at the moment of message creation.

For example, you may predefine default contents of 'From' email field - so you will not have to set 'From'
it every time when composing message:

```
    ...
    'mail' => [
        ...
        'messageConfig'    => [
         'from' => ['noreply@example.com' => 'My Example Site']
        ],
        ...
    ],
    ...
```



USAGE
------------

Example of simple usage:

```
Yii::$app->mail->compose()
     ->setFrom(['noreply@example.com' => 'My Example Site'])
     ->setTo([$form->email => $form->name])
     ->setSubject($form->subject)
     ->setTextBody($form->text)
     ->send();
```

Example of sending html emails rendering view and layout (assumed that default 'From' is set in application configuration file):

```
Yii::$app->mail->compose('passwordResetToken', ['user'       => $user,
                                                'title'      => Yii::t('app', 'Password reset'),
                                                'htmlLayout' => 'layouts/html-reset'])
                ->setTo($this->email)
                ->setSubject(Yii::t('app', 'Password reset for ') . Yii::$app->params['siteName'])
                ->send();
```

**Note:** possibility to set 'htmlLayout' dynamically when composing message is not the native behavior of `BaseMailer`.
This feature was introduced in this specific extension and is experimental.


For more examples of usage you may refer to documentation on Yii 2 `BaseMailer` and `BaseMessage` and PHPDoc blocks in
the extension files.

Additionally some **[Wiki](https://github.com/SDKiller/zyx-phpmailer/wiki)** articles are available.
