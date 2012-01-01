<?php

require_once '../lib/config.php' ;
require_once '../lib/setup.php' ;

{ // haml setup 

	define('HAML_SERVICE_URL','http://haml-to-php.com/en_EN/convert.html');
	define('HAML_SERVICE_AUTH','t,;lugMGQF');
	define('HAML_SERVICE_CAINFO', dirname(__FILE__).'/../implementations/HAML_TO_PHP/haml-to-php-com.crt');
	assert(file_exists(HAML_SERVICE_CAINFO));
	require_once dirname(__FILE__).'/../implementations/HAML_TO_PHP/Haml.php';

	global $haml;
	$haml = new HamlFileCache(dirname(__FILE__).'/haml', dirname(__FILE__).'/haml-cache');
	$haml->options['ugly'] = false;
	@ini_set('xdebug.max_nesting_level','600');
	function haml($a, $ar){ global $haml; return $haml->haml($a, $ar); }

}

if (isset($_GET['html_engine'])){
	$sql = 'SELECT html FROM BENCHMARK_RUNS WHERE id = '.(1* $_GET['html_engine']);
	$res = mysqli_query($db, $sql);
	$wert = mysqli_fetch_assoc($res);
	echo $wert['html'];
	flush();
	exit();
}


$sql = '
	SELECT  
	id,
	time_setup,
	time_case_1,
	time_case_2,

	cpu_info, 
	php_version, 
	n_runs, 
	n_iterations, 
	template_engine_name_version, 
	template_engine_name_info,
	template_engine_url 
	FROM BENCHMARK_RUNS
	ORDER BY template_engine_name_version
	';

$werte = array();
$res = mysqli_query($db, $sql);
while ($wert = mysqli_fetch_assoc($res)){
	$werte[] = $wert;
}

/*
$werte = array(
	array(
		'title' => 'A',
		'url' =>  'x',
		'startup' => '1',
		'run' => '2'
	),
	array(
		'title' => 'B',
		'url' =>  'x',
		'startup' => '5',
		'run' => '20'
	),
);
 */

$as = implode(',', array_map(create_function('$a','return 1000*$a["time_setup"];'),$werte));
$bs = implode(',', array_map(create_function('$a','return 1000*$a["time_case_1"];'),$werte));
$cs = implode(',', array_map(create_function('$a','return 1000*$a["time_case_2"];'),$werte));

$x_max = 18;

$graph_url =
   'http://chart.apis.google.com/chart'
   .'?chxr=0,0,'.$x_max
   .'&chxt=x,y'
   .'&chbh=a'
   .'&chs=600x400'
   .'&cht=bhs'
   .'&chco=00FF00,0000FF,FF0000'
   .'&chds=0,'.$x_max.',0,'.$x_max
   .'&chd=t:'.$as.'|'.$bs.'|'.$cs
   .'&chdl='.implode('|', array('time_setup','time_case_1','time_case_2'))
   .'&chtt=timings+of+phases+(multiple+runs)';

echo haml('main-page.haml', array(
	'title' => 'comprehensive PHP template engine benchmark:',
	'lang_meta' => 'en',
	'graph_url' => $graph_url,
	'werte' => $werte
));
