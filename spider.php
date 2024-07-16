<?php

use League\Csv\Writer;

require "vendor/autoload.php";
require "helper.php";
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
 * The input (domain or CSV file path).
 *
 * @var string $input The input provided by the user.
 */
$input = $arg["d"];

/**
 * Check the type of input provided (sitemap or CSV file).
 *
 * @var string|false $type The type of input ('sitemap', 'csv', or false if invalid).
 */
$type = checkDomain($input);

if ($type === false) {
    echo "Invalid input. Please provide a valid domain or CSV file path.";
    exit(1);
}

$writer = Writer::createFromPath(urlencode($component)."-".urlencode($input).".csv", "w+");

$countok = 0;
$countfail = 0;
$countdup = 0;

/**
 * Function to extract URLs from a sitemap.
 *
 * @param \Spekulatius\PHPScraper\PHPScraper $web The PHPScraper object.
 * @param string $domain The domain to scrape.
 * @return array The array of URLs.
 */
function extractUrlsFromSitemap(\Spekulatius\PHPScraper\PHPScraper $web, string $domain): array {
    $sitemap = $web->go($domain)->sitemap();
    $urls = [];

    foreach ($sitemap as $link => $value) {
        $urls[] = $value->link;
    }

    return $urls;
}


/**
 * Function to read URLs from the "URL" column of a CSV file.
 *
 * @param string $filePath The path to the CSV file.
 * @return array The array of URLs from the "URL" column.
 */
function extractUrlsFromCsv(string $filePath): array {
    $csv = \League\Csv\Reader::createFromPath($filePath, 'r');
    $csv->setHeaderOffset(0);
    $urls = [];

    foreach ($csv->getRecords() as $record) {
        // Fetch the value of the "URL" column
        $urls[] = $record['URL'];
    }

    return $urls;
}

/**
 * Searches for a specific word on a given array of URLs and logs the results.
 *
 * @param array $urls The array of URLs to search in.
 * @param string $filter The CSS filter to apply.
 * @param Writer $writer The CSV writer to log the results.
 * @param int &$countok The total number of pages containing the word.
 * @param int &$countfail The total number of pages not containing the word.
 */
function testSearchForWord(array $urls, string $filter, Writer $writer, int &$countok, int &$countfail): void {
    $writer->insertOne(["URL","Result"]);
    foreach ($urls as $url) {
        try {
            $web = new \Spekulatius\PHPScraper\PHPScraper;
            $web->go($url);
        } catch (Symfony\Component\HttpClient\Exception\TransportException $e) {
            continue;
        }
        try {
            $count = count($web->filter($filter));
            if ($count > 0) {
                echo $url." ".colorLog("FOUND {$count} TIMES ", "s").PHP_EOL;
                $writer->insertOne([$url, "FOUND {$count} TIMES"]);
                $countok++;
            } else {
                echo $url." ".colorLog("NOT FOUND ", "e").PHP_EOL;
                $writer->insertOne([$url, "NOT FOUND"]);
                $countfail++;
            }
        } catch (Exception $e) {
            echo $url." ".colorLog("NOT FOUND ", "e").PHP_EOL;
            $writer->insertOne([$url, "NOT FOUND"]);
            $countfail++;
        }
    }
}

/**
 * Function to test if a given filter is present in a webpage.
 *
 * @param array $urls The array of URLs to search in.
 * @param string $filter The filter to search for.
 * @param Writer $writer The CSV writer object.
 * @param int &$countok The count of successful finds.
 * @param int &$countfail The count of unsuccessful finds.
 * @param int &$countdup The count of duplicate finds.
 */
function testSearchForComponent(array $urls, string $filter, Writer $writer, int &$countok, int &$countfail, int &$countdup) {
    $writer->insertOne(["URL","Result"]);
    foreach ($urls as $url) {
        try {
            $web = new \Spekulatius\PHPScraper\PHPScraper;
            $web->go($url);
        } catch (Symfony\Component\HttpClient\Exception\TransportException $e) {
            $writer->insertOne([$url, "HTTP Request Failed"]);
            continue;
        }
        try {
            $dup = count($web->filter($filter));
            if ($dup > 1) {
                echo $url." ".colorLog("DUPLICATED ", "w").PHP_EOL;
                $writer->insertOne([$url, "DUPLICATED"]);
                $countdup++;
            } else if ($dup == 1) {
                echo $url." ".colorLog("OK ", "s").PHP_EOL;
                $writer->insertOne([$url, "OK"]);
                $countok++;
            } else {
                $writer->insertOne([$url, "NOT FOUND"]);
                echo $url." ".colorLog("NOT FOUND ", "e").PHP_EOL;
                $countfail++;
            }
        } catch (Exception $e) {
            $writer->insertOne([$url, "NOT FOUND "]);
            echo $url." ".colorLog("NOT FOUND", "e").PHP_EOL;
            $countfail++;
        }
    }
}

/**
 * Function to extract meta data from a webpage.
 *
 * @param array $urls The array of URLs to search in.
 * @param Writer $writer The CSV writer object.
 * @param int &$countok The count of successful finds.
 * @param int &$countfail The count of unsuccessful finds.
 */
function testMetaData(array $urls, Writer $writer, int &$countok, int &$countfail) {
    $writer->insertOne(["URL","Meta Title", "Meta Description"]);
    foreach ($urls as $url) {
		$excludeFormats = ['.pdf', '.jpg', '.jpeg', '.png', '.gif', '.tif', '.tiff', '.bmp', '.svg', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.zip', '.rar', '.7z', '.mp3', '.mp4', '.avi', '.mkv', '.mov', '.wmv', '.webm', '.m4a', '.m4v', '.txt', '.csv', '.json', '.xml', '.gz', '.sql', '.db', '.iso', '.dmg', '.exe', '.apk', '.crx', '.ipa', '.deb', '.rpm'];
		foreach ($excludeFormats as $excludeFormat) {
			if (stripos($url, $excludeFormat) !== false) {
				echo $url." ".colorLog("SKIPPING FILE WITH EXCLUDED FORMAT", "w").PHP_EOL;
				$writer->insertOne([$url, "SKIPPING FILE WITH EXCLUDED FORMAT"]);
				continue 2;
			}
		}
        $web = new \Spekulatius\PHPScraper\PHPScraper;
        $web->go($url);
        try {
            $writer->insertOne([$url, $web->title, $web->description]);
            echo $url." ".colorLog($web->title." ", "s").PHP_EOL;
            $countok++;
        } catch (Exception $e) {
            $writer->insertOne([$url, "NOT FOUND"]);
            echo $url." ".colorLog("NOT FOUND", "e").PHP_EOL;
            $countfail++;
        }
    }
}

function testH1(array $urls, Writer $writer, int &$countok, int &$countfail) {
    // Insert the header row
    $writer->insertOne(["URL", "H1", "H1 Length"]);

    // Define formats to exclude
    $excludeFormats = ['.pdf', '.jpg', '.jpeg', '.png', '.gif', '.tif', '.tiff', '.bmp', '.svg', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.zip', '.rar', '.7z', '.mp3', '.mp4', '.avi', '.mkv', '.mov', '.wmv', '.webm', '.m4a', '.m4v', '.txt', '.csv', '.json', '.xml', '.gz', '.sql', '.db', '.iso', '.dmg', '.exe', '.apk', '.crx', '.ipa', '.deb', '.rpm'];

    foreach ($urls as $url) {
        // Check if the URL contains any excluded formats
        foreach ($excludeFormats as $excludeFormat) {
            if (stripos($url, $excludeFormat) !== false) {
                echo $url . " " . colorLog("SKIPPING FILE WITH EXCLUDED FORMAT", "w") . PHP_EOL;
                $writer->insertOne([$url, "SKIPPING FILE WITH EXCLUDED FORMAT"]);
                continue 2; // Skip to the next URL
            }
        }

        $web = new Spekulatius\PHPScraper\PHPScraper;
        $web->go($url);

        try {
            $h1 = trim(preg_replace('/\s+/', ' ', $web->h1[0]));
            
            $h1Length = strlen($h1);

            // Insert the data row
            $writer->insertOne([$url, $h1, $h1Length]);

            echo $url . " " . colorLog($web->title . " ", "s") . PHP_EOL;
            $countok++;
        } catch (Exception $e) {
            $writer->insertOne([$url, "NOT FOUND"]);
            echo $url . " " . colorLog("NOT FOUND", "e") . PHP_EOL;
            $countfail++;
        }
    }
}

// Extract URLs based on the type of input
$urls = [];
if ($type === "sitemap") {
    $urls = extractUrlsFromSitemap($web, "https://".$input);
	echo "Extracting URLs from the site ".colorLog($input, "s")." using filter {$component}".PHP_EOL;
} elseif ($type === "csv") {
	echo "Extracting URLs from ".colorLog("provided CSV file", "w").PHP_EOL;
    $urls = extractUrlsFromCsv($input);
}

$validConditions = ["word", "7", "9", "links", "11", "new-tab"];
if (in_array($arg["c"], $validConditions)) {
    testSearchForWord($urls, $filter, $writer, $countok, $countfail);
} elseif ($arg["c"] == "metatitle" || $arg["c"] == "10") {
    testMetaData($urls, $writer, $countok, $countfail);
} elseif ($arg["c"] == "h1-length" || $arg["c"] == "13") {
    testH1($urls, $writer, $countok, $countfail);
} 
else {
    testSearchForComponent($urls, $filter, $writer, $countok, $countfail, $countdup);
}

echo "Total of pages with ".$component." found: $countok".PHP_EOL;
echo "Total of pages with ".$component." not found: $countfail".PHP_EOL;
echo "Total of pages with ".$component." duplicated: $countdup".PHP_EOL;
