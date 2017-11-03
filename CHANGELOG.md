0.9.3, November 3, 2017
-----------------------------

- Bug #14: Restricted PHPMailer version to 5.2 branch (Ziggizag)
- Bug #2: Callable passed to phpMailer [[doCallback()]] should be static (thanks dryu)
- Bug: Error in formatting mail recipients array for yii2-debug MailPanel (to, reply, cc, bcc not shown)
- Enh: Removed dev/stable separation of releases (let user defines his project stability settings himself).
- Enh: Refactored some methods to avoid duplicate code.
- Enh: Added method `Adapter::getVersion()`. Minor improvements and code style.
- Chg: Added external html2text tool in "require" (see https://github.com/PHPMailer/PHPMailer/issues/232)

0.9.2 (PHPMailer stable), May 15, 2014
-----------------------------

- Enh: Replaced '$path' with [[Yii::getAlias($path, false)]] in [[Message::attach()]] and [[Message::embed()]] methods.
- Chg: Updated phpmailer stable version to 'v5.2.8' in composer.json.


0.9.1 (PHPMailer stable), May 6, 2014
-----------------------------

- Chg: Replaced '@stable' with explicit 'v5.2.7' in composer.json for phpmailer version because project 'minimum-stability' settings override wildcards.

0.9.0 (PHPMailer stable), May 6, 2014
-----------------------------

- Initial release.
