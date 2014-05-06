zyx-phpmailer
=============

PHPMailer integration for Yii 2 framework
-----------------------------------------

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

You have two options here - to install latest **stable** release of **PHPMailer** (as of now it is 5.2.7) or install
latest development version from **dev-master** (you are encouraged to do so as many significant issues were solved and
improvements made since last stable release, but anyway **_the choice is up to you_**).


So, either run

- for PHPMailer stable:

```
php composer.phar require --prefer-dist zyx/zyx-phpmailer "dev-stable"
```

- for dev:

```
php composer.phar require --prefer-dist zyx/zyx-phpmailer "dev-master"
```


or add respectively:

```
"zyx/zyx-phpmailer": "dev-stable"
```

or

```
"zyx/zyx-phpmailer": "dev-master"
```

to the require section of your composer.json.



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

TODO
====


USAGE
------------

TODO
====
