<?php
if (!(PHP_SAPI === 'cli')){
    echo "Sorry, you should run this from CLI\n";
	die;
}
require_once("config.php");
require_once('mySQL.php');
$newConnect = new MySQL();
$dbh = $newConnect->connect();
$dbname = DB_DATABASE;
$sqlItemsTable = "CREATE TABLE Items
        (
        id INTEGER(10) UNSIGNED AUTO_INCREMENT,
        keyword VARCHAR(100),
        data TEXT,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8";
$sth = $dbh->prepare("SHOW TABLES FROM {$dbname} LIKE 'Items';");
$sth->execute();
if ( !$sth->rowCount()){
	$dbh->query($sqlItemsTable);
	$dbh = null;
	echo "Installation finished.";
}
else {
	echo "Table for items already exists.";
}
?>