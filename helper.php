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

