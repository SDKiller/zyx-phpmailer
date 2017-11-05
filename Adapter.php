<?php
/**
 * @link      https://github.com/SDKiller/zyx-phpmailer
 * @copyright Copyright (c) 2014-2017 Serge Postrash
 * @license   BSD 3-Clause, see LICENSE.md
 */

namespace zyx\phpmailer;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * A wrapper class to resolve some inconsistencies both with \yii\mail\BaseMailer and PHPMailer
 *
 * @package zyx\phpmailer
 */
class Adapter extends PHPMailer
{
    /**
     * @var callable Advanced html2text converter
     * (bundled `html2text` was removed for license reasons in https://github.com/PHPMailer/PHPMailer/issues/232)
     */
    public $html2textHandler;


    /**
     * Returns the PHPMailer Version number.
     * @return string
     */
    public function getVersion()
    {
        return parent::VERSION;
    }

    /**
     * Sets the callback function to return results from PHPMailer (see PHPMailer property '$action_function')
     * @param callable $callback character set name.
     * @return void
     */
    public function setCallback($callback)
    {
        $this->action_function = $callback;
    }

    /**
     * Sets the character set of this message.
     * @param string $charset character set name.
     * @return void
     */
    public function setCharset($charset)
    {
        $this->CharSet = $charset;
    }

    /**
     * Returns the character set of this message.
     * @return string the character set of this message.
     */
    public function getCharset()
    {
        return $this->CharSet;
    }

    /**
     * Returns the message sender.
     * @return string the sender email
     */
    public function getFrom()
    {
        return $this->From;
    }

    /**
     * Returns the message sender.
     * @return array the sender email and name
     */
    public function getFromFull()
    {
        return [$this->From => $this->FromName];
    }

    /**
     * Sets the message subject.
     * @param string $subject message subject
     * @return void
     */
    public function setSubject($subject)
    {
        $this->Subject = $subject;
    }

    /**
     * Returns the message subject.
     * @return string the message subject
     */
    public function getSubject()
    {
        return $this->Subject;
    }

    /**
     * @inheritdoc
     */
    public function html2text($html, $advanced = false)
    {
        if ($advanced === true && is_callable($this->html2textHandler)) {

            return call_user_func($this->html2textHandler, $html);
        }

        return parent::html2text($html, $advanced);
    }

    /**
     * Sets message plain text content.
     * @param string $text message plain text conten
     * @return void
     */
    public function msgText($text)
    {
        $this->isHTML(false);
        $text = self::html2text($text, true);
        $text = self::normalizeBreaks($text);
        $this->Body = $text;
    }

    /**
     * Allows for public read access to 'MIMEHeader' property.
     * @return string
     */
    public function getMIMEHeader()
    {
        return $this->MIMEHeader;
    }

    /**
     * Allows for public read access to 'mailHeader' property.
     * @return string
     */
    public function getMailHeader()
    {
        return $this->mailHeader;
    }

    /**
     * Allows for public read access to 'MIMEBody' property.
     * @return string
     */
    public function getMIMEBody()
    {
        return $this->MIMEBody;
    }

    /**
     * @param int $timestamp
     * @return void
     */
    public function setMessageDate($timestamp = null)
    {
        if (empty($timestamp)) {
            $this->MessageDate = self::rfcDate();
        } else {
            date_default_timezone_set(@date_default_timezone_get());
            $this->MessageDate = date('D, j M Y H:i:s O', $timestamp);
        }
    }

    /**
     * @return string RFC 822 formatted message date
     */
    public function getMessageDate()
    {
        return $this->MessageDate;
    }

    /**
     * @return void
     */
    public function resetMailer()
    {
        $this->Subject = '';
        $this->Body = '';
        $this->AltBody = '';
        $this->MIMEBody = '';
        $this->MIMEHeader = '';
        $this->mailHeader = '';
        $this->MessageID = '';
        $this->MessageDate = '';
        $this->to = [];
        $this->cc = [];
        $this->bcc = [];
        $this->ReplyTo = [];
        $this->all_recipients = [];
        $this->attachment = [];
        $this->CustomHeader = [];
        $this->lastMessageID = '';
        $this->message_type = '';
        $this->boundary = [];
    }
}
