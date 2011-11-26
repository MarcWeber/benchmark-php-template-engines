<?php

//	call template engine function by $cache_dir_template & $template_engine_identifier

function TEMPLATE_ENGINE_SMARTY_3_1_4_info(){
  return array(
    "template_engine_name" =>  'smarty',
    "template_engine_version" => '3.1.4',
    "template_engine_settings" => "debugging = false, caching = true, force_compile = false"
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

function TEMPLATE_ENGINE_SMARTY_3_1_4_run($case, $data)
{

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
			$smarty->cache_dir = CACHE_DIR;
			
			$smarty->assign("daat",$data);
			
			$result = $smarty->fetch(TEMPLATE_DIR.'/CASE_1.tpl');
			
			return $result;
			
		break;



	// TEMPLATE CASE 2
		case 'case_2_page':
			
			$smarty = new Smarty();
			
			$smarty->force_compile = false;
			$smarty->debugging = false;
			$smarty->caching = true;
			$smarty->cache_lifetime = 120;
			$smarty->cache_dir = CACHE_DIR;
			
			// maybe there is a faster way
			foreach ($data as $key => $value) {
				$smarty->assign($key, $value);
			}
			
			$result = $smarty->fetch(TEMPLATE_DIR.'/CASE_2.tpl');
			
			return $result;
			
		break;
		
		default:
			throw new Exception("unexpected ".$case);
	}

	set_error_handler("exception_error_handler");
}
