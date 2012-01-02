<?php

define('THIS',dirname(__FILE__));

function TEMPLATE_ENGINE_haanga_info(){
  return array(
    "template_engine_name" =>  'haanga',
    "template_engine_version" => system('cd Haanga; git rev-list -1 HEAD'),
    "template_engine_settings" => "",
    "template_engine_url" => "https://github.com/crodas/Haanga"
  );
}

# this is part of the benchmark
function TEMPLATE_ENGINE_haanga_setup($tmp_dir){
	// based on example
	require_once THIS."/Haanga/lib/Haanga.php";
	require_once THIS."/Haanga/lib/Haanga/Exception.php";


	global $config;

	$config = array(
		'cache_dir' => "$tmp_dir/hangaa/",
		'template_dir' => THIS.'/templates/'
	);
	if (!is_dir($config['cache_dir']))
		mkdir($config['cache_dir'], 0777, true);

	// if (is_callable('xcache_isset')) {
	// 	/* don't check for changes in the template for the next 5 min */
	// 	$config['check_ttl'] = 300;
	// 	$config['check_get'] = 'xcache_get';
	// 	$config['check_set'] = 'xcache_set';
	// }

	Haanga::Configure($config);
}

// this func is used for the benchmark:
// data for case 1 (dump 100 rows), see CASE 1 above
// data for case 2 rendered html result of case 1 thus the result is a complete HTML page.
function TEMPLATE_ENGINE_haanga_run($case, $data){

  ob_start();
  // PHP reference implementation
  switch ($case) {

    // TEMPLATE CASE 2
    case 'case_1_rows':

		Haanga::load('case_1_rows.html', array('data' => $data));
      break;

    // TEMPLATE CASE 1
    case 'case_2_page':

		Haanga::load('case_2_page.html', array('data' => $data));
      break;

    default:
	  throw new Exception("unexpected ".$case);
  }

  return ob_get_clean();
}
