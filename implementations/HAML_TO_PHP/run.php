<?php

//	call template engine function by $cache_dir_template & $template_engine_identifier

function TEMPLATE_ENGINE_HAML_TO_PHP_info(){
http://titanpad.com/moZN7Lm1Nr
  return array(
    "template_engine_name" =>  'HAML-TO-PHP',
    "template_engine_version" => 'WEB '.date('Y-m-d'),
    "template_engine_settings" => "",
    "template_engine_url" => "haml-to-php.com"
  );
}

function TEMPLATE_ENGINE_HAML_TO_PHP_setup($tmp_dir)
{
	define('HAML_SERVICE_URL','https://haml-to-php.com/en_EN/convert.html');
	define('HAML_SERVICE_AUTH','t,;lugMGQF');
	define('HAML_SERVICE_CAINFO', dirname(__FILE__).'/haml-to-php-com.crt');
	require_once dirname(__FILE__).'/Haml.php';

	global $haml;
	$haml = new HamlFileCache(dirname(__FILE__).'/haml', $tmp_dir);
	$haml->options['ugly'] = false;
	@ini_set('xdebug.max_nesting_level','600');
	function haml($a, $ar){ global $haml; return $haml->haml($a, $ar); }
}

function TEMPLATE_ENGINE_HAML_TO_PHP_run($case, $data)
{
	switch ($case)
	{
	// TEMPLATE CASE 1
		case 'case_1_rows':
			return haml('CASE_1.haml', array('data' => $data));
			
		break;



	// TEMPLATE CASE 2
		case 'case_2_page':
			return haml('CASE_2.haml', $data);
			
		break;
		default:
			throw new Exception("unexpected");
	}
}
