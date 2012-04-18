<?php if ( ! defined('BASEPATH')) die ('No direct script access allowed');

class SwiftMailer
{
    // base
    var $CI = FALSE;
    var $spark_path = FALSE;
    var $spark_config = FALSE;

    // state
    var $initialized = FALSE;

    // abstraction mode variables
    var $transport_type = FALSE;
    var $transport = FALSE;
    var $messages = array();
    var $mailer = FALSE;

    // constructor
    public function __construct()
    {
        // initialize base member variables
        $this->CI =& get_instance();
        $this->spark_path = dirname(__FILE__) . '/';

        // load configuration
        $config_path = $this->spark_path . '../config/swift-mailer' . EXT;
        if(file_exists($config_path))
        {
            require_once $config_path;
            if(isset($config)) $this->spark_config = $config;
        }

        // initialize SwiftMailer class
        $this->initialize();
    }

    public function get_config($config_name = FALSE)
    {
        if(!is_array($this->spark_config)) return FALSE;

        return isset($this->spark_config[$config_name]) ? $this->spark_config[$config_name] : FALSE;
    }

    public function initialize()
    {
        // find SwiftMailer include class
        $swift_required_path = $this->spark_path . '../vendor/swift-mailer/swift_required' . EXT;
        if(!file_exists($swift_required_path))
        {
            $this->CI->log->write_log('error', 'The swift-mailer spark could not find the swift-mailer vendor class.');
            return FALSE;
        }

        // include SwiftMailer
        if(!file_exists($swift_required_path)) return false;
        require_once $swift_required_path;

        // do we need to initialize abstraction mode?
        switch($this->get_config('swift_mailer_transport_type'))
        {
            case 'smtp':
            case 'sendmail':
            case 'mail':
                $this->transport_type = strtolower($this->get_config('swift_mailer_transport_type'));
                $this->initialize_abstraction_mode();
                break;
        }

        // initialization complete
        $this->CI->log->write_log('info', 'The swift-mailer spark has been initialized.');
        $this->initialized = TRUE;
    }

    private function create_smtp_transport()
    {
        /* Initialize transport */
        $this->transport = Swift_SmtpTransport::newInstance($this->get_config('swift_mailer_smtp_outgoing_server'), $this->get_config('swift_mailer_smtp_outgoing_server_port'));

        if($this->get_config('swift_mailer_smtp_username'))
            $this->transport->setUsername($this->get_config('swift_mailer_smtp_username'));

        if($this->get_config('swift_mailer_smtp_password'))
            $this->transport->setPassword($this->get_config('swift_mailer_smtp_password'));

        // configure ssl
        if($this->get_config('swift_mailer_smtp_use_starttls'))
            $this->transport->setEncryption('ssl');

        return TRUE;
    }

    private function create_sendmail_transport()
    {
        if(!$this->get_config('swift_mailer_sendmail_path')) return FALSE;

        $this->transport = Swift_SendmailTransport::newInstance($this->get_config('swift_mailer_sendmail_path') . ($this->get_config('swift_mailer_sendmail_parameters') ? ' ' . $this->get_config('swift_mailer_sendmail_parameters') : ''));

        return FALSE;
    }

    private function create_mail_transport()
    {
        $this->transport = Swift_MailTransport::newInstance();

        return FALSE;
    }

    private function initialize_abstraction_mode()
    {
        switch($this->transport_type)
        {
            case 'smtp':
                $this->create_smtp_transport();
                break;
            case 'sendmail':
                $this->create_sendmail_transport();
                break;
            case 'mail':
                $this->create_mail_transport();
                break;
        }
    }

    public function create_message($message_identifier = FALSE, $message_subject = FALSE, $message_body = FALSE, $message_from = FALSE, $message_to = FALSE, $content_type = 'text/html')
    {
        if(!$this->initialized) return false;
        if(!$message_identifier) $message_identifier = 'default';

        // initialize message
        $this->messages[$message_identifier] = Swift_Message::newInstance();

        // set subject / body
        if($message_subject) $this->messages[$message_identifier]->setSubject($message_subject);
        if($message_body) $this->messages[$message_identifier]->setBody($message_body);

        // set from addresses - array('email@address.com' => 'sender from name')
        if($message_from)
        {
            $this->messages[$message_identifier]->setFrom($message_from);
        }

        // set recipient addresses - array('email@address.com' => 'recipient name')
        if($message_to)
        {
            $this->messages[$message_identifier]->setTo($message_to);
        }

        if($content_type)
        {
            $this->messages[$message_identifier]->setContentType($content_type);
        }

        return TRUE;
    }

    public function add_attachment($message_identifier = FALSE, $filepath = FALSE)
    {
        if(!$this->initialized) return false;
        if(!$message_identifier) $message_identifier = 'default';
        if(!$filepath) return FALSE;
        if(!$this->messages[$message_identifier]) $this->create_message($message_identifier);

        $this->messages[$message_identifier]->attach(Swift_Attachment::fromPath($filepath));
    }

    public function add_part($message_identifier = FALSE, $message_part_content = FALSE, $message_part_mime = 'text/html')
    {
        if(!$this->initialized) return false;
        if(!$message_identifier) $message_identifier = 'default';
        if(!$this->messages[$message_identifier]) $this->create_message($message_identifier);

        if($message_part_content) $this->messages[$message_identifier]->addPart($message_part_content, $message_part_mime);
    }

    public function send_message($message_identifier = FALSE)
    {
        if(!$this->initialized) return false;
        if(!$message_identifier) $message_identifier = 'default';
        if(!$this->messages[$message_identifier]) return FALSE;
        if(!$this->transport) return FALSE;

        // set up mailer
        if(!$this->mailer) $this->mailer = Swift_Mailer::newInstance($this->transport);

        // send the message
        return $this->mailer->send($this->messages[$message_identifier]);
    }

    public function send_all_messages()
    {
        if(!$this->initialized) return false;
        if(count($this->messages) == 0) return FALSE;
        if(!$this->transport) return FALSE;

        // set up mailer
        if(!$this->mailer) $this->mailer = Swift_Mailer::newInstance($this->transport);

        // loop through messages
        $results = array();
        foreach($this->messages as $message_identifier => $message)
        {
            // send
            $results[$message_identifier] = $this->mailer->send($message);
        }

        return $results;
    }
}