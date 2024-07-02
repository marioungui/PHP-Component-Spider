<?php

/**
 * Current version of the script
 *
 * @var string
 */
$CurrentVersion = "v0.5.5";

function checkForUpdate() {
    global $CurrentVersion;
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: PHP-Component-Spider\r\n"
        ]
    ]);
    $github_api_url = 'https://api.github.com/repos/marioungui/PHP-Component-Spider/releases/latest';
    $github_content = file_get_contents($github_api_url, false, $context);
    if ($github_content === false) {
        die("Failed to fetch content from Github API.");
    }
    $release = json_decode($github_content);
    $latest_version = $release->tag_name;
    if (version_compare($latest_version, $CurrentVersion) > 0) {
        colorLog("A new version of the script is available. Do you want to download it? (y/n) ", "i");
        $answer = trim(fgets(STDIN));
        if (strtolower($answer) === 'y') {
            $url='https://github.com/marioungui/PHP-Component-Spider/releases/download/'.$release->tag_name.'/PHP-Component-Spider.exe';
            $cmd=sprintf( 'start %s',$url );
            exec( $cmd );
        } else {
            echo "Update canceled.\n";
        }
    } else {
        echo "This script is up to date.\n";
    }
}

// CLI Helper
if(php_sapi_name() != "cli") {
    die("This script is only for CLI environment, please execute from a terminal.");
}

/**
 * Function for echoing a string with colored text on terminal
 * @param string $str The string to be echoed in terminal
 * @param string $type The type of message to be echoed, 'i' for info, 's' for success, 'w' for warning, 'i' for info (default)
 * @return void
 */
function colorLog($str, $type = 'i'){
    switch ($type) {
        case 'e': //error
            echo "\033[31m{$str}\033[0m";
        break;
        case 's': //success
            echo "\033[32m{$str}\033[0m";
        break;
        case 'w': //warning
            echo "\033[33m{$str}\033[0m";
        break;  
        case 'i': //info
            echo "\033[36m{$str}\033[0m";
        break;      
    }
}

checkForUpdate();

// Domain checker
function checkDomain($domain) {
	if(filter_var(gethostbyname($domain), FILTER_VALIDATE_IP)) {
	    return TRUE;
	}
	else {
		return false;
	}
}

//CLI Arg helper

if (!isset($arg)) {
    $arg = getopt("c:d:w:");
    if(!isset($arg["c"]) || !isset($arg["d"])) {
        colorLog("There is missing some variables, make shure that you are launching this script with the required parameters", "e");
        exit();
    }
    if (!checkDomain($arg["d"])) {
        colorLog("The domain isn't valid", "e");
        exit();
    }
}
if($arg["c"] == "word" || $arg["c"] == "7" || $arg["c"] == "links" || $arg["c"] == "9") {
    if (!isset($arg["w"])) {
        colorLog("You should specify with the -w{word} parameter which word are you searching for","e");
        exit();
    }
    else {
        $word = $arg["w"];
    }
}
/**
 * Make a function where you can check if the URL is inside the given domain
 *
 * @param string $url The URL of the current page
 * @param string $domain The domain that we are crawling
 * @return boolean
 */
function validate_domain ($url,$domain) {
       // Remove protocol if exists
       $url = preg_replace("(^https?://)", "", $url);
    
       // Remove www if exists
       $url = preg_replace("(^www.)", "", $url);
   
       // Extract the host from the URL
       $url_host = parse_url($url, PHP_URL_HOST);
   
       // Check if the host of the URL matches the specified domain
       if ($url_host === $domain) {
           return true;
       } else {
           // Check if the domain is a subdomain
           $domain = "." . $domain;
           $pos = strpos($url_host, $domain);
           if ($pos !== false && $pos === strlen($url_host) - strlen($domain)) {
               return true;
           } else {
               throw new Exception("URL is not inside the specified domain");
           }
       }
}