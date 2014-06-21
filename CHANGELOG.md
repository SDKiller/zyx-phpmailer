0.9.3 - under development
-----------------------------

- Bug #2: Callable passed to phpMailer [[doCallback()]] should be static (thanks dryu)
- Bug: Error in formatting mail recipients array for yii2-debug MailPanel (to, reply, cc, bcc not shown)
- Enh: Refactored some methods to avoid duplicate code.

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
