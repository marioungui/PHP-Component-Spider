<?php

/*
THIS IS A TESTING ONLY SCRIPT, IT ONLY DOES CRAWL THE HOME PAGE AND THE URL TO BE TESTED
FOR TESTING A SINGLE URL, JUST REPLACE THE $url VARIABLE AT LINE 81
*/

$arg = getopt("c:w:");

require "helper.php";
require "vendor/autoload.php";

//CLI helper
if(!isset($arg["c"])) {
	colorLog("There is missing some variables, make shure that you are launching this script with the required parameters", "e");
	exit();
}

require "filters.php";


$web = new \Spekulatius\PHPScraper\PHPScraper;

$countok = 0;
$countfail = 0;
$countdup = 0;
$url = "https://www.nestlebabyandme.com.br/dup-test";
$web->go($url);

// Init the Crawler and process all
if ($arg["c"] == "word") {
    $web->go($url);
    try {
        $count = count($web->filter($filter));
        if ($count > 0) {
            echo "{$url} ";
            colorLog("FOUND {$count} TIMES", "s");
            $countok++;
        }
        else {
            echo "{$url} ";
            colorLog("NOT FOUND", "e");
            $countfail++;
        }
    }
    catch (Exception $e) {
        colorLog("{$component} not found in: {$url}", "e");
        $countfail++;
    }
}
else {
    $web->go($url);
    try {
        $dup = count($web->filter($filter));
        if ($dup > 1) {
            echo "{$url} ";
            colorLog("DUPLICATED", "w");
            $countdup++;
        }
        else if ($dup == 1) {
            echo "{$url} ";
            colorLog("OK", "s");
            $countok++;
        }
        else {
            echo "{$url} ";
            colorLog("NOT FOUND", "e");
            $countfail++;
        }
    }
    catch (Exception $e) {
        colorLog("{$component} not found in: {$url}", "e");
        $countfail++;
    }
    echo "Total of pages with $component found: $countok".PHP_EOL;
    echo "Total of pages with $component not found: $countfail".PHP_EOL;
    echo "Total of pages with $component duplicated: $countdup";
}