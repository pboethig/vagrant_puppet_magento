#!/usr/bin/env php
<?php
//get filepath from cmd , check and parse it

require_once(__DIR__ ."/../". DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

//use IniParser\CommandLineInputResolver;

$CommandLineInputResolver = new \CommandLineInputResolver($argv);

echo $CommandLineInputResolver->getItem(new \IniParser($CommandLineInputResolver->getFilePath()));

exit;


