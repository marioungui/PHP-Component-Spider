<?php
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
    }
}
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
