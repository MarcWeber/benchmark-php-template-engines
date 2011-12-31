<?php

require_once 'lib/config.php' ;
require_once 'lib/lib.php';
if (defined('CATCH_ERRORS')) require_once 'lib/error-handling.php';

try {

if (defined('DEBUG'))
	var_dump($argv);

if (!function_exists('cpu_info')){
	function cpu_info(){
		return "cpu_info stub: overwrite it in your lib/config.php file!";
	}
}

# read common args
$mode = $argv[1]; 

assert(in_array($mode, array('live','dry-run'), true));

$work = $argv[2];
assert(in_array($work, array('NOTHING','SETUP','CASE_1_AND_2'), true));

$template_engine_identifier = $argv[3];

$cache_dir_template = $argv[4];

$remaining_args = array_slice($argv, 5);

function setup_template_engine($cache_dir_template, $template_engine_identifier){
	require_once 'implementations/'.$template_engine_identifier.'/run.php';
	call_user_func('TEMPLATE_ENGINE_'.$template_engine_identifier.'_setup', $cache_dir_template);
}

switch ($work) {
  case 'NOTHING':
    exit();
    break;

  case 'SETUP':
    setup_template_engine($cache_dir_template, $template_engine_identifier);
    break;

  case 'CASE_1_AND_2':
    list($n_runs, $n_iterations, $time_setup) = $remaining_args;

    // this does not write to database, benchmarking of this phase has been done by SCRIPT 2
    setup_template_engine($cache_dir_template, $template_engine_identifier);

	{ // CASE_1
		$data_case_1 = array();
		for ($i = 0; $i < 100; $i++) {
			$a = array();
			$a['Surname'] = "user ".$i;
			$a['Lastname'] = "user ".$i;
			$a['age'] = $i;
			$data_case_1[] = $a;
		}

		// benchmark rendering templates:
		list($time_case_1, $html_rows) = time_and_result_of_this_test('TEMPLATE_ENGINE_'.$template_engine_identifier.'_run', array('case_1_rows', $data_case_1), $n_runs, $n_iterations);
	}


	{ // CASE_2

		$data_case_2 = array();
		$data_case_2['title'] = 'TITLE :'.$template_engine_identifier;
		$data_case_2['description'] = 'description';
		$data_case_2['header'] = '<h2>HEADER</h2>';
		$data_case_2['navigation'] = 'NAVIGATION';
		$data_case_2['content'] = $html_rows;

		// TODO  .. call with args to run template CASE 2 passing $html_rows, $title, $description etc. 
		list($time_case_2, $html_page) = time_and_result_of_this_test('TEMPLATE_ENGINE_'.$template_engine_identifier.'_run', array('case_2_page', $data_case_2), $n_runs, $n_iterations);

	}

	$info = call_user_func('TEMPLATE_ENGINE_'.$template_engine_identifier.'_info');

    if ($mode == 'live'){
		require 'lib/setup.php';
		$sql = '
			INSERT INTO BENCHMARK_RUNS 
			(
			  time_setup,
			  time_case_1,
			  time_case_2,

			  cpu_info, 
			  php_version, 
			  n_runs, 
			  n_iterations, 
			  template_engine_name_version, 
			  template_engine_name_info,
			  template_engine_url,

			  html
			) 
			VALUES (?,?,?,?,?,?,?,?,?,?,?)';

		$i = $db->prepare( $sql );
		// throw new Exception($db->error);

		// wants references.. assign to local vars
		$engine   = $info['template_engine_name'].' '.$info['template_engine_version'];
		$cpu_info = cpu_info();
		$php_info = phpversion();
		$template_engine_settings = $info['template_engine_settings'];
		$i->bind_param( 'sssssssssss',
			$time_setup, $time_case_1, $time_case_2, 

			$cpu_info, 
			$php_info,
			$n_runs, $n_iterations,
			$engine,
			$template_engine_settings,
			$info['template_engine_url'],

			$html_page
	    );

      $i->execute();
	  // throw new Exception($db->error);
    }

    break;
  default:
    throw new Exception("unexpected");
}

} catch (Exception $e) {
  uncaught_exception($e); // only defined if CATCH_ERRORS
}
