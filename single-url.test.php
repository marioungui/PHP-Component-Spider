<?php

/*
THIS IS A TESTING ONLY SCRIPT, IT ONLY DOES CRAWL THE HOME PAGE AND THE URL TO BE TESTED
FOR TESTING A SINGLE URL, JUST REPLACE THE $url VARIABLE AT LINE 81
*/

require "helper.php";
require "vendor/autoload.php";

//CLI helper

$arg = getopt("c:");
if(!isset($arg["c"])) {
	colorLog("There is missing some variables, make shure that you are launching this script with the required parameters", "e");
	exit();
}

switch (strtolower($arg["c"])) {
	case 'mvp':
		$component = "MVP Block";
		$filter = "//*[@class='mvp-block']";
		break;
	case 'search':
		$component = "Smart Question Search Engine Block";
		$filter = "//*[@class='sqe-block']";
		break;
	default:
		$component = "MVP Block";
		$filter = "//*[@class='mvp-block']";
		break;
}


$web = new \Spekulatius\PHPScraper\PHPScraper;

$countok = 0;
$countfail = 0;
$countdup = 0;
$url = "https://www.nestlebabyandme.cl/dup-test";
$web->go($url);
try {
    $dup = count($web->filter($filter));
    if ($dup > 1) {
        echo "{$url} ";
        colorLog("DUPLICATED", "w");
        $countdup++;
    }
    else {
        echo "{$url} ";
        colorLog("OK", "s");
        $countok++;
    }
}
catch (Exception $e) {
    colorLog("{$component} not found in: {$url}", "e");
    $countfail++;
}
echo "Total of pages with $component found: $countok".PHP_EOL;
echo "Total of pages with $component not found: $countfail".PHP_EOL;
echo "Total of pages with $component duplicated: $countdup";