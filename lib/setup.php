<?php

require_once dirname(__FILE__).'/config.php';
$db = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
assert(mysqli_connect_errno() == 0);
