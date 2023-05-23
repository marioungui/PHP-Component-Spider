<?php

use League\Csv\Writer;

require "helper.php";
require "vendor/autoload.php";
require "filters.php";

$web = new \Spekulatius\PHPScraper\PHPScraper;
$web->setConfig(['timeout' => 30]); // set the timeout to 30s
$web->setConfig(['max_redirects' => 10]);
$domain = "https://{$arg["d"]}";

$web->go($domain);

$title = $web->title;
echo "Searching for \033[33m".$component."\033[0m on the site \033[36m{$title}\033[0m\n";
$writer = Writer::createFromPath(urlencode($component)."-".urlencode($arg["d"]).".csv","w+");
$writer->insertOne(["URL","Result"]);

$sitemap = $web
    ->go($domain)
    ->sitemap();

$countok = 0;
$countfail = 0;
$countdup = 0;

// Init the Crawler and process all

//If we are searching for a word, then we parse in a somewhat different way
if ($arg["c"] == "word" || $arg["c"] == "7" || $arg["c"] == "9" || $arg["c"] == "links") {
	foreach ($sitemap as $link => $value) {
		$url = $value->link;
		$web->go($url);
		try {
			$count = count($web->filter($filter));
			if ($count > 0) {
				echo "{$url} ";
				colorLog("FOUND {$count} TIMES", "s");
				$writer->insertOne([$url, "FOUND {$count} TIMES"]);
				$countok++;
			}
			else {
				echo "{$url} ";
				colorLog("NOT FOUND", "e");
				$writer->insertOne([$url, "NOT FOUND"]);
				$countfail++;
			}
		}
		catch (Exception $e) {
			colorLog($component." not found in: {$url}", "e");
			$countfail++;
		}
	}
}

// For searching a component
else {
	foreach ($sitemap as $link => $value) {
		$url = $value->link;
		try {
			$web->go($url);
		} catch (Symfony\Component\HttpClient\Exception\TransportException $e) {
			echo "{$url} ";
			colorLog("HTTP Request Failed","e");
			$writer->insertOne([$url, "HTTP Request Failed"]);
			continue;
		}
		
		try {
			$dup = count($web->filter($filter));
			if ($dup > 1) {
				echo "{$url} ";
				colorLog("DUPLICATED", "w");
				$writer->insertOne([$url, "DUPLICATED"]);
				$countdup++;
			}
			else if ($dup == 1) {
				echo "{$url} ";
				colorLog("OK", "s");
				$writer->insertOne([$url, "OK"]);
				$countok++;
			}
			else {
				echo "{$url} ";
				colorLog("NOT FOUND", "e");
				$writer->insertOne([$url, "NOT FOUND"]);
				$countfail++;
			}
		}
		catch (Exception $e) {
			colorLog($component." not found in: {$url}", "e");
			$countfail++;
		}
	}
}

echo "Total of pages with ".$component." found: $countok".PHP_EOL;
echo "Total of pages with ".$component." not found: $countfail".PHP_EOL;
echo "Total of pages with ".$component." duplicated: $countdup".PHP_EOL;