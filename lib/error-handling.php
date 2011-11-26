<?php

  function handle_unexpected_failure($message, $trace){
    global $db;
    $time = date(DATE_ATOM);
    $error_id = time();
    $message = "$time $message";
    $trace = Trace::traceToText($trace);
    $msg_and_trace = $error_id."\n"."\n$message\n$trace\n".var_export($_POST,true);
	echo $msg_and_trace;
  }


// formatieren von Exceptions
// license: do the fuck you want with it

class Trace
{
	static public function exceptionToText($e){
		// Klassenname
		$c = new ReflectionClass($e);
		return "Exception of type ".$c->getName().", message: \n".$e->getMessage()."\n";
	}

	static public function traceToText($trace){
		$s=''; $n="";
		foreach( $trace as $k){
		  $s.="\n".(isset($k['file']) ? $k['file'] : 'nofile') 
				   .':'.(isset($k['line']) ? $k['line'] : 'no line');
		}
		return $s;
	}

	static public function exFullTrace($e){
		return array_merge(
			 array (array('file' => $e->getFile(), 'line' => $e->getLine() ) )
			, $e->getTrace());
	}
}


/* global error / exception handling
 *
 * The set_error_handler is used to throw an Exception for everything which 
 * could look like a failure. Fatal errors such as uncaught Exceptions or
 * call to undefined functions are then noticed by the SHUTDOWN_FUNCTION 
 * funcction.
 *
 * The hook function custom_failure_handler is called unless undefined.
 * An example (writing the trace to a file readable by Vim) could look like this:
 *
 * Optionally surround everything by a global try catch calling 
 * uncaught_exception($ex) so that custom_failure_handler has a nice trace 
 * object
 *
	function custom_failure_handler($message, $trace){
	  global $errorfile;

	  $s = '';
	  foreach( $trace as $k){
		$s.="\n".(isset($k['file']) ? $k['file'] : 'nofile') 
		  .':'.(isset($k['line']) ? $k['line'] : 'no line');
	  }

	  file_put_contents($errorfile, str_replace('<br>',"\n",$message) . $s );
	  // assign 777 because I sometimes use PHP in console..
	  chmod($errorfile, 0777);
	}
 *
 * license: do the fuck you want with it
 * author: Marc Weber
 */

function unexpected_failure($message, $trace){

  // for development (feed traces to editor or ...)
  if (function_exists('custom_failure_handler')){
     custom_failure_handler($message, $trace);
  }

  // should be defined in your setup file
  handle_unexpected_failure($message, $trace);

}

{ 
  # make everything unexpected be an error and cause a trace by throwing an 
  # exception which is catched by either the exception implementation or by the 
  # shutdown handler finally
  function ERROR_HANDLER($error_type, $error_msg, $error_file, $error_line, $error_context){
	throw new Exception($error_type.' '.$error_msg);
  }
  set_error_handler('ERROR_HANDLER');
}

{ # implementation fatal errors.

  function SHUTDOWN_FUNCTION() {
    // if you don't wrap this you'll get "Exception with no stack frame .. good 
    // luck then!"
    try {
      if (defined('DEBUG_MYSQL_QUERIES')){
        global $mysql_queries, $db_total;
        echo "total mysql time ".(isset($db_total) ? $db_total : '')."\n";
        $count = 0;
        if (is_array($mysql_queries))
          foreach ($mysql_queries as $a) {
            if ($count ++ > 500){
              echo "skipping more sql queries\n";
              break;
            }
            echo "\n====================\n".$a['sql']."\n";
            echo "called in\n";
            foreach ($a['trace'] as $t) {
              echo g($t,'file','').':'.g($t,'line','')."\n";
            }
          }
      }

      $error = error_get_last(); 
      if (!$error) return; // no error
        /* 
           $error looks like this:
           array (
                'type' => 1,
                'message' => 'Call to undefined function foo()',
                'file' => 'test.php',
                'line' => 19
                )
         */
      try {
        unexpected_failure($error['type'].' '.$error['message'], array($error));
      } catch (Exception $e) {
        # if formatting fails for whatever reason:
        unexpected_failure('? '.var_export($e,true).var_export($error, true), array());
      }

    } catch (Exception $e) {
       uncaught_exception($e);
       exit(1);
    }
  } 
  register_shutdown_function('SHUTDOWN_FUNCTION'); 

}

{ # implementation exceptions
  # undefinierte variablen fangen etc.
  # note: The shutdown handler above would be run on exceptions as well
  # however catching it means we have a stack trace

  function uncaught_exception($ex){
	unexpected_failure(Trace::exceptionToText($ex), Trace::exFullTrace($ex));
  }
}
