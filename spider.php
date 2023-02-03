<?php
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

$sitemap = $web
    ->go($domain)
    ->sitemap();

foreach ($sitemap as $link => $value) {
	$url = $value->link;
	$web->go($url);
	try {
		$web->filter($filter)->text();   // "Selector Tests"
		echo "{$url} ";
		colorLog("OK", "s");
	}
	catch (Exception $e) {
		colorLog("MVP Block not found in: {$url}", "e");
	}
}


