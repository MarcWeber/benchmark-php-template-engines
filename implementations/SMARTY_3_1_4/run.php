<?php

//	call template engine function by $cache_dir_template & $template_engine_identifier

function TEMPLATE_ENGINE_SMARTY_3_1_4_info(){
  return array(
    "template_engine_name" =>  'smarty',
    "template_engine_version" => '3.1.4',
    "template_engine_settings" => "debugging = false, caching = true, force_compile = false, compile_check=false, for each run using different cache_id"
  );
}

function TEMPLATE_ENGINE_SMARTY_3_1_4_setup($tmp_dir)
{

	define('THIS_DIR',dirname(__FILE__));
	define('TEMPLATE_DIR', THIS_DIR.'/tpl');
	define('CACHE_DIR',$tmp_dir);
	
	require_once(dirname(__FILE__).'/Smarty-3.1.4/libs/Smarty.class.php');

	// strace showed that smarty is using the following php files
	foreach (array(
		"libs/sysplugins/smarty_cacheresource.php",
		"libs/sysplugins/smarty_internal_cacheresource_file.php",
		"libs/sysplugins/smarty_internal_data.php",
		"libs/sysplugins/smarty_internal_resource_file.php",
		"libs/sysplugins/smarty_internal_templatebase.php",
		"libs/sysplugins/smarty_internal_template.php",
		"libs/sysplugins/smarty_internal_write_file.php",
		"libs/sysplugins/smarty_resource.php"
	) as $force_load) {
		require_once THIS_DIR.'/Smarty-3.1.4/'.$force_load;
	}
}

// we don't want to benchmark testing, but how fast it is to generate HTML
// using different contents from database or other sources
// By using incrementing cache_id s smarty is forced to recreate the HTML each 
// time. Eventually this is unfair ..
$global_counter = 0;

function TEMPLATE_ENGINE_SMARTY_3_1_4_run($case, $data)
{
	global $global_counter;

	// smarty does not like our error handling
	restore_error_handler();

	switch ($case)
	{
	// TEMPLATE CASE 1
		case 'case_1_rows':
			
			$smarty = new Smarty;
			
			$smarty->force_compile = false;
			$smarty->debugging = false;
			$smarty->caching = true;
			$smarty->cache_lifetime = 120;
			$smarty->compile_check = false;
			$smarty->cache_dir = CACHE_DIR;
			
			$smarty->assign("data", $data);
			
			$result = $smarty->fetch(TEMPLATE_DIR.'/CASE_1.tpl', $global_counter++);
		break;



	// TEMPLATE CASE 2
		case 'case_2_page':
			
			$smarty = new Smarty();
			
			$smarty->force_compile = false;
			$smarty->debugging = false;
			$smarty->caching = true;
			$smarty->cache_lifetime = 120;
			$smarty->compile_check = false;
			$smarty->cache_dir = CACHE_DIR;
			
			// maybe there is a faster way
			foreach ($data as $key => $value) {
				$smarty->assign($key, $value);
			}
			
			$result = $smarty->fetch(TEMPLATE_DIR.'/CASE_2.tpl', $global_counter++);
			
		break;
		
		default:
			throw new Exception("unexpected ".$case);
	}

	set_error_handler("ERROR_HANDLER");
			
	return $result;
			
}
