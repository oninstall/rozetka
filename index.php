<?php

if ( ! PHP_SAPI === 'cli'){
    echo "Sorry, you should run this from CLI\n";
	die;
}
if ( ! isset($argv[1]) ||
        ! $keyword = htmlspecialchars(trim($argv[1]))){
    //exit("Usage: {$argv[0]} keyword\n");
	$keyword = "фотоаппарат";
}
require_once('config.php');
require_once('rozetka.php');
require_once('request.php');
require_once('mySQL.php');
require_once('model.php');

$newsearch = new Rozetka();
$result = $newsearch->parse($keyword);
?>