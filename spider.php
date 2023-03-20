<?php

require "helper.php";
require "vendor/autoload.php";

//CLI helper

$arg = getopt("c:d:");
if(!isset($arg["c"]) || !isset($arg["d"])) {
	colorLog("There is missing some variables, make shure that you are launching this script with the required parameters", "e");
	exit();
}

if (!checkDomain($arg["d"])) {
	colorLog("The domain isn't valid", "e");
	exit();
}

require "filters.php";

$web = new \Spekulatius\PHPScraper\PHPScraper;
$web->setConfig(['timeout' => 30]); // set the timeout to 30s
$domain = "https://{$arg["d"]}";

$web->go($domain);
$title = $web->title;
echo "Buscando \033[33m{$component}\033[0m en el sitio de \033[36m{$title}\033[0m\n";

$sitemap = $web
    ->go($domain)
    ->sitemap();

$countok = 0;
$countfail = 0;
$countdup = 0;
foreach ($sitemap as $link => $value) {
	$url = $value->link;
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
}
echo "Total of pages with $component found: $countok".PHP_EOL;
echo "Total of pages with $component not found: $countfail".PHP_EOL;
echo "Total of pages with $component duplicated: $countdup";