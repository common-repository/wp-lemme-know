<?php

/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

if (!defined('ABSPATH')) {
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;

class WP_LemmeKnowNotificationSender
{
    /** @var PHPMailer */
    private $mailer;

    /** @var bool */
    private $debug = false;

    /** @var string */
    private $debugOutput = '';

    /**
     * @param bool $smtp
     * @param string|null $hostname
     * @param int|null $port
     * @param string|null $user
     * @param string|null $pass
     * @param string|null $encryption
     * @param string|null $authMode
     */
    public function __construct(
        $smtp = false,
        $hostname = null,
        $port = null,
        $user = null,
        $pass = null,
        $encryption = null,
        $authMode = null
    ) {
        require_once ABSPATH.'wp-includes/PHPMailer/PHPMailer.php';

        $this->mailer = new PHPMailer(true);
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Debugoutput = function($str, $level) {
            $this->debugOutput .= sprintf(
                '%d: %s<br>',
                $level,
                $str
            );
        };

        if ($smtp) {
            require_once ABSPATH.'wp-includes/PHPMailer/SMTP.php';

            $this->mailer->isSMTP();
            $this->mailer->SMTPAutoTLS = false;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Host = $hostname;
            $this->mailer->Port = $port;
            $this->mailer->Username = $user;
            $this->mailer->Password = $pass;
            $this->mailer->SMTPSecure = $encryption;
            $this->mailer->AuthType = $authMode;

            // additional settings for PHP 5.6
            $this->mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ];
        }
    }

    /**
     * Sets From headers.
     *
     * @param string $email
     * @param string $name
     *
     * @return WP_LemmeKnowNotificationSender
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function setFrom($email, $name)
    {
        $this->mailer->setFrom($email, $name);

        return $this;
    }

    /**
     * Sets e-mail subject.
     *
     * @param string $subject
     *
     * @return WP_LemmeKnowNotificationSender
     */
    public function setSubject($subject)
    {
        $this->mailer->Subject = $subject;

        return $this;
    }

    /**
     * Sets e-mail message body.
     *
     * @param string $body
     *
     * @return WP_LemmeKnowNotificationSender
     */
    public function setBody($body)
    {
        $this->mailer->Body = $body;

        return $this;
    }

    /**
     * Sets e-mail recipient address.
     *
     * @param string $email
     *
     * @return WP_LemmeKnowNotificationSender
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function setAddress($email)
    {
        $this->mailer->clearAddresses();
        $this->mailer->clearReplyTos();

        $this->mailer->addAddress($email, $email);
        $this->mailer->addReplyTo($email, $email);

        return $this;
    }

    /**
     * Sets debugging option when sending message.
     *
     * @param bool $debug
     *
     * @return WP_LemmeKnowNotificationSender
     */
    public function setDebug($debug = true)
    {
        $this->mailer->SMTPDebug = $debug ? 2 : 0;
        $this->debug = $debug;

        return $this;
    }

    /**
     * Returns debugging log in HTML format.
     *
     * @return string
     */
    public function getDebugDetails()
    {
        return $this->debugOutput;
    }

    /**
     * Sends e-mail message using current configuration.
     *
     * @return bool
     */
    public function send()
    {
        if (empty($this->mailer->Body)) {
            $this->debugOutput = 'Initial data is missing';

            return false;
        }

        // temporary disable max_execution_time (doesn't work if PHP is running in safe-mode)
        ini_set('max_execution_time', 0);

        try {
            $this->mailer->send();
        } catch (Exception $e) {
            if (!$this->debug) {
                error_log(sprintf('wp-lemme-know error: %s', $e->getMessage()));
            }

            return false;
        }

        return true;
    }
}
