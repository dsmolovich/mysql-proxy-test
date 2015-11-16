<?php

define('DBHOST', 'dbproxy');
define('DBNAME', 'appdb');
define('USERNAME', 'appuser');
define('PASSWORD', 'apppassword');

$resetSQL = <<<SQL
	UPDATE rw_test SET counter=0 WHERE id=1;
SQL;

$writeSQL = <<<SQL
	UPDATE rw_test SET counter=counter+1 WHERE id=1;
SQL;

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
	$resetResult = $db->exec($resetSQL);


	$numberOfFailures = 0;
	for($i=1; $i<=1000000; $i++) {
		$writeResult = $db->exec($writeSQL);

		$stmt = $db->query($readSQL);
		$readResult = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$expected = $i;
		$received = $readResult['counter'];
		$testResult = ($expected == $received ? 'pass' : 'FAIL' );

		if($expected != $received){
			$numberOfFailures ++;
			printf('received: %s, expected: %s, %s'.PHP_EOL, $received, $expected, $testResult);
		}
		
	}
	print str_repeat('=', 10).PHP_EOL;
	printf('Total: %s failures out of %s due to a replication latency'.PHP_EOL, $numberOfFailures, $i);


} catch(PDOException $ex) {
    printf('EXCEPTION: %s'.PHP_EOL, $ex->getMessage());
}


