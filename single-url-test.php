<?php

/*
THIS IS A TESTING ONLY SCRIPT, IT ONLY DOES CRAWL THE HOME PAGE AND THE URL TO BE TESTED
FOR TESTING A SINGLE URL, JUST REPLACE THE $url VARIABLE AT LINE 81
*/

if(php_sapi_name() != "cli") {
    die("This script is only for CLI environment, please execute from a terminal.");
}

function colorLog($str, $type = 'i'){
    switch ($type) {
        case 'e': //error
            echo "\033[31m$str \033[0m\n";
        break;
        case 's': //success
            echo "\033[32m$str \033[0m\n";
        break;
        case 'w': //warning
            echo "\033[33m$str \033[0m\n";
        break;  
        case 'i': //info
            echo "\033[36m$str \033[0m\n";
        break;      
        default:
        # code...
        break;
    }
}
function checkDomain($domain) {
	if(filter_var(gethostbyname($domain), FILTER_VALIDATE_IP)) {
	    return TRUE;
	}
	else {
		return false;
	}
}

$arg = getopt("c:d:");
if(!isset($arg["c"]) || !isset($arg["d"])) {
	colorLog("There is missing some variables, make shure that you are launching this script with the required parameters", "e");
	exit();
}

require "vendor/autoload.php";

//CLI helper

if (!checkDomain($arg["d"])) {
	colorLog("The domain isn't valid", "e");
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
$web->setConfig(['timeout' => 30]); // set the timeout to 30s
$domain = "https://{$arg["d"]}";

$web->go($domain);
$title = $web->title;
echo "Buscando \033[33m{$component}\033[0m en el sitio de \033[36m{$title}\033[0m\n";

$countok = 0;
$countfail = 0;
$countdup = 0;
$url = "https://www.nestlebabyandme.cl/nido-etapas";
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