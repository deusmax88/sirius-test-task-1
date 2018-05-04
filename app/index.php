<?php
require_once('init.php');

$numOfConnectionRetries = 5;

retryToConnectToDB:

try {
    $dbh = new PDO("mysql:dbname=testdb;host=db", "root", "testPassword");
}
catch (\PDOException $e) {
    sleep(10);
    if ($numOfConnectionRetries--) {
        goto retryToConnectToDB;
    }
}

$init = new \Sirius\TestTask\One\init($dbh);

echo count($init->get());