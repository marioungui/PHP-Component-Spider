<?php

use League\Csv\Writer;

require "helper.php";
require "vendor/autoload.php";
require "filters.php";

/**
 * Initialize a new instance of PHPScraper and set its configuration options.
 *
 * @var \Spekulatius\PHPScraper\PHPScraper $web The PHPScraper object.
 */
$web = new \Spekulatius\PHPScraper\PHPScraper;

/**
 * Set the timeout for the HTTP requests to 30 seconds.
 *
 * @var int $timeout The timeout value in seconds.
 */
$web->setConfig(['timeout' => 30]); 

/**
 * Set the maximum number of redirects to 10.
 *
 * @var int $max_redirects The maximum number of redirects.
 */
$web->setConfig(['max_redirects' => 10]);

/**
 * The domain name to scrape, with the protocol and subdomain included.
 *
 * @var string $domain The fully qualified domain name.
 */
$domain = "https://{$arg["d"]}";

$web->go($domain);


$title = $web->title;
echo "Searching for \033[33m".$component."\033[0m on the site \033[36m{$domain}\033[0m\r\nUsing the xPath filter \033[33m{$filter}\033[0m\r\n";
$writer = Writer::createFromPath(urlencode($component)."-".urlencode($arg["d"]).".csv","w+");

$sitemap = $web
    ->go($domain)
    ->sitemap();

$countok = 0;
$countfail = 0;
$countdup = 0;

// Init the Crawler and process all

/**
 * Searches for a specific word on a given sitemap and logs the results.
 *
 * @param array  $sitemap The sitemap to search in.
 * @param string $filter   The CSS filter to apply.
 * @param Writer $writer   The CSV writer to log the results.
 * @param int    &$countok The total number of pages containing the word.
 * @param int    &$countfail The total number of pages not containing the word.
 */
function testSearchForWord(array $sitemap, string $filter, Writer $writer, int &$countok, int &$countfail): void {
	// Iterate over each link in the sitemap.
	$writer->insertOne(["URL","Result"]);
	foreach ($sitemap as $link => $value) {
		$url = $value->link;
		try {
			// Try to go to the URL and create a new instance of PHPScraper.
			$web = new \Spekulatius\PHPScraper\PHPScraper;
			$web->go($url);
		}
		catch (Symfony\Component\HttpClient\Exception\TransportException $e) {
			// If the transport exception is thrown, continue to the next iteration.
			continue;
		}
		try {
			// Count the number of elements matching the filter.
			$count = count($web->filter($filter));
			if ($count > 0) {
				// If the count is greater than 0, write the URL and the count to the CSV.
				echo $url." ".colorLog("FOUND {$count} TIMES " , "s").PHP_EOL;
				$writer->insertOne([$url, "FOUND {$count} TIMES"]);
				$countok++;
			}
			else {
				// If the count is 0, write the URL and "NOT FOUND" to the CSV.
				echo $url." ".colorLog("NOT FOUND ", "e").PHP_EOL;
				$writer->insertOne([$url, "NOT FOUND"]);
				$countfail++;
			}
		}
		catch (Exception $e) {
			// If any other exception is thrown, write the URL and "NOT FOUND" to the CSV and increment $countfail.
			echo $url." ".colorLog("NOT FOUND ", "e").PHP_EOL;
			$writer->insertOne([$url, "NOT FOUND"]);
			$countfail++;
		}
	}
}
/**
 * Function to test if a given filter is present in a webpage.
 *
 * @param array $sitemap The sitemap of the webpage.
 * @param string $filter The filter to search for.
 * @param Writer $writer The CSV writer object.
 * @param int $countok The count of successful finds.
 * @param int $countfail The count of unsuccessful finds.
 * @param int $countdup The count of duplicate finds.
 */
function testSearchForComponent(array $sitemap, string $filter, Writer $writer, int &$countok, int &$countfail, int &$countdup) {
	// Iterate through each URL in the sitemap.
	$writer->insertOne(["URL","Result"]);
	foreach ($sitemap as $link => $value) {
		$url = $value->link;
		
		// Attempt to open the webpage.
		try {
			$web = new \Spekulatius\PHPScraper\PHPScraper;
			$web->go($url);
		}
		catch (Symfony\Component\HttpClient\Exception\TransportException $e) {
			// If HTTP request fails, log the URL and "HTTP Request Failed" to the CSV and continue to the next URL.
			$writer->insertOne([$url, "HTTP Request Failed"]);
			continue;
		}
		
		// Try to find the filter in the webpage.
		try {
			$dup = count($web->filter($filter));
			
			// If the filter is found more than once, log the URL and "DUPLICATED" to the CSV and increment $countdup.
			if ($dup > 1) {
				echo $url." ".colorLog("DUPLICATED ", "w").PHP_EOL;
				$writer->insertOne([$url, "DUPLICATED"]);
				$countdup++;
			}
			// If the filter is found once, log the URL and "OK" to the CSV and increment $countok.
			else if ($dup == 1) {
				echo $url." ".colorLog("OK ", "s").PHP_EOL;
				$writer->insertOne([$url, "OK"]);
				$countok++;
			}
			// If the filter is not found, log the URL and "NOT FOUND" to the CSV and increment $countfail.
			else {
				$writer->insertOne([$url, "NOT FOUND"]);
				echo $url." ".colorLog("NOT FOUND ", "e").PHP_EOL;
				$countfail++;
			}
		}
		// If any other exception is thrown, log the URL and "NOT FOUND" to the CSV and increment $countfail.
		catch (Exception $e) {
			$writer->insertOne([$url, "NOT FOUND "]);
			echo $url." ".colorLog("NOT FOUND", "e").PHP_EOL;
			$countfail++;
		}
	}
}

function testMetaData(array $sitemap, Writer $writer, int &$countok, int &$countfail) {
	$writer->insertOne(["URL","Meta Title", "Meta Description"]);
	foreach ($sitemap as $link => $value) {
		$url = $value->link;
		$web = new \Spekulatius\PHPScraper\PHPScraper;
		$web->go($url);
		try {
			$writer->insertOne([$url, $web->title, $web->description]);
			echo $url." ".colorLog($web->title." ", "s").PHP_EOL;
			$countok++;
		}
		catch (Exception $e) {
			$writer->insertOne([$url, "NOT FOUND"]);
			echo $url." ".colorLog("NOT FOUND", "e").PHP_EOL;
			$countfail++;
		}
	}
}
$validConditions = ["word", "7", "9", "links"];
if (in_array($arg["c"], $validConditions)) {
	testSearchForWord($sitemap, $filter, $writer, $countok, $countfail);
}
else if ($arg["c"] == "metatitle" || $arg["c"] == "10") {
	testMetaData($sitemap, $writer, $countok, $countfail);
}
else {
	testSearchForComponent($sitemap, $filter, $writer, $countok, $countfail, $countdup);
}
echo "Total of pages with ".$component." found: $countok".PHP_EOL;
echo "Total of pages with ".$component." not found: $countfail".PHP_EOL;
echo "Total of pages with ".$component." duplicated: $countdup".PHP_EOL;