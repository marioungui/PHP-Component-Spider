<?php

require "helper.php";
require "vendor/autoload.php";
require "filters.php";

$web = new \Spekulatius\PHPScraper\PHPScraper;
$web->setConfig(['timeout' => 30]); // set the timeout to 30s
$domain = "https://{$arg["d"]}";

$web->go($domain);
$title = $web->title;
echo "Searching for \033[33m{$component}\033[0m on the site \033[36m{$title}\033[0m\n";

$sitemap = $web
    ->go($domain)
    ->sitemap();

$countok = 0;
$countfail = 0;
$countdup = 0;

// Init the Crawler and process all
if ($arg["c"] == "word") {
	foreach ($sitemap as $link => $value) {
		$url = $value->link;
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
}
else {
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
}

echo "Total of pages with $component found: $countok".PHP_EOL;
echo "Total of pages with $component not found: $countfail".PHP_EOL;
echo "Total of pages with $component duplicated: $countdup";