<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | Hooks
  | -------------------------------------------------------------------------
  | This file lets you define "hooks" to extend CI without hacking the core
  | files.  Please see the user guide for info:
  |
  |	http://codeigniter.com/user_guide/general/hooks.html
  |
 */

$hook['pre_system'][] = array(
    'class' => 'URLFixes',
    'function' => 'removeIndexDotPHP',
    'filename' => 'pre_system.php',
    'filepath' => 'hooks',
    'params' => array()
);

$hook['pre_system'][] = array(
    'class' => 'Housekeeping',
    'function' => 'setTimezone',
    'filename' => 'pre_system.php',
    'filepath' => 'hooks',
    'params' => array()
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */