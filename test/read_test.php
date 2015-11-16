<?php

define('DBHOST', 'dbproxy');
define('DBNAME', 'appdb');
define('USERNAME', 'appuser');
define('PASSWORD', 'apppassword');

$readSQL = <<< SQL
	SELECT counter FROM rw_test where id=1;
SQL;

try {
	$connString = 'mysql:host='.DBHOST.';dbname='.DBNAME;
	$db = new PDO(
		'mysql:host='.DBHOST.';dbname='.DBNAME,
		USERNAME,
		PASSWORD
	);


	$numberOfFailures = 0;
	for($i=1; $i<=1000000; $i++) {


		$stmt = $db->query($readSQL);
		$readResult = $stmt->fetch(PDO::FETCH_ASSOC);
		$received = $readResult['counter'];
		printf('i: %s, received: %s'.PHP_EOL, $i, $received);
	}
} catch(PDOException $ex) {
    printf('EXCEPTION: %s'.PHP_EOL, $ex->getMessage());
}


