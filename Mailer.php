<?php
/**
 * @link https://github.com/SDKiller/zyx-phpmailer
 * @copyright Copyright (c) 2014 Serge Postrash
 * @license BSD 3-Clause, see LICENSE.md
 */

namespace zyx\phpmailer;

use Yii;
use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;


class Mailer extends BaseMailer
{
    /**
     * @var string $messageClass - message default class name
     */
    public $messageClass = 'zyx\phpmailer\Message';
    /**
     * @var Adapter $adapter - instance extended from phpMailer class
     */
    public $adapter;
    /**
     * @var array $config - configuration array of initial phpMailer settings
     */
    public $config;
    /**
     * @var bool $success - the result which has been passed by PHPMailer to our callback function
     * (it was designed as static property to avoid 'Using this when not in object context' error on callback)
     */
    protected static $success = false;
    /**
     * @var string $htmlView - html view file, may be set as default (we introduce it in addition to default '$htmlLayout')
     */
    public $htmlView = null;


    /**
     * For example, you may predefine in 'messageConfig' default contents of 'From' field:
     * ~~~
     * 'mail' => [
     *      ...
     *      'messageConfig'    => [
     *          'from' => ['noreply@example.com' => 'My Example Site']
     *      ],
     *      ...
     * ];
     * ~~~
     */
    public function init()
    {
        $this->adapter = new Adapter();

        //setting public properties defined in 'components' => 'mail' => 'config' => [...]
        if (!empty($this->config) && is_array($this->config)) {
            foreach (get_object_vars($this->adapter) as $prop => $value) {
                $key = strtolower($prop);
                if (array_key_exists($key, $this->config)) {
                    $this->adapter->$prop = $this->config[$key];
                }
            }
            if (!empty($this->config['ishtml'])) {
                $this->adapter->isHTML(true);
            } else {
                $this->adapter->isHTML(false);
            }
        }

        //Note: PHPMailer in [[createBody()]] overrides charset and sets 'us-ascii' if no 8-bit chars are found!
        if (empty($this->config['charset']) && empty($this->messageConfig['charset'])) {
            $this->adapter->setCharset(Yii::$app->charset);
        }
        if (empty($this->config['language'])) {
            $this->adapter->setLanguage(Yii::$app->language);
        }
        if (empty($this->config['callback'])) {
            $this->adapter->setCallback('zyx\phpmailer\Mailer::processResult');
        }

        //Set current message date initially - a workaround for MessageDate bug in PHPMailer <= 5.2.7
        //see https://github.com/PHPMailer/PHPMailer/pull/227

        $this->adapter->setMessageDate();
    }

    /**
     * @inheritdoc
     */
    public function compose($view = null, array $params = [])
    {
        //attempt to override default layouts dinamically
        if (array_key_exists('htmlLayout', $params)) {
            $this->htmlLayout = $params['htmlLayout'];
            unset($params['htmlLayout']);
        }
        if (array_key_exists('textLayout', $params)) {
            $this->textLayout = $params['textLayout'];
            unset($params['textLayout']);
        }

        $message = $this->createMessage();

        if ($view !== null) {

            //Note: we cannot process plain text and html bodies at the same time!
            //If we mean 'multipart/alternative' - content in both HTML and plain text format cannot much differ
            //because of anti-spam filters - see http://wiki.apache.org/spamassassin/Rules/MPART_ALT_DIFF
            //PHPMailer autocreates alt-body - we don't need to call additionally [[setTextBody()]] like in parent method

            $params['message'] = $message;

            if (is_array($view)) {

                if (isset($view['html'])) {
                    $this->htmlView = $view['html'];
                    if (sizeof($params) > 1) {
                        //render view only if something is passed to it, othervise consider we just wanted to setup view
                        $html = $this->render($this->htmlView, $params, $this->htmlLayout);
                    }
                } elseif (isset($view['text'])) {
                    //keep it simple - preserve parent behavior for text rendering
                    $text = $this->render($view['text'], $params, $this->textLayout);
                    if (!empty($text)) {
                        $message->setTextBody($text);
                    }
                }

            } else {
                //string argument is considered as html view by BaseMailer - preserve that behavior
                $this->htmlView = $view;
                if (sizeof($params) > 1) {
                    //render view only if something is passed to it, othervise consider we just wanted to setup view
                    $html = $this->render($this->htmlView, $params, $this->htmlLayout);
                }
            }

            if (!empty($html)) {
                $message->setHtmlBody($html);
            }
        }

        return $message;
    }

    /**
     * @inheritdoc
     */
    protected function createMessage()
    {
        $config = array();

        //we have to put 'mailer' at the beginning of configuration array!!!
        $config['mailer'] = $this;

        if (!array_key_exists('class', $this->messageConfig)) {
            $config['class'] = $this->messageClass;
        }
        foreach ($this->messageConfig as $key => $val) {
            $config[$key] = $val;
        }

        return Yii::createObject($config);
    }

    /**
     * @inheritdoc
     */
    public function send($message)
    {
        if (!$this->beforeSend($message)) {
            return false;
        }

        if ($this->useFileTransport) {
            $isSuccessful = $this->saveMessage($message);
        } else {
            $isSuccessful = $this->sendMessage($message);
        }
        $this->afterSend($message, $isSuccessful);

        return $isSuccessful;
    }

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        //Actually PHPMailer returns 'false' on error or exception when composing message on [[preSend()]] stage,
        //after that [[send()]] will return 'true', actual result of sending message is returned to callback function

        $result = $this->adapter->send();

        if ($result === false) {
            //Failure on the very first step, callback function will NOT be called by PHPMailer
            self::$success = $result;
            if (YII_DEBUG) {
                Yii::info('FAILED - Sending email ' . implode(';', array_keys($message->getTo())) . ' "' . $message->getSubject() . '"', 'application');
            }
        }

        //If not - static property will be modified by PHPMailer via our [[processResult()]] callback

        return self::$success;
    }

    /**
     * @inheritdoc
     */
    public function beforeSend($message)
    {
        //actually nothing to do here
        //parent '$event->isValid' implementation makes no sense for us if PHPMailer returns 'false' on [[send()]]

        return true;
    }

    /**
     * This method is invoked right after mail was send.
     * You may override this method to do some postprocessing or logging based on mail send status.
     * If you override this method, please make sure you call the parent implementation first.
     *
     * Note: as of Yii 2.0.0-beta - hardcoded swiftmailer methods result in UnknownMethodException in debug MailPanel,
     * for patch @see https://github.com/yiisoft/yii2/commit/3838b501babc1f5b9e6b0ba452512fba136d3102
     *
     * @param \yii\mail\MessageInterface $message
     * @param boolean                    $isSuccessful
     */
    public function afterSend($message, $isSuccessful)
    {
        parent::afterSend($message, $isSuccessful);
    }

    /**
     * @inheritdoc
     */
    protected function saveMessage($message)
    {
        //we have to generate message first - or [[toString()]] returns empty value
        $this->adapter->preSend();

        return parent::saveMessage($message);
    }

    /**
     * This is a callback function to retrieve result returned by PHPMailer
     *
     * @var bool    $result        result of the send action
     * @var string  $to            email address of the recipient
     * @var string  $cc            cc email addresses
     * @var string  $bcc           bcc email addresses
     * @var string  $subject       the subject
     * @var string  $body          the email body
     * @var string  $from          email address of sender
     */
    public function processResult($result, $to = '', $cc = '', $bcc = '', $subject = '', $body = '', $from = '')
    {
        self::$success = $result;

        if (YII_DEBUG) {
            //native PHPMailer's way to pass results to [[doCallback()]] function is a little bit strange
            $address = (!empty($to)) ? ('To: ' . $to) : '';
            $address .= (!empty($cc)) ? ('Cc: ' . $cc) : '';
            $address .= (!empty($bcc)) ? ('Bcc: ' . $bcc) : '';
            Yii::info(((($result) ? 'OK' : 'FAILED') . ' - Sending email ' . $address . ' "' . $subject . '"'), 'application');
        }
    }

}
