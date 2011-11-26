<?php
// incomplete

require_once 'lib/config.php' ;
require_once 'lib/lib.php';

function my_fun()
{
  sleep(1);
  return 'a';
}

for ($i = 1; $i < 5; $i++) {

	echo "run: $i\n";
	list($time, $r) = time_and_result_of_this_test('my_fun', array(), $i, $i);
	echo "$time\n";

	assert($time >= 1);
	assert($time <= 1.5);

	assert($r == 'a');
}

