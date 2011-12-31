<?php

define('CATCH_ERRORS','');
// define('DEBUG','');

define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASSWORD','mysqlpage');
define('DB_NAME','php-template-engines');


$errorfile = '/var/marc/vimserver/error';
// if (file_exists($errorfile)) unlink($errorfile);

function custom_failure_handler($message, $trace){
  global $errorfile;

  $s = '';
  foreach( $trace as $k){
        $s.="\n".(isset($k['file']) ? $k['file'] : 'nofile') 
          .':'.(isset($k['line']) ? $k['line'] : 'no line');
  }

  file_put_contents($errorfile, str_replace('<br>',"\n",$message) . $s ,FILE_APPEND);
  // assign 777 because I sometimes use PHP in console..
  chmod($errorfile, 0777);
}

function cpu_info(){
	return file_get_contents('/proc/cpuinfo');
}
