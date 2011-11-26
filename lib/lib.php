<?php

function d($ar, $key, $default){ return isset($ar[$key]) ? $ar[$key] : $default; }

// yes, I am paranoid :-), put this into an include file cause its used by both php scripts below
// $n_runs: How often should running the iterations be repeated.
// $n_iterations: how often to run the benchmark
function time_and_result_of_this_test($do_work, $do_work_args, $n_runs, $n_iterations){
  $min_time = 999999;

  # yes, this probably loads the compiled template .. this is too hard to benchmark :(
  $expected_result = call_user_func_array($do_work, $do_work_args);

  $results = array();
  for ($i = 0; $i < $n_iterations; $i++) {
    // fill array - we don't want to benchmark this
    $results[] = null;
  }

  // we run the same iterations many times to give the code a chance to pick
  // the run where the OS did least work.
  for ($j = 0; $j < $n_runs; $j++) {

    // run the test $n_iterations times
    $start = microtime(true);
    for ($i = 0; $i < $n_iterations; $i++) {
      $results[$i] = call_user_func_array($do_work, $do_work_args);
    }
    $end = microtime(true);
    $time = ($end - $start) / $n_iterations;

    // verify results:
    foreach($results as $r){
      if ($r !== $expected_result)
        throw new Exception("failure");
    }

    if ($time < $min_time)
      $min_time = $time;
  }

  return array($min_time, $expected_result);
}
