<?php

require_once APPPATH.'includes/vendor/autoload.php';

/**
 * メール送信ライブラリ
 *
 * @see     http://swiftmailer.org/docs/introduction.html
 * @author  <yuki.hatano@gmail.com>
 */
class MY_mailer {

    // Swiftmailer OBJECT
    private $_mailer        = NULL;
    private $_message       = NULL;
    private $_headers       = NULL;

    // Server properties
    private $smtp_host      = "localhost";  // SMTP Server.  Example: mail.earthlink.net
    private $smtp_user      = "";           // SMTP Username
    private $smtp_pass      = "";           // SMTP Password
    private $smtp_port      = "25";         // SMTP Port
    private $smtp_timeout   = 5;

    // Message properties
    private $from       = '';
    private $to         = '';
    private $subject    = '';
    private $reply_to   = '';
    private $return_path   = '';
    private $cc         = '';
    private $bcc        = '';

    /**
     * Constructor
     *
     * @param   mixed properties
     *          - smtp_host
     *          - smtp_user
     *          - smtp_pass
     *          - smtp_port
     *          - smtp_timeout
     */
    public function __construct($config=array())
    {
        log_debug('MY_Mailer Start ----------------------------------');
        $this->initialize($config);

        $transport = Swift_SmtpTransport::newInstance($this->smtp_host, $this->smtp_port);

        if ($this->smtp_user != '' AND $this->smtp_pass != '')
        {
            $transport->setUsername($this->smtp_user)->setPassword($this->smtp_pass);
        }

        $this->_mailer  = Swift_Mailer::newInstance($transport);

        log_debug("MY_Mailer Initialized.");
    }

    // --------------------------------------------------------------------

    /**
     * Initialize preferences
     *
     * @access  public
     * @param   array
     * @return  void
     */
    public function initialize($config)
    {
        log_debug("MY_Mailer/initialize() run");

        $this->_clear();

        foreach ($config as $key => $val)
        {
            if (isset($this->$key))
            {
                $method = 'set_'.$key;

                if (method_exists($this, $method))
                {
                    $this->$method($val);
                }
                else
                {
                    $this->$key = $val;
                }
            }
        }

        return $this;
    }


    // --------------------------------------------------------------------

    /**
     * Set SMTP Host
     *
     * @access  public
     * @param   string
     * @return  void
     */
    public function set_smtp_host($smtp_host=NULL)
    {
        log_debug("MY_Mailer/set_smtp_host({$smtp_host}) run.");

        if (filter_var($smtp_host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
        {
            $this->smtp_host = $smtp_host;
        }
        else
        {
            $this->smtp_host = 'localhost';
        }

        return $this;
    }

    /**
     * Set SMTP Port
     *
     * @access  public
     * @param   numeric
     * @return  void
     */
    public function set_smtp_port($smtp_port=NULL)
    {
        log_debug("MY_Mailer/set_smtp_port({$smtp_port}) run.");

        if (filter_var($smtp_port, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1, 'max_range'=>65535))))
        {
            $this->smtp_port = $smtp_port;
        }
        else
        {
            $this->smtp_port = '25';
        }

        return $this;
    }

    /**
     * Set SMTP Timeout
     *
     * @access  public
     * @param   int
     * @return  void
     */
    public function set_smtp_timeout($smtp_timeout=NULL)
    {
        log_debug("MY_Mailer/set_smtp_timeout({$smtp_timeout}) run.");

        if (filter_var($smtp_timeout, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1, 'max_range'=>60))))
        {
            $this->smtp_timeout = $smtp_timeout;
        }
        else
        {
            $this->smtp_timeout = 5;
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set Subject
     *
     * @param   strings
     * @return  void
     */
    public function set_subject($subject)
    {
        log_debug("MY_Mailer/set_subject({$subject}) run.");

        $this->_message->setSubject($subject);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set From
     *
     * @param   mixed   'mail@address.tld' or array('mail@address.tld' => 'The Name')
     * @return  void
     */
    public function set_from($from)
    {
        log_debug("MY_Mailer/set_from() run.");
        //log_debug($from);

        $this->_message->setFrom($from);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set Reply-to
     *
     * @param   mixed   'mail@address.tld' or array('mail@address.tld' => 'The Name')
     * @return  void
     */
    public function set_reply_to($reply_to)
    {
        log_debug("MY_Mailer/set_reply_to() run.");

        $this->_message->setReplyTo($reply_to);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set Retrun-Path
     *
     * @param   mixed   'mail@address.tld'
     * @return  void
     */
    public function set_return_path($return_path)
    {
        log_debug("MY_Mailer/set_return_path() run.");

        $this->_message->setReturnPath($return_path);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set To
     *
     * @param   mixed   'mail@address.tld' or array('mail1@address.tld', 'mail2@address.tld') or array('mail@address.tld' => 'The Name'[,...])
     * @return  void
     */
    public function set_to($to)
    {
        log_debug("MY_Mailer/set_to() run.");

        $this->_message->setTo($to);

        return $this;
    }

    public function add_to($to, $name='')
    {
        log_debug("MY_Mailer/add_to() run.");

        $this->_message->addTo($to, $name);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set CC
     *
     * @param   mixed   'mail@address.tld' or array('mail1@address.tld', 'mail2@address.tld') or array('mail@address.tld' => 'The Name'[,...])
     * @return  void
     */
    public function set_cc($cc)
    {
        log_debug("MY_Mailer/set_cc() run.");

        $this->_message->setCc($cc);

        return $this;
    }

    public function add_cc($cc)
    {
        log_debug("MY_Mailer/add_cc() run.");

        $this->_message->addCc($cc);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set BCC
     *
     * @param   mixed   'mail@address.tld' or array('mail1@address.tld', 'mail2@address.tld') or array('mail@address.tld' => 'The Name'[,...])
     * @return  void
     */
    public function set_bcc($bcc)
    {
        log_debug("MY_Mailer/set_bcc() run.");

        $this->_message->setBcc($bcc);

        return $this;
    }

    public function add_bcc($bcc)
    {
        log_debug("MY_Mailer/add_bcc() run.");

        $this->_message->addBcc($bcc);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set Body
     *
     * @param   strings 'mail body'
     * @param   strings mime-type ('text' | 'html')
     * @return  void
     */
    public function set_body($body, $mime='text')
    {
        log_debug("MY_Mailer/set_body({$mime}) run.");

        $mime_type = 'text/plain';

        if ($mime == 'html')
        {
            $mime_type = 'text/html';
        }

        //log_debug($body);
        $this->_message->setBody($body, $mime_type);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set body by smarty
     *
     * @param   strings $tpl_file
     * @param   strings mime-type ('text' | 'html')
     * @return  void
     */
    public function set_body_by_smarty($tpl_file, $assigns, $mime='text')
    {
        log_debug("MY_Mailer/set_body_by_smarty({$tpl_file}, assigns, {$mime}) run.");

        $CI = get_instance();
        $CI->load->library('smarty', NULL, 'mail_view');
        $CI->mail_view->template_dir  = APPPATH . 'views/mail';
        $CI->mail_view->compile_dir   = rtrim(PROJECTPATH.'/tmp/templates_c/mail/');
        $CI->mail_view->addPluginsDir(realpath(APPPATH) . '/third_party/smarty_plugins/');

        if ( ! empty($assigns) && is_array($assigns))
        foreach ($assigns as $key => $val)
        {
            $CI->mail_view->assign($key, $val);
        }

        $this->set_body($CI->mail_view->fetch($tpl_file), $mime);
    }

    // --------------------------------------------------------------------

    /**
     * Add Header
     *
     * @param   string 'Header-Name'
     * @param   string 'Header-Value'
     */
    public function add_header($header_name, $header_value)
    {
        log_debug("add_header({$header_name}, {$header_value}) run.");
        $this->_headers->addTextHeader($header_name, $header_value);
    }

    // --------------------------------------------------------------------

    /**
     * Add Inline image
     *      the `cid` put insert in <img src="{$cid}"> HTML tag.
     *
     * @param   string '/file/path/image'
     * @return  string cid
     */
    public function inline_img($img_path)
    {
        log_debug("MY_Mailer/inline_img({$img_path}) run.");

        $cid = '';

        if (is_file($img_path) && exif_imagetype($img_path))
        {
            $cid = $this->_message->embed(Swift_Image::fromPath($img_path));
        }

        return $cid;
    }

    // --------------------------------------------------------------------

    /**
     * Add Attachment
     */
    public function attach($file_path, $file_name=NULL)
    {
        log_debug("MY_Mailer/attach({$file_path}) run.");

        if (is_file($file_path))
        {

            if (is_null($file_name))
            {
                $file_name = basename($file_path);
            }

            $this->_message->attach(Swift_Attachment::fromPath($file_path)->setFilename($file_name));

        }

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Send the message
     */
    public function send(&$failures=array())
    {
        log_debug('MY_Mailer/send() run.');

        $num = FALSE;

        if (is_object($this->_message))
        {

            log_debug('sending mail [' . $this->get_message_id() . ']');

            try {

                if (is_null($failures))
                {
                    $num = $this->_mailer->send($this->_message);
                }
                else {
                    $num = $this->_mailer->send($this->_message, $failures);
                }

            }
            catch(Exception $e)
            {
                foreach ($this->_message->getTo as $k => $v)
                {
                    if (is_int($k))
                    {
                        $failures[] = $v;
                    }
                    else {
                        $failures[] = $k;
                    }
                }
                log_error($e->getMessage());
            }

        }

        log_debug('Try sending to: =============');
        log_debug(var_export($this->_message->getTo(), TRUE));

        if (count($failures) > 0)
        {
            log_error('Failures: =============');
            log_error(var_export($failures, TRUE));
        }

        log_debug('MY_Mailer End ------------------------------------');

        return $num;

    }

    // --------------------------------------------------------------------

    /**
     * Get Message-ID
     *      送信前に取得すべし
     */
    public function get_message_id()
    {
        if (is_object($this->_message))
        {
            return $this->_message->getId();
        }

        return NULL;
    }

    // --------------------------------------------------------------------

    /**
     * Set In-Reply-To
     */
    public function set_in_reply_to()
    {
        $this->_message->getId();
    }

    // --------------------------------------------------------------------

    /**
     * Initialize the Email Data
     *
     * @access  private
     * @return  void
     */
    private function _clear()
    {
        log_debug("MY_Mailer/_clear() run");

        $this->_message = Swift_Message::newInstance();
        $this->_headers = $this->_message->getHeaders();
        return $this;
    }

}

