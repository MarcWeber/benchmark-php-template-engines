<?php

list($name, $n_runs, $n_iterations) = $argv;

require_once 'lib/config.php' ;
require_once 'lib/lib.php';
if (defined('CATCH_ERRORS_RUN')) require_once 'lib/error-handling.php';

try {

// benchmarks an implementation
function do_bench($impl_name_version, $mode){
  assert(in_array($mode, array('dry-run','live')));
  $impl_dir = dirname(__FILE__)."/implementations/".$impl_name_version;
  assert(is_dir($impl_dir));
  assert(is_file($impl_dir.'/run.php'));

  echo "benchmarking $impl_name_version $mode\n";
  global $n_runs, $n_iterations;

  $cache_dir = dirname(__FILE__).'/tmp/'.$impl_name_version;

  # clean:  ? not necessary. Engines should invalidate caches themselves - or 
  # the setup function should do so
  # rm -fr $cache_dir ; mkdir -p $cache_dir 

  if (!is_dir($cache_dir))
	  mkdir($cache_dir, 0777, true);

  # for n_runs and n_iterations start new php process
  # "php run_benchmark_phase_for.php $mode NOTHING $cache_dir"
  list($time_php_startup, $dummy_no_result) 
    = time_and_result_of_this_test('system', array("php run_benchmark_phase_for.php $mode NOTHING $impl_name_version $cache_dir"), $n_runs, $n_iterations);

  # for n_runs and n_iterations start new 
  # "php run_benchmark_phase_for.php $mode SETUP $cache_dir
  list($time_php_startup_and_template_setup, $dummy_no_result) 
    = time_and_result_of_this_test( 'system', array("php run_benchmark_phase_for.php $mode SETUP $impl_name_version $cache_dir"), $n_runs, $n_iterations);

  $time_setup = $time_php_startup_and_template_setup - $time_php_startup;

  # start new php process eventually writing results to database:
  system( "php run_benchmark_phase_for.php $mode CASE_1_AND_2 $impl_name_version $cache_dir $n_runs $n_iterations $time_setup ");
}

foreach(glob('implementations/*') as $implementation){
  $impl_name_version = basename($implementation); # the TEMPLATE_ENGINE_NAME place holder
   // load everything into OS cache:
  do_bench($impl_name_version, 'dry-run');
  # write to database:
  do_bench($impl_name_version, 'live');
}

} catch (Exception $e) {
  uncaught_exception($e); // only defined if CATCH_ERRORS
}
