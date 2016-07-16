<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Email extends CI_Email {

    // couldn't think of a better synonym for "send," so I used "dispatch"
    public function dispatch($params = array(), $config = array()) {
        // get SMTP config from database
        $ci = & get_instance();
        $ci->load->library('db_options');
        $smtp = $ci->db_options->get('smtp');

        // check if there's an internet connection
        // good for local testing
         if (!@fsockopen($smtp->host, $smtp->port)) {
            return false;
        }

        $config_defaults['useragent'] = 'CodeIgniter';
        $config_defaults['protocol'] = $smtp->authentication == 1 ? 'smtp' : 'sendmail';
        $config_defaults['mailpath'] = '/usr/sbin/sendmail';
        $config_defaults['smtp_host'] = $smtp->host;
        $config_defaults['smtp_user'] = $smtp->username;
        $config_defaults['smtp_pass'] = $smtp->password;
        $config_defaults['smtp_port'] = $smtp->port;
        $config_defaults['smtp_timeout'] = $smtp->timeout;
        $config_defaults['wordwrap'] = TRUE;
        $config_defaults['wrapchars'] = 76;
        $config_defaults['mailtype'] = 'html';
        $config_defaults['charset'] = 'utf-8';
        $config_defaults['validate'] = FALSE;
        $config_defaults['priority'] = 3;
        $config_defaults['crlf'] = "\r\n";
        $config_defaults['newline'] = "\r\n";
        $config_defaults['bcc_batch_mode'] = FALSE;
        $config_defaults['bcc_batch_size'] = 200;

        // use default value when necessary
        foreach ($config_defaults as $k => $v) {
            if (!array_key_exists($k, $config)) {
                $config[$k] = $v;
            }
        }

        //initialize the email class
        $this->initialize($config_defaults);

        // format the subject prefix
        $sp = ($sp = $smtp->subject_prefix) ? $sp . ': ' : '';

        // set default params
        $defaults = array(
            'from' => array($smtp->from_email, $smtp->from_name),
            'to' => array('caparas.jp@gmail.com', 'Joseph Paul Caparas'),
            'cc' => array(),
            'bcc' => array(),
            'subject' => sprintf('%1$s%2$s', $sp, 'Notification'),
            'message' => 'Empty message. Please contact the administrator.'
        );

        if ($smtp->reply_to_name && $smtp->reply_to_email) {
            $defaults['reply_to'] = array($smtp->reply_to_email, $smtp->reply_to_name);
        }

        // merge the params and defaults (without overriding)

        $params = $params + $defaults;

        // set the params
        foreach ($params as $k => $v) {
            // if the key also exists in the defaults, it means its valid
            // and can be used in the callback functions later
            switch ($k) {
                case 'message':
                    // automatically add line breaks to the message
                    $params[$k] = auto_typography($v);
                    break;
                case 'subject':
                    $params[$k] = sprintf('%1$s%2$s', $sp, $v);
                    break;
                default;
                    $params[$k] = $v;
            }

            // format the params
            if (is_array($params[$k])) {
                if (!empty($params[$k])) {
                    call_user_func_array(array($this, $k), $params[$k]);
                }
            } else {
                call_user_func(array($this, $k), $params[$k]);
            }
        }

        return $this->send();
    }

}